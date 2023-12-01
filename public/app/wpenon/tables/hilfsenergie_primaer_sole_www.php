<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Hilfsenergie Primärseite für Sole- bzw. Wasser/Wasser-Wärmepumpen - Tabelle 96.
 */
return array(
	'title'         => __( 'Hilfsenergie Primärseite für Sole- bzw. Wasser/Wasser-Wärmepumpen', 'wpenon' ),
	'description'   => __( 'Hilfsenergie Primärseite für Sole- bzw. Wasser/Wasser-Wärmepumpen', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'id',
	'search_field'  => 'id',
	'search_before' => true,
	'fields'        => array(
		'id'          => array(
			'title' => __( 'ID', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'bezeichnung' => array(
			'title' => __( 'Nennleistung kW', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'grenze_10'    => array(
			'title' => __( 'Heizgrenztemperatur 10C kWh', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'grenze_12'   => array(
			'title' => __( 'Heizgrenztemperatur 12C kWh', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'grenze_15' => array(
			'title' => __( 'Heizgrenztemperatur 15C kWh', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		
	),
);
