<?php
/**
 * Additional fee for subsidies_consultation.
 *
 * @category Class
 * @package  Enon\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;
use WPENON\Model\Energieausweis;

/**
 * Class Add_Costum_Fees_Subsidies_consultation.
 *
 * @since 1.0.0
 */
class Add_Costum_Fees_Subsidies_Consultation implements Filters, Task {
	/**
	 * Premiumbewertung_Popup constructor.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_filters();
	}

	/**
	 * Add filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'eddcf_custom_fees', [ $this, 'filter_fees' ] );
		add_filter( 'wpenon_zusatzoptionen_settings', [ $this, 'filter_options' ] );
		add_filter( 'enon_edd_emails_payment_fees', array( $this, 'filter_emails' ), 10, 3 );
	}

	/**
	 * Filter fees.
	 *
	 * @param array $fees Fees.
	 *
	 * @return array Filtered fees.
	 *
	 * @since 1.0.0
	 */
	public function filter_fees( $fees ) {
		if ( ! $this->reason_allowed() ) {
			return $fees;
		}

		$fees[] = [
			'id' => 'subsidies_consultation',
            'label' => $this->get_label(),
            'amount' => $this->get_price(),
            'description_cb' => [ $this, 'get_description_cb' ],
            'email_note' =>  ''
		];

		return $fees;
	}

	public function filter_options( $options ) {
		$options['subsidies_consultation'] = array(
			'title'  => 'Beratung zur Beantragung von Fördermitteln',
			'fields' => array(
				'subsidies_consultation_label'       => array(
					'title'    => 'Name',
					'type'     => 'text',
					'default'  => $this->get_default_label(),
					'required' => true,
				),
				'subsidies_consultation_description' => array(
					'title'    => 'Beschreibung',
					'type'     => 'wysiwyg',
					'default'  => $this->get_default_description(),
					'required' => true,
					'rows'     => 8,
				),
				'subsidies_consultation_price'       => array(
					'title'   => 'Preis',
					'type'    => 'number',
					'default' => 0,
					'step'    => 0.01,
				),
				'subsidies_consultation_order'       => array(
					'title'       => 'Reihenfolge',
					'description' => 'Je kleiner die Nummer, desto höher die Priorität der Zusatzoption in der Auflistung.',
					'type'        => 'number',
					'default'     => 7,
					'required'    => true,
					'min'         => 1,
					'step'        => 1,
				),
			),
		);

		return $options;
	}

	public function get_default_label() {
		return 'Energieberatung zur Beantragung von Fördermitteln (kostenlose Erstberatung)';
	}

	public function get_default_description() {
		return 'Hier können Sie sich eine kostenlose Erstberatung zur Beantragung von Fördermitteln sichern.';
	}

	public function get_default_price() {
		return 0;
	}

	public function get_default_order() {
		return 8;
	}

	/**
	 * Label callback.
	 * 
	 * @return string Label callback for fee.
	 * 
	 * @since 1.0.0
	 */
	public function get_label() {
		$settings = get_option( 'energieausweis_zusatzoptionen', array() );

		if ( isset( $settings['subsidies_consultation_label'] ) ) {
			return $settings['subsidies_consultation_label'];
		} else {
			return $this->get_default_label();
		}
	}

	/**
	 * Description callback.
	 * 
	 * @return string Description callback for fee.
	 * 
	 * @since 1.0.0
	 */
	public function get_description() {
		$settings = get_option( 'energieausweis_zusatzoptionen', array() );

		if ( isset( $settings['subsidies_consultation_description'] ) ) {
			return $settings['subsidies_consultation_description'];
		} else {
			return $this->get_default_description();
		}
	}

	public function get_description_cb() {
		echo wpautop( $this->get_description() );
	}

	public function get_price() {
		$settings = get_option( 'energieausweis_zusatzoptionen', array() );

		if ( isset( $settings['subsidies_consultation_price'] ) ) {
			return $settings['subsidies_consultation_price'];
		} else {
			return $this->get_default_price();
		}
	}

	public function get_order() {
		$settings = get_option( 'energieausweis_zusatzoptionen', array() );

		if ( isset( $settings['subsidies_consultation_order'] ) ) {
			return $settings['subsidies_consultation_order'];
		} else {
			return $this->get_default_order();
		}
	}

	/**
	 * Checks if reason is allowed for showing custom fee
	 * 
	 * @return bool True if reason matches, false if not.
	 * 
	 * @since 1.0.0
	 */
	private function reason_allowed() {
		$energy_certificate_ids = $this->get_cart_energy_certificate_ids();

		$allowed_reasons = [ 'modernisierung' ];

		foreach( $energy_certificate_ids AS $energy_certificate_id ) {
			$ec = new Energieausweis( $energy_certificate_id );

			if ( in_array( $ec->anlass, $allowed_reasons ) ) {
				return true;
			}
		}

		return false;
	}

    /**
	 * Get energy certificate id of cart.
	 *
	 * @return bool|array Energy certificate ids.
	 *
	 * @since 1.0.0
	 *
	 * @todo Have to go into a separate cart class.
	 */
	public function get_cart_energy_certificate_ids() {
		$cart_contents = EDD()->cart->get_contents();

		$energy_certificate_ids = [];
		if ( isset( $cart_contents[0] ) && isset( $cart_contents[0]['id'] ) ) {
			foreach ( $cart_contents as $cart_content ) {
				$energy_certificate_ids[] = $cart_content['id'];
			}
		}

		return $energy_certificate_ids;
	}

	/**
	 * Filter subsidies consultation email address.
	 *
	 * @param array Email addresses.
	 * @param array Payment fees.
	 * @param Energieausweis 
	 *
	 * @return array Filtered email addresses.
	 *
	 * @since 1.0.0
	 */
	public function filter_emails( array $emails, $payment_fees, $energieausweis ) : array {
		if ( ! $this->has_fee( $payment_fees ) ) {
			return $emails;
		}

		$email = 'harsche-energieberatung@web.de';

		if ( ! in_array( $email, $emails, true ) ) {
			$emails[] = $email;
		}

		$email = 'roland.harsche@immoticket24.de';

		if ( ! in_array( $email, $emails, true ) ) {
			$emails[] = $email;
		}

		return $emails;
	}

	/**
	 * Is premium bewertung selected?
	 * 
	 * @param array   Payment fees.
	 * @return bool True if was selected, false if not.
	 * 
	 * @since 1.0.0
	 */
	private function has_fee( $payment_fees ) {
		foreach ( $payment_fees AS $payment_fee ) {
			if ( $payment_fee['id'] === 'subsidies_consultation' ) {
				return true;
			}
		}

		return false;
	}
}
