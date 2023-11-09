<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202302\Calculations\Tabellen\Kessel_Nennleistung;
use Enev\Schema202302\Calculations\Tabellen\Mittlere_Belastung_Pufferspeicher_Korrekturfaktor;

require_once dirname( __DIR__ ) . '/Tabellen/Kessel_Nennleistung.php';

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
	 * Handelt sich um eine zentrale Wasserversorgung (true) oder um eine dezentrale (false)?
	 *
	 * @var bool
	 */
	protected bool $zentral;

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
		$this->gebaeude        = $gebaeude;
		$this->puffergroesse   = $puffergroesse;
	}

	/**
	 * Nennleistung des Pufferspeichers (pwn).
	 *
	 * @return float
	 */
	public function pwn(): float {
		$pwn = 0;

		if ( $this->zentral ) {
			$nutzwaermebedarf_trinkwasser = $this->gebaeude->wasserversorgung()->nutzwaermebedarf_trinkwasser();

			if ( $this->gebaeude->nutzflaeche() >= 5000 ) {
				$pwn = 0.042 * ( ( $nutzwaermebedarf_trinkwasser * $this->gebaeude->nutzflaeche() ) / ( 365 * 0.036 ) ) ** 0.7;
			} else {
				$pwn = ( new Kessel_Nennleistung( $this->gebaeude->nutzflaeche(), $nutzwaermebedarf_trinkwasser ) )->nennleistung();
			}
		}

		if ( $this->gebaeude->luftwechsel()->h_max() > $pwn ) {
			return $this->gebaeude->luftwechsel()->h_max();
		}

		return $pwn;
	}

	public function pn(): float {
		if ( $this->zentral ) {
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
		return $this->uebergabesystem->ßhd() * $this->uebergabesystem->ehd_korrektur( $this->heizungsanlage );
	}

	/**
	 * Korrektrufaktor mittlere Belastung des Pufferspeichers.
	 *
	 * @param Heizungsanlage $heizungsanlage
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function fßhs(): float {
		$heizungsanlage_beheizt = $this->heizungsanlage->beheizung_anlage() === 'alles' ? true : false;
		return ( new Mittlere_Belastung_Pufferspeicher_Korrekturfaktor( $this->uebergabesystem->auslegungsvorlauftemperatur(), $heizungsanlage_beheizt, $this->ßhs() ) )->fßhs();
	}

	/**
	 * Korrekturfaktor für beliebige mittlere Berlastung und Laufzeit der Heizung $fhs.
	 *
	 * @return float
	 */
	public function fhs(): float {
		return $this->fßhs( $this->heizungsanlage ) * $this->gebaeude->ith_rl() / 5000;
	}
}
