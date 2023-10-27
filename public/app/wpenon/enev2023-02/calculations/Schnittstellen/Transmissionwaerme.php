<?php

namespace Enev\Schema202302\Calculations\Schnittstellen;

/**
 * Diese Klasse entählt die Funktionen zur Berechnung eines Bauteils mit Transmissionswärme.
 */
interface Transmissionswaerme {
	/**
	 * Berechnung der Transmissionswärme.
	 *
	 * @return float
	 */
	public function ht(): float;
}
