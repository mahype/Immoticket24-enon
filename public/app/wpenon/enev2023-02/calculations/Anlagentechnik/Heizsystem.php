<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202302\Calculations\Tabellen\Mittlere_Belastung_Korrekturfaktor;

require_once __DIR__ . '/Heizungsanlage.php';
require_once __DIR__ . '/Heizungsanlagen.php';
require_once __DIR__ . '/Uebergabesysteme.php';
require_once __DIR__ . '/Wasserversorgungen.php';
require_once __DIR__ . '/Pufferspeicher.php';


/**
 * Berechnung des Heizsystems.
 */
class Heizsystem {

	/**
	 * Standort des Heizsystems ("innerhalb" oder "ausserhalb" thermischer Hülle).
	 *
	 * @var string
	 */
	protected string $standort;

	/**
	 * Gebaeude.
	 *
	 * @var Gebaeude
	 */
	protected Gebaeude $gebaeude;

	/**
	 * Liste der Heizungsanlagen.
	 *
	 * @var Heizungsanlagen
	 */
	protected Heizungsanlagen $heizungsanlagen;

	/**
	 * Liste der Übergabesysteme.
	 *
	 * @var Uebergabesysteme
	 */
	protected Uebergabesysteme $uebergabesysteme;

	/**
	 * Wasserversorgungen.
	 *
	 * @var Wasserversorgungen
	 */
	protected Wasserversorgungen $wasserversorgungen;

	/**
	 * Pufferspeicher.
	 *
	 * @var Pufferspeicher
	 */
	protected Pufferspeicher $pufferspeicher;

	/**
	 * Konstruktor.
	 */
	public function __construct( Gebaeude $gebaeude, string $standort ) {
		if ( $standort !== 'innerhalb' && $standort !== 'ausserhalb' ) {
			throw new Calculation_Exception( 'Standort des Heizsystems muss entweder "innerhalb" oder "ausserhalb" sein.' );
		}

		$this->gebaeude           = $gebaeude;
		$this->standort           = $standort;
		$this->heizungsanlagen    = new Heizungsanlagen();
		$this->uebergabesysteme   = new Uebergabesysteme();
		$this->wasserversorgungen = new Wasserversorgungen();
	}

	/**
	 * Liegt das Heizsystem im beheizten Bereich?
	 *
	 * @return bool
	 */
	public function beheizt(): bool {
		return $this->standort === 'innerhalb';
	}

	/**
	 * Heizungsanlagen.
	 *
	 * @return Heizungsanlagen
	 */
	public function heizungsanlagen(): Heizungsanlagen {
		return $this->heizungsanlagen;
	}

	/**
	 * Übergabesysteme.
	 *
	 * @return Uebergabesysteme
	 */
	public function uebergabesysteme(): Uebergabesysteme {
		return $this->uebergabesysteme;
	}

	/**
	 * Wasserversorgungen.
	 *
	 * @return Wasserversorgungen
	 */
	public function wasserversorgungen(): Wasserversorgungen {
		return $this->wasserversorgungen;
	}

	/**
	 * Verteilung Heizung (ehd).
	 *
	 * Berechnung der Wirkungsgrade der Wärmeverluste (Aufwandszahlen) von  Verteilung ehd
	 * Bemerkung: Übergabestationen werden vorerst nicht berücksichtigt
	 * Siehe Tabelle 12, Tabele 30, Tabelle 31
	 *
	 * @param Heizungsanlage
	 *
	 * @return float
	 */
	public function ehd(): float {
		if ( $this->uebergabesysteme()->erstes()->typ() === 'elektroheizungsflaechen' ) {
			return 1;
		}

		return 1 + ( $this->ehd1() - 1 ) * ( 50 / $this->qhce() );
	}

	/**
	 * Berechnung von ehd0
	 *
	 * @return float
	 */
	public function ehd0(): float {
		$uebergabesystem = $this->uebergabesysteme()->erstes();

		if ( $this->gebaeude->anzahl_wohnungen() === 1 ) {
			switch ( $uebergabesystem->typ() ) {
				case '90/70':
					return $this->standort === 'innerhalb' ? 1.099 : 1.1;
				case '70/55':
					return $this->standort === 'innerhalb' ? 1.070 : 1.074;
				case '55/45':
					return $this->standort === 'innerhalb' ? 1.049 : 1.055;
				case '35/28':
					return $this->standort === 'innerhalb' ? 1.019 : 1.028;
			}
		}

		switch ( $uebergabesystem->auslegungstemperaturen() ) {
			case '90/70':
				return $this->standort === 'innerhalb' ? 1.085 : 1.085;
			case '70/55':
				return $this->standort === 'innerhalb' ? 1.060 : 1.063;
			case '55/45':
				return $this->standort === 'innerhalb' ? 1.042 : 1.047;
			case '35/28':
				return $this->standort === 'innerhalb' ? 1.016 : 1.024;
		}
	}

