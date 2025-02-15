<?php
/**
 * Loading ACF Options.
 *
 * @category Class
 * @package  Enon\ACF
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;

use Enon\Logger;
use Enon\Models\Fieldsets\Fieldset_Page;
use Enon\Models\WP\Post_Meta;

/**
 * Class Add_Options.
 *
 * @package Enon\Config
 */
class Add_Page_Fields implements Task, Actions {

	/**
	 * Add_Page_Fields constructor.
	 *
	 * @param Logger $logger Logger object.
	 * @since 1.0.0
	 */
	public function __construct( Logger $logger ) {
		$this->logger = $logger;
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
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
		add_action( 'acf/init', [ $this, 'setup_post_meta' ] );
	}

	/**
	 * Setup option pages.
	 *
	 * @since 1.0.0
	 */
	public function setup_post_meta() {
		( new Post_Meta( 'page' ) )
			->add_fieldset( new Fieldset_Page() )
			->register();
	}
}
