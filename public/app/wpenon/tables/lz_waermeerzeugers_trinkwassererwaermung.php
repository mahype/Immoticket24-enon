<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Jährliche Laufzeit des Wärmeerzeugers zur Trinkwassererwärmung - Tabelle 140.
 */
return array(
	'title'         => __( 'Laufzeit des Wärmeerzeugers zur Trinkwassererwärmung', 'wpenon' ),
	'description'   => __( 'Jährliche Laufzeit des Wärmeerzeugers zur Trinkwassererwärmung', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'id',
	'search_field'  => 'id',
	'search_before' => true,
	'fields'        => array(
		'id'          => array(
			'title' => __( 'ID', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'bezeichnung' => array(
			'title' => __( 'Bezeichnung', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'zuschlag'    => array(
			'title' => __( 'Zuschlagsfaktor fz', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'aufwand_1'   => array(
			'title' => __( 'Aufwandszahl 1', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'aufwand_1_2' => array(
			'title' => __( 'Aufwandszahl 1,2', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'aufwand_2'   => array(
			'title' => __( 'Aufwandszahl 2', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'aufwand_2_5' => array(
			'title' => __( 'Aufwandszahl 2,5', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'aufwand_3'   => array(
			'title' => __( 'Aufwandszahl 3', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'aufwand_3_5' => array(
			'title' => __( 'Aufwandszahl 3,5', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'aufwand_4'   => array(
			'title' => __( 'Aufwandszahl 4', 'wpenon' ),
			'type'  => 'FLOAT',
		),
	),
);
