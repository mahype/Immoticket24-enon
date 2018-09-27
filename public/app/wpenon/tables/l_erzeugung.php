<?php

return array(
  'title'         => __( 'Lüftungsanlagen', 'wpenon' ),
  'description'   => __( 'Diese Tabelle enthält die standardisierten Heizwärmegewinne und Hilfsenergiewerte für diverse Lüftungsanlagen.', 'wpenon' ),
  'asterisks'     => array(
    'HWG'             => __( 'Heizwärmegewinne', 'wpenon' ),
    'HE'              => __( 'Hilfsenergiebedarf', 'wpenon' ),
  ),
  'primary_field' => 'bezeichnung',
  'search_field'  => 'bezeichnung',
  'search_before' => true,
  'fields'        => array(
    'bezeichnung'   => array(
      'title'         => __( 'Bezeichnung (intern)', 'bieaw' ),
      'type'          => 'VARCHAR(100)',
    ),
    'name'          => array(
      'title'         => __( 'Anlage', 'bieaw' ),
      'type'          => 'VARCHAR(100)',
    ),
    'hwg150_1989' => array(
      'title'       => __( 'HWG<sup>1</sup> 150m&sup2; (bis 1989)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'hwg500_1989' => array(
      'title'       => __( 'HWG<sup>1</sup> 500m&sup2; (bis 1989)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'hwg2500_1989'=> array(
      'title'       => __( 'HWG<sup>1</sup> 2500m&sup2; (bis 1989)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'he150_1989'  => array(
      'title'       => __( 'HE<sup>2</sup> 150m&sup2; (bis 1989)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'he500_1989'  => array(
      'title'       => __( 'HE<sup>2</sup> 500m&sup2; (bis 1989)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'he2500_1989' => array(
      'title'       => __( 'HE<sup>2</sup> 2500m&sup2; (bis 1989)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'hwg150_1994' => array(
      'title'       => __( 'HWG<sup>1</sup> 150m&sup2; (bis 1994)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'hwg500_1994' => array(
      'title'       => __( 'HWG<sup>1</sup> 500m&sup2; (bis 1994)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'hwg2500_1994'=> array(
      'title'       => __( 'HWG<sup>1</sup> 2500m&sup2; (bis 1994)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'he150_1994'  => array(
      'title'       => __( 'HE<sup>2</sup> 150m&sup2; (bis 1994)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'he500_1994'  => array(
      'title'       => __( 'HE<sup>2</sup> 500m&sup2; (bis 1994)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'he2500_1994' => array(
      'title'       => __( 'HE<sup>2</sup> 2500m&sup2; (bis 1994)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'hwg150_1995' => array(
      'title'       => __( 'HWG<sup>1</sup> 150m&sup2; (ab 1995)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'hwg500_1995' => array(
      'title'       => __( 'HWG<sup>1</sup> 500m&sup2; (ab 1995)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'hwg2500_1995'=> array(
      'title'       => __( 'HWG<sup>1</sup> 2500m&sup2; (ab 1995)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'he150_1995'  => array(
      'title'       => __( 'HE<sup>2</sup> 150m&sup2; (ab 1995)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'he500_1995'  => array(
      'title'       => __( 'HE<sup>2</sup> 500m&sup2; (ab 1995)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
    'he2500_1995' => array(
      'title'       => __( 'HE<sup>2</sup> 2500m&sup2; (ab 1995)', 'bieaw' ),
      'type'        => 'FLOAT',
    ),
  ),
);
