<?php

namespace Enev\Schema202401\Modernizations;

use WPENON\Model\Energieausweis;

/**
 * Abstract Class Modernizations.
 *
 * @since 1.0.0
 */
abstract class Modernizations {
	/**
	 * Modernizations.
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	protected $modernizations = array();

	/**
	 * Energy certificate
	 *
	 * @var \WPENON\Model\Energieausweis
	 *
	 * @since 1.0.0
	 */
	protected $energieausweis;

	/**
	 * VW_Modernizations constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->load_data();
		add_filter( 'enon_filter_modernization_recommendations', array( $this, 'get_modernizations' ), 10, 2 );
	}

	/**
	 * Modernizations constructor.
	 *
	 * @since 1.0.0
	 */
	public function load_data() {
		$this->modernizations = array(
			'heizung'            => array(
				'bauteil'      => 'Heizung',
				'beschreibung' => 'Austausch der Heizungsanlage',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
				'dibt_value'   => 'Heizung',
			),
			'heizkessel'         => array(
				'bauteil'      => 'Heizkessel',
				'beschreibung' => 'Erneuerung des Heizkessels',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
				'dibt_value'   => 'Heizung',
			),
			'rohrleitungssystem' => array(
				'bauteil'      => 'Rohrleitungssystem',
				'beschreibung' => 'Dämmung freiliegender Heizungsrohre',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
				'dibt_value'   => 'Wärmeverteilung / -abgabe',
			),
			'dach'               => array(
				'bauteil'      => 'Dach',
				'beschreibung' => 'Dämmstärken von mindestens 14 cm oder mehr',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
				'dibt_value'   => 'Dach',
			),
			'decke'              => array(
				'bauteil'      => 'Oberste Geschossdecke',
				'beschreibung' => 'Dämmstärken von mindestens 14 cm oder mehr',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
				'dibt_value'   => 'oberste Geschossdecke',
			),
			'wand'               => array(
				'bauteil'      => 'Wände',
				'beschreibung' => 'Dämmstärken von mindestens 14 cm oder mehr',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
				'dibt_value'   => 'Außenwand gg. Erdreich',
			),
			'boden'              => array(
				'bauteil'      => 'Kellerdecke',
				'beschreibung' => 'Dämmstärken von mindestens 12 cm oder mehr',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
				'dibt_value'   => 'Kellerdecke',
			),
			'fenster'            => array(
				'bauteil'      => 'Fenster',
				'beschreibung' => 'Maximaler Uw - Wert bei 1,3 [W/m&sup2;K]',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
				'dibt_value'   => 'Fenster',
			),
			'solarthermie'       => array(
				'bauteil'      => 'Solarthermie',
				'beschreibung' => 'Solare Unterstützung für Warmwasser und Heizung',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
				'dibt_value'   => 'Sonstiges',
			),
		);
	}

	/**
	 * Add modernizations.
	 *
	 * @param array                        $modernizations Mordernization recommendations.
	 * @param \WPENON\Model\Energieausweis $energieausweis Energy certificate object.
	 *
	 * @return array Filtered modernization recommendations.
	 *
	 * @since 1.0.0
	 */
	public function get_modernizations( array $modernizations, \WPENON\Model\Energieausweis $energieausweis ): array {
		$this->energieausweis = $energieausweis;

		// Remove modernizations which are checked afterwards. // Todo: Have to leave
		$slugs_to_remove = [ 'wand', 'decke', 'boden', 'dach', 'rohrleitungssystem', 'solarthermie', 'heizung', 'fenster' ];
		$modernizations_old  = $this->remove_modernizations( $modernizations, $slugs_to_remove );

		$modernizations = array();
		foreach( $modernizations_old as $modernization_old ) {
			$modernizations[] = $modernization_old;
		}

		// Checking for modernizations.
		if ( $this->needs_heater() && $this->is_recommendation_active( 'heizung', $this->energieausweis ) ) {
			$modernizations[] = $this->get_modernization( 'heizung' );
		}

		if ( $this->needs_rohrleitungssystem() && $this->is_recommendation_active( 'rohrleitungssystem', $this->energieausweis ) ) {
			$modernizations[] = $this->get_modernization( 'rohrleitungssystem' );
		}

		if ( $this->needs_solarthermie() && $this->is_recommendation_active( 'solarthermie', $this->energieausweis ) ) {
			$modernizations[] = $this->get_modernization( 'solarthermie' );
		}

		if ( $this->needs_wand() && $this->is_recommendation_active( 'wand', $this->energieausweis ) ) {
			$modernizations[] = $this->get_modernization( 'wand' );
		}

		if ( $this->needs_boden() && $this->is_recommendation_active( 'boden', $this->energieausweis ) ) {
			$modernizations[] = $this->get_modernization( 'boden' );
		}

		if ( $this->needs_decke() && $this->is_recommendation_active( 'decke', $this->energieausweis ) ) {
			$modernizations[] = $this->get_modernization( 'decke' );
		}

		if ( $this->needs_dach() && $this->is_recommendation_active( 'dach', $this->energieausweis ) ) {
			$modernizations[] = $this->get_modernization( 'dach' );
		}

		if ( $this->needs_windows() && $this->is_recommendation_active( 'fenster', $this->energieausweis ) ) {
		 	$modernizations[] = $this->get_modernization( 'fenster' );
		}

		return $modernizations;
	}

