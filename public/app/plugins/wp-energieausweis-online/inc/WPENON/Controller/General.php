<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Controller;

class General {
	private static $instance;

	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private $model = null;
	private $view = null;

	private function __construct() {
		$this->model = \WPENON\Model\TableManager::instance();

		\WPENON\Model\PaymentGatewayDeposit::instance();
		\WPENON\Model\PaymentGatewaySofortueberweisung::instance();
		\WPENON\Model\PaymentGatewayPaymill::instance();

		add_action( 'init', array( $this, '_registerRewriteRules' ) );
		add_action( 'init', array( $this, '_letListenerDie' ), 100 );

		add_action( 'wpenon_install', array( $this, '_registerRewriteRules' ) );
		add_action( 'wpenon_install', 'flush_rewrite_rules', 100 );
		add_action( 'wpenon_uninstall', 'flush_rewrite_rules', 100 );

		add_action( 'edd_insert_payment', array( $this, '_attachPayment' ), 10, 2 );
		add_action( 'edd_payment_delete', array( $this, '_detachPayment' ), 10, 1 );
		add_action( 'edd_complete_download_purchase', array( $this, '_handlePaymentCompleteActions' ), 10, 1 );
	}

	public function _registerRewriteRules() {
		add_rewrite_rule( '^edd-listener/([^/]+)/?$', 'index.php?edd-listener=$1', 'top' );
	}

	public function _letListenerDie() {
		// if edd-listener is defined and the script has been executed up to this point, no listener was triggered
		if ( isset( $_GET['edd-listener'] ) ) {
			wp_send_json_error( array( 'message' => 'Invalid gateway or missing request object.' ) );
		}
	}

	public function _attachPayment( $payment_id, $payment_data ) {
		if ( isset( $payment_data['cart_details'] ) ) {
			foreach ( $payment_data['cart_details'] as $item ) {
				add_post_meta( $item['id'], '_wpenon_attached_payment_id', $payment_id );
				add_post_meta( $item['id'], '_wpenon_attached_payment_log', 'Function: _attachPayment Payment ID: ' . $payment_id . ' EAID: ' . $item['id'] . ' Payment data ' . print_r( $payment_data, true ) );
			}
		}
	}

	public function _detachPayment( $payment_id ) {
		$cart_details = edd_get_payment_meta_cart_details( $payment_id );
		if ( is_array( $cart_details ) ) {
			foreach ( $cart_details as $item ) {
				delete_post_meta( $item['id'], '_wpenon_attached_payment_id', $payment_id );
				delete_post_meta( $item['id'], '_wpenon_attached_payment_log', $payment_id );
			}
		}
	}

	public function _handlePaymentCompleteActions( $post_id ) {
		$energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis( $post_id );

		$execute = apply_filters( 'wpenon_execute_complete_actions', true, $energieausweis );
		if ( ! $execute ) {
			return;
		}

		if ( ! trim( $energieausweis->ausstellungsdatum ) ) {
			$energieausweis->ausstellungsdatum = current_time( 'Y-m-d' );
		}

		if ( ! trim( $energieausweis->ausstellungszeit ) ) {
			$energieausweis->ausstellungszeit = current_time( 'H:i' );
		}

		$register_status = \WPENON\Util\DIBT::assignRegistryID( $energieausweis );
		if ( $register_status && ! is_wp_error( $register_status ) ) {
			$datasent_status = \WPENON\Util\DIBT::sendData( $energieausweis );
		}
	}

	public function _enqueueScripts( $energieausweis = null, $schema = null, $admin = false ) {
		$locale   = str_replace( '_', '-', get_locale() );
		$language = substr( $locale, 0, 2 );

		//wpenon_enqueue_style( 'select2', 'third-party/select2/select2', array(), '3.5.2' );
		// && wpenon_enqueue_script( 'select2', 'third-party/select2/select2', array( 'jquery' ), '3.5.2' );
		if ( ! wpenon_maybe_enqueue_script( 'select2-locale', 'third-party/select2/select2_locale_' . $locale, array( 'select2' ), '3.5.2' ) ) {
		// 	wpenon_maybe_enqueue_script( 'select2-locale', 'third-party/select2/select2_locale_' . $language, array( 'select2' ), '3.5.2' );
		}

		wpenon_enqueue_script( 'wpenon-parser', 'parser', array(), WPENON_VERSION );
		wp_localize_script( 'wpenon-parser', '_wpenon_data', self::getScriptVars( $energieausweis, $schema, $admin ) );

		wpenon_enqueue_script( 'wpenon-formatter', 'formatter', array( 'wpenon-parser' ), WPENON_VERSION );

		$dynamic_functions_dependencies = array( 'wpenon-parser', 'wpenon-formatter' );
		if ( wpenon_maybe_enqueue_script( 'wpenon-custom-dynamic-functions', WPENON_DATA_URL . '/dynamic-functions.js', $dynamic_functions_dependencies, WPENON_VERSION ) ) {
			$dynamic_functions_dependencies[] = 'wpenon-custom-dynamic-functions';
		}

		wpenon_enqueue_script( 'wpenon-dynamic-functions', 'dynamic-functions', $dynamic_functions_dependencies, WPENON_VERSION );

		wpenon_enqueue_script( 'wpenon-general', 'general', array(
			'jquery',
			'wpenon-parser',
			'wpenon-formatter',
			'wpenon-dynamic-functions'
		), WPENON_VERSION );

	}

	public function getModel() {
		return $this->model;
	}

	public function getView() {
		return $this->view;
	}

	public static function getScriptVars( $energieausweis = null, $schema = null, $admin = false ) {
		$vars = array(
			'ajax_url'             => admin_url( 'admin-ajax.php' ),
			'rest_url'             => home_url('wp-json/'),
			'security_nonce'       => wp_create_nonce( WPENON_AJAX_PREFIX . 'energieausweis' ),
			'debug'                => WPENON_DEBUG,
			'decimal_separator'    => wpenon_get_option( 'decimal_separator' ),
			'thousands_separator'  => wpenon_get_option( 'thousands_separator' ),
			'select2_selector'     => $admin ? '.wpenon-metabox select' : '.wpenon-wrapper select',
			'energieausweis_id'    => is_a( $energieausweis, '\WPENON\Model\Energieausweis' ) ? $energieausweis->id : 0,
			'energieausweis_title' => is_a( $energieausweis, '\WPENON\Model\Energieausweis' ) ? $energieausweis->post_title : 0,
			'dynamic_fields'       => is_a( $schema, '\WPENON\Model\Schema' ) ? $schema->getDynamicFields() : new \stdClass(),
			'dynamic_functions'    => new \stdClass(),
			'parser'               => new \stdClass(),
			'i18n'                 => array(
				'please_select' => __( 'Bitte wählen...', 'wpenon' ),
			),
		);

		return apply_filters( 'wpenon_script_vars', $vars );
	}
}
