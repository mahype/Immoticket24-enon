<?php

use AWSM\LibEstate\Calculations\Heater;

/**
 * Energietraeger Klasse
 * 
 * Vorrübergehend bis zur neuen Berechnng
 */
class Energietraeger {
    protected Heater $data;

    public function __construct( Heater $heater )
    {
        $this->data = $heater;
    }

    private function EnergietraegerIsBurned() : bool
    {
        if ( strpos ( $this->data->getEnergySource()->getId(), 'brennewert' ) === false ) {
            return false;
        }

        return true;
    }

    public function EnergietraegerVerbrauch()
    {
        $id = $this->data->getEnergySource()->getId() . '-' . $this->data->getEnergySource()->getUnit();

        $this->data->getHeatingSystem()->getId();

        /**
         * 
    'heizoel_l'                   => 0,
    'heizoel_kwh'                 => array( 1, 2 ),
    'biooel_l'                    => 6,
    'biooel_kwh'                  => array( 7, 8 ),
    'erdgas_m3'                   => 9,
    'erdgas_kwh'                  => array( 10, 11 ),
    'biogas_m3'                   => 15,
    'biogas_kwh'                  => array( 16, 17 ),
    'fluessiggas_l'               => 19,
    'fluessiggas_m3'              => 18,
    'fluessiggas_kg'              => 20,
    'fluessiggas_kwh'             => array( 21, 22 ),
    'steinkohle_kg'               => 23,
    'steinkohle_kwh'              => 24,
    'braunkohle_kg'               => 27,
    'braunkohle_kwh'              => 28,
    'stueckholz_m3'               => 29,
    'stueckholz_kg'               => 30,
    'stueckholz_kwh'              => array( 31, 32 ),
    'holzpellets_kg'              => 38,
    'holzpellets_kwh'             => array( 39, 40 ),
    'strom_kwh'                   => 43,
    'fernwaermehzwfossil_kwh'     => -1,
    'fernwaermehzwregenerativ_kwh'=> -1,
    'fernwaermekwkfossil_kwh'     => -1,
    'fernwaermekwkregenerativ_kwh'=> -1,
         * 
         */

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
            case 'fluessiggas_kwh':
                return 'XXX';
            case 'xxx':
                return 'XXX';
            case 'xxx':
                return 'XXX';
            case 'xxx':
                return 'XXX';
            case 'xxx':
                return 'XXX';
            case 'xxx':
                return 'XXX';
            case 'xxx':
                return 'XXX';
            case 'xxx':
                return 'XXX';
            case 'xxx':
                return 'XXX';
            case 'xxx':
                return 'XXX';
            case 'xxx':
                return 'XXX';
            case 'xxx':
                return 'XXX';
            case 'xxx':
                return 'XXX';
            case 'xxx':
                return 'XXX';
        }
    }

    public function UntererHeizwert()
    {
        return $this->data->getEnergySource()->getKWhMultiplicator();
    }

    public function Primaerenergiefaktor()
    {
        return $this->data->getEnergySource()->getPrimaryEnergyFactor();
    }

    public function Emissionsfaktor()
    {
        return $this->data->getEnergySource()->getCo2EmissionFactor();
    }
}