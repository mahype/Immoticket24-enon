<?php


class Gebaeude
{
    private $baujahr;
    private $geschossanzahl;
    private $geschosshoehe;
    private $huellflaeche;
    private $huellvolumen;

    public function __construct( int $baujahr, int $geschossanzahl, float $geschosshoehe, float $huellflaeche, float $huellvolumen )
    {
        $this->baujahr = $baujahr;
        $this->geschossanzahl = $geschossanzahl;
        $this->geschosshoehe = $geschosshoehe;
        $this->huellflaeche = $huellflaeche;
        $this->huellvolumen = $huellvolumen;
    }

    public function baujahr(): int
    {
        return $this->baujahr;
    }

    public function geschossanzahl(): int
    {
        return $this->geschossanzahl;
    }

    public function geschosshoehe(): float
    {
        return $this->geschosshoehe;
    }

    public function huellflaeche(): float
    {
        return $this->huellflaeche;
    }    

    public function huellvolumen(): float
    {
        return $this->huellvolumen;
    }

    public function huellvolumen_netto(): float
    {
        return $this->geschossanzahl < 4 ? 0.76 * $this->huellvolumen: 0.8 *  $this->huellvolumen;
    }

    public function av_ratio(): float
    {
        return $this->huellflaeche / $this->huellvolumen_netto();
    }

    public function nutzflaeche(): float
    {
        if ($this->geschosshoehe >= 2.5 && $this->geschosshoehe <= 3.0 ) {
            return $this->huellvolumen() * 0.32;
        } else {
            return $this->huellvolumen() * ( 1.0 / $this->geschosshoehe - 0.04 );
        }
    }
}