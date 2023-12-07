<?php

$anlage = array(
	'title'  => __( 'Anlage', 'wpenon' ),
	'groups' => array(
		'heizung'         => array(
			'title'       => __( 'Heizungsanlage', 'wpenon' ),
			'description' => __( 'Machen Sie hier Angaben zur Heizungsanlage / Wärmeerzeugung des Gebäudes. Sie können bis zu drei unterschiedliche Heizungsanlagen spezifizieren.', 'wpenon' ),
			'fields'      => array(
				'h_erzeugung'                              => array(
					'type'        => 'select',
					'label'       => __( 'Typ der Heizungsanlage', 'wpenon' ),
					'description' => __( 'Falls Sie den mit Gas oder Öl betriebenen Typ der Heizungsanlage nicht bestimmen können, wählen Sie den Niedertemperaturkessel.', 'wpenon' ),
					'options'     => array(
						'standardkessel'              => __( 'Standardkessel', 'wpenon' ),
						'niedertemperaturkessel'      => __( 'Niedertemperaturkessel', 'wpenon' ),
						'brennwertkessel'             => __( 'Brennwertkessel', 'wpenon' ),
						'waermepumpeluft'             => __( 'Wärmepumpe (Luft)', 'wpenon' ),
						'waermepumpewasser'           => __( 'Wärmepumpe (Wasser)', 'wpenon' ),
						'waermepumpeerde'             => __( 'Wärmepumpe (Erde)', 'wpenon' ),
						'etagenheizung'               => __( 'Etagenheizung', 'wpenon' ),
						'infrarotheizung'             => __( 'Infrarotheizung', 'wpenon' ),
						'elektronachtspeicherheizung' => __( 'Elektro-Nachtspeicher', 'wpenon' ),
						'fernwaerme'                  => __( 'Fernwärme', 'wpenon' ),
						'zentral_elektrisch' => __( 'Zentral elektrisch beheizte Wärmeerzeuger', 'wpenon' ),
					),
					'required'    => true,
				),				
				'h_energietraeger_standardkessel'          => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'           => __( 'Heizöl', 'wpenon' ),
						'erdgas'            => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'       => __( 'Flüssiggas', 'wpenon' ),
						'biogas'            => __( 'Biogas', 'wpenon' ),
						'holzpellets'       => __( 'Holzpellets', 'wpenon' ),
						'holzhackschnitzel' => __( 'Holzhackschnitzel', 'wpenon' ),
						'stueckholz'        => __( 'Stückholz', 'wpenon' ),
						'steinkohle'        => __( 'Steinkohle', 'wpenon' ),
						'braunkohle'        => __( 'Braunkohle', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'standardkessel' ),
					),
					'required' => true,
				),
				'h_energietraeger_fernwaerme'              => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'fernwaermehzwfossil' => __( 'Nah-/Fernwärme', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'fernwaerme' ),
					),
					'required' => true,
				),
				'h_energietraeger_niedertemperaturkessel'  => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'           => __( 'Heizöl', 'wpenon' ),
						'erdgas'            => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'       => __( 'Flüssiggas', 'wpenon' ),
						'biogas'            => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'niedertemperaturkessel' ),
					),
					'required' => true,
				),
				'h_energietraeger_etagenheizung'           => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'erdgas'      => __( 'Erdgas', 'wpenon' ),
						'fluessiggas' => __( 'Flüssiggas', 'wpenon' ),
						'biogas'      => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'etagenheizung' ),
					),
					'required' => true,
				),
				'h_energietraeger_brennwertkessel'         => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'     => __( 'Heizöl', 'wpenon' ),
						'erdgas'      => __( 'Erdgas', 'wpenon' ),
						'fluessiggas' => __( 'Flüssiggas', 'wpenon' ),
						'biogas'      => __( 'Biogas', 'wpenon' ),
						'holzpellets'       => __( 'Holzpellets', 'wpenon' ),
						'holzhackschnitzel' => __( 'Holzhackschnitzel', 'wpenon' ),
						'stueckholz'        => __( 'Stückholz', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'brennwertkessel' ),
					),
					'required' => true,
				),
				'h_energietraeger_waermepumpeluft'         => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'waermepumpeluft' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h_energietraeger_waermepumpewasser'       => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'waermepumpewasser' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h_energietraeger_waermepumpeerde'         => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'waermepumpeerde' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h_energietraeger_pelletfeuerung'          => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'holzpellets'       => __( 'Holzpellets', 'wpenon' ),
						'holzhackschnitzel' => __( 'Holzhackschnitzel', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'pelletfeuerung' ),
					),
					'required' => true,
				),
				'h_energietraeger_elektronachtspeicherheizung' => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'elektronachtspeicherheizung' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h_energietraeger_infrarotheizung'         => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'infrarotheizung' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h_energietraeger_zentral_elektrisch'         => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'zentral_elektrisch' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h_energietraeger'                         => array(
					'type'  => 'hidden',
					'value' => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_by_erzeugung',
						'callback_args' => array(
							'field::h_erzeugung',
							'field::h_energietraeger_standardkessel',
							'field::h_energietraeger_niedertemperaturkessel',
							'field::h_energietraeger_brennwertkessel',
							'field::h_energietraeger_brennwertkesselverbessert',
							'field::h_energietraeger_etagenheizung',
							'field::h_energietraeger_fernwaerme',
							'field::h_energietraeger_waermepumpeluft',
							'field::h_energietraeger_waermepumpewasser',
							'field::h_energietraeger_waermepumpeerde',
							'field::h_energietraeger_elektronachtspeicherheizung',
							'field::h_energietraeger_infrarotheizung',
						),
					),
				),
				'h_waermepumpe_luft_stufen'          => array(
					'type'     => 'select',
					'label'    => __( 'Stufen (Name!)', 'wpenon' ),
					'options'  => array(
						'einstufig'           => __( 'Einstufig', 'wpenon' ),
						'mehrstufig'          => __( 'Mehrstufig', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'waermepumpeluft' ),
					),
					'required' => true,
				),
				'h_waermepumpe_erde_typ'          => array(
					'type'     => 'select',
					'label'    => __( 'Typ (Name!)', 'wpenon' ),
					'options'  => array(
						'erdsonde'           => __( 'Erdsonde', 'wpenon' ),
						'erdkollektor'          => __( 'Erdkollektor', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'waermepumpeerde' ),
					),
					'required' => true,
				),
				'h_evu_abschaltung'                        => array(
					'type'        => 'select',
					'label'       => __( 'EVU abschaltung', 'wpenon' ),
					'description' => __( 'Wird ihre Wärmepumpe vom Stromversorger zu verschiedene Zeitpunkten am Tag abgeschaltet?', 'wpenon' ),
					'options'     => array(
						'ja'   => __( 'Ja', 'wpenon' ),
						'nein' => __( 'Nein', 'wpenon' ),
					),
					'default'     => 'nein',
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', array( 'waermepumpeluft', 'waermepumpewasser', 'waermepumpeerde' ) ),
					),
					'required'    => true,
				),
				'h_deckungsanteil'                         => array(
					'type'        => 'int',
					'label'       => __( 'Deckungsanteil der Heizungsanlage', 'wpenon' ),
					'description' => __( 'Geben Sie an, wie groß der Anteil des Wärmebedarfs ist, den die Heizungsanlage abdeckt.', 'wpenon' ),
					'default'     => 100,
					'max'         => 100,
					'required'    => true,
					'unit'        => '%',
					'value'       => array(
						'callback'      => 'wpenon_get_value_by_sum',
						'callback_args' => array(
							100,
							array(
								'h2' => 'field::h2_deckungsanteil',
								'h3' => 'field::h3_deckungsanteil',
							),
							array(
								'h2' => 'field::h2_info',
								'h3' => 'field::h3_info',
							),
							true,
						),
					),
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h2_info', true ),
					),
				),
				'h_standort'                               => array(
					'type'        => 'select',
					'label'       => __( 'Standord der Heizungsanlage', 'wpenon' ),
					'description' => __( 'Wählen Sie aus, ob sich die heizungsanlage innerhalb oder außerhalb der thermischen Hülle befindet.', 'wpenon' ),
					'options'     => array(
						'innerhalb'  => __( 'innerhalb thermischer Hülle', 'wpenon' ),
						'ausserhalb' => __( 'außerhalb thermischer Hülle', 'wpenon' ),
					),
					'required'    => true,
				),
				'h_baujahr'                                => array(
					'type'                  => 'text',
					'label'                 => __( 'Baujahr der Heizungsanlage', 'wpenon' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'placeholder'           => 'Bitte wählen...',
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
				),
				'h_custom'                                 => array(
					'type'        => 'checkbox',
					'label'       => __( 'Benutzerdefinierte Primärenergiefaktoren verwenden?', 'wpenon' ),
					'description' => __( 'In seltenen Fällen kann es vorkommen, dass andere Werte als die Standardparameter aus der Datenbank bescheinigt wurden.', 'wpenon' ),
					'display'     => current_user_can( 'manage_options' ),
				),
				'h_custom_primaer'                         => array(
					'type'     => 'float',
					'label'    => __( 'Primärenergiefaktor', 'wpenon' ),
					'required' => true,
					'display'  => array(
						'callback'      => 'wpenon_show_on_bool_compare_and_is_admin',
						'callback_args' => array( 'field::h_custom', true ),
					),
				),
				'h_custom_2'                               => array(
					'type'        => 'checkbox',
					'label'       => __( 'Benutzerdefinierte CO2-Emissionsfaktoren verwenden?', 'wpenon' ),
					'description' => __( 'In seltenen Fällen kann es vorkommen, dass andere Werte als die Standardparameter aus der Datenbank bescheinigt wurden.', 'wpenon' ),
					'display'     => current_user_can( 'manage_options' ),
				),
				'h_custom_co2'                             => array(
					'type'     => 'float',
					'label'    => __( 'CO2 Emmissionsfaktor', 'wpenon' ),
					'required' => true,
					'display'  => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h_custom_2', true ),
					),
				),
				'h_typenschild'                            => array(
					'type'      => 'image',
					'label'     => __( 'Foto des Typenschilds der Heizungsanlage oder Foto der Heizungsanlage', 'wpenon' ),
					'required'  => true,
					'filetypes' => array(
						'image/png',
						'image/jpeg',
					),
				),
				'h2_info'                                  => array(
					'type'  => 'checkbox',
					'label' => __( '2. Heizungsanlage vorhanden?', 'wpenon' ),
				),
				'h2_erzeugung'                             => array(
					'type'        => 'select',
					'label'       => __( 'Typ der 2. Heizungsanlage', 'wpenon' ),
					'description' => __( 'Falls Sie den mit Gas oder Öl betriebenen Typ der Heizungsanlage nicht bestimmen können, wählen Sie den Niedertemperaturkessel.', 'wpenon' ),
					'options'     => array(
						'standardkessel'              => __( 'Standardkessel', 'wpenon' ),
						'niedertemperaturkessel'      => __( 'Niedertemperaturkessel', 'wpenon' ),
						'brennwertkessel'             => __( 'Brennwertkessel', 'wpenon' ),
						'waermepumpeluft'             => __( 'Wärmepumpe (Luft)', 'wpenon' ),
						'waermepumpewasser'           => __( 'Wärmepumpe (Wasser)', 'wpenon' ),
						'waermepumpeerde'             => __( 'Wärmepumpe (Erde)', 'wpenon' ),
						'etagenheizung'               => __( 'Etagenheizung', 'wpenon' ),
						'infrarotheizung'             => __( 'Infrarotheizung', 'wpenon' ),
						'elektronachtspeicherheizung' => __( 'Elektro-Nachtspeicher', 'wpenon' ),
						'fernwaerme'                  => __( 'Fernwärme', 'wpenon' ),
						'zentral_elektrisch' => __( 'Zentral elektrisch beheizte Wärmeerzeuger', 'wpenon' ),
					),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h2_info', true ),
					),
				),
				'h2_energietraeger_standardkessel'         => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'     => __( 'Heizöl', 'wpenon' ),
						'erdgas'      => __( 'Erdgas', 'wpenon' ),
						'fluessiggas' => __( 'Flüssiggas', 'wpenon' ),
						'biogas'      => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h2_info', 'field::h2_erzeugung', 'standardkessel' ),
					),
					'required' => true,
				),
				'h2_energietraeger_fernwaerme'             => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'fernwaermehzwfossil' => __( 'Nah-/Fernwärme', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h2_info', 'field::h2_erzeugung', 'fernwaerme' ),
					),
					'required' => true,
				),
				'h2_energietraeger_niedertemperaturkessel' => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'           => __( 'Heizöl', 'wpenon' ),
						'erdgas'            => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'       => __( 'Flüssiggas', 'wpenon' ),
						'biogas'            => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h2_info', 'field::h2_erzeugung', 'niedertemperaturkessel' ),
					),
					'required' => true,
				),
				'h2_energietraeger_brennwertkessel'        => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'     => __( 'Heizöl', 'wpenon' ),
						'erdgas'      => __( 'Erdgas', 'wpenon' ),
						'fluessiggas' => __( 'Flüssiggas', 'wpenon' ),
						'biogas'      => __( 'Biogas', 'wpenon' ),
						'holzpellets'       => __( 'Holzpellets', 'wpenon' ),
						'holzhackschnitzel' => __( 'Holzhackschnitzel', 'wpenon' ),
						'stueckholz'        => __( 'Stückholz', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h2_info', 'field::h2_erzeugung', 'brennwertkessel' ),
					),
					'required' => true,
				),
				'h2_energietraeger_etagenheizung'          => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'     => __( 'Heizöl', 'wpenon' ),
						'erdgas'      => __( 'Erdgas', 'wpenon' ),
						'fluessiggas' => __( 'Flüssiggas', 'wpenon' ),
						'biogas'      => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'etagenheizung' ),
					),
					'required' => true,
				),
				'h2_energietraeger_waermepumpeluft'        => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h2_info', 'field::h2_erzeugung', 'waermepumpeluft' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h2_energietraeger_waermepumpewasser'      => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h2_info', 'field::h2_erzeugung', 'waermepumpewasser' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h2_energietraeger_waermepumpeerde'        => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h2_info', 'field::h2_erzeugung', 'waermepumpeerde' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h2_energietraeger_pelletfeuerung'         => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'holzpellets'       => __( 'Holzpellets', 'wpenon' ),
						'holzhackschnitzel' => __( 'Holzhackschnitzel', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h2_info', 'field::h2_erzeugung', 'pelletfeuerung' ),
					),
					'required' => true,
				),
				'h2_energietraeger_elektronachtspeicherheizung' => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array(
							'field::h2_info',
							'field::h2_erzeugung',
							'elektronachtspeicherheizung',
						),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h2_energietraeger_infrarotheizung'        => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h2_info', 'field::h2_erzeugung', 'infrarotheizung' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h2_energietraeger_zentral_elektrisch'         => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'zentral_elektrisch' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h2_energietraeger'                        => array(
					'type'  => 'hidden',
					'value' => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_by_erzeugung',
						'callback_args' => array(
							'field::h2_erzeugung',
							'field::h2_energietraeger_standardkessel',
							'field::h2_energietraeger_niedertemperaturkessel',
							'field::h2_energietraeger_brennwertkessel',
							'field::h2_energietraeger_etagenheizung',
							'field::h2_energietraeger_fernwaerme',
							'field::h2_energietraeger_waermepumpeluft',
							'field::h2_energietraeger_waermepumpewasser',
							'field::h2_energietraeger_waermepumpeerde',
							'field::h2_energietraeger_elektronachtspeicherheizung',
							'field::h2_energietraeger_infrarotheizung',
						),
					),
				),
				'h2_waermepumpe_luft_stufen'          => array(
					'type'     => 'select',
					'label'    => __( 'Stufen (Name!)', 'wpenon' ),
					'options'  => array(
						'einstufig'           => __( 'Einstufig', 'wpenon' ),
						'mehrstufig'          => __( 'Mehrstufig', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'waermepumpeluft' ),
					),
					'required' => true,
				),
				'h2_waermepumpe_erde_typ'          => array(
					'type'     => 'select',
					'label'    => __( 'Typ (Name!)', 'wpenon' ),
					'options'  => array(
						'erdsonde'           => __( 'Erdsonde', 'wpenon' ),
						'erdkollektor'          => __( 'Erdkollektor', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'waermepumpeerde' ),
					),
					'required' => true,
				),
				'h2_deckungsanteil'                        => array(
					'type'        => 'int',
					'label'       => __( 'Deckungsanteil der 2. Heizungsanlage', 'wpenon' ),
					'description' => __( 'Geben Sie an, wie groß der Anteil des Wärmebedarfs ist, den die 2. Heizungsanlage abdeckt.', 'wpenon' ),
					'default'     => 0,
					'max'         => 100,
					'required'    => true,
					'unit'        => '%',
					'value'       => array(
						'callback'      => 'wpenon_get_value_by_sum',
						'callback_args' => array(
							100,
							array(
								'h'  => 'field::h_deckungsanteil',
								'h3' => 'field::h3_deckungsanteil',
							),
							array(
								'h'  => true,
								'h3' => 'field::h3_info',
							),
							true,
						),
					),
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h2_info', true ),
					),
				),
				'h2_baujahr'                               => array(
					'type'                  => 'text',
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
					'placeholder'           => 'Bitte wählen...',
				),
				'h2_custom'                                => array(
					'type'        => 'checkbox',
					'label'       => __( 'Benutzerdefinierte Primärenergiefaktoren verwenden?', 'wpenon' ),
					'description' => __( 'In seltenen Fällen kann es vorkommen, dass andere Werte als die Standardparameter aus der Datenbank bescheinigt wurden.', 'wpenon' ),
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare_and_is_admin',
						'callback_args' => array( 'field::h2_info', true ),
					),
				),
				'h2_custom_primaer'                        => array(
					'type'     => 'float',
					'label'    => __( 'Primärenergiefaktor', 'wpenon' ),
					'required' => true,
					'display'  => array(
						'callback'      => 'wpenon_show_on_bool_compare_and_is_admin',
						'callback_args' => array( array( 'field::h2_custom', 'field::h2_info' ), array( true, true ) ),
					),
				),
				'h2_custom_2'                              => array(
					'type'        => 'checkbox',
					'label'       => __( 'Benutzerdefinierte CO2-Emissionsfaktoren verwenden?', 'wpenon' ),
					'description' => __( 'In seltenen Fällen kann es vorkommen, dass andere Werte als die Standardparameter aus der Datenbank bescheinigt wurden.', 'wpenon' ),
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare_and_is_admin',
						'callback_args' => array( array( 'field::h2_info' ), array( true ) ),
					),
				),
				'h2_custom_co2'                            => array(
					'type'     => 'float',
					'label'    => __( 'CO2 Emmissionsfaktor', 'wpenon' ),
					'required' => true,
					'display'  => array(
						'callback'      => 'wpenon_show_on_bool_compare_and_is_admin',
						'callback_args' => array( array( 'field::h2_custom_2', 'field::h2_info' ), array( true, true ) ),
					),
				),
				'h2_typenschild'                           => array(
					'type'      => 'image',
					'label'     => __( 'Foto des Typenschilds der Heizungsanlage oder Foto der Heizungsanlage', 'wpenon' ),
					'required'  => false,
					'filetypes' => array(
						'image/png',
						'image/jpeg',
					),
					'display'   => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h2_info', true ),
					),
				),
				'h3_info'                                  => array(
					'type'    => 'checkbox',
					'label'   => __( '3. Heizungsanlage vorhanden?', 'wpenon' ),
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h2_info', true ),
					),
				),
				'h3_erzeugung'                             => array(
					'type'        => 'select',
					'label'       => __( 'Typ der 3. Heizungsanlage', 'wpenon' ),
					'description' => __( 'Falls Sie den mit Gas oder Öl betriebenen Typ der Heizungsanlage nicht bestimmen können, wählen Sie den Niedertemperaturkessel.', 'wpenon' ),
					'options'     => array(
						'standardkessel'              => __( 'Standardkessel', 'wpenon' ),
						'niedertemperaturkessel'      => __( 'Niedertemperaturkessel', 'wpenon' ),
						'brennwertkessel'             => __( 'Brennwertkessel', 'wpenon' ),
						'waermepumpeluft'             => __( 'Wärmepumpe (Luft)', 'wpenon' ),
						'waermepumpewasser'           => __( 'Wärmepumpe (Wasser)', 'wpenon' ),
						'waermepumpeerde'             => __( 'Wärmepumpe (Erde)', 'wpenon' ),
						'etagenheizung'               => __( 'Etagenheizung', 'wpenon' ),
						'infrarotheizung'             => __( 'Infrarotheizung', 'wpenon' ),
						'elektronachtspeicherheizung' => __( 'Elektro-Nachtspeicher', 'wpenon' ),
						'fernwaerme'                  => __( 'Fernwärme', 'wpenon' ),
						'zentral_elektrisch' => __( 'Zentral elektrisch beheizte Wärmeerzeuger', 'wpenon' ),
					),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
					),
				),
				'h3_energietraeger_standardkessel'         => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'     => __( 'Heizöl', 'wpenon' ),
						'erdgas'      => __( 'Erdgas', 'wpenon' ),
						'fluessiggas' => __( 'Flüssiggas', 'wpenon' ),
						'biogas'      => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h3_info', 'field::h3_erzeugung', 'standardkessel' ),
					),
					'required' => true,
				),
				'h3_energietraeger_fernwaerme'             => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'fernwaermehzwfossil' => __( 'Nah-/Fernwärme', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h3_info', 'field::h3_erzeugung', 'fernwaerme' ),
					),
					'required' => true,
				),
				'h3_energietraeger_niedertemperaturkessel' => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'           => __( 'Heizöl', 'wpenon' ),
						'erdgas'            => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'       => __( 'Flüssiggas', 'wpenon' ),
						'biogas'            => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h3_info', 'field::h3_erzeugung', 'niedertemperaturkessel' ),
					),
					'required' => true,
				),
				'h3_energietraeger_brennwertkessel'        => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'     => __( 'Heizöl', 'wpenon' ),
						'erdgas'      => __( 'Erdgas', 'wpenon' ),
						'fluessiggas' => __( 'Flüssiggas', 'wpenon' ),
						'biogas'      => __( 'Biogas', 'wpenon' ),
						'holzpellets'       => __( 'Holzpellets', 'wpenon' ),
						'holzhackschnitzel' => __( 'Holzhackschnitzel', 'wpenon' ),
						'stueckholz'        => __( 'Stückholz', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h3_info', 'field::h3_erzeugung', 'brennwertkessel' ),
					),
					'required' => true,
				),
				'h3_energietraeger_etagenheizung'          => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'     => __( 'Heizöl', 'wpenon' ),
						'erdgas'      => __( 'Erdgas', 'wpenon' ),
						'fluessiggas' => __( 'Flüssiggas', 'wpenon' ),
						'biogas'      => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'etagenheizung' ),
					),
					'required' => true,
				),
				'h3_energietraeger_waermepumpeluft'        => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h3_info', 'field::h3_erzeugung', 'waermepumpeluft' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h3_energietraeger_waermepumpewasser'      => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h3_info', 'field::h3_erzeugung', 'waermepumpewasser' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h3_energietraeger_waermepumpeerde'        => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h3_info', 'field::h3_erzeugung', 'waermepumpeerde' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h3_energietraeger_pelletfeuerung'         => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'holzpellets'       => __( 'Holzpellets', 'wpenon' ),
						'holzhackschnitzel' => __( 'Holzhackschnitzel', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h3_info', 'field::h3_erzeugung', 'pelletfeuerung' ),
					),
					'required' => true,
				),
				'h3_energietraeger_elektronachtspeicherheizung' => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array(
							'field::h3_info',
							'field::h3_erzeugung',
							'elektronachtspeicherheizung',
						),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h3_energietraeger_infrarotheizung'        => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_h_energietraeger',
						'callback_args' => array( 'field::h3_info', 'field::h3_erzeugung', 'infrarotheizung' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h3_energietraeger_zentral_elektrisch'         => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'zentral_elektrisch' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h3_energietraeger'                        => array(
					'type'  => 'hidden',
					'value' => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_by_erzeugung',
						'callback_args' => array(
							'field::h3_erzeugung',
							'field::h3_energietraeger_standardkessel',
							'field::h3_energietraeger_niedertemperaturkessel',
							'field::h3_energietraeger_brennwertkessel',
							'field::h3_energietraeger_etagenheizung',
							'field::h3_energietraeger_fernwaerme',
							'field::h3_energietraeger_waermepumpeluft',
							'field::h3_energietraeger_waermepumpewasser',
							'field::h3_energietraeger_waermepumpeerde',
							'field::h3_energietraeger_elektronachtspeicherheizung',
							'field::h3_energietraeger_infrarotheizung',
						),
					),
				),
				'h3_waermepumpe_luft_stufen'          => array(
					'type'     => 'select',
					'label'    => __( 'Stufen (Name!)', 'wpenon' ),
					'options'  => array(
						'einstufig'           => __( 'Einstufig', 'wpenon' ),
						'mehrstufig'          => __( 'Mehrstufig', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'waermepumpeluft' ),
					),
					'required' => true,
				),
				'h3_waermepumpe_erde_typ'          => array(
					'type'     => 'select',
					'label'    => __( 'Typ (Name!)', 'wpenon' ),
					'options'  => array(
						'erdsonde'           => __( 'Erdsonde', 'wpenon' ),
						'erdkollektor'          => __( 'Erdkollektor', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'waermepumpeerde' ),
					),
					'required' => true,
				),
				'h3_deckungsanteil'                        => array(
					'type'        => 'int',
					'label'       => __( 'Deckungsanteil der 3. Heizungsanlage', 'wpenon' ),
					'description' => __( 'Geben Sie an, wie groß der Anteil des Wärmebedarfs ist, den die 3. Heizungsanlage abdeckt.', 'wpenon' ),
					'default'     => 100,
					'max'         => 100,
					'required'    => true,
					'unit'        => '%',
					'value'       => array(
						'callback'      => 'wpenon_get_value_by_sum',
						'callback_args' => array(
							100,
							array(
								'h'  => 'field::h_deckungsanteil',
								'h2' => 'field::h2_deckungsanteil',
							),
							array(
								'h'  => true,
								'h2' => 'field::h2_info',
							),
							true,
						),
					),
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
					),
				),
				'h3_baujahr'                               => array(
					'type'                  => 'text',
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
					'placeholder'           => 'Bitte wählen...',
				),
				'h3_custom'                                => array(
					'type'        => 'checkbox',
					'label'       => __( 'Benutzerdefinierte Primärenergiefaktoren verwenden?', 'wpenon' ),
					'description' => __( 'In seltenen Fällen kann es vorkommen, dass andere Werte als die Standardparameter aus der Datenbank bescheinigt wurden.', 'wpenon' ),
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare_and_is_admin',
						'callback_args' => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
					),
				),
				'h3_custom_primaer'                        => array(
					'type'     => 'float',
					'label'    => __( 'Primärenergiefaktor', 'wpenon' ),
					'required' => true,
					'display'  => array(
						'callback'      => 'wpenon_show_on_bool_compare_and_is_admin',
						'callback_args' => array( array( 'field::h3_custom', 'field::h3_info' ), array( true, true ) ),
					),
				),
				'h3_custom_2'                              => array(
					'type'        => 'checkbox',
					'label'       => __( 'Benutzerdefinierte CO2-Emissionsfaktoren verwenden?', 'wpenon' ),
					'description' => __( 'In seltenen Fällen kann es vorkommen, dass andere Werte als die Standardparameter aus der Datenbank bescheinigt wurden.', 'wpenon' ),
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare_and_is_admin',
						'callback_args' => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
					),
				),
				'h3_custom_co2'                            => array(
					'type'     => 'float',
					'label'    => __( 'CO2 Emmissionsfaktor', 'wpenon' ),
					'required' => true,
					'display'  => array(
						'callback'      => 'wpenon_show_on_bool_compare_and_is_admin',
						'callback_args' => array( 'field::h3_custom_2', true ),
					),
				),
				'h3_typenschild'                           => array(
					'type'      => 'image',
					'label'     => __( 'Foto des Typenschilds der Heizungsanlage oder Foto der Heizungsanlage', 'wpenon' ),
					'required'  => false,
					'filetypes' => array(
						'image/png',
						'image/jpeg',
					),
					'display'   => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h3_info', true ),
					),
				),
				'verteilung_baujahr'                       => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr des Rohrleitungssystems', 'wpenon' ),
					'description'           => __( 'Geben Sie hier das Baujahr der freiliegenden Heizungsrohre an.' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
					'display'               => array(
						'callback'      => 'wpenon_show_on_array_blacklist',
						'callback_args' => array(
							'field::h_erzeugung',
							wpenon_get_heaters_without_piping(),
						),
					),
				),
				'verteilung_gedaemmt'                      => array(
					'type'    => 'checkbox',
					'label'   => __( 'Freiliegende Heizungsrohre zusätzlich gedämmt?', 'wpenon' ),
					'display' => array(
						'callback'      => 'wpenon_immoticket24_show_verteilung_gedaemmt',
						'callback_args' => array(
							'field::h_erzeugung',
							wpenon_get_heaters_without_piping(),
							'field::verteilung_baujahr',
							1978,
						),
					),
				),
			),
		),
		'uebergabesystem' => array(
			'title'       => __( 'Übergabesystem', 'wpenon' ),
			'description' => __( 'Machen Sie hier Angaben zum Übergabesystem des Gebäudes.', 'wpenon' ),
			'fields'      => array(
				'h_uebergabe'                        => array(
					'type'        => 'select',
					'label'       => __( 'Typ des Übergabesystems', 'wpenon' ),
					'description' => __( 'Falls Sie den mit Gas oder Öl betriebenen Typ der Heizungsanlage nicht bestimmen können, wählen Sie den Niedertemperaturkessel.', 'wpenon' ),
					'options'     => array(
						'elektroheizungsflaechen' => __( 'Elektroheizungsflächen', 'wpenon' ),
						'heizkoerper'             => __( 'Heizkörper', 'wpenon' ),
						'flaechenheizung'         => __( 'Flächenheizung Fußboden/Wandheizung', 'wpenon' ),
					),
					'required'    => true,
				),
				'h_uebergabe_auslegungstemperaturen' => array(
					'type'        => 'select',
					'label'       => __( 'Auslegungstemperaturen', 'wpenon' ),
					'description' => __( 'Wählen Sie die Auslegungstemperaturen des Übergabesystems.', 'wpenon' ),
					'options'     => array(
						'90/70' => __( '90/70°', 'wpenon' ),
						'70/55' => __( '70/55°', 'wpenon' ),
						'55/45' => __( '55/45°', 'wpenon' ),
						'35/28' => __( '35/28°', 'wpenon' ),
					),
					'required'    => true,
				),
				'h_uebergabe_flaechenheizungstyp'    => array(
					'type'     => 'select',
					'label'    => __( 'Typ der Flächenheizung', 'wpenon' ),
					'options'  => array(
						'fussbodenheizung' => __( 'Fußbodenheizung', 'wpenon' ),
						'wandheizung'      => __( 'Wandheizung', 'wpenon' ),
						'deckenheizung'    => __( 'Deckenheizung', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_uebergabe', array( 'flaechenheizung' ) ),
					),
					'required' => true,
				),

				'h_uebergabe_mindestdaemmung'        => array(
					'type'    => 'checkbox',
					'label'   => __( 'Die Flächenheizung erreicht die Mindestdämmung.', 'wpenon' ),
					'display' => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_uebergabe', array( 'flaechenheizung' ) ),
					),
				),

			),
		),
		'warmwasser'      => array(
			'title'       => __( 'Warmwasseranlage', 'wpenon' ),
			'description' => __( 'Machen Sie hier Angaben zur Warmwassererzeugung des Gebäudes.', 'wpenon' ),
			'fields'      => array(
				'ww_info'                                  => array(
					'type'        => 'select',
					'label'       => __( 'Der Energieverbrauch für Warmwasser ist:', 'wpenon' ),
					'description' => __( 'Wählen Sie aus, ob die Warmwasserzeugung durch eine der angegebenen Heizungsanlagen oder in einer separaten Anlage stattfindet.', 'wpenon' ),
					'options'     => array(
						'callback'      => 'wpenon_immoticket24_get_ww_info_18599',
						'callback_args' => array(
							'field::h2_info',
							'field::h3_info',
							'field::h_erzeugung',
							'field::h2_erzeugung',
							'field::h3_erzeugung',
							false,
							true,
						),
					),
					'required'    => true,
				),
				'ww_erzeugung'                             => array(
					'type'        => 'select',
					'label'       => __( 'Typ der Warmwasseranlage', 'wpenon' ),
					'description' => __( 'Falls Sie den mit Gas oder Öl betriebenen Typ der Warmwasseranlage nicht bestimmen können, wählen Sie den Niedertemperaturkessel.', 'wpenon' ),
					'options'     => array(
						'dezentralelektroerhitzer' => __( 'elektrischer Durchlauferhitzer', 'wpenon' ),
						'dezentralgaserhitzer'     => __( 'Gas-Durchlauferhitzer', 'wpenon' ),
					),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_ww_erzeugung',
						'callback_args' => array( 'field::ww_info', 'field::h_erzeugung' ),
					),
				),
				'ww_energietraeger_dezentralelektroerhitzer' => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Warmwasseranlage', 'wpenon' ),
					'required' => true,
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'default'  => 'strom',
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_ww_energietraeger',
						'callback_args' => array( 'field::ww_info', 'field::h_erzeugung', 'field::ww_erzeugung', 'dezentralelektroerhitzer' ),
					),
				),
				'ww_energietraeger_dezentralgaserhitzer'   => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Warmwasseranlage', 'wpenon' ),
					'required' => true,
					'options'  => array(
						'erdgas' => __( 'Erdgas', 'wpenon' ),
					),
					'default'  => 'erdgas',
					'display'  => array(
						'callback'      => 'wpenon_immoticket24_show_ww_energietraeger',
						'callback_args' => array( 'field::ww_info', 'field::h_erzeugung', 'field::ww_erzeugung', 'dezentralgaserhitzer' ),
					),
				),
				'ww_energietraeger'                        => array(
					'type'  => 'hidden',
					'value' => array(
						'callback'      => 'wpenon_immoticket24_get_ww_energietraeger_by_erzeugung',
						'callback_args' => array(
							'field::ww_erzeugung',
							'field::ww_energietraeger_dezentralelektroerhitzer',
							'field::ww_energietraeger_dezentralkleinspeicher',
							'field::ww_energietraeger_dezentralgaserhitzer',
						),
					),
				),
				'ww_baujahr'                               => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr der Warmwasseranlage', 'wpenon' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_immoticket24_show_ww_baujahr',
						'callback_args' => array( 'field::ww_info', 'field::ww_erzeugung', 'field::h_erzeugung' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
				),
				'verteilung_versorgung'                    => array(
					'type'        => 'select',
					'label'       => __( 'Warmwasserverteilung', 'wpenon' ),
					'description' => __( 'Wählen Sie aus, ob die Warmwassererzeugung mit oder ohne Zirkulation erfolgt.', 'wpenon' ),
					'options'     => array(
						'ohne' => __( 'ohne Zirkulation', 'wpenon' ),
						'mit'  => __( 'mit Zirkulation', 'wpenon' ),
					),
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::ww_info', array( 'h', 'h2', 'h3' ) ),
					),
					'required'    => true,
				),
			),
		),
		'lueftung'        => array(
			'title'       => __( 'Lüftungsanlage', 'wpenon' ),
			'description' => __( 'Machen Sie hier Angaben zur Lüftungsanlage des Gebäudes.', 'wpenon' ),
			'fields'      => array(
				// 'l_info'      => array(
				// 'type'     => 'select',
				// 'label'    => __( 'Art der Lüftung', 'wpenon' ),
				// 'options'  => array(
				// 'fenster' => __( 'Fensterlüftung', 'wpenon' ),
				// 'anlage_ohne'  => __( 'Lüftungsanlage (ohne Wärmerückgewinnung)', 'wpenon' ),
				// 'anlage_mit'  => __( 'Lüftungsanlage (mit Wärmerückgewinnung)', 'wpenon' ),
				// ),
				// 'required' => true,
				// ),
				'l_info'            => array(
					'type'     => 'select',
					'label'    => __( 'Lüftungsanlage', 'wpenon' ),
					'options'  => array(
						'ohne'      => __( 'Keine', 'wpenon' ),
						'zu_abluft' => __( 'Zu- und Abluftalage', 'wpenon' ),
						'abluft'    => __( 'Abluftanlage', 'wpenon' ),
					),
					'required' => true,
				),
				// 'l_baujahr'   => array(
				// 'type'                  => 'int',
				// 'label'                 => __( 'Baujahr der Lüftungsanlage', 'wpenon' ),
				// 'min'                   => 1800,
				// 'max'                   => wpenon_get_reference_date( 'Y' ),
				// 'required'              => true,
				// 'display'               => array(
				// 'callback'      => 'wpenon_show_on_array_whitelist',
				// 'callback_args' => array( 'field::l_info', 'anlage' ),
				// ),
				// 'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
				// 'validate_dependencies' => array( 'baujahr' ),
				// ),
				// 'l_standort'  => array(
				// 'type'        => 'select',
				// 'label'       => __( 'Standort der Lüftungsanlage', 'wpenon' ),
				// 'description' => __( 'Wählen Sie den Standort der Lüftungsanlage aus.', 'wpenon' ),
				// 'options'     => array(
				// 'innerhalb'         => __( 'innerhalb thermischer Hülle', 'wpenon' ),
				// 'ausserhalb_dach'   => __( 'Dach, außerhalb thermischer Hülle', 'wpenon' ),
				// 'ausserhalb_keller' => __( 'Keller, außerhalb thermischer Hülle', 'wpenon' ),
				// ),
				// 'required'    => true,
				// 'display'     => array(
				// 'callback'      => 'wpenon_show_on_array_whitelist',
				// 'callback_args' => array( 'field::l_info', 'anlage' ),
				// ),
				// ),
				'l_wirkungsgrad'    => array(
					'type'     => 'select',
					'label'    => __( 'Wärmerückgewinnung', 'wpenon' ),
					'options'  => array(
						'0'  => __( 'bis 59%', 'wpenon' ),
						'60' => __( 'bis 79%', 'wpenon' ),
						'80' => __( 'ab 80%', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::l_info', 'zu_abluft' ),
					),
					'required' => true,
				),
				'l_bedarfsgefuehrt' => array(
					'type'    => 'checkbox',
					'label'   => __( 'Ist die Lüftungsanlage bedarfsgeführt?', 'wpenon' ),
					'display' => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::l_info', array( 'zu_abluft', 'abluft' ) ),
					),
				),
				'dichtheit'         => array(
					'type'  => 'checkbox',
					'label' => __( 'Wurde eine Dichtheitsprüfung (z.B. Blower-Door-Test) erfolgreich durchgeführt?', 'wpenon' ),
				),
				'k_info'            => array(
					'type'     => 'select',
					'label'    => __( 'Gebäudekühlung', 'wpenon' ),
					'options'  => array(
						'nicht_vorhanden' => __( 'nicht vorhanden', 'wpenon' ),
						'vorhanden'       => __( 'vorhanden', 'wpenon' ),
					),
					'required' => true,
				),
				'k_leistung'        => array(
					'type'        => 'radio',
					'label'       => __( 'Kühlleistung', 'wpenon' ),
					'description' => __( 'Wie hoch ist die Kühlleistung der Klimaanlage?', 'wpenon' ),
					'required'    => true,
					'options'     => array(
						'groesser' => __( 'größer 12 kW', 'wpenon' ),
						'kleiner'  => __( 'kleiner oder gleich 12 kW', 'wpenon' ),
					),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::k_info', 'vorhanden' ),
					),
				),
				'k_baujahr'         => array(
					'type'        => 'text',
					'label'       => __( 'Baujahr der Klimaanlage', 'wpenon' ),
					'description' => __( 'Welches Baujahr hat die Klimaanlage? (Format MM/JJJJ)', 'wpenon' ),
					'required'    => true,
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_k_baujahr',
						'callback_args' => array( 'field::k_info', 'field::k_leistung' ),
					),
					'validate'    => 'wpenon_immoticket24_validate_month_year',
					'placeholder' => 'MM/JJJJ',
				),
				'k_typenschild'     => array(
					'type'      => 'image',
					'label'     => __( 'Foto des Typenschilds der Klimaanlage', 'wpenon' ),
					'required'  => true,
					'filetypes' => array(
						'image/png',
						'image/jpeg',
					),
					'display'   => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::k_info', 'vorhanden' ),
					),
				),
				'k_automation'      => array(
					'type'        => 'radio',
					'label'       => __( 'Gebäudeautomation', 'wpenon' ),
					'description' => __( 'Verfügt das Gebäude über eine Gebäudeautomation, die die Funktion der Gebäudetechnik überwacht?', 'wpenon' ),
					'options'     => array(
						'yes' => __( 'Ja', 'wpenon' ),
						'no'  => __( 'Nein', 'wpenon' ),
					),
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_k_automation',
						'callback_args' => array( 'field::k_info', 'field::k_leistung' ),
					),
					'required'    => true,
				),
				'k_inspektion'      => array(
					'type'        => 'text',
					'label'       => __( 'Letzte Inspektion', 'wpenon' ),
					'description' => __( 'Wann erfolgte die Inspektion? (Format MM/JJJJ)', 'wpenon' ),
					'display'     => array(
						'callback'      => 'wpenon_immoticket24_show_k_inspektion',
						'callback_args' => array( 'field::k_info', 'field::k_leistung', 'field::k_automation' ),
					),
					'validate'    => 'wpenon_immoticket24_validate_month_year',
					'placeholder' => 'MM/JJJJ',
				),

			),
		),
	),
);
