<?php

namespace Enon\Models\Enon;

use Enon\Models\Exceptions\Exception;
use WPENON\Model\EnergieausweisManager;

/**
 * Prevent completion class.
 * 
 * Plausibilitätsprüfungen für Energieausweise. Wenn eine Plausibilitätsprüfung fehlschlägt, wird der Energieausweis nicht abgeschlossen.
 * 
 * @since 1.0.0
 */
class Prevent_Completion
{
    private static $instance;

    /**
     * All Checks
     * 
     * @var []
     */
    private $checks = [
        // 'check_energy',
        'check_end_energy',
        'check_heater_consumption',
        'check_heater_consumption_for_estimation',
        'check_heater_type',
        'check_building_year_wall_insulation',
        'check_double_heater_including_hotwater',
        'check_too_good_energy_certificate_1'
    ];

    private $errors = [];

    /**
     * Energy certificate.
     * 
     * @var \WPENON\Model|Energieausweis Energieausweis object.
     */
    private $energy_certificate;

    /**
     * Energy certificate calculations.
     * 
     * @var array
     */
    private $calculations;

    /**
     * Init functionality to WP
     */
    public static function init()
    {
        self::$instance = new self;
    }

    private function __construct()
    {
        add_filter('edd_should_update_payment_status', [$this, 'filter_should_update_payment_status'], 10, 3);
        add_action('admin_notices', [$this, 'show_errors']);
    }

    /**
     * Should update payment status filter.
     * 
     * @param bool   Should the payment status be updated?
     * @param int    Payment id.
     * @param string New status which should be updated to.
     * 
     * @return bool  True if the payment status will be updated, false if not.
     * 
     * @since 1.0.0
     */
    public function filter_should_update_payment_status(bool $can_be_updated, int $payment_id,  string $new_status): bool
    {
        try {
            $this->set_energy_certificate($payment_id);

            $failure_mail_sent = get_post_meta($payment_id, 'failure_mail_sent', true);

            if (!$this->can_we_update($can_be_updated, $new_status)) {
                return $can_be_updated;
            }

            $fails = [];
            foreach ($this->checks as $check) {
                $result = $this->$check();

                if ($result !== true) {
                    $fails[] = $result;
                }
            }

            if (!empty($fails) && $failure_mail_sent !== 'yes') {
                $this->send_failure_mail($payment_id, $fails);
                update_post_meta($payment_id, 'failure_mail_sent', 'yes');
                return false;
            }

            if (!empty($fails)) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    /**
     * Can the status of the energy certificate be changed by us.
     * 
     * @param bool $
     */
    private function can_we_update(bool $can_be_updated, $new_status): bool
    {
        if (!$can_be_updated) {
            return false;
        }

        if (current_user_can('administrator') || current_user_can('edit_shop_payments')) {
            return false;
        }

        if (!in_array($new_status, array('complete', 'completed', 'publish'))) {
            return false;
        }

        if (!class_exists(EnergieausweisManager::class)) {
            return false;
        }

        if (!$this->calculations) {
            return false;
        }

        return true;
    }

    /**
     * Set energy certificate.
     * 
     * @param int Pament id.
     * 
     * @throws Exception
     * 
     * @since 1.0.0
     */
    private function set_energy_certificate($payment_id)
    {
        $posts = get_posts(array(
            'post_type'      => 'download',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'     => '_wpenon_attached_payment_id',
                    'value'   => $payment_id,
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                ),
            ),
        ));

        if (!isset($posts[0])) {
            throw new Exception(sprintf('Energy certificate with payment id %s not found.', $payment_id));
        }

        $energy_certificate = EnergieausweisManager::getEnergieausweis($posts[0]);

        if (!$energy_certificate) {
            throw new Exception('Energy certificate can not be initialized.');
        }

        $this->calculations   = $energy_certificate->calculate();
        $this->energy_certificate = $energy_certificate;
    }

    /**
     * Send faiure email.
     * 
     * @param int   Payment id.
     * @param array Fails array.
     * 
     * @since 1.0.0
     */
    private function send_failure_mail(int $payment_id, $fails = [])
    {
        $reasons = '<ul>';
        foreach ($fails as $fail) {
            $reasons .= '<li>' . $fail . '</li>';
        }
        $reasons .= '</ul>';

        wpenon_immoticket24_send_needs_review_email($payment_id, $reasons);
    }

