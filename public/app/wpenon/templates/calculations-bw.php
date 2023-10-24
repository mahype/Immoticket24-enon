<?php
/**
 * @package WPENON
 * @version 1.0.0
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */
?>

<p class="lead"><?php printf( __( 'Hüllvolumen V<sub>e</sub>: %s m&sup3;', 'wpenon' ), \WPENON\Util\Format::float( $data['huellvolumen'] ) ); ?></p>

<?php wpenon_get_view()->displaySubTemplate( 'table-row', '', array(
  'caption'   => __( 'Volumenkomponenten', 'wpenon' ),
  'fields'    => array(
    array(
      'key'       => 'name',
      'headline'  => __( 'Komponente', 'wpenon' ),
    ),
    array(
      'key'       => 'v',
      'headline'  => __( 'Volumen', 'wpenon' ),
      'format'    => 'float',
    ),
  ),
  'data'      => $data['volumenteile'],
) ); ?>
<p class="lead"><?php printf( __( 'Nutzfläche A<sub>N</sub>: %s m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $data['nutzflaeche'] ) ); ?></p>

<p class="lead"><?php printf( __( 'Hüllfläche A: %s m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $data['huellflaeche'] ) ); ?></p>
<?php wpenon_get_view()->displaySubTemplate( 'table-row', '', array(
  'caption'   => __( 'Bauteile', 'wpenon' ),
  'fields'    => array(
    array(
      'key'       => 'name',
      'headline'  => __( 'Bauteil', 'wpenon' ),
    ),
    array(
      'key'       => 'a',
      'headline'  => __( 'Fläche', 'wpenon' ),
      'format'    => 'float',
    ),
    array(
      'key'       => 'u',
      'headline'  => __( 'U-Wert', 'wpenon' ),
      'format'    => 'float',
    ),
    array(
      'key'       => 'fx',
      'headline'  => __( 'Fx-Wert', 'wpenon' ),
      'format'    => 'float',
    ),
  ),
  'data'      => $data['bauteile'],
) ); 

// Gebäude Objekt
$gebaeude = $data['gebaeude'];

// Luftwechsel Objekt
$luftwechsel = $data['luftwechsel'];

// Mittlere Belastung Objekt
$mittlere_belastung = $data['mittlere_belastung'];

?>

<p class="lead"><?php printf( __( 'Transmissionswärmeverluste H<sub>T</sub>: %s W/K', 'wpenon' ), \WPENON\Util\Format::float( $data['ht'] ) ); ?></p>
<p class="lead"><?php printf( __( 'Lüftungswärmeverluste H<sub>V</sub>: %s W/K', 'wpenon' ), \WPENON\Util\Format::float( $data['hv'] ) ); ?></p>

<div class="lead" style="background-color:lightgray; padding:5px;"><strong>NEU 2023</strong><br />
  <table>    
    <tr>
      <td>Nutzfläche</td><td><?php echo \WPENON\Util\Format::float( $gebaeude->nutzflaeche() ); ?> m<sup>2</sup></td>
    </tr>
    <?php if ( isset( $data['bauteile']['kellerwand'] ) ) : ?>
    <tr>
      <td>Kellerfläche</td><td><?php echo $data['bauteile']['boden']['a']; ?> m<sup>2</sup></td>
    </tr>
    <tr>
      <td>Kellerwandfläche</td><td><?php echo $data['bauteile']['kellerwand']['a']; ?> m<sup>2</sup></td>
    </tr>
    <?php endif; ?>
    <tr>
      <td>Nettohüllvolumen</td><td><?php echo \WPENON\Util\Format::float( $gebaeude->huellvolumen() ); ?> m<sup>3</sup></td>
    </tr>
    <tr>
      <td>A/V rate</td><td><?php echo \WPENON\Util\Format::float( $gebaeude->ave_verhaeltnis() ); ?></td>
    </tr>
    <tr>
      <td>Gesamtluftwechsel 𝑛</td><td><?php echo \WPENON\Util\Format::float( $luftwechsel->n() ); ?></td>
    </tr>
    <tr>
      <td>Luftwechselrate 𝑛<sub>0</sub></td><td><?php echo \WPENON\Util\Format::float( $luftwechsel->n0() ); ?></td>
    </tr>
    <tr>
      <td>Lüftungswärmeverluste H<sub>V</sub></td><td><?php echo \WPENON\Util\Format::float( $luftwechsel->hv() ); ?> W/K</td>
    </tr>
    <tr>
      <td>Korrekturfaktor  fwin1</td><td><?php echo \WPENON\Util\Format::float( $luftwechsel->fwin1() ); ?></td>
    </tr>
    <tr>
      <td>Saisonaler Korrekturfaktor fwin2</td><td><?php echo \WPENON\Util\Format::float( $luftwechsel->fwin2() ); ?></td>
    </tr>
    <tr>
      <td>ht</td><td><?php echo \WPENON\Util\Format::float( $data['ht'] ); ?></td>
    </tr>
    <tr>
      <td>N<sub>anl</sub></td><td><?php echo \WPENON\Util\Format::float( $luftwechsel->n_anl() ); ?></td>
    </tr>
    <tr>
      <td>N<sub>wrg</sub></td><td><?php echo \WPENON\Util\Format::float( $luftwechsel->n_wrg() ); ?></td>
    </tr>
    <tr>
      <td>Maximale Heizlast h<sub>max</sub></td><td><?php echo \WPENON\Util\Format::float($luftwechsel->h_max()); ?></td>
    </tr>
    <tr>
      <td>Spezifische Heizlast h<sub>max,spec</sub></td><td><?php echo \WPENON\Util\Format::float($luftwechsel->h_max_spezifisch()); ?></td>
    </tr>
    <tr>
      <td>Mittlere Belastung ßem<sub>max</sub></td><td><?php echo \WPENON\Util\Format::float($mittlere_belastung->ßemMax()); ?></td>
    </tr>
    <tr>
      <td>Jährlicher Trinkwarmwasserbedarf Q<sub>wb</sub></td><td><?php echo \WPENON\Util\Format::float($gebaeude->qwb()); ?> kWh</td>
    </tr>
    </tr>
  </table>
</div>

<?php wpenon_get_view()->displaySubTemplate( 'table-row', '', array(
  'caption'   => __( 'Monatliche Bedarfsinformationen', 'wpenon' ),
  'fields'    => array(
    array(
      'key'       => 'name',
      'headline'  => __( 'Monat', 'wpenon' ),
    ),
    array(
      'key'       => 'tage',
      'headline'  => __( 'Tage', 'wpenon' ),
      'format'    => 'int',
    ),
    array(
      'key'       => 'temperatur',
      'headline'  => __( 'Außentemperatur', 'wpenon' ),
      'format'    => 'float',
    ),
    array(
      'key'       => 'qh',
      'headline'  => __( 'Heizwärmebedarf Q<sub>h, m</sub>', 'wpenon' ),
      'format'    => 'float',
    ),
    /*array(
      'key'       => 'ql',
      'headline'  => __( 'Gesamtverluste Q<sub>l, m</sub>', 'wpenon' ),
      'format'    => 'float',
    ),*/
    array(
      'key'       => 'qt',
      'headline'  => __( 'Transmissionswärmeverluste Q<sub>t, m</sub>', 'wpenon' ),
      'format'    => 'float',
    ),
    array(
      'key'       => 'qv',
      'headline'  => __( 'Lüftungswärmeverluste Q<sub>v, m</sub>', 'wpenon' ),
      'format'    => 'float',
    ),
    /*array(
      'key'       => 'qg',
      'headline'  => __( 'Gesamtgewinne Q<sub>g, m</sub>', 'wpenon' ),
      'format'    => 'float',
    ),*/
    array(
      'key'       => 'qs',
      'headline'  => __( 'Solare Gewinne Q<sub>s, m</sub>', 'wpenon' ),
      'format'    => 'float',
    ),
    array(
      'key'       => 'qi',
      'headline'  => __( 'Interne Gewinne Q<sub>i, m</sub>', 'wpenon' ),
      'format'    => 'float',
    ),
  ),
  'data'      => $data['monate'],
) ); ?>

<p class="lead"><?php printf( __( 'Jahresheizwärmebedarf Q<sub>H</sub>: %s kWh', 'wpenon' ), \WPENON\Util\Format::float( $data['qh'] ) ); ?></p>
<p class="lead"><?php printf( __( 'Jahreswarmwasserbedarf Q<sub>W</sub>: %s kWh', 'wpenon' ), \WPENON\Util\Format::float( $data['qw'] ) ); ?></p>
<p class="lead"><?php printf( __( 'Transmissionswärmeverluste Q<sub>T</sub>: %s kWh', 'wpenon' ), \WPENON\Util\Format::float( $data['qt'] ) ); ?></p>
<p class="lead"><?php printf( __( 'Lüftungswärmeverluste Q<sub>V</sub>: %s kWh', 'wpenon' ), \WPENON\Util\Format::float( $data['qv'] ) ); ?></p>
<p class="lead"><?php printf( __( 'Solare Gewinne Q<sub>S</sub>: %s kWh', 'wpenon' ), \WPENON\Util\Format::float( $data['qs'] ) ); ?></p>
<p class="lead"><?php printf( __( 'Interne Gewinne Q<sub>I</sub>: %s kWh', 'wpenon' ), \WPENON\Util\Format::float( $data['qi'] ) ); ?></p>

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
      'key'       => 'energietraeger_primaer',
      'headline'  => __( 'Primärenergiefaktor', 'wpenon' ),
      'format'    => 'float',
    ),
    array(
      'key'       => 'aufwandszahl',
      'headline'  => __( 'Aufwandszahl E<sub>P</sub>', 'wpenon' ),
      'format'    => 'float',
    ),
    array(
      'key'       => 'hilfsenergie',
      'headline'  => __( 'Hilfsenergie', 'wpenon' ),
      'format'    => 'float',
    ),
    array(
      'key'       => 'heizwaermegewinne',
      'headline'  => __( 'Heizwärmegewinne', 'wpenon' ),
      'format'    => 'float',
    ),
  ),
  'data'      => $data['anlagendaten'],
) ); ?>

