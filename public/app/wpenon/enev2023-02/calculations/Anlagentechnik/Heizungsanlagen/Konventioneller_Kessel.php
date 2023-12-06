<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik\Heizungsanlagen;

use Enev\Schema202302\Calculations\Anlagentechnik\Heizungsanlage;
use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202302\Calculations\Tabellen\Aufwandszahlen_Brennwertkessel;
use Enev\Schema202302\Calculations\Tabellen\Aufwandszahlen_Heizwaermeerzeugung;
use Enev\Schema202302\Calculations\Tabellen\Aufwandszahlen_Heizwaermeerzeugung_Korrekturfaktor;
use Enev\Schema202302\Calculations\Tabellen\Aufwandszahlen_Umlaufwasserheizer;
use Enev\Schema202302\Calculations\Tabellen\Korrekturfaktoren_Gas_Spezial_Heizkessel;

require_once dirname( dirname( __DIR__ ) ) . '/Tabellen/Aufwandszahlen_Brennwertkessel.php';
require_once dirname( dirname( __DIR__ ) ) . '/Tabellen/Aufwandszahlen_Umlaufwasserheizer.php';
require_once dirname( dirname( __DIR__ ) ) . '/Tabellen/Aufwandszahlen_Heizwaermeerzeugung.php';
require_once dirname( dirname( __DIR__ ) ) . '/Tabellen/Aufwandszahlen_Heizwaermeerzeugung_Korrekturfaktor.php';
require_once dirname( dirname( __DIR__ ) ) . '/Tabellen/Korrekturfaktoren_Gas_Spezial_Heizkessel.php';


class Konventioneller_Kessel extends Heizungsanlage {
	/**
	 * Gebaeude.
	 *
	 * @var Gebaeude
	 */
	protected Gebaeude $gebaeude;

	/**
	 * Konstruktor.
	 *
	 * @param Gebaeude $gebaeude Gebäude.
	 * @param string   $erzeuger Erzeuger (standardkessel, niedertemperaturkessel, brennwertkessel, kleinthermeniedertemperatur, kleinthermebrennwert, pelletfeuerung, gasraumheizer, oelofenverdampfungsverbrenner).
	 * @param string   $energietraeger $energietraeger Energieträger (heizoel, erdgas, fluessiggas, biogas, holzpellets, holzhackschnitzel).
	 * @param int      $baujahr Baujahr der Heizung.
	 * @param int      $prozentualer_anteil Prozentualer Anteil der Heizungsanlage im Heizsystem.
	 *
	 * @return void
	 */
	public function __construct(
		Gebaeude $gebaeude,
		string $erzeuger,
		string $energietraeger,
		int $baujahr,
		int $prozentualer_anteil = 100,
	) {
		parent::__construct( $erzeuger, $energietraeger, $baujahr, $gebaeude->heizsystem()->beheizt(), $prozentualer_anteil );
		$this->gebaeude = $gebaeude;
	}

	/**
	 * Erlaubte Typen für konventionelle Kessel.
	 *
	 * @return array
	 */
	public static function erlaubte_erzeuger(): array {
		return array(
			'standardkessel'         => array(
				'name'           => 'Standardkessel',
				'typ'            => 'standardkessel',
				'energietraeger' => array(
					'heizoel'           => 'Heizöl',
					'erdgas'            => 'Erdgas',
					'fluessiggas'       => 'Flüssiggas',
					'biogas'            => 'Biogas',
					'holzpellets'       => 'Holzpellets',
					'holzhackschnitzel' => 'Holzhackschnitzel',
					'stueckholz'        => 'Stückholz',
					'steinkohle'        => 'Steinkohle',
					'braunkohle'        => 'Braunkohle',
				),
			),
			'niedertemperaturkessel' => array(
				'name'           => 'Niedertemperaturkessel',
				'typ'            => 'niedertemperaturkessel',
				'energietraeger' => array(
					'heizoel'     => 'Heizöl',
					'erdgas'      => 'Erdgas',
					'fluessiggas' => 'Flüssiggas',
					'biogas'      => 'Biogas',
				),
			),
			'brennwertkessel'        => array(
				'name'           => 'Brennwertkessel',
				'typ'            => 'brennwertkessel',
				'energietraeger' => array(
					'heizoel'           => 'Heizöl',
					'erdgas'            => 'Erdgas',
					'fluessiggas'       => 'Flüssiggas',
					'biogas'            => 'Biogas',
					'holzpellets'       => 'Holzpellets',
					'holzhackschnitzel' => 'Holzhackschnitzel',
					'stueckholz'        => 'Stückholz',
				),
			),
			'etagenheizung'          => array(
				'name'           => 'Etagenheizung',
				'typ'            => 'umlaufwasserheizer',
				'energietraeger' => array(
					'heizoel'     => 'Heizöl',
					'erdgas'      => 'Erdgas',
					'fluessiggas' => 'Flüssiggas',
					'biogas'      => 'Biogas',
				),
			),
		);
	}

