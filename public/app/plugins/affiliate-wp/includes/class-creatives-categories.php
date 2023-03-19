<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- Name OK.
/**
 * Creative Category Filtering for the frontend.
 *
 * @since 2.12.0
 *
 * @package     AffiliateWP
 * @subpackage  AffiliateWP\Admin\Creatives\Categories
 * @copyright   Copyright (c) 2014, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2
 *
 * @author      Aubrey Portwood <aportwood@awesomemotive.com>
 */

namespace AffiliateWP\Creatives;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/utils/trait-nonce.php';
require_once __DIR__ . '/utils/trait-data.php';
require_once __DIR__ . '/utils/trait-connection.php';
require_once __DIR__ . '/utils/trait-select2.php';

/**
 * Creative Category Filtering.
 *
 * @since 2.12.0
 */
final class Categories {

	use \AffiliateWP\Utils\Nonce;
	use \AffiliateWP\Utils\Data;
	use \AffiliateWP\Utils\Connection;
	use \AffiliateWP\Utils\Select2;

	/**
	 * Nonce name/action for filtering creatives in this UI.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	private $filter_creatives_nonce = '';

	/**
	 * Plural name for the group.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	private $group_plural = '';

	/**
	 * Name for the group type.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	private $group_type = 'creative-category';

	/**
	 * The object property where the item stores it's ID.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	protected $object_id_property = 'creative_id';

	/**
	 * Construct
	 *
	 * @since 2.12.0
	 */
	public function __construct() {

		$this->group_plural = __( 'Categories', 'affiliate-wp' );

		$this->filter_creatives_nonce = $this->nonce_action( 'filter', 'creatives' );

		$this->hooks();
	}

	/**
	 * Filter the creatives shown on the frontend by category.
	 *
	 * @since  2.12.0
	 *
	 * @param  array $creatives Creatives from filter.
	 *
	 * @return array Creatives with items filtered out that are not connected
	 *                 to the selected group (cat).
	 */
	public function filter_creatives( $creatives ) {

		if ( ! is_array( $creatives ) ) {
			return $creatives; // We must have had an issue querying the DB, fail gracefully.
		}

		$group_id = filter_input( INPUT_GET, 'cat', FILTER_SANITIZE_NUMBER_INT );

		if ( ! $this->is_numeric_and_gt_zero( $group_id ) ) {
			return $creatives; // Not a valid selected category, don't filter.
		}

		// Filter out items not connected to our group.
		return $this->filter_out_items_not_connected_to_group(
			$creatives, // Items.
			intval( $group_id )
		);
	}

	/**
	 * Get all the groups (objects).
	 *
	 * @since 2.12.0
	 *
	 * @return mixed The groups, or the count of the groups.
	 *
	 * @throws \InvalidArgumentException If you do not supply a proper count parameter.
	 */
	private function get_all_non_empty_groups() {

		$groups = affiliate_wp()->groups->get_groups(
			array(
				'fields' => 'objects',
				'type'   => $this->group_type,
			),
			false
		);

		if ( ! is_array( $groups ) ) {
			return array(); // We couldn't use the DB API to get groups, fail gracefully.
		}

		if (
			! isset( $groups[0] ) ||
			! is_a(
				current( $groups ),
				'\AffiliateWP\Groups\Group'
			)
		) {
			return array(); // The first item should be a group, something wen't wrong, fail gracefully.
		}

		// Filter out non-empty groups (groups without connections to creatives).
		return array_filter(
			$groups,
			function( $group ) {

				if ( ! isset( $group->group_id ) || ! $this->is_numeric_and_gt_zero( $group->group_id ) ) {
					return false; // Broken group (likely not to happen).
				}

				$connected = array_filter(
					affiliate_wp()->connections->get_connected(
						'creative',
						'group',
						$group->group_id
					),
					function( $creative_id ) {

						$creative = affwp_get_creative( $creative_id );

						return isset( $creative->status )
							? 'active' === $creative->status
							: false;
					}
				);

				if ( ! is_array( $connected ) ) {
					return false; // Broken connetions, fail gracefully.
				}

				return count( $connected ) > 0 ? true : false;
			}
		);
	}

