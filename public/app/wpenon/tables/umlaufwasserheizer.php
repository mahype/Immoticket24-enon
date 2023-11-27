<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Umlaufwasserheizer, Tabelle 82
 */

return array(
	'title'         => __( 'Umlaufwasserheizer', 'wpenon' ),
	'description'   => __( 'Umlaufwasserheizer Baujahr 1987 bis 1994 — 70 °C/55 °C — Erdgas — Aufstellung im unbeheizten Raum', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'uwh_p',
	'search_field'  => 'uwh_p',
	'search_before' => true,
	'fields'        => array(
		'uwh_p'   => array(
			'title' => __( 'P', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'uwh_0_1' => array(
			'title' => __( 'b h,g 0,1', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'uwh_0_2' => array(
			'title' => __( 'b h,g 0,2', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'uwh_0_3' => array(
			'title' => __( 'b h,g 0,3', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'uwh_0_4' => array(
			'title' => __( 'b h,g 0,4', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'uwh_0_5' => array(
			'title' => __( 'b h,g 0,5', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'uwh_0_6' => array(
			'title' => __( 'b h,g 0,6', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'uwh_0_7' => array(
			'title' => __( 'b h,g 0,7', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'uwh_0_8' => array(
			'title' => __( 'b h,g 0,8', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'uwh_0_9' => array(
			'title' => __( 'b h,g 0,9', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'uwh_1_0' => array(
			'title' => __( 'b h,g 1,0', 'wpenon' ),
			'type'  => 'FLOAT',
		),
	),
);
