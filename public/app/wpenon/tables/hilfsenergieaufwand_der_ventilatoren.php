<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 *  Hilfsenergieaufwand der Ventilatoren in Wohnungslüftungsanlagen   - Tabelle 120.
 */
return array(
	'title'         => __( 'Hilfsenergieaufwand der Ventilatoren in Wohnungslüftungsanlagen', 'wpenon' ),
	'description'   => __( 'Hilfsenergieaufwand der Ventilatoren in Wohnungslüftungsanlagen', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'id',
	'search_field'  => 'id',
	'search_before' => true,
	'fields'        => array(
		'id'                 => array(
			'title' => __( 'ID', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'a'                  => array(
			'title' => __( 'A m', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'v'                  => array(
			'title' => __( 'V m', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bedarf_ac'          => array(
			'title' => __( 'Bedarfsgeführt AC', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bedarf_de_ec'       => array(
			'title' => __( 'Bedarfsgeführt DC/EC', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'nicht_bedarf_ac'    => array(
			'title' => __( 'Nicht Bedarfsgeführt AC', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'nicht_bedarf_de_ec' => array(
			'title' => __( 'Nicht Bedarfsgeführt DC/EC', 'wpenon' ),
			'type'  => 'FLOAT',
		),
	),
);
