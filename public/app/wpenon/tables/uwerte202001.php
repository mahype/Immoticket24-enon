<?php

return array(
	'title'         => __( 'U-Werte 2020/01', 'wpenon' ),
	'description'   => __( 'Diese Tabelle enthält die standardisierten Wärmedurchgangskoeffizienten (U-Werte) verschiedener Bauteile, eingeteilt in Zeiträumen von Baujahren.', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'bezeichnung',
	'search_field'  => 'bezeichnung',
	'search_before' => true,
	'rebuild_on_import' => true,
	'fields'        => array(
		'bezeichnung'   => array(
			'title'         => __( 'Bezeichnung (intern)', 'wpenon' ),
			'type'          => 'VARCHAR(100)',
		),
		'name'          => array(
			'title'         => __( 'Bauteil', 'wpenon' ),
			'type'          => 'VARCHAR(100)',
		),
		'bis1918'     => array(
			'title'       => __( 'Bis 1918', 'wpenon' ),
			'type'        => 'FLOAT',
		),
		'bis1948'     => array(
			'title'       => __( '1919-1948', 'wpenon' ),
			'type'        => 'FLOAT',
		),
		'bis1957'     => array(
			'title'       => __( '1949-1957', 'wpenon' ),
			'type'        => 'FLOAT',
		),
		'bis1968'     => array(
			'title'       => __( '1958-1968', 'wpenon' ),
			'type'        => 'FLOAT',
		),
		'bis1978'     => array(
			'title'       => __( '1969-1978', 'wpenon' ),
			'type'        => 'FLOAT',
		),
		'bis1983'     => array(
			'title'       => __( '1979-1983', 'wpenon' ),
			'type'        => 'FLOAT',
		),
		'bis1994'     => array(
			'title'       => __( '1984-1994', 'wpenon' ),
			'type'        => 'FLOAT',
		),
		'bis2001'      => array(
			'title'       => __( '1995-2001', 'wpenon' ),
			'type'        => 'FLOAT',
		),
		'bis2006'      => array(
			'title'       => __( '2002-2006', 'wpenon' ),
			'type'        => 'FLOAT',
		),
		'ab2007'      => array(
			'title'       => __( 'Ab 2007', 'wpenon' ),
			'type'        => 'FLOAT',
		),
	),
);