    /**
     * Check building year wall insulation.
     * 
     * @since1 1.0.0
     * 
     * @return bool|array True if passed, array with errors on failure.
     */
    private function check_building_year_wall_insulation()
    {
        // Only check buildings older then 20 years
        if ($this->energy_certificate->baujahr < (date('Y') - 20)) {
            return true;
        }

        $insulation_parts = [
            'wand_a_daemmung',
            'wand_b_daemmung',
            'wand_c_daemmung',
            'wand_d_daemmung',
            'wand_e_daemmung',
            'wand_f_daemmung',
            'wand_h_daemmung',
            'dach_daemmung',
            'decke_daemmung',
            'boden_daemmung',
            'keller_daemmung',
            'anbauwand_daemmung',
            'anbaudach_daemmung',
            'anbauboden_daemmung'
        ];

        foreach ($insulation_parts as $insulation_part) {
            if (isset($this->energy_certificate->$insulation_part) && (float) $this->energy_certificate->$insulation_part > 0) {
                return 'Nachträgliche Dämmung bei einem Gebäude unter 20 Jahren';
            }
        }

        return true;
    }

    /**
     * Check heater consumption.
     * 
     * @since 1.0.0
     * 
     * @return bool|array True if passed, array with errors on failure.
     */
    private function check_heater_consumption()
    {
        // Only check verbrauchsausweis
        if ('v' !== $this->energy_certificate->mode) {
            return true;
        }

        // True if leerstand is not 0 at all values
        if (
            (int) $this->energy_certificate->verbrauch1_leerstand !== 0 ||
            (int) $this->energy_certificate->verbrauch2_leerstand !== 0 ||
            (int) $this->energy_certificate->verbrauch3_leerstand !== 0
        ) {
            return true;
        }

        $compare_values = [
            $this->energy_certificate->verbrauch1_h,
            $this->energy_certificate->verbrauch2_h,
            $this->energy_certificate->verbrauch3_h,
        ];

        $percentage_treshold = 30;

        $min_value = min($compare_values);
        $max_value = max($compare_values);

        $percentage_max = 100;
        $percentage_min = 100 / $max_value * $min_value;

        $percentage_diff = $percentage_max - $percentage_min;

        // True if percentag difference is under treshold
        if ($percentage_diff >= $percentage_treshold) {
            return 'Der Abstand der Verbrauchsmengen zwischen dem Mindest- und dem Höchstverbrauch liegt über 30%';
        }

        return true;
    }

    public function check_heater_consumption_for_estimation()
    {
        // Only check verbrauchsausweis
        if ('v' !== $this->energy_certificate->mode) {
            return true;
        }

        $values = [
            $this->energy_certificate->verbrauch1_h,
            $this->energy_certificate->verbrauch2_h,
            $this->energy_certificate->verbrauch3_h,
        ];

        // Check for 000 at end
        $count_thousands = 0;
        foreach ($values as $value) {
            $string = (string) $value;
            if (substr($string, strlen($string) - 3, 3) === '000') {
                $count_thousands++;
            }
        }

        // Check for 00 at end
        $count_hundrets = 0;
        foreach ($values as $value) {
            $string = (string) $value;
            if (substr($string, strlen($string) - 2, 2) === '00') {
                $count_hundrets++;
            }
        }

        if ($count_thousands === 3 || $count_hundrets === 3) {
            return 'Die Verbrauchsmengen wurden vermutlich geschätzt eingegeben';
        }

        return true;
    }

    /**
     * Checking heater types.
     * 
     * @since 1.0.0
     * 
     * @return bool|array True if passed, array with errors on failure.
     */
    private function check_heater_type()
    {
        $heaters = [
            'h_erzeugung',
            'h2_erzeugung',
            'h3_erzeugung',
        ];

        foreach ($heaters as $heater) {
            if ($this->energy_certificate->$heater === 'brennwertkesselverbessert') {
                return 'Brennwertkessel verbessert wurde ausgewählt';
            }
        }

        return true;
    }

