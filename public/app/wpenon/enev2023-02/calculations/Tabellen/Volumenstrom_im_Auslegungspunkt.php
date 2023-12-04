<?php

namespace Enev\Schema202302\Calculations\Tabellen;

use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Volumenstrom im Auslegungspunkt (Tabelle 38)
 *
 * @package
 */
class Volumenstrom_im_Auslegungspunkt {
    /**
     * Heizlast des Gebäudes.
     * 
     * @var float
     */
    protected float $heizlast;


    /**
	 * Tabellendaten aus Tabelle 39.
	 *
	 * @var array
	 */
	protected array $table_data;


    public function __construct( float $heizlast )
    {
        $this->heizlast = $heizlast;
        $this->table_data = $this->table_data = wpenon_get_table_results( 'volumenstrom_im_auslegungspunkt' );
    }

    public function V(): float {
        $keys = $values = array(); // Reset key and value arrays.

        foreach ( $this->heizlast_slugs() as $heizlast_slug ) {			
            $keys[]   = floatval( $this->table_data[ $heizlast_slug ]->kw );            
            $values[] = (float) $this->table_data[ $heizlast_slug ]->hk_10_k; // in Abhängikeit der Heizlast und Übergabesystems (Heizkörper 10k; sichere Seite), Fußbodenheizung  
        }

        $interpolated_value = interpolate_value( $this->heizlast, $keys, $values );

        return $interpolated_value;
    }


    protected function heizlast_slugs(): array {
         if( $this->heizlast <= 2.5 ) {
             return array('dw_2_5' );
         } else if( $this->heizlast > 2.5 && $this->heizlast <= 5 ) {
            return array('dw_2_5', 'dw_5' );
         } else if( $this->heizlast > 5 && $this->heizlast <= 10 ) {
            return array('dw_5', 'dw_10' );
         } else if( $this->heizlast > 10 && $this->heizlast <= 20 ) {
            return array('dw_10', 'dw_20' );
         } else if( $this->heizlast > 20 && $this->heizlast <= 30 ) {
            return array('dw_20', 'dw_30' );
         } else if( $this->heizlast > 30 && $this->heizlast <= 40 ) {
            return array('dw_30', 'dw_40' );
         } else if( $this->heizlast > 40 && $this->heizlast <= 50 ) {
            return array('dw_40', 'dw_50' );
         } else if( $this->heizlast > 50 && $this->heizlast <= 60 ) {
            return array('dw_50', 'dw_60' );
         } else if( $this->heizlast > 60 && $this->heizlast <= 70 ) {
            return array('dw_60', 'dw_70' );
         } else if( $this->heizlast > 70 && $this->heizlast <= 80 ) {
            return array('dw_70', 'dw_80' );
         } else if( $this->heizlast > 80 && $this->heizlast <= 90 ) {
            return array('dw_80', 'dw_90' );
         } else if( $this->heizlast > 90 && $this->heizlast <= 100 ) {
            return array('dw_90', 'dw_100' );
         } else if( $this->heizlast > 100 && $this->heizlast <= 110 ) {
            return array('dw_100', 'dw_110' );
         } else if( $this->heizlast > 110 && $this->heizlast <= 120 ) {
            return array('dw_110', 'dw_120' );
         } else if( $this->heizlast > 120 && $this->heizlast <= 130 ) {
            return array('dw_120', 'dw_130' );
         } else if( $this->heizlast > 130 && $this->heizlast <= 140 ) {
            return array('dw_130', 'dw_140' );
         } else if( $this->heizlast > 140 && $this->heizlast <= 150 ) {
            return array('dw_140', 'dw_150' );
         } else if( $this->heizlast > 150 && $this->heizlast <= 160 ) {
            return array('dw_150', 'dw_160' );
         } else if( $this->heizlast > 160 && $this->heizlast <= 170 ) {
            return array('dw_160', 'dw_170' );
         } else if( $this->heizlast > 170 && $this->heizlast <= 180 ) {
            return array('dw_170', 'dw_180' );
         } else if( $this->heizlast > 180 && $this->heizlast <= 190 ) {
            return array('dw_180', 'dw_190' );
         } else if( $this->heizlast > 190 && $this->heizlast <= 200 ) {
            return array('dw_190', 'dw_200' );
         } else if( $this->heizlast > 200 && $this->heizlast <= 300 ) {
            return array('dw_200', 'dw_300' );
         } else if( $this->heizlast > 300 && $this->heizlast <= 400 ) {
            return array('dw_300', 'dw_400' );
         } else if( $this->heizlast > 400 ) {
            return array('dw_400' );
         }
    }
}