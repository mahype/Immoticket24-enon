<?php
/**
 * Plugin Name:       Block - Wave Separator
 * Description:       Create wave separators with the wave separator block.
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Sven Wagener
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       block-wave-separator
 *
 * @package           create-block
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/writing-your-first-block-type/
 */
function create_block_block_wave_separator_block_init() {
	register_block_type( __DIR__ );
}
add_action( 'init', 'create_block_block_wave_separator_block_init' );

function waves_svg() { 
	echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 0 0" height="0" width="0"><clipPath id="wave-1" clipPathUnits="objectBoundingBox" transform="scale(0.00352733686067 0.058823529411765)"><path d="M0,0c11,3,31.4,6.9,55,8.7c38.5,2.9,40.7-2,78.3-0.6c59.5,2.2,91.3,9,125.3,8.7c22.7-0.2,24.9-2.5,24.9-2.5l0,2.8H0L0,0z"/></clipPath></svg>';
	echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 0 0" height="0" width="0"><clipPath id="wave-2" clipPathUnits="objectBoundingBox" transform="scale(0.00352733686067 0.058823529411765)"><path d="M0,5.8c9.8,5,17.3,13.6,56.2,8.8c41.7-5.1,83.2-18.5,178.2-6.7c34.8,4.3,49.1-7.9,49.1-7.9l0,17H0V5.8z"/></clipPath></svg>';
	echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 0 0" height="0" width="0"><clipPath id="wave-3" clipPathUnits="objectBoundingBox" transform="scale(0.00352733686067 0.058823529411765)"><path d="M0,0c9.8,5,36.3,13.7,55.5,13.5c35.4-0.3,59-9.5,91.9-9.7c32-0.1,64,9.1,92.1,9.1c28.1,0,43.9-13,43.9-13l0,17H0V0z"/></clipPath></svg>';
}
add_action( 'wp_footer', 'waves_svg' );