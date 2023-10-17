<?php

return array(
  'title'         => __( 'Ausnutzungsgrad', 'wpenon' ),
  'description'   => __( 'Diese Tabelle enthält den Ausnutzungsgrad für Standard-Zeitkonstanten', 'wpenon' ) . ' ' . __( 'Diese Daten werden für den Bedarfsausweis benötigt.', 'wpenon' ),
  'primary_field' => 'bezeichnung',
  'search_field'  => 'bezeichnung',
  'search_before' => true,
  'fields'        => array(
    'bezeichnung'   => array(
        'title'         => __( 'Bezeichnung (intern)', 'wpenon' ),
        'type'          => 'VARCHAR(100)',
    ),
    'y'   => array(
        'title'         => __( 'y', 'wpenon' ),
        'type'          => 'FLOAT',
    ),
    't30'     => array(
        'title'       => __( 'T 30', 'wpenon' ),
        'type'        => 'FLOAT',
    ),
    't40'     => array(
        'title'       => __( 'T 40', 'wpenon' ),
        'type'        => 'FLOAT',
    ),
    't50'     => array(
        'title'       => __( 'T 50', 'wpenon' ),
        'type'        => 'FLOAT',
    ),
    't60'     => array(
        'title'       => __( 'T 60', 'wpenon' ),
        'type'        => 'FLOAT',
    ),
    't70'     => array(
        'title'       => __( 'T 70', 'wpenon' ),
        'type'        => 'FLOAT',
    ),
    't80'     => array(
        'title'       => __( 'T 80', 'wpenon' ),
        'type'        => 'FLOAT',
    ),
    't90'     => array(
        'title'       => __( 'T 90', 'wpenon' ),
        'type'        => 'FLOAT',
    ),
    't100'     => array(
        'title'       => __( 'T 100', 'wpenon' ),
        'type'        => 'FLOAT',
    ),
    't110'     => array(
        'title'       => __( 'T 110', 'wpenon' ),
        'type'        => 'FLOAT',
    ),
    't120'     => array(
        'title'       => __( 'T 120', 'wpenon' ),
        'type'        => 'FLOAT',
    ),
    't130'     => array(
        'title'       => __( 'T 130', 'wpenon' ),
        'type'        => 'FLOAT',
    ),
    't140'     => array(
        'title'       => __( 'T 140', 'wpenon' ),
        'type'        => 'FLOAT',
    ),
    't150'     => array(
        'title'       => __( 'T 150', 'wpenon' ),
        'type'        => 'FLOAT',
    ),
  ),
);