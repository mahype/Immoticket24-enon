<?php
/**
 * Connecting Items to Groups.
 *
 * All the UI elements and functionality needed to connect items
 * (like creatives) to groups.
 *
 * You can extend this class and by giving the class certain properties
 * it should integrate connection UI elements for groups for a given item,
 * e.g. `creative` would integrate this UI with creatives.
 *
 * This isn't guaranteed to work and may require adding missing filters that
 * other items may not have.
 *
 * @since 2.12.0
 *
 * @package     AffiliateWP
 * @subpackage  AffiliateWP\Admin\Groups
 * @copyright   Copyright (c) 2014, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2
 *
 * @author      Aubrey Portwood <aportwood@awesomemotive.com>
 */

// phpcs:disable PEAR.Functions.FunctionCallSignature.EmptyLine -- Empty lines okay for formatting here.

namespace AffiliateWP\Admin\Groups;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( dirname( __DIR__ ) ) . '/utils/trait-nonce.php';
require_once dirname( dirname( __DIR__ ) ) . '/utils/trait-data.php';
require_once dirname( dirname( __DIR__ ) ) . '/utils/trait-select2.php';
require_once dirname( dirname( __DIR__ ) ) . '/utils/trait-hooks.php';
require_once dirname( dirname( __DIR__ ) ) . '/utils/trait-db.php';

/**
 * Connecting Items to Groups.
 *
 * The items here are things within our own system, e.g.:
 *
 * - affiliates
 * - creatives
 * - referrals
 *
 * etc.
 *
 * If this doesn't work these items might not impliment the same hook names
 * as e.g. creatives.
 *
 * @since 2.12.0
 */
abstract class Connector {

	use \AffiliateWP\Utils\Data;
	use \AffiliateWP\Utils\Nonce;
	use \AffiliateWP\Utils\Select2;
	use \AffiliateWP\Utils\Hooks;
	use \AffiliateWP\Utils\DB;

	/**
	 * Capability for UI pages.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	protected $capability = 'administrator';

	/**
	 * The title for the Group (Singular)
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	protected $group_single = '';

	/**
	 * The title for the Group (Plural)
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	protected $group_plural = '';

	/**
	 * The group type (value for type in the groups table).
	 *
	 * This is a unqiue groups type for connecting this item
	 * to a specific group type.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	protected $group_type = '';

	/**
	 * The name of the items you can group.
	 *
	 * E.g. for grouping creatives, this would be 'cretives'.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	protected $item_plural = '';

	/**
	 * The name of the item you can group.
	 *
	 * E.g. for grouping creatives, this would be 'cretive'.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	protected $item_single = '';

	/**
	 * The item this is associated with.
	 *
	 * E.g. for Creatives Grouping it item is 'creative'.
	 *
	 * This is used to populate the column in the item's
	 * list.
	 *
	 * @see self::hooks() For more information about what this is used for.
	 *
	 * @var string
	 */
	protected $item = '';

	/**
	 * The tag for edit/new items form.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 *
	 * @see AffiliateWP\Admin\Creatives\Categories\Connect for examples of how to use these.
	 */
	protected $modify_form_tag = 'table';

	/**
	 * The class for edit/new items form.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 *
	 * @see AffiliateWP\Admin\Creatives\Categories\Connect for examples of how to use these.
	 */
	protected $modify_form_class = 'form-table';

	/**
	 * The row tag for edit/new items form.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 *
	 * @see AffiliateWP\Admin\Creatives\Categories\Connect for examples of how to use these.
	 */
	protected $modify_row_tag = 'tr';

	/**
	 * The row class for edit/new items form.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 *
	 * @see AffiliateWP\Admin\Creatives\Categories\Connect for examples of how to use these.
	 */
	protected $modify_row_class = 'form-row';

	/**
	 * The label tag for edit/new items form.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 *
	 * @see AffiliateWP\Admin\Creatives\Categories\Connect for examples of how to use these.
	 */
	protected $modify_label_tag = 'th';

	/**
	 * The content tag for edit/new items form.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 *
	 * @see AffiliateWP\Admin\Creatives\Categories\Connect for examples of how to use these.
	 */
	protected $modify_content_tag = 'td';

