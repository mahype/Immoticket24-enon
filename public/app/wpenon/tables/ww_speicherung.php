<?php

return array(
  'title'         => __( 'Warmwasser-Speicherung', 'wpenon' ),
  'description'   => __( 'Diese Tabelle enthält die standardisierten Wärmeverluste, Heizwärmegewinne und Hilfsenergiewerte für die Warmwasser-Speicherung.', 'wpenon' ) . ' ' . __( 'Diese Daten werden für den Bedarfsausweis benötigt.', 'wpenon' ),
  'asterisks'     => array(
    'WV'              => __( 'Wärmeverluste', 'wpenon' ),
    'HWG'             => __( 'Heizwärmegewinne', 'wpenon' ),
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
      'title'         => __( 'Speicherung', 'wpenon' ),
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
    'hwg150'      => array(
      'title'       => __( 'HWG<sup>2</sup> 150m&sup2;', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'hwg500'      => array(
      'title'       => __( 'HWG<sup>2</sup> 500m&sup2;', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'hwg2500'     => array(
      'title'       => __( 'HWG<sup>2</sup> 2500m&sup2;', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he150'       => array(
      'title'       => __( 'HE<sup>3</sup> 150m&sup2;', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he500'       => array(
      'title'       => __( 'HE<sup>3</sup> 500m&sup2;', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he2500'      => array(
      'title'       => __( 'HE<sup>3</sup> 2500m&sup2;', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
  ),
);
