<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- The name of the tile is common among others.
/**
 * Connection Utilities
 *
 * @package     AffiliateWP
 * @subpackage  Database
 * @copyright   Copyright (c) 2020, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.12.0
 * @author      Aubrey Portwood <aubrey@awesomeomotive.com>
 */

namespace AffiliateWP\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( trait_exists( '\AffiliateWP\Utils\Connection' ) ) {
	return;
}

require_once __DIR__ . '/trait-data.php';

/**
 * Connection Utilities
 *
 * @since 2.12.0
 */
trait Connection {

	use \AffiliateWP\Utils\Data;

	/**
	 * Filter items (from list table) out that are not connected to a group.
	 *
	 * @since  2.12.0
	 *
	 * @param array $items    The items from the list table, usually `$table->items`.
	 * @param int   $group_id The group ID.
	 *
	 * @return array The items array, with items filtered out that aren't connected.
	 *
	 * @throws \InvalidArgumentException When you don't pass valid arguments.
	 */
	private function filter_out_items_not_connected_to_group( $items, $group_id ) {

		if ( ! is_array( $items ) ) {
			throw new \InvalidArgumentException( '$items must be an array.' );
		}

		if ( ! $this->is_numeric_and_gt_zero( $group_id ) ) {
			throw new \InvalidArgumentException( '$group_id must be a positive numeric value.' );
		}

		if ( ! isset( $this->object_id_property ) || ! $this->is_string_and_nonempty( $this->object_id_property ) ) {
			throw new \InvalidArgumentException( "\$this must contain an 'object_id_property' property that correlates to the property on the item object's ID." );
		}

		$property = $this->object_id_property;

		// Filter out items that aren't connected to the group.
		foreach ( $items as $item_key => $item ) {

			if (
				! isset( $item->$property ) ||
				! $this->is_numeric_and_gt_zero( $item->$property )
			) {

				// We need e.g. creative_id to be set to a positive numeric value.
				continue;
			}

			$connected = affiliate_wp()->connections->get_connected(
				'creative', // Get creatives.
				'group', // Where groups.
				$group_id // Are connected to this group ID.
			);

			if ( ! is_array( $connected ) ) {
				continue;
			}

			if ( in_array( intval( $item->$property ), array_map( 'intval', $connected ), true ) ) {
				continue; // It's connected to the group, leave it there.
			}

			// Remove the item from the array, it is not connected to the filtered group.
			unset( $items[ $item_key ] );
		}

		return $items;
	}
}
