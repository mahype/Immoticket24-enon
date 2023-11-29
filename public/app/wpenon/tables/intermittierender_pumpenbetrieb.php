<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Faktor für intermittierenden Pumpenbetrieb (Absenkbetrieb) - Tabelle 41.
 */
return array(
	'title'         => __( 'Intermittierender Pumpenbetrieb', 'wpenon' ),
	'description'   => __( 'Faktor für intermittierenden Pumpenbetrieb (Absenkbetrieb)', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'th',
	'search_field'  => 'th',
	'search_before' => true,
	'fields'        => array(
		'th'      => array(
			'title' => __( 'th', 'wpenon' ),
			'type'  => 'VARCHAR(200)',
		),
		'f_int'      => array(
			'title' => __( 'f_int', 'wpenon' ),
			'type'  => 'FLOAT',
		)
	),
);
