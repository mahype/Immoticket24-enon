<?php

namespace Enev\Schema202403\Calculations\Helfer;

/**
 * Berechnung der Fensterflächen anhand der Größe der Maße der Wand.
 * 
 * @param float $wandlaenge Länge der Wand in Metern.
 * @param float $innere_wandhoehe  Höhe der Wand in Metern (Lichte Höhe)
 * @param float $wanddicke  Dicke der Wand in Metern.
 *
 * @package Enev\Schema202403\Calculations\Bauteile
 */
function berechne_fenster_flaeche(float $wandlaenge, float $innere_wandhoehe, float $wanddicke): float
{
	$innere_wandlaenge = $wandlaenge - $wanddicke * 2;
	return (0.55 *  $innere_wandlaenge) * ($innere_wandhoehe - 1.5);
}

/**
 * Berechnung der Rolladenkasten-Flächen anhand der Fläche des Fensters.
 * 
 * @param float $fensterflaeche Fläche des Fensters in m².
 *
 * @package Enev\Schema202403\Calculations\Bauteile
 */
function berechne_rolladenkasten_flaeche(float $fensterflaeche): float
{
	return 0.1 * $fensterflaeche;
}

/**
 * Berechnung der Größe der Heizkörpernischen anhand der Fläche des Fensters.
 * 
 * @param float $fensterflaeche Fläche des Fensters in m².
 *
 * @param float $fensterflaeche Fläche des Fensters.
 */
function berechne_heizkoerpernische_flaeche(float $fensterflaeche): float
{
	return 0.5 * $fensterflaeche;
}
