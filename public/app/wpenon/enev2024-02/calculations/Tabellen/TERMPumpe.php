<?php

namespace Enev\Schema202402\Calculations\Tabellen;

use function Enev\Schema202402\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Berechnungen zum TERMpumpe.
 *
 * @package
 */
class TERMpumpe {
    /**
     * Heizlast des Gebäudes.
     * 
     * @var float
     */
    protected float $ßhd;

    /**
     * Baujahr der Heizung.
     * 
     * @var int
     */
    protected int $baujahr_heizung;


    /**
	 * Tabellendaten aus Tabelle 39.
	 *
	 * @var array
	 */
	protected array $table_data;


    public function __construct( float $ßhd, int $baujahr_heizung )
    {
        $this->ßhd = $ßhd;
        $this->baujahr_heizung = $baujahr_heizung;
        $this->table_data = $this->table_data = wpenon_get_table_results( 'termpumpe' );
    }

    public function TERMpumpe(): float {
        $keys = $values = array(); // Reset key and value arrays.

        foreach ( $this->ßhd_slugs() as $ßhd_slugs ) {			
            $keys[]   = floatval( $this->table_data[ $ßhd_slugs ]->b );
            $spalte = $this->baujahr_heizung <= 1994 ? 'ungeregelt': 'd_p_const';
            $values[] = (float) $this->table_data[ $ßhd_slugs ]->$spalte;
        }

        $interpolated_value = interpolate_value( $this->ßhd, $keys, $values );

        return $interpolated_value;
    }

    public function ßhd_slugs(): array {
         if( $this->ßhd <= 0.1 ) {
             return array( 'tp_0_1' );
         } elseif( $this->ßhd > 0.1 && $this->ßhd <= 0.2 ) {
            return array( 'tp_0_1', 'tp_0_2' );
         } elseif( $this->ßhd > 0.2 && $this->ßhd <= 0.3 ) {
            return array( 'tp_0_2', 'tp_0_3' );
         } elseif( $this->ßhd > 0.3 && $this->ßhd <= 0.4 ) {
            return array( 'tp_0_3', 'tp_0_4' );
         } elseif( $this->ßhd > 0.4 && $this->ßhd <= 0.5 ) {
            return array( 'tp_0_4', 'tp_0_5' );
         } elseif( $this->ßhd > 0.5 && $this->ßhd <= 0.6 ) {
            return array( 'tp_0_5', 'tp_0_6' );
         } elseif( $this->ßhd > 0.6 && $this->ßhd <= 0.7 ) {
            return array( 'tp_0_6', 'tp_0_7' );
         } elseif( $this->ßhd > 0.7 && $this->ßhd <= 0.8 ) {
            return array( 'tp_0_7', 'tp_0_8' );
         } elseif( $this->ßhd > 0.8 && $this->ßhd <= 0.9 ) {
            return array( 'tp_0_8', 'tp_0_9' );
         } elseif( $this->ßhd > 0.9 && $this->ßhd <= 1 ) {
            return array( 'tp_0_9', 'tp_1' );
         } elseif( $this->ßhd > 1 ) {
            return array( 'tp_1_0' );
         }
    }

}