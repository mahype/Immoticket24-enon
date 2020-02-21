<?php
/**
 * This file contains important frontend functionality.
 *
 * @package immoticketenergieausweis
 */

function immoticketenergieausweis_enqueue_scripts()
{
  $dependencies = apply_filters( 'immoticketenergieausweis_stylesheet_dependencies', array() );

  $inline_style = apply_filters( 'immoticketenergieausweis_inline_style', '.site-title, .site-description { position: absolute; clip: rect(1px, 1px, 1px, 1px); }' );

  wp_enqueue_style( 'immoticketenergieausweis', IMMOTICKETENERGIEAUSWEIS_THEME_URL . '/assets/dist/immoticketenergieausweis.min.css', $dependencies, IMMOTICKETENERGIEAUSWEIS_THEME_VERSION );
  wp_add_inline_style( 'immoticketenergieausweis', $inline_style );

  if( file_exists( IMMOTICKETENERGIEAUSWEIS_THEME_PATH . '/assets/dev/fancybox/source/jquery.fancybox.pack.js' ) )
  {
    wp_enqueue_style( 'jquery-fancybox', IMMOTICKETENERGIEAUSWEIS_THEME_URL . '/assets/dev/fancybox/source/jquery.fancybox.css', array(), '2.1.5' );
    wp_enqueue_script( 'jquery-fancybox', IMMOTICKETENERGIEAUSWEIS_THEME_URL . '/assets/dev/fancybox/source/jquery.fancybox.pack.js', array( 'jquery' ), '2.1.5', true );
  }

  wp_enqueue_script( 'jquery-are-you-sure', IMMOTICKETENERGIEAUSWEIS_THEME_URL . '/assets/dev/jquery/dist/jquery.are-you-sure.js', array( 'jquery' ), '1.9.0', true );
  wp_enqueue_script( 'immoticketenergieausweis', IMMOTICKETENERGIEAUSWEIS_THEME_URL . '/assets/dist/immoticketenergieausweis.min.js', array( 'jquery' ), IMMOTICKETENERGIEAUSWEIS_THEME_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'immoticketenergieausweis_enqueue_scripts' );

function immoticketenergieausweis_is_distraction_free() {
  if ( ! is_singular() ) {
    return false;
  }

  if ( function_exists( 'edd_is_checkout' ) && edd_is_checkout() ) {
    return true;
  }

  if ( class_exists( 'WPENON\Model\EnergieausweisManager' ) ) {
    if ( \WPENON\Model\EnergieausweisManager::instance()->getCreatePage() ) {
      return true;
    }
  }

  $post = get_post();
  if ( 'download' === $post->post_type ) {
    return true;
  }

  $df_ids = array( 29879, 58047, 70682 );
  if ( in_array( (int) $post->ID, $df_ids, true ) ) {
    return true;
  }

  return false;
}

function immoticketenergieausweis_after_content( $content ) {
  global $wp_embed, $shortcode_tags;

  if ( ! is_singular() ) {
    return $content;
  }

  if ( 'post' !== get_post_type() ) {
    return $content;
  }

  if ( shortcode_exists( 'shariff' ) ) {
    $content .= '<p style="margin-top:11px;margin-bottom:0;">' . __( 'Wenn Ihnen der Artikel gefällt, würden wir uns über ein Teilen des Artikels mit Ihren Freunden freuen. Damit helfen Sie uns, unsere Website bekannter zu machen!', 'immoticketenergieausweis' ) . '</p>';
    $content .= do_shortcode( '[shariff]' );
  }

  $post_id = get_the_ID();

  $post_thumbnail_source = get_post_meta( $post_id, 'immoticketenergieausweis_post_thumbnail_source', true );
  if ( ! empty( $post_thumbnail_source ) ) {
    $content .= '<p class="post-thumbnail-source">' . __( 'Titelbild:', 'immoticketenergieausweis' ) . ' ' . esc_html( $post_thumbnail_source ) . '</p>';
  }

  $content .= '<hr>';

  $ad_heading = get_post_meta( $post_id, 'immoticketenergieausweis_ad_heading', true );
  if ( ! empty( $ad_heading ) ) {
    $content .= '<h3>' . esc_html( $ad_heading ) . '</h3>';
  }

  $ad_content = get_post_meta( $post_id, 'immoticketenergieausweis_ad_content', true );
  if ( ! empty( $ad_content ) ) {
    $orig_shortcode_tags = $shortcode_tags;
    $shortcode_tags = array();
    add_shortcode( 'button', 'immoticketenergieausweis_button_shortcode' );

    $content .= wpautop( do_shortcode( $wp_embed->autoembed( $ad_content ) ) );

    $shortcode_tags = $orig_shortcode_tags;
  }

  if ( ! empty( $ad_heading ) || ! empty( $ad_content ) ) {
    $content .= '<hr>';
  }

  if ( false === strpos( $content, '[ratings]' ) ) {
    $content .= '<p>' . __( 'War der Seiteninhalt hilfreich? Dann bewerten Sie doch einfach den Artikel auf dieser Website:', 'immoticketenergieausweis' ) . '</p>' . '[ratings]';
  }

  if ( false === strpos( $content, '[trusted_shops_badge]' ) ) {
    $content .= '<h3>' . __( 'Das sagen Kunden:', 'immoticketenergieausweis' ) . '</h3>' . '[trusted_shops_badge]';
  }

  return $content;
}
add_filter( 'the_content', 'immoticketenergieausweis_after_content', 4, 1 );

function immoticketenergieausweis_button_shortcode( $atts, $content ) {
  $href = ! empty( $atts['href'] ) ? $atts['href'] : home_url( '/' );

  return '<a class="btn btn-primary" href="' . esc_url( $href ) . '">' . esc_html( $content ) . '</a>';
}

function immoticketenergieausweis_header_image() {
  $image   = immoticketenergieausweis_get_option( 'it-theme', 'header_image' );
  $caption = immoticketenergieausweis_get_option( 'it-theme', 'header_caption' );
  $url     = immoticketenergieausweis_get_option( 'it-theme', 'header_url' );
  $anchor  = immoticketenergieausweis_get_option( 'it-theme', 'header_anchor' );
  $new_tab = immoticketenergieausweis_get_option( 'it-theme', 'header_new_tab' );

  ?>
  <div class="it-header-image">
    <?php if ( ! empty( $image ) ) : ?>
      <div class="it-header-image-src" style="background-image: url('<?php echo wp_get_attachment_image_url( $image, 'it-header' ); ?>');"></div>
    <?php endif; ?>
    <?php if ( ! empty( $caption ) ) : ?>
      <div class="it-header-image-content">
        <p class="lead"><?php echo esc_html( $caption ); ?></p>
        <?php if( ! empty( $url ) ) : ?>
          <p>
            <a class="btn btn-default btn-lg" href="<?php echo esc_url( $url ); ?>"<?php echo $new_tab ? ' target="_blank"' : ''; ?>>
              <?php echo esc_html( $anchor ); ?>
            </a>
          </p>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
  <?php
}

function immoticketenergieausweis_uptain_script() {
	/**
	 * Set true uptain scripts have to be shown.
	 *
	 * @param bool True if it have to be shown.
	 *
	 * @since 1.0.0
	 */
	$show_uptain_scripts = apply_filters( 'wpenon_show_uptain_scripts', true );

	if (! $show_uptain_scripts ) {
		return;
	}

  $email = apply_filters( 'immoticketenergieausweis_uptain_email', '' );

  if ( ! empty( $email ) ) {
    $email = ' data-email="' . esc_attr( $email ) . '"';
  }

  ?>
  <script id="__up_data_qp" src="https://app.uptain.de/js/uptain.js?x=hnne9BonaNH0mRA0"<?php echo $email; ?>></script>
  <?php
}
add_action( 'wp_footer', 'immoticketenergieausweis_uptain_script', 100 );

function immoticketenergieausweis_trusted_shops_badge_script() {
  ?>
  <script type="text/javascript">
    (function () {
      var _tsid = 'X509FCF5891E8A90932F2A46F02AD28DE';
      _tsConfig = {
        'yOffset': '0', /* offset from page bottom */
        'variant': 'custom_reviews', /* text, default, small, reviews, custom, custom_reviews */
        'customElementId': 'trusted-shops-badge', /* required for variants custom and custom_reviews */
        'customCheckoutElementId': 'trusted-shops-warranty',
        'trustcardDirection': 'bottomLeft', /* for custom variants: topRight, topLeft, bottomRight, bottomLeft */
        'customBadgeWidth': '', /* for custom variants: 40 - 90 (in pixels) */
        'customBadgeHeight': '70', /* for custom variants: 40 - 90 (in pixels) */
        'disableResponsive': 'false', /* deactivate responsive behaviour */
        'disableTrustbadge': 'false', /* deactivate trustbadge */
        'trustCardTrigger': 'mouseenter' /* set to 'click' if you want the trustcard to be opened on click instead */
      };
      var _ts = document.createElement('script');
      _ts.type = 'text/javascript';
      _ts.charset = 'utf-8';
      _ts.async = true;
      _ts.src = '//widgets.trustedshops.com/js/' + _tsid + '.js';
      var __ts = document.getElementsByTagName('script')[0];
      __ts.parentNode.insertBefore(_ts, __ts);
    })();
  </script>
  <?php
}
// add_action( 'wp_footer', 'immoticketenergieausweis_trusted_shops_badge_script', 100 );

function immoticketenergieausweis_trusted_badge_shortcode( $atts ) {
  $atts = shortcode_atts( array(
    'variant'   => 'skyscraper_horizontal',
    'theme'     => 'light',
    'border'    => '#aabbcc',
  ), $atts, 'trusted_shops_badge' );

  ob_start();
  ?>
  <script type="text/javascript">
    _tsRatingConfig = {
      tsid: 'X509FCF5891E8A90932F2A46F02AD28DE',
      variant: '<?php echo $atts['variant']; ?>',
      // valid values: 'skyscraper_vertical', 'skyscraper_horizontal', vertical
      theme: '<?php echo $atts['theme']; ?>',
      reviews: 10,
      // default = 10
      borderColor: '<?php echo $atts['border']; ?>',
      // optional - override the border
      colorclassName: 'test',
      // optional - override the whole sticker style with your own css class
      introtext: 'What our customers say about us:',
      // optional, not used in skyscraper variants
      richSnippets: 'off'
    };
    var scripts = document.getElementsByTagName('SCRIPT'),
    me = scripts[scripts.length - 1];
    var _ts = document.createElement('SCRIPT');
    _ts.type = 'text/javascript';
    _ts.async = true;
    _ts.charset = 'utf-8';
    _ts.src ='//widgets.trustedshops.com/reviews/tsSticker/tsSticker.js';
    me.parentNode.insertBefore(_ts, me);
    _tsRatingConfig.script = _ts;
  </script>
  <?php

  return ob_get_clean();
}
add_shortcode( 'trusted_shops_badge', 'immoticketenergieausweis_trusted_badge_shortcode' );

function immoticketenergieausweis_trusted_checkout_shortcode( $atts ) {
  $session = edd_get_purchase_session();
  if ( isset( $_GET['payment_key'] ) ) {
    $payment_key = urldecode( $_GET['payment_key'] );
  } elseif ( $session ) {
    $payment_key = $session['purchase_key'];
  }

  if ( ! isset( $payment_key ) || ! $payment_key ) {
    return '';
  }

  $payment_id = edd_get_purchase_id_by_key( $payment_key );

  return immoticketenergieausweis_send_order_to_trustedshops( $payment_id );
}
add_shortcode( 'trusted_shops_checkout', 'immoticketenergieausweis_trusted_checkout_shortcode' );

function immoticketenergieausweis_adcell_tracking_script() {
	/**
	 * Set if adcell scripts have to be shown.
	 *
	 * @param bool True if it have to be shown.
	 *
	 * @since 1.0.0
	 */
	$show_tag_manager = apply_filters( 'wpenon_show_adcell_scripts', true );

	if ( ! $show_tag_manager ) {
		return;
	}

  $session = edd_get_purchase_session();
  if ( isset( $_GET['payment_key'] ) ) {
    $payment_key = urldecode( $_GET['payment_key'] );
  } elseif ( $session ) {
    $payment_key = $session['purchase_key'];
  }

  if ( ! isset( $payment_key ) || ! $payment_key ) {
    return '';
  }

  $payment_id = edd_get_purchase_id_by_key( $payment_key );

  $payment = get_post( $payment_id );

  if ( empty( $payment ) ) {
    return '';
  }

  // edd_get_payment_subtotal() doesn't include discounts.
  $subtotal = edd_get_payment_amount( $payment->ID ) - edd_get_payment_tax( $payment->ID );

  $args = array(
    'eventid'  => '5585',
    'pid'      => '4408',
    'referenz' => get_the_title( $payment->ID ),
    'betrag'   => number_format( (float) $subtotal, 2 ),
  );

  $js_url  = add_query_arg( $args, '//www.adcell.de/js/track.js' );
  $php_url = add_query_arg( $args, '//www.adcell.de/event.php' );

  $output = '
<script type="text/javascript" src="' . $js_url . '"></script>
<noscript>
  <img src="' . $php_url . '" border="0" width="1" height="1">
</noscript>
';

  return $output;
}
add_shortcode( 'adcell_tracking_script', 'immoticketenergieausweis_adcell_tracking_script' );

function immoticketenergieausweis_adcell_retargeting_script() {
	/**
	 * Set if adcell scripts have to be shown.
	 *
	 * @param bool True if it have to be shown.
	 *
	 * @since 1.0.0
	 */
	$show_tag_manager = apply_filters( 'wpenon_show_adcell_scripts', true );

	if ( ! $show_tag_manager ) {
		return;
	}

  $url = 'https://www.adcell.de/js/inlineretarget.js';
  $args = array(
    'pid' => '4408',
  );

  echo '<script type="text/javascript" src="https://t.adcell.com/js/trad.js"></script><script>Adcell.Tracking.track();</script>';

  if ( ! class_exists( 'WPENON\Model\EnergieausweisManager' ) ) {
    return;
  }

  $types    = WPENON\Model\EnergieausweisManager::getAvailableTypes();
  $type_ids = array_map( function( $id ) { return $id + 1; }, array_flip( array_keys( $types ) ) );

  if ( is_front_page() || ( is_page() && get_the_ID() === 29066 ) ) {
    $args['method'] = 'track';
    $args['type']   = 'startpage';
  } elseif ( is_singular() ) {
    if ( function_exists( 'edd_is_checkout' ) && edd_is_checkout() ) {
      $product_id_quantities = array();
      $cart_items            = edd_get_cart_contents();
      foreach ( $cart_items as $cart_item ) {
        $energieausweis = WPENON\Model\EnergieausweisManager::getEnergieausweis( $cart_item['id'] );
        if ( $energieausweis ) {
          $type = $energieausweis->wpenon_type;
          if ( ! empty( $type ) ) {
            $type_id = $type_ids[ $type ];
            if ( ! isset( $product_id_quantities[ $type_id ] ) ) {
              $product_id_quantities[ $type_id ] = 1;
            } else {
              $product_id_quantities[ $type_id ]++;
            }
          }
        }
      }
      if ( ! empty( $product_id_quantities ) ) {
        $args['method']             = 'basket';
        $args['productIds']         = implode( ';', array_keys( $product_id_quantities ) );
        $args['quantities']         = implode( ';', array_values( $product_id_quantities ) );
        $args['basketProductCount'] = count( $cart_items );
        $args['basketTotal']        = edd_get_cart_subtotal();
        $args['productSeparator']   = ';';
      }
    } elseif ( function_exists( 'edd_is_success_page' ) && edd_is_success_page() ) {
      $session     = edd_get_purchase_session();
      $payment_key = '';
      if ( isset( $_GET['payment_key'] ) ) {
        $payment_key = urldecode( $_GET['payment_key'] );
      } elseif ( $session ) {
        $payment_key = $session['purchase_key'];
      }

      if ( ! empty( $payment_key ) ) {
        $payment_id = edd_get_purchase_id_by_key( $payment_key );
        if ( get_post( $payment_id ) ) {
          $product_id_quantities = array();
          $payment_items         = edd_get_payment_meta_cart_details( $payment_id );
          foreach ( $payment_items as $payment_item ) {
            $energieausweis = WPENON\Model\EnergieausweisManager::getEnergieausweis( $payment_item['id'] );
            if ( $energieausweis ) {
              $type = $energieausweis->wpenon_type;
              if ( ! empty( $type ) ) {
                $type_id = $type_ids[ $type ];
                if ( ! isset( $product_id_quantities[ $type_id ] ) ) {
                  $product_id_quantities[ $type_id ] = 1;
                } else {
                  $product_id_quantities[ $type_id ]++;
                }
              }
            }
          }
          if ( ! empty( $product_id_quantities ) ) {
            $args['method']             = 'checkout';
            $args['productIds']         = implode( ';', array_keys( $product_id_quantities ) );
            $args['quantities']         = implode( ';', array_values( $product_id_quantities ) );
            $args['basketId']           = $payment_id;
            $args['basketProductCount'] = count( $payment_items );
            $args['basketTotal']        = edd_get_payment_subtotal( $payment_id );
            $args['productSeparator']   = ';';
          }
        }
      }
    } else {
      $type        = '';
      $manager     = WPENON\Model\EnergieausweisManager::instance();
      $create_page = $manager->getCreatePage();
      if ( $create_page ) {
        $type = $create_page;
      } else {
        $energieausweis = WPENON\Model\EnergieausweisManager::getEnergieausweis();
        if ( $energieausweis ) {
          $type = $energieausweis->wpenon_type;
        }
      }

      if ( ! empty( $type ) ) {
        $args['method']           = 'product';
        $args['productId']        = $type_ids[ $type ];
        $args['productName']      = $types[ $type ];
        $args['categoryId']       = '1';
        $args['productIds']       = implode( ';', array_diff( $type_ids, array( $type_ids[ $type ] ) ) );
        $args['productSeparator'] = ';';
      }
    }
  }

  if ( ! empty( $args['method'] ) ) {
    echo '<script type="text/javascript" src="' . add_query_arg( $args, $url ) . '"></script>';
  }
}
add_action( 'wp_footer', 'immoticketenergieausweis_adcell_retargeting_script' );

function immoticketenergieausweis_energy_box_shortcode( $atts, $content = null ) {
  if ( ! $content ) {
    return '';
  }

  $pos = strpos( $content, '<ul>' );
  if ( false === $pos ) {
    return '';
  }

  $content = '<ul class="list-group">' . substr( $content, $pos + 4 );

  $pos = strpos( $content, '</ul>' );
  if ( false === $pos ) {
    return '';
  }

  $content = substr( $content, 0, $pos + 5 );

  $content = str_replace( '<li>', '<li class="list-group-item">', $content );

  $atts = shortcode_atts( array(
    'typ'         => 'verbrauch',
    'titel'       => '',
    'preis'       => '',
    'url'         => '',
  ), $atts, 'energieausweis_box' );

  if ( empty( $atts['titel'] ) ) {
    if ( 'bedarf' === $atts['typ'] ) {
      $atts['titel'] = __( 'Bedarfsausweis', 'immoticketenergieausweis' );
    } else {
      $atts['titel'] = __( 'Verbrauchsausweis', 'immoticketenergieausweis' );
    }
  }

  if ( empty( $atts['preis'] ) ) {
    if ( function_exists( 'wpenon_get_option' ) && function_exists( 'edd_format_amount' ) ) {
      if ( 'bedarf' === $atts['typ'] ) {
		$bw_download_price = apply_filters( 'wpenon_price_bw', wpenon_get_option( 'bw_download_price' ) );
        $atts['preis'] = edd_format_amount( $bw_download_price );
      } else {
		$vw_download_price = apply_filters( 'wpenon_price_vw', wpenon_get_option( 'vw_download_price' ) );
        $atts['preis'] = edd_format_amount( $vw_download_price );
      }
      //$atts['preis'] = sprintf( __( 'ab %s', 'immoticketenergieausweis' ), $atts['preis'] );
    }
  }

  if ( empty( $atts['url'] ) ) {
    if ( function_exists( 'wpenon_get_option' ) ) {
      if ( 'bedarf' === $atts['typ'] ) {
        $atts['url'] = get_permalink( wpenon_get_option( 'new_bw_page' ) );
      } else {
        $atts['url'] = get_permalink( wpenon_get_option( 'new_vw_page' ) );
      }
    }
  }

  $image = IMMOTICKETENERGIEAUSWEIS_THEME_URL . '/assets/media/';
  if ( 'bedarf' === $atts['typ'] ) {
    $image .= 'bedarfsausweis-online-erstellen.jpg';
  } else {
    $image .= 'verbrauchsausweis-online-erstellen.jpg';
  }

  $output = '<div class="energieausweis-box panel panel-primary">';
  $output .= '<div class="panel-heading">';
  $output .= '<h3 class="text-center"><a href="' . esc_url( $atts['url'] ) . '">' . esc_html( $atts['titel'] ) . '</a></h3>';
  $output .= '</div>';
  $output .= '<div class="panel-body">';
  $output .= '<a href="' . esc_url( $atts['url'] ) . '">';
  $output .= '<img src="' . esc_url( $image ) . '">';
  $output .= '</a>';
  $output .= '</div>';
  $output .= $content;
  if ( $atts['preis'] ) {
    $output .= '<div class="panel-footer">';
    $output .= '<p class="lead text-center">' . esc_html( $atts['preis'] ) . '</p>';
    $output .= '</div>';
  }
  $output .= '</div>';

  return $output;
}
add_shortcode( 'energieausweis_box', 'immoticketenergieausweis_energy_box_shortcode' );

function immoticketenergieausweis_wp_title( $sep )
{
  if( defined( 'WPSEO_VERSION' ) )
  {
    wp_title( '' );
  }
  else
  {
    wp_title( $sep, true, 'right' );
    if( !is_feed() )
    {
      global $page, $paged;

      bloginfo( 'name', 'display' );

      $site_description = get_bloginfo( 'description', 'display' );
      if( $site_description && ( is_home() || is_front_page() ) )
      {
        echo ' ' . $sep . ' ' . $site_description;
      }

      if( ( $paged >= 2 || $page >= 2 ) && ! is_404() )
      {
        echo ' ' . $sep . ' ' . sprintf( __( 'Page %s', 'immoticketenergieausweis' ), max( $paged, $page ) );
      }
    }
  }
}

function immoticketenergieausweis_business_data_shortcode( $atts = array() )
{
  extract( shortcode_atts( array(
    'field'       => '',
  ), $atts, 'it-daten' ) );

  $data = immoticketenergieausweis_get_option( 'it-business', $field );

  $output = '';
  if( $data )
  {
    if( $field == 'geschaeftsfuehrer' )
    {
      $first = true;
      foreach( $data as $person )
      {
        if( !empty( $person['name'] ) )
        {
          if( !$first )
          {
            $output .= ', ';
          }
          else
          {
            $first = false;
          }
          $output .= $person['name'];
        }
      }
    }
    elseif( is_string( $data ) )
    {
      $output .= $data;
    }
  }
  return $output;
}
add_shortcode( 'it-daten', 'immoticketenergieausweis_business_data_shortcode' );

function immoticketenergieausweis_display_contact_form( $id )
{
  $id = absint( $id );
  $contact_form = wpcf7_contact_form( $id );
  echo '<h3>' . __( 'Kontakt', 'immoticketenergieausweis' ) . '</h3>';
  echo $contact_form->form_html( array( 'id' => $id ) );
}

function immoticketenergieausweis_display_bezahlmethoden() {
  $output = '<h3>' . __( 'Wir akzeptieren', 'immoticketenergieausweis' ) . '</h3>';
  $output .= '<img src="' . IMMOTICKETENERGIEAUSWEIS_THEME_URL . '/assets/media/akzeptierte-bezahlmethoden.png">';

  if ( class_exists( 'LazyLoad_Images' ) ) {
    $output = LazyLoad_Images::add_image_placeholders( $output );
  }

  echo $output;
}

function immoticketenergieausweis_display_map( $data )
{
  if( !empty( $data['strassenr'] ) && !empty( $data['plz'] ) && !empty( $data['ort'] ) )
  {
    if( !empty( $data['firmenname'] ) )
    {
      $firmenname = $data['firmenname'];
    }
    else
    {
      $firmenname = get_bloginfo( 'name' );
    }
    echo '<h3>' . __( 'Karte', 'immoticketenergieausweis' ) . '</h3>';
    $protocol = 'http';
    if ( strpos( home_url(), 'https' ) === 0 ) {
      $protocol = 'https';
    }
    echo '<iframe width="400" height="400" src="' . $protocol . '://maps.google.de/maps?hl=de&q=' . esc_attr( $data['strassenr'] ) . ', ' . esc_attr( $data['plz'] ) . ' ' . esc_attr( $data['ort'] ) . '+(' . esc_attr( $firmenname ) . ')&ie=UTF8&t=&z=15&iwloc=B&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>';
  }
}

function immoticketenergieausweis_display_business_data( $data )
{
  echo '<div itemprop="sourceOrganization" itemscope itemtype="http://schema.org/RealEstateAgent">';
  if( !empty( $data['firmenname'] ) )
  {
    $firmenname = $data['firmenname'];
  }
  else
  {
    $firmenname = get_bloginfo( 'name' );
  }
  echo '<h3 itemprop="name">' . $firmenname . '</h3>';
  /*if( !empty( $data['logo'] ) )
  {
    echo wp_get_attachment_image( $data['logo'], 'medium', false, array(
      'alt'         => sprintf( __( 'Logo von %s', 'immoticketenergieausweis' ), $firmenname ),
      'itemprop'    => 'logo'
    ) );
  }*/
  if( !empty( $data['firmenname'] ) )
  {
    echo '<meta itemprop="legalName" content="' . $data['firmenname'] . '">';
  }
  if( !empty( $data['founded'] ) )
  {
    echo '<time itemprop="foundingDate" datetime="' . $data['founded'] . '-01-01"></time>';
  }
  if( !empty( $data['geschaeftsfuehrer'] ) )
  {
    echo '<p>';
    echo __( 'Geschäftsführer', 'immoticketenergieausweis' ) . ':<br>';
    $first = true;
    foreach( $data['geschaeftsfuehrer'] as $person )
    {
      if( !empty( $person['name'] ) )
      {
        if( !$first )
        {
          echo ', ';
        }
        else
        {
          $first = false;
        }
        echo '<span itemprop="founder" itemscope itemtype="http://schema.org/Person">';
        echo '<span itemprop="name">';
        echo $person['name'];
        echo '</span>';
        echo '</span>';
      }
    }
    echo '</p>';
  }
  if( !empty( $data['strassenr'] ) )
  {
    echo '<p itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';
    echo '<span itemprop="streetAddress">' . $data['strassenr'] . '</span><br>';
    if( !empty( $data['plz'] ) )
    {
      echo '<span itemprop="postalCode">' . $data['plz'] . '</span> ';
    }
    if( !empty( $data['ort'] ) )
    {
      echo '<span itemprop="addressLocality">' . $data['ort'] . '</span>';
    }
    echo '</p>';
  }
  if( !empty( $data['telefon'] ) && !empty( $data['telefon-maschinell'] ) )
  {
    echo '<p>' . __( 'Telefon', 'immoticketenergieausweis' ) . ': <a href="tel:' . $data['telefon-maschinell'] . '" itemprop="telephone">' . $data['telefon'] . '</a></p>';
  }
  if( !empty( $data['email'] ) )
  {
    echo '<p>' . __( 'Email', 'immoticketenergieausweis' ) . ': <a href="mailto:' . $data['email'] . '">' . str_replace( '@', '(at)', $data['email'] ) . '</a></p>';
  }
  if( !empty( $data['website'] ) )
  {
    echo '<p>' . __( 'Website', 'immoticketenergieausweis' ) . ': <a href="' . $data['website'] . '" itemprop="url">' . str_replace( 'http://', '', str_replace( 'https://', '', $data['website'] ) ) . '</a></p>';
  }
  if( !empty( $data['oeffnungszeiten'] ) && !empty( $data['oeffnungszeiten-maschinell'] ) )
  {
    echo '<h4>' . __( 'Öffnungszeiten', 'immoticketenergieausweis' ) . '</h4>';
    echo '<p><time itemprop="openingHours" datetime="' . $data['oeffnungszeiten-maschinell'] . '">' . $data['oeffnungszeiten'] . '</time></p>';
  }
  echo '</div>';
}

function wpenon_alert_leave( $data ) { ?>
	<script language="JavaScript">
		jQuery(document).ready(function($) {
			$('#wpenon-generate-form').areYouSure({'message': 'Wir bitten Sie zu bestätigen, dass Sie die Seite verlassen möchten.'});
		});
	</script>
<?php
}

add_action('wpenon_form_end', 'wpenon_alert_leave' );


require_once IMMOTICKETENERGIEAUSWEIS_THEME_PATH . '/inc/navigation.php';
require_once IMMOTICKETENERGIEAUSWEIS_THEME_PATH . '/inc/carousel.php';
