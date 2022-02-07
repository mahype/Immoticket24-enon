<?php
/**
 * Add scripts.
 *
 * @category Class
 * @package  Enon\Misc\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks\Scripts;

use Enon\Models\Enon\Enon_Location;
use Enon\Models\Scripts\Script_Loader;

/**
 * Class Google_Tag_Manager
 *
 * @package awsmug\Enon\Tools
 *
 * @since 2020-09-10
 */
class Add_Uptain_Scripts extends Script_Loader {
	/**
	 * Funnel page.
	 *
	 * @since 2020-09-10
	 */
	protected function ec_funnel_contacting_allowed() {
		echo $this->data_tag( [
			'email'   => $this->ec_mail(),
			'scv'     => $this->ec_price(),
			'success' => $this->is_success() ? 1 : 0,
		] );
	}

	/**
	 * Success page
	 * 
	 * @since 2020-09-16
	 */
	protected function is_success() {
		global $edd_receipt_args;

		if( ! Enon_Location::success() || ! array_key_exists( 'id',$edd_receipt_args ) ) {
			return false;
		}

		$payment = get_post( $edd_receipt_args['id'] );
		$status = edd_get_payment_status( $payment );

		switch( $status ) {
			case 'publish':
			case 'pending':
				return true;
				break;
			case 'failed':
			case 'abandoned':
			case 'revoked':
			case 'processing':
			default:
				return false;
				break;
		}
	}

	/**
	 * Script files.
	 * 
	 * @since 2020-09-10
	 */
	protected function script_files() : array {
		return array('https://app.uptain.de/js/uptain.js?x=hnne9BonaNH0mRA0');
	}

	/**
	 * Create uptain data tag.
	 * 
	 * @param array $values Values to add to tag.
	 * @return string Data tag HTML.
	 * 
	 * @since 2020-09-10
	 */
	private function data_tag( array $values ) : string {
		$tag = '<div id="__up_data_qp"';

		/**
		 * Data structure
		 *
		 * data-scv=”{{Warenkorbwert}}
		 * data-ordernumber=”{{Bestellnummer}}
		 * data-success=”{{1 oder 0}}
		 * data-email="{{E-Mail}}"
		 * data-firstname=”{{Vorname}}
		 * data-lastname=”{{Nachname}}
		 * data-usedvoucher=”{{Gutscheincode}}
		 * data-customergroup=”{{Kundengruppe}}
		 * data-revenue={{Kundenumsatz}}
		 */
		foreach( $values AS $key => $value ) {
			$tag.= ' data-' . $key . '="' . $value . '"';
		}

		$tag.= '></div>';

		return $tag;
	}
}
