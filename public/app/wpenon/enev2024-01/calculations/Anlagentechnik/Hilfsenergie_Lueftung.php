<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202302\Calculations\Tabellen\Faktor_Anlagensysteme_Wohnungslueftungsanlagen;
use Enev\Schema202302\Calculations\Tabellen\Faktor_Baujahr_Anlagensysteme;
use Enev\Schema202302\Calculations\Tabellen\Hilfsenergieaufwand_Ventilatoren_Wohnungslueftungsanlagen;

require_once dirname( __DIR__ ) . '/Tabellen/Hilfsenergieaufwand_Ventilatoren_Wohnungslueftungsanlagen.php';

class Hilfsenergie_Lueftung {
    /**
     * Gebäude.
     * 
     * @var string
     */
    protected string $lueftungssystem;

    /**
     * Baujahr.
     * 
     * @var int
     */
    protected int $baujahr;

    /**
     * Ist zentral.
     * 
     * @var bool
     */
    protected string $art;

    /**
     * Gebäude.
     *
     * @param Gebaeude
     */
    public function __construct( string $lueftungsystem, int $baujahr, string $art )
    {
        $this->lueftungssystem = $lueftungsystem;
        $this->baujahr = $baujahr; 
        $this->art = $art;
    }

    /**
     * fsup_decr
     *
     * @return float
     */
    public function fsup_decr(): float {
        if ( $this->lueftungssystem === 'zu_abluft' ) {
            return 0.995;
        }
        if ( $this->lueftungssystem === 'abluft' ) {
            return 1.0;
        }

        throw new \Exception( 'Hilfsenergie für "%s" kann nicht berechnet werden.' );
    }

    public function fgr_exch(): float {
        return 1.0;
    }


    /**
     * fbaujahr
     *
     * @return float
     */
    public function fbaujahr(): float {
        return (new Faktor_Baujahr_Anlagensysteme( $this->lueftungssystem, $this->art, $this->baujahr ))->fbaujahr();
    }

    /**
     * fsystem
     *
     * @return float
     */
    public function fsystem(): float {
        return (new Faktor_Anlagensysteme_Wohnungslueftungsanlagen( $this->lueftungssystem, $this->art, $this->baujahr ))->fsystem();
    }

    /**
     * Wfan0
     *
     * @return float
     */
    public function Wfan0(): float {
        return (new Hilfsenergieaufwand_Ventilatoren_Wohnungslueftungsanlagen( $this->lueftungssystem, $this->baujahr ))->Wfan0();
    }

    /**
     * Wfan
     *
     * @return float
     */
    public function Wfan(): float {
        return $this->Wfan0() * $this->fsystem() * $this->fbaujahr() * $this->fgr_exch() * $this->fsup_decr() * $this->fbetrieb();
    }

    /**
     * Wc.
     * 
     * @return float
     */
    public function Wc(): float {
        return 0.0;
    }

    /**
     * Wpre_h.
     * 
     * @return float
     */
    public function Wpre_h(): float {
        return 0.0;
    }

    /**
     * Wrvg.
     * 
     * @return float
     */
    public function Wrvg(): float {
        return $this->Wfan() + $this->Wc() + $this->Wpre_h();
    }
}


//  Hilfsenergie  für Lüftungsanlagen $Wrvg
// 
//  $fbetrieb=1; //laut Tab.125, T12, Faktor für Anlagenbetrieb; BAnZ 2.3  mechanische Lüftung (ganz JAhresBetrieb ohne Bedarfsführung) und BAnz 3.3
//  $fgr_exch=1.0 //Laut Tab 123, T12, BanZ 11.3 
//
//   if "Zu_ und  Abluftanlage" than
//        $fsup_decr=0,995; // T12, Tab124, Banz 11.3; Für uns sicheren Wert genommen;
//    if "Abluftanlage" than
//        $fsup_decr=1.0; // T12, Tab 124; Da keine Außenluft angesaugt wird ist keine Frostschutz notwendig
//   
//   $fbaujahr= Tab 122, T12, abhängig von Lüftungssystem und Baujahr; vereinfacht nach  Tab fbaujahr Ausarbeitung von Jan
//    
//   $fsystem= Tab 121, T12 i Anhängigkleit des Lüftungsystems und info bis 2009 alle AC ab 2010 DC siehe Tab fsystem von Jan

//    $Wfan0= Tab. 120, T12, in Anhängigkeit der $calculations['nutzflaeche' "nicht bedarfsgeführt" und Abhängi Spalte AC/DC (AC bis 2009 und DC ab 2010), ab 5000m² darf linear extrapoliert werden
//      


// $Wfan=$Wfan0*$fsystem*$fbaujahr*$fgr_exch*$fsup_decr*$fbetrieb

//
//  $Wc=0.0; // laut BanZ 
//  $Wpre_h=0.0// Laut BanZ

//

// $Wrvg=$Wfan+$Wc+$Wpre_h