<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Tabelle 89 — COP – Werte – Wärmequellen/Vorlauftemperatur/Temperaturklassen
 */

 return array(
	'title'         => __( 'COP Werte', 'wpenon' ),
	'description'   => __( 'COP – Werte – Wärmequellen/Vorlauftemperatur/Temperaturklassen', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'vorlauftemperatur',
	'search_field'  => 'vorlauftemperatur',
	'search_before' => true,
	'fields'        => array(
		'vorlauftemperatur'   => array(
			'title' => __( 'Vorlauftemperatur °C', 'wpenon' ),
			'type'  => 'INT',
		),
		'lww_w_7' => array(
			'title' => __( 'lww w-7', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'lww_w2' => array(
			'title' => __( 'lww w2', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'lww_w7' => array(
			'title' => __( 'lww w7', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'sww' => array(
			'title' => __( 'sww °C', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'www' => array(
			'title' => __( 'www °C', 'wpenon' ),
			'type'  => 'FLOAT',
		),
	
	),
);
