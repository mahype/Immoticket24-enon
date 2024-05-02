<?php


namespace Enev\Schema202403\Calculations\Helfer;

require_once __DIR__ . '/Jahr.php';

/**
 * Berechnung des Faktors fum.
 */
function fum(string $month): float
{
	return 1000 / (24 * (new Jahr())->monat($month)->tage());
}
