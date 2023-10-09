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

    public function __construct( int $baujahr, int $geschossanzahl, float $geschosshoehe, float $huellflaeche, float $huellvolumen, int $wohneinheiten )
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
    public function wohneinheiten(): int
    {
        return $this->wohneinheiten;
    }

    /**
     * Jährlicher Nutzwaermebedarf für Trinkwasser (qwb).
     * 
     * Aufgrund der Einfachheit nicht in der Datenbank gespeichert.
     * 
     * Teil 12 - Tabelle 19.
     * 
     * @param float $nutzflaeche Netto-Nutzfläche des Gebäudes.
     * 
     * @return float 
     */
    function nutzwaermebedarf_trinkwasser(): float
    {
        if ($this->nutzflaeche() < 10) {
            return 16.5;
        } elseif ($this->nutzflaeche() >= 10) {
            return 16;
        } elseif ($this->nutzflaeche() >= 20) {
            return 15.5;
        } elseif ($this->nutzflaeche() >= 30) {
            return 15;
        } elseif ($this->nutzflaeche() >= 40) {
            return 14.5;
        } elseif ($this->nutzflaeche() >= 50) {
            return 14;
        } elseif ($this->nutzflaeche() >= 60) {
            return 13.5;
        } elseif ($this->nutzflaeche() >= 70) {
            return 13;
        } elseif ($this->nutzflaeche() >= 80) {
            return 12.5;
        } elseif ($this->nutzflaeche() >= 90) {
            return 12;
        } elseif ($this->nutzflaeche() >= 100) {
            return 11.5;
        } elseif ($this->nutzflaeche() >= 110) {
            return 11;
        } elseif ($this->nutzflaeche() >= 120) {
            return 10.5;
        } elseif ($this->nutzflaeche() >= 130) {
            return 10;
        } elseif ($this->nutzflaeche() >= 140) {
            return 9.5;
        } elseif ($this->nutzflaeche() >= 150) {
            return 9;
        } elseif ($this->nutzflaeche() >= 160) {
            return 8.5;
        }
    }

    /**
     * Monatlicher Nutzwärmebedarf für Trinkwasser (qwb).
     * 
     * @param string $monat Slug des Monats.
     * 
     * @return float Nutzwärmebedarf für Trinkwasser (qwb) in kWh.
     */
    public function nutzwaermebedarf_trinkwasser_monat( string $monat ): float
    {
        $jahr = new Jahr();
        $qwb = $this->nutzwaermebedarf_trinkwasser($this->nutzflaeche());
        return ($this->nutzflaeche()/$this->wohneinheiten()) * $qwb * ($jahr->monat($monat)->tage()/365);
    }
}