<?php

function immoticketenergieausweis_maybe_dequeue_stupid_optimizepress_crap() {
  if ( is_admin() ) {
    return;
  }

  $is_optimizepress = true;

  if ( is_singular() ) {
    $post = get_queried_object();
    if ( $post ) {
      if ( 'download' === $post->post_type ) {
        $is_optimizepress = false;
      } elseif ( class_exists( 'WPENON\Util\Settings' ) ) {
        $settings = \WPENON\Util\Settings::instance();

        $types = array( 'bw', 'vw', 'bn', 'vn' );
        foreach( $types as $slug => $title )
        {
          $setting_name = 'new_' . $slug . '_page';
          if( $post->ID === absint( $settings->$setting_name ) )
          {
            $is_optimizepress = false;
            break;
          }
        }
      }
    }
  }

  if ( ! $is_optimizepress ) {
    if ( class_exists( 'Oppp' ) ) {
      $opplus = Oppp::getInstance();

      remove_action( 'wp_enqueue_scripts', array( $opplus, 'enqueueAllScripts' ) );
      remove_action( 'wp_footer', array( $opplus, 'enqueueFrontendScripts' ) );
      remove_action( 'wp_print_styles', array( $opplus, 'enqueueAllStyles' ) );
    }
  }
}
add_action( 'template_redirect', 'immoticketenergieausweis_maybe_dequeue_stupid_optimizepress_crap' );

function immoticketenergieausweis_optimizepress_template_override( $template ) {
  add_filter( 'immoticketenergieausweis_html_attrs', 'immoticketenergieausweis_optimizepress_html_attrs' );
  add_action( 'wp_footer', 'immoticketenergieausweis_optimizepress_footer_before', -9999 );
  add_action( 'wp_footer', 'immoticketenergieausweis_optimizepress_footer_after', 9999 );
  add_filter( 'immoticketenergieausweis_stylesheet_dependencies', 'immoticketenergieausweis_optimizepress_add_dependencies' );
  add_filter( 'immoticketenergieausweis_inline_style', 'immoticketenergieausweis_optimizepress_inline_style' );
  add_filter( 'option_' . OP_SN . '_op_external_theme_css', '__return_zero' );
  add_filter( 'option_' . OP_SN . '_op_le_external_theme_css', '__return_zero' );
  add_filter( 'option_' . OP_SN . '_op_external_theme_js', '__return_zero' );
  add_filter( 'option_' . OP_SN . '_op_le_external_theme_js', '__return_zero' );

  return IMMOTICKETENERGIEAUSWEIS_THEME_PATH . '/optimizepress.php';
}
add_filter( 'op_check_page_availability', 'immoticketenergieausweis_optimizepress_template_override' );

function immoticketenergieausweis_optimizepress_html_attrs( $attrs ) {
  if ( defined( 'OP_LIVEEDITOR' ) ) {
    $attrs .= ' class="op-live-editor"';
  }
  return $attrs;
}

function immoticketenergieausweis_optimizepress_footer_before() {
  do_action( 'op_footer' );
  op_enqueue_frontend_scripts();

  if (!is_admin()) {
      wp_enqueue_script('op-menus', OP_JS.'menus'.OP_SCRIPT_DEBUG.'.js', array(OP_SCRIPT_BASE), OP_VERSION);
  }

  //Print out footer scripts
  op_print_footer_scripts('front');
}

function immoticketenergieausweis_optimizepress_footer_after() {
  //Return (which will not allow user in), if the user does not have permissions
  if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;
  if (!get_user_option('rich_editing')) return;

  //If we are previewing, run the following script
  $preview = (!empty($_GET['preview']) ? $_GET['preview'] : false);
  echo ($preview ? '
      <script type="text/javascript">
          (function ($) {
              $(\'#TB_window\', window.parent.document).css({marginLeft: \'-\' + parseInt((1050 / 2),10) + \'px\',width:\'1050px\',height:\'600px\'});
              $(\'#TB_iframeContent\', window.parent.document).css({width:\'1050px\',height:\'600px\'});
          }(opjq));
      </script>
  ' : '');
}

function immoticketenergieausweis_optimizepress_add_dependencies( $dependencies ) {
  return array_merge( $dependencies, array(
    'optimizepress-page-style',
    'optimizepress-default',
  ) );
}

function immoticketenergieausweis_optimizepress_inline_style( $style ) {
  $style .= ' .navigation-bar.container {
  overflow: visible;
}
.content .container {
  width: auto !important;
  padding-right: 0 !important;
  padding-left: 0 !important;
}
.row,
li,
.main-navigation {
  margin-bottom: 0 !important;
}
.content {
  margin-right: -15px;
  margin-left: -15px;
}
@media (min-width: 992px) {
  .content {
    margin-right: -30px;
    margin-left: -30px;
  }
}
.btn-primary,
.btn-primary:hover,
.btn-primary:focus {
  color: #ffffff !important;
}';

  return $style;
}
