<?php

namespace awsmug\Enon\Tools;

/**
 * Class Performance
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Optimize_Press {
	/**
	 * Loading nesesary properties and functions.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->load_hooks();
	}

	public function load_hooks(){
		if( is_admin() ) {
			return;
		}
		add_action( 'wp_enqueue_scripts', array( $this, 'remove_scripts' ), 10000 );
		add_action( 'wp_print_styles', array( $this, 'remove_scripts' ), 10000 );

		add_filter( 'immoticketenergieausweis_stylesheet_dependencies', array( $this, 'remove_depencies' ), 1 );
	}

	public function remove_scripts() {
		$this->remove_optmize_press();
	}

	public function remove_depencies( $depencies ) {
		$page_id =  get_the_ID();
		$ban_page_ids = array( 294865 );

		if( in_array( $page_id, $ban_page_ids ) ) {
			remove_filter( 'immoticketenergieausweis_stylesheet_dependencies', 'immoticketenergieausweis_optimizepress_add_dependencies' );
		}

		return $depencies;
	}

	public function remove_optmize_press() {
		global $wp_styles, $wp_scripts;

		if( ! is_page() || ! defined( 'OP_SN' ) ) {
			return;
		}

		$page_id =  get_the_ID();
		$ban_page_ids = array( 294865, 300047 );

		$wp_styles->dequeue( 'op_map_custom' );
		$wp_styles->remove( 'op_map_custom' );

		if( in_array( $page_id, $ban_page_ids ) ) {
			foreach( $wp_scripts->queue as $handle ) {
				if( substr( $handle,0 , strlen(OP_SN ) ) === OP_SN ) {
					$wp_scripts->dequeue( $handle );
					$wp_scripts->remove( $handle );
				}
			}

			foreach( $wp_styles->queue as $handle ) {
				if( substr( $handle,0 , strlen(OP_SN ) ) === OP_SN ) {
					$wp_styles->dequeue( $handle );
					$wp_scripts->remove( $handle );
				}
			}
		}
	}
}
