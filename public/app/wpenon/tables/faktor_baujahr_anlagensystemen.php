<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 *  Faktor für das Baujahr von Anlagensystemen  - Tabelle 122.
 */
return array(
	'title'         => __( 'Faktor für das Baujahr von Anlagensystemen ', 'wpenon' ),
	'description'   => __( 'Faktor für das Baujahr von Anlagensystemen der Wohnungslüftungsanlagen', 'wpenon' ),
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
			'title' => __( 'Anlage', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'Baujahr'     => array(
			'title' => __( 'Baujahr', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'fBaujahr'    => array(
			'title' => __( 'fBaujahr', 'wpenon' ),
			'type'  => 'FLOAT',
		),

	),
);