	/**
	 * Needs wand.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	abstract protected function needs_wand();

	/**
	 * Needs decke.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	abstract protected function needs_decke();

	/**
	 * Needs boden.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	abstract protected function needs_boden();

	/**
	 * Needs dach.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	abstract protected function needs_dach();

	/**
	 * Needs windows.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	abstract protected function needs_windows();

	/**
	 * Needs rohleitungssystem.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	abstract protected function needs_rohrleitungssystem();

	/**
	 * Checks window data for recommendation.
	 *
	 * @param int    $baujahr Year of built.
	 * @param string $bauart  Type of built.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function check_window( $baujahr, $bauart ) {
		if ( intval( $baujahr ) > 1994 ) {
			return false;
		}

		if ( in_array( $bauart, array( 'waermedaemmglas', 'waermedaemmglas2fach' ) ) ) {
			return false;
		}

		// Todo: Not reachable becuase of construction year!
		if ( in_array( $bauart, array( 'aluminium', 'kunststoff', 'stahl' ) ) && $baujahr >= 2005 ) {
			return false;
		}

		return true;
	}

	/**
	 * Needs solarthermie.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function needs_solarthermie() {
		if ( $this->energieausweis->regenerativ_art !== 'keine' ) {
			return false;
		}

		$age_heater = date( 'Y' ) - (int) $this->energieausweis->h_baujahr;

		switch ( $this->energieausweis->h_erzeugung ) {
			case 'kleinthermeniedertemperatur':
			case 'kleinthermebrennwert':
				return false;
			case 'fernwaerme':
			case 'oelofenverdampfungsbrenner':
			case 'kohleholzofen':
			case 'gasraumheizer':
			case 'elektronachtspeicherheizung':
			case 'elektrodirektheizgeraet':
				if ( $age_heater < 25 ) {
					return false;
				}
				return true;
			default:
				return true;
		}
	}

	/**
	 * Needs heater.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function needs_heater() {
		$heatings = array( 'h' );

		$min_age = 30;

		if ( isset( $this->energieausweis->h2_info ) && $this->energieausweis->h2_info ) {
			$heatings[] = 'h2';

			if ( isset( $this->energieausweis->h3_info ) && $this->energieausweis->h3_info ) {
				$heatings[] = 'h3';
			}
		}

		$current_year = absint( current_time( 'Y' ) );

		$types       = array(
			'gasraumheizer',
			'elektrodirektheizgeraet',
			'elektronachtspeicherheizung',
			'oelofenverdampfungsbrenner',
			'kohleholzofen',
		);

		foreach ( $heatings as $heating ) {
			$type_field = $heating . '_erzeugung';
			$year_field = $heating . '_baujahr';

			if ( in_array( $this->energieausweis->$type_field, $types, true ) && ! empty( $this->energieausweis->$year_field ) && $this->energieausweis->$year_field <= $current_year - $min_age ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get modernization recommendation.
	 *
	 * @param string $type Modernization type.
	 *
	 * @return bool|array Modernization information.
	 *
	 * @since 1.0.0
	 */
	protected function get_modernization( $type ) {
		if ( array_key_exists( $type, $this->modernizations ) ) {
			return $this->modernizations[ $type ];
		}

		return false;
	}

	/**
	 * Removing modernizations.
	 *
	 * @param array $modernizations  Modernizations.
	 * @param array $slugs_to_remove Modernization slugs to remove.
	 *
	 * @return array Filtered modernization slugs.
	 *
	 * @since 1.0.0
	 */
	public function remove_modernizations( array $modernizations, array $slugs_to_remove ) {
		$count_modernizations = count( $modernizations );
		for ( $key = 0; $key < $count_modernizations; $key++ ) {
			$slug = $this->get_slug_by_bauteil( $modernizations[ $key ]['bauteil'] );

			if ( ! $slug ) {
				// Should be logged.
				continue;
			}

			if ( in_array( $slug, $slugs_to_remove ) ) {
				unset( $modernizations[ $key ] );
			}
		}

		return $modernizations;
	}

	/**
	 * Is recommendation active.
	 *
	 * @param string         $modernization  Slug of modernization.
	 * @param Energieausweis $energieausweis Energieausweis object.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function is_recommendation_active( string $modernization, Energieausweis $energieausweis ) {
		return ! get_post_meta( $energieausweis->id, 'wpenon_immoticket24_disable_empfehlung_' . $modernization, true );
	}

	/**
	 * Get slug by bauteil.
	 *
	 * @param string $bauteil Bauteil name.
	 *
	 * @return bool|string Modernization slug. False if not found.
	 *
	 * @since 1.0.0
	 */
	public function get_slug_by_bauteil( $bauteil ) {
		foreach ( $this->modernizations as $slug => $modernization ) {
			if ( $bauteil === $modernization['bauteil'] ) {
				return $slug;
			}
		}

		return false;
	}
}
