<?php
/**
 * @package WPENON
 * @version 1.0.0
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */
?>

<?php wpenon_get_view()->displaySubTemplate( 'table-row', '', array(
  'caption'   => __( 'Anlagendaten', 'wpenon' ),
  'fields'    => array(
    array(
      'key'       => 'name',
      'headline'  => __( 'Anlage', 'wpenon' ),
    ),
    array(
      'key'       => 'art',
      'headline'  => __( 'Art', 'wpenon' ),
    ),
    array(
      'key'       => 'energietraeger',
      'headline'  => __( 'Energieträger', 'wpenon' ),
    ),
    array(
      'key'       => 'energietraeger_einheit',
      'headline'  => __( 'Einheit', 'wpenon' ),
      'format'    => 'unit',
    ),
    array(
      'key'       => 'energietraeger_mpk',
      'headline'  => __( 'Umrechnungsfaktor', 'wpenon' ),
      'format'    => 'float',
    ),
    array(
      'key'       => 'energietraeger_primaer',
      'headline'  => __( 'Primärenergiefaktor', 'wpenon' ),
      'format'    => 'float',
    ),
  ),
  'data'      => $data['anlagendaten'],
) ); ?>

<?php wpenon_get_view()->displaySubTemplate( 'table-row', '', array(
  'caption'   => __( 'Verbrauchsdaten', 'wpenon' ),
  'fields'    => array(
    array(
      'key'       => 'start',
      'headline'  => __( 'Startdatum', 'wpenon' ),
    ),
    array(
      'key'       => 'ende',
      'headline'  => __( 'Enddatum', 'wpenon' ),
    ),
    array(
      'key'       => 'energietraeger',
      'headline'  => __( 'Energieträger', 'wpenon' ),
    ),
    array(
      'key'       => 'klima',
      'headline'  => __( 'Klimafaktor', 'wpenon' ),
      'format'    => 'float',
    ),
    array(
      'key'       => 'gesamt_b',
      'headline'  => __( 'Gesamtverbrauch', 'wpenon' ),
      'format'    => 'float',
    ),
    array(
      'key'       => 'heizung_b',
      'headline'  => __( 'Heizungsverbrauch', 'wpenon' ),
      'format'    => 'float',
    ),
    array(
      'key'       => 'warmwasser_b',
      'headline'  => __( 'Warmwasserverbrauch', 'wpenon' ),
      'format'    => 'float',
    ),
  ),
  'data'      => $data['verbrauchsdaten'],
) ); ?>

<p class="lead">
  <?php printf( __( 'Spez. Endenergieverbrauch q<sub>E</sub>: %s kWh/m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $data['endenergie'] ) ); ?>
</p>

<p class="lead">
  <?php printf( __( 'Spez. Primärenergieverbrauch q<sub>P</sub>: %s kWh/m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $data['primaerenergie'] ) ); ?><br>
</p>

<p class="lead">
  <?php printf( __( 'CO2 Emissionen: %s kg/(m²·a) ', 'wpenon' ), \WPENON\Util\Format::float( $data['co2_emissionen'] ) ); ?>
</p>

<?php foreach( $data['co2_emissionen_heaters'] AS $key => $co2_heater ) : ?>
  <p>
    <?php printf( __( 'CO2 Emissionen Heizung %s: %s kg/m²⋅a', 'wpenon' ), ( $key + 1 ) , \WPENON\Util\Format::pdfEncode( $co2_heater ) ) ?>
  </p>
<?php endforeach; ?>

<?php if( $data['co2_emissionen_hotwaterheater'] ) : ?>
<p>
  <?php printf( __( 'CO2 Emissionen Warmwasser: %s kg/m²⋅a', 'wpenon' ), \WPENON\Util\Format::pdfEncode( $data['co2_emissionen_hotwaterheater'] ) ) ?>
</p>
<?php endif; ?>

<?php if( $data['co2_emissionen_cooler'] ) : ?>
<p>
  <?php printf( __( 'CO2 Emissionen: %s kg/m²⋅a', 'wpenon' ), \WPENON\Util\Format::pdfEncode( $data['co2_emissionen_cooler'] ) ) ?>
</p>
<?php endif; ?>