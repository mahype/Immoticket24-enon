<?php

$bauteile = array(
	'title'  => __( 'Bauteile', 'wpenon' ),
	'groups' => array(
		'bauteile_basis'   => array(
			'title'       => __( 'Grundbauteile', 'wpenon' ),
			'description' => __( 'Geben Sie die relevanten Daten für die Grundbestandteile des Gebäudes an.', 'wpenon' ),
			'fields'      => array(
				'wand_daemmung_on' => array(
					'type'        => 'select',
					'label'       => __( 'Wanddämmung', 'wpenon' ),
					'description' => __( 'Wurden die Außenwände zusätzlich gedämmt?', 'wpenon' ),
					'options'     => array(
						'yes'   => __( 'Ja', 'wpenon' ),
						'no' => __( 'Nein', 'wpenon' ),

					),
					'required'    => true,
				),
				'wand_staerke'        => array(
					'type'    => 'int',
					'label'   => __( 'Wandstärke', 'wpenon' ),
					'unit'    => 'cm',
                    'display'     => array(
						'callback'      => 'wpenon_lower_than',
						'callback_args' => array( 'field::baujahr', 1978 ),
					),
				),
				'decke_daemmung_on' => array(
					'type'        => 'select',
					'label'       => __( 'Deckendämmung', 'wpenon' ),
					'description' => __( 'Wurde die Decke zusätzlich gedämmt?', 'wpenon' ),
					'options'     => array(
						'yes'   => __( 'Ja', 'wpenon' ),
						'no' => __( 'Nein', 'wpenon' ),
					),
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::dach', array( 'unbeheizt', 'nicht-vorhanden' ) ),
					),
					'required'    => true,
				),				
				'boden_daemmung_on' => array(
					'type'        => 'select',
					'label'       => __( 'Bodendämmung', 'wpenon' ),
					'description' => __( 'Wurde der Boden zusätzlich gedämmt?', 'wpenon' ),
					'options'     => array(
						'yes'   => __( 'Ja', 'wpenon' ),
						'no' => __( 'Nein', 'wpenon' ),
					),
					'required'    => true,
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
				'dach_daemmung_on' => array(
					'type'        => 'select',
					'label'       => __( 'Dachdämmung', 'wpenon' ),
					'description' => __( 'Wurden das Dach zusätzlich gedämmt?', 'wpenon' ),
					'options'     => array(
						'yes'   => __( 'Ja', 'wpenon' ),
						'no' => __( 'Nein', 'wpenon' ),
					),
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::dach', 'beheizt' ),
					),
					'required'    => true,
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
					'type'                  => 'text',
					'label'                 => __( 'Baujahr der Fenster', 'wpenon' ),
					'placeholder' 			=> 'Bitte wählen...',					
					'required'              => true,
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),					
				),
			),
		),
	),
);