	/**
	 * The object property where the item stores it's ID.
	 *
	 * For a Creative object that would be `creative_id`.
	 * All our item object have an id property, e.g. `affiliate_id`,
	 * `visit_id`, `group_id`, etc.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	protected $object_id_property = '';

	/**
	 * Item's groups selected from the screen (cache).
	 *
	 * We store the REQUEST here in one hook, then access it
	 * when a later hook fires.
	 *
	 * @since 2.12.0
	 *
	 * @var array
	 */
	private $selected_item_groups = array();

	/**
	 * Constuct.
	 *
	 * @since 2.12.0
	 */
	public function __construct() {

		$this->validate_properties();
		$this->register_item_connectable();
		$this->hooks();
	}

	/**
	 * Add a column to the main list for showing groupings for each item.
	 *
	 * @since 2.12.0
	 *
	 * @param array      $columns The columns from the list table.
	 * @param \WP_Screen $screen  The screen.
	 *
	 * @return array If our item's screen, the addition of the grouping column.
	 */
	public function add_groups_column( $columns, $screen ) {

		if ( ! is_a( $screen, '\WP_Screen' ) ) {
			return $columns;
		}

		if ( "affiliates_page_affiliate-wp-{$this->item}s" !== $screen->base ) {
			return $columns; // Only add to the right screen.
		}

		if ( empty( $this->item ) ) {
			return $columns; // We will create the columns, but we need an item (e.g. creative) to add the column to.
		}

		if ( empty( $this->object_id_property ) ) {
			return $columns; // We will create the columns, but we need this to populate the value in the column.
		}

		$reordered_columns = array();

		foreach ( $columns as $column_key => $column ) {

			// We want to add it right after the shortcode column.
			if ( 'shortcode' === $column_key ) {
				$reordered_columns[ $this->get_column_name() ] = ucfirst( $this->group_plural );
			}

			$reordered_columns[ $column_key ] = $column;
		}

		return $reordered_columns;
	}

	/**
	 * Hooks
	 *
	 * @since  2.12.0
	 */
	private function hooks() {

		// Anytime we delete a group, always sever connections with it.
		add_action( 'affwp_delete_group', array( $this, 'delete_connections_to_group' ) );

		// Signal to management (AffiliateWP\Admin\Groups\Management) that we have a connector.
		add_filter( "affwp_admin_groups_management_{$this->item}_has_connector", '__return_true' );

		if ( ! $this->is_items_page() ) {
			return; // Everything below is just loaded on our item's page.
		}

		/*
		 * The below hooks are required to be implimented (by name) in our codebase.
		 *
		 * If any of them do not exist for a given item (many weren't for e.g.
		 * creatives and affiliates), implimentation of this will fail.
		 *
		 * If you are not getting proper integration in the Admin, please review these
		 * hook names per-item and ensure they are created in a similar place.
		 *
		 * All the below hooks have proper implimentation for a 'creative' item in the
		 * admin, so use those examples for the hooks below for proper impimentation.
		 *
		 * @since 2.12.0
		 */

		// Add column to items view to show the categories.
		add_filter( 'affwp_list_table_columns', array( $this, 'add_groups_column' ), 10, 2 );

		// This requires that the List_Table has a similar filter in column_default().
		add_filter( $this->filter_hook_name( "affwp_{$this->item}_table_{$this->get_column_name()}" ), array( $this, 'column_contents' ), 10, 2 );

		// Grouping UI on new and edit items.
		add_action( $this->filter_hook_name( "affwp_edit_{$this->item}_bottom" ), array( $this, 'group_multiselect' ), 10, 1 );
		add_action( $this->filter_hook_name( "affwp_new_{$this->item}_bottom" ), array( $this, 'group_multiselect' ), 10, 1 );

		// Editing items.
		add_action( $this->filter_hook_name( "affwp_add_{$this->item}" ), array( $this, 'connect_groups_to_updated_items' ), 1, 1 );
		add_action( $this->filter_hook_name( "affwp_update_{$this->item}" ), array( $this, 'connect_groups_to_updated_items' ), 1, 1 );

		// New items.
		add_action( $this->filter_hook_name( "affwp_insert_{$this->item}" ), array( $this, 'connect_groups_to_new_item' ) );

		// When deleting an item, remove any connections to the item in the connections table.
		add_action( $this->filter_hook_name( "affwp_delete_{$this->item}" ), array( $this, 'delete_connections_to_item' ) );
		add_action( $this->filter_hook_name( "affwp_{$this->item}_deleted" ), array( $this, 'delete_connections_to_item' ) );

		// Filtering by category by dropdowns.
		add_action( $this->filter_hook_name( "affwp_affiliates_page_affiliate-wp-{$this->item}s_extra_tablenav_after" ), array( $this, 'filter_dropdown' ) );
		add_action( $this->filter_hook_name( "affwp_{$this->item}s_table" ), array( $this, 'filter_items_table' ) ); // See includes/admin/creatives/creatives.php::affwp_creatives_admin() for example filter needed.

		// Load our scripts and styles for select2, etc.
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
	}

