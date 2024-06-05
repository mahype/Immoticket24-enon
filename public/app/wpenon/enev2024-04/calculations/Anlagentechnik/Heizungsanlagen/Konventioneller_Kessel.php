<?php

namespace Enev\Schema202404\Calculations\Anlagentechnik\Heizungsanlagen;

use Enev\Schema202404\Calculations\Anlagentechnik\Heizungsanlage;
use Enev\Schema202404\Calculations\Calculation_Exception;
use Enev\Schema202404\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202404\Calculations\Tabellen\Aufwandszahlen_Brennwertkessel;
use Enev\Schema202404\Calculations\Tabellen\Aufwandszahlen_Heizwaermeerzeugung;
use Enev\Schema202404\Calculations\Tabellen\Aufwandszahlen_Heizwaermeerzeugung_Korrekturfaktor;
use Enev\Schema202404\Calculations\Tabellen\Aufwandszahlen_Umlaufwasserheizer;
use Enev\Schema202404\Calculations\Tabellen\Betriebsbereitschaftsleistung_Pellet_Holzhackschnitzelkessel;
use Enev\Schema202404\Calculations\Tabellen\Brennwertkessel_Hilfsenergieaufwand;
use Enev\Schema202404\Calculations\Tabellen\Laufzeit_Waermeerzeuger_Trinkwassererwaermung;
use Enev\Schema202404\Calculations\Tabellen\Umlaufwasserheizer_Hilfsenergieaufwand;
use Enev\Schema202404\Calculations\Tabellen\Pelletkessel_Hilfsenergieaufwand;

require_once dirname(dirname(__DIR__)) . '/Tabellen/Aufwandszahlen_Brennwertkessel.php';
require_once dirname(dirname(__DIR__)) . '/Tabellen/Aufwandszahlen_Umlaufwasserheizer.php';
require_once dirname(dirname(__DIR__)) . '/Tabellen/Aufwandszahlen_Heizwaermeerzeugung.php';
require_once dirname(dirname(__DIR__)) . '/Tabellen/Aufwandszahlen_Heizwaermeerzeugung_Korrekturfaktor.php';
require_once dirname(dirname(__DIR__)) . '/Tabellen/Korrekturfaktoren_Gas_Spezial_Heizkessel.php';
require_once dirname(dirname(__DIR__)) . '/Tabellen/Korrekturfaktoren_Holzhackschnitzelkessel.php';
require_once dirname(dirname(__DIR__)) . '/Tabellen/Laufzeit_Waermeerzeuger_Trinkwassererwaermung.php';
require_once dirname(dirname(__DIR__)) . '/Tabellen/Brennwertkessel_Hilfsenergieaufwand.php';
require_once dirname(dirname(__DIR__)) . '/Tabellen/Betriebsbereitschaftsleistung_Pellet_Holzhackschnitzelkessel.php';
require_once dirname(dirname(__DIR__)) . '/Tabellen/Umlaufwasserheizer_Hilfsenergieaufwand.php';
require_once dirname(dirname(__DIR__)) . '/Tabellen/Pelletkessel_Hilfsenergieaufwand.php';


class Konventioneller_Kessel extends Heizungsanlage
{
	/**
	 * Konstruktor.
	 *
	 * @param Gebaeude   $gebaeude Gebäude.
	 * @param string     $erzeuger Erzeuger (standardkessel, niedertemperaturkessel, brennwertkessel, kleinthermeniedertemperatur, kleinthermebrennwert, pelletfeuerung, gasraumheizer, oelofenverdampfungsverbrenner).
	 * @param string     $energietraeger $energietraeger Energieträger (heizoel, erdgas, fluessiggas, biogas, holzpellets, holzhackschnitzel).
	 * @param int        $baujahr Baujahr der Heizung.
	 * @param int        $prozentualer_anteil Prozentualer Anteil der Heizungsanlage im Heizsystem.
	 * @param float|null $fp                  Manuell gesetzter Primärenergiefaktor.
	 * @param float|null $fco2                Manuell gesetzter CO2 Emissionsfaktor.
	 *
	 * @return void
	 */
	public function __construct(
		Gebaeude $gebaeude,
		string $erzeuger,
		string $energietraeger,
		int $baujahr,
		int $prozentualer_anteil = 100,
		float|null $fp = null,
		float|null $fco2 = null
	) {
		parent::__construct($gebaeude, $erzeuger, $energietraeger, $baujahr, $gebaeude->heizsystem()->beheizt(), $prozentualer_anteil, $fp, $fco2);
	}

