<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202302\Calculations\Tabellen\Kessel_Nennleistung;
use Enev\Schema202302\Calculations\Tabellen\Mittlere_Belastung_Pufferspeicher_Korrekturfaktor;

require_once dirname( __DIR__ ) . '/Tabellen/Kessel_Nennleistung.php';
require_once dirname( __DIR__ ) . '/Tabellen/Mittlere_Belastung_Pufferspeicher_Korrekturfaktor.php';

class Pufferspeicher {
	/**
	 * Gebäude.
	 *
	 * @var Gebaeude
	 */
	protected Gebaeude $gebaeude;

	/**
	 * Pufferspeicher Größe in Liter.
	 *
	 * @var float
	 */
	protected float|null $puffergroesse = null;

	/**
	 * Übergabesystem.
	 *
	 * @var Uebergabesystem
	 */
	protected Uebergabesystem $uebergabesystem;

	/**
	 * Liegt eine Warmwasserspeicher vor
	 *
	 * @param Gebaeude   $gebaeude               Gebäude.
	 * @param float|null $puffergroesse         Puffergröße in Liter.
	 */
	public function __construct(
		Gebaeude $gebaeude,
		float $puffergroesse = null
	) {
		$this->gebaeude      = $gebaeude;
		$this->puffergroesse = $puffergroesse;
	}

	/**
	 * Volumen des Pufferspeichers.
	 * 
	 * @return float 
	 * 
	 * @throws Calculation_Exception 
	 */
	public function volumen(): float {
		if ( $this->puffergroesse ) {
			return $this->puffergroesse;
		}

		$biomassekessel = $this->gebaeude->heizsystem()->heizungsanlagen()->biomassekessel_vorhanden();
		$waermepumpe    = $this->gebaeude->heizsystem()->heizungsanlagen()->waermepumpe_vorhanden();

		// Wenn Biomassekessel vorhanden ist, dann wird der Pufferspeicher immer mit Faktor 50 berechnet, egal ob Wärmepumpe vorhanden oder nicht.
		if ( $biomassekessel ) {
			return 50 * $this->pn();
		}

		// Wenn Wärmepumpe vorhanden ist, aber kein Biomassekessel dann wird der Pufferspeicher immer Faktor 9.5 berechnet.
		if ( $waermepumpe ) {
			return 9.5 * $this->pn();
		}

		// Alle Anderen Speicher.
		return 0;
	}

	/**
	 * Nennleistung des Pufferspeichers (pwn).
	 *
	 * @return float
	 */
	public function pwn(): float {
		$pwn = 0;

		if ( $this->gebaeude->wasserversorgung()->zentral() ) {
			$nutzwaermebedarf_trinkwasser = $this->gebaeude->wasserversorgung()->nutzwaermebedarf_trinkwasser();

			if ( $this->gebaeude->nutzflaeche() > 5000 ) {
				$pwn = 0.042 * ( ( $nutzwaermebedarf_trinkwasser * $this->gebaeude->nutzflaeche() ) / ( 365 * 0.036 ) ) ** 0.7;
			} else {
				$pwn = ( new Kessel_Nennleistung( $this->gebaeude->nutzflaeche(), $nutzwaermebedarf_trinkwasser ) )->nennleistung() * 1000; // Umrechnung in Watt
			}
		}

		if ( $this->gebaeude->luftwechsel()->h_max() > $pwn ) {
			return $this->gebaeude->luftwechsel()->h_max();
		}

		return $pwn;
	}

	/**
	 * Nennleistung des Pufferspeichers (pn).
	 * 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function pn(): float {
		if ( $this->gebaeude->wasserversorgung()->zentral() ) {
			if ( $this->gebaeude->heizsystem()->heizungsanlagen()->waermepumpe_vorhanden() ) {
				return 1.3 * $this->gebaeude->luftwechsel()->h_max();
			} else {
				return 1.5 * $this->pwn();
			}
		}

		if ( $this->gebaeude->heizsystem()->heizungsanlagen()->waermepumpe_vorhanden() ) {
			return $this->gebaeude->luftwechsel()->h_max();
		} else {
			return 1.5 * $this->pwn();
		}
	}
	/**
	 * Mittlere Belastung für Speicherung.
	 *
	 * @param Heizungsanlage $heizungsanlage
	 * @return float
	 *
	 * @throws Calculation_Exception
	 */
	public function ßhs(): float {
		// $ßhs=$ßhd*$ehdkorr
		return $this->gebaeude->heizsystem()->ßhd() * $this->gebaeude->heizsystem()->ehd_korrektur();
	}

	/**
	 * Korrektrufaktor mittlere Belastung des Pufferspeichers.
	 *
	 * @param Heizungsanlage $heizungsanlage
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function fßhs(): float {
		return ( new Mittlere_Belastung_Pufferspeicher_Korrekturfaktor( $this->gebaeude->heizsystem()->uebergabesysteme()->erstes()->auslegungsvorlauftemperatur(), $this->gebaeude->heizsystem()->beheizt(), $this->ßhs() ) )->fßhs();
	}

	/**
	 * Korrekturfaktor für beliebige mittlere Berlastung und Laufzeit der Heizung $fhs.
	 *
	 * @return float
	 */
	public function fhs(): float {
		return $this->fßhs() * $this->gebaeude->ith_rl() / 5000;
	}
}
