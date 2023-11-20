<?php

return array(
  'title'         => __( 'Warmeverlust Trinkwasserspeicher', 'wpenon' ),
  'description'   => __( 'Diese Tabelle enthält den die Werte für Wärmeverluste von Trinkwarmwasserspeichern (nach 1994) — indirekt beheizt (Tabelle 55 / 12).', 'wpenon' ) . ' ' . __( 'Diese Daten werden für den Bedarfsausweis benötigt.', 'wpenon' ),
  'primary_field' => 'bezeichnung',
  'search_field'  => 'bezeichnung',
  'search_before' => true,
  'fields'        => array(
    'bezeichnung'   => array(
        'title'         => __( 'Bezeichnung (intern)', 'wpenon' ),
        'type'          => 'VARCHAR(100)',
    ),
    'volumen'   => array(
        'title'         => __( 'Volumen', 'wpenon' ),
        'type'          => 'FLOAT',
    ),
    'beheizt_mit_zirkulation'  => array(
        'title'         => __( 'Beheizt mit Zirkulation', 'wpenon' ),
        'type'          => 'FLOAT',
    ),
    'beheizt_ohne_zirkulation'  => array(
        'title'         => __( 'Beheizt ohne Zirkulation', 'wpenon' ),
        'type'          => 'FLOAT',
    ),
    'unbeheizt_mit_zirkulation'  => array(
        'title'         => __( 'Unbeheizt mit Zirkulation', 'wpenon' ),
        'type'          => 'FLOAT',
    ),
    'unbeheizt_ohne_zirkulation'  => array(
        'title'         => __( 'Unbeheizt ohne Zirkulation', 'wpenon' ),
        'type'          => 'FLOAT',
    ),
  )
);