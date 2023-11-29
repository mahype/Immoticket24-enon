<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 *  Fern- und Nahwärme-Hausstationen Tabelle 104
 *
 * ww_nt_pt_105_ubh =  Warmwasser, niedrige Temperatur, Auslegung Primärtemperatur Qprim,DS = 105 °C, Aufstellung im unbeheizten Bereich
 * ww_nt_pt_105_bh  =  Warmwasser, niedrige Temperatur, Auslegung Primärtemperatur Qprim,DS = 105 °C, Aufstellung im beheizten Bereich
 * ww_nt_pt_150_ubh =  Warmwasser, niedrige Temperatur, Auslegung Primärtemperatur Qprim,DS = 150 °C, Aufstellung im unbeheizten Bereich
 * ww_nt_pt_150_bh  =  Warmwasser, niedrige Temperatur, Auslegung Primärtemperatur Qprim,DS = 150 °C, Aufstellung im beheizten Bereich
 *
 * Slug
 * ww_nt_pt_150_bh _ Auslegungtemperatur _ Nennleistung Fernwärmestation
 * ww_nt_pt_150_bh_90_70_bis_30 = ww_nt_pt_150_bh, 90 c 70 Grad, <30kW
 */
return array(
	'title'         => __( 'Fern- und Nahwärme-Hausstationen', 'wpenon' ),
	'description'   => __( 'Korrekturfaktor Temperatur zu Aufwandszahlen Heizwärmeerzeugung Fernwärme', 'wpenon' ),
	'asterisks'     => array(),
	'primary_field' => 'id',
	'search_field'  => 'id',
	'search_before' => true,
	'fields'        => array(
		'id'      => array(
			'title' => __( 'id', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'at'      => array(
			'title' => __( 'Auslegungstemperatur', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'nf'      => array(
			'title' => __( 'Nennleistung Fernwärmestation', 'wpenon' ),
			'type'  => 'VARCHAR(100)',
		),
		'bhg_0_1' => array(
			'title' => __( 'b h,g 0,1', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_0_2' => array(
			'title' => __( 'b h,g 0,2', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_0_3' => array(
			'title' => __( 'b h,g 0,3', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_0_4' => array(
			'title' => __( 'b h,g 0,4', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_0_5' => array(
			'title' => __( 'b h,g 0,5', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_0_6' => array(
			'title' => __( 'b h,g 0,6', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_0_7' => array(
			'title' => __( 'b h,g 0,7', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_0_8' => array(
			'title' => __( 'b h,g 0,8', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_0_9' => array(
			'title' => __( 'b h,g 0,9', 'wpenon' ),
			'type'  => 'FLOAT',
		),
		'bhg_1_0' => array(
			'title' => __( 'b h,g 1,0', 'wpenon' ),
			'type'  => 'FLOAT',
		),
	),
);
