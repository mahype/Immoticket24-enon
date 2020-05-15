<?php
/*
 * Plugin Name: wpenon Image Cleanup
 * Plugin URI:
 * Description: Aufräum Plugin für enon - macht dinge gut und sauber
 * Version: 1.0.2
 * License: Private
 *
 * @package WPENON
 * @version 1.0.2
 */

add_action('init', function (){
	$chunk_size = 500;

	$is_runing = !empty($_GET['wpenon_image_cleanup_progress']) ? true : false;

	$item_count = (int) !empty($_GET['wpenon_image_cleanup_items']) ? $_GET['wpenon_image_cleanup_items'] : get_posts_count();

	$cunks = (int) !empty($_GET['wpenon_image_cleanup_chunks']) ? $_GET['wpenon_image_cleanup_chunks'] : get_cunk_size($item_count, $chunk_size);

	$total = (int) !empty($_GET['wpenon_image_cleanup_total']) ? $_GET['wpenon_image_cleanup_total'] : $cunks;

	if($is_runing){
		set_marker_on_post_thumbnail($chunk_size);
		sleep(20);
	}

	$next = $cunks-1;

	if($next === 0){
		wp_die('wpenon_image_cleanup - fertig mit aufgeräumen');
	}

	$query_args = [
		'wpenon_image_cleanup_progress' => true,
		'wpenon_image_cleanup_items' => $item_count,
		'wpenon_image_cleanup_total' => $total,
		'wpenon_image_cleanup_chunks' => $next,
	];

	add_filter('x_redirect_by', function (){
		return 'wpenon_image_cleanup';
	});

	$query = build_query($query_args);
	$progress = round($next*100/$total);
	header( "wpenon-image-cleanup-progress: " . $progress);
	wp_redirect( home_url() . '?' . $query );
	exit();
});


function get_posts_count(){
	global $wpdb;
	return $wpdb->get_results("SELECT count(*) FROM wpit24_posts WHERE wpit24_posts.post_type = 'download';", ARRAY_A)[0]['count(*)'];
}

function get_cunk_size ($item_count, $chunk_size){
	return round($item_count / $chunk_size, 0);
}

function set_marker_on_post_thumbnail($chunk_size){
	$posts = get_posts([
		                   'post_type' => 'download',
		                   'numberposts' => $chunk_size
	                   ]);

	foreach ($posts as $post){
		$imageId = get_post_meta( $post->ID, '_thumbnail_id', true );
		update_post_meta($imageId, '_wp_attached_post', $post->ID);
	}

	return true;
}
