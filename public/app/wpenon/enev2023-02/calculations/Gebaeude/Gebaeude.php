<?php

namespace Enev\Schema202302\Calculations\Gebaeude;

use Enev\Schema202302\Calculations\Anlagentechnik\Heizsystem;
use Enev\Schema202302\Calculations\Anlagentechnik\Wasserversorgung;
use Enev\Schema202302\Calculations\Bauteile\Bauteile;
use Enev\Schema202302\Calculations\Bauteile\Dach;
use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Helfer\Jahr;
use Enev\Schema202302\Calculations\Tabellen\Ausnutzungsgrad;
use Enev\Schema202302\Calculations\Tabellen\Bilanz_Innentemperatur;
use Enev\Schema202302\Calculations\Tabellen\Luftwechsel;
use Enev\Schema202302\Calculations\Tabellen\Mittlere_Belastung;
use Enev\Schema202302\Calculations\Tabellen\Mittlere_Belastung_Korrekturfaktor;
use Enev\Schema202302\Calculations\Tabellen\Monatsdaten;

use function Enev\Schema202302\Calculations\Helfer\fum;
use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

require_once __DIR__ . '/Keller.php';

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
	 * Monatsdaten
	 * 
	 * @var Monatsdaten
	 */
	protected Monatsdaten $monatsdaten;

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
		$this->baujahr          = $baujahr;
		$this->geschossanzahl   = $geschossanzahl;
		$this->geschosshoehe    = $geschosshoehe;
		$this->anzahl_wohnungen = $anzahl_wohnungen;
		$this->grundriss        = $grundriss;

		$this->c_wirk = 50; // Für den vereinfachten Rechenweg festgelegt auf den Wert 50.

		$this->monatsdaten = new Monatsdaten();
		$this->bauteile   = new Bauteile();
		$this->heizsystem = new Heizsystem( $this );
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
	 * Maximaler Wärmestrom Q gesamt.
	 *
	 * @return float
	 */
	public function q(): float {
		return $this->h() * 32;
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
		return  ( $this->qh() / $this->thm() * $this->luftwechsel()->h_max() ) * 1000;
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

	/**
	 * Wärmesenken als Leistung in W (P*h sink).
	 *
	 * @param string $monat
	 *
	 * @return int|float
	 */
	public function psh_sink_monat( string $monat ) {
		$psh_sink = $this->ph_sink_monat( $monat ) - ( $this->qi_prozesse_monat( $monat ) + ( 0.5 * $this->qs_monat( $monat ) ) * fum( $monat ) );
		return $psh_sink < 0 ? 0 : $psh_sink;
	}

	/**
	 * Monatliche solare Wärmegewinne.
	 *
	 * @param string $monat
	 * @return float
	 */
	public function qs_monat( string $monat ): float {
		return $this->bauteile()->fenster()->qs_monat( $monat );
	}

	/**
	 * Solare Wärmegewinne für ein Jahr.
	 *
	 * @return float
	 */
	public function qs(): float {
		return $this->bauteile()->fenster()->qs();
	}

	/**
	 * Interne Wärmequellen infolge von Personen
	 *
	 * @return float
	 * @throws Exception
	 */
	public function qi_prozesse(): float {
		$qi_prozesse = 0;

		foreach ( $this->monatsdaten->monate() as $monat ) {
			$qi_prozesse += $this->qi_prozesse_monat( $monat );
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
			return 45 * $this->nutzflaeche() * $this->monatsdaten->tage( $monat ) * 0.001;
		} else {
			return ( 90.0 * $this->nutzflaeche() / ( $this->anzahl_wohnungen() * $this->monatsdaten->tage( $monat ) ) ) * 0.001;
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
		return $this->wasserversorgung->QWB_monat( $monat ) * $this->wasserversorgung()->fh_w();
	}

	/**
	 * Interne Wärmequelle infolge von Warmwasser (Qi,w).
	 *
	 * @return float
	 */
	public function qi_wasser(): float {
		$qi_wasser = 0;

		foreach ( $this->monatsdaten->monate() as $monat ) {
			$qi_wasser += $this->qi_wasser_monat( $monat );
		}

		return $qi_wasser;
	}

	/**
	 * Interne Wärmequelle infolge von Heizung (Qi,h) für einen Monat.
	 *
	 * @param string $monat
	 * @return float
	 *
	 * @throws Calculation_Exception
	 */
	public function qi_heizung_monat( string $monat ): float {
		return $this->psh_sink_monat( $monat ) * ( $this->mittlere_belastung()->ßem1( $monat ) / $this->mittlere_belastung()->ßemMax() ) * $this->heizsystem()->fa_h() / fum( $monat );
	}

	/**
	 * Interne Wärmequelle infolge von Heizung (Qi,h).
	 *
	 * @return float
	 *
	 * @throws Calculation_Exception
	 */
	public function qi_heizung(): float {
		$qi_heizung = 0;

		foreach ( $this->monatsdaten->monate() as $monat ) {
			$qi_heizung += $this->qi_heizung_monat( $monat );
		}

		return $qi_heizung;
	}

	/**
	 * Berechnung von Qi gesamt für einen Monat.
	 *
	 * @param string $monat
	 * @return float
	 *
	 * @throws Calculation_Exception
	 */
	public function qi_monat( string $monat ): float {
		return $this->qi_prozesse_monat( $monat ) + $this->qi_wasser_monat( $monat ) + $this->qi_heizung_monat( $monat ) + $this->qs_monat( $monat );
	}

	/**
	 * Berechnung von Qi gesamt.
	 *
	 * @return float
	 *
	 * @throws Calculation_Exception
	 */
	public function qi(): float {
		$qi = 0;

		foreach ( $this->monatsdaten->monate() as $monat ) {
			$qi += $this->qi_monat( $monat );
		}

		return $qi;
	}

	/**
	 * Berechnung von pi für ein Jahr..
	 * 
	 * @return float 
	 * 
	 * @throws Calculation_Exception 
	 */
	public function pi(): float {
		$pi = 0;

		foreach ( $this->monatsdaten->monate() as $monat ) {
			$pi += $this->pi_monat( $monat );
		}

		return $pi;
	}

	/**
	 * Berechnung von pi für einen Monat.
	 * 
	 * @return float 
	 * 
	 * @throws Calculation_Exception 
	 */
	public function pi_monat( $monat ) {
		return $this->qi_monat( $monat ) - fum( $monat );
	}

	/**
	 * Berechnung von PH Source für einen Monat (in den Dokumenten auch "pi" genannt).
	 *
	 * @param string $monat
	 *
	 * @return float
	 */
	public function ph_source_monat( string $monat ): float {
		return $this->qi_monat( $monat ) - fum( $monat );
	}

	/**
	 * Berechnung das monatlichen Wärmequellen-/Wärmesenken-Verhältnis ym.
	 *
	 * @return float
	 */
	public function ym_monat( string $monat ): float {
		return $this->ph_source_monat( $monat ) / $this->ph_sink_monat( $monat );
	}

	/**
	 * Ausnutzungsgrad nm für einen Monat.
	 * 
	 * @param mixed $monat
	 * @return float
	 * 
	 * @throws Calculation_Exception
	 */
	public function nm_monat( $monat ): float {
		return ( new Ausnutzungsgrad( $this->tau(), $this->ym_monat( $monat ) ) )->nm();
	}

	/**
	 * Berechnung von ßhm für einen Monat.
	 * // Frage: Was ist ßhm?
	 * 
	 * @param string $monat 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function ßhm_monat( string $monat ): float {		
		return $this->mittlere_belastung()->ßem1( $monat ) * $this->k_monat( $monat );
	}	

	/**
	 * Berechnung von ßhm für ein Jahr.
	 * 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function ßhm() : float
	{
		$ßhm = 0;

		foreach ( $this->monatsdaten->monate() as $monat ) {
			$ßhm += $this->ßhm_monat( $monat );
		}

		return $ßhm;
	}

	/**
	 * Berechnung von thm für einen Monat.
	 * 
	 * @param string $monat 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function thm_monat( string $monat ): float
	{ 
		if( $this->ßhm_monat( $monat ) > 0.05 ) {
			return $this->monatsdaten->tage( $monat ) * 24;
		}

		return ( $this->ßhm_monat( $monat ) / 0.05 ) * $this->monatsdaten->tage( $monat ) * 24;
	}

	/**
	 * Berechnung von thm für ein Jahr.
	 * 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function thm(): float{
		$thm = 0;

		foreach ( $this->monatsdaten->monate() as $monat ) {
			$thm += $this->thm_monat( $monat );
		}

		return $thm;
	}

	/**
	 * Heizwärmebedarf/ Nutzenergie in kWh für einen Monat.
	 * 
	 * Ph,sink*(1-nm*ym)*thm/1000 = Ph,sink* k*thm/1000.
	 * 
	 * @param string $monat 
	 * @return float
	 * 
	 * @throws Calculation_Exception 
	 */
	public function qh_monat( string $monat ): float {
		return $this->ph_sink_monat( $monat ) * $this->k_monat( $monat ) * $this->thm_monat( $monat ) / 1000;
	}


	/**
	 * Heizwärmebedarf/ Nutzenergie im Jahr in kWh.
	 * 
	 * @return float 
	 * 
	 * @throws Calculation_Exception 
	 */
	public function qh(): float {
		$qh = 0;

		foreach ( $this->monatsdaten->monate() as $monat ) {
			$qh += $this->qh_monat( $monat );
		}

		return $qh;
	}

	/**
	 * Bestimmung von flna.
	 * 
	 * // Frage: Was ist flna?
	 * 
	 * @param string $monat
	 * 
	 * @return float
	 */
	public function flna_monat( $monat ): float {
		if ( $this->anzahl_wohnungen() === 1 ) {
			return 1;
		} 
		
		return 1 - ( ( 10 - $this->monatsdaten->temperatur( $monat ) ) / 22 );
	}

	/**
	 * Bestimmung von trl.
	 * 
	 * // Frage: Was ist trl?
	 * 
	 * @param string $monat
	 * 
	 * @return float
	 */
	public function trl_monat( $monat ): float {		
		$trl = 24 - $this->flna_monat( $monat ) * 7;

		if ( $trl < 17 ) {
			$trl = 17;
		}

		return $trl;
	}

	/**
	 * Bestimmung von ith,rl für einen Monat.
	 * 
	 *  // Frage: Was ist ith,rl?
	 * 
	 * @param string $monat 
	 * @return float 
	 * 
	 * @throws Calculation_Exception 
	 */
	public function ith_rl_monat( string $monat ): float {		
		return $this->thm_monat( $monat ) * 0.042 * $this->trl_monat( $monat );
	}

	/**
	 * Bestimmung von ith,rl für ein Jahr.
	 * 
	 * @return float
	 */
	public function ith_rl(): float {
		$ith_rl = 0;

		foreach ( $this->monatsdaten->monate() as $monat ) {
			$ith_rl += $this->ith_rl_monat( $monat );
		}

		return $ith_rl;
	}

	/**
	 * Zwischenberechnung; Bestimmung von k = (1-nm*ym).
	 * 
	 * @param string $monat 
	 * @return float 
	 * 
	 * @throws Calculation_Exception 
	 */
	public function k_monat( string $monat ): float {
		return ( 1 - $this->nm_monat( $monat ) * $this->ym_monat( $monat ) );
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
	 * Nutzfläche pro Wohneinheit.
	 * 
	 * @return float 
	 */
	public function nutzflaeche_pro_wohneinheit(): float {
		return $this->nutzflaeche() / $this->anzahl_wohnungen();
	}
}