	/**
	 * Hooks
	 *
	 * @since  2.12.0
	 */
	private function hooks() {

		if ( is_admin() ) {
			return; // Just to be safe no hooks in admin are affected.
		}

		add_action( 'template_redirect', array( $this, 'register_connectables' ) );
		add_action( 'affwp_before_creatives', array( $this, 'view' ) );
		add_action( 'affwp_before_creatives_no_results', array( $this, 'view' ) );
		add_action( 'template_redirect', array( $this, 'redirect_with_selected_filter' ) );
		add_filter( 'affwp_creatives', array( $this, 'filter_creatives' ) );
	}

	/**
	 * Catch the POST then redirect with &cat= set.
	 *
	 * @since  2.12.0
	 *
	 * @return void If we are not filtering.
	 */
	public function redirect_with_selected_filter() {

		if (
			! isset( $_POST['filter-creative-category'] ) ||

			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- We're just checking if it's a valid ID.
			! $this->is_numeric_and_gt_zero( $_POST['filter-creative-category'] )
		) {
			return;
		}

		if ( ! $this->verify_nonce_action( $this->filter_creatives_nonce, 'filter-creatives' ) ) {
			return; // Nonce expired.
		}

		check_admin_referer(
			$this->nonce_action( $this->filter_creatives_nonce, 'filter-creatives' ),
			$this->nonce_action( $this->filter_creatives_nonce, 'filter-creatives' )
		);

		// Do a re-direct where &cat=int is set, that way POST is not re-submitted and NONCE can't expire.
		wp_safe_redirect(
			add_query_arg(
				array(
					'tab' => 'creatives',
					'cat' => intval( $_POST['filter-creative-category'] ),
				),
				get_the_permalink()
			)
		);

		exit;
	}

	/**
	 * Register connectables for creatives a groups (for the frontend).
	 *
	 * @since  2.12.0
	 *
	 * @throws \Exception If we cannot register the connectable.
	 *
	 * @return void If we already have the connectables registered.
	 */
	public function register_connectables() {

		if (
			affiliate_wp()->connections->is_registered_connectable( 'groups' ) &&
			affiliate_wp()->connections->is_registered_connectable( 'creative' )
		) {
			return; // Already registered.
		}

		$groups = affiliate_wp()->connections->register_connectable(
			array(
				'name'   => 'group',
				'table'  => affiliate_wp()->groups->table_name,
				'column' => affiliate_wp()->groups->primary_key,
			)
		);

		if ( true !== $groups ) {
			throw new \Exception( 'Unable to register groups as a connectable.' );
		}

		$items = affiliate_wp()->connections->register_connectable(
			array(
				'name'   => 'creative',
				'table'  => affiliate_wp()->creatives->table_name,
				'column' => affiliate_wp()->creatives->primary_key,
			)
		);

		if ( true === $items ) {
			return; // Done.
		}

		throw new \Exception( 'Unable to register a connectable for creatives.' );
	}

	/**
	 * View
	 *
	 * @since  2.12.0
	 */
	public function view() {

		$groups = $this->get_all_non_empty_groups();

		if ( empty( $groups ) ) {
			return; // No groups to select.
		}

		?>

		<form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'creatives', get_the_permalink() ) ); ?>">

			<p>
				<select name="filter-creative-category">

					<option value="">
						<?php

						// Translators: %s is the translated name of the group, e.g. Categories.
						echo esc_html( sprintf( __( 'All %s', 'affiliate-wp' ), $this->group_plural ) );

						?>
					</option>

					<?php foreach ( $groups as $group ) : ?>

						<?php $group_id = $group->get_id(); ?>

						<option
							<?php echo esc_attr( intval( filter_input( INPUT_GET, 'cat', FILTER_SANITIZE_NUMBER_INT ) ) === intval( $group_id ) ? 'selected' : '' ); ?>
							value="<?php echo absint( $group_id ); ?>">
								<?php echo esc_html( wp_trim_words( $group->get_title(), 10 ) ); ?>
						</option>
					<?php endforeach; ?>

				</select>

				<input type="submit" class="button" value="<?php esc_html_e( 'Filter', 'affiliate-wp' ); ?>">
			</p>

			<?php

			wp_nonce_field(
				$this->nonce_action( $this->filter_creatives_nonce, 'filter-creatives' ),
				$this->nonce_action( $this->filter_creatives_nonce, 'filter-creatives' )
			);

			?>

		</form>

		<?php
	}
}
