<?php

require( dirname( dirname(__FILE__) ) .'/vendor/autoload.php' );

use AWSM\LibEstate\Data\Building;
use AWSM\LibEstate\Data\Energy\EnergySource;
use AWSM\LibEstate\Data\Energy\EnergySourceUnit;
use AWSM\LibEstate\Data\Systems\Heater;
use AWSM\LibEstate\Data\Systems\HeaterSystem;
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
        $this->loadTableData();
        $this->setupBuilding();
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

        $this->formData->area = $this->ec->flaeche;
        $this->formData->area = $this->ec->flaeche;
    }

    public function hasHeater( int $heaterId ) : bool
    {
        if ( $heaterId === 0 ) {
            return true;
        }

        if ( $heaterId === 1 && $this->ec->h2_info ) {
            return true;
        }

        if ( $heaterId === 2 && $this->ec->h3_info ) {
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
        switch( $heaterNumber ) {
            case 0:
                $varName = 'h';
                break;
            case 1:
                $varName = 'h2';
                break;
            case 2:
                $varName = 'h3';
                break;
        }

        $heateIdVarname = $varName . '_erzeugung';
        $buildingYearVarname = $varName . '_erzeugung';

        $heaterId = $this->ec->$heateIdVarname;
        $buildingYear = $this->ec->$buildingYearVarname;

        $heater = new Heater (
            $this->getHeaterSystem( $heaterId ),
            $this->getEnergySource( $heaterId ),
            $buildingYear
        );

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
                'value'   => $this->ec->$heaterId,
                'compare' => '='
            )
        ), array( 'name' ), true );

        $heaterSystem = new HeaterSystem ( $this->ec->$heaterId, $heaterName );

        return $heaterSystem;
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
        $varName = 'h_energietraeger_' . $energySourceId;
        $energySourceId = $this->ec->$varName;

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

    public function loadTableData() 
    {
        $this->tableData = new stdClass();

        $search = array(
            'bezeichnung' => array(
                'value'   => $this->formData->postcode,
                'compare' => '>='
            )
        );

        $this->tableData->climateFactor = wpenon_get_table_results( 'klimafaktoren202001', $search, array(), true );


    }

    /**
     * Setup building with EC data
     * 
     * @since 1.0.0
     */
    public function setupBuilding() 
    {

        $this->building = new Building( $this->formData->area, $this->formData->flatCount, $this->formData->hotWaterSource );


    }
}