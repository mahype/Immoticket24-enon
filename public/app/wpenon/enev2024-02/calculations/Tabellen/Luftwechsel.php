<?php

namespace Enev\Schema202402\Calculations\Tabellen;

use Enev\Schema202402\Calculations\Calculation_Exception;
use Enev\Schema202402\Calculations\Gebaeude\Gebaeude;

use function Enev\Schema202402\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Berechnungen zum Luftwechsel.
 *
 * @package
 */
class Luftwechsel {

	/**
	 * Gebäude.
	 *
	 * @var Gebaeude
	 */
	protected Gebaeude $gebaeude;

	/**
	 * Wärmetransferkoeffizent aller Bauteile der Gebäudehülle.
	 *
	 * @var float
	 */
	protected float $ht;

	/**
	 * Air system.
	 *
	 * @var string
	 */
	protected string $lueftungssystem;

	/**
	 * Air system demand based.
	 *
	 * @var bool
	 */
	protected bool $bedarfsgefuehrt;

	/**
	 * Category of density.
	 *
	 * @var bool
	 */
	protected string $gebaeudedichtheit;

	/**
	 * Efficiency.
	 *
	 * @var float
	 */
	protected float $wirkungsgrad;

	/**
	 * n0_small_buildings
	 * 
	 * @var float
	 */
	protected float $n0_small_buildings;

	/**
	 * n0_large_buildings
	 * 
	 * @var float
	 */
	protected float $n0_large_buildings;

	/**
	 * fwin1_small_buildings
	 * 
	 * @var float
	 */
	protected float $fwin1_small_buildings;

	/**
	 * fwin1_large_buildings
	 * 
	 * @var float
	 */
	protected float $fwin1_large_buildings;

	/**
	 * fwin2
	 * 
	 * @var float
	 */
	protected float $fwin2;

	/**
	 * h max
	 * 
	 * @var float
	 */
	protected float $h_max;

	/**
	 * Constructor.
	 *
	 * @param Gebaeude  $gebaeude          Gebäude.	 
	 * @param string    $lueftungssystem   Lüftungsyystemn (zu_abluft, abluft,ohne).
	 * @param string    $gebaeudedichtheit Kategorie der Gebäudedichtheit (din_4108_7, ohne, andere, undichtheiten).
	 * @param bool      $bedarfsgefuehrt   Ist das Lüftungssystem bedarfsgeführt?
	 * @param float|int $wirkungsgrad      Der Wirklungsgrad der wärmerückgewinnung (nur bei Zu- und Abluft)
	 */
	public function __construct(
		string $lueftungssystem,
		string $gebaeudedichtheit,
		bool $bedarfsgefuehrt = false,
		float $wirkungsgrad = 0,
	) {
		$this->lueftungssystem   = $lueftungssystem;
		$this->bedarfsgefuehrt   = $bedarfsgefuehrt;
		$this->gebaeudedichtheit = $gebaeudedichtheit;
		$this->wirkungsgrad      = $wirkungsgrad;
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
		}

