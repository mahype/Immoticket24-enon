<?php

namespace Enev\Schema202302\Calculations\Tabellen;

/**
 * Gibt den U-Wert eines Bauteils zurück.
 *
 * @param string $bauteil Slug des Bauteils
 * @param int    $baujahr Baujahr des Bauteils
 *
 * @return float Uwert.
 */
function uwert( string $bauteil, int $baujahr ): float {
	$uwerte = wpenon_get_table_results( 'uwerte2021' );

	$steps = array( 1918, 1948, 1957, 1968, 1978, 1983, 1994, 2001, 2006 );
	foreach ( $steps as $step ) {
		if ( $baujahr <= $step ) {
			$jahr_slug = 'bis' . $step;
			break;
		}
	}

	if ( ! isset( $jahr_slug ) ) {
		$jahr_slug = 'ab2007';
	}

	return floatval( $uwerte[ $bauteil ]->$jahr_slug );
}
