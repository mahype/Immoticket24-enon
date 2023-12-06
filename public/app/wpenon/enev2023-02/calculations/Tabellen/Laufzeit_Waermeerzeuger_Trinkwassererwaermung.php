<?php

namespace Enev\Schema202302\Calculations\Tabellen;

use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Korrekturfaktor aus Tabelle 86.
 *
 * @package
 */
class Laufzeit_Waermeerzeuger_Trinkwassererwaermung {
    /**
     * ewd * ews
     * 
     * @var float
     */
    protected float $ewd_ews;

    /**
     * Handelt es sich um ein bestehendes Gebäude?
     * 
     * @var float
     */
    protected bool $bestehendes_gebaeude;

    /**
     * Tabellendaten aus Tabelle 86.
     *
     * @var array
     */
    protected array $table_data;

    public function __construct( float $ewd_ews, bool $bestehendes_gebaeude = true ) {
        $this->ewd_ews = $ewd_ews;
        $this->bestehendes_gebaeude = $bestehendes_gebaeude;
        $this->table_data = wpenon_get_table_results( 'lz_waermeerzeugers_trinkwassererwaermung' );
    }

    public function twpn0(): float {
        $keys = $values = array(); // Reset key and value arrays.

        $bestand_slug = $this->bestand_slug();
        $ewd_ews_slugs = $this->ewd_ews_slugs();

        foreach ( $ewd_ews_slugs as $ewd_ews_slug ) {
            $spalten_teile  = explode( '.', $ewd_ews_slug );

            $spalten_name = 'aufwand_' . $spalten_teile[0];
            $spalten_name .= isset( $spalten_teile[1] ) ? '_' . $spalten_teile[1] : '';

            $keys[]   = (float) $ewd_ews_slug;     
            $values[] = (float) $this->table_data[ $bestand_slug ]->$spalten_name; // in Abhängikeit der Heizlast und Übergabesystems (Heizkörper 10k; sichere Seite), Fußbodenheizung  
        }

        $interpolated_value = interpolate_value( $this->ewd_ews, $keys, $values );
        return $interpolated_value;
    }

    protected function bestand_slug(): string {
        return $this->bestehendes_gebaeude ? 'anlage_bestand' : 'anlage_neubau';
    }

    protected function ewd_ews_slugs(): array {
        if( $this->ewd_ews <= 1 ) {
            return array( 1 );
        } elseif( $this->ewd_ews > 1 && $this->ewd_ews <= 1.2 ) {
            return array( 1, 1.2 );
        } elseif( $this->ewd_ews > 1.2 && $this->ewd_ews <= 2 ) {
            return array( 1.2, 2 );
        } elseif( $this->ewd_ews > 2 && $this->ewd_ews <= 2.5 ) {
            return array( 2, 2.5 );
        } elseif( $this->ewd_ews > 2.5 && $this->ewd_ews <= 3 ) {
            return array( 2.5, 3 );
        } elseif( $this->ewd_ews > 3 && $this->ewd_ews <= 3.5 ) {
            return array( 3, 3.5 );
        } elseif( $this->ewd_ews > 3.5 && $this->ewd_ews <= 4 ) {
            return array( 3.5, 4 );
        } elseif( $this->ewd_ews > 4 ) {
            return array( 4 );
        }
    }
}