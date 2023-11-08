<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202302\Calculations\Tabellen\Mittlere_Belastung_Korrekturfaktor;


/**
 * Berechnung eines Übergabeystems (Heizkörper).
 */
class Uebergabesystem {
	/**
	 * Gebäude.
	 *
	 * @var Gebaeude
	 */
	protected Gebaeude $gebaeude;

	/**
	 * Erlaubte Typen.
	 *
	 * @var string[]
	 */
	protected $typen = array(
		'elektroheizungsflaechen',
		'heizkoerper',
		'flaechenheizung',
	);

	/**
	 * Erlaubte Auslegungstemperaturen.
	 *
	 * @var string[]
	 */
	protected $erlaubte_auslegungstemperaturen = array(
		'90/70',
		'70/55',
		'55/45',
		'35/28',
	);

	/**
	 * Typ des Übergabesystems (elektroheizungsflaechen, heizkoerper oder flaechenheizung).
	 */
	protected $typ;

	/**
	 * Auslegungstemperaturen.
	 *
	 * @var string
	 */
	protected string $auslegungstemperaturen;

	/**
	 * Prozentualer Anteil der Heizungsanlage im Heizsystem
	 *
	 * @var int
	 */
	protected int $prozentualer_anteil;

	/**
	 * Mindestämmung für Flächenheizungen vorhanden?
	 *
	 * @var bool
	 */
	protected bool $mindestdaemmung;

	/**
	 * Konstruktor.
	 *
	 * @param Gebaeude $gebaeude               Gebäude.
	 * @param string   $typ                    Typ des Übergabesystems (elektroheizungsflaechen, heizkoerper, fussbodenheizung, wandheizung, deckenheizung).
	 * @param string   $auslegungstemperaturen Auslegungstemperaturen der Heizungsanlage. Mögliche Werte: ' 90/70', '70/55', '55/45' oder '35/28'.
	 * @param int      $anzahl_wohnungen       Anzahl der Wohnungen.
	 * @param bool     $heizungsanlage_beheizt Ist die Heizungsanlage beheizt? Wenn der Kunde dies nicht weis, dann nein (false).
	 * @param string   $prozentualer_anteil    Prozentualer Anteil des Übergabesystems im Heizsystem.
	 * @param bool     $mindestdaemmung        Ist die Mindestdämmung vorhanden? Wenn der Kunde dies nicht weis, dann nein (false). Wird nur bei flaechenheizungen benötigt.
	 */
	public function __construct(
		Gebaeude $gebaeude,
		string $typ,
		string $auslegungstemperaturen,
		int $prozentualer_anteil = 100,
		bool $mindestdaemmung = false
	) {
		// Check der Übergabe-Typen
		if ( ! in_array( $typ, $this->typen ) ) {
			throw new Calculation_Exception( 'Typ des Übergabesystems nicht bekannt.' );
		}

		// Check der Auslegungstemperaturen.
		if ( ! in_array( $auslegungstemperaturen, $this->erlaubte_auslegungstemperaturen ) ) {
			throw new Calculation_Exception( 'Auslegungstemperaturen nicht bekannt.' );
		}

		$this->gebaeude               = $gebaeude;
		$this->typ                    = $typ;
		$this->auslegungstemperaturen = $auslegungstemperaturen;
		$this->prozentualer_anteil    = $prozentualer_anteil;
		$this->mindestdaemmung        = $mindestdaemmung;
	}

	/**
	 * Typ.
	 *
	 * @return string
	 */
	public function typ(): string {
		return $this->typ;
	}

	/**
	 * Auslegungstemperaturen.
	 *
	 * @return string
	 */
	public function auslegungstemperaturen(): string {
		return $this->auslegungstemperaturen;
	}

