<?php

namespace Enon\Config\Tasks;

use Awsm\WP_Plugin\Building_Plans\Hooks_Actions;
use Awsm\WP_Plugin\Building_Plans\Service;
use Awsm\WP_Plugin\Loaders\Hooks_Loader;
use Awsm\WP_Plugin\Loaders\Loader;
use Awsm\WPWrapper\BuildingPlans\Actions;
use Awsm\WPWrapper\BuildingPlans\Task;

/**
 * Class Gutenberg Config.
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class TaskGutenberg implements Actions, Task {
	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->addActions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function addActions() {
		add_action( 'admin_head', array( __CLASS__, 'fullWidth' ) );
		add_action( 'after_setup_theme', array( __CLASS__, 'colorPalette' ) );
	}

	/**
	 * Full width for Gutenberg Editor.
	 *
	 * @since 1.0.0
	 */
	public static function fullWidth() {
		echo '<style>
			body.gutenberg-editor-page .editor-post-title__block, body.gutenberg-editor-page .editor-default-block-appender, body.gutenberg-editor-page .editor-block-list__block {
						max-width: 80% !important;
				}
			.block-editor__container .wp-block {
				max-width: 80% !important;
			}
		  </style>';
	}

	/**
	 * Defining own colors in color palette.
	 *
	 * @since 1.0.0
	 */
	public static function colorPalette() {
		add_theme_support(
			'editor-color-palette',
			array(
				array(
					'name'  => esc_html__( 'Black', 'enon' ),
					'slug' => 'black',
					'color' => '#000000',
				),
				array(
					'name'  => esc_html__( 'White', 'enon' ),
					'slug' => 'white',
					'color' => '#FFFFFF',
				),
				array(
					'name'  => esc_html__( 'Green', 'enon' ),
					'slug' => 'green',
					'color' => '#00af30',
				),
			)
		);

	}
}
