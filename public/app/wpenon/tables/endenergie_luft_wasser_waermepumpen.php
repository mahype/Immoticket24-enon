<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Endenergie – Luft-Wasser-Wärmepumpen, Tabelle 93
 */
return array(
	'title'         => __( 'Berechnung Endenergie – Luft-Wasser-Wärmepumpen', 'wpenon' ),
	'description'   => __( 'Aufwandszahl eges – Luft-Wasser-Wärmepumpen', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'monat',
	'search_field'  => 'monat',
	'search_before' => true,
	'fields'        => array(
		'monat'   => array(
			'title' => __( 'Monat', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'lww_w_7' => array(
			'title' => __( 'lww w-7', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'lww_w2'  => array(
			'title' => __( 'lww w2', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'lww_w7'  => array(
			'title' => __( 'lww w7', 'wpenon' ),
			'type'  => 'FLOAT',
		),

	),
);
