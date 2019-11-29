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
	 * Fieldsets which need to be registered.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $fieldSets;

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
		add_action( 'acf/init', [ $this, 'registerFields' ] );
	}

	/**
	 * Adding field group.
	 *
	 * @since 1.0.0
	 */
	public function registerFields()
	{
		$confirmation_email_default_subject = '';
		$confirmation_email_default_content = '';

		acf_add_local_field_group(
			array(
				'key' => 'company',
				'title' => '1. Company',
				'fields' => array (
					array (
						'key' => 'field_company_name',
						'label' => __( 'Company Name', 'enon' ),
						'name' => 'company_name',
						'type' => 'text',
						'instructions' => __( 'Resellers company name.', 'enon' ),
						'required' => 0,
					),
					array (
						'key' => 'field_contact_name',
						'label' => __( 'Contact Name', 'enon' ),
						'name' => 'contact_name',
						'type' => 'text',
						'instructions' => __( 'The name of the contact person on the company.', 'enon' ),
						'required' => 0,
					),
					array (
						'key' => 'field_contact_email',
						'label' => __( 'Contact Email', 'enon' ),
						'name' => 'contact_email',
						'type' => 'email',
						'instructions' => __( 'The email of the contact person on the company. All emails from the system will be sent to this address.', 'enon' ),
						'required' => 0,
					),
					array (
						'key' => 'field_token',
						'label' => __( 'Token', 'enon' ),
						'name' => 'token',
						'type' => 'text',
						'default_value' => substr( md5( rand() ), 0, 14 ),
						'instructions' => __( 'The token which have to be set by the reseller.', 'enon' ),
						'required' => 0,
					),
					array (
						'key' => 'field_affiliateId',
						'label' => __( 'Affiliate ID', 'enon' ),
						'name' => 'affiliate_id',
						'type' => 'number',
						'instructions' => __( 'Affiliate WP id.', 'enon' ),
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
			)
		);

		acf_add_local_field_group(
			array(
				'key' => 'site',
				'title' => '2. Iframe Settings',
				'fields' => array (
					array (
						'key' => 'field_user_interface',
						'label' => __( 'User Interface', 'enon' ),
						'name' => 'user_interface',
						'type' => 'checkbox',
						'choices' => array(
							'show_headline' => __( 'Show headline', 'enon' ),
						),
						'default_value' => array(
							0 => 'show_headline',
						),
						'return_format' => 'value',
					),
					array (
						'key' => 'field_technical',
						'label' => __( 'Technical', 'enon' ),
						'name' => 'technical',
						'type' => 'checkbox',
						'choices' => array(
							'submit_iframe_height' => __( 'Submit iframe height', 'enon' ),
						),
						'return_format' => 'value',
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
			)
		);

		acf_add_local_field_group(
			array(
				'key' => 'site',
				'title' => '3. Website data',
				'fields' => array (
					array (
						'key' => 'field_website_name',
						'label' => __( 'Website name', 'enon' ),
						'name' => 'website_name',
						'type' => 'text',
						'instructions' => __( 'This is the website name, which appears in emails.', 'enon' ),
					),
					array (
						'key' => 'field_customerEditURL',
						'label' => __( 'Customer Edit URL', 'enon' ),
						'instructions' => __( 'The edit url of the reseller website.', 'enon' ),
						'name' => 'customer_edit_url',
						'type' => 'url',
						'placeholder' => 'https://'
					),
					array (
						'key' => 'field_payment_successful_url',
						'label' => __( 'Payment successful URL', 'enon' ),
						'instructions' => __( 'This url will be shown after successful payment.', 'enon' ),
						'name' => 'payment_successful_url',
						'type' => 'url',
						'placeholder' => 'https://'
					),
					array (
						'key' => 'field_payment_failed_url',
						'label' => __( 'Payment failed URL', 'enon' ),
						'instructions' => __( 'This url will be shown after failed payment.', 'enon' ),
						'name' => 'payment_failed_url',
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
			)
		);

		acf_add_local_field_group(
			array(
				'key' => 'email',
				'title' => '4. Confirmation email',
				'fields' => array (
					array (
						'key' => 'field_email_sender_address',
						'label' => __( 'E-Mail sender address', 'enon' ),
						'name' => 'email_sender_address',
						'type' => 'email',
					),
					array (
						'key' => 'field_email_sender_name',
						'label' => __( 'E-Mail sender name', 'enon' ),
						'name' => 'email_sender_name',
						'type' => 'text',
					),
					array (
						'key' => 'field_email_footer',
						'label' => __( 'E-Mail Footer', 'enon' ),
						'name' => 'email_footer',
						'type' => 'textarea',
					),
					/* New
					array (
						'key' => 'field_email_subject',
						'label' => __( 'E-Mail Subject', 'enon' ),
						'name' => 'email_text',
						'type' => 'text',
					),
					array (
						'key' => 'field_email_text',
						'label' => __( 'E-Mail Text', 'enon' ),
						'name' => 'email_text',
						'type' => 'wysiwyg',
					)
					*/
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
			)
		);
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
