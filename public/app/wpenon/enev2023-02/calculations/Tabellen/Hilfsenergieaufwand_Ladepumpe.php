<?php

namespace Enev\Schema202302\Calculations\Tabellen;

use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Berechnungen zum Hilfsenergieaufwand_Ladepumpe.
 *
 * @package
 */
class Hilfsenergieaufwand_Ladepumpe {
    /**
     * @var float
     */
    protected float $Vws;


    /**
	 * Tabellendaten aus Tabelle 39.
	 *
	 * @var array
	 */
	protected array $table_data;


    public function __construct( float $Vws )
    {
        $this->Vws = $Vws;
        $this->table_data = $this->table_data = wpenon_get_table_results( 'hilfsenergieaufwand_ladepumpe' );
    }

    public function Wws0(): float {
        $keys = $values = array(); // Reset key and value arrays.

        $slugs = $this->Vws_slugs();

        foreach ( $slugs as $slug ) {			
            $keys[]   = (float) $this->table_data[ $slug ]->speicher;     
            $values[] = (float) $this->table_data[ $slug ]->energie; // in Abhängikeit der Heizlast und Übergabesystems (Heizkörper 10k; sichere Seite), Fußbodenheizung  
        }

        $interpolated_value = interpolate_value( $this->Vws, $keys, $values );

        return $interpolated_value;
    }

    protected function Vws_slugs(): array {
        if( $this->Vws <= 5 ) {
            return array( 'v_5' ); 
        } elseif( $this->Vws > 5 && $this->Vws < 10 ) {
            return array( 'v_5', 'v_10' );
        } elseif( $this->Vws >= 10 && $this->Vws < 20 ) {
            return array( 'v_10', 'v_20' );
        } elseif( $this->Vws >= 20 && $this->Vws < 30 ) {
            return array( 'v_20', 'v_30' );
        } elseif( $this->Vws >= 30 && $this->Vws < 40 ) {
            return array( 'v_30', 'v_40' );
        } elseif( $this->Vws >= 40 && $this->Vws < 50 ) {
            return array( 'v_40', 'v_50' );
        } elseif( $this->Vws >= 50 && $this->Vws < 60 ) {
            return array( 'v_50', 'v_60' );
        } elseif( $this->Vws >= 60 && $this->Vws < 70 ) {
            return array( 'v_60', 'v_70' );
        } elseif( $this->Vws >= 70 && $this->Vws < 80 ) {
            return array( 'v_70', 'v_80' );
        } elseif( $this->Vws >= 80 && $this->Vws < 90 ) {
            return array( 'v_80', 'v_90' );
        } elseif( $this->Vws >= 90 && $this->Vws < 100 ) {
            return array( 'v_90', 'v_100' );
        } elseif( $this->Vws >= 100 && $this->Vws < 150 ) {
            return array( 'v_100', 'v_150' );
        } elseif( $this->Vws >= 150 && $this->Vws < 200 ) {
            return array( 'v_150', 'v_200' );
        } elseif( $this->Vws >= 200 && $this->Vws < 300 ) {
            return array( 'v_200', 'v_300' );
        } elseif( $this->Vws >= 300 && $this->Vws < 400 ) {
            return array( 'v_300', 'v_400' );
        } elseif( $this->Vws >= 400 && $this->Vws < 500 ) {
            return array( 'v_400', 'v_500' );
        } elseif( $this->Vws >= 500 && $this->Vws < 600 ) {
            return array( 'v_500', 'v_600' );
        } elseif( $this->Vws >= 600 && $this->Vws < 700 ) {
            return array( 'v_600', 'v_700' );
        } elseif( $this->Vws >= 700 && $this->Vws < 800 ) {
            return array( 'v_700', 'v_800' );
        } elseif( $this->Vws >= 800 && $this->Vws < 900 ) {
            return array( 'v_800', 'v_900' );
        } elseif( $this->Vws >= 900 && $this->Vws < 1000 ) {
            return array( 'v_900', 'v_1000' );
        } elseif( $this->Vws >= 1000 && $this->Vws < 1500 ) {
            return array( 'v_1000', 'v_1500' );
        } elseif( $this->Vws >= 1500 ) {
            return array( 'v_ueber_1500' );
        }
    }
}