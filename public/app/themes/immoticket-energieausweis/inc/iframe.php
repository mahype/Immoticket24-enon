<?php
/**
 * This file contains Energieausweis-IFrame functionality.
 *
 * @package immoticketenergieausweis
 */

function immoticketenergieausweis_maybe_load_iframe_template( $template ) {
  if ( ! isset( $_REQUEST['iframe'] ) || 'true' !== $_REQUEST['iframe'] ) {
    return $template;
  }

  $privacy_page    = immoticketenergieausweis_get_option( 'it-theme', 'page_for_privacy' );
  $terms_page      = immoticketenergieausweis_get_option( 'it-theme', 'page_for_terms' );
  $withdrawal_page = immoticketenergieausweis_get_option( 'it-theme', 'page_for_withdrawal' );

  if ( is_page( array( $privacy_page, $terms_page, $withdrawal_page ) ) ) {
    remove_action( 'wp_footer', 'immoticketenergieausweis_userlike_script', 100 );
    remove_action( 'wp_footer', 'immoticketenergieausweis_ekomi_widget_script', 100 );
    remove_action( 'wp_footer', 'immoticketenergieausweis_google_remarketing_tag_script', 100 );
    remove_action( 'wp_footer', 'immoticketenergieausweis_bing_ads_uet_tag_script', 100 );
    remove_action( 'wp_footer', 'immoticketenergieausweis_trusted_shops_badge_script', 100 );
    
    return locate_template( array( 'energieausweis-iframe.php' ) );
  }

  if ( ! class_exists( 'WPENON\Controller\Frontend' ) ) {
    if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
      return $template;
    }

    if ( ! edd_is_checkout() && ! edd_is_success_page() && ! edd_is_failed_transaction_page() ) {
      return $template;
    }
  }

  $view = \WPENON\Controller\Frontend::instance()->getView();
  if ( ! $view ) {
    if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
      return $template;
    }

    if ( ! edd_is_checkout() && ! edd_is_success_page() && ! edd_is_failed_transaction_page() ) {
      return $template;
    }
  }

  if ( ! current_user_can( 'manage_options' ) || ! empty( $_REQUEST['iframe_token'] ) ) {
    if ( empty( $_REQUEST['iframe_token'] ) ) {
      wp_die( __( 'Fehlerhaftes IFrame-Token.', 'immoticketenergieausweis' ) );
    }

    $current_token = wp_unslash( $_REQUEST['iframe_token'] );

    $email = immoticketenergieausweis_get_email_by_iframe_token( $current_token );
    if ( ! $email ) {
      wp_die( __( 'Fehlerhaftes IFrame-Token.', 'immoticketenergieausweis' ) );
    }
  }

  add_action( 'wp_head', 'wp_no_robots' );
  remove_action( 'wp_footer', 'immoticketenergieausweis_userlike_script', 100 );
  remove_action( 'wp_footer', 'immoticketenergieausweis_ekomi_widget_script', 100 );
  remove_action( 'wp_footer', 'immoticketenergieausweis_google_remarketing_tag_script', 100 );
  remove_action( 'wp_footer', 'immoticketenergieausweis_bing_ads_uet_tag_script', 100 );
  remove_action( 'wp_footer', 'immoticketenergieausweis_trusted_shops_badge_script', 100 );

  return locate_template( array( 'energieausweis-iframe.php' ) );
}
add_filter( 'template_include', 'immoticketenergieausweis_maybe_load_iframe_template' );

