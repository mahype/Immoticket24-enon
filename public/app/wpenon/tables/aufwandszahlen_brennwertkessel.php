<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Brennwertkessel, Tabelle 77
 */
return array(
	'title'         => __( 'Aufwandszahlen Brennwertkessel', 'wpenon' ),
	'description'   => __( 'Brennwertkessel verbessert (Baujahr ab 1999) – 70 °C/55 °C – Erdgas – Aufstellung im unbeheizten Raum (Tabelle 77).', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'bwk_pn_kw',
	'search_field'  => 'bwk_pn_kw',
	'search_before' => true,
	'fields'        => array(
		'bwk_pn_kw'   => array(
			'title' => __( 'Pn kW', 'wpenon' ),
			'type'  => 'INT',
		),
		'bwk_0_1' => array(
			'title' => __( 'b h,g 0,1', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bwk_0_2' => array(
			'title' => __( 'b h,g 0,2', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bwk_0_3' => array(
			'title' => __( 'b h,g 0,3', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bwk_0_4' => array(
			'title' => __( 'b h,g 0,4', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bwk_0_5' => array(
			'title' => __( 'b h,g 0,5', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bwk_0_6' => array(
			'title' => __( 'b h,g 0,6', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bwk_0_7' => array(
			'title' => __( 'b h,g 0,7', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bwk_0_8' => array(
			'title' => __( 'b h,g 0,8', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bwk_0_9' => array(
			'title' => __( 'b h,g 0,9', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bwk_1_0' => array(
			'title' => __( 'b h,g 1,0', 'wpenon' ),
			'type'  => 'FLOAT',
		),
	),
);
