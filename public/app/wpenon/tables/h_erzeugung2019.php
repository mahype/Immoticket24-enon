<?php

return array(
	'title' => __( 'Heizungsanlagen 2019', 'wpenon' ),
	'description' => __( 'Diese Tabelle enthält die standardisierten Anlagen-Aufwandszahlen (E<sub>P</sub>) und Hilfsenergiewerte für diverse Heizungsanlagen.', 'wpenon' ),
	'asterisks' => array(
		'E<sub>P</sub>' => __( 'Anlagen-Aufwandszahl', 'wpenon' ),
		'HE' => __( 'Hilfsenergiebedarf', 'wpenon' ),
	),
	'primary_field' => 'bezeichnung',
	'search_field' => 'bezeichnung',
	'rebuild_on_import' => true,
	'search_before' => true,
	'fields' => array(
		'bezeichnung' => array(
			'title' => __( 'Bezeichnung (intern)', 'wpenon' ),
			'type' => 'VARCHAR(100)',
		),
		'name' => array(
			'title' => __( 'Anlage', 'wpenon' ),
			'type' => 'VARCHAR(100)',
		),
		'hktemp' => array(
			'title' => __( 'Heizkreistemperatur', 'wpenon' ),
			'type' => 'VARCHAR(20)',
		),
		'typ' => array(
			'title' => __( 'Typ', 'wpenon' ),
			'type' => 'VARCHAR(50)',
		),
		'speicher' => array(
			'title' => __( 'Speicher (intern)', 'wpenon' ),
			'type' => 'VARCHAR(100)',
		),
		'uebergabe' => array(
			'title' => __( 'Übergabe (intern)', 'wpenon' ),
			'type' => 'VARCHAR(100)',
		),
		'ep150_1986' => array(
			'title' => __( 'E<sub>P</sub><sup>1</sup> 150m&sup2; (bis 1986)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'ep500_1986' => array(
			'title' => __( 'E<sub>P</sub><sup>1</sup> 500m&sup2; (bis 1986)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'ep2500_1986' => array(
			'title' => __( 'E<sub>P</sub><sup>1</sup> 2500m&sup2; (bis 1986)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'he150_1986' => array(
			'title' => __( 'HE<sup>2</sup> 150m&sup2; (bis 1986)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'he500_1986' => array(
			'title' => __( 'HE<sup>2</sup> 500m&sup2; (bis 1986)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'he2500_1986' => array(
			'title' => __( 'HE<sup>2</sup> 2500m&sup2; (bis 1986)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'ep150_1994' => array(
			'title' => __( 'E<sub>P</sub><sup>1</sup> 150m&sup2; (bis 1994)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'ep500_1994' => array(
			'title' => __( 'E<sub>P</sub><sup>1</sup> 500m&sup2; (bis 1994)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'ep2500_1994' => array(
			'title' => __( 'E<sub>P</sub><sup>1</sup> 2500m&sup2; (bis 1994)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'he150_1994' => array(
			'title' => __( 'HE<sup>2</sup> 150m&sup2; (bis 1994)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'he500_1994' => array(
			'title' => __( 'HE<sup>2</sup> 500m&sup2; (bis 1994)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'he2500_1994' => array(
			'title' => __( 'HE<sup>2</sup> 2500m&sup2; (bis 1994)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'ep150_1995' => array(
			'title' => __( 'E<sub>P</sub><sup>1</sup> 150m&sup2; (ab 1995)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'ep500_1995' => array(
			'title' => __( 'E<sub>P</sub><sup>1</sup> 500m&sup2; (ab 1995)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'ep2500_1995' => array(
			'title' => __( 'E<sub>P</sub><sup>1</sup> 2500m&sup2; (ab 1995)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'he150_1995' => array(
			'title' => __( 'HE<sup>2</sup> 150m&sup2; (ab 1995)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'he500_1995' => array(
			'title' => __( 'HE<sup>2</sup> 500m&sup2; (ab 1995)', 'wpenon' ),
			'type' => 'FLOAT',
		),
		'he2500_1995' => array(
			'title' => __( 'HE<sup>2</sup> 2500m&sup2; (ab 1995)', 'wpenon' ),
			'type' => 'FLOAT',
		),
	),
);
