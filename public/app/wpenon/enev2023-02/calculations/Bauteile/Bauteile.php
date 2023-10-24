<?php
/**
 * Temporäre Klasse zur Aufnahme der Daten. Später sollen die Bauteile, Transmissions usw. hier berechnet werden.
 */
class Bauteile {
    protected float $huellvolumen;
    protected float $huellflaeche;
    protected float $ht;
    protected float $hw;

    public function __construct(float $huellvolumen, float $huellflaeche, float $ht, float $hw)
    {
        $this->huellvolumen = $huellvolumen;
        $this->huellflaeche = $huellflaeche;
        $this->ht = $ht;
        $this->hw = $hw;
    }

    public function huellvolumen(): float
    {
        return $this->huellvolumen;
    }

    public function huellflaeche(): float
    {
        return $this->huellflaeche;
    }

    public function ht(): float
    {
        return $this->ht;
    }

    public function hw(): float
    {
        return $this->hw;
    }

}