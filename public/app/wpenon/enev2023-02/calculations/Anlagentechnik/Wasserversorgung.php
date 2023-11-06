<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202302\Calculations\Tabellen\Kessel_Nennleistung;
use Enev\Schema202302\Calculations\Tabellen\Monatsdaten;

use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

/**
 * Bestimmung des Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen
 * über Tabelle 142 & 143 Abschnitt 12.
 */
class Wasserversorgung {
	/**
	 * Gebäude.
	 *
	 * @var Gebaeude
	 */
	protected Gebaeude $gebaeude;

	/**
	 * Handelt sich um eine zentrale Wasserversorgung (true) oder um eine dezentrale (false)?
	 *
	 * @var bool
	 */
	protected bool $zentral;

	/**
	 * Welcher Teil der Anlage ist beheizt. Mögliche Werte: 'alles', 'nichts' oder 'verteilung'.
	 *
	 * @var string
	 */
	protected string $beheizte_bereiche;

	/**
	 * Liegt eine Warmwasserspeicher vor?
	 *
	 * @var bool $mit_warmwasserspeicher
	 */
	protected bool $mit_warmwasserspeicher;

	/**
	 * Ist die Anlage mit Zirkulation?
	 *
	 * @var bool $mit_zirkulation
	 */
	protected bool $mit_zirkulation;

	/**
	 * Prozentualer Anteil.
	 *
	 * @var int
	 */
	protected int $prozentualer_anteil;

	/**
	 * Monatsdaten
	 *
	 * @var Monatsdaten
	 */
	protected Monatsdaten $monatsdaten;

	/**
	 * Liegt eine Warmwasserspeicher vor
	 *
	 * @param bool $zentral            Handelt sich um eine zentrale Wasserversorgung (true) oder um eine dezentrale (false)?
	 * @param bool $beheizte_bereiche  Welcher Teil der Anlage ist beheizt. Mögliche Werte: 'alles', 'nichts' oder 'verteilung'.
	 * @param bool $mit_warmwasserspeicher Liegt eine Warmwasserspeicher vor?
	 * @param bool $mit_zirkulation        Trinkwasserverteilung mit Zirkulation (true) oder ohne (false).
	 */
	public function __construct(
		Gebaeude $gebaeude,
		bool $zentral,
		string $beheizte_bereiche = 'alles',
		bool $mit_warmwasserspeicher = false,
		bool $mit_zirkulation = false,
		int $prozentualer_anteil = 100
	) {
		// Beheizung der Anlage überprüfen und wenn falsch angegeben, Fehler werfen.
		if ( $beheizte_bereiche !== 'alles' && $beheizte_bereiche !== 'nichts' && $beheizte_bereiche !== 'verteilung' ) {
			throw new Calculation_Exception( 'Beheizung der Anlage muss entweder "alles", "nichts" oder "verteilung" sein.' );
		}

		if ( $mit_zirkulation && ! $zentral ) {
			throw new Calculation_Exception( 'Zirkulation ist nur bei zentraler Wasserversorgung möglich.' );
		}

		$this->gebaeude               = $gebaeude;
		$this->zentral                = $zentral;
		$this->beheizte_bereiche      = $beheizte_bereiche;
		$this->mit_warmwasserspeicher = $mit_warmwasserspeicher;
		$this->mit_zirkulation        = $mit_zirkulation;
		$this->prozentualer_anteil    = $prozentualer_anteil;

		$this->monatsdaten = new Monatsdaten(); }

	/**
	 * Prozentualer Anteil.
	 *
	 * @return int
	 */
	public function prozentualer_anteil(): int {
		return $this->prozentualer_anteil;
	}

