<?php

namespace Enev\Schema202401\Calculations\Tabellen;

use function Enev\Schema202401\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Berechnungen zum Differenzdruck_Leitungslaenge.
 *
 * @package
 */
class Differenzdruck_Leitungslaenge {
    /**
     * LmaxTWW des Gebäudes.
     * 
     * @var float
     */
    protected float $LmaxTWW;


    /**
	 * Tabellendaten aus Tabelle 39.
	 *
	 * @var array
	 */
	protected array $table_data;


    public function __construct( float $LmaxTWW )
    {
        $this->LmaxTWW = $LmaxTWW;
        $this->table_data = $this->table_data = wpenon_get_table_results( 'differenzdruck_leitungslaenge' );
    }

    public function AP(): float {
        $keys = $values = array(); // Reset key and value arrays.

        foreach ( $this->LmaxTWWSlugs() as $LmaxTWWSlug ) {			
            $keys[]   = floatval( $LmaxTWWSlug );            
            $values[] = (float) $this->table_data[ $LmaxTWWSlug ]->durchflusssystem; // in Abhängikeit der Heizlast und Übergabesystems (Heizkörper 10k; sichere Seite), Fußbodenheizung  
        }

        $interpolated_value = interpolate_value( $this->LmaxTWW, $keys, $values );

        return $interpolated_value;
    }

    protected function LmaxTWWSlugs(): array {
         if( $this->LmaxTWW <= 10 ) {
            return array( 10 );
         } elseif( $this->LmaxTWW > 10 && $this->LmaxTWW <= 25 ) {
            return array( 10, 25 );
         } elseif( $this->LmaxTWW > 25 && $this->LmaxTWW <= 50 ) {
            return array( 25, 50 );
         } elseif( $this->LmaxTWW > 50 && $this->LmaxTWW <= 60 ) {
            return array( 50, 60 );
         } elseif( $this->LmaxTWW > 60 && $this->LmaxTWW <= 70 ) {
            return array( 60, 70 );
         } elseif( $this->LmaxTWW > 70 && $this->LmaxTWW <= 80 ) {
            return array( 70, 80 );
         } elseif( $this->LmaxTWW > 80 && $this->LmaxTWW <= 90 ) {
            return array( 80, 90 );
         } elseif( $this->LmaxTWW > 90 && $this->LmaxTWW <= 100 ) {
            return array( 90, 100 );
         } elseif( $this->LmaxTWW > 100 && $this->LmaxTWW <= 110 ) {
            return array( 100, 110 );
         } elseif( $this->LmaxTWW > 110 && $this->LmaxTWW <= 120 ) {
            return array( 110, 120 );
         } elseif( $this->LmaxTWW > 120 && $this->LmaxTWW <= 130 ) {
            return array( 120, 130 );
         } elseif( $this->LmaxTWW > 130 && $this->LmaxTWW <= 140 ) {
            return array( 130, 140 );
         } elseif( $this->LmaxTWW > 140 && $this->LmaxTWW <= 150 ) {
            return array( 140, 150 );
         } elseif( $this->LmaxTWW > 150 && $this->LmaxTWW <= 160 ) {
            return array( 150, 160 );
         } elseif( $this->LmaxTWW > 160 && $this->LmaxTWW <= 170 ) {
            return array( 160, 170 );
         } elseif( $this->LmaxTWW > 170 && $this->LmaxTWW <= 180 ) {
            return array( 170, 180 );
         } elseif( $this->LmaxTWW > 180 && $this->LmaxTWW <= 190 ) {
            return array( 180, 190 );
         } elseif( $this->LmaxTWW > 190 && $this->LmaxTWW <= 200 ) {
            return array( 190, 200 );
         } elseif( $this->LmaxTWW > 200 && $this->LmaxTWW <= 250 ) {
            return array( 200, 250 );
         } elseif( $this->LmaxTWW > 250 && $this->LmaxTWW <= 300 ) {
            return array( 250, 300 );
         } elseif( $this->LmaxTWW > 300 && $this->LmaxTWW <= 350 ) {
            return array( 300, 350 );
         } elseif( $this->LmaxTWW > 350 && $this->LmaxTWW <= 400 ) {
            return array( 350, 400 );
         } elseif( $this->LmaxTWW > 400 && $this->LmaxTWW <= 450 ) {
            return array( 400, 450 );
         } elseif( $this->LmaxTWW > 450 && $this->LmaxTWW <= 500 ) {
            return array( 450, 500 );
         } elseif( $this->LmaxTWW > 500 ) {
            return array( 500 );
         }
    }
}