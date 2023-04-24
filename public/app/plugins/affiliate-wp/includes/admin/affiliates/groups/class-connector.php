<?php
/**
 * Connecting Affiliates to Groups.
 *
 * @package     AffiliateWP
 * @subpackage  AffiliateWP\Admin\Affiliates\Groups
 * @copyright   Copyright (c) 2014, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.12.0
 *
 * @author      Aubrey Portwood <aportwood@awesomemotive.com>
 */

// phpcs:disable Generic.Commenting.DocComment.MissingShort -- No need to re-document some methods and properties.
// phpcs:disable PEAR.Functions.FunctionCallSignature.EmptyLine -- Empty lines OK.

namespace AffiliateWP\Admin\Affiliates\Groups;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( dirname( __DIR__ ) ) . '/groups/class-connector.php';

/**
 * Connecting Affiliates to Groups.
 *
 * @since 2.12.0
 */
final class Connector extends \AffiliateWP\Admin\Groups\Connector {

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $capability = 'manage_affiliates';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $groups_column_before = 'affiliate_id';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $group_plural = '';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $group_single = '';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $group_type = 'affiliate-group';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $item = 'affiliate';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $item_plural = '';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $item_single = '';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $object_id_property = 'affiliate_id';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $selector_type = 'single';

	/**
	 * Construct
	 *
	 * @since 2.12.0
	 */
	public function __construct() {

		// Translate properties.
		$this->item_plural  = __( 'Affiliates', 'affiliate-wp' );
		$this->item_single  = __( 'Affiliate', 'affiliate-wp' );
		$this->group_plural = __( 'Groups', 'affiliate-wp' ); // Yes, you can only select one group, so no "groups".
		$this->group_single = __( 'Group', 'affiliate-wp' );

		// Setup the template for this being in the after status field on the new/edit forms.
		$this->modify_form_tag   = 'tr';
		$this->modify_form_class = 'form-row';
		$this->modify_row_tag    = '';

		$this->modify_multiselect_position_to_after_status();
		$this->show_info_tooltip_on_groups();

		parent::__construct();
	}

	/**
	 * Hooks for showing tooltip on groups for more information.
	 *
	 * @since 2.13.0
	 *
	 * @return void
	 */
	private function show_info_tooltip_on_groups() {
		add_filter( 'affwp_group_management_after_column_group_title', array( $this, 'format_groups' ), 10, 3 );
	}

	/**
	 * Format groups w/ tooltip.
	 *
	 * This helps users see what the settings are for a group
	 * in the list table.
	 *
	 * @since 2.13.0
	 *
	 * @param string $title      The title of the group (default).
	 * @param string $group_type The group type for the connector.
	 * @param string $item       The item for the connector.
	 *
	 * @return string
	 */
	public function format_groups( string $title, string $group_type, string $item ) : string {

		if ( ! $this->is_same_connector( $group_type, $item ) ) {
			return $title;
		}

		$group_id = affiliate_wp()->groups->get_group_id_by_title( wp_strip_all_tags( $title ), $group_type );

		if ( ! $this->is_numeric_and_gt_zero( $group_id ) ) {
			return $title;
		}

		$group = affiliate_wp()->groups->get_group( $group_id );

		if ( ! is_a( $group, '\AffiliateWP\Groups\Group' ) ) {
			return $title;
		}

		return affwp_text_tooltip(
			$title,
			sprintf(
				'%1$s<br> %2$s<br> %3$s<br> %4$s',

				// Translators: $s is the value of the information.
				sprintf( __( 'Default Group: %s', 'affiliate-wp' ), true === $group->get_meta( 'default-group', false ) ? __( 'Yes', 'affiliate-wp' ) : __( 'No', 'affiliate-wp' ) ),

				// Translators: $s is the value of the information.
				sprintf( __( 'Rate: %s', 'affiliate-wp' ), $this->get_rate_by_meta( $group->get_meta( 'rate', '' ), $group->get_meta( 'rate-type', '' ) ) ),

				// Translators: $s is the value of the information.
				sprintf( __( 'Rate Type: %s', 'affiliate-wp' ), $this->get_rate_type_by_meta( $group->get_meta( 'rate-type', '' ) ) ),

				// Translators: $s is the value of the information.
				sprintf( __( 'Flate Rate Basis: %s', 'affiliate-wp' ), $this->get_flat_rate_basis_by_meta( $group->get_meta( 'flat-rate-basis', '' ) ) )
			),
			false
		);
	}

	/**
	 * Get the rate.
	 *
	 * @since 2.13.0
	 *
	 * @param int|float $rate      The rate.
	 * @param string    $rate_type The rate type.
	 *
	 * @return string
	 */
	private function get_rate_by_meta( $rate, string $rate_type ) : string {

		if ( empty( $rate ) ) {
			return __( 'Use Global Setting', 'affiliate-wp' );
		}

		if ( 'percentage' === $rate_type && ! is_float( $rate ) ) {
			return $rate / 100;
		}

		return $rate;
	}

	/**
	 * Get flat rate basis.
	 *
	 * @since 2.13.0
	 *
	 * @param string $setting The setting in the group meta.
	 *
	 * @return string
	 */
	private function get_flat_rate_basis_by_meta( string $setting ) : string {

		if ( 'per-order' === $setting ) {
			return __( 'Per Order', 'affiliate-wp' );
		}

		if ( 'per-product' === $setting ) {
			return __( 'Per Product', 'affiliate-wp' );
		}

		return __( 'Use Global Setting', 'affiliate-wp' );
	}

	/**
	 * Get rate type.
	 *
	 * @since 2.13.0
	 *
	 * @param string $rate_type The rate type of the group.
	 *
	 * @return string
	 */
	private function get_rate_type_by_meta( string $rate_type ) : string {

		foreach ( affwp_get_affiliate_rate_types() as $type => $out ) {

			if ( $type === $rate_type ) {
				return $out;
			}
		}

		return __( 'Use Global Setting', 'affiliate-wp' );
	}

	/**
	 * Modify the positioning hooks for the add/new templates to after status field.
	 *
	 * Instead of last (default of the abstract class).
	 *
	 * @since  2.12.0
	 */
	private function modify_multiselect_position_to_after_status() {

		add_filter( 'affwp_filter_hook_name_affwp_edit_affiliate_bottom', array( $this, 'set_group_selector_to_after_status_field' ) );
		add_filter( 'affwp_filter_hook_name_affwp_new_affiliate_bottom', array( $this, 'set_group_selector_to_after_status_field' ) );
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
	public function set_group_selector_to_after_status_field( $filter_name ) {

		if ( 'affwp_edit_affiliate_bottom' === $filter_name ) {
			return 'affwp_edit_affiliate_after_status'; // We added these hooks specifically for affiliates.
		}

		if ( 'affwp_new_affiliate_bottom' === $filter_name ) {
			return 'affwp_new_affiliate_after_status'; // We added these hooks specifically for affiliates.
		}

		return $filter_name;
	}
}
