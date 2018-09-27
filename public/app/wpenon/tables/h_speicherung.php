<?php

return array(
  'title'         => __( 'Heizenergie-Speicherung', 'wpenon' ),
  'description'   => __( 'Diese Tabelle enthält die standardisierten Wärmeverluste und Hilfsenergiewerte für die Heizenergie-Speicherung.', 'wpenon' ) . ' ' . __( 'Diese Daten werden für den Bedarfsausweis benötigt.', 'wpenon' ),
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
      'title'         => __( 'Speicherung', 'wpenon' ),
      'type'          => 'VARCHAR(100)',
    ),
    'wv150_1994'  => array(
      'title'       => __( 'WV<sup>1</sup> 150m&sup2; (bis 1994)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'wv500_1994'  => array(
      'title'       => __( 'WV<sup>1</sup> 500m&sup2; (bis 1994)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'wv2500_1994' => array(
      'title'       => __( 'WV<sup>1</sup> 2500m&sup2; (bis 1994)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he150_1994'  => array(
      'title'       => __( 'HE<sup>2</sup> 150m&sup2; (bis 1994)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he500_1994'  => array(
      'title'       => __( 'HE<sup>2</sup> 500m&sup2; (bis 1994)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he2500_1994' => array(
      'title'       => __( 'HE<sup>2</sup> 2500m&sup2; (bis 1994)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'wv150_1995'  => array(
      'title'       => __( 'WV<sup>1</sup> 150m&sup2; (ab 1995)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'wv500_1995'  => array(
      'title'       => __( 'WV<sup>1</sup> 500m&sup2; (ab 1995)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'wv2500_1995' => array(
      'title'       => __( 'WV<sup>1</sup> 2500m&sup2; (ab 1995)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he150_1995'  => array(
      'title'       => __( 'HE<sup>2</sup> 150m&sup2; (ab 1995)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he500_1995'  => array(
      'title'       => __( 'HE<sup>2</sup> 500m&sup2; (ab 1995)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he2500_1995' => array(
      'title'       => __( 'HE<sup>2</sup> 2500m&sup2; (ab 1995)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
  ),
);
