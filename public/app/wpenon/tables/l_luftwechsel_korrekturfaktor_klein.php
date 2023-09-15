<?php

return array(
	'title' => __( 'Gesamtluftwechsel Korrekturfaktor Wohngebäude (bis 1500m3)', 'wpenon' ),
	'description' => __( 'Diese Tabelle enthält Korrekturfaktoren für die Gesamtluftwechwselraten für Wohngebäude bis 1500m3.', 'wpenon' ) . ' ' . __( 'Diese Daten werden für den Bedarfsausweis benötigt.', 'wpenon' ),
	'primary_field' => 'bezeichnung',
	'search_field' => 'bezeichnung',
	'rebuild_on_import' => true,
	'search_before' => true,
	'fields' => array(
		'bezeichnung' => array(
			'title' => __( 'Bezeichnung (intern)', 'wpenon' ),
			'type' => 'VARCHAR(100)',
		),
		'name' => array(
			'title' => __( 'Kategorie', 'wpenon' ),
			'type' => 'VARCHAR(100)',
		),
		'ohne' => array(
			'title' => __( 'Ohne mechanische Lüftung', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'zu_abluft_bedarfsgefuehrt_ab_0' => array(
			'title' => __( 'Zu-/Abluftanlage Bedarfsgeführt ab 0%', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'zu_abluft_bedarfsgefuehrt_ab_60' => array(
			'title' => __( 'Zu-/Abluftanlage Bedarfsgeführt ab 60%', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'zu_abluft_bedarfsgefuehrt_ab_80' => array(
			'title' => __( 'Zu-/Abluftanlage Bedarfsgeführt ab 80%', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'zu_abluft_nichtbedarfsgefuehrt_ab_0' => array(
			'title' => __( 'Zu-/Abluftanlage nicht Bedarfsgeführt ab 0%', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'zu_abluft_nichtbedarfsgefuehrt_ab_60' => array(
			'title' => __( 'Zu-/Abluftanlage nicht Bedarfsgeführt ab 60%', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'zu_abluft_nichtbedarfsgefuehrt_ab_80' => array(
			'title' => __( 'Zu-/Abluftanlage nicht Bedarfsgeführt ab 80%', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'abluft_bedarfsgefuehrt' => array(
			'title' => __( 'Abluftanlage Bedarfsgeführt', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'abluft_nichtbedarfsgefuehrt' => array(
			'title' => __( 'Abluftanlage nicht Bedarfsgeführt', 'wpenon' ),
			'type' => 'FLOAT',
		),		
	),
);
