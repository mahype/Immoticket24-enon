<?php

namespace Enev\Schema202403\Calculations\Bauteile;

use Enev\Schema202403\Calculations\Schnittstellen\Transmissionswaerme;

require_once __DIR__ . '/Dach.php';

/**
 * Diese Klasse entählt die Funktionen zur Berechnung eines Pultdachs.
 *
 * @package
 */
class Pultdach extends Dach implements Transmissionswaerme
{

	/**
	 * Berechnung des volumens und der Dachfläche-
	 *
	 * @return void
	 * @throws Exception
	 */
	protected function berechnen(): void
	{
		$flaeche = 0;
		$volumen = 0;

		switch ($this->grundriss->form()) {
			case 'a':
				if ($this->grundriss->wand_laenge('a') > $this->grundriss->wand_laenge('b')) {
					$dach_s = sqrt(pow($this->hoehe(), 2) + pow($this->grundriss->wand_laenge('b'), 2));
					// array_push( $dachwinkel, atan( $this->hoehe() / $this->grundriss->wand_laenge( 'b' ) ) );
					$flaeche              += $this->grundriss->wand_laenge('a') * $dach_s;
					$volumen              += 0.5 * $this->grundriss->wand_laenge('a') * $this->grundriss->wand_laenge('b') * $this->hoehe();
					$dachwandflaechen['b'] = 0.5 * $this->grundriss->wand_laenge('b') * $this->hoehe();
					$dachwandflaechen['d'] = 0.5 * $this->grundriss->wand_laenge('d') * $this->hoehe();
					$dachwandflaechen['c'] = $this->grundriss->wand_laenge('a') * $this->hoehe();
				} else {
					$dach_s = sqrt(pow($this->hoehe(), 2) + pow($this->grundriss->wand_laenge('a'), 2));
					// array_push( $dachwinkel, atan( $this->hoehe() / $this->grundriss->wand_laenge( 'a' ) ) );
					$flaeche              += $this->grundriss->wand_laenge('b') * $dach_s;
					$volumen              += 0.5 * $this->grundriss->wand_laenge('a') * $this->grundriss->wand_laenge('b') * $this->hoehe();
					$dachwandflaechen['a'] = 0.5 * $this->grundriss->wand_laenge('a') * $this->hoehe();
					$dachwandflaechen['c'] = 0.5 * $this->grundriss->wand_laenge('c') * $this->hoehe();
					$dachwandflaechen['d'] = $this->grundriss->wand_laenge('b') * $this->hoehe();
				}
				break;
			case 'b':
				if ($this->grundriss->wand_laenge('a') > $this->grundriss->wand_laenge('b')) {
					$dach_s1 = sqrt(pow($this->hoehe(), 2) + pow($this->grundriss->wand_laenge('f'), 2));
					$dach_s2 = sqrt(pow($this->hoehe(), 2) + pow($this->grundriss->wand_laenge('d'), 2));
					// array_push( $dachwinkel, atan( $this->hoehe() / $this->grundriss->wand_laenge( 'f' ) ), atan( $this->hoehe() / $this->grundriss->wand_laenge( 'd' ) ) );
					$flaeche              += $this->grundriss->wand_laenge('a') * $dach_s1 + $this->grundriss->wand_laenge('c') * $dach_s2;
					$volumen              += 0.5 * $this->grundriss->wand_laenge('a') * $this->grundriss->wand_laenge('f') * $this->hoehe() + 0.5 * $this->grundriss->wand_laenge('c') * $this->grundriss->wand_laenge('d') * $this->hoehe();
					$dachwandflaechen['b'] = 0.5 * $this->grundriss->wand_laenge('f') * $this->hoehe() + 0.5 * $this->grundriss->wand_laenge('d') * $this->hoehe();
					$dachwandflaechen['d'] = 0.5 * $this->grundriss->wand_laenge('d') * $this->hoehe();
					$dachwandflaechen['e'] = $this->grundriss->wand_laenge('e') * $this->hoehe();
					$dachwandflaechen['f'] = 0.5 * $this->grundriss->wand_laenge('f') * $this->hoehe();
				} else {
					$dach_s1 = sqrt(pow($this->hoehe(), 2) + pow($this->grundriss->wand_laenge('c'), 2));
					$dach_s2 = sqrt(pow($this->hoehe(), 2) + pow($this->grundriss->wand_laenge('e'), 2));
					// array_push( $dachwinkel, atan( $this->hoehe() / $this->grundriss->wand_laenge( 'c' ) ), atan( $this->hoehe() / $this->grundriss->wand_laenge( 'e' ) ) );
					$flaeche              += $this->grundriss->wand_laenge('b') * $dach_s1 + $this->grundriss->wand_laenge('f') * $dach_s2;
					$volumen              += 0.5 * $this->grundriss->wand_laenge('b') * $this->grundriss->wand_laenge('c') * $this->hoehe() + 0.5 * $this->grundriss->wand_laenge('f') * $this->grundriss->wand_laenge('e') * $this->hoehe();
					$dachwandflaechen['a'] = 0.5 * $this->grundriss->wand_laenge('c') * $this->hoehe() + 0.5 * $this->grundriss->wand_laenge('e') * $this->hoehe();
					$dachwandflaechen['c'] = 0.5 * $this->grundriss->wand_laenge('c') * $this->hoehe();
					$dachwandflaechen['d'] = $this->grundriss->wand_laenge('d') * $this->hoehe();
					$dachwandflaechen['e'] = 0.5 * $this->grundriss->wand_laenge('e') * $this->hoehe();
				}
				break;
			case 'c':
				$dach_s1 = sqrt(pow($this->hoehe(), 2) + pow($this->grundriss->wand_laenge('b'), 2));
				$dach_s2 = sqrt(pow($this->hoehe(), 2) + pow($this->grundriss->wand_laenge('d'), 2));
				// array_push( $dachwinkel, atan( $this->hoehe() / $this->grundriss->wand_laenge( 'b' ) ), atan( $this->hoehe() / $this->grundriss->wand_laenge( 'd' ) ) );
				$flaeche              += $this->grundriss->wand_laenge('a') * $dach_s1 + $this->grundriss->wand_laenge('e') * $dach_s2;
				$volumen              += 0.5 * $this->grundriss->wand_laenge('a') * $this->grundriss->wand_laenge('b') * $this->hoehe() + 0.5 * $this->grundriss->wand_laenge('e') * $this->grundriss->wand_laenge('d') * $this->hoehe();
				$dachwandflaechen['b'] = 0.5 * $this->grundriss->wand_laenge('b') * $this->hoehe();
				$dachwandflaechen['c'] = $this->grundriss->wand_laenge('c') * $this->hoehe();
				$dachwandflaechen['d'] = 0.5 * $this->grundriss->wand_laenge('d') * $this->hoehe();
				$dachwandflaechen['f'] = 0.5 * $this->grundriss->wand_laenge('f') * $this->hoehe();
				$dachwandflaechen['g'] = $this->grundriss->wand_laenge('g') * $this->hoehe();
				$dachwandflaechen['h'] = 0.5 * $this->grundriss->wand_laenge('h') * $this->hoehe();
				break;
			case 'd':
				$dach_s1 = sqrt(pow($this->hoehe(), 2) + pow($this->grundriss->wand_laenge('b') - $this->grundriss->wand_laenge('d'), 2));
				$dach_s2 = sqrt(pow($this->hoehe(), 2) + pow($this->grundriss->wand_laenge('d'), 2));
				$dach_s3 = sqrt(pow($this->hoehe(), 2) + pow($this->grundriss->wand_laenge('f'), 2));
				// array_push( $dachwinkel, atan( $this->hoehe() / ( $this->grundriss->wand_laenge( 'b' ) - $this->grundriss->wand_laenge( 'd' ) ) ), atan( $this->hoehe() / $this->grundriss->wand_laenge( 'd' ) ), atan( $this->hoehe() / $this->grundriss->wand_laenge( 'f' ) ) );
				$flaeche              += $this->grundriss->wand_laenge('a') * $dach_s1 + $this->grundriss->wand_laenge('c') * $dach_s2 + $this->grundriss->wand_laenge('g') * $dach_s3;
				$volumen              += 0.5 * $this->grundriss->wand_laenge('a') * ($this->grundriss->wand_laenge('b') - $this->grundriss->wand_laenge('d')) * $this->hoehe() + 0.5 * $this->grundriss->wand_laenge('c') * $this->grundriss->wand_laenge('d') * $this->hoehe() + 0.5 * $this->grundriss->wand_laenge('g') * $this->grundriss->wand_laenge('f') * $this->hoehe();
				$dachwandflaechen['b'] = 0.5 * ($this->grundriss->wand_laenge('b') - $this->grundriss->wand_laenge('d')) * $this->hoehe() + 0.5 * $this->grundriss->wand_laenge('d') * $this->hoehe();
				$dachwandflaechen['d'] = 0.5 * $this->grundriss->wand_laenge('d') * $this->hoehe();
				$dachwandflaechen['e'] = $this->grundriss->wand_laenge('e') * $this->hoehe();
				$dachwandflaechen['f'] = 0.5 * $this->grundriss->wand_laenge('f') * $this->hoehe();
				$dachwandflaechen['h'] = 0.5 * ($this->grundriss->wand_laenge('h') - $this->grundriss->wand_laenge('f')) * $this->hoehe() + 0.5 * $this->grundriss->wand_laenge('f') * $this->hoehe();
				break;
			default:
		}

		$this->flaeche = $flaeche;
		$this->volumen = $volumen + $this->volumen_kniestock();
	}
}
