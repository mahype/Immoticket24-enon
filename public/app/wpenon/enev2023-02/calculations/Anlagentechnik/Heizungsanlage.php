<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;

/**
 * Berechnungen für eine Heizungsanlage.
 */
abstract class Heizungsanlage {
	/**
	 * Gebaeude.
	 *
	 * @var Gebaeude
	 */
	protected Gebaeude $gebaeude;

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
	 * Qfhges.
	 * 
	 * @return float
	 */
	protected float $Qfhges;

	/**
	 * Qfwges.
	 * 
	 * @return float
	 */
	protected float $Qfwges;

	/**
	 * Konstruktor.
	 *
	 * @param Gebaeude $gebaeude
	 * @param string   $erzeuger                    Typ der Heizungsanlage.
	 * @param string   $energietraeger              Energieträger der Heizungsanlage.
	 * @param int      $baujahr                     Baujahr der Heizungsanlage.
	 * @param bool     $heizung_im_beheizten_bereich       Liegt die Heizungsanlage der Heizung im beheiztem Bereich.
	 * @param int      $prozentualer_anteil    Prozentualer Anteil der Heizungsanlage im Heizsystem
	 */
	public function __construct( Gebaeude $gebaeude, string $erzeuger, string $energietraeger, int $baujahr, bool $heizung_im_beheizten_bereich, int $prozentualer_anteil = 100 ) {
		$erlaubte_erzeuger = array_keys( static::erlaubte_erzeuger() );

		if ( ! in_array( $erzeuger, $erlaubte_erzeuger ) ) {
			throw new Calculation_Exception( sprintf( 'Der erzeuger "%s" ist nicht erlaubt.', $erzeuger ) );
		}

		$erlaubte_energietraeger = array_keys( static::erlaubte_energietraeger( $erzeuger ) );

		if ( ! in_array( $energietraeger, $erlaubte_energietraeger ) ) {
			throw new Calculation_Exception( sprintf( 'Der Energieträger "%s" der Heizungsanlage für den Erzeuger "%s" nicht erlaubt.', $energietraeger, $erzeuger ) );
		}

		$this->gebaeude                     = $gebaeude;
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
		if ( ! array_key_exists( $erzeuger, static::erlaubte_erzeuger() ) ) {
			throw new Calculation_Exception( 'Der Erzeuger "' . $erzeuger . '" ist nicht erlaubt.' );
		}

		if ( ! array_key_exists( 'energietraeger', static::erlaubte_erzeuger()[ $erzeuger ] ) ) {
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
		$path = explode( '\\', static::class );
		return strtolower( array_pop( $path ) );
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
	 * Hilfsenergie für Heizunganlage im Bereich Erzeugung.
	 *
	 * @return float
	 */
	abstract public function Whg(): float;

	/**
	 * Korrekturfaktor Trinkwarmwasser im Bereich Erzeugung.
	 *
	 * @return float
	 */
	abstract public function ewg(): float;

	/**
	 * Primärenergiefaktor für einen bestimmten Energieträger.
	 * 
	 * Werte aus Tabelle A.1 Teil 1.
	 * 
	 * @param string $energietraeger 
	 * @return float|void 
	 */
	protected function fp_energietraeger( string $energietraeger ) {
		switch( $energietraeger ) {
			case 'biogas':
			case 'biooel':
				return 0.4;
			case 'holzpellets':
			case 'holzhackschnitzel':
			case 'stueckholz':
				return 0.2;
			case 'heizoel':
			case 'erdgas':
			case 'fluessiggas':
			case 'steinkohle':
				return 1.1;
			case 'braunkohle':
				return 1.2;
			case 'fernwaermekwkwfossil':
				return 0.7;
			case 'fernwaermehzwfossil':
				return 1.3;
			case 'strom':
				return 1.8;
		}
	}

	/**
	 * Primärenergiefaktor des Energieträgers.
	 * 
	 * @return float 
	 */
	public function fp(): float {
		return $this->fp_energietraeger( $this->energietraeger() );
	}

	/**
	 * Energieträgerabhängige Umrechnungsfaktor für einen bestimmt Energieträger.
	 * 
	 * Werte aus Tabelle B.1 Teil 1.
	 * 
	 * @param string $energietraeger 
	 * @return float|void 
	 */
	protected function fhshi_energietraeger( string $energietraeger ) {
		switch( $energietraeger ) {
			case 'biooel':
			case 'heizoel':
				return 1.06;
			case 'biogas':
			case 'erdgas':
				return 1.11;
			case 'steinkohle':
				return 1.04;
			case 'braunkohle':
				return 1.07;
			case 'holzpellets':
			case 'holzhackschnitzel':
			case 'stueckholz':
				return 1.08;
			case 'heizoel':			
			case 'fernwaermekwkwfossil':
			case 'fernwaermehzwfossil':				
			case 'strom':
				return 1.0;
		}
	}

	/**
	 * Energieträgerabhängige Umrechnungsfaktor.
	 * 
	 * @return float 
	 */
	public function fhshi(): float {
		return $this->fhshi_energietraeger( $this->energietraeger() );
	}

	/**
	 *
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function kee(): float {
		// Hier wird nur WWS berücksichtig, Endenergie WWS, Bei Solaranlage vornadnen dann ist kee=0,5 laut Tab. 59 und Banz Tab. 8 flachkollektoren oder wenn nicht dann kee =0
		if ( $this->gebaeude->trinkwarmwasseranlage()->solarthermie_vorhanden() ) {
			return 0.5;
		}

		return 0.0;
	}

	/**
	 * Korrigierter Korrekturfaktor für die Heizungsanlage.
	 *
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function ehg_korrektur(): float {
		return 1 + ( $this->ehg() - 1 ) * ( 8760 / $this->gebaeude->ith_rl() ); // Inkl. Korrektur.
	}

	/**
	 * Hilfsenergie für Warmwasser (Wwg).
	 *
	 * @return float;
	 */
	abstract public function Wwg(): float;

	public function Qfhges(): float {
		if( isset( $this->Qfhges ) ) {
			return $this->Qfhges;
		}

		// $Qfhges1=  (($calculations['qh']*ece*ed)*es*eg1*$kgn1)
		$this->Qfhges = ( $this->gebaeude->qh() * $this->gebaeude->heizsystem()->ehce() * $this->gebaeude->heizsystem()->ehd_korrektur() ) * $this->gebaeude->heizsystem()->ehs() * $this->ehg_korrektur() * $this->prozentualer_faktor();
		
		return $this->Qfhges;
	}

	public function Qfwges(): float {
		if( isset( $this->Qfwges ) ) {
			return $this->Qfwges;
		}

		// $Qfwges1=  (($calculations['QWB']']*$ewce*$ewd)*$ews*$ewg1*$kgn1*(1-$kee))
		$this->Qfwges = ( ( $this->gebaeude->trinkwarmwasseranlage()->QWB() * $this->gebaeude->trinkwarmwasseranlage()->ewce() * $this->gebaeude->trinkwarmwasseranlage()->ewd() ) * $this->gebaeude->trinkwarmwasseranlage()->ews() * $this->ewg() * $this->prozentualer_faktor() * ( 1 - $this->kee() ) );

		return $this->Qfwges;
	}

	public function Qpges(): float {
		if( isset( $this->Qpges ) ) {
			return $this->Qpges;
		}

		// Trinkwarmwasseranlage zentral
		if( $this->gebaeude->trinkwarmwasseranlage()->zentral() ) {
			// $Qpges1=($Qfhges1+$Qfwges1)*($fp1/$fhshi1)
			$this->Qpges = ( $this->Qfhges() + $this->Qfwges() ) * ( $this->fp() / $this->fhshi() );
			return $this->Qpges;
		}

		// Trinkwarmwasseranlage dezentral
		$this->Qpges = ( $this->Qfhges() ) * ( $this->fp() / $this->fhshi() );
		return $this->Qpges;
	}
}
