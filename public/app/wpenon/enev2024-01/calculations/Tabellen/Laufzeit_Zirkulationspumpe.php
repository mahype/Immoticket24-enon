<?php

namespace Enev\Schema202401\Calculations\Tabellen;

use function Enev\Schema202401\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Berechnungen zum Laufzeit der Zirkulatuionspumpe.
 *
 * @package
 */
class Laufzeit_Zirkulationspumpe {
    /**
     * Nutfläche des Gebäudes.
     * 
     * @var float
     */
    protected float $nutzflaeche;

    /**
     * Handelt es sich um ein Einfamilienhaus?
     * 
     * @var bool
     */
    protected bool $ist_einfamlienhaus;

    /**
	 * Tabellendaten aus Tabelle 39.
	 *
	 * @var array
	 */
	protected array $table_data;


    public function __construct( float $nutzflaeche, bool $ist_einfamlienhaus )
    {
        $this->nutzflaeche = $nutzflaeche;
        $this->ist_einfamlienhaus = $ist_einfamlienhaus;
        $this->table_data = $this->table_data = wpenon_get_table_results( 'laufzeit_zirkulationspumpe' );
    }

    public function z(): float {
        $keys = $values = array(); // Reset key and value arrays.

        if( ! $this->ist_einfamlienhaus && $this->nutzflaeche > 5000 ) {
            return 24;
        }

        $nutzflaeche_slugs = $this->ist_einfamlienhaus ? $this->nutzflaeche_slugs_efh() : $this->nutzflaeche_slugs_mfh();
        $spaltenname = $this->ist_einfamlienhaus ? 'efh' : 'mfh';

        foreach ( $nutzflaeche_slugs as $nutzflaeche_slug ) {			
            $keys[]   = (float) $this->table_data[ $nutzflaeche_slug ]->angf_m2;     
            $values[] = (float) $this->table_data[ $nutzflaeche_slug ]->$spaltenname; // in Abhängikeit der Heizlast und Übergabesystems (Heizkörper 10k; sichere Seite), Fußbodenheizung  
        }

        $interpolated_value = interpolate_value( $this->nutzflaeche, $keys, $values );

        return $interpolated_value;
    }

    protected function nutzflaeche_slugs_efh(): array {        
        if( $this->nutzflaeche <= 50 ) {
            return array( 'lz_50' );
        } elseif( $this->nutzflaeche > 50 && $this->nutzflaeche <= 100 ) {
            return array( 'lz_50', 'lz_100' );
        } elseif( $this->nutzflaeche > 100 && $this->nutzflaeche <= 150 ) {
            return array( 'lz_100', 'lz_150' );
        } elseif( $this->nutzflaeche > 150 && $this->nutzflaeche <= 200 ) {
            return array( 'lz_150', 'lz_200' );
        } elseif( $this->nutzflaeche > 200 && $this->nutzflaeche <= 300 ) {
            return array( 'lz_200', 'lz_300' );
        } elseif( $this->nutzflaeche > 300 && $this->nutzflaeche <= 400 ) {
            return array( 'lz_300', 'lz_400' );
        } elseif( $this->nutzflaeche > 400 && $this->nutzflaeche <= 500 ) {
            return array( 'lz_400', 'lz_500' );
        } elseif( $this->nutzflaeche > 500 ) {
            return array( 'lz_500' );
        }
    }

    protected function nutzflaeche_slugs_mfh(): array {
        if( $this->nutzflaeche <= 50 ) {
            return array( 'lz_50' );
        } elseif( $this->nutzflaeche > 50 && $this->nutzflaeche <= 100 ) {
            return array( 'lz_50', 'lz_100' );
        } elseif( $this->nutzflaeche > 100 && $this->nutzflaeche <= 150 ) {
            return array( 'lz_100', 'lz_150' );
        } elseif( $this->nutzflaeche > 150 && $this->nutzflaeche <= 200 ) {
            return array( 'lz_150', 'lz_200' );
        } elseif( $this->nutzflaeche > 200 && $this->nutzflaeche <= 300 ) {
            return array( 'lz_200', 'lz_300' );
        } elseif( $this->nutzflaeche > 300 && $this->nutzflaeche <= 400 ) {
            return array( 'lz_300', 'lz_400' );
        } elseif( $this->nutzflaeche > 400 && $this->nutzflaeche <= 500 ) {
            return array( 'lz_400', 'lz_500' );
        } elseif( $this->nutzflaeche > 500 && $this->nutzflaeche <= 600 ) {
            return array( 'lz_500', 'lz_600' );
        } elseif( $this->nutzflaeche > 600 && $this->nutzflaeche <= 700 ) {
            return array( 'lz_600', 'lz_700' );
        } elseif( $this->nutzflaeche > 700 && $this->nutzflaeche <= 800 ) {
            return array( 'lz_700', 'lz_800' );
        } elseif( $this->nutzflaeche > 800 && $this->nutzflaeche <= 900 ) {
            return array( 'lz_800', 'lz_900' );
        } elseif( $this->nutzflaeche > 900 && $this->nutzflaeche <= 1000 ) {
            return array( 'lz_900', 'lz_1000' );
        } elseif( $this->nutzflaeche > 1000 && $this->nutzflaeche <= 2000 ) {
            return array( 'lz_1000', 'lz_2000' );
        } elseif( $this->nutzflaeche > 2000 && $this->nutzflaeche <= 3000 ) {
            return array( 'lz_2000', 'lz_3000' );
        } elseif( $this->nutzflaeche > 3000 && $this->nutzflaeche <= 4000 ) {
            return array( 'lz_3000', 'lz_4000' );
        } elseif( $this->nutzflaeche > 4000 && $this->nutzflaeche <= 5000 ) {
            return array( 'lz_4000', 'lz_5000' );
        } elseif( $this->nutzflaeche > 5000 ) {
            return array( 'lz_ueber_5000' );
        }
    }
}