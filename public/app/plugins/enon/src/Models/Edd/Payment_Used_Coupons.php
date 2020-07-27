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

use Awsm\WP_Wrapper\Interfaces\Actions;

/**
 * Class Payment_Used_Coupons
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Taks\Plugins
 */
class Payment_Used_Coupons implements Actions {

	public function add_actions() {
		add_action( 'edd_payment_advanced_filters_after_fields', array( $this, 'add_coupon_filed' ) );
	}

	public function add_coupon_filed(){
		$coupon = '';
		?>
		<span>
			<label for="coupon"><?php _e( 'End Date:', 'easy-digital-downloads' ); ?></label>
			<input type="text" id="coupon" name="coupon" class="edd_coupon" value="<?php echo $coupon; ?>" placeholder="Coupon"/>
		</span>
		<?php
	}
}
