<?php

namespace Enev\Schema202302\Calculations\Gebaeude;

use Enev\Schema202302\Calculations\Anlagentechnik\Heizsystem;
use Enev\Schema202302\Calculations\Anlagentechnik\Wasserversorgung;
use Enev\Schema202302\Calculations\Bauteile\Bauteile;
use Enev\Schema202302\Calculations\Bauteile\Dach;
use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Helfer\Jahr;
use Enev\Schema202302\Calculations\Tabellen\Bilanz_Innentemperatur;
use Enev\Schema202302\Calculations\Tabellen\Luftwechsel;
use Enev\Schema202302\Calculations\Tabellen\Mittlere_Belastung;
use Enev\Schema202302\Calculations\Tabellen\Monatsdaten;

use function Enev\Schema202302\Calculations\Helfer\fum;

require_once __DIR__ . '/Keller.php';

require_once dirname( __DIR__ ) . '/Helfer/Jahr.php';
require_once dirname( __DIR__ ) . '/Bauteile/Bauteile.php';

require_once dirname( __DIR__ ) . '/Anlagentechnik/Heizsystem.php';
require_once dirname( __DIR__ ) . '/Anlagentechnik/Wasserversorgung.php';

require_once dirname( __DIR__ ) . '/Tabellen/Luftwechsel.php';
require_once dirname( __DIR__ ) . '/Tabellen/Mittlere_Belastung.php';
require_once dirname( __DIR__ ) . '/Tabellen/Bilanz_Innentemperatur.php';

/**
 * Gebäude.
 *
 * @package
 */
class Gebaeude {

	/**
	 * Jahr Objekt.
	 *
	 * @var Jahr
	 */
	private Jahr $jahr;

	/**
	 * Baujahr des Gebäudes.
	 *
	 * @var int
	 */
	private $baujahr;

	/**
	 * Anzahl der Geschosse.
	 *
	 * @var int
	 */
	private $geschossanzahl;

	/**
	 * Geschosshöhe vom Boden inkl. Decke des darüberliegenden Geschosses.
	 *
	 * @var float
	 */
	private $geschosshoehe;

	/**
	 * Anzahl der Wohneinheiten.
	 *
	 * @var string
	 */
	private $anzahl_wohnungen;

	/**
	 * Luftwechsel
	 *
	 * @var Luftwechsel
	 */
	private Luftwechsel $luftwechsel;

	/**
	 * Mittlere Belastung
	 *
	 * @var Mittlere_Belastung
	 */
	private Mittlere_Belastung $mittlere_belastung;

	/**
	 * Bilanz Innentemperatur
	 *
	 * @var Bilanz_Innentemperatur
	 */
	private Bilanz_Innentemperatur $bilanz_innentemperatur;

	/**
	 * Grundriss
	 *
	 * @var Grundriss
	 */
	private Grundriss $grundriss;

	/**
	 * Bauteile
	 *
	 * @var Bauteile
	 */
	private Bauteile $bauteile;

	/**
	 * Heizsystem.
	 *
	 * @var Heizsystem
	 */
	private Heizsystem $heizsystem;

	/**
	 * Wasserversorgung.
	 *
	 * @var Wasserversorgung
	 */
	private Wasserversorgung $wasserversorgung;

	/**
	 * Anbau.
	 *
	 * @var Anbau
	 */
	private Anbau|null $anbau = null;

	/**
	 * Keller.
	 *
	 * @var Keller
	 */
	private Keller|null $keller = null;

	/**
	 * Wirksame Wärmespeicherkapazität in Abhängigkeit der Gebäudeschwere.
	 *
	 * @var int
	 */
	private int $c_wirk;

