<?php

/**
 * Die Klasse Dach.
 */
abstract class Dach extends Bauteil {
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
     * Konstruktor.
     * @param Grundriss $grundriss 
     * @param string $name 
     * @param float $flaeche 
     * @param float $uwert 
     * @return void 
     */
    public function __construct( Grundriss $grundriss,  string $name, float $flaeche, float $uwert, float $hoehe ) {
        $this->grundriss = $grundriss;
        $this->name = $name;
        $this->flaeche = $flaeche;
        $this->uwert = $uwert;
        $this->hoehe = $hoehe;

        $this->fx = 1.0;
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
}