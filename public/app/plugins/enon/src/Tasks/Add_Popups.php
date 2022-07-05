<?php
/**
 * Google tag manager tasks.
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

use Enon\Models\Popups\Popup_Premiumbewertung;

/**
 * Class Google_Tag_Manager
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Add_Popups implements Actions, Task {
	/**
	 * Premiumbewertung.
	 *
	 * @var Popup_Premiumbewertung
	 *
	 * @since 1.0.0
	 */
	private $popup_premiumbewertung;

	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->add_actions();
		$this->popup_premiumbewertung = new Popup_Premiumbewertung();
	}

	/**
	 * Load targeting scripts into hooks.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
			add_action( 'wp_footer', array( $this, 'modal_premiumbewertung' ) );
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
		$cart_contents     = EDD()->cart->get_contents();

		$energy_certificate_ids = false;
		if ( isset( $cart_contents[0] ) && isset( $cart_contents[0]['id'] ) ) {
			foreach ( $cart_contents as $cart_content ) {
				$energy_certificate_ids[] = $cart_content['id'];
			}
		}

		return $energy_certificate_ids;
	}

	/**
	 * Print out badge HTML.
	 *
	 * @since 1.0.0
	 */
	public function modal_premiumbewertung() {
		if ( ! edd_is_checkout() ) {
			return;
		}

		$certificate_ids = $this->get_cart_energy_certificate_ids();

		if( false === $certificate_ids ) {
			return;
		}

		if ( 0 === count( $certificate_ids ) ) {
			return;
		}

		$found_verkauf = false;

		foreach ( $certificate_ids as $certificate_id ) {
			$certificate = new \WPENON\Model\Energieausweis( $certificate_id );
			$anlass = $certificate->anlass;

			if ( 'verkauf' === $anlass ) {
				$found_verkauf = true;
			}
		}

		if ( ! $found_verkauf ) {
			return;
		}

		echo $this->popup_premiumbewertung->html();
	}

	/**
	 * Add JS.
	 *
	 * @since 1.0.0
	 */
	public function js() {
		// phpcs:ignore
		echo $this->popup_premiumbewertung->js();
	}
}