	/**
	 * Groups for the item.
	 *
	 * @since  2.12.0
	 *
	 * @param mixed $value The value.
	 * @param mixed $item  The item (must be object with ID property).
	 *
	 * @return string Content for the grouping column.
	 *
	 * @throws \InvalidArgumentException If `$item` is not an object.
	 * @throws \InvalidArgumentException If `$item` does not have an ID stored.
	 * @throws \InvalidArgumentException If `$item`'s stored ID is not a valid ID.
	 */
	public function column_contents( $value, $item ) {

		if ( ! is_object( $item ) ) {
			throw new \InvalidArgumentException( '$item needs to be an object.' );
		}

		$property = $this->object_id_property;

		if ( ! isset( $item->$property ) ) {
			throw new \InvalidArgumentException( "\$item must contain a readable property called '{$property}'." );
		}

		if ( ! $this->is_numeric_and_gt_zero( $item->$property ) ) {
			throw new \InvalidArgumentException( "\$item must have a valid ID stored in object property '{$property}'." );
		}

		$none = esc_html__( 'None', 'affiliate-wp' );

		$connected = affiliate_wp()->connections->get_connected(
			'group',
			$this->item,
			$item->$property
		);

		if ( ! is_array( $connected ) ) {
			return $none;
		}

		$groups = array();

		foreach ( $connected as $group_id ) {

			if ( ! $this->is_numeric_and_gt_zero( $group_id ) ) {
				continue;
			}

			$group_title = affiliate_wp()->groups->get_group_title( $group_id );

			if ( ! $this->is_string_and_nonempty( $group_title ) ) {
				continue;
			}

			// Form a valid nonce for filtering.
			$nonce_name  = $this->nonce_action( 'filter', 'items' );
			$nonce_value = wp_create_nonce( $nonce_name );

			// Generate a URL for filtering.
			$url = "?page=affiliate-wp-{$this->item}s&filter-{$this->item}-top={$group_id}&{$nonce_name}={$nonce_value}";

			// Add the link to the list.
			$groups[ $group_title ] = "<a href='{$url}'>{$group_title}</a>";
		}

		if ( empty( $groups ) ) {
			return $none;
		}

		ksort( $groups );

		return wp_kses(
			implode( ', ', $groups ),
			array(
				'a' => array(
					'href' => true,
				),
			)
		);
	}

	/**
	 * Connect groups to new item.
	 *
	 * When the new item form is submitted, we cache the data submitted
	 * in `$this->selected_item_groups`, and when it's finally added to the database
	 * here we take that data and connect it to the item in the database.
	 *
	 * @see self::connect_groups_to_updated_items()
	 *
	 * @since  2.12.0
	 *
	 * @param int $item_id Item ID.
	 *
	 * @throws \InvalidArgumentException If `$item_id` is not a valid ID.
	 */
	public function connect_groups_to_new_item( $item_id ) {

		if ( ! $this->is_numeric_and_gt_zero( $item_id ) ) {
			throw new \InvalidArgumentException( '$item_id must be a positive numeric value.' );
		}

		if (
			! is_array( $this->selected_item_groups ) ||
			empty( $this->selected_item_groups )
		) {
			return; // No data to save, make no changes.
		}

		// Disconnect groups that weren't selected (but selected previously).
		$this->disconnect_unselected_groups_from_item(
			$item_id,
			$this->selected_item_groups
		);

		// Connect the selected groups.
		$this->connect_groups_to_item(
			$item_id,
			$this->selected_item_groups
		);
	}

