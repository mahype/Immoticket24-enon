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
class Popup_Premiumbewertung extends Popup_Component {
	/**
	 * Premiumbewertung_Popup constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct( 'modal_premiumbewertung' );
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
