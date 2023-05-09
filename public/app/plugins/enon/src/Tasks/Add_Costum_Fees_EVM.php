<?php
/**
 * EVM Zusatzoption
 *
 * @category Class
 * @package  Enon\Models\Popups
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;
use WPENON\Model\Energieausweis;

/**
 * Class Premiumbewertung_Popup.
 *
 * @since 1.0.0
 */
class Add_Costum_Fees_EVM implements Filters, Task {
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
		if ( ( ! $this->postcoce_allowed() || ! $this->reason_allowed() ) && edd_is_checkout() ) {
			return $fees;
		}

		$fees[] = [
			'id' => 'check_evm',
            'label' => $this->get_label(),
            'amount' => $this->get_price(),
            'description_cb' => [ $this, 'get_description_cb' ],
            'email_note' =>  ''
		];

		return $fees;
	}

	public function filter_options( $options ) {
		$options['check_evm'] = array(
			'title'  => 'Überprüfung Energieverträge',
			'fields' => array(
				'check_evm_label'       => array(
					'title'    => 'Name',
					'type'     => 'text',
					'default'  => $this->get_default_label(),
					'required' => true,
				),
				'check_evm_description' => array(
					'title'    => 'Beschreibung',
					'type'     => 'wysiwyg',
					'default'  => $this->get_default_description(),
					'required' => true,
					'rows'     => 8,
				),
				'check_evm_price'       => array(
					'title'   => 'Preis',
					'type'    => 'number',
					'default' => 0,
					'step'    => 0.01,
				),
				'check_evm_order'       => array(
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
		return 'NEU! Überprüfung Energieverträge auf Einsparpotenzial Sparen Sie Energiekosten.';
	}

	public function get_default_description() {
		return 'Mit unserem Partner vor Ort, der EVM, überprüfen wir Ihre Energieverträge, ob sich hierbei Einsparungen für Sie ermöglichen.';
	}

	public function get_default_price() {
		return 0;
	}

	public function get_default_order() {
		return 7;
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

		if ( isset( $settings['check_evm_label'] ) ) {
			return $settings['check_evm_label'];
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

		if ( isset( $settings['check_evm_description'] ) ) {
			return $settings['check_evm_description'];
		} else {
			return $this->get_default_description();
		}
	}

	public function get_description_cb() {
		echo wpautop( $this->get_description() );
	}

	public function get_price() {
		$settings = get_option( 'energieausweis_zusatzoptionen', array() );

		if ( isset( $settings['check_evm_price'] ) ) {
			return $settings['check_evm_price'];
		} else {
			return $this->get_default_price();
		}
	}

	public function get_order() {
		$settings = get_option( 'energieausweis_zusatzoptionen', array() );

		if ( isset( $settings['check_evm_order'] ) ) {
			return $settings['check_evm_order'];
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

		$allowed_reasons = [ 'modernisierung', 'vermietung' ];

		foreach( $energy_certificate_ids AS $energy_certificate_id ) {
			$ec = new Energieausweis( $energy_certificate_id );

			if ( in_array( $ec->anlass, $allowed_reasons ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if postcode is allowed for showing custom fee
	 * 
	 * @return bool True if postcode matches, false if not.
	 * 
	 * @since 1.0.0
	 */
	private function postcoce_allowed() {
		$energy_certificate_ids = $this->get_cart_energy_certificate_ids();

		if( empty( $energy_certificate_ids ) ) {
			return false;
		}

		$allowed_postcodes = [ 53, 54, 55, 56, 57, 58, 42 ];

		foreach( $energy_certificate_ids AS $energy_certificate_id ) {
			$ec = new Energieausweis( $energy_certificate_id );
			$postcode_compare = substr( $ec->adresse_plz, 0, 2 );

			if ( in_array( $postcode_compare, $allowed_postcodes ) ) {
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

		$energy_certificate_ids = false;
		if ( isset( $cart_contents[0] ) && isset( $cart_contents[0]['id'] ) ) {
			foreach ( $cart_contents as $cart_content ) {
				$energy_certificate_ids[] = $cart_content['id'];
			}
		}

		return $energy_certificate_ids;
	}
}
