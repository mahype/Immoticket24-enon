<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Betriebsbereitschaftsleistung für Pellet- und Holzhackschnitzelkessel (aktuelle Standardwerte) – Hilfsenergieaufwand - Tabelle 87.
 */
return array(
	'title'         => __( 'Betriebsbereitschaftsleistung für Pellet- und Holzhackschnitzelkessel', 'wpenon' ),
	'description'   => __( 'Betriebsbereitschaftsleistung für Pellet- und Holzhackschnitzelkessel – Hilfsenergieaufwand', 'wpenon' ),
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
			'title' => __( 'Pn kW', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'pk_kw'       => array(
			'title' => __( 'Pelletkessel kW', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'hk_kw'       => array(
			'title' => __( 'Hackschnitzelkessel kW', 'wpenon' ),
			'type'  => 'FLOAT',
		),

	),
);
