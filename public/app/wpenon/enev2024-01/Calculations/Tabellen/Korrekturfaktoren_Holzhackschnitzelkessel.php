<?php

namespace Enev\Schema202302\Calculations\Tabellen;

use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Korrekturfaktor aus Tabelle 86.
 *
 * @package
 */
class Korrekturfaktoren_Holzhackschnitzelkessel {
    /**
     * pn
     * 
     * @var float
     */
    protected float $pn;

    /**
     * ßhg?
     * 
     * @var float
     */
    protected float $ßhg;

    /**
     * Tabellendaten aus Tabelle 86.
     *
     * @var array
     */
    protected array $table_data;

    public function __construct( float $pn, float $ßhg ) {
        $this->pn = $pn;
        $this->ßhg = $ßhg;
        $this->table_data = wpenon_get_table_results( 'korrekturfaktoren_holzhackschnitzelkessel' );
    }

    public function fphgaux(): float {
		$zeilen_keys   = array();
		$zeilen_values = array();

		foreach ( $this->pn_slugs() as $pn_slug ) {
			$spalten_keys   = array();
			$spalten_values = array();

			foreach ( $this->ßhg_slugs() as $ßhg_slug ) {
				$spalten_keys[]   = $ßhg_slug;
				$spalten_teile  = explode( '.', $ßhg_slug );

                if( ! isset( $spalten_teile[1] ) ) {
					$spalten_teile[1] = 0;
				}
                
				$spalten_name = 'bwk_' . $spalten_teile[0] . '_' . $spalten_teile[1];
				$spalten_values[] = $this->table_data[ $pn_slug ]->$spalten_name;
			}

         	$zeilen_keys[] = $pn_slug;
			$interpolierter_wert = interpolate_value( $this->ßhg, $spalten_keys, $spalten_values );
			$zeilen_values[] = $interpolierter_wert;
		}

		$interpolierter_wert = interpolate_value( $this->pn, $zeilen_keys, $zeilen_values );
      	return $interpolierter_wert;
	}

    protected function pn_slugs(): array {
        if( $this->pn <= 5 ) {
            return array( 5 );
        } elseif( $this->pn > 5 && $this->pn <= 10 ) {
            return array(  5, 10 );
        } elseif( $this->pn > 10 && $this->pn <= 20 ) {
            return array( 10, 20 );
        } elseif( $this->pn > 20 && $this->pn <= 40 ) {
            return array( 20, 40 );
        } elseif( $this->pn > 40 && $this->pn <= 95 ) {
            return array( 40, 95 );
        } elseif( $this->pn > 95 && $this->pn <= 270 ) {
            return array( 95, 270 );
        } elseif( $this->pn > 270 && $this->pn <= 400 ) {
            return array( 270, 400 );
        } else {
            return array( 400 );
        }
    }
    
    protected function ßhg_slugs(): array {
        if ( $this->ßhg <= 0.1 ) {
			return array( 0.1 );
		} elseif ( $this->ßhg > 0.1 && $this->ßhg <= 0.2 ) {
			return array( 0.1, 0.2 );
		} elseif ( $this->ßhg > 0.2 && $this->ßhg <= 0.3 ) {
			return array( 0.2, 0.3 );
		} elseif ( $this->ßhg > 0.3 && $this->ßhg <= 0.4 ) {
			return array( 0.3, 0.4 );
		} elseif ( $this->ßhg > 0.4 && $this->ßhg <= 0.5 ) {
			return array( 0.4, 0.5 );
		} elseif ( $this->ßhg > 0.5 && $this->ßhg <= 0.6 ) {
			return array( 0.5, 0.6 );
		} elseif ( $this->ßhg > 0.6 && $this->ßhg <= 0.7 ) {
			return array( 0.6, 0.7 );
		} elseif ( $this->ßhg > 0.7 && $this->ßhg <= 0.8 ) {
			return array( 0.7, 0.8 );
		} elseif ( $this->ßhg > 0.8 && $this->ßhg <= 0.9 ) {
			return array( 0.8, 0.9 );
		} elseif ( $this->ßhg > 0.9 && $this->ßhg <= 1.0 ) {
			return array( 0.9, 1.0 );
		} else {
			return array( 1.0 );
		}
    }
}