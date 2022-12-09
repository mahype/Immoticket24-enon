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

namespace Enon_Reseller\Tasks\EVM;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;
use Enon\Logger;
use Enon_Reseller\Models\Reseller;

use Enon\Models\Enon\Energieausweis;
use WPENON\Model\Energieausweis as ModelEnergieausweis;

/**
 * Class PluginEdd.
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller\Tasks\Sparkasse
 */
class Add_Discounts implements Task, Filters {
	/**
	 * Discount codes
	 * 
	 * @var array  
	 * 
	 * @since 1.0.0 
	 */
	private array $discount_codes;

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

		$this->discount_codes = [
			[
				'code'    => 'Entdecker-VA5',
				'ec_type' => 'vw'
			],
			[
				'code'    => 'Entdecker-BA20',
				'ec_type' => 'bw'
			],
		];
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
	}

	/**
	 * Adding fiilters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'edd_is_discount_valid', array( $this, 'is_valid' ), 10, 4 );
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

		// Checking discount type.
		$discount_code_type = $this->get_discount_code_type( $discount_code );

		// Get current engergy certificate ids in cart.
		$energy_certificate_ids = $this->get_cart_energy_certificate_ids();

		if ( empty( $discount_code_type ) ) {
			return $is_valid;
		}

		// Bail out if there is no energy certificate.
		if ( ! $energy_certificate_ids ) {
			$debug_values = array(
				'coupon_code'        => $discount_code,
				'energy_certificates' => $energy_certificate_ids,
			);

			$this->logger->alert( 'Coupon code not valid. No energy certificates found in basket.', $debug_values );
			return $is_valid;
		}

		if( count( $energy_certificate_ids ) > 1 ) 
		{
			\edd_set_error( 'edd-discount-error', __( 'Energieausweis Gutschein-Code kann nur auf einen einzelnen Energieausweis angewendet werden.', 'enon-reseller' ) );
			return false;
		}

		$energy_certificate_id = $energy_certificate_ids[0];
		$energy_certificate    = new ModelEnergieausweis( $energy_certificate_id );

		switch ( $this->get_discount_code_type( $discount_code ) )
		{
			case 'vw':
				if( $energy_certificate->mode != 'v' )
				{
					\edd_set_error( 'edd-discount-error', __( 'Der Gutscheincode lässt sich nur auf einen Verbrauchsausweis anwenden.', 'enon-reseller' ) );
					return false;
				}
				return true;
			case 'bw':
				if( $energy_certificate->mode != 'b' )
				{
					\edd_set_error( 'edd-discount-error', __( 'Der Gutscheincode lässt sich nur auf einen Bedarfsausweis anwenden.', 'enon-reseller' ) );
					return false;
				}
				return true;
			default:
				return $is_valid;
		}
	}

	/**
	 * Get discount code type
	 * 
	 * @param string Discount code
	 * 
	 * @return string Discount code type (vw, or bw)
	 * 
	 * @since 1.0.0 
	 */
	private function get_discount_code_type( $discount_code )
	{
		foreach( $this->discount_codes AS $discount_code_values )
		{
			if( $discount_code_values['code'] == $discount_code )
			{
				return $discount_code_values['ec_type'];
			}
		}
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
		$cart_contents = EDD()->cart->get_contents();

		$energy_certificate_ids = false;
		if ( isset( $cart_contents[0] ) && isset( $cart_contents[0]['id'] ) ) {
			foreach ( $cart_contents AS $cart_content ) {
				$energy_certificate_ids[] = $cart_content['id'];
			}
		}

		return $energy_certificate_ids;
	}
}
