<?php

namespace Enev\Schema202302\Calculations\Helfer;

/**
 * Berechnung der Fensterflächen anhand der Größe der Maße der Wand.
 *
 * @package Enev\Schema202302\Calculations\Bauteile
 */
function berechne_fenster_flaeche( float $wandlaenge, float $wandhoehe, float $wanddicke ): float {
	return ( 0.55 * ( $wandlaenge - $wanddicke ) ) * ( $wandhoehe - 1.5 );
}

/**
 * Berechnung der Rolladenkasten-Flächen anhand der Fläche des Fensters.
 *
 * @package Enev\Schema202302\Calculations\Bauteile
 */
function berechne_rolladenkasten_flaeche( float $fensterflaeche ): float {
	return 0.1 * $fensterflaeche;
}

/**
 * Berechnung der Größe der Heizkörpernischen anhand der Fläche des Fensters.
 *
 * @param float $fensterflaeche Fläche des Fensters.
 */
function berechne_heizkoepfernische_flaeche( float $fensterflaeche ): float {
	return 0.5 * $fensterflaeche;
}