<?php wpenon_get_view()->displaySubTemplate( 'table-row', '', array(
  'caption'   => __( 'Energieübergabedaten', 'wpenon' ),
  'fields'    => array(
    array(
      'key'       => 'name',
      'headline'  => __( 'Übergabe', 'wpenon' ),
    ),
    array(
      'key'       => 'art',
      'headline'  => __( 'Art', 'wpenon' ),
    ),
    array(
      'key'       => 'waermeverluste',
      'headline'  => __( 'Wärmeverluste', 'wpenon' ),
      'format'    => 'float',
    ),
  ),
  'data'      => $data['uebergabe'],
) ); ?>

<?php wpenon_get_view()->displaySubTemplate( 'table-row', '', array(
  'caption'   => __( 'Energieverteilungsdaten', 'wpenon' ),
  'fields'    => array(
    array(
      'key'       => 'name',
      'headline'  => __( 'Verteilung', 'wpenon' ),
    ),
    array(
      'key'       => 'art',
      'headline'  => __( 'Art', 'wpenon' ),
    ),
    array(
      'key'       => 'waermeverluste',
      'headline'  => __( 'Wärmeverluste', 'wpenon' ),
      'format'    => 'float',
    ),
    array(
      'key'       => 'hilfsenergie',
      'headline'  => __( 'Hilfsenergie', 'wpenon' ),
      'format'    => 'float',
    ),
    array(
      'key'       => 'heizwaermegewinne',
      'headline'  => __( 'Heizwärmegewinne', 'wpenon' ),
      'format'    => 'float',
    ),
  ),
  'data'      => $data['verteilung'],
) ); ?>

