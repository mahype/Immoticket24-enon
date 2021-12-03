<?php
/**
 * This file contains navigation functionality.
 *
 * @package immoticketenergieausweis
 */

function immoticketenergieausweis_nav_menu( $theme_location, $mode = '', $echo = true )
{
  $menu_class = 'menu';
  if ( 'inline' == $mode || 'social' == $mode ) {
    $menu_class .= ' inline-menu';
  }

  $args = array(
    'theme_location'  => $theme_location,
    'container'       => false,
    'depth'           => 1,
    'menu_class'      => $menu_class,
    'echo'            => $echo,
  );

  if ( 'social' == $mode ) {
    $args['link_before'] = '<span class="sr-only">';
    $args['link_after'] = '</span>';
  }

  if( !empty( $mode ) )
  {
    $mode = '_' . ucfirst( $mode );
  }
  $walker_class = 'Immoticketenergieausweis' . $mode . '_Nav_Menu_Walker';
  if( !class_exists( $walker_class ) )
  {
    $walker_class = str_replace( $mode, '', $walker_class );
  }
  $args['walker'] = new $walker_class();
  
  return wp_nav_menu( $args );
}

class Immoticketenergieausweis_Nav_Menu_Walker extends Walker_Nav_Menu
{
  public function check_current( $classes )
  {
    return preg_match( '/(current[-_])|active|dropdown/', $classes );
  }

  public function start_lvl( &$output, $depth = 0, $args = array() )
  {
    $output .= "\n<ul class=\"dropdown-menu\">\n";
  }

  public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 )
  {
    $item_html = '';
    parent::start_el($item_html, $item, $depth, $args);
    
    if( $item->is_dropdown && ( $depth === 0 ) )
    {
      $item_html = str_replace( '<a', '<a class="dropdown-toggle" data-toggle="dropdown" data-target="#"', $item_html );
      $item_html = str_replace( '</a>', ' <span class="caret"></span></a>', $item_html );
    }
    elseif( stristr( $item_html, 'li class="divider' ) )
    {
      $item_html = preg_replace( '/<a[^>]*>.*?<\/a>/iU', '', $item_html );
    }
    elseif( stristr( $item_html, 'li class="nav-header' ) )
    {
      $item_html = preg_replace( '/<a[^>]*>(.*)<\/a>/iU', '$1', $item_html );
    }
    
    $output .= $item_html;
  }

  public function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output )
  {
    $element->is_dropdown = ( ( !empty( $children_elements[$element->ID] ) && ( ( $depth + 1 ) < $max_depth || ( $max_depth === 0 ) ) ) );
    
    if( $element->is_dropdown )
    {
      if( $depth === 0 )
      {
        $element->classes[] = 'dropdown';
      }
      elseif( $depth === 1 )
      {
        $element->classes[] = 'dropdown-submenu';
      }
    }
    
    parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
  }
}

class Immoticketenergieausweis_Button_Nav_Menu_Walker extends Immoticketenergieausweis_Nav_Menu_Walker
{
  public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 )
  {
    $item_html = '';
    parent::start_el($item_html, $item, $depth, $args);

    if( strpos( $item_html, '<a class="' ) !== false )
    {
      $item_html = str_replace( '<a class="', '<a class="btn btn-primary ', $item_html );
    }
    else
    {
      $item_html = str_replace( '<a', '<a class="btn btn-primary"', $item_html );
    }
    
    $output .= $item_html;
  }
}

class Immoticketenergieausweis_Inline_Nav_Menu_Walker extends Walker_Nav_Menu {
  public function start_lvl( &$output, $depth = 0, $args = array() ) {
    if ( $depth < 1 ) {
      parent::start_lvl( $output, $depth, $args );
    }
  }

  public function end_lvl( &$output, $depth = 0, $args = array() ) {
    if ( $depth < 1 ) {
      parent::end_lvl( $output, $depth, $args );
    }
  }

  public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
    if ( $depth < 1 ) {
      parent::start_el( $output, $item, $depth, $args, $id );
    }
  }

  public function end_el( &$output, $item, $depth = 0, $args = array() ) {
    if ( $depth < 1 ) {
      parent::end_el( $output, $item, $depth, $args );
    }
  }

  public function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
    if ( $depth < 1 ) {
      parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }
  }
}

function immoticketenergieausweis_nav_menu_css_class( $classes, $item )
{
  $slug = sanitize_title( $item->title );
  
  $classes = preg_replace( '/(current(-menu-|[-_]page[-_])(item|parent|ancestor))/', 'active', $classes );
  $classes = preg_replace( '/^((menu|page)[-_\w+]+)+/', '', $classes );
  
  $classes[] = 'menu-' . $slug;
  
  $classes = array_unique( $classes );
  
  return array_filter( $classes, 'immoticketenergieausweis_is_element_empty' );
}
add_filter( 'nav_menu_css_class', 'immoticketenergieausweis_nav_menu_css_class', 10, 2 );
