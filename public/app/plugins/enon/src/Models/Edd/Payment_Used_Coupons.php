<?php
/**
 * Wrapper class for Payment fIlter coupons functions.
 *
 * @category Class
 * @package  Enon\Models\Edd
 * @author   Rene Reimann
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Edd;

use Awsm\WP_Wrapper\Assets\Admin_Script;
use Awsm\WP_Wrapper\Interfaces\Actions;

/**
 * Class Payment_Used_Coupons
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Taks\Plugins
 */
class Payment_Used_Coupons  implements Actions {

	public function add_actions() {
		add_action( 'edd_payment_advanced_filters_row', array( $this, 'add_coupon_filed' ) );
	}

	public function add_coupon_filed(){
		$bundle_path = plugin_dir_url( __DIR__ . '/../../Assets/Dist/bundle.js') . 'bundle.js';

		$assets = new Admin_Script('payment_used_coupons', $bundle_path, [], '1.0.0', true);
		$assets->enqueue();


		$coupon = '';
		?>
		<p class="coupon-search-box" style="top:37px">
			<label class="screen-reader-text" for="coupon"></label>
			<input type="search" id="<coupon" name="s_coupon" value="coupon" placeholder="Coupon"/>
			<?php submit_button( 'Coupon suchen', 'button', false, false, array('ID' => 'search-coupon-submit') ); ?><br/>
		</p>
		<?php
	}
}
