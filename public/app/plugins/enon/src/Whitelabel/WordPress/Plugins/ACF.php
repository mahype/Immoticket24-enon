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
	public function addFieldGroup()
	{
		acf_add_local_field_group(array(
			'key' => 'reseller',
			'title' => '1. Reseller',
			'fields' => array (
				array (
					'key' => 'field_companyName',
					'label' => __( 'Company Name', 'enon' ),
					'name' => 'companyName',
					'type' => 'text',
					'append' => __( 'Resellers company name.', 'enon' ),
					'required' => 0,
				),
				array (
					'key' => 'field_name',
					'label' => __( 'Name', 'enon' ),
					'name' => 'name',
					'type' => 'text',
					'append' => __( 'The name of the contact person on the company.', 'enon' ),
					'required' => 0,
				),
				array (
					'key' => 'field_email',
					'label' => __( 'Email', 'enon' ),
					'name' => 'email',
					'type' => 'email',
					'append' => __( 'The email of the contact person on the company.', 'enon' ),
					'required' => 0,
				),
				array (
					'key' => 'field_token',
					'label' => __( 'Token', 'enon' ),
					'name' => 'token',
					'type' => 'text',
					'default_value' => substr( md5( rand() ), 0, 14 ),
					'append' => __( 'The token which have to be set by the reseller.', 'enon' ),
				    'required' => 0,
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
			'title' => '2. Site data',
			'fields' => array (
				array (
					'key' => 'field_websiteName',
					'label' => __( 'Website name', 'enon' ),
					'name' => 'websiteName',
					'type' => 'text',
					'append' => __( 'This is the website name, which appears in emails.', 'enon' ),
				),
				array (
					'key' => 'field_customerEditURL',
					'label' => __( 'Customer Edit URL', 'enon' ),
					'append' => __( 'Customer Edit URL', 'enon' ),
					'name' => 'customerEditURL',
					'type' => 'url',
					'placeholder' => 'https://'
				),
				array (
					'key' => 'field_paymentSuccessfulURL',
					'label' => __( 'Payment successful URL', 'enon' ),
					'name' => 'customerEditURL',
					'type' => 'url',
					'placeholder' => 'https://'
				),
				array (
					'key' => 'field_paymentFailedURL',
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
			'title' => '3. Email data',
			'fields' => array (
				array (
					'key' => 'field_emailSenderAdress',
					'label' => __( 'E-Mail sender address', 'enon' ),
					'name' => 'emailSenderAdress',
					'type' => 'email',
				),
				array (
					'key' => 'field_emailSenderName',
					'label' => __( 'E-Mail sender name', 'enon' ),
					'name' => 'emailSenderAdress',
					'type' => 'text',
				),
				array (
					'key' => 'field_emailFooter',
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
