<?php

$calculations = array();

$calculations['nutzflaeche_mpk'] = 1.2;
if ( $energieausweis->wohnungen <= 2 && $energieausweis->keller == 'beheizt' ) {
	$calculations['nutzflaeche_mpk'] = 1.35;
}

$calculations['nutzflaeche'] = $energieausweis->flaeche * $calculations['nutzflaeche_mpk'];

$calculations['reference'] = 125;

$klimafaktoren       = wpenon_get_table_results( 'klimafaktoren202001', array(
	'bezeichnung' => array(
		'value'   => $energieausweis->adresse_plz,
		'compare' => '>='
	)
), array(), true );
$klimafaktoren_datum = $energieausweis->verbrauch_zeitraum;

/*************************************************
 * ANLAGENDATEN
 *************************************************/

$calculations['anlagendaten'] = array();

$energietraeger_list = array();
$primaer_list        = array();

// Migrate old data to new Energietraeger table data.
$energietraeger_fields = array( 'h_energietraeger', 'h2_energietraeger', 'h3_energietraeger', 'ww_energietraeger' );

foreach ( $energietraeger_fields as $energietraeger_field ) {
	if ( false !== strpos( $energieausweis->$energietraeger_field, '_kwhheizwert' ) ) {
		$energieausweis->$energietraeger_field = str_replace( '_kwhheizwert', '_kwh', $energieausweis->$energietraeger_field );
	} elseif ( false !== strpos( $energieausweis->$energietraeger_field, '_kwhbrennwert' ) ) {
		$energieausweis->$energietraeger_field = str_replace( '_kwhbrennwert', '_kwh', $energieausweis->$energietraeger_field );
	}
}

$h_energietraeger_name = 'h_energietraeger_' . $energieausweis->h_erzeugung;
$h_energietraeger_value = $energieausweis->$h_energietraeger_name;

$h_energietraeger_umrechnungen     = wpenon_get_table_results( 'energietraeger_umrechnungen', array(
	'bezeichnung' => array(
		'value'   => $h_energietraeger_value,
		'compare' => '='
	)
), array(), true );


$h_energietraeger                  = wpenon_get_table_results( 'energietraeger202001', array(
	'bezeichnung' => array(
		'value'   => $h_energietraeger_umrechnungen->energietraeger,
		'compare' => '='
	)
), array(), true );
$h_anlagentyp                      = wpenon_get_table_results( 'h_erzeugung2019', array(
	'bezeichnung' => array(
		'value'   => $energieausweis->h_erzeugung,
		'compare' => '='
	)
), array( 'name' ), true );

