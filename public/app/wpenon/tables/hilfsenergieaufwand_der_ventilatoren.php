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
		'gebaeudenutzfläche'                  => array(
			'title' => __( 'Gebäudenutzfläche', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'ac'                  => array(
			'title' => __( 'AC', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'dc_ec'          => array(
			'title' => __( 'DC/EC', 'wpenon' ),
			'type'  => 'FLOAT',
		),
	),
);
