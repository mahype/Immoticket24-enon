<?php

namespace Enev\Schema202302\Calculations\Bauteile;

use Enev\Schema202302\Calculations\Schnittstellen\Transmissionswaerme;

/**
 * Abstrakte Klasse für ein Dach.
 */
abstract class Dach extends Bauteil implements Transmissionswaerme {
    /**
     * Grundriss.
     * 
     * @var Grundriss
     */
    protected Grundriss $grundriss;

    /**
     * Höhe des Dachs.
     * 
     * @var float
     */
    protected float $hoehe;

    /**
     * Volumen des Dachs.
     * 
     * @var float
     */
    protected float $volumen;

    /**
     * Fläche des Dachs.
     * 
     * @var float
     */
    protected float $flaeche;

    /**
     * Höhe des Kniestocks.
     * 
     * @var float
     */
    protected float $kniestock_hoehe = 0.0;

    /**
     * Dachwandflächen.
     * 
     * @var array
     */
    protected array $wand_flaechen = array();

    /**
     * Dämmung des Bauteils.
     * 
     * @var float
     */
    protected float $daemmung;

    /**
     * Konstruktor.
     * 
     * @param Grundriss $grundriss 
     * @param string $name 
     * @param float $flaeche 
     * @param float $uwert 
     * @return void 
     */
    public function __construct( Grundriss $grundriss,  string $name, float $hoehe, float $kniestock_hoehe, float $uwert, float $daemmung ) {
        $this->grundriss = $grundriss;
        $this->name = $name;
        $this->uwert = $uwert;
        $this->hoehe = $hoehe;
        $this->kniestock_hoehe = $kniestock_hoehe;
        $this->daemmung = $daemmung;

        $this->fx = 1.0;

        $this->berechnen();
    }

    /**
     * Höhe des Dachs.
     * 
     * @return float 
     */
    public function hoehe(): float {
        return $this->hoehe;
    }

    /**
     * Volumen des Dachs.
     * 
     * @return float 
     */
    public function volumen(): float {
        return $this->volumen;
    }

    /**
     * Fläche des Dachs.
     * 
     * @return float 
     */
    public function flaeche(): float {
        return $this->flaeche;
    }

    /**
     * Volumen kniestock.
     * 
     * @return float
     */
    public function volumen_kniestock(): float {
        return $this->grundriss->flaeche() * $this->kniestock_hoehe;
    }

    /**
     * Wandfläche kniestock.
     * 
     * @param string $seite Seite des Kniestocks.
     * 
     * @return float
     * 
     * @throws Exception
     */
    protected function wandflaeche_kniestock( string $seite = null): float {
        if( $seite ) {
            return $this->grundriss->wand_laenge( $seite ) * $this->kniestock_hoehe;
        }

        return $this->grundriss->umfang() * $this->kniestock_hoehe;
    }

    /**
     * Wandfläche für eine bestimmte Seite.
     * 
     * @param string $seite 
     * @return array 
     * @throws Exception 
     */
    public function wand_flaeche( string $seite ): array {
        $wand_flaeche = 0;

        if( array_key_exists($seite, $this->wand_flaechen) ) {
            $wand_flaeche += $this->wand_flaechen[ $seite ];
        }

        $wand_flaeche += $this->wandflaeche_kniestock( $seite );

        return $this->wand_flaechen;
    }

    /**
     * Berechnung der Werte.
     * 
     * @return void 
     */
    abstract protected function berechnen(): void;
}