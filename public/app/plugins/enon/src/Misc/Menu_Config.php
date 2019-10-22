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
class Menu_Config implements Hooks_Filters{
	use Loader, Hooks_Loader;

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public static function add_filters() {
		add_filter( 'wp_nav_menu_objects', array( __CLASS__, 'filter_main_menu' ), 10, 2 );
	}

	/**
	 * Filtering main menu.
	 *
	 * @since 1.0.0
	 *
	 * @param array     $sorted_menu_items The menu items, sorted by each menu item's menu order.
	 * @param \stdClass $args              An object containing wp_nav_menu() arguments.
	 * @return array    $sorted_menu_items The filtered menu items.
	 */
	public static function filter_main_menu( $sorted_menu_items, $args ) {
		// Only showing "Gewerbeschein senden" on "Für Makler" page.
		if ( 'primary' === $args->theme_location && 23110 !== get_the_ID() ) {
			$sorted_menu_items = self::remove_entry_by_title( $sorted_menu_items, 'Gewerbeschein senden' );
		}
		return $sorted_menu_items;
	}

	/**
	 * Removing entry by title.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $sorted_menu_items The menu items, sorted by each menu item's menu order.
	 * @param string $title The title to remove.
	 * @return array $sorted_menu_items The menu items without item with specific title.
	 */
	public static function remove_entry_by_title( $sorted_menu_items, $title ) {
		foreach ( $sorted_menu_items as $key => $item ) {
			if ( $item->title === $title ) {
				unset( $sorted_menu_items[ $key ] );
			}
		}
		return $sorted_menu_items;
	}
}
