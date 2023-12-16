<?php

namespace Enev\Schema202302\Calculations\Gebaeude;

use Enev\Schema202302\Calculations\Anlagentechnik\Heizsystem;
use Enev\Schema202302\Calculations\Anlagentechnik\Hilfsenergie;
use Enev\Schema202302\Calculations\Anlagentechnik\Lueftung;
use Enev\Schema202302\Calculations\Anlagentechnik\Photovoltaik_Anlage;
use Enev\Schema202302\Calculations\Anlagentechnik\Trinkwarmwasseranlage;
use Enev\Schema202302\Calculations\Bauteile\Bauteile;
use Enev\Schema202302\Calculations\Bauteile\Dach;
use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Tabellen\Ausnutzungsgrad;
use Enev\Schema202302\Calculations\Tabellen\Bilanz_Innentemperatur;
use Enev\Schema202302\Calculations\Tabellen\Mittlere_Belastung;
use Enev\Schema202302\Calculations\Tabellen\Monatsdaten;

use function Enev\Schema202302\Calculations\Helfer\fum;

require_once __DIR__ . '/Keller.php';

require_once dirname( __DIR__ ) . '/Bauteile/Bauteile.php';

require_once dirname( __DIR__ ) . '/Anlagentechnik/Heizsystem.php';
require_once dirname( __DIR__ ) . '/Anlagentechnik/Trinkwarmwasseranlage.php';
require_once dirname( __DIR__ ) . '/Anlagentechnik/Hilfsenergie.php';

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
	 * Hüllvolumen.
	 *
	 * @var float
	 */
	private float $huellvolumen;

	/**
	 * Nutzfläche.
	 *
	 * @var float
	 */
	private float $nutzflaeche;

	/**
	 * Monatsdaten
	 *
	 * @var Monatsdaten
	 */
	protected Monatsdaten $monatsdaten;

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
	 * Trinkwarmwasseranlage.
	 *
	 * @var Trinkwarmwasseranlage
	 */
	private Trinkwarmwasseranlage $trinkwarmwasseranlage;

	/**
	 * Photovoltaik-Anlage.
	 *
	 * @var Photovoltaik_Anlage|null
	 */
	private Photovoltaik_Anlage|null $photovoltaik_anlage = null;

	/**
	 * Lueftung.
	 *
	 * @var Lueftung
	 */
	private Lueftung $lueftung;

	/**
	 * Hilfsenergie.
	 *
	 * @var Hilfsenergie
	 */
	private Hilfsenergie $hilfsenergie;

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
	 * Qfwges
	 * 
	 * @var float
	 */
	private float $Qfwges;

	/**
	 * Konstruktor
	 *
	 * @param Grundriss $grundriss Grundriss des Gebäudes.
	 * @param int       $baujahr Baujahr des Gebäudes.
	 * @param int       $geschossanzahl Anzahl der Geschosse.
	 * @param float     $geschosshoehe Geschosshöhe vom Boden bis zur Decke des darüberliegenden Geschosses (lichte Höhe). Die Deckenhöhe wird automatisch mit 25 cm für die Decke addiert.
	 * @param int       $anzahl_wohnungen Anzahl der Wohneinheiten.
	 * @param string    $standort_heizsystem Standort des Heizsystems ("innerhalb" oder "ausserhalb" der thermischen Hülle).
	 *
	 * @return void
	 */
	public function __construct( Grundriss $grundriss, int $baujahr, int $geschossanzahl, float $geschosshoehe, int $anzahl_wohnungen, string $standort_heizsystem ) {
		$this->baujahr          = $baujahr;
		$this->geschossanzahl   = $geschossanzahl;
		$this->geschosshoehe    = $geschosshoehe + 0.25;
		$this->anzahl_wohnungen = $anzahl_wohnungen;
		$this->grundriss        = $grundriss;

		$this->c_wirk = 50; // Für den vereinfachten Rechenweg festgelegt auf den Wert 50.

		$this->monatsdaten  = new Monatsdaten();
		$this->bauteile     = new Bauteile();
		$this->heizsystem   = new Heizsystem( $this, $standort_heizsystem );
		$this->hilfsenergie = new Hilfsenergie( $this );
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
	 * Trinkwarmwasseranlage.
	 *
	 * @param Trinkwarmwasseranlage
	 *
	 * @return Trinkwarmwasseranlage
	 */
	public function trinkwarmwasseranlage( Trinkwarmwasseranlage $trinkwarmwasseranlage = null ): Trinkwarmwasseranlage {
		if ( $trinkwarmwasseranlage !== null ) {
			$this->trinkwarmwasseranlage = $trinkwarmwasseranlage;
		}

		if ( $this->trinkwarmwasseranlage === null ) {
			throw new Calculation_Exception( 'Trinkwarmwasseranlage wurde nicht gesetzt.' );
		}

		return $this->trinkwarmwasseranlage;
	}

	/**
	 * Photovaltaik-Anlage.
	 * 
	 * @param null|Photovoltaik_Anlage $photovoltaik_anlage 
	 * @return Photovoltaik_Anlage 
	 * @throws Calculation_Exception 
	 */
	public function photovoltaik_anlage( Photovoltaik_Anlage|null $photovoltaik_anlage = null ): Photovoltaik_Anlage {
		if ( $photovoltaik_anlage !== null ) {
			$this->photovoltaik_anlage = $photovoltaik_anlage;
		}

		if ( $this->photovoltaik_anlage === null ) {
			throw new Calculation_Exception( 'Photovoltaik-Anlage wurde nicht gesetzt.' );
		}

		return $this->photovoltaik_anlage;
	}

	/**
	 * Prüft, ob eine Photovoltaik-Anlage vorhanden ist.
	 * 
	 * @return bool 
	 */
	public function photovoltaik_anlage_vorhanden(): bool {
		return $this->photovoltaik_anlage !== null;
	}

	/**
	 * Lüftung.
	 *
	 * @param null|Lueftung $lueftung
	 * @return Lueftung
	 * @throws Calculation_Exception
	 */
	public function lueftung( Lueftung|null $lueftung = null ) {
		if ( $lueftung !== null ) {
			$this->lueftung = $lueftung;
		}

		if ( $this->lueftung === null ) {
			throw new Calculation_Exception( 'Lüftung wurde nicht gesetzt.' );
		}

		return $this->lueftung;
	}

	public function mittlere_belastung(): Mittlere_Belastung {
		if ( empty( $this->mittlere_belastung ) ) {
			$this->mittlere_belastung = new Mittlere_Belastung( $this->lueftung()->h_max_spezifisch() ); // Mittlere Belastung wird immer mit Teilbeheizung gerechnet
			$this->mittlere_belastung->gebaeude( $this );
		}

		return $this->mittlere_belastung;
	}


	public function bilanz_innentemperatur(): Bilanz_Innentemperatur {
		if ( empty( $this->bilanz_innentemperatur ) ) {
			$this->bilanz_innentemperatur = new Bilanz_Innentemperatur( $this->lueftung()->h_max_spezifisch() );
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
		return $this->geschosshoehe; // Die vom Kunden angegebenen Geschosshöhe zzgl. 25 cm für die Decke des darüberliegenden Geschosses.
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
	 * Handelt es sich um ein Einfamilienhaus?
	 *
	 * Das Haus gilt als Mehrfamilienhaus, wenn es mehr als zwei Wohneinheiten hat (Laut BANZ? bzw. DIN18599).
	 *
	 * @return bool
	 */
	public function ist_einfamilienhaus(): bool {
		return $this->anzahl_wohnungen() <= 2;
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
		if ( empty( $this->huellvolumen ) ) {
			$this->huellvolumen = $this->huellvolumen_vollgeschosse() + $this->huellvolumen_keller() + $this->huellvolumen_dach() + $this->huellvolumen_anbau();
		}

		return $this->huellvolumen;
	}

	/**
	 * Hüllvolumen der Vollgeschosse.
	 *
	 * @return float
	 */
	public function huellvolumen_vollgeschosse(): float {
		return $this->grundriss->flaeche() * $this->geschossanzahl() * $this->geschosshoehe();
	}

	/**
	 * Hüllvolumen des Kellers.
	 *
	 * @return float
	 */
	public function huellvolumen_keller(): float {
		return $this->keller_vorhanden() ? $this->keller->volumen() : 0;
	}

	/**
	 * Hüllvolumen des Dachs.
	 *
	 * @return float
	 */
	public function huellvolumen_dach(): float {
		return $this->dach_vorhanden() ? $this->dach()->volumen() : 0;
	}

	/**
	 * Hüllvolumen des Anbaus.
	 *
	 * @return float
	 */
	public function huellvolumen_anbau(): float {
		return $this->anbau_vorhanden() ? $this->anbau->volumen() : 0;
	}

	/**
	 * Wärmetransferkoeffizient des Gebäudes.
	 *
	 * @return float
	 *
	 * @throws Exception
	 */
	public function h_ges() {
		return $this->bauteile()->ht() + $this->lueftung()->hv() + $this->ht_wb();
	}

	/**
	 * Transmissionswärmeverlust des Gebäudes.
	 *
	 * Frage: Ist das so korrekt implementiert?
	 *
	 * @return float
	 */
	public function ht_ges(): float {
		// 0,1 = Wärmebrückenzuschlag
		return $this->bauteile()->ht() + $this->ht_wb();
	}

	/**
	 * Berechnung ht_wb.
	 *
	 * @return float
	 */
	public function ht_wb(): float {
		return 0.1 * $this->huellflaeche();
	}

	/**
	 * Zeitkonstante Tau.
	 *
	 * @return float
	 *
	 * @throws Exception
	 */
	public function tau(): float {
		return ( $this->c_wirk() * $this->nutzflaeche() ) / $this->h_ges();
	}

	/**
	 * Maximaler Wärmestrom Q gesamt.
	 *
	 * @return float
	 */
	public function q(): float {
		return $this->h_ges() * 32;
	}

	/**
	 * Aufwandszahl der Heizungsübergabe (ehce).
	 *
	 * @return float
	 */
	public function ehce(): float {
		return $this->heizsystem()->uebergabesysteme()->erstes()->ehce();
	}

	/**
	 * Flächenbezogene leistung der Übergabe der Heizung (qhce).
	 *
	 * @return float
	 */
	public function qhce(): float {
		return $this->heizsystem()->uebergabesysteme()->erstes()->qhce();
	}

	/**
	 * Verteilung Heizung (ehd0).
	 *
	 * @return float
	 */
	public function ehd0(): float {
		$this->heizsystem()->ehd0();
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
		$psh_sink = $this->ph_sink_monat( $monat ) - ( ( $this->qi_prozesse_monat( $monat ) + ( 0.5 * $this->qi_solar_monat( $monat ) ) ) * fum( $monat ) );
		return $psh_sink < 0 ? 0 : $psh_sink;
	}

	/**
	 * Monatliche solare Wärmegewinne.
	 *
	 * @param string $monat
	 * @return float
	 */
	public function qi_solar_monat( string $monat ): float {
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
		if ( $this->ist_einfamilienhaus() ) {
			return ( 45 * $this->nutzflaeche() * $this->monatsdaten->tage( $monat ) ) / 1000;
		} else {
			return ( 90.0 * $this->nutzflaeche() / $this->anzahl_wohnungen() * $this->monatsdaten->tage( $monat ) ) / 1000;
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
		return $this->trinkwarmwasseranlage->QWB_monat( $monat ) * $this->trinkwarmwasseranlage()->Faw();
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
		return $this->qi_prozesse_monat( $monat ) + $this->qi_wasser_monat( $monat ) + $this->qi_heizung_monat( $monat ) + $this->qi_solar_monat( $monat );
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
		return $this->ph_source_monat( $monat );
	}

	/**
	 * Berechnung von PH Source für einen Monat (in den Dokumenten auch "pi" genannt).
	 *
	 * @param string $monat
	 *
	 * @return float
	 */
	public function ph_source_monat( string $monat ): float {
		return $this->qi_monat( $monat ) * fum( $monat );
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
	 * Berechnung von ßhma.
	 *
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function ßhma(): float {
		$ßhm = 0;

		foreach ( $this->monatsdaten->monate() as $monat ) {
			$ßhm += $this->ßhm_monat( $monat );
		}

		return $ßhm;
	}

	/**
	 * ßoutgmth.
	 *
	 * Berechnung der mittlere Belastung Erzeuger Nutzwärme
	 *
	 * @param string $monat
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function ßoutgmth( string $monat ): float {
		return $this->ßhm_monat( $monat ) / $this->ßhma();
	}

	/**
	 * Berechnung von thm für einen Monat.
	 *
	 * @param string $monat
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function thm_monat( string $monat ): float {
		if ( $this->ßhm_monat( $monat ) > 0.05 ) {
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
	public function thm(): float {
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
	 * // NOTE: Was ist flna?
	 *
	 * @param string $monat
	 *
	 * @return float
	 */
	public function flna_monat( $monat ): float {
		if ( $this->ist_einfamilienhaus() ) {
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
	 * Berechnung der monaltichen Laufzeit (ith,rl).
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
	 * Berechnung der jährlichen Laufzeit (ith,rl).
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
		$k = ( 1 - $this->nm_monat( $monat ) * $this->ym_monat( $monat ) );
		return $k < 0 ? 0 : $k;
	}

	/**
	 * Netto Hüllvolumen des Gebäudes.
	 *
	 * @return float
	 */
	public function huellvolumen_netto(): float {
		return $this->geschossanzahl < 4 ? 0.76 * $this->huellvolumen() : 0.8 * $this->huellvolumen();
	}

	/**
	 * Verhältnis von Hüllfläche zu Netto Hüllvolumen.
	 *
	 * @return float
	 */
	public function ave_verhaeltnis(): float {
		return $this->huellflaeche() / $this->huellvolumen();
	}

	/**
	 * Nutzfläche des Gebäudes.
	 *
	 * @return float
	 */
	public function nutzflaeche(): float {
		if ( ! empty( $this->nutzflaeche ) ) {
			return $this->nutzflaeche;
		}

		if ( $this->geschosshoehe() >= 2.5 && $this->geschosshoehe() <= 3.0 ) {
			$this->nutzflaeche = $this->huellvolumen() * 0.32;
		} else {
			$this->nutzflaeche = $this->huellvolumen() * ( 1.0 / $this->geschosshoehe() - 0.04 );
		}

		return $this->nutzflaeche;
	}

	/**
	 * Nutzfläche pro Wohneinheit.
	 *
	 * @return float
	 */
	public function nutzflaeche_pro_wohneinheit(): float {
		return $this->nutzflaeche() / $this->anzahl_wohnungen();
	}

	public function hilfsenergie(): Hilfsenergie {
		return $this->hilfsenergie;
	}

	public function Qfwges(): float {
		if( isset( $this->Qfwges ) ) {
			return $this->Qfwges;
		}

		if ( $this->trinkwarmwasseranlage()->zentral() ) {
			$this->Qfwges = $this->heizsystem()->heizungsanlagen()->Qfwges();
			return $this->Qfwges;
		}

		$this->Qfwges = $this->trinkwarmwasseranlage()->Qfwges() + $this->trinkwarmwasseranlage()->ews();
		return $this->Qfwges;
	}

	public function Qfhges(): float {
		return $this->heizsystem()->heizungsanlagen()->Qfhges();
	}

	public function Qfgesamt(): float {
		// (Qfwges + Qws)
		return $this->Qfhges() + $this->Qfwges() + $this->hilfsenergie()->Wges();
	}

	public function Qfstrom(): float {
		// case 1 Zentrale Trinkwassererwärmung
		// if Stromheizung  oder && Dezentrale Durchlauferhitzter Strom than
		// $Qfstrom1-3 = $Qfhges1-3+$Qfwges1-3+($Wges/n)
		// else
		// $Qfstrom1-3 = ($Wges/n)              , Die gilt für alle fossilen Heizungen und den Gas-Durchlauferhitzter
		// $Qfstrom= $Qfstrom1-3

		$Qfstrom = $this->hilfsenergie()->Wges(); // Hilfsenergie ist immer Strom
		$Qfstrom += $this->heizsystem()->heizungsanlagen()->Qfstromges(); // Strom aus mit Strom betriebene Heizungsanlagen (ohne Hilfsenergie)

		if( ! $this->trinkwarmwasseranlage()->zentral() ) {
			$Qfstrom += $this->trinkwarmwasseranlage()->Qfwges(); // Strom aus zentraler Trinkwassererwärmung
		}
		
		return $Qfstrom;
	}

	public function Qpges(): float {
		$Qpges = 0;

		foreach ( $this->heizsystem->heizungsanlagen()->alle() as $heizungsanlage ) {
			$Qpges += $heizungsanlage->Qpges();
		}

		if( ! $this->trinkwarmwasseranlage()->zentral() ) {
			$Qpges += $this->trinkwarmwasseranlage()->Qpges();
		}

		$Qpges += $this->hilfsenergie()->Wges() * 1.8;

		return $Qpges;
	}

	/**
	 * Berechnung der CO2-Emissionen (kg).
	 * 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function MCO2(): float {
		$MCO2 = 0;

		foreach ( $this->heizsystem->heizungsanlagen()->alle() as $heizungsanlage ) {
			$MCO2 += $heizungsanlage->MCO2();
		}

		// Was ist mit Trinkwarwasser?

		// if( ! $this->trinkwarmwasseranlage()->zentral() ) {
		// 	$MCO2 += $this->trinkwarmwasseranlage()->MCO2();
		// }

		// Was ist mit Strom aus Hilfsenergie?
		// $MCO2 += $this->hilfsenergie()->Wges() * 1.8;

		return $MCO2;
	}

	public function MCO2a(): float {
		return $this->MCO2() / $this->nutzflaeche();
	}

	/**
	 * Berechnung der flächenbezogenen Endenergie (Brennwert)  (kwh/m^2a).
	 * 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function Qf(): float {
		// $Qf =   $Qfges -  $Pvans
		$Qf = $this->Qfgesamt();

		if( $this->photovoltaik_anlage_vorhanden() ) {
			$Qf -= $this->photovoltaik_anlage()->Pvans( $this->Qfstrom() );
		}

		// $Qf =   $Qf/$calculations['nutzflaeche']
		return $Qf / $this->nutzflaeche();
	}

	
	public function Qp(): float {
		$Qp = $this->Qpges();

		if( ! $this->photovoltaik_anlage_vorhanden() ) {
			return $this->Qpges() / $this->nutzflaeche();
		}

		//  $Qp= $Qpges -  $Pvans *1.8
		$Qp -= $this->photovoltaik_anlage()->Pvans( $this->Qfstrom() ) * 1.8;

		return $Qp / $this->nutzflaeche();
	}
}
