<?php
/**
 * Setting up gutenberg editor.
 *
 * @category Class
 * @package  Enon\Config\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\WP\Tasks;

use Awsm\WP_Wrapper\Interfaces\Hooks_Actions;
use Awsm\WP_Wrapper\Interfaces\Service;
use Awsm\WP_Wrapper\Loaders\Hooks_Loader;
use Awsm\WP_Wrapper\Loaders\Loader;
use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class Setup_Gutenberg.
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Setup_Gutenberg implements Actions, Task {
	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->add_actions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
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
					'slug'  => 'black',
					'color' => '#000000',
				),
				array(
					'name'  => esc_html__( 'White', 'enon' ),
					'slug'  => 'white',
					'color' => '#FFFFFF',
				),
				array(
					'name'  => esc_html__( 'Green', 'enon' ),
					'slug'  => 'green',
					'color' => '#00af30',
				),
			)
		);
	}
}
