<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */
?>
<div class="access-box well well-sm">
  <p>
    <small>
      <?php _e( 'Sie können Ihren Energieausweis jederzeit auch zu einem späteren Zeitpunkt aufrufen, um ihn anzusehen oder die Arbeit daran fortzusetzen. Speichern Sie sich dazu einfach den hier angezeigten Link ab und rufen Sie ihn später wieder auf.', 'wpenon' ); ?>
      <br>
      <?php _e( 'Gehen Sie bitte mit dem Link vertraulich um, damit nur Sie darauf Zugriff haben!', 'wpenon' ); ?>
    </small>
  </p>
  <input type="text" class="form-control input-lg" value="<?php echo $data; ?>" onClick="this.select();" readonly>
</div>
