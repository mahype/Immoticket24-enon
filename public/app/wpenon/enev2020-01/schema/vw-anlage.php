<?php

$anlage = array(
	'title'  => __( 'Anlage', 'wpenon' ),
	'groups' => array(
		'heizung'    => array(
			'title'       => __( 'Heizungsanlage', 'wpenon' ),
			'description' => __( 'Machen Sie hier Angaben zur Heizungsanlage / Wärmeerzeugung des Gebäudes. Sie können bis zu drei unterschiedliche Heizungsanlagen spezifizieren.', 'wpenon' ),
			'fields'      => array(
				'h_erzeugung'       => array(
					'type'     => 'select',
					'label'    => __( 'Typ der Heizungsanlage', 'wpenon' ),
					'options'  => wpenon_immoticket24_get_heizungsanlagen2019(),
					'required' => true,
				),
				'h_energietraeger'  => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => wpenon_immoticket24_get_energietraeger( true ),
					'required' => true,
				),
				'h_baujahr'         => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr der Heizungsanlage', 'wpenon' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
				),
				'h2_info'           => array(
					'type'  => 'checkbox',
					'label' => __( '2. Heizungsanlage vorhanden?', 'wpenon' ),
				),
				'h2_erzeugung'      => array(
					'type'     => 'select',
					'label'    => __( 'Typ der 2. Heizungsanlage', 'wpenon' ),
					'options'  => wpenon_immoticket24_get_heizungsanlagen2019(),
					'required' => true,
					'display'  => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h2_info', true ),
					),
				),
				'h2_energietraeger' => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der 2. Heizungsanlage', 'wpenon' ),
					'options'  => wpenon_immoticket24_get_energietraeger( true ),
					'required' => true,
					'display'  => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h2_info', true ),
					),
				),
				'h2_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr der 2. Heizungsanlage', 'wpenon' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h2_info', true ),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
				),
				'h3_info'           => array(
					'type'    => 'checkbox',
					'label'   => __( '3. Heizungsanlage vorhanden?', 'wpenon' ),
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h2_info', true ),
					),
				),
				'h3_erzeugung'      => array(
					'type'     => 'select',
					'label'    => __( 'Typ der 3. Heizungsanlage', 'wpenon' ),
					'options'  => wpenon_immoticket24_get_heizungsanlagen2019(),
					'required' => true,
					'display'  => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
					),
				),
				'h3_energietraeger' => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der 3. Heizungsanlage', 'wpenon' ),
					'options'  => wpenon_immoticket24_get_energietraeger( true ),
					'required' => true,
					'display'  => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
					),
				),
				'h3_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr der 3. Heizungsanlage', 'wpenon' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
				),
			),
		),
		'warmwasser' => array(
			'title'       => __( 'Warmwasseranlage', 'wpenon' ),
			'description' => __( 'Machen Sie hier Angaben zur Warmwassererzeugung des Gebäudes.', 'wpenon' ),
			'fields'      => array(
				'ww_info'           => array(
					'type'        => 'select',
					'label'       => __( 'Art der Warmwassererzeugung', 'wpenon' ),
					'description' => __( 'Wählen Sie aus, ob die Warmwasserzeugung durch eine der angegebenen Heizungsanlagen oder in einer separaten Anlage stattfindet. Alternativ können Sie auch &quot;Unbekannt&quot; auswählen, in diesem Fall wird der Verbrauch pauschal um 20 kWh/(m&sup2;a) erhöht.', 'wpenon' ),
					'options'     => array(
						'callback'      => 'wpenon_immoticket24_get_ww_info',
						'callback_args' => array( 'field::h2_info', 'field::h3_info', false, false, false, true ),
					),
					'required'    => true,
				),
				'ww_erzeugung'      => array(
					'type'     => 'select',
					'label'    => __( 'Typ der Warmwasseranlage', 'wpenon' ),
					'options'  => wpenon_immoticket24_get_warmwasseranlagen2019(),
					'required' => true,
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::ww_info', 'ww' ),
					),
				),
				'ww_energietraeger' => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Warmwasseranlage', 'wpenon' ),
					'options'  => wpenon_immoticket24_get_energietraeger( true ),
					'required' => true,
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::ww_info', 'ww' ),
					),
				),
				'ww_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr der Warmwasseranlage', 'wpenon' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::ww_info', 'ww' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
				),
			),
		),
		'lueftung'   => array(
			'title'       => __( 'Lüftungsanlage', 'wpenon' ),
			'description' => __( 'Machen Sie hier Angaben zur Lüftungsanlage des Gebäudes.', 'wpenon' ),
			'fields'      => array(
				'l_info'      => array(
					'type'     => 'select',
					'label'    => __( 'Art der Lüftung', 'wpenon' ),
					'options'  => array(
						'fenster' => __( 'Fensterlüftung', 'wpenon' ),
						'schacht' => __( 'Schachtlüftung', 'wpenon' ),
						'anlage'  => __( 'Lüftungsanlage', 'wpenon' ),
					),
					'required' => true,
				),
				'l_erzeugung' => array(
					'type'        => 'select',
					'label'       => __( 'Typ der Lüftungsanlage', 'wpenon' ),
					'description' => __( 'Wählen Sie den Typ der Lüftungsanlage aus.', 'wpenon' ),
					'options'     => wpenon_immoticket24_get_lueftungsanlagen(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::l_info', 'anlage' ),
					),
				),
				'k_info'      => array(
					'type'     => 'select',
					'label'    => __( 'Gebäudekühlung', 'wpenon' ),
					'options'  => array(
						'nicht_vorhanden' => __( 'nicht vorhanden', 'wpenon' ),
						'vorhanden'       => __( 'vorhanden', 'wpenon' ),
					),
					'required' => true,
				),
				'k_flaeche'   => array(
					'type'                  => 'float',
					'label'                 => __( 'Gekühlte Fläche', 'wpenon' ),
					'description'           => __( 'Geben Sie die gekühlte Wohnfläche in Quadratmetern ein.', 'wpenon' ),
					'required'              => true,
					'unit'                  => 'm&sup2;',
					'display'               => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::k_info', 'vorhanden' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_area_lower_than',
					'validate_dependencies' => array( 'flaeche' ),
				),
			),
		),
	),
);
