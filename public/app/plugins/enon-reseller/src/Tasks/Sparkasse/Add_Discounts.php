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
use Awsm\WP_Wrapper\Interfaces\Task;

use Enon\Logger;
use Enon_Reseller\Models\Reseller;

use Enon\Models\Enon\Energieausweis;

/**
 * Class PluginEdd.
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller\Tasks\Sparkasse
 */
class Add_Discounts implements Task, Actions, Filters {
	/**
	 * Discount_Types
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	private $discount_types = array();

	/**
	 * Discount_Amounts
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	private $discount_amounts = array();

	/**
	 * Glory coupon codes are valid without further validation.
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	private $glory_codes = array();

	/**
	 * Allowed zip areas.
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	private $allowed_zip_areas = array();

	/**
	 * Allowed zip cities.
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	private $allowed_zip_cities = array();

	/**
	 * Loading Plugin scripts.
	 *
	 * @param Reseller $reseller Logger object.
	 * @param Logger   $logger   Logger object.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Logger $logger ) {
        $this->logger = $logger;

		$this->discount_types = array(
			'spk', // Sparkasse standard coupon.
			'web', // Sparkasse web coupon.
			'immo-hd', // Sparkasse full amount coupon.
		);

		// This coupon codes are valid without further validation.
		// @todo Should move to higher place in code.
		$this->glory_codes = array( 'ARPI221', 'CORRECT211', 'FAILURE-MT01' );

		// Use spk-c22db for testing.
		$this->discount_amounts = array(
			// Coupon codes beginning with spk.
			'spk' => array(
				'bw' => 18.55, // Amount which will be deducted on bedarfsausweis.
				'vw' => 14, // Amount which will be deducted on verbrauchsausweis.
			),
			// Coupon codes beginning with web.
			'web' => array(
				'bw' => 34.45,
				'vw' => 14,
			),
		);

		// Zip zones which are allowed for coupon codes.
		$this->allowed_zip_areas = array(
			0 => array(
				'from' => 0,
				'to'   => 0,
			),
		);

		// Specific zips which are allowed for coupon codes.
		$this->allowed_zip_cities = array(
			'heidelberg' => [ 69115, 69117, 69118, 69120, 69121, 69123, 69124, 69126 ],
			'neckargemuend' => [ 69151, 69239, 69245, 69250, 69253, 69256, 69257, 69259, 69434, 74909, 74931 ],
			'walldorf_wiesloch' => [ 68789, 69168, 69181, 69190, 69207, 69226, 69231, 69234, 69242, 69254, 74918 ],
			'schwetzingen' => [ 68723, 68775, 68782, 69214 ],
			'hockenheim' => [ 68766, 68799, 68804, 68809 ],
		);
	}

	/**
	 * Running scripts.
	 *
	 * @return mixed|void
	 *
	 * @since 1.0.0
	 */
	public function run() {
        $this->add_filters();
        $this->add_actions();
	}

	/**
	 * Adding fiilters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'edd_is_discount_valid', array( $this, 'is_valid' ), 10, 4 );
		add_filter( 'edd_get_cart_item_discounted_amount', array( $this, 'set_discount' ), 10, 4 );
		add_filter( 'edd_get_cart_discount_html', array( $this, 'cartdiscount_html' ), 10, 4 );
    }
    
    public function add_actions() {
        remove_action( 'edd_checkout_form_top', 'edd_discount_field', - 1 );
		add_action( 'edd_checkout_form_top', array( $this, 'edd_discount_field' ), - 1 );
		// add_action( 'admin_init', array( $this, 'add_new_discount_codes' ) );
    }

	/**
	 * Checks if coupon code is valid.
	 *
	 * @param bool   $is_valid      Is coupon code valid.
	 * @param int    $discount_id   Discount id.
	 * @param string $discount_code Discount code.
	 * @param string $user          User information.
	 *
	 * @return bool True if coupon code is valid.
	 *
	 * @since 1.0.0
	 */
	public function is_valid( $is_valid, $discount_id, $discount_code, $user ) {
		if ( ! $is_valid ) {
			return $is_valid;
		}

		/**
		 * Filter glory codes which are valid without further validation.
		 *
		 * @param array $glory_codes Coupon codes.
		 *
		 * @since 1.0.0
		 */
		$glory_codes = apply_filters( 'enon_reseller_glory_codes', $this->glory_codes );

		// Glory codes are valid everywhere.
		if ( in_array( $discount_code, $glory_codes ) ) {
			return true;
		}

		// Checking discount type.
		$discount_code_type = $this->get_discount_code_type( $discount_code );

		// Get current engergy certificate ids in cart.
		$energy_certificate_ids = $this->get_cart_energy_certificate_ids();

		if ( ! $discount_code_type ) {
			$debug_values = array(
				'coupon_code'        => $discount_code,
				'energy_certificates' => $energy_certificate_ids,
			);

			$this->logger->alert( 'Coupon code not valid. Cant get discount code type.', $debug_values );
			\edd_set_error( 'edd-discount-error', _x( 'Energieausweis Gutschein-Code ist nicht gültig.', 'Coupon code not within allowed zip areas.', 'enon-reseller' ) );
			return false;
		}

		// Bail out if there is no energy certificate.
		if ( ! $energy_certificate_ids ) {
			$debug_values = array(
				'coupon_code'        => $discount_code,
				'energy_certificates' => $energy_certificate_ids,
			);

			$this->logger->alert( 'Coupon code not valid. No energy certificates found.', $debug_values );
			return $is_valid;
		}

		return true;
	}

