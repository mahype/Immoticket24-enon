<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Differenzdruck Wärmeerzeuger - Tabelle 39.
 */
return array(
	'title'         => __( 'Differenzdruck Wärmeerzeuger', 'wpenon' ),
	'description'   => __( 'Differenzdruck Wärmeerzeuger: Gas-BW und Gas-NT < 35 kW für Heizkörper (HK) und  Fußbodenheizung (FBH)', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'id',
	'search_field'  => 'id',
	'search_before' => true,
	'fields'        => array(
		'id'      => array(
			'title' => __( 'ID', 'wpenon' ),
			'type'  => 'VARCHAR(200)',
		),
		'kw'      => array(
			'title' => __( 'kW', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'hk_15_k' => array(
			'title' => __( 'HK 15 K', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'hk_10_k' => array(
			'title' => __( 'HK 10 K', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'fbh'     => array(
			'title' => __( 'FBH', 'wpenon' ),
			'type'  => 'FLOAT',
		),
	),
);
