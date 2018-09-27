<?php

return array(
  'title'         => __( 'Energieträger-Umrechnungsfaktoren', 'wpenon' ),
  'description'   => __( 'Diese Tabelle enthält Energieträger in unterschiedlichen Einheiten und ihre Umrechnungsfaktoren.', 'wpenon' ),
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
      'title'         => __( 'Energieträger / Einheit', 'wpenon' ),
      'type'          => 'VARCHAR(100)',
    ),
    'energietraeger'=> array(
      'title'         => __( 'Energieträger-Bezeichnung (intern)', 'wpenon' ),
      'type'          => 'VARCHAR(100)',
    ),
    'einheit'       => array(
      'title'         => __( 'Einheit', 'wpenon' ),
      'type'          => 'VARCHAR(20)',
    ),
    'mpk'           => array(
      'title'         => __( 'Umrechnungsfaktor', 'wpenon' ),
      'type'          => 'FLOAT',
    ),
  ),
);
