<?php

namespace Enev\Schema;

$anlage = array(
	'title'  => __( 'Anlage', 'wpenon' ),
	'groups' => array(
		'heizung'    => array(
			'title'       => __( 'Heizungsanlage', 'wpenon' ),
			'description' => __( 'Machen Sie hier Angaben zur Heizungsanlage / Wärmeerzeugung des Gebäudes. Sie können bis zu drei unterschiedliche Heizungsanlagen spezifizieren.', 'wpenon' ),
			'fields'      => array(
				'h_erzeugung'                                   => array(
					'type'        => 'select',
					'label'       => __( 'Typ der Heizungsanlage', 'wpenon' ),
					'description' => __( 'Falls Sie den mit Gas oder Öl betriebenen Typ der Heizungsanlage nicht bestimmen können, wählen Sie den Niedertemperaturkessel.', 'wpenon' ),
					'options'     => wpenon_immoticket24_get_heizungsanlagen2019(),
					'required'    => true,
				),
				'h_energietraeger_standardkessel'               => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'      => __( 'Heizöl', 'wpenon' ),
						'erdgas'       => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'  => __( 'Flüssiggas', 'wpenon' ),
						'erdgasbiogas' => __( 'Erdgas-Biogas-Gemisch', 'wpenon' ),
						'biogas'       => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'standardkessel' ),
					),
					'required' => true,
				),
				'h_energietraeger_fernwaerme'                   => array(
					'type'     => 'select',
					'label'    => __( 'Nah-/Fernwärme-Übergabestation', 'wpenon' ),
					'options'  => array(
						'fernwaermehzwfossil'      => __( 'Nah- und Fernwärme aus Heizwerken fossil', 'wpenon' ),
						'fernwaermehzwregenerativ' => __( 'Nah- und Fernwärme aus Heizwerken regenerativ', 'wpenon' ),
						'fernwaermekwkfossil'      => __( 'Nah- und Fernwärme mit Kraft-Wärme-Kopplung fossil', 'wpenon' ),
						'fernwaermekwkregenerativ' => __( 'Nah- und Fernwärme mit Kraft-Wärme-Kopplung regenerativ', 'wpenon' ),
						'biogas'                   => __( 'Nah- und Fernwärme mit Kraft-Wärme-Kopplung fossil mit Biomasseanteil', 'wpenon' ),
						// Gibt es nicht als Wert in Tabelle
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'fernwaerme' ),
					),
					'required' => true,
				),
				'h_energietraeger_niedertemperaturkessel'       => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'      => __( 'Heizöl', 'wpenon' ),
						'erdgas'       => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'  => __( 'Flüssiggas', 'wpenon' ),
						'erdgasbiogas' => __( 'Erdgas-Biogas-Gemisch', 'wpenon' ),
						'biogas'       => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'niedertemperaturkessel' ),
					),
					'required' => true,
				),
				'h_energietraeger_kleinthermeniedertemperatur'       => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'      => __( 'Heizöl', 'wpenon' ),
						'erdgas'       => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'  => __( 'Flüssiggas', 'wpenon' ),
						'erdgasbiogas' => __( 'Erdgas-Biogas-Gemisch', 'wpenon' ),
						'biogas'       => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'kleinthermeniedertemperatur' ),
					),
					'required' => true,
				),
				'h_energietraeger_kleinthermebrennwert'       => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'      => __( 'Heizöl', 'wpenon' ),
						'erdgas'       => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'  => __( 'Flüssiggas', 'wpenon' ),
						'erdgasbiogas' => __( 'Erdgas-Biogas-Gemisch', 'wpenon' ),
						'biogas'       => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'kleinthermebrennwert' ),
					),
					'required' => true,
				),
				'h_energietraeger_brennwertkessel'              => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'      => __( 'Heizöl', 'wpenon' ),
						'erdgas'       => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'  => __( 'Flüssiggas', 'wpenon' ),
						'erdgasbiogas' => __( 'Erdgas-Biogas-Gemisch', 'wpenon' ),
						'biogas'       => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'brennwertkessel' ),
					),
					'required' => true,
				),
				'h_energietraeger_brennwertkesselverbessert'    => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'      => __( 'Heizöl', 'wpenon' ),
						'erdgas'       => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'  => __( 'Flüssiggas', 'wpenon' ),
						'erdgasbiogas' => __( 'Erdgas-Biogas-Gemisch', 'wpenon' ),
						'biogas'       => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'brennwertkesselverbessert' ),
					),
					'required' => true,
				),
				'h_energietraeger_waermepumpeluft'              => array(
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
				'h_energietraeger_waermepumpewasser'            => array(
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
				'h_energietraeger_waermepumpeerde'              => array(
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
				'h_energietraeger_pelletfeuerung'               => array(
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
				'h_energietraeger_elektronachtspeicherheizung'  => array(
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
				'h_energietraeger_elektrodirektheizgeraet'      => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'elektrodirektheizgeraet' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h_energietraeger_kohleholzofen'                => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'braunkohle' => __( 'Braunkohle', 'wpenon' ),
						'steinkohle' => __( 'Steinkohle', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'kohleholzofen' ),
					),
					'required' => true,
				),
				'h_energietraeger_gasraumheizer'                => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'erdgas'      => __( 'Erdgas', 'wpenon' ),
						'fluessiggas' => __( 'Flüssiggas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'gasraumheizer' ),
					),
					'required' => true,
				),
				'h_energietraeger_oelofenverdampfungsbrenner'   => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel' => __( 'Heizöl', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h_erzeugung', 'oelofenverdampfungsbrenner' ),
					),
					'default'  => 'heizoel',
					'required' => true,
				),
				'h_energietraeger'                                  => array(
					'type'     => 'hidden',
					'value'        => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_by_erzeugung',
						'callback_args' => array(
							'field::h_erzeugung',
							'field::h_energietraeger_standardkessel',
							'field::h_energietraeger_niedertemperaturkessel',
							'field::h_energietraeger_brennwertkessel',
							'field::h_energietraeger_brennwertkesselverbessert',
							'field::h_energietraeger_kleinthermeniedertemperatur',
							'field::h_energietraeger_kleinthermebrennwert',
							'field::h_energietraeger_fernwaerme',
							'field::h_energietraeger_waermepumpeluft',
							'field::h_energietraeger_waermepumpewasser',
							'field::h_energietraeger_waermepumpeerde',
							'field::h_energietraeger_elektronachtspeicherheizung',
							'field::h_energietraeger_elektrodirektheizgeraet',
							'field::h_energietraeger_pelletfeuerung',
							'field::h_energietraeger_kohleholzofen',
							'field::h_energietraeger_gasraumheizer',
							'field::h_energietraeger_oelofenverdampfungsbrenner',
						),
					),
				),
				'h_deckungsanteil'                              => array(
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
							array( 'h2' => 'field::h2_deckungsanteil', 'h3' => 'field::h3_deckungsanteil' ),
							array( 'h2' => 'field::h2_info', 'h3' => 'field::h3_info' ),
							true
						),
					),
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h2_info', true ),
					),
				),
				'h_baujahr'                                     => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr der Heizungsanlage', 'wpenon' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
				),
				'h2_info'                                       => array(
					'type'  => 'checkbox',
					'label' => __( '2. Heizungsanlage vorhanden?', 'wpenon' ),
				),
				'h2_erzeugung'                                  => array(
					'type'        => 'select',
					'label'       => __( 'Typ der 2. Heizungsanlage', 'wpenon' ),
					'description' => __( 'Falls Sie den mit Gas oder Öl betriebenen Typ der Heizungsanlage nicht bestimmen können, wählen Sie den Niedertemperaturkessel.', 'wpenon' ),
					'options'     => wpenon_immoticket24_get_heizungsanlagen2019(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h2_info', true ),
					),
				),
				'h2_energietraeger_standardkessel'              => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'      => __( 'Heizöl', 'wpenon' ),
						'erdgas'       => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'  => __( 'Flüssiggas', 'wpenon' ),
						'erdgasbiogas' => __( 'Erdgas-Biogas-Gemisch', 'wpenon' ),
						'biogas'       => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'standardkessel' ),
					),
					'required' => true,
				),
				'h2_energietraeger_fernwaerme'                  => array(
					'type'     => 'select',
					'label'    => __( 'Nah-/Fernwärme-Übergabestation', 'wpenon' ),
					'options'  => array(
						'fernwaermehzwfossil'      => __( 'Nah- und Fernwärme aus Heizwerken fossil', 'wpenon' ),
						'fernwaermehzwregenerativ' => __( 'Nah- und Fernwärme aus Heizwerken regenerativ', 'wpenon' ),
						'fernwaermekwkfossil'      => __( 'Nah- und Fernwärme mit Kraft-Wärme-Kopplung fossil', 'wpenon' ),
						'fernwaermekwkregenerativ' => __( 'Nah- und Fernwärme mit Kraft-Wärme-Kopplung regenerativ', 'wpenon' ),
						'biogas'                   => __( 'Nah- und Fernwärme mit Kraft-Wärme-Kopplung fossil mit Biomasseanteil', 'wpenon' ),
						// Gibt es nicht als Wert in Tabelle
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'fernwaerme' ),
					),
					'required' => true,
				),
				'h2_energietraeger_niedertemperaturkessel'      => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'      => __( 'Heizöl', 'wpenon' ),
						'erdgas'       => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'  => __( 'Flüssiggas', 'wpenon' ),
						'erdgasbiogas' => __( 'Erdgas-Biogas-Gemisch', 'wpenon' ),
						'biogas'       => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'niedertemperaturkessel' ),
					),
					'required' => true,
				),
				'h2_energietraeger_brennwertkessel'             => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'      => __( 'Heizöl', 'wpenon' ),
						'erdgas'       => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'  => __( 'Flüssiggas', 'wpenon' ),
						'erdgasbiogas' => __( 'Erdgas-Biogas-Gemisch', 'wpenon' ),
						'biogas'       => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'brennwertkessel' ),
					),
					'required' => true,
				),
				'h2_energietraeger_brennwertkesselverbessert'   => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'      => __( 'Heizöl', 'wpenon' ),
						'erdgas'       => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'  => __( 'Flüssiggas', 'wpenon' ),
						'erdgasbiogas' => __( 'Erdgas-Biogas-Gemisch', 'wpenon' ),
						'biogas'       => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'brennwertkesselverbessert' ),
					),
					'required' => true,
				),
				'h2_energietraeger_waermepumpeluft'             => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'waermepumpeluft' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h2_energietraeger_waermepumpewasser'           => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'waermepumpewasser' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h2_energietraeger_waermepumpeerde'             => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'waermepumpeerde' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h2_energietraeger_pelletfeuerung'              => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'holzpellets'       => __( 'Holzpellets', 'wpenon' ),
						'holzhackschnitzel' => __( 'Holzhackschnitzel', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'pelletfeuerung' ),
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
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'elektronachtspeicherheizung' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h2_energietraeger_elektrodirektheizgeraet'     => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'elektrodirektheizgeraet' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h2_energietraeger_kohleholzofen'               => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'braunkohle' => __( 'Braunkohle', 'wpenon' ),
						'steinkohle' => __( 'Steinkohle', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'kohleholzofen' ),
					),
					'required' => true,
				),
				'h2_energietraeger_gasraumheizer'               => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'erdgas'      => __( 'Erdgas', 'wpenon' ),
						'fluessiggas' => __( 'Flüssiggas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'gasraumheizer' ),
					),
					'required' => true,
				),
				'h2_energietraeger_oelofenverdampfungsbrenner'  => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel' => __( 'Heizöl', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h2_erzeugung', 'oelofenverdampfungsbrenner' ),
					),
					'default'  => 'heizoel',
					'required' => true,
				),
				'h2_energietraeger'                                  => array(
					'type'     => 'hidden',
					'value'        => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_by_erzeugung',
						'callback_args' => array(
							'field::h2_erzeugung',
							'field::h2_energietraeger_standardkessel',
							'field::h2_energietraeger_niedertemperaturkessel',
							'field::h2_energietraeger_brennwertkessel',
							'field::h2_energietraeger_brennwertkesselverbessert',
							'field::h2_energietraeger_kleinthermeniedertemperatur',
							'field::h2_energietraeger_kleinthermebrennwert',
							'field::h2_energietraeger_fernwaerme',
							'field::h2_energietraeger_waermepumpeluft',
							'field::h2_energietraeger_waermepumpewasser',
							'field::h2_energietraeger_waermepumpeerde',
							'field::h2_energietraeger_elektronachtspeicherheizung',
							'field::h2_energietraeger_elektrodirektheizgeraet',
							'field::h2_energietraeger_pelletfeuerung',
							'field::h2_energietraeger_kohleholzofen',
							'field::h2_energietraeger_gasraumheizer',
							'field::h2_energietraeger_oelofenverdampfungsbrenner',
						),
					),
				),
				'h2_deckungsanteil'                             => array(
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
							array( 'h' => 'field::h_deckungsanteil', 'h3' => 'field::h3_deckungsanteil' ),
							array( 'h' => true, 'h3' => 'field::h3_info' ),
							true
						),
					),
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h2_info', true ),
					),
				),
				'h2_baujahr'                                    => array(
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
				'h3_info'                                       => array(
					'type'    => 'checkbox',
					'label'   => __( '3. Heizungsanlage vorhanden?', 'wpenon' ),
					'display' => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::h2_info', true ),
					),
				),
				'h3_erzeugung'                                  => array(
					'type'        => 'select',
					'label'       => __( 'Typ der 3. Heizungsanlage', 'wpenon' ),
					'description' => __( 'Falls Sie den mit Gas oder Öl betriebenen Typ der Heizungsanlage nicht bestimmen können, wählen Sie den Niedertemperaturkessel.', 'wpenon' ),
					'options'     => wpenon_immoticket24_get_heizungsanlagen2019(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
					),
				),
				'h3_energietraeger_standardkessel'              => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'      => __( 'Heizöl', 'wpenon' ),
						'erdgas'       => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'  => __( 'Flüssiggas', 'wpenon' ),
						'erdgasbiogas' => __( 'Erdgas-Biogas-Gemisch', 'wpenon' ),
						'biogas'       => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'standardkessel' ),
					),
					'required' => true,
				),
				'h3_energietraeger_fernwaerme'                  => array(
					'type'     => 'select',
					'label'    => __( 'Nah-/Fernwärme-Übergabestation', 'wpenon' ),
					'options'  => array(
						'fernwaermehzwfossil'      => __( 'Nah- und Fernwärme aus Heizwerken fossil', 'wpenon' ),
						'fernwaermehzwregenerativ' => __( 'Nah- und Fernwärme aus Heizwerken regenerativ', 'wpenon' ),
						'fernwaermekwkfossil'      => __( 'Nah- und Fernwärme mit Kraft-Wärme-Kopplung fossil', 'wpenon' ),
						'fernwaermekwkregenerativ' => __( 'Nah- und Fernwärme mit Kraft-Wärme-Kopplung regenerativ', 'wpenon' ),
						'biogas'                   => __( 'Nah- und Fernwärme mit Kraft-Wärme-Kopplung fossil mit Biomasseanteil', 'wpenon' ),
						// Gibt es nicht als Wert in Tabelle
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'fernwaerme' ),
					),
					'required' => true,
				),
				'h3_energietraeger_niedertemperaturkessel'      => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'      => __( 'Heizöl', 'wpenon' ),
						'erdgas'       => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'  => __( 'Flüssiggas', 'wpenon' ),
						'erdgasbiogas' => __( 'Erdgas-Biogas-Gemisch', 'wpenon' ),
						'biogas'       => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'niedertemperaturkessel' ),
					),
					'required' => true,
				),
				'h3_energietraeger_brennwertkessel'             => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'      => __( 'Heizöl', 'wpenon' ),
						'erdgas'       => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'  => __( 'Flüssiggas', 'wpenon' ),
						'erdgasbiogas' => __( 'Erdgas-Biogas-Gemisch', 'wpenon' ),
						'biogas'       => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'brennwertkessel' ),
					),
					'required' => true,
				),
				'h3_energietraeger_brennwertkesselverbessert'   => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel'      => __( 'Heizöl', 'wpenon' ),
						'erdgas'       => __( 'Erdgas', 'wpenon' ),
						'fluessiggas'  => __( 'Flüssiggas', 'wpenon' ),
						'erdgasbiogas' => __( 'Erdgas-Biogas-Gemisch', 'wpenon' ),
						'biogas'       => __( 'Biogas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'brennwertkesselverbessert' ),
					),
					'required' => true,
				),
				'h3_energietraeger_waermepumpeluft'             => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'waermepumpeluft' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h3_energietraeger_waermepumpewasser'           => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'waermepumpewasser' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h3_energietraeger_waermepumpeerde'             => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'waermepumpeerde' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h3_energietraeger_pelletfeuerung'              => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'holzpellets'       => __( 'Holzpellets', 'wpenon' ),
						'holzhackschnitzel' => __( 'Holzhackschnitzel', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'pelletfeuerung' ),
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
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'elektronachtspeicherheizung' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h3_energietraeger_elektrodirektheizgeraet'     => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'elektrodirektheizgeraet' ),
					),
					'default'  => 'strom',
					'required' => true,
				),
				'h3_energietraeger_kohleholzofen'               => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'braunkohle' => __( 'Braunkohle', 'wpenon' ),
						'steinkohle' => __( 'Steinkohle', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'kohleholzofen' ),
					),
					'required' => true,
				),
				'h3_energietraeger_gasraumheizer'               => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'erdgas'      => __( 'Erdgas', 'wpenon' ),
						'fluessiggas' => __( 'Flüssiggas', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'gasraumheizer' ),
					),
					'required' => true,
				),
				'h3_energietraeger_oelofenverdampfungsbrenner'  => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
					'options'  => array(
						'heizoel' => __( 'Heizöl', 'wpenon' ),
					),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::h3_erzeugung', 'oelofenverdampfungsbrenner' ),
					),
					'default'  => 'heizoel',
					'required' => true,
				),
				'h3_energietraeger'                                  => array(
					'type'     => 'hidden',
					'value'        => array(
						'callback'      => 'wpenon_immoticket24_get_energietraeger_by_erzeugung',
						'callback_args' => array(
							'field::h3_erzeugung',
							'field::h3_energietraeger_standardkessel',
							'field::h3_energietraeger_niedertemperaturkessel',
							'field::h3_energietraeger_brennwertkessel',
							'field::h3_energietraeger_brennwertkesselverbessert',
							'field::h3_energietraeger_kleinthermeniedertemperatur',
							'field::h3_energietraeger_kleinthermebrennwert',
							'field::h3_energietraeger_fernwaerme',
							'field::h3_energietraeger_waermepumpeluft',
							'field::h3_energietraeger_waermepumpewasser',
							'field::h3_energietraeger_waermepumpeerde',
							'field::h3_energietraeger_elektronachtspeicherheizung',
							'field::h3_energietraeger_elektrodirektheizgeraet',
							'field::h3_energietraeger_pelletfeuerung',
							'field::h3_energietraeger_kohleholzofen',
							'field::h3_energietraeger_gasraumheizer',
							'field::h3_energietraeger_oelofenverdampfungsbrenner',
						),
					),
				),
				'h3_deckungsanteil'                             => array(
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
							array( 'h' => 'field::h_deckungsanteil', 'h2' => 'field::h2_deckungsanteil' ),
							array( 'h' => true, 'h2' => 'field::h2_info' ),
							true
						),
					),
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
					),
				),
				'h3_baujahr'                                    => array(
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
				'verteilung_baujahr'                            => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr des Rohrleitungssystems', 'wpenon' ),
					'description'           => __( 'Geben Sie hier das Baujahr der freiliegenden Heizungsrohre an.' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_blacklist',
						'callback_args' => array( 'field::h_erzeugung', array( 'elektronachtspeicherheizung', 'elektrodirektheizgeraet', 'kohleholzofen', 'kleinthermeniedertemperatur', 'kleinthermebrennwert', 'gasraumheizer', 'oelofenverdampfungsbrenner' ) ),
					),
				),
				'verteilung_gedaemmt'                           => array(
					'type'    => 'checkbox',
					'label'   => __( 'Freiliegende Heizungsrohre zusätzlich gedämmt?', 'wpenon' ),
					'display' => array(
						'callback'      => 'wpenon_show_on_number_lower',
						'callback_args' => array( 'field::verteilung_baujahr', 1978 ),
					),
				),
				'speicherung'                                   => array(
					'type'  => 'checkbox',
					'label' => __( 'Pufferspeicher vorhanden?', 'wpenon' ),
				),
				'speicherung_baujahr'                           => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr des Pufferspeichers', 'wpenon' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
					'display'               => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::speicherung', true ),
					),
				),
				'speicherung_standort'                          => array(
					'type'        => 'select',
					'label'       => __( 'Standort des Pufferspeichers', 'wpenon' ),
					'description' => __( 'Wählen Sie aus, ob sich der Pufferspeicher innerhalb oder außerhalb der thermischen Hülle befindet.', 'wpenon' ),
					'options'     => array(
						'innerhalb'  => __( 'innerhalb thermischer Hülle', 'wpenon' ),
						'ausserhalb' => __( 'außerhalb thermischer Hülle', 'wpenon' ),
					),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_bool_compare',
						'callback_args' => array( 'field::speicherung', true ),
					),
				),
			),
		),
		'warmwasser' => array(
			'title'       => __( 'Warmwasseranlage', 'wpenon' ),
			'description' => __( 'Machen Sie hier Angaben zur Warmwassererzeugung des Gebäudes.', 'wpenon' ),
			'fields'      => array(
				'ww_info'                                    => array(
					'type'        => 'select',
					'label'       => __( 'Art der Warmwassererzeugung', 'wpenon' ),
					'description' => __( 'Wählen Sie aus, ob die Warmwasserzeugung durch eine der angegebenen Heizungsanlagen oder in einer separaten Anlage stattfindet.', 'wpenon' ),
					'options'     => array(
						'callback'      => 'wpenon_immoticket24_get_ww_info',
						'callback_args' => array(
							'field::h2_info',
							'field::h3_info',
							'field::h_erzeugung',
							'field::h2_erzeugung',
							'field::h3_erzeugung'
						),
					),
					'required'    => true,
				),
				'ww_erzeugung'                               => array(
					'type'        => 'select',
					'label'       => __( 'Typ der Warmwasseranlage', 'wpenon' ),
					'description' => __( 'Falls Sie den mit Gas oder Öl betriebenen Typ der Warmwasseranlage nicht bestimmen können, wählen Sie den Niedertemperaturkessel.', 'wpenon' ),
					'options'     => wpenon_immoticket24_get_warmwasseranlagen202001(),
					'required'    => true,
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::ww_info', 'ww' ),
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
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::ww_erzeugung', 'dezentralelektroerhitzer' ),
					),
				),
				'ww_energietraeger_dezentralgaserhitzer' => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Warmwasseranlage', 'wpenon' ),
					'required' => true,
					'options'  => array(
						'erdgas' => __( 'Erdgas', 'wpenon' ),
					),
					'default'  => 'erdgas',
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::ww_erzeugung', 'dezentralgaserhitzer' ),
					),
				),
				'ww_energietraeger_dezentralkleinspeicher' => array(
					'type'     => 'select',
					'label'    => __( 'Energieträger der Warmwasseranlage', 'wpenon' ),
					'required' => true,
					'options'  => array(
						'strom' => __( 'Strom', 'wpenon' ),
					),
					'default'  => 'strom',
					'display'  => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::ww_erzeugung', 'dezentralkleinspeicher' ),
					),
				),
				'ww_baujahr'                                 => array(
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
				'verteilung_versorgung'                      => array(
					'type'        => 'select',
					'label'       => __( 'Warmwasserverteilung', 'wpenon' ),
					'description' => __( 'Wählen Sie aus, ob die Warmwassererzeugung mit oder ohne Zirkulation erfolgt.', 'wpenon' ),
					'options'     => array(
						'ohne' => __( 'ohne Zirkulation', 'wpenon' ),
						'mit'  => __( 'mit Zirkulation', 'wpenon' ),
					),
					'required'    => true,
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
				'l_baujahr'   => array(
					'type'                  => 'int',
					'label'                 => __( 'Baujahr der Lüftungsanlage', 'wpenon' ),
					'min'                   => 1800,
					'max'                   => wpenon_get_reference_date( 'Y' ),
					'required'              => true,
					'display'               => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::l_info', 'anlage' ),
					),
					'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
					'validate_dependencies' => array( 'baujahr' ),
				),
				'l_standort'  => array(
					'type'        => 'select',
					'label'       => __( 'Standort der Lüftungsanlage', 'wpenon' ),
					'description' => __( 'Wählen Sie den Standort der Lüftungsanlage aus.', 'wpenon' ),
					'options'     => array(
						'innerhalb'         => __( 'innerhalb thermischer Hülle', 'wpenon' ),
						'ausserhalb_dach'   => __( 'Dach, außerhalb thermischer Hülle', 'wpenon' ),
						'ausserhalb_keller' => __( 'Keller, außerhalb thermischer Hülle', 'wpenon' ),
					),
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
				'dichtheit'   => array(
					'type'  => 'checkbox',
					'label' => __( 'Wurde eine Dichtheitsprüfung (z.B. Blower-Door-Test) erfolgreich durchgeführt?', 'wpenon' ),
				),
			),
		),
	),
);
