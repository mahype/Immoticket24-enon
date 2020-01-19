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

namespace Enon\Edd\Tasks;

use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;

use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Traits\Logger as Logger_Trait;
use Enon\Reseller\Models\Reseller;

use Enon\Models\Enon\Energieausweis;

/**
 * Class PluginEdd.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class Sparkasse_Discounts implements Task, Filters {

	use Logger_Trait;

	/**
	 * Discount_Types
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $discount_types = array();

	/**
	 * Discount_Amounts
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $discount_amounts = array();

	/**
	 * Loading Plugin scripts.
	 *
	 * @since 1.0.0
	 *
	 * @param Logger $logger Logger object.
	 */
	public function __construct( Logger $logger ) {
		$this->logger = $logger;

		$this->discount_types = array( 'spk', 'web' );

		$this->discount_amounts = array(
			'spk' => array(
				'bw' => 50,
				'vw' => 28,
			),
			'web' => array(
				'bw' => 34.45,
				'vw' => 14,
			),
		);
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	public function run() {
		$this->add_filters();
	}

	/**
	 * Adding fiilters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'edd_get_cart_item_discounted_amount', array( $this, 'set_discount' ), 10, 4 );
		add_filter( 'edd_get_cart_discount_html', array( $this, 'cartdiscount_html' ), 10, 4 );
	}

	/**
	 * Discount HTML in Cart.
	 *
	 * @since 1.0.0
	 *
	 * @param string $discount_html Discount HTML.
	 * @param string $discount_code Discount code.
	 * @param float  $rate         Discount rate.
	 * @param string $remove_url   Remove URL.
	 *
	 * @return string $discount_html Filtered Discount HTML.
	 */
	public function cartdiscount_html( $discount_html, $discount_code, $rate, $remove_url ) {
		$cart_contents = EDD()->cart->get_contents();

		if ( ! $this->get_discount_type( $discount_code ) || ! isset( $cart_contents[0] ) ) {
			return $discount_html;
		}

		$energieausweis_id = $cart_contents[0]['id'];
		$energieausweis    = new Energieausweis( $energieausweis_id );
		$discount_id       = edd_get_discount_id_by_code( $discount_code );
		$discount_type     = edd_get_discount_type( $discount_id );
		$rate              = edd_format_discount_rate( $discount_type, $this->get_discount_amount( $discount_code, $energieausweis->get_type() ) );

		$discount_html  = "<span class=\"edd_discount\">\n";
		$discount_html .= "<span class=\"edd_discount_rate\">$discount_code&nbsp;&ndash;&nbsp;$rate</span>\n";
		$discount_html .= "<a href=\"$remove_url\" data-code=\"$discount_code\" class=\"edd_discount_remove\"></a>\n";
		$discount_html .= "</span>\n";

		return $discount_html;
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

		$discount_price = $price - $this->get_discount_amount( $discount_code, $energieausweis->get_type() );

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
		$discount_type   = $this->get_discount_type( $discount_code );
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
	private function get_discount_type( $discount_code ) {
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
}
