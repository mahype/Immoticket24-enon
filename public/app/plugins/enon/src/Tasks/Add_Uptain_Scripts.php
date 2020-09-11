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
use WPENON\Model\Energieausweis;

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
			$ec = $this->ec();

			if ( ! $ec ) {
				return;
			}

			if( ! $this->contact_accepeted( $ec ) ) {
				return;
			}

			$email = $ec->wpenon_email;

			echo $this->data_tag( [
				'email' => $email,
				'scv'   => $this->ec_price( $ec )
			] );
		}
	}

	/**
	 * Get current ec (works on funnel pages)
	 * 
	 * @return Energieausweis Energy certificate object.
	 * 
	 * @since 2020-09-11
	 */
	private function ec() {
		if( ! Enon_Location::funnel() ) {
			return false;
		}

		$ec_manager = EnergieausweisManager::instance();
		$ec = $ec_manager::getEnergieausweis();

		if ( ! $ec ) {
			return false;
		}

		return $ec;
	}

	/**
	 * Get price of ec.
	 * 
	 * @return float Price
	 * 
	 * @since 2020-09-11
	 */
	private function ec_price( $ec ) : float {
		switch( $ec->type ) {
			case 'bw':
				return 89.95;
				break;
			case 'vw':
				return 39.95;
				break;
			default:
				return 0;
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
