<?php

/**
 * Die Klasse Boden.
 */
class Boden extends Bauteil
{
    /**
     * Dämmung des Bauteils.
     * 
     * @var float
     */
    protected float $daemmung;

    /**
     * Konstruktor.
     * 
     * @param  string $name     Name des Bauteils.
     * @param  float  $flaeche  Fläche des Bauteils.
     * @param  float  $uwert    U-Wert des Bauteils.
     * @param  float  $daemmung Dämmung des Bauteils.
     */
    public function __construct( string $name, float $flaeche, float $uwert, float $daemmung )
    {
        $this->name = $name;
        $this->flaeche = $flaeche;
        $this->uwert = $uwert;
        $this->daemmung = $daemmung;

        $this->fx = 0.8;
    }

    /**
     * Dämmung des Bauteils.
     * 
     * @return float 
     */
    public function daemmung(): float
    {
        return $this->daemmung;
    }
}