	/**
	 * Auslegungsvorlauftemperatur.
	 *
	 * @return int
	 */
	public function auslegungsvorlauftemperatur(): int {
		$temperaturen = explode( '/', $this->auslegungstemperaturen() );
		return (int) $temperaturen[0];
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
	 * Aufwandszahl für freie Heizflächen.
	 *
	 * @return float
	 */
	public function ehce(): float {
		$ehce = 0;

		switch ( $this->typ ) {
			case 'elektroheizungsflaechen':
				$ehce += 1.066; // Es wird immer "Elektro-Direktheizung mit Raum-Regelung" angenommen. ehce 0
				$ehce += 0.018; // Immeririerender betrieb ist bei Elektroheizungsflächen immer  0,018
				// Alle anderen Werte sind immer 0.

				return 1.066;
			case 'heizkoerper':
				// Raumtemperaturregelung_ Es wird immer "P-Regler" angenommen.
				$ehce = 1.042; // ehce 0

				// Übertemperatur. Es wird vom Zweirohrsystem ausgegangen ehce 1
				switch ( $this->auslegungstemperaturen ) {
					case '90/70':
						$ehce += 1.042;
						break;
					case '70/55':
						$ehce += 0.021;
						break;
					case '55/45':
						$ehce += 0.015;
						break;
					case '35/28':
						$ehce += 0.012;
						break;
				}

				// Spezigische Wärmeverluste - ehce 2
				// Es wird immer angenommen dass der Heizkoerper
				// an einer aussenliegenden Wand montiert ist.
				$ehce += 0.009;

				// Immeririerender betrieb - ehce 3 - Es wird immer von "Regler" ausgegangen. Ergebnis +0,000.

				// Erhöhte Strahlung - ehce 4 - Ist aufgrund von Wert in Tabelle ist der Wert immer +0.000.

				// Einzelraumregelung - ehce 5 - Es wird immer von "eigenständig" ausgegangen.
				$ehce += 0.030;

				// Da immer vom "Zweirohrsystem" ausgegangen wird, kommt ehcehyd mit 0.036 hinzu.
				$ehce += 0.036;

				return $ehce;
			// Flächenheizungen
			case 'flaechenheizung':
				// Raumtemperaturregelung_ Es wird immer "P-Regler" angenommen.
				$ehce = 1.042; // ehce 0

				switch ( $this->typ ) {
					case 'fussbodenheizung':
						// Fussbodenheizung - ehce 1
						$ehce += 0.021;
						break;
					case 'wandheizung':
						// Wandheizung - ehce 1
						$ehce += 0.045;
						break;
					case 'deckenheizung':
						// Deckenheizung - ehce 1
						$ehce += 0.063;
						break;
				}

				if ( $this->mindestdaemmung ) {
					// Mindestdämmung vorhanden - ehce 2
					$ehce += 0.015;
				} else {
					// Mindestdämmung nicht vorhanden - ehce 2
					$ehce += 0.042;
				}

				// ehce3 = 0.0 Tab 22 18599 T12, intermetierender Betrieb Heizungebetrieb, da hier von uns der schlechtere Wert angesetzt wird
				// ehce4 = 0.0 Tab 22 18599 T12
				// ehce5 = -0.03,  Tab 22 18599 T12, hier wird nach "Smart Home-Lösungen" gefragt. Das Optimierungspotzenzial,welches die DIN vorgibt ist sehr groß... Abfrage aber komplex für das Verstädnis des Kunden. Wir können aber ehce 5 = -0,030 setzen für manuele Betätigung der Einzelraumregelsystem ohne weitere Nachfrage.
				$ehce += -0.030;

				// Da immer vom "Zweirohrsystem" ausgegangen wird, kommt ehcehyd mit 0.036 hinzu.
				$ehce += 0.036;
		}

		return $ehce;
	}

	/**
	 * Flächenbezogene leistung der Übergabe der Heizung Qhce.
	 *
	 * @param float $h_max_spezifisch
	 * @return float
	 */
	public function qhce(): float {
		return $this->gebaeude->luftwechsel()->h_max_spezifisch() * $this->ehce();
	}	

	/**
	 * Verteilung Heizung (ehd0).
	 *
	 * @return float|void
	 */
	public function ehd0( Heizungsanlage $heizungsanlage, int $anzahl_wohnungen ): float {
		if ( $anzahl_wohnungen === 1 ) {
			switch ( $this->auslegungstemperaturen ) {
				case '90/70':
					return $heizungsanlage->beheizung_anlage() === 'alles' ? 1.099 : 1.1;
				case '70/55':
					return $heizungsanlage->beheizung_anlage() === 'alles' ? 1.070 : 1.074;
				case '55/45':
					return $heizungsanlage->beheizung_anlage() === 'alles' ? 1.049 : 1.055;
				case '35/28':
					return $heizungsanlage->beheizung_anlage() === 'alles' ? 1.019 : 1.028;
			}
		}

		// Mehrfamilienhaus
		switch ( $this->auslegungstemperaturen ) {
			case '90/70':
				return $heizungsanlage->beheizung_anlage() === 'alles' ? 1.085 : 1.085;
			case '70/55':
				return $heizungsanlage->beheizung_anlage() === 'alles' ? 1.060 : 1.063;
			case '55/45':
				return $heizungsanlage->beheizung_anlage() === 'alles' ? 1.042 : 1.047;
			case '35/28':
				return $heizungsanlage->beheizung_anlage() === 'alles' ? 1.016 : 1.024;
		}
	}	
}