function immoticketenergieausweis_maybe_filter_iframe_url( $url ) {
  $args = array();
  if ( ! empty( $_GET['iframe'] ) ) {
    $args['iframe'] = wp_unslash( $_GET['iframe'] );
  }
  if ( ! empty( $_GET['iframe_token'] ) ) {
    $args['iframe_token'] = wp_unslash( $_GET['iframe_token'] );
  }

  if ( empty( $args ) ) {
    return $url;
  }

  return add_query_arg( $args, $url );
}
add_filter( 'wpenon_filter_url', 'immoticketenergieausweis_maybe_filter_iframe_url', 100 );
add_filter( 'edd_get_checkout_uri', 'immoticketenergieausweis_maybe_filter_iframe_url', 100 );
add_filter( 'edd_get_success_page_uri', 'immoticketenergieausweis_maybe_filter_iframe_url', 100 );
add_filter( 'edd_get_failed_transaction_uri', 'immoticketenergieausweis_maybe_filter_iframe_url', 100 );
add_filter( 'edd_remove_fee_url', 'immoticketenergieausweis_maybe_filter_iframe_url', 100 );

function immoticketenergieausweis_get_email_by_iframe_token( $iframe_token ) {
  $tokens = immoticketenergieausweis_get_option( 'it-iframe', 'tokens' );
  if ( ! is_array( $tokens ) ) {
    return false;
  }

  $email = false;
  foreach ( $tokens as $token ) {
    if ( $iframe_token === $token['token'] && 'yes' === $token['active'] ) {
      $email = $token['email'];
      break;
    }
  }

  return $email;
}

function immoticketenergieausweis_get_affiliate_id_by_iframe_token( $iframe_token ) {
  $email = immoticketenergieausweis_get_email_by_iframe_token( $iframe_token );
  if ( ! $email ) {
    return false;
  }

  $user = get_user_by( 'email', $email );
  if ( ! $user ) {
    return false;
  }

  if ( ! function_exists( 'affwp_get_affiliate_id' ) ) {
    return false;
  }

  return affwp_get_affiliate_id( $user->ID );
}

function immoticketenergieausweis_adjust_fallback_track_referral() {
  if ( ! function_exists( 'affiliate_wp' ) ) {
    return;
  }

  if ( ! isset( $_REQUEST['iframe'] ) || 'true' !== $_REQUEST['iframe'] ) {
    return;
  }

  if ( empty( $_REQUEST['iframe_token'] ) ) {
    return;
  }

  $current_token = wp_unslash( $_REQUEST['iframe_token'] );

  $affiliate_id = immoticketenergieausweis_get_affiliate_id_by_iframe_token( $current_token );
  if ( ! $affiliate_id ) {
    return;
  }

  affiliate_wp()->tracking->referral = $affiliate_id;
}
add_action( 'template_redirect', 'immoticketenergieausweis_adjust_fallback_track_referral', -10000, 0 );

/*function immoticketenergieausweis_was_referred( $referred, $tracking ) {
  if ( $referred ) {
    return $referred;
  }

  if ( ! isset( $_REQUEST['iframe'] ) || 'true' !== $_REQUEST['iframe'] ) {
    return $referred;
  }

  if ( empty( $_REQUEST['iframe_token'] ) ) {
    return $referred;
  }

  $current_token = wp_unslash( $_REQUEST['iframe_token'] );

  $affiliate_id = immoticketenergieausweis_get_affiliate_id_by_iframe_token( $current_token );
  if ( ! $affiliate_id ) {
    return false;
  }

  if ( ! $tracking->is_valid_affiliate( $affiliate_id ) ) {
    return false;
  }

  return true;
}
add_filter( 'affwp_was_referred', 'immoticketenergieausweis_was_referred', 10, 2 );

function immoticketenergieausweis_get_affiliate_id_filter( $affiliate_id ) {
  if ( $affiliate_id ) {
    return $affiliate_id;
  }

  if ( ! isset( $_REQUEST['iframe'] ) || 'true' !== $_REQUEST['iframe'] ) {
    return $affiliate_id;
  }

  if ( empty( $_REQUEST['iframe_token'] ) ) {
    return $affiliate_id;
  }

  $current_token = wp_unslash( $_REQUEST['iframe_token'] );

  return immoticketenergieausweis_get_affiliate_id_by_iframe_token( $current_token );
}
add_filter( 'affwp_tracking_get_affiliate_id', 'immoticketenergieausweis_get_affiliate_id_filter', 10, 1 );*/
