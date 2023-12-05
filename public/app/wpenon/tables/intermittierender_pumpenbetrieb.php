<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Faktor für intermittierenden Pumpenbetrieb (Absenkbetrieb) - Tabelle 41.
 */
return array(
	'title'         => __( 'Intermittierender Pumpenbetrieb', 'wpenon' ),
	'description'   => __( 'Faktor für intermittierenden Pumpenbetrieb (Absenkbetrieb)', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'bezeichnung',
	'search_field'  => 'bezeichnung',
	'search_before' => true,
	'fields'        => array(
		'bezeichnung'   => array(
			'title'         => __( 'Bezeichnung (intern)', 'wpenon' ),
			'type'          => 'VARCHAR(100)',
		),
		'th'      => array(
			'title' => __( 'th', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'f_int'      => array(
			'title' => __( 'f_int', 'wpenon' ),
			'type'  => 'FLOAT',
		)
	),
);