$energietraeger_list[]             = $h_energietraeger->name;
$primaer_list[]                    = $h_energietraeger->primaer;
$calculations['anlagendaten']['h'] = array(
	'name'                   => $h_anlagentyp,
	'slug'                   => $energieausweis->h_erzeugung,
	'art'                    => 'heizung',
	'energietraeger'         => $h_energietraeger->name,
	'energietraeger_slug'    => $h_energietraeger->bezeichnung,
	'energietraeger_einheit' => $h_energietraeger_umrechnungen->einheit,
	'energietraeger_mpk'     => floatval( $h_energietraeger_umrechnungen->mpk ),
	'energietraeger_primaer' => $energieausweis->h_custom ? floatval( $energieausweis->h_custom_primaer ) : floatval( $h_energietraeger->primaer ),
	'verbrauch'              => array(),
);
if ( $energieausweis->h2_info ) {
	$h2_energietraeger_name = 'h2_energietraeger_' . $energieausweis->h2_erzeugung;
	$h2_energietraeger_value = $energieausweis->$h2_energietraeger_name;

	$h2_energietraeger_umrechnungen     = wpenon_get_table_results( 'energietraeger_umrechnungen', array(
		'bezeichnung' => array(
			'value'   => $h2_energietraeger_value,
			'compare' => '='
		)
	), array(), true );
	$h2_energietraeger                  = wpenon_get_table_results( 'energietraeger202001', array(
		'bezeichnung' => array(
			'value'   => $h2_energietraeger_umrechnungen->energietraeger,
			'compare' => '='
		)
	), array(), true );
	$h2_anlagentyp                      = wpenon_get_table_results( 'h_erzeugung2019', array(
		'bezeichnung' => array(
			'value'   => $energieausweis->h2_erzeugung,
			'compare' => '='
		)
	), array( 'name' ), true );
	$energietraeger_list[]              = $h2_energietraeger->name;
	$primaer_list[]                     = $h2_energietraeger->primaer;
	$calculations['anlagendaten']['h2'] = array(
		'name'                   => $h2_anlagentyp,
		'slug'                   => $energieausweis->h2_erzeugung,
		'art'                    => 'heizung',
		'energietraeger'         => $h2_energietraeger->name,
		'energietraeger_slug'    => $h2_energietraeger->bezeichnung,
		'energietraeger_einheit' => $h2_energietraeger_umrechnungen->einheit,
		'energietraeger_mpk'     => floatval( $h2_energietraeger_umrechnungen->mpk ),
		'energietraeger_primaer' => $energieausweis->h2_custom ? floatval( $energieausweis->h2_custom_primaer ) : floatval( $h2_energietraeger->primaer ),
		'verbrauch'              => array(),
	);

	if ( $energieausweis->h3_info ) {
		$h3_energietraeger_name = 'h3_energietraeger_' . $energieausweis->h3_erzeugung;
		$h3_energietraeger_value = $energieausweis->$h3_energietraeger_name;

		$h3_energietraeger_umrechnungen     = wpenon_get_table_results( 'energietraeger_umrechnungen', array(
			'bezeichnung' => array(
				'value'   => $h3_energietraeger_value,
				'compare' => '='
			)
		), array(), true );
		$h3_energietraeger                  = wpenon_get_table_results( 'energietraeger202001', array(
			'bezeichnung' => array(
				'value'   => $h3_energietraeger_umrechnungen->energietraeger,
				'compare' => '='
			)
		), array(), true );
		$h3_anlagentyp                      = wpenon_get_table_results( 'h_erzeugung2019', array(
			'bezeichnung' => array(
				'value'   => $energieausweis->h3_erzeugung,
				'compare' => '='
			)
		), array( 'name' ), true );
		$energietraeger_list[]              = $h3_energietraeger->name;
		$primaer_list[]                     = $h3_energietraeger->primaer;
		$calculations['anlagendaten']['h3'] = array(
			'name'                   => $h3_anlagentyp,
			'slug'                   => $energieausweis->h3_erzeugung,
			'art'                    => 'heizung',
			'energietraeger'         => $h3_energietraeger->name,
			'energietraeger_slug'    => $h3_energietraeger->bezeichnung,
			'energietraeger_einheit' => $h3_energietraeger_umrechnungen->einheit,
			'energietraeger_mpk'     => floatval( $h3_energietraeger_umrechnungen->mpk ),
			'energietraeger_primaer' => $energieausweis->h3_custom ? floatval( $energieausweis->h3_custom_primaer ) : floatval( $h3_energietraeger->primaer ),
			'verbrauch'              => array(),
		);
	}
}

if ( $energieausweis->ww_info == 'ww' ) {
	$ww_energietraeger_name = 'ww_energietraeger_' . $energieausweis->ww_erzeugung;
	$ww_energietraeger_value = $energieausweis->$ww_energietraeger_name;

	$ww_energietraeger_umrechnungen     = wpenon_get_table_results( 'energietraeger_umrechnungen', array(
		'bezeichnung' => array(
			'value'   => $ww_energietraeger_value,
			'compare' => '='
		)
	), array(), true );
	$ww_energietraeger                  = wpenon_get_table_results( 'energietraeger202001', array(
		'bezeichnung' => array(
			'value'   => $ww_energietraeger_umrechnungen->energietraeger,
			'compare' => '='
		)
	), array(), true );
	$ww_anlagentyp                      = wpenon_get_table_results( 'ww_erzeugung202001', array(
		'bezeichnung' => array(
			'value'   => $energieausweis->ww_erzeugung,
			'compare' => '='
		)
	), array( 'name' ), true );
	$energietraeger_list[]              = $ww_energietraeger->name;
	$primaer_list[]                     = $ww_energietraeger->primaer;
	$calculations['anlagendaten']['ww'] = array(
		'name'                   => $ww_anlagentyp,
		'slug'                   => $energieausweis->ww_erzeugung,
		'art'                    => 'warmwasser',
		'energietraeger'         => $ww_energietraeger->name,
		'energietraeger_slug'    => $ww_energietraeger->bezeichnung,
		'energietraeger_einheit' => $ww_energietraeger_umrechnungen->einheit,
		'energietraeger_mpk'     => floatval( $ww_energietraeger_umrechnungen->mpk ),
		'energietraeger_primaer' => floatval( $ww_energietraeger->primaer ),
		'verbrauch'              => array(),
	);
}

