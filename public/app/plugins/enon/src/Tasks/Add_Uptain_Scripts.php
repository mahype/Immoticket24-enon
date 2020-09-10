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

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;
use Enon\Models\Enon\Enon_Location;
use WPENON\Model\EnergieausweisManager;

/**
 * Class Google_Tag_Manager
 *
 * @package awsmug\Enon\Tools
 *
 * @since 2010-09-10
 */
class Add_Uptain_Scripts implements Task, Actions {
	/**
	 * @return mixed|void
	 */
	public function run() {
		$this->add_actions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 2010-09-10
	 */
	public function add_actions() {
		add_action( 'wp_footer', [ $this, 'controller' ] );
	}

	/**
	 * Controller
	 *
	 * @since 2010-09-10
	 */
	public function controller() {
		if ( Enon_Location::overview() || Enon_Location::edit() || Enon_Location::cart() ) {
			$ec_manager = EnergieausweisManager::instance();
			$ec = $ec_manager::getEnergieausweis();

			if ( ! $ec ) {
				return;
			}

			if( ! $this->contact_accepeted( $ec ) ) {
				return;
			}

			$type = $ec->type;
			$email = $ec->wpenon_email;

			switch( $type ) {
				case 'bw':
					$price = 89.95;
					break;
				case 'vw':
					$price = 39.95;
					break;
				default:
					return;
			}

			echo self::data_tag( [
				'email' => $email,
				'scv'   => $price
			] );
		}
	}

	/**
	 * Checks if user wants to be contacted.
	 * 
	 * @param Energieausweis $ec Energy certificate object.
	 * @return Bool True if user wants to be contacted, false if not.
	 * 
	 * @since 2020-09-10
	 */
	private function contact_accepeted( $ec ) : bool {
		return (bool) get_post_meta( $ec->ID, 'contact_acceptance', '1' );
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
		$tag.= '<script type="text/javascript" src="https://app.uptain.de/js/uptain.js?x=hnne9BonaNH0mRA0" async></script>';

		return $tag;
	}
}
