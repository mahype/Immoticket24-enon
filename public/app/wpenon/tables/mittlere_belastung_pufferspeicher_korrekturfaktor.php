<?php

return array(
	'title'         => __( 'Mittlere Belastung Pufferspeicher Korrekturfaktor', 'wpenon' ),
	'description'   => __( 'Diese Tabelle enthält Korrekturfaktor (fßhs) für die mittlere Belastung für Pufferspeicher.', 'wpenon' ) . ' ' . __( 'Diese Daten werden für den Bedarfsausweis benötigt.', 'wpenon' ),
	'primary_field' => 'bezeichnung',
	'search_field'  => 'bezeichnung',
	'search_before' => true,
	'fields'        => array(
		'bezeichnung'  => array(
			'title' => __( 'Bezeichnung (intern)', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'bhs'          => array(
			'title' => __( 'Mittlere Belastung (bhs)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'beheizt_90'   => array(
			'title' => __( '90 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'unbeheizt_90' => array(
			'title' => __( '90 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'beheizt_70'   => array(
			'title' => __( '70 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'unbeheizt_70' => array(
			'title' => __( '70 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'beheizt_55'   => array(
			'title' => __( '55 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'unbeheizt_55' => array(
			'title' => __( '55 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'beheizt_35'   => array(
			'title' => __( '35 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'unbeheizt_35' => array(
			'title' => __( '35 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
	),
);