/*************************************************
 * VERBRAUCHSDATEN
 *************************************************/

$energietraeger_list = array_unique( $energietraeger_list );
$primaer_list        = array_unique( $primaer_list );

$calculations['verbrauchsdaten'] = array();

$calculations['qh_e_b']         = 0.0;
$calculations['qw_e_b']         = 0.0;
$calculations['endenergie']     = 0.0;
$calculations['primaerenergie'] = 0.0;

for ( $i = 0; $i < 3; $i ++ ) {
	$count         = $i + 1;
	$faktor_key    = wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $klimafaktoren_datum, $i, false, 'slug' );
	$leerstand_key = 'verbrauch' . $count . '_leerstand';

	$startdatum       = wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $klimafaktoren_datum, $i, false, 'data' );
	$enddatum         = wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $klimafaktoren_datum, $i, true, 'data' );
	$klimafaktor      = floatval( $klimafaktoren->$faktor_key );
	$leerstandsfaktor = 1.0 - $energieausweis->$leerstand_key / 100.0;

	$heizungsverbrauch     = 0.0;
	$heizungsverbrauch_b   = 0.0;
	$warmwasserverbrauch   = 0.0;
	$warmwasserverbrauch_b = 0.0;

	foreach ( $calculations['anlagendaten'] as $key => &$data ) {
		$verbrauch_key = 'verbrauch' . $count . '_' . $key;
		$h_verbrauch   = $ww_verbrauch = $h_verbrauch_b = $ww_verbrauch_b = 0.0;
		if ( $key == 'ww' ) {
			$ww_verbrauch = $energieausweis->$verbrauch_key * $data['energietraeger_mpk'];
		} else {
			$h_verbrauch = $energieausweis->$verbrauch_key * $data['energietraeger_mpk'];
			$ww_verbrauch = $h_verbrauch * 0.18;
			$h_verbrauch  -= $ww_verbrauch;
		}

		if ( $h_verbrauch > 0.0 ) {
			$h_verbrauch_b = ( $h_verbrauch * $klimafaktor ) / ( $calculations['nutzflaeche'] * $leerstandsfaktor );
		}
		if ( $ww_verbrauch > 0.0 ) {
			$ww_verbrauch_b = $ww_verbrauch / ( $calculations['nutzflaeche'] * $leerstandsfaktor );
		}

		$temp = array(
			'start'        => $startdatum,
			'ende'         => $enddatum,
			'klima'        => $klimafaktor,
			'heizung'      => $h_verbrauch,
			'heizung_b'    => $h_verbrauch_b,
			'warmwasser'   => $ww_verbrauch,
			'warmwasser_b' => $ww_verbrauch_b,
		);

		$calculations['qh_e_b']         += $temp['heizung_b'];
		$calculations['qw_e_b']         += $temp['warmwasser_b'];
		$calculations['endenergie']     += $temp['heizung_b'] + $temp['warmwasser_b'];
		$calculations['primaerenergie'] += ( $temp['heizung_b'] + $temp['warmwasser_b'] ) * $data['energietraeger_primaer'];

		$heizungsverbrauch     += $temp['heizung'];
		$heizungsverbrauch_b   += $temp['heizung_b'];
		$warmwasserverbrauch   += $temp['warmwasser'];
		$warmwasserverbrauch_b += $temp['warmwasser_b'];

		$data['verbrauch'][] = $temp;
	}
	unset( $data );
	unset( $key );

	$daten = array(
		'start'          => $startdatum,
		'ende'           => $enddatum,
		'energietraeger' => implode( ' / ', $energietraeger_list ),
		'primaer'        => implode( ' / ', $primaer_list ),
		'klima'          => $klimafaktor,
		'heizung'        => $heizungsverbrauch,
		'heizung_b'      => $heizungsverbrauch_b,
		'warmwasser'     => $warmwasserverbrauch,
		'warmwasser_b'   => $warmwasserverbrauch_b,
		'gesamt'         => $heizungsverbrauch + $warmwasserverbrauch,
		'gesamt_b'       => $heizungsverbrauch_b + $warmwasserverbrauch_b,
	);

	if ( strpos( $daten['primaer'], '/' ) === false ) {
		$daten['primaer'] = floatval( $daten['primaer'] );
	}

	$calculations['verbrauchsdaten'][] = $daten;
}

