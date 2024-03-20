<?php

use AWSM\LibEstate\Calculations\Building;
use AWSM\LibEstate\Calculations\Heaters;
use AWSM\LibEstate\Calculations\HotWaterHeaters;

/**
 * ConsumptionPeriods Klasse
 * 
 * Vorrübergehend bis zur neuen Berechnng
 */
class ConsumptionPeriod {
    protected string $period;
    protected string $end;
    protected Heaters $heaters;
    protected HotWaterHeaters $hotWaterHeaters;

    public function __construct( int $period, string $start, string $end, Heaters $heaters, HotWaterHeaters $hotWaterHeaters )
    {
        $this->period          = $period;
        $this->start           = $start;
        $this->end             = $end;
        $this->heaters         = $heaters;
        $this->hotWaterHeaters = $hotWaterHeaters;
    }

    public function Startdatum()
    {
        return date( 'Y-m-d', strtotime( $this->start ) );
    }

    public function Enddatum()
    {
        return date( 'Y-m-d', strtotime( $this->end ) );
    }

    public function Leerstandsfaktor()
    {
        $leerstandsfaktor= 0;
        foreach( $this->heaters AS $heater ) 
        {
            $leerstandsfaktor = $heater->getVacancyMultiplicatorOfPeriod( $this->period );
        }

        return round( $leerstandsfaktor / $this->heaters->count(), 2 );
    }

    public function LeerstandsfaktorWW()
    {
        $leerstandsfaktor= 0;
        foreach( $this->hotWaterHeaters AS $heater ) 
        {
            $leerstandsfaktor = $heater->getVacancyMultiplicatorOfPeriod( $this->period );
        }

        return $leerstandsfaktor / $this->hotWaterHeaters->count();
    }

    public function LeerstandszuschlagKwh()
    {
        $leerstandszuschlagKwh = 0;
        foreach( $this->heaters AS $heater )
        {
            $leerstandszuschlagKwh = $heater->getVacancySurchargeOfPeriod( $this->period );
        }

        return round( $leerstandszuschlagKwh, 0 );
    }

    public function LeerstandszuschlagWWKwh()
    {
        $leerstandszuschlagKwh = 0;
        foreach( $this->hotWaterHeaters AS $heater )
        {
            $leerstandszuschlagKwh = $heater->getVacancySurchargeOfPeriod( $this->period );
        }

        return round( $leerstandszuschlagKwh, 0 );
    }

    public function Primaerenergiefaktor()
    {
        $primaerenergiefaktor= 0;
        foreach( $this->heaters AS $heater ) 
        {
            $primaerenergiefaktor = $heater->getEnergySource()->getPrimaryEnergyFactor();
        }

        return $primaerenergiefaktor / $this->heaters->count();
    }

    public function PrimaerenergiefaktorWW()
    {
        $primaerenergiefaktor= 0;
        foreach( $this->hotWaterHeaters AS $heater ) 
        {
            $primaerenergiefaktor = $heater->getEnergySource()->getPrimaryEnergyFactor();
        }

        return $primaerenergiefaktor / $this->hotWaterHeaters->count();
    }
}