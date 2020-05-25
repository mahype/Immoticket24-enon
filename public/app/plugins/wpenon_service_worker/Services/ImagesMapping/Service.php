<?php
namespace WPENON\ServiceWorker\ImagesMapping;

use WPENON\ServiceWorker\Interfaces;
use WPENON\ServiceWorker;

class Service extends ServiceWorker\Services implements Interfaces\Action {

	private $is_runing = false;
	private $progress_start = false;
	private $serviceArgument = [];

	public function __construct() {
		$this::setServiceName('wpenon_image_cleanup');
		$this->serviceArgument = $this::getParseServiceArguments($_GET);

		$this->is_runing = !empty($this->serviceArgument['progress']) ?? False;
		$this->progress_start = !empty($this->serviceArgument['progress_start']) ?? False;

		if(!$this->progress_start && !$this->is_runing){
			return false;
		}

		add_action('init', [$this, 'initAction'], 2, 1);
	}

	public function initAction() {
		$chunk_size = 500;

		$item_count = (int) !empty($this->serviceArgument['items']) ? $this->serviceArgument['items'] : get_posts_count();
		$cunks = (int) !empty($this->serviceArgument['chunks']) ? $this->serviceArgument['chunks'] : get_cunk_size($item_count, $chunk_size);
		$total = (int) !empty($this->serviceArgument['total']) ? $this->serviceArgument['total'] : $cunks;

		#get_thumbnail_with_post();
		#set_marker_on_post_thumbnail($chunk_size);
		sleep(5);

		$next = $cunks - 1;

		if ($next === 0) {
			wp_die('wpenon_image_cleanup - fertig mit aufgeräumen');
		}

		$query_args = [
			$this::getServiceName() . '_progress' => TRUE,
			$this::getServiceName() . '_items'    => $item_count,
			$this::getServiceName() . '_total'    => $total,
			$this::getServiceName() . '_chunks'   => $next,
		];

		add_filter('x_redirect_by', function () {
			return $this::getServiceName();
		});

		$query    = build_query($query_args);
		$progress = round($next * 100 / $total);
		header($this::getServiceName() . "_progress: " . $progress);
		wp_redirect(home_url() . '?' . $query);
		exit();
	}

public function getName() {
	// TODO: Implement getName() method.
}}


function init() {

}

/**
 * @return mixed
 */
function get_posts_count() {
	global $wpdb;
	return $wpdb->get_results("SELECT count(*) FROM wpit24_posts WHERE wpit24_posts.post_type = 'download';", ARRAY_A)[0]['count(*)'];
}

/**
 * @param $item_count
 * @param $chunk_size
 *
 * @return false|float
 */
function get_cunk_size( $item_count, $chunk_size ) {

	return round($item_count / $chunk_size, 0);
}

/**
 * @param $chunk_size
 *
 * @return bool
 */
function set_marker_on_post_thumbnail( $chunk_size ) {

	$posts = get_posts([
		                   'post_type'   => 'download',
		                   'numberposts' => $chunk_size
	                   ]);

	foreach ($posts as $post) {
		$imageId = get_post_meta($post->ID, '_thumbnail_id', TRUE);
		update_post_meta($imageId, '_wp_attached_post', $post->ID);
	}

	return TRUE;
}

/**
 * @return bool
 */
function get_thumbnail_with_post() {
	$args = [
		'post_type'     => 'attachment',
		'meta_query' => [
			'key' => '_wp_attached_post',
			'compare' => 'EXISTS'
		]
	];

	$the_query = query_posts($args);

	return TRUE;
}
