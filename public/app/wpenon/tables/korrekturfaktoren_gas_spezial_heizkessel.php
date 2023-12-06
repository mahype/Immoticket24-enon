<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Korrekturfaktoren für verschiedene Leistungsbereiche für Gas-Spezial-Heizkessel - Tabelle 84.
 */
return array(
	'title'         => __( 'Korrekturfaktoren Gas-Spezial-Heizkessel', 'wpenon' ),
	'description'   => __( 'Korrekturfaktoren für verschiedene Leistungsbereiche für Gas-Spezial-Heizkessel (Tabelle 84).', 'wpenon' ),
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
