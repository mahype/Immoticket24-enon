<?php
/**
 * Setup Affiliate WP
 *
 * @category Class
 * @package  Enon\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class Setup_Edd.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class Setup_AffilateWP implements Task, Filters {	
	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 */
	public function run() {
        $this->add_filters();
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
	    add_filter('affwp_affiliate_table_columns', array( $this, 'add_pending_column' ), 10, 3 );
        add_filter('affwp_affiliate_table_pending_referals', array( $this, 'add_pending_column_content' ), 10, 2 );
        add_filter('affwp_affiliate_table_sortable_columns', array( $this, 'add_sortable_column' ), 10, 1 );
	}

    /**
     * Add a column to the affiliates table to show the pending referrals count.
     * 
     * @param array $prepared_columns Array of columns.
     * @param array $columns          Array of columns.
     * @param \AffWP\Affiliates_Table $list_table Affiliates list table object.
     * 
     * @return array
     * 
     * @since 1.0.0
     */
    public function add_pending_column( $prepared_columns, $columns, $list_table ) {
        $pos = 7;
        $new_col['pending_referals'] = __( 'Pending', 'affiliate-wp' );

        $prepared_columns = array_merge(
            array_slice($prepared_columns, 0, $pos),
            $new_col,
            array_slice($prepared_columns, $pos)
        );
        
        return $prepared_columns;
    }

    /**
     * Add the pending referrals column to the sortable columns array.
     * 
     * @param array $columns Array of sortable columns.
     * 
     * @return array
     * 
     * @since 1.0.0
     */
    public function add_sortable_column( $columns ) {
        $pos = 6;
        
        $new_col['pending_referals'] = array( 'pending', false );

        $columns = array_merge(
            array_slice($columns, 0, $pos),
            $new_col,
            array_slice($columns, $pos)
        );
        
        return $columns;
    }

    /**
     * Add the pending referrals count to the pending column.
     * 
     * @param string $value      Column value.
     * @param object $affiliate  Affiliate object.
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function add_pending_column_content( $value, $affiliate ) {
		$pending_count = affiliate_wp()->referrals->count_by_status( 'pending', $affiliate->affiliate_id );
		$value = affwp_admin_link( 'referrals', $pending_count, array( 'affiliate_id' => $affiliate->affiliate_id, 'status' => 'pending' ) );
        return $value;
    }
}
