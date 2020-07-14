<?php
/**
 * Loading sparkasse discount code tasks.
 *
 * @category Class
 * @package  Enon\Misc
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Sparkasse;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Filters;

use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Tools\Logger_Trait;
use Enon_Reseller\Models\Reseller;

use Enon\Models\Enon\Energieausweis;

/**
 * Class PluginEdd.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class Sparkasse_Setup_Edd extends Sparkasse_Frontend_Task implements Actions, Filters {
	/**
	 * Loading Plugin scripts.
	 *
	 * @param Reseller $reseller Logger object.
	 * @param Logger $logger Logger object.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Reseller $reseller, Logger $logger ) {
		parent::__construct( $reseller, $logger );

		if ( ! $this->can_run() ) {
			return;
		}
	}

	/**
	 * Running scripts.
	 *
	 * @return mixed|void
	 *
	 * @since 1.0.0
	 */
	public function run() {
		if ( ! $this->can_run() ) {
			return;
		}

		$this->add_filters();
		$this->add_actions();
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		remove_action( 'edd_checkout_form_top', 'edd_discount_field', - 1 );
		add_action( 'edd_checkout_form_top', array( $this, 'edd_discount_field' ), - 1 );
	}

	/**
	 * Adding discount code field without hiding before.
	 *
	 * @since 1.0.0
	 */
	public function edd_discount_field() {
		if ( isset( $_GET['payment-mode'] ) && edd_is_ajax_disabled() ) {
			return;
		}

		if ( ! edd_is_checkout() ) {
			return;
		}

		if ( edd_has_active_discounts() && edd_get_cart_total() ) :

			$color = edd_get_option( 'checkout_color', 'blue' );
			$color = ( $color == 'inherit' ) ? '' : $color;
			$style = edd_get_option( 'button_style', 'button' );
			?>
			<fieldset id="edd_discount_code">
				<p id="edd-discount-code-wrap-sparkasse" class="edd-cart-adjustment">
					<label class="edd-label" for="edd-discount">
						<?php _e( 'Discount', 'easy-digital-downloads' ); ?>
					</label>
					<span
						class="edd-description"><?php _e( 'Enter a coupon code if you have one.', 'easy-digital-downloads' ); ?></span>
					<span class="edd-discount-code-field-wrap">
					<input class="edd-input" type="text" id="edd-discount" name="edd-discount"
					       placeholder="<?php _e( 'Enter discount', 'easy-digital-downloads' ); ?>"/>
					<input type="submit" class="edd-apply-discount edd-submit <?php echo $color . ' ' . $style; ?>"
					       value="<?php echo _x( 'Apply', 'Apply discount at checkout', 'easy-digital-downloads' ); ?>"/>
				</span>
					<span class="edd-discount-loader edd-loading" id="edd-discount-loader" style="display:none;"></span>
					<span id="edd-discount-error-wrap" class="edd_error edd-alert edd-alert-error" aria-hidden="true"
					      style="display:none;"></span>
				</p>
			</fieldset>
		<?php
		endif;
	}
}