$post = get_post( $energieausweis->id );
if ( $post && ! empty( $post->post_date_gmt ) && '0000-00-00 00:00:00' !== $post->post_date_gmt ) {
	$energieausweis_timestamp = mysql2date( 'G', $post->post_date_gmt, false );
} else {
	$energieausweis_timestamp = current_time( 'timestamp', true );
}
$should_calculations_be_fixed = $energieausweis_timestamp > 1536326720; // 09/07/2018 @ 1:25pm (UTC)

if ( $energieausweis->ww_info === 'unbekannt' ) {
	$calculations['warmwasser_zuschlag'] = 20.0 * $calculations['nutzflaeche'] * 3;
	if ( $should_calculations_be_fixed ) {
		$calculations['warmwasser_zuschlag_b'] = $calculations['warmwasser_zuschlag'] / $calculations['nutzflaeche'];
	} else {
		$calculations['warmwasser_zuschlag_b'] = $calculations['warmwasser_zuschlag'] / ( $calculations['nutzflaeche'] * 3 );
	}

	$calculations['verbrauchsdaten'][] = array(
		'start'          => $calculations['verbrauchsdaten'][0]['start'],
		'ende'           => $calculations['verbrauchsdaten'][2]['ende'],
		'energietraeger' => 'Warmwasserzuschlag',
		'primaer'        => $calculations['anlagendaten']['h']['energietraeger_primaer'],
		'klima'          => '',
		'heizung'        => 0.0,
		'heizung_b'      => 0.0,
		'warmwasser'     => $calculations['warmwasser_zuschlag'],
		'warmwasser_b'   => $calculations['warmwasser_zuschlag_b'],
		'gesamt'         => $calculations['warmwasser_zuschlag'],
		'gesamt_b'       => $calculations['warmwasser_zuschlag_b'],
	);

	$calculations['qw_e_b']         += $calculations['warmwasser_zuschlag_b'];
	$calculations['endenergie']     += $calculations['warmwasser_zuschlag_b'];
	$calculations['primaerenergie'] += $calculations['warmwasser_zuschlag_b'] * $calculations['anlagendaten']['h']['energietraeger_primaer'];
}

if ( $energieausweis->k_info == 'vorhanden' ) {
	$kuehlung_energietraeger = wpenon_get_table_results( 'energietraeger202001', array(
		'bezeichnung' => array(
			'value'   => 'strom',
			'compare' => '='
		)
	), array(), true );
	$kuehlung_flaeche        = $energieausweis->k_flaeche ? floatval( $energieausweis->k_flaeche ) * $calculations['nutzflaeche_mpk'] : $calculations['nutzflaeche'];

	$calculations['kuehlung_zuschlag'] = 6.0 * $kuehlung_flaeche * 3;
	if ( $should_calculations_be_fixed ) {
		$calculations['kuehlung_zuschlag_b'] = $calculations['kuehlung_zuschlag'] / $calculations['nutzflaeche'];
	} else {
		$calculations['kuehlung_zuschlag_b'] = $calculations['kuehlung_zuschlag'] / ( $calculations['nutzflaeche'] * 3 );
	}

	$calculations['verbrauchsdaten'][] = array(
		'start'          => $calculations['verbrauchsdaten'][0]['start'],
		'ende'           => $calculations['verbrauchsdaten'][2]['ende'],
		'energietraeger' => 'Kühlungszuschlag',
		'primaer'        => floatval( $kuehlung_energietraeger->primaer ),
		'klima'          => '',
		'heizung'        => 0.0,
		'heizung_b'      => 0.0,
		'warmwasser'     => 0.0,
		'warmwasser_b'   => 0.0,
		'gesamt'         => $calculations['kuehlung_zuschlag'],
		'gesamt_b'       => $calculations['kuehlung_zuschlag_b'],
	);

	$calculations['endenergie']     += $calculations['kuehlung_zuschlag_b'];
	$calculations['primaerenergie'] += $calculations['kuehlung_zuschlag_b'] * floatval( $kuehlung_energietraeger->primaer );
}

$calculations['qh_e_b']         /= 3.0;
$calculations['qw_e_b']         /= 3.0;
$calculations['endenergie']     /= 3.0;
$calculations['primaerenergie'] /= 3.0;

return $calculations;
