<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * TERMPumpe - Tabelle 40.
 */
return array(
	'title'         => __( 'TERMPumpe', 'wpenon' ),
	'description'   => __( 'TERMPumpe in Abhängigkeit vom Belastungsgrad und der Pumpenregelung', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'id',
	'search_field'  => 'id',
	'search_before' => true,
	'fields'        => array(
		'id'      => array(
			'title' => __( 'ID', 'wpenon' ),
			'type'  => 'VARCHAR(200)',
		),
		'b'      => array(
			'title' => __( 'Belastungsgrad ß', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'd_p_var' => array(
			'title' => __( 'Δp <sub>variabel</sub>', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'd_p_const' => array(
			'title' => __( 'Δp <sub>const</sub>', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'ungeregelt'     => array(
			'title' => __( 'ungeregelt', 'wpenon' ),
			'type'  => 'FLOAT',
		),
	),
);
