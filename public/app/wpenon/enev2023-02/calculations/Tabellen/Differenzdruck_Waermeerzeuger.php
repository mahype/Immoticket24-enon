<?php

namespace Enev\Schema202302\Calculations\Tabellen;

use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;

use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Berechnungen zum Luftwechsel.
 *
 * @package
 */
class Differenzdruck_Waermeerzeuger {
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
        $this->table_data = $this->table_data = wpenon_get_table_results( 'differenzdruck_waermeerzeuger' );
    }

    public function pg(): float {
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
         } else if( $this->heizlast > 30 && $this->heizlast <= 34 ) {
            return array('dw_30', 'dw_34' );
         } else if( $this->heizlast > 34 ) {
            return array('dw_34' );
         }
    }
}