<?php
/**
 * Setting up post types
 *
 * @category Class
 * @package  Enon\Config\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\WP\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class Setup_Gutenberg.
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Setup_Post_Types implements Actions, Task {
	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->add_actions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
        add_action( 'init', 'post_type_supprt');		
	}

    /**
     * Post type support
     * 
     * @since 1.0.0
     */
    public function post_type_supprt() {
        add_post_type_support( 'post', 'page-attributes' );
    }
}
