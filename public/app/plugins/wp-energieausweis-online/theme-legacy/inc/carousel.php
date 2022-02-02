<?php
/**
 * This file contains the carousel functions.
 *
 * @package immoticketenergieausweis
 */

function immoticketenergieausweis_header_carousel( $id )
{
  $items = immoticketenergieausweis_get_option( 'it-slider', 'data' );

  if( count( $items ) > 0 )
  {
  ?>
    <div id="<?php echo $id; ?>" class="carousel slide" data-ride="carousel">
      <ol class="carousel-indicators">
      <?php foreach( $items as $key => $item ) : ?>
        <li data-target="#<?php echo $id; ?>" data-slide-to="<?php echo $key; ?>"<?php echo $key == 0 ? ' class="active"' : ''; ?>></li>
      <?php endforeach; ?>
      </ol>
      <div class="carousel-inner" role="listbox">
      <?php foreach( $items as $key => $item ) : ?>
        <div id="item-<?php echo $key; ?>" class="item<?php echo $key == 0 ? ' active' : ''; ?>">
          <?php if( !empty( $item['image'] ) ) : ?>
          <div class="carousel-image" style="background-image: url('<?php echo wp_get_attachment_image_src( $item['image'], 'it-header' )[0]; ?>');">
          </div>
          <?php endif; ?>
          <?php if( !empty( $item['caption'] ) ) : ?>
          <div class="carousel-content">
            <p class="lead"><?php echo $item['caption']; ?></p>
            <?php if( !empty( $item['url'] ) ) : ?>
            <p><?php echo '<a class="btn btn-default btn-lg" href="' . $item['url'] . '"' . ( $item['new_tab'] ? ' target="_blank"' : '' ) . '>' . $item['anchor'] . '</a>'; ?></p>
            <?php endif; ?>
            </p>
          </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
      </div>
    </div>
  <?php
  }
}

function immoticketenergieausweis_header_homeday( $id ) {
  ?>
  <div id="<?php echo $id; ?>" class="homeday-header">
    <div class="homeday-iframe-wrap">
      <iframe src="https://app.homeday.de/properties/new/?utm_medium=Partner&amp;utm_source=immoticket24&amp;landing_page=energieausweis" class="do-not-wrap" width="100%" height="100%" border="0" frameborder="0"></iframe>
    </div>
    <div class="homeday-badges">
      <img class="homeday-badge" src="<?php echo IMMOTICKETENERGIEAUSWEIS_THEME_URL; ?>/assets/media/homeday-tuev-saarland.jpg" alt="Homeday: ausgezeichnet durch TÜV Saarland">
      <img class="homeday-badge" src="<?php echo IMMOTICKETENERGIEAUSWEIS_THEME_URL; ?>/assets/media/homeday-trustpilot.jpg" alt="Homeday: ausgezeichnet durch Trustpilot">
      <img class="homeday-badge" src="<?php echo IMMOTICKETENERGIEAUSWEIS_THEME_URL; ?>/assets/media/homeday-maklervermittlungsportale.jpg" alt="Homeday: ausgezeichnet durch Maklervermittlungsportale">
    </div>
  </div>
  <?php
}
