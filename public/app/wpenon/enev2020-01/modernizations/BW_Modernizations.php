<?php

namespace Enev\Modernizations;

require_once( dirname( __FILE__ ) . '/Modernizations.php' );

/**
 * Class Bedarfsausweis modernizations.
 *
 * @since 1.0.0
 */
class BW_Modernizations extends Modernizations {
	/**
	 * Needs wand.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function needs_wand() {
		switch ( $this->energieausweis->gebaeudekonstruktion ) {
			case 'massiv':
				$wand_bauart = 'massiv_' . $this->energieausweis->wand_bauart_massiv;
				break;
			case 'holz':
				$wand_bauart = 'holzhaus_' . $this->energieausweis->wand_bauart_holz;
				break;
			case 'fachwerk':
				$wand_bauart = 'fachwerk_' . $this->energieausweis->wand_bauart_fachwerk;
				break;
			default:
				return false;
		}

		if ( intval( $this->energieausweis->baujahr ) < 1995 && ( ! $wand_bauart || 'wand_massiv_vollziegel_ueber_30cm' !== $wand_bauart ) ) {
			foreach ( array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h' ) as $wand ) {
				$laengenslug   = 'wand_' . $wand . '_laenge';
				$nachbarslug   = 'wand_' . $wand . '_nachbar';
				$daemmungsslug = 'wand_' . $wand . '_daemmung';

				$wand_laenge   = $this->energieausweis->$laengenslug;
				$wand_daemmung = $this->energieausweis->$daemmungsslug;
				$wand_nachbar  = $this->energieausweis->$nachbarslug;

				if ( $wand_laenge > 0.0 && ! $wand_nachbar && $wand_daemmung < 4.0 ) {
					return true;
				}
			}
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
		if ( intval( $this->energieausweis->baujahr ) < 1995 && 'unbeheizt' === $this->energieausweis->keller && 6.0 > $this->energieausweis->boden_daemmung ) {
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
		if ( intval( $this->energieausweis->baujahr ) < 1995 && 'unbeheizt' === $this->energieausweis->dach && 14.0 > $this->energieausweis->decke_daemmung ) {
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
		if ( intval( $this->energieausweis->baujahr ) < 1995 && 14.0 > $this->energieausweis->dach_daemmung && ( 'beheizt' === $this->energieausweis->dach || 'nicht-vorhanden' === $this->energieausweis->dach ) ) {
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
		if ( $this->energieausweis->fenster_manuell ) {
			foreach ( array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h' ) as $fenster ) {
				$flaecheslug = 'fenster_' . $fenster . '_flaeche';
				$bauartslug  = 'fenster_' . $fenster . '_bauart';
				$baujahrslug = 'fenster_' . $fenster . '_baujahr';

				if ( 0 === intval( $this->energieausweis->$flaecheslug ) ) {
					continue;
				}

				if ( ! $this->check_window( $this->energieausweis->$baujahrslug, $this->energieausweis->$bauartslug ) ) {
					continue;
				}

				return true;
			}
		} else {
			return $this->check_window( $this->energieausweis->fenster_baujahr, $this->energieausweis->fenster_bauart );
		}

		return false;
	}
}
