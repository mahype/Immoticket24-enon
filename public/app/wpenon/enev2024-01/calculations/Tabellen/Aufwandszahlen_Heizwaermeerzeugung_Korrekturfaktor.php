<?php

namespace Enev\Schema202401\Calculations\Tabellen;

use Heizungsanlage;

use function Enev\Schema202401\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Berechnung der Daten zur Mittleren Belastung aus Tablle 78.
 *
 * @package
 */
class Aufwandszahlen_Heizwaermeerzeugung_Korrekturfaktor {
	/**
	 * Erzeuger.
	 *
	 * @var string
	 */
	protected string $erzeuger;

	/**
	 * Energieträger.
	 *
	 * @var string
	 */
	protected string $energietraeger;

	/**
	 * Baujahr.
	 *
	 * @var int
	 */
	protected int $baujahr;

	/**
	 * Zeilwert Spalte.
	 *
	 * @var float
	 */
	protected float $spalte_zielwert;

	/**
	 * Heizung im beheizten Bereich.
	 *
	 * @var bool
	 */
	protected bool $heizung_im_beheizten_bereich;

	/**
	 * Tabellendaten aus Tabelle 79, 80 und 81.
	 *
	 * @var array
	 */
	protected array $table_data;

	/**
	 * Konstruktor.
	 *
	 * @param string $heizung Typ der Heizung (standardkessel, niedertemperaturkessel, brennwertkessel, kleinthermeniedertemperatur, kleinthermebrennwert, pelletfeuerung, gasraumheizer, oelofenverdampfungsverbrenner).
	 * @param string $energietraeger Energieträger (heizoel, erdgas, fluessiggas, biogas, holzpellets, holzhackschnitzel)
	 * @param float  $ßhg ßhg.
	 *
	 * @return void
	 */
	public function __construct( string $erzeuger, string $energietraeger, int $baujahr, float $ßhg ) {
		$this->erzeuger        = $erzeuger;
		$this->energietraeger  = $energietraeger;
		$this->baujahr         = $baujahr;
		$this->spalte_zielwert = $ßhg;
		$this->table_data      = wpenon_get_table_results( 'aufwandszahlen_heizwaermeerzeugung_korrektur' );
	}

    public function f_temp(): float {
        return $this->interpolierter_wert();
    }

    protected function interpolierter_wert(): float {
        $spalten_keys   = array();
        $spalten_values = array();

        foreach ( $this->spalten() as $spalte ) {
            $spalten_keys[]   = $spalte;
            $spalten_teile  = explode( '.', $spalte );

			if( !isset( $spalten_teile[1] ) ) {
				$spalten_teile[1] = 0;
			}

            $spalten_name = 'bhg_' . $spalten_teile[0] . '_' . $spalten_teile[1];
            $zeilen_name = $this->zeile();
            $spalten_values[] = floatval( $this->table_data[ $zeilen_name ]->$spalten_name );
        }

        return interpolate_value( $this->spalte_zielwert, $spalten_keys, $spalten_values );
    }

	protected function spalten(): array {
		if ( $this->spalte_zielwert <= 0.1 ) {
			return array( 0.1 );
		} elseif ( $this->spalte_zielwert > 0.1 && $this->spalte_zielwert <= 0.2 ) {
			return array( 0.1, 0.2 );
		} elseif ( $this->spalte_zielwert > 0.2 && $this->spalte_zielwert <= 0.3 ) {
			return array( 0.2, 0.3 );
		} elseif ( $this->spalte_zielwert > 0.3 && $this->spalte_zielwert <= 0.4 ) {
			return array( 0.3, 0.4 );
		} elseif ( $this->spalte_zielwert > 0.4 && $this->spalte_zielwert <= 0.5 ) {
			return array( 0.4, 0.5 );
		} elseif ( $this->spalte_zielwert > 0.5 && $this->spalte_zielwert <= 0.6 ) {
			return array( 0.5, 0.6 );
		} elseif ( $this->spalte_zielwert > 0.6 && $this->spalte_zielwert <= 0.7 ) {
			return array( 0.6, 0.7 );
		} elseif ( $this->spalte_zielwert > 0.7 && $this->spalte_zielwert <= 0.8 ) {
			return array( 0.7, 0.8 );
		} elseif ( $this->spalte_zielwert > 0.8 && $this->spalte_zielwert <= 0.9 ) {
			return array( 0.8, 0.9 );
		} elseif ( $this->spalte_zielwert > 0.9 && $this->spalte_zielwert <= 1.0 ) {
			return array( 0.9, 1.0 );
		} else {
			return array( 1.0 );
		}
	}

	protected function zeile(): string {
		return $this->erzeuger . '_' . $this->energietraeger_slug() . '_'. $this->jahr_slug();
	}


	protected function jahr_slug(): string {
		if ( $this->erzeuger === 'standardkessel' ) {
			return $this->standardkessel_jahr_slug();
		}

		if ( $this->erzeuger === 'niedertemperaturkessel' ) {
			return $this->niedertemperaturkessel_jahr_slug();
		}

		if ( $this->erzeuger === 'brennwertkessel' ) {
			return $this->brennwertkessel_jahr_slug();
		}
	}

	protected function standardkessel_jahr_slug(): string {
		if ( $this->energietraeger === 'holzpellets' || $this->energietraeger === 'holzhackschnitzel' || $this->energietraeger === 'stueckholz') {
			return 'bis_heute';
		}

		if ( $this->baujahr <= 1986 ) {
			return 'bis_1986';
		}

		if ( $this->baujahr <= 1994 ) {
			return 'bis_1994';
		}

		return 'bis_heute';
	}

    protected function energietraeger_slug(): string {
        switch( $this->energietraeger ) {
            case 'heizoel':
            case 'erdgas':
            case 'fluessiggas':
            case 'biogas':
                return 'gas_und_heizoel';
            case 'holzpellets':
            case 'holzhackschnitzel':
            case 'stueckholz':
                return 'holz';
            case 'braunkohle':
            case 'steinkohle':
                return 'feststoff';
            default:
                throw new \Exception( 'Unbekannter Energieträger: ' . $this->energietraeger );
        }
}

	protected function niedertemperaturkessel_jahr_slug(): string {
		if ( $this->baujahr <= 1986 ) {
			return 'bis_1986';
		}

		if ( $this->baujahr <= 1994 ) {
			return 'bis_1994';
		}

		return 'bis_heute';
	}

	protected function brennwertkessel_jahr_slug(): string {
		if ( $this->energietraeger === 'holzpellets' || $this->energietraeger === 'holzhackschnitzel' || $this->energietraeger === 'stueckholz' ) {
			return 'bis_heute';
		}

		if ( $this->baujahr <= 1986 ) {
			return 'bis_1986';
		}

		if ( $this->baujahr <= 1994 ) {
			return 'bis_1994';
		}

		if ( $this->baujahr <= 1999 ) {
			return 'bis_1999';
		}

		return 'bis_heute';
	}
}
