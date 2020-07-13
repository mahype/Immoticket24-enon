<?php
namespace WPENON\ServiceWorker\GenerateSparkassenCSV;

use WPENON\ServiceWorker\Interfaces;
use WPENON\ServiceWorker;

class Service extends ServiceWorker\Services implements Interfaces\Action {

	/**
	 * @var array
	 */
	private $serviceArgument;

	public function __construct() {
		$this::setServiceName('wpenon_deactivate_ptimizepress');
		$this->serviceArgument = $this::getParseServiceArguments($_GET);

		add_action('init', [$this, 'initAction'], 2, 1);
	}

	public function initAction() {

		$args = [
			'post_type'     => ['page'],
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'meta_query' => [
				'key' => 'classic-editor-remember',
				'compare' => 'EXISTS'
			]
		];

		$the_query = query_posts($args);

		if($the_query){
			$result = [];

			foreach($the_query as $post){
				$is_gutenberg = strpos($post->post_content, '<!-- wp:');

				$post = [
					'id' => $post->ID,
					'title' => $post->post_title,
					'link' => get_permalink($post->ID)
				];

				if($is_gutenberg === false){
					array_push($result, $post);
				}
			}

			echo '<pre>';
			print_r(json_encode($result));
			echo '</pre>';
			die();
		}



		echo 'foo';
	}

}
