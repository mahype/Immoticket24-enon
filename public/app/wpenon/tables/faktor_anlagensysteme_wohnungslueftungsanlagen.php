<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 *   Faktor für Anlagensysteme der Wohnungslüftungsanlagen  - Tabelle 121.
 */
return array(
	'title'         => __( 'Faktor für Anlagensysteme der Wohnungslüftungsanlagen ', 'wpenon' ),
	'description'   => __( 'Faktor für Anlagensysteme der Wohnungslüftungsanlagen', 'wpenon' ),
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
		'fsystem'     => array(
			'title' => __( 'fsystem', 'wpenon' ),
			'type'  => 'FLOAT',
		),

	),
);
