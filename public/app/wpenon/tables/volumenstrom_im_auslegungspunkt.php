<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Volumenstrom im Auslegungspunkt - Tabelle 38.
 */
return array(
	'title'         => __( 'Volumenstrom im Auslegungspunkt', 'wpenon' ),
	'description'   => __( 'Volumenstrom im Auslegungspunkt in m3/h für Heizkörper (HK) und Fußbodenheizung (FBH)', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'id',
	'search_field'  => 'id',
	'search_before' => true,
	'fields'        => array(
		'id'      => array(
			'title' => __( 'ID', 'wpenon' ),
			'type'  => 'VARCHAR(200)',
		),
		'kw'      => array(
			'title' => __( 'kW', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'hk_15_k' => array(
			'title' => __( 'HK 15 K', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'hk_10_k' => array(
			'title' => __( 'HK 10 K', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'fbh_7_K'     => array(
			'title' => __( 'FBH 7 K', 'wpenon' ),
			'type'  => 'FLOAT',
		),
	),
);
