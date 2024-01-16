<?php

namespace Enev\Schema202401\Calculations\Tabellen;

/**
 * Daten aus der Tabelle Monatsdaten.
 * 
 * @package Enev\Schema202401\Calculations\Tabellen
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
        $this->table_data = wpenon_get_table_results( 'monate' );
    }

    /**
     * 
     * @return array 
     */
    public function monate(): array {
        $monate = array();

        foreach( $this->table_data as $monat => $data ) {
            $monate[] = $monat;
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
        $column_name = 'w_' . $himmelsrichtung . $winkel;
        return $this->table_data[ $monat ]->$column_name;
    }

    /**
     * Tage des Monats.
     * 
     * @param mixed $monat 
     * @return mixed 
     */
    public function tage( string $monat ) {
        return $this->table_data[ $monat ]->tage;
    }

    /**
     * Temperatur des Monats.
     * 
     * @param float $monat
     * 
     * @return float
     */
    public function temperatur( string $monat ): float {
        return $this->table_data[ $monat ]->temperatur;
    }

    /**
     * Name des Monats.
     * 
     * @param string Monatsname.
     */
    public function name( string $monat ): string {
        return $this->table_data[ $monat ]->name;
    }
}