<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;

class Konventioneller_Kessel extends Heizungsanlage {
	/**
	 * Pufferspeicher.
	 *
	 * @var Gebaeude
	 */
	protected Gebaeude $gebaeude;

	/**
	 * Konstruktor.
	 *
	 * @param string $typ Typ der Heizung (standardkessel, niedertemperaturkessel, brennwertkessel, kleinthermeniedertemperatur, kleinthermebrennwert, pelletfeuerung, gasraumheizer, oelofenverdampfungsverbrenner).
	 * @param string $energietraeger $energietraeger Energieträger (heizoel, erdgas, fluessiggas, biogas, holzpellets, holzhackschnitzel).
	 * @param string $auslegungstemperaturen
	 * @param bool   $heizung_im_beheizten_bereich
	 * @param int    $prozentualer_anteil
	 * @param float  $ithrl
	 *
	 * @return void
	 */
	public function __construct(
		Gebaeude $gebaeude,
		string $typ,
		string $energietraeger,
		string $auslegungstemperaturen,
		bool $heizung_im_beheizten_bereich,
		int $prozentualer_anteil = 100,
	) {
		parent::__construct( $typ, $energietraeger, $auslegungstemperaturen, $heizung_im_beheizten_bereich, $prozentualer_anteil );
		$this->gebaeude = $gebaeude;
	}

	/**
	 * Erlaubte Typen für konventionelle Kessel.
	 *
	 * @return array
	 */
	public static function erlaubte_typen(): array {
		return array(
			'standardkessel'              => array(
				'name'           => 'Standardkessel',
                'typ' => 'standardkessel',                
				'energietraeger' => array(
					'heizoel'     => 'Heizöl',
					'erdgas'      => 'Erdgas',
					'fluessiggas' => 'Flüssiggas',
					'biogas'      => 'Biogas',
				),
			),
			'brennwertkessel'             => array(
				'name'           => 'Brennwertkessel',
                'typ' => 'brennwertkessel',
				'energietraeger' => array(
					'heizoel'     => 'Heizöl',
					'erdgas'      => 'Erdgas',
					'fluessiggas' => 'Flüssiggas',
					'biogas'      => 'Biogas',
				),
			),
			'kleinthermeniedertemperatur' => array(
				'name'           => 'Kleintherme Niedertemperatur',
                'typ' => 'umlaufwasserheizer',
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
		return ( $this->eg() - 1 ) * 8760 / $this->gebaeude->ith_rl() + 1;
	}

	/**
	 *
	 * @return float
	 */
	public function eg(): float {
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
		return 0.0;
	}

	public function ßhg(): float {
		// $ßhg = $ßhs * $ehs // mittlere Belastung der *Übergabe Heizung; damit für Alle Erzeuger (einschl Fern/Nahwärme)
		return $this->gebaeude->heizsystem()->pufferspeicher()->ßhs() * $this->gebaeude->heizsystem()->pufferspeicher()->ehs();
	}

	public function fbj(): float {
		// if "Umlaufwasserheizer" than
		// $fbj = Tab 82 T12, in Anhängigkeit von "Umlaufwasserheizer" und $ßhg
		// else
		// $fbj = Tab 78 T12, in Anhängigkeit von "Baujahr der Heizung" und $ßhg
		return 0.0;
	}

	public function fegt(): float {
		// if Umlaufwasserheizer &&  "Energieträger = Hackschnitzel" && "Energieträger = Scheitholz" && "Energieträger = Pellet"  than
		// $fegt = 1.0
		// if  "Brennwertkessel" &&  "Energieträger = Gas" && "Energieträger = Biogas" && "Energieträger = Flüssiggas"  than
		// $fegt = Tab.79  in Abhängigkeit  "Vor- und Rücklauftemperatur" und $ßhg und "unbeheizt/beheizt"
		// if  "Brennwertkessel" &&  "Energieträger = Heizöl" && "Energieträger = Bioöl"  than
		// $fegt = Tab.80  in Abhängigkeit  "Vor- und Rücklauftemperatur" und $ßhg und "unbeheizt/beheizt"
		// else
		// $fegt = Tab. 81 in Abhängigkeit  "Vor- und Rücklauftemperatur" und $ßhg und "unbeheizt/beheizt"
		return 0.0;
	}
}
