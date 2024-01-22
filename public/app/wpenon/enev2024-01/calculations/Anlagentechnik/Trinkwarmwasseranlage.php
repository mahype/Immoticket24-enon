<?php

namespace Enev\Schema202401\Calculations\Anlagentechnik;

use Enev\Schema202401\Calculations\Calculation_Exception;
use Enev\Schema202401\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202401\Calculations\Tabellen\Monatsdaten;
use Enev\Schema202401\Calculations\Tabellen\Thermische_Solaranlagen;
use Enev\Schema202401\Calculations\Tabellen\Umrechnungsfaktoren_Kollektorflaeche;
use Enev\Schema202401\Calculations\Tabellen\Waermeverlust_Trinkwasserspeicher;

use function Enev\Schema202401\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Tabellen/Kessel_Nennleistung.php';
require_once dirname( __DIR__ ) . '/Tabellen/Thermische_Solaranlagen.php';
require_once dirname( __DIR__ ) . '/Tabellen/Umrechnungsfaktoren_Kollektorflaeche.php';
require_once dirname( __DIR__ ) . '/Tabellen/Waermeverlust_Trinkwasserspeicher.php';

/**
 * Bestimmung des Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen
 * über Tabelle 142 & 143 Abschnitt 12.
 */
class Trinkwarmwasseranlage {
	/**
	 * Gebäude.
	 *
	 * @var Gebaeude
	 */
	protected Gebaeude $gebaeude;

	/**
	 * Handelt sich um eine zentrale Trinkwarmwasseranlage (true) oder um eine dezentrale (false)?
	 *
	 * @var bool
	 */
	protected bool $zentral;

	/**
	 * Dezenraler Erzeuger
	 *
	 * @var string|null
	 */
	protected ?string $erzeuger;

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
	 * Wird Solarthermie genutzt?
	 *
	 * @var bool $mit_solarthermie
	 */
	protected bool $mit_solarthermie;

	/**
	 * Solarthermie Neigung.
	 *
	 * @var int|null
	 */
	protected ?int $solarthermie_neigung;

	/**
	 * Solarthermie Richtung.
	 *
	 * @var string|null
	 */
	protected ?string $solarthermie_richtung;

	/**
	 * Solarthermie Baujahr.
	 *
	 * @var int|null
	 */
	protected ?int $solarthermie_baujahr;

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
	 * Daten für eine Thermische Solaranlage.
	 *
	 * @var Thermische_Solaranlagen
	 */
	protected Thermische_Solaranlagen $thermische_solaranlagen;

	// solarthermie_neigung
	// solarthermie_richtung
	// solarthermie_baujahr

