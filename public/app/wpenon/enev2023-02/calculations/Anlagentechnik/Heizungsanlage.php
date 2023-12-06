<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Calculation_Exception;

/**
 * Berechnungen für eine Heizungsanlage.
 */
abstract class Heizungsanlage {

	/**
	 * Erlaubte Typen.
	 *
	 * @var array
	 */
	protected array $erlaubte_erzeuger;

	/**
	 * Erzeuger.
	 *
	 * @var string
	 */
	protected string $erzeuger;

	/**
	 * Energietraeger.
	 *
	 * @var string
	 */
	protected string $energietraeger;

	/**
	 * Baujahr.
	 */
	protected int $baujahr;

	/**
	 * Welcher Teil der Anlage ist beheizt. Mögliche Werte: 'alles', 'nichts', 'verteilung' oder 'verteilung_erzeuger'.
	 *
	 * @var string
	 */
	protected string $heizung_im_beheizten_bereich;

	/**
	 * Prozentualer Anteil der Heizungsanlage im Heizsystem
	 *
	 * @var int
	 */
	protected int $prozentualer_anteil;

	/**
	 * Konstruktor.
	 *
	 * @param string $erzeuger                    Typ der Heizungsanlage.
	 * @param string $auslegungstemperaturen Auslegungstemperaturen der Heizungsanlage. Mögliche Werte: ' 90/70', '70/55', '55/45' oder '35/28'.
	 * @param bool   $heizung_im_beheizten_bereich       Liegt die Heizungsanlage der Heizung im beheiztem Bereich.
	 * @param int    $prozentualer_anteil    Prozentualer Anteil der Heizungsanlage im Heizsystem
	 */
	public function __construct( string $erzeuger, string $energietraeger, int $baujahr, bool $heizung_im_beheizten_bereich, int $prozentualer_anteil = 100 ) {
		$erlaubte_erzeuger = array_keys( static::erlaubte_erzeuger() );

		if ( ! in_array( $erzeuger, $erlaubte_erzeuger ) ) {
			throw new Calculation_Exception( sprintf( 'Der erzeuger "%s" nicht erlaubt.', $erzeuger ) );
		}

		$erlaubte_energietraeger = array_keys( static::erlaubte_energietraeger( $erzeuger ) );

		if ( ! in_array( $energietraeger, $erlaubte_energietraeger ) ) {
			throw new Calculation_Exception( sprintf( 'Der Energieträger "%s" der Heizungsanlage für den Erzeuger "%s" nicht erlaubt.', $energietraeger, $erzeuger ) );
		}

		$this->erzeuger                     = $erzeuger;
		$this->energietraeger               = $energietraeger;
		$this->baujahr                      = $baujahr;
		$this->heizung_im_beheizten_bereich = $heizung_im_beheizten_bereich;
		$this->prozentualer_anteil          = $prozentualer_anteil;
	}

	/**
	 * Erlaubte Typen der Heizungsanlage.
	 *
	 * @return array
	 */
	abstract public static function erlaubte_erzeuger(): array;

	/**
	 * Typ der Heizungsanlage.
	 *
	 * @return string
	 */
	public function erzeuger(): string {
		return $this->erzeuger;
	}

	/**
	 * Erlaubte Energietraeger.
	 *
	 * @return array
	 */
	public static function erlaubte_energietraeger( $erzeuger ): array {
		if( ! array_key_exists( $erzeuger, static::erlaubte_erzeuger() ) ) {
			throw new Calculation_Exception( 'Der Erzeuger "' . $erzeuger . '" ist nicht erlaubt.' );
		}

		if( ! array_key_exists( 'energietraeger', static::erlaubte_erzeuger()[ $erzeuger ] ) ) {
			throw new Calculation_Exception( 'Der Erzeuger "' . $erzeuger . '" hat keine Energieträger.' );
		}

		return static::erlaubte_erzeuger()[ $erzeuger ]['energietraeger'];
	}

	/**
	 * Energietraeger.
	 *
	 * @return string
	 */
	public function energietraeger(): string {
		return $this->energietraeger;
	}

	/**
	 * Typ der Heizungsanlage.
	 *
	 * @return string
	 */
	public function typ(): string {
		return $this->erlaubte_erzeuger()[ $this->erzeuger() ]['typ'];
	}

	public function kategorie(): string {
		$path = explode('\\', static::class);
		return strtolower( array_pop($path) );
	}

	/**
	 * Baujahr.
	 *
	 * @return int
	 */
	public function baujahr(): int {
		return $this->baujahr;
	}

	/**
	 * Beheizung der Anlage.
	 *
	 * @return bool
	 */
	public function heizung_im_beheizten_bereich(): bool {
		return $this->heizung_im_beheizten_bereich;
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
	 * Nutzbare Wärme.
	 *
	 * @param string $auslegungstemperaturen Auslegungstemperaturen der Heizungsanlage. Mögliche Werte: ' 90/70', '70/55', '55/45' oder '35/28'.
	 *                                       Wenn nicht angegeben, wird der Wert aus dem Konstruktor verwendet. Es können auch andere Auslegungstemperaturen
	 *                                       als die der Heizungsanlage angesetzt werden (beispielsweise die des Übergabesystems).
	 *
	 * @return float Anteils nutzbarer Wärme von Heizungsanlagen (fa-h) aus Tabelle 141 / Teil 12, anteilig für die Heizungsanlage.
	 */
	public function fa_h( $auslegungstemperaturen = null ) {
		// Wertzuweisungen je nach Auslegungstemperatur und Beheizung der Anlage.
		switch ( $auslegungstemperaturen ) {
			case '90/70':
				return $this->heizung_im_beheizten_bereich() ? 0.123 : 0.039;
			case '70/55':
				return $this->heizung_im_beheizten_bereich() ? 0.099 : 0.028;
			case '55/45':
				return $this->heizung_im_beheizten_bereich() ? 0.082 : 0.02;
			case '35/28':
				return $this->heizung_im_beheizten_bereich() ? 0.057 : 0.008;
			default:
				throw new Calculation_Exception( 'Auslegungstemperaturen müssen entweder "90/70", "70/55", "55/45" oder "35/28" sein.' );
		}
	}

	/**
	 * Hilfsenergie für Heizungsystem (WHg).
	 * 
	 * @return float;
	 */
	abstract public function WHg(): float;
}