<?php wpenon_get_view()->displaySubTemplate( 'table-row', '', array(
  'caption'   => __( 'Energiespeicherungsdaten', 'wpenon' ),
  'fields'    => array(
    array(
      'key'       => 'name',
      'headline'  => __( 'Speicherung', 'wpenon' ),
    ),
    array(
      'key'       => 'art',
      'headline'  => __( 'Art', 'wpenon' ),
    ),
    array(
      'key'       => 'waermeverluste',
      'headline'  => __( 'Wärmeverluste', 'wpenon' ),
      'format'    => 'float',
    ),
    array(
      'key'       => 'hilfsenergie',
      'headline'  => __( 'Hilfsenergie', 'wpenon' ),
      'format'    => 'float',
    ),
    array(
      'key'       => 'heizwaermegewinne',
      'headline'  => __( 'Heizwärmegewinne', 'wpenon' ),
      'format'    => 'float',
    ),
  ),
  'data'      => $data['speicherung'],
) ); ?>

<p class="lead">
  <?php printf( __( 'Spez. Endenergiebedarf Heizung q<sub>H, E</sub>: %s kWh/m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $data['qh_e_b'] ) ); ?><br>
  <?php printf( __( 'Spez. Endenergiebedarf Warmwasser q<sub>W, E</sub>: %s kWh/m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $data['qw_e_b'] ) ); ?><br>
  <?php printf( __( 'Spez. Endenergiebedarf Lüftung q<sub>L, E</sub>: %s kWh/m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $data['ql_e_b'] ) ); ?><br>
  <?php printf( __( 'Spez. Endenergiebedarf Hilfsenergie q<sub>HE, E</sub>: %s kWh/m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $data['qhe_e_b'] ) ); ?>
</p>

<p class="lead">
  <?php printf( __( 'Spez. Primärenergiebedarf Heizung q<sub>H, P</sub>: %s kWh/m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $data['qh_p_b'] ) ); ?><br>
  <?php printf( __( 'Spez. Primärenergiebedarf Warmwasser q<sub>W, P</sub>: %s kWh/m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $data['qw_p_b'] ) ); ?><br>
  <?php printf( __( 'Spez. Primärenergiebedarf Lüftung q<sub>L, P</sub>: %s kWh/m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $data['ql_p_b'] ) ); ?><br>
  <?php printf( __( 'Spez. Primärenergiebedarf Hilfsenergie q<sub>HE, P</sub>: %s kWh/m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $data['qhe_p_b'] ) ); ?>
</p>

<p class="lead">
  <?php printf( __( 'Spez. Endenergiebedarf q<sub>E</sub>: %s kWh/m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $data['endenergie'] ) ); ?>
</p>

<p class="lead">
  <?php printf( __( 'Spez. Primärenergiebedarf q<sub>P, vorh.</sub>: %s kWh/m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $data['primaerenergie'] ) ); ?><br>
  <?php printf( __( 'Spez. Primärenergiebedarf des Referenzgebäudes q<sub>P, zul.</sub>: %s kWh/m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $data['primaerenergie_reference'] ) ); ?>
</p>

<p class="lead">
  <?php printf( __( 'Spez. Transmissionswärmeverluste H<sub>T, vorh.</sub>: %s', 'wpenon' ), \WPENON\Util\Format::float( $data['ht_b'] ) ); ?><br>
  <?php printf( __( 'Spez. Transmissionswärmeverluste des Referenzgebäudes H<sub>T, zul.</sub>: %s', 'wpenon' ), \WPENON\Util\Format::float( $data['ht_b_reference'] ) ); ?>
</p>

