<?php

return array(
	'title'         => __( 'Mittlere Belastung Korrekturfaktor', 'wpenon' ),
	'description'   => __( 'Diese Tabelle enthält Korrekturfaktor fβ,d für die mittlere Belastung.', 'wpenon' ) . ' ' . __( 'Diese Daten werden für den Bedarfsausweis benötigt.', 'wpenon' ),
	'primary_field' => 'bezeichnung',
	'search_field'  => 'bezeichnung',
	'search_before' => true,
	'fields'        => array(
		'bezeichnung'       => array(
			'title' => __( 'Bezeichnung (intern)', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'name'              => array(
			'title' => __( 'Rohrnetztyp', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
        // 0,1
		'unbeheizt_9070_01' => array(
			'title' => __( '90/70 < 0,1 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'unbeheizt_7055_01' => array(
			'title' => __( '70/55 < 0,1 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'unbeheizt_5545_01' => array(
			'title' => __( '55/45 < 0,1 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'unbeheizt_3528_01' => array(
			'title' => __( '35/28 < 0,1 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'beheizt_9070_01' => array(
			'title' => __( '90/70 < 0,1 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'beheizt_7055_01' => array(
			'title' => __( '70/55 < 0,1 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'beheizt_5545_01' => array(
			'title' => __( '55/45 < 0,1 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'beheizt_3528_01' => array(
			'title' => __( '35/28 < 0,1 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        // 0,3
        'unbeheizt_9070_03' => array(
			'title' => __( '90/70 < 0,3 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'unbeheizt_7055_03' => array(
			'title' => __( '70/55 < 0,3 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'unbeheizt_5545_03' => array(
			'title' => __( '55/45 < 0,3 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'unbeheizt_3528_03' => array(
			'title' => __( '35/28 < 0,3 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'beheizt_9070_03' => array(
			'title' => __( '90/70 < 0,3 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'beheizt_7055_03' => array(
			'title' => __( '70/55 < 0,3 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'beheizt_5545_03' => array(
			'title' => __( '55/45 < 0,3 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'beheizt_3528_03' => array(
			'title' => __( '35/28 < 0,3 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        // 0,5
        'unbeheizt_9070_05' => array(
			'title' => __( '90/70 < 0,5 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'unbeheizt_7055_05' => array(
			'title' => __( '70/55 < 0,5 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'unbeheizt_5545_05' => array(
			'title' => __( '55/45 < 0,5 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'unbeheizt_3528_05' => array(
			'title' => __( '35/28 < 0,5 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'beheizt_9070_05' => array(
			'title' => __( '90/70 < 0,5 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'beheizt_7055_05' => array(
			'title' => __( '70/55 < 0,5 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'beheizt_5545_05' => array(
			'title' => __( '55/45 < 0,5 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'beheizt_3528_05' => array(
			'title' => __( '35/28 < 0,5 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        // 1,0
        'unbeheizt_9070_10' => array(
			'title' => __( '90/70 < 1,0 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'unbeheizt_7055_10' => array(
			'title' => __( '70/55 < 1,0 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'unbeheizt_5545_10' => array(
			'title' => __( '55/45 < 1,0 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'unbeheizt_3528_10' => array(
			'title' => __( '35/28 < 1,0 (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'beheizt_9070_10' => array(
			'title' => __( '90/70 < 1,0 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'beheizt_7055_10' => array(
			'title' => __( '70/55 < 1,0 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'beheizt_5545_10' => array(
			'title' => __( '55/45 < 1,0 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        'beheizt_3528_10' => array(
			'title' => __( '35/28 < 1,0 (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
        
	),
);
