<?php
/**
 * Affiliates Grouping (Groups) Admin Screen Management
 *
 * @package     AffiliateWP
 * @subpackage  AffiliateWP\Admin\Affiliates
 * @copyright   Copyright (c) 2014, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.13.0
 *
 * @author      Aubrey Portwood <aportwood@awesomemotive.com>
 */

// phpcs:disable Generic.Commenting.DocComment.MissingShort -- No need to re-document some methods and properties.
// phpcs:disable PEAR.Functions.FunctionCallSignature.FirstArgumentPosition -- Allowing comments in function call lines.
// phpcs:disable PEAR.Functions.FunctionCallSignature.EmptyLine -- Allowing comments in function call lines.

namespace AffiliateWP\Admin\Affiliates\Groups;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once untrailingslashit( AFFILIATEWP_PLUGIN_DIR ) . '/includes/utils/trait-data.php';

// Meta traits.
require_once untrailingslashit( AFFILIATEWP_PLUGIN_DIR ) . '/includes/admin/affiliates/groups/meta/trait-custom-rate.php';
require_once untrailingslashit( AFFILIATEWP_PLUGIN_DIR ) . '/includes/admin/affiliates/groups/meta/trait-rate.php';
require_once untrailingslashit( AFFILIATEWP_PLUGIN_DIR ) . '/includes/admin/affiliates/groups/meta/trait-rate-type.php';
require_once untrailingslashit( AFFILIATEWP_PLUGIN_DIR ) . '/includes/admin/affiliates/groups/meta/trait-default-group.php';
require_once untrailingslashit( AFFILIATEWP_PLUGIN_DIR ) . '/includes/admin/affiliates/groups/meta/trait-flat-rate-basis.php';
require_once untrailingslashit( AFFILIATEWP_PLUGIN_DIR ) . '/includes/admin/groups/meta/trait-description.php';

require_once dirname( dirname( __DIR__ ) ) . '/groups/class-management.php';

/**
 * Affiliates Grouping (Groups) Admin Screen Management.
 *
 * @since 2.13.0
 */
final class Management extends \AffiliateWP\Admin\Groups\Management {

	use \AffiliateWP\Utils\Data;

	/* Meta methods cut into traits for easier class organization. */
	use \AffiliateWP\Admin\Affiliates\Groups\Meta\Rate;
	use \AffiliateWP\Admin\Affiliates\Groups\Meta\Rate_Type;
	use \AffiliateWP\Admin\Affiliates\Groups\Meta\Default_Group;
	use \AffiliateWP\Admin\Affiliates\Groups\Meta\Flat_Rate_Basis;
	use \AffiliateWP\Admin\Affiliates\Groups\Meta\Description;
	use \AffiliateWP\Admin\Affiliates\Groups\Meta\Custom_Rate;

	/** @var string This is documented in includes/admin/groups/class-management.php */
	protected $capability = 'manage_affiliates';

	/** @var string This is documented in includes/admin/groups/class-management.php */
	protected $group_type = 'affiliate-group';

	/** @var string This is documented in includes/admin/groups/class-management.php */
	protected $item = 'affiliate';

	/** @var string This is documented in includes/admin/groups/class-management.php */
	protected $menu_slug = 'affiliate-groups';

	/** @var string This is documented in includes/admin/groups/class-management.php */
	protected $object_id_property = 'affiliate_id';

	/** @var string This is documented in includes/admin/groups/class-management.php */
	protected $parent = 'affiliate-wp-affiliates';

	/**
	 * Construct
	 *
	 * @since 2.13.0
	 */
	public function __construct() {

		$this->item_plural  = __( 'Affiliates', 'affiliate-wp' );
		$this->item_single  = __( 'Affiliate', 'affiliate-wp' );
		$this->menu_title   = __( 'Groups', 'affiliate-wp' );
		$this->page_title   = __( 'Affiliate Groups', 'affiliate-wp' );
		$this->plural_title = __( 'Groups', 'affiliate-wp' );
		$this->single_title = __( 'Group', 'affiliate-wp' );

		// Meta fields (see ./meta/trait-*.php).
		$this->meta_fields = array(

			// Default group.
			'default-group'   => array(
				'main'  => array( $this, 'default_group_main' ),
				'edit'  => array( $this, 'default_group_edit' ),
				'save'  => array( $this, 'default_group_save' ),
				'hooks' => array( $this, 'default_group_hooks' ),
			),

			// Description field.
			'description'     => array(
				'main'          => array( $this, 'description_main' ),
				'edit'          => array( $this, 'description_edit' ),
				'save'          => array( $this, 'description_save' ),
				'column_header' => array( $this, 'description_column_header' ),
				'column_value'  => array( $this, 'description_column_value' ),
			),

			// Assign custom rate.
			'custom-rate'     => array(
				'main' => array( $this, 'custom_rate_main' ),
				'edit' => array( $this, 'custom_rate_edit' ),
			),

			// Rate type.
			'rate-type'       => array(
				'main'          => array( $this, 'rate_type_main' ),
				'edit'          => array( $this, 'rate_type_edit' ),
				'save'          => array( $this, 'rate_type_save' ),
				'column_header' => array( $this, 'rate_type_column_header' ),
				'column_value'  => array( $this, 'rate_type_column_value' ),
			),

			// Flat rate basis.
			'flat-rate-basis' => array(
				'main'          => array( $this, 'flat_rate_basis_main' ),
				'edit'          => array( $this, 'flat_rate_basis_edit' ),
				'save'          => array( $this, 'flat_rate_basis_save' ),
				'column_header' => array( $this, 'flat_rate_basis_column_header' ),
				'column_value'  => array( $this, 'flat_rate_basis_column_value' ),
			),

			// Rate value.
			'rate'            => array(
				'main'          => array( $this, 'rate_main' ),
				'edit'          => array( $this, 'rate_edit' ),
				'save'          => array( $this, 'rate_save' ),
				'column_header' => array( $this, 'rate_column_header' ),
				'column_value'  => array( $this, 'rate_column_value' ),
			),

		);

		parent::__construct();
	}
}
