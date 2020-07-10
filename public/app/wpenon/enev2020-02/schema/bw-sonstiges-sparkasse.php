<?php

$qualities = [
	'mauerwerk' => [
		[
			'title'       => 'Einfach',
			'description' => 'z.B. dünnes Mauerwerk, ungedämmt',
		],
		[
			'title'       => 'Durchschnittlich',
			'description' => 'z.B. mittleres Mauerwerk, ungedämmt',
		],
		[
			'title'       => 'Überdurchschnittlich',
			'description' => 'z.B. starkes Mauerwerk, gedämmt',
		],
		[
			'title'       => 'Aufwändig',
			'description' => 'z.B. starkes bis sehr starkes Mauerwerk mit neuwertiger Dämmung',
		]
	],
	'dach'      => [
		[
			'title'       => 'Einfach',
			'description' => 'z.B. Dach ohne Wärmedämmung',
		],
		[
			'title'       => 'Durchschnittlich',
			'description' => 'z.B. Dach mit mittlerer Wärmedämmung',
		],
		[
			'title'       => 'Überdurchschnittlich',
			'description' => 'z.B. Dach mit hoher Wärmedämmung',
		],
		[
			'title'       => 'Aufwändig',
			'description' => 'z.B. Dachausschnitte mit Glas, aufwendige Dachausbauten',
		]
	],
	'daemmung'  => [
		[
			'title'       => 'Einfach',
			'description' => 'z.B. einfacher Wärmedämmungsstandard',
		],
		[
			'title'       => 'Durchschnittlich',
			'description' => 'z.B. mittlerer Wärmedämmungsstandard',
		],
		[
			'title'       => 'Überdurchschnittlich',
			'description' => 'z.B. hoher Wärmestandard',
		],
		[
			'title'       => 'Aufwändig',
			'description' => 'z.B. sehr hoher Wärmestandard, Passivhaus',
		]
	],
	'fenster'   => [
		[
			'title'       => 'Einfach',
			'description' => 'z.B. Einfachverglasung',
		],
		[
			'title'       => 'Durchschnittlich',
			'description' => 'z.B. Kunststofffenster, Isolierverglasung, Rollläden',
		],
		[
			'title'       => 'Überdurchschnittlich',
			'description' => 'z.B. Aluminiumfenster, Wärmeschutzverglasung',
		],
		[
			'title'       => 'Aufwändig',
			'description' => 'z.B. elektrische Rollläden, Schallschutzverglasung',
		]
	],
	'boden'     => [
		[
			'title'       => 'Einfach',
			'description' => 'z.B. PVC-Böden (niedriger Standard)',
		],
		[
			'title'       => 'Durchschnittlich',
			'description' => 'z.B. Teppich, PVC Boden (mittlerer Standard), Fliesen',
		],
		[
			'title'       => 'Überdurchschnittlich',
			'description' => 'z.B. Fliesenboden, Parkett',
		],
		[
			'title'       => 'Aufwändig',
			'description' => 'z.B. Natursteinböden',
		]
	],
	'heizung'   => [
		[
			'title'       => 'Einfach',
			'description' => 'z. B. Speicherheizung, Einzelöfen',
		],
		[
			'title'       => 'Durchschnittlich',
			'description' => 'z.B. Zentralheizung',
		],
		[
			'title'       => 'Überdurchschnittlich',
			'description' => 'z.B. Zentralheizung',
		],
		[
			'title'       => 'Aufwändig',
			'description' => 'z.B. Fußbodenheizung, Klimaanlage, Solaranlage',
		]
	],
	'baeder'    => [
		[
			'title'       => 'Einfach',
			'description' => 'z.B. Bäder ohne oder nur mit geringer Verfliesung',
		],
		[
			'title'       => 'Durchschnittlich',
			'description' => 'z.B. Bad mit Dusche und Badewanne',
		],
		[
			'title'       => 'Überdurchschnittlich',
			'description' => 'z.B. zwei Bäder, Gäste-WC',
		],
		[
			'title'       => 'Aufwändig',
			'description' => 'z.B. mehrere Bäder mit Bidet, Whirlpool',
		]
	],
	'generell'    => [
		[
			'title'       => 'Einfach',
			'description' => 'z.B. Einfachverglasung, Dach ohne Wärmedämmung, PVC-Boden (niedriger Standard), Speicherheizung, Einzelöfen, Bäder ohne oder nur mit geringer Verfliesung.',
		],
		[
			'title'       => 'Durchschnittlich',
			'description' => 'z.B. Kunststoffenster, Isolierverglasung, Rolläden, mittlerer Wärmedämmungsstandard, Bad mit Dusche und Badewanne, Teppich, PVC Kunststofftüren, Zentralheizung.',
		],
		[
			'title'       => 'Überdurchschnittlich',
			'description' => 'z.B. Aluminiumfenster, Wärmeschutzverglasung, hoher Wärmedämmstandard, zwei Bäder, Gäste WC, Fliesenboden, Parkett, Glastüren, Zentralheizung.',
		],
		[
			'title'       => 'Aufwändig',
			'description' => 'z.B. wandhohe Verfliesung, raumhohe Verglasung, elektrische Rolläden, Schallschutzverglasung, Dachausschnitte mit Glas, Oberlichter, aufwendige Dachausbauten, Natursteinböden, mehrere Bäder mit Bidet, Whirlpool, Einbruchschutz, massive Türen, Fußbodenheizung, Klimaanlage, Solaranlage.',
		]
	]
];

