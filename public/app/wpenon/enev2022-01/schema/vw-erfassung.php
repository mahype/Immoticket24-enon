<?php

$erfassung = array(
	'title'  => __( 'Verbrauchserfassung', 'wpenon' ),
	'groups' => array(
		'zeitraum'          => array(
			'title'       => __( 'Zeitraum', 'wpenon' ),
			'description' => __( 'Wählen Sie aus, für welchen Zeitraum Sie die Verbrauchsdaten eingeben möchten.', 'wpenon' ),
			'fields'      => array(
				'verbrauch_zeitraum' => array(
					'type'        => 'select',
					'label'       => __( 'Zeitraum der Eingabedaten', 'wpenon' ),
					'description' => __( 'Die Verbrauchsdaten müssen für drei aufeinanderfolgende Jahre eingegeben werden. Wählen Sie hier den entsprechenden Zeitraum aus.', 'wpenon' ),
					'options'     => wpenon_immoticket24_get_klimafaktoren_zeitraeume202101(),
					'required'    => true,
				),
			),
		),
		'verbrauchseingabe' => array(
			'title'  => __( 'Eingabe der Verbrauchsdaten', 'wpenon' ),
			'fields' => array(
				'verbrauch1_headline'  => array(
					'type'        => 'headline',
					'description' => __( 'Geben Sie die Verbrauchsdaten für das erste Jahr an.', 'wpenon' ),
					'label'       => array(
						'callback'      => 'wpenon_immoticket24_get_zeitraum_headline',
						'callback_args' => array( 'field::verbrauch_zeitraum', 0 ),
					),
				),
				'verbrauch1_h'         => array(
					'type'        => 'float',
					'label'       => __( 'Verbrauch Heizungsanlage', 'wpenon' ),
					'description' => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
					'required'    => true,
					'unit'        => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_unit',
						'callback_args' => array( 'field::h_energietraeger' ),
					),
				),
				'verbrauch1_h2'        => array(
					'type'        => 'float',
					'label'       => __( 'Verbrauch 2. Heizungsanlage', 'wpenon' ),
					'description' => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
					'required'    => true,
					'unit'        => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_unit',
						'callback_args' => array( 'field::h2_energietraeger' ),
					),
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h2_info', true ),
					),
				),
				'verbrauch1_h3'        => array(
					'type'        => 'float',
					'label'       => __( 'Verbrauch 3. Heizungsanlage', 'wpenon' ),
					'description' => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
					'required'    => true,
					'unit'        => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_unit',
						'callback_args' => array( 'field::h3_energietraeger' ),
					),
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
					),
				),
				'verbrauch1_ww'        => array(
					'type'        => 'float',
					'label'       => __( 'Verbrauch Warmwasseranlage', 'wpenon' ),
					'description' => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
					'required'    => true,
					'unit'        => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_unit',
						'callback_args' => array( 'field::ww_energietraeger' ),
					),
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_ww_verbrauch',
						'callback_args' => array( 'field::ww_info', 'field::ww_erzeugung', 'field::h_erzeugung' ),
					),
				),
				'verbrauch1_leerstand' => array(
					'type'        => 'int',
					'label'       => __( 'Leerstand', 'wpenon' ),
					'description' => __( 'Geben Sie hier den Leerstand des Gebäudes in Prozent an. Dieser errechnet sich, in dem Sie die leerstehende Fläche pro Jahr stets im Verhältnis zur gesamten Fläche angeben.', 'wpenon' ) . ' ' . __( 'Beispiel: Ein Gebäude hat 2 Wohnungen mit je 50 m² Wohnfläche. Wohnung 1 stand in einem Jahr 3 Monate leer. Berechnung: Pro Jahr stellt das Gebäude somit insgesamt 1200 m² (= 12 Monate * 100 m²) Wohnfläche zur Verfügung, davon waren 150 m² (Wohnung 1 stand 3 Monate leer (= 50 m² * 3)) nicht vermietet. Dies ergäbe einen Leerstand von 12,5 Prozent (= 150 m² / 1200 m²).', 'wpenon' ),
					'default'     => 0,
					'max'         => 50,
					'unit'        => '%',
				),
				'verbrauch2_headline'  => array(
					'type'        => 'headline',
					'description' => __( 'Geben Sie die Verbrauchsdaten für das zweite Jahr an.', 'wpenon' ),
					'label'       => array(
						'callback'      => 'wpenon_immoticket24_get_zeitraum_headline',
						'callback_args' => array( 'field::verbrauch_zeitraum', 1 ),
					),
				),
				'verbrauch2_h'         => array(
					'type'        => 'float',
					'label'       => __( 'Verbrauch Heizungsanlage', 'wpenon' ),
					'description' => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
					'required'    => true,
					'unit'        => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_unit',
						'callback_args' => array( 'field::h_energietraeger' ),
					),
				),
				'verbrauch2_h2'        => array(
					'type'        => 'float',
					'label'       => __( 'Verbrauch 2. Heizungsanlage', 'wpenon' ),
					'description' => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
					'required'    => true,
					'unit'        => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_unit',
						'callback_args' => array( 'field::h2_energietraeger' ),
					),
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h2_info', true ),
					),
				),
				'verbrauch2_h3'        => array(
					'type'        => 'float',
					'label'       => __( 'Verbrauch 3. Heizungsanlage', 'wpenon' ),
					'description' => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
					'required'    => true,
					'unit'        => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_unit',
						'callback_args' => array( 'field::h3_energietraeger' ),
					),
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
					),
				),
				'verbrauch2_ww'        => array(
					'type'        => 'float',
					'label'       => __( 'Verbrauch Warmwasseranlage', 'wpenon' ),
					'description' => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
					'required'    => true,
					'unit'        => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_unit',
						'callback_args' => array( 'field::ww_energietraeger' ),
					),
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_ww_verbrauch',
						'callback_args' => array( 'field::ww_info', 'field::ww_erzeugung', 'field::h_erzeugung' ),
					),
				),
				'verbrauch2_leerstand' => array(
					'type'        => 'int',
					'label'       => __( 'Leerstand', 'wpenon' ),
					'description' => __( 'Geben Sie hier den Leerstand des Gebäudes in Prozent an. Dieser errechnet sich, in dem Sie die leerstehende Fläche pro Jahr stets im Verhältnis zur gesamten Fläche angeben.', 'wpenon' ) . ' ' . __( 'Beispiel: Ein Gebäude hat 2 Wohnungen mit je 50 m² Wohnfläche. Wohnung 1 stand in einem Jahr 3 Monate leer. Berechnung: Pro Jahr stellt das Gebäude somit insgesamt 1200 m² (= 12 Monate * 100 m²) Wohnfläche zur Verfügung, davon waren 150 m² (Wohnung 1 stand 3 Monate leer (= 50 m² * 3)) nicht vermietet. Dies ergäbe einen Leerstand von 12,5 Prozent (= 150 m² / 1200 m²).', 'wpenon' ),
					'default'     => 0,
					'max'         => 50,
					'unit'        => '%',
				),
				'verbrauch3_headline'  => array(
					'type'        => 'headline',
					'description' => __( 'Geben Sie die Verbrauchsdaten für das dritte Jahr an.', 'wpenon' ),
					'label'       => array(
						'callback'      => 'wpenon_immoticket24_get_zeitraum_headline',
						'callback_args' => array( 'field::verbrauch_zeitraum', 2 ),
					),
				),
				'verbrauch3_h'         => array(
					'type'        => 'float',
					'label'       => __( 'Verbrauch Heizungsanlage', 'wpenon' ),
					'description' => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
					'required'    => true,
					'unit'        => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_unit',
						'callback_args' => array( 'field::h_energietraeger' ),
					),
				),
				'verbrauch3_h2'        => array(
					'type'        => 'float',
					'label'       => __( 'Verbrauch 2. Heizungsanlage', 'wpenon' ),
					'description' => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
					'required'    => true,
					'unit'        => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_unit',
						'callback_args' => array( 'field::h2_energietraeger' ),
					),
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h2_info', true ),
					),
				),
				'verbrauch3_h3'        => array(
					'type'        => 'float',
					'label'       => __( 'Verbrauch 3. Heizungsanlage', 'wpenon' ),
					'description' => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
					'required'    => true,
					'unit'        => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_unit',
						'callback_args' => array( 'field::h3_energietraeger' ),
					),
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
					),
				),
				'verbrauch3_ww'        => array(
					'type'        => 'float',
					'label'       => __( 'Verbrauch Warmwasseranlage', 'wpenon' ),
					'description' => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
					'required'    => true,
					'unit'        => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_unit',
						'callback_args' => array( 'field::ww_energietraeger' ),
					),
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_ww_verbrauch',
						'callback_args' => array( 'field::ww_info', 'field::ww_erzeugung', 'field::h_erzeugung' ),
					),
				),
				'verbrauch3_leerstand' => array(
					'type'        => 'int',
					'label'       => __( 'Leerstand', 'wpenon' ),
					'description' => __( 'Geben Sie hier den Leerstand des Gebäudes in Prozent an. Dieser errechnet sich, in dem Sie die leerstehende Fläche pro Jahr stets im Verhältnis zur gesamten Fläche angeben.', 'wpenon' ) . ' ' . __( 'Beispiel: Ein Gebäude hat 2 Wohnungen mit je 50 m² Wohnfläche. Wohnung 1 stand in einem Jahr 3 Monate leer. Berechnung: Pro Jahr stellt das Gebäude somit insgesamt 1200 m² (= 12 Monate * 100 m²) Wohnfläche zur Verfügung, davon waren 150 m² (Wohnung 1 stand 3 Monate leer (= 50 m² * 3)) nicht vermietet. Dies ergäbe einen Leerstand von 12,5 Prozent (= 150 m² / 1200 m²).', 'wpenon' ),
					'default'     => 0,
					'max'         => 50,
					'unit'        => '%',
				),
			),
		),
	),
);
