<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- The name of the tile is common among others.
/**
 * Select2 Utilities
 *
 * @package     AffiliateWP
 * @subpackage  Data
 * @copyright   Copyright (c) 2020, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.12.0
 * @author      Aubrey Portwood <aubrey@awesomeomotive.com>
 */

namespace AffiliateWP\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( trait_exists( '\AffiliateWP\Utils\Select2' ) ) {
	return;
}

require_once __DIR__ . '/trait-data.php';

/**
 * Select2 Utilities
 *
 * @since 2.12.0
 *
 * @see Affiliate_WP_Data
 */
trait Select2 {

	use \AffiliateWP\Utils\Data;

	/**
	 * Load scripts and styles.
	 *
	 * Try and run this on the `wp_enqueue_scripts` or
	 * `admin_enqueue_scripts` hook.
	 *
	 * This will automatically enqueue `assets/js/select2-init.js`
	 * for you and pass the chosen selector to the JS instance.
	 *
	 * @since  2.12.0
	 *
	 * @param string $selector       The jQuery Selector to target.
	 * @param array  $args           The arguments to pass to `.select2()`.
	 * @param string $label_selector The jQuery selector for the label, if one.
	 *
	 * @throws \InvalidArgumentException If you do not supply proper parameters.
	 */
	private function enqueue_select2( $selector, $args = array(), $label_selector = '' ) {

		if ( ! $this->is_string_and_nonempty( $selector ) ) {
			throw new \InvalidArgumentException( '$selector must be a non-empty string.' );
		}

		if ( ! is_array( $args ) ) {
			throw new \InvalidArgumentException( '$args must be an array.' );
		}

		if ( ! is_string( $label_selector ) ) {
			throw new \InvalidArgumentException( '$label_selector must be a string.' );
		}

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Enqueue jQuery .select2().
		wp_enqueue_style( 'affwp-select2' );
		wp_enqueue_script( 'affwp-select2' );

		// Load the script.
		wp_enqueue_script(
			'affwp-select2-init',
			AFFILIATEWP_PLUGIN_URL . "assets/js/select2-init{$suffix}.js",
			array( 'jquery', 'affwp-select2' ),
			defined( 'AFFILIATEWP_VERSION' ) ? AFFILIATEWP_VERSION : time(),
			true
		);

		// Pass the selector and arguments.
		wp_localize_script(
			'affwp-select2-init',
			'affwpSelect2',
			array(
				'args'          => $args,
				'selector'      => $selector,
				'labelSelector' => $label_selector,
			)
		);
	}
}
