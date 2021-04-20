<?php

require( dirname( dirname(__FILE__) ) .'/vendor/autoload.php' );

use AWSM\LibEstate\Calculations\ConsumptionCalculations;
use AWSM\LibEstate\Data\Building;
use AWSM\LibEstate\Data\ClimateFactor;
use AWSM\LibEstate\Data\ClimateFactors;
use AWSM\LibEstate\Data\Energy\EnergySource;
use AWSM\LibEstate\Data\Energy\EnergySourceUnit;
use AWSM\LibEstate\Data\Systems\Cooler;
use AWSM\LibEstate\Data\Systems\Coolers;
use AWSM\LibEstate\Data\Systems\Heater;
use AWSM\LibEstate\Data\Systems\Heaters;
use AWSM\LibEstate\Data\Systems\HeaterSystem;
use AWSM\LibEstate\Data\Systems\HotWaterHeater;
use AWSM\LibEstate\Data\Systems\HotWaterHeaters;
use AWSM\LibEstate\Data\Systems\HotWaterHeaterSystem;
use AWSM\LibEstate\Helpers\ConsumptionPeriod;
use AWSM\LibEstate\Helpers\ConsumptionPeriods;
use AWSM\LibEstate\Helpers\TimePeriod;
use AWSM\LibEstate\Helpers\TimePeriods;
use WPENON\Model\Energieausweis;

/**
 * Class CalculationsCC
 * 
 * @since 1.0.0
 */
class CalculationsCC {
    /**
     * Energy certificate (Energy certificate)
     * 
     * @var Energieausweis
     * 
     * @since 1.0.0
     */    
    protected Energieausweis $ec;

    /**
     * Building
     * 
     * @var Building
     * 
     * @since 1.0.0
     */
    protected Building $building;

    /**
     * Time periods
     * 
     * @var TimePeriods
     * 
     * @since 1.0.0
     */
    protected TimePeriods $timePerdiods;

    /**
     * Mapped form data varables
     * 
     * @var array 
     * 
     * @since 1.0.0
     */
    protected stdClass $formData;

    /**
     * Mapped table data variables
     * 
     * @var array 
     * 
     * @since 1.0.0
     */
    protected stdClass $tableData;

    /**
     * Constructor
     * 
     * @param Energiausweis
     * 
     * @since 1.0.0
     */
    public function __construct( Energieausweis $ec )
    {
        $this->ec = $ec;
        
        $this->loadformData();
        $this->setupBuilding();
    }

    public function calculation() : ConsumptionCalculations
    {
        $energySourceElectric = $this->getEnergySource('strom_kwh');
        $calculation = new ConsumptionCalculations( $this->building, $this->getTimePeriods(), $this->getClimateFactors(), $energySourceElectric );        
        return $calculation;       
    }  

    /**
     * Setup building with EC data
     * 
     * @since 1.0.0
     */
    public function setupBuilding() 
    {
        $this->building     = new Building( $this->formData->area, $this->formData->flatCount, $this->formData->hotWaterSource );
        $this->timePerdiods = $this->getTimePeriods();

        $numHeaters = 3;

        $this->building->heaters = new Heaters();        

        for ( $i = 0; $i < $numHeaters; $i++ ) 
        {
            if ( ! $this->hasHeater( $i ) ) {
                continue;
            }

            $this->building->heaters->add( $this->getHeater( $i ) );
        }

        if ( $this->formData->hotWaterSource === 'separate' )
        {
            $this->building->hotWaterHeaters = $this->getHotWaterHeaters();
        }

        if ( $this->formData->cooler )
        {
            $this->building->coolers  = $this->getCoolers();
        }
    }

    