	/**
	 * Verteilung Heizung korrektur (ehd_korrektur).
	 *
	 * @param Heizungsanlage $heizungsanlage
	 * @return float
	 */
	public function ehd_korrektur(): float {
		if ( $this->uebergabesysteme()->erstes()->typ() === 'elektroheizungsflaechen' ) {
			return 1;
		}

		// 1 + (ehd-1)*(8760/$calculations['ith,rl'] )
		return 1 + ( $this->ehd() - 1 ) * ( 8760 / $this->gebaeude->ith_rl() );
	}

	/**
	 * Verteilung Heizung (ehd1).
	 *
	 * @param Heizungsanlage $heizungsanlage
	 * @param int            $anzahl_wohnungen
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function ehd1(): float {
		return $this->ehd0() * $this->fßd();
	}

	/**
	 * Mitttlere Belastung für die Verteilung (ßhd).
	 *
	 * @return float
	 */
	public function ßhd(): float {
		if ( $this->uebergabesysteme()->erstes()->typ() === 'elektroheizungsflaechen' ) {
			return 0;
		}

		// Wir nehmen immer an, dass kein hydraulischer Abgleich durchgeführt wurde um die Anzahl der Fragen zu reduzieren.
		// Da dies aber später Pflicht wird, muss das später noch angepasst werden.
		$fhydr = 1.06;

		return $this->ßhce() * $this->ehce() * $fhydr;
	}

	/**
	 * Mittlere Belastung Korrekturfaktor.
	 *
	 * @param Heizungsanlage $heizungsanlage
	 * @param int            $anzahl_wohnungen
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function fßd(): float {
		return ( new Mittlere_Belastung_Korrekturfaktor( $this->beheizt(), $this->gebaeude->anzahl_wohnungen(), $this->uebergabesysteme()->erstes()->auslegungstemperaturen(), $this->ßhd() ) )->fßd();
	}

	/**
	 * Aufwandszahl der Heizungsübergabe (ehce).
	 *
	 * @return float
	 */
	public function ehce(): float {
		return $this->uebergabesysteme()->erstes()->ehce();
	}

	/**
	 * Mittlere Belastung bei Übergabe der Heizung.
	 *
	 * @return float
	 *
	 * @throws Calculation_Exception
	 */
	public function ßhce(): float {
		// $ßhce=($calculations['qh']/($calculations['thm']*$Φh,max))*1000;
		return ( $this->gebaeude->qh() / ($this->gebaeude->thm() * $this->gebaeude->luftwechsel()->h_max() ) ) * 1000;
	}

	/**
	 * Flächenbezogene leistung der Übergabe der Heizung (qhce).
	 *
	 * @return float
	 */
	public function qhce(): float {
		return $this->uebergabesysteme()->erstes()->qhce();
	}

	/**
	 * Berechnung von fa h.
	 *
	 * @return float
	 */
	public function fa_h(): float {
		$fa_h = 0;

		if ( count( $this->heizungsanlagen()->alle() ) === 0 ) {
			throw new Calculation_Exception( 'Keine Heizungsanlagen vorhanden' );
		}

		/**
		 * Wir berecnen fh-a des Heizungsystems, indem wir alle Heizungsanlagen sowie Übergabesysteme durchlaufen
		 * und den fa-h Wert anhand der Auslegungstemperaturen des Übergabesystems ermitteln. Die Werte werden
		 * anteilig der einzelnen Heizungsanlagen und Übergabesysteme gewichtet.
		 */
		foreach ( $this->heizungsanlagen()->alle() as $heizungsanlage ) {
			if ( count( $this->uebergabesysteme()->alle() ) === 0 ) {
				throw new Calculation_Exception( 'Keine Übergabesysteme vorhanden' );
			}

			foreach ( $this->uebergabesysteme()->alle() as $uebergabesystem ) {
				$fa_h += $heizungsanlage->fa_h( $uebergabesystem->auslegungstemperaturen() ) * $heizungsanlage->prozentualer_faktor() * $uebergabesystem->prozentualer_faktor();
			}
		}

		return $fa_h;
	}

	/**
	 * Heizungsanlage mit groesstem Anteil.
	 *
	 * @return Heizungsanlage
	 */
	public function heizungasanlage_mit_groesstem_anteil(): Heizungsanlage {
		// Finde Heizugnsanlage mit groesstem Anteil
		foreach ( $this->heizungsanlagen()->alle() as $heizungsanlage ) {
			if ( ! isset( $max ) ) {
				$max = $heizungsanlage;
			} elseif ( $heizungsanlage->prozentualer_faktor() > $max->prozentualer_faktor() ) {
					$max = $heizungsanlage;
			}
		}

		return $max;
	}
}
