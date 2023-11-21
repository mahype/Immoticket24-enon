<?php

return array(
	'title'         => __( 'Thermische Solaranlagen', 'wpenon' ),
	'description'   => __( 'Diese Tabelle enthält den Kenngrößen thermischer Solaranlagen – Trinkwassererwärmung mit Zirkulation – Verteilung im beheizten Raum', 'wpenon' ) . ' ' . __( 'Diese Daten werden für den Bedarfsausweis benötigt.', 'wpenon' ),
	'primary_field' => 'bezeichnung',
	'search_field'  => 'bezeichnung',
	'search_before' => true,
	'fields'        => array(
		'bezeichnung' => array(
			'title' => __( 'Bezeichnung (intern)', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'a'           => array(
			'title' => __( 'Flaeche', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'unbeheizt_vs_sol'           => array(
			'title' => __( 'Vs,sol (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'unbeheizt_vs_aux'           => array(
			'title' => __( 'Vs,aux (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'unbeheizt_vs_ges'           => array(
			'title' => __( 'Vs,ges (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'unbeheizt_flach_a'           => array(
			'title' => __( 'Flachkollektoren A (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'unbeheizt_flach_q'           => array(
			'title' => __( 'Flachkollektoren Q (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'unbeheizt_roehren_a'           => array(
			'title' => __( 'Flachkollektoren A (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'unbeheizt_roehren_q'           => array(
			'title' => __( 'Roehrenkollektoren Q (unbeheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'beheizt_vs_sol'           => array(
			'title' => __( 'Vs,sol (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'beheizt_vs_aux'           => array(
			'title' => __( 'Vs,aux (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'beheizt_vs_ges'           => array(
			'title' => __( 'Vs,ges (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'beheizt_flach_a'           => array(
			'title' => __( 'Flachkollektoren A (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'beheizt_flach_q'           => array(
			'title' => __( 'Flachkollektoren Q (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'beheizt_roehren_a'           => array(
			'title' => __( 'Flachkollektoren A (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'beheizt_roehren_q'           => array(
			'title' => __( 'Roehrenkollektoren Q (beheizt)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
	),
);
