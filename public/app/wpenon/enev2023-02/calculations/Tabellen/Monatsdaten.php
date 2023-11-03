<?php

namespace Enev\Schema202302\Calculations\Tabellen;

/**
 * Daten aus der Tabelle Monatsdaten.
 * 
 * @package Enev\Schema202302\Calculations\Tabellen
 */
class Monatsdaten {
    /**
	 * Tabellendaten aus Tabelle 9 bei Einfamilienhaus oder Tabelle 11 bei Mehrfamilienhaus.
	 *
	 * @var array
	 */
	protected array $table_data;

    public function __construct()
    {
        $this->table_data = wpenon_get_table_results( 'monatsdaten' );
    }

    /**
     * 
     * @return array 
     */
    public function monate(): array {
        $monate = array();

        foreach( $this->table_data as $monat ) {
            $monate[] = $monat['monat'];
        }

        return $monate;
    }

    /**
     * Strahlungsfaktor für Fenster.
     * 
     * @param string $monat Monat.
     * @param int $winkel Einbauwinkel des Fensters.
     * @param string $himmelsrichtung Himmelsrichtung des Fensters.
     * 
     * @return float
     */
    public function strahlungsfaktor( string $monat, int $winkel, string $himmelsrichtung ): float {
        $column_name = 'w_' . $himmelsrichtung . '_' . $winkel;
        return $this->table_data[ $monat ]->$column_name;
    }
}