	/**
	 * Konstruktor
	 *
	 * @param Grundriss $grundriss Grundriss des Gebäudes.
	 * @param int       $baujahr Baujahr des Gebäudes.
	 * @param int       $geschossanzahl Anzahl der Geschosse.
	 * @param float     $geschosshoehe Geschosshöhe vom Boden bis zur Decke des darüberliegenden Geschosses (lichte Höhe). Die Deckenhöhe wird automatisch mit 25 cm für die Decke addiert.
	 * @param int       $anzahl_wohnungen Anzahl der Wohneinheiten.
	 *
	 * @return void
	 */
	public function __construct( Grundriss $grundriss, int $baujahr, int $geschossanzahl, float $geschosshoehe, int $anzahl_wohnungen ) {
		$this->jahr             = new Jahr();
		$this->baujahr          = $baujahr;
		$this->geschossanzahl   = $geschossanzahl;
		$this->geschosshoehe    = $geschosshoehe;
		$this->anzahl_wohnungen = $anzahl_wohnungen;
		$this->grundriss        = $grundriss;

		$this->c_wirk = 50; // Für den vereinfachten Rechenweg festgelegt auf den Wert 50.

		$this->bauteile   = new Bauteile();
		$this->heizsystem = new Heizsystem();
	}

	/**
	 * Bauteile
	 *
	 * @return Bauteile
	 */
	public function bauteile(): Bauteile {
		return $this->bauteile;
	}

	/**
	 * Heizsystem.
	 *
	 * @return Heizsystem
	 */
	public function heizsystem(): Heizsystem {
		return $this->heizsystem;
	}

	/**
	 * Wasserversorgung.
	 *
	 * @param Wasserversorgung
	 *
	 * @return Wasserversorgung
	 */
	public function wasserversorgung( Wasserversorgung $wasserversorgung = null ): Wasserversorgung {
		if ( $wasserversorgung !== null ) {
			$this->wasserversorgung = $wasserversorgung;
		}

		if ( $this->wasserversorgung === null ) {
			throw new Calculation_Exception( 'Wasserversorgung wurde nicht gesetzt.' );
		}

		return $this->wasserversorgung;
	}

	/**
	 * Luftwechsel
	 *
	 * @param Luftwechsel|null Luftwechsel object oder null, sofern bereits angegeben.
	 *
	 * @return Luftwechsel
	 */
	public function luftwechsel( Luftwechsel|null $luftwechsel = null ): Luftwechsel {
		if ( ! empty( $luftwechsel ) ) {
			$this->luftwechsel = $luftwechsel;
			$this->luftwechsel->gebaeude( $this );
		}

		return $this->luftwechsel;
	}

	public function mittlere_belastung(): Mittlere_Belastung {
		if ( empty( $this->mittlere_belastung ) ) {
			$this->mittlere_belastung = new Mittlere_Belastung( $this->luftwechsel()->h_max_spezifisch() ); // Mittlere Belastung wird immer mit Teilbeheizung gerechnet
			$this->mittlere_belastung->gebaeude( $this );
		}

		return $this->mittlere_belastung;
	}


	public function bilanz_innentemperatur(): Bilanz_Innentemperatur {
		if ( empty( $this->bilanz_innentemperatur ) ) {
			$this->bilanz_innentemperatur = new Bilanz_Innentemperatur( $this->luftwechsel()->h_max_spezifisch() );
			$this->bilanz_innentemperatur->gebaeude( $this );
		}

		return $this->bilanz_innentemperatur;
	}

	/**
	 * Grundriss.
	 *
	 * @return Grundriss
	 */
	public function grundriss(): Grundriss {
		return $this->grundriss;
	}

	/**
	 * Anbau.
	 *
	 * @param Anbau|null Anbau object oder null, sofern bereits angegeben.
	 */
	public function anbau( Anbau|null $anbau = null ): Anbau {
		if ( ! empty( $anbau ) ) {
			$this->anbau = $anbau;
		}

		if ( empty( $this->anbau ) ) {
			throw new Calculation_Exception( 'Anbau wurde nicht gesetzt.' );
		}

		return $this->anbau;
	}

	/**
	 * Prüft, ob ein Anbau vorhanden ist.
	 *
	 * @return bool
	 */
	public function anbau_vorhanden(): bool {
		return $this->anbau !== null;
	}

