<?php

class Heizkoerpernische extends Bauteil {
    /**
     * Himmelsrichtung des Bauteils.
     * 
     * @var string
     */
    private string $himmelsrichtung;

    /**
     * Konstruktor
     * 
     * @param  float  $flaeche         Fläche
     *                                 des
     *                                 Bauteils.
     * @param  float  $uwert           Uwert des Bauteils.
     * @param  int    $baujahr         Baujahr des Bauteils.
     * @param  string $himmelsrichtung Himmelsrichtung des Bauteils.
     * @param  float  $daemmung        Dämmung des Bauteils.
     * @param  int    $winkel          Winkel des Bauteils.
     */
    public function __construct( string $name, float $flaeche, float $uwert, int $baujahr, string $himmelsrichtung ) {
        $this->name = $name;
        $this->flaeche = $flaeche;
        $this->uwert = $uwert;
        $this->baujahr = $baujahr;
        $this->himmelsrichtung = $himmelsrichtung;

        $this->fx = 1.0;
    }

    /**
     * Himmelsrichtung des Bauteils.
     * 
     * @return string 
     */
    public function himmelsrichtung(): string {
        return $this->himmelsrichtung;
    }
}
