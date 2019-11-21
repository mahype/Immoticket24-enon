<?php

namespace Enon\Whitelabel\WordPress\Plugins;

use Awsm\WPWrapper\BuildingPlans\Actions;
use Awsm\WPWrapper\BuildingPlans\Task;
use Enon\Traits\Logger as LoggerTrait;
use Enon\Logger;
use Enon\Whitelabel\Reseller;

/**
 * Managing ACF Fields.
 *
 * @since 1.0.0
 *
 * @package Enon\Whitelabel\WordPress
 */
class ACF implements Task, Actions
{
	use LoggerTrait;

	/**
	 * AffiliateWP constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Reseller $reseller Reseller object.
	 * @param Logger $logger Logger object.
	 */
	public function __construct( Logger $logger )
	{
		$this->logger = $logger;
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run()
	{
		if ( ! self::isActivated() ) {
			$this->logger->warning('Advanced custom fields seems not to be activated.');
			return;
		}

		$this->addActions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function addActions()
	{
		add_action( 'acf/init', [ $this, 'addFieldGroup' ] );
	}

	/**
	 * Adding field group.
	 *
	 * @since 1.0.0
	 */
	public function addFieldGroup() {
		acf_add_local_field_group(array(
			'key' => 'reseller',
			'title' => 'Reseller',
			'fields' => array (
				array (
					'key' => 'company',
					'label' => __( 'Company', 'enon' ),
					'name' => 'company',
					'type' => 'text',
				),
				array (
					'key' => 'name',
					'label' => __( 'Name', 'enon' ),
					'name' => 'name',
					'type' => 'text',
				),
				array (
					'key' => 'email',
					'label' => __( 'Email', 'enon' ),
					'name' => 'email',
					'type' => 'email',
				),
				array (
					'key' => 'token',
					'label' => __( 'Token', 'enon' ),
					'name' => 'token',
					'type' => 'text',
				)
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'reseller',
					),
				),
			),
		));

		acf_add_local_field_group(array(
			'key' => 'site',
			'title' => 'Site data',
			'fields' => array (
				array (
					'key' => 'websiteName',
					'label' => __( 'Website name', 'enon' ),
					'name' => 'websiteName',
					'type' => 'text',
				),
				array (
					'key' => 'customerEditURL',
					'label' => __( 'Customer Edit URL', 'enon' ),
					'append' => __( 'Customer Edit URL', 'enon' ),
					'name' => 'customerEditURL',
					'type' => 'url',
					'placeholder' => 'https://'
				),
				array (
					'key' => 'paymentSuccessfulURL',
					'label' => __( 'Payment successful URL', 'enon' ),
					'name' => 'customerEditURL',
					'type' => 'url',
					'placeholder' => 'https://'
				),
				array (
					'key' => 'paymentFailedURL',
					'label' => __( 'Payment failed URL', 'enon' ),
					'name' => 'customerEditURL',
					'type' => 'url',
					'placeholder' => 'https://'
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'reseller',
					),
				),
			),
		));

		acf_add_local_field_group(array(
			'key' => 'email',
			'title' => 'Email data',
			'fields' => array (
				array (
					'key' => 'emailSenderAdress',
					'label' => __( 'E-Mail sender address', 'enon' ),
					'name' => 'emailSenderAdress',
					'type' => 'email',
				),
				array (
					'key' => 'emailSenderName',
					'label' => __( 'E-Mail sender name', 'enon' ),
					'name' => 'emailSenderAdress',
					'type' => 'text',
				),
				array (
					'key' => 'emailFooter',
					'label' => __( 'E-Mail footer', 'enon' ),
					'name' => 'emailFooter',
					'type' => 'textarea',
				)
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'reseller',
					),
				),
			),
		));
	}

	/**
	 * Is activated.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Is Affiliate WP activated.
	 */
	public static function isActivated()
	{
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return false;
		}

		return true;
	}
}
