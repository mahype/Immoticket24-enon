<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

if ( count( $data ) > 0 ) : ?>
  <div class="notice-warning notice is-dismissible">
    <p>
      <?php _e( 'Bitte überprüfen Sie Ihre Eingaben in den folgenden Feldern auf Korrektheit:', 'wpenon' ); ?>
    </p>
    <ul>
      <?php foreach ( $data as $field_slug => $warning ) : ?>
        <li>
          <?php echo $warning['label']; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif;