	/**
	 * Update item groups.
	 *
	 * @since  2.12.0
	 *
	 * @param array $data Data.
	 *
	 * @return array Data.
	 */
	public function connect_groups_to_updated_items( $data ) {

		if ( ! current_user_can( $this->capability ) ) {
			return $data;
		}

		if ( ! is_array( $data ) ) {
			return $data; // We expected an array, but fail gracefully (no changes).
		}

		if ( ! $this->verify_nonce_action( 'update', 'item' ) ) {
			return $data; // Nonce expired.
		}

		check_admin_referer(
			$this->nonce_action( 'update', 'item' ),
			$this->nonce_action( 'update', 'item' )
		);

		if ( ! isset( $data[ "{$this->item}_id" ] ) ) {

			// Cache the data/groups for later.
			$this->selected_item_groups = isset( $data[ "{$this->item}_groups" ] ) ? $data[ "{$this->item}_groups" ] : array();

			return $data; // Stop here, nother hook will update the data with the right ID.
		}

		if ( ! $this->is_numeric_and_gt_zero( $data[ "{$this->item}_id" ] ) ) {
			return $data; // Must be a valid ID to update the item, fail gracefully (no changes).
		}

		// Disconnect groups that might have previously been connected.
		$this->disconnect_unselected_groups_from_item(
			$data[ "{$this->item}_id" ],

			// Disconnect groups not in this list.
			isset( $data[ "{$this->item}_groups" ] )
				? $data[ "{$this->item}_groups" ]
				: array()
		);

		$connections = array();

		// Connect the groups they selected.
		$this->connect_groups_to_item(
			$data[ "{$this->item}_id" ],

			// Connect groups in this list.
			isset( $data[ "{$this->item}_groups" ] )
				? $data[ "{$this->item}_groups" ]
				: array()
		);

		if ( ! isset( $data[ "{$this->item}_groups" ] ) ) {
			return $data;
		}

		unset( $data[ "{$this->item}_groups" ] );

		return $data; // No changes to data, connections should have been formed.
	}

	/**
	 * Connect groups to an item.
	 *
	 * @since  2.12.0
	 *
	 * @param int   $item_id The item ID.
	 * @param array $groups  The Group ID's.
	 *
	 * @throws \InvalidArgumentException If you do not supply valid parameters.
	 */
	private function connect_groups_to_item( $item_id, $groups ) {

		if ( ! $this->is_numeric_and_gt_zero( $item_id ) ) {
			throw new \InvalidArgumentException( '$item_id must be a positive numeric value.' );
		}

		if ( ! is_array( $groups ) ) {
			throw new \InvalidArgumentException( '$groups must be an array of positive numeric values.' );
		}

		foreach ( $groups as $group_id ) {

			if ( ! $this->is_numeric_and_gt_zero( $group_id ) ) {
				continue; // Can't connect to this group (not a valid ID).
			}

			// Try and form a connection to this group.
			affiliate_wp()->connections->connect(
				array(
					"{$this->item}" => intval( $item_id ),
					'group'         => intval( $group_id ),
				)
			);
		}
	}

	/**
	 * Try and delete all connections to item when a group is deleted.
	 *
	 * @since  2.12.0
	 *
	 * @param int $item_id Item ID.
	 *
	 * @return void
	 */
	public function delete_connections_to_item( $item_id ) {

		if ( ! $this->is_numeric_and_gt_zero( $item_id ) ) {
			return; // Fail gracefully, it's just a stray connection.
		}

		if ( ! affiliate_wp()->connections->is_registered_connectable( $this->item ) ) {
			return; // Only delete connections that have registered connectables (to be safe).
		}

		global $wpdb;

		// Delete (try) all the things connected to this group.
		$wpdb->delete(
			affiliate_wp()->connections->table_name,
			array(
				$this->item => $item_id,
			),
			array(
				$this->item => '%d',
			)
		);
	}

