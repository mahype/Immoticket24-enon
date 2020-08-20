<?php
/**
 * Google tag manager tasks.
 *
 * @category Class
 * @package  Enon\Misc\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;

use Enon\Models\Popups\Popup_Premiumbewertung;

/**
 * Class Google_Tag_Manager
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Show_Edd_Log_Post_Type implements Filters, Task {


	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->add_filters();
	}

	/**
	 * Load targeting scripts into hooks.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_action( 'register_post_type_args', array( $this, 'modify_edd_log_post_type' ), 10, 2 );
	}

	public function modify_edd_log_post_type( $args, $name ) {
		if ( $name !== 'edd_log' ) {
			return $args;
		}

		$args['show_ui'] = true;

		return $args;
	}
}
