<?php

namespace Enev\Schema202402\Calculations\Tabellen;

use Enev\Schema202402\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202402\Calculations\Helfer\Jahr;

use function Enev\Schema202402\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';
require_once dirname( __DIR__ ) . '/Helfer/Jahr.php';

/**
 * Berechnung der Daten zur Mittleren Belastung aus Tablle 9 und 11.
 *
 * @package
 */
class Mittlere_Belastung {

	/**
	 * Gebäude.
	 *
	 * @var Gebaeude
	 */
	protected Gebaeude $gebaeude;

	/**
	 * Maximale spezifische Heizlast des Gebäudes.
	 *
	 * @var float
	 */
	protected float $h_max_spezifisch;

	/**
	 * Zeitkonstante des Gebäudes.
	 *
	 * @var float
	 */
	protected float $tau;

	/**
	 * Ist das Gebäude teilbeheizt?
	 *
	 * @var bool
	 */
	protected bool $teilbeheizung;

	/**
	 * Tabellendaten aus Tabelle 9 bei Einfamilienhaus oder Tabelle 11 bei Mehrfamilienhaus.
	 *
	 * @var array
	 */
	protected array $table_data;

	/**
	 * Constructor.
	 *
	 * @param float $h_max_spezifisch Maximale spezifische Heizlast des Gebäudes.
	 * @param bool  $teilbeheizung    Ist das Gebäude teilbeheizt? Wir rechnen im Moment immer mit teilbeheizt.
	 */
	public function __construct( float $h_max_spezifisch, bool $teilbeheizung = true ) {
		$this->h_max_spezifisch = $h_max_spezifisch;
		$this->teilbeheizung    = $teilbeheizung;
	}

	/**
	 * Gebäude.
	 *
	 * @param Gebaeude|null $gebaeude
	 * @return Gebaeude
	 */
	public function gebaeude( Gebaeude|null $gebaeude = null ): Gebaeude {
		if ( ! empty( $gebaeude ) ) {
			$this->gebaeude = $gebaeude;

			if ( $this->gebaeude->ist_einfamilienhaus() ) {
				$this->table_data = wpenon_get_table_results( 'mittlere_belastung_efh' );
			} else {
				$this->table_data = wpenon_get_table_results( 'mittlere_belastung_mfh' );
			}
		}

		return $this->gebaeude;
	}

	/**
	 * Tau slugs anhand von Tau ermitteln.
	 *
	 * Dieser wird zur Zusammensetzung der Spaltennamen zur Ermittlung der
	 * Außentemperaturabhängigen Belastung ßem1 benötigt.
	 *
	 * @return array
	 */
	protected function tau_slugs(): array {
		if ( $this->gebaeude->tau() <= 50 ) {
			return array( 't50' );
		} elseif ( $this->gebaeude->tau() > 50 && $this->gebaeude->tau() <= 90 ) {
			return array( 't50', 't90' );
		} elseif ( $this->gebaeude->tau() > 90 && $this->gebaeude->tau() < 130 ) {
			return array( 't90', 't130' );
		} else {
			return array( 't130' );
		}
	}

	/**
	 * Teilbeheizung slugs anhand von h_max_spezifisch ermitteln.
	 *
	 * Dieser wird zur Zusammensetzung der Spaltennamen zur Ermittlung der
	 * Außentemperaturabhängigen Belastung ßem1 benötigt.
	 *
	 * @return array Teilbeheizungs slug.
	 */
	protected function teilbeheizung_slugs(): array {
		if ( ! $this->teilbeheizung ) {
			return array( 'ohne' );
		}

		if ( $this->h_max_spezifisch <= 5 ) {
			return array( '5wm2' );
		} elseif ( $this->h_max_spezifisch > 5 && $this->h_max_spezifisch <= 10 ) {
			return array( '5wm2', '10wm2' );
		} elseif ( $this->h_max_spezifisch > 10 && $this->h_max_spezifisch <= 25 ) {
			return array( '10wm2', '25wm2' );
		} elseif ( $this->h_max_spezifisch > 25 && $this->h_max_spezifisch <= 50 ) {
			return array( '25wm2', '50wm2' );
		} elseif ( $this->h_max_spezifisch > 50 && $this->h_max_spezifisch <= 75 ) {
			return array( '50wm2', '75wm2' );
		} elseif ( $this->h_max_spezifisch > 75 && $this->h_max_spezifisch <= 100 ) {
			return array( '75wm2', '100wm2' );
		} elseif ( $this->h_max_spezifisch > 100 && $this->h_max_spezifisch <= 125 ) {
			return array( '100wm2', '125wm2' );
		} elseif ( $this->h_max_spezifisch > 125 && $this->h_max_spezifisch <= 150 ) {
			return array( '125wm2', '150wm2' );
		} else {
			return array( '150wm2' );
		}
	}

	/**
	 * Außentemperaturabhängige Belastung ßem1 für einen Monat ermitteln.
	 *
	 * @param string $month Slug des Monats.
	 * @return float AußentemperaturabhängigenBelastung ßem1
	 */
	public function ßem1( string $month ): float {
		if ( ! isset( $this->table_data[ $month ] ) ) {
			return 0;
		}

		$tau_keys   = array();
		$tau_values = array();

		foreach ( $this->tau_slugs() as $tau_slug ) {
			$keys = $values = array(); // Reset key and value arrays.

			foreach ( $this->teilbeheizung_slugs() as $teilbeheizung_slug ) {
				// Column name in table_data.
				$column = $tau_slug . '_' . $teilbeheizung_slug;

				$keys[]   = (int) str_replace( 'wm2', '', $teilbeheizung_slug );
				$values[] = (float) $this->table_data[ $month ]->$column;
			}

			$tau_keys[]   = (int) str_replace( 't', '', $tau_slug );
			$tau_values[] = interpolate_value( $this->h_max_spezifisch, $keys, $values ); // Interpolate h_max_spezifisch.
		}

		return interpolate_value( $this->gebaeude->tau(), $tau_keys, $tau_values );
	}

	/**
	 * Maximale Außentemperaturabhängige Belastung ßem1 aus einem Jahr ermitteln.
	 *
	 * @return float Maximale Außentemperaturabhängige Belastung ßem1.
	 */
	public function ßemMax(): float {
		$ßemMax = 0;

		$jahr = new Jahr();

		foreach ( $jahr->monate() as $monat ) {
			$ßem = $this->ßem1( $monat->slug() );

			if ( $ßem > $ßemMax ) {
				$ßemMax = $ßem;
			}
		}

		return $ßemMax;
	}
}
