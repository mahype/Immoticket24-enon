<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Tabelle 90 — Korrekturfaktor fΔθ für unterschiedliche Temperaturdifferenzen bei
 * Messung und Betrieb der Wärmepumpe
 */

return array(
	'title'         => __( 'COP Werte Korrektur', 'wpenon' ),
	'description'   => __( 'Korrekturfaktor fΔθ für unterschiedliche Temperaturdifferenzen bei Messung und Betrieb der Wärmepumpe', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'k',
	'search_field'  => 'k',
	'search_before' => true,
	'fields'        => array(
		'k' => array(
			'title' => __( 'Betrieb Δθop K', 'wpenon' ),
			'type'  => 'INT',
		),
		'k_3'     => array(
			'title' => __( 'K 3', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'k_4'     => array(
			'title' => __( 'K 4', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'k_5'     => array(
			'title' => __( 'K 5', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'k_6'     => array(
			'title' => __( 'K 6', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'k_7'     => array(
			'title' => __( 'K 7', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'k_8'     => array(
			'title' => __( 'K 8', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'k_9'     => array(
			'title' => __( 'K 9', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'k_10'    => array(
			'title' => __( 'K 10', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'k_11'    => array(
			'title' => __( 'K 11', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'k_12'    => array(
			'title' => __( 'K 12', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'k_13'    => array(
			'title' => __( 'K 13', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'k_14'    => array(
			'title' => __( 'K 14', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'k_15'    => array(
			'title' => __( 'K 15', 'wpenon' ),
			'type'  => 'FLOAT',
		),

	),
);
