<?php
/**
 * @package WPENON
 * @version 1.0.0
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

/*
 * This file holds all the import constants for the plugin. To customize the plugin,
 * create a file named wpenon-config.php in the root directory of your WordPress installation
 * (just like wp-config.php). In this file you can override any of the following constants you want.
 * Furthermore, if you define a constant with the name of any EDD option (prefixed by WPENON_SETTING_),
 * the value of this constant will be used for the option value.
 *
 * For example, if you want to force the decimal separator to be the dot (.), you should create do this:
 * define( 'WPENON_SETTING_DECIMAL_SEPARATOR', '.' );
 *
 * You can also define constants with default values for the options. This works almost like the above example,
 * you only need to append _DEFAULT to the end of the constant name.
 */

/* GENERAL */
if ( ! defined( 'WPENON_PREFIX' ) ) {
  define( 'WPENON_PREFIX', 'wpenon_' );
}
if ( ! defined( 'WPENON_AJAX_PREFIX' ) ) {
  define( 'WPENON_AJAX_PREFIX', 'wpenon_ajax_' );
}
if ( ! defined( 'WPENON_ENERGIEAUSWEIS_TITLE_STRUCTURE' ) ) {
  define( 'WPENON_ENERGIEAUSWEIS_TITLE_STRUCTURE', 'ENERGIEAUSWEIS-{date:y}-{year-count:6}' );
}
if ( ! defined( 'WPENON_RECHNUNG_TITLE_STRUCTURE' ) ) {
  define( 'WPENON_RECHNUNG_TITLE_STRUCTURE', 'RECHNUNG-{date:y}-{year-count:6}' );
}
if ( ! defined( 'WPENON_DISCOUNTS' ) ) {
  define( 'WPENON_DISCOUNTS', true );
}
if ( ! defined( 'WPENON_REPORTS' ) ) {
  define( 'WPENON_REPORTS', true );
}
if ( ! defined( 'WPENON_TOOLS' ) ) {
  define( 'WPENON_TOOLS', true );
}
if ( ! defined( 'WPENON_ADDONS' ) ) {
  define( 'WPENON_ADDONS', true );
}
if ( ! defined( 'WPENON_POSTAL' ) ) {
  define( 'WPENON_POSTAL', true );
}
if ( ! defined( 'WPENON_AUDIT' ) ) {
  define( 'WPENON_AUDIT', true );
}
if ( ! defined( 'WPENON_DEFAULT_CHARSET' ) ) {
  define( 'WPENON_DEFAULT_CHARSET', 'UTF-8' );
}

/* PLUGIN DEBUG MODE */
if ( ! defined( 'WPENON_DEBUG' ) ) {
  define( 'WPENON_DEBUG', WP_DEBUG );
}

/* ENERGIEAUSWEIS TYPES */
if ( ! defined( 'WPENON_BW' ) ) {
  define( 'WPENON_BW', true );
}
if ( ! defined( 'WPENON_BN' ) ) {
  define( 'WPENON_BN', true );
}
if ( ! defined( 'WPENON_VW' ) ) {
  define( 'WPENON_VW', true );
}
if ( ! defined( 'WPENON_VN' ) ) {
  define( 'WPENON_VN', true );
}

/* ENERGIEAUSWEIS STANDARDS */
if ( ! defined( 'WPENON_STANDARDS' ) ) {
  define( 'WPENON_STANDARDS', serialize( array(
    'enev2013'    => array( __( 'EnEV 2013', 'wpenon' ), '2013-11-18', '2014-05-01' ),
  ) ) );
}

/* MENU POSITION */
if ( ! defined( 'WPENON_MENU_POSITION' ) ) {
  define( 'WPENON_MENU_POSITION', 6 );
}

/* CAPABILITIES */
if ( ! defined( 'WPENON_CERTIFICATE_CAP' ) ) {
  define( 'WPENON_CERTIFICATE_CAP', 'manage_options' );
}
if ( ! defined( 'WPENON_TABLE_CAP' ) ) {
  define( 'WPENON_TABLE_CAP', 'manage_options' );
}

/* SETTINGS */
if ( ! defined( 'WPENON_SETTING_TEST_MODE' ) ) {
  define( 'WPENON_SETTING_TEST_MODE', WPENON_DEBUG );
}
if ( ! defined( 'WPENON_SETTING_ITEM_QUANTITIES' ) ) {
  define( 'WPENON_SETTING_ITEM_QUANTITIES', false );
}
if ( ! defined( 'WPENON_SETTING_FIELD_DOWNLOADS' ) ) {
  define( 'WPENON_SETTING_FIELD_DOWNLOADS', false );
}
if ( ! defined( 'WPENON_SETTING_DOWNLOAD_METHOD' ) ) {
  define( 'WPENON_SETTING_DOWNLOAD_METHOD', 'direct' );
}
if ( ! defined( 'WPENON_SETTING_SYMLINK_FILE_DOWNLOADS' ) ) {
  define( 'WPENON_SETTING_SYMLINK_FILE_DOWNLOADS', false );
}
if ( ! defined( 'WPENON_SETTING_FILE_DOWNLOAD_LIMIT' ) ) {
  define( 'WPENON_SETTING_FILE_DOWNLOAD_LIMIT', 1 );
}
if ( ! defined( 'WPENON_SETTING_DOWNLOAD_LINK_EXPIRATION' ) ) {
  define( 'WPENON_SETTING_DOWNLOAD_LINK_EXPIRATION', 24 );
}
if ( ! defined( 'WPENON_SETTING_DISABLE_REDOWNLOAD' ) ) {
  define( 'WPENON_SETTING_DISABLE_REDOWNLOAD', false );
}
if ( ! defined( 'WPENON_SETTING_ACCOUNTING_SETTINGS' ) ) {
  define( 'WPENON_SETTING_ACCOUNTING_SETTINGS', false );
}
if ( ! defined( 'WPENON_SETTING_ENABLE_SKUS' ) ) {
  define( 'WPENON_SETTING_ENABLE_SKUS', false );
}
if ( ! defined( 'WPENON_SETTING_ENABLE_SEQUENTIAL' ) ) {
  define( 'WPENON_SETTING_ENABLE_SEQUENTIAL', false );
}
if ( ! defined( 'WPENON_SETTING_SEQUENTIAL_START' ) ) {
  define( 'WPENON_SETTING_SEQUENTIAL_START', 1 );
}
if ( ! defined( 'WPENON_SETTING_SEQUENTIAL_PREFIX' ) ) {
  define( 'WPENON_SETTING_SEQUENTIAL_PREFIX', '' );
}
if ( ! defined( 'WPENON_SETTING_SEQUENTIAL_POSTFIX' ) ) {
  define( 'WPENON_SETTING_SEQUENTIAL_POSTFIX', '' );
}
if ( ! defined( 'WPENON_SETTING_BUTTON_HEADER' ) ) {
  define( 'WPENON_SETTING_BUTTON_HEADER', '' );
}
if ( ! defined( 'WPENON_SETTING_BUTTON_STYLE' ) ) {
  define( 'WPENON_SETTING_BUTTON_STYLE', 'btn-primary' );
}
if ( ! defined( 'WPENON_SETTING_CHECKOUT_COLOR' ) ) {
  define( 'WPENON_SETTING_CHECKOUT_COLOR', 'inherit' );
}
