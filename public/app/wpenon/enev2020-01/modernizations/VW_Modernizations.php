<?php

namespace Enev\Modernizations;

require_once( dirname( __FILE__ ) . '/Modernizations.php' );

/**
 * Class Bedarfsausweis modernizations.
 *
 * @since 1.0.0
 */
class VW_Modernizations extends Modernizations {
	/**
	 * Needs wand.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function needs_wand() {
		if ( intval( $this->energieausweis->baujahr ) < 1995 && 'no' === $this->energieausweis->wand_daemmung_on ) {
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
	protected function needs_boden() {
		if ( intval( $this->energieausweis->baujahr ) < 1995 && 'no' === $this->energieausweis->boden_daemmung_on && 'unbeheizt' === $this->energieausweis->keller ) {
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
	protected function needs_decke() {
		if ( intval( $this->energieausweis->baujahr ) < 1995 && 'no' === $this->energieausweis->decke_daemmung_on && ( 'nicht-vorhanden' === $this->energieausweis->dach || 'unbeheizt' === $this->energieausweis->dach ) ) {
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
	protected function needs_dach() {
		if ( intval( $this->energieausweis->baujahr ) < 1995 && 'no' === $this->energieausweis->dach_daemmung_on && 'beheizt' === $this->energieausweis->dach ) {
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
	protected function needs_windows() {
		return $this->check_window( $this->energieausweis->fenster_baujahr, $this->energieausweis->fenster_bauart );
	}
}