	/**
	 * Discount HTML in Cart.
	 *
	 * @since 1.0.0
	 *
	 * @param string $discount_html Discount HTML.
	 * @param string $discount_code Discount code.
	 * @param float  $rate          Discount rate.
	 * @param string $remove_url    Remove URL.
	 *
	 * @return string $discount_html Filtered Discount HTML.
	 */
	public function cartdiscount_html( $discount_html, $discount_code, $rate, $remove_url ) {
		$discount_code_type = $this->get_discount_code_type( $discount_code );
		if ( ! $discount_code_type ) {
			return $discount_html;
		}

		$energy_certificate_ids = $this->get_cart_energy_certificate_ids();

		if ( ! $energy_certificate_ids ) {
			return $discount_html;
		}

		$energieausweis_id = $energy_certificate_ids[0];
		$energieausweis    = new Energieausweis( $energieausweis_id );
		$discount_id       = edd_get_discount_id_by_code( $discount_code );
		$discount_type     = edd_get_discount_type( $discount_id );
		$discount_amount   = $this->get_discount_amount( $discount_code, $energieausweis->get_type() );

		if ( null === $discount_amount ) {
			$discount_amount = edd_get_discount_amount( $discount_id );
		}

		$rate              = edd_format_discount_rate( $discount_type, $discount_amount );

		$discount_html  = "<span class=\"edd_discount\">\n";
		$discount_html .= "<span class=\"edd_discount_rate\">$discount_code&nbsp;&ndash;&nbsp;$rate</span>\n";
		$discount_html .= "<a href=\"$remove_url\" data-code=\"$discount_code\" class=\"edd_discount_remove\"></a>\n";
		$discount_html .= "</span>\n";

		return $discount_html;
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
			foreach ( $cart_contents AS $cart_content ) {
				$energy_certificate_ids[] = $cart_content['id'];
			}
		}

		return $energy_certificate_ids;
	}

	/**
	 * Set discount height.
	 *
	 * @since 1.0.0
	 *
	 * @param float $discount_price Discount price.
	 * @param array $discounts      Discounts.
	 * @param array $item           Item to discount.
	 * @param float $price          Price.
	 *
	 * @return float $discount_price Discounted price.
	 */
	public function set_discount( $discount_price, $discounts, $item, $price ) {
		$energieausweis_id = $item['id'];
		$energieausweis    = new Energieausweis( $energieausweis_id );
		$discount_code     = $this->find_discount_code( $discounts );

		if ( ! $discount_code ) {
			return $discount_price;
		}

		$discount_type   = $this->get_discount_code_type( $discount_code );

		if ( 'immo-hd' === $discount_type ) {
			return $discount_price;
		}

		$discount_price = $price - $this->get_discount_amount( $discount_code, $energieausweis->get_type() );

		$debug_values = array(
			'energy_certificate_id' => $energieausweis_id,
			'discount_code'         => $discount_code,
			'discount_price'        => $discount_price,
		);

		$this->logger->notice( 'Setting special sparkasse discount.', $debug_values );

		return $discount_price;
	}

	/**
	 * Get discount price.
	 *
	 * @since 1.0.0
	 *
	 * @param string $discount_code     Discount Code.
	 * @param string $engieausweis_type Energieausweis type.
	 *
	 * @return float $discount_amount Discount Amount.
	 */
	private function get_discount_amount( $discount_code, $engieausweis_type ) {
		$discount_type   = $this->get_discount_code_type( $discount_code );
		$discount_amount = $this->discount_amounts[ $discount_type ][ $engieausweis_type ];

		return $discount_amount;
	}

	/**
	 * Get discount type of a discount code.
	 *
	 * @since 1.0.0
	 *
	 * @param string $discount_code Discount code.
	 * @return bool|mixed $discount_type Discount type if found or false.
	 */
	private function get_discount_code_type( $discount_code ) {
		foreach ( $this->discount_types as $discount_type ) {
			$discount_type_length = strlen( $discount_type );

			if ( substr( $discount_code, 0, $discount_type_length ) === $discount_type ) {
				return $discount_type;
			}
		}

		return false;
	}

	/**
	 * Finding sparkasse discount code.
	 *
	 * @since 1.0.0
	 *
	 * @param array $discount_codes Discount codes.
	 * @return bool|string $discount_code Discount code if found or false.
	 */
	private function find_discount_code( $discount_codes ) {
		foreach ( $discount_codes as $discount_code ) {
			foreach ( $this->discount_types as $discount_type ) {
				$discount_prefix_length = strlen( $discount_type ) + 1;
				$discount_prefix = $discount_type . '-';

				if ( substr( $discount_code, 0, $discount_prefix_length ) === $discount_prefix ) {
					return $discount_code;
				}
			}
		}

		return false;
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

	public function add_new_discount_codes() {
		if( ! array_key_exists('add-edd-discount', $_GET) ) {
			return;
		}

		$prefix = 'immo-es';
		$num    = 100;
		$code_length = 5;

		for( $i = 0; $i < $num; $i++ ) {
			$hash = substr( md5( $prefix . $i . time() ), 0, $code_length );
			$discount_code = $prefix . '-' . $hash;
			$name = 'Sparkasse Esslingen ' . ( $i+1 );

			$code_args = [
				'name' => $name,
				'code' => $discount_code,
				'type' => 'percent',
				'amount' => 100,
				'uses' => 0,
				'use_once' => 1,
				'max' => 1,
				'edd-max-uses'
			];

			edd_store_discount( $code_args );

			echo $name . ',' . $discount_code . PHP_EOL;
		}
		exit;
	}
}
