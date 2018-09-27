<?php

define( 'WPENON_PREFIX', 'it24ea_' );
define( 'WPENON_AJAX_PREFIX', 'wpenon_ajax_' );

define( 'WPENON_ENERGIEAUSWEIS_TITLE_STRUCTURE', 'ENERGIE-{date:y}-{year-count:6}' );
define( 'WPENON_RECHNUNG_TITLE_STRUCTURE', 'RE-{date:y}-{year-count:6}' );
define( 'WPENON_POSTAL', false );
define( 'WPENON_AUDIT', false );

define( 'WPENON_BW', true );
define( 'WPENON_BN', false );
define( 'WPENON_VW', true );
define( 'WPENON_VN', false );

define( 'WPENON_STANDARDS', serialize( array(
  'enev2013'    => array( __( 'EnEV 2013', 'wpenon' ), '2013-11-18', '2014-05-01' ),
  'enev2017'    => array( __( 'EnEV 2013 (ab 1.7.2017)', 'wpenon' ), '2013-11-18', '2017-07-01' ),
) ) );

define( 'WPENON_SETTING_CUSTOM_BOOTSTRAP_CSS', 'immoticketenergieausweis' );
define( 'WPENON_SETTING_CUSTOM_BOOTSTRAP_JS', 'immoticketenergieausweis' );
define( 'WPENON_SETTING_DECIMAL_SEPARATOR', ',' );
define( 'WPENON_SETTING_THOUSANDS_SEPARATOR', '' );

if ( function_exists( 'get_current_user_id' ) && get_current_user_id() == 1 ) {
  define( 'WPENON_TOOLS', true );
  define( 'WPENON_ADDONS', true );
} else {
  define( 'WPENON_TOOLS', false );
  define( 'WPENON_ADDONS', false );
}

define( 'WPENON_DIBT_USER', 'harsche-energieberatung@web.de' );
define( 'WPENON_DIBT_PASSWORD', '139K76m88' );
define( 'WPENON_DIBT_DEBUG_USER', 'roland.harsche@immoticket24.de' );
define( 'WPENON_DIBT_DEBUG_PASSWORD', 'k4f4m2g7F6' );

function wpenon_immoticket24_hack_allow_changes_after_order( $allow_changes_after_order, $energieausweis ) {
	if ( 79241 == $energieausweis->id ) {
		if ( current_time( 'timestamp' ) < strtotime( '2017-09-13' ) ) {
			return true;
		}
	}

	return $allow_changes_after_order;
}
add_filter( 'wpenon_allow_changes_after_order', 'wpenon_immoticket24_hack_allow_changes_after_order', 10, 2 );
