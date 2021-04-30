<?php
/**
 * The footer for our theme.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package immoticketenergieausweis
 */
?>
    </div><!-- #content -->

    <?php if ( ! immoticketenergieausweis_is_distraction_free() ) : ?>
      <div class="pre-footer">
        <div class="row">
          <div class="contact-form col-md-4">
            <?php immoticketenergieausweis_display_contact_form( immoticketenergieausweis_get_option( 'it-theme', 'footer-contactform' ) ); ?>
          </div>
          <div class="map col-md-4">
            <?php
            immoticketenergieausweis_display_bezahlmethoden();
            //immoticketenergieausweis_display_map( immoticketenergieausweis_get_option( 'it-business' ) );
            ?>
          </div>
          <div class="business-data col-md-4">
            <?php immoticketenergieausweis_display_business_data( immoticketenergieausweis_get_option( 'it-business' ) ); ?>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <footer class="footer" role="contentinfo">
      <?php if ( ! immoticketenergieausweis_is_distraction_free() && has_nav_menu( 'footerpages' ) ) : ?>
        <div class="text-center">
          <?php immoticketenergieausweis_nav_menu( 'footerpages', 'inline' ); ?>
        </div>
      <?php endif; ?>
      <?php if ( has_nav_menu( 'footer' ) ) : ?>
        <div class="text-center">
          <?php immoticketenergieausweis_nav_menu( 'footer', 'inline' ); ?>
        </div>
      <?php endif; ?>
      <div class="row">
        <div class="copyright col-md-6">
          <p>&copy; <?php echo immoticketenergieausweis_get_option( 'it-business', 'founded' ) . '-' . date( 'Y' ); ?> <?php printf( __( 'Ein Service von %s', 'immoticketenergieausweis' ), '<a href="' . esc_url( immoticketenergieausweis_get_option( 'it-business', 'website' ) ) . '" title="' . esc_attr( immoticketenergieausweis_get_option( 'it-business', 'firmenname' ) ) . '" target="_blank">' . esc_html( immoticketenergieausweis_get_option( 'it-business', 'firmenname' ) ) . '</a>' ); ?></p>
        </div>
        <div class="credit col-md-6">
          <p><?php printf( __( 'Entwickelt von %s', 'immoticketenergieausweis' ), '<a href="https://felix-arntz.me" target="_blank">leaves-and-love.net</a>' ); ?></p>
        </div>
      </div>
    </footer>

  </div><!-- .wrapper -->

<?php wp_footer(); ?>

<?php echo date('Y-m-d', time() ); ?>

</body>
</html>
