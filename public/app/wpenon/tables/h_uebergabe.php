<?php

return array(
  'title'         => __( 'Heizenergie-Übergabe', 'wpenon' ),
  'description'   => __( 'Diese Tabelle enthält die standardisierten Wärmeverluste und Hilfsenergiewerte für die Übergabe der Heizung.', 'wpenon' ) . ' ' . __( 'Diese Daten werden für den Bedarfsausweis benötigt.', 'wpenon' ),
  'asterisks'     => array(
    'WV'              => __( 'Wärmeverluste', 'wpenon' ),
    'HE'              => __( 'Hilfsenergiebedarf', 'wpenon' ),
  ),
  'primary_field' => 'bezeichnung',
  'search_field'  => 'bezeichnung',
  'search_before' => true,
  'fields'        => array(
    'bezeichnung'   => array(
      'title'         => __( 'Bezeichnung (intern)', 'wpenon' ),
      'type'          => 'VARCHAR(100)',
    ),
    'name'          => array(
      'title'         => __( 'Übergabe', 'wpenon' ),
      'type'          => 'VARCHAR(100)',
    ),
    'wv150'       => array(
      'title'       => __( 'WV<sup>1</sup> 150m&sup2;', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'wv500'       => array(
      'title'       => __( 'WV<sup>1</sup> 500m&sup2;', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'wv2500'      => array(
      'title'       => __( 'WV<sup>1</sup> 2500m&sup2;', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
  ),
);
