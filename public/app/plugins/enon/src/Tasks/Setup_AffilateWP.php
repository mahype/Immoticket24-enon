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
use Awsm\WP_Wrapper\Tools\Logger;

use Enon\Models\Enon\Prevent_Completion;

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
		// add_filter('affwp_affiliate_table_columns', array( $this, 'add_pending_column' ), 10, 3 );
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
        $prepared_columns['pending'] = __( 'Pending', 'affiliate-wp' );
        return $prepared_columns;
    }
}
