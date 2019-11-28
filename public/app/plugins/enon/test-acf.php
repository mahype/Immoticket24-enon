<?php

/**
 * Plugin initialization file
 *
 * @package ENON
 * @since 1.0.0
 *
 * @wordpress-plugin
 * Plugin Name: Test ACF
 * Plugin URI:  https://energieausweis-online-erstellen.de
 * Version:     1.0.0
 * Author:      Awesome UG
 * Author URI:  https://www.awesome.ug
 * Text Domain: enon
 */

add_action( 'init',  function ()
{
	$labels = array(
		'name'               => _x( 'Resellerx', 'post type general name', 'enon' ),
		'singular_name'      => _x( 'Resellerx', 'post type singular name', 'enon' ),
		'menu_name'          => _x( 'Resellerx', 'admin menu', 'enon' ),
		'name_admin_bar'     => _x( 'Resellerx', 'add new on admin bar', 'enon' ),
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

	register_post_type( 'resellerx', $args );
});

add_action( 'acf/init', function () {
	acf_add_local_field_group(array(
		'key' => 'group_1',
		'title' => 'My Group',
		'fields' => array(
			array(
				'key' => 'field_1',
				'label' => 'Sub Title',
				'name' => 'sub_title',
				'type' => 'text',
			)
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'resellerx',
				),
			),
		),
	));
});
