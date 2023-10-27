<?php

namespace Enev\Schema202302\Calculations\Gebaeude;

use Enev\Schema202302\Calculations\Helfer\Jahr;

require_once __DIR__ . '/Bauteile.php';
require_once __DIR__ . '/Heizsystem.php';
require_once __DIR__ . '/Wasserversorgung.php';
require_once __DIR__ . '/Luftwechsel.php';
require_once __DIR__ . '/Mittlere_Belastung.php';
require_once __DIR__ . '/Bilanz_Innentemperatur.php';

require_once dirname( __DIR__ ) . '/Helfer/Jahr.php';


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
	private $anzahl_wohneinheiten;

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
	private Mittlere_Belastung $mittlere_Belastung;

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
	private Anbau $anbau;

	/**
	 * Wirksame Wärmespeicherkapazität in Abhängigkeit der Gebäudeschwere.
	 *
	 * @var int
	 */
	private int $c_wirk;

	public function __construct( int $baujahr, int $geschossanzahl, float $geschosshoehe, int $anzahl_wohneinheiten, Grundriss $grundriss ) {
		$this->jahr                 = new Jahr();
		$this->baujahr              = $baujahr;
		$this->geschossanzahl       = $geschossanzahl;
		$this->geschosshoehe        = $geschosshoehe;
		$this->anzahl_wohneinheiten = $anzahl_wohneinheiten;
		$this->grundriss            = $grundriss;

		$this->c_wirk = 50; // Für den vereinfachten Rechenweg festgelegt auf den Wert 50.

		$this->bauteile   = new Bauteile();
		$this->heizsystem = new Heizsystem();
	}

	/**
	 * Bauteile
	 *
	 * @return Bauteile
	 */
	public function _bauteile(): Bauteile {
		return $this->bauteile;
	}

	/**
	 * Heizsystem.
	 *
	 * @return Heizsystem
	 */
	public function _heizsystem(): Heizsystem {
		return $this->heizsystem;
	}

	/**
	 * Wasserversorgung.
	 *
	 * @param Wasserversorgung
	 *
	 * @return Wasserversorgung
	 */
	public function _wasserversorgung( Wasserversorgung $wasserversorgung = null ): Wasserversorgung {
		if ( $wasserversorgung !== null ) {
			$this->wasserversorgung = $wasserversorgung;
		}

		if ( $this->wasserversorgung === null ) {
			throw new Exception( 'Wasserversorgung wurde nicht gesetzt.' );
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
	public function _luftwechsel( Luftwechsel|null $luftwechsel = null ): Luftwechsel {
		if ( ! empty( $luftwechsel ) ) {
			$this->luftwechsel = $luftwechsel;
			$this->luftwechsel->gebaeude( $this );
		}

		return $this->luftwechsel;
	}

	protected function _mittlere_belastung(): Mittlere_Belastung {
		if ( empty( $this->mittlere_Belastung ) ) {
			$this->mittlere_Belastung = new Mittlere_Belastung( $this->_luftwechsel()->h_max_spezifisch() ); // Mittlere Belastung wird immer mit Teilbeheizung gerechnet
			$this->mittlere_Belastung->gebaeude( $this );
		}

		return $this->mittlere_Belastung;
	}


	protected function _bilanz_innentemperatur(): Bilanz_Innentemperatur {
		if ( empty( $this->bilanz_innentemperatur ) ) {
			$this->bilanz_innentemperatur = new Bilanz_Innentemperatur( $this->_luftwechsel()->h_max_spezifisch() );
			$this->bilanz_innentemperatur->gebaeude( $this );
		}

		return $this->bilanz_innentemperatur;
	}

	/**
	 * Grundriss.
	 *
	 * @return Grundriss
	 */
	public function _grundriss(): Grundriss {
		return $this->grundriss;
	}

	/**
	 * Anbau.
	 *
	 * @param Anbau|null Anbau object oder null, sofern bereits angegeben.
	 */
	public function _anbau( Anbau|null $anbau = null ): Anbau {
		if ( ! empty( $anbau ) ) {
			$this->anbau = $anbau;
		}

		if ( empty( $this->anbau ) ) {
			throw new Exception( 'Anbau wurde nicht gesetzt.' );
		}

		return $this->anbau;
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
	public function anzahl_wohneinheiten(): int {
		return $this->anzahl_wohneinheiten;
	}

	/**
	 * Hüllfläche des Gebäudes.
	 *
	 * @return float
	 */
	public function huellflaeche(): float {
		return $this->_bauteile()->flaeche();
	}

	/**
	 * Hüllvolumen des Gebäudes.
	 *
	 * @return float
	 */
	public function huellvolumen(): float {
		$volumen = 0;

		// Volumen der Geschosse
		$volumen += $this->grundriss->flaeche() * $this->geschossanzahl() * $this->geschosshoehe();

		// Volumen des Anbaus
		if ( $this->anbau !== null ) {
			$volumen += $this->anbau->volumen();
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
		return $this->_bauteile()->ht() + $this->_luftwechsel()->hv();
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
		return $this->q() * ( ( $this->_bilanz_innentemperatur()->θih_monat( $monat ) + 12 ) / 32 ) * $this->_mittlere_belastung()->ßem1( $monat );
	}

	public function psh_sink_monat( string $monat ) {
		// $calculations['monate'][ $monat ]['psh_sink'] = $gebaeude->ph_sink_monat( $monat) - ($gebaeude->qi_prozesse_monat( $monat ) + ( 0.5 * $calculations['monate'][ $monat ]['qi_solar'] ) * $fum);
		// $calculations['monate'][ $monat ]['psh_sink'] = $calculations['monate'][ $monat ]['psh_sink'] < 0 ? 0 : $calculations['monate'][ $monat ]['psh_sink'];
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
		if ( $this->anzahl_wohneinheiten() === 1 ) {
			// Einfamlienhaus
			return 45 * $this->nutzflaeche() * $this->jahr->monat( $monat )->tage() * 0.001;
		} else {
			// Mehrfamilienhaus
			return ( 90.0 * $this->nutzflaeche() / ( $this->anzahl_wohneinheiten() * $this->jahr->monat( $monat )->tage() ) ) * 0.001;
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
		return $this->qwb_monat( $monat ) * $this->_wasserversorgung()->fh_w();
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
		return $this->geschossanzahl < 4 ? 0.76 * $this->_bauteile()->huellvolumen() : 0.8 * $this->_bauteile()->huellvolumen();
	}

	/**
	 * Verhältnis von Hüllfläche zu Netto Hüllvolumen.
	 *
	 * @return float
	 */
	public function ave_verhaeltnis(): float {
		return $this->_bauteile()->huellflaeche() / $this->huellvolumen_netto();
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
	function nutzwaermebedarf_trinkwasser(): float {
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
	 * Bilanz Innentemperatur für einen Monat.
	 *
	 * @param  string $monat
	 * @return float
	 * @throws Exception
	 */
	public function θih_monat( string $monat ): float {
		return $this->_bilanz_innentemperatur()->θih_monat( $monat );
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
		return ( $this->nutzflaeche() / $this->anzahl_wohneinheiten() ) * $qwb * ( $this->jahr->monat( $monat )->tage() / 365 );
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
