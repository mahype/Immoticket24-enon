<?php

class Wand extends Bauteil
{
    /**
     * Seite des Bauteils (a, b, c...)
     * 
     * @var string
     */
    private string $seite;

    /**
     * Himmelsrichtung des Bauteils.
     * 
     * @var string
     */
    private string $himmelsrichtung;

    /**
     * Dämmung des Bauteils.
     * 
     * @var float
     */
    private float $daemmung;

    /**
     * Konstruktor
     * 
     * @param  string $seite           Seite des Bauteils (a, b, c...)
     * @param  float  $flaeche         Fläche
     *                                 des
     *                                 Bauteils.
     * @param  float  $uwert           Uwert des Bauteils.
     * @param  int    $baujahr         Baujahr des Bauteils.
     * @param  string $himmelsrichtung Himmelsrichtung des Bauteils.
     * @param  float  $daemmung        Dämmung des Bauteils.
     */
    public function __construct( string $name, string $seite, float $flaeche, float $uwert, int $baujahr, string $himmelsrichtung, float $daemmung )
    {
        $this->name = $name;
        $this->seite = $seite;
        $this->flaeche = $flaeche;
        $this->uwert = $uwert;
        $this->baujahr = $baujahr;
        $this->himmelsrichtung = $himmelsrichtung;
        $this->daemmung = $daemmung;

        $this->fx = 1.0;
    }

    /**
     * Seite des Bauteils (a, b, c...)
     * 
     * @return string 
     */
    public function seite(): string {
        return $this->seite;
    }

    /**
     * Himmelsrichtung des Bauteils.
     * 
     * @return string 
     */
    public function himmelsrichtung(): string
    {
        return $this->himmelsrichtung;
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
