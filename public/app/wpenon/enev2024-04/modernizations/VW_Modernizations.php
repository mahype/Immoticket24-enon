<?php

namespace Enev\Schema202404\Modernizations;

require_once(dirname(__FILE__) . '/Modernizations.php');

/**
 * Class Bedarfsausweis modernizations.
 *
 * @since 1.0.0
 */
class VW_Modernizations extends Modernizations
{
	/**
	 * Needs heater.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function needs_heater() {
		$heatings = array( 'h' );

		$max_age = 30;

		if ( isset( $this->energieausweis->h2_info ) && $this->energieausweis->h2_info ) {
			$heatings[] = 'h2';

			if ( isset( $this->energieausweis->h3_info ) && $this->energieausweis->h3_info ) {
				$heatings[] = 'h3';
			}
		}

		$current_year = absint( current_time( 'Y' ) );

		$types_general = array(
			'standardkessel',
			'elektronachtspeicherheizung',
		);

		$types_older_max_age  = array(
			'gasraumheizer',
			'elektrodirektheizgeraet',
			'oelofenverdampfungsbrenner',
			'kohleholzofen',
		);

		foreach ( $heatings as $heating ) {
			$type_field = $heating . '_erzeugung';
			$year_field = $heating . '_baujahr';

			if ( in_array( $this->energieausweis->$type_field, $types_general, true ) ) {
				return true;
			}

			if ( in_array( $this->energieausweis->$type_field, $types_older_max_age, true ) && ! empty( $this->energieausweis->$year_field ) && $this->energieausweis->$year_field <= $current_year - $max_age ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Needs wand.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function needs_wand()
	{
		if (!isset($this->energieausweis->wand_staerke)) {
			return false;
		}
		if ($this->energieausweis->wand_staerke >= 40) {
			return false;
		}

		if (intval($this->energieausweis->baujahr) < 1995 && 'no' === $this->energieausweis->wand_daemmung_on) {
			return true;
		}

		return false;
	}

	/**
	 * Needs boden.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function needs_boden()
	{
		if (intval($this->energieausweis->baujahr) < 1995 && 'no' === $this->energieausweis->boden_daemmung_on && 'unbeheizt' === $this->energieausweis->keller) {
			return true;
		}

		return false;
	}

	/**
	 * Needs decke.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function needs_decke()
	{
		if (intval($this->energieausweis->baujahr) < 1995 && 'no' === $this->energieausweis->decke_daemmung_on && ('nicht-vorhanden' === $this->energieausweis->dach || 'unbeheizt' === $this->energieausweis->dach)) {
			return true;
		}

		return false;
	}

	/**
	 * Needs dach.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function needs_dach()
	{
		if (intval($this->energieausweis->baujahr) < 1995 && 'no' === $this->energieausweis->dach_daemmung_on && 'beheizt' === $this->energieausweis->dach) {
			return true;
		}

		return false;
	}

	/**
	 * Needs windows.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function needs_windows()
	{
		return $this->check_window($this->energieausweis->fenster_baujahr, $this->energieausweis->fenster_bauart);
	}

	/**
	 * Needs rohrleitungssystem.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function needs_rohrleitungssystem()
	{
		if (
			intval($this->energieausweis->verteilung_baujahr) < 1995 &&
			!$this->energieausweis->verteilung_gedaemmt &&
			!in_array($this->energieausweis->h_erzeugung, wpenon_get_heaters_without_piping())
		) {
			return true;
		}

		return false;
	}
}
