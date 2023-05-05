<?php
/**
 * Connecting Creatives to Affiliate Groups.
 *
 * @package     AffiliateWP
 * @subpackage  AffiliateWP\Admin\Creatives\Affiliates
 * @copyright   Copyright (c) 2014, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.13.0
 *
 * @author      Aubrey Portwood <aportwood@awesomemotive.com>
 */

// phpcs:disable Generic.Commenting.DocComment.MissingShort -- No need to re-document some methods and properties.
// phpcs:disable PEAR.Functions.FunctionCallSignature.FirstArgumentPosition -- Formatting OK in this file.
// phpcs:disable PEAR.Functions.FunctionCallSignature.EmptyLine -- Formatting OK in this file.
// phpcs:ignore Squiz.Commenting.InlineComment.NoSpaceBefore -- Formatting OK.
// phpcs:disable PEAR.Functions.FunctionCallSignature.ContentAfterOpenBracket, PEAR.Functions.FunctionCallSignature.CloseBracketLine -- Allow surrounding code w/out line breaks.

namespace AffiliateWP\Admin\Affiliates\Groups\Creatives;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( dirname( __DIR__ ) ) . '/groups/class-connector.php';

/**
 * Connecting Creatives to Affiliate Groups.
 *
 * @since 2.13.0
 */
final class Connector extends \AffiliateWP\Admin\Groups\Connector {

	use \AffiliateWP\Utils\Data;

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $capability = 'manage_creatives';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $groups_column_before = 'status';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $group_plural = '';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $group_single = '';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $group_type = 'affiliate-group';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $item = 'creative';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $item_plural = '';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $item_single = '';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $object_id_property = 'creative_id';

	/**
	 * Construct
	 *
	 * @since 2.13.0
	 */
	public function __construct() {

		// Translate properties.
		$this->item_plural  = __( 'Creatives', 'affiliate-wp' );
		$this->item_single  = __( 'Creative', 'affiliate-wp' );
		$this->group_plural = __( 'Groups', 'affiliate-wp' );
		$this->group_single = __( 'Affiliate Group', 'affiliate-wp' );

		// Setup the template for this being in the 2nd place on the new/edit forms.
		$this->modify_form_tag   = 'tr';
		$this->modify_form_class = 'form-row';
		$this->modify_row_tag    = 'div';
		$this->modify_row_class  = '';

		// Hooks & Modifications.
		$this->modify_multiselect_position_to_before_status_hooks();
		$this->customize_label_hooks();
		$this->language_hooks();

		// Assigning creatives to affiliates when there are low affiliates.
		$this->connect_creatives_to_affiliates_hooks();
		$this->show_affiliates_in_group_tooltip_hooks();

		parent::__construct();
	}

	/**
	 * Hooks for customizing language.
	 *
	 * @since 2.13.1
	 */
	private function language_hooks() : void {

		add_filter( 'affwp_group_connector_description', array( $this, 'description' ), 10, 4 );
		add_filter( 'affwp_connector_column', array( $this, 'customize_column' ), 10, 3 );
		add_filter( 'affwp_connector_column_contents_none', array( $this, 'set_none_to_public' ), 9, 3 );
		add_filter( 'affwp_connector_group_selector_placeholder', array( $this, 'set_group_selector_placeholder' ), 10, 3 );
		add_filter( 'affwp_connector_all_groups_text', array( $this, 'set_all_groups_text' ), 10, 3 );
		add_filter( 'affwp_connector_no_groups_text', array( $this, 'set_no_groups_text' ), 10, 3 );
		add_filter( 'affwp_connector_filter_dropdown_selected_group_select2_json', array( $this, 'add_affiliate_group_designation_to_selected_filter_dropdown_group' ), 10, 3 );
		add_filter( 'affwp_connector_filter_dropdown_selected_unknown_item_select2_json', array( $this, 'add_selected_affiliate_in_filter_dropdown' ), 10, 4 );
	}

	/**
	 * Add (Affiliate) designation to selected group in the filter drop-down.
	 *
	 * @since 2.13.2
	 *
	 * @param array  $json                   The JSON for Select2.
	 * @param string $group_type             The group type of the connector.
	 * @param string $item                   The item of the connector.
	 * @param mixed  $selected_filter_option The option we got from GET.
	 *
	 * @return array
	 */
	public function add_selected_affiliate_in_filter_dropdown(
		array $json,
		string $group_type,
		string $item,
		$selected_filter_option
	) : array {

		if ( 'creative' !== $item ) {
			return $json;
		}

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $json;
		}

		if ( ! strstr( $selected_filter_option, 'affiliate-' ) ) {
			return $json; // We only care about affiliates being selected.
		}

		$affiliate_id = str_replace( 'affiliate-', '', $selected_filter_option );

		if ( ! is_numeric( $affiliate_id ) ) {
			return $json;
		}

		if ( ! affwp_get_affiliate( $affiliate_id ) ) {
			return $json; // Not an affiliate.
		}

		$json = array(

			// The selected affiliate.
			$this->esc_select_2_json_item( array(
				'selected' => true,
				'id'       => $selected_filter_option,
				'text'     => sprintf(
					'%1$s %2$s',
					$this->get_affiliate_option_text_format( $affiliate_id ),
					$this->get_affiliate_option_designation_text()
				),
			) ),
		);

