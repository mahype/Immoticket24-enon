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
						'modernisierung' => __( 'Modernisierung / Erweiterung', 'wpenon' ),
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
				'gebaeudetyp'          => array(
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
				'gebaeudeteil'         => array(
					'type'        => 'radio',
					'label'       => __( 'Gebäudeteil', 'wpenon' ),
					'description' => __( 'Wählen Sie den Gebäudeteil aus, für den der Energieausweis erstellt wird.', 'wpenon' ),
					'options'     => array(
						'gesamt'   => __( 'Gesamtes Gebäude', 'wpenon' ),
						'gemischt' => __( 'Wohnteil (bei Wohn- und Geschäftshaus)', 'wpenon' ),
					),
					'required'    => true,
				),
				'gebaeudekonstruktion' => array(
					'type'     => 'radio',
					'label'    => __( 'Gebäudekonstruktion', 'wpenon' ),
					'options'  => array(
						'massiv'   => __( 'Massivhaus', 'wpenon' ),
						'holz'     => __( 'Holzhaus', 'wpenon' ),
						'fachwerk' => __( 'Fachwerkhaus', 'wpenon' ),
					),
					'required' => true,
				),
				'wohnungen'            => array(
					'type'        => 'int',
					'label'       => __( 'Wohnungen', 'wpenon' ),
					'description' => __( 'Geben Sie die Anzahl der Wohnungen im Gebäude ein.', 'wpenon' ),
					'default'     => 1,
					'min'         => 1,
					'required'    => true,
				),
				'baujahr'              => array(
					'type'        => 'int',
					'label'       => __( 'Baujahr', 'wpenon' ),
					'description' => __( 'Jahr des Bauantrags', 'wpenon' ),
					'min'         => 1800,
					'max'         => wpenon_get_reference_date( 'Y' ),
					'required'    => true,
					'placeholder' => 'Bitte wählen...'
				),
				'gebauedefoto'              => array(
					'type'                  => 'image',
					'label'                 => __( 'Foto des Gebäudes', 'wpenon' ),
					'text'                  => __( 'Laden Sie hier ein Foto von der aktuellen Außenansicht des kompletten Gebäudes hoch.', 'wpenon' ),
					'required'              => true,
					'filetypes' => array(
						'image/png',
						'image/jpeg'
					),
					'validate' => 'wpenon_immoticket24_validate_house_image_upload'
				),
			),
		),
	),
);
