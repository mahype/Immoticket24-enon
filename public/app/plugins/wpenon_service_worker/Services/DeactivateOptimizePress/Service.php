<?php
namespace WPENON\ServiceWorker\DeactivateOptimizePress;

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
			'post_type'     => ['post', 'page'],
			'posts_per_page' => -1,
			'meta_query' => [
				'key' => 'classic-editor-remember',
				'compare' => 'EXISTS'
			]
		];

		$the_query = query_posts($args);

		if($the_query){
			$result = [
				'gutenberg' => [],
				'other' => [],
			];

			foreach($the_query as $post){
				$is_gutenberg = strpos($post->post_content, '<!-- wp:');

				$post = [
					'id' => $post->ID,
					'title' => $post->post_title,
					'urls' => [
						'edit' => admin_url('/post.php?post=' . $post->ID . '&action=edit'),
						'view' => $post->guid
					]
				];

				if($is_gutenberg !== false){
					array_push($result['gutenberg'], $post);
				}else{
					array_push($result['other'], $post);
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
