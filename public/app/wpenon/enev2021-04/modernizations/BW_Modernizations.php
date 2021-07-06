<?php

namespace Enev\Schema202104\Modernizations;

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
		if ( $this->energieausweis->wand_staerke > 40 ) {
			return false;		
		}
		
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

        switch( $this->energieausweis->grundriss_form ) {
            case 'a':
                $walls = ['a', 'b', 'c', 'd'];
                break;
            case 'b':
                $walls = ['a', 'b', 'c', 'd', 'e', 'f'];
                break;
            case 'c':
                $walls = ['a', 'b', 'c', 'd', 'e', 'f'];
                break;
            case 'd':
                $walls = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
                break;
            default:
                $walls = [];
                break;
        }
        
		if ( intval( $this->energieausweis->baujahr ) < 1995 && ( ! $wand_bauart || 'wand_massiv_vollziegel_ueber_30cm' !== $wand_bauart ) ) {
			foreach ( $walls as $wall ) {
				$laengenslug   = 'wand_' . $wall . '_laenge';
				$nachbarslug   = 'wand_' . $wall . '_nachbar';
				$daemmungsslug = 'wand_' . $wall . '_daemmung';

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
		if ( $this->energieausweis->fenster_baujahr >= 1995 ) {
			return false;
		}

		if ( $this->energieausweis->fenster_bauart == 'waermedaemmglas2fach' || $this->energieausweis->fenster_bauart == 'waermedaemmglas' ) {
			return false;
		}

		return true;
	}

	/**
	 * Needs rohrleitungssystem.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function needs_rohrleitungssystem() {
		$irrelevant_heaters = array(
			'elektronachtspeicherheizung',
			'elektrodirektheizgeraet',
			'kohleholzofen',
			'kleinthermeniedertemperatur',
			'kleinthermebrennwert',
			'gasraumheizer',
			'oelofenverdampfungsbrenner',
		);

		if ( intval( $this->energieausweis->verteilung_baujahr ) < 1978 && ! $this->energieausweis->verteilung_gedaemmt && ! in_array( $this->energieausweis->h_erzeugung, $irrelevant_heaters ) ) {
			return true;
		}

		return false;
	}
}
