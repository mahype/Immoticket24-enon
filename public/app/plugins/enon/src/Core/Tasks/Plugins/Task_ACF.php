<?php

namespace Enon\Core\Tasks\Plugins;

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
class Task_ACF implements Task, Actions {

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
		if ( ! self::is_activated() ) {
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
		add_action( 'acf/init', array( $this, 'register_general_options' ) );
		add_action( 'acf/init', array( $this, 'register_mail_options' ) );
	}

	/**
	 * Adding field group.
	 *
	 * @since 1.0.0
	 */
	public function register_general_options() {
		$settings = acf_add_options_page(
			array(
				'page_title' => __( 'Allgemein', 'enon' ),
				'menu_title' => __( 'Enon', 'enon' ),
				'menu_slug'  => 'enon-settings',
				'icon_url'   => 'dashicons-admin-tools',
				'capability' => 'edit_posts',
				'redirect'   => false,
			)
		);

		acf_add_options_page( $settings );
	}

	/**
	 * Adding field group.
	 *
	 * @since 1.0.0
	 */
	public function register_mail_options() {
		$page = acf_add_options_page(
			array(
				'page_title'  => __( 'Mails', 'enon' ),
				'menu_title'  => __( 'Mails', 'enon' ),
				'menu_slug'   => 'enon-mails',
				'capability'  => 'edit_posts',
				'parent_slug' => 'enon-settings',
				'redirect'    => false,
			)
		);

		acf_add_options_page( $page );

		$options = array(
			'key' => 'group_email_settings',
			'title' => 'Email Einstellungen',
			'fields' => array(
				array(
					'key' => 'field_tab_confirmation_email',
					'label' => __( 'Bestellbestätigung', 'enon' ),
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
					'key' => 'field_confirmation_sender_name',
					'label' => __( 'Absender Name', 'enon' ),
					'name' => 'confirmation_sender_name',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'Immoticket24.de GmbH',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_confirmation_sender_email',
					'label' => __( 'Anbsender Email', 'enon' ),
					'name' => 'confirmation_sender_email',
					'type' => 'email',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'christian@energieausweis-online-erstellen.de',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'field_confirmation_subject',
					'label' => __( 'Betreff', 'enon' ),
					'name' => 'confirmation_subject',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'Ihr Energieausweis',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_confirmation_content',
					'label' => 'Inhalt',
					'name' => 'confirmation_content',
					'type' => 'wysiwyg',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'tabs' => 'all',
					'toolbar' => 'full',
					'media_upload' => 1,
					'delay' => 0,
				),
				array(
					'key' => 'field_bill',
					'label' => __( 'Rechnung', 'enon' ),
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
					'key' => 'field_bill_sender_name',
					'label' => __( 'Absender Name', 'enon' ),
					'name' => 'bill_sender_name',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'Immoticket24.de GmbH',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_bill_sender_email',
					'label' => __( 'Absender Email', 'enon' ),
					'name' => 'bill_sender_email',
					'type' => 'email',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'christian@energieausweis-online-erstellen.de',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'field_bill_subject',
					'label' => __( 'Betreff', 'enon' ),
					'name' => 'bill_subject',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'Zahlungsaufforderung',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_bill_content',
					'label' => 'Inhalt',
					'name' => 'bill_content',
					'type' => 'wysiwyg',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'tabs' => 'all',
					'toolbar' => 'full',
					'media_upload' => 1,
					'delay' => 0,
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'enon-mails',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
		);

		acf_add_local_field_group( $options );
	}

	/**
	 * Is activated.
	 *
	 * @return bool Is Affiliate WP activated.
	 * @since 1.0.0
	 */
	public static function is_activated() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return false;
		}

		return true;
	}
}
