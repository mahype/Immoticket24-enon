<?php
/**
 * @package WPENON
 * @version 1.0.0
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

use Enon\Enon\Standards_Config;

$prefix = '';
if ( function_exists( 'edd_get_download_price' ) ) {
  $price = edd_get_download_price( get_the_ID() );
  if ( $price ) {
    $prefix = edd_currency_filter( edd_format_amount( $price ) ) . '&nbsp;&ndash;&nbsp;';
  }
}

$standard = new Standards_Config();
$oldStandard = $standard->isStandardOlderThenDate( $data['meta']['standard_unformatted'], '2021-07-08' );

if( $oldStandard )
{
  $image = isset( $data['thumbnail']['id'] ) &&  $data['thumbnail']['id'] > 0 ? wpenon_get_image_url( $data['thumbnail']['id'], 'enon-energieausweiss-image' ) : ''; 
} else {
  $image = isset( $data['meta']['gebauedefoto'] ) ? $data['meta']['gebauedefoto']: ''; 
}

$showImage = $oldStandard || ! empty ( $image ) ? true : false;

?>

<script> var object_email = '<?php echo $data['meta']['email']; ?>';</script>

<div class="row">
  <div class="<?php if ( $showImage ): ?>col-sm-8<?php else: ?>col-sm-12<?php endif; ?>">
    <div class="overview-meta well">
      <h4>
        <?php _e( 'Energieausweis-Basisdaten', 'wpenon' ); ?>
      </h4>
      <p>
        <small>
          <?php _e( 'Dies sind allgemeine Daten zu Ihrem Energieausweis. Diese Informationen sind, einmal festgelegt, nicht mehr veränderlich. Beachten Sie, dass einige Daten erst nach Bezahlung festgelegt werden.', 'wpenon' ); ?>
        </small>
      </p>
      <?php
      $table_data = array(
        'fields'    => array(
          array(
            'key'       => 'type',
            'headline'  => __( 'Typ', 'wpenon' ),
          ),
          array(
            'key'       => 'registriernummer',
            'headline'  => __( 'Registriernummer', 'wpenon' ),
          ),
          array(
            'key'       => 'ausstellungsdatum',
            'headline'  => __( 'Ausstellungsdatum', 'wpenon' ),
          ),
          array(
            'key'       => 'adresse',
            'headline'  => __( 'Adresse des Gebäudes', 'wpenon' ),
          ),
        ),
        'data'      => array(
          array(
            'type'              => $data['meta']['type'],
            'registriernummer'  => $data['meta']['registriernummer'] ? $data['meta']['registriernummer'] : __( 'wird nach Bezahlung festgelegt', 'wpenon' ),
            'ausstellungsdatum' => $data['meta']['ausstellungsdatum'] ? $data['meta']['ausstellungsdatum'] : __( 'wird nach Bezahlung festgelegt', 'wpenon' ),
            'adresse'           => $data['meta']['adresse_strassenr'] . '<br>' . $data['meta']['adresse_plz'] . ' ' . $data['meta']['adresse_ort'] . '<br>' . $data['meta']['adresse_bundesland'],
          ),
        ),
      );
      wpenon_get_view()->displaySubTemplate( 'responsive-data', '', $table_data );
      ?>
    </div>
  </div>

  <?php if ( $showImage ): ?>
  <div class="col-sm-4">
    <div class="overview-thumbnail">      
      <h4>
        <?php _e( 'Bild des Gebäudes', 'wpenon' ); ?>
      </h4>
      <p>
        <small>
          <?php _e( 'Wenn Sie ein Bild hochladen, wird dieses im Energieausweis angezeigt.', 'wpenon' ); ?>
        </small>
      </p>
     
      <div class="thumbnail-wrapper">
        <?php if ( ! empty ( $image ) ) : ?>
	        <img src="<?php echo $image; ?>">
        <?php else : ?>
          <span class="glyphicon glyphicon-picture"></span>
        <?php endif; ?>
      </div>

      <?php if ( $oldStandard ): ?>
      <?php if ( $data['thumbnail']['error'] ) : ?>
        <div class="alert alert-danger">
          <p>
            <?php echo $data['thumbnail']['error']; ?>
          </p>
        </div>
      <?php endif; ?>
      
      <form id="wpenon-thumbnail-form" role="form" action="<?php echo $data['action_url']; ?>" method="post" enctype="multipart/form-data" novalidate>
        <p>
          <input type="file" name="<?php echo $data['thumbnail']['file_field_name']; ?>">
          <input type="hidden" name="<?php echo $data['thumbnail']['nonce_field_name']; ?>" value="<?php echo $data['thumbnail']['nonce_field_value']; ?>">
        </p>
        <p class="image-buttons">
          <button type="submit" name="<?php echo $data['thumbnail']['upload_button_name']; ?>" class="btn btn-primary"><?php _e( 'Bild hochladen', 'wpenon' ); ?></button>
          <?php if ( $data['thumbnail']['id'] > 0 ) : ?>
            <button type="submit" name="<?php echo $data['thumbnail']['delete_button_name']; ?>" class="btn btn-danger btn-xs"><?php _e( 'Bild löschen', 'wpenon' ); ?></button>
          <?php endif; ?>
        </p>
      </form>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php wpenon_get_view()->displaySubTemplate( 'access-box', '', $data['access_link'] ); ?>

<div class="action-buttons row">
  <div class="col-sm-4">
    <?php if ( $data['allow_changes_after_order'] && ( ! $data['ordered'] || $data['paid'] ) || ! $data['allow_changes_after_order'] && ! $data['ordered'] ) : ?>
      <a class="btn btn-primary btn-lg btn-block" href="<?php echo $data['edit_url']; ?>">
        <?php
        _e( 'Bearbeiten', 'wpenon' );
        if ( ! $data['edit_url'] ) :
          echo '<br><small>';
          _e( '(erst nach Bezahlung wieder möglich)', 'wpenon' );
          echo '</small>';
        endif;
        ?>
      </a>
    <?php else : ?>
      <a class="btn btn-primary btn-lg btn-block" href="<?php echo $data['editoverview_url']; ?>">
        <?php
        _e( 'Angaben überprüfen', 'wpenon' );
        ?>
      </a>
    <?php endif; ?>
  </div>
  <div class="col-sm-4">
    <a class="btn btn-primary btn-lg btn-block<?php echo $data['pdf_url'] ? '' : ' disabled'; ?>" href="<?php echo $data['pdf_url'] ? $data['pdf_url'] : '#'; ?>" target="_blank">
      <?php
      if ( $data['paid'] ) {
        _e( 'Energieausweis ansehen', 'wpenon' );
      } else {
        _e( 'Vorschau ansehen', 'wpenon' );
      }
      if ( ! $data['pdf_url'] ) :
        echo '<br><small>';
        _e( '(erst nach vollständiger Dateneingabe möglich)', 'wpenon' );
        echo '</small>';
      endif;
      ?>
    </a>
  </div>
  <div class="col-sm-4">
    <a class="btn btn-primary btn-lg btn-block<?php echo $data['buy_url'] ? '' : ' disabled'; ?>" href="<?php echo ( $data['buy_url'] && $data['editoverview_url'] ) ? ( $data['ordered'] ? $data['buy_url'] : $data['editoverview_url'] ) : '#'; ?>">
      <?php
      if ( $data['ordered'] ) :
        _e( 'Rechnung ansehen', 'wpenon' );
      else :
        _e( 'Bestellen', 'wpenon' );
        if ( ! $data['buy_url'] ) :
          echo '<br><small>';
          _e( '(erst nach vollständiger Dateneingabe möglich)', 'wpenon' );
          echo '</small>';
        endif;
      endif;
      ?>
    </a>
  </div>
</div>

<div class="calculations well">
  <h3>
    <?php _e( 'Ergebnisse der Berechnungen', 'wpenon' ); ?>
  </h3>

  <p class="lead text-right">
    <strong><?php _e( 'Status:', 'wpenon' ); ?></strong>
    <?php
    if ( $data['finalized'] ) :
      echo '<span class="wpenon-finalized-message">';
      _e( 'Angaben vollständig', 'wpenon' );
      echo '</span>';
    else :
      echo '<span class="wpenon-not-finalized-message">';
      _e( 'Angaben noch unvollständig', 'wpenon' );
      echo '</span>';
    endif;
    ?>
  </p>
  <p class="text-right">
    <small>
      <?php
      if ( $data['finalized'] ) :
        _e( 'Hier sehen Sie die Ergebnisse der Energieausweis-Berechnungen.', 'wpenon' );
      else :
        _e( 'Nach der korrekten Eingabe aller Daten werden Sie hier die Ergebnisse der Energieausweis-Berechnungen sehen.', 'wpenon' );
      endif;
      ?>
    </small>
  </p>

  <div class="energy-bar-wrapper">
    <?php wpenon_get_view()->displaySubTemplate( 'energy-bar', '', $data['energy_bar'] ); ?>
  </div>

  <?php if ( $data['efficiency_class'] ) :
    $image = IMMOTICKETENERGIEAUSWEIS_THEME_URL . '/assets/media/klasse_' . strtolower( str_replace( '+', '_plus', $data['efficiency_class'] ) ) . '.jpg';
    ?>
    <p class="lead text-center">
      <?php printf( __( 'Energieeffizienzklasse: %s', 'wpenon' ), '<img src="' . $image . '" alt="' . sprintf( __( 'Energieeffizienzklasse: %s', 'wpenon' ), $data['efficiency_class'] ) . '">' ); ?>
    </p>
  <?php endif; ?>

	<?php if( $data['calculations']['co2_emissionen'] ) : ?>
	<p>
		<?php printf( __( 'CO2 Emissionen: %s kg/m²⋅a', 'wpenon' ), \WPENON\Util\Format::pdfEncode( $data['calculations']['co2_emissionen'] ) ) ?>
	</p>
	<?php endif; ?>
</div>

<?php if ( count( $data['calculations'] ) > 0 && is_user_logged_in() && current_user_can( WPENON_CERTIFICATE_CAP ) ) : ?>
  <p class="text-right">
    <a class="btn btn-default" data-toggle="collapse" href="#calculation-details">
      <?php _e( 'Details zur Berechnung anzeigen', 'wpenon' ); ?>
    </a>
  </p>
  <div id="calculation-details" class="collapse">
    <h4><?php _e( 'Details zur Berechnung', 'wpenon' ); ?></h4>
    <p><?php _e( 'Die folgenden Informationen sind nur für den Shop-Betreiber sichtbar. Der Kunde hat keinen Zugriff darauf.', 'wpenon' ); ?></p>
    <?php wpenon_get_view()->displaySubTemplate( 'calculations', $data['template_suffix'], $data['calculations'] ); ?>
  </div>
<?php endif; ?>
