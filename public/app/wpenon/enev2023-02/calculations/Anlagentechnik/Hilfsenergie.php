<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202302\Calculations\Tabellen\Differenzdruck_Waermeerzeuger;
use Enev\Schema202302\Calculations\Tabellen\TERMpumpe;
use Enev\Schema202302\Calculations\Tabellen\Volumenstrom_im_Auslegungspunkt;

require_once dirname( __DIR__ ) . '/Tabellen/Differenzdruck_Waermeerzeuger.php';
require_once dirname( __DIR__ ) . '/Tabellen/Volumenstrom_im_Auslegungspunkt.php';
require_once dirname( __DIR__ ) . '/Tabellen/TERMpumpe.php';

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
        $ist_gaskessel = false;
    
        foreach( $this->gebaeude->heizsystem()->heizungsanlagen() as $heizungsanlage ) {
            if( $heizungsanlage->typ() !== 'brennwertkessel' &&  $heizungsanlage->typ() !== 'niedertemperaturkessel' ) {
                continue;     
            }

            if( $heizungsanlage->energietraeger() !== 'erdgas' && $heizungsanlage->energietraeger() !== 'fluessiggas' && $heizungsanlage->energietraeger() !== 'biogas' ) {
                continue;
            }

            $ist_gaskessel = true;
        }

        if( $ist_gaskessel ) {
            //   if GasBrennwertheizung=! GasNiedertemperatrukessel && $Pn <35  than
            //       $pg= Tab 39, T12, in Abhängikeit der h_max und Übergabesystems (Heizkörper 10k; sichere Seite), Fußbodenheizung  
            //   else $pg= 1.0;
            return (new Differenzdruck_Waermeerzeuger( $this->gebaeude->luftwechsel()->h_max() / 1000 ))->pg();
        }

        return 1;
    }

    public function WHce(): float {
        $uebergabesystem = $this->gebaeude->heizsystem()->uebergabesysteme()->erstes();

        switch( $uebergabesystem->typ() ) {
            case 'heizkoerper':
                return 0;
            case 'flaechenheizung':
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
     * Bestimmung der Hilfsenergie Übergabe Trinkwarmwasser 
     * 
     * return float 
     */
    public function WsolPumpece(): float {
        //     if thermische SolarAnlage than
        //        $WsolPumpece=0, // 18599 T12 Beispielrechnung; ansonsten keine Def. in  Normung. Es wird in T8 etc. nur die Hilfsenergie für Erzeuung berücksichtigt
        //     else
        //        $WsolPumpece=0; // Nach Absprache mit Jan 04.12.2023

        return 0;
    }

    public function fgeoHzg(): float {
        //    $fgeoHzg=0,392; T12, Tab D1.
        return 0.392;
    }

    public function fblHzg(): float {
        // $fblHzg=0,31; T12, Tab D1.
        return 0.31;
    }

    public function fgeoTWW(): float {
        //    $fgeoTWW=0,277; T12, Tab D1.
        return 0.277;
    }

    public function fblTWW(): float {
        //    $fblTWW=0,22; T12, Tab D1.
        return 0.22;
    }

    public function LcharHzg(): float {
        //       $LcharHzg= ($calculations['nutzflaeche']/$nG*$fgeoHzg)^(1/2); // Welches "geo" ist gemeint
        return ( $this->gebaeude->nutzflaeche() / $this->gebaeude->geschossanzahl() * $this->fgeoHzg() ) ** ( 1 / 2 );
    }

    public function LcharTWW(): float {
        //       $LcharTWW= ($calculations['nutzflaeche']/$nG*$fgeoTWW)^(1/2); // Welches "geo" ist gemeint
        return ( $this->gebaeude->nutzflaeche() / $this->gebaeude->geschossanzahl() * $this->fgeoTWW() ) ** ( 1 / 2 );
    }

    public function BcarHzg(): float {
        //       $BcarHzg=$LcharHzg*0,31;
        return $this->LcharHzg() * 0.31;
    }

    public function BcarWW(): float {
        //       $BcarWW=$LcharWW*0,31;
        return $this->LcharTWW() * 0.31;
    }

    public function LmaxHzg(): float {
        //       $LmaxHzg=2*($LcharHzg+($BcarHzg/2)+$nG*$hG+10); //10=ld ; definiert da wir nur 2-Rohrsystem Heizung betrachten T12 S. 305
        return 2 * ( $this->LcharHzg() + ( $this->BcarHzg() / 2 ) + $this->gebaeude->geschossanzahl() * $this->gebaeude->geschosshoehe() + 10 );
    }

    public function LmaxTWW(): float {
        //       $LmaxTWW=2*($LcharWW+2.5+$nG*$hG); //
        return 2 * ( $this->LcharTWW() + 2.5 + $this->gebaeude->geschossanzahl() * $this->gebaeude->geschosshoehe() );
    }
    
    public function TERMp(): float {
        //   If Heizkörper than
        //       $TERMp= 0.13*$LmaxHzg+2+0; //bestimmen in Abhängikeit von $LmaxHzg  && Heizkörper oder Fußbodenheizung, wenn Übergabesytem Heizkörper bzw. Fußbodenheizung prozentrual anteilig zu berechnen  //Tab37, T12
        //   if Fußbodenheizung/Wandheizung/Deckenheizung than
        //       $TERMp= 0.13*$LmaxHzg+2+25;

        $uebergabesystem = $this->gebaeude->heizsystem()->uebergabesysteme()->erstes();

        switch( $uebergabesystem->typ() ) {
            case 'heizkoerper':
                return 0.13 * $this->LmaxHzg() + 2 + 0;
            case 'flaechenheizung':
                return 0.13 * $this->LmaxHzg() + 2 + 25;
            default:
                throw new Calculation_Exception( sprintf( 'TERMp für "%s" kann nicht ermittelt werden.', $uebergabesystem->typ() ) );
        }
    }

    public function Vstr(): float {
        //  $Vstr= aus TAb 38/T12 in Abhängikeit der h_max und Übergabesystems (Heizkörper 10k; sichere Seite), Fußbodenheizung  
        return (new Volumenstrom_im_Auslegungspunkt( $this->gebaeude->luftwechsel()->h_max() / 1000 ))->V();
    }

    public function PhydrHzg(): float {
        // $PhydrHzg=0.2778*($TERMp+$pg+11)*$Vstr; //10=Pwmz; Pstranga=1 daraus ergibt sich die 11 beides entnommen aus  T12, S.83
        return 0.2778 * ( $this->TERMp() + $this->pg() + 11 ) * $this->Vstr();
    }

    public function fe(): float {
        // $fe = ((1.25+(200/$PhydrHzg)^0.5)*2// Banz wir können für Pumpe(2 Faktor) berücksichtigen, da diese als nich bedarfsausgelegt definiert wird
        return ( ( 1.25 + ( 200 / $this->PhydrHzg() ) ** 0.5 ) * 2 );
    }

    public function TERMpumpe(): float {
       //  $TERMpumpe = Nach Tab.40 T12, in Anhängikeit von $ßhd && Bj Heizung bis 1994 ungregelt ab 1995 konstant
       $TERMpumpe = (new TERMpumpe( $this->gebaeude->heizsystem()->ßhd(), $this->gebaeude->heizsystem()->aelteste_heizungsanlage()->baujahr() ) )->TERMpumpe();
       return $TERMpumpe;
    }
}