	/**
	 * Liegt eine Warmwasserspeicher vor
	 *
	 * @param Gebaeude    $gebaeude               Gebäude.
	 * @param bool        $zentral                Läuft die Warmwasserversorgung über die Heizungsanlage?
	 * @param bool        $heizung_im_beheizten_bereich      Liegt die Heizung im beheitzen Bereich?
	 * @param string|null $erzeuger   			  Dezentraler Erzeuger (dezentralgaserhitzer oder dezentralelektroerhitzer).
	 * @param bool        $mit_warmwasserspeicher Liegt eine Warmwasserspeicher vor?
	 * @param bool        $mit_zirkulation        Trinkwasserverteilung mit Zirkulation (true) oder ohne (false).
	 * @param bool        $mit_solarthermie       Wird die Trinkwarmwasseranlage mit Solarthermie betrieben?
	 * @param int|null    $solarthermie_neigung   Neigung der Solarthermie.
	 * @param string|null $solarthermie_richtung  Himmelsrichtung der Solarthermieanlage.
	 * @param int|null    $solarthermie_baujahr   Baujahr der Solarthermieanlage.
	 * @param int         $prozentualer_anteil    Prozentualer Anteil.
	 */
	public function __construct(
		Gebaeude $gebaeude,
		bool $zentral,
		bool $heizung_im_beheizten_bereich,
		string|null $erzeuger = null,
		bool $mit_warmwasserspeicher = false,
		bool $mit_zirkulation = false,
		bool $mit_solarthermie = false,
		int $solarthermie_neigung = null,
		string $solarthermie_richtung = null,
		int $solarthermie_baujahr = null,
		int $prozentualer_anteil = 100
	) {
		if ( $mit_zirkulation && ! $zentral ) {
			throw new Calculation_Exception( 'Zirkulation dezentraler Trinkwasseranlage ist nicht möglich.' );
		}

		$this->monatsdaten = new Monatsdaten();

		$this->gebaeude                     = $gebaeude;
		$this->zentral                      = $zentral;
		$this->heizung_im_beheizten_bereich = $heizung_im_beheizten_bereich;
		$this->erzeuger                     = $erzeuger;
		$this->mit_warmwasserspeicher       = $mit_warmwasserspeicher;
		$this->mit_zirkulation              = $mit_zirkulation;
		$this->mit_solarthermie             = $mit_solarthermie;
		$this->prozentualer_anteil          = $prozentualer_anteil;

		if ( $mit_solarthermie ) {
			$this->thermische_solaranlagen = new Thermische_Solaranlagen( $this->gebaeude->nutzflaeche(), $this->gebaeude->heizsystem()->beheizt() );
			$this->solarthermie_neigung    = $solarthermie_neigung;
			$this->solarthermie_richtung   = $solarthermie_richtung;
			$this->solarthermie_baujahr    = $solarthermie_baujahr;
		}
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
	 * Dezentraler Erzeuger (dezentralgaserhitzer oder dezentralelektroerhitzer).
	 *
	 * @return string|null
	 */
	public function erzeuger(): ?string {
		return $this->erzeuger;
	}

	/**
	 * Wird die Trinkwarmwasseranlage mit Solarthermie betrieben?
	 *
	 * @return bool
	 */
	public function solarthermie_vorhanden(): bool {
		return $this->mit_solarthermie;
	}

	/**
	 * ewce
	 *
	 * @return float
	 */
	public function ewce(): float {
		return 1.0;
	}

	/**
	 * Zwischenwert für die Berechnung von ewd (ewd0).
	 *
	 * @return float
	 */
	public function ewd0(): float {
		if ( ! $this->zentral() ) {
			return 1.193;
		}

		if ( ! $this->gebaeude->heizsystem()->beheizt() ) {
			return 2.290;
		} else {
			return 2.252;
		}
	}

	/**
	 * Aufwandszahlen für die Verteilung von Trinkwarmwasser.
	 *
	 * @return float
	 */
	public function ewd() {
		return 1 + ( $this->ewd0() - 1 ) * ( 12.5 / $this->nutzwaermebedarf_trinkwasser() );
	}

	/**
	 * Bestimmung des Korrekturfaktors (fwb).
	 *
	 * @return float
	 */
	public function fwb(): float {
		return ( $this->nutzwaermebedarf_trinkwasser() / 12.5 ) * ( 1 + ( $this->ewd0() - 1 ) * ( 12.5 / $this->nutzwaermebedarf_trinkwasser() ) ) / $this->ewd0();
	}

	/**
	 * Volumen Speicher 1 in Litern.
	 *
	 * @return float
	 */
	public function Vs01(): float {
		if ( $this->gebaeude->nutzflaeche() < 5000 ) {
			return $this->interpoliertes_volumen( $this->gebaeude->nutzflaeche() );
		}

		return 1122;
	}

	/**
	 * Volumen Speicher 2 in Litern.
	 *
	 * @return float
	 */
	public function Vs02(): float {
		if ( $this->gebaeude->nutzflaeche() < 5000 ) {
			return 0;
		}

		if ( $this->gebaeude->nutzflaeche() < 10000 ) {
			return $this->interpoliertes_volumen( $this->gebaeude->nutzflaeche() - 5000 );
		}

		return 1122;
	}

	/**
	 * Volumen Speicher 3 in Litern.
	 *
	 * @return float
	 */
	public function Vs03(): float {
		if ( $this->gebaeude->nutzflaeche() < 10000 ) {
			return 0;
		}

		if ( $this->gebaeude->nutzflaeche() < 13368.98 ) {
			return $this->interpoliertes_volumen( $this->gebaeude->nutzflaeche() - 10000 );
		}

		// NOTE: Was bedeutet "Vs03 wird nicht berücksichtigt"?
		return 756; // Die drei Volumina addiert ergeben 3336 Liter, 3000 Liter sind max. nach T12 zulässig deswegen werden beim letzten Speicher abgezogen. Vs03 wird nicht berücksichtigt.
	}

	/**
	 * Berechnung von Vsw.
	 *
	 * @return float
	 */
	public function Vsw(): float {
		return $this->Vs0() * ( $this->nutzwaermebedarf_trinkwasser() / 12.5 );
	}

	/**
	 * Berechnung von Vsw1. // NOTE: Was ist das?
	 *
	 * @return float
	 */
	public function Vsw1(): float {
		$Vsw = $this->Vsw();

		if ( $Vsw > 3000 ) {
			$Vsw = 3000;
		}

		if ( $Vsw >= 1500 ) {
			return 1500;
		}

		return $Vsw;
	}

	/**
	 * Berechnung von Vsw2. // NOTE: Was ist das?
	 *
	 * @return float
	 */
	public function Vsw2(): float {
		$Vsw = $this->Vsw();

		if ( $Vsw > 3000 ) {
			$Vsw = 3000;
		}

		if ( $Vsw < 1500 ) {
			return 0;
		}

		return $Vsw - 1500;
	}

	/**
	 * Berechnung des Wärmeverlusts mittels Vsw1 ohne solar.
	 *
	 * @return float
	 */
	public function Qws01(): float {
		return ( new Waermeverlust_Trinkwasserspeicher( $this->Vsw1(), $this->heizung_im_beheizten_bereich, $this->mit_zirkulation ) )->Qws0();
	}

	/**
	 * Berechnung des Wärmeverlusts mittels Vsw2 ohne solar.
	 *
	 * @return float
	 */
	public function Qws02(): float {
		if ( $this->Vsw2() == 0 ) {
			return 0;
		}

		return ( new Waermeverlust_Trinkwasserspeicher( $this->Vsw2(), $this->heizung_im_beheizten_bereich, $this->mit_zirkulation ) )->Qws0();
	}

	/**
	 * Gesamter Wärmeverlust. (Wärmeverluste eines bivalenten Speichers)
	 *
	 * @return float
	 */
	public function Qws(): float {
		if ( $this->solarthermie_vorhanden() ) {
			return $this->Qws_mit_solar();
		}

		return $this->Qws_ohne_solar();
	}

	/**
	 * Berechnung Hilfsenergie Trinkwarmwasser Wws
	 *
	 * @return float
	 */
	public function Qwoutg(): float {
		// Qwoutg = QWB * ewce * ewd * ews
		return $this->QWB() * $this->ewce() * $this->ewd() * $this->ews();
	}

	/**
	 * Berechnung von ews.
	 *
	 * @return float
	 */
	public function ews(): float {
		if ( ! $this->zentral ) {
			return 1;
		}

		// Berechnung der Aufwandszahl Trinkwarmwasserspeicher ews inklusive thermischer Solaranlage. ews hier bezieht sich nur auf reine Trinkwassernutzung der Solaranlag
		return 1 + ( $this->Qws() / ( $this->QWB() * $this->ewd() * $this->ewce() ) );
	}

	/**
	 * Berechnung von keew.
	 *
	 * @return float
	 */
	public function keew(): float {
		// 0,5 * fqsol
		return 0.5 * $this->fQsola();
	}

	/**
	 * Berechnung von keeh.
	 *
	 * @return float
	 */
	public function keeh(): float {
		return 0; // Zum jetzigen Zeitpunkt ist keew immer 0, da wir keine Solarthermie in der Heizung haben.
	}

	/**
	 * Wärmeverlust ohne Solar.
	 *
	 * @return float
	 */
	public function Qws_ohne_solar(): float {
		return ( $this->Qws01() + $this->Qws02() ) * 1.32;
	}

	/**
	 * Wärmeverlust mit Solar.
	 *
	 * @return float
	 */
	public function Qws_mit_solar(): float {
		return $this->fbivalent() * ( 0.4 + 0.2 * ( pow( $this->Vsaux() + $this->Vssol(), 0.4 ) ) ) * pow( $this->Vsaux() / ( $this->Vsaux() + $this->Vssol() ), 2 ) * 365;
	}

	/**
	 * Vsaux0 direkt als interpolierter Wert aus Tabelle.
	 *
	 * @return float
	 */
	public function Vsaux0(): float {
		return $this->thermische_solaranlagen->vs_aux();
	}

	/**
	 * Berechnung von Vsaucx.
	 *
	 * @return float
	 */
	public function Vsaux(): float {
		$Vsaux = $this->Vsaux0();

		if ( $this->gebaeude->nutzflaeche() >= 5000 ) {
			$Vsaux = $Vsaux * ( $this->gebaeude->nutzflaeche() / 5000 );
		}

		$Vsaux *= $this->fwb();

		return $Vsaux;
	}

	/**
	 * Vssol direkt als interpolierter Wert aus Tabelle.
	 *
	 * @return float
	 */
	public function Vssol0(): float {
		return $this->thermische_solaranlagen->vs_sol();
	}

	/**
	 * Berechnung von Vssol.
	 *
	 * @return float
	 */
	public function Vssol(): float {
		$Vssol = $this->Vssol0();

		if ( $this->gebaeude->nutzflaeche() >= 5000 ) {
			$Vssol = $Vssol * ( $this->gebaeude->nutzflaeche() / 5000 );
		}

		$Vssol *= $this->fwb();

		return $Vssol;
	}

	/**
	 * Berechnung von fAc.
	 *
	 * @return float
	 */
	public function fAc(): float {
		return (new Umrechnungsfaktoren_Kollektorflaeche( $this->solarthermie_richtung, $this->solarthermie_neigung, $this->solarthermie_baujahr ))->fAc();
	}

	/**
	 * Ac0 direkt als interpolierter Wert aus Tabelle.
	 *
	 * @return float
	 */
	public function Ac0(): float {
		return $this->thermische_solaranlagen->flach_a();
	}

	/**
	 * Fläche der Solarthermiekollektoren.
	 *
	 * @return float
	 */
	public function Ac(): float {
		$Ac = $this->Ac0();

		if ( $this->gebaeude->nutzflaeche() >= 5000 ) {
			$Ac = $Ac * ( $this->gebaeude->nutzflaeche() / 5000 );
		}	

		$Ac *= $this->fwb();

		return $Ac;
	}

	/**
	 * fQsola.
	 * 
	 * @return float
	 */
	public function fQsola(): float {
		if( ! $this->solarthermie_vorhanden() ) {
			return 0;
		}
		
		return (new Umrechnungsfaktoren_Kollektorflaeche( $this->solarthermie_richtung, $this->solarthermie_neigung, $this->solarthermie_baujahr ))->fQsola();
	}

	/**
	 * Qwsola0 direkt als interpolierter Wert aus Tabelle.
	 *
	 * @return float
	 */
	public function Qwsola0(): float {
		return $this->thermische_solaranlagen->flach_q();
	}

	/**
	 * Berechnung von Qwsola.
	 *
	 * @return float
	 */
	public function Qwsola(): float {
		if( ! $this->solarthermie_vorhanden() ) {
			return 0;
		}
		
		$Qwsola = $this->Qwsola0();

		if ( $this->gebaeude->nutzflaeche() >= 5000 ) {
			$Qwsola = $Qwsola * ( $this->gebaeude->nutzflaeche() / 5000 );
		}

		$Qwsola = $Qwsola * ( $this->Ac() / $this->thermische_solaranlagen->flach_a() ) * $this->fQsola();

		return $Qwsola;
	}

	/**
	 * Volumen Speicher gesamnt in Litern.
	 *
	 * @return float
	 */
	public function Vs0(): float {
		return $this->Vs01() + $this->Vs02() + $this->Vs03();
	}

	/**
	 * Berechnung des interpolierten Volumens (Tabelle 54)
	 *
	 * @param float $nutzflaeche
	 *
	 * @return float
	 */
	public function interpoliertes_volumen( float $nutzflaeche ): float {
		$keys   = array();
		$values = array();
		if ( $nutzflaeche <= 50 ) {
			$keys   = array( 50 );
			$values = array( 78 );
		} elseif ( $nutzflaeche > 50 && $nutzflaeche <= 100 ) {
			$keys   = array( 50, 100 );
			$values = array( 78, 116 );
		} elseif ( $nutzflaeche > 100 && $nutzflaeche <= 150 ) {
			$keys   = array( 100, 150 );
			$values = array( 116, 147 );
		} elseif ( $nutzflaeche > 150 && $nutzflaeche <= 200 ) {
			$keys   = array( 150, 200 );
			$values = array( 147, 173 );
		} elseif ( $nutzflaeche > 200 && $nutzflaeche <= 300 ) {
			$keys   = array( 200, 300 );
			$values = array( 173, 219 );
		} elseif ( $nutzflaeche > 300 && $nutzflaeche <= 400 ) {
			$keys   = array( 300, 400 );
			$values = array( 219, 259 );
		} elseif ( $nutzflaeche > 400 && $nutzflaeche <= 500 ) {
			$keys   = array( 400, 500 );
			$values = array( 259, 295 );
		} elseif ( $nutzflaeche > 500 && $nutzflaeche <= 600 ) {
			$keys   = array( 500, 600 );
			$values = array( 295, 328 );
		} elseif ( $nutzflaeche > 600 && $nutzflaeche <= 700 ) {
			$keys   = array( 600, 700 );
			$values = array( 328, 359 );
		} elseif ( $nutzflaeche > 700 && $nutzflaeche <= 800 ) {
			$keys   = array( 700, 800 );
			$values = array( 359, 388 );
		} elseif ( $nutzflaeche > 800 && $nutzflaeche <= 900 ) {
			$keys   = array( 800, 900 );
			$values = array( 388, 415 );
		} elseif ( $nutzflaeche > 900 && $nutzflaeche <= 1000 ) {
			$keys   = array( 900, 1000 );
			$values = array( 415, 441 );
		} elseif ( $nutzflaeche > 1000 && $nutzflaeche <= 2000 ) {
			$keys   = array( 1000, 2000 );
			$values = array( 441, 660 );
		} elseif ( $nutzflaeche > 2000 && $nutzflaeche <= 3000 ) {
			$keys   = array( 2000, 3000 );
			$values = array( 660, 834 );
		} elseif ( $nutzflaeche > 3000 && $nutzflaeche <= 4000 ) {
			$keys   = array( 3000, 4000 );
			$values = array( 834, 986 );
		} elseif ( $nutzflaeche > 4000 && $nutzflaeche <= 5000 ) {
			$keys   = array( 4000, 5000 );
			$values = array( 986, 1122 );
		} else {
			$keys   = array( 5000 );
			$values = array( 1122 );
		}

		return interpolate_value( $nutzflaeche, $keys, $values );
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
		return ( $this->gebaeude->nutzflaeche() / $this->gebaeude->anzahl_wohnungen() ) * $this->nutzwaermebedarf_trinkwasser() * ( $this->monatsdaten->tage( $monat ) / 365 );
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
		if ( $this->heizung_im_beheizten_bereich ) {
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

	/**
	 * Bestimmung des Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen.
	 *
	 * @return float
	 */
	public function fbivalent(): float {
		return $this->gebaeude->heizsystem()->beheizt() ? 1.008 : 1.2096;
	}

	public function ewg(): float {
		//      if "Elektrodurchlauferhitzer" than  // Wird nur hydraulischer Durchlauferhitzer wird berücksichtigt (auf der sicheren Seite) // Gilt auch für Elektro-Kleinspeicher
        //           $ewg = 1.01;
        //      if "Gasdurchlauferhitzer"   Than
        //             $ewg = 1.26
        //      else??

		if( $this->zentral() ) {
			throw new Calculation_Exception( 'Bei Trinkwarmwasser aus Heizungsanlagen bitte ewg() Funktion aus Heizungsanlagen nutzen.' );
		}

		if ( $this->erzeuger() === 'dezentralelektroerhitzer' ) {
			return 1.01;
		}
		
		return 1.26;
	}

	public function Qfwges(): float {
		if( $this->zentral() ) {
			throw new Calculation_Exception( 'Bei Trinkwarmwasser aus Heizungsanlagen bitte Qfwges() Funktion aus Heizungsanlagen nutzen.' );
		}

		// (($calculations['QWB']']*$ewd)*$ewg1)
		return ( $this->QWB() * $this->ewd() ) * $this->ewg();
	}

	public function energietraeger(): string {
		if ( $this->erzeuger() === 'dezentralelektroerhitzer' ) {
			return 'strom';
		}
		
		return 'gas';
	}

	public function fp(): float {
		if( $this->zentral() ) {
			throw new Calculation_Exception( 'fp kann nur für dezentrale Trinkwasseranlagen genutzt werden.' );
		}
		
		if ( $this->erzeuger() === 'dezentralelektroerhitzer' ) {
			return  1.8;
		}
		
		return 1.1;
	}

	public function fhshid(): float {
		if( $this->zentral() ) {
			throw new Calculation_Exception( 'fhshid kann nur für dezentrale Trinkwasseranlagen genutzt werden.' );
		}

		if ( $this->erzeuger() === 'dezentralelektroerhitzer' ) {
			return  1.0;
		}
		
		return 1.11;
	}

	public function Qpges(): float {
		if( $this->zentral() ) {
			throw new Calculation_Exception( 'Qpges kann nur für dezentrale Trinkwasseranlagen berechnet werden.' );
		}

		return $this->Qfwges() * $this->fp() * $this->fhshid();
	}
}
