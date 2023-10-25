<?php

class Dach extends Bauteil {
    public function __construct( string $name, float $flaeche, float $uwert, int $baujahr ) {
        $this->name = $name;
        $this->flaeche = $flaeche;
        $this->uwert = $uwert;
        $this->baujahr = $baujahr;

        $this->fx = 1.0;
    }
}