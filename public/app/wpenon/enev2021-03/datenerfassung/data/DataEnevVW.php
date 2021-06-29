<?php

require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/calculations/CalculationsCC.php';

require dirname( __FILE__ ) . '/DataEnev.php';
require dirname( __FILE__ ) . '/ConsumptionPeriod.php';
require dirname( __FILE__ ) . '/Energietraeger.php';
require dirname( __FILE__ ) . '/Moderniserungsempfehlung.php';
require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/modernizations/VW_Modernizations.php';

use Enev\Schema202103\Modernizations\VW_Modernizations;

/**
 * Data Enev Bedarsausweis
 * 
 * @since 1.0.0
 */
class DataEnevVW extends DataEnev {
    /**
     * Daten der Berechnungen
     * 
     * @var array
     * 
     * @since 1.0.0
     */
    private CalculationsCC $calculations;

    /**
     * Berechnungen Bedarfsausweis
     * 
     * @return mixed
     * 
     * @since 1.0.0
     */
    public function calculations() : CalculationsCC
    {
        if ( empty( $this->calculations ) )
        {
            $this->calculations = new CalculationsCC( $this->energieausweis );
        }

        return $this->calculations;
    }

    /**
     * Startdatum
     * 
     * @return string date
     * 
     * @since 1.0.0
     */
    public function Startdatum()
    {
        $consumptionPeriods = $this->calculations->getConsumptionPeriods();
        return date('Y-m-d', strtotime( $consumptionPeriods[0]['start'] ) );
    }

    /**
     * Enddatum
     * 
     * @return string date
     * 
     * @since 1.0.0
     */
    public function Enddatum()
    {
        $consumptionPeriods = $this->calculations->getConsumptionPeriods();
        return date('Y-m-d', strtotime( $consumptionPeriods[2]['end'] ) );
    }

    /**
     * Wesentliche Energieträger Heizunbg
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function WesentlicheEnergietraegerHeizung() : string
    {        
        $energietraeger = array();
        
        $energietraeger[] = wpenon_immoticket24_get_energietraeger_name_2021( $this->energieausweis->h_energietraeger, true );
        if ( $this->energieausweis->h2_info ) {
            $energietraeger[] = wpenon_immoticket24_get_energietraeger_name_2021( $this->energieausweis->h2_energietraeger, true );
            if ( $this->energieausweis->h3_info ) {
                $energietraeger[] = wpenon_immoticket24_get_energietraeger_name_2021( $this->energieausweis->h3_energietraeger, true );
            }
        }

        return implode( ', ', array_unique( $energietraeger ) );
    }
    
    /**
     * Wesentliche Energieträger Heizunbg
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function WesentlicheEnergietraegerWarmWasser() : string
    {
        if ( $this->energieausweis->ww_info == 'ww' ) {
            return wpenon_immoticket24_get_energietraeger_name_2021( $this->energieausweis->ww_energietraeger, true );
        } else if ( $this->energieausweis->ww_info == 'h' ) {
            if( ! wpenon_is_water_independend_heater( $this->energieausweis->h_erzeugung ) ) {
                $energietraeger[] = wpenon_immoticket24_get_energietraeger_name_2021( $this->energieausweis->h_energietraeger, true );
            }
            if ( $this->energieausweis->h2_info ) {
                if( ! wpenon_is_water_independend_heater( $this->energieausweis->h2_erzeugung ) ) {
                    $energietraeger[] = wpenon_immoticket24_get_energietraeger_name_2021( $this->energieausweis->h2_energietraeger, true );
                }
                if ( $this->energieausweis->h3_info ) {
                    if( ! wpenon_is_water_independend_heater( $this->energieausweis->h3_erzeugung ) ) {
                        $energietraeger[] = wpenon_immoticket24_get_energietraeger_name_2021( $this->energieausweis->h3_energietraeger, true );
                    }
                }
            }
            return implode( ', ', array_unique( $energietraeger ) );
        } else if ( $this->energieausweis->ww_info == 'unbekannt' ) {
            return $this->calculations()->getBuilding()->getHeaters()->getHeaterByHighestEnergyValue()->getEnergySource()->getName();;
        }
    }

    /**
     * Gebaeudenutzflaeche
     * 
     * @return float
     */
    public function Gebaeudenutzflaeche()
    {
        return $this->calculations()->getBuilding()->getUsefulArea();
    }

