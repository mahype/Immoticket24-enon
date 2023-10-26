<?php

class Fenster extends Bauteil {
    /**
     * Himmelsrichtung des Bauteils.
     * 
     * @var string
     */
    private string $himmelsrichtung;

    /**
     * Winkel des Bauteils.
     * 
     * @var int
     */
    private int $winkel;

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
    public function __construct( string $name, float $flaeche, float $uwert, string $himmelsrichtung, int $winkel = 90 ) {
        $this->name = $name;
        $this->flaeche = $flaeche;
        $this->uwert = $uwert;
        $this->himmelsrichtung = $himmelsrichtung;
        $this->winkel = $winkel;

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

    /**
     * Winkel des Bauteils.
     * 
     * @return int 
     */
    public function winkel(): int {
        return $this->winkel;
    }
}
