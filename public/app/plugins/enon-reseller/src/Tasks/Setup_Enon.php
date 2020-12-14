<?php
/**
 * Task which loads reseller scripts.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;
use Awsm\WP_Wrapper\Tools\Logger_Trait;
use Awsm\WP_Wrapper\Tools\Logger;

use Enon_Reseller\Models\Reseller;

/**
 * Class Setup_Enon.
 *
 * Running enon scripts
 *
 * @package Enon_Reseller\WordPress
 */
class Setup_Enon implements Task, Filters {
	/**
	 * Running scripts.
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
		add_filter( 'wpenon_show_tag_manager_scripts', '__return_false' );
		add_filter( 'wpenon_show_uptain_scripts', '__return_false' );
		add_filter( 'wpenon_show_adcell_scripts', '__return_false' );
	}
}
