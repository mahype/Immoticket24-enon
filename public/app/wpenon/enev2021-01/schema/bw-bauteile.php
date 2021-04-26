<?php

$bauteile = array(
	'title'  => __( 'Bauteile', 'wpenon' ),
	'groups' => array(
		'bauteile_basis'   => array(
			'title'       => __( 'Grundbauteile', 'wpenon' ),
			'description' => __( 'Geben Sie die relevanten Daten für die Grundbestandteile des Gebäudes an.', 'wpenon' ),
			'fields'      => array(
				'wand_staerke'    => array(
					'type'        => 'select',
					'label'       => __( 'Wandstärke', 'wpenon' ),
					'description' => __( 'Wählen Sie die Wandstärke der Außenwand aus.', 'wpenon' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_wandstaerken(),
					'required'    => true,
					'unit'     => 'cm',
				),
				'wand_bauart_holz'          => array(
					'type'        => 'select',
					'label'       => __( 'Wandbauart', 'wpenon' ),
					'description' => __( 'Wählen Sie die Bauart der Außenwand aus.', 'wpenon' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_bauarten_holzhaus(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_wand_bauart',
						'callback_args' => array( 'field::gebaeudekonstruktion', 'holz' ),
					),
				),
				'wand_bauart_fachwerk'      => array(
					'type'        => 'select',
					'label'       => __( 'Wandbauart', 'wpenon' ),
					'description' => __( 'Wählen Sie die Bauart der Außenwand aus.', 'wpenon' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_bauarten_fachwerkaus(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_wand_bauart',
						'callback_args' => array( 'field::gebaeudekonstruktion', 'fachwerk' ),
					),
				),
				'wand_bauart_massiv'        => array(
					'type'        => 'select',
					'label'       => __( 'Wandbauart', 'wpenon' ),
					'description' => __( 'Wählen Sie die Bauart der Außenwand aus.', 'wpenon' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_bauarten_massiv(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_wand_bauart',
						'callback_args' => array( 'field::gebaeudekonstruktion', 'massiv' ),
					),
				),
				'wand_a_headline'           => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Wand %s', 'wpenon' ), 'a' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'a' ),
					),
				),
				'wand_a_laenge'             => array(
					'type'     => 'float_length_wall',
					'label'    => __( 'Länge', 'wpenon' ),
					'required' => true,
					'unit'     => 'm',
					'max'      => 50,
					'value'    => array(
						'callback'      => 'wpenon_immoticket24_calculate_wand',
						'callback_args' => array(
							'field::grundriss_form',
							'a',
							0.0,
							'field::wand_b_laenge',
							'field::wand_c_laenge',
							'field::wand_d_laenge',
							'field::wand_e_laenge',
							'field::wand_f_laenge',
							'field::wand_g_laenge',
							'field::wand_h_laenge'
						),
						'callback_hard' => true,
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'a' ),
					),
				),
				'wand_a_nachbar'            => array(
					'type'    => 'checkbox',
					'label'   => sprintf( __( 'Wand %s grenzt an Nachbargebäude?', 'wpenon' ), 'a' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'a' ),
					),
				),
				'wand_a_daemmung'           => array(
					'type'    => 'int',
					'label'   => __( 'Nachträgliche Dämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'a', 'field::wand_a_nachbar' ),
					),
				),
				'wand_a_daemmung_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Jahr der nachträglichen Dämmung', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Jahr der nachträglichen Dämmung an Wand %s an.', 'wpenon' ), 'a' ),
					'min'                   => 1800,					
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_daemmung_baujahr',
						'callback_args' => array( 'field::wand_a_daemmung', 'field::baujahr' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_daemmung_baujahr',
					'validate_dependencies' => array( 'baujahr' ),
					'default' => 0,
				),
				'wand_b_headline'           => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Wand %s', 'wpenon' ), 'b' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'b' ),
					),
				),
				'wand_b_laenge'             => array(
					'type'     => 'float_length_wall',
					'label'    => __( 'Länge', 'wpenon' ),
					'required' => true,
					'unit'     => 'm',
					'max'      => 50,
					'value'    => array(
						'callback'      => 'wpenon_immoticket24_calculate_wand',
						'callback_args' => array(
							'field::grundriss_form',
							'b',
							'field::wand_a_laenge',
							0.0,
							'field::wand_c_laenge',
							'field::wand_d_laenge',
							'field::wand_e_laenge',
							'field::wand_f_laenge',
							'field::wand_g_laenge',
							'field::wand_h_laenge'
						),
						'callback_hard' => true,
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'b' ),
					),
				),
				'wand_b_nachbar'            => array(
					'type'    => 'checkbox',
					'label'   => sprintf( __( 'Wand %s grenzt an Nachbargebäude?', 'wpenon' ), 'b' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'b' ),
					),
				),
				'wand_b_daemmung'           => array(
					'type'    => 'int',
					'label'   => __( 'Nachträgliche Dämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'b', 'field::wand_b_nachbar' ),
					),
				),
				'wand_b_daemmung_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Jahr der nachträglichen Dämmung', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Jahr der nachträglichen Dämmung an Wand %s an.', 'wpenon' ), 'b' ),
					'min'                   => 1800,					
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_daemmung_baujahr',
						'callback_args' => array( 'field::wand_b_daemmung', 'field::baujahr' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_daemmung_baujahr',
					'validate_dependencies' => array( 'baujahr' ),
					'default' => 0,
				),
				'wand_c_headline'           => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Wand %s', 'wpenon' ), 'c' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'c' ),
					),
				),
				'wand_c_laenge'             => array(
					'type'     => 'float_length_wall',
					'label'    => __( 'Länge', 'wpenon' ),
					'required' => true,
					'unit'     => 'm',
					'max'      => 50,
					'value'    => array(
						'callback'      => 'wpenon_immoticket24_calculate_wand',
						'callback_args' => array(
							'field::grundriss_form',
							'c',
							'field::wand_a_laenge',
							'field::wand_b_laenge',
							0.0,
							'field::wand_d_laenge',
							'field::wand_e_laenge',
							'field::wand_f_laenge',
							'field::wand_g_laenge',
							'field::wand_h_laenge'
						),
						'callback_hard' => true,
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'c' ),
					),
				),
				'wand_c_nachbar'            => array(
					'type'    => 'checkbox',
					'label'   => sprintf( __( 'Wand %s grenzt an Nachbargebäude?', 'wpenon' ), 'c' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'c' ),
					),
				),
				'wand_c_daemmung'           => array(
					'type'    => 'int',
					'label'   => __( 'Nachträgliche Dämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'c', 'field::wand_c_nachbar' ),
					),
				),
				'wand_c_daemmung_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Jahr der nachträglichen Dämmung', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Jahr der nachträglichen Dämmung an Wand %s an.', 'wpenon' ), 'c' ),
					'min'                   => 1800,					
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_daemmung_baujahr',
						'callback_args' => array( 'field::wand_c_daemmung', 'field::baujahr' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_daemmung_baujahr',
					'validate_dependencies' => array( 'baujahr' ),
					'default' => 0,
				),
				'wand_d_headline'           => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Wand %s', 'wpenon' ), 'd' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'd' ),
					),
				),
				'wand_d_laenge'             => array(
					'type'     => 'float_length_wall',
					'label'    => __( 'Länge', 'wpenon' ),
					'required' => true,
					'unit'     => 'm',
					'max'      => 50,
					'value'    => array(
						'callback'      => 'wpenon_immoticket24_calculate_wand',
						'callback_args' => array(
							'field::grundriss_form',
							'd',
							'field::wand_a_laenge',
							'field::wand_b_laenge',
							'field::wand_c_laenge',
							0.0,
							'field::wand_e_laenge',
							'field::wand_f_laenge',
							'field::wand_g_laenge',
							'field::wand_h_laenge'
						),
						'callback_hard' => true,
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'd' ),
					),
				),
				'wand_d_nachbar'            => array(
					'type'    => 'checkbox',
					'label'   => sprintf( __( 'Wand %s grenzt an Nachbargebäude?', 'wpenon' ), 'd' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'd' ),
					),
				),
				'wand_d_daemmung'           => array(
					'type'    => 'int',
					'label'   => __( 'Nachträgliche Dämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'd', 'field::wand_d_nachbar' ),
					),
				),
				'wand_d_daemmung_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Jahr der nachträglichen Dämmung', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Jahr der nachträglichen Dämmung an Wand %s an.', 'wpenon' ), 'a' ),
					'min'                   => 1800,					
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_daemmung_baujahr',
						'callback_args' => array( 'field::wand_d_daemmung', 'field::baujahr' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_daemmung_baujahr',
					'validate_dependencies' => array( 'baujahr' ),
					'default' => 0,
				),
				'wand_e_headline'           => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Wand %s', 'wpenon' ), 'e' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'e' ),
					),
				),
				'wand_e_laenge'             => array(
					'type'     => 'float_length_wall',
					'label'    => __( 'Länge', 'wpenon' ),
					'required' => true,
					'unit'     => 'm',
					'max'      => 50,
					'value'    => array(
						'callback'      => 'wpenon_immoticket24_calculate_wand',
						'callback_args' => array(
							'field::grundriss_form',
							'e',
							'field::wand_a_laenge',
							'field::wand_b_laenge',
							'field::wand_c_laenge',
							'field::wand_d_laenge',
							0.0,
							'field::wand_f_laenge',
							'field::wand_g_laenge',
							'field::wand_h_laenge'
						),
						'callback_hard' => true,
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'e' ),
					),
				),
				'wand_e_nachbar'            => array(
					'type'    => 'checkbox',
					'label'   => sprintf( __( 'Wand %s grenzt an Nachbargebäude?', 'wpenon' ), 'e' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'e' ),
					),
				),
				'wand_e_daemmung'           => array(
					'type'    => 'int',
					'label'   => __( 'Nachträgliche Dämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'e', 'field::wand_e_nachbar' ),
					),
				),
				'wand_e_daemmung_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Jahr der nachträglichen Dämmung', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Jahr der nachträglichen Dämmung an Wand %s an.', 'wpenon' ), 'a' ),
					'min'                   => 1800,					
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_daemmung_baujahr',
						'callback_args' => array( 'field::wand_e_daemmung', 'field::baujahr' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_daemmung_baujahr',
					'validate_dependencies' => array( 'baujahr' ),
					'default' => 0,
				),
				'wand_f_headline'           => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Wand %s', 'wpenon' ), 'f' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'f' ),
					),
				),
				'wand_f_laenge'             => array(
					'type'     => 'float_length_wall',
					'label'    => __( 'Länge', 'wpenon' ),
					'required' => true,
					'unit'     => 'm',
					'max'      => 50,
					'value'    => array(
						'callback'      => 'wpenon_immoticket24_calculate_wand',
						'callback_args' => array(
							'field::grundriss_form',
							'f',
							'field::wand_a_laenge',
							'field::wand_b_laenge',
							'field::wand_c_laenge',
							'field::wand_d_laenge',
							'field::wand_e_laenge',
							0.0,
							'field::wand_g_laenge',
							'field::wand_h_laenge'
						),
						'callback_hard' => true,
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'f' ),
					),
				),
				'wand_f_nachbar'            => array(
					'type'    => 'checkbox',
					'label'   => sprintf( __( 'Wand %s grenzt an Nachbargebäude?', 'wpenon' ), 'f' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'f' ),
					),
				),
				'wand_f_daemmung'           => array(
					'type'    => 'int',
					'label'   => __( 'Nachträgliche Dämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'f', 'field::wand_f_nachbar' ),
					),
				),
				'wand_f_daemmung_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Jahr der nachträglichen Dämmung', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Jahr der nachträglichen Dämmung an Wand %s an.', 'wpenon' ), 'a' ),
					'min'                   => 1800,					
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_daemmung_baujahr',
						'callback_args' => array( 'field::wand_f_daemmung', 'field::baujahr' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_daemmung_baujahr',
					'validate_dependencies' => array( 'baujahr' ),
					'default' => 0,
				),
				'wand_g_headline'           => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Wand %s', 'wpenon' ), 'g' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'g' ),
					),
				),
				'wand_g_laenge'             => array(
					'type'     => 'float_length_wall',
					'label'    => __( 'Länge', 'wpenon' ),
					'required' => true,
					'unit'     => 'm',
					'max'      => 50,
					'value'    => array(
						'callback'      => 'wpenon_immoticket24_calculate_wand',
						'callback_args' => array(
							'field::grundriss_form',
							'g',
							'field::wand_a_laenge',
							'field::wand_b_laenge',
							'field::wand_c_laenge',
							'field::wand_d_laenge',
							'field::wand_e_laenge',
							'field::wand_f_laenge',
							0.0,
							'field::wand_h_laenge'
						),
						'callback_hard' => true,
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'g' ),
					),
				),
				'wand_g_nachbar'            => array(
					'type'    => 'checkbox',
					'label'   => sprintf( __( 'Wand %s grenzt an Nachbargebäude?', 'wpenon' ), 'g' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'g' ),
					),
				),
				'wand_g_daemmung'           => array(
					'type'    => 'int',
					'label'   => __( 'Nachträgliche Dämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'g', 'field::wand_g_nachbar' ),
					),
				),
				'wand_g_daemmung_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Jahr der nachträglichen Dämmung', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Jahr der nachträglichen Dämmung an Wand %s an.', 'wpenon' ), 'a' ),
					'min'                   => 1800,					
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_daemmung_baujahr',
						'callback_args' => array( 'field::wand_g_daemmung', 'field::baujahr' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_daemmung_baujahr',
					'validate_dependencies' => array( 'baujahr' ),
					'default' => 0,
				),
				'wand_h_headline'           => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Wand %s', 'wpenon' ), 'h' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'h' ),
					),
				),
				'wand_h_laenge'             => array(
					'type'     => 'float_length_wall',
					'label'    => __( 'Länge', 'wpenon' ),
					'required' => true,
					'unit'     => 'm',
					'value'    => array(
						'callback'      => 'wpenon_immoticket24_calculate_wand',
						'callback_args' => array(
							'field::grundriss_form',
							'h',
							'field::wand_a_laenge',
							'field::wand_b_laenge',
							'field::wand_c_laenge',
							'field::wand_d_laenge',
							'field::wand_e_laenge',
							'field::wand_f_laenge',
							'field::wand_g_laenge',
							0.0
						),
						'callback_hard' => true,
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'h' ),
					),
				),
				'wand_h_nachbar'            => array(
					'type'    => 'checkbox',
					'label'   => sprintf( __( 'Wand %s grenzt an Nachbargebäude?', 'wpenon' ), 'h' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'h' ),
					),
				),
				'wand_h_daemmung'           => array(
					'type'    => 'int',
					'label'   => __( 'Nachträgliche Dämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'h', 'field::wand_h_nachbar' ),
					),
				),
				'wand_h_daemmung_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Jahr der nachträglichen Dämmung', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Jahr der nachträglichen Dämmung an Wand %s an.', 'wpenon' ), 'a' ),
					'min'                   => 1800,					
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_daemmung_baujahr',
						'callback_args' => array( 'field::wand_h_daemmung', 'field::baujahr' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_daemmung_baujahr',
					'validate_dependencies' => array( 'baujahr' ),
					'default' => 0,
				),
				'anbauwand_headline'        => array(
					'type'    => 'headline',
					'label'   => __( 'Anbau', 'wpenon' ),
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
				),
				'anbauwand_staerke'    => array(
					'type'        => 'select',
					'label'       => __( 'Wandstärke Anbau', 'wpenon' ),
					'description' => __( 'Wählen Sie die Wandstärke der Außenwand aus.', 'wpenon' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_wandstaerken(),
					'required'    => true,
					'unit'     => 'cm',
				),
				'anbauwand_bauart_holz'     => array(
					'type'        => 'select',
					'label'       => __( 'Anbau-Wandbauart', 'wpenon' ),
					'description' => __( 'Wählen Sie die Bauart der Außenwand aus.', 'wpenon' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_bauarten_holzhaus(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_anbauwand_bauart',
						'callback_args' => array( 'field::anbau', 'field::gebaeudekonstruktion', 'holz' ),
					),
				),
				'anbauwand_bauart_fachwerk' => array(
					'type'        => 'select',
					'label'       => __( 'Anbau-Wandbauart', 'wpenon' ),
					'description' => __( 'Wählen Sie die Bauart der Außenwand aus.', 'wpenon' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_bauarten_fachwerkaus(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_anbauwand_bauart',
						'callback_args' => array( 'field::anbau', 'field::gebaeudekonstruktion', 'fachwerk' ),
					),
				),
				'anbauwand_bauart_massiv'   => array(
					'type'        => 'select',
					'label'       => __( 'Anbau-Wandbauart', 'wpenon' ),
					'description' => __( 'Wählen Sie die Bauart der Außenwand aus.', 'wpenon' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_bauarten_massiv(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_anbauwand_bauart',
						'callback_args' => array( 'field::anbau', 'field::gebaeudekonstruktion', 'massiv' ),
					),
				),
				'anbauwand_daemmung'        => array(
					'type'    => 'int',
					'label'   => __( 'Nachträgliche Anbau-Wanddämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
				),
				'anbauwand_daemmung_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Jahr der nachträglichen Dämmung', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Jahr der nachträglichen Dämmung an Wand %s an.', 'wpenon' ), 'a' ),
					'min'                   => 1800,					
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_daemmung_baujahr',
						'callback_args' => array( 'field::anbauwand_daemmung', 'field::baujahr' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_daemmung_baujahr',
					'validate_dependencies' => array( 'baujahr' ),
					'default' => 0,
				),
				'anbauwand_b_laenge'        => array(
					'type'     => 'float_length',
					'label'    => __( 'Anbaubreite b', 'wpenon' ),
					'required' => true,
					'unit'     => 'm',
					'value'    => array(
						'callback'      => 'wpenon_immoticket24_calculate_anbauwand',
						'callback_args' => array(
							'field::anbau_form',
							'b',
							0.0,
							'field::anbauwand_t_laenge',
							'field::anbauwand_s1_laenge',
							'field::anbauwand_s2_laenge'
						),
						'callback_hard' => true,
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_anbauwand',
						'callback_args' => array( 'field::anbau_form', 'b', 'field::anbau' ),
					),
				),
				'anbauwand_t_laenge'        => array(
					'type'     => 'float_length',
					'label'    => __( 'Anbautiefe t', 'wpenon' ),
					'required' => true,
					'unit'     => 'm',
					'value'    => array(
						'callback'      => 'wpenon_immoticket24_calculate_anbauwand',
						'callback_args' => array(
							'field::anbau_form',
							't',
							'field::anbauwand_b_laenge',
							0.0,
							'field::anbauwand_s1_laenge',
							'field::anbauwand_s2_laenge'
						),
						'callback_hard' => true,
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_anbauwand',
						'callback_args' => array( 'field::anbau_form', 't', 'field::anbau' ),
					),
				),
				'anbauwand_s1_laenge'       => array(
					'type'     => 'float_length',
					'label'    => __( 'Anbau-Schnittlänge s1', 'wpenon' ),
					'required' => true,
					'unit'     => 'm',
					'value'    => array(
						'callback'      => 'wpenon_immoticket24_calculate_anbauwand',
						'callback_args' => array(
							'field::anbau_form',
							's1',
							'field::anbauwand_b_laenge',
							'field::anbauwand_t_laenge',
							0.0,
							'field::anbauwand_s2_laenge'
						),
						'callback_hard' => true,
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_anbauwand',
						'callback_args' => array( 'field::anbau_form', 's1', 'field::anbau' ),
					),
				),
				'anbauwand_s2_laenge'       => array( // Hidden by CSS!
					'type'     => 'float_length',
					'label'    => __( 'Anbau-Schnittlänge s2', 'wpenon' ),
					'required' => true,
					'unit'     => 'm',
					'value'    => array(
						'callback'      => 'wpenon_immoticket24_calculate_anbauwand',
						'callback_args' => array(
							'field::anbau_form',
							's2',
							'field::anbauwand_b_laenge',
							'field::anbauwand_t_laenge',
							'field::anbauwand_s1_laenge',
							0.0
						),
						'callback_hard' => true,
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_anbauwand',
						'callback_args' => array( 'field::anbau_form', 's2', 'field::anbau' ),
					),
				),
			),
		),
		'bauteile_dach'    => array(
			'title'       => __( 'Dach', 'wpenon' ),
			'description' => __( 'Geben Sie die relevanten Daten für das Dachgeschoss des Gebäudes an.', 'wpenon' ),
			'fields'      => array(
				'dach'               => array(
					'type'     => 'select',
					'label'    => __( 'Dachgeschoss', 'wpenon' ),
					'options'  => array(
						'nicht-vorhanden' => __( 'nicht vorhanden (Flachdach)', 'wpenon' ),
						'unbeheizt'       => __( 'unbeheizt', 'wpenon' ),
						'beheizt'         => __( 'beheizt', 'wpenon' ),
					),
					'required' => true,
				),
				'dach_form'          => array(
					'type'        => 'select',
					'label'       => __( 'Dachtyp', 'wpenon' ),
					'description' => __( 'Falls das Dach Ihres Hauses nicht einer dieser Formen entspricht, wählen Sie hier bitte die Dachform aus, die Ihrem Dach am nächsten kommt.', 'wpenon' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_dach_formen(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::dach', 'beheizt' ),
					),
				),
				'dach_hoehe'         => array(
					'type'        => 'float_length',
					'label'       => __( 'Dachhöhe', 'wpenon' ),
					'description' => __( 'Geben Sie hier die lichte Höhe des Dachgeschosses an. Die Höhe vom Boden des DG bis zur Decke des DG bzw. falls DG offen dann bis zum Giebel.', 'wpenon' ),
					'default'     => 3.0,
					'max'         => 8.0,
					'required'    => true,
					'unit'        => 'm',
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::dach', 'beheizt' ),
					),
				),
				'dach_bauart'        => array(
					'type'        => 'select',
					'label'       => __( 'Dachbauart', 'wpenon' ),
					'description' => __( 'Wählen Sie die Bauart des Daches aus.', 'wpenon' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::dach', 'nicht-vorhanden' ),
					),
				),
				'dach_daemmung'      => array(
					'type'        => 'int',
					'label'       => __( 'Nachträgliche Dachdämmung', 'wpenon' ),
					'description' => __( 'Falls das Dach nachträglich gedämmt worden ist, geben Sie hier dessen Dämmstärke in Zentimetern an.', 'wpenon' ),
					'unit'        => 'cm',
					'max'         => 30,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_blacklist',
						'callback_args' => array( 'field::dach', 'unbeheizt' ),
					),
				),
				'dach_daemmung_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Jahr der nachträglichen Dämmung', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Jahr der nachträglichen Dämmung an Wand %s an.', 'wpenon' ), 'a' ),
					'min'                   => 1800,					
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_daemmung_baujahr',
						'callback_args' => array( 'field::dach_daemmung', 'field::baujahr' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_daemmung_baujahr',
					'validate_dependencies' => array( 'baujahr' ),
					'default' => 0,
				),
				'decke_bauart'       => array(
					'type'        => 'select',
					'label'       => __( 'Deckenbauart', 'wpenon' ),
					'description' => __( 'Wählen Sie die Bauart der Obersten Geschossdecke aus.', 'wpenon' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::dach', 'unbeheizt' ),
					),
				),
				'decke_daemmung'     => array(
					'type'        => 'int',
					'label'       => __( 'Nachträgliche Deckendämmung', 'wpenon' ),
					'description' => __( 'Falls die Oberste Geschossdecke nachträglich gedämmt worden ist, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon' ),
					'unit'        => 'cm',
					'max'         => 30,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::dach', 'unbeheizt' ),
					),
				),
				'decke_daemmung_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Jahr der nachträglichen Dämmung', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Jahr der nachträglichen Dämmung an Wand %s an.', 'wpenon' ), 'a' ),
					'min'                   => 1800,					
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_daemmung_baujahr',
						'callback_args' => array( 'field::decke_daemmung', 'field::baujahr' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_daemmung_baujahr',
					'validate_dependencies' => array( 'baujahr' ),
					'default' => 0,
				),
				'anbaudach_headline' => array(
					'type'    => 'headline',
					'label'   => __( 'Anbau', 'wpenon' ),
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
				),
				'anbaudach_bauart'   => array(
					'type'        => 'select',
					'label'       => __( 'Anbau-Dachbauart', 'wpenon' ),
					'description' => __( 'Wählen Sie die Bauart des Dachs des Anbaus aus.', 'wpenon' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
				),
				'anbaudach_daemmung' => array(
					'type'    => 'int',
					'label'   => __( 'Nachträgliche Anbau-Dachdämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 30,
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
				),
				'anbaudach_daemmung_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Jahr der nachträglichen Dämmung', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Jahr der nachträglichen Dämmung an Wand %s an.', 'wpenon' ), 'a' ),
					'min'                   => 1800,					
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_daemmung_baujahr',
						'callback_args' => array( 'field::anbaudach_daemmung', 'field::baujahr' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_daemmung_baujahr',
					'validate_dependencies' => array( 'baujahr' ),
					'default' => 0,				
				),
			),
		),
		'bauteile_keller'  => array(
			'title'       => __( 'Keller', 'wpenon' ),
			'description' => __( 'Geben Sie die relevanten Daten für das Kellergeschoss des Gebäudes an.', 'wpenon' ),
			'fields'      => array(
				'keller'              => array(
					'type'     => 'select',
					'label'    => __( 'Kellergeschoss', 'wpenon' ),
					'options'  => array(
						'nicht-vorhanden' => __( 'nicht vorhanden', 'wpenon' ),
						'unbeheizt'       => __( 'unbeheizt', 'wpenon' ),
						'beheizt'         => __( 'beheizt', 'wpenon' ),
					),
					'required' => true,
				),
				'keller_groesse'      => array(
					'type'        => 'int',
					'label'       => __( 'Unterkellerung', 'wpenon' ),
					'description' => __( 'Geben Sie den Anteil der Unterkellerung des Gebäudes in Bezug auf die Grundfläche ein.', 'wpenon' ),
					'default'     => 100,
					'min'         => 5,
					'max'         => 100,
					'required'    => true,
					'unit'        => '%',
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_blacklist',
						'callback_args' => array( 'field::keller', 'nicht-vorhanden' ),
					),
				),
				'keller_hoehe'        => array(
					'type'        => 'float_length',
					'label'       => __( 'Kellerhöhe', 'wpenon' ),
					'description' => __( 'Geben Sie die lichte Höhe des Kellers in Metern ein.', 'wpenon' ),
					'default'     => 2.1,
					'max'         => 5.0,
					'required'    => true,
					'unit'        => 'm',
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::keller', 'beheizt' ),
					),
				),
				'keller_bauart'       => array(
					'type'        => 'select',
					'label'       => __( 'Kellerwandbauart', 'wpenon' ),
					'description' => __( 'Wählen Sie die Bauart der Kellerwand aus.', 'wpenon' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_bauarten_keller(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::keller', 'beheizt' ),
					),
				),
				'keller_daemmung'     => array(
					'type'        => 'int',
					'label'       => __( 'Nachträgliche Kellerwanddämmung', 'wpenon' ),
					'description' => __( 'Falls die Kellerwände nachträglich gedämmt worden sind, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon' ),
					'unit'        => 'cm',
					'max'         => 23,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::keller', 'beheizt' ),
					),
				),
				'keller_daemmung_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Jahr der nachträglichen Dämmung', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Jahr der nachträglichen Dämmung an Wand %s an.', 'wpenon' ), 'a' ),
					'min'                   => 1800,					
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_daemmung_baujahr',
						'callback_args' => array( 'field::keller_daemmung', 'field::baujahr' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_daemmung_baujahr',
					'validate_dependencies' => array( 'baujahr' ),
					'default' => 0,
				),
				'boden_bauart'        => array(
					'type'        => 'select',
					'label'       => __( 'Bodenbauart', 'wpenon' ),
					'description' => __( 'Wählen Sie die Bauart der Bodenplatte / Kellerdecke aus.', 'wpenon' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_bauarten_boden(),
					'required'    => true,
				),
				'boden_daemmung'      => array(
					'type'        => 'int',
					'label'       => __( 'Nachträgliche Bodendämmung', 'wpenon' ),
					'description' => __( 'Falls die Bodenplatte / Kellerdecke nachträglich gedämmt worden ist, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon' ),
					'unit'        => 'cm',
					'max'         => 25,
				),
				'boden_daemmung_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Jahr der nachträglichen Dämmung', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Jahr der nachträglichen Dämmung an Wand %s an.', 'wpenon' ), 'a' ),
					'min'                   => 1800,					
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_daemmung_baujahr',
						'callback_args' => array( 'field::boden_daemmung', 'field::baujahr' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_daemmung_baujahr',
					'validate_dependencies' => array( 'baujahr' ),
					'default' => 0,
				),		
				'anbauboden_headline' => array(
					'type'    => 'headline',
					'label'   => __( 'Anbau', 'wpenon' ),
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
				),
				'anbauboden_bauart'   => array(
					'type'        => 'select',
					'label'       => __( 'Anbau-Bodenbauart', 'wpenon' ),
					'description' => __( 'Wählen Sie die Bauart des Bodens des Anbaus aus.', 'wpenon' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_bauarten_boden(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
				),
				'anbauboden_daemmung' => array(
					'type'    => 'int',
					'label'   => __( 'Nachträgliche Anbau-Bodendämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 25,
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
				),
				'anbauboden_daemmung_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Jahr der nachträglichen Dämmung', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Jahr der nachträglichen Dämmung an Wand %s an.', 'wpenon' ), 'a' ),
					'min'                   => 1800,					
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_daemmung_baujahr',
						'callback_args' => array( 'field::anbauboden_daemmung', 'field::baujahr' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_daemmung_baujahr',
					'validate_dependencies' => array( 'baujahr' ),
					'default' => 0,
				),					
			),
		),
		'bauteile_fenster' => array(
			'title'       => __( 'Fenster', 'wpenon' ),
			'description' => __( 'Geben Sie die relevanten Daten für die Fenster des Gebäudes an.', 'wpenon' ),
			'fields'      => array(
				'fenster_manuell'                                       => array(
					'type'  => 'checkbox',
					'label' => __( 'Fensterflächen manuell eingeben', 'wpenon' ),
				),
				'fenster_bauart' => array(
					'type' => 'select',
					'label' => __('Bauart', 'wpenon'),
					'description' => __('Wählen Sie die Bauart der Fenster des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon'),
					'options' => wpenon_immoticket24_get_fenster_bauarten(),
					'required' => true,
					'display' => array(
						'callback' => 'wpenon_show_on_bool_compare',
						'callback_args' => array('field::fenster_manuell', false),
					),
				),
				'fenster_baujahr' => array(
					'type' => 'int',
					'label' => __('Baujahr', 'wpenon'),
					'description' => __('Geben Sie das Baujahr der Fenster des Gebäudes an.', 'wpenon'),
					'min' => 1800,
					'max' => wpenon_get_reference_date('Y'),
					'required' => true,
					'display' => array(
						'callback' => 'wpenon_show_on_bool_compare',
						'callback_args' => array('field::fenster_manuell', false),
					),
					'validate' => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array('baujahr'),
				),
				'fenster_a_headline'       => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Fenster %s', 'wpenon' ), 'a' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::grundriss_form', 'a', 'field::wand_a_nachbar' ),
					),
				),				
				'fenster_a_flaeche'        => array(
					'type'                  => 'float',
					'label'                 => __( 'Fläche', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie die gesamte Fläche aller Öffnungen des Baukörpers nach außen (Fensterflächen & Haustüren) an Seite %s des Gebäudes ein.', 'wpenon' ), 'a' ),
					'unit'                  => 'm&sup2;',
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::grundriss_form', 'a', 'field::wand_a_nachbar' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_fenster',
					'validate_dependencies' => array(						
						'fenster_b_flaeche',
						'fenster_c_flaeche',
						'fenster_d_flaeche',
						'fenster_e_flaeche',
						'fenster_f_flaeche',
						'fenster_g_flaeche',
						'fenster_h_flaeche',
						'wand_a_nachbar',
						'wand_b_nachbar',
						'wand_c_nachbar',
						'wand_d_nachbar',
						'wand_e_nachbar',
						'wand_f_nachbar',
						'wand_g_nachbar',
						'wand_h_nachbar'
					),
				),
				'fenster_a_bauart'         => array(
					'type'        => 'select',
					'label'       => __( 'Bauart', 'wpenon' ),
					'description' => sprintf( __( 'Wählen Sie die Bauart der Fenster an Seite %s des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon' ), 'a' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_fenster_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::grundriss_form',
							'a',
							'field::wand_a_nachbar',
							'field::fenster_a_flaeche'
						),
					),
				),
				'fenster_a_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Baujahr der Fenster an Seite %s des Gebäudes an.', 'wpenon' ), 'a' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::grundriss_form',
							'a',
							'field::wand_a_nachbar',
							'field::fenster_a_flaeche'
						),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
                    'validate_dependencies' => array( 'baujahr' ),
                    'value'                 => array(
						'callback'      => 'wpenon_get_construction_year',
						'callback_args' => array( 'field::baujahr', 'field::fenster_a_baujahr' ),
					),
				),
				'fenster_b_headline'       => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Fenster %s', 'wpenon' ), 'b' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::grundriss_form', 'b', 'field::wand_b_nachbar' ),
					),
				),
				'fenster_b_flaeche'        => array(
					'type'        => 'float',
					'label'       => __( 'Fläche', 'wpenon' ),
					'description' => sprintf( __( 'Geben Sie die gesamte Fläche aller Öffnungen des Baukörpers nach außen (Fensterflächen & Haustüren) an Seite %s des Gebäudes ein.', 'wpenon' ), 'b' ),
					'unit'        => 'm&sup2;',
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::grundriss_form', 'b', 'field::wand_b_nachbar' ),
					),
				),
				'fenster_b_bauart'         => array(
					'type'        => 'select',
					'label'       => __( 'Bauart', 'wpenon' ),
					'description' => sprintf( __( 'Wählen Sie die Bauart der Fenster an Seite %s des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon' ), 'b' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_fenster_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::grundriss_form',
							'b',
							'field::wand_b_nachbar',
							'field::fenster_b_flaeche'
						),
					),
				),
				'fenster_b_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Baujahr der Fenster an Seite %s des Gebäudes an.', 'wpenon' ), 'b' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::grundriss_form',
							'b',
							'field::wand_b_nachbar',
							'field::fenster_b_flaeche'
						),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
					'value'                 => array(
						'callback'      => 'wpenon_get_construction_year',
						'callback_args' => array( 'field::baujahr', 'field::fenster_b_baujahr' ),
					),
				),
				'fenster_c_headline'       => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Fenster %s', 'wpenon' ), 'c' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::grundriss_form', 'c', 'field::wand_c_nachbar' ),
					),
				),
				'fenster_c_flaeche'        => array(
					'type'        => 'float',
					'label'       => __( 'Fläche', 'wpenon' ),
					'description' => sprintf( __( 'Geben Sie die gesamte Fläche aller Öffnungen des Baukörpers nach außen (Fensterflächen & Haustüren) an Seite %s des Gebäudes ein.', 'wpenon' ), 'c' ),
					'unit'        => 'm&sup2;',
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::grundriss_form', 'c', 'field::wand_c_nachbar' ),
					),
				),
				'fenster_c_bauart'         => array(
					'type'        => 'select',
					'label'       => __( 'Bauart', 'wpenon' ),
					'description' => sprintf( __( 'Wählen Sie die Bauart der Fenster an Seite %s des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon' ), 'c' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_fenster_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::grundriss_form',
							'c',
							'field::wand_c_nachbar',
							'field::fenster_c_flaeche'
						),
					),
				),
				'fenster_c_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Baujahr der Fenster an Seite %s des Gebäudes an.', 'wpenon' ), 'c' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::grundriss_form',
							'c',
							'field::wand_c_nachbar',
							'field::fenster_c_flaeche'
						),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
					'value'                 => array(
						'callback'      => 'wpenon_get_construction_year',
						'callback_args' => array( 'field::baujahr', 'field::fenster_c_baujahr' ),
					),
				),
				'fenster_d_headline'       => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Fenster %s', 'wpenon' ), 'd' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::grundriss_form', 'd', 'field::wand_d_nachbar' ),
					),
				),
				'fenster_d_flaeche'        => array(
					'type'        => 'float',
					'label'       => __( 'Fläche', 'wpenon' ),
					'description' => sprintf( __( 'Geben Sie die gesamte Fläche aller Öffnungen des Baukörpers nach außen (Fensterflächen & Haustüren) an Seite %s des Gebäudes ein.', 'wpenon' ), 'd' ),
					'unit'        => 'm&sup2;',
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::grundriss_form', 'd', 'field::wand_d_nachbar' ),
					),
				),
				'fenster_d_bauart'         => array(
					'type'        => 'select',
					'label'       => __( 'Bauart', 'wpenon' ),
					'description' => sprintf( __( 'Wählen Sie die Bauart der Fenster an Seite %s des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon' ), 'd' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_fenster_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::grundriss_form',
							'd',
							'field::wand_d_nachbar',
							'field::fenster_d_flaeche'
						),
					),
				),
				'fenster_d_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Baujahr der Fenster an Seite %s des Gebäudes an.', 'wpenon' ), 'd' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::grundriss_form',
							'd',
							'field::wand_d_nachbar',
							'field::fenster_d_flaeche'
						),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
					'value'                 => array(
						'callback'      => 'wpenon_get_construction_year',
						'callback_args' => array( 'field::baujahr', 'field::fenster_d_baujahr' ),
					),
				),
				'fenster_e_headline'       => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Fenster %s', 'wpenon' ), 'e' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::grundriss_form', 'e', 'field::wand_e_nachbar' ),
					),
				),
				'fenster_e_flaeche'        => array(
					'type'        => 'float',
					'label'       => __( 'Fläche', 'wpenon' ),
					'description' => sprintf( __( 'Geben Sie die gesamte Fläche aller Öffnungen des Baukörpers nach außen (Fensterflächen & Haustüren) an Seite %s des Gebäudes ein.', 'wpenon' ), 'e' ),
					'unit'        => 'm&sup2;',
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::grundriss_form', 'e', 'field::wand_e_nachbar' ),
					),
				),
				'fenster_e_bauart'         => array(
					'type'        => 'select',
					'label'       => __( 'Bauart', 'wpenon' ),
					'description' => sprintf( __( 'Wählen Sie die Bauart der Fenster an Seite %s des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon' ), 'e' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_fenster_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::grundriss_form',
							'e',
							'field::wand_e_nachbar',
							'field::fenster_e_flaeche'
						),
					),
				),
				'fenster_e_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Baujahr der Fenster an Seite %s des Gebäudes an.', 'wpenon' ), 'e' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::grundriss_form',
							'e',
							'field::wand_e_nachbar',
							'field::fenster_e_flaeche'
						),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
					'value'                 => array(
						'callback'      => 'wpenon_get_construction_year',
						'callback_args' => array( 'field::baujahr', 'field::fenster_e_baujahr' ),
					),
				),
				'fenster_f_headline'       => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Fenster %s', 'wpenon' ), 'f' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::grundriss_form', 'f', 'field::wand_f_nachbar' ),
					),
				),
				'fenster_f_flaeche'        => array(
					'type'        => 'float',
					'label'       => __( 'Fläche', 'wpenon' ),
					'description' => sprintf( __( 'Geben Sie die gesamte Fläche aller Öffnungen des Baukörpers nach außen (Fensterflächen & Haustüren) an Seite %s des Gebäudes ein.', 'wpenon' ), 'f' ),
					'unit'        => 'm&sup2;',
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::grundriss_form', 'f', 'field::wand_f_nachbar' ),
					),
				),
				'fenster_f_bauart'         => array(
					'type'        => 'select',
					'label'       => __( 'Bauart', 'wpenon' ),
					'description' => sprintf( __( 'Wählen Sie die Bauart der Fenster an Seite %s des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon' ), 'f' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_fenster_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::grundriss_form',
							'f',
							'field::wand_f_nachbar',
							'field::fenster_f_flaeche'
						),
					),
				),
				'fenster_f_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Baujahr der Fenster an Seite %s des Gebäudes an.', 'wpenon' ), 'f' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::grundriss_form',
							'f',
							'field::wand_f_nachbar',
							'field::fenster_f_flaeche'
						),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
					'value'                 => array(
						'callback'      => 'wpenon_get_construction_year',
						'callback_args' => array( 'field::baujahr', 'field::fenster_f_baujahr' ),
					),
				),
				'fenster_g_headline'       => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Fenster %s', 'wpenon' ), 'g' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::grundriss_form', 'g', 'field::wand_g_nachbar' ),
					),
				),
				'fenster_g_flaeche'        => array(
					'type'        => 'float',
					'label'       => __( 'Fläche', 'wpenon' ),
					'description' => sprintf( __( 'Geben Sie die gesamte Fläche aller Öffnungen des Baukörpers nach außen (Fensterflächen & Haustüren) an Seite %s des Gebäudes ein.', 'wpenon' ), 'g' ),
					'unit'        => 'm&sup2;',
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::grundriss_form', 'g', 'field::wand_g_nachbar' ),
					),
				),
				'fenster_g_bauart'         => array(
					'type'        => 'select',
					'label'       => __( 'Bauart', 'wpenon' ),
					'description' => sprintf( __( 'Wählen Sie die Bauart der Fenster an Seite %s des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon' ), 'g' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_fenster_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::grundriss_form',
							'g',
							'field::wand_g_nachbar',
							'field::fenster_g_flaeche'
						),
					),
				),
				'fenster_g_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Baujahr der Fenster an Seite %s des Gebäudes an.', 'wpenon' ), 'g' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::grundriss_form',
							'g',
							'field::wand_g_nachbar',
							'field::fenster_g_flaeche'
						),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
					'value'                 => array(
						'callback'      => 'wpenon_get_construction_year',
						'callback_args' => array( 'field::baujahr', 'field::fenster_g_baujahr' ),
					),
				),
				'fenster_h_headline'       => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Fenster %s', 'wpenon' ), 'h' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::grundriss_form', 'h', 'field::wand_h_nachbar' ),
					),
				),
				'fenster_h_flaeche'        => array(
					'type'        => 'float',
					'label'       => __( 'Fläche', 'wpenon' ),
					'description' => sprintf( __( 'Geben Sie die gesamte Fläche aller Öffnungen des Baukörpers nach außen (Fensterflächen & Haustüren) an Seite %s des Gebäudes ein.', 'wpenon' ), 'h' ),
					'unit'        => 'm&sup2;',
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::grundriss_form', 'h', 'field::wand_h_nachbar' ),
					),
				),
				'fenster_h_bauart'         => array(
					'type'        => 'select',
					'label'       => __( 'Bauart', 'wpenon' ),
					'description' => sprintf( __( 'Wählen Sie die Bauart der Fenster an Seite %s des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon' ), 'h' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_fenster_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::grundriss_form',
							'h',
							'field::wand_h_nachbar',
							'field::fenster_h_flaeche'
						),
					),
				),
				'fenster_h_baujahr'        => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Baujahr der Fenster an Seite %s des Gebäudes an.', 'wpenon' ), 'h' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_specific_fenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::grundriss_form',
							'h',
							'field::wand_h_nachbar',
							'field::fenster_h_flaeche'
						),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
					'value'                 => array(
						'callback'      => 'wpenon_get_construction_year',
						'callback_args' => array( 'field::baujahr', 'field::fenster_h_baujahr' ),
					),
				),
				'anbaufenster_headline'    => array(
					'type'    => 'headline',
					'label'   => __( 'Anbau-Fenster', 'wpenon' ),
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( array( 'field::fenster_manuell', 'field::anbau' ), array( true, true ) ),
					),
				),
				'anbaufenster_headline_auto'    => array(
					'type'    => 'headline',
					'label'   => __( 'Anbau-Fenster', 'wpenon' ),
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau' ), array( true, true ) ),
					),
				),
				'anbaufenster_bauart' => array(
					'type' => 'select',
					'label' => __('Bauart', 'wpenon'),
					'description' => __('Wählen Sie die Bauart der Fenster des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon'),
					'options' => wpenon_immoticket24_get_fenster_bauarten(),
					'required' => true,
					'display' => array(
						'callback' => 'wpenon_show_on_bool_compare',
						'callback_args' => array('field::fenster_manuell', false),
					),
				),
				'anbaufenster_baujahr' => array(
					'type' => 'int',
					'label' => __('Baujahr', 'wpenon'),
					'description' => __('Geben Sie das Baujahr der Fenster des Gebäudes an.', 'wpenon'),
					'min' => 1800,
					'max' => wpenon_get_reference_date('Y'),
					'required' => true,
					'display' => array(
						'callback' => 'wpenon_show_on_bool_compare',
						'callback_args' => array('field::fenster_manuell', false),
					),
					'validate' => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array('baujahr'),
				),
				'anbaufenster_b_headline'  => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Anbau-Fenster %s', 'wpenon' ), 'b' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_specific_anbaufenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::anbau', 'field::anbau_form', 'b' ),
					),
				),
				'anbaufenster_b_flaeche'   => array(
					'type'        => 'float',
					'label'       => __( 'Fläche', 'wpenon' ),
					'description' => sprintf( __( 'Geben Sie die gesamte Fläche aller Fenster an Seite %s des Anbaus ein.', 'wpenon' ), 'b' ),
					'unit'        => 'm&sup2;',
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_anbaufenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::anbau', 'field::anbau_form', 'b' ),
					),
				),
				'anbaufenster_b_bauart'    => array(
					'type'        => 'select',
					'label'       => __( 'Bauart', 'wpenon' ),
					'description' => sprintf( __( 'Wählen Sie die Bauart der Fenster an Seite %s des Anbaus aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon' ), 'b' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_fenster_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_anbaufenster',
						'callback_args' => array(
							true,
							'field::anbau',
							'field::anbau_form',
							'b',
							'field::anbaufenster_b_flaeche'
						),
					),
				),
				'anbaufenster_b_baujahr'   => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Baujahr der Fenster an Seite %s des Anbaus an.', 'wpenon' ), 'b' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_specific_anbaufenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::anbau',
							'field::anbau_form',
							'b',
							'field::anbaufenster_b_flaeche'
						),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
				),
				'anbaufenster_t_headline'  => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Anbau-Fenster %s', 'wpenon' ), 't' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_specific_anbaufenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::anbau', 'field::anbau_form', 't' ),
					),
				),
				'anbaufenster_t_flaeche'   => array(
					'type'        => 'float',
					'label'       => __( 'Fläche', 'wpenon' ),
					'description' => sprintf( __( 'Geben Sie die gesamte Fläche aller Fenster an Seite %s des Anbaus ein.', 'wpenon' ), 't' ),
					'unit'        => 'm&sup2;',
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_anbaufenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::anbau', 'field::anbau_form', 't' ),
					),
				),
				'anbaufenster_t_bauart'    => array(
					'type'        => 'select',
					'label'       => __( 'Bauart', 'wpenon' ),
					'description' => sprintf( __( 'Wählen Sie die Bauart der Fenster an Seite %s des Anbaus aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon' ), 't' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_fenster_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_anbaufenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::anbau',
							'field::anbau_form',
							't',
							'field::anbaufenster_t_flaeche'
						),
					),
				),
				'anbaufenster_t_baujahr'   => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Baujahr der Fenster an Seite %s des Anbaus an.', 'wpenon' ), 't' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_specific_anbaufenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::anbau',
							'field::anbau_form',
							't',
							'field::anbaufenster_t_flaeche'
						),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
				),
				'anbaufenster_s1_headline' => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Anbau-Fenster %s', 'wpenon' ), 's1' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_specific_anbaufenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::anbau', 'field::anbau_form', 's1' ),
					),
				),
				'anbaufenster_s1_flaeche'  => array(
					'type'        => 'float',
					'label'       => __( 'Fläche', 'wpenon' ),
					'description' => sprintf( __( 'Geben Sie die gesamte Fläche aller Fenster an Seite %s des Anbaus ein.', 'wpenon' ), 's1' ),
					'unit'        => 'm&sup2;',
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_anbaufenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::anbau', 'field::anbau_form', 's1' ),
					),
				),
				'anbaufenster_s1_bauart'   => array(
					'type'        => 'select',
					'label'       => __( 'Bauart', 'wpenon' ),
					'description' => sprintf( __( 'Wählen Sie die Bauart der Fenster an Seite %s des Anbaus aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon' ), 's1' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_fenster_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_anbaufenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::anbau',
							'field::anbau_form',
							's1',
							'field::anbaufenster_s1_flaeche'
						),
					),
				),
				'anbaufenster_s1_baujahr'  => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Baujahr der Fenster an Seite %s des Anbaus an.', 'wpenon' ), 's1' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_specific_anbaufenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::anbau',
							'field::anbau_form',
							's1',
							'field::anbaufenster_s1_flaeche'
						),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
				),
				'anbaufenster_s2_headline' => array(
					'type'    => 'headline',
					'label'   => sprintf( __( 'Anbau-Fenster %s', 'wpenon' ), 's2' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_specific_anbaufenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::anbau', 'field::anbau_form', 's2' ),
					),
				),
				'anbaufenster_s2_flaeche'  => array(
					'type'        => 'float',
					'label'       => __( 'Fläche', 'wpenon' ),
					'description' => sprintf( __( 'Geben Sie die gesamte Fläche aller Fenster an Seite %s des Anbaus ein.', 'wpenon' ), 's2' ),
					'unit'        => 'm&sup2;',
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_anbaufenster',
						'callback_args' => array( 'field::fenster_manuell', 'field::anbau', 'field::anbau_form', 's2' ),
					),
				),
				'anbaufenster_s2_bauart'   => array(
					'type'        => 'select',
					'label'       => __( 'Bauart', 'wpenon' ),
					'description' => sprintf( __( 'Wählen Sie die Bauart der Fenster an Seite %s des Anbaus aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon' ), 's2' ),
					'options'     => \Enev\Schema202002\Schema\Standard_Options::get_fenster_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_specific_anbaufenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::anbau',
							'field::anbau_form',
							's2',
							'field::anbaufenster_s2_flaeche'
						),
					),
				),
				'anbaufenster_s2_baujahr'  => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr', 'wpenon' ),
					'description'           => sprintf( __( 'Geben Sie das Baujahr der Fenster an Seite %s des Anbaus an.', 'wpenon' ), 's2' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_specific_anbaufenster',
						'callback_args' => array(
							'field::fenster_manuell',
							'field::anbau',
							'field::anbau_form',
							's2',
							'field::anbaufenster_s2_flaeche'
						),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
				),
			),
		),
		'bauteile_sonstiges' => array(
			'title'       => __( 'Sonstiges', 'wpenon' ),
			'description' => __( 'Geben Sie die relevanten Daten für sonstige Bauteile des Gebäudes an.', 'wpenon' ),
			'fields'      => array(
				'heizkoerpernischen'       => array(
					'type'     => 'select',
					'label'    => __( 'Heizkörpernischen', 'wpenon' ),
					'options'  => array(
						'nicht-vorhanden' => __( 'nicht vorhanden', 'wpenon' ),
						'vorhanden'       => __( 'vorhanden', 'wpenon' ),
					),
					'required' => true,
				),
				'rollladenkaesten'         => array(
					'type'     => 'select',
					'label'    => __( 'Rollladenkästen', 'wpenon' ),
					'options'  => array(
						'nicht-vorhanden'  => __( 'nicht vorhanden', 'wpenon' ),
						'aussen'           => __( 'außenliegend', 'wpenon' ),
						'innen_ungedaemmt' => __( 'innenliegend, ungedämmt', 'wpenon' ),
						'innen_gedaemmt'   => __( 'innenliegend, gedämmt', 'wpenon' ),
					),
					'required' => true,
				),
			),
		),
	),
);
