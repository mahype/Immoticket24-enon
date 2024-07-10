<?php
/*
Plugin Name: Energieausweis-Benutzerrollen
Plugin URI: https://energieausweis.de
Description: Benutzerrollen und -management für energieausweis.de.
Version: 1.0.0
Author: Felix Arntz
Author URI: https://felix-arntz.me
*/

defined( 'ABSPATH' ) || exit;

function energieausweis_roles_init() {
	global $wp_user_roles;

	$role_slugs = array(
		/* Core roles */
		'subscriber', 'contributor', 'author', 'editor', 'administrator', 'wpenon_manager', 'wpenon_reseller', 'reseller_manager'
	);

	$wp_user_roles = array();
	foreach ( $role_slugs as $role_slug ) {
		$cap_keys = energieausweis_roles_get_caps( $role_slug );
		$cap_values = array_fill( 0, count( $cap_keys ), true );

		$wp_user_roles[ $role_slug ] = array(
			'name'         => energieausweis_roles_get_name( $role_slug ),
			'capabilities' => array_combine( $cap_keys, $cap_values ),
		);
	}

	// Grant extra caps for active plugins. This must be executed immediately, not on 'plugins_loaded'.
	energieausweis_roles_grant_extra_plugin_caps();

	remove_filter( 'user_has_cap', 'wp_maybe_grant_install_languages_cap', 1 );

	add_filter( 'user_has_cap', 'energieausweis_grant_owner_capabilities', 10, 4 );
	add_filter( 'map_meta_cap', 'energieausweis_roles_map_meta_cap', 10, 4 );

	add_action( 'admin_menu', 'energieausweis_adjust_menu_capabilities' );
}
add_action( 'muplugins_loaded', 'energieausweis_roles_init' );

function energieausweis_roles_get_name( $role_slug ) {
	switch ( $role_slug ) {
		/* Core roles */
		case 'subscriber':
			return __( 'Abonnent', 'energieausweis-roles' );
		case 'contributor':
			return __( 'Mitarbeiter', 'energieausweis-roles' );
		case 'author':
			return __( 'Autor', 'energieausweis-roles' );
		case 'editor':
			return __( 'Redakteur', 'energieausweis-roles' );
		case 'administrator':
			return __( 'Administrator', 'energieausweis-roles' );
		case 'wpenon_manager':
			return __( 'Energieausweis Manager', 'energieausweis-roles' );
		case 'wpenon_reseller':
			return __( 'Energieausweis Reseller', 'energieausweis-roles' );
		case 'reseller_manager':
			return __( 'Reseller Manager', 'energieausweis-roles' );
	}

	return '';
}

function energieausweis_roles_get_caps( $role_slug ) {
	$caps = array();

	switch ( $role_slug ) {
		/* Core roles */
		case 'administrator':
			$caps = array_merge( $caps, array(
				'rocket_manage_options',
				'rocket_purge_cache',
				'edit_dashboard',
				'update_core',
				'install_themes',
				'update_themes',
				'delete_themes',
				'install_plugins',
				'update_plugins',
				'delete_plugins',
				'switch_themes',
				'edit_themes',
				'edit_theme_options',
				'activate_plugins',
				'edit_plugins',
				'create_users',
				'edit_users',
				'list_users',
				'remove_users',
				'promote_users',
				'delete_users',
				'import',
				'export',
				'manage_options',
				'edit_files',
				'unfiltered_upload',
				'level_10',
				'level_9',
				'level_8',
				/* The following are meta caps by default */
				'install_languages',
				'update_languages',
				/* Custom caps */
				'manage_ratings',
				'backwpup',
				'backwpup_jobs',
				'backwpup_jobs_edit',
				'backwpup_jobs_start',
				'backwpup_backups',
				'backwpup_backups_download',
				'backwpup_backups_delete',
				'backwpup_logs',
				'backwpup_logs_delete',
				'backwpup_settings',
				'backwpup_restore',
				'manage_borlabs_cookie',
				'wf2fa_manage_settings',
				'wf2fa_activate_2fa_self',
				'wf2fa_activate_2fa_others'
			) );
		case 'editor':
			$caps = array_merge( $caps, array(
				'edit_pages',
				'publish_pages',
				'delete_pages',
				'edit_published_pages',
				'delete_published_pages',
				'edit_others_posts',
				'delete_others_posts',
				'edit_others_pages',
				'delete_others_pages',
				'read_private_posts',
				'edit_private_posts',
				'delete_private_posts',
				'read_private_pages',
				'edit_private_pages',
				'delete_private_pages',
				'manage_categories',
				'manage_links',
				'moderate_comments',
				'unfiltered_html',
				'level_7',
				'level_6',
				'level_5',
				'level_4',
				'level_3',
			) );
		case 'author':
			$caps = array_merge( $caps, array(
				'publish_posts',
				'edit_published_posts',
				'delete_published_posts',
				'upload_files',
				'level_2',
			) );
		case 'contributor':
			$caps = array_merge( $caps, array(
				'edit_posts',
				'delete_posts',
				'level_1',
			) );
		case 'wpenon_manager':
			$caps = array_merge( $caps, array(
				'read_product',
				'edit_product',
				'delete_product',
				'edit_products',
				'edit_others_products',
				'edit_others_items',
				'edit_published_products',
				'read_shop_payment',
				'edit_shop_payment',
				'edit_shop_payments',
				'edit_shop_customer',
				'manage_shop_discounts',
				'view_shop_reports'
			) );
		case 'reseller_manager':
			$caps = array_merge( $caps, array(
				'edit_reseller',
				'read_reseller',
				'delete_reseller',
				'edit_resellers',
				'edit_others_resellers',
				'publish_resellers',
				'read_private_resellers',
				'create_resellers',
				'view_affiliate_reports',
				'export_affiliate_data',
				'export_referral_data',
				'export_customer_data',
				'export_visit_data',
				'export_payout_data',
				'manage_affiliates',
				'manage_referrals',
				'manage_customers',
				'manage_visits',
				'manage_creatives',
				'manage_payouts',
				'manage_consumers',
			) );
		case 'wpenon_reseller':
			$caps = array_merge( $caps, array(
				'view_reseller_leads',
			) );
		case 'subscriber':
			$caps = array_merge( $caps, array(
				'read',
				'level_0',
			) );
	}

	return $caps;
}