	/**
	 * Bestimmung von $eg0.
	 *
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function ewg(): float {
		// $ewg= ($eg-1)*8760/$calculations['ithrl']+1;
		return ( $this->ehg() - 1 ) * 8760 / $this->gebaeude->ith_rl() + 1;
	}

	/**
	 * Erzeugung Korrekturfaktur für die Heizungsanlage.
	 *
	 * @return float
	 */
	public function ehg(): float {
		// $eg=$eg0*$fbj*$fegt
		return $this->eg0() * $this->fbj() * $this->fegt();
	}

	/**
	 * Bestimmung von $eg0.
	 *
	 * @return float
	 */
	public function eg0(): float {
		// if "Umlaufwasserheizer" than
		// $eg0 = Tab 82 T12, in Anhängigkeit von $Pn und $ßhg
		// else
		// $eg0 = Tab 77 T12, in Anhängigkeit von $Pn und $ßhg

		$pn  = $this->gebaeude->heizsystem()->pn() / 1000;
		$ßhg = $this->ßhg();

		if ( $this->typ() === 'umlaufwasserheizer' ) {
			$eg0 = ( new Aufwandszahlen_Umlaufwasserheizer( $pn, $ßhg ) )->eg0();
		} else {
			$eg0 = ( new Aufwandszahlen_Brennwertkessel( $pn, $ßhg ) )->eg0();
		}

		return $eg0;
	}

	public function ßhg(): float {
		// $ßhg = $ßhs * $ehs // mittlere Belastung der *Übergabe Heizung; damit für Alle Erzeuger (einschl Fern/Nahwärme)
		return $this->gebaeude->heizsystem()->ßhs() * $this->gebaeude->heizsystem()->ehs();
	}

	public function fbj(): float {
		// if "Umlaufwasserheizer" than
		// $fbj = Tab 82 T12, in Anhängigkeit von "Umlaufwasserheizer" und $ßhg
		// else
		// $fbj = Tab 78 T12, in Anhängigkeit von "Baujahr der Heizung" und $ßhg
		if ( $this->typ() === 'umlaufwasserheizer' ) {
			return ( new Aufwandszahlen_Umlaufwasserheizer( $this->gebaeude->heizsystem()->pn(), $this->ßhg() ) )->eg0();
		}

		return ( new Aufwandszahlen_Heizwaermeerzeugung_Korrekturfaktor( $this->erzeuger(), $this->energietraeger(), $this->baujahr(), $this->ßhg() ) )->f_temp();
	}

	public function fegt(): float {
		$fegt = ( new Aufwandszahlen_Heizwaermeerzeugung(
			$this->erzeuger(),
			$this->energietraeger(),
			$this->gebaeude->heizsystem()->uebergabesysteme()->erstes()->auslegungstemperaturen(),
			$this->ßhg(),
			$this->heizung_im_beheizten_bereich()
		)
		)->fegt();

		return $fegt;
	}

	/**
	 * Hilfsenenergiebedarf für die Heizungsanlage.
	 * 
	 * @return void 
	 */
	public function fphgaux() {
		//  if "Brennwertheizung", "Gasetagenheizung" und "Heizung Pellet, Stückholz, Hackschnitzel mit Baujahr ab 1995" than   
		//     $fphgaux=1.0;
		//  if "Standardkessel NT Kessel" "Feststoffkessel"than
		//      $fphgaux = Tab.84, T12 in Anhängikeit $Pn und $ßhg;
		//   if "Heizung Pellet, Stückholz, Hackschnitzel mit Baujahr älter 1995"
		//      $fphgaux = Tab. 86 T12 in Anhängikeit $Pn und $ßhg;
		// else????

		if( $this->energietraeger() === 'holzpellets' || $this->energietraeger() === 'holzhackschnitzel' || $this->energietraeger() === 'stueckholz' ) {
			if( $this->baujahr() >= 1995 ) {
				return 1.0;
			}
		}

		if( $this->typ() === 'brennwertkessel' || $this->typ() === 'etagenheizung' ) {
			return ( new Korrekturfaktoren_Gas_Spezial_Heizkessel( $this->gebaeude->heizsystem()->pn(), $this->ßhg() ) )->fphgaux();
		}
	}
}
