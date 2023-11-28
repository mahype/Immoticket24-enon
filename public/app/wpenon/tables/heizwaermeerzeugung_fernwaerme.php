<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Heizwärmeerzeugung Fernwärme - Tabelle 102.
 */
return array(
	'title'         => __( 'Aufwandszahlen Heizwärmeerzeugung Fernwärme', 'wpenon' ),
	'description'   => __( 'Fern- und Nahwärme-Hausstation, Sekundärseite 70 °C/55 °C, Aufstellung im unbeheizten Bereich, Dämmklasse nach DIN EN 12828, Sekundärseite 4 und Primärseite 5', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'bezeichnung',
	'search_field'  => 'bezeichnung',
	'search_before' => true,
	'fields'        => array(
		'bezeichnung' => array(
			'title' => __( 'kW', 'wpenon' ),
			'type'  => 'INT',
		),
		'bhg_0_1'     => array(
			'title' => __( 'b h,g 0,1', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_0_2'     => array(
			'title' => __( 'b h,g 0,2', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_0_3'     => array(
			'title' => __( 'b h,g 0,3', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_0_4'     => array(
			'title' => __( 'b h,g 0,4', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_0_5'     => array(
			'title' => __( 'b h,g 0,5', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_0_6'     => array(
			'title' => __( 'b h,g 0,6', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_0_7'     => array(
			'title' => __( 'b h,g 0,7', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_0_8'     => array(
			'title' => __( 'b h,g 0,8', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_0_9'     => array(
			'title' => __( 'b h,g 0,9', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_1_0'     => array(
			'title' => __( 'b h,g 1,0', 'wpenon' ),
			'type'  => 'FLOAT',
		),


	),
);
