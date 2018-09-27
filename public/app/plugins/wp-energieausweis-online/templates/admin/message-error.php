<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

if ( count( $data ) > 0 ) : ?>
  <div class="error notice is-dismissible">
    <p>
      <?php _e( 'In den folgenden Feldern wurden ungültige Eingaben festgestellt:', 'wpenon' ); ?>
    </p>
    <ul>
      <?php foreach ( $data as $field_slug => $error ) : ?>
        <li>
          <?php echo $error['label']; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif;
