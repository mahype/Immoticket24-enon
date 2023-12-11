<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Jährliche Endenergie aus Photovoltaikanlagen (flächenbezogen)
 * Tabelle 115
 */
return array(
	'title'         => __( 'Jährliche Endenergie aus Photovoltaikanlagen', 'wpenon' ),
	'description'   => __( 'Jährliche Endenergie aus Photovoltaikanlagen qf,Prod,PV,i  kWh/(m2∙a)', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'bezeichnung',
	'search_field'  => 'bezeichnung',
	'search_before' => true,
	'fields'        => array(
		'bezeichnung'       => array(
			'title' => __( 'Bezeichnung', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'neigung'   => array(
			'title' => __( 'Neigung', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'n'    => array(
			'title' => __( 'Nord', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'no'   => array(
			'title' => __( 'Nord-Ost', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'o'   => array(
			'title' => __( 'Ost', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'so'   => array(
			'title' => __( 'Süd-Ost', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		's'  => array(
			'title' => __( 'Süd', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'sw' => array(
			'title' => __( 'Süd-West', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'w' => array(
			'title' => __( 'West', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'nw' => array(
			'title' => __( 'Nord-West', 'wpenon' ),
			'type'  => 'FLOAT',
        ),
	),
);
