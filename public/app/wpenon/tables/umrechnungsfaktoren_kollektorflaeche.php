<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Umrechnungsfaktoren für Kollektorfläche, solarem Ertrag und Deckungsanteil
 * Tabelle 63
 * Tabelle 64
 * Tabelle 65
 */
return array(
	'title'         => __( 'Umrechnungsfaktoren für Kollektorfläche', 'wpenon' ),
	'description'   => __( 'Umrechnungsfaktoren für Kollektorfläche, solarem Ertrag und Deckungsanteil', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'bezeichnung',
	'search_field'  => 'bezeichnung',
	'search_before' => true,
	'fields'        => array(
		'bezeichnung'       => array(
			'title' => __( 'Bezeichnung', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'Himmelsrichtung'   => array(
			'title' => __( 'Himmelsrichtung', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'faktor'            => array(
			'title' => __( 'Faktor', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'0_grad_ab_1999'    => array(
			'title' => __( '0 Grad (ab  1999)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'30_grad_ab_1999'   => array(
			'title' => __( '30 Grad (ab  1999)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'45_grad_ab_1999'   => array(
			'title' => __( '45 Grad (ab  1999)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'60_grad_ab_1999'   => array(
			'title' => __( '60 Grad (ab  1999)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'90_grad_ab_1999'   => array(
			'title' => __( '90 Grad (ab  1999)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'0_grad_1990_1998'  => array(
			'title' => __( '0 Grad (1990-1998)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'30_grad_1990_1998' => array(
			'title' => __( '30 Grad (1990-1998)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'45_grad_1990_1998' => array(
			'title' => __( '45 Grad (1990-1998)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'60_grad_1990_1998' => array(
			'title' => __( '60 Grad (1990-1998)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'90_grad_1990_1998' => array(
			'title' => __( '90 Grad (1990-1998)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'0_grad_vor_1990'   => array(
			'title' => __( '0 Grad (vor 1990)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'30_grad_vor_1990'  => array(
			'title' => __( '30 Grad (vor 1990)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'45_grad_vor_1990'  => array(
			'title' => __( '45 Grad (vor 1990)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'60_grad_vor_1990'  => array(
			'title' => __( '60 Grad (vor 1990)', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'90_grad_vor_1990'  => array(
			'title' => __( '90 Grad (vor 1990)', 'wpenon' ),
			'type'  => 'FLOAT',
		),

	),
);