	/**
	 * Keller.
	 *
	 * @param Keller|null Keller object oder null, sofern bereits angegeben.
	 */
	public function keller( Keller|null $keller = null ): Keller {
		if ( ! empty( $keller ) ) {
			$this->keller = $keller;
		}

		if ( empty( $this->keller ) ) {
			throw new Calculation_Exception( 'Keller wurde nicht gesetzt.' );
		}

		return $this->keller;
	}

	/**
	 * Prüft, ob ein Keller vorhanden ist.
	 *
	 * @return bool
	 */
	public function keller_vorhanden(): bool {
		return $this->keller !== null;
	}

	/**
	 * Dach.
	 *
	 * @return Dach
	 */
	public function dach(): Dach {
		$elemente = array();
		$elemente = array_merge( $elemente, $this->bauteile()->filter( typ: 'Satteldach' )->alle() );
		$elemente = array_merge( $elemente, $this->bauteile()->filter( typ: 'Walmdach' )->alle() );
		$elemente = array_merge( $elemente, $this->bauteile()->filter( typ: 'Pultdach' )->alle() );
		$elemente = array_merge( $elemente, $this->bauteile()->filter( typ: 'Flachdach' )->alle() );

		$bauteile = new Bauteile( $elemente );
		return $bauteile->erstes();
	}

	/**
	 * Prüft, ob ein Dach vorhanden ist.
	 *
	 * @return bool
	 */
	public function dach_vorhanden(): bool {
		$elemente = array();
		$elemente = array_merge( $elemente, $this->bauteile()->filter( typ: 'Satteldach' )->alle() );
		$elemente = array_merge( $elemente, $this->bauteile()->filter( typ: 'Walmdach' )->alle() );
		$elemente = array_merge( $elemente, $this->bauteile()->filter( typ: 'Pultdach' )->alle() );
		$elemente = array_merge( $elemente, $this->bauteile()->filter( typ: 'Flachdach' )->alle() );

		$bauteile = new Bauteile( $elemente );
		return $bauteile->anzahl() > 0 ? true : false;
	}

	/**
	 * Baujahr des Gebäudes.
	 *
	 * @return int
	 */
	public function baujahr(): int {
		return $this->baujahr;
	}

	/**
	 * Wirksame Wärmespeicherkapazität in Abhängigkeit der Gebäudeschwere.
	 *
	 * @return int
	 */
	public function c_wirk(): int {
		return $this->c_wirk;
	}

	/**
	 * Anzahl der Geschosse.
	 *
	 * @return int
	 */
	public function geschossanzahl(): int {
		return $this->geschossanzahl;
	}

	/**
	 * Geschosshöhe vom Boden inkl. Decke des darüberliegenden Geschosses.
	 *
	 * @return float
	 */
	public function geschosshoehe(): float {
		return $this->geschosshoehe + 0.25; // Die vom Kunden angegebenen Geschosshöhe zzgl. 25 cm für die Decke des darüberliegenden Geschosses.
	}

	/**
	 * Anzahl der Wohneinheiten.
	 *
	 * @return string
	 */
	public function anzahl_wohnungen(): int {
		return $this->anzahl_wohnungen;
	}

	/**
	 * Hüllfläche des Gebäudes.
	 *
	 * @return float
	 */
	public function huellflaeche(): float {
		return $this->bauteile()->flaeche();
	}

	/**
	 * Hüllvolumen des Gebäudes.
	 *
	 * @return float
	 */
	public function huellvolumen(): float {
		$volumen = 0;

		// Volumen der Geschosse.
		$volumen += $this->grundriss->flaeche() * $this->geschossanzahl() * $this->geschosshoehe();

		// Volumen des Anbaus.
		if ( $this->anbau !== null ) {
			$volumen += $this->anbau->volumen();
		}

		// Volumen des Dachs.
		if ( $this->dach_vorhanden() ) {
			$volumen += $this->dach()->volumen();
		}

		return $volumen;
	}



	/**
	 * Wärmetransferkoeffizient des Gebäudes.
	 *
	 * @return float
	 *
	 * @throws Exception
	 */
	public function h() {
		return $this->bauteile()->ht() + $this->luftwechsel()->hv();
	}