    /**
     * Checking vw heaters if more than one used and including hot water.
     * 
     * @since 1.0.0
     * 
     * @return bool|array True if passed, array with errors on failure.
     */
    private function check_double_heater_including_hotwater()
    {
        // Only check verbrauchsausweis
        if ('v' !== $this->energy_certificate->mode) {
            return true;
        }

        $count = 1; // Erste Heizungsanlage wird immer mitgezählt

        if ($this->energy_certificate->h2_info) {
            $count++;
        }

        if ($this->energy_certificate->h3_info) {
            $count++;
        }

        if ($count < 2) {
            return true;
        }

        if ($this->energy_certificate->ww_info !== 'h') {
            return true;
        }

        return 'Es wurden zwei Heizungsanlagen ausgewählt mit Warmwasser pauschal in der Heizungsanlage enthalten.';
    }

    /**
     * Check end energy values.
     * 
     * @since 1.0.0
     * 
     * @return bool|array True if passed, array with errors on failure.
     */
    private function check_end_energy()
    {
        $boundaries = 'v' === $this->energy_certificate->mode ? array(5.0, 250.0) : array(5.0, 400.0);

        $checks = array();

        if ($this->calculations['endenergie'] <= $boundaries[0]) {
            $checks[] = sprintf('Zu geringer Verbrauch. (Endenergie in Höhe von %s ist kleiner/gleich %s.)', number_format_i18n($this->calculations['endenergie'], 2), $boundaries[0]);
        }

        if ($this->calculations['endenergie'] >= $boundaries[1]) {
            $checks[] = sprintf('Zu hoher Verbrauch. (Endenergie in Höhe von %s ist größer/gleich %s.)', number_format_i18n($this->calculations['endenergie'], 2), $boundaries[1]);
        }

        if (!empty($checks)) {
            return implode(' ', $checks);
        }

        return true;
    }

    private function check_too_good_energy_certificate_1()
    {
        if ($this->energy_certificate->baujahr > 2010) {
            return true;
        }

        $energy_effiency_class = wpenon_get_class($this->calculations['endenergie'], $this->energy_certificate->mode === 'v' ? 'vw' : 'bw');

        if ($energy_effiency_class != 'A+' && $energy_effiency_class != 'A' && $energy_effiency_class != 'B') {
            return true;
        }

        if ($this->energy_certificate->h_erzeugung == 'waermepumpeluft' || $this->energy_certificate->h_erzeugung == 'waermepumpewasser' || $this->energy_certificate->h_erzeugung == 'waermepumpeerde') {
            return true;
        }

        if ($this->energy_certificate->h2_info && ($this->energy_certificate->h2_erzeugung == 'waermepumpeluft' || $this->energy_certificate->h2_erzeugung == 'waermepumpewasser' || $this->energy_certificate->h2_erzeugung == 'waermepumpeerde')) {
            return true;
        }

        if ($this->energy_certificate->h3_info && ($this->energy_certificate->h3_erzeugung == 'waermepumpeluft' || $this->energy_certificate->h3_erzeugung == 'waermepumpewasser' || $this->energy_certificate->h3_erzeugung == 'waermepumpeerde')) {
            return true;
        }

        return \sprintf('Zu gute Energieeffizienzklasse %s für Gebäude bis 2010 (keine Wärmepumpe vorhanden).', $energy_effiency_class);
    }

    /**
     * Check energy values.
     * 
     * @since 1.0.0
     * 
     * @return bool|array True if passed, array with errors on failure.
     */
    private function check_energy()
    {
        // qh_e_b = Endenergiekennwert-Waerme-AN
        // qw_e_b = Endenergiebedarf-Waerme-AN
        $checks = array();

        if ($this->calculations['qh_e_b'] <= 5.0) {
            $checks[] = sprintf('Endenergiekennwert-Waerme-AN in Höhe von %s ist kleiner/gleich %s.', number_format_i18n($this->calculations['qh_e_b'], 2), 5.0);
        }

        if ($this->calculations['qw_e_b'] <= 5.0) {
            $checks[] = sprintf('Endenergiebedarf-Waerme-AN in Höhe von %s ist kleiner/gleich %s.', number_format_i18n($this->calculations['qw_e_b']), 5.0);
        }

        if (!empty($checks)) {
            return implode(' ', $checks);
        }

        return true;
    }

    public function show_errors()
    {
        if (empty($this->errors)) {
            return;
        }

        echo '<div class="notice notice-error">';
        echo '<p><strong>Es wurden Fehler gefunden:</strong></p>';
        echo '<ul>';
        foreach ($this->errors as $error) {
            echo '<li>' . $error . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
}
