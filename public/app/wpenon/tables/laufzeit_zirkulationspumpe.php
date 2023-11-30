<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Laufzeit der Zirkulationspumpe - Tabelle 45.
 */
return array(
	'title'         => __( 'Laufzeit Zirkulationspumpe', 'wpenon' ),
	'description'   => __( 'Laufzeit der Zirkulationspumpe', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'angf_m2',
	'search_field'  => 'angf_m2',
	'search_before' => true,
	'fields'        => array(
		'id'      => array(
			'title' => __( 'ID', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'angf_m2' => array(
			'title' => __( 'A<sub>ngf</sub> m&sup2;', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'ehf'     => array(
			'title' => __( 'EFH z h/d', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'mfh'     => array(
			'title' => __( 'MFH z h/', 'wpenon' ),
			'type'  => 'FLOAT',
		),
	),
);
