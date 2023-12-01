<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Hilfsenergieaufwand für die Ladepumpe von Trinkwarmwasser-Speichern - Tabelle 58.
 */
return array(
	'title'         => __( 'Hilfsenergieaufwand Ladepumpe', 'wpenon' ),
	'description'   => __( 'Hilfsenergieaufwand für die Ladepumpe von Trinkwarmwasser-Speichern', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'speicher',
	'search_field'  => 'speicher',
	'search_before' => true,
	'fields'        => array(
		'speicher' => array(
			'title' => __( 'Speichervolumen V<sub>s</sub> l', 'wpenon' ),
			'type'  => 'INT',
		),
		'pumpe'     => array(
			'title' => __( 'Pumpenleistung P<sub>Pu</sub> W', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'energie'     => array(
			'title' => __( 'Energieaufwand  W<sub>w,s,0</sub> kWh/a', 'wpenon' ),
			'type'  => 'FLOAT',
		),
	),
);
