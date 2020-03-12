<?php

/**
 * Class Modernizations.
 *
 * @since 1.0.0
 */
class Modernizations {
	/**
	 * Modernizations.
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	protected $modernizations = array();

	/**
	 * Modernizations constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->modernizations = array(
			'heizung'            => array(
				'bauteil'      => 'Heizung',
				'beschreibung' => 'Austausch der Heizungsanlage',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
			),
			'heizkessel'         => array(
				'bauteil'      => 'Heizkessel',
				'beschreibung' => 'Erneuerung des Heizkessels',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
			),
			'rohrleitungssystem' => array(
				'bauteil'      => 'Rohrleitungssystem',
				'beschreibung' => 'Dämmung freiliegender Heizungsrohre',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
			),
			'dach'               => array(
				'bauteil'      => 'Dach',
				'beschreibung' => 'Dämmstärken von mindestens 14 cm oder mehr',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
			),
			'decke'              => array(
				'bauteil'      => 'Oberste Geschossdecke',
				'beschreibung' => 'Dämmstärken von mindestens 14 cm oder mehr',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
			),
			'wand'               => array(
				'bauteil'      => 'Wände',
				'beschreibung' => 'Dämmstärken von mindestens 14 cm oder mehr',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
			),
			'boden'              => array(
				'bauteil'      => 'Kellerdecke',
				'beschreibung' => 'Dämmstärken von mindestens 12 cm oder mehr',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
			),
			'fenster'            => array(
				'bauteil'      => 'Fenster',
				'beschreibung' => 'Maximaler Uw - Wert bei 1,3 [W/m&sup2;K]',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
			),
			'solarthermie'       => array(
				'bauteil'      => 'Solarthermie',
				'beschreibung' => 'Solare Unterstützung für Warmwasser und Heizung',
				'gesamt'       => true,
				'einzeln'      => true,
				'amortisation' => '',
				'kosten'       => '',
			),
		);
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

	/**
	 * Check if moderinzation is alreade added.
	 *
	 * @param array $modernizations     Modernizations.
	 * @param array $modernization_info Single Modernization information.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function modernization_already_added( array $modernizations, array $modernization_info ) {
		foreach ( $modernizations as $modernization ) {
			if ( $modernization['bauteil'] === $modernization_info['bauteil'] ) {
				return true;
			}
		}

		return false;
	}
}
