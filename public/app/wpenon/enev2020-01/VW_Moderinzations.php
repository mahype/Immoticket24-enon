<?php

require_once ( dirname( __FILE__ ) . '/Modernizations.php' );

/**
 * Class VW_Modernizations.
 *
 * @since 1.0.0
 */
class VW_Modernizations extends Modernizations {
	/**
	 * VW_Modernizations constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();

		add_filter( 'enon_filter_modernization_recommendations', array( $this, 'add_modernizations' ), 10, 2 );
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
	public function add_modernizations( array $modernizations, \WPENON\Model\Energieausweis $energieausweis ) : array {
		// Remove modernizations which are checked afterwards.
		$slugs_to_remove = [ 'wand', 'decke', 'boden', 'dach', 'rohrleitungssystem' ];
		$modernizations = $this->remove_modernizations( $modernizations, $slugs_to_remove );

		// Checking for modernizations.
		if ( 1995 > $energieausweis->baujahr && 'no' === $energieausweis->wand_daemmung_on ) {
			$modernization = $this->get_modernization( 'wand' );
			if ( ! $this->modernization_already_added( $modernizations, $modernization ) ) {
				$modernizations[] = $modernization;
			}
		}

		if ( 1995 > $energieausweis->baujahr && 'no' === $energieausweis->decke_daemmung_on && ( 'nicht-vorhanden' === $energieausweis->dach || 'unbeheizt' === $energieausweis->dach ) ) {
			$modernization = $this->get_modernization( 'decke' );
			if ( ! $this->modernization_already_added( $modernizations, $modernization ) ) {
				$modernizations[] = $modernization;
			}
		}

		if ( 1995 > $energieausweis->baujahr && 'no' === $energieausweis->boden_daemmung_on && 'unbeheizt' === $energieausweis->keller ) {
			$modernization = $this->get_modernization( 'boden' );
			if ( ! $this->modernization_already_added( $modernizations, $modernization ) ) {
				$modernizations[] = $modernization;
			}
		}

		if ( 1995 > $energieausweis->baujahr && 'no' === $energieausweis->dach_daemmung_on && 'beheizt' === $energieausweis->dach ) {
			$modernization = $this->get_modernization( 'dach' );
			if ( ! $this->modernization_already_added( $modernizations, $modernization ) ) {
				$modernizations[] = $modernization;
			}
		}

		$irrelevant_heaters = array( 'elektronachtspeicherheizung', 'elektrodirektheizgeraet', 'kohleholzofen', 'kleinthermeniedertemperatur', 'kleinthermebrennwert', 'gasraumheizer', 'oelofenverdampfungsbrenner' );

		if ( 1995 > $energieausweis->verteilung_baujahr && ! $energieausweis->verteilung_gedaemmt && ! in_array( $energieausweis->h_erzeugung, $irrelevant_heaters ) ) {
			$modernization = $this->get_modernization( 'rohrleitungssystem' );
			if ( ! $this->modernization_already_added( $modernizations, $modernization ) ) {
				$modernizations[] = $modernization;
			}
		}

		return $modernizations;
	}
}
