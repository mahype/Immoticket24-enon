<?php

namespace Enon\Models\Enon;

use Enon\Models\Exceptions\Exception;
use WPENON\Model\EnergieausweisManager;

/**
 * Prevent completion class.
 * 
 * @since 1.0.0
 */
class Prevent_Completion {
    private static $instance;

    /**
     * All Checks
     * 
     * @var []
     */
    private $checks = [
        'check_energy',
        'check_end_energy',
        'check_heater_consumption',
        'check_heater_type',
        'check_building_year_wall_insulation',    
    ];

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
    public static function init() {
        self::$instance = new self;
    }

    private function __construct() {
        add_filter( 'edd_should_update_payment_status', [ $this, 'filter_should_update_payment_status' ], 10, 3  );
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
    public function filter_should_update_payment_status ( bool $can_be_updated, int $payment_id,  string $new_status ) : bool {
        $this->set_energy_certificate( $payment_id );

        if ( ! $this->can_we_update( $can_be_updated, $new_status ) ) {
            return $can_be_updated;
        }

        $fails = [];
        foreach( $this->checks AS $check ) {
            $result = $this->$check();

            if( $result !== true ) {
                $fails[] = $result;
            }
        }
        
        if ( ! empty( $fails ) ) {
            $this->send_failure_mail( $payment_id, $fails );
            return false;
        }
    
        return true;
    }

    /**
     * Can the status of the energy certificate be changed by us.
     * 
     * @param bool $
     */
    private function can_we_update( bool $can_be_updated, $new_status ) : bool {
        if ( ! $can_be_updated ) {
            return false;
        }
        
        if ( ! in_array( $new_status, array( 'complete', 'completed', 'publish' ) ) ) {
            return false;
        }

        if ( ! class_exists( EnergieausweisManager::class ) ) {
            return false;
        }

        if ( ! $this->calculations ) {
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
    private function set_energy_certificate( $payment_id ) {
        $posts = get_posts( array(
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
        ) );

        if ( ! isset( $posts[0] ) ) {
            throw new Exception( 'Energy certificate not found.' );
        }

        $energy_certificate = EnergieausweisManager::getEnergieausweis( $posts[0] );

        if ( ! $energy_certificate ) {
            throw new Exception( 'Energy certificate can not be initialized.' );
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
    private function send_failure_mail ( int $payment_id, $fails = [] ) {
        $reasons = '<ul>';
        foreach( $fails as $fail ) {
            $reasons.= '<li>' . $fail . '</li>';
        }
        $reasons.= '</ul>';

        wpenon_immoticket24_send_needs_review_email( $payment_id, $reasons );
    }

    /**
     * Check building year wall insulation.
     * 
     * @since1 1.0.0
     * 
     * @return bool|array True if passed, array with errors on failure.
     */
    private function check_building_year_wall_insulation() {
        // Only check buildings older then 20 years
        if ( $this->energy_certificate->baujahr < ( date('Y') - 20 ) ) {
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
    
        foreach ( $insulation_parts AS $insulation_part ) {
            if( isset( $this->energy_certificate->$insulation_part ) && (float) $this->energy_certificate->$insulation_part > 0 ) {
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
    private function check_heater_consumption() {
        // Only check verbrauchsausweis
        if ( 'v' !== $this->energy_certificate->mode ) {
            return true;
        }

        // True if leerstand is not 0 at all values
        if ( 
                (int) $this->energy_certificate->verbrauch1_leerstand !== 0 ||
                (int) $this->energy_certificate->verbrauch2_leerstand !== 0 || 
                (int) $this->energy_certificate->verbrauch3_leerstand !== 0
        ) 
        {
            return true; 
        }

        $compare_values = [
            $this->energy_certificate->verbrauch1_h,
            $this->energy_certificate->verbrauch2_h,
            $this->energy_certificate->verbrauch3_h,
        ];

        $percentage_treshold = 30;

        $min_value = min( $compare_values );
        $max_value = max( $compare_values );

        $percentage_max = 100;
        $percentage_min = 100 / $max_value * $min_value;

        $percentage_diff = $percentage_max - $percentage_min;

        // True if percentag difference is under treshold
        if ( $percentage_diff >= $percentage_treshold ) {
            return [ 'Der Abstand der Verbrauchsmengen zwischen dem Mindest- und dem Höchstverbrauch liegt über 30%' ];
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
    private function check_heater_type() {
        $heaters = [
            'h_erzeuger',
            'h2_erzeuger',
            'h3_erzeuger',
        ];

        foreach( $heaters AS $heater ) {
            if ( $this->energy_certificate->$heater === 'brennwertkesselverbessert') {
                return [ 'Brennwertkessel verbessert wurde ausgewählt' ];
            }
        }

        return true;
    }

    /**
     * Check end energy values.
     * 
     * @since 1.0.0
     * 
     * @return bool|array True if passed, array with errors on failure.
     */
    private function check_end_energy() {
        $boundaries = 'v' === $this->energy_certificate->mode ? array( 5.0, 250.0 ) : array( 5.0, 400.0 );

        $checks = array();

        if ( $this->calculations['endenergie'] <= $boundaries[0] ) {
            $checks[] = sprintf( 'Zu geringer Verbrauch. (Endenergie in Höhe von %s ist kleiner/gleich %s.)', number_format_i18n( $this->calculations['endenergie'], 2 ), $boundaries[0] );
        }

        if ( $this->calculations['endenergie'] >= $boundaries[1] ) {
            $checks[] = sprintf( 'Zu hoher Verbrauch. (Endenergie in Höhe von %s ist größer/gleich %s.)', number_format_i18n( $this->calculations['endenergie'], 2 ), $boundaries[1] );
        }

        if( ! empty( $checks ) ) {
            return implode( ' ', $checks );
        }

        return true;
    }

    /**
     * Check energy values.
     * 
     * @since 1.0.0
     * 
     * @return bool|array True if passed, array with errors on failure.
     */
    private function check_energy() {
        // qh_e_b = Endenergiekennwert-Waerme-AN
        // qw_e_b = Endenergiebedarf-Waerme-AN
        $checks = array();

        if ( $this->calculations['qh_e_b'] <= 5.0 ) {
            $checks[] = sprintf( 'Endenergiekennwert-Waerme-AN in Höhe von %s ist kleiner/gleich %s.', number_format_i18n( $this->calculations['qh_e_b'], 2 ) , 5.0 );
        }

        if ( $this->calculations['qw_e_b'] <= 5.0 ) {
            $checks[] = sprintf( 'Endenergiebedarf-Waerme-AN in Höhe von %s ist kleiner/gleich %s.', number_format_i18n( $this->calculations['qw_e_b'] ), 5.0 );
        }

        if( ! empty( $checks ) ) {
            return implode( ' ', $checks );
        }

        return true;
    }
}