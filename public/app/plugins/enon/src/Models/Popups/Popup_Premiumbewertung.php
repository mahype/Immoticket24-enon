<?php
/**
 * Premiumbewerung Popup.
 *
 * @category Class
 * @package  Enon\Models\Popups
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Popups;

use Awsm\WP_Wrapper\Interfaces\Filters;

/**
 * Class Premiumbewertung_Popup.
 *
 * @since 1.0.0
 */
class Popup_Premiumbewertung extends Popup_Component implements Filters {
	/**
	 * Premiumbewertung_Popup constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct( 'modal_premiumbewertung' );
		$this->add_filters();
	}

	/**
	 * Add filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'eddcf_custom_fees', [ $this, 'filter_fees' ] );
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
		$discounts = EDD()->cart->get_discounts();

		foreach ( $discounts as $discount ) {
			if ( 'xLswR42' === $discount ) {
				$fees = $this->remove_premiumbewertung_from_fees( $fees );
				break;
			}
		}

		return $fees;
	}

	/**
	 * Remove Premiumbewertung from fees.
	 *
	 * @param array $fees Fees.
	 *
	 * @return array Fees without 'premiumbewertung'.
	 * @since 1.0.0
	 */
	private function remove_premiumbewertung_from_fees( $fees ) {
		foreach ( $fees as $key => $fee ) {
			if ( 'premium_bewertung' === $fee['id'] ) {
				unset( $fees[ $key ] );
				return $fees;
			}
		}
	}

	/**
	 * Ekomi Badge.
	 *
	 * @return string Badge HTML.
	 *
	 * @since 1.0.0
	 */
	public function html() : string {
		$html = parent::html_popup(
			'Jetzt zusätzlich Gratis-Wertermittlung Ihrer Immobilie sichern',
			'Sichern Sie sich jetzt noch schnell eine kostenlose vor-Ort-Bewertung Ihrer Immobilie, damit Sie beim Verkauf kein Geld verschenken.',
			'Energieausweis mit kostenloser Immobilien-Wertermittlung bestellen',
			'Energieausweis ohne Immobilien-Wertermittlung bestellen',
			plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'src/Assets/Img/Popups/Premium-Bewertung.jpg'
		);

		return $html;
	}

	/**
	 * Returns JS code of component.
	 *
	 * @return string JS content.
	 *
	 * @since 1.0.0
	 */
	public function js_action(): string {
		return '';
	}

	/**
	 * Returns JS code of component.
	 *
	 * @return string JS content.
	 *
	 * @since 1.0.0
	 */
	public function js(): string {
		return '';
	}
}