    /**
     * Load mappings for energy certificate values.
     * 
     * @since 1.0.0
     */
    public function loadformData() 
    {
        $this->formData = new stdClass();

        $this->formData->postcode  = $this->ec->adresse_plz;

        $this->formData->area      = $this->ec->flaeche;
        $this->formData->flatCount = $this->ec->wohnungen;
        $this->formData->startDate = $this->ec->verbrauch_zeitraum;

        switch( $this->ec->ww_info ) {
            case 'ww':
                $this->formData->hotWaterSource = 'separate';
                break;
            case 'h':
                $this->formData->hotWaterSource = 'heater';
                break;
            case 'unbekannt':
                $this->formData->hotWaterSource = 'unknown';
                break;
        }

        switch( $this->ec->keller ) {
            case 'beheizt':
                $this->formData->basement = true;
                $this->formData->basementHeated = true;
                break;
            case 'unbeheizt':
                $this->formData->basement = true;
                $this->basementHeated = false;
                break;
            case 'nicht-vorhanden':
                $this->formData->basement = false;
                $this->formData->basementHeated = false;
                break;                
        }

        switch( $this->ec->dach ) {
            case 'beheizt':
                $this->formData->roof = true;
                $this->formData->roofHeated = true;
                break;
            case 'unbeheizt':
                $this->formData->roof = true;
                $this->formData->roofHeated = false;
                break;
            case 'nicht-vorhanden':
                $this->formData->roof = false;
                $this->formData->roofHeated = false;
                break;
        }

        $this->formData->cooler = false;

        if( $this->ec->k_info === 'vorhanden' ) {
            $this->formData->cooler      = true;
            $this->formData->coolerArea  = $this->ec->k_flaeche;
            $this->formData->coolerPower = $this->ec->k_leistung === 'groesser' ? 'bigger' : 'smaller';
        }        
    }

    public function hasCooler() : bool
    {
        return $this->formData->cooler;
    }

    public function getClimateFactors() {
        $climateFactors = new ClimateFactors();

        $climateFactorsDates = wpenon_get_table_results( 'klimafaktoren202001', array(
            'bezeichnung' => array(
                'value'   => $this->formData->postcode,
                'compare' => '>='
            )
        ), array(), true );

        foreach( $climateFactorsDates AS $date => $value ) 
        {
            if( $date === 'bezeichnung' ) 
            {
                continue;
            }

            $date = str_replace( '_', '-', $date );

            $climateFactors->add( new ClimateFactor( new DateTime( $date ), $value )  );
        }

        return $climateFactors;
    }

    /**
     * Get time periods
     * 
     * @return TimePeriods
     * 
     * @since 1.0.0
     */
    public function getTimePeriods() 
    {
        $timePerdiods = new TimePeriods();

        for ( $i = 0; $i < 3; $i++ ) {
            $start = wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $this->formData->startDate, $i, false, 'data' );
            $end   = wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $this->formData->startDate, $i, true, 'data' );