	/**
	 * Erlaubte Typen für konventionelle Kessel.
	 *
	 * @return array
	 */
	public static function erlaubte_erzeuger(): array
	{
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
			'feststoffkessel'        => array(
				'name'           => 'Feststoffkessel',
				'typ'            => 'feststoffkessel',
				'energietraeger' => array(
					'steinkohle' => 'Steinkohle',
					'braunkohle' => 'Braunkohle',
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

	public function fbjw(): float
	{
		// if "Umlaufwasserheizer" than
		// $fbj = Tab 82 T12, in Anhängigkeit von "Umlaufwasserheizer" und $ßhg
		// else
		// $fbj = Tab 78 T12, in Anhängigkeit von "Baujahr der Heizung" und $ßhg
		if ($this->typ() === 'umlaufwasserheizer') {
			return (new Aufwandszahlen_Umlaufwasserheizer($this->gebaeude->heizsystem()->pn(), $this->ßwg()))->ewg0();
		}

		return (new Aufwandszahlen_Heizwaermeerzeugung_Korrekturfaktor($this->erzeuger(), $this->energietraeger(), $this->baujahr(), $this->ßwg()))->f_temp();
	}

	public function fegtw(): float
	{
		$fegt = (new Aufwandszahlen_Heizwaermeerzeugung(
			$this->erzeuger(),
			$this->energietraeger(),
			$this->gebaeude->heizsystem()->uebergabesysteme()->erstes()->auslegungstemperaturen(),
			$this->ßwg(),
			$this->heizung_im_beheizten_bereich()
		)
		)->fegtw();

		return $fegt;
	}

	public function ßwg(): float
	{
		return 1;
	}

	public function ewg0(): float
	{
		$pn  = $this->gebaeude->heizsystem()->pn() / 1000;
		$ßwg = $this->ßwg();

		if ($this->typ() === 'umlaufwasserheizer') {
			$ewg0 = (new Aufwandszahlen_Umlaufwasserheizer($pn, $ßwg))->ewg0();
		} else {
			$ewg0 = (new Aufwandszahlen_Brennwertkessel($pn, $ßwg))->ewg0();
		}

		return $ewg0;
	}

	/**
	 * ewg
	 *
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function ewg(): float
	{
		// Basis ewg Wert
		$ewg = $this->ewg0() * $this->fbjw() * $this->fegtw();
		// Korrekturfaktor für Kessel
		return 1 + ($ewg - 1) * (8760 / $this->gebaeude->ith_rl());
	}

	/**
	 * Korrigierter Korrekturfaktor für die Heizungsanlage.
	 *
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function ehg(): float
	{
		// Basis ehg Wert
		$ehg = $this->eg0() * $this->fbaujahr() * $this->fegt();
		// Korrekturfaktor für Kessel
		return 1 + ($ehg - 1) * (8760 / $this->gebaeude->ith_rl());
	}

	/**
	 * Bestimmung von $eg0.
	 *
	 * @return float
	 */
	public function eg0(): float
	{
		// if "Umlaufwasserheizer" than
		// $eg0 = Tab 82 T12, in Anhängigkeit von $Pn und $ßhg
		// else
		// $eg0 = Tab 77 T12, in Anhängigkeit von $Pn und $ßhg,

		$pn  = $this->gebaeude->heizsystem()->pn() / 1000;
		$ßhg = $this->ßhg();

		if ($this->typ() === 'umlaufwasserheizer') {
			$eg0 = (new Aufwandszahlen_Umlaufwasserheizer($pn, $ßhg))->eg0();
		} else {
			$eg0 = (new Aufwandszahlen_Brennwertkessel($pn, $ßhg))->eg0();
		}

		return $eg0;
	}

	public function ßhg(): float
	{
		// $ßhg = $ßhs * $ehs // mittlere Belastung der *Übergabe Heizung; damit für Alle Erzeuger (einschl Fern/Nahwärme)
		return $this->gebaeude->heizsystem()->ßhs() * $this->gebaeude->heizsystem()->ehs();
	}

	public function fbaujahr(): float
	{
		// if "Umlaufwasserheizer" than
		// $fbj = Tab 82 T12, in Anhängigkeit von "Umlaufwasserheizer" und $ßhg
		// else
		// $fbj = Tab 78 T12, in Anhängigkeit von "Baujahr der Heizung" und $ßhg
		if ($this->typ() === 'umlaufwasserheizer') {
			return (new Aufwandszahlen_Umlaufwasserheizer($this->gebaeude->heizsystem()->pn(), $this->ßhg()))->eg0();
		}

		$baujahr = !$this->gebaeude->ist_referenzgebaeude() ? $this->baujahr() : 1995;

		return (new Aufwandszahlen_Heizwaermeerzeugung_Korrekturfaktor($this->erzeuger(), $this->energietraeger(), $baujahr, $this->ßhg()))->f_temp();
	}

	public function fegt(): float
	{
		$fegt = (new Aufwandszahlen_Heizwaermeerzeugung(
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
	 * Korrekturfaktor Hilfsenergie Heizung im Bereich Erzeugung.
	 *
	 * @return float
	 */
	public function fphgaux(): float
	{
		// if "Brennwertheizung (Öl+Gas)", "Gasetagenheizung(Gas)" und "(Brennwert+Standard) für Pellet, Stückholz, Hackschnitzel mit Baujahr ab 1995" than
		// $fphgaux=1.0;
		// if "Brennwert + Standard für Pellet, Stückholz, Hackschnitzel mit Baujahr bis 1995"
		// $fphgaux = Tab. 86 T12 in Anhängikeit $Pn und $ßhg;
		// if "Standardkessel & NT Kessel (Öl+Gas)" "Feststoffkessel"than
		// $fphgaux = Tab.84, T12 in Anhängikeit $Pn und $ßhg;

		// Holz bei Standard- und Brennwertkesseln.
		// Kesseltyp nicht abgefragt, da Holz nur bei Standard- und Brennwertkesseln möglich ist.

		// if ( $this->energietraeger() === 'holzpellets' || $this->energietraeger() === 'stueckholz' || $this->energietraeger() === 'holzhackschnitzel' ) {
		// if ( $this->baujahr() >= 1995 ) {
		// return 1.0;
		// } else {
		// return ( new Korrekturfaktoren_Holzhackschnitzelkessel( $this->gebaeude->heizsystem()->pn(), $this->ßhg() ) )->fphgaux();
		// }
		// }

		// if ( $this->erzeuger() === 'brennwertkessel' || $this->erzeuger() === 'etagenheizung' ) {
		// return 1.0;
		// }

		// return ( new Korrekturfaktoren_Gas_Spezial_Heizkessel( $this->gebaeude->heizsystem()->pn(), $this->ßhg() ) )->fphgaux();

		return 1; // Norm sagt nicht klar aus, welcher Wert hier verwendet werden soll. Seite 130/131 Teil 12 völlig missverständlich.
	}

	/**
	 * Korrekturfaktor für die Hilfsenergie Erzeugung Warmwasser.
	 *
	 * @return float
	 */
	public function fpwgaux(): float
	{
		// if "Brennwertheizung (Öl+Gas)", "Gasetagenheizung(Gas)" und "(Brennwert+Standard) für Pellet, Stückholz, Hackschnitzel mit Baujahr ab 1995" than
		// $fphgaux=1.0;
		// if "Brennwert + Standard für Pellet, Stückholz, Hackschnitzel mit Baujahr bis 1995"
		// $fphgaux = Tab. 86 T12 in Anhängikeit $Pn und $ßhg;
		// if "Standardkessel & NT Kessel (Öl+Gas)" "Feststoffkessel"than
		// $fphgaux = Tab.84, T12 in Anhängikeit $Pn und $ßhg;

		// Holz bei Standard- und Brennwertkesseln.
		// Kesseltyp nicht abgefragt, da Holz nur bei Standard- und Brennwertkesseln möglich ist.

		// $ßhg = 1;
		// if ( $this->energietraeger() === 'holzpellets' || $this->energietraeger() === 'stueckholz' || $this->energietraeger() === 'holzhackschnitzel' ) {
		// if ( $this->baujahr() >= 1995 ) {
		// return 1.0;
		// } else {
		// return ( new Korrekturfaktoren_Holzhackschnitzelkessel( $this->gebaeude->heizsystem()->pn(), $ßhg) )->fphgaux();
		// }
		// }

		// if ( $this->erzeuger() === 'brennwertkessel' || $this->erzeuger() === 'etagenheizung' ) {
		// return 1.0;
		// }

		// return ( new Korrekturfaktoren_Gas_Spezial_Heizkessel( $this->gebaeude->heizsystem()->pn(), $ßhg) )->fphgaux();

		return 1; // Norm sagt nicht klar aus, welcher Wert hier verwendet werden soll. Seite 130/131 Teil 12 völlig missverständlich.
	}

	/**
	 * Elektrische Hilfsenergieleistung für die Heizungsanlage
	 *
	 * @return float
	 */
	public function Phgaux(): float
	{
		// if "Brennwertheizung (Öl+Gas bis heute/ Holz bois 1994)", "Standardkessel (Öl+Gas bis heute/ Holz bois 1994) - NT(Öl+Gas bis heute)", "Feststoffkessel" than
		// $Phgaux=nach Tab. 83 T12 in Abhängigkeit von $Pn und $hg;
		// if  "Brennwert + Standardkessel für Pellet, Stückholz, Hackschnitzel mit Baujahr ab 1995" than
		// $Phgaux = Tab.85, T12 in Anhängikeit $Pn und $ßhg;
		// if "Gasetagenheizung"
		// $Phgaux = Tab. 88 T12 in Anhängikeit $Pn und $ßhg;
		// else????

		if ($this->erzeuger() === 'etagenheizung') {
			return (new Umlaufwasserheizer_Hilfsenergieaufwand($this->gebaeude->heizsystem()->pn() / 1000, $this->ßhg()))->Phgaux();
		}

		if ($this->energietraeger() === 'holzpellets' || $this->energietraeger() === 'stueckholz' || $this->energietraeger() === 'holzhackschnitzel') {
			if ($this->baujahr() > 1994) {
				return (new Pelletkessel_Hilfsenergieaufwand($this->gebaeude->heizsystem()->pn() / 1000, $this->ßhg()))->Phgaux();
			}
		}

		return (new Brennwertkessel_Hilfsenergieaufwand($this->gebaeude->heizsystem()->pn() / 1000, $this->ßhg()))->Phgaux();
	}

	/**
	 * Elektrische Hilfsenergieleistung Trinkwarmwasser im Bereich Erzeugung.
	 *
	 * @return float
	 */
	public function Pwgaux(): float
	{
		// if "Brennwertheizung (Öl+Gas bis heute/ Holz bois 1994)", "Standardkessel (Öl+Gas bis heute/ Holz bois 1994) - NT(Öl+Gas bis heute)", "Feststoffkessel" than
		// $Phgaux=nach Tab. 83 T12 in Abhängigkeit von $Pn und $hg;
		// if  "Brennwert + Standardkessel für Pellet, Stückholz, Hackschnitzel mit Baujahr ab 1995" than
		// $Phgaux = Tab.85, T12 in Anhängikeit $Pn und $ßhg;
		// if "Gasetagenheizung"
		// $Phgaux = Tab. 88 T12 in Anhängikeit $Pn und $ßhg;
		// else????

		if ($this->erzeuger() === 'etagenheizung') {
			return (new Umlaufwasserheizer_Hilfsenergieaufwand($this->gebaeude->heizsystem()->pn() / 1000, 1))->Phgaux();
		}

		if ($this->energietraeger() === 'holzpellets' || $this->energietraeger() === 'stueckholz' || $this->energietraeger() === 'holzhackschnitzel') {
			if ($this->baujahr() > 1994) {
				return (new Pelletkessel_Hilfsenergieaufwand($this->gebaeude->heizsystem()->pn() / 1000, 1))->Phgaux();
			}
		}

		return (new Brennwertkessel_Hilfsenergieaufwand($this->gebaeude->heizsystem()->pn() / 1000, 1))->Phgaux();
	}

	/**
	 * Hilfsenergie - PhauxP0.
	 *
	 * @return float
	 */
	public function PhauxP0(): float
	{
		// if "Gasetagenheizung, Brennwertkessel, NT-Kessel, Festsoffkessel (+ Stückholz) ab 1987" than
		// $PhauxP0=0.015; in kW;
		// if "Gasetagenheizung, Standardkessel, Brennwertkessel, NT-Kessel, Festsoffkessel  (+ Stückholz)  vor 1987" than
		// $PhauxP0=0.15; in kW;
		// if "Pelletheizung", "Hackschnitzelkessel" than
		// $PhauxP0= Tabelle 87 T12 in Anhängigkeit $Pn in Spalte Pelletkessel;
		// else???

		if ($this->energietraeger() === 'holzpellets' || $this->energietraeger() === 'holzhackschnitzel') {
			return (new Betriebsbereitschaftsleistung_Pellet_Holzhackschnitzelkessel($this->gebaeude->heizsystem()->pn() / 1000, 'pelletkessel'))->PhauxP0();
		}

		if ($this->erzeuger() === 'standardkessel') { // Laut S. 129 Teil 12 sind alle Heizungen ohne Regelung = Standardkessel mit 150W.
			return 0.15;
		}

		return 0.015;
	}

	/**
	 * Hilfsenergie - twpn0.
	 *
	 * @return float
	 */
	public function twpn0(): float
	{
		// $twpn0= Tab 140, T12 in Anhägingkeit ($ewd*$ews) und "bei bestehenden Anlagen"
		$ewd_ews = $this->gebaeude->trinkwarmwasseranlage()->ewd() * $this->gebaeude->trinkwarmwasseranlage()->ews();
		return (new Laufzeit_Waermeerzeuger_Trinkwassererwaermung($ewd_ews, 'bestehende_anlagen'))->twpn0();
	}

	/**
	 * Hilfsenergie - twpn - Laufzeit der Wärmeerzeuger für Trinkwassererwärmung.
	 *
	 * @return float
	 */
	public function twpn(): float
	{
		// $twpn= $twpn0*(($calculations['nutzflaeche']*50*$qwb)/($Pn*1000*12.5));
		return $this->twpn0() * (($this->gebaeude->nutzflaeche() * 50 * $this->gebaeude->trinkwarmwasseranlage()->nutzwaermebedarf_trinkwasser()) / ($this->gebaeude->heizsystem()->pn() * 12.5));
	}

	/**
	 * Hilfsenergie für Heizunganlage im Bereich Erzeugung.
	 *
	 * @return float
	 */
	public function Whg(): float
	{
		// $Whg= $fphgaux*$Phgaux*($calculations['ith,rl']-$twpn)+$PhauxP0*(8760-$calculations['ith,rl']);
		return $this->fphgaux() * $this->Phgaux() * ($this->gebaeude->ith_rl() - $this->twpn()) + $this->PhauxP0() * (8760 - $this->gebaeude->ith_rl());
	}

	/**
	 * Hilfsenergie für Warmwasser.
	 *
	 * @return float
	 */
	public function Wwg(): float
	{
		if (!$this->gebaeude->trinkwarmwasseranlage()->zentral()) {
			return 0.0;
		}

		return $this->fpwgaux() * $this->Pwgaux() * $this->twpn();
	}
}
