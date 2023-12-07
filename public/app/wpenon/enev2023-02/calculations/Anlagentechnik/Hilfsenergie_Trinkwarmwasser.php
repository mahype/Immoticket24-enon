<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;

class Hilfsenergie_Lueftung {
    /**
     * Gebäude.
     * 
     * @var Gebaeude
     */
    protected Gebaeude $gebaeude;

    /**
     * Gebäude.
     *
     * @param Gebaeude
     */
    public function __construct( $gebaeude )
    {
        $this->gebaeude = $gebaeude;        
    }
}