	/**
	 * Try and delete all connections to group when it's deleted.
	 *
	 * @since  2.12.0
	 *
	 * @param int $group_id Group ID.
	 *
	 * @return void
	 */
	public function delete_connections_to_group( $group_id ) {

		if ( ! $this->is_numeric_and_gt_zero( $group_id ) ) {
			return; // Fail gracefully, it's just a stray connection.
		}

		global $wpdb;

		// Delete (try) all the things connected to this group.
		$wpdb->delete(
			affiliate_wp()->connections->table_name,
			array(
				'group' => $group_id,
			),
			array(
				'group' => '%d',
			)
		);
	}

	/**
	 * Disconnect groups that are not selected.
	 *
	 * @since  2.12.0
	 *
	 * @param  int   $item_id The item ID.
	 * @param  array $selected_groups  The selected groups.
	 *
	 * @throws \InvalidArgumentException If you do not supply valid parameters.
	 */
	private function disconnect_unselected_groups_from_item( $item_id, $selected_groups ) {

		if ( ! $this->is_numeric_and_gt_zero( $item_id ) ) {
			throw new \InvalidArgumentException( '$item_id must be a positive numeric value.' );
		}

		if ( ! is_array( $selected_groups ) ) {
			throw new \InvalidArgumentException( '$selected_groups must be an array of positive numeric values.' );
		}

		foreach ( affiliate_wp()->connections->get_connected(
			'group',
			$this->item,
			intval( $item_id )
		) as $group_id ) {

			if ( in_array( intval( $group_id ), array_map( 'intval', $selected_groups ), true ) ) {
				continue; // We want this one to remain connected.
			}

			// Dis-connect (try) old connections.
			affiliate_wp()->connections->disconnect(
				array(

					// creative        // = creative_id.
					"{$this->item}" => intval( $item_id ),
					'group'         => intval( $group_id ),
				)
			);
		}
	}

	/**
	 * Is the filtered group ID selected from the dropdown?
	 *
	 * @since  2.12.0
	 *
	 * @param int $group_id The group ID.
	 *
	 * @return bool
	 *
	 * @throws \InvalidArgumentException If you do not pass a positive numeric value for group ID.
	 */
	private function filtered_dropdown_group_is_selected( $group_id ) {

		if ( ! $this->is_numeric_and_gt_zero( $group_id ) ) {
			throw new \InvalidArgumentException( '$group_id must be a positive numeric value.' );
		}

		return intval( filter_input( INPUT_GET, "filter-{$this->item}-top", FILTER_SANITIZE_NUMBER_INT ) ) === intval( $group_id );
	}

	/**
	 * Filter items out that are not conected to the selected group.
	 *
	 * @since  2.12.0
	 *
	 * @param mixed $table List table instance.
	 *
	 * @return void
	 *
	 * @throws \InvalidArgumentException If $table is not a valid list table instance.
	 */
	public function filter_items_table( $table ) {

		if ( ! is_admin() ) {
			return;
		}

		if ( ! $this->is_items_page() ) {
			return;
		}

		if (
			! is_a( $table, '\AffWP\Admin\List_Table' ) ||
			! isset( $table->items )
		) {
			throw new \InvalidArgumentException( '$table must be a object that extends AffWP\Admin\List_Table.' );
		}

		if ( ! is_array( $table->items ) ) {
			return; // No way to modify items, fail gracefully.
		}

		if ( ! $this->is_numeric_and_gt_zero(
			filter_input( INPUT_GET, "filter-{$this->item}-top", FILTER_SANITIZE_NUMBER_INT )
		) ) {
			return; // A valid group ID was not sent by the filter dropdown, fail gracefully.
		}

		if ( ! $this->verify_nonce_action( 'filter', 'items' ) ) {
			return; // Nonce expired.
		}

		check_admin_referer(
			$this->nonce_action( 'filter', 'items' ),
			$this->nonce_action( 'filter', 'items' )
		);

		// Filter out any items that aren't connected to the selected group.
		$table->items = $this->filter_out_items_not_connected_to_group(
			$table->items,
			intval( $_GET[ "filter-{$this->item}-top" ] )
		);
	}

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

		$property = $this->object_id_property;

