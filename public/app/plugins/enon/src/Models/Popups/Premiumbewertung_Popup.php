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

/**
 * Class Premiumbewertung_Popup.
 *
 * @since 1.0.0
 */
class Premiumbewertung_Popup extends Popup_Component {
	/**
	 * Premiumbewertung_Popup constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->set_trigger( 'document', 'wpenon.update_payment_method' );
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
			'Premium Bewertung',
			'Sichern Sie sich jetzt noch schnell eine kostenlose vor-Ort-Bewertung Ihrer Immobilie, damit Sie beim Verkauf kein Geld verschenken.',
			'Energieausweis inklusive kostenloser Immobilien-Bewertung bestellen',
			'Energieausweis ohne Bewertung bestellen',
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
	public function js_action_on_trigger(): string {
		ob_start()
		?>
		var premium_bewertung_is_checked = $('#edd_custom_fee_premium_bewertung').is(":checked");
		if ( premium_bewertung_is_checked ) {
		    return;
		}
		<?php

		return ob_get_clean();
	}

	/**
	 * Returns JS code of component.
	 *
	 * @return string JS content.
	 *
	 * @since 1.0.0
	 */
	public function js_action(): string {
		ob_start()
		?>
		alert

		<?php

		return ob_get_clean();
	}
}
