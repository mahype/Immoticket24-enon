<?php
/**
 * Connecting Creatives to Groups.
 *
 * @package     AffiliateWP
 * @subpackage  AffiliateWP\Admin\Creatives\Categories
 * @copyright   Copyright (c) 2014, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.12.0
 *
 * @author      Aubrey Portwood <aportwood@awesomemotive.com>
 */

// phpcs:disable Generic.Commenting.DocComment.MissingShort -- No need to re-document some methods and properties.

namespace AffiliateWP\Admin\Creatives\Categories;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( dirname( __DIR__ ) ) . '/groups/class-connector.php';

/**
 * Connecting Creatives to Groups.
 *
 * @since 2.12.0
 */
final class Connector extends \AffiliateWP\Admin\Groups\Connector {

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $capability = 'manage_creatives';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $groups_column_before = 'shortcode';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $group_plural = '';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $group_single = '';

	/** @var string This is documented in includes/admin/groups/class-connector.php */
	protected $group_type = 'creative-category';

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
	 * @since 2.12.0
	 */
	public function __construct() {

		// Translate properties.
		$this->item_plural  = __( 'Creatives', 'affiliate-wp' );
		$this->item_single  = __( 'Creative', 'affiliate-wp' );
		$this->group_plural = __( 'Categories', 'affiliate-wp' );
		$this->group_single = __( 'Category', 'affiliate-wp' );

		// Setup the template for this being in the 2nd place on the new/edit forms.
		$this->modify_form_tag   = 'tr';
		$this->modify_form_class = 'form-row';
		$this->modify_row_tag    = 'div';
		$this->modify_row_class  = '';

		$this->modify_multiselect_position_to_before_description_hooks();

		parent::__construct();
	}

	/**
	 * Modify the positioning hooks for the add/new templates to before description.
	 *
	 * Instead of last (default of the abstract class).
	 *
	 * @since  2.12.0
	 */
	private function modify_multiselect_position_to_before_description_hooks() {

		add_filter( 'affwp_filter_hook_name_affwp_edit_creative_bottom', array( $this, 'change_multiselect_form_position_filter_to_before_description' ) );
		add_filter( 'affwp_filter_hook_name_affwp_new_creative_bottom', array( $this, 'change_multiselect_form_position_filter_to_before_description' ) );
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
	public function change_multiselect_form_position_filter_to_before_description( $filter_name ) {

		if ( 'affwp_edit_creative_bottom' === $filter_name ) {
			return 'affwp_edit_before_description'; // We added these hooks specifically for creatives.
		}

		if ( 'affwp_new_creative_bottom' === $filter_name ) {
			return 'affwp_new_before_description'; // We added these hooks specifically for creatives.
		}

		return $filter_name;
	}
}