		// Filter out items that aren't connected to the group.
		foreach ( $items as $item_key => $item ) {

			if (
				! isset( $item->$property ) ||
				! $this->is_numeric_and_gt_zero( $item->$property )
			) {

				// We need e.g. creative_id to be set to a positive numeric value, fail gracefully.
				continue;
			}

			// Get the items connected to the group.
			$connected = affiliate_wp()->connections->get_connected(
				$this->item, // Get items (e.g. {creative}s).
				'group',     // Where groups.
				$group_id    // Are connected to this group ID.
			);

			if ( ! is_array( $connected ) ) {
				continue; // Nothing was found connected to the item.
			}

			if ( in_array(
				// Item ID.
				intval( $item->$property ),
				// Items connected to the group.
				array_map( 'intval', $connected ),
				true
			) ) {
				continue; // The item is connected to the group, leave it.
			}

			// Remove the item from the list, it is not connected to the group.
			unset( $items[ $item_key ] );
		}

		return $items;
	}

	/**
	 * Get the column name for the main list of items.
	 *
	 * @since  2.12.0
	 *
	 * @return string Based on `self::$item_plural`,
	 *                e.g. Creatives become 'creatives'
	 *                for the column name.
	 */
	private function get_column_name() {
		return sanitize_key( strtolower( $this->group_plural ) );
	}

	/**
	 * Get a list of all the groups.
	 *
	 * @since  2.12.0
	 *
	 * @return array
	 */
	private function get_all_groups() {

		static $cache = null;

		if ( ! is_null( $cache ) ) {
			return $cache;
		}

		// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found -- We are just caching the result at runtime.
		return $cache = affiliate_wp()->groups->get_groups(
			array(
				'fields' => 'objects',
				'type'   => $this->group_type,
			)
		);
	}

	/**
	 * UI/HTML for selecting groups for an item.
	 *
	 * @since  2.12.0
	 *
	 * @param mixed $item Item object.
	 *
	 * @throws \InvalidArgumentException If `$item` is not an object.
	 * @throws \InvalidArgumentException If `$item` does not have a proper ID property.
	 */
	public function group_multiselect( $item ) {

		$property = $this->object_id_property;

		if ( is_object( $item ) && ! isset( $item->$property ) ) {
			throw new \InvalidArgumentException( "\$item must contain a readable property called '{$property}'." );
		}

		if ( is_object( $item ) && ! $this->is_numeric_and_gt_zero( $item->$property ) ) {
			throw new \InvalidArgumentException( "\$item must have a valid ID stored in object property '{$property}'." );
		}

		$groups = $this->get_all_groups();

		/** This filter is documented in includes/admin/groups/class-management.php::broadcast_management_link(). */
		$management_link = apply_filters( strtolower( "affwp_connector_{$this->item_single}_href" ), '' );

		if ( empty( $management_link ) && empty( $groups ) ) {
			return;
		}

		?>

		<<?php echo esc_attr( $this->modify_form_tag ); ?> class="<?php echo esc_attr( $this->modify_form_class ); ?>">

			<<?php echo esc_attr( $this->modify_row_tag ); ?> class="<?php echo esc_attr( $this->modify_row_class ); ?>">

				<<?php echo esc_attr( $this->modify_label_tag ); ?> scope="row">
					<label for="<?php echo esc_attr( $this->item ); ?>_groups[]"><?php echo esc_html( ucfirst( $this->group_single ) ); ?></label>
				</<?php echo esc_attr( $this->modify_label_tag ); ?>>

				<<?php echo esc_attr( $this->modify_content_tag ); ?>>

					<!--

					Fix our multiselect select2.

					Because of how select2 formats their multiselects, it's larger
					than we want it to be. This fixes just our multiselect below.

					It's inline because it's a small fix and I didn't think it necessary
					to enqueue it.

					-->
					<style media="screen">

						.select2 .select2-selection.select2-selection--multiple {
							border: 1px solid #8c8f94 !important;
						}

						.select2-selection__choice,
						.select2-search.select2-search--inline {
							margin-bottom: 2px;
						}

						.select2-search.select2-search--inline input {
							min-height: 20px;
							height: 20px;
						}
					</style>

					<select
						name="<?php echo esc_attr( $this->item ); ?>_groups[]"
						id="<?php echo esc_attr( $this->item ); ?>_groups"
						style="min-width: 350px;"
						class="select2"
						multiple>

						<?php

						foreach ( $groups as $group ) :

							?>

							<option value="<?php echo absint( $group->get_id() ); ?>" <?php echo esc_attr( $this->is_group_selected( $group->get_id(), $item ) ? 'selected' : '' ); ?>><?php echo esc_html( wp_trim_words( $group->get_title(), 10 ) ); ?></option>

						<?php endforeach; ?>
					</select>

					<p class="description">
						<?php if ( empty( $groups ) && ! empty( $management_link ) ) : ?>
							<?php

							echo wp_kses(
								sprintf(
									/* Translators: %1$s is the grouping singular, %2$s is the item singlular. */
									__( '%1$sCreate%2$s one or more %3$s to assign them to this %4$s.', 'affiliate-wp' ),
									sprintf(
										'<a href="%s" target="_blank">',
										$management_link
									),
									'</a>',
									strtolower( $this->group_plural ),
									strtolower( $this->item_single )
								),
								array(
									'a' => array(
										'href' => true,
									),
								)
							);

							?>
						<?php else : ?>
							<?php

							/* Translators: %1$s is the grouping singular, %2$s is the item singlular. */
							echo esc_html( sprintf( __( 'Select one or more %1$s for this %2$s.', 'affiliate-wp' ), strtolower( $this->group_plural ), strtolower( $this->item_single ) ) );

							?>
						<?php endif; ?>
					</p>
				</<?php echo esc_attr( $this->modify_content_tag ); ?>>

			</<?php echo esc_attr( $this->modify_row_tag ); ?>>

			<?php

			wp_nonce_field(
				$this->nonce_action( 'update', 'item' ),
				$this->nonce_action( 'update', 'item' )
			);

			?>
		</<?php echo esc_attr( $this->modify_form_tag ); ?>>

		<?php
	}

	/**
	 * Are we on our admin page?
	 *
	 * @since  2.12.0
	 *
	 * @return bool
	 *
	 * @throws \InvalidArgumentException If you pass a non-string to `$page`.
	 */
	private function is_items_page() {

		return "affiliate-wp-{$this->item}s"
			=== filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
	}

	/**
	 * Items filtering dropdown.
	 *
	 * @since  2.12.0
	 *
	 * @param string $which Either `top` or `bottom`.
	 * @return void
	 */
	public function filter_dropdown( $which ) {

		if ( 'bottom' === $which ) {
			return; // Never on the bottom.
		}

		$groups = $this->get_all_groups();

		if ( empty( $groups ) ) {
			return; // No groups to select.
		}

		?>

		<select name="filter-<?php echo esc_attr( $this->item ); ?>-<?php echo esc_attr( $which ); ?>">

			<option value="">
				<?php

				// Translators: %s is the translated name of the group, e.g. Categories.
				echo esc_html( sprintf( __( 'All %s', 'affiliate-wp' ), $this->group_plural ) );

				?>
			</option>

			<?php foreach ( $groups as $group ) : ?>
				<?php

				$group_id = $group->get_id();

				?>

				<option
					<?php echo esc_attr( $this->filtered_dropdown_group_is_selected( $group_id ) ? 'selected' : '' ); ?>
					value="<?php echo absint( $group_id ); ?>">
						<?php echo esc_html( wp_trim_words( $group->get_title(), 10 ) ); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<?php

		wp_nonce_field(
			$this->nonce_action( 'filter', 'items' ),
			$this->nonce_action( 'filter', 'items' )
		);

		?>

		<input type="submit" class="button action" value="<?php esc_html_e( 'Filter', 'affiliate-wp' ); ?>">

		<?php
	}

	/**
	 * Is a group selected?
	 *
	 * @since  2.12.0
	 *
	 * @param int    $group_id Group ID.
	 * @param object $item     Item object.
	 *
	 * @return bool `false` if we can't figure out if it's selected.
	 *              `false` if it's not selected.
	 *              `true` if it is selected.
	 */
	private function is_group_selected( $group_id, $item ) {

		if ( ! $this->is_numeric_and_gt_zero( $group_id ) ) {
			return false;
		}

		if ( ! is_object( $item ) ) {
			return false;
		}

		// Get the creatives that is connected to the group.
		$connected_items = affiliate_wp()->connections->get_connected(
			$this->item,
			'group',
			$group_id
		);

		$object_id_property = $this->object_id_property;

		if ( ! isset( $item->$object_id_property ) ) {
			return false; // No way to know if the item object was selected.
		}

		return in_array(
			intval( $item->$object_id_property ),
			array_map( 'intval', $connected_items ),
			true
		);
	}

	/**
	 * Register the item as connectable.
	 *
	 * @since 2.12.0
	 *
	 * @return void If it's already registered as a connectable.
	 *
	 * @throws \Exception If we can't register item as a connectable.
	 */
	private function register_item_connectable() {

		if ( affiliate_wp()->connections->is_registered_connectable( $this->item ) ) {
			return; // Already registered, move on gracefully.
		}

		$api = "{$this->item}s"; // We always name them plural.

		if (
			is_multisite() &&
			! $this->table_exists( affiliate_wp()->$api->table_name ) &&
			is_callable( array( affiliate_wp()->$api, 'create_table' ) )
		) {

			// Try and create the table for this item if we can.
			affiliate_wp()->$api->create_table();
		}

		// Creatives.
		$items = affiliate_wp()->connections->register_connectable(
			array(
				'name'   => $this->item,
				'table'  => affiliate_wp()->$api->table_name,
				'column' => affiliate_wp()->$api->primary_key,
			)
		);

		if ( true === $items ) {
			return; // Done.
		}

		throw new \Exception( "Unable to register {$this->item} as connectable." );
	}

	/**
	 * Scripts
	 *
	 * @since  2.12.0
	 *
	 * @return void Only happens on our items page(s).
	 */
	public function scripts() {

		if ( ! $this->is_items_page() ) {
			return;
		}

		$this->enqueue_select2(
			'.select2',
			array(
				'disabled' => empty( $this->get_all_groups() ),
			),
			'label[for="creative_groups[]"]'
		);
	}

	/**
	 * Validate instance properties.
	 *
	 * @since  2.12.0
	 *
	 * @throws \InvalidArgumentException When invalid properties are found.
	 */
	private function validate_properties() {

		if ( ! $this->is_string_and_nonempty( $this->item ) ) {
			throw new \InvalidArgumentException( "self::item needs to be a non-empty string set to the item it's associated with like 'creative' or 'affiliate'." );
		}

		if ( ! $this->is_string_and_nonempty( $this->object_id_property ) ) {
			throw new \InvalidArgumentException( "self::object_id_property needs to be a non-empty string set to the item's property where it's ID is stored." );
		}

		if ( ! $this->is_string_and_nonempty( $this->capability ) ) {
			throw new \InvalidArgumentException( '$this->capability must be a non-empty string.' );
		}

		if ( ! $this->is_string_and_nonempty( $this->item_plural ) ) {
			throw new \InvalidArgumentException( '$this->item_plural must be a non-empty string.' );
		}

		if ( ! $this->is_string_and_nonempty( $this->item_single ) ) {
			throw new \InvalidArgumentException( '$this->item_single must be a non-empty string.' );
		}

		if ( ! $this->is_string_and_nonempty( $this->item ) ) {
			throw new \InvalidArgumentException( '$this->item must be a non-empty string.' );
		}

		if ( ! $this->is_string_and_nonempty( $this->object_id_property ) ) {
			throw new \InvalidArgumentException( '$this->object_id_property must be a non-empty string.' );
		}

		if ( ! $this->is_string_and_nonempty( $this->group_plural ) ) {
			throw new \InvalidArgumentException( '$this->group_plural must be a non-empty string.' );
		}

		if ( ! $this->is_string_and_nonempty( $this->group_single ) ) {
			throw new \InvalidArgumentException( '$this->group_single must be a non-empty string.' );
		}

		if ( ! $this->is_string_and_nonempty( $this->group_type ) ) {
			throw new \InvalidArgumentException( '$this->group_type must be a non-empty string.' );
		}
	}
}
