<?php

return array(
  'vw_basisdaten'         => array(
    'title'                 => __( 'Basisdaten', 'wpenon' ),
    'groups'                => array(
      'energieausweis'        => array(
        'title'                 => __( 'Allgemein', 'wpenon' ),
        'description'           => __( 'Wählen Sie hier die passenden Angaben für Ihren Energieausweis aus.', 'wpenon' ),
        'fields'                => array(
          'anlass'                => array(
            'type'                  => 'select',
            'label'                 => __( 'Anlass', 'wpenon' ),
            'description'           => __( 'Wählen Sie aus, für welchen Zweck dieser Energieausweis verwendet werden soll.', 'wpenon' ),
            'options'               => array(
              'modernisierung'        => __( 'Modernisierung / Erweiterung', 'wpenon' ),
              'vermietung'            => __( 'Vermietung', 'wpenon' ),
              'verkauf'               => __( 'Verkauf', 'wpenon' ),
              'sonstiges'             => __( 'sonstiges', 'wpenon' ),
            ),
            'required'              => true,
          ),
        ),
      ),
      'gebaeude'              => array(
        'title'                 => __( 'Gebäudeinformationen', 'wpenon' ),
        'description'           => __( 'Machen Sie hier grundsätzliche Angaben zum Gebäude.', 'wpenon' ),
        'fields'                => array(
          'gebaeudetyp'           => array(
            'type'                  => 'select',
            'label'                 => __( 'Gebäudetyp', 'wpenon' ),
            'description'           => __( 'Wählen Sie den passenden Typ für das Gebäude aus.', 'wpenon' ),
            'options'               => array(
              'freistehend'            => __( 'freistehendes Haus', 'wpenon' ),
              'reihenhaus'             => __( 'Reihenhaus', 'wpenon' ),
              'reiheneckhaus'          => __( 'Reiheneckhaus', 'wpenon' ),
              'doppelhaushaelfte'      => __( 'Doppelhaushälfte', 'wpenon' ),
              'fertighausfachwerkhaus' => __( 'Fertighaus/Fachwerkhaus', 'wpenon' ),
              'mehrfamilienhaus'       => __( 'Mehrfamilienhaus', 'wpenon' ),
              'sonstiges'              => __( 'sonstiges Wohngebäude', 'wpenon' ),
            ),
            'disabled_options'      => array(
              'wohnung'               => __( 'Wohnung (gemäß EnEV nicht möglich)', 'wpenon' ),
            ),
            'required'              => true,
          ),
          'gebaeudeteil'          => array(
            'type'                  => 'radio',
            'label'                 => __( 'Gebäudeteil', 'wpenon' ),
            'description'           => __( 'Wählen Sie den Gebäudeteil aus, für den der Energieausweis erstellt wird.', 'wpenon' ),
            'options'               => array(
              'gesamt'                => __( 'Gesamtes Gebäude', 'wpenon' ),
              'gemischt'              => __( 'Wohnteil gemischt genutztes Gebäude', 'wpenon' ),
            ),
            'required'              => true,
          ),
			'geschosse'             => array(
				'type'                  => 'int',
				'label'                 => __( 'Anzahl der Geschosse', 'wpenon' ),
				'description'           => __( 'Geben Sie die Anzahl der Geschosse im Gebäude ein.', 'wpenon' ),
				'default'               => 1,
				'min'                   => 1,
				'required'              => true,
			),
          'wohnungen'             => array(
            'type'                  => 'int',
            'label'                 => __( 'Wohnungen', 'wpenon' ),
            'description'           => __( 'Geben Sie die Anzahl der Wohnungen im Gebäude ein.', 'wpenon' ),
            'default'               => 1,
            'min'                   => 1,
            'required'              => true,
          ),
          'baujahr'               => array(
            'type'                  => 'int',
            'label'                 => __( 'Baujahr', 'wpenon' ),
            'description'           => __( 'Geben Sie das Baujahr des Gebäudes an.', 'wpenon' ),
            'min'                   => 1800,
            'max'                   => wpenon_get_reference_date( 'Y' ),
            'required'              => true,
          ),
          'flaeche'               => array(
            'type'                  => 'float',
            'label'                 => __( 'Wohnfläche', 'wpenon' ),
            'description'           => __( 'Da Energieausweise stets gebäudebezogen sind, geben Sie hier die Wohnfläche des gesamten Gebäudes (ohne Keller) in Quadratmetern an. Hieraus wird im Energieausweis automatisch die Gebäudenutzfläche berechnet.', 'wpenon' ),
            'required'              => true,
            'unit'                  => 'm&sup2;',
          ),
        ),
      ),
      'regenerativ'           => array(
        'title'                 => __( 'Erneuerbare Energien', 'wpenon' ),
        'description'           => __( 'Falls Ihr Gebäude zum Teil erneuerbare Energien verwendet, machen Sie hier entsprechende Angaben.', 'wpenon' ),
        'fields'                => array(
          'regenerativ_art'       => array(
            'type'                  => 'text',
            'label'                 => __( 'Art der erneuerbaren Energien', 'wpenon' ),
            'description'           => __( 'Geben Sie die Art der erneuerbaren Energien ein, sofern Sie Photovoltaik, Geothermie, Solaranlage, Windenergie oder Energie aus Biogas nutzen. Falls nicht vorhanden, geben Sie bitte &quot;Keine&quot; ein. Dies dient ausschließlich der Information.', 'wpenon' ),
            'default'               => __( 'Keine', 'wpenon' ),
            'required'              => true,
            'max'                   => 40,
          ),
          'regenerativ_nutzung'   => array(
            'type'                  => 'text',
            'label'                 => __( 'Verwendung der erneuerbaren Energien', 'wpenon' ),
            'description'           => __( 'Geben Sie die Verwendung der erneuerbaren Energien ein, wenn Sie diese zur Wärmeerzeugung, Warmwassererzeugung, Energiespeicherung oder Stromerzeugung nutzen. Falls nicht vorhanden, geben Sie bitte &quot;Keine&quot; ein. Dies dient ausschließlich der Information.', 'wpenon' ),
            'default'               => __( 'Keine', 'wpenon' ),
            'required'              => true,
            'max'                   => 40,
          ),
        ),
      ),
    ),
  ),
  'vw_bauteile'           => array(
    'title'                 => __( 'Bauteile', 'wpenon' ),
    'groups'                => array(
      'bauteile_basis'        => array(
        'title'                 => __( 'Grundbauteile', 'wpenon' ),
        'description'           => __( 'Geben Sie die relevanten Daten für die Grundbestandteile des Gebäudes an.', 'wpenon' ),
        'fields'                => array(
          'wand_daemmung'         => array(
            'type'                  => 'int',
            'label'                 => __( 'Nachträgliche Wanddämmung', 'wpenon' ),
            'description'           => __( 'Falls die Außenwände zusätzlich gedämmt worden sind, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon' ),
            'unit'                  => 'cm',
            'max'                   => 23,
          ),
          'wand_porenbeton'       => array(
            'type'                  => 'select',
            'label'                 => __( 'Sind die Außenwände aus Porenbeton (z.B. Ytong)?', 'wpenon' ),
            'options'               => array(
              'ja'                    => __( 'Ja', 'wpenon' ),
              'nein'                  => __( 'Nein', 'wpenon' ),
              'unbekannt'             => __( 'Unbekannt', 'wpenon' ),
            ),
            'display'               => array(
              'callback'              => 'wpenon_immoticket24_show_wand_porenbeton_verbrauch',
              'callback_args'         => array( 'field::wand_daemmung' ),
            ),
          ),
          'decke_daemmung'        => array(
            'type'                  => 'int',
            'label'                 => __( 'Nachträgliche Deckendämmung', 'wpenon' ),
            'description'           => __( 'Falls die Oberste Geschossdecke zusätzlich gedämmt worden ist, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon' ),
            'unit'                  => 'cm',
            'max'                   => 30,
            'display'               => array(
              'callback'              => 'wpenon_show_on_array_blacklist',
              'callback_args'         => array( 'field::dach', 'beheizt' ),
            ),
          ),
          'boden_daemmung'        => array(
            'type'                  => 'int',
            'label'                 => __( 'Nachträgliche Bodendämmung', 'wpenon' ),
            'description'           => __( 'Falls die Bodenplatte / Kellerdecke zusätzlich gedämmt worden ist, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon' ),
            'unit'                  => 'cm',
            'max'                   => 25,
          ),
        ),
      ),
      'bauteile_dach'         => array(
        'title'                 => __( 'Dach', 'wpenon' ),
        'description'           => __( 'Geben Sie die relevanten Daten für das Dachgeschoss des Gebäudes an.', 'wpenon' ),
        'fields'                => array(
          'dach'                  => array(
            'type'                  => 'select',
            'label'                 => __( 'Dachgeschoss', 'wpenon' ),
            'options'               => array(
              'nicht-vorhanden'       => __( 'nicht vorhanden', 'wpenon' ),
              'unbeheizt'             => __( 'unbeheizt', 'wpenon' ),
              'beheizt'               => __( 'beheizt', 'wpenon' ),
            ),
            'required'              => true,
          ),
          'dach_daemmung'         => array(
            'type'                  => 'int',
            'label'                 => __( 'Dachdämmung', 'wpenon' ),
            'description'           => __( 'Falls das Dach zusätzlich gedämmt worden ist, geben Sie hier dessen Dämmstärke in Zentimetern an.', 'wpenon' ),
            'unit'                  => 'cm',
            'max'                   => 30,
            'display'               => array(
              'callback'              => 'wpenon_show_on_array_whitelist',
              'callback_args'         => array( 'field::dach', 'beheizt' ),
            ),
          ),
        ),
      ),
      'bauteile_keller'       => array(
        'title'                 => __( 'Keller', 'wpenon' ),
        'description'           => __( 'Geben Sie die relevanten Daten für das Kellergeschoss des Gebäudes an.', 'wpenon' ),
        'fields'                => array(
          'keller'                => array(
            'type'                  => 'select',
            'label'                 => __( 'Kellergeschoss', 'wpenon' ),
            'options'               => array(
              'nicht-vorhanden'       => __( 'Nicht vorhanden', 'wpenon' ),
              'unbeheizt'             => __( 'Unbeheizt', 'wpenon' ),
              'beheizt'               => __( 'Beheizt', 'wpenon' ),
              'teilunterkellert'      => __( 'Teilunterkellert', 'wpenon' ),
              'vollunterkellert'      => __( 'Voll unterkellert', 'wpenon' ),
            ),
            'required'              => true,
          ),
          'keller_daemmung'       => array(
            'type'                  => 'int',
            'label'                 => __( 'Kellerwanddämmung', 'wpenon' ),
            'description'           => __( 'Falls die Kellerwände zusätzlich gedämmt worden sind, geben Sie hier deren Dämmstärke in Zentimetern an.', 'wpenon' ),
            'unit'                  => 'cm',
            'max'                   => 23,
            'display'               => array(
              'callback'              => 'wpenon_show_on_array_whitelist',
              'callback_args'         => array( 'field::keller', 'beheizt' ),
            ),
          ),
        ),
      ),
      'bauteile_fenster'      => array(
        'title'                 => __( 'Fenster', 'wpenon' ),
        'description'           => __( 'Geben Sie die relevanten Daten für die Fenster des Gebäudes an.', 'wpenon' ),
        'fields'                => array(
          'fenster_bauart'        => array(
            'type'                  => 'select',
            'label'                 => __( 'Bauart der Fenster', 'wpenon' ),
            'options'               => wpenon_immoticket24_get_fenster_bauarten(),
            'required'              => true,
          ),
          'fenster_baujahr'       => array(
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
  ),
  'vw_anlage'             => array(
    'title'                 => __( 'Anlage', 'wpenon' ),
    'groups'                => array(
      'heizung'               => array(
        'title'                 => __( 'Heizungsanlage', 'wpenon' ),
        'description'           => __( 'Machen Sie hier Angaben zur Heizungsanlage / Wärmeerzeugung des Gebäudes. Sie können bis zu drei unterschiedliche Heizungsanlagen spezifizieren.', 'wpenon' ),
        'fields'                => array(
          'h_erzeugung'           => array(
            'type'                  => 'select',
            'label'                 => __( 'Typ der Heizungsanlage', 'wpenon' ),
            'options'               => wpenon_immoticket24_get_heizungsanlagen(),
            'required'              => true,
          ),
          'h_energietraeger'      => array(
            'type'                  => 'select',
            'label'                 => __( 'Energieträger der Heizungsanlage', 'wpenon' ),
            'options'               => wpenon_immoticket24_get_energietraeger( true ),
            'required'              => true,
          ),
          'h_baujahr'             => array(
            'type'                  => 'int',
            'label'                 => __( 'Baujahr der Heizungsanlage', 'wpenon' ),
            'min'                   => 1800,
            'max'                   => wpenon_get_reference_date( 'Y' ),
            'required'              => true,
            'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
            'validate_dependencies' => array( 'baujahr' ),
          ),
          /*'h_custom'              => array(
            'type'                  => 'checkbox',
            'label'                 => __( 'Benutzerdefinierten Primärenergiefaktor verwenden?', 'wpenon' ),
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
          ),*/
          'h2_info'               => array(
            'type'                  => 'checkbox',
            'label'                 => __( '2. Heizungsanlage vorhanden?', 'wpenon' ),
          ),
          'h2_erzeugung'          => array(
            'type'                  => 'select',
            'label'                 => __( 'Typ der 2. Heizungsanlage', 'wpenon' ),
            'options'               => wpenon_immoticket24_get_heizungsanlagen(),
            'required'              => true,
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( 'field::h2_info', true ),
            ),
          ),
          'h2_energietraeger'     => array(
            'type'                  => 'select',
            'label'                 => __( 'Energieträger der 2. Heizungsanlage', 'wpenon' ),
            'options'               => wpenon_immoticket24_get_energietraeger( true ),
            'required'              => true,
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( 'field::h2_info', true ),
            ),
          ),
          'h2_baujahr'            => array(
            'type'                  => 'int',
            'label'                 => __( 'Baujahr der 2. Heizungsanlage', 'wpenon' ),
            'min'                   => 1800,
            'max'                   => wpenon_get_reference_date( 'Y' ),
            'required'              => true,
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( 'field::h2_info', true ),
            ),
            'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
            'validate_dependencies' => array( 'baujahr' ),
          ),
          /*'h2_custom'             => array(
            'type'                  => 'checkbox',
            'label'                 => __( 'Benutzerdefinierten Primärenergiefaktor für den 2. Energieträger verwenden?', 'wpenon' ),
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
          ),*/
          'h3_info'               => array(
            'type'                  => 'checkbox',
            'label'                 => __( '3. Heizungsanlage vorhanden?', 'wpenon' ),
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( 'field::h2_info', true ),
            ),
          ),
          'h3_erzeugung'          => array(
            'type'                  => 'select',
            'label'                 => __( 'Typ der 3. Heizungsanlage', 'wpenon' ),
            'options'               => wpenon_immoticket24_get_heizungsanlagen(),
            'required'              => true,
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
            ),
          ),
          'h3_energietraeger'     => array(
            'type'                  => 'select',
            'label'                 => __( 'Energieträger der 3. Heizungsanlage', 'wpenon' ),
            'options'               => wpenon_immoticket24_get_energietraeger( true ),
            'required'              => true,
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
            ),
          ),
          'h3_baujahr'            => array(
            'type'                  => 'int',
            'label'                 => __( 'Baujahr der 3. Heizungsanlage', 'wpenon' ),
            'min'                   => 1800,
            'max'                   => wpenon_get_reference_date( 'Y' ),
            'required'              => true,
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
            ),
            'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
            'validate_dependencies' => array( 'baujahr' ),
          ),
          /*'h3_custom'             => array(
            'type'                  => 'checkbox',
            'label'                 => __( 'Benutzerdefinierten Primärenergiefaktor für den 3. Energieträger verwenden?', 'wpenon' ),
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
          ),*/
        ),
      ),
      'warmwasser'            => array(
        'title'                 => __( 'Warmwasseranlage', 'wpenon' ),
        'description'           => __( 'Machen Sie hier Angaben zur Warmwassererzeugung des Gebäudes.', 'wpenon' ),
        'fields'                => array(
          'ww_info'               => array(
            'type'                  => 'select',
            'label'                 => __( 'Art der Warmwassererzeugung', 'wpenon' ),
            'description'           => __( 'Wählen Sie aus, ob die Warmwasserzeugung durch eine der angegebenen Heizungsanlagen oder in einer separaten Anlage stattfindet. Alternativ können Sie auch &quot;Unbekannt&quot; auswählen, in diesem Fall wird der Verbrauch pauschal um 20 kWh/(m&sup2;a) erhöht.', 'wpenon' ),
            'options'               => array(
              'callback'              => 'wpenon_immoticket24_get_ww_info',
              'callback_args'         => array( 'field::h2_info', 'field::h3_info', false, false, false, true ),
            ),
            'required'              => true,
          ),
          'ww_erzeugung'          => array(
            'type'                  => 'select',
            'label'                 => __( 'Typ der Warmwasseranlage', 'wpenon' ),
            'options'               => wpenon_immoticket24_get_warmwasseranlagen(),
            'required'              => true,
            'display'               => array(
              'callback'              => 'wpenon_show_on_array_whitelist',
              'callback_args'         => array( 'field::ww_info', 'ww' ),
            ),
          ),
          'ww_energietraeger'     => array(
            'type'                  => 'select',
            'label'                 => __( 'Energieträger der Warmwasseranlage', 'wpenon' ),
            'options'               => wpenon_immoticket24_get_energietraeger( true ),
            'required'              => true,
            'display'               => array(
              'callback'              => 'wpenon_show_on_array_whitelist',
              'callback_args'         => array( 'field::ww_info', 'ww' ),
            ),
          ),
          'ww_baujahr'            => array(
            'type'                  => 'int',
            'label'                 => __( 'Baujahr der Warmwasseranlage', 'wpenon' ),
            'min'                   => 1800,
            'max'                   => wpenon_get_reference_date( 'Y' ),
            'required'              => true,
            'display'               => array(
              'callback'              => 'wpenon_show_on_array_whitelist',
              'callback_args'         => array( 'field::ww_info', 'ww' ),
            ),
            'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
            'validate_dependencies' => array( 'baujahr' ),
          ),
        ),
      ),
      'lueftung'              => array(
        'title'                 => __( 'Lüftungsanlage', 'wpenon' ),
        'description'           => __( 'Machen Sie hier Angaben zur Lüftungsanlage des Gebäudes.', 'wpenon' ),
        'fields'                => array(
          'l_info'                => array(
            'type'                  => 'select',
            'label'                 => __( 'Art der Lüftung', 'wpenon' ),
            'options'               => array(
              'fenster'               => __( 'Fensterlüftung', 'wpenon' ),
              'schacht'               => __( 'Schachtlüftung', 'wpenon' ),
              'anlage'                => __( 'Lüftungsanlage', 'wpenon' ),
            ),
            'required'              => true,
          ),
          'l_erzeugung'           => array(
            'type'                  => 'select',
            'label'                 => __( 'Typ der Lüftungsanlage', 'wpenon' ),
            'description'           => __( 'Wählen Sie den Typ der Lüftungsanlage aus.', 'wpenon' ),
            'options'               => wpenon_immoticket24_get_lueftungsanlagen(),
            'required'              => true,
            'display'               => array(
              'callback'              => 'wpenon_show_on_array_whitelist',
              'callback_args'         => array( 'field::l_info', 'anlage' ),
            ),
          ),
          'k_info'                => array(
            'type'                  => 'select',
            'label'                 => __( 'Gebäudekühlung', 'wpenon' ),
            'options'               => array(
              'nicht_vorhanden'       => __( 'nicht vorhanden', 'wpenon' ),
              'vorhanden'             => __( 'vorhanden', 'wpenon' ),
            ),
            'required'              => true,
          ),
          'k_flaeche'             => array(
            'type'                  => 'float',
            'label'                 => __( 'Gekühlte Fläche', 'wpenon' ),
            'description'           => __( 'Geben Sie die gekühlte Wohnfläche in Quadratmetern ein.', 'wpenon' ),
            'required'              => true,
            'unit'                  => 'm&sup2;',
            'display'               => array(
              'callback'              => 'wpenon_show_on_array_whitelist',
              'callback_args'         => array( 'field::k_info', 'vorhanden' ),
            ),
            'validate'              => 'wpenon_immoticket24_validate_area_lower_than',
            'validate_dependencies' => array( 'flaeche' ),
          ),
        ),
      ),
    ),
  ),
  'vw_erfassung'          => array(
    'title'                 => __( 'Verbrauchserfassung', 'wpenon' ),
    'groups'                => array(
      'zeitraum'              => array(
        'title'                 => __( 'Zeitraum', 'wpenon' ),
        'description'           => __( 'Wählen Sie aus, für welchen Zeitraum Sie die Verbrauchsdaten eingeben möchten.', 'wpenon' ),
        'fields'                => array(
          'verbrauch_zeitraum'    => array(
            'type'                  => 'select',
            'label'                 => __( 'Zeitraum der Eingabedaten', 'wpenon' ),
            'description'           => __( 'Die Verbrauchsdaten müssen für drei aufeinanderfolgende Jahre eingegeben werden. Wählen Sie hier den entsprechenden Zeitraum aus.', 'wpenon' ),
            'options'               => wpenon_immoticket24_get_klimafaktoren_zeitraeume(),
            'required'              => true,
          ),
        ),
      ),
      'verbrauchseingabe'     => array(
        'title'                 => __( 'Eingabe der Verbrauchsdaten', 'wpenon' ),
        'fields'                => array(
          'verbrauch1_headline'   => array(
            'type'                  => 'headline',
            'description'           => __( 'Geben Sie die Verbrauchsdaten für das erste Jahr an.', 'wpenon' ),
            'label'                 => array(
              'callback'              => 'wpenon_immoticket24_get_zeitraum_headline',
              'callback_args'         => array( 'field::verbrauch_zeitraum', 0 ),
            ),
          ),
          'verbrauch1_h'          => array(
            'type'                  => 'float',
            'label'                 => __( 'Verbrauch Heizungsanlage', 'wpenon' ),
            'description'           => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
            'required'              => true,
            'unit'                  => array(
              'callback'              => 'wpenon_immoticket24_get_energietraeger_unit',
              'callback_args'         => array( 'field::h_energietraeger' ),
            ),
          ),
          'verbrauch1_h2'         => array(
            'type'                  => 'float',
            'label'                 => __( 'Verbrauch 2. Heizungsanlage', 'wpenon' ),
            'description'           => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
            'required'              => true,
            'unit'                  => array(
              'callback'              => 'wpenon_immoticket24_get_energietraeger_unit',
              'callback_args'         => array( 'field::h2_energietraeger' ),
            ),
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( 'field::h2_info', true ),
            ),
          ),
          'verbrauch1_h3'         => array(
            'type'                  => 'float',
            'label'                 => __( 'Verbrauch 3. Heizungsanlage', 'wpenon' ),
            'description'           => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
            'required'              => true,
            'unit'                  => array(
              'callback'              => 'wpenon_immoticket24_get_energietraeger_unit',
              'callback_args'         => array( 'field::h3_energietraeger' ),
            ),
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
            ),
          ),
          'verbrauch1_ww'         => array(
            'type'                  => 'float',
            'label'                 => __( 'Verbrauch Warmwasseranlage', 'wpenon' ),
            'description'           => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
            'required'              => true,
            'unit'                  => array(
              'callback'              => 'wpenon_immoticket24_get_energietraeger_unit',
              'callback_args'         => array( 'field::ww_energietraeger' ),
            ),
            'display'               => array(
              'callback'              => 'wpenon_show_on_array_whitelist',
              'callback_args'         => array( 'field::ww_info', 'ww' ),
            ),
          ),
          'verbrauch1_leerstand'  => array(
            'type'                  => 'int',
            'label'                 => __( 'Leerstand', 'wpenon' ),
            'description'           => __( 'Geben Sie hier den Leerstand des Gebäudes in Prozent an. Dieser errechnet sich, in dem Sie die leerstehende Fläche pro Jahr stets im Verhältnis zur gesamten Fläche angeben.', 'wpenon' ) . ' ' . __( 'Beispiel: Ein Gebäude hat 2 Wohnungen mit je 50 m² Wohnfläche. Wohnung 1 stand in einem Jahr 3 Monate leer. Berechnung: Pro Jahr stellt das Gebäude somit insgesamt 1200 m² (= 12 Monate * 100 m²) Wohnfläche zur Verfügung, davon waren 150 m² (Wohnung 1 stand 3 Monate leer (= 50 m² * 3)) nicht vermietet. Dies ergäbe einen Leerstand von 12,5 Prozent (= 150 m² / 1200 m²).', 'wpenon' ),
            'default'               => 0,
            'max'                   => 50,
            'unit'                  => '%',
          ),
          'verbrauch2_headline'   => array(
            'type'                  => 'headline',
            'description'           => __( 'Geben Sie die Verbrauchsdaten für das zweite Jahr an.', 'wpenon' ),
            'label'                 => array(
              'callback'              => 'wpenon_immoticket24_get_zeitraum_headline',
              'callback_args'         => array( 'field::verbrauch_zeitraum', 1 ),
            ),
          ),
          'verbrauch2_h'          => array(
            'type'                  => 'float',
            'label'                 => __( 'Verbrauch Heizungsanlage', 'wpenon' ),
            'description'           => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
            'required'              => true,
            'unit'                  => array(
              'callback'              => 'wpenon_immoticket24_get_energietraeger_unit',
              'callback_args'         => array( 'field::h_energietraeger' ),
            ),
          ),
          'verbrauch2_h2'         => array(
            'type'                  => 'float',
            'label'                 => __( 'Verbrauch 2. Heizungsanlage', 'wpenon' ),
            'description'           => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
            'required'              => true,
            'unit'                  => array(
              'callback'              => 'wpenon_immoticket24_get_energietraeger_unit',
              'callback_args'         => array( 'field::h2_energietraeger' ),
            ),
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( 'field::h2_info', true ),
            ),
          ),
          'verbrauch2_h3'         => array(
            'type'                  => 'float',
            'label'                 => __( 'Verbrauch 3. Heizungsanlage', 'wpenon' ),
            'description'           => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
            'required'              => true,
            'unit'                  => array(
              'callback'              => 'wpenon_immoticket24_get_energietraeger_unit',
              'callback_args'         => array( 'field::h3_energietraeger' ),
            ),
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
            ),
          ),
          'verbrauch2_ww'         => array(
            'type'                  => 'float',
            'label'                 => __( 'Verbrauch Warmwasseranlage', 'wpenon' ),
            'description'           => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
            'required'              => true,
            'unit'                  => array(
              'callback'              => 'wpenon_immoticket24_get_energietraeger_unit',
              'callback_args'         => array( 'field::ww_energietraeger' ),
            ),
            'display'               => array(
              'callback'              => 'wpenon_show_on_array_whitelist',
              'callback_args'         => array( 'field::ww_info', 'ww' ),
            ),
          ),
          'verbrauch2_leerstand'  => array(
            'type'                  => 'int',
            'label'                 => __( 'Leerstand', 'wpenon' ),
            'description'           => __( 'Geben Sie hier den Leerstand des Gebäudes in Prozent an. Dieser errechnet sich, in dem Sie die leerstehende Fläche pro Jahr stets im Verhältnis zur gesamten Fläche angeben.', 'wpenon' ) . ' ' . __( 'Beispiel: Ein Gebäude hat 2 Wohnungen mit je 50 m² Wohnfläche. Wohnung 1 stand in einem Jahr 3 Monate leer. Berechnung: Pro Jahr stellt das Gebäude somit insgesamt 1200 m² (= 12 Monate * 100 m²) Wohnfläche zur Verfügung, davon waren 150 m² (Wohnung 1 stand 3 Monate leer (= 50 m² * 3)) nicht vermietet. Dies ergäbe einen Leerstand von 12,5 Prozent (= 150 m² / 1200 m²).', 'wpenon' ),
            'default'               => 0,
            'max'                   => 50,
            'unit'                  => '%',
          ),
          'verbrauch3_headline'   => array(
            'type'                  => 'headline',
            'description'           => __( 'Geben Sie die Verbrauchsdaten für das dritte Jahr an.', 'wpenon' ),
            'label'                 => array(
              'callback'              => 'wpenon_immoticket24_get_zeitraum_headline',
              'callback_args'         => array( 'field::verbrauch_zeitraum', 2 ),
            ),
          ),
          'verbrauch3_h'          => array(
            'type'                  => 'float',
            'label'                 => __( 'Verbrauch Heizungsanlage', 'wpenon' ),
            'description'           => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
            'required'              => true,
            'unit'                  => array(
              'callback'              => 'wpenon_immoticket24_get_energietraeger_unit',
              'callback_args'         => array( 'field::h_energietraeger' ),
            ),
          ),
          'verbrauch3_h2'         => array(
            'type'                  => 'float',
            'label'                 => __( 'Verbrauch 2. Heizungsanlage', 'wpenon' ),
            'description'           => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
            'required'              => true,
            'unit'                  => array(
              'callback'              => 'wpenon_immoticket24_get_energietraeger_unit',
              'callback_args'         => array( 'field::h2_energietraeger' ),
            ),
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( 'field::h2_info', true ),
            ),
          ),
          'verbrauch3_h3'         => array(
            'type'                  => 'float',
            'label'                 => __( 'Verbrauch 3. Heizungsanlage', 'wpenon' ),
            'description'           => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
            'required'              => true,
            'unit'                  => array(
              'callback'              => 'wpenon_immoticket24_get_energietraeger_unit',
              'callback_args'         => array( 'field::h3_energietraeger' ),
            ),
            'display'               => array(
              'callback'              => 'wpenon_show_on_bool_compare',
              'callback_args'         => array( array( 'field::h2_info', 'field::h3_info' ), array( true, true ) ),
            ),
          ),
          'verbrauch3_ww'         => array(
            'type'                  => 'float',
            'label'                 => __( 'Verbrauch Warmwasseranlage', 'wpenon' ),
            'description'           => __( 'Geben Sie die Zahlenwerte ohne Punkt an (zum Beispiel 27134 statt 27.134). Beachten Sie auch, dass Sie bei Mehrfamilienhäusern die Verbrauchswerte des gesamten Gebäudes angeben, da Energieausweise stets gebäudebezogen sind.', 'wpenon' ),
            'required'              => true,
            'unit'                  => array(
              'callback'              => 'wpenon_immoticket24_get_energietraeger_unit',
              'callback_args'         => array( 'field::ww_energietraeger' ),
            ),
            'display'               => array(
              'callback'              => 'wpenon_show_on_array_whitelist',
              'callback_args'         => array( 'field::ww_info', 'ww' ),
            ),
          ),
          'verbrauch3_leerstand'  => array(
            'type'                  => 'int',
            'label'                 => __( 'Leerstand', 'wpenon' ),
            'description'           => __( 'Geben Sie hier den Leerstand des Gebäudes in Prozent an. Dieser errechnet sich, in dem Sie die leerstehende Fläche pro Jahr stets im Verhältnis zur gesamten Fläche angeben.', 'wpenon' ) . ' ' . __( 'Beispiel: Ein Gebäude hat 2 Wohnungen mit je 50 m² Wohnfläche. Wohnung 1 stand in einem Jahr 3 Monate leer. Berechnung: Pro Jahr stellt das Gebäude somit insgesamt 1200 m² (= 12 Monate * 100 m²) Wohnfläche zur Verfügung, davon waren 150 m² (Wohnung 1 stand 3 Monate leer (= 50 m² * 3)) nicht vermietet. Dies ergäbe einen Leerstand von 12,5 Prozent (= 150 m² / 1200 m²).', 'wpenon' ),
            'default'               => 0,
            'max'                   => 50,
            'unit'                  => '%',
          ),
        ),
      ),
    ),
  ),
);
