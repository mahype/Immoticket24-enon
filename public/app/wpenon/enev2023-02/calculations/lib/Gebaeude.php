<?php

/**
 * Gebäude.
 * 
 * @package 
 */
class Gebaeude
{
    /**
     * Baujahr des Gebäudes.
     * 
     * @var int
     */
    private $baujahr;

    /**
     * Anzahl der Geschosse.
     * 
     * @var int
     */
    private $geschossanzahl;

    /**
     * Geschosshöhe vom Boden inkl. Decke des darüberliegenden Geschosses.
     * 
     * @var float
     */
    private $geschosshoehe;

    /**
     * Hüllfläche des Gebäudes.
     * 
     * @var float
     */
    private $huellflaeche;

    /**
     * Hüllvolumen des Gebäudes.
     * 
     * @var float
     */
    private $huellvolumen;

    /**
     * Anzahl der Wohneinheiten.
     * 
     * @var string
     */
    private $wohneinheiten;

    /**
     * Wirksame Wärmespeicherkapazität in Abhängigkeit der Gebäudeschwere.
     * 
     * Für den vereinfachten Rechenweg festgelegt auf den Wert 50. 
     * 
     * @var int
     */
    private int $c_wirk = 50;

    public function __construct( int $baujahr, int $geschossanzahl, float $geschosshoehe, float $huellflaeche, float $huellvolumen, string $wohneinheiten )
    {
        $this->baujahr = $baujahr;
        $this->geschossanzahl = $geschossanzahl;
        $this->geschosshoehe = $geschosshoehe;
        $this->huellflaeche = $huellflaeche;
        $this->huellvolumen = $huellvolumen;
        $this->wohneinheiten = $wohneinheiten;
    }

    /**
     * Baujahr des Gebäudes.
     * 
     * @return int 
     */
    public function baujahr(): int
    {
        return $this->baujahr;
    }

    /**
     * Wirksame Wärmespeicherkapazität in Abhängigkeit der Gebäudeschwere.
     * 
     * @return int 
     */
    public function c_wirk(): int
    {
        return $this->c_wirk;
    }

    /**
     * Anzahl der Geschosse.
     * 
     * @return int 
     */
    public function geschossanzahl(): int
    {
        return $this->geschossanzahl;
    }

    /**
     * Geschosshöhe vom Boden inkl. Decke des darüberliegenden Geschosses.
     * 
     * @return float 
     */
    public function geschosshoehe(): float
    {
        return $this->geschosshoehe;
    }

    /**
     * Hüllfläche des Gebäudes.
     * 
     * @return float 
     */
    public function huellflaeche(): float
    {
        return $this->huellflaeche;
    }    

    /**
     * Hüllvolumen des Gebäudes.
     * 
     * @return float 
     */
    public function huellvolumen(): float
    {
        return $this->huellvolumen;
    }

    /**
     * Netto Hüllvolumen des Gebäudes.
     * 
     * @return float 
     */
    public function huellvolumen_netto(): float
    {
        return $this->geschossanzahl < 4 ? 0.76 * $this->huellvolumen: 0.8 *  $this->huellvolumen;
    }

    /**
     * Verhältnis von Hüllfläche zu Netto Hüllvolumen.
     * 
     * @return float 
     */
    public function av_ratio(): float
    {
        return $this->huellflaeche / $this->huellvolumen_netto();
    }

    /**
     * Nutzfläche des Gebäudes.
     * 
     * @return float 
     */
    public function nutzflaeche(): float
    {
        if ($this->geschosshoehe >= 2.5 && $this->geschosshoehe <= 3.0 ) {
            return $this->huellvolumen() * 0.32;
        } else {
            return $this->huellvolumen() * ( 1.0 / $this->geschosshoehe - 0.04 );
        }
    }

    /**
     * Anzahl der Wohneinheiten.
     * 
     * @return string 
     */
    public function wohneinheiten(): string
    {
        return $this->wohneinheiten;
    }
}