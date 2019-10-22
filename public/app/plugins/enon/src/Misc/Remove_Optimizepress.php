<?php

namespace Enon\Misc;

use Awsm\WP_Plugin\Building_Plans\Hooks_Actions;
use Awsm\WP_Plugin\Building_Plans\Hooks_Filters;
use Awsm\WP_Plugin\Loaders\Hooks_Loader;
use Awsm\WP_Plugin\Loaders\Loader;

/**
 * Class Performance
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Remove_Optimizepress implements Hooks_Actions, Hooks_Filters {
	use Loader, Hooks_Loader;

	/**
	 * Add actions.
	 *
	 * @since 1.0.0
	 */
	public static function add_actions() {
		if ( is_admin() ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'remove_scripts' ), 10000 );
		add_action( 'wp_print_styles', array( __CLASS__, 'remove_scripts' ), 10000 );
	}

	/**
	 * Add filtters.
	 *
	 * @since 1.0.0
	 */
	public static function add_filters() {
		if ( is_admin() ) {
			return;
		}

		add_filter( 'immoticketenergieausweis_stylesheet_dependencies', array( __CLASS__, 'remove_depencies' ), 1 );
	}


	/**
	 * Removing depencies from Scripts.
	 *
	 * @since 1.0.0
	 */
	public static function remove_depencies( $depencies ) {
		$page_id =  get_the_ID();
		$ban_page_ids = array( 294865 );

		if( in_array( $page_id, $ban_page_ids ) ) {
			remove_filter( 'immoticketenergieausweis_stylesheet_dependencies', 'immoticketenergieausweis_optimizepress_add_dependencies' );
		}

		return $depencies;
	}

	/**
	 * Removing Scripts.
	 *
	 * @since 1.0.0
	 */
	public static function remove_scripts() {
		global $wp_styles, $wp_scripts;

		if( ! is_page() || ! defined( 'OP_SN' ) ) {
			return;
		}

		$page_id =  get_the_ID();
		$ban_page_ids = array( 294865, 300047 );

		$wp_styles->dequeue( 'op_map_custom' );
		$wp_styles->remove( 'op_map_custom' );

		if( in_array( $page_id, $ban_page_ids ) ) {
			foreach( $wp_scripts->queue as $handle ) {
				if( substr( $handle,0 , strlen(OP_SN ) ) === OP_SN ) {
					$wp_scripts->dequeue( $handle );
					$wp_scripts->remove( $handle );
				}
			}

			foreach( $wp_styles->queue as $handle ) {
				if( substr( $handle,0 , strlen(OP_SN ) ) === OP_SN ) {
					$wp_styles->dequeue( $handle );
					$wp_scripts->remove( $handle );
				}
			}
		}
	}
}
