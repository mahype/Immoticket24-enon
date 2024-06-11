<?php

namespace Enev\Schema202404\Calculations\Tabellen;

use function Enev\Schema202404\Calculations\Helfer\interpolate_value;

require_once dirname(__DIR__) . '/Helfer/Math.php';

/**
 * Kenngrößen thermischer Solaranlagen – Trinkwassererwärmung mit Zirkulation – Verteilung im beheizten Raum.
 *
 * @package
 */
class Waermeverlust_Trinkwasserspeicher
{

    /**
     * Volumen des Trinkwasserspeichers in Liter.
     *
     * @var float
     */
    protected float $volumen;

    /**
     * Trinkwasserspeicher beheizt.
     * 
     * @var bool
     */
    protected bool $heizung_im_beheizten_bereich;

    /**
     * Mit Zirkulation.
     * 
     * @var bool
     */
    protected bool $mit_zirkulation;

    /**
     * Tabellendaten aus Tabelle 9 bei Einfamilienhaus oder Tabelle 11 bei Mehrfamilienhaus.
     *
     * @var array
     */
    protected array $table_data;

    /**
     * Konstruktor.
     *
     * @param float $volumen
     * @return void
     */
    public function __construct(float $volumen, bool $heizung_im_beheizten_bereich, bool $mit_zirkulation)
    {
        $this->volumen = $volumen;
        $this->heizung_im_beheizten_bereich = $heizung_im_beheizten_bereich;
        $this->mit_zirkulation = $mit_zirkulation;

        $this->table_data = wpenon_get_table_results('waermeverlust_trinkwasserspeicher');
    }

    /**
     * Ermittelt den Wert Qws0 aus der Tabelle.
     * 
     * @return float 
     */
    public function Qws0(): float
    {
        $zeilen = $this->zeilen();

        if (count($zeilen) === 1) {
            return $this->table_data['v_' . $zeilen[0]]->{$this->spalte()};
        }

        $keys = array();
        $values = array();

        foreach ($zeilen as $zeile) {
            $keys[] = $zeile;
            $values[] = $this->table_data['v_' . $zeile]->{$this->spalte()};
        }

        return interpolate_value($this->volumen, $keys, $values);
    }

    /**
     * Ermittelt Zeilen aus der Tabelle.
     * 
     * @return array 
     */
    protected function zeilen(): array
    {
        $zeilen = array();

        if ($this->volumen <= 5) {
            $zeilen = array(5);
        } else if ($this->volumen > 5 && $this->volumen < 10) {
            $zeilen = array(5, 10);
        } else if ($this->volumen >= 10 && $this->volumen < 20) {
            $zeilen = array(10, 20);
        } else if ($this->volumen >= 20 && $this->volumen < 30) {
            $zeilen = array(20, 30);
        } else if ($this->volumen >= 30 && $this->volumen < 40) {
            $zeilen = array(30, 40);
        } else if ($this->volumen >= 40 && $this->volumen < 50) {
            $zeilen = array(40, 50);
        } else if ($this->volumen >= 50 && $this->volumen < 60) {
            $zeilen = array(50, 60);
        } else if ($this->volumen >= 60 && $this->volumen < 70) {
            $zeilen = array(60, 70);
        } else if ($this->volumen >= 70 && $this->volumen < 80) {
            $zeilen = array(70, 80);
        } else if ($this->volumen >= 80 && $this->volumen < 90) {
            $zeilen = array(80, 90);
        } else if ($this->volumen >= 90 && $this->volumen < 100) {
            $zeilen = array(90, 100);
        } else if ($this->volumen >= 100 && $this->volumen < 150) {
            $zeilen = array(100, 150);
        } else if ($this->volumen >= 150 && $this->volumen < 200) {
            $zeilen = array(150, 200);
        } else if ($this->volumen >= 200 && $this->volumen < 300) {
            $zeilen = array(200, 300);
        } else if ($this->volumen >= 300 && $this->volumen < 400) {
            $zeilen = array(300, 400);
        } else if ($this->volumen >= 400 && $this->volumen < 500) {
            $zeilen = array(400, 500);
        } else if ($this->volumen >= 500 && $this->volumen < 600) {
            $zeilen = array(500, 600);
        } else if ($this->volumen >= 600 && $this->volumen < 700) {
            $zeilen = array(600, 700);
        } else if ($this->volumen >= 700 && $this->volumen < 800) {
            $zeilen = array(700, 800);
        } else if ($this->volumen >= 800 && $this->volumen < 900) {
            $zeilen = array(800, 900);
        } else if ($this->volumen >= 900 && $this->volumen < 1000) {
            $zeilen = array(900, 1000);
        } else if ($this->volumen >= 1000 && $this->volumen < 1500) {
            $zeilen = array(1000, 1500);
        } else if ($this->volumen >= 1500) {
            $zeilen = array(1500);
        }

        return $zeilen;
    }

    /**
     * Ermittelt die Spalte aus der Tabelle.
     * 
     * @return string 
     */
    protected function spalte(): string
    {
        $spalte = '';

        if ($this->heizung_im_beheizten_bereich) {
            $spalte .= 'beheizt';
        } else {
            $spalte .= 'unbeheizt';
        }

        if ($this->mit_zirkulation) {
            $spalte .= '_mit_zirkulation';
        } else {
            $spalte .= '_ohne_zirkulation';
        }

        return $spalte;
    }
}