    /**
     * Mittlerer-Endenergieverbrauch
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function MittlererEndenergieverbrauch() : float
    {
        return round( $this->calculations()->getBuilding()->getFinalEnergy(), 2 );
    }
    
    /**
     * Mittlerer-Primaerenergieverbrauch
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function MittlererPrimaerenergieverbrauch() : float
    {
        return round( $this->calculations()->getBuilding()->getPrimaryEnergy(), 2 );
    }

    /**
     * Treibhausgasemissionen
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Treibhausgasemissionen() : float
    {        
        return round( $this->calculations()->getBuilding()->getCo2Emissions(), 2 );
    }
    
    /**
     * Energieeffizienzklasse
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Energieeffizienzklasse()
    {
        return wpenon_get_class( $this->calculations()->getBuilding()->getFinalEnergy(), 'bw' );
    }

    /**
     * Energietraeger
     * 
     * @return Energietraeger[]
     * 
     * @since 1.0.0
     */
    public function Energietraeger() : array
    {
        $energietraeger = [];

        foreach( $this->calculations()->getBuilding()->getHeaters() AS $heater )
        {
            $energietraeger[] = new Energietraeger( $this->calculations()->getConsumptionPeriods(),  $heater, $this->calculations()->getBuilding()->getHotWaterHeaters(),$this->calculations()->getHotWater() );
        }
        
        return $energietraeger;
    }

    /**
     * Energietraeger
     * 
     * @return ConsumptionPeriod[]
     * 
     * @since 1.0.0
     */
    public function ConsumptionPeriods() : array
    {
        $consumptionPeriods = [];

        foreach( $this->calculations()->getConsumptionPeriods() AS $period => $consumptionPeriod ) 
        {
            $consumptionPeriods[] = new ConsumptionPeriod( $period, $consumptionPeriod['start'], $consumptionPeriod['end'], $this->calculations()->getBuilding()->getHeaters(), $this->calculations()->getBuilding()->getHotWaterHeaters() );
        }
        
        return $consumptionPeriods;
    }

    /**
     * LeerstandszuschlagHeizung
     * 
     * @return float
     * 
     * @since 1.0.0
     */
    public function LeerstandszuschlagHeizung()
    {
        return $this->calculations()->getBuilding()->getHeaters()->getVacancySurcharge();
    }

    /**
     * LeerstandszuschlagWarmWasser
     * 
     * @return float
     * 
     * @since 1.0.0
     */
    public function LeerstandszuschlagWarmWasser()
    {
        return $this->calculations()->getBuilding()->getHotWaterHeaters()->getVacancySurcharge();
    }

    /**
     * Zuschlagsfaktor
     * 
     * @return float
     * 
     * @since 1.0.0
     */
    public function Zuschlagsfaktor() : float
    {
        $zuschlagsfaktor = 0;
        
        foreach( $this->calculations()->getBuilding()->getHeaters() AS $heater ) 
        {
            $zuschlagsfaktor += $heater->getVacancySurchargeMultiplicator();
        }

        return $zuschlagsfaktor / $this->calculations()->getBuilding()->getHeaters()->count();
    }

    /**     
     * Empfehlungen-moeglich
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Warmwasserzuschlag() : float
    {
        return $this->calculations->getBuilding()->getHotWaterSurcharge();
    }

    /**
     * Empfehlungen-moeglich
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function WarmwasserPrimaerenergiefaktor() : float
    {
        return $this->calculations->getBuilding()->getHotWaterHeaters()->getPrimaryEnergyFactorAverage();
    }

    /**
     * Kuehlzuschlag
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Kuehlzuschlag() : float
    {
        return $this->calculations->getBuilding()->getCoolerSurcharge();
    }
    /**
     * Kuehler Primaerenergiefaktor
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function KuehlerPrimaerenergiefaktor() : float
    {
        return $this->calculations->getBuilding()->getCoolers()->getPrimaryEnergyFactorAverage();
    }

    /**
     * Gebaeudenutzflaeche-gekuehlt
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function GebaeudenutzflaecheGekuehlt() : int
    {
        return $this->calculations->getBuilding()->getUsefulArea();
    }
    
    /**
     * Empfehlungen-moeglich
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function EmpfehlungenMoeglich()
    {
        $empfehlungen = new VW_Modernizations();
        return $empfehlungen->get_modernizations( array(), $this->energieausweis ) > 0 ? true : false;
    }

    /**
     * Modernisierungsempfehlungen
     * 
     * @return Moderniserungsempfehlung[]
     * 
     * @since 1.0.0
     */
    public function Modernisierungsempfehlungen()
    {
        $modernisierungen = new VW_Modernizations();

        $empfehlungen = [];
        foreach( $modernisierungen->get_modernizations( array(), $this->energieausweis ) AS $empfehlung )
        {
            $empfehlungen[] = new Moderniserungsempfehlung( $empfehlung );
        }

        return $empfehlungen;
    }
}