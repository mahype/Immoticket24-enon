<?php

namespace Enev\Schema202302\Calculations\Tabellen;

use Enev\Schema202302\Calculations\Calculation_Exception;

use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

class Mittlere_Belastung_Pufferspeicher_Korrekturfaktor {
    /**
     * Auslegungsvorlauftemperatur.
     * 
     * @var int
     */
    protected int $auslegungsvorlauftemperatur;

    /**
     * Heizungsanlage beheizt.
     * 
     * @var bool
     */
    protected bool $heizungsanlage_beheizt;

    /**
     * Mittlere Belastung (ßhs).
     * 
     * @var float
     */
    protected float $ßhs;

    /**
	 * Tabellendaten aus Tabelle 32.
	 *
	 * @var array
	 */
	protected array $table_data;
    
    /**
     * Konstruktor.
     * 
     * @param int $auslegungsvorlauftemperatur
     * @param bool $heizungsanlage_beheizt
     * @param float $ßhs
     */
    public function __construct( int $auslegungsvorlauftemperatur, bool $heizungsanlage_beheizt, float $ßhs ) {
        $this->table_data =  wpenon_get_table_results( 'mittlere_belastung_pufferspeicher_korrekturfaktor' );

        $this->auslegungsvorlauftemperatur = $auslegungsvorlauftemperatur;
        $this->heizungsanlage_beheizt = $heizungsanlage_beheizt;
        $this->ßhs = $ßhs;
    }

    /**
     * Mittlere Belastung Korrekturfaktor.
     * 
     * @return float 
     * 
     * @throws Calculation_Exception 
     */
    public function fßhs(): float {
        $mittlere_belastung_slugs = $this->mittlere_belastung_slugs();

        if( count( $mittlere_belastung_slugs ) === 1 ) {
            return $this->table_data[ $mittlere_belastung_slugs[0] ][ $this->heizung_col() ];
        }

        $keys = array();
        $values = array();

        foreach( $this->mittlere_belastung_slugs() AS $mittlere_belastung_slug ) {
            $heizung_col = $this->heizung_col();
            $keys[] = $this->table_data[ $mittlere_belastung_slug ]->bhs;
            $values[] = $this->table_data[ $mittlere_belastung_slug ]->$heizung_col;
        }

        return interpolate_value( $this->ßhs, $keys, $values );
    }

    /**
     * Heizungsspalte.
     * 
     * @return string 
     */
    public function heizung_col(): string {
        return $this->heizungsanlage_beheizt ? 'beheizt'  . '_'  .$this->auslegungsvorlauftemperatur : 'unbeheizt' . '_'  .$this->auslegungsvorlauftemperatur;
    }

    /**
     * Mittlere Belastung Slugs.
     * 
     * @return array 
     */
    public function mittlere_belastung_slugs(): array {
        if ( $this->ßhs <= 0.1 ) {
			return array( 'bhs_01' );
		} elseif ( $this->ßhs > 0.1 && $this->ßhs <= 0.2 ) {
			return array( 'bhs_01', 'bhs_02' );
		} elseif ( $this->ßhs > 0.2 && $this->ßhs <= 0.3 ) {
			return array( 'bhs_02', 'bhs_03' );
		} elseif ( $this->ßhs > 0.3 && $this->ßhs <= 0.4 ) {
			return array( 'bhs_03', 'bhs_04' );
		} elseif ( $this->ßhs > 0.4 && $this->ßhs <= 0.5 ) {
			return array( 'bhs_04', 'bhs_05' );
		} elseif ( $this->ßhs > 0.5 && $this->ßhs <= 0.6 ) {
			return array( 'bhs_05', 'bhs_06' );
		} elseif ( $this->ßhs > 0.6 && $this->ßhs <= 0.7 ) {
			return array( 'bhs_06', 'bhs_07' );
		} elseif ( $this->ßhs > 0.7 && $this->ßhs <= 0.8 ) {
			return array( 'bhs_07', 'bhs_08' );
		} elseif ( $this->ßhs > 0.8 && $this->ßhs <= 0.9 ) {
			return array( 'bhs_08', 'bhs_09' );
		} elseif ( $this->ßhs > 0.9 && $this->ßhs <= 1.0 ) {
			return array( 'bhs_09', 'bhs_10' );
		} else {
			return array( 'bhs_10' );
		}
    }
}