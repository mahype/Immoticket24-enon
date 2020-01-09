<?php

namespace Enon\Misc\Tasks\Plugins;

use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;

use Enon\Models\Enon\Energieausweis;
use Enon\Traits\Logger as Logger_Trait;
use Enon\Logger;
use Enon\Reseller\Models\Reseller;

/**
 * Class PluginEdd.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class TaskEddSparkasseDiscounts implements Task, Filters
{
	use Logger_Trait;

	/**
	 * Discount_Types
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $discountTypes = array();

	/**
	 * Discount_Amounts
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $discountAmounts = array();

	/**
	 * Loading Plugin scripts.
	 *
	 * @since 1.0.0
	 *
	 * @param Reseller $reseller Reseller object.
	 * @param Logger   $logger   Logger object.
	 */
	public function __construct( Logger $logger ) {
		$this->logger = $logger;

		$this->discountTypes = [ 'spk', 'web' ];

		$this->discountAmounts = array(
			'spk' => array(
				'bw' => 50,
				'vw' => 28
			),
			'web' => array(
				'bw' => 34.45,
				'vw' => 14
			)
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
		add_filter( 'edd_get_cart_item_discounted_amount', [ $this, 'setDiscount' ], 10, 4 );
		add_filter( 'edd_get_cart_discount_html', [ $this, 'cartDiscountHtml' ], 10, 4 );
	}

	/**
	 * Discount HTML in Cart.
	 *
	 * @since 1.0.0
	 *
	 * @param string $discountHtml Discount HTML.
	 * @param string $discountCode Discount code.
	 * @param float  $rate         Discount rate.
	 * @param string $remove_url   Remove URL.
	 *
	 * @return string $discountHtml Filtered Discount HTML.
	 */
	public function cartDiscountHtml( $discountHtml, $discountCode, $rate, $remove_url ) {
		$cartContents = EDD()->cart->get_contents();

		if( ! $this->getDiscountType( $discountCode ) || ! isset( $cartContents[0] )  ) {
			return $discountHtml;
		}

		$energieausweisId = $cartContents[0]['id'];
		$energieausweis = new Energieausweis( $energieausweisId );

		$discountId = edd_get_discount_id_by_code( $discountCode );
		$discountType = edd_get_discount_type( $discountId );
		$rate = edd_format_discount_rate( $discountType, $this->getDiscountAmount( $discountCode, $energieausweis->getType() ) );

		$discountHtml = "<span class=\"edd_discount\">\n";
			$discountHtml .= "<span class=\"edd_discount_rate\">$discountCode&nbsp;&ndash;&nbsp;$rate</span>\n";
			$discountHtml .= "<a href=\"$remove_url\" data-code=\"$discountCode\" class=\"edd_discount_remove\"></a>\n";
		$discountHtml .= "</span>\n";

		return $discountHtml;
	}

	/**
	 * Set discount height.
	 *
	 * @since 1.0.0
	 *
	 * @param float $discountedPrice
	 * @param array $discounts
	 * @param array $item
	 * @param float $price
	 *
	 * @return float $discountedPrice Discounted price.
	 */
	public function setDiscount( $discountedPrice, $discounts, $item, $price ) {
		$energieausweis_id = $item['id'];
		$energieausweis = new Energieausweis( $energieausweis_id );

		if( ! $discountCode = $this->findDiscountCode( $discounts ) ) {
			return $discountedPrice;
		}

		$discountedPrice = $price - $this->getDiscountAmount( $discountCode, $energieausweis->getType() );

		return $discountedPrice;
	}

	/**
	 * Get discount price.
	 *
	 * @since 1.0.0
	 *
	 * @param string $discountCode     Discount Code.
	 * @param string $engieausweisType Energieausweis type.
	 *
	 * @return float $discountAmount Discount Amount.
	 */
	private function getDiscountAmount( $discountCode, $engieausweisType ) {
		$discountType = $this->getDiscountType( $discountCode );
		$discountAmount = $this->discountAmounts[ $discountType ][ $engieausweisType ];

		return $discountAmount;
	}

	/**
	 * Get discount type of a discount code.
	 *
	 * @since 1.0.0
	 *
	 * @param string $discountCode Discount code.
	 * @return bool|mixed $discountType Discount type if found or false.
	 */
	private function getDiscountType( $discountCode ) {
		foreach( $this->discountTypes AS $discountType ) {
			$discountTypeLength = strlen( $discountType );

			if( substr( $discountCode, 0 , $discountTypeLength ) === $discountType ) {
				return $discountType;
			}
		}

		return false;
	}

	/**
	 * Finding sparkasse discount code.
	 *
	 * @since 1.0.0
	 *
	 * @param array        $discountCodes Discount codes.
	 * @return bool|string $discountCode Discount code if found or false.
	 */
	private function findDiscountCode( $discountCodes ) {
		foreach( $discountCodes AS $discountCode ) {
			foreach( $this->discountTypes AS $discountType ) {
				$discountPrefixLength = strlen( $discountType ) + 1;
				$discountPrefix = $discountType . '-';

				if( substr( $discountCode, 0, $discountPrefixLength ) === $discountPrefix ) {
					return $discountCode;
				}
			}
		}

		return false;
	}
}