            $timePerdiod = new TimePeriod( new DateTime( $start ), new DateTime( $end ) );
            $timePerdiods->add( $timePerdiod );
        }

        return $timePerdiods;
    }

    /**
     * Checks if heater exist
     * 
     * @param int Heater number (0,1 or 2)
     */
    public function hasHeater( int $heaterNumber ) : bool
    {
        if ( $heaterNumber === 0 ) {
            return true;
        }

        if ( $heaterNumber === 1 && $this->ec->h2_info ) {
            return true;
        }

        if ( $heaterNumber === 2 && $this->ec->h3_info ) {
            return true;
        }

        return false;
    }

    /**
     * Get heater
     * 
     * @param  int Number of heater (0,1 or 2)
     * @return Heater
     * 
     * @since 1.0.0
     */
    public function getHeater( int $heaterNumber ) : Heater
    {
        $heaterPrefix = $this->getHeaterPrefix( $heaterNumber );

        $buildingYearVarname   = $heaterPrefix . '_baujahr';
        $heaterIdVarname       = $heaterPrefix . '_erzeugung';
        $energySourceIdVarname = $heaterPrefix . '_energietraeger_' . $this->ec->$heaterIdVarname;

        $buildingYear   = $this->ec->$buildingYearVarname; 
        $heaterId       = $this->ec->$heaterIdVarname;
        $energySourceId = $this->ec->$energySourceIdVarname;

        $heater = new Heater (
            $this->getHeaterSystem( $heaterId ),
            $this->getEnergySource( $energySourceId ),
            $buildingYear
        );
        
        $consumptionPeriods   = new ConsumptionPeriods();

        foreach( $this->timePerdiods AS $key => $timePeriod )
        {            
            $consumptionValueName = 'verbrauch' . ( $key + 1 ) . '_' . $heaterPrefix;

            $consumption          = $this->ec->$consumptionValueName;
            $consumptionPeriod    = new ConsumptionPeriod( $timePeriod->start, $timePeriod->end, $consumption );

            $consumptionPeriods->add( $consumptionPeriod );
        }

        $heater->consumptionPeriods = $consumptionPeriods;

        return $heater;
    }

    /**
     * Get heater system
     * 
     * @param  string Heater id
     * @return HeaterSystem
     * 
     * @since 1.0.0
     */
    public function getHeaterSystem( string $heaterId ) 
    {
        $heaterName = wpenon_get_table_results( 'h_erzeugung2019', array(
            'bezeichnung' => array(
                'value'   => $heaterId,
                'compare' => '='
            )
        ), array( 'name' ), true );

        $heaterSystem = new HeaterSystem ( $heaterId, $heaterName );

        return $heaterSystem;
    }

    /**
     * Get heater prefix
     * 
     * @param int     Heater number
     * @return string Heater prefix
     * 
     * @since 1.0.0
    */
    public function getHeaterPrefix( int $heaterNumber ) : string
    {
        switch( $heaterNumber ) {
            case 0:
                return 'h';
            case 1:
                return 'h2';
            case 2:
                return 'h3';
        }
    }

    public function getHotWaterHeaters() : HotWaterHeaters
    {
        $hotWaterHeaters = new HotWaterHeaters();
        $hotWaterHeaters->add( $this->getHotWaterHeater() );
        
        return $hotWaterHeaters;
    }

    /**
     * Get hot water
     * 
     * @return HotWaterHeater
     * 
     * @since 1.0.0     
     */
    public function getHotWaterHeater() : HotWaterHeater
    {
        $hotWaterIdVarname     = 'ww_erzeugung';
        $hotWaterId            = $this->ec->$hotWaterIdVarname;

        $energySourceIdVarname = 'ww_energietraeger_' . $hotWaterId;        
        $energySourceId        = $this->ec->$energySourceIdVarname;

        $hotWaterHeaterSystem = $this->getHotWaterHeaterSystem( $hotWaterId );
        $energySource   = $this->getEnergySource( $energySourceId );

        $consumptionPeriods   = new ConsumptionPeriods();

        foreach( $this->timePerdiods AS $key => $timePeriod )
        {            
            $consumptionValueName = 'verbrauch' . $key + 1 . '_ww';
            $consumption          = $this->ec->$consumptionValueName;
            $consumptionPeriod    = new ConsumptionPeriod( $timePeriod->start, $timePeriod->end, $consumption );

            $consumptionPeriods->add( $consumptionPeriod );
        }

        $hotWaterHeater = new HotWaterHeater( $hotWaterHeaterSystem, $energySource );
        $hotWaterHeater->consumptionPeriods = $consumptionPeriods;

        return $hotWaterHeater;
    }

    /**
     * Get hot water system
     * 
     * @param string System id
     * @return HotWaterSystem
     * 
     * @since 1.0.0 
     */
    public function getHotWaterHeaterSystem( string $systemId ) : HotWaterHeaterSystem
    {
        $hotWaterName = wpenon_get_table_results( 'ww_erzeugung202001', array(
            'bezeichnung' => array(
                'value'   => $systemId,
                'compare' => '='
            )
        ), array( 'name' ), true );

        return new HotWaterHeaterSystem ( $systemId, $hotWaterName );
    }

    /**
     * Get coolers
     * 
     * @return Coolers
     * 
     * @since 1.0.0
     */
    public function getCoolers() : Coolers
    {
        $coolers = new Coolers();
        $coolers->add( $this->getCooler() );
        return $coolers;    
    }

    /**
     * Get hot water
     * 
     * @return HotWater
     * 
     * @since 1.0.0     
     */
    public function getCooler() : Cooler
    {
        return new Cooler( 
            $this->getEnergySource( 'strom_kwh' ),
            $this->formData->coolerArea
        );
    }

    /**
     * Get energy source
     * 
     * @param string Energy source id
     * 
     * @since 1.0.0
     */
    public function getEnergySource( string $energySourceId ) : EnergySource
    {
        $conversions = wpenon_get_table_results( 'energietraeger_umrechnungen', array(
            'bezeichnung' => array(
                'value'   => $energySourceId,
                'compare' => '='
            )
        ), array(), true );

        $energySourceValues = wpenon_get_table_results( 'energietraeger202001', array(
            'bezeichnung' => array(
                'value'   => $conversions->energietraeger,
                'compare' => '='
            )
        ), array(), true );

        $energySourceUnit = new EnergySourceUnit (
            $conversions->bezeichnung,
            $conversions->einheit,
            $conversions->name,
            $conversions->mpk
        );

        $energySource = new EnergySource ( 
            $energySourceValues->bezeichnung, 
            $energySourceValues->name, 
            $energySourceUnit, 
            $energySourceValues->primaer,
            $energySourceValues->co2
        );

        return $energySource;
    }

    
}