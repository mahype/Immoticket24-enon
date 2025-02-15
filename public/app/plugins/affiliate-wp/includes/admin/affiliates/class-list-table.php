<?php
/**
 * Affiliates Admin List Table
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Affiliates
 * @copyright   Copyright (c) 2014, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.9
 */

use AffWP\Admin\List_Table;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AffWP_Affiliates_Table Class
 *
 * Renders the Affiliates table on the Affiliates page
 *
 * @since 1.0
 *
 * @see \AffWP\Admin\List_Table
 */
class AffWP_Affiliates_Table extends List_Table {

	/**
	 * Default number of items to show per page
	 *
	 * @var string
	 * @since 1.0
	 */
	public $per_page = 30;

	/**
	 * Total number of affiliates found
	 *
	 * @var int
	 * @since 1.0
	 */
	public $total_count;

	/**
	 * Number of active affiliates found
	 *
	 * @var string
	 * @since 1.0
	 */
	public $active_count;

	/**
	 *  Number of inactive affiliates found
	 *
	 * @var string
	 * @since 1.0
	 */
	public $inactive_count;

	/**
	 * Number of pending affiliates found
	 *
	 * @var string
	 * @since 1.0
	 */
	public $pending_count;

	/**
	 * Number of rejected affiliates found
	 *
	 * @var string
	 * @since 1.0
	 */
	public $rejected_count;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @see WP_List_Table::__construct()
	 *
	 * @param array $args Optional. Arbitrary display and query arguments to pass through
	 *                    the list table. Default empty array.
	 */
	public function __construct( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'singular' => 'affiliate',
			'plural'   => 'affiliates',
		) );

		parent::__construct( $args );

		$this->get_affiliate_counts();
	}

	/**
	 * Show the search field
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param string $text Label for the search box
	 * @param string $input_id ID of the search box
	 *
	 * @return void
	 */
	public function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
			return;

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) )
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		if ( ! empty( $_REQUEST['order'] ) )
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
			<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
			<?php submit_button( $text, 'button', false, false, array( 'ID' => 'search-submit' ) ); ?>
		</p>
	<?php
	}

	/**
	 * Retrieve the view types
	 *
	 * @access public
	 * @since 1.0
	 * @return array $views All the views available
	 */
	public function get_views() {
		$base           = affwp_admin_url( 'affiliates' );

		$current        = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$total_count    = '&nbsp;<span class="count">(' . $this->total_count    . ')</span>';
		$active_count   = '&nbsp;<span class="count">(' . $this->active_count . ')</span>';
		$inactive_count = '&nbsp;<span class="count">(' . $this->inactive_count  . ')</span>';
		$pending_count  = '&nbsp;<span class="count">(' . $this->pending_count  . ')</span>';
		$rejected_count = '&nbsp;<span class="count">(' . $this->rejected_count  . ')</span>';

		$labels = affwp_get_affiliate_statuses();

		$views = array(
			'all'		=> sprintf( '<a href="%s"%s>%s</a>', esc_url( remove_query_arg( 'status', $base ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __('All', 'affiliate-wp') . $total_count ),
			'active'	=> sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'active', $base ) ), $current === 'active' ? ' class="current"' : '', $labels['active'] . $active_count ),
			'inactive'	=> sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'inactive', $base ) ), $current === 'inactive' ? ' class="current"' : '', $labels['inactive'] . $inactive_count ),
			'pending'	=> sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'pending', $base ) ), $current === 'pending' ? ' class="current"' : '', $labels['pending'] . $pending_count ),
			'rejected'	=> sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'rejected', $base ) ), $current === 'rejected' ? ' class="current"' : '', $labels['rejected'] . $rejected_count ),
		);

		return $views;
	}

	/**
	 * Retrieve the table columns
	 *
	 * @access public
	 * @since 1.0
	 * @return array $columns Array of all the list table columns
	 */
	public function get_columns() {
		$columns = array(
			'cb'              => '<input type="checkbox" />',
			'name'            => __( 'Name', 'affiliate-wp' ),
			'affiliate_id'    => __( 'Affiliate ID', 'affiliate-wp' ),
			'username'        => __( 'Username', 'affiliate-wp' ),
			'earnings'        => __( 'Paid Earnings', 'affiliate-wp' ),
			'unpaid_earnings' => __( 'Unpaid Earnings', 'affiliate-wp' ),
			'rate'            => __( 'Rate', 'affiliate-wp' ),
			'unpaid'          => __( 'Unpaid Referrals', 'affiliate-wp' ),
			'paid'            => __( 'Paid Referrals', 'affiliate-wp' ),
			'visits'          => __( 'Visits', 'affiliate-wp' ),
			'kyc_status'      => __( 'Identity Verification', 'affiliate-wp' ),
			'status'          => __( 'Status', 'affiliate-wp' ),
		);

		if ( ! affiliate_wp()->settings->get( 'enable_payouts_service' ) ) {
			unset( $columns['kyc_status'] );
		}

		/**
		 * Filters the affiliate list table columns.
		 *
		 * @since 1.0
		 *
		 * @param array                   $prepared_columns Prepared columns.
		 * @param array                   $columns          The columns for this list table.
		 * @param \AffWP_Affiliates_Table $this             List table instance.
		 */
		return apply_filters( 'affwp_affiliate_table_columns', $this->prepare_columns( $columns ), $columns, $this );
	}

	/**
	 * Retrieve the table's sortable columns
	 *
	 * @access public
	 * @since 1.0
	 * @return array Array of all the sortable columns
	 */
	public function get_sortable_columns() {
		$columns = array(
			'username'        => array( 'username',        false ),
			'name'            => array( 'name',            false ),
			'affiliate_id'    => array( 'affiliate_id',    false ),
			'earnings'        => array( 'earnings',        false ),
			'unpaid_earnings' => array( 'unpaid_earnings', false ),
			'rate'            => array( 'rate',            false ),
			'unpaid'          => array( 'unpaid',          false ),
			'paid'            => array( 'paid',            false ),
			'visits'          => array( 'visits',          false ),
			'status'          => array( 'status',          false ),
		);

		/**
		 * Filters the affiliates list table sortable columns.
		 *
		 * @since 1.0
		 *
		 * @param array                   $columns The sortable columns for this list table.
		 * @param \AffWP_Affiliates_Table $this    List table instance.
		 */
		return apply_filters( 'affwp_affiliate_table_sortable_columns', $columns, $this );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param \AffWP\Affiliate $affiliate   The current affiliate object.
	 * @param string           $column_name The name of the column
	 * @return string The column value.
	 */
	function column_default( $affiliate, $column_name ) {
		switch( $column_name ){

			default:
				$value = isset( $affiliate->$column_name ) ? $affiliate->$column_name : '';
				break;
		}

		/**
		 * Filters the default value for each affiliates list table column.
		 *
		 * This dynamic filter is appended with a suffix of the column name, for example:
		 *
		 *     `affwp_affiliate_table_referrals`
		 *
		 * @param string           $value     The column data.
		 * @param \AffWP\Affiliate $affiliate The current affiliate object
		 */
		return apply_filters( 'affwp_affiliate_table_' . $column_name, $value, $affiliate );
	}

	/**
	 * Renders the "Name" column in the affiliates list table.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param \AffWP\Affiliate $affiliate The current affiliate object.
	 * @return string Data shown in the Name column.
	 */
	public function column_name( $affiliate ) {

		$name = affiliate_wp()->affiliates->get_affiliate_name( $affiliate->affiliate_id );

		$base_query_args = array(
			'page'         => 'affiliate-wp-affiliates',
			'affiliate_id' => $affiliate->ID,
		);

		$username = affwp_get_affiliate_username( $affiliate );

		$row_actions = array_merge(
			array(
				'reports' => $this->get_row_action_link(
					__( 'Reports', 'affiliate-wp' ),
					array(
						'page'            => 'affiliate-wp-reports',
						'tab'             => 'referral',
						'affiliate_login' => $username ? $username : __( '(user deleted)', 'affiliate-wp' ),
						'range'           => 'this_month',
					),
				),
			),
			affiliate_wp()->affiliates->get_affiliate_name( $affiliate->affiliate_id )
				? array(
					'edit_user' => $this->get_row_action_link(
						__( 'Edit User', 'affiliate-wp' ),
						array(),
						array( 'base_uri' => get_edit_user_link( $affiliate->user_id ) )
					),
				)
				: array(),
		);

		if ( strtolower( $affiliate->status ) === 'active' ) {

			$row_actions = array_merge(
				$row_actions,
				array(
					'deactivate' => $this->get_row_action_link(
						__( 'Deactivate', 'affiliate-wp' ),
						array_merge(
							$base_query_args,
							array(
								'affwp_notice' => 'affiliate_deactivated',
								'action'       => 'deactivate',
							)
						),
						array( 'nonce' => 'affiliate-nonce' )
					),
				),
			);

		} elseif ( strtolower( $affiliate->status ) === 'pending' ) {

			$row_actions = array_merge(
				$row_actions,
				array(
					'review' => $this->get_row_action_link(
						__( 'Review', 'affiliate-wp' ),
						array_merge(
							$base_query_args,
							array(
								'affwp_notice' => false,
								'action'       => 'review_affiliate',
							),
						),
						array( 'nonce' => 'affiliate-nonce' )
					),
					'accept' => $this->get_row_action_link(
						__( 'Accept', 'affiliate-wp' ),
						array_merge(
							$base_query_args,
							array(
								'affwp_notice' => 'affiliate_accepted',
								'action'       => 'accept',
							),
						),
						array( 'nonce' => 'affiliate-nonce' )
					),
					'reject' => $this->get_row_action_link(
						__( 'Reject', 'affiliate-wp' ),
						array_merge(
							$base_query_args,
							array(
								'affwp_notice' => 'affiliate_rejected',
								'action'       => 'reject',
							),
						),
						array( 'nonce' => 'affiliate-nonce' )
					),
				),
			);

		} else {

			$row_actions = array_merge(
				$row_actions,
				array(
					'activate' => $this->get_row_action_link(
						__( 'Activate', 'affiliate-wp' ),
						array_merge(
							$base_query_args,
							array(
								'affwp_notice' => 'affiliate_activated',
								'action'       => 'activate',
							),
						),
						array( 'nonce' => 'affiliate-nonce' )
					),
				)
			);
		}

		$row_actions = array_merge(
			$row_actions,
			array(
				'delete' => $this->get_row_action_link(
					__( 'Delete', 'affiliate-wp' ),
					array_merge(
						$base_query_args,
						array(
							'affwp_notice' => false,
							'action'       => 'delete',
						),
					),
					array( 'nonce' => 'affiliate-nonce' )
				),
			)
		);

		/**
		 * Filters the name column data for the affiliates list table.
		 *
		 * @since 1.0
		 *
		 * @param string           $value     Data shown in the Name column.
		 * @param \AffWP\Affiliate $affiliate The current affiliate object.
		 */
		return apply_filters(
			'affwp_affiliate_table_name',
			sprintf(
				'
					<div class="info">
						<span class="gravatar">%1$s</span>
						<a class="name" href="%2$s">%3$s</a>
					</div>
					%4$s
				',
				get_avatar( $affiliate->user_id ?? 0, 32, '', $name ),
				esc_url(
					add_query_arg(
						array_merge(
							array(
								'page'         => 'affiliate-wp-affiliates',
								'affiliate_id' => $affiliate->ID,
							),
							array(
								'affwp_notice' => false,
								'action'       => 'edit_affiliate',
							)
						),
						admin_url( 'admin.php' )
					)
				),
				$name
					? $name
					: __( '(user deleted)', 'affiliate-wp' ),
				// Row Actions.
				sprintf(
					'<div class="row-actions">%1$s</div',
					$this->row_actions(
						/**
						 * Filters the row actions array for the Affiliates list table.
						 *
						 * @since 1.0
						 *
						 * @param array            $row_actions Row actions array.
						 * @param \AffWP\Affiliate $affiliate   Current affiliate.
						 */
						apply_filters( 'affwp_affiliate_row_actions', $row_actions, $affiliate ),
						true
					)
				)
			),
			$affiliate
		);
	}

	/**
	 * Renders the "Username" column in the affiliates list table.
	 *
	 * @access public
	 * @since  1.8
	 *
	 * @param \AffWP\Affiliate $affiliate The current affiliate object.
	 * @return string Data shown in the Username column.
	 */
	public function column_username( $affiliate ) {

		$row_actions = array();

		$username = affwp_get_affiliate_username( $affiliate );

		/**
		 * Filters the username column data for the affiliates list table.
		 *
		 * @since 1.8
		 *
		 * @param string           $username     Data shown in the Username column.
		 * @param \AffWP\Affiliate $affiliate The current affiliate object.
		 */
		return apply_filters(
			'affwp_affiliate_table_username',
			sprintf(
				'<span class="username">%1$s</span>',
				$username ? $username : __( '(user deleted)', 'affiliate-wp' )
			),
			$affiliate
		);
	}

	/**
	 * Renders the checkbox column in the affiliates list table.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param \AffWP\Affiliate $affiliate The current affiliate object.
	 * @return string Displays a checkbox.
	 */
	function column_cb( $affiliate ) {
		return '<input type="checkbox" name="affiliate_id[]" value="' . absint( $affiliate->affiliate_id ) . '" />';
	}

	/**
	 * Renders the earnings column in the affiliates list table.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param \AffWP\Affiliate $affiliate The current affiliate object.
	 * @return string Affiliate paid earnings link.
	 */
	function column_earnings( $affiliate ) {
		$value = affwp_get_affiliate_earnings( $affiliate->affiliate_id, true );

		/**
		 * Filters the earnings column data for the affiliates list table.
		 *
		 * @since 1.0
		 *
		 * @param string           $value     Data shown in the Earnings column.
		 * @param \AffWP\Affiliate $affiliate The current affiliate object.
		 */
		return apply_filters( 'affwp_affiliate_table_earnings', $value, $affiliate );
	}

	/**
	 * Renders the Unpaid Earnings column in the affiliates list table.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param \AffWP\Affiliate $affiliate The current affiliate object.
	 * @return string Affiliate paid earnings link.
	 */
	function column_unpaid_earnings( $affiliate ) {
		$value = affwp_get_affiliate_unpaid_earnings( $affiliate, true );

		/**
		 * Filters the Unpaid Earnings column data for the affiliates list table.
		 *
		 * @since 2.0
		 *
		 * @param string           $value     Data shown in the Unpaid Earnings column.
		 * @param \AffWP\Affiliate $affiliate The current affiliate object.
		 */
		return apply_filters( 'affwp_affiliate_table_unpaid_earnings', $value, $affiliate );
	}

	/**
	 * Renders the "Rate" column in the affiliates list table.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param \AffWP\Affiliate $affiliate The current affiliate object.
	 * @return string Affiliate rate.
	 */
	function column_rate( $affiliate ) {
		$value = affwp_get_affiliate_rate( $affiliate->affiliate_id, true );

		/**
		 * Filters the rate column data for the affiliates list table.
		 *
		 * @since 1.0
		 *
		 * @param string           $value     Data shown in the Rate column.
		 * @param \AffWP\Affiliate $affiliate The current affiliate object.
		 */
		return apply_filters( 'affwp_affiliate_table_rate', $value, $affiliate );
	}


	/**
	 * Renders the unpaid referrals column in the affiliates list table.
	 *
	 * @access public
	 * @since  1.7.5
	 *
	 * @param \AffWP\Affiliate $affiliate   The current affiliate object.
	 * @return string The unpaid referrals link.
	 */
	function column_unpaid( $affiliate ) {
		$unpaid_count = affiliate_wp()->referrals->unpaid_count( '', $affiliate->affiliate_id );

		$value = affwp_admin_link( 'referrals', $unpaid_count, array( 'affiliate_id' => $affiliate->affiliate_id, 'status' => 'unpaid' ) );

		/**
		 * Filters the unpaid referrals column data for the affiliates list table.
		 *
		 * @since 1.7.5
		 *
		 * @param string           $value     Data shown in the Unpaid column.
		 * @param \AffWP\Affiliate $affiliate The current affiliate object.
		 */
		return apply_filters( 'affwp_affiliate_table_unpaid', $value, $affiliate );
	}


	/**
	 * Renders the paid referrals column in the affiliates list table.
	 *
	 * @access public
	 * @since  1.0
	 * @since 2.9.5 Renamed to 'column_paid' from 'column_referrals'.
	 *
	 * @param \AffWP\Affiliate $affiliate The current affiliate object.
	 * @return string The affiliate referrals link.
	 */
	function column_paid( $affiliate ) {
		$paid_count = affiliate_wp()->referrals->paid_count( '', $affiliate->affiliate_id );

		$value = affwp_admin_link( 'referrals', $paid_count, array( 'affiliate_id' => $affiliate->affiliate_id, 'status' => 'paid' ) );

		/**
		 * Filters the paid referrals column data for the affiliates list table.
		 *
		 * @since 1.0 as 'affwp_affiliate_table_referrals'
		 * @since 2.9.5 Renamed to 'affwp_affiliate_table_paid'
		 *
		 * @param string $value     Data shown in the Paid Referrals column.
		 * @param array  $affiliate Contains all the data of the affiliate.
		 */
		return apply_filters( 'affwp_affiliate_table_paid', $value, $affiliate );
	}

	/**
	 * Renders the visits column in the affiliates list table.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param \AffWP\Affiliate $affiliate The current affiliate object.
	 * @return string visits link
	 */
	function column_visits( $affiliate ) {
		$value = affwp_admin_link( 'visits', affwp_get_affiliate_visit_count( $affiliate->affiliate_id ), array( 'affiliate' => $affiliate->affiliate_id ) );

		/**
		 * Filters the username visits data for the affiliates list table.
		 *
		 * @since 1.0
		 *
		 * @param string           $value     Data shown in the Visits column.
		 * @param \AffWP\Affiliate $affiliate The current affiliate object.
		 */
		return apply_filters( 'affwp_affiliate_table_visits', $value, $affiliate );
	}

	/**
	 * Renders the identity verification column in the affiliates list table.
	 *
	 * @since 2.4
	 *
	 * @param \AffWP\Affiliate $affiliate The current affiliate object.
	 * @return string Visits link.
	 */
	public function column_kyc_status( $affiliate ) {

		$value = '';
		$payouts_service_account_meta = affwp_get_affiliate_meta( $affiliate->ID, 'payouts_service_account', true );

		if ( $payouts_service_account_meta && isset( $payouts_service_account_meta['kyc_status'] ) ) {
			$value = $payouts_service_account_meta['kyc_status'];
		}

		/**
		 * Filters the identity verification data for the affiliates list table.
		 *
		 * @since 2.4
		 *
		 * @param string           $value     Data shown in the Visits column.
		 * @param \AffWP\Affiliate $affiliate The current affiliate object.
		 */
		return apply_filters( 'affwp_affiliate_table_kyc_status', $value, $affiliate );
	}

	/**
	 * Renders the Status column in the affiliates list table.
	 *
	 * @since 2.7
	 * @since 2.16.0 Added html to color-code the status label.
	 *
	 * @param \AffWP\Affiliate $affiliate The current affiliate object.
	 * @return string Status label.
	 */
	public function column_status( $affiliate ) {
		return sprintf( '<span class="affwp-status %1$s">%2$s</span>',
			esc_attr( $affiliate->status ),
			affwp_get_affiliate_status_label( $affiliate )
		);
	}

	/**
	 * Renders the message to be displayed when there are no affiliates.
	 *
	 * @access public
	 * @since  1.7.2
	 */
	function no_items() {
		_e( 'No affiliates found.', 'affiliate-wp' );
	}

	/**
	 * Retrieve the bulk actions
	 *
	 * @access public
	 * @since 1.0
	 * @return array $actions Array of the bulk actions
	 */
	public function get_bulk_actions() {
		$actions = array(
			'accept'     => __( 'Accept', 'affiliate-wp' ),
			'reject'     => __( 'Reject', 'affiliate-wp' ),
			'activate'   => __( 'Activate', 'affiliate-wp' ),
			'deactivate' => __( 'Deactivate', 'affiliate-wp' ),
			'delete'     => __( 'Delete', 'affiliate-wp' )
		);

		/**
		 * Filters the bulk actions to return in the affiliates list table.
		 *
		 * @since 1.0
		 *
		 * @param array $actions Bulk actions.
		 */
		return apply_filters( 'affwp_affiliates_bulk_actions', $actions );
	}

	/**
	 * Process the bulk actions
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function process_bulk_action() {

		if ( empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-affiliates' ) && ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'affiliate-nonce' ) ) {
			return;
		}

		$ids = isset( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : false;

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		$ids = array_map( 'absint', $ids );

		if ( empty( $ids ) ) {
			return;
		}

		if ( 'delete' === $this->current_action() ) {
			$redirect = affwp_admin_url( 'affiliates', array(
				'action'                        => 'delete',
				'affiliate_id'                  => $ids,
				'affwp_delete_affiliates_nonce' => wp_create_nonce( 'affwp_delete_affiliates_nonce' ),
			) );

			wp_safe_redirect( $redirect );
		}

		$dynamic_coupons_enabled = affiliate_wp()->settings->get( 'dynamic_coupons' );

		foreach ( $ids as $id ) {

			if ( $dynamic_coupons_enabled && in_array( $this->current_action(), array( 'accept', 'activate' ) ) ) {
				$coupons = affwp_get_dynamic_affiliate_coupons( $id, false );

				if ( empty( $coupons ) ) {
					$coupon_added = affiliate_wp()->affiliates->coupons->add( array( 'affiliate_id' => $id ) );

					if ( false === $coupon_added ) {
						affiliate_wp()->utils->log( sprintf( 'Coupon could not be added for affiliate #%1$d.', $id ) );
					}
				}
			}

			if ( 'accept' === $this->current_action() ) {
				affwp_set_affiliate_status( $id, 'active' );
			}

			if ( 'reject' === $this->current_action() ) {
				affwp_set_affiliate_status( $id, 'rejected' );
			}

			if ( 'activate' === $this->current_action() ) {
				affwp_set_affiliate_status( $id, 'active' );
			}

			if ( 'deactivate' === $this->current_action() ) {
				affwp_set_affiliate_status( $id, 'inactive' );
			}

			/**
			 * Fires after an affiliate bulk action is performed.
			 *
			 * The dynamic portion of the hook name, `$this->current_action()` refers
			 * to the current bulk action being performed.
			 *
			 * @since 2.0.2
			 *
			 * @param int $id The ID of the object.
			 */
			do_action( 'affwp_affiliates_do_bulk_action_' . $this->current_action(), $id );

		}

	}

	/**
	 * Retrieve the discount code counts
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function get_affiliate_counts() {

		$search = isset( $_GET['s'] ) ? $_GET['s'] : '';

		$this->active_count = affiliate_wp()->affiliates->count(
			array_merge( $this->query_args, array(
				'status' => 'active',
				'search' => $search
			) )
		);

		$this->inactive_count = affiliate_wp()->affiliates->count(
			array_merge( $this->query_args, array(
				'status' => 'inactive',
				'search' => $search
			) )
		);

		$this->pending_count = affiliate_wp()->affiliates->count(
			array_merge( $this->query_args, array(
				'status' => 'pending',
				'search' => $search
			) )
		);

		$this->rejected_count = affiliate_wp()->affiliates->count(
			array_merge( $this->query_args, array(
				'status' => 'rejected',
				'search' => $search
			) )
		);

		$this->total_count = $this->active_count + $this->inactive_count + $this->pending_count + $this->rejected_count;
	}

	/**
	 * Retrieve all the data for all the Affiliates
	 *
	 * @access public
	 * @since 1.0
	 * @since 2.24.1 Improved for additional security (see comments).
	 *
	 * @return array $affiliate_data Array of all the data for the Affiliates
	 */
	public function affiliate_data() {

		$page = ( isset( $_GET['paged'] ) && is_numeric( $_GET['paged'] ) )
			? absint( $_GET['paged'] )
			: 1;

		// Note: Status validated in Affiliate_WP_DB_Affiliates::get_affiliates() against all valid status'.
		$status = ( isset( $_GET['status'] ) && is_string( $_GET['status'] ) )
			? $_GET['status']
			: '';

		// Note: Escaped with %%LIKE%% in Affiliate_WP_DB_Affiliates::get_affiliates().
		$search = ( isset( $_GET['s'] ) && is_string( $_GET['s'] ) )
			? $_GET['s']
			: '';

		// Note: Sanitized in Affiliate_WP_DB_Affiliates::get_affiliates(), can only be ASC or DESC.
		$order = ( isset( $_GET['order'] ) && is_string( $_GET['order'] ) )
			? $_GET['order']
			: 'DESC';

		// Note: Sanitized in Affiliate_WP_DB_Affiliates::get_affiliates(), accepts only specific columns in the affiliates database table.
		$orderby = ( isset( $_GET['orderby'] ) && is_string( $_GET['orderby'] ) )
			? $_GET['orderby']
			: 'affiliate_id';

		$per_page = $this->get_items_per_page( 'affwp_edit_affiliates_per_page', $this->per_page );

		$args = wp_parse_args( $this->query_args, array(
			'number'  => $per_page,
			'offset'  => $per_page * ( $page - 1 ),
			'status'  => $status,
			'search'  => $search,
			'orderby' => sanitize_text_field( $orderby ),
			'order'   => sanitize_text_field( $order ),
		) );

		/**
		 * Filters the arguments used to retrieve affiliates for the Affiliates list table.
		 *
		 * @since 2.4.4
		 *
		 * @param array                   $args Arguments passed to get_affiliates() to retrieve
		 *                                      the affiliate records for display.
		 * @param \AffWP_Affiliates_Table $this Affiliates list table instance.
		 */
		$args = apply_filters( 'affwp_affiliate_table_get_affiliates', $args, $this );

		$affiliates = affiliate_wp()->affiliates->get_affiliates( $args );

		// Retrieve the "current" total count for pagination purposes.
		$args['number'] = -1;

		$this->current_count = affiliate_wp()->affiliates->count( $args );

		return $affiliates;
	}

	/**
	 * Setup the final data for the table
	 *
	 * @access public
	 * @since 1.0
	 * @uses AffWP_Affiliates_Table::get_columns()
	 * @uses AffWP_Affiliates_Table::get_sortable_columns()
	 * @uses AffWP_Affiliates_Table::process_bulk_action()
	 * @uses AffWP_Affiliates_Table::affiliate_data()
	 * @uses WP_List_Table::get_pagenum()
	 * @uses WP_List_Table::set_pagination_args()
	 * @return void
	 */
	public function prepare_items() {
		$per_page = $this->get_items_per_page( 'affwp_edit_affiliates_per_page', $this->per_page );

		$columns = $this->get_columns();

		$hidden = array();

		$sortable = $this->get_sortable_columns();

		$this->get_column_info();

		$this->process_bulk_action();

		$data = $this->affiliate_data();

		$current_page = $this->get_pagenum();

		$status = isset( $_GET['status'] ) ? $_GET['status'] : 'any';

		switch( $status ) {
			case 'active':
				$total_items = $this->active_count;
				break;
			case 'inactive':
				$total_items = $this->inactive_count;
				break;
			case 'pending':
				$total_items = $this->pending_count;
				break;
			case 'rejected':
				$total_items = $this->rejected_count;
				break;
			case 'any':
				$total_items = $this->current_count;
				break;
		}

		$this->items = $data;

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );
	}
}
