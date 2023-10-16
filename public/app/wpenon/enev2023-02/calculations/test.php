<?php

require_once __DIR__ . '/Bauteile/Heizsystem.php';

$heizsystem = new Heizsystem();

$heizungsanlage = new Heizungsanlage( '90/70', 'alles', 50 );
$heizungsanlage = new Heizungsanlage( '70/55', 'alles', 50 );

$uebergabesystem = new Uebergabesystem( '35/28', 50 );
$uebergabesystem = new Uebergabesystem( '70/55', 50 );

$heizsystem->heizungsanlagen()->hinzufuegen( $heizungsanlage );
$heizsystem->heizungsanlagen()->hinzufuegen( $heizungsanlage );
$heizsystem->uebergabesysteme()->hinzufuegen( new Uebergabesystem( '35/28', 50 ) );
$heizsystem->uebergabesysteme()->hinzufuegen( new Uebergabesystem( '70/55', 50 ) );

$heizsystem->wasserversorgungen()->hinzufuegen_ueber_heizung( $heizungsanlage, true, true );

echo $heizsystem->fh_a();