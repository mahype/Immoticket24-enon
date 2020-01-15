<?php

namespace Enon\Reseller\Tasks\Plugins;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Enon\Traits\Logger as Logger_Trait;
use Enon\Logger;
use Enon\Reseller\Models\Reseller;

/**
 * Managing ACF Fields.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class TaskACF implements Task, Actions {

	use Logger_Trait;

	/**
	 * Fieldsets which need to be registered.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $field_sets;

	/**
	 * AffiliateWP constructor.
	 *
	 * @param Logger $logger Logger object.
	 * @since 1.0.0
	 */
	public function __construct( Logger $logger ) {
		$this->logger = $logger;
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		if ( ! self::isActivated() ) {
			$this->logger->warning( 'Advanced custom fields seems not to be activated.' );
			return;
		}

		$this->add_actions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		 add_action( 'acf/init', array( $this, 'registerFields' ) );
	}

	/**
	 * Adding field group.
	 *
	 * @since 1.0.0
	 */
	public function registerFields() {
		$confirmation_email_default_subject = '';
		$confirmation_email_default_content = '';

		$fields = array();

		$company_fields = array(
			array(
				'key' => 'field_tab_confirmation_semail',
				'label' => __( 'Firma', 'enon' ),
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'top',
				'endpoint' => 0,
			),
			array(
				'key' => 'field_company_name',
				'label' => __( 'Company Name', 'enon' ),
				'name' => 'company_name',
				'type' => 'text',
				'instructions' => __( 'Resellers company name.', 'enon' ),
				'required' => 0,
			),
			array(
				'key' => 'field_contact_name',
				'label' => __( 'Contact Name', 'enon' ),
				'name' => 'contact_name',
				'type' => 'text',
				'instructions' => __( 'The name of the contact person on the company.', 'enon' ),
				'required' => 0,
			),
			array(
				'key' => 'field_contact_email',
				'label' => __( 'Contact Email', 'enon' ),
				'name' => 'contact_email',
				'type' => 'email',
				'instructions' => __( 'The email of the contact person on the company. All emails from the system will be sent to this address.', 'enon' ),
				'required' => 0,
			),
			array(
				'key' => 'field_send_data_to_reseller',
				'label' => __( 'Send order to reseller', 'enon' ),
				'name' => 'send_order_to_reseller',
				'type' => 'checkbox',
				'instructions' => __( 'Check if order email should be sent to reseller.', 'enon' ),
				'required' => 0,
			),
			array(
				'key' => 'field_token',
				'label' => __( 'Token', 'enon' ),
				'name' => 'token',
				'type' => 'text',
				'default_value' => substr( md5( rand() ), 0, 14 ),
				'instructions' => __( 'The token which have to be set by the reseller.', 'enon' ),
				'required' => 0,
			),
			array(
				'key' => 'field_affiliateId',
				'label' => __( 'Affiliate ID', 'enon' ),
				'name' => 'affiliate_id',
				'type' => 'number',
				'instructions' => __( 'Affiliate WP id.', 'enon' ),
				'required' => 0,
			),
		);

		$confirmation_email_fields = array(
			array(
				'key' => 'field_tab_confirmation_email',
				'label' => __( 'Confirmation Email', 'enon' ),
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'top',
				'endpoint' => 0,
			),
			array(
				'key' => 'field_confirmation_email_sender_address',
				'label' => __( 'E-Mail sender address', 'enon' ),
				'name' => 'confirmation_email_sender_address',
				'type' => 'email',
			),
			array(
				'key' => 'field_confirmation_email_sender_name',
				'label' => __( 'E-Mail sender name', 'enon' ),
				'name' => 'confirmation_email_sender_name',
				'type' => 'text',
			),
			array(
				'key' => 'field_confirmation_email_subject',
				'label' => __( 'E-Mail Subject', 'enon' ),
				'name' => 'confirmation_email_subject',
				'type' => 'text',
				'default' => 'Ihr Energieausweis',
			),
			array(
				'key' => 'field_confirmation_email_content',
				'label' => __( 'E-Mail Content', 'enon' ),
				'name' => 'confirmation_email_content',
				'type' => 'wysiwyg',
			),
		);

		$bill_email_fields = array(
			array(
				'key' => 'field_tab_bill_email',
				'label' => __( 'Bill Email', 'enon' ),
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'top',
				'endpoint' => 0,
			),
			array(
				'key' => 'field_bill_email_sender_address',
				'label' => __( 'E-Mail sender address', 'enon' ),
				'name' => 'bill_email_sender_address',
				'type' => 'email',
			),
			array(
				'key' => 'field_bill_email_sender_name',
				'label' => __( 'E-Mail sender name', 'enon' ),
				'name' => 'bill_email_sender_name',
				'type' => 'text',
			),
			array(
				'key' => 'field_bill_email_subject',
				'label' => __( 'E-Mail Subject', 'enon' ),
				'name' => 'bill_email_subject',
				'type' => 'text',
				'default' => 'Zahlungsaufforderung',
			),
			array(
				'key' => 'field_bill_email_content',
				'label' => __( 'E-Mail Content', 'enon' ),
				'name' => 'bill_email_content',
				'type' => 'wysiwyg',
			),
		);

		$iframe_settings_fields = array(
			array(
				'key' => 'field_tab_iframe_settings',
				'label' => __( 'Iframe Settings', 'enon' ),
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'top',
				'endpoint' => 0,
			),
			array(
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
			array(
				'key' => 'field_extra_css',
				'label' => __( 'Extra CSS', 'enon' ),
				'name' => 'extra_css',
				'type' => 'acf_code_field',
				'instructions' => __( 'Add some extra CSS for reseller.', 'enon' ),
				'mode' => 'css',
				'theme' => 'monokai',
			),
			array(
				'key' => 'field_extra_js',
				'label' => __( 'Extra JS', 'enon' ),
				'name' => 'extra_js',
				'type' => 'acf_code_field',
				'instructions' => __( 'Add some extra JavaScript for reseller.', 'enon' ),
				'mode' => 'javascript',
				'theme' => 'monokai',
			),
		);

		$form_settings_fields = array(
			array(
				'key' => 'field_tab_form_settings',
				'label' => __( 'Schema Settings', 'enon' ),
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'top',
				'endpoint' => 0,
			),
			array(
				'key' => 'field_bw_schema_file',
				'label' => __( 'Bedarfsausweis schema file', 'enon' ),
				'name' => 'bw_schema_file',
				'type' => 'text',
				'instructions' => __( 'Leave blank for standard file (bw.php).', 'enon' ),
			),
			array(
				'key' => 'field_vw_schema_file',
				'label' => __( 'Verbrauchsausweis schema file', 'enon' ),
				'name' => 'vw_schema_file',
				'type' => 'text',
				'instructions' => __( 'Leave blank for standard file (vw.php).', 'enon' ),
			),
		);

		$website_data_fields = array(
			array(
				'key' => 'field_tab_website_settings',
				'label' => __( 'Website Settings', 'enon' ),
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'top',
				'endpoint' => 0,
			),
			array(
				'key' => 'field_website_name',
				'label' => __( 'Website name', 'enon' ),
				'name' => 'website_name',
				'type' => 'text',
				'instructions' => __( 'This is the website name, which appears in emails.', 'enon' ),
			),
			array(
				'key' => 'field_customer_edit_url',
				'label' => __( 'Customer Edit URL', 'enon' ),
				'instructions' => __( 'The edit url of the reseller website.', 'enon' ),
				'name' => 'customer_edit_url',
				'type' => 'url',
				'placeholder' => 'https://',
			),
			array(
				'key' => 'field_payment_successful_url',
				'label' => __( 'Payment successful URL', 'enon' ),
				'instructions' => __( 'This url will be shown after successful payment.', 'enon' ),
				'name' => 'payment_successful_url',
				'type' => 'url',
				'placeholder' => 'https://',
			),
			array(
				'key' => 'field_payment_failed_url',
				'label' => __( 'Payment failed URL', 'enon' ),
				'instructions' => __( 'This url will be shown after failed payment.', 'enon' ),
				'name' => 'payment_failed_url',
				'type' => 'url',
				'placeholder' => 'https://',
			),
		);

		$post_data_fields = array(
			array(
				'key' => 'field_tab_post_data',
				'label' => __( 'Post Data', 'enon' ),
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'top',
				'endpoint' => 0,
			),
			array(
				'key' => 'field_post_endpoint',
				'label' => __( 'Endpoint', 'enon' ),
				'name' => 'post_endpoint',
				'type' => 'url',
				'instructions' => __( 'URL to send data.', 'enon' ),
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => 'https://',
			),
			array(
				'key' => 'field_post_data_config class',
				'label' => __( 'Data config class.', 'enon' ),
				'name' => 'post_data_config_class',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'post_endpoint',
							'operator' => '!=empty',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => 'SendEnergieausweisStandard',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
		);

		$fields = array_merge(
			$company_fields,
			$confirmation_email_fields,
			$bill_email_fields,
			$form_settings_fields,
			$website_data_fields,
			$iframe_settings_fields,
			$post_data_fields
		);

		acf_add_local_field_group(
			array(
				'key' => 'reseller_settings',
				'title' => __( 'Reseller Settings', 'enon' ),
				'fields' => $fields,
				'location' => array(
					array(
						array(
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
	 * @return bool Is Affiliate WP activated.
	 * @since 1.0.0
	 */
	public static function isActivated() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return false;
		}

		return true;
	}
}
