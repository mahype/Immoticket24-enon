<?php

return array(
  'title'         => __( 'Energieträger 2016', 'wpenon' ),
  'description'   => __( 'Diese Tabelle enthält Energieträger und ihre Primärenergiefaktoren (gültig ab 2016).', 'wpenon' ),
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
      'title'         => __( 'Energieträger', 'wpenon' ),
      'type'          => 'VARCHAR(100)',
    ),
    'primaer'     => array(
      'title'       => __( 'Primärenergiefaktor', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'co2'         => array(
      'title'       => __( 'CO<sub>2</sub>-Emissionsfaktor', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
  ),
);
