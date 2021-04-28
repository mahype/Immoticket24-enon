<?php

$basisdaten = array(
	'title'  => __( 'Basisdaten', 'wpenon' ),
	'groups' => array(
		'energieausweis' => array(
			'title'       => __( 'Allgemein', 'wpenon' ),
			'description' => __( 'Wählen Sie hier die passenden Angaben für Ihren Energieausweis aus.', 'wpenon' ),
			'fields'      => array(
				'anlass' => array(
					'type'        => 'select',
					'label'       => __( 'Anlass', 'wpenon' ),
					'description' => __( 'Wählen Sie aus, für welchen Zweck dieser Energieausweis verwendet werden soll.', 'wpenon' ),
					'options'     => array(
						'vermietung'     => __( 'Vermietung', 'wpenon' ),
						'verkauf'        => __( 'Verkauf', 'wpenon' ),
						'sonstiges'      => __( 'Sonstiges', 'wpenon' ),
					),
					'required'    => true,
                ),
			),
        ),
		'gebaeude'       => array(
			'title'       => __( 'Gebäudeinformationen', 'wpenon' ),
			'description' => __( 'Machen Sie hier grundsätzliche Angaben zum Gebäude.', 'wpenon' ),
			'fields'      => array(
				'gebaeudetyp'  => array(
					'type'             => 'select',
					'label'            => __( 'Gebäudetyp', 'wpenon' ),
					'description'      => __( 'Wählen Sie den passenden Typ für das Gebäude aus.', 'wpenon' ),
					'options'          => array(
						'freistehend'       => __( 'freistehendes Haus', 'wpenon' ),
						'reihenhaus'        => __( 'Reihenmittelhaus', 'wpenon' ),
						'reiheneckhaus'     => __( 'Reiheneckhaus', 'wpenon' ),
						'doppelhaushaelfte' => __( 'Doppelhaushälfte', 'wpenon' ),
						'sonstiges'         => __( 'sonstiges Wohngebäude', 'wpenon' ),
					),
					'disabled_options' => array(
						'wohnung' => __( 'Wohnung (gemäß EnEV nicht möglich)', 'wpenon' ),
					),
					'required'         => true,
				),
				'gebaeudeteil' => array(
					'type'        => 'radio',
					'label'       => __( 'Gebäudeteil', 'wpenon' ),
					'description' => __( 'Wählen Sie den Gebäudeteil aus, für den der Energieausweis erstellt wird.', 'wpenon' ),
					'options'     => array(
						'gesamt'   => __( 'Gesamtes Gebäude', 'wpenon' ),
						'gemischt' => __( 'Wohnteil (bei Wohn- und Geschäftshaus)', 'wpenon' ),
					),
					'required'    => true,
				),
				'wohnungen'    => array(
					'type'        => 'int',
					'label'       => __( 'Wohnungen', 'wpenon' ),
					'description' => __( 'Geben Sie die Anzahl der Wohnungen im Gebäude ein.', 'wpenon' ),
					'default'     => 1,
					'min'         => 1,
					'required'    => true,
				),
				'baujahr'      => array(
					'type'        => 'int',
					'label'       => __( 'Baujahr', 'wpenon' ),
					'description' => __( 'Geben Sie das Baujahr des Gebäudes an.', 'wpenon' ),
					'min'         => 1800,
					'max'         => wpenon_get_reference_date( 'Y' ),
					'required'    => true,
				),
				'flaeche'      => array(
					'type'        => 'float',
					'label'       => __( 'Wohnfläche', 'wpenon' ),
					'description' => __( 'Da Energieausweise stets gebäudebezogen sind, geben Sie hier die Wohnfläche des gesamten Gebäudes (ohne Keller) in Quadratmetern an. Hieraus wird im Energieausweis automatisch die Gebäudenutzfläche berechnet.', 'wpenon' ),
					'required'    => true,
					'unit'        => 'm&sup2;',
				),
			),
		),
		'regenerativ'    => array(
			'title'       => __( 'Erneuerbare Energien', 'wpenon' ),
			'description' => __( 'Falls Ihr Gebäude zum Teil erneuerbare Energien verwendet, machen Sie hier entsprechende Angaben.', 'wpenon' ),
			'fields'      => array(
				'regenerativ_art'           => array(
					'type'        => 'select',
					'label'       => __( 'Art der erneuerbaren Energien', 'wpenon' ),
					'options'     => array(
						'keine' => 'Keine thermische Solaranlage',
						'solar' => 'Solargestützte Warmwasser-/Heizungsunterstützung',
					),
					'required'    => true,
				),
				'regenerativ_nutzung' => array(
					'type'        => 'select',
					'label'       => __( 'Verwendung der erneuerbaren Energien', 'wpenon' ),
					'options'     => array(
						'warmwasser'                 => 'Warmwasser',
						'warmwasser_waermeerzeugung' => 'Warmwasser und Wärmeerzeugung',
					),
					'display'     => array(
						'callback'      => 'wpenon_show_on_array_whitelist',
						'callback_args' => array( 'field::regenerativ_art', 'solar' ),
					),
					'required'    => true,
				),
			),
		),
	),
);
