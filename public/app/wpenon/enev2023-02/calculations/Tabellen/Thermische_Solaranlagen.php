<?php

namespace Enev\Schema202302\Calculations\Tabellen;

use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Kenngrößen thermischer Solaranlagen – Trinkwassererwärmung mit Zirkulation – Verteilung im beheizten Raum.
 *
 * @package
 */
class Thermische_Solaranlagen {

    /**
     * Fläche in qm.
     *
     * @var float
     */
	protected float $flaeche;

	/**
	 * Tabellendaten aus Tabelle 9 bei Einfamilienhaus oder Tabelle 11 bei Mehrfamilienhaus.
	 *
	 * @var array
	 */
	protected array $table_data;

    /**
     * Heizung im beheiztem Bereich.
     * 
     * @var bool
     */
    protected bool $heizung_im_beheizten_bereich;

	/**
	 * Konstruktor.
	 *
	 * @param float $flaeche
	 * @return void
	 */
	public function __construct( float $flaeche, bool $heizung_im_beheizten_bereich ) {
		$this->flaeche = $flaeche;
        $this->heizung_im_beheizten_bereich = $heizung_im_beheizten_bereich;
		$this->table_data = wpenon_get_table_results( 'thermische_solaranlagen' );
	}

    /**
     * Ermittelt den Wert aus der Tabelle.
     * 
     * @param string $spalte Name der Spalte.
     * @return float 
     */
	protected function ermittle_wert( string $spalte ): float {
		$flaechen_ids = $this->flaechen();
        $spalte = $this->prefix() . $spalte;

		if ( count( $flaechen_ids ) === 1 ) {
            return $this->table_data['a_' . $flaechen_ids[0]]->$spalte;
		}

        $keys = array();
        $values = array();
        foreach( $flaechen_ids AS $flaechen_id ) {
            $keys[] = $flaechen_id;
            $values[] = $this->table_data['a_' . $flaechen_id]->$spalte;
        }

        return interpolate_value( $this->flaeche, $keys, $values );
	}

    /**
     * Prefix für die Spalte.
     * 
     * @return string 
     */
    protected function prefix(): string {
        if( $this->heizung_im_beheizten_bereich ) {
            return 'beheizt_';
        }

        return 'unbeheizt_';
    }

    /**
     * Ermittelt Vs,sol.
     * 
     * @return float 
     */
	public function vs_sol(): float {
		return $this->ermittle_wert( 'vs_sol', $this->flaechen() );
	}

    /**
     * Ermittelt Vs,aux.
     * 
     * @return float 
     */
    public function vs_aux(): float {
        return $this->ermittle_wert( 'vs_aux', $this->flaechen() );
    }

    /**
     * Ermittelt Vs,ges.
     * 
     * @return float 
     */
    public function vs_ges(): float {
        return $this->ermittle_wert( 'vs_ges', $this->flaechen() );
    }

    /**
     * Ermittelt Fläche Flachkollektoren (Ac)  in qm.
     * 
     * @return float 
     */
    public function flach_a(): float {
        return $this->ermittle_wert( 'flach_a', $this->flaechen() );
    }

    /**
     * Ermittelt Energie Flachkollektoren (Qw,sol,a) in kWh/a.
     * 
     * @return float 
     */
    public function flach_q(): float {
        return $this->ermittle_wert( 'flach_q', $this->flaechen() );
    }

    /**
     * Ermittelt Fläche Röhrenkollektoren (Ac) in qm.
     * 
     * @return float 
     */
    public function roehren_a(): float {
        return $this->ermittle_wert( 'roehren_a', $this->flaechen() );
    }

    /**
     * Ermittelt Energie Röhrenkollektoren (Qw,sol,a) in kWh/a.
     * 
     * @return float 
     */
    public function roehren_q(): float {
        return $this->ermittle_wert( 'roehren_q', $this->flaechen() );
    }

	/**
	 * Zeilen anhand der Fläche ermitteln.
	 *
	 * @return array
	 */
	public function flaechen(): array {
		$flaechen = array();

		if ( $this->flaeche <= 150 ) {
			$flaechen = array( 150 );
		} elseif ( $this->flaeche > 150 && $this->flaeche <= 200 ) {
			$flaechen = array( 150, 200 );
		} elseif ( $this->flaeche > 200 && $this->flaeche <= 300 ) {
			$flaechen = array( 200, 300 );
		} elseif ( $this->flaeche > 300 && $this->flaeche <= 400 ) {
			$flaechen = array( 300, 400 );
		} elseif ( $this->flaeche > 400 && $this->flaeche <= 500 ) {
			$flaechen = array( 400, 500 );
		} elseif ( $this->flaeche > 500 && $this->flaeche <= 600 ) {
			$flaechen = array( 500, 600 );
		} elseif ( $this->flaeche > 600 && $this->flaeche <= 700 ) {
			$flaechen = array( 600, 700 );
		} elseif ( $this->flaeche > 700 && $this->flaeche <= 800 ) {
			$flaechen = array( 700, 800 );
		} elseif ( $this->flaeche > 800 && $this->flaeche <= 900 ) {
			$flaechen = array( 800, 900 );
		} elseif ( $this->flaeche > 900 && $this->flaeche <= 1000 ) {
			$flaechen = array( 900, 1000 );
		} elseif ( $this->flaeche > 1000 && $this->flaeche <= 2000 ) {
			$flaechen = array( 1000, 2000 );
		} elseif ( $this->flaeche > 2000 && $this->flaeche <= 3000 ) {
			$flaechen = array( 2000, 3000 );
		} elseif ( $this->flaeche > 3000 && $this->flaeche <= 4000 ) {
			$flaechen = array( 3000, 4000 );
		} elseif ( $this->flaeche > 4000 && $this->flaeche <= 5000 ) {
			$flaechen = array( 4000, 5000 );
		} elseif ( $this->flaeche > 5000 ) {
			$flaechen = array( 5000 );
		}

		return $flaechen;
	}
}
