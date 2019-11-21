<?php

namespace Enon\Whitelabel\WordPress\Core;

use Awsm\WPWrapper\BuildingPlans\Actions;
use Awsm\WPWrapper\BuildingPlans\Task;

class CPTReseller implements Task, Actions
{
	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run()
	{
		$this->addActions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function addActions()
	{
		add_action( 'init',  [ $this, 'add' ] );
		add_action( 'add_meta_boxes', [ $this, 'removeMetaBoxes' ], 100 );
	}

	/**
	 * Removing meta Boxes.
	 */
	public function removeMetaBoxes() {
		remove_meta_box('wpseo_meta', 'reseller', 'normal');
	}

	public function add()
	{
		$labels = array(
			'name'               => _x( 'Reseller', 'post type general name', 'enon' ),
			'singular_name'      => _x( 'Reseller', 'post type singular name', 'enon' ),
			'menu_name'          => _x( 'Reseller', 'admin menu', 'enon' ),
			'name_admin_bar'     => _x( 'Reseller', 'add new on admin bar', 'enon' ),
			'add_new'            => _x( 'Add New', 'reseller', 'enon' ),
			'add_new_item'       => __( 'Add New reseller', 'enon' ),
			'new_item'           => __( 'New reseller', 'enon' ),
			'edit_item'          => __( 'Edit reseller', 'enon' ),
			'view_item'          => __( 'View reseller', 'enon' ),
			'all_items'          => __( 'All resellers', 'enon' ),
			'search_items'       => __( 'Search reseller', 'enon' ),
			'parent_item_colon'  => __( 'Parent resellers:', 'enon' ),
			'not_found'          => __( 'No reseller found.', 'enon' ),
			'not_found_in_trash' => __( 'No reseller found in Trash.', 'enon' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'enon' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'book' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'thumbnail' )
		);

		register_post_type( 'reseller', $args );
	}
}