	/**
	 * Transmissionswärmeverlust des Gebäudes.
	 *
	 * Frage: Ist das so korrekt implementiert?
	 *
	 * @return float
	 */
	public function ht(): float {
		return $this->bauteile()->ht() + 0.1 * $this->huellflaeche();
	}

	/**
	 * Zeitkonstante Tau
	 *
	 * @return float
	 *
	 * @throws Exception
	 */
	public function tau(): float {
		return ( $this->c_wirk() * $this->nutzflaeche() ) / $this->h();
	}

	/**
	 * Maximaler Wärmestrom.
	 *
	 * @return float
	 */
	public function q(): float {
		return $this->h() * 32;
	}

	/**
	 * Wärmesenken als Leistung in W für einen Monat.
	 *
	 * @param  string $monat
	 * @return float
	 * @throws Exception
	 */
	public function ph_sink_monat( string $monat ) {
		return $this->q() * ( ( $this->bilanz_innentemperatur()->θih_monat( $monat ) + 12 ) / 32 ) * $this->mittlere_belastung()->ßem1( $monat );
	}

	public function psh_sink_monat( string $monat ) {		
		// $calculations['monate'][ $monat ]['psh_sink'] = $gebaeude->ph_sink_monat( $monat) - ($gebaeude->qi_prozesse_monat( $monat ) + ( 0.5 * $calculations['monate'][ $monat ]['qi_solar'] ) * $fum);
		// $calculations['monate'][ $monat ]['psh_sink'] = $calculations['monate'][ $monat ]['psh_sink'] < 0 ? 0 : $calculations['monate'][ $monat ]['psh_sink'];

		$phs_sink = $this->ph_sink_monat( $monat ) - ( $this->qi_prozesse_monat( $monat ) + ( 0.5 * $calculations['monate'][ $monat ]['qi_solar'] ) * fum( $monat ) );
	}

	/**
	 * Monatliche solare Wärmegewinne.
	 * 
	 * @param string $monat 
	 * @return float 
	 */
	public function qi_solar_monat( string $monat ) : float {		
		return $this->bauteile()->fenster()->qi_solar_monat( $monat );
	}

	/**
	 * Solare Wärmegewinne für ein Jahr.
	 * 
	 * @return float 
	 */
	public function qi_solar(): float {
		return $this->bauteile()->fenster()->qi_solar();
	}

	/**
	 * Interne Wärmequellen infolge von Personen
	 *
	 * @return float
	 * @throws Exception
	 */
	public function qi_prozesse(): float {
		$qi_prozesse = 0;

		foreach ( $this->jahr->monate() as $monat ) {
			$qi_prozesse += $this->qi_prozesse_monat( $monat->slug() );
		}

		return $qi_prozesse;
	}

	/**
	 * Interne Wärmequellen infolge von Personen für einen Monat.
	 *
	 * @param  string $monat
	 * @return float
	 * @throws Exception
	 */
	public function qi_prozesse_monat( string $monat ): float {
		if ( $this->anzahl_wohnungen() === 1 ) {
			return 45 * $this->nutzflaeche() * $this->jahr->monat( $monat )->tage() * 0.001;
		} else {
			return ( 90.0 * $this->nutzflaeche() / ( $this->anzahl_wohnungen() * $this->jahr->monat( $monat )->tage() ) ) * 0.001;
		}
	}

	/**
	 * Interne Wärmequelle infolge von Warmwasser (Qi,w) für einen Monat.
	 *
	 * @param string $monat
	 * @return float
	 * @throws Exception
	 */
	public function qi_wasser_monat( string $monat ): float {
		return $this->qwb_monat( $monat ) * $this->wasserversorgung()->fh_w();
	}

	/**
	 * Interne Wärmequelle infolge von Warmwasser (Qi,w).
	 *
	 * @return float
	 */
	public function qi_wasser(): float {
		$qi_wasser = 0;

		foreach ( $this->jahr->monate() as $monat ) {
			$qi_wasser += $this->qi_wasser_monat( $monat->slug() );
		}

		return $qi_wasser;
	}

