<?php

return array(
  'title'         => __( 'Warmwasseranlagen', 'wpenon' ),
  'description'   => __( 'Diese Tabelle enthält die standardisierten Anlagen-Aufwandszahlen (E<sub>P</sub>), Heizwärmegewinne und Hilfsenergiewerte für diverse Warmwasseranlagen.', 'wpenon' ),
  'asterisks'     => array(
    'E<sub>P</sub>'   => __( 'Anlagen-Aufwandszahl', 'wpenon' ),
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
      'title'         => __( 'Anlage', 'wpenon' ),
      'type'          => 'VARCHAR(100)',
    ),
    'typ'           => array(
      'title'         => __( 'Typ', 'wpenon' ),
      'type'          => 'VARCHAR(50)',
    ),
    'speicher'      => array(
      'title'         => __( 'Speicher (intern)', 'wpenon' ),
      'type'          => 'VARCHAR(100)',
    ),
    'ep150_1986'  => array(
      'title'       => __( 'E<sub>P</sub><sup>1</sup> 150m&sup2; (bis 1986)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'ep500_1986'  => array(
      'title'       => __( 'E<sub>P</sub><sup>1</sup> 500m&sup2; (bis 1986)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'ep2500_1986' => array(
      'title'       => __( 'E<sub>P</sub><sup>1</sup> 2500m&sup2; (bis 1986)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'hwg150_1986' => array(
      'title'       => __( 'HWG<sup>2</sup> 150m&sup2; (bis 1986)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'hwg500_1986' => array(
      'title'       => __( 'HWG<sup>2</sup> 500m&sup2; (bis 1986)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'hwg2500_1986'=> array(
      'title'       => __( 'HWG<sup>2</sup> 2500m&sup2; (bis 1986)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he150_1986'  => array(
      'title'       => __( 'HE<sup>3</sup> 150m&sup2; (bis 1986)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he500_1986'  => array(
      'title'       => __( 'HE<sup>3</sup> 500m&sup2; (bis 1986)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he2500_1986' => array(
      'title'       => __( 'HE<sup>3</sup> 2500m&sup2; (bis 1986)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'ep150_1994'  => array(
      'title'       => __( 'E<sub>P</sub><sup>1</sup> 150m&sup2; (bis 1994)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'ep500_1994'  => array(
      'title'       => __( 'E<sub>P</sub><sup>1</sup> 500m&sup2; (bis 1994)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'ep2500_1994' => array(
      'title'       => __( 'E<sub>P</sub><sup>1</sup> 2500m&sup2; (bis 1994)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'hwg150_1994' => array(
      'title'       => __( 'HWG<sup>2</sup> 150m&sup2; (bis 1994)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'hwg500_1994' => array(
      'title'       => __( 'HWG<sup>2</sup> 500m&sup2; (bis 1994)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'hwg2500_1994'=> array(
      'title'       => __( 'HWG<sup>2</sup> 2500m&sup2; (bis 1994)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he150_1994'  => array(
      'title'       => __( 'HE<sup>3</sup> 150m&sup2; (bis 1994)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he500_1994'  => array(
      'title'       => __( 'HE<sup>3</sup> 500m&sup2; (bis 1994)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he2500_1994' => array(
      'title'       => __( 'HE<sup>3</sup> 2500m&sup2; (bis 1994)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'ep150_1995'  => array(
      'title'       => __( 'E<sub>P</sub><sup>1</sup> 150m&sup2; (ab 1995)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'ep500_1995'  => array(
      'title'       => __( 'E<sub>P</sub><sup>1</sup> 500m&sup2; (ab 1995)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'ep2500_1995' => array(
      'title'       => __( 'E<sub>P</sub><sup>1</sup> 2500m&sup2; (ab 1995)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'hwg150_1995' => array(
      'title'       => __( 'HWG<sup>2</sup> 150m&sup2; (ab 1995)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'hwg500_1995' => array(
      'title'       => __( 'HWG<sup>2</sup> 500m&sup2; (ab 1995)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'hwg2500_1995'=> array(
      'title'       => __( 'HWG<sup>2</sup> 2500m&sup2; (ab 1995)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he150_1995'  => array(
      'title'       => __( 'HE<sup>3</sup> 150m&sup2; (ab 1995)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he500_1995'  => array(
      'title'       => __( 'HE<sup>3</sup> 500m&sup2; (ab 1995)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
    'he2500_1995' => array(
      'title'       => __( 'HE<sup>3</sup> 2500m&sup2; (ab 1995)', 'wpenon' ),
      'type'        => 'FLOAT',
    ),
  ),
);
