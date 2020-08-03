<?php
/**
 * Remove OptimizePress.
 *
 * @category Class
 * @package  Enon\Mis\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class Performance
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Remove_Optimizepress implements Actions, Filters, Task {
	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Add actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		if ( is_admin() ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'removeScripts' ), 10000 );
		add_action( 'wp_print_styles', array( __CLASS__, 'removeScripts' ), 10000 );
	}

	/**
	 * Add filtters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		if ( is_admin() ) {
			return;
		}

		add_filter( 'immoticketenergieausweis_stylesheet_dependencies', array( __CLASS__, 'remove_epencies' ), 1 );
	}

	/**
	 * Removing depencies from Scripts.
	 *
	 * @param array $depencies Depencies.
	 * @return array $depencies Filtered depencies.
	 *
	 * @since 1.0.0
	 */
	public static function remove_epencies( $depencies ) {
		$page_id      = get_the_ID();
		$ban_page_ids = array( 294865 );

		if ( in_array( $page_id, $ban_page_ids, true ) ) {
			remove_filter( 'immoticketenergieausweis_stylesheet_dependencies', 'immoticketenergieausweis_optimizepress_add_dependencies' );
		}

		return $depencies;
	}

	/**
	 * Removing Scripts.
	 *
	 * @since 1.0.0
	 */
	public static function removeScripts() {
		global $wp_styles, $wp_scripts;

		if ( ! is_page() || ! defined( 'OP_SN' ) ) {
			return;
		}

		$page_id      = get_the_ID();
		$ban_page_ids = array( 294865, 300047 );

		$wp_styles->dequeue( 'op_map_custom' );
		$wp_styles->remove( 'op_map_custom' );

		if ( in_array( $page_id, $ban_page_ids, true ) ) {
			foreach ( $wp_scripts->queue as $handle ) {
				if ( substr( $handle, 0, strlen( OP_SN ) ) === OP_SN ) {
					$wp_scripts->dequeue( $handle );
					$wp_scripts->remove( $handle );
				}
			}

			foreach ( $wp_styles->queue as $handle ) {
				if ( substr( $handle, 0, strlen( OP_SN ) ) === OP_SN ) {
					$wp_styles->dequeue( $handle );
					$wp_scripts->remove( $handle );
				}
			}
		}
	}
}
