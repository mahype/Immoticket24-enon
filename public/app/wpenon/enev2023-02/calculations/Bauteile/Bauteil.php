<?php

abstract class Bauteil {
    /**
     * Name der Wand.
     */
    protected string $name;

    /**
     * Fläche des Bauteils.
     * 
     * @var float
     */
    protected float $flaeche;

    /**
     * U-Wert des Bauteils.
     * 
     * @var float
     */
    protected float $uwert;

    /**
     * Baujahr des Bauteils.
     * 
     * @var int
     */
    protected int $baujahr;

    /**
     * Fx-Wert des Bauteils.
     * 
     * @var float
     */
    protected float $fx;

    /**
     * Name der Wand.
     * 
     * @return string 
     */
    public function name(): string {
        return $this->name;
    }

    /**
     * Fläche des Bauteils.
     * 
     * @return float 
     */
    public function flaeche(): float
    {
        return $this->flaeche;
    }

    /**
     * U-Wert des Bauteils.
     * 
     * @return float 
     */
    public function uwert(): float
    {
        return $this->uwert;
    }

    /**
     * Baujahr des Bauteils.
     * 
     * @return int 
     */
    public function baujahr(): int
    {
        return $this->baujahr;
    }
    
    /**
     * Fx-Wert des Bauteils.
     * 
     * @return float 
     */
    public function fx(): float
    {
        return $this->fx;
    }

    /**
     * Transmissionswärmeverlust des Bauteils.
     * 
     * @return float 
     */
    public function ht(): float
    {
        return $this->flaeche * $this->uwert * $this->fx;
    }
}