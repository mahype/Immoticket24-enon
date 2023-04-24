<?php
/**
 * Admin: Affiliate Groups
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Affiliates
 * @copyright   Copyright (c) 2021, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.13.0
 */

/**
 * Initialize Affiliate + Grouping Connections UI.
 *
 * @since  2.13.0
 */
function affwp_affiliates_admin_connect() {

	static $instance = null;

	if ( ! is_null( $instance ) ) {
		return $instance;
	}

	require_once __DIR__ . '/groups/class-connector.php';

	$instance = new \AffiliateWP\Admin\Affiliates\Groups\Connector();
}
add_action( 'plugins_loaded', 'affwp_affiliates_admin_connect', 9 );

/**
 * Initialize Affiliate Groups Screen.
 *
 * @since  2.13.0
 */
function affwp_affiliates_admin_categories() {

	static $instance = null;

	if ( ! is_null( $instance ) ) {
		return $instance;
	}

	require_once __DIR__ . '/groups/class-management.php';

	$instance = new \AffiliateWP\Admin\Affiliates\Groups\Management();
}
add_action( 'plugins_loaded', 'affwp_affiliates_admin_categories', 10 );
