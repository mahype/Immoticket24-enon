<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202302\Calculations\Tabellen\Monatsdaten;

use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Tabellen/Kessel_Nennleistung.php';

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
	 * @var bool
	 */
	protected bool $heizung_im_beheizten_bereich;

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
	 * @param Gebaeude $gebaeude               Gebäude.
	 * @param bool     $zentral                Läuft die Warmwasserversorgung über die Heizungsanlage?
	 * @param bool     $heizung_im_beheizten_bereich      Liegt die Heizung im beheitzen Bereich?
	 * @param bool     $mit_warmwasserspeicher Liegt eine Warmwasserspeicher vor?
	 * @param bool     $mit_zirkulation        Trinkwasserverteilung mit Zirkulation (true) oder ohne (false).
	 * @param int      $prozentualer_anteil    Prozentualer Anteil.
	 */
	public function __construct(
		Gebaeude $gebaeude,
		bool $zentral,
		bool $heizung_im_beheizten_bereich,
		bool $mit_warmwasserspeicher = false,
		bool $mit_zirkulation = false,
		int $prozentualer_anteil = 100
	) {
		if ( $mit_zirkulation && ! $zentral ) {
			throw new Calculation_Exception( 'Zirkulation ist nur bei zentraler Wasserversorgung möglich.' );
		}

		$this->gebaeude                     = $gebaeude;
		$this->zentral                      = $zentral;
		$this->heizung_im_beheizten_bereich = $heizung_im_beheizten_bereich;
		$this->mit_warmwasserspeicher       = $mit_warmwasserspeicher;
		$this->mit_zirkulation              = $mit_zirkulation;
		$this->prozentualer_anteil          = $prozentualer_anteil;

		$this->monatsdaten = new Monatsdaten();
	}

	/**
	 * Läuft die Warmwasserversorgung über die Heizungsanlage?
	 *
	 * @return bool
	 */
	public function zentral(): bool {
		return $this->zentral;
	}

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

		return interpolate_value( $this->gebaeude->nutzflaeche_pro_wohneinheit(), $keys, $values );
	}

	/**
	 * Berechnung des monatlichen Wärmebedarfs für Warmwasser (qwb).
	 *
	 * @param string $monat Slug des Monats.
	 *
	 * @return float Nutzwärmebedarf für Trinkwasser (qwb) in kWh.
	 */
	public function QWB_monat( string $monat ): float {
		$qwb = $this->nutzwaermebedarf_trinkwasser();
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
	 * Bestimmung des Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen.
	 *
	 * @return float
	 */
	public function Faw(): float {
		// There is
		if ( ! $this->zentral ) {
			return 0.193;
		}

		if ( ! $this->mit_warmwasserspeicher ) {
			return $this->Faw_ohne_warmwasserspeicher();
		}

		return $this->Faw_mit_warmwasserspeicher();
	}

	/**
	 * Bestimmung des Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen mit Warmwasserspeicher.
	 *
	 * @return float
	 */
	protected function Faw_mit_warmwasserspeicher(): float {
		// Werte aus Tabelle 142 & 143 nach den drei
		// Möglichkeiten der Beheizung der Anlage aufgeteilt,
		// je nachdem ob mit oder ohne Zirkulation.
		if( $this->heizung_im_beheizten_bereich ) {
			return $this->mit_zirkulation ? 1.554 : 0.647;
		}

		return $this->mit_zirkulation ? 0.815 : 0.335;
	}

	/**
	 * Bestimmung des Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen ohne Warmwasserspeicher.
	 *
	 * @return float
	 */
	protected function Faw_ohne_warmwasserspeicher(): float {
		// Werte aus Tabelle 142 & 143 ohne Warmwasserspeicher
		// je nachdem ob mit oder ohne Zirkulation.
		// Es wird der schlechtere Wert der beidem beheizten Varianten genommen.
		if ( $this->mit_zirkulation ) {
			return 1.321;
		}

		return 0.451;
	}
}
