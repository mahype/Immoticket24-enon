<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202302\Calculations\Tabellen\Differenzdruck_Waermeerzeuger;

require_once dirname( __DIR__ ) . '/Tabellen/Differenzdruck_Waermeerzeuger.php';

/**
 * Hilfsenergie.
 */
class Hilfsenergie {

    /**
     * Gebäude.
     * 
     * @var Gebaeude $gebaeude  
     */
    protected Gebaeude $gebaeude;

    /**
     * Hilfsenergie.
     * 
     * @param Gebaeude $gebaeude Gebäude.
     */
    public function __construct( Gebaeude $gebaeude )
    {
        $this->gebaeude = $gebaeude;
    }

    public function pg() {
        $heizlast = $this->gebaeude->luftwechsel()->h_max() / 1000; // Umrechnung in kWh



        $pg = (new Differenzdruck_Waermeerzeuger( $heizlast ))->pg();
        return $pg;
    }

    public function Nr(): float {
        return $this->gebaeude->nutzflaeche() * 1 / 7;
    }

    public function WHce(): float {
        $uebergabesystem = $this->gebaeude->heizsystem()->uebergabesysteme()->erstes();

        switch( $uebergabesystem->typ() ) {
            case 'heizkoerper':
                return 0;
            case 'fussbodenheizung':
            case 'wandheizung':
            case 'deckenheizung':
                // $nR= $calculations['nutzflaeche']*$AnteileFBHZ/7 
                $nR = $this->gebaeude->nutzflaeche() * $uebergabesystem->prozentualer_anteil() / 7;
                return 0.876 * $nR;
            default:
                throw new Calculation_Exception( sprintf( 'WHce für "%s" kann nicht ermittelt werden.', $uebergabesystem->typ() ) );
        }
    }

    public function Wc(): float {
        return 0;
    }

    /**
     * Bestimmung der Hilfsenergie Übergabe Lüftung (nur Lüftung nicht Umluftheizung).
     * 
     * // Quelle Banz Lfn: 5.2/5.3
     * 
     * @return float 
     */
    public function Wrvce(): float {
        return 0;
    }

    /**
     * Bestimmung der Hilfsenergie Übergabe Trinkwarmwasser.
     * 
     * // 18599 T8, S.28 oder T12 . 6.3.3.2
     * 
     * @return float 
     */
    public function Wwce(): float {
        return 0;
    }

    /**
     *  Bestimmung der Hilfsenergie Übergabe Trinkwarmwasser.
     * 
     */


}