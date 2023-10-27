<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

/**
 * Bestimmung des Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen
 * über Tabelle 142 & 143 Abschnitt 12.
 */
class Wasserversorgung {

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
	 * Liegt eine Warmwasserspeicher vor
	 *
	 * @param bool $zentral            Handelt sich um eine zentrale Wasserversorgung (true) oder um eine dezentrale (false)?
	 * @param bool $beheizte_bereiche  Welcher Teil der Anlage ist beheizt. Mögliche Werte: 'alles', 'nichts' oder 'verteilung'.
	 * @param bool $mit_warmwasserspeicher Liegt eine Warmwasserspeicher vor?
	 * @param bool $mit_zirkulation        Trinkwasserverteilung mit Zirkulation (true) oder ohne (false).
	 */
	public function __construct(
		bool $zentral,
		string $beheizte_bereiche = 'alles',
		bool $mit_warmwasserspeicher = false,
		bool $mit_zirkulation = false,
		int $prozentualer_anteil = 100
	) {
		// Beheizung der Anlage überprüfen und wenn falsch angegeben, Fehler werfen.
		if ( $beheizte_bereiche !== 'alles' && $beheizte_bereiche !== 'nichts' && $beheizte_bereiche !== 'verteilung' ) {
			throw new Exception( 'Beheizung der Anlage muss entweder "alles", "nichts" oder "verteilung" sein.' );
		}

		if ( $mit_zirkulation && ! $zentral ) {
			throw new Exception( 'Zirkulation ist nur bei zentraler Wasserversorgung möglich.' );
		}

		$this->zentral                = $zentral;
		$this->beheizte_bereiche      = $beheizte_bereiche;
		$this->mit_warmwasserspeicher = $mit_warmwasserspeicher;
		$this->mit_zirkulation        = $mit_zirkulation;
		$this->prozentualer_anteil    = $prozentualer_anteil;
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
			return $this->fh_w_ohne_mit_warmwasserspeicher();
		}

		return $this->fh_w_mit_mit_warmwasserspeicher();
	}

	/**
	 * Bestimmung des Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen mit Warmwasserspeicher.
	 *
	 * @return float
	 */
	protected function fh_w_mit_mit_warmwasserspeicher(): float {
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
	protected function fh_w_ohne_mit_warmwasserspeicher(): float {
		// Werte aus Tabelle 142 & 143 ohne Warmwasserspeicher
		// je nachdem ob mit oder ohne Zirkulation.
		// Es wird der schlechtere Wert der beidem beheizten Varianten genommen.
		if ( $this->mit_zirkulation ) {
			return 1.321;
		}

		return 0.451;
	}
}