	/**
	 * Netto Hüllvolumen des Gebäudes.
	 *
	 * @return float
	 */
	public function huellvolumen_netto(): float {
		return $this->geschossanzahl < 4 ? 0.76 * $this->huellflaeche() : 0.8 * $this->huellvolumen();
	}

	/**
	 * Verhältnis von Hüllfläche zu Netto Hüllvolumen.
	 *
	 * @return float
	 */
	public function ave_verhaeltnis(): float {
		return $this->huellflaeche() / $this->huellvolumen_netto();
	}

	/**
	 * Nutzfläche des Gebäudes.
	 *
	 * @return float
	 */
	public function nutzflaeche(): float {
		if ( $this->geschosshoehe >= 2.5 && $this->geschosshoehe <= 3.0 ) {
			return $this->huellvolumen() * 0.32;
		} else {
			return $this->huellvolumen() * ( 1.0 / $this->geschosshoehe - 0.04 );
		}
	}

	/**
	 * Jährlicher Nutzwaermebedarf für Trinkwasser (qwb).
	 *
	 * Aufgrund der Einfachheit nicht in der Datenbank gespeichert.
	 *
	 * Teil 12 - Tabelle 19.
	 *
	 * @param float $nutzflaeche Netto-Nutzfläche des Gebäudes.
	 *
	 * @return float
	 */
	public function nutzwaermebedarf_trinkwasser(): float {
		if ( $this->nutzflaeche() < 10 ) {
			return 16.5;
		} elseif ( $this->nutzflaeche() >= 10 ) {
			return 16;
		} elseif ( $this->nutzflaeche() >= 20 ) {
			return 15.5;
		} elseif ( $this->nutzflaeche() >= 30 ) {
			return 15;
		} elseif ( $this->nutzflaeche() >= 40 ) {
			return 14.5;
		} elseif ( $this->nutzflaeche() >= 50 ) {
			return 14;
		} elseif ( $this->nutzflaeche() >= 60 ) {
			return 13.5;
		} elseif ( $this->nutzflaeche() >= 70 ) {
			return 13;
		} elseif ( $this->nutzflaeche() >= 80 ) {
			return 12.5;
		} elseif ( $this->nutzflaeche() >= 90 ) {
			return 12;
		} elseif ( $this->nutzflaeche() >= 100 ) {
			return 11.5;
		} elseif ( $this->nutzflaeche() >= 110 ) {
			return 11;
		} elseif ( $this->nutzflaeche() >= 120 ) {
			return 10.5;
		} elseif ( $this->nutzflaeche() >= 130 ) {
			return 10;
		} elseif ( $this->nutzflaeche() >= 140 ) {
			return 9.5;
		} elseif ( $this->nutzflaeche() >= 150 ) {
			return 9;
		} elseif ( $this->nutzflaeche() >= 160 ) {
			return 8.5;
		}
	}

	/**
	 * Monatlicher Nutzwärmebedarf für Trinkwasser (qwb).
	 *
	 * @param string $monat Slug des Monats.
	 *
	 * @return float Nutzwärmebedarf für Trinkwasser (qwb) in kWh.
	 */
	public function qwb_monat( string $monat ): float {
		$qwb = $this->nutzwaermebedarf_trinkwasser( $this->nutzflaeche() );
		return ( $this->nutzflaeche() / $this->anzahl_wohnungen() ) * $qwb * ( $this->jahr->monat( $monat )->tage() / 365 );
	}

	/**
	 *  Nutzwärmebedarf für Trinkwasser (qwb) für ein Jahr.
	 *
	 * @return float Nutzwärmebedarf für Trinkwasser (qwb) in kWh.
	 */
	public function qwb(): float {
		$qwb = 0;

		foreach ( $this->jahr->monate() as $monat ) {
			$qwb += $this->qwb_monat( $monat->slug() );
		}

		return $qwb;
	}
}
