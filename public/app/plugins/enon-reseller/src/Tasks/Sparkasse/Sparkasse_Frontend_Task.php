<?php
/**
 * Sparkasse Frontend class.
 *
 * @category Class
 * @package  Enon\Misc
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Sparkasse;

use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;

use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Tools\Logger_Trait;
use Enon_Reseller\Models\Reseller;

/**
 * Class PluginEdd.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
abstract class Sparkasse_Frontend_Task implements Task {
	use Logger_Trait;

	/**
	 * Loading Plugin scripts.
	 *
	 * @param Reseller $reseller Logger object.
	 * @param Logger   $logger   Logger object.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Reseller $reseller, Logger $logger ) {
		$this->logger   = $logger;
		$this->reseller = $reseller;
	}

	/**
	 * Reseller object.
	 *
	 * @var Reseller;
	 *
	 * @since 1.0.0
	 */
	private $reseller;

	/**
	 * Runs sparkasse Frontend Task is possible.
	 *
	 * @return bool
	 */
	public function can_run() {
		if ( 321587 !== $this->reseller->get_post_id() ) {
			return false;
		}

		return true;
	}
}
