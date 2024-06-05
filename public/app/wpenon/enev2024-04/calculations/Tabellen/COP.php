<?php

namespace Enev\Schema202404\Calculations\Tabellen;

use Enev\Schema202404\Calculations\Calculation_Exception;

use function Enev\Schema202404\Calculations\Helfer\interpolate_value;

require_once dirname(__DIR__) . '/Helfer/Math.php';

/**
 * Berechnung der COP Werte für Wärmepumpen aus Tabelle 89.
 *
 * @package
 */
class COP
{
    /**
     * Erzeuger.
     * 
     * @var string
     */
    protected string $erzeuger;

    /**
     * Zielwert der Interpolation.
     * 
     * @var float
     */
    protected float $zielwert;

    /**
     * Tabellendaten mit den COP Werten (Tabelle 89).
     *
     * @var array
     */
    protected array $table_data_cop;

    /**
     * Tabellendaten mit den Korrekturfaktoren (Tabelle 90).
     * 
     * @var array
     */
    protected array $table_data_endenergie_luft_wasser;

    /**
     * Konstruktor.
     *
     * @return void
     */
    public function __construct(string $erzeuger, float $θvl)
    {
        $this->erzeuger = $erzeuger;
        $this->zielwert = $θvl;
        $this->table_data_cop = wpenon_get_table_results('cop_werte');
        $this->table_data_endenergie_luft_wasser = wpenon_get_table_results('endenergie_luft_wasser_waermepumpen'); // Werden zur Zeit nicht gebraucht
    }

    /**
     * COPtk -7°.
     */
    public function COPtk_7(): float
    {
        return $this->wert('lww_w_7');
    }

    /**
     * COPtk 2°.
     */
    public function COPtk2(): float
    {
        return $this->wert('lww_w2');
    }

    /**
     * COPtk 7°.
     */
    public function COPtk7(): float
    {
        return $this->wert('lww_w7');
    }

    /**
     * COPtk. 
     * 
     * @param float $θvl
     */
    public function COPtk(): float
    {
        if ($this->erzeuger === 'waermepumpewasser') {
            return $this->wert('www');
        } elseif ($this->erzeuger === 'waermepumpeerde') {
            return $this->wert('sww');
        } else {
            throw new Calculation_Exception(sprintf('COPtk für "%s" kann nicht ermittelt werden.', $this->erzeuger));
        }
    }


    /**
     * Wert aus beliebiger Spalte.
     * 
     * @spalte string
     */
    protected function wert(string $spalte): float
    {
        $zeilen = $this->zeilen();

        if (count($zeilen) === 1) {
            $zeile = $zeilen[0];
            return $this->table_data_cop[$zeile]->$spalte;
        }

        $keys = $zeilen;

        foreach ($zeilen as $zeile) {
            $values[] = $this->table_data_cop[$zeile]->$spalte;
        }

        return interpolate_value($this->zielwert, $keys, $values);
    }

    /**
     * Endendergie bei -7°.
     * 
     * @param mixed $monat 
     * @return float 
     */
    public function COP_7_Endenergie_Monat($monat): float
    {
        return floatval($this->table_data_endenergie_luft_wasser[$monat]->lww_w_7);
    }

    /**
     * Endendergie bei 2°.
     * 
     * @param mixed $monat 
     * @return float 
     */
    public function COP2_Endenergie_Monat($monat): float
    {
        return floatval($this->table_data_endenergie_luft_wasser[$monat]->lww_w2);
    }

    /**
     * Endendergie bei 7°.
     * 
     * @param mixed $monat 
     * @return float 
     */
    public function COP7_Endenergie_Monat($monat): float
    {
        return floatval($this->table_data_endenergie_luft_wasser[$monat]->lww_w7);
    }

    /**
     * Zeilen zur Interpolation.
     * 
     * @return array
     */
    protected function zeilen(): array
    {
        if ($this->zielwert <= 30) {
            return array(30);
        } elseif ($this->zielwert > 30 && $this->zielwert <= 31) {
            return array(30, 31);
        } elseif ($this->zielwert > 31 && $this->zielwert <= 33) {
            return array(31, 33);
        } elseif ($this->zielwert > 33 && $this->zielwert <= 35) {
            return array(33, 35);
        } elseif ($this->zielwert > 35 && $this->zielwert <= 37) {
            return array(35, 37);
        } elseif ($this->zielwert > 37 && $this->zielwert <= 39) {
            return array(37, 39);
        } elseif ($this->zielwert > 39 && $this->zielwert <= 40) {
            return array(39, 40);
        } elseif ($this->zielwert > 40 && $this->zielwert <= 41) {
            return array(40, 41);
        } elseif ($this->zielwert > 41 && $this->zielwert <= 42) {
            return array(41, 42);
        } elseif ($this->zielwert > 42 && $this->zielwert <= 45) {
            return array(42, 45);
        } elseif ($this->zielwert > 45 && $this->zielwert <= 47) {
            return array(45, 47);
        } elseif ($this->zielwert > 47 && $this->zielwert <= 49) {
            return array(47, 49);
        } elseif ($this->zielwert > 49 && $this->zielwert <= 50) {
            return array(49, 50);
        } elseif ($this->zielwert > 50 && $this->zielwert <= 51) {
            return array(50, 51);
        } elseif ($this->zielwert > 51 && $this->zielwert <= 53) {
            return array(51, 53);
        } elseif ($this->zielwert > 53 && $this->zielwert <= 55) {
            return array(53, 55);
        } elseif ($this->zielwert > 55) {
            return array(55);
        }
    }
}
