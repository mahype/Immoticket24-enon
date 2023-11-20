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
		'vs_sol'           => array(
			'title' => __( 'Vs,sol', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'vs_aux'           => array(
			'title' => __( 'Vs,aux', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'vs_ges'           => array(
			'title' => __( 'Vs,ges', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'flach_a'           => array(
			'title' => __( 'Flachkollektoren A', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'flach_q'           => array(
			'title' => __( 'Flachkollektoren Q', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'roehren_a'           => array(
			'title' => __( 'Flachkollektoren A', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'roehren_q'           => array(
			'title' => __( 'Roehrenkollektoren Q', 'wpenon' ),
			'type'  => 'FLOAT',
		),
	),
);
