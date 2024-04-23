<?php

namespace Enev\Schema202402\Calculations\Bauteile;

use Enev\Schema202402\Calculations\Schnittstellen\Transmissionswaerme;

require_once __DIR__ . '/Dach.php';

/**
 * Diese Klasse entählt die Funktionen zur Berechnung eines Walmdaches.
 *
 * @package
 */
class Walmdach extends Dach implements Transmissionswaerme {
	/**
	 * Berechnung des volumens und der Dachfläche-
	 *
	 * @return void
	 * @throws Exception
	 */
	protected function berechnen(): void {
		$flaeche = 0;
		$volumen = 0;

		switch ( $this->grundriss->form() ) {
			case 'a':
				if ( $this->grundriss->wand_laenge( 'a' ) > $this->grundriss->wand_laenge( 'b' ) ) {
					$dach_th = $this->grundriss->wand_laenge( 'a' );
					$dach_f  = $this->grundriss->wand_laenge( 'a' ) - $this->grundriss->wand_laenge( 'b' );
					$dach_b  = 0.5 * $this->grundriss->wand_laenge( 'b' );
					$dach_x  = 0.5 * ( $this->grundriss->wand_laenge( 'a' ) - $dach_f );
				} else {
					$dach_th = $this->grundriss->wand_laenge( 'b' );
					$dach_f  = $this->grundriss->wand_laenge( 'b' ) - $this->grundriss->wand_laenge( 'a' );
					$dach_b  = 0.5 * $this->grundriss->wand_laenge( 'a' );
					$dach_x  = 0.5 * ( $this->grundriss->wand_laenge( 'b' ) - $dach_f );
				}
				$dach_sh = sqrt( pow( $this->hoehe(), 2 ) + pow( $dach_b, 2 ) );
				$dach_sw = sqrt( pow( $this->hoehe(), 2 ) + pow( $dach_x, 2 ) );
				// array_push( $dachwinkel, atan( $this->hoehe() / $dach_b ), atan( $this->hoehe() / $dach_x ) );
				$flaeche += 2 * ( 0.5 * $dach_b * $dach_sw + 0.5 * ( $dach_th + $dach_f ) * $dach_sh );
				$volumen += ( 1.0 / 3.0 ) * ( 2 * $dach_b ) * ( 2 * $dach_x ) * $this->hoehe() + 0.5 * ( 2 * $dach_b ) * $dach_f * $this->hoehe();
				break;
			case 'b':
				$dach_b1_gross = $this->grundriss->wand_laenge( 'f' );
				$dach_b1       = 0.5 * $this->grundriss->wand_laenge( 'f' );
				$dach_b2_gross = $this->grundriss->wand_laenge( 'c' );
				$dach_b2       = 0.5 * $this->grundriss->wand_laenge( 'c' );
				$dach_t1       = $this->grundriss->wand_laenge( 'b' );
				$dach_t2       = $this->grundriss->wand_laenge( 'a' );
				$dach_t3       = $this->grundriss->wand_laenge( 'd' );
				$dach_t4       = $this->grundriss->wand_laenge( 'e' );
				$dach_f1       = $dach_t3;
				$dach_f2       = $dach_t2 - 2 * $dach_b1;
				$dach_s1       = sqrt( pow( $this->hoehe(), 2 ) + pow( $dach_b1, 2 ) );
				$dach_s2       = sqrt( pow( $this->hoehe(), 2 ) + pow( $dach_b2, 2 ) );
				// array_push( $dachwinkel, atan( $this->hoehe() / $dach_b1 ), atan( $this->hoehe() / $dach_b2 ) );
				$flaeche += 0.5 * $dach_b1_gross * $dach_s1 + 0.5 * $dach_b2_gross * $dach_s2 + 0.5 * ( $dach_t1 + $dach_f1 ) * $dach_s2 + 0.5 * ( $dach_t2 + $dach_f2 ) * $dach_s1 + ( 0.5 * ( $dach_t2 + $dach_f2 ) * $dach_s1 - 0.5 * $dach_b2_gross * $dach_s2 ) + $dach_t3 * $dach_s2;
				$volumen += ( 1.0 / 3.0 ) * $dach_b1_gross * $dach_b1_gross * $this->hoehe() + 0.5 * $dach_b1_gross * $dach_f2 * $this->hoehe() + 0.5 * $dach_b2_gross * $dach_t3 * $this->hoehe();
				break;
			case 'c':
				$dach_b1_gross = $this->grundriss->wand_laenge( 'b' );
				$dach_b1       = 0.5 * $this->grundriss->wand_laenge( 'b' );
				$dach_b2_gross = $this->grundriss->wand_laenge( 'e' );
				$dach_b2       = 0.5 * $this->grundriss->wand_laenge( 'e' );
				$dach_t1       = $this->grundriss->wand_laenge( 'b' ) + $this->grundriss->wand_laenge( 'd' );
				$dach_t2       = $this->grundriss->wand_laenge( 'a' );
				$dach_t3       = $this->grundriss->wand_laenge( 'f' );
				$dach_t4       = $this->grundriss->wand_laenge( 'g' );
				$dach_f1       = $dach_t3;
				$dach_f2       = $dach_t2 - 2 * $dach_b1;
				$dach_s1       = sqrt( pow( $this->hoehe(), 2 ) + pow( $dach_b1, 2 ) );
				$dach_s2       = sqrt( pow( $this->hoehe(), 2 ) + pow( $dach_b2, 2 ) );
				// array_push( $dachwinkel, atan( $this->hoehe() / $dach_b1 ), atan( $this->hoehe() / $dach_b2 ) );
				$flaeche += 2 * ( 0.5 * $dach_b1_gross * $dach_s1 ) + 0.5 * $dach_b2_gross * $dach_s2 + 0.5 * ( $dach_t2 + $dach_f2 ) * $dach_s1 + ( 0.5 * ( $dach_t2 + $dach_f2 ) * $dach_s1 - 0.5 * $dach_b2_gross * $dach_s2 ) + 2 * ( $dach_t3 * $dach_s2 );
				$volumen += ( 1.0 / 3.0 ) * $dach_b1_gross * $dach_b1_gross * $this->hoehe() + 0.5 * $dach_b1_gross * $dach_f2 * $this->hoehe() + 0.5 * $dach_b2_gross * $dach_t3 * $this->hoehe();
				break;
			case 'd':
				$dach_b1_gross = $this->grundriss->wand_laenge( 'b' ) - $this->grundriss->wand_laenge( 'd' );
				$dach_b1       = 0.5 * ( $this->grundriss->wand_laenge( 'b' ) - $this->grundriss->wand_laenge( 'd' ) );
				$dach_b2_gross = $this->grundriss->wand_laenge( 'c' );
				$dach_b2       = 0.5 * $this->grundriss->wand_laenge( 'c' );
				$dach_b3_gross = $this->grundriss->wand_laenge( 'g' );
				$dach_b3       = 0.5 * $this->grundriss->wand_laenge( 'g' );
				$dach_t1       = $this->grundriss->wand_laenge( 'b' );
				$dach_t2       = $this->grundriss->wand_laenge( 'a' );
				$dach_t3       = $this->grundriss->wand_laenge( 'h' );
				$dach_t4       = $this->grundriss->wand_laenge( 'd' );
				$dach_t5       = $this->grundriss->wand_laenge( 'e' );
				$dach_t6       = $this->grundriss->wand_laenge( 'f' );
				$dach_f1       = $dach_t1 - $dach_b1 - $dach_b2;
				$dach_f2       = $dach_t2 - $dach_b2 - $dach_b3;
				$dach_f3       = $dach_t3 - $dach_b1 - $dach_b3;
				$dach_s1       = sqrt( pow( $this->hoehe(), 2 ) + pow( $dach_b1, 2 ) );
				$dach_s2       = sqrt( pow( $this->hoehe(), 2 ) + pow( $dach_b2, 2 ) );
				$dach_s3       = sqrt( pow( $this->hoehe(), 2 ) + pow( $dach_b3, 2 ) );
				// array_push( $dachwinkel, atan( $this->hoehe() / $dach_b1 ), atan( $this->hoehe() / $dach_b2 ), atan( $this->hoehe() / $dach_b3 ) );
				$flaeche += 0.5 * $dach_b2_gross * $dach_s2 + 0.5 * $dach_b3_gross * $dach_s3 + 0.5 * ( $dach_t1 + $dach_f1 ) * $dach_s2 + 0.5 * ( $dach_t2 + $dach_f2 ) * $dach_s1 + 0.5 * ( $dach_t3 + $dach_f3 ) * $dach_s3 + $dach_t4 * $dach_s2 + 0.5 * ( $dach_t5 + $dach_f2 ) * $dach_s1 + $dach_t6 * $dach_s3;
				$volumen += ( 1.0 / 3.0 ) * $dach_b1_gross * $dach_b1_gross * $this->hoehe() + 0.5 * $dach_b1_gross * $dach_f2 * $this->hoehe() + 0.5 * $dach_b2_gross * $dach_t4 * $this->hoehe() + 0.5 * $dach_b3_gross * $dach_t6 * $this->hoehe();
				break;
			default:
		}

		$this->flaeche = $flaeche;
		$this->volumen = $volumen + $this->volumen_kniestock();
	}
}
