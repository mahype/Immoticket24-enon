<?php

namespace Enon\Config;

use Awsm\WP_Plugin\Building_Plans\Hooks_Actions;
use Awsm\WP_Plugin\Loaders\Hooks_Loader;
use Awsm\WP_Plugin\Loaders\Loader;

/**
 * Class Gutenberg Config.
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Gutenberg implements Hooks_Actions {
	use Loader, Hooks_Loader;

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public static function add_actions() {
		add_action( 'admin_head', array( __CLASS__, 'full_width' ) );
		add_action( 'after_setup_theme', array( __CLASS__, 'color_palette' ) );
	}

	/**
	 * Full width for Gutenberg Editor.
	 *
	 * @since 1.0.0
	 */
	public static function full_width() {
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
	public static function color_palette() {
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
