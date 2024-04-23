<?php

require_once __DIR__ . '/Gebaeude/Grundriss.php';
require_once __DIR__ . '/Gebaeude/Grundriss_Anbau.php';
require_once __DIR__ . '/Gebaeude/Anbau.php';

use Enev\Schema202402\Calculations\Gebaeude\Anbau;
use Enev\Schema202402\Calculations\Gebaeude\Grundriss;
use Enev\Schema202402\Calculations\Gebaeude\Grundriss_Anbau;

// $grundriss = new Grundriss( 'b', 'so' );

// $grundriss->wand_laenge( 'a', 20 );
// $grundriss->wand_laenge( 'b', 10 );

// $grundriss->wand_laenge( 'c', 5 );
// $grundriss->wand_laenge( 'd', 5 );

// foreach ( $grundriss->waende() as $wand ) {
// 	echo strtoupper( $wand . ' ' . $grundriss->wand_laenge( $wand ) ) . PHP_EOL;
// }

// echo 'Fläche: ' . $grundriss->flaeche() . PHP_EOL;

// echo 'Himmelsrichtung: ' . $grundriss->wand_himmelsrichtung( 'd' ) . PHP_EOL; // sw

// $grundriss_anbau = new Grundriss_Anbau( 'a', 'so' );

// $grundriss_anbau->wand_laenge( 'b', 10 );
// $grundriss_anbau->wand_laenge( 't', 5 );
// $grundriss_anbau->wand_laenge( 's1', 2 );

// foreach ( $grundriss_anbau->waende() as $wand ) {
// 	echo strtoupper( $wand . ' ' . $grundriss_anbau->wand_laenge( $wand ) ) . PHP_EOL;
// }

// echo 'Fläche Anbau: ' . $grundriss_anbau->flaeche() . PHP_EOL;

// $anbau = new Anbau( $grundriss_anbau, 2.5 );
// echo 'Überlappung Fläche: ' . $anbau->ueberlappung_flaeche() . PHP_EOL;
// echo 'Überlappung Fläche Wand s1: ' . $anbau->ueberlappung_flaeche( 's1' ) . PHP_EOL;
// echo 'Überlappung Fläche Wand b: ' . $anbau->ueberlappung_flaeche_gebaeude( 'b' ) . PHP_EOL;
// echo 'Volumen: ' . $anbau->volumen() . PHP_EOL;

// echo 'Himmelsrichtung: ' . $grundriss_anbau->wand_himmelsrichtung( 't' ) . PHP_EOL; // no

echo 'Gebäude' . PHP_EOL;

$grundriss = new Grundriss( 'a', 's' );

$grundriss->wand_laenge( 'a', 20 );
$grundriss->wand_laenge( 'b', 10 );

foreach ( $grundriss->waende() as $wand ) {
	echo strtoupper( $wand . ' ' . $grundriss->wand_laenge( $wand ) ) . PHP_EOL;
}

echo 'Anbau' . PHP_EOL;

// $grundriss_anbau = new Grundriss_Anbau( 'a', 's' );

// $anbau = new Anbau( $grundriss_anbau, 2.5 );

// $grundriss_anbau->wand_laenge( 'b', 10 );
// $grundriss_anbau->wand_laenge( 't', 10 );
// $grundriss_anbau->wand_laenge( 's1', 7.5 );

// foreach ( $grundriss_anbau->waende() as $wand ) {
// 	echo strtoupper( $wand . ' ' . $grundriss_anbau->wand_laenge( $wand ) ) . PHP_EOL;
// }

// echo strtoupper( 'S1 ' . ' ' . $grundriss_anbau->wand_laenge( 's1' ) ) . PHP_EOL;
// echo 'Wandlänge: ' . $grundriss_anbau->wand_laenge_gesamt() . PHP_EOL;

$grundriss_anbau = new Grundriss_Anbau( 'b', 's' );

$grundriss_anbau->wand_laenge( 'b', 10 );
$grundriss_anbau->wand_laenge( 't', 10 );
$grundriss_anbau->wand_laenge( 's1', 5 );
$grundriss_anbau->wand_laenge( 's2', 5 );

foreach ( $grundriss_anbau->waende() as $wand ) {
	echo strtoupper( $wand . ' ' . $grundriss_anbau->wand_laenge( $wand ) ) . PHP_EOL;
	echo 'Himmelsrichtung Wand ' . $wand . ': ' . $grundriss_anbau->wand_himmelsrichtung( $wand ) . PHP_EOL;
}

echo 'Wandlänge: ' . $grundriss_anbau->wand_laenge_gesamt() . PHP_EOL;
echo 'Fläche: ' . $grundriss_anbau->flaeche() . PHP_EOL;

$anbau = new Anbau( $grundriss_anbau, 2.5 );
echo 'Volumen Anbau: ' . $anbau->volumen() . PHP_EOL;

echo 'Länge s1: ' . $grundriss_anbau->wand_laenge( $wand ) . PHP_EOL;
echo 'Überlappung Fläche Wand s1: ' . $anbau->ueberlappung_flaeche( 's1' ) . PHP_EOL;
echo 'Überlappung Fläche Wand b: ' . $anbau->ueberlappung_flaeche_gebaeude( 'b' ) . PHP_EOL;

echo 'Länge s2: ' . $grundriss_anbau->wand_laenge( $wand ) . PHP_EOL;
echo 'Überlappung Fläche Wand s2: ' . $anbau->ueberlappung_flaeche( 's2' ) . PHP_EOL;
echo 'Überlappung Fläche Wand a: ' . $anbau->ueberlappung_flaeche_gebaeude( 'a' ) . PHP_EOL;