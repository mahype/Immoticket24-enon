<?php

/**
 * Block Patterns
 */

function pattern_replace_urls( $pattern_content ) {
	$content_urls = array(
		'https://wp.test/wp-content',
		'https://enon.test/wp-content',
		'https://2021.energieausweis-online-erstellen.de/app',
		'https://staging.energieausweis-online-erstellen.de/app',
		'https://energieausweis.de/app'
	);

	foreach( $content_urls AS $content_url )
	{
		$pattern_content = str_replace( $content_url, content_url(), $pattern_content );
	}

	return $pattern_content;
}

function google_site_verification() {
		echo '<meta name="google-site-verification" content="ZdRDoVTYbiT47J7E5uD09uB5jq7g98tQsQ_7gTKwym8" />';
}
add_action('wp_head', 'google_site_verification');

function jason_load_patterns() {	
	require( dirname( __FILE__ ) . '/patterns/content/3-guetesiegel.php' );
	require( dirname( __FILE__ ) . '/patterns/content/5-gruende.php' );
	require( dirname( __FILE__ ) . '/patterns/content/6-gruende.php' );
	require( dirname( __FILE__ ) . '/patterns/content/7-gruende.php' );
	require( dirname( __FILE__ ) . '/patterns/content/angaben-energieausweis.php' );
	require( dirname( __FILE__ ) . '/patterns/content/angaben-bedarfsausweis.php' );
	require( dirname( __FILE__ ) . '/patterns/content/angaben-verbrauchsausweis.php' );
	require( dirname( __FILE__ ) . '/patterns/content/das-ist-ein-energieausweis.php' );
	require( dirname( __FILE__ ) . '/patterns/content/das-ist-ein-bedarfsausweis.php' );	
	require( dirname( __FILE__ ) . '/patterns/content/das-ist-ein-verbrauchsausweis.php' );
	require( dirname( __FILE__ ) . '/patterns/content/preise-klein.php' );
	require( dirname( __FILE__ ) . '/patterns/content/preise-gross.php' );
	require( dirname( __FILE__ ) . '/patterns/content/banner.php' );
	require( dirname( __FILE__ ) . '/patterns/content/checklisten.php' );
	require( dirname( __FILE__ ) . '/patterns/content/das-sagen-kunden.php' );
	require( dirname( __FILE__ ) . '/patterns/content/tabelle-welcher-energieausweis.php' );

	register_block_pattern_category( 'content', [
		'label' => _x('Inhalt', 'textdomain'),
	]);
}
add_action('init', 'jason_load_patterns');

/**
 * Block Styles
 */
function jason_block_styles() {
	register_block_style(
		'core/button',
		array(
			'name'         => 'button-white',
			'label'        => __( 'Weiss', 'textdomain' )
		)
	);

	register_block_style(
		'core/button',
		array(
			'name'         => 'button-red',
			'label'        => __( 'Rot', 'textdomain' )
		)
	);

	register_block_style(
		'core/list',
		array(
			'name'         => 'checkmarks-neutral',
			'label'        => __( 'Haken (neutral)', 'textdomain' )
		)
	);

	register_block_style(
		'core/column',
		array(
			'name'         => 'border-left-white',
			'label'        => __( 'Weißer Rand links', 'textdomain' )
		)
	);

	register_block_style(
		'core/list',
		array(
			'name'         => 'checkmarks-formatted',
			'label'        => __( 'Haken (formatiert)', 'textdomain' )
		)
	);

	register_block_style(
		'core/group',
		array(
			'name'         => 'group-main',
			'label'        => __( 'Hauptgruppe', 'textdomain' )
		)
	);

	register_block_style(
		'core/group',
		array(
			'name'         => 'group-card',
			'label'        => __( 'Karte', 'textdomain' )
		)
	);

	register_block_style(
		'core/group',
		array(
			'name'         => 'label-green',
			'label'        => __( 'Grünes Label', 'textdomain' )
		)
	);

	register_block_style(
		'core/cover',
		array(
			'name'         => 'cover-main',
			'label'        => __( 'Hauptgruppe', 'textdomain' )
		)
	);

	register_block_style(
		'core/quote',
		array(
			'name'         => 'blockqote-border-left',
			'label'        => __( 'Rahmen links', 'textdomain' )
		)
	);
}

add_action( 'init', 'jason_block_styles' );

/**
 * Frontend Scripts
 */
function jason_frontend_scripts() {	
	wp_enqueue_style( 'jason-css', get_template_directory_uri() . '/assets/css/frontend.min.css' );
	wp_enqueue_script( 'jason-js', get_template_directory_uri() . '/assets/js/frontend.min.js' );

	/**
	 * Legacy form
	 */
	wp_enqueue_style( 'jason-legacy-css', get_template_directory_uri() . '/assets/css/_legacy.min.css' );
	wp_enqueue_script( 'jason-legacy-js', get_template_directory_uri() . '/assets/js/_legacy.min.js' );
}
add_action( 'wp_enqueue_scripts', 'jason_frontend_scripts' );


/**
 * Editor Scripts
 */
function jason_editor_scripts() {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.min.css' );
}
add_action( 'admin_init', 'jason_editor_scripts' );

/**
 * WordPress Settings
 */
function allow_svg_upload( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'allow_svg_upload' );

/**
 * Block editor settings
 */
function jason_setup_theme() {
	remove_theme_support( 'core-block-patterns' );
}
add_action( 'after_setup_theme', 'jason_setup_theme' );

function filter_metadata_registration( $metadata ) {
	if ( $metadata['name'] !== 'core/group' ) {
		return $metadata;
	}

    $metadata['supports']['spacing']['margin'] = [ "top", "bottom" ];

    return $metadata;
};
add_filter( 'block_type_metadata', 'filter_metadata_registration' );

/**
 * Only search blog posts
 */
function jason_search($query) {
	if(is_admin()) {
		return $query;
	}
	
    if ($query->is_search) {
        $query->set('post_type', 'post');
    }
    return $query;
}

add_filter('pre_get_posts','jason_search');
