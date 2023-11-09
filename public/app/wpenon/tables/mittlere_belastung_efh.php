<?php

return array(
  'title'         => __( 'Mittlere Belastung Einfamilienhaus', 'wpenon' ),
  'description'   => __( 'Diese Tabelle enthält Mittlere monatliche außentemperaturabhängige Belastung 𝛃e,m für Wohngebäude – Einfamilienhäuser', 'wpenon' ) . ' ' . __( 'Diese Daten werden für den Bedarfsausweis benötigt.', 'wpenon' ),
  'primary_field' => 'bezeichnung',
  'search_field'  => 'bezeichnung',
  'search_before' => true,
  'fields'        => array(
    'bezeichnung'   => array(
      'title'         => __( 'Bezeichnung (intern)', 'wpenon' ),
      'type'          => 'VARCHAR(100)',
    ),
    'name'          => array(
      'title'         => __( 'Monat', 'wpenon' ),
      'type'          => 'VARCHAR(100)',
    ),
    't50_ohne'     => array(
      'title'       => __( 'T50 Ohne Teilbeheizung', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't50_5wm2'     => array(
      'title'       => __( 'T50 < 5W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't50_10wm2'     => array(
      'title'       => __( 'T50 10W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't50_25wm2'     => array(
      'title'       => __( 'T50 25W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't50_50wm2'     => array(
      'title'       => __( 'T50 50W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't50_75wm2'     => array(
      'title'       => __( 'T50 75W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't50_100wm2'     => array(
      'title'       => __( 'T50 100W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't50_125wm2'     => array(
      'title'       => __( 'T50 125W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't50_150wm2'     => array(
      'title'       => __( 'T50 > 150W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't90_ohne'     => array(
        'title'       => __( 'T90 Ohne Teilbeheizung', 'wpenon' ),
        'type'        => 'FLOAT',
    ),
    't90_5wm2'     => array(
      'title'       => __( 'T90 < 5W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't90_10wm2'     => array(
      'title'       => __( 'T90 10W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't90_25wm2'     => array(
      'title'       => __( 'T90 25W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't90_50wm2'     => array(
      'title'       => __( 'T90 50W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't90_75wm2'     => array(
      'title'       => __( 'T90 75W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't90_100wm2'     => array(
      'title'       => __( 'T90 100W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't90_125wm2'     => array(
      'title'       => __( 'T90 125W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't90_150wm2'     => array(
      'title'       => __( 'T90 > 150W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't130_ohne'     => array(
        'title'       => __( 'T130 Ohne Teilbeheizung', 'wpenon' ),
        'type'        => 'FLOAT',
      ),
    't130_5wm2'     => array(
      'title'       => __( 'T130 < 5W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't130_10wm2'     => array(
      'title'       => __( 'T130 10W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't130_25wm2'     => array(
      'title'       => __( 'T130 25W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't130_50wm2'     => array(
      'title'       => __( 'T130 50W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't130_75wm2'     => array(
      'title'       => __( 'T130 75W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't130_100wm2'     => array(
      'title'       => __( 'T130 100W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't130_125wm2'     => array(
      'title'       => __( 'T130 125W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    't130_150wm2'     => array(
      'title'       => __( 'T130 > 150W/m2', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
  ),
);