<?php

require_once __DIR__ . '/Bauteile/Grundriss.php';
require_once __DIR__ . '/Bauteile/Grundriss_Anbau.php';
require_once __DIR__ . '/Bauteile/Anbau.php';

$grundriss = new Grundriss('b', 'so');

$grundriss->wand_laenge( 'a', 20 );
$grundriss->wand_laenge( 'b', 10 );
$grundriss->wand_laenge( 'c', 5 );
$grundriss->wand_laenge( 'd', 5 );

foreach( $grundriss->waende() AS $wand ) {
    echo strtoupper( $wand . ' ' . $grundriss->wand_laenge( $wand ) ) . PHP_EOL;
}

echo 'Fläche: ' . $grundriss->flaeche() . PHP_EOL;

echo 'Himmelsrichtung: ' . $grundriss->wand_himmelsrichtung( 'd' ) . PHP_EOL; // sw

$grundriss_anbau = new Grundriss_Anbau('a', 'so');

$grundriss_anbau->wand_laenge('b', 10);
$grundriss_anbau->wand_laenge('t', 5);
$grundriss_anbau->wand_laenge('s1', 2);

foreach( $grundriss_anbau->waende() AS $wand ) {
     echo strtoupper( $wand . ' ' . $grundriss_anbau->wand_laenge( $wand ) ) . PHP_EOL;
}

echo 'Fläche Anbau: ' . $grundriss_anbau->flaeche() . PHP_EOL;

$anbau = new Anbau( $grundriss_anbau, 2.5 );
echo 'Überlappung Fläche: ' . $anbau->ueberlappung_flaeche() . PHP_EOL;
echo 'Überlappung Fläche Wand s1: ' . $anbau->ueberlappung_flaeche( 's1' ) . PHP_EOL;
echo 'Überlappung Fläche Wand b: ' . $anbau->ueberlappung_flaeche_gebaeude( 'b' ) . PHP_EOL;
echo 'Volumen: ' . $anbau->volumen() . PHP_EOL;

echo 'Himmelsrichtung: ' . $grundriss_anbau->wand_himmelsrichtung( 't' ) . PHP_EOL; // no