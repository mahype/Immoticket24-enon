<?php

namespace awsmug\Enon\Modules\Performance;

use awsmug\Enon\Modules\Hooks_Submodule_Interface;
use awsmug\Enon\Modules\Hooks_Submodule_Trait;

/**
 * Class Optimize Press
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Optimize_Press extends Performance implements Hooks_Submodule_Interface {
	use Hooks_Submodule_Trait;

	/**
	 * Bootstraps the submodule by setting properties.
	 *
	 * @since 1.0.0
	 */
	protected function bootstrap() {
		$this->slug  = 'optimize-press';
		$this->title = __( 'OptimizePress', 'enon' );
	}

	/**
	 * Removing depencies.
	 *
	 * @param array $depencies Array with depencies.
	 * @return array $depencies Filtered array with depencies.
	 *
	 * @since 1.0.0
	 */
	public function remove_depencies( $depencies ) {
		$page_id = get_the_ID();
		$ban_page_ids = array( 294865, 300047 );

		if ( in_array( $page_id, $ban_page_ids ) ) {
			remove_filter( 'immoticketenergieausweis_stylesheet_dependencies', 'immoticketenergieausweis_optimizepress_add_dependencies' );
		}

		return $depencies;
	}

	/**
	 * Remove loaded scripts.
	 *
	 * @since 1.0.0
	 */
	public function remove_scripts() {
		global $wp_styles, $wp_scripts;

		if ( ! is_page() || ! defined( 'OP_SN' ) ) {
			return;
		}

		$page_id = get_the_ID();
		$ban_page_ids = array( 294865, 300047 );

		$wp_styles->dequeue( 'op_map_custom' );
		$wp_styles->remove( 'op_map_custom' );

		if ( in_array( $page_id, $ban_page_ids ) ) {
			foreach ( $wp_scripts->queue as $handle ) {
				if ( substr( $handle,0, strlen( OP_SN ) ) === OP_SN ) {
					$wp_scripts->dequeue( $handle );
					$wp_scripts->remove( $handle );
				}
			}

			foreach ( $wp_styles->queue as $handle ) {
				if ( substr( $handle,0 , strlen(OP_SN ) ) === OP_SN ) {
					$wp_styles->dequeue( $handle );
					$wp_scripts->remove( $handle );
				}
			}
		}
	}

	/**
	 * Sets up all action and filter hooks for the service.
	 *
	 * @since 1.0.0
	 */
	protected function setup_hooks() {
		parent::setup_hooks();

		if ( is_admin() ) {
			return;
		}

		$this->actions[] = array(
			'name'     => 'wp_enqueue_scripts',
			'callback' => array( $this, 'remove_scripts' ),
			'priority' => 10000,
		);
		$this->actions[] = array(
			'name'     => 'wp_print_styles',
			'callback' => array( $this, 'remove_scripts' ),
			'priority' => 10000,
		);
		$this->filters[] = array(
			'name'     => 'immoticketenergieausweis_stylesheet_dependencies',
			'callback' => array( $this, 'remove_depencies' ),
			'priority' => 1,
			'num_args' => 1,
		);
	}
}
