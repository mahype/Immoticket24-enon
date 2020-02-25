<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package immoticketenergieausweis
 */

$data = immoticketenergieausweis_get_option( 'it-business' );

$html_attrs = apply_filters( 'immoticketenergieausweis_html_attrs', '' );

?><!DOCTYPE html>
<html <?php echo $html_attrs; ?> <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php immoticketenergieausweis_wp_title( '|' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>
  <div class="wrapper container">

    <div class="top-bar">
      <div class="row">
        <div class="advantages col-sm-9">
          <ul class="inline-menu">
            <li>
              <span class="advantage-1"><?php _e( 'Sofort verfügbar', 'immoticketenergieausweis' ); ?></span>
            </li>
            <li>
              <span class="advantage-2"><?php _e( 'Zufriedenheitsgarantie', 'immoticketenergieausweis' ); ?></span>
            </li>
            <li>
              <span class="advantage-3"><?php _e( 'Kundenhotline', 'immoticketenergieausweis' ); ?> - <a href="tel:<?php echo $data['telefon-maschinell']; ?>" itemprop="telephone"><?php echo $data['telefon']; ?></a></span>
            </li>
        </div>
        <div class="social-icons col-sm-3">
          <div class="social-navigation">
            <?php immoticketenergieausweis_nav_menu( 'social', 'social' ); ?>
          </div>
        </div>
      </div>
    </div>

    <header class="header" role="banner">

      <div class="row">
        <div class="branding col-md-5">
            <p class="site-title"><a href="<?php echo home_url( '/' ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
            <p class="site-description lead"><?php bloginfo( 'description' ); ?></p>
            <?php if ( function_exists( 'the_custom_logo' ) ) : ?>
              <?php the_custom_logo(); ?>
            <?php else : ?>
              <a href="<?php echo home_url( '/' ); ?>" class="custom-logo-link" rel="home" itemprop="url">
                <img class="custom-logo" itemprop="logo" src="<?php echo IMMOTICKETENERGIEAUSWEIS_THEME_URL; ?>/assets/media/logo.png">
              </a>
            <?php endif; ?>
        </div>
        <div class="trust col-md-7">
          <div class="badges">
	          <?php
	          /**
	           * Header badges.
	           *
	           * @since 1.0.0
	           */
	            do_action('enon_header_badges' );
	          ?>
          </div>
        </div>
      </div>

      <?php if ( ! immoticketenergieausweis_is_distraction_free() ) : ?>
        <?php if ( has_nav_menu( 'secondary' ) ) : ?>
        <div class="navigation-bar-wrapper">
          <div class="navigation-bar container">
            <div class="navigation-branding">
              <span class="site-title"><a href="<?php echo home_url( '/' ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span>
              <?php if ( function_exists( 'the_custom_logo' ) ) : ?>
                <?php the_custom_logo(); ?>
              <?php else : ?>
                <a href="<?php echo home_url( '/' ); ?>" class="custom-logo-link" rel="home" itemprop="url">
                  <img class="custom-logo" itemprop="logo" src="<?php echo IMMOTICKETENERGIEAUSWEIS_THEME_URL; ?>/assets/media/logo.png">
                </a>
              <?php endif; ?>
            </div>
            <div class="navigation-toggle">
              <button type="button" class="navigation-bar-toggle-button">
                <span class="icon-text"><?php _e( 'Navigation', 'immoticketenergieausweis' ); ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
            </div>
            <nav class="navigation-menu is-collapsed">
              <?php immoticketenergieausweis_nav_menu( 'secondary' ); ?>
            </nav>
            <div class="navigation-trust">
              <a href="https://www.trustedshops.de/bewertung/info_X509FCF5891E8A90932F2A46F02AD28DE.html" target="_blank">
                <img src="<?php echo IMMOTICKETENERGIEAUSWEIS_THEME_URL; ?>/assets/media/trusted-shops-badge.png">
              </a>
              <a href="https://www.service-tested.de/bewertungen/immoticket24-de-gmbh-www-energieausweis-online-erstellen-de/kundenurteil-gesamtbewertung-ekomi/" target="_blank">
                <img src="/app/plugins/enon/src/Assets/Img/Badges/tuev-saarland-logo.png" />
              </a>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <?php immoticketenergieausweis_header_image(); ?>
        <?php
        $business_name = immoticketenergieausweis_get_option( 'it-business', 'firmenname' );
        $business_logo = immoticketenergieausweis_get_option( 'it-business', 'logo' );
        $business_url = immoticketenergieausweis_get_option( 'it-business', 'website' );
        if( $business_logo > 0 )
        {
          $business_logo = wp_get_attachment_image_src( $business_logo, 'it-logo-nav', false );
          if( is_array( $business_logo ) )
          {
            $business_logo = $business_logo[0];
          }
        }
        ?>
        <?php if ( false /*function_exists( 'edd_is_success_page' ) && edd_is_success_page()*/ ) : ?>
        <div class="main-navigation">
          <div style="margin: 0 auto;padding-top:40px;padding-bottom:40px;max-width:750px;">
            <h2 style="margin-top:0;margin-bottom:20px;">In 3 Schritten zum Maklervergleich</h2>
            <div class="row" style="margin-bottom:20px;">
              <div class="col-md-3">
                <img src="<?php echo IMMOTICKETENERGIEAUSWEIS_THEME_URL; ?>/assets/media/homeday-icon-1.png" style="display:block;">
              </div>
              <div class="col-md-9">
                <p><strong>Um welche Art der Immobilie geht es?</strong></p>
                <p>Sie geben die Daten Ihrer Immobilie in unser Formular oben ein, um den Maklervergleich zu starten.</p>
              </div>
            </div>
            <div class="row" style="margin-bottom:20px;">
              <div class="col-md-3">
                <img src="<?php echo IMMOTICKETENERGIEAUSWEIS_THEME_URL; ?>/assets/media/homeday-icon-2.png" style="display:block;">
              </div>
              <div class="col-md-9">
                <p><strong>Wir suchen den passenden Makler</strong></p>
                <p>Anhand der Daten von führenden Immobilien-Portalen ermitteln wir die Makler, die vergleichbare Objekte in Ihrer Nähe am besten vermarktet haben.</p>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <img src="<?php echo IMMOTICKETENERGIEAUSWEIS_THEME_URL; ?>/assets/media/homeday-icon-3.png" style="display:block;">
              </div>
              <div class="col-md-9">
                <p><strong>Sie wählen einen Makler aus</strong></p>
                <p>Wir besprechen Ihre Präferenzen bei der Maklerwahl. Anschließend erhalten Sie bis zu drei Maklerempfehlungen per E-Mail, abgestimmt auf Ihre Immobilie.</p>
              </div>
            </div>
          </div>
        </div>
        <?php elseif ( has_nav_menu( 'primary' ) ) : ?>
        <div class="main-navigation">
          <div class="row">
            <nav class="navigation-content col-md-12" role="navigation">
              <?php immoticketenergieausweis_nav_menu( 'primary', 'button' ); ?>
            </nav>
          </div>
        </div>
        <?php endif; ?>
      <?php endif; ?>

    </header>

    <div class="content">
