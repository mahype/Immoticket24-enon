<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 *  Brennwertgeräte - Korrekturfaktoren Aufwandszahlen Heizwärmeerzeugung
 *  Tabelle 79, 80 und 81
 * 
 *   bh - beheizt
 *  ubh - unbeheizt
 * 
 */
return array(
	'title'         => __( 'Aufwandszahlen Heizwärmeerzeugung', 'wpenon' ),
	'description'   => __( 'Korrekturfaktoren Aufwandszahlen Heizwärmeerzeugung (Tabelle 79, 80 und 81 zusammengefasst).', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'bezeichnung',
	'search_field'  => 'bezeichnung',
	'search_before' => true,
	'fields'        => array(
		'bezeichnung' => array(
			'title' => __( 'Auslegungs-temperaturen', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'ubh_0_1'     => array(
			'title' => __( 'b h,g 0,1 (ubh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'ubh_0_2'     => array(
			'title' => __( 'b h,g 0,2 (ubh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'ubh_0_3'     => array(
			'title' => __( 'b h,g 0,3 (ubh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'ubh_0_4'     => array(
			'title' => __( 'b h,g 0,4 (ubh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'ubh_0_5'     => array(
			'title' => __( 'b h,g 0,5 (ubh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'ubh_0_6'     => array(
			'title' => __( 'b h,g 0,6 (ubh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'ubh_0_7'     => array(
			'title' => __( 'b h,g 0,7 (ubh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'ubh_0_8'     => array(
			'title' => __( 'b h,g 0,8 (ubh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'ubh_0_9'     => array(
			'title' => __( 'b h,g 0,9 (ubh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'ubh_1_0'     => array(
			'title' => __( 'b h,g 1,0 (ubh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bh_0_1'      => array(
			'title' => __( 'b h,g 0,1 (bh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bh_0_2'      => array(
			'title' => __( 'b h,g 0,2 (bh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bh_0_3'      => array(
			'title' => __( 'b h,g 0,3 (bh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bh_0_4'      => array(
			'title' => __( 'b h,g 0,4 (bh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bh_0_5'      => array(
			'title' => __( 'b h,g 0,5 (bh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bh_0_6'      => array(
			'title' => __( 'b h,g 0,6 (bh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bh_0_7'      => array(
			'title' => __( 'b h,g 0,7 (bh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bh_0_8'      => array(
			'title' => __( 'b h,g 0,8 (bh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bh_0_9'      => array(
			'title' => __( 'b h,g 0,9 (bh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bh_1_0'      => array(
			'title' => __( 'b h,g 1,0 (bh)', 'wpenon' ),
			'type'  => 'FLOAT',
		),

	),
);
