<?php

require_once ( dirname( __DIR__ ) . '/Standard_Options.php' );

return array(
	'bw_basisdaten' => array(
		'title' => __('Basisdaten', 'wpenon'),
		'groups' => array(
			'energieausweis' => array(
				'title' => __('Allgemein', 'wpenon'),
				'description' => __('Wählen Sie hier die passenden Angaben für Ihren Energieausweis aus.', 'wpenon'),
				'fields' => array(
					'anlass' => array(
						'type' => 'select',
						'label' => __('Anlass', 'wpenon'),
						'description' => __('Wählen Sie aus, für welchen Zweck dieser Energieausweis verwendet werden soll.', 'wpenon'),
						'options' => array(
							'modernisierung' => __('Modernisierung / Erweiterung', 'wpenon'),
							'vermietung' => __('Vermietung', 'wpenon'),
							'verkauf' => __('Verkauf', 'wpenon'),
							'sonstiges' => __('sonstiges', 'wpenon'),
						),
						'required' => true,
					),
				),
			),
			'gebaeude' => array(
				'title' => __('Gebäudeinformationen', 'wpenon'),
				'description' => __('Machen Sie hier grundsätzliche Angaben zum Gebäude.', 'wpenon'),
				'fields' => array(
					'gebaeudetyp' => array(
						'type' => 'select',
						'label' => __('Gebäudetyp', 'wpenon'),
						'description' => __('Wählen Sie den passenden Typ für das Gebäude aus.', 'wpenon'),
						'options' => array(
							'freistehend' => __('freistehendes Haus', 'wpenon'),
							'reihenhaus' => __('Reihenhaus', 'wpenon'),
							'reiheneckhaus' => __('Reiheneckhaus', 'wpenon'),
							'doppelhaushaelfte' => __('Doppelhaushälfte', 'wpenon'),
							'sonstiges' => __('sonstiges Wohngebäude', 'wpenon'),
						),
						'disabled_options' => array(
							'wohnung' => __('Wohnung (gemäß EnEV nicht möglich)', 'wpenon'),
						),
						'required' => true,
					),
					'gebaeudeteil' => array(
						'type' => 'radio',
						'label' => __('Gebäudeteil', 'wpenon'),
						'description' => __('Wählen Sie den Gebäudeteil aus, für den der Energieausweis erstellt wird.', 'wpenon'),
						'options' => array(
							'gesamt' => __('Gesamtes Gebäude', 'wpenon'),
							'gemischt' => __('Wohnteil gemischt genutztes Gebäude', 'wpenon'),
						),
						'required' => true,
					),
					'gebaeudekonstruktion' => array(
						'type' => 'radio',
						'label' => __('Gebäudekonstruktion', 'wpenon'),
						'options' => array(
							'massiv'   => __('Massivhaus', 'wpenon'),
							'holz'     => __('Holzhaus', 'wpenon'),
							'fachwerk' => __('Fachwerkhaus', 'wpenon'),
						),
						'required' => true,
					),
					'wohnungen' => array(
						'type' => 'int',
						'label' => __('Wohnungen', 'wpenon'),
						'description' => __('Geben Sie die Anzahl der Wohnungen im Gebäude ein.', 'wpenon'),
						'default' => 1,
						'min' => 1,
						'required' => true,
					),
					'baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr', 'wpenon'),
						'description' => __('Geben Sie das Baujahr des Gebäudes an.', 'wpenon'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
					),
				),
			),
			'regenerativ' => array(
				'title' => __('Erneuerbare Energien', 'wpenon'),
				'description' => __('Falls Ihr Gebäude zum Teil erneuerbare Energien verwendet, machen Sie hier entsprechende Angaben.', 'wpenon'),
				'fields' => array(
					'regenerativ_art' => array(
						'type' => 'text',
						'label' => __('Art der erneuerbaren Energien', 'wpenon'),
						'description' => __('Geben Sie die Art der erneuerbaren Energien ein, sofern Sie Photovoltaik, Geothermie, Solaranlage, Windenergie oder Energie aus Biogas nutzen. Falls nicht vorhanden, geben Sie bitte &quot;Keine&quot; ein. Dies dient ausschließlich der Information.', 'wpenon'),
						'default' => __('Keine', 'wpenon'),
						'required' => true,
						'max' => 40,
					),
					'regenerativ_nutzung' => array(
						'type' => 'text',
						'label' => __('Verwendung der erneuerbaren Energien', 'wpenon'),
						'description' => __('Geben Sie die Verwendung der erneuerbaren Energien ein, wenn Sie diese zur Wärmeerzeugung, Warmwassererzeugung, Energiespeicherung oder Stromerzeugung nutzen. Falls nicht vorhanden, geben Sie bitte &quot;Keine&quot; ein. Dies dient ausschließlich der Information.', 'wpenon'),
						'default' => __('Keine', 'wpenon'),
						'required' => true,
						'max' => 40,
					),
					'regenerativ_aktiv' => array(
						'type' => 'checkbox',
						'label' => __('Thermische Solaranlage vorhanden?', 'wpenon'),
					),
				),
			),
		),
	),
	'bw_gebaeude' => array(
		'title' => __('Gebäudetopologie', 'wpenon'),
		'groups' => array(
			'grundriss' => array(
				'title' => __('Grundriss', 'wpenon'),
				'description' => __('Machen Sie hier Angaben zum Grundriss Ihres Gebäudes.', 'wpenon'),
				'fields' => array(
					'grundriss_form' => array(
						'type' => 'select',
						'label' => __('Form des Grundrisses', 'wpenon'),
						'description' => __('Wählen Sie hier die Form aus (Draufsicht), die auf den Grundriss Ihres Gebäudes zutrifft.', 'wpenon'),
						'options' => Standard_Options::get_grundriss_formen(),
						'required' => true,
					),
					'grundriss_richtung' => array(
						'type' => 'select',
						'label' => __('Orientierung', 'wpenon'),
						'description' => __('Wählen Sie die Himmelsrichtung aus, in die Wand a im obigen Bild zeigt.', 'wpenon'),
						'options' => wpenon_immoticket24_get_himmelsrichtungen(),
						'required' => true,
					),
					'anbau' => array(
						'type' => 'checkbox',
						'label' => __('Beheizter Anbau vorhanden?', 'wpenon'),
						'default' => false,
					),
					'anbau_form' => array(
						'type' => 'select',
						'label' => __('Form des Anbaus', 'wpenon'),
						'description' => __('Wählen Sie hier die Form aus (Draufsicht), die auf den Anbau Ihres Gebäudes zutrifft.', 'wpenon'),
						'options' => Standard_Options::get_anbau_formen(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::anbau', true),
						),
					),
					'anbau_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr des Anbaus', 'wpenon'),
						'description' => __('Geben Sie das Baujahr des Anbaus an.', 'wpenon'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::anbau', true),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
				),
			),
			'geschosse' => array(
				'title' => __('Geschosse', 'wpenon'),
				'description' => __('Geben Sie die Informationen zu den Vollgeschossen des Gebäudes (ohne Dach- oder Kellergeschoss!) an.', 'wpenon'),
				'fields' => array(
					'geschoss_zahl' => array(
						'type' => 'int',
						'label' => __('Anzahl der Vollgeschosse', 'wpenon'),
						'description' => __('Geben Sie die Anzahl der Vollgeschosse ein, also die Anzahl aller Geschosse ohne eventuelle Dach- oder Kellergeschosse.', 'wpenon'),
						'min' => 1,
						'max' => 10,
						'required' => true,
					),
					'geschoss_hoehe' => array(
						'type' => 'float_length',
						'label' => __('Geschosshöhe', 'wpenon'),
						'description' => __('Geben Sie die lichte Höhe eines einzelnen Geschosses ein, also die Höhe vom Boden bis zur Decke.', 'wpenon'),
						'default' => 2.10,
						'max' => 5.0,
						'required' => true,
						'unit' => 'm',
					),
					'anbau_hoehe' => array(
						'type' => 'float_length',
						'label' => __('Höhe des Anbaus', 'wpenon'),
						'description' => __('Geben Sie die lichte Anbauhöhe ein, vom Boden bis zur Decke.', 'wpenon'),
						'default' => 2.10,
						'max' => 5.0,
						'required' => true,
						'unit' => 'm',
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::anbau', true),
						),
					),
				),
			),
		),
	),
	'bw_bauteile' => array(
		'title' => __('Bauteile', 'wpenon'),
		'groups' => array(
			'bauteile_basis' => array(
				'title' => __('Grundbauteile', 'wpenon'),
				'description' => __('Geben Sie die relevanten Daten für die Grundbestandteile des Gebäudes an.', 'wpenon'),
				'fields' => array(
					'wand_bauart_holz' => array(
						'type' => 'select',
						'label' => __('Wandbauart', 'wpenon'),
						'description' => __('Wählen Sie die Bauart der Außenwand aus.', 'wpenon'),
						'options' => Standard_Options::get_bauarten_holzhaus(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand_bauart',
							'callback_args' => array('field::gebaeudekonstruktion', 'holz'),
						),
					),
					'wand_bauart_fachwerk' => array(
						'type' => 'select',
						'label' => __('Wandbauart', 'wpenon'),
						'description' => __('Wählen Sie die Bauart der Außenwand aus.', 'wpenon'),
						'options' => Standard_Options::get_bauarten_fachwerkaus(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand_bauart',
							'callback_args' => array('field::gebaeudekonstruktion', 'fachwerk'),
						),
					),
					'wand_bauart_massiv' => array(
						'type' => 'select',
						'label' => __('Wandbauart', 'wpenon'),
						'description' => __('Wählen Sie die Bauart der Außenwand aus.', 'wpenon'),
						'options' => Standard_Options::get_bauarten_massiv(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand_bauart',
							'callback_args' => array('field::gebaeudekonstruktion', 'massiv'),
						),
					),
					'wand_a_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Wand %s', 'wpenon'), 'a'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'a'),
						),
					),
					'wand_a_laenge' => array(
						'type' => 'float_length_wall',
						'label' => __('Länge', 'wpenon'),
						'required' => true,
						'unit' => 'm',
						'max' => 50,
						'value' => array(
							'callback' => 'wpenon_immoticket24_calculate_wand',
							'callback_args' => array('field::grundriss_form', 'a', 0.0, 'field::wand_b_laenge', 'field::wand_c_laenge', 'field::wand_d_laenge', 'field::wand_e_laenge', 'field::wand_f_laenge', 'field::wand_g_laenge', 'field::wand_h_laenge'),
							'callback_hard' => true,
						),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'a'),
						),
					),
					'wand_a_nachbar' => array(
						'type' => 'checkbox',
						'label' => sprintf(__('Wand %s grenzt an Nachbargebäude?', 'wpenon'), 'a'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'a'),
						),
					),
					'wand_a_daemmung' => array(
						'type' => 'int',
						'label' => __('Nachträgliche Dämmung', 'wpenon'),
						'unit' => 'cm',
						'max' => 23,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'a', 'field::wand_a_nachbar'),
						),
					),
					'wand_b_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Wand %s', 'wpenon'), 'b'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'b'),
						),
					),
					'wand_b_laenge' => array(
						'type' => 'float_length_wall',
						'label' => __('Länge', 'wpenon'),
						'required' => true,
						'unit' => 'm',
						'max' => 50,
						'value' => array(
							'callback' => 'wpenon_immoticket24_calculate_wand',
							'callback_args' => array('field::grundriss_form', 'b', 'field::wand_a_laenge', 0.0, 'field::wand_c_laenge', 'field::wand_d_laenge', 'field::wand_e_laenge', 'field::wand_f_laenge', 'field::wand_g_laenge', 'field::wand_h_laenge'),
							'callback_hard' => true,
						),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'b'),
						),
					),
					'wand_b_nachbar' => array(
						'type' => 'checkbox',
						'label' => sprintf(__('Wand %s grenzt an Nachbargebäude?', 'wpenon'), 'b'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'b'),
						),
					),
					'wand_b_daemmung' => array(
						'type' => 'int',
						'label' => __('Nachträgliche Dämmung', 'wpenon'),
						'unit' => 'cm',
						'max' => 23,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'b', 'field::wand_b_nachbar'),
						),
					),
					'wand_c_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Wand %s', 'wpenon'), 'c'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'c'),
						),
					),
					'wand_c_laenge' => array(
						'type' => 'float_length_wall',
						'label' => __('Länge', 'wpenon'),
						'required' => true,
						'unit' => 'm',
						'max' => 50,
						'value' => array(
							'callback' => 'wpenon_immoticket24_calculate_wand',
							'callback_args' => array('field::grundriss_form', 'c', 'field::wand_a_laenge', 'field::wand_b_laenge', 0.0, 'field::wand_d_laenge', 'field::wand_e_laenge', 'field::wand_f_laenge', 'field::wand_g_laenge', 'field::wand_h_laenge'),
							'callback_hard' => true,
						),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'c'),
						),
					),
					'wand_c_nachbar' => array(
						'type' => 'checkbox',
						'label' => sprintf(__('Wand %s grenzt an Nachbargebäude?', 'wpenon'), 'c'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'c'),
						),
					),
					'wand_c_daemmung' => array(
						'type' => 'int',
						'label' => __('Nachträgliche Dämmung', 'wpenon'),
						'unit' => 'cm',
						'max' => 23,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'c', 'field::wand_c_nachbar'),
						),
					),
					'wand_d_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Wand %s', 'wpenon'), 'd'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'd'),
						),
					),
					'wand_d_laenge' => array(
						'type' => 'float_length_wall',
						'label' => __('Länge', 'wpenon'),
						'required' => true,
						'unit' => 'm',
						'max' => 50,
						'value' => array(
							'callback' => 'wpenon_immoticket24_calculate_wand',
							'callback_args' => array('field::grundriss_form', 'd', 'field::wand_a_laenge', 'field::wand_b_laenge', 'field::wand_c_laenge', 0.0, 'field::wand_e_laenge', 'field::wand_f_laenge', 'field::wand_g_laenge', 'field::wand_h_laenge'),
							'callback_hard' => true,
						),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'd'),
						),
					),
					'wand_d_nachbar' => array(
						'type' => 'checkbox',
						'label' => sprintf(__('Wand %s grenzt an Nachbargebäude?', 'wpenon'), 'd'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'd'),
						),
					),
					'wand_d_daemmung' => array(
						'type' => 'int',
						'label' => __('Nachträgliche Dämmung', 'wpenon'),
						'unit' => 'cm',
						'max' => 23,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'd', 'field::wand_d_nachbar'),
						),
					),
					'wand_e_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Wand %s', 'wpenon'), 'e'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'e'),
						),
					),
					'wand_e_laenge' => array(
						'type' => 'float_length_wall',
						'label' => __('Länge', 'wpenon'),
						'required' => true,
						'unit' => 'm',
						'max' => 50,
						'value' => array(
							'callback' => 'wpenon_immoticket24_calculate_wand',
							'callback_args' => array('field::grundriss_form', 'e', 'field::wand_a_laenge', 'field::wand_b_laenge', 'field::wand_c_laenge', 'field::wand_d_laenge', 0.0, 'field::wand_f_laenge', 'field::wand_g_laenge', 'field::wand_h_laenge'),
							'callback_hard' => true,
						),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'e'),
						),
					),
					'wand_e_nachbar' => array(
						'type' => 'checkbox',
						'label' => sprintf(__('Wand %s grenzt an Nachbargebäude?', 'wpenon'), 'e'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'e'),
						),
					),
					'wand_e_daemmung' => array(
						'type' => 'int',
						'label' => __('Nachträgliche Dämmung', 'wpenon'),
						'unit' => 'cm',
						'max' => 23,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'e', 'field::wand_e_nachbar'),
						),
					),
					'wand_f_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Wand %s', 'wpenon'), 'f'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'f'),
						),
					),
					'wand_f_laenge' => array(
						'type' => 'float_length_wall',
						'label' => __('Länge', 'wpenon'),
						'required' => true,
						'unit' => 'm',
						'max' => 50,
						'value' => array(
							'callback' => 'wpenon_immoticket24_calculate_wand',
							'callback_args' => array('field::grundriss_form', 'f', 'field::wand_a_laenge', 'field::wand_b_laenge', 'field::wand_c_laenge', 'field::wand_d_laenge', 'field::wand_e_laenge', 0.0, 'field::wand_g_laenge', 'field::wand_h_laenge'),
							'callback_hard' => true,
						),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'f'),
						),
					),
					'wand_f_nachbar' => array(
						'type' => 'checkbox',
						'label' => sprintf(__('Wand %s grenzt an Nachbargebäude?', 'wpenon'), 'f'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'f'),
						),
					),
					'wand_f_daemmung' => array(
						'type' => 'int',
						'label' => __('Nachträgliche Dämmung', 'wpenon'),
						'unit' => 'cm',
						'max' => 23,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'f', 'field::wand_f_nachbar'),
						),
					),
					'wand_g_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Wand %s', 'wpenon'), 'g'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'g'),
						),
					),
					'wand_g_laenge' => array(
						'type' => 'float_length_wall',
						'label' => __('Länge', 'wpenon'),
						'required' => true,
						'unit' => 'm',
						'max' => 50,
						'value' => array(
							'callback' => 'wpenon_immoticket24_calculate_wand',
							'callback_args' => array('field::grundriss_form', 'g', 'field::wand_a_laenge', 'field::wand_b_laenge', 'field::wand_c_laenge', 'field::wand_d_laenge', 'field::wand_e_laenge', 'field::wand_f_laenge', 0.0, 'field::wand_h_laenge'),
							'callback_hard' => true,
						),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'g'),
						),
					),
					'wand_g_nachbar' => array(
						'type' => 'checkbox',
						'label' => sprintf(__('Wand %s grenzt an Nachbargebäude?', 'wpenon'), 'g'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'g'),
						),
					),
					'wand_g_daemmung' => array(
						'type' => 'int',
						'label' => __('Nachträgliche Dämmung', 'wpenon'),
						'unit' => 'cm',
						'max' => 23,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'g', 'field::wand_g_nachbar'),
						),
					),
					'wand_h_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Wand %s', 'wpenon'), 'h'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'h'),
						),
					),
					'wand_h_laenge' => array(
						'type' => 'float_length_wall',
						'label' => __('Länge', 'wpenon'),
						'required' => true,
						'unit' => 'm',
						'value' => array(
							'callback' => 'wpenon_immoticket24_calculate_wand',
							'callback_args' => array('field::grundriss_form', 'h', 'field::wand_a_laenge', 'field::wand_b_laenge', 'field::wand_c_laenge', 'field::wand_d_laenge', 'field::wand_e_laenge', 'field::wand_f_laenge', 'field::wand_g_laenge', 0.0),
							'callback_hard' => true,
						),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'h'),
						),
					),
					'wand_h_nachbar' => array(
						'type' => 'checkbox',
						'label' => sprintf(__('Wand %s grenzt an Nachbargebäude?', 'wpenon'), 'h'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'h'),
						),
					),
					'wand_h_daemmung' => array(
						'type' => 'int',
						'label' => __('Nachträgliche Dämmung', 'wpenon'),
						'unit' => 'cm',
						'max' => 23,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_wand',
							'callback_args' => array('field::grundriss_form', 'h', 'field::wand_h_nachbar'),
						),
					),
					'anbauwand_headline' => array(
						'type' => 'headline',
						'label' => __('Anbau', 'wpenon'),
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::anbau', true),
						),
					),
					'anbauwand_bauart_holz' => array(
						'type' => 'select',
						'label' => __('Anbau-Wandbauart', 'wpenon'),
						'description' => __('Wählen Sie die Bauart der Außenwand aus.', 'wpenon'),
						'options' => Standard_Options::get_bauarten_holzhaus(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_anbauwand_bauart',
							'callback_args' => array('field::anbau', 'field::gebaeudekonstruktion', 'holz'),
						),
					),
					'anbauwand_bauart_fachwerk' => array(
						'type' => 'select',
						'label' => __('Anbau-Wandbauart', 'wpenon'),
						'description' => __('Wählen Sie die Bauart der Außenwand aus.', 'wpenon'),
						'options' => Standard_Options::get_bauarten_fachwerkaus(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_anbauwand_bauart',
							'callback_args' => array('field::anbau', 'field::gebaeudekonstruktion', 'fachwerk'),
						),
					),
					'anbauwand_bauart_massiv' => array(
						'type' => 'select',
						'label' => __('Anbau-Wandbauart', 'wpenon'),
						'description' => __('Wählen Sie die Bauart der Außenwand aus.', 'wpenon'),
						'options' => Standard_Options::get_bauarten_massiv(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_anbauwand_bauart',
							'callback_args' => array('field::anbau', 'field::gebaeudekonstruktion', 'massiv'),
						),
					),
					'anbauwand_daemmung' => array(
						'type' => 'int',
						'label' => __('Nachträgliche Anbau-Wanddämmung', 'wpenon'),
						'unit' => 'cm',
						'max' => 23,
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::anbau', true),
						),
					),
					'anbauwand_b_laenge' => array(
						'type' => 'float_length',
						'label' => __('Anbaubreite b', 'wpenon'),
						'required' => true,
						'unit' => 'm',
						'value' => array(
							'callback' => 'wpenon_immoticket24_calculate_anbauwand',
							'callback_args' => array('field::anbau_form', 'b', 0.0, 'field::anbauwand_t_laenge', 'field::anbauwand_s1_laenge', 'field::anbauwand_s2_laenge'),
							'callback_hard' => true,
						),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_anbauwand',
							'callback_args' => array('field::anbau_form', 'b', 'field::anbau'),
						),
					),
					'anbauwand_t_laenge' => array(
						'type' => 'float_length',
						'label' => __('Anbautiefe t', 'wpenon'),
						'required' => true,
						'unit' => 'm',
						'value' => array(
							'callback' => 'wpenon_immoticket24_calculate_anbauwand',
							'callback_args' => array('field::anbau_form', 't', 'field::anbauwand_b_laenge', 0.0, 'field::anbauwand_s1_laenge', 'field::anbauwand_s2_laenge'),
							'callback_hard' => true,
						),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_anbauwand',
							'callback_args' => array('field::anbau_form', 't', 'field::anbau'),
						),
					),
					'anbauwand_s1_laenge' => array(
						'type' => 'float_length',
						'label' => __('Anbau-Schnittlänge s1', 'wpenon'),
						'required' => true,
						'unit' => 'm',
						'value' => array(
							'callback' => 'wpenon_immoticket24_calculate_anbauwand',
							'callback_args' => array('field::anbau_form', 's1', 'field::anbauwand_b_laenge', 'field::anbauwand_t_laenge', 0.0, 'field::anbauwand_s2_laenge'),
							'callback_hard' => true,
						),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_anbauwand',
							'callback_args' => array('field::anbau_form', 's1', 'field::anbau'),
						),
					),
					'anbauwand_s2_laenge' => array( // Hidden by CSS!
						'type' => 'float_length',
						'label' => __('Anbau-Schnittlänge s2', 'wpenon'),
						'required' => true,
						'unit' => 'm',
						'value' => array(
							'callback' => 'wpenon_immoticket24_calculate_anbauwand',
							'callback_args' => array('field::anbau_form', 's2', 'field::anbauwand_b_laenge', 'field::anbauwand_t_laenge', 'field::anbauwand_s1_laenge', 0.0),
							'callback_hard' => true,
						),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_anbauwand',
							'callback_args' => array('field::anbau_form', 's2', 'field::anbau'),
						),
					),
				),
			),
			'bauteile_dach' => array(
				'title' => __('Dach', 'wpenon'),
				'description' => __('Geben Sie die relevanten Daten für das Dachgeschoss des Gebäudes an.', 'wpenon'),
				'fields' => array(
					'dach' => array(
						'type' => 'select',
						'label' => __('Dachgeschoss', 'wpenon'),
						'options' => array(
							'nicht-vorhanden' => __('nicht vorhanden (Flachdach)', 'wpenon'),
							'unbeheizt' => __('unbeheizt', 'wpenon'),
							'beheizt' => __('beheizt', 'wpenon'),
						),
						'required' => true,
					),
					'dach_form' => array(
						'type' => 'select',
						'label' => __('Dachtyp', 'wpenon'),
						'description' => __('Falls das Dach Ihres Hauses nicht einer dieser Formen entspricht, wählen Sie hier bitte die Dachform aus, die Ihrem Dach am nächsten kommt.', 'wpenon'),
						'options' => Standard_Options::get_dach_formen(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_array_whitelist',
							'callback_args' => array('field::dach', 'beheizt'),
						),
					),
					'dach_hoehe' => array(
						'type' => 'float_length',
						'label' => __('Dachhöhe', 'wpenon'),
						'description' => __('Geben Sie die lichte Höhe des Daches, also die Höhe vom Boden des Dachgeschosses bis zum Dachgiebel, in Metern ein.', 'wpenon'),
						'default' => 3.0,
						'max' => 8.0,
						'required' => true,
						'unit' => 'm',
						'display' => array(
							'callback' => 'wpenon_show_on_array_whitelist',
							'callback_args' => array('field::dach', 'beheizt'),
						),
					),
					'dach_bauart' => array(
						'type' => 'select',
						'label' => __('Dachbauart', 'wpenon'),
						'description' => __('Wählen Sie die Bauart des Daches aus.', 'wpenon'),
						'options' => Standard_Options::get_bauarten(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_array_whitelist',
							'callback_args' => array('field::dach', 'nicht-vorhanden'),
						),
					),
					'dach_daemmung' => array(
						'type' => 'int',
						'label' => __('Nachträgliche Dachdämmung', 'wpenon'),
						'description' => __('Falls das Dach zusätzlich gedämmt worden ist, geben Sie hier dessen Dämmstärke in Zentimetern an.', 'wpenon'),
						'unit' => 'cm',
						'max' => 30,
						'display' => array(
							'callback' => 'wpenon_show_on_array_blacklist',
							'callback_args' => array('field::dach', 'unbeheizt'),
						),
					),
					'decke_bauart' => array(
						'type' => 'select',
						'label' => __('Deckenbauart', 'wpenon'),
						'description' => __('Wählen Sie die Bauart der Obersten Geschossdecke aus.', 'wpenon'),
						'options' => Standard_Options::get_bauarten(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_array_whitelist',
							'callback_args' => array('field::dach', 'unbeheizt'),
						),
					),
					'decke_daemmung' => array(
						'type' => 'int',
						'label' => __('Nachträgliche Deckendämmung', 'wpenon'),
						'description' => __('Falls die Oberste Geschossdecke zusätzlich gedämmt worden ist, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon'),
						'unit' => 'cm',
						'max' => 30,
						'display' => array(
							'callback' => 'wpenon_show_on_array_whitelist',
							'callback_args' => array('field::dach', 'unbeheizt'),
						),
					),
					'anbaudach_headline' => array(
						'type' => 'headline',
						'label' => __('Anbau', 'wpenon'),
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::anbau', true),
						),
					),
					'anbaudach_bauart' => array(
						'type' => 'select',
						'label' => __('Anbau-Dachbauart', 'wpenon'),
						'description' => __('Wählen Sie die Bauart des Dachs des Anbaus aus.', 'wpenon'),
						'options' => Standard_Options::get_bauarten(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::anbau', true),
						),
					),
					'anbaudach_daemmung' => array(
						'type' => 'int',
						'label' => __('Nachträgliche Anbau-Dachdämmung', 'wpenon'),
						'unit' => 'cm',
						'max' => 30,
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::anbau', true),
						),
					),
				),
			),
			'bauteile_keller' => array(
				'title' => __('Keller', 'wpenon'),
				'description' => __('Geben Sie die relevanten Daten für das Kellergeschoss des Gebäudes an.', 'wpenon'),
				'fields' => array(
					'keller' => array(
						'type' => 'select',
						'label' => __('Kellergeschoss', 'wpenon'),
						'options' => array(
							'nicht-vorhanden' => __('nicht vorhanden', 'wpenon'),
							'unbeheizt' => __('unbeheizt', 'wpenon'),
							'beheizt' => __('beheizt', 'wpenon'),
						),
						'required' => true,
					),
					'keller_groesse' => array(
						'type' => 'int',
						'label' => __('Unterkellerung', 'wpenon'),
						'description' => __('Geben Sie den Anteil der Unterkellerung des Gebäudes in Bezug auf die Grundfläche ein.', 'wpenon'),
						'default' => 100,
						'min' => 5,
						'max' => 100,
						'required' => true,
						'unit' => '%',
						'display' => array(
							'callback' => 'wpenon_show_on_array_blacklist',
							'callback_args' => array('field::keller', 'nicht-vorhanden'),
						),
					),
					'keller_hoehe' => array(
						'type' => 'float_length',
						'label' => __('Kellerhöhe', 'wpenon'),
						'description' => __('Geben Sie die lichte Höhe des Kellers in Metern ein.', 'wpenon'),
						'default' => 2.1,
						'max' => 5.0,
						'required' => true,
						'unit' => 'm',
						'display' => array(
							'callback' => 'wpenon_show_on_array_whitelist',
							'callback_args' => array('field::keller', 'beheizt'),
						),
					),
					'keller_bauart' => array(
						'type' => 'select',
						'label' => __('Kellerwandbauart', 'wpenon'),
						'description' => __('Wählen Sie die Bauart der Kellerwand aus.', 'wpenon'),
						'options' => Standard_Options::get_bauarten_keller(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_array_whitelist',
							'callback_args' => array('field::keller', 'beheizt'),
						),
					),
					'keller_daemmung' => array(
						'type' => 'int',
						'label' => __('Nachträgliche Kellerwanddämmung', 'wpenon'),
						'description' => __('Falls die Kellerwände zusätzlich gedämmt worden sind, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon'),
						'unit' => 'cm',
						'max' => 23,
						'display' => array(
							'callback' => 'wpenon_show_on_array_whitelist',
							'callback_args' => array('field::keller', 'beheizt'),
						),
					),
					'boden_bauart' => array(
						'type' => 'select',
						'label' => __('Bodenbauart', 'wpenon'),
						'description' => __('Wählen Sie die Bauart der Bodenplatte / Kellerdecke aus.', 'wpenon'),
						'options' => Standard_Options::get_bauarten_boden(),
						'required' => true,
					),
					'boden_daemmung' => array(
						'type' => 'int',
						'label' => __('Nachträgliche Bodendämmung', 'wpenon'),
						'description' => __('Falls die Bodenplatte / Kellerdecke zusätzlich gedämmt worden ist, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon'),
						'unit' => 'cm',
						'max' => 25,
					),
					'anbauboden_headline' => array(
						'type' => 'headline',
						'label' => __('Anbau', 'wpenon'),
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::anbau', true),
						),
					),
					'anbauboden_bauart' => array(
						'type' => 'select',
						'label' => __('Anbau-Bodenbauart', 'wpenon'),
						'description' => __('Wählen Sie die Bauart des Bodens des Anbaus aus.', 'wpenon'),
						'options' => Standard_Options::get_bauarten_boden(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::anbau', true),
						),
					),
					'anbauboden_daemmung' => array(
						'type' => 'int',
						'label' => __('Nachträgliche Anbau-Bodendämmung', 'wpenon'),
						'unit' => 'cm',
						'max' => 25,
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::anbau', true),
						),
					),
				),
			),
			'bauteile_fenster' => array(
				'title' => __('Fenster', 'wpenon'),
				'description' => __('Geben Sie die relevanten Daten für die Fenster des Gebäudes an.', 'wpenon'),
				'fields' => array(
					'fenster_a_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Fenster %s', 'wpenon'), 'a'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'a', 'field::wand_a_nachbar'),
						),
					),
					'fenster_a_flaeche' => array(
						'type' => 'float',
						'label' => __('Fläche', 'wpenon'),
						'description' => sprintf(__('Geben Sie die gesamte Fläche aller Fenster an Seite %s des Gebäudes ein.', 'wpenon'), 'a'),
						'unit' => 'm&sup2;',
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'a', 'field::wand_a_nachbar'),
						),
						'validate' => 'wpenon_immoticket24_validate_at_least_one_fenster',
						'validate_dependencies' => array('fenster_b_flaeche', 'fenster_c_flaeche', 'fenster_d_flaeche', 'fenster_e_flaeche', 'fenster_f_flaeche', 'fenster_g_flaeche', 'fenster_h_flaeche'),
					),
					'fenster_a_bauart' => array(
						'type' => 'select',
						'label' => __('Bauart', 'wpenon'),
						'description' => sprintf(__('Wählen Sie die Bauart der Fenster an Seite %s des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon'), 'a'),
						'options' => Standard_Options::get_fenster_bauarten(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'a', 'field::wand_a_nachbar', 'field::fenster_a_flaeche'),
						),
					),
					'fenster_a_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr', 'wpenon'),
						'description' => sprintf(__('Geben Sie das Baujahr der Fenster an Seite %s des Gebäudes an.', 'wpenon'), 'a'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'a', 'field::wand_a_nachbar', 'field::fenster_a_flaeche'),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					'fenster_b_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Fenster %s', 'wpenon'), 'b'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'b', 'field::wand_b_nachbar'),
						),
					),
					'fenster_b_flaeche' => array(
						'type' => 'float',
						'label' => __('Fläche', 'wpenon'),
						'description' => sprintf(__('Geben Sie die gesamte Fläche aller Fenster an Seite %s des Gebäudes ein.', 'wpenon'), 'b'),
						'unit' => 'm&sup2;',
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'b', 'field::wand_b_nachbar'),
						),
					),
					'fenster_b_bauart' => array(
						'type' => 'select',
						'label' => __('Bauart', 'wpenon'),
						'description' => sprintf(__('Wählen Sie die Bauart der Fenster an Seite %s des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon'), 'b'),
						'options' => Standard_Options::get_fenster_bauarten(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'b', 'field::wand_b_nachbar', 'field::fenster_b_flaeche'),
						),
					),
					'fenster_b_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr', 'wpenon'),
						'description' => sprintf(__('Geben Sie das Baujahr der Fenster an Seite %s des Gebäudes an.', 'wpenon'), 'b'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'b', 'field::wand_b_nachbar', 'field::fenster_b_flaeche'),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					'fenster_c_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Fenster %s', 'wpenon'), 'c'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'c', 'field::wand_c_nachbar'),
						),
					),
					'fenster_c_flaeche' => array(
						'type' => 'float',
						'label' => __('Fläche', 'wpenon'),
						'description' => sprintf(__('Geben Sie die gesamte Fläche aller Fenster an Seite %s des Gebäudes ein.', 'wpenon'), 'c'),
						'unit' => 'm&sup2;',
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'c', 'field::wand_c_nachbar'),
						),
					),
					'fenster_c_bauart' => array(
						'type' => 'select',
						'label' => __('Bauart', 'wpenon'),
						'description' => sprintf(__('Wählen Sie die Bauart der Fenster an Seite %s des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon'), 'c'),
						'options' => Standard_Options::get_fenster_bauarten(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'c', 'field::wand_c_nachbar', 'field::fenster_c_flaeche'),
						),
					),
					'fenster_c_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr', 'wpenon'),
						'description' => sprintf(__('Geben Sie das Baujahr der Fenster an Seite %s des Gebäudes an.', 'wpenon'), 'c'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'c', 'field::wand_c_nachbar', 'field::fenster_c_flaeche'),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					'fenster_d_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Fenster %s', 'wpenon'), 'd'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'd', 'field::wand_d_nachbar'),
						),
					),
					'fenster_d_flaeche' => array(
						'type' => 'float',
						'label' => __('Fläche', 'wpenon'),
						'description' => sprintf(__('Geben Sie die gesamte Fläche aller Fenster an Seite %s des Gebäudes ein.', 'wpenon'), 'd'),
						'unit' => 'm&sup2;',
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'd', 'field::wand_d_nachbar'),
						),
					),
					'fenster_d_bauart' => array(
						'type' => 'select',
						'label' => __('Bauart', 'wpenon'),
						'description' => sprintf(__('Wählen Sie die Bauart der Fenster an Seite %s des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon'), 'd'),
						'options' => Standard_Options::get_fenster_bauarten(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'd', 'field::wand_d_nachbar', 'field::fenster_d_flaeche'),
						),
					),
					'fenster_d_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr', 'wpenon'),
						'description' => sprintf(__('Geben Sie das Baujahr der Fenster an Seite %s des Gebäudes an.', 'wpenon'), 'd'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'd', 'field::wand_d_nachbar', 'field::fenster_d_flaeche'),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					'fenster_e_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Fenster %s', 'wpenon'), 'e'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'e', 'field::wand_e_nachbar'),
						),
					),
					'fenster_e_flaeche' => array(
						'type' => 'float',
						'label' => __('Fläche', 'wpenon'),
						'description' => sprintf(__('Geben Sie die gesamte Fläche aller Fenster an Seite %s des Gebäudes ein.', 'wpenon'), 'e'),
						'unit' => 'm&sup2;',
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'e', 'field::wand_e_nachbar'),
						),
					),
					'fenster_e_bauart' => array(
						'type' => 'select',
						'label' => __('Bauart', 'wpenon'),
						'description' => sprintf(__('Wählen Sie die Bauart der Fenster an Seite %s des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon'), 'e'),
						'options' => Standard_Options::get_fenster_bauarten(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'e', 'field::wand_e_nachbar', 'field::fenster_e_flaeche'),
						),
					),
					'fenster_e_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr', 'wpenon'),
						'description' => sprintf(__('Geben Sie das Baujahr der Fenster an Seite %s des Gebäudes an.', 'wpenon'), 'e'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'e', 'field::wand_e_nachbar', 'field::fenster_e_flaeche'),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					'fenster_f_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Fenster %s', 'wpenon'), 'f'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'f', 'field::wand_f_nachbar'),
						),
					),
					'fenster_f_flaeche' => array(
						'type' => 'float',
						'label' => __('Fläche', 'wpenon'),
						'description' => sprintf(__('Geben Sie die gesamte Fläche aller Fenster an Seite %s des Gebäudes ein.', 'wpenon'), 'f'),
						'unit' => 'm&sup2;',
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'f', 'field::wand_f_nachbar'),
						),
					),
					'fenster_f_bauart' => array(
						'type' => 'select',
						'label' => __('Bauart', 'wpenon'),
						'description' => sprintf(__('Wählen Sie die Bauart der Fenster an Seite %s des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon'), 'f'),
						'options' => Standard_Options::get_fenster_bauarten(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'f', 'field::wand_f_nachbar', 'field::fenster_f_flaeche'),
						),
					),
					'fenster_f_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr', 'wpenon'),
						'description' => sprintf(__('Geben Sie das Baujahr der Fenster an Seite %s des Gebäudes an.', 'wpenon'), 'f'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'f', 'field::wand_f_nachbar', 'field::fenster_f_flaeche'),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					'fenster_g_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Fenster %s', 'wpenon'), 'g'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'g', 'field::wand_g_nachbar'),
						),
					),
					'fenster_g_flaeche' => array(
						'type' => 'float',
						'label' => __('Fläche', 'wpenon'),
						'description' => sprintf(__('Geben Sie die gesamte Fläche aller Fenster an Seite %s des Gebäudes ein.', 'wpenon'), 'g'),
						'unit' => 'm&sup2;',
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'g', 'field::wand_g_nachbar'),
						),
					),
					'fenster_g_bauart' => array(
						'type' => 'select',
						'label' => __('Bauart', 'wpenon'),
						'description' => sprintf(__('Wählen Sie die Bauart der Fenster an Seite %s des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon'), 'g'),
						'options' => Standard_Options::get_fenster_bauarten(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'g', 'field::wand_g_nachbar', 'field::fenster_g_flaeche'),
						),
					),
					'fenster_g_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr', 'wpenon'),
						'description' => sprintf(__('Geben Sie das Baujahr der Fenster an Seite %s des Gebäudes an.', 'wpenon'), 'g'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'g', 'field::wand_g_nachbar', 'field::fenster_g_flaeche'),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					'fenster_h_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Fenster %s', 'wpenon'), 'h'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'h', 'field::wand_h_nachbar'),
						),
					),
					'fenster_h_flaeche' => array(
						'type' => 'float',
						'label' => __('Fläche', 'wpenon'),
						'description' => sprintf(__('Geben Sie die gesamte Fläche aller Fenster an Seite %s des Gebäudes ein.', 'wpenon'), 'h'),
						'unit' => 'm&sup2;',
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'h', 'field::wand_h_nachbar'),
						),
					),
					'fenster_h_bauart' => array(
						'type' => 'select',
						'label' => __('Bauart', 'wpenon'),
						'description' => sprintf(__('Wählen Sie die Bauart der Fenster an Seite %s des Gebäudes aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon'), 'h'),
						'options' => Standard_Options::get_fenster_bauarten(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'h', 'field::wand_h_nachbar', 'field::fenster_h_flaeche'),
						),
					),
					'fenster_h_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr', 'wpenon'),
						'description' => sprintf(__('Geben Sie das Baujahr der Fenster an Seite %s des Gebäudes an.', 'wpenon'), 'h'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_fenster',
							'callback_args' => array(true, 'field::grundriss_form', 'h', 'field::wand_h_nachbar', 'field::fenster_h_flaeche'),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					'fenster_dach_headline' => array(
						'type' => 'headline',
						'label' => __('Dachfenster', 'wpenon'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_dachfenster',
							'callback_args' => array(true, 'field::dach'),
						),
					),
					'fenster_dach_flaeche' => array(
						'type' => 'float',
						'label' => __('Fläche', 'wpenon'),
						'description' => __('Geben Sie die gesamte Fläche aller Fenster ein, welche sich auf dem Dach befinden.', 'wpenon'),
						'unit' => 'm&sup2;',
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_dachfenster',
							'callback_args' => array(true, 'field::dach'),
						),
					),
					'fenster_dach_bauart' => array(
						'type' => 'select',
						'label' => __('Bauart', 'wpenon'),
						'description' => __('Wählen Sie die Bauart der Dachfenster aus.', 'wpenon'),
						'options' => Standard_Options::get_fenster_bauarten(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_dachfenster',
							'callback_args' => array(true, 'field::dach', 'field::fenster_dach_flaeche'),
						),
					),
					'fenster_dach_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr', 'wpenon'),
						'description' => __('Geben Sie das Baujahr der Dachfenster an.', 'wpenon'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_dachfenster',
							'callback_args' => array(true, 'field::dach', 'field::fenster_dach_flaeche'),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					'anbaufenster_headline' => array(
						'type' => 'headline',
						'label' => __('Anbau-Fenster', 'wpenon'),
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array(array(true, 'field::anbau'), array(true, true)),
						),
					),
					'anbaufenster_b_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Anbau-Fenster %s', 'wpenon'), 'b'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_anbaufenster',
							'callback_args' => array(true, 'field::anbau', 'field::anbau_form', 'b'),
						),
					),
					'anbaufenster_b_flaeche' => array(
						'type' => 'float',
						'label' => __('Fläche', 'wpenon'),
						'description' => sprintf(__('Geben Sie die gesamte Fläche aller Fenster an Seite %s des Anbaus ein.', 'wpenon'), 'b'),
						'unit' => 'm&sup2;',
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_anbaufenster',
							'callback_args' => array(true, 'field::anbau', 'field::anbau_form', 'b'),
						),
					),
					'anbaufenster_b_bauart' => array(
						'type' => 'select',
						'label' => __('Bauart', 'wpenon'),
						'description' => sprintf(__('Wählen Sie die Bauart der Fenster an Seite %s des Anbaus aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon'), 'b'),
						'options' => Standard_Options::get_fenster_bauarten(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_anbaufenster',
							'callback_args' => array(true, 'field::anbau', 'field::anbau_form', 'b', 'field::anbaufenster_b_flaeche'),
						),
					),
					'anbaufenster_b_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr', 'wpenon'),
						'description' => sprintf(__('Geben Sie das Baujahr der Fenster an Seite %s des Anbaus an.', 'wpenon'), 'b'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_anbaufenster',
							'callback_args' => array(true, 'field::anbau', 'field::anbau_form', 'b', 'field::anbaufenster_b_flaeche'),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					'anbaufenster_t_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Anbau-Fenster %s', 'wpenon'), 't'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_anbaufenster',
							'callback_args' => array(true, 'field::anbau', 'field::anbau_form', 't'),
						),
					),
					'anbaufenster_t_flaeche' => array(
						'type' => 'float',
						'label' => __('Fläche', 'wpenon'),
						'description' => sprintf(__('Geben Sie die gesamte Fläche aller Fenster an Seite %s des Anbaus ein.', 'wpenon'), 't'),
						'unit' => 'm&sup2;',
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_anbaufenster',
							'callback_args' => array(true, 'field::anbau', 'field::anbau_form', 't'),
						),
					),
					'anbaufenster_t_bauart' => array(
						'type' => 'select',
						'label' => __('Bauart', 'wpenon'),
						'description' => sprintf(__('Wählen Sie die Bauart der Fenster an Seite %s des Anbaus aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon'), 't'),
						'options' => Standard_Options::get_fenster_bauarten(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_anbaufenster',
							'callback_args' => array(true, 'field::anbau', 'field::anbau_form', 't', 'field::anbaufenster_t_flaeche'),
						),
					),
					'anbaufenster_t_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr', 'wpenon'),
						'description' => sprintf(__('Geben Sie das Baujahr der Fenster an Seite %s des Anbaus an.', 'wpenon'), 't'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_anbaufenster',
							'callback_args' => array(true, 'field::anbau', 'field::anbau_form', 't', 'field::anbaufenster_t_flaeche'),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					'anbaufenster_s1_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Anbau-Fenster %s', 'wpenon'), 's1'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_anbaufenster',
							'callback_args' => array(true, 'field::anbau', 'field::anbau_form', 's1'),
						),
					),
					'anbaufenster_s1_flaeche' => array(
						'type' => 'float',
						'label' => __('Fläche', 'wpenon'),
						'description' => sprintf(__('Geben Sie die gesamte Fläche aller Fenster an Seite %s des Anbaus ein.', 'wpenon'), 's1'),
						'unit' => 'm&sup2;',
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_anbaufenster',
							'callback_args' => array(true, 'field::anbau', 'field::anbau_form', 's1'),
						),
					),
					'anbaufenster_s1_bauart' => array(
						'type' => 'select',
						'label' => __('Bauart', 'wpenon'),
						'description' => sprintf(__('Wählen Sie die Bauart der Fenster an Seite %s des Anbaus aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon'), 's1'),
						'options' => Standard_Options::get_fenster_bauarten(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_anbaufenster',
							'callback_args' => array(true, 'field::anbau', 'field::anbau_form', 's1', 'field::anbaufenster_s1_flaeche'),
						),
					),
					'anbaufenster_s1_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr', 'wpenon'),
						'description' => sprintf(__('Geben Sie das Baujahr der Fenster an Seite %s des Anbaus an.', 'wpenon'), 's1'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_anbaufenster',
							'callback_args' => array(true, 'field::anbau', 'field::anbau_form', 's1', 'field::anbaufenster_s1_flaeche'),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					'anbaufenster_s2_headline' => array(
						'type' => 'headline',
						'label' => sprintf(__('Anbau-Fenster %s', 'wpenon'), 's2'),
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_anbaufenster',
							'callback_args' => array(true, 'field::anbau', 'field::anbau_form', 's2'),
						),
					),
					'anbaufenster_s2_flaeche' => array(
						'type' => 'float',
						'label' => __('Fläche', 'wpenon'),
						'description' => sprintf(__('Geben Sie die gesamte Fläche aller Fenster an Seite %s des Anbaus ein.', 'wpenon'), 's2'),
						'unit' => 'm&sup2;',
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_anbaufenster',
							'callback_args' => array(true, 'field::anbau', 'field::anbau_form', 's2'),
						),
					),
					'anbaufenster_s2_bauart' => array(
						'type' => 'select',
						'label' => __('Bauart', 'wpenon'),
						'description' => sprintf(__('Wählen Sie die Bauart der Fenster an Seite %s des Anbaus aus. Hinweis: Wärmedämmglas ist die Weiterentwicklung der Isolierverglasung. Seit 1995 müssen neue Fenster mit Wärmedämmglas ausgestattet sein.', 'wpenon'), 's2'),
						'options' => Standard_Options::get_fenster_bauarten(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_anbaufenster',
							'callback_args' => array(true, 'field::anbau', 'field::anbau_form', 's2', 'field::anbaufenster_s2_flaeche'),
						),
					),
					'anbaufenster_s2_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr', 'wpenon'),
						'description' => sprintf(__('Geben Sie das Baujahr der Fenster an Seite %s des Anbaus an.', 'wpenon'), 's2'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_immoticket24_show_specific_anbaufenster',
							'callback_args' => array(true, 'field::anbau', 'field::anbau_form', 's2', 'field::anbaufenster_s2_flaeche'),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					'heizkoerpernischen' => array(
						'type' => 'select',
						'label' => __('Heizkörpernischen', 'wpenon'),
						'options' => array(
							'nicht-vorhanden' => __('nicht vorhanden', 'wpenon'),
							'vorhanden' => __('vorhanden', 'wpenon'),
						),
						'required' => true,
					),
					'rollladenkaesten' => array(
						'type' => 'select',
						'label' => __('Rollladenkästen', 'wpenon'),
						'options' => array(
							'nicht-vorhanden' => __('nicht vorhanden', 'wpenon'),
							'aussen' => __('außenliegend', 'wpenon'),
							'innen_ungedaemmt' => __('innenliegend, ungedämmt', 'wpenon'),
							'innen_gedaemmt' => __('innenliegend, gedämmt', 'wpenon'),
						),
						'required' => true,
					),
				),
			),
		),
	),
	'bw_anlage' => array(
		'title' => __('Anlage', 'wpenon'),
		'groups' => array(
			'heizung' => array(
				'title' => __('Heizungsanlage', 'wpenon'),
				'description' => __('Machen Sie hier Angaben zur Heizungsanlage / Wärmeerzeugung des Gebäudes. Sie können bis zu drei unterschiedliche Heizungsanlagen spezifizieren.', 'wpenon'),
				'fields' => array(
					'h_erzeugung' => array(
						'type' => 'select',
						'label' => __('Typ der Heizungsanlage', 'wpenon'),
						'description' => __('Falls Sie den mit Gas oder Öl betriebenen Typ der Heizungsanlage nicht bestimmen können, wählen Sie den Niedertemperaturkessel.', 'wpenon'),
						'options' => wpenon_immoticket24_get_heizungsanlagen2019(),
						'required' => true,
					),
					'h_energietraeger' => array(
						'type' => 'select',
						'label' => __('Energieträger der Heizungsanlage', 'wpenon'),
						'options' => wpenon_immoticket24_get_energietraeger(),
						'required' => true,
					),
					'h_deckungsanteil' => array(
						'type' => 'int',
						'label' => __('Deckungsanteil der Heizungsanlage', 'wpenon'),
						'description' => __('Geben Sie an, wie groß der Anteil des Wärmebedarfs ist, den die Heizungsanlage abdeckt.', 'wpenon'),
						'default' => 100,
						'max' => 100,
						'required' => true,
						'unit' => '%',
						'value' => array(
							'callback' => 'wpenon_get_value_by_sum',
							'callback_args' => array(100, array('h2' => 'field::h2_deckungsanteil', 'h3' => 'field::h3_deckungsanteil'), array('h2' => 'field::h2_info', 'h3' => 'field::h3_info'), true),
						),
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::h2_info', true),
						),
					),
					'h_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr der Heizungsanlage', 'wpenon'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					/*'h_custom'              => array(
            'type'                  => 'checkbox',
            'label'                 => __( 'Benutzerdefinierte Primärenergie- und CO2-Emissionsfaktoren verwenden?', 'wpenon' ),
            'description'           => __( 'In seltenen Fällen kann es vorkommen, dass andere Werte als die Standardparameter aus der Datenbank bescheinigt wurden.', 'wpenon' ),
          ),
          'h_custom_primaer'      => array(
            'type'                  => 'float',
            'label'                 => __( 'Primärenergiefaktor', 'wpenon' ),
            'required'              => true,
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( 'field::h_custom', true ),
            ),
          ),
          'h_custom_co2'          => array(
            'type'                  => 'float',
            'label'                 => __( 'CO2-Emissionsfaktor', 'wpenon' ),
            'required'              => true,
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( 'field::h_custom', true ),
            ),
          ),*/
					'h2_info' => array(
						'type' => 'checkbox',
						'label' => __('2. Heizungsanlage vorhanden?', 'wpenon'),
					),
					'h2_erzeugung' => array(
						'type' => 'select',
						'label' => __('Typ der 2. Heizungsanlage', 'wpenon'),
						'description' => __('Falls Sie den mit Gas oder Öl betriebenen Typ der Heizungsanlage nicht bestimmen können, wählen Sie den Niedertemperaturkessel.', 'wpenon'),
						'options' => wpenon_immoticket24_get_heizungsanlagen2019(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::h2_info', true),
						),
					),
					'h2_energietraeger' => array(
						'type' => 'select',
						'label' => __('Energieträger der 2. Heizungsanlage', 'wpenon'),
						'options' => wpenon_immoticket24_get_energietraeger(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::h2_info', true),
						),
					),
					'h2_deckungsanteil' => array(
						'type' => 'int',
						'label' => __('Deckungsanteil der 2. Heizungsanlage', 'wpenon'),
						'description' => __('Geben Sie an, wie groß der Anteil des Wärmebedarfs ist, den die 2. Heizungsanlage abdeckt.', 'wpenon'),
						'default' => 0,
						'max' => 100,
						'required' => true,
						'unit' => '%',
						'value' => array(
							'callback' => 'wpenon_get_value_by_sum',
							'callback_args' => array(100, array('h' => 'field::h_deckungsanteil', 'h3' => 'field::h3_deckungsanteil'), array('h' => true, 'h3' => 'field::h3_info'), true),
						),
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::h2_info', true),
						),
					),
					'h2_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr der 2. Heizungsanlage', 'wpenon'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::h2_info', true),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					/*'h2_custom'             => array(
            'type'                  => 'checkbox',
            'label'                 => __( 'Benutzerdefinierte Primärenergie- und CO2-Emissionsfaktoren für den 2. Energieträger verwenden?', 'wpenon' ),
            'description'           => __( 'In seltenen Fällen kann es vorkommen, dass andere Werte als die Standardparameter aus der Datenbank bescheinigt wurden.', 'wpenon' ),
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( 'field::h2_info', true ),
            ),
          ),
          'h2_custom_primaer'     => array(
            'type'                  => 'float',
            'label'                 => __( 'Primärenergiefaktor des 2. Energieträgers', 'wpenon' ),
            'required'              => true,
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( array( 'field::h2_info', 'field::h2_custom' ), array( true, true ) ),
            ),
          ),
          'h2_custom_co2'         => array(
            'type'                  => 'float',
            'label'                 => __( 'CO2-Emissionsfaktor des 2. Energieträgers', 'wpenon' ),
            'required'              => true,
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( array( 'field::h2_info', 'field::h2_custom' ), array( true, true ) ),
            ),
          ),*/
					'h3_info' => array(
						'type' => 'checkbox',
						'label' => __('3. Heizungsanlage vorhanden?', 'wpenon'),
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::h2_info', true),
						),
					),
					'h3_erzeugung' => array(
						'type' => 'select',
						'label' => __('Typ der 3. Heizungsanlage', 'wpenon'),
						'description' => __('Falls Sie den mit Gas oder Öl betriebenen Typ der Heizungsanlage nicht bestimmen können, wählen Sie den Niedertemperaturkessel.', 'wpenon'),
						'options' => wpenon_immoticket24_get_heizungsanlagen2019(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array(array('field::h2_info', 'field::h3_info'), array(true, true)),
						),
					),
					'h3_energietraeger' => array(
						'type' => 'select',
						'label' => __('Energieträger der 3. Heizungsanlage', 'wpenon'),
						'options' => wpenon_immoticket24_get_energietraeger(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array(array('field::h2_info', 'field::h3_info'), array(true, true)),
						),
					),
					'h3_deckungsanteil' => array(
						'type' => 'int',
						'label' => __('Deckungsanteil der 3. Heizungsanlage', 'wpenon'),
						'description' => __('Geben Sie an, wie groß der Anteil des Wärmebedarfs ist, den die 3. Heizungsanlage abdeckt.', 'wpenon'),
						'default' => 100,
						'max' => 100,
						'required' => true,
						'unit' => '%',
						'value' => array(
							'callback' => 'wpenon_get_value_by_sum',
							'callback_args' => array(100, array('h' => 'field::h_deckungsanteil', 'h2' => 'field::h2_deckungsanteil'), array('h' => true, 'h2' => 'field::h2_info'), true),
						),
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array(array('field::h2_info', 'field::h3_info'), array(true, true)),
						),
					),
					'h3_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr der 3. Heizungsanlage', 'wpenon'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array(array('field::h2_info', 'field::h3_info'), array(true, true)),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					/*'h3_custom'             => array(
            'type'                  => 'checkbox',
            'label'                 => __( 'Benutzerdefinierte Primärenergie- und CO2-Emissionsfaktoren für den 3. Energieträger verwenden?', 'wpenon' ),
            'description'           => __( 'In seltenen Fällen kann es vorkommen, dass andere Werte als die Standardparameter aus der Datenbank bescheinigt wurden.', 'wpenon' ),
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
            ),
          ),
          'h3_custom_primaer'     => array(
            'type'                  => 'float',
            'label'                 => __( 'Primärenergiefaktor des 3. Energieträgers', 'wpenon' ),
            'required'              => true,
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( array( 'field::h2_info', 'field::h3_info', 'field::h3_custom' ), array( true, true, true ) ),
            ),
          ),
          'h3_custom_co2'         => array(
            'type'                  => 'float',
            'label'                 => __( 'CO2-Emissionsfaktor des 3. Energieträgers', 'wpenon' ),
            'required'              => true,
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( array( 'field::h2_info', 'field::h3_info', 'field::h3_custom' ), array( true, true, true ) ),
            ),
          ),*/
					'verteilung_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr des Rohrleitungssystems', 'wpenon'),
						'description' => __('In der Regel ist dies identisch mit dem Baujahr der Heizungsanlage.', 'wpenon'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					'verteilung_gedaemmt' => array(
						'type' => 'checkbox',
						'label' => __('Rohrleitungssystem zusätzlich gedämmt?', 'wpenon'),
						'display' => array(
							'callback' => 'wpenon_show_on_number_lower',
							'callback_args' => array('field::verteilung_baujahr', 1978),
						),
					),
					'speicherung' => array(
						'type' => 'checkbox',
						'label' => __('Pufferspeicher vorhanden?', 'wpenon'),
					),
					'speicherung_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr des Pufferspeichers', 'wpenon'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::speicherung', true),
						),
					),
					'speicherung_standort' => array(
						'type' => 'select',
						'label' => __('Standort des Pufferspeichers', 'wpenon'),
						'description' => __('Wählen Sie aus, ob sich der Pufferspeicher innerhalb oder außerhalb der thermischen Hülle befindet.', 'wpenon'),
						'options' => array(
							'innerhalb' => __('innerhalb thermischer Hülle', 'wpenon'),
							'ausserhalb' => __('außerhalb thermischer Hülle', 'wpenon'),
						),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_bool_compare',
							'callback_args' => array('field::speicherung', true),
						),
					),
				),
			),
			'warmwasser' => array(
				'title' => __('Warmwasseranlage', 'wpenon'),
				'description' => __('Machen Sie hier Angaben zur Warmwassererzeugung des Gebäudes.', 'wpenon'),
				'fields' => array(
					'ww_info' => array(
						'type' => 'select',
						'label' => __('Art der Warmwassererzeugung', 'wpenon'),
						'description' => __('Wählen Sie aus, ob die Warmwasserzeugung durch eine der angegebenen Heizungsanlagen oder in einer separaten Anlage stattfindet.', 'wpenon'),
						'options' => array(
							'callback' => 'wpenon_immoticket24_get_ww_info',
							'callback_args' => array('field::h2_info', 'field::h3_info', 'field::h_erzeugung', 'field::h2_erzeugung', 'field::h3_erzeugung'),
						),
						'required' => true,
					),
					'ww_erzeugung' => array(
						'type' => 'select',
						'label' => __('Typ der Warmwasseranlage', 'wpenon'),
						'description' => __('Falls Sie den mit Gas oder Öl betriebenen Typ der Warmwasseranlage nicht bestimmen können, wählen Sie den Niedertemperaturkessel.', 'wpenon'),
						'options' => wpenon_immoticket24_get_warmwasseranlagen2019(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_array_whitelist',
							'callback_args' => array('field::ww_info', 'ww'),
						),
					),
					'ww_energietraeger' => array(
						'type' => 'select',
						'label' => __('Energieträger der Warmwasseranlage', 'wpenon'),
						'options' => wpenon_immoticket24_get_energietraeger(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_array_whitelist',
							'callback_args' => array('field::ww_info', 'ww'),
						),
					),
					'ww_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr der Warmwasseranlage', 'wpenon'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_array_whitelist',
							'callback_args' => array('field::ww_info', 'ww'),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					'verteilung_versorgung' => array(
						'type' => 'select',
						'label' => __('Warmwasserverteilung', 'wpenon'),
						'description' => __('Wählen Sie aus, ob die Warmwassererzeugung mit oder ohne Zirkulation erfolgt.', 'wpenon'),
						'options' => array(
							'ohne' => __('ohne Zirkulation', 'wpenon'),
							'mit' => __('mit Zirkulation', 'wpenon'),
						),
						'required' => true,
					),
				),
			),
			'lueftung' => array(
				'title' => __('Lüftungsanlage', 'wpenon'),
				'description' => __('Machen Sie hier Angaben zur Lüftungsanlage des Gebäudes.', 'wpenon'),
				'fields' => array(
					'l_info' => array(
						'type' => 'select',
						'label' => __('Art der Lüftung', 'wpenon'),
						'options' => array(
							'fenster' => __('Fensterlüftung', 'wpenon'),
							'schacht' => __('Schachtlüftung', 'wpenon'),
							'anlage' => __('Lüftungsanlage', 'wpenon'),
						),
						'required' => true,
					),
					'l_erzeugung' => array(
						'type' => 'select',
						'label' => __('Typ der Lüftungsanlage', 'wpenon'),
						'description' => __('Wählen Sie den Typ der Lüftungsanlage aus.', 'wpenon'),
						'options' => wpenon_immoticket24_get_lueftungsanlagen(),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_array_whitelist',
							'callback_args' => array('field::l_info', 'anlage'),
						),
					),
					'l_baujahr' => array(
						'type' => 'int',
						'label' => __('Baujahr der Lüftungsanlage', 'wpenon'),
						'min' => 1800,
						'max' => wpenon_get_reference_date('Y'),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_array_whitelist',
							'callback_args' => array('field::l_info', 'anlage'),
						),
						'validate' => 'wpenon_immoticket24_validate_year_greater_than',
						'validate_dependencies' => array('baujahr'),
					),
					'l_standort' => array(
						'type' => 'select',
						'label' => __('Standort der Lüftungsanlage', 'wpenon'),
						'description' => __('Wählen Sie den Standort der Lüftungsanlage aus.', 'wpenon'),
						'options' => array(
							'innerhalb' => __('innerhalb thermischer Hülle', 'wpenon'),
							'ausserhalb_dach' => __('Dach, außerhalb thermischer Hülle', 'wpenon'),
							'ausserhalb_keller' => __('Keller, außerhalb thermischer Hülle', 'wpenon'),
						),
						'required' => true,
						'display' => array(
							'callback' => 'wpenon_show_on_array_whitelist',
							'callback_args' => array('field::l_info', 'anlage'),
						),
					),
					'k_info' => array(
						'type' => 'select',
						'label' => __('Gebäudekühlung', 'wpenon'),
						'options' => array(
							'nicht_vorhanden' => __('nicht vorhanden', 'wpenon'),
							'vorhanden' => __('vorhanden', 'wpenon'),
						),
						'required' => true,
					),
					'dichtheit' => array(
						'type' => 'checkbox',
						'label' => __('Wurde eine Dichtheitsprüfung (z.B. Blower-Door-Test) erfolgreich durchgeführt?', 'wpenon'),
					),
				),
			),
		),
	),
);
