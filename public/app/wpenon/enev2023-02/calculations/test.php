<?php

require_once __DIR__ . '/Bauteile/Grundriss.php';

$grundriss = new Grundriss('b', 'so');

$grundriss->wandlaenge( 'a', 20 );
$grundriss->wandlaenge( 'b', 10 );
$grundriss->wandlaenge( 'c', 5 );
$grundriss->wandlaenge( 'd', 5 );


foreach( $grundriss->waende() AS $wand ) {
    echo strtoupper( $wand . ' ' . $grundriss->wandlaenge( $wand ) ) . PHP_EOL;
}

echo 'Fläche: ' . $grundriss->flaeche() . PHP_EOL;