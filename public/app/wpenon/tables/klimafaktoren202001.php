<?php

if ( ! function_exists( 'wpenon_immoticket24_pre_rebuild_klimafaktoren_table' ) ) {
	function wpenon_immoticket24_pre_rebuild_klimafaktoren_table( $file, $charset ) {
		$file_handle = fopen( $file, 'r' );
		$first_line  = fgetcsv( $file_handle, 0, ';', '"', '"' );
		fclose( $file_handle );

		$months_count = count( $first_line ) - 1;

		$start = get_option( 'wpenon_immoticket24_klimafaktoren_start', '2018_01' );
		list( $startjahr, $startmonat ) = array_map( 'absint', explode( '_', $start ) );

		$endjahr  = $startjahr + absint( floor( $months_count / 12 ) );
		$endmonat = $startmonat + $months_count % 12 - 1;
		if ( $endmonat > 12 ) {
			$endjahr  += 1;
			$endmonat = $endmonat % 12;
		}

		update_option( 'wpenon_immoticket24_klimafaktoren_end', zeroise( $endjahr, 4 ) . '_' . zeroise( $endmonat, 2 ) );
	}
}

$_klimafaktoren_schema = array(
	'title'             => __( 'Klimafaktoren 2020/01', 'wpenon' ),
	'description'       => __( 'Diese Tabelle enthält Klimafaktoren für sämtliche Postleitzahlen.', 'wpenon' ),
	'asterisks'         => array(
		'PLZ' => __( 'Postleitzahl', 'wpenon' ),
	),
	'primary_field'     => 'bezeichnung',
	'search_field'      => 'bezeichnung',
	'search_before'     => true,
	'rebuild_on_import' => 'wpenon_immoticket24_pre_rebuild_klimafaktoren_table',
	'fields'            => array(
		'bezeichnung' => array(
			'title' => __( 'PLZ<sup>1</sup>', 'wpenon' ),
			'type'  => 'VARCHAR(5)',
		),
	),
);

$start = get_option( 'wpenon_immoticket24_klimafaktoren_start', '2018_01' );
$end   = get_option( 'wpenon_immoticket24_klimafaktoren_end', '2019_12' );
list( $startjahr, $startmonat ) = array_map( 'absint', explode( '_', $start ) );
list( $endjahr, $endmonat ) = array_map( 'absint', explode( '_', $end ) );

for ( $i = $startjahr; $i <= $endjahr; $i ++ ) {
	$_s = 1;
	$_e = 12;
	if ( $i == $startjahr ) {
		$_s = $startmonat;
	} elseif ( $i == $endjahr ) {
		$_e = $endmonat;
	}

	for ( $j = $_s; $j <= $_e; $j ++ ) {
		$datum                                     = zeroise( $i, 4 ) . '_' . zeroise( $j, 2 );
		$_klimafaktoren_schema['fields'][ $datum ] = array(
			'title'   => wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $datum, 0, false, 'data' ) . ' - ' . wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $datum, 0, true, 'data' ),
			'type'    => 'FLOAT',
			'default' => '1.0',
		);
	}
}

return $_klimafaktoren_schema;
