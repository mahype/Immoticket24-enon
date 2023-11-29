<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik\Heizungsanlagen;

use Enev\Schema202302\Calculations\Anlagentechnik\Heizungsanlagen\Typen\Konventioneller_Kessel;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;

require_once __DIR__ . '/Typen/Konventioneller_Kessel.php';

/**
 * Standardkessel.
 * 
 * @package Enev\Schema202302\Calculations\Anlagentechnik\Heizungsanlagen
 */
class Standardkessel extends Konventioneller_Kessel {
    /**
     * Konstruktor.
     * @param Gebaeude $gebaeude
     * @param string $energietraeger 
     * @param int $prozentualer_anteil 
     * @return void 
     * 
     * @see Konventioneller_Kessel::__construct()
     */
    public function __construct( Gebaeude $gebaeude, string $energietraeger, int $baujahr, int $prozentualer_anteil = 100 )
    {
        parent::__construct( $gebaeude, 'standardkessel', $energietraeger, $baujahr, $prozentualer_anteil);
    }
}