		return $json;
	}

	/**
	 * Format for displaying an affiliate option.
	 *
	 * Displays name (dash) email.
	 *
	 * @since 2.13.2
	 *
	 * @param int $affiliate_id The affiliate id.
	 *
	 * @return string
	 *
	 * @throws \InvalidArgumentException If not an affiliate id.
	 */
	private function get_affiliate_option_text_format( int $affiliate_id ) {

		if ( ! affwp_get_affiliate( $affiliate_id ) ) {
			throw new \InvalidArgumentException( '$affiliate_id is not a valid affiliate id.' );
		}

		return sprintf(

			// Format the affiliate name - email.
			'%1$s&nbsp;&mdash;&nbsp;%2$s',
			affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id ),
			affwp_get_affiliate_email( $affiliate_id )
		);
	}

	/**
	 * Add (Affiliate Group) designation to selected affiliate group in the filter drop-down.
	 *
	 * @since 2.13.2
	 *
	 * @param array  $json       The JSON for Select2.
	 * @param string $group_type The group type of the connector.
	 * @param string $item       The item of the connector.
	 *
	 * @return array
	 */
	public function add_affiliate_group_designation_to_selected_filter_dropdown_group(
		array $json,
		string $group_type,
		string $item
	) : array {

		if ( 'creative' !== $item ) {
			return $json;
		}

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $json;
		}

		// Re-format the group text to include (Affiliate Group).
		$json[0]['text'] = esc_attr( esc_html( sprintf(
			'%1$s %2$s',
			$json[0]['text'],
			$this->get_affiliate_group_option_designation_text()
		) ) );

		return $json;
	}

	/**
	 * Set no groups option text.
	 *
	 * @since 2.13.2
	 *
	 * @param string $text       The normal text.
	 * @param string $group_type The group type of the connector.
	 * @param string $item       The item of the connector.
	 *
	 * @return string
	 */
	public function set_no_groups_text( string $text, string $group_type, string $item ) : string {

		if ( 'creative' !== $item ) {
			return $text;
		}

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $text;
		}

		// Creatives with no affiliates or affiliate groups are public.
		return __( 'Public', 'affiliate-wp' );
	}

	/**
	 * Set all groups option text.
	 *
	 * @since 2.13.2
	 *
	 * @param string $text       The normal text.
	 * @param string $group_type The group type of the connector.
	 * @param string $item       The item of the connector.
	 *
	 * @return string
	 */
	public function set_all_groups_text( string $text, string $group_type, string $item ) : string {

		if ( 'creative' !== $item ) {
			return $text;
		}

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $text;
		}

		return __( 'Private & Public', 'affiliate-wp' );
	}

	/**
	 * Set the placeholder text.
	 *
	 * @since 2.13.2
	 *
	 * @param string $placeholder The placeholder text.
	 * @param string $group_type  The group type of the selector.
	 * @param string $item        The item of the selector.
	 *
	 * @return string
	 */
	public function set_group_selector_placeholder( string $placeholder, string $group_type, string $item ) : string {

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $placeholder;
		}

		if ( 'creative' !== $item ) {
			return $placeholder;
		}

		return __( 'Type to search affiliate groups and affiliates.', 'affiliate-wp' );
	}

	/**
	 * Get creative categories associated with the an item (creative).
	 *
	 * @since 2.13.0
	 *
	 * @param int $item_id The Item (creative) ID.
	 *
	 * @return array
	 */
	private function get_creative_categories_for_item( int $item_id ) : array {

		return array_filter(

			// Get all groups (regardless of type) connected to the creative.
			affiliate_wp()->connections->get_connected(
				'group',
				'creative',
				$item_id
			),

			// Only use groups that are of creative-category type.
			function ( $group_id ) {

				$group = affiliate_wp()->groups->get_group( $group_id );

				return 'creative-category' === $group->get_type();
			}
		);
	}

	/**
	 * Hooks to show affiliates in groups.
	 *
	 * @since 2.13.0
	 *
	 * @return void
	 */
	private function show_affiliates_in_group_tooltip_hooks() : void {

		// Make sure our text tooltips will work.
		add_filter( 'affwp_group_connector_after_column_group_title_kses', array( $this, 'add_tooltip_allowed_html' ) );

		// Format groups.
		add_filter( 'affwp_group_connector_after_column_group_title', array( $this, 'format_groups' ), 10, 3 );
	}

	/**
	 * Allow tooltip HTML.
	 *
	 * @since 2.13.0
	 *
	 * @param array $allowed_html Default allowed HTML of the connector.
	 *
	 * @return array
	 */
	public function add_tooltip_allowed_html( array $allowed_html ) : array {

		return array_merge(
			$allowed_html,
			array(
				'span' => array(
					'aria-describedby'    => true,
					'class'               => true,
					'id'                  => true,
					'style'               => true,
					'x-data'              => true,
					'x-on:mouseover'      => true,
					'x-on:mouseover.away' => true,
					'x-show'              => true,
					'x-transition'        => true,
				),
			)
		);
	}

	/**
	 * All the hooks necessary to allow the connector to connect to affiliate groups AND affiliates.
	 *
	 * @since 2.13.0
	 *
	 * @return void
	 */
	private function connect_creatives_to_affiliates_hooks() : void {

		if ( ! $this->user_has_capability() ) {
			return; // None of the hooks below are required.
		}

		// Connect affiliates when updating/adding creative.
		add_filter( 'affwp_group_connector_item_groups', array( $this, 'connect_selected_affiliates' ), 10, 4 );

		// Show selected individual affiliates in the list table view.
		add_filter( 'affwp_connector_column_contents', array( $this, 'add_affiliates_to_affiliates_column' ), 10, 4 );

		// If an affiliate is selected from the filter drop-down, filter those items out (that are not associated with the affiliate).
		add_filter( 'affwp_connector_filter_table_get_items_args', array( $this, 'filter_creatives_associated_with_selected_affiliate' ), 10, 3 );

		// Keep group selector drop-down working when there are affiliates.
		add_filter( 'affwp_connector_group_selector_disabled', array( $this, 'only_disable_selector_when_there_are_also_no_affiliates' ), 10, 3 );
		add_filter( 'affwp_connector_filter_dropdown_hidden', array( $this, 'dont_hide_dropdown_when_affiliates_are_selectable' ), 10, 3 );

		// Make sure the selected affiliates show in the group selector.
		add_filter( 'affwp_connector_get_selected_groups_select2_json', array( $this, 'set_connected_affiliates_in_group_selector_that_are_connected_to_creative' ), 10, 4 );

		// Add affiliates (in the mix) to the selectable affiliate groups (over AJAX using Select2).
		add_filter( 'get_groups_in_select2_json_format', array( $this, 'add_affiliates_to_affiliate_groups_selector_in_select2_json_format' ), 10, 4 );

		if ( $this->assigning_affiliates_to_creatives_is_disabled() ) {
			return;
		}

		// Add affiliates for bulk assignment.
		add_filter( 'affwp_connector_bulk_items', array( $this, 'add_affiliate_bulk_items' ), 10, 3 );

		// Save bulk assignment.
		foreach ( $this->get_affiliates_bulk_items() as $action => $name ) {
			add_action( $this->filter_hook_name( "affwp_{$this->item}s_do_bulk_action_{$action}" ), array( $this, 'connect_bulk_selected_affiliate_to_item' ) );
		}
	}

	/**
	 * Connect a bulk selected item (affiliate) to the applied item.
	 *
	 * @since 2.13.0
	 *
	 * @param int $item_id The ID of the item.
	 */
	public function connect_bulk_selected_affiliate_to_item( int $item_id ) : void {

		$affiliate_id = $this->get_affiliate_id_from_action();

		if ( ! $this->is_numeric_and_gt_zero( $affiliate_id ) ) {
			return; // No affiliate chosen/sent (this shouldn't happen).
		}

		if ( ! $this->user_has_capability() ) {
			return;
		}

		$this->connect_affiliate_with_item( $affiliate_id, $item_id );
	}

	/**
	 * Connect an affiliate with an item (by id).
	 *
	 * @since 2.13.0
	 *
	 * @param int $affiliate_id The affiliate id.
	 * @param int $item_id      The item id.
	 *
	 * @return void
	 */
	private function connect_affiliate_with_item( int $affiliate_id, int $item_id ) : void {

		if ( ! $this->user_has_capability() ) {
			return;
		}

		affiliate_wp()->connections->connect(
			array(
				'affiliate' => intval( $affiliate_id ),
				$this->item => intval( $item_id ),
			)
		);
	}

	/**
	 * Append assign to affiliate bulk items.
	 *
	 * @since 2.13.0
	 * @since 2.13.2 Note that this might be disabled, see filter for more.
	 *
	 * @param array  $actions      Default bulk items.
	 * @param string $group_type The group type of the connector.
	 * @param string $item       The item of the connector.
	 *
	 * @return array
	 */
	public function add_affiliate_bulk_items( array $actions, string $group_type, string $item ) : array {

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $actions; // Not our connector.
		}

		if ( $this->assigning_affiliates_to_creatives_is_disabled() ) {
			return $actions;
		}

		return array_merge(
			$actions,
			$this->get_affiliates_bulk_items()
		);
	}

	/**
	 * Are there too many affiliates to list?
	 *
	 * @since 2.13.1
	 *
	 * @return bool
	 */
	private function assigning_affiliates_to_creatives_is_disabled() : bool {

		/**
		 * Filter whether to disable the ability to assign creatives to affiliates.
		 *
		 * On Apr 12, 2023 we decided to disable the ability to assign creatives to affiliates.
		 *
		 * @since 2.13.1
		 *
		 * @param $disabled Set to `true` to disable the feature (default), `false` to allow assigning creatives to individual affiliates.
		 */
		return apply_filters( 'affwp_assigning_affiliates_to_creatives_is_disabled', true );
	}

	/**
	 * Get the bulk items for assigning to affiliates.
	 *
	 * @since 2.13.0
	 *
	 * @return array
	 */
	private function get_affiliates_bulk_items() : array {

		static $cache = null;

		if ( ! is_null( $cache ) ) {
			return $cache;
		}

		$affiliate_assign_to_bulk_items = array();

		foreach ( $this->get_all_affiliate_ids_sorted_alpha() as $name => $affiliate_id ) {

			// Add assigning bulk item.
			$affiliate_assign_to_bulk_items[ "assign-affiliate:{$affiliate_id}" ] = sprintf(

				// Translators: %s is the name of the affiliate.
				__( 'Assign Affiliate: %s', 'affiliate-wp' ),
				sprintf(
					'%1$s&nbsp;&mdash;&nbsp;%2$s',
					esc_html( $name ),
					affwp_get_affiliate_email( $affiliate_id )
				)
			);
		}

		// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found -- Used to cache.
		return $cache = $affiliate_assign_to_bulk_items;
	}

	/**
	 * The designation for an affiliate option.
	 *
	 * @since 2.13.2
	 *
	 * @return string
	 */
	private function get_affiliate_option_designation_text() : string {
		return __( '(Affiliate)', 'affiliate-wp' );
	}

	/**
	 * The designation for an affiliate group option.
	 *
	 * @since 2.13.2
	 *
	 * @return string
	 */
	private function get_affiliate_group_option_designation_text() : string {
		return __( '(Affiliate Group)', 'affiliate-wp' );
	}

	/**
	 * Add all affiliates to the selectable list of affiliate groups.
	 *
	 * Adds affiliates in the mix.
	 *
	 * @since 2.13.2
	 *
	 * @param array  $results    The affiliate groups.
	 * @param string $search     The search term if any.
	 * @param string $group_type The group type of the connector.
	 * @param string $item       The item of the connector.
	 *
	 * @return array
	 */
	public function add_affiliates_to_affiliate_groups_selector_in_select2_json_format(
		array $results,
		string $search,
		string $group_type,
		string $item
	) : array {

		if ( 'creative' !== $item ) {
			return $results;
		}

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $results;
		}

		$sorted = array();

		// We need to re-sort groups.
		foreach ( $results as $affiliate_group ) {

			$sorted[ strtolower( $affiliate_group['text'] ) ] = $this->esc_select_2_json_item( array(
				'id'   => $affiliate_group['id'],
				'text' => sprintf(
					'%1$s %2$s',
					$affiliate_group['text'],
					$this->get_affiliate_group_option_designation_text()
				),
			) );
		}

		// We need to sort injected affiliates.
		foreach ( $this->get_all_affiliate_ids() as $affiliate_id ) {

			$text = html_entity_decode(
				$this->get_affiliate_option_text_format( $affiliate_id )
			);

			if ( ! empty( $search ) && (
				! $this->stristr_search_select_2_text( $text, $search )
			) ) {
				continue; // There was a search, don't include this affiliate in the mix.
			}

			$sort = strtolower( affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id ) );

			$sorted[ $sort ] = $this->esc_select_2_json_item( array(
				'id'   => "affiliate-{$affiliate_id}",
				'text' => sprintf(
					'%1$s %2$s',
					$text,
					$this->get_affiliate_option_designation_text()
				),
			) );
		}

		ksort( $sorted );

		unset( $results ); // Clean up this large thing now.

		return array_values( $sorted );
	}

	/**
	 * Set the affiliates that are selected for the creative.
	 *
	 * @since 2.13.2
	 *
	 * @param array  $selected    The selected affiliate groups.
	 * @param string $group_type  The group type of the connector.
	 * @param string $item        The item of the connector.
	 * @param mixed  $item_object The creative object.
	 *
	 * @return array
	 */
	public function set_connected_affiliates_in_group_selector_that_are_connected_to_creative(
		array $selected,
		string $group_type,
		string $item,
		$item_object
	) : array {

		if ( 'creative' !== $item ) {
			return $selected;
		}

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $selected;
		}

		if ( ! isset( $item_object->creative_id ) ) {
			return $selected;
		}

		return array_merge(
			array_map(
				function( $affiliate_group ) {

					return $this->esc_select_2_json_item( array(
						'selected' => true,
						'id'       => $affiliate_group['id'],
						'text'     => sprintf(
							'%1$s %2$s',
							$affiliate_group['text'],
							$this->get_affiliate_group_option_designation_text()
						),
					) );
				},
				$selected
			),
			array_map(
				function( $affiliate_id ) {
					return $this->esc_select_2_json_item( array(
						'selected' => true,
						'id'       => "affiliate-{$affiliate_id}",
						'text'     => sprintf(
							'%1$s %2$s',
							$this->get_affiliate_option_text_format( $affiliate_id ),
							$this->get_affiliate_option_designation_text()
						),
					) );
				},
				$this->get_affiliate_ids_connected_to_creative( $item_object->creative_id )
			)
		);
	}

	/**
	 * Set None to Public for Creatives.
	 *
	 * @since 2.13.0
	 *
	 * @param string $none       The value for None when there are none.
	 * @param string $group_type The group type of the connector.
	 * @param string $item       The item of the connector.
	 *
	 * @return string
	 */
	public function set_none_to_public( string $none, string $group_type, string $item ) : string {

		if ( 'creative' !== $item ) {
			return $none;
		}

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $none;
		}

		return $this->none_value();
	}

	/**
	 * The value for 'None'.
	 *
	 * @since 2.13.0
	 *
	 * @return string
	 */
	private function none_value() : string {
		return __( 'Public', 'affiliate-wp' );
	}

	/**
	 * Customize the column header.
	 *
	 * @since 2.13.0
	 *
	 * @param string $column_title The title we use normally.
	 * @param string $group_type   The group type of the connector.
	 * @param string $item         The item of the connector.
	 *
	 * @return string
	 */
	public function customize_column( string $column_title, string $group_type, string $item ) : string {

		if ( 'creative' !== $item ) {
			return $column_title;
		}

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $column_title;
		}

		// See https://github.com/awesomemotive/AffiliateWP/issues/4653 .
		return __( 'Privacy', 'affiliate-wp' );
	}

	/**
	 * Don't hide the connector dropdown if there are affiliates to select.
	 *
	 * @since 2.13.0
	 *
	 * @param bool   $hidden     Whether it will be hidden (usually no groups).
	 * @param string $group_type The group type of the connector.
	 * @param string $item       The item of the connector.
	 *
	 * @return bool
	 */
	public function dont_hide_dropdown_when_affiliates_are_selectable( bool $hidden, string $group_type, string $item ) : bool {

		if ( 'creative' !== $item ) {
			return $hidden;
		}

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $hidden;
		}

		// It was already going to be hidden (no affiliate groups).
		return $hidden &&

			// There are no affiliates to select either.
			$this->there_are_no_affiliates();
	}

	/**
	 * Disable the affiliate group selector only when there are no groups AND no affiliates.
	 *
	 * @since 2.13.0
	 *
	 * @param bool   $disabled   The default, `false` if there are no affiliate groups.
	 * @param string $group_type The group type of the selector.
	 * @param string $item       The item of the selector.
	 *
	 * @return [type] [description]
	 */
	public function only_disable_selector_when_there_are_also_no_affiliates(
		bool $disabled,
		string $group_type,
		string $item
	) {

		// Only consider this when editing a creative.
		if ( 'creative' !== $item ) {
			return $disabled;
		}

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $disabled;
		}

		// If it was already disabled.
		return $disabled &&

			// And there are no affiliates to select.
			$this->there_are_no_affiliates();
	}

	/**
	 * Get the affiliate id from the bulk action.
	 *
	 * @since 2.13.0
	 *
	 * @return int Negative number if not there, positive numeric if it is.
	 */
	private function get_affiliate_id_from_action() : int {

		$action = filter_input( INPUT_GET, 'action', FILTER_UNSAFE_RAW );

		if ( ! strstr( $action, 'assign-affiliate:' ) ) {
			return -1; // Not an assign affiliate ID (not sure why this would ever happen w/ the hook we use).
		}

		$affiliate_id = str_replace( 'assign-affiliate:', '', $action );

		if ( ! $this->is_numeric_and_gt_zero( $affiliate_id ) ) {
			return -2; // The selected ID isn't valid (shouldn't happen).
		}

		return intval( $affiliate_id );
	}

	/**
	 * Get all the affiliates (by id) sorted alphabetically.
	 *
	 * @since 2.13.0
	 *
	 * @return array
	 */
	private function get_all_affiliate_ids_sorted_alpha() : array {

		static $cache = null;

		if ( ! is_null( $cache ) ) {
			return $cache;
		}

		$affiliates_sorted = array();

		foreach ( $this->get_all_affiliate_ids() as $affiliate_id ) {
			$affiliates_sorted[ affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id ) ] = intval( $affiliate_id );
		}

		ksort( $affiliates_sorted );

		// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found -- Used to cache.
		return $cache = $affiliates_sorted;
	}

	/**
	 * Get all affiliate ids.
	 *
	 * @since 2.13.0
	 *
	 * @return array
	 */
	private function get_all_affiliate_ids() : array {

		static $cache = null;

		if ( is_array( $cache ) ) {
			return $cache;
		}

		// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found -- Used for caching.
		return $cache = array_map(
			'intval',
			affiliate_wp()->affiliates->get_affiliates(
				array(
					'number' => apply_filters( 'affwp_unlimited', -1, 'affiliate_group_creative_connector_get_all_affiliate_ids_number' ),
					'fields' => 'ids',
				)
			)
		);
	}

	/**
	 * Get the count of affiliates.
	 *
	 * @since 2.13.2
	 *
	 * @return int
	 */
	private function get_affiliate_count() : int {

		return affiliate_wp()->affiliates->get_affiliates(
			array(
				'number' => apply_filters( 'affwp_unlimited', -1, 'affiliate_group_creative_connector_get_affiliate_count' ),
				'fields' => 'ids',
			),
			true
		);
	}

	/**
	 * Get the selected GET affiliate.
	 *
	 * @since 2.13.0
	 *
	 * @return string|int Something like affiliate-(int) or '-1'.
	 */
	private function get_selected_filtered_affiliate_option() {

		$value = filter_input( INPUT_GET, $this->get_filter_dropdown_key(), FILTER_UNSAFE_RAW );

		if (

			! is_string( $value ) &&
			! is_numeric( $value )
		) {

			// Value should be a string (affiliate-int) or none.
			return '';
		}

		if ( 'none' === trim( $value ) ) {
			return $value; // This means none was selected, just send that back now.
		}

		if ( ! strstr( $value, 'affiliate-' ) ) {
			return ''; // Should be either none or affiliate-(int).
		}

		return $value;
	}

	/**
	 * Filter creatives (list table arguments) shown for the selected affiliate.
	 *
	 * @since 2.13.0
	 *
	 * @param array  $args       Query arguments.
	 * @param string $group_type The group type of the connector.
	 * @param string $item       The item of the connector.
	 *
	 * @return array
	 */
	public function filter_creatives_associated_with_selected_affiliate( array $args, string $group_type, string $item ) : array {

		if ( 'creative' !== $item ) {
			return $args; // Only filter creatives list table.
		}

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $args; // Don't filter items, not our connector (not even creatives).
		}

		// No Affiliates selected in drop-down...
		if ( $this->no_affiliate_option_selected() ) {

			return array_merge(
				$args,
				array(

					// Make sure we only show creatives that have no assigned affiliates.
					'include' => $this->get_creative_ids_without_connected_affiliates(),
				)
			);
		}

		if ( ! strstr( $this->get_selected_filtered_affiliate_option(), 'affiliate-' ) ) {
			return $args; // We didn't select an affiliate from the drop-down, it should have affiliate-(int) value.
		}

		$selected_affiliate_id = trim( str_replace( 'affiliate-', '', $this->get_selected_filtered_affiliate_option() ) );

		if ( ! $this->is_numeric_and_gt_zero( $selected_affiliate_id ) ) {
			return $args; // Not a valid ID, don't filter items.
		}

		$creatives = array_merge(

			// Creatives that are directly connected.
			$this->get_creative_ids_connected_directly_to_affiliate( intval( $selected_affiliate_id ) ),

			// Creatives that share the same affiliate group the selected affiliate is in.
			$this->get_creative_ids_connected_to_affiliate_via_affiliate_groups( intval( $selected_affiliate_id ) )
		);

		return array_merge(
			$args,
			empty( $creatives )
				? array(

					// Force no items to show, because there are no creatives associated with the affiliate.
					'creative_id' => -1,
				)
				: array(

					// Make sure and, inclusively, show creatives connected to the selected affiliate id.
					'include' => $creatives,
				),
		);
	}

	/**
	 * Was the No Affiliate option selected from the drop-down?
	 *
	 * @since 2.13.0
	 *
	 * @return bool
	 */
	private function no_affiliate_option_selected() : bool {

		// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found -- Used for caching.
		return 'none' === $this->get_selected_filtered_affiliate_option();
	}

	/**
	 * Get creatives (ids) that share the same affiliate group as affiliate.
	 *
	 * @since 2.13.0
	 *
	 * @param int $affiliate_id The affiliate id.
	 *
	 * @return array
	 */
	private function get_creative_ids_connected_to_affiliate_via_affiliate_groups( int $affiliate_id ) : array {

		$creatives = array();

		foreach (

			// Go over the list of affiliate groups associated with the affiliate...
			affiliate_wp()->groups->filter_groups_by_type(
				affiliate_wp()->connections->get_connected(
					'group',
					'affiliate',
					intval( $affiliate_id )
				),
				'affiliate-group'
			) as $affiliate_group_id
		) {

			// Append creatives that are also associated with the same group.
			$creatives = array_merge(
				$creatives,

				// Get the creatives associated with the same group...
				affiliate_wp()->connections->get_connected(
					'creative',
					'group',
					intval( $affiliate_group_id )
				)
			);
		}

		return array_unique( array_map( 'intval', $creatives ) );
	}

	/**
	 * Get creative ids that have no associations with affiliates.
	 *
	 * @since 2.13.0
	 *
	 * @return array
	 */
	private function get_creative_ids_without_connected_affiliates() : array {

		return array_filter(
			$this->get_all_creative_ids(),
			function( $creative_id ) {

				// Keep affiliates that don't have connected affiliates.
				return empty( $this->get_affiliate_ids_connected_to_creative( $creative_id ) )
					? true // They have no groups, keep them.
					: false; // They have groups, omit them.
			}
		);
	}

	/**
	 * Get all the affiliates connected to creative.
	 *
	 * @since 2.13.0
	 *
	 * @param int $creative_id The creative id.
	 *
	 * @return array
	 */
	private function get_affiliate_ids_connected_to_creative( int $creative_id ) : array {

		return affiliate_wp()->connections->get_connected(
			'affiliate',
			'creative',
			intval( $creative_id )
		);
	}

	/**
	 * Get all creative ids.
	 *
	 * @since 2.13.0
	 *
	 * @return array
	 */
	private function get_all_creative_ids() : array {

		static $cache = null;

		if ( is_array( $cache ) ) {
			return $cache;
		}

		// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found -- Used for caching.
		return $cache = array_map(
			'intval',
			affiliate_wp()->creatives->get_creatives(
				array(
					'number' => apply_filters( 'affwp_unlimited', -1, 'affiliate_group_creative_connector_get_all_creative_ids_number' ),
					'fields' => 'ids',
				)
			)
		);
	}

	/**
	 * Get all the creative (ids) connected to affiliate (by id).
	 *
	 * @since 2.13.0
	 *
	 * @param int $affiliate_id The affiliate id.
	 *
	 * @return array
	 */
	private function get_creative_ids_connected_directly_to_affiliate( int $affiliate_id ) : array {

		return affiliate_wp()->connections->get_connected(
			'creative', // Where creative id is...
			'affiliate', // Affiliate.

			// This affiliate.
			intval( $affiliate_id )
		);
	}

	/**
	 * Modify the positioning hooks for the add/new templates to 2nd.
	 *
	 * Instead of last (default of the abstract class).
	 *
	 * @since  2.12.0
	 */
	private function modify_multiselect_position_to_before_status_hooks() {

		// Position of Connector <select>.
		add_filter( 'affwp_filter_hook_name_affwp_edit_creative_bottom', array( $this, 'change_multiselect_form_position_filter_to_before_status' ), 20, 1 );
		add_filter( 'affwp_filter_hook_name_affwp_new_creative_bottom', array( $this, 'change_multiselect_form_position_filter_to_before_status' ), 20, 1 );
	}

	/**
	 * Format groups.
	 *
	 * @since 2.13.0
	 *
	 * @param string $title      The title of the group (HTML).
	 * @param string $group_type The group type of the connector.
	 * @param string $item       The item of the connector.
	 *
	 * @return string
	 */
	public function format_groups( string $title, string $group_type, string $item ) : string {

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $title;
		}

		// Get all the affiliates in the group, we'll show it in the tooltip.
		$connected_affiliate_ids = affiliate_wp()->connections->get_connected(

			// Affiliates.
			'affiliate',

			// Connected to the group.
			'group',

			// With ID that matches the title of the group.
			affiliate_wp()->groups->get_group_id_by_title(
				wp_strip_all_tags( $title ),
				$group_type
			)
		);

		// Show a text-tooltip of the connected affiliates in this affiliate group.
		return affwp_text_tooltip(

			// Creative view should show a strong affiliate group titles.
			'creative' === $item ? "<strong>{$title}</strong>" : $title,
			empty( $connected_affiliate_ids )

				// No affiliates in this affiliate group.
				? __( 'No affiliates in this group.', 'affiliate-wp' )

				// Add affiliates to the column values.
				: implode(
					', ',
					array_map(
						function( $affiliate_id ) {
							return $this->get_affiliate_option_text_format( $affiliate_id );
						},
						$connected_affiliate_ids
					)
				),
			false
		);
	}

	/**
	 * Show affiliates in column content.
	 *
	 * @since 2.13.0
	 *
	 * @param string $contents   Current content.
	 * @param string $group_type The group type of the connector.
	 * @param string $item       The item of the connector.
	 * @param int    $item_id    the item's ID from the connector.
	 *
	 * @return string
	 */
	public function add_affiliates_to_affiliates_column( string $contents, string $group_type, string $item, int $item_id ) : string {

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $contents; // Not our group type.
		}

		// Get affiliates associated with this creative.
		$connected_affiliates = affiliate_wp()->connections->get_connected(
			'affiliate',
			'creative',
			$item_id
		);

		$none = $this->none_value();

		if ( empty( $connected_affiliates ) ) {
			return $contents; // None, don't append anything.
		}

		// Append affiliates to the end of the column contents.
		return sprintf(
			stristr( $contents, $none )

				// There were "None" original contents, remove comma.
				? '%1$s%2$s'

				// There were affiliate groups, add after comma.
				: '%1$s, %2$s',

			// The original contents.
			stristr( $contents, $none )

				// The original contents said none, but there are affiliates to append, remove "None".
				? '<!-- No Affiliate Groups -->'

				// There were contents (affiliate groups), so add those first.
				: $contents,

			// Append affiliates to the end.
			implode(

				// Comma-separate.
				', ',
				array_map(

					// Convert ID's to admin tooltip links.
					function( int $affiliate_id ) : string {

						return affwp_text_tooltip(

							// Tooltip text: link: affiliate name.
							sprintf(

								// Format.
								'<a href="%1$s">%2$s</a>',

								// Link to affiliate edit page.
								esc_url(
									wp_nonce_url(
										admin_url( "admin.php?page=affiliate-wp-{$this->item}s&filter-{$this->item}-{$this->group_type}-top=affiliate-{$affiliate_id}" ),
										$this->nonce_action( 'filter', 'items' ),
										$this->nonce_action( 'filter', 'items' )
									),
								),

								// Show the affiliates name (without email).
								esc_html( affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id ) )
							),

							// Tooltip content.
							esc_attr(
								$this->get_affiliate_option_text_format( $affiliate_id )
							),
							false // Return tooltip markup.
						);
					},

					// Make sure all the ID's are valid.
					array_filter(
						$connected_affiliates,
						function( $value ) {

							// Not sure why this would happen.
							return $this->is_numeric_and_at_least_zero( $value );
						}
					)
				)
			)
		);
	}

	/**
	 * Connect affiliates and return ID's of groups.
	 *
	 * @since 2.13.0
	 *
	 * @param array  $selected_options  The selected values of the selector `<select>`.
	 * @param string $group_type        The group type of the connector.
	 * @param string $item              The item of the connector.
	 * @param int    $item_id           The ID of the item.
	 *
	 * @return array Once we've connected the affiliates, we'll strip out the ID's
	 *               and send back the group ID's to connect from the `<select>`.
	 */
	public function connect_selected_affiliates( array $selected_options, string $group_type, string $item, int $item_id ) : array {

		if ( ! $this->user_has_capability() ) {
			return $selected_options;
		}

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $selected_options; // Not our group type.
		}

		$selected_affiliate_ids = array_map(
			function( $formatted_affiliate_option_value ) {

				// Convert the affiliate-(int) to integers.
				return str_replace( 'affiliate-', '', $formatted_affiliate_option_value );
			},
			array_filter(
				$selected_options,
				function ( $maybe_affiliate_id ) {

					// Anything prefixed with affiliate-, keep (and filter above).
					return strstr( $maybe_affiliate_id, 'affiliate-' );
				}
			)
		);

		foreach (

			// Get un-selected affiliate ids.
			array_diff(

				// From all the affiliates.
				$this->get_all_affiliate_ids(),

				// Remove the selected affiliates.
				$selected_affiliate_ids

			) as $unselected_affiliate_id
		) {

			// Disconnect any affiliates that were not selected (that might have previously been selected).
			affiliate_wp()->connections->disconnect(
				array(
					$item       => intval( $item_id ),
					'affiliate' => intval( $unselected_affiliate_id ),
				)
			);
		}

		// Connect affiliates to creative.
		foreach ( $selected_affiliate_ids as $affiliate_id ) {

			// Connect selected affiliate to the creative.
			affiliate_wp()->connections->connect(
				array(
					$item       => intval( $item_id ),
					'affiliate' => intval( $affiliate_id ),
				)
			);
		}

		// Filter out our affiliate-(int) numbers.
		return array_filter(

			// They might have selected e.g. affiliate- options.
			$selected_options,

			// Filter those out so the connector gets back true group IDs.
			function( $maybe_group_id ) {

				return strstr( $maybe_group_id, 'affiliate-' )
					? false // Affiliate ID, remove.
					: true; // Probably group ID, leave.
			}
		);
	}

	/**
	 * Customize the label.
	 *
	 * @since 2.13.1
	 */
	private function customize_label_hooks() {
		add_filter( 'affwp_group_connector_label', array( $this, 'label' ), 10, 3 );
	}

	/**
	 * Filter the label for the connector.
	 *
	 * @since 2.13.0
	 *
	 * @param string $label      The default label.
	 * @param string $group_type The group type of the connector.
	 * @param string $item       The item of the connector.
	 *
	 * @return string
	 */
	public function label( string $label, string $group_type, string $item ) : string {

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $label; // Not our group type.
		}

		return __( 'Privacy', 'affiliate-wp' );
	}

	/**
	 * Are there no affiliates?
	 *
	 * @since 2.13.0
	 *
	 * @return bool
	 */
	private function there_are_no_affiliates() : bool {
		return $this->get_affiliate_count() <= 0;
	}

	/**
	 * Filter the description of the connector.
	 *
	 * @since 2.13.0
	 *
	 * @param string $description The default description.
	 * @param string $context The context.
	 * @param string $group_type The group type of the connector.
	 * @param string $item The item of the connector.
	 *
	 * @return string
	 */
	public function description( string $description, string $context, string $group_type, string $item ) : string {

		if ( 'creative' !== $item ) {
			return $description;
		}

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $description; // Not our group type.
		}

		if ( 'no_groups' === $context && $this->there_are_no_affiliates() ) {

			// There are no groups and there are no affiliates.
			return sprintf(

				// Translators: %1$s is the instruction, %2$s is the link instruction.
				__( 'Add %1$s and/or %2$s to share this creative privately with.', 'affiliate-wp' ),
				sprintf(
					'<a href="admin.php?page=affiliate-wp-affiliates">%1$s</a>',
					__( 'affiliates', 'affiliate-wp' )
				),
				sprintf(
					'<a href="admin.php?page=affiliate-wp-creatives-categories">%1$s</a>',
					__( 'affiliate groups', 'affiliate-wp' )
				)
			);
		}

		return __( 'Select affiliate(s) and/or affiliate groups to share this creative privately. Or, leave blank to share this creative publicly with all affiliates.', 'affiliate-wp' );
	}

	/**
	 * Get affiliates in Select2 JSON format.
	 *
	 * @since 2.13.2
	 *
	 * @param string $search Search term, if any.
	 *
	 * @return array
	 */
	private function get_affiliates_in_select2_json_format( $search = '' ) : array {

		$all_affiliates = $this->get_all_affiliate_ids_sorted_alpha();

		$transient_key = $this->create_data_transient_key(
			'connector_get_affiliates_in_select2_format_',
			array(
				$search,
				$all_affiliates,
				$this->group_type,
				$this->item,
			)
		);

		$cache = get_transient( $transient_key );

		if ( is_array( $cache ) ) {
			return $cache;
		}

		$results = array_values(
			array_filter(
				array_map(
					function( int $affiliate_id ) {

						$id = absint( $affiliate_id );

						return $this->esc_select_2_json_item( array(
							'id'   => "affiliate-{$id}",
							'text' => html_entity_decode(
								$this->get_affiliate_option_text_format( $id )
							),
						) );
					},
					$all_affiliates
				),
				function ( $affiliate_select_2_json ) use ( $search ) {

					return empty( $search )
						? true // Include in no search.

						// Only if search term is found in name/email.
						: $this->stristr_search_select_2_text( $affiliate_select_2_json['text'], $search );
				}
			)
		);

		set_transient(
			$transient_key,
			$results,

			/**
			 * Filter how long the cache persists.
			 *
			 * @since 2.13.2
			 *
			 * @param int    $length     Number in seconds.
			 * @param string $group_type Group type of the connector.
			 * @param string $item       Item of the connector.
			 */
			apply_filters(
				'connector_get_affiliates_in_select2_format_cache_timeout',
				5,
				$this->group_type,
				$this->item
			)
		);

		/**
		 * Filter the results of affiliates in JSON format.
		 *
		 * @since 2.13.2
		 *
		 * @param array $results     Results.
		 * @param string $search     Search term, if any.
		 * @param string $group_type Group type of the connector.
		 * @param string $item       Item of the connector.
		 */
		return apply_filters(
			'get_affiliates_in_select2_json_format',
			$results,
			$search,
			$this->group_type,
			$this->item
		);
	}

	/**
	 * Modify the filter names for multiselect positioning.
	 *
	 * @since  2.12.0
	 *
	 * @param  string $filter_name Filter.
	 *
	 * @return string Our filter.
	 */
	public function change_multiselect_form_position_filter_to_before_status( $filter_name ) : string {

		if ( 'affwp_edit_creative_bottom' === $filter_name ) {
			return 'affwp_edit_creative_before_status'; // We added these hooks specifically for creatives.
		}

		if ( 'affwp_new_creative_bottom' === $filter_name ) {
			return 'affwp_new_creative_before_status'; // We added these hooks specifically for creatives.
		}

		return $filter_name;
	}
}
