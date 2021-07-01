<?php

use AWSM\LibEstate\Calculations\Heater;
use AWSM\LibEstate\Calculations\HotWaterHeaters;

/**
 * Energietraeger Klasse
 * 
 * Vorrübergehend bis zur neuen Berechnng
 */
class Energietraeger {
    protected array $consumptionPeriods;
    protected Heater          $heater;
    protected HotWaterHeaters $hotWaterHeaters;
    protected string          $hotWater;

    public function __construct( array $consumptionPeriods, Heater $heater, HotWaterHeaters $hotWaterHeaters, string $hotWater )
    {
        $this->consumptionPeriods = $consumptionPeriods;
        $this->heater             = $heater;
        $this->hotWaterHeaters    = $hotWaterHeaters;
        $this->hotWater           = $hotWater;
    }

    private function EnergietraegerIsBurned() : bool
    {
        if ( strpos ( $this->heater->getEnergySource()->getId(), 'brennwert' ) === false ) {
            return false;
        }

        return true;
    }

    public function EnergietraegerVerbrauch()
    {
        $id = $this->heater->getEnergySource()->getId() . '_' . $this->heater->getEnergySource()->getUnit();

        $this->heater->getHeatingSystem()->getId();

        switch( $id )
        {
            case 'heizoel_l':
                return 'Heizöl in Liter';
            case 'heizoel_kwh':
                return $this->EnergietraegerIsBurned() ? 'Heizöl in kWh Brennwert' : 'Heizöl in kWh Heizwert';
            case 'erdgas_m3':
                return 'Erdgas in m³';            
            case 'erdgas_kwh':
                return $this->EnergietraegerIsBurned() ? 'Erdgas in kWh Brennwert' : 'Erdgas in kWh Heizwert';
            case 'biooel_l':
                return 'Bioöl in Liter';
            case 'biooel_kwh':
                return $this->EnergietraegerIsBurned() ? 'Bioöl in kWh Brennwert' : 'Bioöl in kWh Heizwert';
            case 'biogas_m3':
                return 'Biogas in m³';
            case 'biogas_kwh':
                return $this->EnergietraegerIsBurned() ? 'Biogas in kWh Brennwert' : 'Biogas in kWh Heizwert';
            case 'fluessiggas_l':
                return 'Flüssiggas in Liter flüssig';
            case 'fluessiggas_m3':
                return 'Flüssiggas in m³ gasförmig';
            case 'fluessiggas_kg':
                return 'Flüssiggas in kg';
            case 'steinkohle_kg':
                return 'Steinkohle in kg';
            case 'steinkohle_kwh':
                return 'Steinkohle in kWh Heizwert';
            case 'braunkohle_kg':
                return 'Braunkohle in kg';
            case 'braunkohle_kwh':
                return 'Braunkohle in kWh Heizwert';
            case 'stueckholz_m3':
                return 'Holz in Raummeter';
            case 'stueckholz_kg':
                return 'Holz in kg';
            case 'stueckholz_kwh':
                return $this->EnergietraegerIsBurned() ? 'Holz in kWh Brennwert' : 'Holz in kWh Heizwert';
            case 'holzpellets_kg': // ?
                return 'Holz in kg';
            case 'holzpellets_kwh':
                return $this->EnergietraegerIsBurned() ? 'Holz in kWh Brennwert' : 'Holz in kWh Heizwert';
            case 'strom_kwh':
                return 'Strom netzbezogen in kWh';
            case 'fernwaermehzwfossil_kwh':
                return 'Nah-/Fernwärme aus Heizwerken, fossiler Brennstoff (Stein-/Braunkohle) bzw. Energieträger in kWh';
            case 'fernwaermehzwregenerativ_kwh':
                return 'Nah-/Fernwärme aus Heizwerken, erneuerbarer Brennstoff bzw. Energieträger in kWh';
            case 'fernwaermekwkfossil_kwh':
                return 'Nah-/Fernwärme aus KWK, fossiler Brennstoff (Stein-/Braunkohle) bzw. Energieträger in kWh';
            case 'fernwaermekwkregenerativ_kwh':
                return 'Nah-/Fernwärme aus KWK, erneuerbarer Brennstoff bzw. Energieträger in kWh';
        }
    }

    public function UntererHeizwert()
    {
        return $this->heater->getEnergySource()->getKWhMultiplicator();
    }

    public function Primaerenergiefaktor()
    {
        return $this->heater->getEnergySource()->getPrimaryEnergyFactor();
    }

    public function Emissionsfaktor()
    {
        return $this->heater->getEnergySource()->getCo2EmissionFactor() * 1000;
    }

    public function Startdatum( int $period )
    {
        return date( 'Y-m-d', strtotime( $this->consumptionPeriods[ $period ][ 'start' ] ) );
    }

    public function Enddatum( int $period )
    {
        return date( 'Y-m-d', strtotime( $this->consumptionPeriods[ $period ][ 'end' ] ) );
    }

    public function VerbrauchteMenge( int $period ) 
    {
        return round( $this->heater->getEnergyConsumptionOfPeriod( $period ), 0 );
    }

    public function Energieverbrauch( int $period ) 
    {
        return round( $this->heater->getKWhOfPeriod( $period ), 0 );
    }

    public function Warmwasserwertermittlung( ) 
    {
        switch( $this->hotWater ) {
            case 'heater':
                return 'Pauschale für dezentrale Warmwasserbereitung (Wohngebäude)';
            case 'separate':
                return 'Rechenwert nach Heizkostenverordnung (Wohngebäude)';
            case 'unknown':
                return 'Pauschale für dezentrale Warmwasserbereitung (Wohngebäude)';
        }
    }

    public function EnergieverbrauchsanteilWarmwasserZentral( $period )
    {
        if( $this->hotWater == 'heater' )
        {
            return round( $this->hotWaterHeaters->getEnergyConsumptionOfPeriod( $period ), 0 );
        }

        return 0;
    }

    public function EnergieverbrauchsanteilHeizung( $period )
    {
        return round( $this->heater->getEnergyConsumptionOfPeriod( $period ), 0 );
    }

    public function Klimafaktor( int $period )
    {
        return $this->heater->getClimateFactorOfPeriod( $period );
    }
}