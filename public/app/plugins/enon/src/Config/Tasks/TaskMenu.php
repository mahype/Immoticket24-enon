<?php

namespace Enon\Config\Tasks;

use Awsm\WP_Plugin\Building_Plans\Hooks_Filters;
use Awsm\WP_Plugin\Building_Plans\Service;
use Awsm\WP_Plugin\Loaders\Hooks_Loader;
use Awsm\WP_Plugin\Loaders\Loader;
use Awsm\WPWrapper\BuildingPlans\Filters;
use Awsm\WPWrapper\BuildingPlans\Task;

/**
 * Class Performance
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class TaskMenu implements Filters, Task {
	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->addFilters();
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function addFilters() {
		add_filter( 'wp_nav_menu_objects', array( __CLASS__, 'filterMainMenu' ), 10, 2 );
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
	public static function filterMainMenu( $sorted_menu_items, $args ) {
		// Only showing "Gewerbeschein senden" on "Für Makler" page.
		if ( 'primary' === $args->theme_location && 23110 !== get_the_ID() ) {
			$sorted_menu_items = self::removeEntryByTitle( $sorted_menu_items, 'Gewerbeschein senden' );
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
	public static function removeEntryByTitle( $sorted_menu_items, $title ) {
		foreach ( $sorted_menu_items as $key => $item ) {
			if ( $item->title === $title ) {
				unset( $sorted_menu_items[ $key ] );
			}
		}
		return $sorted_menu_items;
	}
}
