<?php

namespace Enev\Schema202402\Calculations\Anlagentechnik;

use Enev\Schema202402\Calculations\Calculation_Exception;
use Enev\Schema202402\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202402\Calculations\Tabellen\Differenzdruck_Waermeerzeuger;
use Enev\Schema202402\Calculations\Tabellen\Differenzdruck_Leitungslaenge;
use Enev\Schema202402\Calculations\Tabellen\Hilfsenergieaufwand_Ladepumpe;
use Enev\Schema202402\Calculations\Tabellen\Intermittierender_Pumpenbetrieb;
use Enev\Schema202402\Calculations\Tabellen\TERMpumpe;
use Enev\Schema202402\Calculations\Tabellen\Volumenstrom_im_Auslegungspunkt;
use Enev\Schema202402\Calculations\Tabellen\Laufzeit_Zirkulationspumpe;

require_once dirname( __DIR__ ) . '/Tabellen/Differenzdruck_Leitungslaenge.php';
require_once dirname( __DIR__ ) . '/Tabellen/Differenzdruck_Waermeerzeuger.php';
require_once dirname( __DIR__ ) . '/Tabellen/Hilfsenergieaufwand_Ladepumpe.php';
require_once dirname( __DIR__ ) . '/Tabellen/Volumenstrom_im_Auslegungspunkt.php';
require_once dirname( __DIR__ ) . '/Tabellen/TERMPumpe.php';
require_once dirname( __DIR__ ) . '/Tabellen/Intermittierender_Pumpenbetrieb.php';
require_once dirname( __DIR__ ) . '/Tabellen/Laufzeit_Zirkulationspumpe.php';

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
	public function __construct( Gebaeude $gebaeude ) {
		$this->gebaeude = $gebaeude;
	}

	public function pg() {
		$ist_gaskessel = false;

		foreach ( $this->gebaeude->heizsystem()->heizungsanlagen()->alle() as $heizungsanlage ) {
			if ( $heizungsanlage->typ() !== 'brennwertkessel' && $heizungsanlage->typ() !== 'niedertemperaturkessel' ) {
				continue;
			}

			if ( $heizungsanlage->energietraeger() !== 'erdgas' && $heizungsanlage->energietraeger() !== 'fluessiggas' && $heizungsanlage->energietraeger() !== 'biogas' ) {
				continue;
			}

			$ist_gaskessel = true;
		}

		if ( $ist_gaskessel && ( $this->gebaeude->lueftung()->h_max() / 1000 ) < 35 ) {
			// if GasBrennwertheizung=! GasNiedertemperatrukessel && $h_max <35  than
			// $pg= Tab 39, T12, in Abhängikeit der h_max und Übergabesystems (Heizkörper 10k; sichere Seite), Fußbodenheizung
			// else $pg= 1.0;
			return ( new Differenzdruck_Waermeerzeuger( $this->gebaeude->lueftung()->h_max() / 1000 ) )->pg();
		}

		return 1;
	}

	public function WHce(): float {
		$uebergabesystem = $this->gebaeude->heizsystem()->uebergabesysteme()->erstes();

		switch ( $uebergabesystem->typ() ) {
			case 'heizkoerper':
			case 'elektroheizungsflaechen':
				return 0;
			case 'flaechenheizung':
				// $nR= $calculations['nutzflaeche']*$AnteileFBHZ/7
				$nR = $this->gebaeude->nutzflaeche() / 7;
				return 0.876 * $nR;
			default:
				throw new Calculation_Exception( sprintf( 'WHce für "%s" kann nicht ermittelt werden.', $uebergabesystem->typ() ) );
		}
	}

	/**
	 * Zwischenwert zur Berechnung der Lüftungsanlagen.
	 * 
	 * @return float 
	 */
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
		// if thermische SolarAnlage than
		// $WsolPumpece=0, // 18599 T12 Beispielrechnung; ansonsten keine Def. in  Normung. Es wird in T8 etc. nur die Hilfsenergie für Erzeuung berücksichtigt
		// else
		// $WsolPumpece=0; // Nach Absprache mit Jan 04.12.2023

		return 0;
	}

	public function fgeoHzg(): float {
		// $fgeoHzg=0,392; T12, Tab D1.
		return 0.392;
	}

	public function fblHzg(): float {
		// $fblHzg=0,31; T12, Tab D1.
		return 0.31;
	}

	public function fgeoTWW(): float {
		// $fgeoTWW=0,277; T12, Tab D1.
		return 0.277;
	}

	public function fblTWW(): float {
		// $fblTWW=0,22; T12, Tab D1.
		return 0.22;
	}

	public function LcharHzg(): float {
		// $LcharHzg= ($calculations['nutzflaeche']/$nG*$fgeoHzg)^(1/2); // Welches "geo" ist gemeint
		return ( $this->gebaeude->nutzflaeche() / ( $this->gebaeude->geschossanzahl() * $this->fgeoHzg() ) ) ** ( 1 / 2 );
	}

	public function LcharTWW(): float {
		// $LcharTWW= ($calculations['nutzflaeche']/$nG*$fgeoTWW)^(1/2); // Welches "geo" ist gemeint
		return ( $this->gebaeude->nutzflaeche() / ( $this->gebaeude->geschossanzahl() * $this->fgeoTWW() ) ) ** ( 1 / 2 );
	}

	public function BcarHzg(): float {
		// $BcarHzg=$LcharHzg*$fblHzg;
		return $this->LcharHzg() * $this->fblHzg();
	}

	public function BcarWW(): float {
		// $BcarWW=$LcharWW*$fblTWW;
		return $this->LcharTWW() * $this->fblTWW();
	}

	public function LmaxHzg(): float {
		// $LmaxHzg=2*($LcharHzg+($BcarHzg/2)+$nG*$hG+10); //10=ld ; definiert da wir nur 2-Rohrsystem Heizung betrachten T12 S. 305
		return 2 * ( $this->LcharHzg() + ( $this->BcarHzg() / 2 ) + $this->gebaeude->geschossanzahl() * $this->gebaeude->geschosshoehe() + 10 );
	}

	public function LmaxTWW(): float {
		// $LmaxTWW=2*($LcharWW+2.5+$nG*$hG); //
		return 2 * ( $this->LcharTWW() + 2.5 + $this->gebaeude->geschossanzahl() * $this->gebaeude->geschosshoehe() );
	}

	public function TERMp(): float {
		// If Heizkörper than
		// $TERMp= 0.13*$LmaxHzg+2+0; //bestimmen in Abhängikeit von $LmaxHzg  && Heizkörper oder Fußbodenheizung, wenn Übergabesytem Heizkörper bzw. Fußbodenheizung prozentrual anteilig zu berechnen  //Tab37, T12
		// if Fußbodenheizung/Wandheizung/Deckenheizung than
		// $TERMp= 0.13*$LmaxHzg+2+25;

		$uebergabesystem = $this->gebaeude->heizsystem()->uebergabesysteme()->erstes();

		switch ( $uebergabesystem->typ() ) {
			case 'heizkoerper':
				return 0.13 * $this->LmaxHzg() + 2 + 0;			
			case 'flaechenheizung':
				return 0.13 * $this->LmaxHzg() + 2 + 25;
			case 'elektroheizungsflaechen':
				return 0;
			default:
				throw new Calculation_Exception( sprintf( 'TERMp für "%s" kann nicht ermittelt werden.', $uebergabesystem->typ() ) );
		}
	}

	public function Vstr(): float {
		// $Vstr= aus TAb 38/T12 in Abhängikeit der h_max und Übergabesystems (Heizkörper 10k; sichere Seite), Fußbodenheizung
		return ( new Volumenstrom_im_Auslegungspunkt( $this->gebaeude->lueftung()->h_max() / 1000, $this->gebaeude->heizsystem()->uebergabesysteme()->erstes()->typ() ) )->V();
	}

	public function PhydrHzg(): float {
		// $PhydrHzg=0.2778*($TERMp+$pg+11)*$Vstr; //10=Pwmz; Pstranga=1 daraus ergibt sich die 11 beides entnommen aus  T12, S.83
		return 0.2778 * ( $this->TERMp() + $this->pg() + 11 ) * $this->Vstr();
	}

	/**
	 * Effizientfaktor für Pumpen Heizung
	 *
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function fe(): float {
		// $fe = ((1.25+(200/$PhydrHzg)^0.5)*2// Banz wir können für Pumpe(2 Faktor) berücksichtigen, da diese als nich bedarfsausgelegt definiert wird
		return ( ( 1.25 + ( 200 / $this->PhydrHzg() ) ) ** 0.5 ) * 2;
	}

	/**
	 * Differenzdruck Pumpe für Heizung (FB + Heizkörper)
	 *
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function TERMpumpe(): float {
		// $TERMpumpe = Nach Tab.40 T12, in Anhängikeit von $ßhd && Bj Heizung bis 1994 ungregelt ab 1995 konstant
		$TERMpumpe = ( new TERMpumpe( $this->gebaeude->heizsystem()->ßhd(), $this->gebaeude->heizsystem()->aelteste_heizungsanlage()->baujahr() ) )->TERMpumpe();
		return $TERMpumpe;
	}

	/**
	 * Faktor für intermitierenden Betrieb (Absenkungsbetrieb, Betriebsnotwendige LAufzeiten)
	 *
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function fint(): float {
		$fint = 1; // Mehrfamilienhaus

		if ( $this->gebaeude->ist_einfamilienhaus() ) {
			$fint = ( new Intermittierender_Pumpenbetrieb( $this->gebaeude->ith_rl(), $this->gebaeude->thm() ) )->fint();
		}

		return $fint;
	}

	/**
	 * Berechnung der Hilfsenergie_Verteilung Lüftung Wrvd
	 *
	 * @return float
	 */
	public function Wrvd(): float {
		// $Wrvd=0   // Laut Banz 7.2 und 7.3
		return 0;
	}

	/**
	 * Berchnung von $Lv (Verteilleitungslänge) und $Ls  (Strangleitungslänge)
	 *
	 * @return float
	 */
	public function Lv(): float {
		// $C2= 0.11; /Tab.10, T8, Einzonenmodell, Gebäudgruppe 1 Rohrnetz komplett im bheizten Bereich oder im unebheiztenbereich. In beiden Fällen gitb die Tabelle die selben werte für Netztyp 1 aus. Wir haben Netztyp ein TWW anlaog zu Heizung definiert.
		// $C3=1.24;
		// $Lv= 0.11*(($calculations['nutzflaeche']/$nG)^1.24) ; // T12, S. 351  $Lv= $C2*(($calculations['nutzflaeche']/$nG)^$C3) diese FOrmel wird unten mir C2 und C3 Faktoren wiedergegeben
		return 0.11 * ( ( $this->gebaeude->nutzflaeche() / $this->gebaeude->geschossanzahl() ) ** 1.24 );
	}

	public function Ls(): float {
		// $C5=0.005;Tab 10 T8
		// $C6=1.38; Tab 10 T8
		// $Ls = 0.005*(($calculations['nutzflaeche']/$nG)^1.38)
		return 0.005 * ( ( $this->gebaeude->nutzflaeche() / $this->gebaeude->geschossanzahl() ) ** 1.38 );
	}

	public function Pwda(): float {
		// $Pwda=0.2*$Lv*(57.5-20)+0.255*$Ls*(57.5-20);
		return 0.2 * $this->Lv() * ( 57.5 - 20 ) + 0.255 * $this->Ls() * ( 57.5 - 20 );
	}

	public function PhydrTWW(): float {
		// Bestimmung $PhydrTWW
		// if $LmaxTWW <= 500 than
		// $∆P=nach Tab 46 in Abhängikeit von $LmaxTWW und "Durchlusssystem"-Spate // wir Durchflusssystzm da hier der ungünstiger.
		// else
		// $∆P=0.1*$LmaxTWW+27;
		// $PhydrTWW = 0.2778*$∆P*($Pwda/(1.15*5*1000);

		if ( $this->LmaxTWW() <= 500 ) {
			$AP = ( new Differenzdruck_Leitungslaenge( $this->LmaxTWW() ) )->AP();
		} else {
			$AP = 0.1 * $this->LmaxTWW() + 27;
		}

		return 0.2778 * $AP * ( $this->Pwda() / ( 1.15 * 5 * 1000 ) );
	}

	public function z(): float {
		$z = ( new Laufzeit_Zirkulationspumpe( $this->gebaeude->nutzflaeche(), $this->gebaeude->ist_einfamilienhaus() ) )->z();
		return $z;
	}

	public function Wwd(): float {
		if ( ! $this->gebaeude->trinkwarmwasseranlage()->zentral() ) {
			return 0;
		}

		// ($Cp1+$Cp2) = Konstanten in Anhängikeit der Pumpenregelung
		//
		// if $Bj bis 1994 than
		// ($Cp1+$Cp2) = 1.19; // bedeutet ungeregelte Pumpen laut Banz
		// else
		// ($Cp1+$Cp2) = 1.13 // geregelte Pumpen laut Banz
		//
		//

		// $z= nach Tab. 45 in Abhängikeit von $calculations['nutzflaeche'] und Einfamilienhaus oder Mehrfamilienhaus

		// $Wwd=($PhydrTWW/1000)*365*$z*(1.25+((200/$PhydrTWW)^0.5))*2*($Cp1+$Cp2); // 365 Tage JAhr T12. Seite 90, $b=2 "pumpe nicht auf Bedarf ausgelegt" laut Banz zulässig,

		if ( $this->gebaeude->heizsystem()->aelteste_heizungsanlage()->baujahr() <= 1994 ) {
			$Cp = 1.19;
		} else {
			$Cp = 1.13;
		}

		$z = $this->z();

		$Wwd = ( $this->PhydrTWW() / 1000 ) * 365 * $z * ( 1.25 + ( 200 / $this->PhydrTWW() ) ** 0.5 ) * 2 * $Cp;
		return $Wwd;
	}

	/**
	 * Berechnung der Hilfsenergie_Solarpumpe
	 *
	 * @return float
	 */
	public function WsolPumped(): float {
		return 0;
	}

	/**
	 * Berechnung Hilfsenergie Heizung Whs
	 *
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function Whs(): float {
		if( $this->gebaeude->heizsystem()->uebergabesysteme()->erstes()->typ() === 'elektroheizungsflaechen' ) {
			return 0;
		}

		// if "Pufferspeicher nicht vorhanden" then
		// $Whs=0.0;
		if ( ! $this->gebaeude->heizsystem()->pufferspeicher_vorhanden() ) {
			return 0;
		}

		// else
		// $Whs0=0.15*$calculations['nutzflaeche']+200;// Gl. 63, T12
		// $Whs=$Whs0*($calculations['ith,rl']/5000);

		$Whs0 = 0.15 * $this->gebaeude->nutzflaeche() + 200;
		return $Whs0 * ( $this->gebaeude->ith_rl() / 5000 );
	}

	/**
	 * Berechnung Hilfsenergie Lüftung Wrvs.
	 *
	 * @return float
	 */
	public function Wrvs(): float {
		// $Wrvs=0.0; // laut Banz 9.2 und 9.3
		return 0;
	}

	/**
	 * Berechnung Hilfsenergie Speicherung Trinkwarmwasser Wws
	 *
	 * @return float
	 */
	public function tpu(): float {
		// $tpu=1.1*($calculations['Qwoutg']/$Pn);
		return 1.1 * ( $this->gebaeude->trinkwarmwasseranlage()->Qwoutg() / ( $this->gebaeude->heizsystem()->pn() / 1000 ) );
	}

	public function Vws(): float {
		// if "keine Solaranlage" than
		// $Vws=$Vsw
		// else
		// $Vws=$Vsaux1+$Vssol1;
        
        if( ! $this->gebaeude->trinkwarmwasseranlage()->zentral() ) {
            return 0;
        }

		if ( ! $this->gebaeude->trinkwarmwasseranlage()->solarthermie_vorhanden() ) {
			return $this->gebaeude->trinkwarmwasseranlage()->Vsw();
		}

		return $this->gebaeude->trinkwarmwasseranlage()->Vsaux() + $this->gebaeude->trinkwarmwasseranlage()->Vssol();
	}

	public function Wws0(): float {
		// Tab 58 wird in Abhängigkeit von $Vws interpoliert Hilfsenergieaufwand Ladepumpe
		// if $Vws >1500 than
		// $Wws1= Tab58 $Vws1 = 1500 //Hilfsenergie bestimmen
		// $Wws2= Bedingung (3000liter-$Vws1) = Differnezwert in Tab 58 und Spalte Hilfsenergie Ladepumpe Stromverbrauch interpolieren
		// $Wws0= $Wws1+$Wws2
		// else
		// $Wws0= Tab58 $Vws;  //Hilfsenergie bestimmen

        $Vws = $this->Vws();

        if ( $Vws > 1500 ) {
            $Wws1 = ( new Hilfsenergieaufwand_Ladepumpe( 1500 ) )->Wws0();
            $Wws2 = ( new Hilfsenergieaufwand_Ladepumpe( $Vws - 1500 ) )->Wws0();
            return $Wws1 + $Wws2;
        }

        return ( new Hilfsenergieaufwand_Ladepumpe( $Vws ) )->Wws0();
	}

    public function Wws(): float {
		if ( ! $this->gebaeude->trinkwarmwasseranlage()->zentral() ) {
			return 0;
		}
		
        // $Wws=$Wws0*($tpu/8760);
        return $this->Wws0() * ( $this->tpu() / 8760 );
    }

	/**
	 * Hilfsenergie Heizung im Bereich Verteilung.
	 * 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function Whd(): float {
		if( $this->gebaeude->heizsystem()->uebergabesysteme()->erstes()->typ() === 'elektroheizungsflaechen' ) {
			return 0;
		}

		// $Whd=($PhydrHzg/1000)*$ßhd*$thm*1*1*$fe*$TERMpumpe*(0.25/0.25)*$fint; 
		return ( $this->PhydrHzg() / 1000 ) * $this->gebaeude->heizsystem()->ßhd() * $this->gebaeude->thm() * 1 * 1 * $this->fe() * $this->TERMpumpe() * ( 0.25 / 0.25 ) * $this->fint();
	}

    public function WsolPumpes(): float {
        // Berechnung Hilfsenergie Solarpumpe Speicherung
        return 0;
    }

	/**
	 * Hilfsenergie Heizsystem.
	 * 
	 * @return float
	 */
	public function Wh(): float {
		// $Wh=$Whce + $Whd + $Whs + $Whg1;   // >Heizsystem 
		// $Wh=$Whce + $Whd + $Whs + $Whg1 + $Whg2;   // >Heizsystem zwei Kessel
		// $Wh=$Whce + $Whd + $Whs + $Whg1 + $Whg2 + $Whg3;   // >Heizsystem  drei Kessel

		$Wh = $this->Whce() + $this->Whd() + $this->Whs();

		foreach( $this->gebaeude->heizsystem()->heizungsanlagen()->alle() as $heizungsanlage ) {			
			$Wh += $heizungsanlage->Whg();	
		}

		return $Wh;
	}

	/**
	 * Hilfsenergie Trinkwarmwasser.
	 */
	public function Ww(): float {
		// $Ww=$Wwce + $Wwd + $Wws + $Wwg1;   // TWW-System     
		// $Ww=$Wwce + $Wwd + $Wws + $Wwg1 + $Wwg2   // TWW-System       
		// $Ww=$Wwce + $Wwd + $Wws + $Wwg1 + $Wwg2 + $Wwg3   // TWW-System 

		$Ww = $this->Wwce() + $this->Wwd() + $this->Wws();

		foreach( $this->gebaeude->heizsystem()->heizungsanlagen()->alle() as $heizungsanlage ) {			
			$Ww += $heizungsanlage->Wwg();	
		}

		return $Ww;
	}

	/**
	 * Hilfsenergie Lüftung.
	 * 
	 * @return float
	 */
	public function Wrv(): float {
		// $Wrv=$Wrvce + Wrvd + Wrvs + Wrvg;  // Lüftung 
		return $this->Wrvce() + $this->Wrvd() + $this->Wrvs() + $this->gebaeude->lueftung()->Wrvg();
	}

	public function WsolPumpeg(): float {
		// $WsolPumpeg=0.025*$Qwsola1;
		return 0.025 * $this->gebaeude->trinkwarmwasseranlage()->Qwsola();
	}

	/**
	 * Hilfsenergie Solar.
	 * 
	 * @return float
	 */
	public function WsolPumpe(): float {
		// $WsolPumpe=$WsolPumpece + WsolPumped + WsolPumpes + WsolPumpeg; // Solarpumpe 
		return $this->WsolPumpece() + $this->WsolPumped() + $this->WsolPumpes() + $this->WsolPumpeg();
	}

	/**
	 * Hilfsenergie Gesamt.
	 * 
	 * @return float
	 */
	public function Wges(): float {
		// $W=$Wh + $Ww + $Wrv + $WsolPumpe;  // Gesamt
		return $this->Wh() + $this->Ww() + $this->Wrv() + $this->WsolPumpe();
	}
}