function energieausweis_filter_edit_customer_role( $role ) {
	return 'edit_shop_customer';
}
add_filter( 'edd_edit_customers_role', 'energieausweis_filter_edit_customer_role' );
add_filter( 'edd_view_customers_role', 'energieausweis_filter_edit_customer_role' );

function energieausweis_roles_grant_extra_plugin_caps() {
	global $wp_user_roles;

	$active_plugins = get_option( 'active_plugins', array() );

	if ( in_array( 'wordpress-seo-premium/wp-seo-premium.php', $active_plugins, true ) || in_array( 'wordpress-seo/wp-seo.php', $active_plugins, true ) ) {
		$wp_user_roles['administrator']['capabilities']['wpseo_manage_options']         = true;
		$wp_user_roles['administrator']['capabilities']['wpseo_bulk_edit']              = true;
		$wp_user_roles['administrator']['capabilities']['wpseo_edit_advanced_metadata'] = true;
	}

	if ( in_array( 'easy-digital-downloads/easy-digital-downloads.php', $active_plugins, true ) ) {
		$wp_user_roles['administrator']['capabilities']['view_shop_reports'] = true;
		$wp_user_roles['administrator']['capabilities']['view_shop_sensitive_data'] = true;
		$wp_user_roles['administrator']['capabilities']['export_shop_reports'] = true;
		$wp_user_roles['administrator']['capabilities']['manage_shop_discounts'] = true;
		$wp_user_roles['administrator']['capabilities']['manage_shop_settings'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_product'] = true;
		$wp_user_roles['administrator']['capabilities']['read_product'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_product'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_products'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_others_products'] = true;
		$wp_user_roles['administrator']['capabilities']['publish_products'] = true;
		$wp_user_roles['administrator']['capabilities']['read_private_products'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_products'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_private_products'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_published_products'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_others_products'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_private_products'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_published_products'] = true;
		$wp_user_roles['administrator']['capabilities']['manage_product_terms'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_product_terms'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_product_terms'] = true;
		$wp_user_roles['administrator']['capabilities']['assign_product_terms'] = true;
		$wp_user_roles['administrator']['capabilities']['view_product_stats'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_shop_payment'] = true;
		$wp_user_roles['administrator']['capabilities']['read_shop_payment'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_shop_payment'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_shop_payments'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_others_shop_payments'] = true;
		$wp_user_roles['administrator']['capabilities']['publish_shop_payments'] = true;
		$wp_user_roles['administrator']['capabilities']['read_private_shop_payments'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_shop_payments'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_private_shop_payments'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_published_shop_payments'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_others_shop_payments'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_private_shop_payments'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_published_shop_payments'] = true;
		$wp_user_roles['administrator']['capabilities']['manage_shop_payment_terms'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_shop_payment_terms'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_shop_payment_terms'] = true;
		$wp_user_roles['administrator']['capabilities']['assign_shop_payment_terms'] = true;
		$wp_user_roles['administrator']['capabilities']['view_shop_payment_stats'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_shop_discount'] = true;
		$wp_user_roles['administrator']['capabilities']['read_shop_discount'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_shop_discount'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_shop_discounts'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_others_shop_discounts'] = true;
		$wp_user_roles['administrator']['capabilities']['publish_shop_discounts'] = true;
		$wp_user_roles['administrator']['capabilities']['read_private_shop_discounts'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_shop_discounts'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_private_shop_discounts'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_published_shop_discounts'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_others_shop_discounts'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_private_shop_discounts'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_published_shop_discounts'] = true;
		$wp_user_roles['administrator']['capabilities']['manage_shop_discount_terms'] = true;
		$wp_user_roles['administrator']['capabilities']['edit_shop_discount_terms'] = true;
		$wp_user_roles['administrator']['capabilities']['delete_shop_discount_terms'] = true;
		$wp_user_roles['administrator']['capabilities']['assign_shop_discount_terms'] = true;
		$wp_user_roles['administrator']['capabilities']['view_shop_discount_stats'] = true;
		$wp_user_roles['administrator']['capabilities']['import_products'] = true;
		$wp_user_roles['administrator']['capabilities']['import_shop_payments'] = true;
		$wp_user_roles['administrator']['capabilities']['import_shop_discounts'] = true;
	}

	if ( in_array( 'affiliate-wp/affiliate-wp.php', $active_plugins, true ) ) {
		$wp_user_roles['administrator']['capabilities']['view_affiliate_reports'] = true;
		$wp_user_roles['administrator']['capabilities']['export_affiliate_data'] = true;
		$wp_user_roles['administrator']['capabilities']['manage_affiliate_options'] = true;
		$wp_user_roles['administrator']['capabilities']['manage_affiliates'] = true;
		$wp_user_roles['administrator']['capabilities']['manage_referrals'] = true;
		$wp_user_roles['administrator']['capabilities']['manage_visits'] = true;
		$wp_user_roles['administrator']['capabilities']['manage_creatives'] = true;
		$wp_user_roles['administrator']['capabilities']['export_referral_data'] = true;
		$wp_user_roles['administrator']['capabilities']['export_payout_data'] = true;
		$wp_user_roles['administrator']['capabilities']['manage_payouts'] = true;
		$wp_user_roles['administrator']['capabilities']['manage_consumers'] = true;
		$wp_user_roles['administrator']['capabilities']['export_visit_data'] = true;
		$wp_user_roles['administrator']['capabilities']['export_customer_data'] = true;
		$wp_user_roles['administrator']['capabilities']['manage_customers'] = true;
	}
}

function energieausweis_grant_owner_capabilities( $allcaps, $caps, $args, $user ) {
	$global_caps = energieausweis_roles_get_global_admin_caps();

	$admin_user = [
		'svenwagener'
	];

	if ( in_array( $user->user_login, $admin_user ) || ( 'development' === WP_ENV && 'admin' === $user->user_login ) ) {
		foreach ( $global_caps as $global_cap ) {
			$allcaps[ $global_cap ] = true;
		}
		$allcaps = array_merge( $allcaps, array_fill_keys( $global_caps, true ) );
	} else {
		$allcaps = array_diff_key( $allcaps, array_flip( $global_caps ) );
	}

	return $allcaps;
}

function energieausweis_roles_get_global_admin_caps() {
	return array(
		/* Core capabilities */
		'update_core',
		'install_themes',
		'update_themes',
		'delete_themes',
		'install_plugins',
		'update_plugins',
		'delete_plugins',
		'install_languages',
		'update_languages',
		'switch_themes',
		'edit_themes',
		'edit_plugins',
		'edit_files',
		'import',
		'export',
		/* Custom capabilities */
		'manage_setup',
	);
}

function energieausweis_roles_map_meta_cap( $caps, $cap, $user_id, $args ) {
	switch ( $cap ) {
		case 'install_languages':
		case 'update_languages':
			if ( ! function_exists( 'wp_can_install_language_pack' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
			}

			if ( ! wp_can_install_language_pack() ) {
				$caps = array( 'do_not_allow' );
			} elseif ( is_multisite() && ! is_super_admin( $user_id ) ) {
				$caps = array( 'do_not_allow' );
			} else {
				$caps = array( $cap );
			}
			break;
	}

	return $caps;
}

function energieausweis_adjust_menu_capabilities() {
	global $menu, $submenu;

	// Tools menu.
	if ( isset( $menu[75] ) ) {
		$menu[75][1] = 'manage_setup';

		if ( isset( $submenu['tools.php'][5] ) ) {
			$submenu['tools.php'][5][1] = 'manage_setup';
		}
	}
}
