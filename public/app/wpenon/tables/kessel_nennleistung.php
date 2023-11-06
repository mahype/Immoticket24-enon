<?php

return array(
  'title'         => __( 'Kessel Nennleistung', 'wpenon' ),
  'description'   => __( 'Diese Tabelle enthält die Kessel-Nennleistung zur Trinkwasserwärmung (Tabelle 139).', 'wpenon' ),
  'primary_field' => 'bezeichnung',
  'search_field'  => 'bezeichnung',
  'search_before' => true,
  'fields'        => array(
    'bezeichnung'   => array(
      'title'         => __( 'Bezeichnung (intern)', 'wpenon' ),
      'type'          => 'VARCHAR(100)',
    ),
    'name'          => array(
      'title'         => __( 'Nettogrundflaeche des Wohngebaeudes', 'bieaw' ),
      'type'          => 'VARCHAR(100)',
    ),
    'kwh_160' => array(
      'title'       => __( 'kWh 16', 'wpenon' ),
      'type'        => 'FLOAT'
    ),
    'kwh_155' => array(
        'title'       => __( 'kWh 15.5', 'wpenon' ),
        'type'        => 'FLOAT'
    ),
    'kwh_150' => array(
        'title'       => __( 'kWh 15', 'wpenon' ),
        'type'        => 'FLOAT'
    ),
    'kwh_145' => array(
        'title'       => __( 'kWh 14.5', 'wpenon' ),
        'type'        => 'FLOAT'
    ),
    'kwh_140' => array(
        'title'       => __( 'kWh 14', 'wpenon' ),
        'type'        => 'FLOAT'
    ),
    'kwh_135' => array(
        'title'       => __( 'kWh 13.5', 'wpenon' ),
        'type'        => 'FLOAT'
    ),
    'kwh_130' => array(
        'title'       => __( 'kWh 13', 'wpenon' ),
        'type'        => 'FLOAT'
    ),
    'kwh_125' => array(
        'title'       => __( 'kWh 12.5', 'wpenon' ),
        'type'        => 'FLOAT'
    ),
    'kwh_120' => array(
        'title'       => __( 'kWh 12', 'wpenon' ),
        'type'        => 'FLOAT'
    ),
    'kwh_115' => array(
        'title'       => __( 'kWh 11.5', 'wpenon' ),
        'type'        => 'FLOAT'
    ),
    'kwh_110' => array(
        'title'       => __( 'kWh 11', 'wpenon' ),
        'type'        => 'FLOAT'
    ),
    'kwh_105' => array(
        'title'       => __( 'kWh 10.5', 'wpenon' ),
        'type'        => 'FLOAT'
    ),
    'kwh_100' => array(
        'title'       => __( 'kWh 10', 'wpenon' ),
        'type'        => 'FLOAT'
    ),
    'kwh_95' => array(
        'title'       => __( 'kWh 9.5', 'wpenon' ),
        'type'        => 'FLOAT'
    ),
    'kwh_90' => array(
        'title'       => __( 'kWh 9', 'wpenon' ),
        'type'        => 'FLOAT'
    ),
    'kwh_85' => array(
        'title'       => __( 'kWh 8.5', 'wpenon' ),
        'type'        => 'FLOAT'
    ),
  ),
);
