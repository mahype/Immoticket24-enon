<?php

namespace Enev\Schema202401\Calculations\Tabellen;

use function Enev\Schema202401\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Korrekturfaktor aus Tabelle 96.
 *
 * @package
 */
class Hilfsenergie_Primaerseite_Sole_Wasser_Waermepumpen {
    /**
     * pn
     * 
     * @var float
     */
    protected float $pn;

    /**
     * Heizgrenztemperatur?
     * 
     * @var float
     */
    protected float $heizgrenztemperatur;

    /**
     * Tabellendaten aus Tabelle 96.
     *
     * @var array
     */
    protected array $table_data;

    public function __construct( float $pn, float $heizgrenztemperatur ) {
        $this->pn = $pn;
        $this->heizgrenztemperatur = $heizgrenztemperatur;
        $this->table_data = wpenon_get_table_results( 'hilfsenergie_primaer_sole_www' );
    }

    public function Whg(): float {
		$zeilen_keys   = array();
		$zeilen_values = array();

		foreach ( $this->pn_slugs() as $pn_slug ) {			
            $spalte = $this->heizgrenztemperatur_slug();

         	$zeilen_keys[] = $pn_slug;            
			$zeilen_values[] = $this->table_data[$pn_slug]->$spalte;
		}

		$interpolierter_wert = interpolate_value( $this->pn, $zeilen_keys, $zeilen_values );
      	return $interpolierter_wert;
	}

    protected function pn_slugs(): array {       
        if( $this->pn <= 2 ) {
            return array( 2 );
        } elseif( $this->pn > 2 && $this->pn <= 5 ) {
            return array( 2, 5 );
        } elseif( $this->pn > 5 && $this->pn <= 10 ) {
            return array( 5, 10 );
        } elseif( $this->pn > 10 && $this->pn <= 15 ) {
            return array( 10, 15 );
        } elseif( $this->pn > 15 && $this->pn <= 20 ) {
            return array( 15, 20 );
        } elseif( $this->pn > 20 && $this->pn <= 25 ) {
            return array( 20, 25 );
        } elseif( $this->pn > 25 && $this->pn <= 30 ) {
            return array( 25, 30 );
        } elseif( $this->pn > 30 && $this->pn <= 35 ) {
            return array( 30, 35 );
        } elseif( $this->pn > 35 && $this->pn <= 40 ) {
            return array( 35, 40 );
        } elseif( $this->pn > 40 && $this->pn <= 50 ) {
            return array( 40, 50 );
        } elseif( $this->pn > 50 && $this->pn <= 60 ) {
            return array( 50, 60 );
        } elseif( $this->pn > 60 && $this->pn <= 70 ) {
            return array( 60, 70 );
        } elseif( $this->pn > 70 && $this->pn <= 80 ) {
            return array( 70, 80 );
        } elseif( $this->pn > 80 && $this->pn <= 90 ) {
            return array( 80, 90 );
        } elseif( $this->pn > 90 && $this->pn <= 100 ) {
            return array( 90, 100 );
        } elseif( $this->pn > 100 && $this->pn <= 120 ) {
            return array( 100, 120 );
        } elseif( $this->pn > 120 && $this->pn <= 140 ) {
            return array( 120, 140 );
        } elseif( $this->pn > 140 && $this->pn <= 160 ) {
            return array( 140, 160 );
        } elseif( $this->pn > 160 && $this->pn <= 180 ) {
            return array( 160, 180 );
        } elseif( $this->pn > 180 && $this->pn <= 200 ) {
            return array( 180, 200 );
        } elseif( $this->pn > 200 && $this->pn <= 300 ) {
            return array( 200, 300 );
        } elseif( $this->pn > 300 && $this->pn <= 400 ) {
            return array( 300, 400 );
        } elseif( $this->pn > 400 ) {
            return array( 400 );
        }
    }
    
    protected function heizgrenztemperatur_slug(): string {
        return 'grenze_' . $this->heizgrenztemperatur;
    }
}