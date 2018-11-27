<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class CustomerMeta {
	private static $instance;

	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private static $table_slug = 'customermeta';
	private static $base_slug = 'customer';

	private function __construct() {
		self::_registerTable();

		add_action( 'edd_customer_post_create', array( $this, 'addCustomerMeta' ), 10, 2 );
		add_action( 'edd_customer_post_update', array( $this, 'updateCustomerMeta' ), 10, 3 );
		add_action( 'edd_pre_delete_customer', array( $this, 'deleteCustomerMeta' ), 10, 3 );

		if ( is_admin() ) {
			add_action( 'wpenon_install', array( __CLASS__, '_installTable' ) );
		}
	}

	public function addCustomerMeta( $customer_id, $args = array() ) {
		$mappings = array(
			'line1' => 'card_address',
			'line2' => 'card_address_2',
			'zip'   => 'card_zip',
			'city'  => 'card_city',
		);

		foreach ( self::_getFields() as $field => $title ) {
			$value = '';
			if ( isset( $mappings[ $field ] ) && isset( $_POST[ $mappings[ $field ] ] ) ) {
				$value = $_POST[ $mappings[ $field ] ];
			} elseif ( isset( $_POST[ 'wpenon_' . $field ] ) ) {
				$value = $_POST[ 'wpenon_' . $field ];
			}
			self::add( $customer_id, $field, $value, true );
		}

		return true;
	}

	public function updateCustomerMeta( $updated, $customer_id, $args = array() ) {
		$mappings = array(
			'line1'   => 'card_address',
			'line2'   => 'card_address_2',
			'zip'     => 'card_zip',
			'city'    => 'card_city',
			'state'   => 'card_state',
			'country' => 'billing_country',
		);

		foreach ( self::_getFields() as $field => $title ) {
			$value = '';
			if ( isset( $mappings[ $field ] ) && isset( $_POST[ $mappings[ $field ] ] ) ) {
				$value = $_POST[ $mappings[ $field ] ];
			} elseif ( isset( $_POST[ 'wpenon_' . $field ] ) ) {
				$value = $_POST[ 'wpenon_' . $field ];
			}
			if ( ! empty( $value ) ) {
				self::update( $customer_id, $field, $value );
			}
		}

		return true;
	}

	public function deleteCustomerMeta( $customer_id, $confirm = true, $remove_data = false ) {
		foreach ( self::_getFields() as $field => $title ) {
			self::delete( $customer_id, $field );
		}

		return true;
	}

	public function getCustomerMeta( $customer_id ) {
		$meta = array();

		foreach ( self::_getFields() as $field => $title ) {
			$meta[ $field ] = self::get( $customer_id, $field, true );
		}

		return $meta;
	}

	public static function get( $customer_id, $key = '', $single = false ) {
		$base_slug = \WPENON\Util\Format::prefix( self::$base_slug );

		return get_metadata( $base_slug, $customer_id, $key, $single );
	}

	public static function add( $customer_id, $meta_key, $meta_value, $unique = false ) {
		$base_slug = \WPENON\Util\Format::prefix( self::$base_slug );

		return add_metadata( $base_slug, $customer_id, $meta_key, $meta_value, $unique );
	}

	public static function update( $customer_id, $meta_key, $meta_value, $prev_value = '' ) {
		$base_slug = \WPENON\Util\Format::prefix( self::$base_slug );

		return update_metadata( $base_slug, $customer_id, $meta_key, $meta_value, $prev_value );
	}

	public static function delete( $customer_id, $meta_key, $meta_value = '' ) {
		$base_slug = \WPENON\Util\Format::prefix( self::$base_slug );

		return delete_metadata( $base_slug, $customer_id, $meta_key, $meta_value );
	}

	public static function delete_by_key( $meta_key ) {
		$base_slug = \WPENON\Util\Format::prefix( self::$base_slug );

		return delete_metadata( $base_slug, null, $meta_key, '', true );
	}

	public static function _registerTable() {
		return \WPENON\Util\DB::registerTable( \WPENON\Util\Format::prefix( self::$table_slug ) );
	}

	public static function _installTable() {
		global $wpdb;

		$table_slug = \WPENON\Util\Format::prefix( self::$table_slug );
		$base_slug  = \WPENON\Util\Format::prefix( self::$base_slug );

		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		$max_index_length = 191; // see wp-admin/includes/schema.php

		$wpdb_table = $wpdb->$table_slug;

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb_table';" ) != $wpdb_table ) {
			$query = "CREATE TABLE $wpdb_table (
  meta_id bigint(20) unsigned NOT NULL auto_increment,
  " . $base_slug . "_id bigint(20) unsigned NOT NULL default '0',
  meta_key varchar(255) default NULL,
  meta_value longtext,
  PRIMARY KEY (meta_id),
  KEY " . $base_slug . "_id (" . $base_slug . "_id),
  KEY meta_key (meta_key($max_index_length))
  ) $charset_collate;\n";
			if ( ! function_exists( 'dbDelta' ) ) {
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			}

			return dbDelta( $query );
		}

		return false;
	}

	private static function _getFields() {
		return apply_filters( 'wpenon_customer_meta_fields', array(
			'line1'         => __( 'Adresszeile 1', 'wpenon' ),
			'line2'         => __( 'Adresszeile 2', 'wpenon' ),
			'zip'           => __( 'Postleitzahl', 'wpenon' ),
			'city'          => __( 'Stadt', 'wpenon' ),
			'state'         => __( 'Bundesland', 'wpenon' ),
			'country'       => __( 'Land', 'wpenon' ),
			'business_name' => __( 'Firmenname', 'wpenon' ),
			'ustid'         => __( 'USt-Identifikationsnummer', 'wpenon' ),
			'steuernummer'  => __( 'Steuernummer', 'wpenon' ),
			'telefon'       => __( 'Telefonnummer', 'wpenon' ),
		) );
	}

}
