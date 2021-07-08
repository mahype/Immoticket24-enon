<?php
/**
 * @package WPENON
 * @version 1.0.0
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */
?>

<?php 

$calc = $data['object']; 
$building = $data['object']->getBuilding(); 

$hotWaterHeater = null;
if( $building->issetHotWaterHeaters() ) {
  $hotWaterHeater = $building->getHotWaterHeaters()->current();
}

$consumptionPeriods = $calc->getConsumptionPeriods();

$start = $consumptionPeriods[ 0 ]['start'];
$end   = $consumptionPeriods[ count( $consumptionPeriods ) - 1 ]['end'];

?>

<?php foreach( $building->getHeaters() AS $heater ): ?>
  <table class="table">
    <tr>
      <th>Heizungssytem</th>
      <th>Energieträger</th>
      <th>kWh-Multiplikator</th>
      <th>Primärenergiefaktor</th>
      <th>CO2-Emissionsfaktor</th>
    </tr>  
      <tr>
        <td><?php echo $heater->getHeatingSystem()->getName(); ?></td>
        <td><?php echo $heater->getEnergySource()->getName(); ?></td>
        <td><?php echo $heater->getEnergySource()->getKWhMultiplicator(); ?></td>
        <td><?php echo $heater->getEnergySource()->getPrimaryEnergyFactor(); ?></td>
        <td><?php echo $heater->getEnergySource()->getCo2EmissionFactor(); ?></td>
      </tr>  
  </table>

  <table class="table">
    <tr>
      <th>Startdatum</th>
      <th>Enddatum</th>
      <th>kWh</th>
      <th>f<sub>k</sub></th>
      <th>E<sub>vb,h</sub></th>
      <th>E<sub>vb,ww</sub></th>
      <th>E<sub>vb</sub></th>
      <th>E<sub>leer,h</sub></th>
      <th>E<sub>leer,ww</sub></th>
      <th>e<sub>h</sub></th>
      <th>e<sub>ww</sub></th>
      <th>e</th>
    </tr>
    <?php foreach( $calc->getConsumptionPeriods() AS $key => $period ): ?>
    <?php

      $hotWaterHeaterVacancySurcharge    = isset( $hotWaterHeater ) ? $hotWaterHeater->getVacancySurchargeOfPeriod( $key ) : 0; 
      $hotWaterHeaterFinalEnergyOfPeriod = isset( $hotWaterHeater ) ? $hotWaterHeater->getFinalEnergyOfPeriod( $key ) : 0;
      $heaterFinalEnergyOfPeriod         = $heater->getFinalEnergyOfPeriod( $key );
      $finalEnergy                       = $heaterFinalEnergyOfPeriod + $hotWaterHeaterFinalEnergyOfPeriod;
      
    ?>
      <tr>
        <td><?php echo $period['start']; ?></td>
        <td><?php echo $period['end']; ?></td>
        <td><?php echo $heater->getKWhOfPeriod( $key ); ?></td>
        <td><?php echo round( $heater->getClimateFactorOfPeriod( $key ), 2 ); ?></td>        
        <td><?php echo round( $heater->getHeaterEnergyConsumptionOfPeriod( $key ), 2 ); ?></td>
        <td><?php echo round( $heater->getHotWaterEnergyConsumptionOfPeriod( $key ), 2 ); ?></td>
        <td><?php echo round( $heater->getEnergyConsumptionOfPeriod( $key ), 2 ); ?></td>        
        <td><?php echo round( $heater->getVacancySurchargeOfPeriod( $key ), 2 ); ?></td>
        <td><?php echo round( $hotWaterHeaterVacancySurcharge, 2 ); ?></td>
        <td><?php echo round( $heater->getFinalEnergyOfPeriod( $key ), 2 ); ?></td>
        <td><?php echo round( $hotWaterHeaterFinalEnergyOfPeriod, 2); ?></td>
        <td><?php echo round( $finalEnergy, 2 ); ?></td>
      </tr>
    <?php endforeach; ?>
    </table>

<?php endforeach; ?>
<?php if( $$this->getHotWaterSurCharge() > 0 ): ?>
      <div><?php echo sprintf( 'Warmwasserzuschlag: %s', $building->getHotWaterSurChargeAverage() ); ?> </div>
<?php endif; ?>
<?php if( $$this->getCoolerSurCharge() > 0 ): ?>
      <div><?php echo sprintf( 'Warmwasserzuschlag: %s', $building->getCoolerSurChargeAverage() ); ?> </div>
<?php endif; ?>

<p class="lead">
<?php printf( __( 'Spez. Endenergieverbrauch q<sub>E</sub>: %s kWh/m&sup2;', 'wpenon' ),round( $building->getFinalEnergy(), 2 ) ); ?>
</p>

<p class="lead">
  <?php printf( __( 'Spez. Primärenergieverbrauch q<sub>P</sub>: %s kWh/m&sup2;', 'wpenon' ), round( $building->getPrimaryEnergy(), 2 ) ); ?><br>
</p>

<p class="lead">
  <?php printf( __( 'CO2 Emissionen: %s kg/(m²·a) ', 'wpenon' ), round( $building->getCo2Emissions(), 2 ) ); ?>
</p>


<div style="background:#ccc;">
Alte Berechnungen
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
</div>