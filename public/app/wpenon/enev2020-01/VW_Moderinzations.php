<?php

require_once( dirname( __FILE__ ) . '/Modernizations.php' );

/**
 * Class VW_Modernizations.
 *
 * @since 1.0.0
 */
class VW_Modernizations extends Modernizations {
	/**
	 * Energy certificate
	 *
	 * @var \WPENON\Model\Energieausweis
	 *
	 * @since 1.0.0
	 */
	private $energieausweis;

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
	public function add_modernizations( array $modernizations, \WPENON\Model\Energieausweis $energieausweis ): array {
		$this->energieausweis = $energieausweis;

		// Remove modernizations which are checked afterwards.
		$slugs_to_remove = [ 'wand', 'decke', 'boden', 'dach', 'rohrleitungssystem', 'solarthermie' ];
		$modernizations  = $this->remove_modernizations( $modernizations, $slugs_to_remove );

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
			$modernization    = $this->get_modernization( 'boden' );
			$modernizations[] = $modernization;
		}

		if ( 1995 > $energieausweis->baujahr && 'no' === $energieausweis->dach_daemmung_on && 'beheizt' === $energieausweis->dach ) {
			$modernization    = $this->get_modernization( 'dach' );
			$modernizations[] = $modernization;
		}

		$irrelevant_heaters = array(
			'elektronachtspeicherheizung',
			'elektrodirektheizgeraet',
			'kohleholzofen',
			'kleinthermeniedertemperatur',
			'kleinthermebrennwert',
			'gasraumheizer',
			'oelofenverdampfungsbrenner',
		);

		if ( 1995 > $energieausweis->verteilung_baujahr && ! $energieausweis->verteilung_gedaemmt && ! in_array( $energieausweis->h_erzeugung, $irrelevant_heaters ) ) {
			$modernization    = $this->get_modernization( 'rohrleitungssystem' );
			$modernizations[] = $modernization;
		}

		if ( $this->needs_solarthermie() ) {
			$modernization    = $this->get_modernization( 'solarthermie' );
			$modernizations[] = $modernization;
		}

		return $modernizations;
	}

	/**
	 * Needs solarthermie.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function needs_solarthermie() {
		$regenerativ_art   = trim( $this->energieausweis->regenerativ_art );
		$regenerativ_aktiv = isset( $this->energieausweis->regenerativ_aktiv ) ? $this->energieausweis->regenerativ_aktiv : false;

		if ( ! empty( $regenerativ_aktiv ) && strtolower( $regenerativ_art ) !== 'keine' ) {
			return false;
		}

		if ( $regenerativ_aktiv ) {
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
}
