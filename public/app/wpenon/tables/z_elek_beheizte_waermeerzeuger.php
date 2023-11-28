<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Aufwandszahlen zentrale elektrisch beheizte Wärmeerzeuger - Tabelle 101
 */
return array(
	'title'         => __( 'zentrale elektrisch beheizte Wärmeerzeuger', 'wpenon' ),
	'description'   => __( 'Aufwandszahlen zentrale elektrisch beheizte Wärmeerzeuger', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'bezeichnung',
	'search_field'  => 'bezeichnung',
	'search_before' => true,
	'fields'        => array(
		'bezeichnung'   => array(
			'title' => __( 'Bezeichnung', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'lww_w_7' => array(
			'title' => __( 'e<sub>g</sub>', 'wpenon' ),
			'type'  => 'FLOAT',
		),


	),
);
