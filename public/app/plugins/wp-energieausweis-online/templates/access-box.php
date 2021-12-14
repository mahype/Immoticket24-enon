<?php
/**
 * @package WPENON
 * @version 1.0.0
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */
?>
<div class="access-box well well-sm">
  <p><?php _e( 'Sie können Ihren Energieausweis jederzeit auch zu einem späteren Zeitpunkt aufrufen, um ihn anzusehen oder die Arbeit daran fortzusetzen. Speichern Sie sich dazu einfach den hier angezeigten Link ab und rufen Sie ihn später wieder auf.', 'wpenon' ); ?></p>
  <input type="text" class="form-control input-lg" value="<?php echo $data; ?>" onClick="this.select();" readonly>
  <p style="margin-top:11px;">
    <small><strong><em>
      <?php _e( 'Gehen Sie bitte mit dem Link vertraulich um, damit nur Sie auf diesen Zugriff haben!', 'wpenon' ); ?>
    </em></strong></small>
  </p>
</div>
