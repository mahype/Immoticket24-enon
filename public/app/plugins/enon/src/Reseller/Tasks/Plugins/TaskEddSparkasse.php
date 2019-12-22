<?php

namespace Enon\Reseller\Tasks\Plugins;

use Awsm\WPWrapper\BuildingPlans\Filters;
use Awsm\WPWrapper\BuildingPlans\Task;

use Enon\Models\Enon\Energieausweis;
use Enon\Traits\Logger as LoggerTrait;
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
	use LoggerTrait;

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
	public function __construct( Reseller $reseller, Logger $logger ) {
		$this->reseller = $reseller;
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
		$this->addFilters();
	}

	/**
	 * Adding fiilters.
	 *
	 * @since 1.0.0
	 */
	public function addFilters() {
		add_filter( 'edd_get_cart_item_discounted_amount', [ $this, 'setDiscount' ] );
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

		$discountedPrice = $this->getDiscount( $discountCode, $price, $energieausweis->getType() );

		return $discountedPrice;
	}

	/**
	 * Get discount price.
	 *
	 * @since 1.0.0
	 *
	 * @param string $discountCode     Discount Code.
	 * @param float  $price	           Price.
	 * @param string $engieausweisType Energieausweis type.
	 *
	 * @return mixed
	 */
	private function getDiscount( $discountCode, $price, $engieausweisType ) {
		$discountType = $this->getDiscountType( $discountCode );
		$discountAmount = $this->discountAmounts[ $discountType ][ $engieausweisType ];

		$price -= $discountAmount;

		return $price;
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
