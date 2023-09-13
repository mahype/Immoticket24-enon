<?php
/**
 * Plugin Name:       Block - Trusted shops
 * Description:       Trusted shops block
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Sven Wagener
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       block-trusted-shops
 *
 * @package           awsm
 */

namespace AWSM\BlockTrustedShops;

use AWSM\LibAPI\Examples\TrustedShopsAPI;
use AWSM\LibAPI\Examples\TrustedShopsAuth;

require( dirname(__FILE__) . '/vendor/autoload.php' );

// 'TS_CLIENT_ID' and TS_CLIENT_SECRET' constants are set in wp-config.

function btsInit() {
	register_block_type( 
        __DIR__,
        [ 'render_callback' => 'AWSM\\BlockTrustedShops\\btsShowTestimonials' ]            
    );
}

add_action( 'init', 'AWSM\\BlockTrustedShops\\btsInit' );

function btsAddPostType() {
    register_post_type( 'ts-rating', [
        'labels'              => [
            'name'                  => _x( 'Ratings', 'Post type general name', 'textdomain' ),
            'singular_name'         => _x( 'Rating', 'Post type singular name', 'textdomain' ),
            'menu_name'             => _x( 'Ratings', 'Admin Menu text', 'textdomain' ),
            'name_admin_bar'        => _x( 'Rating', 'Add New on Toolbar', 'textdomain' ),
            'add_new'               => __( 'Add New', 'textdomain' ),
            'add_new_item'          => __( 'Add New Rating', 'textdomain' ),
            'new_item'              => __( 'New Rating', 'textdomain' ),
            'edit_item'             => __( 'Edit Rating', 'textdomain' ),
            'view_item'             => __( 'View Rating', 'textdomain' ),
            'all_items'             => __( 'All Ratings', 'textdomain' ),
            'search_items'          => __( 'Search Ratings', 'textdomain' ),
            'parent_item_colon'     => __( 'Parent Ratings:', 'textdomain' ),
            'not_found'             => __( 'No ratings found.', 'textdomain' ),
            'not_found_in_trash'    => __( 'No ratings found in Trash.', 'textdomain' ),
            'featured_image'        => _x( 'Rating Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'archives'              => _x( 'Rating archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
            'insert_into_item'      => _x( 'Insert into rating', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
            'uploaded_to_this_item' => _x( 'Uploaded to this rating', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
            'filter_items_list'     => _x( 'Filter ratings list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
            'items_list_navigation' => _x( 'Ratings list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
            'items_list'            => _x( 'Ratings list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
        ],
        'public'              => true,
        'show_in_rest'        => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'capability_type'     => 'post',
        'publicly_queryable'  => false,
        'supports'            => array( 'title', 'editor', 'custom-fields' ),
        'has_archive'         => false,
        'can_export'          => false,
    ] );
}

add_action( 'init', 'AWSM\\BlockTrustedShops\\btsAddPostType' );

function btsShowTestimonials() {
    $args = array(
        'post_type' => 'ts-rating',
        'post_status' => 'publish',
        'orderby'   => 'rand',
        'posts_per_page' => 3, 
    );
     
    $the_query = new \WP_Query( $args );
     
    if (  ! $the_query->have_posts() ) {
        return  'Keine Testimonials gefunden.';
    }
    
    $content = '<div class="wp-block-columns testimonials">';
    while ( $the_query->have_posts() ) {       
        $the_query->the_post();
        $post = get_post();

        $text       = $post->post_content;
        $rating     = get_post_meta( $post->ID, 'rating', true );
        $dateFormat = get_option( 'date_format' );
        $date       = date_i18n( $dateFormat, strtotime( get_post_meta( $post->ID, 'review_date', true ) ) );

        /**
         * Stars
         */
        $stars = '<div class="testimonial-stars">';
        for( $i = 0; $i < $rating; $i++ ) {
            $stars.= '<div class="testimonial-star"><img src="/app/themes/jason/assets/img/icons/stern.webp" /></div>';
        }
        $stars.= '</div>';

        /**
         * Banner
         */
        $content.= '<div class="testimonial">';
            $content.= '<div class="testimonial-icon"><img src="/app/themes/jason/assets/img/icons/unbekanntes-testimonial.webp" /></div>';
            $content.= '<div class="testimonial-content"><div class="testimonial-date">' . $date . '</div><blockquote class="testimonial-text"><p>' . $text . '</p></blockquote>' . $stars . '</div>';
        $content.= '</div>';
    }
    $content.= '</div>'; 
    
    /* Restore original Post Data */
    wp_reset_postdata();
     
    return $content;
}

add_filter( 'rest_prepare_ts-rating', 'AWSM\\BlockTrustedShops\\btsShowMetaInRest', 10, 3 );

function btsUpdateRatings() {
    $tsAuth = new TrustedShopsAuth( TS_CLIENT_ID, TS_CLIENT_SECRET );
    $tsAPI  = new TrustedShopsAPI( $tsAuth );

    update_option( 'btsLastUpdate', 0 );
    $lastUpdate     = get_option( 'btsLastUpdate', 0 );
    $submittedAfter = date( 'Y-m-d H:i:s', $lastUpdate );

    $datetime       = \DateTime::createFromFormat( "Y-m-d H:i:s", $submittedAfter );
    $submittedAfter = $datetime->format( \DateTime::RFC3339 );

    $params = [
        'count' => 50,
        'rating' => 5,
        'status' => 'APPROVED',
        'submittedAfter' => $submittedAfter
    ];

    $reviews = $tsAPI->request( '/reviews', 'GET', $params );

    if( ! property_exists( $reviews, 'items' ) ) {
        return;
    }

    if( count( $reviews->items ) == 0 ) {
        return;
    }

    foreach( $reviews->items AS $review ) {
        $args = array(
            'post_type'    => 'ts-rating',
            'numberposts'  => -1,
            'post_status'  => 'any',
            'meta_key'     => 'review_id',
            'meta_value'   => $review->id
        );
        
        $posts = get_posts( $args );

        if ( count( $posts ) > 0 ) {
            continue;
        }

        $date = date( 'Y-m-d H:i:s', strtotime( $review->createdAt ) );

        $post = [
            'post_date'    => $date,
            'post_title'   => $review->title,
            'post_content' => $review->comment,
            'post_type'    => 'ts-rating'
        ];

        $postId = wp_insert_post( $post );

        update_post_meta( $postId, 'rating', $review->rating );
        update_post_meta( $postId, 'review_id', $review->id );
        update_post_meta( $postId, 'review_date', $date );
    }

    update_option( 'btsLastUpdate', time() );
}

add_action( 'bts_update', 'AWSM\\BlockTrustedShops\\btsUpdateRatings' );

function btsActivationHook() {
    if ( ! wp_next_scheduled( 'bts_update' ) ) {
        wp_schedule_event( time(), 'hourly', 'bts_update' );
    }
}

register_activation_hook( __FILE__, 'AWSM\\BlockTrustedShops\\btsActivationHook' );