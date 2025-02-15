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
					'options'     => \Enev\Schema202401\Schema\Standard_Options::get_wandstaerken(),
					'required'    => true,
					'unit'     => 'cm',
				),
				'wand_bauart_holz'          => array(
					'type'        => 'select',
					'label'       => __( 'Wandbauart', 'wpenon' ),
					'description' => __( 'Wählen Sie die Bauart der Außenwand aus.', 'wpenon' ),
					'options'     => \Enev\Schema202401\Schema\Standard_Options::get_bauarten_holzhaus(),
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
					'options'     => \Enev\Schema202401\Schema\Standard_Options::get_bauarten_fachwerkaus(),
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
					'options'     =>  array(
						'callback'      => 'wpenon_immoticket24_wand_massiv',
						'callback_args' => array( 'field::wand_staerke' ),
					),
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
					'label'   => sprintf( __( 'Wand %s an Nachbargebäude angebaut?', 'wpenon' ), 'a' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'a' ),
					),
				),
				'wand_a_daemmung'           => array(
					'type'    => 'int',
					'label'   => __( 'Dämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'a', 'field::wand_a_nachbar' ),
					),
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
					'label'   => sprintf( __( 'Wand %s an Nachbargebäude angebaut?', 'wpenon' ), 'b' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'b' ),
					),
				),
				'wand_b_daemmung'           => array(
					'type'    => 'int',
					'label'   => __( 'Dämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'b', 'field::wand_b_nachbar' ),
					),
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
					'label'   => sprintf( __( 'Wand %s an Nachbargebäude angebaut?', 'wpenon' ), 'c' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'c' ),
					),
				),
				'wand_c_daemmung'           => array(
					'type'    => 'int',
					'label'   => __( 'Dämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'c', 'field::wand_c_nachbar' ),
					),
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
					'label'   => sprintf( __( 'Wand %s an Nachbargebäude angebaut?', 'wpenon' ), 'd' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'd' ),
					),
				),
				'wand_d_daemmung'           => array(
					'type'    => 'int',
					'label'   => __( 'Dämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'd', 'field::wand_d_nachbar' ),
					),
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
					'label'   => sprintf( __( 'Wand %s an Nachbargebäude angebaut?', 'wpenon' ), 'e' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'e' ),
					),
				),
				'wand_e_daemmung'           => array(
					'type'    => 'int',
					'label'   => __( 'Dämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'e', 'field::wand_e_nachbar' ),
					),
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
					'label'   => sprintf( __( 'Wand %s an Nachbargebäude angebaut?', 'wpenon' ), 'f' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'f' ),
					),
				),
				'wand_f_daemmung'           => array(
					'type'    => 'int',
					'label'   => __( 'Dämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'f', 'field::wand_f_nachbar' ),
					),
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
					'label'   => sprintf( __( 'Wand %s an Nachbargebäude angebaut?', 'wpenon' ), 'g' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'g' ),
					),
				),
				'wand_g_daemmung'           => array(
					'type'    => 'int',
					'label'   => __( 'Dämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'g', 'field::wand_g_nachbar' ),
					),
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
					'label'   => sprintf( __( 'Wand %s an Nachbargebäude angebaut?', 'wpenon' ), 'h' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'h' ),
					),
				),
				'wand_h_daemmung'           => array(
					'type'    => 'int',
					'label'   => __( 'Dämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_wand',
						'callback_args' => array( 'field::grundriss_form', 'h', 'field::wand_h_nachbar' ),
					),
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
					'options'     => \Enev\Schema202401\Schema\Standard_Options::get_wandstaerken(),
					'required'    => true,
					'unit'     => 'cm',
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
				),				
				'anbauwand_bauart_holz'     => array(
					'type'        => 'select',
					'label'       => __( 'Anbau-Wandbauart', 'wpenon' ),
					'description' => __( 'Wählen Sie die Bauart der Außenwand aus.', 'wpenon' ),
					'options'     => \Enev\Schema202401\Schema\Standard_Options::get_bauarten_holzhaus(),
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
					'options'     => \Enev\Schema202401\Schema\Standard_Options::get_bauarten_fachwerkaus(),
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
					'options'     =>  array(
						'callback'      => 'wpenon_immoticket24_wand_massiv',
						'callback_args' => array( 'field::anbauwand_staerke' ),
					),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_anbauwand_bauart',
						'callback_args' => array( 'field::anbau', 'field::gebaeudekonstruktion', 'massiv' ),
					),
				),
				'anbauwand_daemmung'        => array(
					'type'    => 'int',
					'label'   => __( 'Anbau-Wanddämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 23,
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
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
					'validate' => 'wpenon_validate_anbau_s1',
					'validate_dependencies' => array( 'anbau_form', 'anbauwand_t_laenge' ),
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
					'validate' => 'wpenon_validate_anbau_s2',
					'validate_dependencies' => array( 'anbau_form', 'anbauwand_b_laenge' ),
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
					'options'     => \Enev\Schema202401\Schema\Standard_Options::get_dach_formen(),
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
				'kniestock_hoehe'         => array(
					'type'        => 'float_length',
					'label'       => __( 'Kniestockhöhe', 'wpenon' ),
					'description' => __( 'Geben Sie die Höhe der Wand unter dem Dach an.', 'wpenon' ),
					'default'     => 0,
					'max'         => 3,
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
					'options'     => \Enev\Schema202401\Schema\Standard_Options::get_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::dach', array( 'nicht-vorhanden', 'beheizt' ) ),
					),
				),
				'dach_daemmung'      => array(
					'type'        => 'int',
					'label'       => __( 'Dachdämmung', 'wpenon' ),
					'description' => __( 'Falls das Dach gedämmt worden ist, geben Sie hier dessen Dämmstärke in Zentimetern an.', 'wpenon' ),
					'unit'        => 'cm',
					'max'         => 30,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_blacklist',
						'callback_args' => array( 'field::dach', 'unbeheizt' ),
					),
				),
				'decke_bauart'       => array(
					'type'        => 'select',
					'label'       => __( 'Deckenbauart', 'wpenon' ),
					'description' => __( 'Wählen Sie die Bauart der Obersten Geschossdecke aus.', 'wpenon' ),
					'options'     => \Enev\Schema202401\Schema\Standard_Options::get_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::dach', 'unbeheizt' ),
					),
				),
				'decke_daemmung'     => array(
					'type'        => 'int',
					'label'       => __( 'Deckendämmung', 'wpenon' ),
					'description' => __( 'Falls die Oberste Geschossdecke gedämmt worden ist, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon' ),
					'unit'        => 'cm',
					'max'         => 30,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::dach', 'unbeheizt' ),
					),
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
					'options'     => \Enev\Schema202401\Schema\Standard_Options::get_bauarten(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
				),
				'anbaudach_daemmung' => array(
					'type'    => 'int',
					'label'   => __( 'Anbau-Dachdämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 30,
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
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
					'options'     => \Enev\Schema202401\Schema\Standard_Options::get_bauarten_keller(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::keller', 'beheizt' ),
					),
				),
				'keller_daemmung'     => array(
					'type'        => 'int',
					'label'       => __( 'Kellerwanddämmung', 'wpenon' ),
					'description' => __( 'Falls die Kellerwände gedämmt worden sind, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon' ),
					'unit'        => 'cm',
					'max'         => 23,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::keller', 'beheizt' ),
					),
				),
				'boden_bauart'        => array(
					'type'        => 'select',
					'label'       => __( 'Bodenbauart', 'wpenon' ),
					'description' => __( 'Wählen Sie die Bauart der Bodenplatte / Kellerdecke aus.', 'wpenon' ),
					'options'     => \Enev\Schema202401\Schema\Standard_Options::get_bauarten_boden(),
					'required'    => true,
				),
				'boden_daemmung'      => array(
					'type'        => 'int',
					'label'       => __( 'Bodendämmung', 'wpenon' ),
					'description' => __( 'Falls die Bodenplatte / Kellerdecke gedämmt worden ist, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon' ),
					'unit'        => 'cm',
					'max'         => 25,
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
					'options'     => \Enev\Schema202401\Schema\Standard_Options::get_bauarten_boden(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
				),
				'anbauboden_daemmung' => array(
					'type'    => 'int',
					'label'   => __( 'Anbau-Bodendämmung', 'wpenon' ),
					'unit'    => 'cm',
					'max'     => 25,
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::anbau', true ),
					),
				),				
			),
		),
		'bauteile_fenster' => array(
			'title'       => __( 'Fenster', 'wpenon' ),
			'description' => __( 'Geben Sie die relevanten Daten für die Fenster des Gebäudes an.', 'wpenon' ),
			'fields'      => array(
				'fenster_bauart' => array(
					'type' => 'select',
					'label' => __('Bauart', 'wpenon'),
					'description' => __('Wählen Sie die Bauart der Fenster des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon'),
					'options' => wpenon_immoticket24_get_fenster_bauarten(),
					'required' => true,
				),
				'fenster_uwert_info'                               => array(
					'type'        => 'checkbox',
					'label'       => __( 'Benutzerdefinierte U-Wert verwenden?', 'wpenon' ),
					'description' => __( 'In seltenen Fällen kann es vorkommen, dass andere Werte als die Standardparameter aus der Datenbank bescheinigt wurden. Dieses Feld ist nur für Administratoren sichtbar.', 'wpenon' ),
					'display'     => current_user_can( 'manage_options' ),
				),
				'fenster_uwert' => array(
					'type' => 'float',
					'label' => __('U-Wert der Fenster', 'wpenon'),
					'required' => false,
					'display'               => array(
						'callback'              => 'wpenon_show_on_bool_compare_and_is_admin',
						'callback_args'         => array( 'field::fenster_uwert_info', true ),
					),
				),
				'fenster_baujahr' => array(
					'type' => 'int',
					'label' => __('Baujahr', 'wpenon'),
					'description' => __('Geben Sie das Baujahr der Fenster des Gebäudes an.', 'wpenon'),
					'min' => 1800,
					'max' => wpenon_get_reference_date('Y'),
					'required' => true,
					'validate' => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array('baujahr'),
				),				
				'anbaufenster_headline'    => array(
					'type'    => 'headline',
					'label'   => __( 'Anbau-Fenster', 'wpenon' ),
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( array( 'field::anbau' ), array( true ) ),
					),
				),
				'anbaufenster_bauart' => array(
					'type' => 'select',
					'label' => __('Anbaufenster Bauart', 'wpenon'),
					'description' => __('Wählen Sie die Bauart der Fenster des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon'),
					'options' => wpenon_immoticket24_get_fenster_bauarten(),
					'required' => true,
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( array( 'field::anbau' ), array( true ) ),
					),
				),
				'anbaufenster_uwert_info'                               => array(
					'type'        => 'checkbox',
					'label'       => __( 'Benutzerdefinierte U-Wert verwenden?', 'wpenon' ),
					'description' => __( 'In seltenen Fällen kann es vorkommen, dass andere Werte als die Standardparameter aus der Datenbank bescheinigt wurden. Dieses Feld ist nur für Administratoren sichtbar.', 'wpenon' ),
					'display'     => current_user_can( 'manage_options' ),
				),
				'anbaufenster_uwert' => array(
					'type' => 'float',
					'label' => __('U-Wert der Fenster', 'wpenon'),
					'required' => false,
					'display'               => array(
						'callback'              => 'wpenon_show_on_bool_compare_and_is_admin',
						'callback_args'         => array( 'field::anbaufenster_uwert_info', true ),
					),
				),
				'anbaufenster_baujahr' => array(
					'type' => 'int',
					'label' => __('Anbaufenster Baujahr', 'wpenon'),
					'description' => __('Geben Sie das Baujahr der Fenster des Gebäudes an.', 'wpenon'),
					'min' => 1800,
					'max' => wpenon_get_reference_date('Y'),
					'required' => true,
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( array( 'field::anbau' ), array( true ) ),
					),
					'validate' => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array('baujahr'),
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
