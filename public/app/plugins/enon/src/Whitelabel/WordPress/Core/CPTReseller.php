<?php

namespace Enon\Whitelabel\WordPress\Core;

use Awsm\WPWrapper\BuildingPlans\Actions;
use Awsm\WPWrapper\BuildingPlans\Filters;
use Awsm\WPWrapper\BuildingPlans\Task;
use Enon\Whitelabel\ResellerData;

class CPTReseller implements Task, Actions, Filters
{
	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run()
	{
		$this->addActions();
		$this->addFilters();
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

		add_action( 'manage_reseller_posts_custom_column' , [ $this, 'reseller_custom_column_values' ], 10, 2 );
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function addFilters()
	{
		add_filter( 'manage_reseller_posts_columns', [ $this, 'reseller_posts_columns' ], 1000, 1 );
	}

	/**
	 * Removing meta Boxes.
	 */
	public function removeMetaBoxes() {
		remove_meta_box('wpseo_meta', 'reseller', 'normal');
	}

	/**
	 * Adding post type.
	 *
	 * @since 1.0.0
	 */
	public function add()
	{
		$labels = array(
			'name'               => _x( 'Reseller', 'post type general name', 'enon' ),
			'singular_name'      => _x( 'Reseller', 'post type singular name', 'enon' ),
			'menu_name'          => _x( 'Resellers', 'admin menu', 'enon' ),
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
			'menu_icon'          => 'dashicons-businessman',
			'supports'           => array( 'thumbnail' )
		);

		register_post_type( 'reseller', $args );
	}

	public function reseller_posts_columns( $columns ) {
		// unset( $columns['title']  );
		unset( $columns['author'] );
		unset( $columns['date']   );
		unset( $columns['wpseo-links']   );
		unset( $columns['ratings']   );

		$columns['company_name']  = __( 'Company Name', 'enon' );
		$columns['contact_name']  = __( 'Contact Name', 'enon' );
		$columns['contact_email'] = __( 'Contact Email', 'enon' );
		$columns['contact_email'] = __( 'Contact Email', 'enon' );

		return $columns;
	}

	public function reseller_custom_column_values( $column, $postId ) {
		$resellerData = new ResellerData();
		$resellerData->setPostId( $postId );

		switch ( $column ) {
			case 'company_name':
				echo $resellerData->getCompanyName();
				break;
			case 'contact_name':
				echo $resellerData->getContactName();
				break;
			case 'contact_email':
				echo $resellerData->getContactEmail();
				break;
		}
	}
}
