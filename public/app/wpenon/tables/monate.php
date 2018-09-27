<?php

return array(
  'title'         => __( 'Monatsdaten', 'wpenon' ),
  'description'   => __( 'Diese Tabelle enthält die standardisierten Daten für die unterschiedlichen Monate des Jahres.', 'wpenon' ) . ' ' . __( 'Diese Daten werden für den Bedarfsausweis benötigt.', 'wpenon' ),
  'asterisks'     => array(),
  'primary_field' => 'bezeichnung',
  'search_field'  => 'bezeichnung',
  'search_before' => true,
  'fields'        => array(
    'bezeichnung'   => array(
      'title'         => __( 'Bezeichnung (intern)', 'wpenon' ),
      'type'          => 'VARCHAR(100)',
    ),
    'name'          => array(
      'title'         => __( 'Monatsname', 'wpenon' ),
      'type'          => 'VARCHAR(100)',
    ),
    'tage'        => array(
      'title'       => __( 'Anzahl Tage', 'wpenon' ),
      'type'        => 'INT',
    ),
    'temperatur'  => array(
      'title'       => __( 'Durchschnittstemperatur', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_0'         => array(
      'title'       => __( 'Str. horizontal', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_s30'       => array(
      'title'       => __( 'Str. Süd 30°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_s45'       => array(
      'title'       => __( 'Str. Süd 45°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_s60'       => array(
      'title'       => __( 'Str. Süd 60°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_s90'       => array(
      'title'       => __( 'Str. Süd 90°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_so30'       => array(
      'title'       => __( 'Str. Südost 30°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_so45'       => array(
      'title'       => __( 'Str. Südost 45°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_so60'       => array(
      'title'       => __( 'Str. Südost 60°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_so90'       => array(
      'title'       => __( 'Str. Südost 90°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_sw30'       => array(
      'title'       => __( 'Str. Südwest 30°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_sw45'       => array(
      'title'       => __( 'Str. Südwest 45°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_sw60'       => array(
      'title'       => __( 'Str. Südwest 60°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_sw90'       => array(
      'title'       => __( 'Str. Südwest 90°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_o30'       => array(
      'title'       => __( 'Str. Ost 30°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_o45'       => array(
      'title'       => __( 'Str. Ost 45°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_o60'       => array(
      'title'       => __( 'Str. Ost 60°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_o90'       => array(
      'title'       => __( 'Str. Ost 90°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_w30'       => array(
      'title'       => __( 'Str. West 30°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_w45'       => array(
      'title'       => __( 'Str. West 45°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_w60'       => array(
      'title'       => __( 'Str. West 60°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_w90'       => array(
      'title'       => __( 'Str. West 90°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_nw30'       => array(
      'title'       => __( 'Str. Nordwest 30°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_nw45'       => array(
      'title'       => __( 'Str. Nordwest 45°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_nw60'       => array(
      'title'       => __( 'Str. Nordwest 60°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_nw90'       => array(
      'title'       => __( 'Str. Nordwest 90°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_no30'       => array(
      'title'       => __( 'Str. Nordost 30°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_no45'       => array(
      'title'       => __( 'Str. Nordost 45°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_no60'       => array(
      'title'       => __( 'Str. Nordost 60°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_no90'       => array(
      'title'       => __( 'Str. Nordost 90°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_n30'       => array(
      'title'       => __( 'Str. Nord 30°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_n45'       => array(
      'title'       => __( 'Str. Nord 45°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_n60'       => array(
      'title'       => __( 'Str. Nord 60°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'w_n90'       => array(
      'title'       => __( 'Str. Nord 90°', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
  ),
);