	/**
	 * Prozentualer Faktor.
	 *
	 * @return float
	 */
	public function prozentualer_faktor(): float {
		return $this->prozentualer_anteil() / 100;
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
	 * @return float Nutzwärmebedarf für Trinkwasser (qwb) in kWh/(ma).
	 */
	public function nutzwaermebedarf_trinkwasser(): float {
		$keys = array(
			0,
			10,
			20,
			30,
			40,
			50,
			60,
			70,
			80,
			90,
			100,
			110,
			120,
			130,
			140,
			150,
			160,
		);

		$values = array(
			16.5,
			16,
			15.5,
			15,
			14.5,
			14,
			13.5,
			13,
			12.5,
			12,
			11.5,
			11,
			10.5,
			10,
			9.5,
			9,
			8.5,
		);

		return interpolate_value( $this->gebaeude->nutzflaeche(), $keys, $values );
	}

	/**
	 * Berechnung des monatlichen Wärmebedarfs für Warmwasser (qwb).
	 *
	 * @param string $monat Slug des Monats.
	 *
	 * @return float Nutzwärmebedarf für Trinkwasser (qwb) in kWh.
	 */
	public function QWB_monat( string $monat ): float {
		$qwb = $this->nutzwaermebedarf_trinkwasser( $this->gebaeude->nutzflaeche() );
		return ( $this->gebaeude->nutzflaeche() / $this->gebaeude->anzahl_wohnungen() ) * $qwb * ( $this->monatsdaten->tage( $monat ) / 365 );
	}

	/**
	 * Berechnung des monatlichen Wärmebedarfs für Warmwasser(QWB) für ein Jahr.
	 *
	 * @return float Nutzwärmebedarf für Trinkwasser (qwb) in kWh.
	 */
	public function QWB(): float {
		$qwb = 0;

		foreach ( $this->monatsdaten->monate() as $monat ) {
			$qwb += $this->QWB_monat( $monat );
		}

		return $qwb;
	}

	/**
	 * Berechnung von pwn. 
	 * 
	 * @return float 
	 */
	public function pwn(): float {	
		$pwn = 0;
		
		if( $this->zentral ) {
			if( $this->gebaeude->nutzflaeche() >= 5000 ) {
				$pwn = 0.042 * ( ( $this->nutzwaermebedarf_trinkwasser() * $this->gebaeude->nutzflaeche() ) / ( 365 * 0.036 ) ) ** 0.7;
			} else {
				$pwn = ( new Kessel_Nennleistung( $this->gebaeude->nutzflaeche(), $this->nutzwaermebedarf_trinkwasser() ) )->nennleistung();
			}
		}

		if( $pwn > $this->gebaeude->luftwechsel()->h_max() ) {
			return $this->gebaeude->luftwechsel()->h_max();
		}

		return $pwn;
	}

	/**
	 * Bestimmung des Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen.
	 *
	 * @return float
	 */
	public function fh_w(): float {
		// There is
		if ( ! $this->zentral ) {
			return 0.193;
		}

		if ( ! $this->mit_warmwasserspeicher ) {
			return $this->fh_w_ohne_warmwasserspeicher();
		}

		return $this->fh_w_mit_warmwasserspeicher();
	}

	/**
	 * Bestimmung des Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen mit Warmwasserspeicher.
	 *
	 * @return float
	 */
	protected function fh_w_mit_warmwasserspeicher(): float {
		// Werte aus Tabelle 142 & 143 nach den drei
		// Möglichkeiten der Beheizung der Anlage aufgeteilt,
		// je nachdem ob mit oder ohne Zirkulation.
		switch ( $this->beheizte_bereiche ) {
			case 'alles':
				return $this->mit_zirkulation ? 1.554 : 0.647;
			case 'nichts':
				return $this->mit_zirkulation ? 0.815 : 0.335;
			case 'verteilung':
				return $this->mit_zirkulation ? 1.321 : 0.451;
		}
	}

	/**
	 * Bestimmung des Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen ohne Warmwasserspeicher.
	 *
	 * @return float
	 */
	protected function fh_w_ohne_warmwasserspeicher(): float {
		// Werte aus Tabelle 142 & 143 ohne Warmwasserspeicher
		// je nachdem ob mit oder ohne Zirkulation.
		// Es wird der schlechtere Wert der beidem beheizten Varianten genommen.
		if ( $this->mit_zirkulation ) {
			return 1.321;
		}

		return 0.451;
	}
}
