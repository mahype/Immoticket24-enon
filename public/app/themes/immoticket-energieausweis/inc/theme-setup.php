<?php
/**
 * This file sets up the theme.
 *
 * @package immoticketenergieausweis
 */

if( !isset( $content_width ) )
{
  $content_width = 640;
}

function immoticketenergieausweis_setup()
{
  load_theme_textdomain( 'immoticketenergieausweis', IMMOTICKETENERGIEAUSWEIS_THEME_PATH . '/languages' );

  add_theme_support( 'post-thumbnails' );
  add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

  add_editor_style();

  set_post_thumbnail_size( 900, 350 );

  register_nav_menus( array(
    'primary'       => __( 'Hauptmenü', 'immoticketenergieausweis' ),
    'secondary'     => __( 'Top-Navigation', 'immoticketenergieausweis' ),
    'social'        => __( 'Social Media-Menü', 'immoticketenergieausweis' ),
    'footerpages'   => __( 'Oberes Fußmenü', 'immoticketenergieausweis' ),
    'footer'        => __( 'Fußmenü', 'immoticketenergieausweis' ),
  ) );

  add_image_size( 'enon-object-image-preview', 303, 200, array( 'center', 'center' ) );
  add_image_size( 'enon-object-image-pdf', 485, 678, array( 'center', 'center' ) );


  add_image_size( 'it-header', 680, 300, array( 'right', 'center' ) );
  add_image_size( 'it-logo', 405, 135, true );
  add_image_size( 'it-logo-nav', 9999, 60, false );

  add_theme_support( 'custom-logo', array(
    'width'       => 405,
    'height'      => 135,
    'header-text' => array( 'site-title', 'site-description' ),
  ) );
}
add_action( 'after_setup_theme', 'immoticketenergieausweis_setup' );

function immoticketenergieausweis_widgets_init()
{
  register_sidebar( array(
    'name'          => __( 'Seitenleiste', 'immoticketenergieausweis' ),
    'id'            => 'primary',
    'description'   => __( 'Diese Seitenleiste wird rechts neben dem Inhalt (ausgenommen des Blogs) angezeigt.', 'immoticketenergieausweis' ),
    'before_widget' => '<aside id="%1$s" class="panel panel-primary widget %2$s">',
    'after_widget'  => '</div></aside>',
    'before_title'  => '<div class="panel-heading"><h3 class="widget-title">',
    'after_title'   => '</h3></div><div class="panel-body">',
  ) );

  register_sidebar( array(
    'name'          => __( 'Blog-Seitenleiste', 'immoticketenergieausweis' ),
    'id'            => 'blog',
    'description'   => __( 'Diese Seitenleiste wird rechts neben Inhalten des Blogs angezeigt.', 'immoticketenergieausweis' ),
    'before_widget' => '<aside id="%1$s" class="panel panel-primary widget %2$s">',
    'after_widget'  => '</div></aside>',
    'before_title'  => '<div class="panel-heading"><h3 class="widget-title">',
    'after_title'   => '</h3></div><div class="panel-body">',
  ) );

  register_widget( 'Immoticketenergieausweis_Banner_Widget' );
}
add_action( 'widgets_init', 'immoticketenergieausweis_widgets_init' );

function immoticketenergieausweis_get_wpenon_firmendaten_mappings() {
  $mappings = array(
    'firmenname'      => 'firmenname',
    'firmenlogo'      => 'MANUELL',
    'inhaber'         => 'MANUELL',
    'strassenr'       => 'strassenr',
    'plz'             => 'plz',
    'ort'             => 'ort',
    'telefon'         => 'telefon',
    'email'           => 'email',
    'automail'        => 'MANUELL',
    'kontoinhaber'    => 'kontoinhaber',
    'kontonummer'     => 'kontonummer',
    'bankleitzahl'    => 'bankleitzahl',
    'kreditinstitut'  => 'kreditinstitut',
    'iban'            => 'iban',
    'bic'             => 'bic',
    'amtsgericht'     => 'handelsregister',
    'steuernummer'    => 'steuernr',
    'ustid'           => 'ustidnr',
  );

  return $mappings;
}

function immoticketenergieausweis_enable_wpenon_options_field( $enable, $field_slug ) {
  if ( class_exists( '\WPENON\Util\Format' ) ) {
    $field_slug = \WPENON\Util\Format::unprefix( $field_slug );

    $mappings = immoticketenergieausweis_get_wpenon_firmendaten_mappings();

    if ( isset( $mappings[ $field_slug ] ) ) {
      return false;
    }
  }
  return $enable;
}
add_filter( 'wpenon_enable_field', 'immoticketenergieausweis_enable_wpenon_options_field', 20, 2 );

function immoticketenergieausweis_get_wpenon_option( $value, $key, $default ) {
  if ( class_exists( '\WPENON\Util\Format' ) ) {
    $key = \WPENON\Util\Format::unprefix( $key );

    $mappings = immoticketenergieausweis_get_wpenon_firmendaten_mappings();
    if ( isset( $mappings[ $key ] ) ) {
      if ( $mappings[ $key ] == 'MANUELL' ) {
        switch( $key ) {
          case 'automail':
            return 'christian@energieausweis-online-erstellen.de';
          case 'inhaber':
            $geschaeftsfuehrer = immoticketenergieausweis_get_option( 'it-business', 'geschaeftsfuehrer' );
            $inhaber = array();
            foreach( $geschaeftsfuehrer as $g ) {
              $inhaber[] = $g['name'];
            }
            return implode( ', ', $inhaber );
          case 'firmenlogo':
            $logo = immoticketenergieausweis_get_option( 'it-business', 'logo' );
            if ( $logo > 0 ) {
              $logo = wp_get_attachment_image_src( $logo, 'medium', false );
              if ( is_array( $logo ) ) {
                return $logo[0];
              }
            }
            return '';
          default:
        }
      } else {
        return immoticketenergieausweis_get_option( 'it-business', $mappings[ $key ] );
      }
    }
  }
  return $value;
}
add_filter( 'edd_get_option', 'immoticketenergieausweis_get_wpenon_option', 20, 3 );
