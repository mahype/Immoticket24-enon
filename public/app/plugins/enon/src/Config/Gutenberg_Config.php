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
class Gutenberg_Config implements Hooks_Actions {
	use Loader, Hooks_Loader;

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public static function add_actions() {
		add_action( 'admin_head', array( __CLASS__, 'full_width' ) );
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
}
