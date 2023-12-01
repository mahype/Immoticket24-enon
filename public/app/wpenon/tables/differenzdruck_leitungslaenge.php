<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * ∆p Differenzdruck in Abhängigkeit von maximaler Leitungslänge für Speicher und
 * Durchflusssystem - Tabelle 46.
 */
return array(
	'title'         => __( 'Differenzdruck Leitungslänge', 'wpenon' ),
	'description'   => __( '∆p Differenzdruck in Abhängigkeit von maximaler Leitungslänge für Speicher und      Durchflusssystem', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'lmax_m',
	'search_field'  => 'lmax_m',
	'search_before' => true,
	'fields'        => array(
		'lmax_m'           => array(
			'title' => __( 'l<sub>max</sub> m', 'wpenon' ),
			'type'  => 'int',
		),
		'speicher'         => array(
			'title' => __( 'Speicher kPa', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'durchflusssystem' => array(
			'title' => __( 'Durchflusssystem kPa', 'wpenon' ),
			'type'  => 'FLOAT',
		),
	),
);
