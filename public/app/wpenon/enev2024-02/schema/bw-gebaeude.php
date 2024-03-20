<?php

$gebaeude = array(
	'title'  => __( 'Gebäudetopologie', 'wpenon' ),
	'groups' => array(
		'grundriss' => array(
			'title'       => __( 'Grundriss', 'wpenon' ),
			'description' => __( 'Machen Sie hier Angaben zum Grundriss Ihres Gebäudes.', 'wpenon' ),
			'fields'      => array(
				'grundriss_form'     => array(
					'type'        => 'select',
					'label'       => __( 'Form des Grundrisses', 'wpenon' ),
					'description' => __( 'Wählen Sie hier die Form aus (Draufsicht), die auf den Grundriss Ihres Gebäudes zutrifft.', 'wpenon' ),
					'options'     => \Enev\Schema202402\Schema\Standard_Options::get_grundriss_formen(),
					'required'    => true,
				),
				'grundriss_richtung' => array(
					'type'        => 'select',
					'label'       => __( 'Orientierung', 'wpenon' ),
					'description' => __( 'Wählen Sie die Himmelsrichtung aus, in die Wand a im obigen Bild zeigt.', 'wpenon' ),
					'options'     => wpenon_immoticket24_get_himmelsrichtungen(),
					'required'    => true,
				),
				'anbau'              => array(
					'type'    => 'checkbox',
					'label'   => __( 'Beheizter Anbau vorhanden?', 'wpenon' ),
					'default' => false,
				),
				'anbau_form'         => array(
					'type'        => 'select',
					'label'       => __( 'Form des Anbaus', 'wpenon' ),
					'description' => __( 'Wählen Sie hier die Form aus (Draufsicht), die auf den Anbau Ihres Gebäudes zutrifft.', 'wpenon' ),
					'options'     => \Enev\Schema202402\Schema\Standard_Options::get_anbau_formen(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
				),
				'anbau_baujahr'      => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr des Anbaus', 'wpenon' ),
					'description'           => __( 'Geben Sie das Baujahr des Anbaus an.', 'wpenon' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
				),
			),
		),
		'geschosse' => array(
			'title'       => __( 'Geschosse', 'wpenon' ),
			'description' => __( 'Geben Sie die Informationen zu den Vollgeschossen des Gebäudes (ohne Dach- oder Kellergeschoss!) an.', 'wpenon' ),
			'fields'      => array(
				'geschoss_zahl'  => array(
					'type'        => 'int',
					'label'       => __( 'Anzahl der Vollgeschosse', 'wpenon' ),
					'description' => __( 'Geben Sie die Anzahl der Vollgeschosse ein, also die Anzahl aller Geschosse ohne eventuelle Dach- oder Kellergeschosse.', 'wpenon' ),
					'min'         => 1,
					'max'         => 100,
					'required'    => true,
				),
				'geschoss_hoehe' => array(
					'type'        => 'float_length',
					'label'       => __( 'Geschosshöhe', 'wpenon' ),
					'description' => __( 'Geben Sie die lichte Höhe eines einzelnen Geschosses ein, also die Höhe vom Boden bis zur Decke.', 'wpenon' ),
					'default'     => 2.10,
					'max'         => 5.0,
					'required'    => true,
					'unit'        => 'm',
				),
				'anbau_hoehe'    => array(
					'type'        => 'float_length',
					'label'       => __( 'Höhe des Anbaus', 'wpenon' ),
					'description' => __( 'Geben Sie die lichte Anbauhöhe ein, vom Boden bis zur Decke.', 'wpenon' ),
					'default'     => 2.10,
					'max'         => 12.0,
					'required'    => true,
					'unit'        => 'm',
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
				),
			),
		),
	),
);
