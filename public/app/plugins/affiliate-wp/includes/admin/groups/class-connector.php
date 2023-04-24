<?php
/**
 * Connecting Items to Groups.
 *
 * Note, this does not connect items to items.
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
// phpcs:disable PEAR.Functions.FunctionCallSignature.FirstArgumentPosition -- Empty lines are okay.

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
	 * Name of the column to add groups column after.
	 *
	 * @since 2.13.0
	 *
	 * @var string
	 */
	protected $groups_column_before = '';

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
	 * Selector type.
	 *
	 * Can be multiple or single.
	 *
	 * @since 2.13.0
	 *
	 * @var string
	 */
	protected $selector_type = 'multiple';

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

		$column_title = apply_filters(
			'affwp_connector_column',
			ucfirst(
				'multiple' === $this->selector_type
					? $this->group_plural
					: $this->group_single
			),
			$this->group_type,
			$this->item,
		);

		if ( empty( $this->groups_column_before ) ) {

			return array_merge(
				$columns,
				array(
					$this->get_column_name() => $column_title,
				)
			);
		}

		$reordered_columns = array();

		foreach ( $columns as $column_key => $column ) {

			if ( ! $this->is_string_and_nonempty( $this->groups_column_before ) ) {
				continue; // Fail gracefully.
			}

			// Add the column before column specified.
			if ( $this->groups_column_before === $column_key ) {
				$reordered_columns[ $this->get_column_name() ] = $column_title;
			}

			// Insert the other columns right after.
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
		add_action( $this->filter_hook_name( "affwp_edit_{$this->item}_bottom" ), array( $this, 'group_selector' ), 10, 1 );
		add_action( $this->filter_hook_name( "affwp_new_{$this->item}_bottom" ), array( $this, 'group_selector' ), 10, 1 );

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
		add_action( $this->filter_hook_name( "affwp_{$this->item}_table_get_{$this->item}s" ), array( $this, 'filter_table_get_items_args' ) ); // See includes/admin/creatives/creatives.php::affwp_creatives_admin() for example filter needed.

		// Load our scripts and styles for select2, etc.
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );

		// Add bulk actions for assigning bulk items to group.
		add_action( $this->filter_hook_name( "affwp_{$this->item}s_bulk_actions" ), array( $this, 'add_assign_to_bulk_actions' ) );
		add_action( $this->filter_hook_name( "affwp_{$this->item}_bulk_actions" ), array( $this, 'add_assign_to_bulk_actions' ) );

		foreach ( $this->get_assign_to_bulk_items() as $action => $name ) {
			add_action( $this->filter_hook_name( "affwp_{$this->item}s_do_bulk_action_{$action}" ), array( $this, 'connect_bulk_applied_group_to_item' ) );
		}
	}

	/**
	 * Connect a bulk selected item to the applied group.
	 *
	 * @since 2.13.0
	 *
	 * @param int $item_id The ID of the item.
	 */
	public function connect_bulk_applied_group_to_item( int $item_id ) : void {

		if ( ! $this->user_has_capability() ) {
			return;
		}

		$group_id = $this->get_assign_to_id_from_action();

		if (
			! $this->is_numeric_and_gt_zero( $group_id ) ||
			! $this->is_numeric_and_gt_zero( $item_id )
		) {

			// We need a valid group id and item id to connect.
			return;
		}

		// Single types can only assign one group to the item.
		if ( 'single' === $this->selector_type ) {

			// First disconnect the affiliate from ALL groups (we'll connect the right one later).
			foreach (

				// Make sure connected groups are of the same type.
				affiliate_wp()->groups->filter_groups_by_type(

					// Get any groups already connected to the item id.
					affiliate_wp()->connections->get_connected(
						'group',
						$this->item,
						$item_id
					),

					// Match this group type.
					$this->group_type
				) as $prev_connected_group_id
			) {

				// Skip the group we want to eventually connect.
				if ( intval( $group_id ) === intval( $prev_connected_group_id ) ) {
					continue; // Don't bother disconnecting, just to connect it later.
				}

				// Disconnect the previously connected group.
				affiliate_wp()->connections->disconnect(
					array(
						'group'     => $prev_connected_group_id,
						$this->item => $item_id,
					)
				);
			}
		}

		// Connect the item to the group id (note: no disconnecting, so append).
		affiliate_wp()->connections->connect(
			array(
				'group'     => intval( $group_id ),
				$this->item => $item_id,
			)
		);
	}

	/**
	 * Get the assign to ID from the bulk action.
	 *
	 * @since 2.13.0
	 *
	 * @return int The ID, a negative number if no ID sent.
	 */
	private function get_assign_to_id_from_action() : int {

		$action = filter_input( INPUT_GET, 'action', FILTER_UNSAFE_RAW );

		if ( ! strstr( $action, "assign-{$this->group_type}:" ) ) {
			return -1; // Not an assignment.
		}

		$group_id = str_replace( "assign-{$this->group_type}:", '', $action );

		if ( ! $this->is_numeric_and_gt_zero( $group_id ) ) {
			return -2; // The ID is not a valid id.
		}

		if ( ! affiliate_wp()->groups->group_exists( $group_id ) ) {
			return -3; // The selected group ID doesn't lead to a group in the DB.
		}

		return intval( $group_id );
	}

	/**
	 * Add bulk action items for assigning items.
	 *
	 * @since 2.13.0
	 *
	 * @param array $actions Default actions.
	 *
	 * @return array
	 */
	public function add_assign_to_bulk_actions( array $actions ) : array {

		return array_merge(
			$actions,
			$this->get_assign_to_bulk_items()
		);
	}

	/**
	 * Get all the assign to bulk options.
	 *
	 * @since 2.13.0
	 *
	 * @return array
	 */
	private function get_assign_to_bulk_items() : array {

		$assign_to_groups = array();

		foreach ( $this->get_all_groups() as $group ) {

			// Add bulk item value (used to assign) and output name.
			$assign_to_groups[ "assign-{$this->group_type}:{$group->get_id()}" ] = esc_html(
				sprintf(

					// Translators: %1$s is the name of the type (singular), %2$s is the name of the group.
					__( 'Assign %1$s: %2$s', 'affiliate-wp' ),
					ucwords( $this->group_single ),
					$group->get_title()
				)
			);
		}

		/**
		 * Filter bulk items shown for the connector.
		 *
		 * @param array  $items      The bulk items the connector adds.
		 * @param string $group_type The group type of the connector.
		 * @param string $item       The item of the connector.
		 *
		 * phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found -- Used to cache.
		 */
		return apply_filters( 'affwp_connector_bulk_items', $assign_to_groups, $this->group_type, $this->item );
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

		/**
		 * Filter the value for None when there are no groups.
		 *
		 * @since 2.13.0
		 *
		 * @param string $none       The value for None when there are none.
		 * @param string $group_type The group type of the connector.
		 * @param string $item       The item of the connector.
		 */
		$none = apply_filters(
			'affwp_connector_column_contents_none',
			esc_html__( 'None', 'affiliate-wp' ),
			$this->group_type,
			$this->item
		);

		// Get connected groups to the item.
		$connected_groups = array_filter(
			affiliate_wp()->connections->get_connected(
				'group',
				$this->item,
				$item->$property
			),
			function( $group_id ) {

				// Only get connected groups of the same type.
				return affiliate_wp()->groups->get_group_type( $group_id ) === $this->group_type;
			}
		);

		if ( ! is_array( $connected_groups ) ) {

			/** This filter is documented in the return below. */
			return apply_filters(
				'affwp_connector_column_contents',
				$none,
				$this->group_type,
				$this->item,
				$item->$property
			);
		}

		/**
		 * Filter the groups sown in the connector column.
		 *
		 * You can use this to inject other groups of the same group type
		 * to be shown (and formatted) below.
		 *
		 * @since 2.13.0
		 *
		 * @param array  $connected_groups Group ID's of groups to show.
		 * @param string $group_type       Group type of the connector.
		 * @param string $item             The item of the connector.
		 * @param int    $item_id          The ID of the item.
		 */
		$connected_groups = apply_filters(
			'affwp_group_connector_column_value_before_titled_groups',
			$connected_groups,
			$this->group_type,
			$this->item,
			$item->$property
		);

		$groups = array();

		foreach ( $connected_groups as $group_id ) {

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
			$url = "?page=affiliate-wp-{$this->item}s&filter-{$this->item}-{$this->group_type}-top={$group_id}&{$nonce_name}={$nonce_value}";

			// Add the link to the list.
			$groups[ $group_title ] = "<a href='{$url}'>{$group_title}</a>";
		}

		if ( empty( $groups ) ) {

			/** This filter is documented in the return below. */
			return apply_filters(
				'affwp_connector_column_contents',
				$none,
				$this->group_type,
				$this->item,
				$item->$property
			);
		}

		ksort( $groups );

		return wp_kses(

			/**
			 * Filter the column contents.
			 *
			 * @param string $contents   The contents.
			 * @param string $group_type The connector group type.
			 * @param string $item       The item for the connector.
			 * @param int    $item_id    The ID of the item shown.
			 *
			 * @since 2.13.0
			 */
			apply_filters(
				'affwp_connector_column_contents',
				implode(
					', ',
					array_map(
						function( $group ) {

							/**
							 * Filter the group title shown in the column.
							 *
							 * @since 2.13.0
							 *
							 * @param string $group_title Group title.
							 * @param string $group_type  Group type.
							 */
							return apply_filters(
								'affwp_group_connector_after_column_group_title',
								$group,
								$this->group_type,
								$this->item
							);
						},
						$groups
					)
				),
				$this->group_type,
				$this->item,
				$item->$property
			),

			/**
			 * Filter the allowed HTML for the group title.
			 *
			 * @since 2.13.0
			 *
			 * @param array $allowed_html Allowed HTML for `wp_kses()`.
			 */
			apply_filters(
				'affwp_group_connector_after_column_group_title_kses',
				array(
					'a'      => array(
						'href'   => true,
						'target' => true,
						'title'  => true,
						'class'  => true,
					),
					'br'     => true,
					'strong' => array(
						'title' => true,
						'class' => true,
					),
					'span'   => array(
						'class' => true,
						'title' => true,
					),
					'div'    => array(
						'class' => true,
						'title' => true,
					),
					'em'     => array(
						'class' => true,
						'title' => true,
					),
					'hr'     => array(
						'class' => true,
						'title' => true,
					),
				)
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

		if ( ! is_array( $this->selected_item_groups ) ) {
			return; // No data to save, make no changes.
		}

		/**
		 * Filter the groups for new item.
		 *
		 * @since 2.13.0
		 *
		 * @param array  $groups     Array of values sent via `POST` (cached for new item).
		 * @param string $group_Type The connector group type.
		 * @param string $item       The item of the connector.
		 * @param int    $item_id    The ID of the item.
		 */
		$this->selected_item_groups = apply_filters(
			'affwp_group_connector_item_groups',
			$this->selected_item_groups,
			$this->group_type,
			$this->item,
			isset( $item_id )
				? intval( $item_id )
				: 0
		);

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

		if ( ! $this->user_has_capability() ) {
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

		if (
			! isset( $data[ "{$this->item}_id" ] ) &&
			! isset( $data[ "{$this->item}_{$this->group_type}_groups" ] )
		) {
			return $data; // New item and no groups selected for that new item.
		}

		if (
			isset( $data[ "{$this->item}_id" ] ) &&
			! isset( $data[ "{$this->item}_{$this->group_type}_groups" ] )
		) {

			// Disconnect all groups from this item, none were sent.
			$this->disconnect_unselected_groups_from_item(
				intval( $data[ "{$this->item}_id" ] ),
				array()
			);

			/** This filter is documented in this class, see `self::connect_groups_to_new_item( $item_id )`. */
			$groups = apply_filters(
				'affwp_group_connector_item_groups',
				array(),
				$this->group_type,
				$this->item,
				isset( $data[ "{$this->item}_id" ] )
					? intval( $data[ "{$this->item}_id" ] )
					: 0
			);

			return $data; // No changes to current groups.
		}

		if ( ! isset( $data[ "{$this->item}_id" ] ) ) {

			// Cache the data/groups for later (new item).
			$this->selected_item_groups = $data[ "{$this->item}_{$this->group_type}_groups" ];

			// Stop here, nother hook will update the data with the right ID.
			return $data;
		}

		/** This filter is documented in this class, see `self::connect_groups_to_new_item( $item_id )`. */
		$groups = apply_filters(
			'affwp_group_connector_item_groups',
			$data[ "{$this->item}_{$this->group_type}_groups" ],
			$this->group_type,
			$this->item,
			isset( $data[ "{$this->item}_id" ] )
				? intval( $data[ "{$this->item}_id" ] )
				: 0
		);

		$item_id = $data[ "{$this->item}_id" ];

		if ( ! $this->is_numeric_and_gt_zero( $item_id ) ) {
			return $data; // Must be a valid ID to update the item, fail gracefully (no changes).
		}

		// Disconnect groups that might have previously been connected.
		$this->disconnect_unselected_groups_from_item(
			$item_id,
			$groups // Disconnect groups not in this list from this item.
		);

		// Connect the groups they selected.
		$this->connect_groups_to_item(
			$item_id,
			$groups // Connect groups in this list to this item.
		);

		if ( isset( $data[ "{$this->item}_{$this->group_type}_groups" ] ) ) {
			unset( $data[ "{$this->item}_{$this->group_type}_groups" ] );
		}

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

		if ( ! $this->user_has_capability() ) {
			return;
		}

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

			$group = affiliate_wp()->groups->get_group( $group_id );

			if ( $group->get_type() !== $this->group_type ) {
				continue; // Don't connect a group that isn't of the type setup.
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

		if ( ! $this->user_has_capability() ) {
			return;
		}

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

		if ( ! $this->user_has_capability() ) {
			return;
		}

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

		if ( ! $this->user_has_capability() ) {
			return;
		}

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
				continue; // We want this one to remain connected, don't dis-connect.
			}

			$group = affiliate_wp()->groups->get_group( $group_id );

			if ( $group->get_type() !== $this->group_type ) {
				continue; // Don't disconnect this group, it's a different type that's connected.
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

		return intval( filter_input( INPUT_GET, $this->get_filter_dropdown_key(), FILTER_SANITIZE_NUMBER_INT ) ) === intval( $group_id );
	}

	/**
	 * The key we use to store the selected group from the drop-down.
	 *
	 * @since 2.13.0
	 *
	 * @return string
	 */
	protected function get_filter_dropdown_key() : string {
		return "filter-{$this->item}-{$this->group_type}-top";
	}

	/**
	 * Filter arguments to get items on the list table.
	 *
	 * @since 2.13.0
	 *
	 * @param array $args Arguments used by default.
	 *
	 * @return array
	 */
	public function filter_table_get_items_args( $args ) : array {

		if ( ! is_admin() ) {
			return $args;
		}

		if ( ! $this->is_items_page() ) {
			return $args;
		}

		if ( ! $this->verify_nonce_action( 'filter', 'items' ) ) {
			return $args; // Nonce expired.
		}

		check_admin_referer(
			$this->nonce_action( 'filter', 'items' ),
			$this->nonce_action( 'filter', 'items' )
		);

		if ( $this->no_group_selected() ) {

			// Show only items that have no groups (by excluding any that have groups).
			return $this->apply_filters_to_table_get_items_args(
				array_merge(
					$args,
					array(
						'exclude' => $this->get_items_with_groups(),
					)
				)
			);
		}

		$selected_group = $this->get_selected_group();

		if ( ! $this->is_numeric_and_gt_zero( $selected_group ) ) {
			return $this->apply_filters_to_table_get_items_args( $args ); // Not a group id, can't help you, but still filter.
		}

		$group_items = $this->get_items_by_group_id( $selected_group );

		// Show all items, including those (exclusively) that are connected to the selected group.
		return $this->apply_filters_to_table_get_items_args(
			array_merge(
				$args,

				// If there are no items connected to the group, show no items.
				empty( $group_items )
					? array(

						// Use this for force no items from showing.
						"{$this->item}_id" => -1,
					)

					// Show, inclusively, items found in that group.
					: array(
						'include' => $group_items,
					)
			)
		);
	}

	/**
	 * Get items connected to specific group (by id).
	 *
	 * @since 2.13.0
	 *
	 * @param [type] $group_id The group id.
	 *
	 * @return array Items connected to the group.
	 */
	private function get_items_by_group_id( $group_id ) : array {

		return array_map(
			'intval',
			affiliate_wp()->connections->get_connected(
				$this->item,
				'group',
				intval( $group_id )
			)
		);
	}

	/**
	 * Filter arguments for the table items (arguments).
	 *
	 * @since 2.13.0
	 *
	 * @param array $args Arguments that will ultimately decide what items show on the table.
	 *
	 * @return array Filtered arguments.
	 */
	private function apply_filters_to_table_get_items_args( array $args ) : array {

		/**
		 * Filter arguments.
		 *
		 * @since 2.13.0
		 *
		 * @param array  $args       Arguments
		 * @param string $group_type Group type of the connector.
		 * @param string $item       Item of the connector.
		 */
		return apply_filters(
			'affwp_connector_filter_table_get_items_args',
			$args,
			$this->group_type,
			$this->item
		);
	}

	/**
	 * Get the selected group.
	 *
	 * @since 2.13.0
	 *
	 * @return string
	 */
	private function get_selected_group() : string {

		$get = filter_input( INPUT_GET, $this->get_filter_dropdown_key(), FILTER_UNSAFE_RAW );

		return is_string( $get ) ? trim( $get ) : '';
	}

	/**
	 * Was no group (-1) selected from the drop-down?
	 *
	 * @since 2.13.0
	 *
	 * @return bool
	 */
	private function no_group_selected() : bool {
		return -1 === intval( $this->get_selected_group() );
	}

	/**
	 * Get the method used in the DB class for getting items.
	 *
	 * @since 2.13.0
	 *
	 * @return string Always something like `get_creatives`, or `get_affiliates`, etc.
	 */
	private function get_items_method() : string {
		return "get_{$this->item}s";
	}

	/**
	 * Wrapper method for callback on the DB class for the item.
	 *
	 * E.g. For creatives this would be `get_creatives()`, for
	 * affiliates this would be `get_affiliates()`.
	 *
	 * @since 2.13.0
	 *
	 * @param array $args The arguments we would pass to the get method.
	 *
	 * @return mixed Results of the get method.
	 */
	private function get_items( array $args ) {

		$get_items_cb = $this->get_items_method();

		return $this->get_item_api()->$get_items_cb( $args );
	}

	/**
	 * Get items that have no group connection.
	 *
	 * @since 2.13.0
	 *
	 * @return array Id's of items with no group connection.
	 */
	private function get_items_with_groups() : array {

		static $cache = null;

		if ( is_array( $cache ) ) {
			return $cache;
		}

		// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found -- Used for caching.
		return $cache = array_filter(
			$this->get_all_item_ids(),
			function( $item_id ) {

				// Items with no groups...
				return empty( $this->get_groups_by_item_id( $item_id ) )
					? false // Are NOT included.
					: true; // Are included.
			}
		);
	}

	/**
	 * Get all the items (by id).
	 *
	 * @since 2.13.0
	 *
	 * @return array
	 */
	private function get_all_item_ids() : array {

		static $cache = null;

		if ( ! is_null( $cache ) ) {
			return $cache;
		}

		// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found -- For caching.
		return $cache = $this->get_items(
			array(
				'number' => apply_filters( 'affwp_unlimited', -1, 'abstract_connector_get_all_item_ids_number' ),
				'fields' => 'ids',
			)
		);
	}

	/**
	 * Get groups by item id.
	 *
	 * This always makes sure the groups that are connected
	 * to the item are also of the set group type.
	 *
	 * @since 2.13.0
	 *
	 * @param int $item_id The item's id.
	 *
	 * @return array
	 */
	private function get_groups_by_item_id( int $item_id ) : array {

		return affiliate_wp()->groups->filter_groups_by_type(
			affiliate_wp()->connections->get_connected(
				'group',
				$this->item,
				intval( $item_id )
			),
			$this->group_type
		);
	}

	/**
	 * Get the item's API.
	 *
	 * @since 2.13.0
	 *
	 * @return mixed `false` if there isn't one, the object if there is.
	 */
	private function get_item_api() {

		$api = "{$this->item}s";

		if ( ! isset( affiliate_wp()->$api ) ) {
			return false;
		}

		return affiliate_wp()->$api;
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
	 * @since  2.13.0 Added `$sorted` option.
	 *
	 * @param string $sorted Format, accepts alpha which sorts them alphabetically.
	 *
	 * @return array
	 */
	private function get_all_groups( string $sorted = 'alpha' ) {

		if ( empty( trim( $sorted ) ) ) {
			$sorted = 'none'; // So we can cache unsorted groups.
		}

		$groups = affiliate_wp()->groups->get_groups(
			array(
				'fields' => 'objects',
				'type'   => $this->group_type,
			)
		);

		// Sort the groups alphabetically.
		if ( 'alpha' === $sorted ) {

			$alpha = array();

			foreach ( $groups as $group ) {
				$alpha[ $group->get_title() ] = $group;
			}

			ksort( $alpha );

			// Return alpha groups (alpha).
			return $alpha; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found -- We are just caching the result at runtime.
		}

		return $groups;
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
	public function group_selector( $item ) {

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

		/**
		 * Should the selector be disabled or not?
		 *
		 * @since 2.13.0
		 *
		 * @param bool   $disabled   Set to `true` if there are no groups to select.
		 * @param string $group_type Group type of the selector.
		 * @param string $item       Item of the selector.
		 *
		 * @var [type]
		 */
		$disabled = apply_filters(
			'affwp_connector_group_selector_disabled',
			empty( $this->get_all_groups() ),
			$this->group_type,
			$this->item
		);

		?>

		<?php if ( ! empty( $this->modify_form_tag ) ) : ?>
			<<?php echo esc_attr( $this->modify_form_tag ); ?> class="<?php echo esc_attr( $this->modify_form_class ); ?>">
		<?php endif; ?>

			<?php if ( ! empty( $this->modify_row_tag ) ) : ?>
				<<?php echo esc_attr( $this->modify_row_tag ); ?> class="<?php echo esc_attr( $this->modify_row_class ); ?>">
			<?php endif; ?>

				<<?php echo esc_attr( $this->modify_label_tag ); ?> scope="row">
					<label for="<?php echo esc_attr( "{$this->item}_{$this->group_type}" ); ?>_groups[]">
						<?php

						echo esc_html(

							/**
							 * Filter the label.
							 *
							 * @since 2.13.0
							 *
							 * @param string $group_single The normal label of the connector.
							 * @param string $group_Type   The connector group type.
							 * @param string $item         The item of the connector.
							 */
							apply_filters(
								'affwp_group_connector_label',
								ucfirst( $this->group_single ),
								$this->group_type,
								$this->item
							)
						);

						?>
					</label>
				</<?php echo esc_attr( $this->modify_label_tag ); ?>>

				<?php if ( ! empty( $this->modify_content_tag ) ) : ?>
					<<?php echo esc_attr( $this->modify_content_tag ); ?>>
				<?php endif; ?>

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

						.select2 .select2-selection__choice,
						.select2 .select2-search.select2-search--inline {
							margin-bottom: 2px;
						}

						.select2 .select2-search.select2-search--inline input {
							min-height: 20px;
							height: 20px;
						}

						.select2-search--dropdown .select2-search__field {
							padding: 0 4px !important;
						}
					</style>

					<select
						name="<?php echo esc_attr( "{$this->item}_{$this->group_type}" ); ?>_groups[]"
						id="<?php echo esc_attr( "{$this->item}_{$this->group_type}" ); ?>_groups"
						style="min-width: 350px;"
						class="select2"
						data-label="<?php echo esc_attr( "{$this->item}_{$this->group_type}" ); ?>_groups[]"
						data-args='{ "disabled": <?php echo esc_attr( $disabled ? 'true' : 'false' ); ?> }'
						<?php echo esc_attr( 'multiple' === $this->selector_type ? 'multiple' : '' ); ?>>

						<?php if ( 'single' === $this->selector_type ) : ?>
							<option><?php echo esc_html_e( 'None', 'affiliate-wp' ); ?></option>
						<?php endif; ?>

						<?php

						/**
						 * Before we list out the group options.
						 *
						 * @since 2.13.0
						 *
						 * @param string $group_type Group type.
						 * @param string $item       Item.
						 * @param array  $groups     The groups.
						 */
						do_action(
							'affwp_group_connector_before_group_options',
							$this->group_type,
							$this->item,
							$groups
						);

						?>

						<?php

						foreach ( $groups as $group ) :

							?>

							<?php // phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentBeforeOpen -- We need to suppress extra whitespace. ?>
							<option
								value="<?php echo absint( $group->get_id() ); ?>"
								<?php echo esc_attr( $this->is_group_selected( $group->get_id(), $item ) ? 'selected=selected' : '' ); ?>><?php // phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentBeforeOpen, Squiz.PHP.EmbeddedPhp.ContentAfterOpen -- We want to eliminate whitespace.

									echo esc_html(

										/**
										 * Filter the group name in the option.
										 *
										 * @since 2.13.0
										 *
										 * @param string $group_title Group title to filter.
										 * @param string $group_Type   The connector group type.
										 * @param string $item         The item of the connector.
										 */
										apply_filters(
											'affwp_group_connector_group_name',
											wp_trim_words( $group->get_title(), 10 ),
											$group,
											$this->group_type,
											$this->item
										)
									);
								?><?php // phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentAfterOpen, Squiz.PHP.EmbeddedPhp.ContentAfterEnd, Squiz.PHP.EmbeddedPhp.ContentBeforeOpen -- We want to eliminate whitespace.

								/**
								 * Fire after showing the option for the group.
								 *
								 * @since 2.13.0
								 *
								 * @param \AffiliateWP\Groups\Group $group      Group object.
								 * @param string                    $group_Type Group type.
								 * @param string                    $item       The item.
								 */
								do_action(
									'affwp_group_connector_after_group_option',
									$group,
									$this->group_type,
									$this->item
								);

							// phpcs:ignore Generic.WhiteSpace.ScopeIndent.IncorrectExact, Squiz.PHP.EmbeddedPhp.ContentAfterEnd -- We want to supress whtiespace.
							?></option>

						<?php endforeach; ?>

						<?php

						/**
						 * After we list out the group options.
						 *
						 * @since 2.13.0
						 *
						 * @param string $group_type Group type.
						 * @param string $item       Item.
						 * @param array  $groups     The groups we're working with.
						 */
						do_action(
							'affwp_group_connector_after_group_options',
							$this->group_type,
							$this->item,
							$groups
						);

						?>

						<?php

						/**
						 * Fire after the group options.
						 *
						 * @since 2.13.0
						 *
						 * @param string $group_Type   The connector group type.
						 * @param string $item         The item of the connector.
						 * @param int    $item_id      The ID of the item.
						 */
						do_action(
							'affwp_group_connector_after_groups_options',
							$this->group_type,
							$this->item,
							isset( $item->$property ) ? $item->$property : 0
						);

						?>
					</select>

					<p class="description">
						<?php if ( empty( $groups ) && ! empty( $management_link ) ) : ?>
							<?php

							echo wp_kses(

								/**
								 * Filter the connector dropdown description.
								 *
								 * @since 2.13.0
								 *
								 * @param string $description The description.
								 * @param string $context     Context.
								 * @param string $group_Type   The connector group type.
								 * @param string $item         The item of the connector.
								 */
								apply_filters(
									'affwp_group_connector_description',

									// Default description.
									sprintf(
										/* Translators: %1$s is the grouping singular, %2$s is the item singlular. */
										__( '%1$sCreate%2$s %3$s %4$s to assign to this %5$s.', 'affiliate-wp' ),
										sprintf(
											'<a href="%s">',
											$management_link
										),
										'</a>',
										'multiple' === $this->selector_type
											? __( 'one or more', 'affiliate-wp' )
											: __( 'a', 'affiliate-wp' ),
										'multiple' === $this->selector_type
											? strtolower( $this->group_plural )
											: strtolower( $this->group_single ),
										strtolower( $this->item_single )
									),
									'no_groups',
									$this->group_type,
									$this->item
								),

								// Allowed HTML.
								array(
									'a' => array(
										'href'   => true,
										'title'  => true,
										'target' => true,
									),
								)
							);

							?>
						<?php else : ?>
							<?php

							// Description.
							echo esc_html(

								/** See previous description (above) in this file. */
								apply_filters(
									'affwp_group_connector_description',
									sprintf(
									/* Translators: %1$s is the language for plural or single selection,  %2$s is the grouping singular, %3$s is the item singlular. E.g.: 'Select a group for this affiliate.', or 'Select one or more groups for this affiliate.' */
										__( 'Select %1$s %2$s for this %3$s.', 'affiliate-wp' ),
										'multiple' === $this->selector_type
											? __( 'one or more', 'affiliate-wp' )
											: __( 'a', 'affiliate-wp' ),
										'multiple' === $this->selector_type
											? strtolower( $this->group_plural )
											: strtolower( $this->group_single ),
										strtolower( $this->item_single )
									),
									'has_groups',
									$this->group_type,
									$this->item
								)
							);

							?>
						<?php endif; ?>
					</p>

				<?php if ( ! empty( $this->modify_content_tag ) ) : ?>
					</<?php echo esc_attr( $this->modify_content_tag ); ?>>
				<?php endif; ?>

			<?php if ( ! empty( $this->modify_row_tag ) ) : ?>
				</<?php echo esc_attr( $this->modify_row_tag ); ?>>
			<?php endif; ?>

			<?php

			wp_nonce_field(
				$this->nonce_action( 'update', 'item' ),
				$this->nonce_action( 'update', 'item' )
			);

			?>
		<?php if ( ! empty( $this->modify_form_tag ) ) : ?>
			</<?php echo esc_attr( $this->modify_form_tag ); ?>>
		<?php endif; ?>

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
			=== filter_input( INPUT_GET, 'page', FILTER_UNSAFE_RAW );
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

		/**
		 * Filter whether we show the dropdown or not.
		 *
		 * @since 2.13.0
		 *
		 * @param bool  $hide        Set to true if no groups are present by default.
		 * @param string $group_type The group type of the connector.
		 * @param string $item       The item of the connector.
		 */
		$hide_dropdown = apply_filters(
			'affwp_connector_filter_dropdown_hidden',
			empty( $groups ),
			$this->group_type,
			$this->item
		);

		if ( $hide_dropdown ) {
			return; // No groups to select.
		}

		?>

		<select name="<?php echo esc_attr( $this->get_filter_dropdown_key() ); ?>">

			<option value="">
				<?php

				// Translators: %s is the translated name of the group, e.g. Categories.
				echo esc_html( sprintf( __( 'All %s', 'affiliate-wp' ), $this->group_plural ) );

				?>
			</option>

			<option
				<?php echo esc_attr( -1 === intval( filter_input( INPUT_GET, $this->get_filter_dropdown_key(), FILTER_SANITIZE_NUMBER_INT ) ) ? 'selected' : '' ); ?>
				value="-1">

				<?php

				// Translators: %s is the translated name of the group, e.g. Categories.
				echo esc_html( sprintf( __( 'No %s', 'affiliate-wp' ), $this->group_plural ) );

				?>
			</option>

			<?php

			/**
			 * Filters right before showing group options.
			 *
			 * @since 2.13.0
			 *
			 * @param string $group_type The group type of the connector.
			 * @param string $item       The item of the connector.
			 * @param string $wihich     The context (top/bottom) of the filter dropdown.
			 * @param array  $groups     Array of group objects shown in the dropdown.
			 */
			do_action(
				'affwp_connector_filter_dropdown_before_group_options',
				$this->group_type,
				$this->item,
				$which,
				$groups
			);

			?>

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

			<?php

			/**
			 * Fires after we output the dropdown options for the filter select.
			 *
			 * @since 2.13.0
			 *
			 * @param string $group_type The group type of the connector
			 * @param string $item       The item of the connector.
			 * @param string $wihich     The context (top/bottom) of the filter dropdown.
			 * @param array  $groups     Array of group objects shown in the dropdown.
			 */
			do_action(
				'affwp_connector_filter_dropdown_after_group_options',
				$this->group_type,
				$this->item,
				$which,
				$groups
			);

			?>
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
	 *
	 * @throws \Exception If we can't get proper connected items.
	 */
	private function is_group_selected( $group_id, $item ) {

		if ( ! $this->is_numeric_and_gt_zero( $group_id ) ) {
			return false;
		}

		if ( ! is_object( $item ) ) {

			/**
			 * Filter the selected group when there is no item (usually a new item).
			 *
			 * @since 2.13.0
			 *
			 * @param bool   $selected Set to to true to set it as selected.
			 * @param int    $group_id   The ID of the group in the database.
			 * @param string $group_type The group type of the connector.
			 * @param string $item       The item of the connector.
			 */
			return apply_filters(
				'affwp_group_connector_group_is_selected_new_item',
				false,
				$group_id,
				$this->group_type,
				$this->item
			);
		}

		// Get the creatives that is connected to the group.
		$connected_items = affiliate_wp()->connections->get_connected(
			$this->item,
			'group',
			$group_id
		);

		if ( ! is_array( $connected_items ) ) {
			throw new \Exception( '$connected_items should be an array.' );
		}

		$object_id_property = $this->object_id_property;

		if ( ! isset( $item->$object_id_property ) ) {

			return false; // Fail gracefully, not sure why this would ever happen.
		}

		if ( ! is_array( $connected_items ) ) {

			return false; // Fail gracefully, but did not get $connected_items.
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

		if (
			is_multisite() &&
			! $this->table_exists( $this->get_item_api()->table_name ) &&
			is_callable( array( $this->get_item_api(), 'create_table' ) )
		) {

			// Try and create the table for this item if we can.
			$this->get_item_api()->create_table();
		}

		// Creatives.
		$items = affiliate_wp()->connections->register_connectable(
			array(
				'name'   => $this->item,
				'table'  => $this->get_item_api()->table_name,
				'column' => $this->get_item_api()->primary_key,
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

		$this->enqueue_select2();
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

		if ( ! $this->string_is_one_of( $this->selector_type, array( 'multiple', 'single' ) ) ) {
			throw new \InvalidArgumentException( "\$this->selector_type must be either 'multiple' or 'single'." );
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

	/**
	 * Is the item/group type the same?
	 *
	 * @since 2.13.0
	 *
	 * @param string $group_type The group type.
	 * @param string $item       The item of the filtered connector.
	 *
	 * @return bool
	 */
	protected function is_same_connector( string $group_type, string $item ) : bool {
		return $this->group_type === $group_type && $this->item === $item;
	}

	/**
	 * Does the user have the required capability.
	 *
	 * @since 2.13.0
	 *
	 * @return bool
	 */
	protected function user_has_capability() : bool {

		return current_user_can( $this->capability )

			// Admins always can.
			|| current_user_can( 'administrator' )

			// Super-admins always can.
			|| is_super_admin();
	}
}