if ( ! function_exists( 'get_quality_description' ) ) {
	function get_quality_description( $elements ) {
		$html = '';

		foreach ( $elements as $element ) {
			$html .= sprintf( "<b>%s</b><br />%s<br /><br />", $element['title'], $element['description'] );
		}

		return $html;
	}
}

$sonstiges = array(
	'title'  => __( 'Sonstiges', 'wpenon' ),
	'groups' => array(
		'notice'                => array(
			'title' => __( 'Hinweis', 'wpenon' ),
		),
		'modernisierung'        => array(
			'title'  => __( 'Modernisierungen', 'wpenon' ),
			'fields' => array(
				'modernisierung_baeder'            => array(
					'type'     => 'select',
					'label'    => __( 'Modernisierung von Bädern', 'wpenon' ),
					'options'  => array(
						'nein'  => __( 'Keine Modernisierung', 'wpenon' ),
						'0-5'   => __( 'Vor 0-5 Jahren', 'wpenon' ),
						'6-10'  => __( 'Vor 6-10 Jahren', 'wpenon' ),
						'11-15' => __( 'Vor 11-15 Jahren', 'wpenon' ),
						'16-25' => __( 'Vor 16- 25 Jahren', 'wpenon' ),
						'25'    => __( 'Vor über 25 Jahren', 'wpenon' ),
					),
					'required' => false,
				),
				'modernisierung_innenausbau'       => array(
					'type'     => 'select',
					'label'    => __( 'Modernisierung des Innenausbaus (z.B. Decken, Fußböden, Treppen)', 'wpenon' ),
					'options'  => array(
						'nein'  => __( 'Keine Modernisierung', 'wpenon' ),
						'0-5'   => __( 'Vor 0-5 Jahren', 'wpenon' ),
						'6-10'  => __( 'Vor 6-10 Jahren', 'wpenon' ),
						'11-15' => __( 'Vor 11-15 Jahren', 'wpenon' ),
						'16-25' => __( 'Vor 16- 25 Jahren', 'wpenon' ),
						'25'    => __( 'Vor über 25 Jahren', 'wpenon' ),
					),
					'required' => false,
				),
				'verbesserung_grundrissgestaltung' => array(
					'type'        => 'select',
					'label'       => __( 'Wesentliche Verbesserung der Grundrissgestaltung', 'wpenon' ),
					'description' => __( 'Wurden die Bäder modernisiert und wenn ja vor wie viel Jahren?', 'wpenon' ),
					'options'     => array(
						'nein'  => __( 'Keine Verbesserung', 'wpenon' ),
						'0-5'   => __( 'Vor 0-5 Jahren', 'wpenon' ),
						'6-10'  => __( 'Vor 6-10 Jahren', 'wpenon' ),
						'11-15' => __( 'Vor 11-15 Jahren', 'wpenon' ),
						'16-25' => __( 'Vor 16- 25 Jahren', 'wpenon' ),
						'25'    => __( 'Vor über 25 Jahren', 'wpenon' ),
					),
					'required'    => false,
				),
			),
		),
		'qualitaet_ausstattung' => array(
			'title'  => __( 'Qualität und Ausstattung', 'wpenon' ),
			'fields' => array(
				'qualitaet_generell'        => array(
					'type'        => 'select',
					'label'       => __( 'Wie ist die generelle Qualität und Ausstattung Ihrer Immobilie?', 'wpenon' ),
					'options'     => array(
						'einfach'               => __( 'Einfach', 'wpenon' ),
						'durchschnittlich'      => __( 'Durchschnittlich', 'wpenon' ),
						'ueberdurchschnittlich' => __( 'Überdurchschnittlich', 'wpenon' ),
						'aufwaendig_luxus'      => __( 'Aufwändig/Luxus', 'wpenon' ),
					),
					'required'    => false,
					'description' => get_quality_description( $qualities['generell'] ),
				),
			),
		),
		'grundstück'            => array(
			'title'  => __( 'Grundstück und Lage', 'wpenon' ),
			'fields' => array(
				'grundstuecksflaeche' => array(
					'type'    => 'int',
					'label'   => __( 'Grundstücksfläche', 'wpenon' ),
					'default' => 0,
					'unit'    => 'm²',
				),
			),
		),
	),
);