		return $this->gebaeude;
	}

	/**
	 * Maximale Heizlast.
	 *
	 * @return float
	 */
	public function h_max(): float {
		if( isset( $this->h_max ) ) {
			return $this->h_max;
		}

		switch ( $this->lueftungssystem ) {
			case 'zu_abluft':
			case 'abluft':
				$this->h_max = ( $this->gebaeude()->ht_ges() + $this->hv() - 0.5 * 0.34 * $this->gebaeude()->huellvolumen_netto() * ( $this->n_wrg() - $this->n_anl() ) ) * 32;
			case 'ohne':
				$this->h_max = ( $this->gebaeude()->ht_ges() + 0.5 * $this->hv() ) * 32;
		}

		return $this->h_max;
	}

	/**
	 * Maximale spezifische Heizlast.
	 *
	 * @return float
	 */
	public function h_max_spezifisch(): float {
		return $this->h_max() / $this->gebaeude()->nutzflaeche();
	}


	public function n_wrg(): float {
		if( $this->lueftungssystem === 'ohne' ) {
			return 0;
		}

		if ( $this->gebaeude()->huellvolumen_netto() <= 1500 ) {
			return (float) $this->n_wrg_small_buildings();
		} else {
			return (float) $this->n_wrg_large_buildings();
		}
	}

	protected function n_wrg_small_buildings(): float {
		$column_name = $this->column_name( wirkungsgrad_slug:'ab_0' );
		$results     = wpenon_get_table_results( 'l_luftwechsel_klein' );
		$rate        = $results[ $this->gebaeudedichtheit ]->{$column_name};
		return $rate;
	}


	protected function n_wrg_large_buildings(): float {
		$column_name = $this->column_name( wirkungsgrad_slug:'ab_0' );

		$results = wpenon_get_table_results( 'l_luftwechsel_gross' );

		$ratio_keys = array( '02', '04', '06', '08' );
		$ratios     = array();

		foreach ( $ratio_keys as $ratio_key ) {
			$ratios[] = $results[ $this->gebaeudedichtheit . '_' . $ratio_key ]->{$column_name};
		}

		$rate = interpolate_value(
			$this->gebaeude()->ave_verhaeltnis(),
			array( 0.2, 0.4, 0.6, 0.8 ),
			$ratios
		);

		return $rate;
	}

	public function n_anl(): float {
		switch ( $this->lueftungssystem ) {
			case 'zu_abluft':
			case 'abluft':
				return $this->bedarfsgefuehrt ? 0.35 : 0.4;
			case 'ohne':
				return 0;
			default:
				throw new Calculation_Exception( sprintf( 'Ungültiges Lüftungssystem: %s.', $this->lueftungssystem ) );
		}
	}

	/**
	 * Luftechselvolumen (Hv ges = 𝑛 × 𝑐 × 𝑝 × 𝑉).
	 *
	 * @return float
	 *
	 * @throws Exception
	 */
	public function hv(): float {
		return $this->n() * 0.34 * $this->gebaeude()->huellvolumen_netto();
	}

	/**
	 *
	 *
	 * @return float
	 *
	 * @throws Exception
	 */
	public function n(): float {
		return $this->n0() * ( 1 - $this->fwin1() + $this->fwin1() * $this->fwin2() );
	}

	/**
	 * Luftwechselrate (n0).
	 *
	 * @return float
	 */
	public function n0(): float {
		if ( $this->gebaeude()->huellvolumen_netto() <= 1500 ) {
			return $this->n0_small_buildings();
		} else {
			return $this->n0_large_buildings();
		}
	}

	/**
	 * Luftwechselrate bei Gebäuden bis zu 1500m³.
	 *
	 * @return float
	 */
	protected function n0_small_buildings(): float {
		$column_name = $this->column_name();

		$results = wpenon_get_table_results( 'l_luftwechsel_klein' );
		$rate    = $results[ $this->gebaeudedichtheit ]->{$column_name};

		return $rate;
	}

	/**
	 * Luftwechselrate bei Gebäuden größer als 1500m³.
	 *
	 * @return float
	 */
	protected function n0_large_buildings(): float {
		$column_name = $this->column_name();

		$results = wpenon_get_table_results( 'l_luftwechsel_gross' );

		$ratio_keys = array( '02', '04', '06', '08' );
		$ratios     = array();

		foreach ( $ratio_keys as $ratio_key ) {
			$ratios[] = $results[ $this->gebaeudedichtheit . '_' . $ratio_key ]->{$column_name};
		}

		$rate = interpolate_value(
			$this->gebaeude()->ave_verhaeltnis(),
			array( 0.2, 0.4, 0.6, 0.8 ),
			$ratios
		);

		return $rate;
	}

	/**
	 * Korrekturfaktor fwin1.
	 *
	 * @return float
	 */
	public function fwin1(): float {
		if ( $this->gebaeude()->huellvolumen_netto() <= 1500 ) {
			return $this->fwin1_small_buildings();
		} else {
			return $this->fwin1_large_buildings();
		}
	}

	/**
	 * Korrekturfaktor bei Gebäuden bis zu 1500m³.
	 *
	 * @throws Exception
	 */
	protected function fwin1_small_buildings(): float {
		$column_name = $this->column_name();

		$results           = wpenon_get_table_results( 'l_luftwechsel_korrekturfaktor_klein' );
		$correction_factor = $results[ $this->gebaeudedichtheit ]->{$column_name};

		return $correction_factor;
	}

	/**
	 * Korrekturfaktor bei Gebäuden größer als 1500m³.
	 *
	 * @return float
	 */
	protected function fwin1_large_buildings() {
		$column_name = $this->column_name();

		$results = wpenon_get_table_results( 'l_luftwechsel_korrekturfaktor_gross' );

		$ratio_keys = array( '02', '04', '06', '08' );
		$ratios     = array();

		foreach ( $ratio_keys as $ratio_key ) {
			$ratios[] = $results[ $this->gebaeudedichtheit . '_' . $ratio_key ]->{$column_name};
		}

		$factor = interpolate_value(
			$this->gebaeude()->ave_verhaeltnis(),
			array( 0.2, 0.4, 0.6, 0.8 ),
			$ratios
		);

		return $factor;
	}

	/**
	 * Spalte in der Tabelle.
	 *
	 * @return string
	 * @throws Exception
	 */
	protected function column_name( string $wirkungsgrad_slug = null ) {
		switch ( $this->lueftungssystem ) {
			case 'zu_abluft':
				$column_name = 'zu_abluft';
				break;
			case 'abluft':
				$column_name = 'abluft';
				break;
			case 'ohne':
				return 'ohne';
				break;
			default:
				throw new Calculation_Exception( sprintf( 'Invalid air system: %s.', $this->lueftungssystem ) );
		}
		
		if( $this->gebaeudedichtheit !== 'din_4108_7' ) { // Wurde kein Blower Door Test durchgeführt, so wird immer der Wert aus "ohne mechanische Lüftung" verwendet.
			return 'ohne';
		}

		if ( $this->bedarfsgefuehrt ) {
			$column_name .= '_bedarfsgefuehrt';
		} else {
			$column_name .= '_nichtbedarfsgefuehrt';
		}

		if ( $this->lueftungssystem === 'abluft' ) {
			return $column_name;
		}

		if ( $wirkungsgrad_slug !== null ) {
			return $column_name . '_' . $wirkungsgrad_slug;
		}

		if ( $this->wirkungsgrad < 60 ) {
			$column_name .= '_ab_0';
		} elseif ( $this->wirkungsgrad < 80 ) {
			$column_name .= '_ab_60';
		} elseif ( $this->wirkungsgrad <= 100 ) {
			$column_name .= '_ab_80';
		} else {
			throw new Calculation_Exception( 'Invalid wirkungsgrad.' );
		}

		return $column_name;
	}

	/**
	 * Saisinaler Korrekturfaktor fwin2.
	 *
	 * @return float
	 */
	public function fwin2(): float {
		if ( $this->gebaeude()->baujahr() <= 2002 ) {
			return 1.066;
		}

		return 0.979;
	}
}
