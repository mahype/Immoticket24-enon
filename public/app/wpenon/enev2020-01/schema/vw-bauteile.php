<?php

$bauteile = array(
	'title'  => __( 'Bauteile', 'wpenon' ),
	'groups' => array(
		'bauteile_basis'   => array(
			'title'       => __( 'Grundbauteile', 'wpenon' ),
			'description' => __( 'Geben Sie die relevanten Daten für die Grundbestandteile des Gebäudes an.', 'wpenon' ),
			'fields'      => array(
				'wand_daemmung'   => array(
					'type'        => 'int',
					'label'       => __( 'Wanddämmung', 'wpenon' ),
					'description' => __( 'Falls die Außenwände zusätzlich gedämmt worden sind, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon' ),
					'unit'        => 'cm',
					'max'         => 23,
				),
				'wand_porenbeton' => array(
					'type'    => 'select',
					'label'   => __( 'Sind die Außenwände aus Porenbeton (z.B. Ytong)?', 'wpenon' ),
					'options' => array(
						'ja'        => __( 'Ja', 'wpenon' ),
						'nein'      => __( 'Nein', 'wpenon' ),
						'unbekannt' => __( 'Unbekannt', 'wpenon' ),
					),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand_porenbeton_verbrauch',
						'callback_args' => array( 'field::wand_daemmung' ),
					),
				),
				'decke_daemmung'  => array(
					'type'        => 'int',
					'label'       => __( 'Deckendämmung', 'wpenon' ),
					'description' => __( 'Falls die Oberste Geschossdecke zusätzlich gedämmt worden ist, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon' ),
					'unit'        => 'cm',
					'max'         => 30,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_blacklist',
						'callback_args' => array( 'field::dach', 'beheizt' ),
					),
				),
				'boden_daemmung'  => array(
					'type'        => 'int',
					'label'       => __( 'Bodendämmung', 'wpenon' ),
					'description' => __( 'Falls die Bodenplatte / Kellerdecke zusätzlich gedämmt worden ist, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon' ),
					'unit'        => 'cm',
					'max'         => 25,
				),
			),
		),
		'bauteile_dach'    => array(
			'title'       => __( 'Dach', 'wpenon' ),
			'description' => __( 'Geben Sie die relevanten Daten für das Dachgeschoss des Gebäudes an.', 'wpenon' ),
			'fields'      => array(
				'dach'          => array(
					'type'     => 'select',
					'label'    => __( 'Dachgeschoss', 'wpenon' ),
					'options'  => array(
						'nicht-vorhanden' => __( 'nicht vorhanden', 'wpenon' ),
						'unbeheizt'       => __( 'unbeheizt', 'wpenon' ),
						'beheizt'         => __( 'beheizt', 'wpenon' ),
					),
					'required' => true,
				),
				'dach_daemmung' => array(
					'type'        => 'int',
					'label'       => __( 'Dachdämmung', 'wpenon' ),
					'description' => __( 'Falls das Dach zusätzlich gedämmt worden ist, geben Sie hier dessen Dämmstärke in Zentimetern an.', 'wpenon' ),
					'unit'        => 'cm',
					'max'         => 30,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::dach', 'beheizt' ),
					),
				),
			),
		),
		'bauteile_keller'  => array(
			'title'       => __( 'Keller', 'wpenon' ),
			'description' => __( 'Geben Sie die relevanten Daten für das Kellergeschoss des Gebäudes an.', 'wpenon' ),
			'fields'      => array(
				'keller'          => array(
					'type'     => 'select',
					'label'    => __( 'Kellergeschoss', 'wpenon' ),
					'options'  => array(
						'nicht-vorhanden' => __( 'nicht vorhanden', 'wpenon' ),
						'unbeheizt'       => __( 'unbeheizt', 'wpenon' ),
						'beheizt'         => __( 'beheizt', 'wpenon' ),
					),
					'required' => true,
				),
				'keller_daemmung' => array(
					'type'        => 'int',
					'label'       => __( 'Kellerwanddämmung', 'wpenon' ),
					'description' => __( 'Falls die Kellerwände zusätzlich gedämmt worden sind, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon' ),
					'unit'        => 'cm',
					'max'         => 23,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::keller', 'beheizt' ),
					),
				),
			),
		),
		'bauteile_fenster' => array(
			'title'       => __( 'Fenster', 'wpenon' ),
			'description' => __( 'Geben Sie die relevanten Daten für die Fenster des Gebäudes an.', 'wpenon' ),
			'fields'      => array(
				'fenster_bauart'  => array(
					'type'     => 'select',
					'label'    => __( 'Bauart der Fenster', 'wpenon' ),
					'options'  => wpenon_immoticket24_get_fenster_bauarten(),
					'required' => true,
				),
				'fenster_baujahr' => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr der Fenster', 'wpenon' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
				),
			),
		),
	),
);
