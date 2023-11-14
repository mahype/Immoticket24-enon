<?php
//bei Neubauten siehe Kommentare BUndesanzeiger  GeG Anlage 5 (Jahn Kopie vom 12.09.2023), 
// Bei Bestandsgebäuden keine Einschränkung mit Ausnahme QWarmwasser (Tab. Faktoreen je m² Wohnfläche)??????
//Kühlung wird im Quellcode nicht rechnerisch berücksichtig. Nur Abfragen kleiner 12 kW( dann uninteressant)  größer 12kW (vermerk im Ausweis), dass Wartung notwendig ist. Über vereinfachtes Verfahren jedoch a 01.01.24 nicht mehr rechenbar nicht berechenbar
// Daher Hinweis an den KUnden, dass bei anklicken von Kühlung "Es meldet sich unser Energiebrater" 

enon
sqrt
require_once ( 'lib/Extension.php' );
require_once ( 'lib/Extension_Form_A.php' );
require_once ( 'lib/Extension_Form_B.php' );

$tableNames = new stdClass();

$tableNames->h_erzeugung                 = 'h_erzeugung2019';
$tableNames->h_uebergabe                 = 'h_uebergabe';
$tableNames->ww_erzeugung                = 'ww_erzeugung202001';
$tableNames->energietraeger              = 'energietraeger2021';
$tableNames->energietraeger_umrechnungen = 'energietraeger_umrechnungen';
$tableNames->uwerte                      = 'uwerte2021';
$tableNames->l_erzeugung                 = 'l_erzeugung2021';
$tableNames->l_verteilung                = 'l_verteilung2021';

$tableNames->klimafaktoren               = 'klimafaktoren202301';

$calculations = array();
$calculations['reference'] = 125;

/*************************************************
 * BAUTEILE BERECHNUNG
 *************************************************/

$calculations['bauteile'] = array();
$calculations['volumenteile'] = array();

//Definition Himmelsrichtung; Wo kommen die %8 her? und warum wird mit einer Schleife gearbeitet/
//Wichtig für mein Verständnis für was steht $Energieausweis

$himmelsrichtungen = array_keys( wpenon_immoticket24_get_himmelsrichtungen() );
$hr_nullrichtung = array_search( $energieausweis->grundriss_richtung, $himmelsrichtungen );
$hr_mappings = array();
for ( $i = 0; $i < 4; $i++ ) {
  $hr_mappings[] = $himmelsrichtungen[ ( $hr_nullrichtung + 2 * $i ) % 8 ];
}

//Vor Auslesen der Längen, werden die Längenvariablen alle auf Null gesetzt

$wand_a_laenge = $wand_b_laenge = $wand_c_laenge = $wand_d_laenge = $wand_e_laenge = $wand_f_laenge = $wand_g_laenge = $wand_h_laenge = 0.0;


// Ab hier werden scheinbar die alle Wandlängen addiert (a+b+c+d+e etc. $to_calculate[$wand]=$data ; $data sollte dann die Gesamtwandlänge enhtalten?!


$grundriss_formen = wpenon_immoticket24_get_grundriss_formen();
$grundriss_form = $grundriss_formen['a'];
if ( isset( $grundriss_formen[ $energieausweis->grundriss_form ] ) ) {
  $grundriss_form = $grundriss_formen[ $energieausweis->grundriss_form ];
}
$flaechenberechnungsformel = $grundriss_form['fla'];
unset( $grundriss_form['fla'] );

$to_calculate = array();
foreach ( $grundriss_form as $wand => $data ) {
  if ( $data[0] === true ) {
    $l_slug = 'wand_' . $wand . '_laenge';
    $$l_slug = $energieausweis->$l_slug;
  } else {
    $to_calculate[ $wand ] = $data;
  }
}
unset( $data );  //warum löscht er hier den Variableninhalt????

foreach ( $to_calculate as $wand => $data ) {
  $laenge = 0.0;
  $current_operator = '+';
  $formel = explode( ' ', $data[0] );
  foreach ( $formel as $t ) {
    switch ( $t ) {
      case '+':
      case '-':
        $current_operator = $t;
        break;
      default:
        $l_slug = 'wand_' . $t . '_laenge';
        switch ( $current_operator ) {
          case '+':
            $laenge += $$l_slug;
            break;
          case '-':
            $laenge -= $$l_slug;
            break;
          default:
        }
    }
  }
  if ( $laenge > 0.0 ) {
    $l_slug = 'wand_' . $wand . '_laenge';
    $$l_slug = $laenge;
  }
}
unset( $data );
unset( $to_calculate );

//ab hier weden die Geschosshöhen ermittel (hierbei 25cm Deckendicke berücksichtigt) mit der Anzahl der Geschosse mal genommen und damit die Gebäudegesamthöhe ermittelt

$geschosshoehe = $energieausweis->geschoss_hoehe + 0.25;
//die Wandhöhe des geamten Gebäudes; nach der folgenden foreach_Schleife wird unten diese Gesamthöhe mal der Grundläche genommen. Ergebnis: Innenvolumen (inkl. Außenwandvolumen) ohne Dachgeschossvolumen
$wandhoehe = $energieausweis->geschoss_zahl * $geschosshoehe + 0.25;

$grundflaeche = 0.0;
foreach ( $flaechenberechnungsformel as $index => $_produkt ) {
  $produkt = 1.0;
  for ( $i = 0; $i < 2; $i++ ) {
    $_faktor = $_produkt[ $i ];
    $faktor = 0.0;
    $current_operator = '+';
    $_faktor = explode( ' ', $_faktor );
    foreach ( $_faktor as $t ) {
      switch ( $t ) {
        case '+':
        case '-':
          $current_operator = $t;
          break;
        default:
          $l_slug = 'wand_' . $t . '_laenge';
          switch ( $current_operator ) {
            case '+':
  // für was wird hier eine slug eingesetzt 
              $faktor += $$l_slug;
              break;
            case '-':
              $faktor -= $$l_slug;
              break;
            default:
          }
      }
    }
    if ( $faktor < 0.0 ) {
      $faktor = 0.0;
    }
    $produkt *= $faktor;  //vertehe ich nicht
  }
  $grundflaeche += $produkt;
}

$calculations['volumenteile']['grundriss'] = array(
  'name'          => __( 'Grundriss', 'wpenon' ),
  'v'             => $grundflaeche * $wandhoehe,  // WO wird die Gesamtlänge und die GesamtAußenwandFläche ermittelt.????????????????????????????
);
//So wie ich das sehe werden nur BruttoAußenfläche berücksichtigt; bis hier wird keine Wanddicke abgezogen (z.B.  - 0,25m); dürft aber nicht sein, da Wandstärke im Front-End abgefragt wird. Siund in den Grudnfläche die Wandstärken abgezogen?



//Definition schwere des Gebäudes

switch ( $energieausweis->gebaeudekonstruktion ) {
	case 'massiv':
		$wand_bauart = $energieausweis->wand_bauart_massiv;
		break;
	case 'holz':
		$wand_bauart = $energieausweis->wand_bauart_holz;
		break;
	case 'fachwerk':
		$wand_bauart = $energieausweis->wand_bauart_fachwerk;
		break;
}




//was wird hier gemacht? die Gesamtwandlänge addiert? $wandlaenge += $$l_slug; ?
// Sammlung der Bauteile - Wände
// Drempel & Wandflächen einbauen + Seite des Dachgiebels

$wandlaenge = 0.0;
$calculations['wandrichtungen'] = array();
foreach ( $grundriss_form as $wand => $data ) {
  $l_slug = 'wand_' . $wand . '_laenge';
  $n_slug = 'wand_' . $wand . '_nachbar';
  $wandlaenge += $$l_slug;
  if ( ! $energieausweis->$n_slug ) {
    $d_slug = 'wand_' . $wand . '_daemmung';
    $calculations['bauteile'][ 'wand_' . $wand ] = array(
      'name'          => sprintf( __( 'Außenwand %s', 'wpenon' ), $wand ),
      'typ'           => 'wand',
      'modus'         => 'opak',
      'bauart'        => $wand_bauart,
      'baujahr'       => $energieausweis->baujahr,
      'richtung'      => $hr_mappings[ $data[1] ],
      'a'             => $$l_slug * $wandhoehe,
      'd'             => $energieausweis->$d_slug,
    );
    if ( ! isset( $calculations['wandrichtungen'][ $calculations['bauteile'][ 'wand_' . $wand ]['richtung'] ] ) ) {
      $calculations['wandrichtungen'][ $calculations['bauteile'][ 'wand_' . $wand ]['richtung'] ] = array();
    }
    $calculations['wandrichtungen'][ $calculations['bauteile'][ 'wand_' . $wand ]['richtung'] ][] = $wand;
  }
}
unset( $data ); // warum werden immerwieder die Daten zurückgesetzt?


// Ab hier werden falls vorhanden die Wandflächen von Anbauten berechnet 

if ( $energieausweis->anbau ) {
  $anbauwand_b_laenge = $anbauwand_t_laenge = $anbauwand_s1_laenge = $anbauwand_s2_laenge = 0.0;

  $anbau_formen = wpenon_immoticket24_get_anbau_formen();
  $anbau_form = $anbau_formen['a'];
  if ( isset( $anbau_formen[ $energieausweis->anbau_form ] ) ) {
    $anbau_form = $anbau_formen[ $energieausweis->anbau_form ];
  }
  $anbau_flaechenberechnungsformel = $anbau_form['fla'];
  unset( $anbau_form['fla'] );

  $to_calculate = array();
  foreach ( $anbau_form as $anbauwand => $data ) {
    if ( $data[0] === true ) {
      $l_slug = 'anbauwand_' . $anbauwand . '_laenge';
      $$l_slug = $energieausweis->$l_slug;
    } else {
      $to_calculate[ $anbauwand ] = $data;
    }
  }
  unset( $data );
  foreach ( $to_calculate as $anbauwand => $data ) {
    $laenge = 0.0;
    $current_operator = '+';
    $formel = explode( ' ', $data[0] );
    foreach ( $formel as $t ) {
      switch ( $t ) {
        case '+':
        case '-':
          $current_operator = $t;
          break;
        default:
          $l_slug = 'anbauwand_' . $t . '_laenge';
          switch ( $current_operator ) {
            case '+':
              $laenge += $$l_slug;
              break;
            case '-':
              $laenge -= $$l_slug;
              break;
            default:
          }
      }
    }
    if ( $laenge > 0.0 ) {
      $l_slug = 'anbauwand_' . $anbauwand . '_laenge';
      $$l_slug = $laenge;
    }
  }
  unset( $data );
  unset( $to_calculate );

  $anbauwandhoehe = $energieausweis->anbau_hoehe + 0.25 * 2;

  $anbaugrundflaeche = 0.0;
  foreach ( $anbau_flaechenberechnungsformel as $index => $_produkt ) {
    $produkt = 1.0;
    for ( $i = 0; $i < 2; $i++ ) {
      $_faktor = $_produkt[ $i ];
      $faktor = 0.0;
      $current_operator = '+';
      $_faktor = explode( ' ', $_faktor );
      foreach ( $_faktor as $t ) {
        switch ( $t ) {
          case '+':
          case '-':
            $current_operator = $t;
            break;
          default:
            $l_slug = 'anbauwand_' . $t . '_laenge';
            switch ( $current_operator ) {
              case '+':
                $faktor += $$l_slug;
                break;
              case '-':
                $faktor -= $$l_slug;
                break;
              default:
            }
        }
      }
      if ( $faktor < 0.0 ) {
        $faktor = 0.0;
      }
      $produkt *= $faktor;
    }
    $anbaugrundflaeche += $produkt;
  }

  $calculations['volumenteile']['anbau'] = array(
    'name'          => __( 'Anbau', 'wpenon' ),
    'v'             => $anbaugrundflaeche * $anbauwandhoehe,
  );

	switch( $energieausweis->anbau_form ) {
		case 'a':
			$extension = new Extension_Form_A();
			$extension->set_height( $energieausweis->anbau_hoehe );
			$extension->set_walls( $energieausweis->anbauwand_s1_laenge, $energieausweis->anbauwand_t_laenge, $energieausweis->anbauwand_b_laenge );
			$surface_areas = $extension->get_surface_areas();
			break;
		case 'b':
			$extension = new Extension_Form_B();
			$extension->set_height( $energieausweis->anbau_hoehe );
			$extension->set_walls( $energieausweis->anbauwand_s1_laenge, $energieausweis->anbauwand_s2_laenge, $energieausweis->anbauwand_t_laenge, $energieausweis->anbauwand_b_laenge );
			$surface_areas = $extension->get_surface_areas();
			break;
	}

	$anbauwand_bauart_field = 'anbauwand_bauart_' . $energieausweis->gebaeudekonstruktion;

  $anbauwandlaenge = 0.0;
  $calculations['anbauwandrichtungen'] = array();
  foreach ( $anbau_form as $wand => $data ) {
    $l_slug = 'anbauwand_' . $wand . '_laenge';
    $anbauwandlaenge += $$l_slug;
    $_dslug = $$l_slug;

    $patch_time = strtotime( '2020-12-22 15:00' );
    $ec_time = strtotime( $energieausweis->date );

    if ( $patch_time <= $ec_time ){
        $calculations['bauteile'][ 'anbauwand_' . $wand ] = array(
            'name'          => sprintf( __( 'Anbau-Wand %s', 'wpenon' ), $wand ),
            'typ'           => 'wand',
            'modus'         => 'opak',
            'bauart'        => $energieausweis->$anbauwand_bauart_field,
            'baujahr'       => $energieausweis->anbau_baujahr,
            'richtung'      => $hr_mappings[ $data[1] ],
            'a'             => $surface_areas[ $wand ],
            'd'             => $energieausweis->anbauwand_daemmung,
        );
    } else {
        $calculations['bauteile'][ 'anbauwand_anbauwand_' . $wand ] = array(
            'name'          => sprintf( __( 'Anbau-Wand %s', 'wpenon' ), $wand ),
            'typ'           => 'wand',
            'modus'         => 'opak',
            'bauart'        => $energieausweis->$anbauwand_bauart_field,
            'baujahr'       => $energieausweis->anbau_baujahr,
            'richtung'      => $hr_mappings[ $data[1] ],
            'a'             => $surface_areas[ $wand ],
            'd'             => $energieausweis->anbauwand_daemmung,
            );
    }

    if ( ! isset( $calculations['anbauwandrichtungen'][ $calculations['bauteile'][ 'anbauwand_' . $wand ]['richtung'] ] ) ) {
      $calculations['anbauwandrichtungen'][ $calculations['bauteile'][ 'anbauwand_' . $wand ]['richtung'] ] = array();
    }
    $calculations['anbauwandrichtungen'][ $calculations['bauteile'][ 'anbauwand_' . $wand ]['richtung'] ][] = $wand;
  }
  unset( $data );

  // Subtract Anbau overlap from Grundriss manually.    
  //Oben Kommentar ist von Programmiere. Was bedeutet da ? Wo Überschneidne sich die Grundrisse?
  
  if ( ! empty( $calculations['wandrichtungen'][ $calculations['bauteile']['anbauwand_t']['richtung'] ] ) ) {
    $grundrisswand = $calculations['wandrichtungen'][ $calculations['bauteile']['anbauwand_t']['richtung'] ][0];
    $calculations['bauteile'][ 'wand_' . $grundrisswand ]['a'] -= $calculations['bauteile']['anbauwand_t']['a'] - $calculations['bauteile']['anbauwand_s1']['a'];
    if ( $calculations['bauteile'][ 'wand_' . $grundrisswand ]['a'] < 0.0 ) {
      $calculations['bauteile'][ 'wand_' . $grundrisswand ]['a'] = 0.0;
    }
  }
  if ( $anbauwand_s2_laenge < $anbauwand_b_laenge && ! empty( $calculations['wandrichtungen'][ $calculations['bauteile']['anbauwand_b']['richtung'] ] ) ) {
    $grundrisswand = $calculations['wandrichtungen'][ $calculations['bauteile']['anbauwand_b']['richtung'] ][0];
    $calculations['bauteile'][ 'wand_' . $grundrisswand ]['a'] -= $calculations['bauteile']['anbauwand_b']['a'] - $calculations['bauteile']['anbauwand_s2']['a'];
    if ( $calculations['bauteile'][ 'wand_' . $grundrisswand ]['a'] < 0.0 ) {
      $calculations['bauteile'][ 'wand_' . $grundrisswand ]['a'] = 0.0;
    }
  }

  $calculations['bauteile']['anbauboden'] = array(
    'name'          => __( 'Anbau-Boden', 'wpenon' ),
    'typ'           => 'boden',
    'modus'         => 'opak',
    'bauart'        => $energieausweis->anbauboden_bauart,
    'baujahr'       => $energieausweis->anbau_baujahr,
    'a'             => $anbaugrundflaeche,
    'd'             => $energieausweis->anbauboden_daemmung,
  );

  $calculations['bauteile']['anbaudach'] = array(
    'name'          => __( 'Anbau-Dach', 'wpenon' ),
    'typ'           => 'dach',
    'modus'         => 'dach',
    'bauart'        => $energieausweis->anbaudach_bauart,
    'baujahr'       => $energieausweis->anbau_baujahr,
    'a'             => $anbaugrundflaeche,
    'd'             => $energieausweis->anbaudach_daemmung,
  );
}


//Wichtig! ab hier werden Kellerflächen berücksichtigt. Kellerflächen, die beheizt sind



$kellerflaeche = $grundflaeche;
switch ( $energieausweis->keller ) {
  case 'beheizt':
    $keller_anteil = $energieausweis->keller_groesse * 0.01; // bei 80% ist Faktor 0,8
    $kellerwandhoehe = $energieausweis->keller_hoehe + 0.25;

    $calculations['bauteile']['kellerwand'] = array(
      'name'          => __( 'Kellerwand', 'wpenon' ),
      'typ'           => 'wand',
      'modus'         => 'opak',
      'bauart'        => $energieausweis->keller_bauart,
      'baujahr'       => $energieausweis->baujahr,
      'a'             => $wandlaenge * $kellerwandhoehe * $keller_anteil, // Bei 50% Kelleranteil kann fehlt eine Wand, wenn man die Hälfte der Wände weg nimmt. Mit Umfang neuberechnen
      'd'             => $energieausweis->keller_daemmung,
    );
    $calculations['bauteile']['boden'] = array(
      'name'          => __( 'Boden', 'wpenon' ),
      'typ'           => 'boden',
      'modus'         => 'opak',
      'bauart'        => $energieausweis->boden_bauart,
      'baujahr'       => $energieausweis->baujahr,
      'a'             => $kellerflaeche, // Gesamtfläche ohne Kelleranteil?
      'd'             => $energieausweis->boden_daemmung,
    );
    $calculations['volumenteile']['keller'] = array(
      'name'          => __( 'Kellergeschoss', 'wpenon' ),
      'v'             => $grundflaeche * $kellerwandhoehe * $keller_anteil,
    );
    break;
  case 'unbeheizt':
    $kellerflaeche *= $energieausweis->keller_groesse * 0.01;
    $calculations['bauteile']['kellerdecke'] = array(
      'name'          => __( 'Kellerdecke', 'wpenon' ),
      'typ'           => 'boden',
      'modus'         => 'opak',
      'bauart'        => $energieausweis->boden_bauart,
      'baujahr'       => $energieausweis->baujahr,
      'a'             => $kellerflaeche,
      'd'             => $energieausweis->boden_daemmung,
    );
    if ( $kellerflaeche < $grundflaeche ) {
      $calculations['bauteile']['boden'] = array(
        'name'          => __( 'Boden', 'wpenon' ),
        'typ'           => 'boden',
        'modus'         => 'opak',
        'bauart'        => $energieausweis->boden_bauart,
        'baujahr'       => $energieausweis->baujahr,
        'a'             => $grundflaeche - $kellerflaeche,
        'd'             => $energieausweis->boden_daemmung,
      );
    }
    break;
  case 'nicht-vorhanden':
  default:
    $calculations['bauteile']['boden'] = array(
      'name'          => __( 'Boden', 'wpenon' ),
      'typ'           => 'boden',
      'modus'         => 'opak',
      'bauart'        => $energieausweis->boden_bauart,
      'baujahr'       => $energieausweis->baujahr,
      'a'             => $kellerflaeche,
      'd'             => $energieausweis->boden_daemmung,
    );
}


// ab hier werden die Dachflächen und das DAchvolumen berechnet. ...Scheinbar ist das Kniestock(Trempel) nicht berücksichtigt. Fehlt also in der Außenwanflächenberchnung (Transmission) sowie in Volumen des DAchgeschosses.

$deckenflaeche = $grundflaeche;
$dachwinkel_formatted = 0.0;
switch ( $energieausweis->dach ) {
  case 'beheizt':
    $dachhoehe = $energieausweis->dach_hoehe;
    // Drempel muss von der Dachhöhe abgezogen werden und das Volumen des Drempels in das Dach eingerechnet werden.
    $dachflaeche = $dachvolumen = 0.0;
    $dachwinkel = array();
    $dachwandflaechen = array();
    switch ( $energieausweis->dach_form ) {
      case 'walmdach':
        switch ( $energieausweis->grundriss_form ) {
          case 'a':
            if ( $wand_a_laenge > $wand_b_laenge ) {
              $dach_th = $wand_a_laenge;
              $dach_f = $wand_a_laenge - $wand_b_laenge;
              $dach_b = 0.5 * $wand_b_laenge;
              $dach_x = 0.5 * ( $wand_a_laenge - $dach_f );
            } else {
              $dach_th = $wand_b_laenge;
              $dach_f = $wand_b_laenge - $wand_a_laenge;
              $dach_b = 0.5 * $wand_a_laenge;
              $dach_x = 0.5 * ( $wand_b_laenge - $dach_f );
            }
            $dach_sh = sqrt( pow( $dachhoehe, 2 ) + pow( $dach_b, 2 ) );
            $dach_sw = sqrt( pow( $dachhoehe, 2 ) + pow( $dach_x, 2 ) );
            array_push( $dachwinkel, atan( $dachhoehe / $dach_b ), atan( $dachhoehe / $dach_x ) );
            $dachflaeche += 2 * ( 0.5 * $dach_b * $dach_sw + 0.5 * ( $dach_th + $dach_f ) * $dach_sh );
            $dachvolumen += ( 1.0 / 3.0 ) * ( 2 * $dach_b ) * ( 2 * $dach_x ) * $dachhoehe + 0.5 * ( 2 * $dach_b ) * $dach_f * $dachhoehe;
            break;
          case 'b':
            $dach_b1_gross = $wand_f_laenge;
            $dach_b1 = 0.5 * $wand_f_laenge;
            $dach_b2_gross = $wand_c_laenge;
            $dach_b2 = 0.5 * $wand_c_laenge;
            $dach_t1 = $wand_b_laenge;
            $dach_t2 = $wand_a_laenge;
            $dach_t3 = $wand_d_laenge;
            $dach_t4 = $wand_e_laenge;
            $dach_f1 = $dach_t3;
            $dach_f2 = $dach_t2 - 2 * $dach_b1;
            $dach_s1 = sqrt( pow( $dachhoehe, 2 ) + pow( $dach_b1, 2 ) );
            $dach_s2 = sqrt( pow( $dachhoehe, 2 ) + pow( $dach_b2, 2 ) );
            array_push( $dachwinkel, atan( $dachhoehe / $dach_b1 ), atan( $dachhoehe / $dach_b2 ) );
            $dachflaeche += 0.5 * $dach_b1_gross * $dach_s1 + 0.5 * $dach_b2_gross * $dach_s2 + 0.5 * ( $dach_t1 + $dach_f1 ) * $dach_s2 + 0.5 * ( $dach_t2 + $dach_f2 ) * $dach_s1 + ( 0.5 * ( $dach_t2 + $dach_f2 ) * $dach_s1 - 0.5 * $dach_b2_gross * $dach_s2 ) + $dach_t3 * $dach_s2;
            $dachvolumen += ( 1.0 / 3.0 ) * $dach_b1_gross * $dach_b1_gross * $dachhoehe + 0.5 * $dach_b1_gross * $dach_f2 * $dachhoehe + 0.5 * $dach_b2_gross * $dach_t3 * $dachhoehe;
            break;
          case 'c':
            $dach_b1_gross = $wand_b_laenge;
            $dach_b1 = 0.5 * $wand_b_laenge;
            $dach_b2_gross = $wand_e_laenge;
            $dach_b2 = 0.5 * $wand_e_laenge;
            $dach_t1 = $wand_b_laenge + $wand_d_laenge;
            $dach_t2 = $wand_a_laenge;
            $dach_t3 = $wand_f_laenge;
            $dach_t4 = $wand_g_laenge;
            $dach_f1 = $dach_t3;
            $dach_f2 = $dach_t2 - 2 * $dach_b1;
            $dach_s1 = sqrt( pow( $dachhoehe, 2 ) + pow( $dach_b1, 2 ) );
            $dach_s2 = sqrt( pow( $dachhoehe, 2 ) + pow( $dach_b2, 2 ) );
            array_push( $dachwinkel, atan( $dachhoehe / $dach_b1 ), atan( $dachhoehe / $dach_b2 ) );
            $dachflaeche += 2 * ( 0.5 * $dach_b1_gross * $dach_s1 ) + 0.5 * $dach_b2_gross * $dach_s2 + 0.5 * ( $dach_t2 + $dach_f2 ) * $dach_s1 + ( 0.5 * ( $dach_t2 + $dach_f2 ) * $dach_s1 - 0.5 * $dach_b2_gross * $dach_s2 ) + 2 * ( $dach_t3 * $dach_s2 );
            $dachvolumen += ( 1.0 / 3.0 ) * $dach_b1_gross * $dach_b1_gross * $dachhoehe + 0.5 * $dach_b1_gross * $dach_f2 * $dachhoehe + 0.5 * $dach_b2_gross * $dach_t3 * $dachhoehe;
            break;
          case 'd':
            $dach_b1_gross = $wand_b_laenge - $wand_d_laenge;
            $dach_b1 = 0.5 * ( $wand_b_laenge - $wand_d_laenge );
            $dach_b2_gross = $wand_c_laenge;
            $dach_b2 = 0.5 * $wand_c_laenge;
            $dach_b3_gross = $wand_g_laenge;
            $dach_b3 = 0.5 * $wand_g_laenge;
            $dach_t1 = $wand_b_laenge;
            $dach_t2 = $wand_a_laenge;
            $dach_t3 = $wand_h_laenge;
            $dach_t4 = $wand_d_laenge;
            $dach_t5 = $wand_e_laenge;
            $dach_t6 = $wand_f_laenge;
            $dach_f1 = $dach_t1 - $dach_b1 - $dach_b2;
            $dach_f2 = $dach_t2 - $dach_b2 - $dach_b3;
            $dach_f3 = $dach_t3 - $dach_b1 - $dach_b3;
            $dach_s1 = sqrt( pow( $dachhoehe, 2 ) + pow( $dach_b1, 2 ) );
            $dach_s2 = sqrt( pow( $dachhoehe, 2 ) + pow( $dach_b2, 2 ) );
            $dach_s3 = sqrt( pow( $dachhoehe, 2 ) + pow( $dach_b3, 2 ) );
            array_push( $dachwinkel, atan( $dachhoehe / $dach_b1 ), atan( $dachhoehe / $dach_b2 ), atan( $dachhoehe / $dach_b3 ) );
            $dachflaeche += 0.5 * $dach_b2_gross * $dach_s2 + 0.5 * $dach_b3_gross * $dach_s3 + 0.5 * ( $dach_t1 + $dach_f1 ) * $dach_s2 + 0.5 * ( $dach_t2 + $dach_f2 ) * $dach_s1 + 0.5 * ( $dach_t3 + $dach_f3 ) * $dach_s3 + $dach_t4 * $dach_s2 + 0.5 * ( $dach_t5 + $dach_f2 ) * $dach_s1 + $dach_t6 * $dach_s3;
            $dachvolumen += ( 1.0 / 3.0 ) * $dach_b1_gross * $dach_b1_gross * $dachhoehe + 0.5 * $dach_b1_gross * $dach_f2 * $dachhoehe + 0.5 * $dach_b2_gross * $dach_t4 * $dachhoehe + 0.5 * $dach_b3_gross * $dach_t6 * $dachhoehe;
            break;
          default:
        }
        break;
      case 'pultdach':
        switch ( $energieausweis->grundriss_form ) {
          case 'a':
            if ( $wand_a_laenge > $wand_b_laenge ) {
              $dach_s = sqrt( pow( $dachhoehe, 2 ) + pow( $wand_b_laenge, 2 ) );
              array_push( $dachwinkel, atan( $dachhoehe / $wand_b_laenge ) );
              $dachflaeche += $wand_a_laenge * $dach_s;
              $dachvolumen += 0.5 * $wand_a_laenge * $wand_b_laenge * $dachhoehe;
              $dachwandflaechen['b'] = 0.5 * $wand_b_laenge * $dachhoehe;
              $dachwandflaechen['d'] = 0.5 * $wand_d_laenge * $dachhoehe;
              $dachwandflaechen['c'] = $wand_a_laenge * $dachhoehe;
            } else {
              $dach_s = sqrt( pow( $dachhoehe, 2 ) + pow( $wand_a_laenge, 2 ) );
              array_push( $dachwinkel, atan( $dachhoehe / $wand_a_laenge ) );
              $dachflaeche += $wand_b_laenge * $dach_s;
              $dachvolumen += 0.5 * $wand_a_laenge * $wand_b_laenge * $dachhoehe;
              $dachwandflaechen['a'] = 0.5 * $wand_a_laenge * $dachhoehe;
              $dachwandflaechen['c'] = 0.5 * $wand_c_laenge * $dachhoehe;
              $dachwandflaechen['d'] = $wand_b_laenge * $dachhoehe;
            }
            break;
          case 'b':
            if ( $wand_a_laenge > $wand_b_laenge ) {
              $dach_s1 = sqrt( pow( $dachhoehe, 2 ) + pow( $wand_f_laenge, 2 ) );
              $dach_s2 = sqrt( pow( $dachhoehe, 2 ) + pow( $wand_d_laenge, 2 ) );
              array_push( $dachwinkel, atan( $dachhoehe / $wand_f_laenge ), atan( $dachhoehe / $wand_d_laenge ) );
              $dachflaeche += $wand_a_laenge * $dach_s1 + $wand_c_laenge * $dach_s2;
              $dachvolumen += 0.5 * $wand_a_laenge * $wand_f_laenge * $dachhoehe + 0.5 * $wand_c_laenge * $wand_d_laenge * $dachhoehe;
              $dachwandflaechen['b'] = 0.5 * $wand_f_laenge * $dachhoehe + 0.5 * $wand_d_laenge * $dachhoehe;
              $dachwandflaechen['d'] = 0.5 * $wand_d_laenge * $dachhoehe;
              $dachwandflaechen['e'] = $wand_e_laenge * $dachhoehe;
              $dachwandflaechen['f'] = 0.5 * $wand_f_laenge * $dachhoehe;
            } else {
              $dach_s1 = sqrt( pow( $dachhoehe, 2 ) + pow( $wand_c_laenge, 2 ) );
              $dach_s2 = sqrt( pow( $dachhoehe, 2 ) + pow( $wand_e_laenge, 2 ) );
              array_push( $dachwinkel, atan( $dachhoehe / $wand_c_laenge ), atan( $dachhoehe / $wand_e_laenge ) );
              $dachflaeche += $wand_b_laenge * $dach_s1 + $wand_f_laenge * $dach_s2;
              $dachvolumen += 0.5 * $wand_b_laenge * $wand_c_laenge * $dachhoehe + 0.5 * $wand_f_laenge * $wand_e_laenge * $dachhoehe;
              $dachwandflaechen['a'] = 0.5 * $wand_c_laenge * $dachhoehe + 0.5 * $wand_e_laenge * $dachhoehe;
              $dachwandflaechen['c'] = 0.5 * $wand_c_laenge * $dachhoehe;
              $dachwandflaechen['d'] = $wand_d_laenge * $dachhoehe;
              $dachwandflaechen['e'] = 0.5 * $wand_e_laenge * $dachhoehe;
            }
            break;
          case 'c':
            $dach_s1 = sqrt( pow( $dachhoehe, 2 ) + pow( $wand_b_laenge, 2 ) );
            $dach_s2 = sqrt( pow( $dachhoehe, 2 ) + pow( $wand_d_laenge, 2 ) );
            array_push( $dachwinkel, atan( $dachhoehe / $wand_b_laenge ), atan( $dachhoehe / $wand_d_laenge ) );
            $dachflaeche += $wand_a_laenge * $dach_s1 + $wand_e_laenge * $dach_s2;
            $dachvolumen += 0.5 * $wand_a_laenge * $wand_b_laenge * $dachhoehe + 0.5 * $wand_e_laenge * $wand_d_laenge * $dachhoehe;
            $dachwandflaechen['b'] = 0.5 * $wand_b_laenge * $dachhoehe;
            $dachwandflaechen['c'] = $wand_c_laenge * $dachhoehe;
            $dachwandflaechen['d'] = 0.5 * $wand_d_laenge * $dachhoehe;
            $dachwandflaechen['f'] = 0.5 * $wand_f_laenge * $dachhoehe;
            $dachwandflaechen['g'] = $wand_g_laenge * $dachhoehe;
            $dachwandflaechen['h'] = 0.5 * $wand_h_laenge * $dachhoehe;
            break;
          case 'd':
            $dach_s1 = sqrt( pow( $dachhoehe, 2 ) + pow( $wand_b_laenge - $wand_d_laenge, 2 ) );
            $dach_s2 = sqrt( pow( $dachhoehe, 2 ) + pow( $wand_d_laenge, 2 ) );
            $dach_s3 = sqrt( pow( $dachhoehe, 2 ) + pow( $wand_f_laenge, 2 ) );
            array_push( $dachwinkel, atan( $dachhoehe / ( $wand_b_laenge - $wand_d_laenge ) ), atan( $dachhoehe / $wand_d_laenge ), atan( $dachhoehe / $wand_f_laenge ) );
            $dachflaeche += $wand_a_laenge * $dach_s1 + $wand_c_laenge * $dach_s2 + $wand_g_laenge * $dach_s3;
            $dachvolumen += 0.5 * $wand_a_laenge * ( $wand_b_laenge - $wand_d_laenge ) * $dachhoehe + 0.5 * $wand_c_laenge * $wand_d_laenge * $dachhoehe + 0.5 * $wand_g_laenge * $wand_f_laenge * $dachhoehe;
            $dachwandflaechen['b'] = 0.5 * ( $wand_b_laenge - $wand_d_laenge ) * $dachhoehe + 0.5 * $wand_d_laenge * $dachhoehe;
            $dachwandflaechen['d'] = 0.5 * $wand_d_laenge * $dachhoehe;
            $dachwandflaechen['e'] = $wand_e_laenge * $dachhoehe;
            $dachwandflaechen['f'] = 0.5 * $wand_f_laenge * $dachhoehe;
            $dachwandflaechen['h'] = 0.5 * ( $wand_h_laenge - $wand_f_laenge ) * $dachhoehe + 0.5 * $wand_f_laenge * $dachhoehe;
            break;
          default:
        }
        break;
      case 'satteldach':
      default:
        switch ( $energieausweis->grundriss_form ) {
          case 'a':
            if ( $wand_a_laenge > $wand_b_laenge ) {
              $dach_s = sqrt( pow( $dachhoehe, 2 ) + pow( 0.5 * $wand_b_laenge, 2 ) );
              array_push( $dachwinkel, atan( $dachhoehe / ( 0.5 * $wand_b_laenge ) ) );
              $dachflaeche += $wand_a_laenge * $dach_s + $wand_c_laenge * $dach_s;
              $dachvolumen += 0.5 * $wand_a_laenge * $wand_b_laenge * $dachhoehe;
              $dachwandflaechen['b'] = 0.5 * $wand_b_laenge * $dachhoehe;
              $dachwandflaechen['d'] = 0.5 * $wand_d_laenge * $dachhoehe;
            } else {
              $dach_s = sqrt( pow( $dachhoehe, 2 ) + pow( 0.5 * $wand_a_laenge, 2 ) );
              array_push( $dachwinkel, atan( $dachhoehe / ( 0.5 * $wand_a_laenge ) ) );
              $dachflaeche += $wand_b_laenge * $dach_s + $wand_d_laenge * $dach_s;
              $dachvolumen += 0.5 * $wand_a_laenge * $wand_b_laenge * $dachhoehe;
              $dachwandflaechen['a'] = 0.5 * $wand_a_laenge * $dachhoehe;
              $dachwandflaechen['c'] = 0.5 * $wand_c_laenge * $dachhoehe;
            }
            break;
          case 'b':
            if ( $wand_a_laenge > $wand_b_laenge ) {
              $dach_s1 = sqrt( pow( $dachhoehe, 2 ) + pow( 0.5 * $wand_f_laenge, 2 ) );
              $dach_s2 = sqrt( pow( $dachhoehe, 2 ) + pow( 0.5 * $wand_c_laenge, 2 ) );
              array_push( $dachwinkel, atan( $dachhoehe / ( 0.5 * $wand_f_laenge ) ), atan( $dachhoehe / ( 0.5 * $wand_c_laenge ) ) );
              $dachflaeche += 2 * ( $wand_a_laenge - 0.25 * $wand_c_laenge ) * $dach_s1 + 2 * ( $wand_d_laenge + 0.25 * $wand_f_laenge ) * $dach_s2;
              $dachvolumen += 0.5 * $wand_a_laenge * $wand_f_laenge * $dachhoehe + 0.5 * $wand_d_laenge * $wand_c_laenge * $dachhoehe + ( 1.0 / 3.0 ) * ( 0.5 * $wand_c_laenge * $dachhoehe ) * ( 0.5 * $wand_f_laenge );
              $dachwandflaechen['b'] = 0.5 * $wand_f_laenge * $dachhoehe;
              $dachwandflaechen['c'] = 0.5 * $wand_c_laenge * $dachhoehe;
              $dachwandflaechen['f'] = 0.5 * $wand_f_laenge * $dachhoehe;
            } else {
              $dach_s1 = sqrt( pow( $dachhoehe, 2 ) + pow( 0.5 * $wand_c_laenge, 2 ) );
              $dach_s2 = sqrt( pow( $dachhoehe, 2 ) + pow( 0.5 * $wand_f_laenge, 2 ) );
              array_push( $dachwinkel, atan( $dachhoehe / ( 0.5 * $wand_c_laenge ) ), atan( $dachhoehe / ( 0.5 * $wand_f_laenge ) ) );
              $dachflaeche += 2 * ( $wand_b_laenge - 0.25 * $wand_f_laenge ) * $dach_s1 + 2 * ( $wand_e_laenge + 0.25 * $wand_c_laenge ) * $dach_s2;
              $dachvolumen += 0.5 * $wand_b_laenge * $wand_c_laenge * $dachhoehe + 0.5 * $wand_e_laenge * $wand_f_laenge * $dachhoehe + ( 1.0 / 3.0 ) * ( 0.5 * $wand_f_laenge * $dachhoehe ) * ( 0.5 * $wand_c_laenge );
              $dachwandflaechen['a'] = 0.5 * $wand_c_laenge * $dachhoehe;
              $dachwandflaechen['c'] = 0.5 * $wand_c_laenge * $dachhoehe;
              $dachwandflaechen['f'] = 0.5 * $wand_f_laenge * $dachhoehe;
            }
            break;
          case 'c':
            $dach_s1 = sqrt( pow( $dachhoehe, 2 ) + pow( 0.5 * $wand_b_laenge, 2 ) );
            $dach_s2 = sqrt( pow( $dachhoehe, 2 ) + pow( 0.5 * $wand_e_laenge, 2 ) );
            array_push( $dachwinkel, atan( $dachhoehe / ( 0.5 * $wand_b_laenge ) ), atan( $dachhoehe / ( 0.5 * $wand_e_laenge ) ) );
            $dachflaeche += 2 * ( $wand_a_laenge - 0.25 * $wand_e_laenge ) * $dach_s1 + 2 * ( $wand_d_laenge + 0.25 * $wand_b_laenge ) * $dach_s2;
            $dachvolumen += 0.5 * $wand_a_laenge * $wand_b_laenge * $dachhoehe + 0.5 * $wand_e_laenge * $wand_d_laenge * $dachhoehe + ( 1.0 / 3.0 ) * ( 0.5 * $wand_e_laenge * $dachhoehe ) * ( 0.5 * $wand_b_laenge );
            $dachwandflaechen['b'] = 0.5 * $wand_b_laenge * $dachhoehe;
            $dachwandflaechen['e'] = 0.5 * $wand_e_laenge * $dachhoehe;
            $dachwandflaechen['h'] = 0.5 * $wand_h_laenge * $dachhoehe;
            break;
          case 'd':
            $dach_s1 = sqrt( pow( $dachhoehe, 2 ) + pow( 0.5 * ( $wand_b_laenge - $wand_d_laenge ), 2 ) );
            $dach_s2 = sqrt( pow( $dachhoehe, 2 ) + pow( 0.5 * $wand_c_laenge, 2 ) );
            $dach_s3 = sqrt( pow( $dachhoehe, 2 ) + pow( 0.5 * $wand_g_laenge, 2 ) );
            array_push( $dachwinkel, atan( $dachhoehe / ( 0.5 * ( $wand_b_laenge - $wand_d_laenge ) ) ), atan( $dachhoehe / ( 0.5 * $wand_c_laenge ) ), atan( $dachhoehe / ( 0.5 * $wand_g_laenge ) ) );
            $dachflaeche += 2 * ( $wand_a_laenge - 0.25 * ( $wand_c_laenge + $wand_g_laenge ) ) * $dach_s1 + 2 * ( $wand_d_laenge + 0.25 * ( $wand_b_laenge - $wand_d_laenge ) ) * $dach_s2 + 2 * ( $wand_f_laenge + 0.25 * ( $wand_h_laenge - $wand_f_laenge ) ) * $dach_s3;
            $dachvolumen += 0.5 * $wand_a_laenge * ( $wand_b_laenge - $wand_d_laenge ) * $dachhoehe + 0.5 * $wand_c_laenge * $wand_d_laenge * $dachhoehe + 0.5 * $wand_g_laenge * $wand_f_laenge * $dachhoehe + ( 1.0 / 3.0 ) * ( 0.5 * $wand_c_laenge * $dachhoehe ) * ( 0.5 * ( $wand_b_laenge - $wand_d_laenge ) ) + ( 1.0 / 3.0 ) * ( 0.5 * $wand_g_laenge * $dachhoehe ) * ( 0.5 * ( $wand_h_laenge - $wand_f_laenge ) );
            $dachwandflaechen['b'] = 0.5 * ( $wand_b_laenge - $wand_d_laenge ) * $dachhoehe;
            $dachwandflaechen['c'] = 0.5 * $wand_c_laenge * $dachhoehe;
            $dachwandflaechen['g'] = 0.5 * $wand_g_laenge * $dachhoehe;
            $dachwandflaechen['h'] = 0.5 * ( $wand_h_laenge - $wand_f_laenge ) * $dachhoehe;
            break;
          default:
        }
        break;
    }


//Warum wird hier  der Dachwinkel berechnet?
    $_dachwinkel = $dachwinkel;
    $dachwinkel = 0.0;
    foreach ( $_dachwinkel as $w ) {
      $dachwinkel += $w * 180.0 / pi();
    }
    $dachwinkel_formatted = $dachwinkel / count( $_dachwinkel );
    $dachwinkel = $dachwinkel_formatted * pi() / 180.0;

    foreach ( $dachwandflaechen as $wand => $flaeche ) {
      if ( isset( $calculations['bauteile'][ 'wand_' . $wand ] ) ) {
        $calculations['bauteile'][ 'wand_' . $wand ]['a'] += $flaeche;
      }
    }

//über array werden den "Schlüsselbergriffen" Werte zugeordnet

    $calculations['bauteile']['dach'] = array(
      'name'          => __( 'Dach', 'wpenon' ),
      'typ'           => 'dach',
      'modus'         => 'dach',
      'bauart'        => $energieausweis->dach_bauart,
      'baujahr'       => $energieausweis->baujahr,
      'a'             => $dachflaeche,
      'd'             => $energieausweis->dach_daemmung,
    );

    $calculations['volumenteile']['dach'] = array(
      'name'          => __( 'Dachgeschoss', 'wpenon' ),
      'v'             => $dachvolumen,
    );
    break;
  case 'unbeheizt':
    $calculations['bauteile']['decke'] = array(
      'name'          => __( 'Oberste Geschossdecke', 'wpenon' ),
      'typ'           => 'decke',
      'modus'         => 'opak',
      'bauart'        => $energieausweis->decke_bauart,
      'baujahr'       => $energieausweis->baujahr,
      'a'             => $deckenflaeche,
      'd'             => $energieausweis->decke_daemmung,
    );
    break;
  case 'nicht-vorhanden':
  default:
    $calculations['bauteile']['dach'] = array(
      'name'          => __( 'Flachdach', 'wpenon' ),
      'typ'           => 'dach',
      'modus'         => 'dach',
      'bauart'        => $energieausweis->dach_bauart,
      'baujahr'       => $energieausweis->baujahr,
      'a'             => $deckenflaeche,
      'd'             => $energieausweis->dach_daemmung,
    );
}

/*************************************************
 * BAUTEILE GEBÄUDEVOLUMEN
 *************************************************/
// Addieren der Volumenteile zur Ermittlung des Gesamtvolumens.
$calculations['huellvolumen'] = 0.0;
foreach ( $calculations['volumenteile'] as $slug => $data ) {
  $calculations['huellvolumen'] += $data['v'];
}
unset( $data );



if ( $geschosshoehe >= 2.5 && $geschosshoehe <= 3.0 ) {
  $calculations['nutzflaeche'] = $calculations['huellvolumen'] * 0.32; //Brutto volumen und Gebäudenutzfläche
} else {
 
 // das soll die Grundfläche ergeben, Berchnung der Gebäudenutzfläche für diese Bedingung für Gebäude kleiner 2,5m und größer 3m (Geschoßhöhe)!!!
  $calculations['nutzflaeche'] = $calculations['huellvolumen'] * ( 1.0 / $geschosshoehe - 0.04 );
}

//Variable 'Nutzflaeche' wird weiter unten in der Monats-Bilanzierung eingesetzt

/*************************************************
 * BAUTEILE TRANSPARENT etc.
 *************************************************/

foreach ( $grundriss_form as $wand => $data ) {
if ( isset( $calculations['bauteile'][ 'wand_' . $wand ] ) ) {
  
  // Automatische Berechnung
  $l_slug = 'wand_' . $wand . '_laenge';
  $n_slug = 'wand_' . $wand . '_nachbar';    

  if ( $energieausweis->$n_slug === true ) {
    continue;
  }

// Fensterfläche Wand a: 0,55 * (Wandlänge a - 2 * Wandstärke) * ((Geschosshöhe - 1,50 m) * Anzahl Vollgeschoss)

// es wird Bruttofläche angesetzt (2 * Wandstärke)
  $fensterflaeche = 0.55 * ( $energieausweis->$l_slug - 2 * $energieausweis->wand_staerke / 100 ) * ( ( $energieausweis->geschoss_hoehe - 1.5 ) * $energieausweis->geschoss_zahl );

  $calculations['bauteile'][ 'fenster_' . $wand ] = array(
    'name'          => sprintf( __( 'Fenster Wand %s', 'wpenon' ), $wand ),
    'typ'           => 'fenster',
    'modus'         => 'transparent',
    'bauart'        => $energieausweis->fenster_bauart,
    'baujahr'       => $energieausweis->fenster_baujahr,
    'richtung'      => $calculations['bauteile'][ 'wand_' . $wand ]['richtung'],
    'a'             => $fensterflaeche,
    'd'             => 0,
    'winkel'        => 90.0,
  );

  $calculations['bauteile'][ 'wand_' . $wand ]['a'] -= $calculations['bauteile'][ 'fenster_' . $wand ]['a'];
  hydraulische
}

}
unset( $data );

if ( $energieausweis->anbau ) {
  foreach ( $anbau_form as $wand => $data ) {      
    if ( isset( $calculations['bauteile'][ 'anbauwand_' . $wand ] ) ) {
      $a_slug = 'anbaufenster_' . $wand . '_flaeche';

      $l_slug = 'anbauwand_' . $wand . '_laenge';

      $b_laenge = $energieausweis->anbauwand_b_laenge;
      $t_laenge = $energieausweis->anbauwand_t_laenge;
      $s1_laenge = $energieausweis->anbauwand_s1_laenge;
      $s2_laenge = $energieausweis->anbauwand_s2_laenge;

      $anbauwand_staerke = $energieausweis->anbauwand_staerke / 100; // Umrechnen von cm in m
      $geschoss_hoehe     = $energieausweis->geschoss_hoehe;

      if ( $energieausweis->anbau_form === 'a' ) {
        switch( $wand ) {
          case 'b':
          
 // Fensterfläche Wand b: 0,55 * (Wandlänge Anbaubreite b - 2 * Wandstärke Anbau) * (Höhe des Anbau - 1,50 m)
 // es wird die Bruttofläche benutzt (2*Wandstärke)
            $fensterflaeche = 0.55 * ( $b_laenge - ( 2 * $anbauwand_staerke ) ) * ( $geschoss_hoehe - 1.5 );
            break;
          case 't':              
            // Fensterfläche Wand t: 0,55 * (Wandlänge Anbautiefe t - 2 * Wandstärke Anbau) * (Höhe des Anbau - 1,50 m) 
            $fensterflaeche = 0.55 * ( $t_laenge - ( 2 * $anbauwand_staerke ) ) * ( $geschoss_hoehe - 1.5 );
            break;
          case 's1':
            // Fensterfläche Wand s1: 0,55 * (Wandlänge Anbautiefe t - Wandstärke Anbau) * (Höhe des Anbau - 1,50 m) - Anbau Schnittlänge s1
            $fensterflaeche = 0.55 * ( $t_laenge - $anbauwand_staerke ) * ( $geschoss_hoehe - 1.5 ) - $s1_laenge;
            break;
          case 's2':
            // Fensterfläche Wand s2: 0,55 * (Anbau Schnittlänge s2 - 2 * Wandstärke Anbau) * (Höhe des Anbau - 1,50 m) 
            $fensterflaeche = 0.55 * ( $s2_laenge - ( 2 * $anbauwand_staerke ) ) * ( $geschoss_hoehe - 1.5 );
            break;
          
        }
      }

      if ( $energieausweis->anbau_form === 'b' ) {
        switch( $wand ) {
          case 'b':
            // Fensterfläche Wand b: 0,55 * (Wandlänge Anbaubreite b - 2 * Wandstärke Anbau) * (Höhe des Anbau - 1,50 m)
            $fensterflaeche = 0.55 * ( $b_laenge - ( 2 * $anbauwand_staerke ) ) * ( $geschoss_hoehe - 1.5 );
            break;
          case 't':              
            // Fensterfläche Wand t: 0,55 * (Wandlänge Anbautiefe t - 2 * Wandstärke Anbau) * (Höhe des Anbau - 1,50 m) 
            $fensterflaeche = 0.55 * ( $t_laenge - ( 2 * $anbauwand_staerke ) ) * ( $geschoss_hoehe - 1.5 );
            break;
          case 's1':
            // Fensterfläche Wand s1: 0,55 * (Wandlänge Anbautiefe t - Wandstärke Anbau) * (Höhe des Anbau - 1,50 m) - Anbau Schnittlänge s1
            $fensterflaeche = 0.55 * ( $t_laenge - $anbauwand_staerke ) * ( $geschoss_hoehe - 1.5 ) - $s1_laenge;
            break;
          case 's2':
            // Fensterfläche Wand s2: 0,55 * (Anbaubreite b - Wandstärke Anbau) * (Höhe des Anbau - 1,50 m) - Anbau Schnittlänge s2
            $fensterflaeche = 0.55 * ( $b_laenge - $anbauwand_staerke ) * ( $geschoss_hoehe - 1.5 ) - $s2_laenge;
            break;
        }
      }

      if ( $fensterflaeche < 0 ) {
        $fensterflaeche = 0;
      }

      $calculations['bauteile'][ 'anbaufenster_' . $wand ] = array(
        'name'          => sprintf( __( 'Anbau-Fenster Wand %s', 'wpenon' ), $wand ),
        'typ'           => 'fenster',
        'modus'         => 'transparent',
        'bauart'        => $energieausweis->anbaufenster_bauart,
        'baujahr'       => $energieausweis->anbaufenster_baujahr,
        'richtung'      => $calculations['bauteile'][ 'anbauwand_' . $wand ]['richtung'],
        'a'             => $fensterflaeche,^
        'd'             => 0,
        'winkel'        => 90.0, //Fenster werden senkrecht angesetzt! Dachfrenster etc. finden da daher keine Berücksichtigung!!!!! Oder?, Sollte später erweitert werden um z.B. Dachfenster
        
      );

      $calculations['bauteile'][ 'anbauwand_' . $wand ]['a'] -= $calculations['bauteile'][ 'anbaufenster_' . $wand ]['a']; 
    }
  }
  unset( $data );
}


//Heizkörpernischen_Berechnung. Hier nimmt er aus dem arrey 'bauteile' die Fensterfläche'a' und multipiziert diese mit 0,5; Er nimmt also an, dass die Nische halb so gr0ß ist wie das darüberliegende Fesnter. 



if ( $energieausweis->heizkoerpernischen == 'vorhanden' ) {
  foreach ( $grundriss_form as $wand => $data ) {
    if ( isset( $calculations['bauteile'][ 'fenster_' . $wand ] ) ) {
      $flaeche = $calculations['bauteile'][ 'fenster_' . $wand ]['a'] * 0.5;
  
  //Frage: er zieht hier  von der  Wandfläche die Fensterfläche ab 
      $calculations['bauteile'][ 'wand_' . $wand ]['a'] -= $flaeche;
      if ( $calculations['bauteile'][ 'wand_' . $wand ]['a'] < 0.0 ) {
        $flaeche += $calculations['bauteile'][ 'wand_' . $wand ]['a'];
        $calculations['bauteile'][ 'wand_' . $wand ]['a'] -= $calculations['bauteile'][ 'wand_' . $wand ]['a'];
      }
      if ( $flaeche > 0.0 ) {
        $calculations['bauteile'][ 'heizkoerpernischen_' . $wand ] = array(
          'name'          => sprintf( __( 'Heizkörpernischen Wand %s', 'wpenon' ), $wand ),
          'typ'           => 'heizkoerpernischen',
          'modus'         => 'opak',
          'bauart'        => $calculations['bauteile'][ 'wand_' . $wand ]['bauart'],
          'baujahr'       => $energieausweis->baujahr,
          'richtung'      => $calculations['bauteile'][ 'wand_' . $wand ]['richtung'],
          'a'             => $flaeche,
          'd'             => 0,
        );
      }
    }
  }
  unset( $data );
}


//1111

if ( substr( $energieausweis->rollladenkaesten, 0, 6 ) == 'innen_' ) {
  $bauart = str_replace( 'innen_', '', $energieausweis->rollladenkaesten );
  foreach ( $grundriss_form as $wand => $data ) {
    if ( isset( $calculations['bauteile'][ 'fenster_' . $wand ] ) ) {
      $flaeche = $calculations['bauteile'][ 'fenster_' . $wand ]['a'] * 0.1;
      $calculations['bauteile'][ 'wand_' . $wand ]['a'] -= $flaeche;
      if ( $calculations['bauteile'][ 'wand_' . $wand ]['a'] < 0.0 ) {
        $flaeche += $calculations['bauteile'][ 'wand_' . $wand ]['a'];
        $calculations['bauteile'][ 'wand_' . $wand ]['a'] -= $calculations['bauteile'][ 'wand_' . $wand ]['a'];
      }
      if ( $flaeche > 0.0 ) {
        $calculations['bauteile'][ 'rollladenkaesten_' . $wand ] = array(
          'name'          => sprintf( __( 'Rollladenkästen Wand %s', 'wpenon' ), $wand ),
          'typ'           => 'rollladen',
          'modus'         => 'opak',
          'bauart'        => $bauart,
          'baujahr'       => $energieausweis->baujahr,
          'a'             => $flaeche,
          'd'             => 0,
        );
      }
    }
  }
  unset( $data );
}



//Ab hier fängt die Abfrage der IST-Größen aus den angehängten Tabellen an und parallel werden die zugehörigen Referenzgrößen hinterlegt.
// In den Berechnungen werden scheinbar zuerst die "Soll"-Wert-Größen berchnet und dann direkt danach die Referenzgrößen.

// $fxwerte sind das Referenzwerte. $uwerte_reference werden im array Referenz-U-Werte aus dem GeG S. 56 berücksichtigt. Scheinbar keinen Unterschied alt zu neu. Jeoch wir berücksichtigen nur 90° Fenster
// Lichtkuppel etc. scheinbar nicht; der Gesamtenergiedurchlassgard wurde scheinbar nicht berücksichtig.
$fxwerte = array(
  'decke'           => 0.8,
  'kellerwand'      => 0.75, //schlechtester Wert aus Tab c4 18599/T12
  'boden'           => 0.8,// Wert aus Tab c4 18599/T12
);
$uwerte = wpenon_get_table_results( $tableNames->uwerte  ); // hier wird die gesamte Tabelle von Wordpress in diesen Code hochgeladen
$uwerte_reference = array(  
  'dach'            => 0.2,
  'decke'           => 0.2,
  'wand'            => 0.28,
  'boden'           => 0.35,
  'fenster'         => 1.3,
  'rollladen'       => 1.8,
  'tuer'            => 1.8,
);

foreach ( $calculations['bauteile'] as $slug => &$data ) {
  if ( $data['a'] < 0.0 ) {
    $data['a'] = 0.0;
  }

  $data['fx'] = isset( $fxwerte[ $slug ] ) ? $fxwerte[ $slug ] : 1.0; //was wird mit fx hier gemacht?

  if ( $data['typ'] == 'heizkoerpernischen' ) {
    $wand = str_replace( 'heizkoerpernischen_', '', $slug );
    // Uwert korrekt für Nische angesetzt für vereinfachtes Datenaufnahme "Bekanntanzeige AT04.12.2020B1", Falls Nachträgliche Dämmung vorhanden, dann U-Wert der ungedämmten Wand ohne nachträgliche Dämmung dann zweifacher U-Wert alt. !!!!Das sollten wir später anpassen, Zielprogrammierung: ungedämmt bleibt 2 x UI-wert alt, ansonsten neuen U-wert vollständig berechnen!!!!
    $data['u'] = $calculations['bauteile'][ 'wand_' . $wand ]['d'] > 0.0 ? $calculations['bauteile'][ 'wand_' . $wand ]['u'] : 2 * $calculations['bauteile'][ 'wand_' . $wand ]['u'];
    $data['u_reference'] = 2 * $uwerte_reference['wand'];
  } else {
    $uslug = $data['typ'];
    if ( ! empty( $data['bauart'] ) ) {
      $uslug .= '_' . $data['bauart'];
    }

    // Special wish (26.02.2020
	// Wenn Dach beheizt ist, dann immer die Uwerte von dach aus holz. 
    //Sollte an die neue Norm angepasst werden... Herr Esch soll eine Entscheidung treffen. kläre mit Esch
    if ( 'dach' === $slug && 'beheizt' === $energieausweis->dach ) {
	    $uslug = 'dach_holz';
    }

    if ( isset( $uwerte[ $uslug ] ) ) {
      $yearkey = wpenon_immoticket24_make_yearkey( $data['baujahr'], $tableNames->uwerte ); //U-Werte nach Baujahr herausgezogen; wenn kein U-wert in Tab. dann sind mit U=1,0 rechnen
      $data['u'] = $uwerte[ $uslug ]->$yearkey;
    } else {
      $data['u'] = 1.0;
    }

    if ( ( $daemmung = $data['d'] ) > 0 ) {
      $daemmung /= 100.0;
      $data['u'] = 1.0 / ( 1.0 / $data['u'] + $daemmung / 0.04 ); //Das müsste der bereich sein, wo zusätzliche Dämmung berücksichtigt wird , 0,04 Wärmeleitfähigkeit der Dämmung: .
    }
    $data['u_reference'] = isset( $uwerte_reference[ $data['typ'] ] ) ? $uwerte_reference[ $data['typ'] ] : 1.0;
  }
}
unset( $data );

/*************************************************
 * BAUTEILE ERGEBNIS
 *************************************************/

$calculations['huellflaeche'] = 0.0;
$calculations['ht'] = 0.0;
$calculations['ht_reference'] = 0.0;
$calculations['hw'] = 0.0;
$calculations['hw_reference'] = 0.0;
foreach ( $calculations['bauteile'] as $slug => $data ) {
  $calculations['huellflaeche'] += $data['a']; //ecxel datei D27
  //Transmissionswärmebedarf sollte Tab E.3 bis A5 entsrpechen Excel Dtei ab Zeile 21
  $calculations['ht'] += $data['fx'] * $data['u'] * $data['a']; //Excel E27, F27, g27, h27, d27 , Ergebnis [ht] = i27 
  $calculations['ht_reference'] += $data['fx'] * $data['u_reference'] * $data['a'];
  

  
  if ( in_array( $data['typ'], array( 'fenster', 'tuer' ) ) ) {
    $calculations['hw'] += $data['fx'] * $data['u'] * $data['a'];
    $calculations['hw_reference'] += $data['fx'] * $data['u_reference'] * $data['a'];
  }
}
unset( $data );

$calculations['ht'] += 0.1 * $calculations['huellflaeche'];  // 0,1 = Wärmebrückenzuschlag ohne Nachweis, Bei  Bestandsgebäude bleibt 0,1 als Faktor ansonden müsste genaue berechnet werden, dies ist nur möglich nach Begehungstermin oder nach <nachweis des Kunden, dass z.B. Wärmebrücken extern berechnet wurden.

$calculations['ht_reference'] += 0.1 * $calculations['huellflaeche'];

$calculations['ave_verhaeltnis'] = $calculations['huellflaeche'] / $calculations['huellvolumen']; //Excel H,I 11


//Jetzt müsste der Wärmesenken(Lüftung Excel Zeile 74) beginnen 0.04
//No Faktor wo?  Die Summe

$calculations['hv'] = 0.0;
$calculations['hv_reference'] = 0.0;
$hv_mpk1 = 0.8; //Nettovolumenberechnung, Gebäude mit mehr als 3 Vollgeschossen

if ( $energieausweis->geschoss_zahl < 4 ) {
  $hv_mpk1 = 0.76; //Nettovolumenberechnung, Gebäude mit max. 3 Vollgeschosse.
}
//__________________________________________________________________________
// Suchbegriff 1113
//mpk2 entspricht LUftwechsel. muss nach 18599 berechnet werden siehe folgende Formel
//Die  neue Ausgangsformel N = n0 x (1-fwin,1 + fwin,1 x fwin,2)
//Bestimmung der Luftwechselrate nach DIN 18599-12 neu , 
//____________________
// Bestimmung von $n0 //  
// 
//if  $hv_mpk1 * $calculations['huellvolumen'] <1500m³ Tab. 12
//else $hv_mpk1 * $calculations['huellvolumen'] >1500m³ Tab. 14, Hier muss das A/V-Verhältnis berechnet werden für Zuordnung.
//___________________
// Abfrage-Routine
//Luftdichtigkeitstest, Lüftungsanlage Abluft, ZU/Abluft, Fensterlüftung, wenn Lüftunganlage bearfsgeführt Ja/nein, Wärmerückgewinnungsgrad bei Zu-/Abluftanlagen mit Wärmerückgewinnung
// 1) Blower Door Test durchgeführt?
// 2)Fensterlüftung
// 3)Abhängig: Abluft, bedarfsgeführt
// 4) Abhängig: ZU/Abluft fragen Wärmerückgewinnnung, Bedarfsgeführt, Wärmerückgewinnungsgrad effizienz
//_________________________________________________________________________
//
//Suchbegriff 1114, Bestimmung von $fwin,1
//
//if  $hv_mpk1 * $calculations['huellvolumen'] <1500m³ Tab. 13
//else $hv_mpk1 * $calculations['huellvolumen'] >1500m³ Tab. 15, Hier muss das A/V-Verhältnis berechnet werden für Zuordnung.
//__________________________________________________________________________
//Suchbegriff 1115, Bestimmung von fwin,2
//
//Da fwin2 abhängig von Hges (ht+hv) ist und wir für hv fwin2 benötigenwird hierbei über pausch Werte in Abhängikeit des Baujahrs fwin2 bestimmt. 
//Puschale Definition $fwin,2 = 
// 1,006 für Gebäudestand bis einschließlich 2002
// 0,979 für Gebäudebestand ab 2003
//_____________________________________________________________________________
//
//
//Suchbegriff 1116 Berechnung von hv
//
// $Hv = n0 x (1-$fwin,1 + $fwin,1 x $fwin,2)*0.34*$hv_mpk1 * $calculations['huellvolumen']

//
// Dabei ist c*p=0,34 (Standardwert)

//
//Kom. Nach Anhang 5  GEG bis max.6 Vollgeschosse ohne DAchgeschoss; dieser Kom. bezieht sich auf Neugebäude die nach dem vereinfachten Rechenweg berechnet werden sollen

//____________________________________________________________________________
_______________________________________________________________________________

//_________________________________________________________________________________
// ht wird über ProgrmmierZeile 1050 berechnet

//korrekt, kann bleiben muss aber um den max. Wärmestrom erweitert werden 

// $Hges= $calculations['ht'] + $Hv;  // Gesamttranskoeffizient in W/K unter Berücksichtigung der Art der Lüftung Zeile 88 bis 114; scheinabr ohne  Φh,max  (Außlegungstemperaturen)
//________________________________________________________________________________

//Suchbegriff 1117
// Berechnung max. Wärmestrom

// $Qges = $Hges *32; //(Einheit W)

//________________________________________________________________________________


// Suchbegriff 1118 Heizlastberechnung
// defintion formel
// a) Berechnung der Heizlast ohne mechanische Lüftung
// Φh,max = ($calculations['ht']+0,5*$Hv)*32 //max Heizlast ohne Lüftungsanlage

// Prüfung Blower Door Test ja/nein zur Einstufung in Tabelle 12 un 14
// b) Berechnung der Heizlast mit mechanischer Lüftung Unterscheidung von bedarfsgeführt und Nicht-bedarfsgeführt (Tab.12) 
//  1)  Nicht-bedarfsgeführt,, dann nAnl= 0,4, nwrg=jeweils aus der 0% Spalte entnehmen
//      Rücksprache sinnvoll

//      Φh,max = ($calculations['ht']+ $Hv -0,5*$hv_mpk1 * $calculations['huellvolumen'] *0,34*(nwrg,o-nAnl))*32, Formel as der DIN

//      Φh,max = ($calculations['ht']+ $Hv -0,5*$hv_mpk1 * $calculations['huellvolumen'] *0,34*(nwrg,o-0,4))*32


//   2) Bedarfsgeführt, nAnl = 0,35, nwrg=jeweils aus der 0% Spalte entnehmen

//       Φh,max = ($calculations['ht']+ $Hv -0,5*$hv_mpk1 * $calculations['huellvolumen'] *0,34*(nwrg,o-0,35))*32


//   Spezifische Heizlast

//       Φh,max,spez= $calculations['Φh,max']/$calculations['nutzflaeche']
//
//--------------------------------------------------------------------------------------------------------------------------------------

//__________________________________________________________________________________
// Suchbegriff 1119
// Neu berechnet: nur 50
// $calculations['cwirk'] = 50
//
//

// Es wird nur 50 als Standardwert angesetzt  (Jan Formelsammulung!!!!!!!!!!!!!!)
//Suchbegriff 1120, Neue Berechnung von Tau laut Tab. A.6 T 12

//$calculations['tau'] = ($calculations['cwirk']*$calculations['nutzflaeche'] )/ $Hges;
//
//
//_____________________________________________________________________________________


//_________________________________________________________________________________

//________________________________________________________________________________

// Suchbegriff 1122 
//Ab hier wird bilanziert. 
// Bilanzverahren nach DIN 18599 T12 muss hier neu aufgebaut werden

$monate = wpenon_get_table_results( 'monate' ); //Alte Tab. muss noch überarbeitet werden auf Stand 18599 T12 Tab in Teil 10, Stand 21.09.23
//___________________________________________________________________________________________________
//Wichtig mit Sven besprechen !Ob die Routine okay ist!!!!!

//Suchbegriff 1124a Die Nachfolgende Berechnung a) und b) wurden bereits vor der Bilanzierungsroutine berchnet um ßemMax bestimmen zu können. Nun wird die Routine erneut durhlaufen um die Funktion ßem/ßemMax bestimmen zu können
//
//Berechnung der monatlichen AußentemperaturabhängigenBelastung ßem1
//
// a)
//Einfamilienhaus
//  Tab 9 unter Berücksichtigung von Tau und spez. Heizlaste Φh,max,spez. Teilbeheizung bei EInfamilienhäuser α = 0,25 Standard
//  $calculations['monate'][ $monat ]['ßem1'] =  Aus Tab 9 entnehmen EFH α = 0,25 per Definiton, mit Tau (Zeitkonstante) auswählen <= 50h, Tau<=>90, Tau >=130 und interpolieren welche Teil der Tab 9. und dann Φh,max,spez. nutzen und 0-150 Spalte aussuchen und Interpolireen
//
// b)
//Mehrfamilienhaus
//  Tab 11 unter Berücksichtigung von Tau und spez. Heizlaste Φh,max,spez. Teilbeheizung bei Mehrfamilienhäuser = 0,15 Standard
//$calculations['monate'][ $monat ]['ßem1'] =  Aus Tab 11 entnehmen EFH α = 0,15 per Definiton, mit Tau (Zeitkonstante) auswählen <= 50h, Tau<=>90, Tau >=130 und interpolieren welche Teil der Tab 11. und dann Φh,max,spez. nutzen und 0-150 Spalte aussuchen und Interpolireen
//_________--_
//   Schleife vor MOnatsschleife übre alle 12 Monate notwendig um den ßemMax des Jahres herauszubestimmen

//$calculations['monate'][ $monat ]['ßemMaxmonth'] = Maximalwert der 12 Monate aus ßem1

//ßemMax= größter Wert aus ßemMaxmonth'

// Nachfolgende $calculations['monate'] Routinen müssen dann noch unter "foreach ( $monate as $monat => $monatsdaten )" einkopiert werden



//""""""""""""""""""""""""""""""""""""""""""""
//Blianzierung fängt hier an
______________________________________________________________________________________________________
// Suchbegriff 1123

//Berechnung der monatlichen Bilanzinnentemperatur
// a)
//Einfamilienhaus
//  Tab 8 unter Berücksichtigung von Tau und spez. Heizlaste Φh,max,spez. Teilbeheizung bei EInfamilienhäuser α = 0,25 Standard
//  $calculations['monate'][ $monat ]['θih'] =  Aus Tab 8 entnehmen EFH α = 0,25 per Definiton, mit Tau (Zeitkonstante) auswählen <= 50h, Tau=90, Tau >=130 und interpolieren welche Teil der Tab 8. und dann Φh,max,spez. nutzen und 0-150 Spalte aussuchen und Interpolireen
//
// b)
//Mehrfamilienhaus
//  Tab 10 unter Berücksichtigung von Tau und spez. Heizlaste Φh,max,spez. Teilbeheizung bei Mehrfamilienhäuser = 0,15 Standard
//$calculations['monate'][ $monat ]['θih'] =  Aus Tab 10 entnehmen EFH α = 0,15 per Definiton, mit Tau (Zeitkonstante) auswählen <= 50h, Tau=90, Tau >=130 und interpolieren welche Teil der Tab 10. und dann Φh,max,spez. nutzen und 0-150 Spalte aussuchen und Interpolireen
//____________________________________________________________________________________________________



// Suchbergriff 1125
//
//Berechnung von Ph,sink
////Kom. zu Ph,sink: Wärmesenken als Leistung in W 

// NOTE: ßem wird hier verwendet, oben ist aber nur ßem1 definiert. Bitte prüfen, ob das so richtig ist.
//$calculations['monate'][ $monat ]['Ph,sink']= $Qges *(($calculations['monate'][ $monat ]['θih']+12)/32)*$calculations['monate'][ $monat ]['ßem']
//
//______________________________________________________________________________________________________
//
//Suchbegriff 1126
// INternen Wärmequelle nur für Personenhaushalt
//Ph,source, Berechnung der gesamten internen Wärmequellen im Gebäude
//Bestehend aus: Internen Wärmequellen (Haushaltsgeräte)
//  Internen Wärmequellen (Haushaltsgeräte) Code qi unten entfällt damit
//  

//  BUG: Berechnung für Mehrfamilienhäuser geht nicht auf
//  a) Einfamilienhäuser

//  $calculations['monate'][ $monat ]['qi,P'] = 45.0 * $calculations['nutzflaeche'] * $calculations['monate'][ $monat ]['tage']  *0.001 ; //0,001 = /1000 Ziel W auf kWh ändern
// 
//
//  b) Mehrfamilienhäuser

//  $calculations['monate'][ $monat ]['qi,P'] = (90.0 * $calculations['nutzflaeche'] / ('Anzahl Wohneinheiten' * $calculations['monate'][ $monat ]['tage']))*0,001 ;


//_______________________________________________________________________________

//Suchbegriff 1127

//  Berechnung der solaren WärmeGewinne Qs
// fs=0,9 (18599 T2), fv=1,0 (Kom. aus 18599 T10 nicht in T12 enthalten), Fw=0,9,(18599 T2) Ff=0,7(18599 T2) , g=über Max. Werte bestimmen, Esol=Tab.7 Teil12
// qs=A*Ff*Fv*g*Fw*Fs*Esol (kWh), Basisformel



//--------------------------------------------------------------------------------
// Zwischenschritt Suchbegriff 1127a  g-Wert-Bestimmung; ( siehe direkt unten die Bestimmung; sollte dann in funktion.php übernommen werden)

//g-Wert Bestimmung abhängig Fensterart und Baujahr =wpenon_immoticket24_get_g_wert( $data['bauart']

//g = 0,87 einfachverglasung 18599 T2, Tab.8
//g = 0,78 zweifachverglasung     ""
//g = 0,7 dreifachverglasung       ""    
//
//Da in Norm 18599 nur zweifach verglaste Fenster angegeben Voschlag  Untermenuepunkt mit der Engabe " beo 3 fachverglasung" bitte Uw Wert händisch eingeben. Daraus ergibt sich dann für diese Fenster bei 3-fach verglast  g= 0,7, das muss dann im Büro Esch nachgeprüft werden.


// if 3fachverglast than eingabe durch Kunde U-wert 
//    U-Wert von Kunde wird dann mit g  = 0,7 multipiziert werden
//--------------------------------------------------------------------------------


//$solar_gewinn_mpk = 0.9 * 1.0 * 0.9*0,7*wpenon_immoticket24_get_g_wert( $data['bauart']; //Fs, Fw,Ff, g



$calculations['qh'] = 0.0;
$calculations['qh_reference'] = 0.0;
$calculations['qt'] = 0.0;
$calculations['qt_reference'] = 0.0;
$calculations['qv'] = 0.0;
$calculations['qv_reference'] = 0.0;
$calculations['ql'] = 0.0;
$calculations['ql_reference'] = 0.0;
$calculations['qi'] = 0.0;
$calculations['qi_reference'] = 0.0;
$calculations['qs'] = 0.0;
$calculations['qs_reference'] = 0.0;
$calculations['qg'] = 0.0;
$calculations['qg_reference'] = 0.0;
$calculations['monate'] = array();
foreach ( $monate as $monat => $monatsdaten ) {
  $calculations['monate'][ $monat ] = array();
  $calculations['monate'][ $monat ]['name'] = $monatsdaten->name;
  $calculations['monate'][ $monat ]['tage'] = absint( $monatsdaten->tage );
  $calculations['monate'][ $monat ]['temperatur'] = floatval( $monatsdaten->temperatur );
//*********************************************************************************
 
 
  // solare Gewinne Qs
  $calculations['monate'][ $monat ]['qs'] = 0.0;
  $calculations['monate'][ $monat ]['qs_reference'] = 0.0;
  foreach ( $calculations['bauteile'] as $slug => $data ) {
    if ( $data['typ'] == 'fenster' ) {
      $winkel = isset( $data['winkel'] ) ? $data['winkel'] : 90.0;
      $strahlungsfaktor = 0.0;
      if ( $winkel > 0.0 && $winkel < 90.0 ) {
        $prefix = 'w_' . $data['richtung']; // w_n90
        $str30 = $prefix . '30';
        $str45 = $prefix . '45';
        $str60 = $prefix . '60';
        $str90 = $prefix . '90';
        $strahlungsfaktor = wpenon_interpolate( $winkel, array(
          array(
            'keysize'   => 0,
            'value'     => $monatsdaten->w_0,
          ),
          array(
            'keysize'   => 30,
            'value'     => $monatsdaten->$str30,
          ),
          array(
            'keysize'   => 45,
            'value'     => $monatsdaten->$str45,
          ),
          array(
            'keysize'   => 60,
            'value'     => $monatsdaten->$str60,
          ),
          array(
            'keysize'   => 90,
            'value'     => $monatsdaten->$str90, // w_n90
          ),
        ) );
      } elseif ( $winkel >= 90.0 ) {
        $str90 = 'w_' . $data['richtung'] . '90';
        $strahlungsfaktor = $monatsdaten->$str90;
      } else {
        $strahlungsfaktor = $monatsdaten->w_0;
      }
      // T148:T159
      //Zeilennummer function

//$calculations['monate'][ $monat ]['qs'] += $strahlungsfaktor * $solar_gewinn_mpk * $data['a'] * 0.024 * $calculations['monate'][ $monat ]['tage']; Neue Berechnung; Programmierzeile direkt unten enflällt
 


// Suchbegriff 1128 Zusammenfassung der internen Wärmengewinne 

//Hier fehlt der Interne Gewinn durch Heizung- und & Wasserzeugung
 //___________________________________ 
//1)  Berechnung interne Wasseerwärmegewinne für Wasser
  // qiwasser=(An/SumWohneinheiten)*qwb*Faw*(dmth/365)
  // $qwb=jährlicher Nutzwasserbedarf für Trinkwasserbedarf=Tab.19 T12 in Abhängigkeit Anfg,NWohnungen= (Anfg/SummWohneinheiten)
  //dmth= Tage des jeweiligen Monats, Tab. 8 T10

  // Faw=Tab.142 oder 143 T12 in Abhängigkeit Abfrage: 1) Dezentrale und zentrale Wasserversorgung
  //                                                   2) Liegt eine Warmwasserspeicher vor (Bemerkung im Formular Warmwasserspeicher Auswahl ja/nein vorgeben) if Tab. 141 keine Speicherung than Spalte Spicherung = unebheizt, aus Tab 143-141
  //                                                   3) ob Trinkwasserverteilung mit Zirkulation oder ohne; Wenn dezantrale Wasserversorgung dann ohne Zirkulation
  //                                                   4) Wenn dezantrale Wasserversorgung dann ohne Zirkulation (Tab 142, T12)
  //                                                      a)Liegt  die Anlage im unbeheizen Bereich = 0,335
  //                                                      b)liegt ihre Anlage im behiezen Bereich =  0,647
  //                                                      c)Liegt das Verteilsystem im Beheizen Breich und der Rest  im unbeheizten Bereich= 0,451
//                                                        d)if Tab. 141 keine Speicherung than Spalte Spicherung = unebheizt, aus Tab 143-141, größer Wert
  //                                                   5) Wenn zentral Wasserversorgung dann mit Zirkulation (Tab 143, T12)
  //                                                      a)Liegt  die Anlage im unbeheizen bereich = 0,815
  //                                                      b)liegt ihre Anlage im behiezen bereich =  1,554
  //                                                      c)Liegt das Verteilsystem im beheizen Breich und der Rest im unbeheizten Bereich= 1,321
  ////                                                    d)if Tab. 141 keine Speicherung than Spalte Spicherung = unebheizt, aus Tab 143-141, größer Wert

  // Im Formular wird die Anzahl der Wohneinheiten (Wohnungen) definiert (abgefragt) werden; Dieser  wird unten berücksichtigt. $NWohnungen

// Berechnung des monatlichen Wärmebedarfs für Warmwasser (kWh)
// $calculations['monate'][ $monat ]['QWB'] = ($calculations['nutzflaeche']/$NWohnungen)*$qwb*($calculations['monate'][ $monat ]['dmth']/365);

// Berechnung der internen Wärmequelle infolge von Warmwasser.
 // $calculations['monate'][ $monat ]['qiwasser'] = $calculations['monate'][ $monat ]['QWB']*$Faw;
//____________________________________________
//Suchbegriff 1129

//2) Berechnung der innernen Wärmegewinne für Heizung 
//




//----------------------------------
////[$calculations['monate'][ $monat ]['Ph*,sink']= [$calculations['monate'][ $monat ]['Ph,sink'] - ($calculations['monate'][ $monat ]['qi,P'] + (0.5*$calculations['monate'][ $monat ]['qs'])*fum)
// If [$calculations['monate'][ $monat ]['Ph*,sink'] <=0 than [$calculations['monate'][ $monat ]['Ph*,sink'] = 0, else [$calculations['monate'][ $monat ]['Ph*,sink'];

// Qisource= (Ph*,sink*(ßem/ßemmax)*fa-h)/fum //
//$calculations['monate'][ $monat ]['fum']=1000/(24*$calculations['monate'][ $monat ]['dmth'])
//---------------------------------- 
 
// fa-h Bestimmung
// Auslegungstemperaturen festlegen durch Eingabe im Formular, Diskussion notwendig: ob eine genauere Beschreibung wie z.B. "Heizkörper, Niedertemperatur" notwendig ist.
//                                                       a) 90/70°C, Heizkörper, Heizkessel nicht mehr zulässig ohne Regelung
//                                                       b) 70/55°C Heizkörper, Niedertemperatur
//                                                       c) 55/45°C, Heizkörper, Brennwert
//                                                       d) 35/28°C, Wand- oder Fußbodenheizung
//
// 1)Frage an Kunden ob mehr als ein Übergabesystem vorhanden? Z.B. Heizkörper +  Fußbodenheizung/Wandheizung 
//
// 2) Sind beide Übertragungssystem vorhanden: Abfrage in welchem Verhältnis (Flächenverhältnis) (z.B 30% zu 70%)
//
// 3) für jede Übergabesystem muss aus Tabelle 141 der jeweilige fa-h(Fußbodenheizung sowiefür fa-h(Heizkörper) Wert bestimmt werden entsprechend seiner Systemtemperaturen und lage der Leitungen etc.

// 4) fa-hges= faktor aus Tab141 für Heizkörper*Prozentangabe des Übergabsystems + faktor aus Tab141 für Fußboden/Wandheizung*Prozentangabe des Übergabsystems

//________________________
// Tabelle 141 Auswahlverfahren fa-h:
//  a) Verteilleitung, Speicherung, Erzeuger im Kaltbereichc :
//                                                            90/70°C  =  0,039
//                                                            70/55°C  =  0,028                                     
 //                                                           55/45°C  =  0,02  
//                                                            35/28°C  =  0,008   
//  b) Verteilleitung beheizt. Speicherung unbeheizt Erzeuger im unbebereich :
//                                                            90/70°C  =  0,078
//                                                            70/55°C  =  0,055                                     
 //                                                           55/45°C  =  0,038  
//                                                            35/28°C  =  0,015   
//  c) Verteilleitung beheizt, Speicherung beheizt, Erzeuger beheizt :
//                                                            90/70°C  =  0,123
//                                                            70/55°C  =  0,099                                     
 //                                                           55/45°C  =  0,082  
//                                                            35/28°C  =  0,057  
//  d) Verteilleitung beheizt. Speicherung unbeheizt und Erzeuger im beheizt:
//                                                            90/70°C  =  0,118
//                                                            70/55°C  =  0,095                                    
 //                                                           55/45°C  =  0,077 
//                                                            35/28°C  =  0,053
//_____________________________________________________________________
// 


 // $calculations['monate'][ $monat ]['qiHeizung'] = [$calculations['monate'][ $monat ]['Ph*,sink']*( $calculations['monate'][ $monat ]['ßem']/$ßemMax)*fa-h/$calculations['monate'][ $monat ]['fum'];
 // 
//// Die Summe der Wärmequellen als Energie kWh
  //// $calculations['monate'][ $monat ]['qi'] =$calculations['monate'][ $monat ]['qi,P']+ $calculations['monate'][ $monat ]['qiwasser']+$calculations['monate'][ $monat ]['qiHeizung']+$calculations['monate'][ $monat ]['qs']

// Die Summe der Wärmequellen als Leistung in W
// $calculations['monate'][ $monat ]['pi'] = $calculations['monate'][ $monat ]['qi']*1000/(24*$calculations['monate'][ $monat ]['tage'])
/______________________________________________________________________________________


//_____________________________________________________________________________________
// Suchbegriff  1130

//neue Korrekturfaktoren
// Bestimmung des Korrelturfaktors ym
// ym=phsource/phsink
// $calculations['monate'][ $monat ]['ym']= $calculations['monate'][ $monat ]['pi']/$calculations['monate'][ $monat ]['Ph,sink']


//_______________________________________________________________________________
//Suchbegriff 1131
// Ausnutzungsgrad nm
//nm ist abhängig von Tau und ym
// 
// nm aus Tabelle 18 Ausnutzungsgrad für tandardzeitkonstanten entnehmen
// Interpolieren Waagerechte ist Tau und Senkrechte ist ym
// 
// $calculations['monate'][ $monat ]['nm'] = Interpolieren Waagerechte ist Tau und Senkrechte ist ym [auf monat bezogen]
//___________________________________________________________________________________
//Suchbegriff 1132
//Zwischenberechnung; Bestimmung von k = (1-nm*ym)

// if k <= 0 dann ist k = 0, else k=berechneter Wert
//$calculations['monate'][ $monat ]['k'] = (1-$calculations['monate'][ $monat ]['nm']*$calculations['monate'][ $monat ]['ym'])
//_______________________________________________________________________________
//Suchbegriff 1133
// ßhm=ßem*(1-nm*ym)=ßem*k
// $calculations['monate'][ $monat ]['ßhm']= $calculations['monate'][ $monat ]['ßem']* $calculations['monate'][ $monat ]['k']
//$calculations['ßhma']+=$calculations['monate'][ $monat ]['ßhm']

______________________________________________________________________________

// Suchbegirff 1134
//Bestimmung der Heizstunden thm

// if  
//    $calculations['monate'][ $monat ]['ßhm']> 0,05 than
//    $calculations['monate'][ $monat ]['thm'] =$calculations['monate'][ $monat ]['tage'] *24
// else 
//   
// $calculations['monate'][ $monat ]['thm'] =($calculations['monate'][ $monat ]['ßhm']/0,05)*$calculations['monate'][ $monat ]['tage']*24
//
//________________________________________________________________________________

// Suchbegriff 1135

// Bestimmung des Nutzenergiebedarfs [kWh]
// qh=Ph,sink*(1-nm*ym)*thm/1000 = Ph,sink* k*thm/1000 
//
//$calculations['monate'][ $monat ]['qh']=$calculations['monate'][ $monat ]['Ph,sink']*$calculations['monate'][ $monat ]['k']*$calculations['monate'][ $monat ]['thm']/1000
//_____________________________________________________________________________________
// Suchbegriff 1136, Ermittlung der Summernwerte über 12 Monate
// Berechnung der Jahresbeträge Summe der Monatswerte

// Heizwärmebedarf/ Nutzenergie im Jahr
// $calculations['qh'] += $calculations['monate'][ $monat ]['qh']; 

//  interne Wärmequellen infolge Heizung +WW + Personen + Solar kWh/a
//  $calculations['qi'] += $calculations['monate'][ $monat ]['qi'];

// Summe der Heizstunden / jahr
// $calculations['thm'] += $calculations['monate'][ $monat ]['thm'];

// Berechnung des jährlichen Warmwasserbedarfs

//$calculations['QWB'] +=$calculations['monate'][ $monat ]['QWB'];


// Ende des Nutzenergiebedarfs .... 

// ============================================================================
// Definition der Wärmeerzeuger und Übergabesysteme sowie Deckunganteil des jeweiligen Erzeugers (Öl/Wärmepumpe etc.)
//  Abfrage Kunden:

//       - welche Erzeuger liegen vor (max. drei Stück)
//          $nEr= // 1 bis 3 Stück;
//          $TypEr=  (z.B. Niedertemperaturkessel etc.);
//          $TypErTräger= (z.B. Öl, Gas, Pellet, Holz, Hackschnitzel; Strom, Fernwärme); //Bei Pellet, Holz, Hackshcnitzel ist gleich 
//          $TypErAnteil= Anhaben ind % (1. 30%, 2. 50%, 3. 20%);
//

//        - Solarthermische Anlage vorhanden
//        
//      if nein than
//              Energieversorgung durch thermische Solaranlage = 0;
//         else 
//            if ja than 
//               if Solar Anlage  nur Brauchwasser
//                       TWW
//               if  Solar Brauchwasser und Heizung    
//                       TWW + Hzg
//-------------------------------
//
//   Abfrage für TrinkwassererwärungsTypen
//
//          
//
//
//
//  Anlagentechnik
// Suchbegriff 1137
// Neuer ProgrammierCode


// Berechnung der monatlichen rechnerischen Laufzeit (Heizung)
//
//Berechnung laut Norm: ithrl= thm*fwe*trl
//
// thm wurde berechnete 
// fwe gilt als Standardwert =0,042 (muss mit DIBt geklärt werden)
// trl = Monatswerte abhängig von EFH und MFH Tab. E.11 DIN 18599-12 dort zu  entnehmen // 18599 T10 Tab.4 MFH Absenkung reduzierte Betrieb, EFH Abschlatung = 0 % Betrieb, Laufzeit T5 Glg. 24                          (muss mit dem "dibt" geklärt werden)

 ////------------------
//Berechnung der monatlichen Laufzeit:

// if Einfamilienhaus trl = 17 than
// $calculations['monate'][ $monat ]['ithrl'] = $calculations['monate'][ $monat ]['thm'] * 0,042 * $calculations['monate'][ $monat ]['trl']


// if MFH  than 
//   Kom. Berechnung von trl nach DIN18599/ T5 Kab. 5.4.1 = Trl = 24-flna*(24-thoptday)= da mittlere Potdam gleich beibt kann $calculations['monate'][ $monat ]['trl'] nach DIN 18599 T12 Tab A-13 entnommen werden 
//  $calculations['monate'][ $monat ]['ithrl'] = $calculations['monate'][ $monat ]['thm'] * 0,042 * $calculations['monate'][ $monat ]['trl']


// 
//BErechnung der jährlichen Laufzeit
//
//$calculations['ithrl'] += $calculations['monate'][ $monat ]['ithrl']


// solange keine Klärung über DIBt nehmen wir für EFH und MFH 17 Std. an
//-------------------
// Suchbegriff 1138
// Berechnung der Wirkungsgrade der Wärmeverluste (Aufwandszahlen) von Übergabe, Verteilung, Speicherung,Erzeuge
//---------
/
// ----------------------------------------------------             
// ehce Aufwandszahl der Heizungsübergabe //$UebergabeAufwandszahl['ehce']//
                                           
//    Für Bestimmung folgende Abfragen nötig??=  
// -----------------------------------------------------------
////                                           -Elektroheizungsflächen
//                                              ehce0=  
//                                             1) if "Heizflächen überwiegend auf der Außenwand montiert" (Abfrage Kunde), than Tab.24T12 Außenwandbereich-Werte nutzen else Innenwandbereichwert nutzen
//
//
//                                             2) if "Elektro-Direktheizung mit Raum-Regelung" Than  Außenwand Tab24/T12 ehce0=1.066
//                                                if "Speicherheizung ungeregelt" than Außenwand Tab 24/T12 ehce0=1.161
//                                                if "Speicherheizung mit mit Raumregelung und Witterungeführter Regelung" than Außenwand Tab24/12 ehec0=1.089
//
////                                             2) if "Elektro-Direktheizung mit Raum-Regelung" Than  InnenWand Tab24/T12 ehce0=1.089
//                                                if "Speicherheizung ungeregelt" than InnenWand Tab 24/T12 ehce0=1.185
//                                                if "Speicherheizung mit mit Raumregelung und Witterungeführter Regelung" than InnenWand Tab24/12 ehec0=1.113
//  
//                                              ehce1= 0,018, ehce2=0, ehce3=0, ehce4=0, ehce5=0, ehcehyd=0 // Bedingung Tab.24, T12 max Raumhöhe mit 4m angeben
// NOTE: Wenn Deckenhöhe > 4m dann soll sich Energieberater melden. Abfrage im Frontend.
//                                             3) if Deckenhöhe > 4m than nach Abschluss der Eingabenmitteilung, dass sich der Energieberater persönlich meldet
//   
//    -----------------------------------------------------
//                                              -Heizkörper
//                                              ehce0=1,042, da P-Regler angenommen konform mit Tab21 18599 T12
//                                              ehce1= Tab 21 18599 T12, Abhängig von 1- bzw. 2 Rohrsystem sowie Vor- und Rücklauftemperatur sieh Tab., Übertemperatur bei Zweisystem oberen 4 Werte ansetzen bei 1-Rohrsystem die unteren zwei Werte
//                                              ehce2= Tab 21 18599 T12, Anhängig des Standortes der Heizkörper im Gebäude (innenwand=0,039, Außenwand=0,009), Die Norm gibt hierbei keinen genaue Herangensweise vor. Wir setzten daher als Vorschlag ehce2 mit 0,009 für Außenwandmontage fest; Da in der Norm auch bei der Berechnung der Heizköpernischen in der Außemwand berücksichtigt werden , mit Esch abgesprochen                                                  
//                                              ehce3= Tab 21 18599 T12, intermetierender Betrieb Heizungebetrieb = ehce3 = 0, da hier von uns der schlechtere Wert angesetzt wird
//                                              ehce4= Tab 21 18599 T12, erhöhte stahlung  ehce4=0
//                                              ehce5= Tab 21 18599 T12, hier wird nach "Smart Home-Lösungen" gefragt. Das Optimierungspotzenzial,welches die DIN vorgibt ist sehr groß...Abfrage aber komplex für das Verstädnis des Kunden. Wir können aber ehce 5 = -0,030 setzen für manuele Betätigung der Einzelraumregelsystem ohne weitere NAchfrage  

//                                              ehcehyd = 
//                                            1)if "Einrohrsytem" than ehcehyd = 0.042
//                                              if "Zweirohrsystem" than ehcehyd = 0.036
//                                              else                                 
//                                              ehcehyd = 0.042   // Wenn Kunde keine Angaben
//                                             3) if Deckenhöhe > 4m than nach Abschluss der Eingabenmitteilung, dass sich der Energieberater persönlich meldet

//-----------------------------------------------------
//
////                                           -Flächenheizung Fußboden/Wandheizung
//                                              ehce0=1,042, da P-Regler angenommen konform mit Tab21 18599 T12
//---------------------------------------------------
//                                              ehce1= Tab 22 18599 T12,
//
//                                           1) if "Fußbodenheizug" than ehce1= 0.021
//                                              if "Wandheizung" than ehce1= 0.045
//                                              if "Deckenheizung" than ehce1= 0.063
//                                               else  ehce1=0.063

//                                           2) if "Fußbodenheizug" than ehce1= 0.021
//                                              if "Wandheizung" than ehce1= 0.045
//                                              if "Deckenheizung" than ehce1= 0.063
//                                               else  ehce1=0.063
//---------------------------------------------------
//                                              ehce2= Tab 22 18599 T12,  

//                                           1) if "Mindestdämmung vorhanden" than ehce2= 0.015
//                                              if "Mindestdämmung nicht vorhanden" than ehce2= 0.042
//                                              if "keine Ahnung" than ehce2=0.042
//                                              else  ehce2=0.042                         
//--------------------------------------------------------------------     

//                                               
//                                              ehce3=0.0 Tab 22 18599 T12, intermetierender Betrieb Heizungebetrieb = ehce3 = 0, da hier von uns der schlechtere Wert angesetzt wird
//----------------------------------------------------------------------

//                                              ehce4= 0.0 Tab 22 18599 T12, ehce4=0
//-----------------------------------------------------------------------
//
//                                              ehce5= Tab 22 18599 T12, 
//
//                                              ehce5= -0.03,  Tab 22 18599 T12, hier wird nach "Smart Home-Lösungen" gefragt. Das Optimierungspotzenzial,welches die DIN vorgibt ist sehr groß...Abfrage aber komplex für das Verstädnis des Kunden. Wir können aber ehce 5 = -0,030 setzen für manuele Betätigung der Einzelraumregelsystem ohne weitere NAchfrage  
//-------------------------------------------------------------------------


//                                              ehcehyd = 
//                                            1)if "Einrohrsytem" than ehcehyd = 0.042
//                                              if "Zweirohrsystem" than ehcehyd = 0.036
//                                              else                                 
//                                              ehcehyd = 0.042   // Wenn Kunde keine Angaben
//                                             3) if Deckenhöhe > 4m than nach Abschluss der Eingabenmitteilung, dass sich der Energieberater persönlich meldet

//-------------------------------------------------------------------------
//                                                                                  
//                                             -Umluftheizung 
//                                              ehce1= 0, ehce2=0, ehce3=0, ehce4=0, ehce5=0, ehcehyd=0// Bedingung Tab.24, T12 max Raumhöhe mit 4m angeben
//
//                                              ehce0=
//                                           1)   if "Mauelle Steuerung von Raumfühlern" than ehce0=1.066
//                                                if "automatische Regelung" than ehce0=1,042
//                                                 else ehce0=1.066
//
//                                           2)   if Deckenhöhe > 4m than nach Abschluss der Eingabenmitteilung, dass sich der Energieberater persönlich meldet
//                                              
//                                           3) if Deckenhöhe > 4m than nach Abschluss der Eingabenmitteilung, dass sich der Energieberater persönlich meldet//                                               
//-------------------------------------------------------------------------

// Auswertung unten
//   
//      $UebergabeAufwandszahl['ehce'] = ehce0+ (ehce1+ehce2+ehce3+ehce4+eche5) + ehcehyd
//  
//
////    $ßhce=($calculations['qh']/($calculations['thm']*$Φh,max))*1000; //mittlere Belastund bei Übergabe der Heizung

//      Flächenbezogene leistung der Übergabe der Heizung
//      $qhce=(Φh,max/ $calculations['nutzflaeche'])*$UebergabeAufwandszahl['ehce']



//----------------------------------------------------------------
// TODO: Verteilung Heizung
// Suchbegriff 1139
// NOTE: Berechnung der Wirkungsgrade der Wärmeverluste (Aufwandszahlen) von  Verteilung ehd// Bemerkung: Übergabestationen werden vorerst nicht berücksichtigt 
//-----------------------------
// Unten Berechnung von $ehd0
//    if EFH && Heizkörper  && Fußbodenheizung && Wandheizung 
//      case 1 unbeheizt && 90/70  $ehd0=1,1
//      case 2 uneheizt && 70/55   $ehd0=1,074
//      case 3 uneheizt && 55/45   $ehd0=1,055
//      case 4 uneheizt && 35/28   $ehd0=1,028
//      case 5 beheizt && 90/70    $ehd0=1,099
//      case 6 beheizt && 70/55   $ehd0=1,070
//      case 7 beheizt && 55/45   $ehd0=1,049
//      case 8 beheizt && 35/28   $ehd0=1,019

//    if MFH && Heizkörper  && Fußbodenheizung && Wandheizung 
//      case 1 unbeheizt && 90/70  $ehd0=1,085
//      case 2 uneheizt && 70/55   $ehd0=1,063
//      case 3 uneheizt && 55/45   $ehd0=1,047
//      case 4 uneheizt && 35/28   $ehd0=1,024
//      case 5 beheizt && 90/70    $ehd0=1,085
//      case 6 beheizt && 70/55   $ehd0=1,060
//      case 7 beheizt && 55/45   $ehd0=1,042
//      case 8 beheizt && 35/28   $ehd0=1,016

//    if EFH && MFH && Umfluftheizung 
//      case 1 unbeheizt && 90/70  $ehd0=1,051
//      case 2 uneheizt && 70/55   $ehd0=1,038
//      case 3 uneheizt && 55/45   $ehd0=1,028
//      case 4 uneheizt && 35/28   $ehd0=1,014
//      case 5 beheizt && 90/70    $ehd0=1,051
//      case 6 beheizt && 70/55   $ehd0=1,036
//      case 7 beheizt && 55/45   $ehd0=1,025
//      case 8 beheizt && 35/28   $ehd0=1,010
//      case else     
//   case end
// Kommentar zu oben $ehd0 aus Tab 30, Tab 31, T12, hierzu wird 
//               a)in Anhängigkeit ob Verteilung im beheizen (Tab31) oder unbeheizen(Tab30) liegt
// 
//               b) in Abhänigkeit von Rohrnetztyp (Verteilnetz), EFH = Etagenringtyp, MFH = Streigstragtyp ( Bedingungen aus Bundesanzeiger Bekanntmwchung BAnz AT 04.12.2020 B1)
//               c) In Anhängigkeit der Systempemeraturen
//-----------------------------

   



//
//Da es keine Aufwandszahl für Vereilung bei Elektroheizung daher ist ehd_Elektroheizung=1 anzusetzen
// If "Elektroheizung" than ehd=0 else.....// If " .. !=Elektroheizung= "... (unter if-Bedingung) (Aufbau einer inneren Schleife)
//   If " .. !=Elektroheizung= "... (unter Case or if-Bedingung) (Aufbau einer inneren Routine)
//    case 1   "keine hydraulischer Abgleich vorhanden" than $fhydr=1.06;
              //    $ßhd  = $ßhce * $ehce *$fhydr; 
//    case 2 "hydraulischer Abgleich vorhanden" than $fhydr=1.02;
//                  $ßhd  = $ßhce * $ehce *$fhydr
//    case 3 "Bei luftheizungen" than $fhydr=1.0;
//                  $ßhd  = $ßhce * $ehce *$fhydr
//    case 4 "Bei Elektroheizung" than $fhydr=0.0 ;
//    //             $ßhd  = $ßhce * $ehce *$fhydr
///  case else
//  case end
//
// --------------------------
////    if EFH && Heizkörper  && Fußbodenheizung && Wandheizung && unbeheizt
//         goto Tabelle 32, I Etagenringtyp  spaltenweis interpolieren auf Basis von $ßhd
////    if MFH && Heizkörper  && Fußbodenheizung && Wandheizung && unbeheizt
//         goto Tabelle 32 Steigstrangtyp III spaltenweis interpolieren auf Basis von $ßhd
////    if EFH && Heizkörper  && Fußbodenheizung && Wandheizung && beheizt
//         goto Tabelle 32, I Etagenringtyp  spaltenweis interpolieren auf Basis von $ßhd
////    if MFH && Heizkörper  && Fußbodenheizung && Wandheizung && beheizt
//         goto Tabelle 32 Steigstrangtyp III spaltenweis interpolieren auf Basis von $ßhd
////    if MFH && EFH && Umluftheizung && unbeheizt
//         goto Tabelle 32 Strahlungs & Luftheizung VI spaltenweis interpolieren auf Basis von $ßhd
////    if MFH && EFH && Umluftheizung && beheizt
//         goto Tabelle 32 Strahlungs & Luftheizung VI spaltenweis interpolieren auf Basis von $ßhd

//           Kommentar zu oben $fßd aus Tab32, T12
////               a)in Anhängigkeit ob Verteilung im beheizen (Tab32) oder unbeheizen(Tab32) liegt
//                 b) in Abhänigkeit von Rohrnetztyp (Verteilnetz), EFH = Etagenringtyp, MFH = Streigstragtyp ( Bedingungen aus Bundesanzeiger Bekanntmwchung BAnz AT 04.12.2020 B1)
//                 c) In Anhängigkeit der Systempemeraturen
//                 d) In Anhängikeit von $ßhd, Intepolierne notwendig (Tab 32,T12)
//-------------------------------
//        
//        $ehd1=$ehd0*$fßd


//        $ehd=1+($ehd1-1)*(50/$qhce)

//        $ehdkorr= 1 + (ehd-1)*(8760/$calculations['ith,rl'] )      //Gl.A13ehdo= Aufwandszahl für Heizungsverteilung im Referenzfall (Tab 30, Tab 31 aus T12)
//                   // ehd0= Aufwandszahl für Heizungsverteilung im Referenzfall (Tab 30, Tab 31 aus T12)


//        Hier verlasse ich die if case Schleife


//  else  $ehdkorr = 1                 //Wert, wenn die Schleife übersprungen wird und wir eine Elektroheizung haben
//

//--------------------------------------------------------------------------------

//// TODO: Speicherung Heizung (Pufferspeicher)
// Suchbegriff 1140
// $qwb 

// NOTE: Berechnung der Nennleistung


////// Abfrage ob  die Warmwassererzeugung direkt über den Wärmeerezeuger (Heizkessel) erfolgt  Frontend : ja/nein
//  if "Nur Heizung über Heizungsanlage" than
//      $Pwn =0 
//  if "Heizung und Warmwasser über Heizungsanlage" && $calculations['nutzflaeche'] <= 5000m² than
//       $Pwn = Trinkwasserkesselnennleistung = Tab.139 in Anhängigkeit von Gebäudenutzfläche      $calculations['nutzflaeche']     und    $qwb
//  if  "Heizung und Warmwasser über Heizungsanlage" && $calculations['nutzflaeche'] > 5000m² than             
//       $Pwn=0.42*(($qwb*$calculations['nutzflaeche'])/(365*0,036))^0,7    
//  else
//          $Pwn=0.42*(($qwb*$calculations['nutzflaeche'])/(365*0,036))^0,7   
//

//-------------------------
//  if $Φh,max > $Pwn than           // Die nächsten 4 Zeilen besagen den MAx Wert entweder Heizlast oder Pwn
//    $Φpwn = $Φh,max 
// else
//    $Φpwn = $Pwn

/
//                          
//
//-----------------------------
// "Ja" = Heizung und Trinkwarmwasser für zentrale Kesselanlage

//  If "ja"  != "Wärmepumpe" than  // $fz = 1,5 bei Bestandsanlagen, /T12, Seite39; unser Kundenstamm, die Berechnung Pn=fz*max.[Φh,max;Pwn] nur für alle Heizungen außer Wärmepumpen
//   
//     $Pn=$fz*[$Φpwn]  // alles außer Wärmepumpe 

//  If "Nein"  != "Wärmepumpe" than  // $fz = 1,5 bei Bestandsanlagen; unser Kundenstamm, die Berechnung Pn=fz*max.[Φh,max;Pwn] nur für alle Heizungen außer Wärmepumpen, Hierbei wird oben bei der Ermittlung von $Φpwn = $Pwn festgelegt, da $Φh,max = Leitlast ist und $Pwn=0 ist wird automatisch die Heizungsheizlast angesetzt.
//   
//     $Pn=$fz*[$Φpwn]  // alles außer Wärmepumpe  
  
//  If "ja" && "Wärmepumpe" 
//      $Pn=1,3* $Φh,max         // für  Wärmepumpe Gl.29
/
////  If "Nein" && "Wärmepumpe" 
//      $Pn= $Φh,max         // für  Wärmepumpe Gl.29
//
//   else ???
//----------------------------------------------
//
//ßhs=mittlere Belastung für Speicherung, ßhd=mittlere Belastung für Vereilung, ehdkorr = Aufwandszahl der Verteilung für Heizung
// Suchbegriff 1141

// $ßhs=$ßhd*$ehdkorr


//---------------------------
// NOTE: Korrektrufaktor mittlere Belastung des Pufferspeichers fßhs
//$fßhs= Tab52, Wert muss interpoliert werden (Interpolation nur über Spalte $ßhs)
// Abhängigkeit  Vorlauftemperatur, $ßhs, Aufstellraum beheizt/unbeheizt (Aufstellort Puffer=Aufstellort Kessel)

//-----------------------------------------

// NOTE: Korrekturfaktor für beliebige mittlere Berlastung und Laufzeit der Heizung $fhs

//$fhs=$fßhs*$calculations['ith,rl']/5000


//------------------------------------------
// NOTE: Berechnung ges Pufferspeichervolumens.
// BAnz AT 04.12.2020 B1 definiert, dass bei gas- und ölbetriebenen Kesseln sowie Fernwärme keine Pufferpeicher vorgesehen werden. Bei Wärmepumpen, solare Heizungsunterstüzung, Biomasse und KWK  werden Puffer berücksichtig

//Abfrage: Pufferspeicher bekannte Ja/Nein
// If "Ja" than "Eingabe Kunde Wieviel Liter"
 //     $Vs= Angabe in Liter
//    else 
        //  if "Wärmepumpe" than
//           $Vs=9.5*$Pn; // Vs Liter und Pn in kW
//          if "Biomassekessel" than
//             $Vs=50*$Pn;
//          if "Wärmepumpe && Biomassekessel" than
 //               $Vs=50*$Pn;
//            else 
//                 $Vs = 0;

// Die Norm definiert in T12 Seite 96, dass es max. 2 Speicher gibt. Wobei der erste eine max. Größe von 1500Liter hat und der zweite die Differenz übernimmt. Die zugehörige Tabelle 50 und 51 haben jedoch nur Volumenangaben bis max. 1500Liter. Ergo können wir streng nach Norm nur 3000Liter als max. Puffervolumen in dieser Berechnung ansetzen.   
// Es feht hierbei der formeltechnische Ansatz über die Norm. Rechtlich ergit aus T12 nur Berechnungsgrundlagen für 2 Puffer mit max. a 1500L = 3000Liter


//------------------------------
//
// NOTE: Wärmeverluste Pufferspeicher  kWh/a,  $Qhs0 aus Tab. 50 & 51, Wärmeverluste im Reverenzfall
//
//
// if $Vs > 1500Liter than
//   $Vs1 = 1500Liter;
//    $Vs2= $Vs - $Vs1
// else 
//    $Vs1 = $Vs
//    $Vs2= 0

// if  $Vs2 > 1500Liter than
//       $Vs2 = 1500Liter
// else
//     $Vs2 = $Vs2

//  $Qhs0Vs1 = Vorlauftemperatur, Speichervolumen $Vs1 (in der Spalte  Speichervolumen wird interpoliert)
//  $Qhs0Vs2 = Vorlauftemperatur, Speichervolumen $Vs2 (in der Spalte  Speichervolumen wird interpoliert)
//-------------------------------
//
// Reale Wärmeverlust Pufferspeicher über das Jahr kWh/a,  $Qhs 
//// 
// $Qhs=$fhs*($Qhs0Vs1+$Qhs0Vs2)

//-----------------------------------------
// NOTE: Bestimmung von ehs, ehs= Auwandszahl für Pufferspeicher
//
//   if "Wärmepumpe"&&"Biomassekessel" than
//         $ehs = 1 + $Qhs/($calculations['qh']*$UebergabeAufwandszahl['ehce']*$ehdkorr)
//   else 
//        $ehs = 1   //Für gas- und ölbetriebe Kessel sowie Elektroheizung. Bei Fernwärme wird kein Heizungspufferspeicher nach Bundesanzeiger 04.12.2020 B1. 


//   


//________________________________________
// // Suchbegriff 
// Speichervolumen thermische Solaranlage Pufferspeicher, Heizungserzeugung
//Die Berechnung von ehs wie oben oben beschrieben. Bei der Nutzung einer Solarthermieanlag (Heizung + TWW und nur TWW(bivalent)) erfolgt im Bereich der Bereich der Bereichnung von ews



//_______________________________________________________________________________
// Suchbegriff 1142
// TODO: Trinkwarmwasser
// NOTE: Bestimmung der Aufwandszahlen;
//----------------------------------
//Übergabe
// ewce ist nach Tab. 29, T12 ein Standardwert
//

//   $ewce = 1.0;

//--------------------------

// Verteilung

// ewd, nach BAnZ 04.12.2020 B1, Standardangaben  sind beim Verteilnetz "SteigenstandTyp", Zitkulation kann angesetzt werden (damit wird ohne Zirkulation vernachlässigt!) 
//
//  If "dezentrale Versorgung" than
//     $ewd0 = 1.193;
//  if "ungeheizt" than
//      $ewd0=2.290;
//  if "beheizt" than;
//      $ewd0=2.252;
//  else ????
//
//  $ewd= 1+($ewd0-1)*(12.5/$qwb)
//
//-----------------------------
//   if "Solaranlage = Nein" than  // Äußere Schleife
// NOTE: Speicherung
//nach BAnZ 04.12.2020 B1 müssen wir nur  indirekt beheizte Speicher (Bj 1987 bis 1994) ansetzten (normale Trinklwaasserspeicher mit einem WendelWärmetausche). Bei thermischer Solaranlage anlog jedoch als bivaltenter Speicher (mind. 2 Wendelwärmetauscher)
//Speichervolumen für Warmwasserspeicher Vsw im indirekt beheizsten Speicher , Bj '85 bis '94 vorgebebn durch BAnz  

//                   if $calculations['nutzflaeche'] < 5000 m² than
//                         $Vs01 = inpol ($calculations['nutzflaeche'])// Tab. 54 Spalte 1 
//                         $Vs02= 0 
//                         $Vs03= 0


//                   if $calculations['nutzflaeche'] >= 5000 m² && $calculations['nutzflaeche'] < 10000 than
//                        $Vs01 = 1122 //Liter
//                        $Vs02= inpol ($calculations['nutzflaeche'] -5000)// Tab. 54 Spalte 1 
//                        $Vs03= 0
//
//                   if $calculations['nutzflaeche'] >= 10000 m² && $calculations['nutzflaeche'] < 13368.98 than
//                      $Vs01 = 1122 //Liter
//                      $Vs02=  1122 //Liter 
//                       $Vs03=  inpol ($calculations['nutzflaeche'] -10000)// Tab. 54 Spalte 1

//                   else  
//                       $Vs01 =  1122 //Liter                                
//                       $Vs02 =  1122 //Liter 
//                       $Vs03 =  756 //Liter; die drei Voluminas addiert ergeben 3336Liter, 3000Liter sind max. nach T12 zulässig deswegen werden beim letzten Speicher abgezogen. Vs03 wird nicht berücksichtigt

//
// NOTE: Berechnung Volumen
//                 $Vs0=$Vs01+$Vs02+$Vs3
//
// 

//                $Vsw= $Vs0 * ($qwb/12.5)
//-----------------
//               if $Vsw <= 3000liter than
//                  $Vsw =$Vsw
//               else
//                  $Vsw =3000Liter
//---------------------------  
//
//
//                 if $Vsw >= 1500Liter than
//                    $Vsw1 = 1500Liter;
//                      $Vsw2= $Vsw - $Vsw1
//                 else 
//                   $Vsw1 = $Vsw
//                   $Vsw2= 0
//

//                  $Qws01 = Tab.55, Vsw1, unbeheizt/beheizt, Zirkulation mit (Spalte Zirkulation)
//                  $Qws02 = Tab.55, Vsw2, unbeheizt/beheizt, Zirkulation mit (Spalte Zirkulation)

//                  $Qws= ($Qws01+$Qws02)*1.32 // Faktor 1.32 nach BaNz,  fwBj. 1987-1994, T12, Tab. 55; besieht sich auf indirekt beheizte Speicher und nicht auf bivalente Speicher.

//-------------------------------
//
// NOTE: Bestimmung der Aufwandszahl der Trinkwasserwarmwasserspeicher ews
//
//
//          Diese Frormel befindet sich am Ende der Äußeren Schleife         $ews= 1+($Qws/($calculations['QWB']*$ewd*$ewce))  Kann Man diese Löschen????

//---------------------------------


////   if "Solaranlage = Ja" && "Trinkwassererwärmung" than  //// Äußere Schleife
//
// Speicherung Trinkwasser in bivalenten Speicher (u.a. für Solaranlage thermisch). Vereinfachung durch BAnz  ...ebenfalls durch BAnz können zur Vereinfachung nur Flachkollektoren angesetzt werden. Ebenfalls wird nur "mit Zirkulation". Folglich kann im Frontende Zirkulation entfernt werden; nicht mehr abgefragt werden.

// NOTE: Bestimmung von fbivalent

//                    if "beheizt" than
//                         $fbivalent=1.008
//                  else
//                          $fbivalent=1.2096

// Bestimmung der Volumina der Speichernenninhalte für für den Bereitschaftsanteil (Vsaux) und den Solaranteil (Vssol), Aperturfläche Solaranlage Ac, Der Energieertrag der Solaranlage Qwsola

// NOTE: Anpassung der Volumina an Grundfläche nach Formel 

// Tab. 59 & 60 T12, 
// 
//                    if ($calculations['nutzflaeche'] >=5000 than
//                           $nutzflaeche1 = 5000
//                   else
//                            $nutzflaeche1 =($calculations['nutzflaeche'] 


//                   if "unbeheizt" && $nutzflaeche1 than
//                             $Vsaux0= inpol (Tab.59; in Abhängikeit von  $nutzflaeche1, Spalte Flachkollektoren )
//                              $Vssol0= inpol (Tab.59; in Abhängikeit von  $nutzflaeche1, Spalte Flachkollektoren )
//                               $Ac0= inpol (Tab.59; in Abhängikeit von  $nutzflaeche1, Spalte Flachkollektoren )
//                              $Qwsola0= inpol (Tab.59; in Abhängikeit von  $nutzflaeche1, Spalte Flachkollektoren )
//                   if "beheizt" && $nutzflaeche1 than
//                              $Vssol0= inpol (Tab.60; in Abhängikeit von  $nutzflaeche1, Spalte Flachkollektoren )
//                              $Vsaux0= inpol (Tab.60; in Abhängikeit von  $nutzflaeche1, Spalte Flachkollektoren )
//                              $Ac0= inpol (Tab.59; in Abhängikeit von  $nutzflaeche1, Spalte Flachkollektoren )
//                             $Qwsola0= inpol (Tab.59; in Abhängikeit von  $nutzflaeche1, Spalte Flachkollektoren )
//                             else ???   

//                   if ($calculations['nutzflaeche'] >=5000 than
//                            $Vsaux0= $Vsaux0*($calculations['nutzflaeche']/5000)
//                            $Vssol0= $Vssol0*($calculations['nutzflaeche']/5000)
//                            $Ac0=    $Ac0*($calculations['nutzflaeche']/5000)
//                            $Qwsola0= $Qwsola0*($calculations['nutzflaeche']/5000)
//                    else
//                            $Vsaux0= $Vsaux0
//                             $Vssol0= $Vssol0
//                              $Ac0=    $Ac0
//                             $Qwsola0= $Qwsola0

//

// NOTE: folgend Berechnung von ews für Solarthereminutzung for TWW und Heizung


// if "Solarthermie für Heizung und Warmwasser" than  //äußeren Schleife

// Speicherung Trinkwasser in bivalenten Speicher (u.a. für Solaranlage thermisch). Vereinfachung durch BAnz  ...ebenfalls durch BAnz können zur Vereinfachung nur Flachkollektoren angesetzt werden. Ebenfalls wird nur "mit Zirkulation". Folglich kann im Frontende Zirkulation entfernt werden; nicht mehr abgefragt werden.

// NOTE: Bestimmung von fbivalent
// 
//                        if "beheizt" than
//                            $fbivalent=1.008
//                        else
//                           $fbivalent=1.2096

// Bestimmung der Volumina der Speichernenninhalte für für den Bereitschaftsanteil (Vsaux) und den Solaranteil (Vssol), Aperturfläche Solaranlage Ac, Der Energieertrag der Solaranlage Qwsola

// NOTE: Anpassung der Volumina an Grundfläche nach Formel 

// Tab. 59 & 60 T12, 
// 
//                        if ($calculations['nutzflaeche'] >=5000 than
//                            $nutzflaeche1 = 5000
//                       else
//                           $nutzflaeche1 =($calculations['nutzflaeche'] 


//                        if "unbeheizt" && $nutzflaeche1 than
//                             $Vsaux0= (inpol (Tab.59; in Abhängikeit von  $nutzflaeche1, Spalte Flachkollektoren ))*2 //*2 ergibt sich aus Seite 116, T12
//                             $Vssol0= (inpol (Tab.59; in Abhängikeit von  $nutzflaeche1, Spalte Flachkollektoren ))*2
//                             $Ac0= (inpol (Tab.59; in Abhängikeit von  $nutzflaeche1, Spalte Flachkollektoren ))*2
//                             $Qwsola0= (inpol (Tab.59; in Abhängikeit von  $nutzflaeche1, Spalte Flachkollektoren ))*2
//                        if "beheizt" && $nutzflaeche1 than
//                             $Vssol0= (inpol (Tab.60; in Abhängikeit von  $nutzflaeche1, Spalte Flachkollektoren ))*2
//                             $Vsaux0= (inpol (Tab.60; in Abhängikeit von  $nutzflaeche1, Spalte Flachkollektoren ))*2
//                             $Ac0= (inpol (Tab.59; in Abhängikeit von  $nutzflaeche1, Spalte Flachkollektoren ))*2
//                             $Qwsola0= (inpol (Tab.59; in Abhängikeit von  $nutzflaeche1, Spalte Flachkollektoren ))*2
//                        else ???   
//

//                       if ($calculations['nutzflaeche'] >=5000 than
//                             $Vsaux0= $Vsaux0*($calculations['nutzflaeche']/5000)
//                             $Vssol0= $Vssol0*($calculations['nutzflaeche']/5000)
//                             $Ac0=    $Ac0*($calculations['nutzflaeche']/5000)
//                             $Qwsola0= $Qwsola0*($calculations['nutzflaeche']/5000)

//                          else
//                              $Vsaux0= $Vsaux0
//                               $Vssol0 = $Vssol0
//                               $Ac0=    $Ac0
//                              $Qwsola0= $Qwsola0

// else???? // Ende der äußeren Schleife,






// NOTE: Korrektur der Faktorwerte; da bisher auf 12,5 (80m² Wohnung ) alles bezogen wird

// Korrekturfaktor fwb // Anpassungswert an tatsächeliche Trinkwarmwasserbedarf nach Gl. 74, T12



//  $fwb = ($qwb/12,5)*/((1+($ewd0-1))*(12,5/$qwb)/$ewd0)

// NOTE: Bestimmung Anpassung an den realen Warmwasserbedarf des Gebäudes
// Hierbei ist Ac1 die kollektrofläche; diese wird jedoch nicht beim Kunden abgefragt sondern wird aus den Tabellen vorgegeben (Tab. 59 u. 60).
//
//   $Vsaux1= $Vsaux0 * $fwb //Nur für Trinkwasser bercüksicht Gl67. T12
//   $Vssol1= $Vssol0 * $fwb//Nur für Trinkwasser bercüksicht Gl67. T12
//   $Ac1=    $Ac0 * $fwb
//   $Qwsola1= $Qwsola0 *($Ac1/$Ac0)

// 

//----------------------------------

// NOTE: Berechnung Qws= Wärmeverluste eines bivalenten Speichers
// $Qws= $fbivalent * (0.4+0.2*(($Vsaux1+$Vssol1)^0.4)*(($Vsaux1/$Vsaux1+$Vssol1)^2)*365; //Gl.67, T12

//--------------------------------------

// NOTE: Berechnung der Aufwandszahl Trinkwarmwasserspeicher ews inklusive thermischer Solaranlage. ews hier bezieht sich nur auf reine Trinkwassernutzung der Solaranlage

// $ews= 1+($Qws/($calculations['QWB']*$ewd*$ewce))






//___________________________________________________________________________

// 
// Suchbegriffe 1143
// TODO: Berechnung der Erzeugernutzwärmeabgabe Qoutg

// Wird einmal für Heizung und einmal für TWW berechnet 

//Monatliche Zuammenstellung der solerthermische Erträge über eine Jahr, Hierbei ergibt sich die Solarkollektrofläche asu den vorgegebenen Wert aus Tab 59/60; Nach Bundesanzeiger  werden vereinfacht nur Flachkollektoren berücksicchtigt.
//Unter berücksichtigung der Ausrichtung und des Anstellwinkels wird in Tab67-69 optimal Ertrag ermittelt,






///////////// Der Bereich zwischen ////// wurde in die if-Abfrage genommen könnte also gelöscht werden. //////

// NOTE: Beginne des Foreach Schleife (monatsweise)

// $calculations['monate'][ $monat ]['Qhoutg'] = $calculations['monate'][ $monat ]['qh']*$ehce*$ehd*$ehs    // Erzeugernutzwärme pro MOnat Heizung
//  $calculations['Qhoutg'] += $calculations['monate'][ $monat ]['Qhoutg']; 


// $calculations['monate'][ $monat ]['Qwoutg'] = $calculations['monate'][ $monat ]['QWB'];*$ewce*$ewd*$ews    // Erzeugernutzwärme pro MOnat TWW
//  $calculations['Qwoutg'] += $calculations['monate'][ $monat ]['Qwoutg']; 


// Bestimmung von qsolmth
// Aufstellwinkel dem Kunden folgende Winkel vorgeben : 30°, 45, 60, 90° 
// Abfragen der Ausrichtung: (N, No;NW, W, O, SO,SW,S)




// $calculations['monate'][ $monat ]['qsol'] = Tab 67-69 in Abhängikeit von Ausrichtung (N, No;NW, W, O, SO,SW,S) und Aufstellwinkel der Kollektoranlage


// $calculations['monate'][ $monat ]['Qsol'] =$calculations['monate'][ $monat ]['qsol']*$Ac1*$calculations['monate'][ $monat ]['dmth']  // Ac1= Fläche. Wird nicht abgefragt , da die Fläche vorgegeben wird in T12, Tab59/60 in Anhängigkeit der ($calculations['nutzflaeche']

///////////////////////////////////////////////////////////////////////////////////






// Bestimmung von Qwsol
// 
//a) Bestimmng von  Qwoutg
//   minimal Wert aus $Qwoutg; und $Qwsol monatsbezogen!! 
//   
//  if  ($calculations['monate'][ $monat ]['Qwoutg'] = $calculations['monate'][ $monat ]['QWB'];*$ewce*$ewd*$ews)  > ($calculations['monate'][ $monat ]['Qsol'] =$calculations['monate'][ $monat ]['qsol'] = Tab 67-69 in Abhängikeit von Ausrichtung (N, No;NW, W, O, SO,SW,S)*$Ac1*$calculations['monate'][ $monat ]['dmth']]) than
//           $calculations['monate'][ $monat ]['Qwsol'] = $calculations['monate'][ $monat ]['Qsol']
//                     $Qwsol1=$Qwsol1+$Qwsol  //Berechnung für den anteiligen Jahreswert
//  else  
//           $calculations['monate'][ $monat ]['Qwsol'] = $calculations['monate'][ $monat ]['Qwoutg']
//                     $Qwsol2=$Qwsol2+Qwoutg //Berechnung für den anteiligen Jahreswert


//      $Qwsol = $Qwsol1 +$Qwsol2  //Berechnung des gesamten Jahreswert// Muss hier wie folgt grechntet werden?  $calculations['Qwsol'] += $calculations['monate'][ $monat ]['Qwsol'];







//Ende des Foreach Schleife


////////////////////////////////////////////////////////////////////////////////////
//Anfang des Foreach Schleife (monatsweise)
// Berechnung von Qhsol

//  a) Bestimmung $QhsolMax

//      if ($calculations['monate'][ $monat ]['Qsol'] - $calculations['monate'][ $monat ]['Qwoutg']) > 0 than
//         $QhsolMax  = QhsolMax1+($calculations['monate'][ $monat ]['Qsol'] - $calculations['monate'][ $monat ]['Qwoutg'])
//   

//  else 
//          $QhsolMax  = 0

//        

//
//
//  b)  Bestimmung von $Qhsol
//
//      if $calculations['monate'][ $monat ]['Qhoutg'] < $QhsolMax than
//                $Qhsol =   $calculations['monate'][ $monat ]['Qhoutg']
//      else
//               $Qhsol = $QhsolMax
//
// $calculations['Qhsol'] += $calculations['monate'][ $monat ]['Qhsol'];

//Ende des Foreach Schleife


//////////////////////////////////////////////////////////////////////

//---------------------------------------------



//   Deckungsanteil erneuererbare Energieen; Solarthermie...Wärmepumpen wie berücksichtigt?

//  If "Solarthermie nur TWW" than
//      $keew=0.5   //Tab. 59 und 60
//      $keeh=0
//  if "Solarthermie Heizung & Wasser" than
 //     $keew=$Qwsol/$calculations['Qwoutg'] 
 //     $keeh=$calculations['Qhsol']/$calculations['Qhoutg']
//   else???

//_______________________________________________________________________________
//// Suchbegriffe 1144
// TODO: Energieerzeuger

//  Die Bestimmung von $eg ist abhängig von der Anzahl der Wärmeerzueger(max. 3 Stck.=eg1, eg2, eg3) 
//
// Beginn der Schleife, Wenn mehr 1 Kessel 1 durchlauf mit eg1 und 2Kessel 2 mal durchlauf mit eg2  und  eg 1 etc eg 1, eg2, eg3
// Wenn mehr  kessel (max 3.) müssen diese jeweils entsprechend der unteren Bedinungen berechnet werden (Sven Rücksprache mit Jan, bitte)

// Etagenkesselanlagen/Thermen werden nicht einzeln berücksichtigt. Nach DIN 18599 T12 werden die Übertragungswerte für die einzelnen Wohnungen würden in der Summe imm 100% ergeben (Faktor 1.0), Die Kesselleitung des fiktiven Kessels wir berechnet. Sie T12 Seite

// NOTE: a) Konventionelle Kessel

//---------------------------------------------
//- Standard Gas Banz (Hochtemperaturkessel)
//               Bj. durch Eingaben bestimmen  und aus Tabellen Wert ermitteln
//                  
//       - Standardkessel Öl
//               Bj. durch Eingaben bestimmen  und aus Tabellen Wert ermitteln
//                   
//       - Brennwert  Pelletkessel bis 105kW
//               Bj. durch Eingaben bestimmen  und aus Tabellen Wert ermitteln
//----------------------------------------------
//       - Niedertemperaturkessel Gas
//               ab  1987 -2009
//                   
//       - Niedertemperaturkessel Öl
//               ab 1987 - 2009
//                   
//       - Niedertemperatur Pelletkessel bis 105kW
//              nach 1994                
//--------------------------------------------
//       - Brennwertkessel Gas Banz
//               
//                   2009- jetzt
//       - Brennwertkessel Öl
//               
//                   2009- jetzt  
//       - Brennwert  Pelletkessel bis 105kW
//                   nach 1994
//-------------------------------------------------

// 1) Eingabe KesselTyp
// 2) Eingabe Energieträger
// 3) Eingabe Baujahr

//  Unten nur eine Auflistung der konventionellen Kesseltypen 
//       if "Standard Kessel" && "Energieträger = Öl"         
//       if "Standard Kessel" && "Energieträger = Erdgas"   
//       if "Standard Kessel" && "Energieträger = Flüssiggas"   
//       if "Standard Kessel" && "Energieträger = Biogas" 
//       if "Standard Kessel" && "Energieträger = Bioöl" 
//       if "Standard Kessel" && "Energieträger = Pellet"  
//       if "Standard Kessel" && "Energieträger = Hackschnitzel" 
//    

//       if "Niedertemperatur Kessel" && "Energieträger = Öl" 
//       if "Niedertemperatur Kessel" && "Energieträger = Gas"   
//       if "NiedertermperturKessel" && "Energieträger = Flüssiggas"  
//       if "NiedertemperaturKessel" && "Energieträger = Biogas" 
//       if "NiedertemperaturKessel" && "Energieträger = Bioöl" 
//       if "NiedertemperaturKessel" && "Energieträger = Pellet"  
//       if "NiedertemperaturKessel" && "Energieträger = Hackschnitzel" 

//       if "Brennwertkessel" && "Energieträger = Öl" 
//       if "Brennwertkessel" && "Energieträger = Gas"   
//       if "Brennwertkessel" && "Energieträger = Flüssiggas" 
//       if "Brennwertkessel" && "Energieträger = Biogas" 
//       if "Brennwertkessel" && "Energieträger = Bioöl" 
//       if "Brennwertkessel" && "Energieträger  = Pellet"  
//       if "Brennwertkessel" && "Energieträger = Hackschnitzel" 

//       if "Feststoffkessel" && "Energieträger = Hackschnitzel" 
//       if "Feststoffkessel" && "Energieträger = Scheitholz" 
//       if "Feststoffkessel" && "Energieträger = Pellet" 
//       if "Feststoffkessel" && "Energieträger = "Steinkohle" 
//       if "Feststoffkessel" && "Energieträger = "Braunkohle"

//  Holz=Hackschnitzel=Pellet

//       if "Umlaufwasserheizer" && "Energieträger = Gas" //ehemals Etagenheizung, da in tab 78 keine passend Werte vorhanden für Anlagen jünger als 1987 vorhanden sind soll sich der KUnden für Brennwert oder Niedertemperatur entscheide
//       if "Umlaufwasserheizer" && "Energieträger = Öl" 

//----------------------
// Berechnung Aufwandszahl für die Erzeugung eg für konvetionelle Heizkessel 
//
//  $ßhg = $ßhs * $ehs // mittlere Belastung der *Übergabe Heizung; damit für Alle Erzeuger (einschl Fern/Nahwärme)
//
// Bestimmung von $eg0, Tab 77 und 82
//
//   if "Umlaufwasserheizer" than
//      $ego = Tab 82 T12, in Anhängigkeit von $Pn und $ßhg
//   else
//       $ego = Tab 77 T12, in Anhängigkeit von $Pn und $ßhg
//
//---------------------
/// Bestimmung von $fbj, Tab 78 und 82
//
//   if "Umlaufwasserheizer" than
//      $fbj = Tab 82 T12, in Anhängigkeit von "Umlaufwasserheizer" und $ßhg
//   else
//       $ego = Tab 78 T12, in Anhängigkeit von "Baujahr der Heizung" und $ßhg
//
// ---------------------
//  
//    if Umlaufwasserheizer &&  "Energieträger = Hackschnitzel" && "Energieträger = Scheitholz" && "Energieträger = Pellet"  than
//              $fegt = 1.0
//    if  "Brennwertheizung" &&  "Energieträger = Gas" && "Energieträger = Biogas" && "Energieträger = Flüssiggas"  than
//              $fegt = Tab.79  in Abhängigkeit  "Vor- und Rücklauftemperatur" und $ßhg und "unbeheizt/beheizt"
//    if  "Brennwertheizung" &&  "Energieträger = Heizöl" && "Energieträger = Bioöl"  than
//              $fegt = Tab.80  in Abhängigkeit  "Vor- und Rücklauftemperatur" und $ßhg und "unbeheizt/beheizt"
//   else 
//             $fegt = Tab. 81 in Abhängigkeit  "Vor- und Rücklauftemperatur" und $ßhg und "unbeheizt/beheizt"

//----------------------------


////Bestimmung von Aufwandszahl der Erzeugung $eg Heizung
// $eg=$eg0*$fbj*$fegt  //$eg wird auch in Trinkwarmwasser genutzt
/

//_______________________________________
// 
//  
//  konventionelle Heizkessel WasserWarm laut FOrmael A16 T12, S.210. 
//   $ewg= ($eg-1)*8760/$calculations['ithrl']+1;


/
//------------------------------------
// NOTE: b) Wärmepumpe
//
// BanZ: luft/Wasserwärmepumpen = bivalenter Betrieb; Heizgrenztemperatur=15°C kleiner 15°C dann ON-Betrieb der Wärmepumpe, Bivalenztempaeratur bei - 2°C; ab -2°C beginnt der Elektroheizstab=On und Wärmepumpe= OFF
// BanZ: Sole-/Wasser oder Wasser/Wasser Wärmepumpe nehmen wir Mono-ValentenBetrieb an. 
//
//  if "in der Angabe der Systemperaturen:  "90°C / 70°C" && "70 / 55" angegeben  than
//         $θva = 55 ;
//  if  55°/35 than
//         $θva = 55 ;
//  if  35° / 28° than
//         $θva =35;
//else???



// Berechnung von Vorlauftemperatur als Monatsmittel-Wert 
//  if "Heizkörper" than
//  $θvl = (($θva-20)*(($calculations['ßhma']/12)^(1/1.3)))+20 ; // 2-Rohrnetz Heizkörper
//  if  "Fußbodenheizung" than
//  $θvl = (($θva-20)*(($calculations['ßhma']/12)^(1/1.1)))+20 ; // 2-Rohrnetz Fußbodenheizung/Wandheizung
// else???
//

//
//  if $θvl < 30 than
//    $θvl = 30;
 // else 
//    $θvl = $θvl;




//------------!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

//  if Wärmepumentyp = "Luft Wasser-Wärmepumpe" than



// Berechnung COP-Wert für Luft Wasser-Wärmepume für Heizung
//
//Luft/Wasser :Ermittlung eines durchschnittlichen COP-Wert; Da Luft Wasserwärmepumpe keine konstante Temperaturniveaus  (Außenlufttemperatur variabel) besitzt. 
// Betrachtet werden 3 Temperaturklassen (-7°C, +2°C, +7°C)

// Raussuchen von $COPtk nach Tab. 89 T12; in Anhängigkeit von $θvl ($COPtk-7,$COPtk2,$COPtk7)
// Bestimmung von COPkorr nach Tab 90 T12; Nach Vorgaben der DIN 18599 T5 S. 98; schlechterer Wert und damit auf der sicheren Seite. (alternativ. 1.02)
// 
//   $COPkorr-7 = $COPtk-7 * 1.0 ;
//   $COPkorr2 = $COPtk2 * 1.0 ;
//   $COPkorr7 = $COPtk7 * 1.0 ;
//------------
//
//  Berechnung der mittlere Belastung Erzeuger NUtzwärme  ; $ßoutgmth
// 
//   $calculations['monate'][ $monat ]['ßoutgmth'] = $calculations['monate'][ $monat ]['ßhm']/$calculations['ßhma'];
//
////-----------
//   Gesamtwichtung  der Temperaturklassen nach Tab 93, T12

//    $calculations['monate'][ $monat ]['W-7']= $calculations['monate'][ $monat ]['ßoutgmth']* (Tab93 MOnatwert für W -7 herauskopieren);
//    $calculations['monate'][ $monat ]['W2']= $calculations['monate'][ $monat ]['ßoutgmth']* (Tab93 MOnatwert für W +2 herauskopieren);
//    $calculations['monate'][ $monat ]['W7']= $calculations['monate'][ $monat ]['ßoutgmth']* (Tab93 MOnatwert für W +7 herauskopieren);


// Jahreessumme bilden 
//    $calculations['W-7']+= $calculations['monate'][ $monat ]['W-7'];
//    $calculations['W2']+= $calculations['monate'][ $monat ]['W2'];
//    $calculations['W7']+= $calculations['monate'][ $monat ]['W7'];
//--------------


//  Berechnung des zweiten Wärmeerzeugers (elektrischer Heizstab); Bivalenztemperatur -2°C und weniger läuft der Elektrische Heizstab und WP wird ausgeschaltet.


//  Abfrage an den Kunden: "Wird ihre Wärmepumpe vom Stromversorger zu verschiedene Zeitpunkten am Tag abgeschaltet; EVU-Abschaltung? " Anwort : Ja/Nein

//     if "ja" than  
////      $calculations['W-7']=0.016+0,05;
//     else
//        $calculations['W-7']=0.016;
//
//--------------------------------
//
//
//
//
//  Berechnung der Endenergie ohne COP-Einfluss

//  $Qhfwpw-7 = $calculations['qh']*$UebergabeAufwandszahl['ehce']*$ehdkorr*$ehs*$calculations['W-7'];
//  $Qhfwpw2 = $calculations['qh']*$UebergabeAufwandszahl['ehce']*$ehdkorr*$ehs*$calculations['W2'];
//  $Qhfwpw7 = $calculations['qh']*$UebergabeAufwandszahl['ehce']*$ehdkorr*$ehs*$calculations['W7'];

//   $Qhfwp = $Qhfwpw-7+$Qhfwpw2+$Qhfwpw7;
//--------------------------------------


//  $Qhfwpw-7* =$Qhfwpw-7/$COPkorr-7 ;
//  $Qhfwpw2* = $Qhfwpw2/$COPkorr2 ;
//  $Qhfwpw7* = $Qhfwpw7/$COPkorr7 ;

//   $Qhfwp* = $Qhfwpw-7*+$Qhfwpw2*+$Qhfwpw7*;
//



// Berechnung von eg Luft/Wasserwärmepumpe,  für Gesamtaufwandszahl
// a) Einstufige Wärmepumpe (Abgabeleistung immer konstant; immer eine feste KW Zahl in der Abgabe, nicht leistungsgeregelt)
//     
//     $eg = $Qhfwp*/$Qhfwp  ; // Einstufige Wärmepumpe
//  
// b) Mehrstufige/geregelte Leistungsabgabe 
  
//     $eg = 1/(1/($Qhfwp*/$Qhfwp)+0.1); // mehrstufige Wärmepumpe

//-------------------!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


//  else 


//  Berechnung COP-Wert für Sole/Wasser-Wärmepume und Wasser/Wasser-Wärmepumpe Heizung

// Bestimmung COPtk
//
//// Raussuchen von $COPtk nach Tab. 89 T12; in Anhängigkeit von $θvl und der Wärmepumpenart, wobei  für Sole/Wasser-Wärmepume und Wasser/Wasser-Wärmepumpe nur ein COP-Wert definiert wird
// Bestimmung von COPkorr nach Tab 90 T12; Nach Vorgaben der DIN 18599 T5 S. 98; schlechterer Wert und damit auf der sicheren Seite. (alternativ. 1.02)
// 
//  
//   $COPkorr = $COPtk * 1.0 ;
//
//-------------
// Bestimmung der Aufwandszahl eg für heizung Sole/Wasser-Wärmepume und Wasser/Wasser-Wärmepumpe

// $eg = 1/$COPkorr;

//--------------------
// Berechnung Aufwandszahl ewg Wärmepumpe für Trinkwarmwasser TWW
// Bestimmung ewg0

// Abfrage : bei SoleWasser  Nach a) Erdsonde oder b) Erdkollektor

// if Luft/wasserWärmepumpe than
// 
// $ewg0=0.365;
// if SoleWasserWärmepumpe && Erdsonde than
// $ewg0=0.364;

// if SoleWasserWärmepume && ( Erdkollektor) than
//   $ewg0=0.378;

// if WasserWasserWärmepume than

// $ewg0=0.308;
//else?????
//-------------
//
// if Luft/wasserWärmepumpe than
//   $k=0.05;
// else
//  $k=0.0;

// $ewg= (1-k)*$ewg0+k ;



//------------------!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!




// NOTE: c) zentral elektrisch beheizte Wärmeerzeuger, Tab 101, T12
// Abfrage Kunden elektrischer Wärmererzeuger (Heizstab) mit Pufferspeicher: Ja/nein
// If "Ja" than // 
//    $eg = 1.09; 
// else
//    $eg = 1.11;// für Speicherung mit separater Erzeugung

// Bestimmung Aufwandszahl $ewg (Trinkwarmwasser) für zentral elektrisch beheizten Wärmeerzeuger

//   $ewg=1.0; // Keine Angaben in der Norm

//----------------------------------------

// NOTE: d) Fernwärme/Nahwärme
//
//  Berechnung der Aufwandszahl für Nah- und Fernwärme $eg, Bestimmung auf Bassis BamZ (Standardwert)
//
//Bestimung der Aufwandszahl $eg0 in Reverenzfall 
//
//  $eg0= in Abhängikeit von $ßhg und $Pn Tab102, T12, 
// 
// Bestimmmung von $fiso, Vereinfachungen durch BanZ (5) Fernwärme; Defintion (siehe Tab.103) vereinfachnung Dämmklasse: Sekundärseite  = 1 und Primärseite =2 und Heißwasser  über 110 bis 150°C 
// UNser Ansatz : wir nehmen entsprechen BanZ las Temperatur 150°C an. 
//  
//  if $Pn < 30 than  // 30kW 
//   $fiso = 1,003;
////  if $Pn >= 30 && $Pn <100 than
//   $fiso = 1,001;
//  else
//    $fiso = 1,000;
//
// Bestimmung $ftemp, KorrekturTemperatur zur Aufwandszahl , nach Tab. 104, T12
//    
//     $ftemp = aus Tab 104, T12, Abhängig von $ßhg und $Pn und Auslegungtemperatur und beheizt/unbeheizt ; Info: es werden nur der Tab.Bereich Qprim,ds = 150°C berücksichtigt
//// 
//
//    $eg= $eg0*$fiso*$ftemp;

//  Berechnung der Aufwandszahlen ewg (Warmwasser)
//  

//    $ewg=1.0; // Keine Angaben in der Norm, Beispielrechnung mit Hottebroth = Wärmebedarf Erzeugung = 0kWH ergo $ewg=1



//------------------------------------------
//  NOTE: e) Dezentrale KWK, BHKW= primär Wärme Abfallprodukt Strom, Brennstoffzelle= Primär Strom , Abfallprodukt= Wärme

//




//--------------------------------------------
// NOTE:  f) dezentrale Systeme
//  Infobox: "Infrarotheizflächen und/oder Nachtspeicherheizung"
//  Berechnung der Aufwandszahlen eg (Heizung)
//  $eg = 1.0;

//  Berechnung der Aufwandszahlen ewg (Warmwasser)
//  
//      if "Elektrodurchlauferhitzer" than  // Wird nur hydraulischer Durchlauferhitzer wird berücksichtigt (auf der sicheren Seite)
 //           $ewg = 1.01;
//      if "Gasdurchlauferhitzer"   Than
//             $ewg = 1.26
//      else??
// ------------------------------------------ 

//  b) Zu/Abluftsystem (Zentral und Dezentral) keine Wärmepumpe, Banz AT 04.12.2022
//
//
//_______________________________________________________________________________
//
//  Endenergieermittlung abhängi von Primärenergiefaktor
//
//   Für 1.1.24 werden drei Wärmerzeuger und nur ein Übergabesystem vorbereitet. Erweiterung 2024 auf 2 Übergabesystem. In Absprache mit Herrn Esch und Jan.
// Ebenfalls wurde mit Herrn Esch und JAn besprochen, dass Solarthermie für 1.1.24 nur im Bereich Warmwasser berücksichtigt wird. 2024 folgt Heizung und Warmwassernutzung.
//
// Bei mehreren Kesseln (max 3)
// Abfrage ; welchen Decknunganteil der jeweilige Kessel hat $kgn1, $kgn2, $kgn3 (in %) %/100

 //-----------------------------                                                                                           
// Hier wird nur Heizung berücksichtigt! Enedenergie Heizung
// if nur ein Kessel than
//     $Qfhges1=  (($calculations['qh']*ece*ed)*es*eg1*$kgn1)  // $es Puffer wirdnur einmal berücksichtigt
//      $Qfhges= $Qfhges1
// if zwei Kessel
//    $Qfhges1=  (($calculations['qh']*ece*ed)*es*eg1*$kgn1) 
//    $Qfhges2=  (($calculations['qh']*ece*ed)*eg2*$kgn2)  // $es wird hier nicht mehr berückscihtigt
//     $Qfhges= $Qfhges1+$Qfhges2
//if drei Kessel
//    $Qfhges1=  (($calculations['qh']*ece*ed)*es*eg1*$kgn1) 
//    $Qfhges2=  (($calculations['qh']*ece*ed)*eg2*$kgn2) 
//    $Qfhges3=  (($calculations['qh']*ece*ed)*eg3*$kgn3) 
 //    $Qfhges= $Qfhges1+$Qfhges2+$Qfhges3
// 
// else ???

//  Definition des Energieträgers für $Qfhges1, $Qfhges2, $Qfhges3 ist notwendig für spätere berechnung der Primärenergie

//--------------------------------------------------------------------
// Hier wird nur WWS berücksichtig, Endenergie WWS, Bei Solaranlage vornadnen dann ist kee=0,5 laut Tab. 59 und Banz Tab. 8 flachkollektoren oder wenn nicht dann kee =0
//
//    if nur  Dezentrale Trinkwassererwärung than
//     $Qfwges1=  (($calculations['QWB']']*$ewd)*$ewg1)  //
//     $Qfwges2=0
//     $Qfwges3= 0
//      $Qfwges= $Qfwges1
//// if nur ein Kessel than
//     $Qfwges1=  (($calculations['QWB']']*$ewce*$ewd)*$ews*$ewg1*$kgn1*(1-$kee))  // $ews TWS wirdnur einmal berücksichtigt
//      $Qfwges= $Qfwges1
// if zwei Kessel
//    $Qfwges1=  (($calculations['QWB']*$ewce*$ewd)*$ews*$ewg1*$kgn1*(1-$kee)) 
//    $Qfwges2=  (($calculations['QWB']*$ewce*$ewd)*$ewg2*$kgn2*(1-$kee)) // $es wird hier nicht mehr berückscihtigt
//     $Qfwges= $Qfwges1+$Qfwges2
//if drei Kessel
//    $Qfwges1=  (($calculations['QWB']*$ewce*$ewd)*es*eg1*$kgn1*(1-$kee))
//    $Qfwges2=  (($calculations['QWB']*$ewce*$ewd)*$ewg2*$kgn2*(1-$kee)) 
//    $Qfwges3=  (($calculations['QWB']*$ewce*$ewd)*$ewg3*$kgn3*(1-$kee)) 
 //    $Qfwges= $Qfwges1+$Qfwges2+$Qfwges3
// 
// else ???

//  Definition des Energieträgers für $Qfwges1, $Qfwges2, $Qfwges3 ist notwendig für spätere berechnung der Primärenergie
//


//----------------------------


// TODO: Berechnung der Hilfsenergie
//
// 1.Abfrage=  "Ist Lüftungsanlage vorhanden" wenn Ja. 
// 2.Abfrage= dann Abfrage ob "Zu- und Abluftanlage" oder nur "Abluftanlage"  // Nacherhitzung wird nicht über das vereinfachte Verfahren berechnet; Jan hat entsprechende Tabellen erstellt für spätere Berechnungen erstellt
// 3.Abfrage= Zentrales System oder Dezentrales System (Infobox mit darstellenden Bildern)
// 4.Abfrage= $fbaujahr ("Baujahr der Anlage");
// Wärmepumpen, nur Zuluftanlagen und Luftheizungen werden nichtabgefragt siehe BanZ 

//---------------------
// Wichtig bei mehreren Erzeugern 

//  Die Bestimmung von $whg und $wwg ist abhängig von der Anzahl der Wärmeerzueger(max. 3 Stck.=$whg1, $whg2, $whg3,$wwg1,$wwg2,$wwg3) 
//
// Wenn mehr  kessel (max 3.) müssen diese jeweils entsprechend der unteren Bedinungen berechnet werden (Sven Rücksprache mit Jan, bitte)



// NOTE: Bestimmung der Hilfsenergie_Übergabe Wce

// a)  Heizung WHce
//-----
// Bestimmung von Wc Tab.26, T12
//    Bestimmung der Regelungskomponenten
//           Bestimmung der Anzahl der Regelungen // wird berücksichtigt bei Flächenheizung
//  Die Anzahl der Stellantriebe und Typ der Stellantriebe könnte abgefragt werden. Nach Esch nicht erfwünscht. Somit berechnet wir die Anzahl der Stellantriebe vereinfacht nach einem pratischen Ansatz. 7m²/Heizkreis als Standardgröße bezogen auf die Gebäudenutzfläche bzw. nach Angaben des Kunden mit Flächenheizungen beheizt werden.
// Hierbei setzten wir Flächen von Fußbodenheizung=Wandheizungen=Deckenheizungen, da bei gleichen Systemtemperaturen (35/28) zu gleichgroßen Flächen führen würden. In Absprache mit Herrn Esch 18.10.23
//

//    $nR= $calculations['nutzflaeche']*$AnteileFBHZ/7  //derzeit werden nur 2 Übertragungssysteme berücksichtigt. Sollten später mehr Flächenheizungen berücksichitg werdn müsse hier $Anteile Fußbodenheizung+Wandheizung+Deckenheizung addiert werden.
//
//    if Heizkörper than
//        $WHce=0; // Wir  berücksichtigen hierbei keine elektriche unterstützung der Regelung (NUr normale HeizköperThermostatventile, thermische geregel// in Absprach mit Esch, 18.10.2023)
//    if Wandheizung && Fußbodenheizung && Deckenheizung than
//        $WHce= 0.876*$nR; // in Absprach mit Esch
//      $WHce = $Wc;
     
//
// b) Bestimmung der Hilfsenergie Übergabe Lüftung (nur Lüftung nicht Umluftheizung)
//
//        $Wrvce=0; /Quelle Banz Lfn: 5.2/5.3

// c) Bestimmung der Hilfsenergie Übergabe Trinkwarmwasser 
//   
//       $Wwce=0; // 18599 T8, S.28 oder T12 . 6.3.3.2
//

//  c) Bestimmung der Hilfsenergie Übergabe Trinkwarmwasser 

//     if thermische SolarAnlage than
//        $WsolPumpece=0, // 18599 T12 Beispielrechnung; ansonsten keine Def. in  Normung. Es wird in T8 etc. nur die Hilfsenergie für Erzeuung berücksichtigt

//-------------------------------------------------------------

// NOTE: Bestimmung der Hilfsenergie_Verteilung Wd
//----------------
//  a) Berechnung des Rohrnetzes 

//    $nG = Anzahl der Geschosse (alter Quellcode; Variable suchen); was ist mit dem beheizten Dachgeschoss?
//     $hG = Geschosshöhe (alter Quellcode; Variable suchen)
//



//    $fgeoHzg=0,392; T12, Tab D1.
//    $fblHzg=0,31; T12, Tab D1.
//    $fgeoTWW=0,277; T12, Tab D1.
//    $fblTWW=0,22; T12, Tab D1.


//       $Lchar= ($calculations['nutzflaeche']/$nG*$fgeo)^(1/2);

//       $Bcar=$Lchar*0,31;

//       $LmaxHzg=2*($Lchar+($Bcar/2)+$nG*$hG+10); //10=ld ; definiert da wir nur 2-Rohrsystem Heizung betrachten T12 S. 305

//       $LmaxTWW=2*($Lchar+2.5+$nG*$hG); //


//----------------------------------------------------------------
     
// Berechnung der Hilfsenergie_Verteilung Heizung Whd , Rohrnetzberechnung
//   
//   If Heizkörper than
//       $TERMp= 0.13*$LmaxHzg+2+0; //bestimmen in Abhängikeit von $LmaxHzg  && Heizkörper oder Fußbodenheizung, wenn Übergabesytem Heizkörper bzw. Fußbodenheizung prozentrual anteilig zu berechnen  //Tab37, T12
//   if Fußbodenheizung/Wandheizung/Deckenheizung than
//       $TERMp= 0.13*$LmaxHzg+2+25; 
//

//   if GasBrennwertheizung=! GasNiedertemperatrukessel && $Pn <35  than
//       $pg= Tab 39, T12, in Abhängikeit der Heizlast und Übergabesystems (Heizkörper 10k; sichere Seite), Fußbodenheizung  
//   else $pg= 1.0;
//--------------------
//    Volumenstrom Tab 38/ T12
//      $Vstr= aus TAb 38/T12 in Abhängikeit der Heizlast und Übergabesystems (Heizkörper 10k; sichere Seite), Fußbodenheizung  



//
// $PhydrHzg=0.2778*($TERMp+$pg+11)*$Vstr; //10=Pwmz; Pstranga=1 daraus ergibt sich die 11 beides entnommen aus  T12, S.83

//-------------------------------------------------------------------
//  effizientfaktor für Pumpen Heizung

// $fe = ((1.25+(200/$PhydrHzg)^0.5)*2// Banz wir können für Pumpe(2 Faktor) berücksichtigen, da diese als nich bedarfsausgelegt definiert wird

//-------------------------------------------------
//  Differenzdruck Pumpe für Heizung (FB + Heizkörper)
//  $TERMpumpe = Nach Tab.40 T12, in Anhängikeit von $ßhd && Bj Heizung bis 1994 ungregelt ab 1995 konstant

//---------------------------------------------------
//   Faktor für intermitierenden Betrieb (Absenkungsbetrieb, Betriebsnotwendige LAufzeiten)  

//   if Einfamilienhaus than
//      $fint= Tab 41 T12, in Anhängikeit von $calculations['ith,rl']/$calculations['thm']; 
//   else (MFH)
//      $fint=1;
//---------------------------------------------------------


// TODO: Berechnung der Hilfsenergie für Heizsysteme
//  $Whd=($PhydrHzg/1000)*$ßhd*$thm*1*1*$fe*$TERMpumpe*(0.25/0.25)*$fint;  /// woher kommen die beiden 1`er? 1) fdpm=banz T 12 S.83; 2) fsch=Esch (Zweirohrnetz); 0,25= eei bei unbekannter Pumpe oder Pumpe ohne Kennzeichnung= Staandarwert T12 S.84
//__________________________________________________
//
// Berechnung der Hilfsenergie_Verteilung Lüftung Wrvd

// $Wrvd=0   // Laut Banz 7.2 und 7.3

//_______________________________________________________________________
// Berechnung der Hilfsenergie_Verteilung TWW $Wwd
//
//      // Beispiel für die korrekte Formel und die Angepasst $Wwd=($PhydrTWW/1000)*$dopa*$z*(1.25+((200/$PhydrTWW)^0.5))*$b*($Cp1+$Cp2);//

// Berchnung von $Lv (Verteilleitungslänge) und $Ls  (Strangleitungslänge)

//   

//  $C2= 0.11; /Tab.10, T8, Einzonenmodell, Gebäudgruppe 1 Rohrnetz komplett im bheizten Bereich oder im unebheiztenbereich. In beiden Fällen gitb die Tabelle die selben werte für Netztyp 1 aus. Wir haben Netztyp ein TWW anlaog zu Heizung definiert.

//  $C3=1.24;

//   $Lv= 0.11*(($calculations['nutzflaeche']/$nG)^1.24) ; // T12, S. 351  $Lv= $C2*(($calculations['nutzflaeche']/$nG)^$C3) diese FOrmel wird unten mir C2 und C3 Faktoren wiedergegeben
//---------------------
//  $C5=0.005;Tab 10 T8
// $C6=1.38; Tab 10 T8

//   $Ls = 0.005*(($calculations['nutzflaeche']/$nG)^1.38) 
//--------------------

// Bestimmung von $Pwda
// $Pwda=0.2*$Lv*(57.5-20)+0.255*$Ls*(57.5-20);

//----------------------- 
// Bestimmung $PhydrTWW 
////  if $LmaxTWW <= 500 than
   //   $∆P=nach Tab 46 in Abhängikeit von $LmaxTWW und "Durchlusssystem"-Spate // wir Durchflusssystzm da hier der ungünstiger.
//   else
//      $∆P=0.1*$LmaxTWW+27;


// $PhydrTWW = 0.2778*$∆P*($Pwda/(1.15*5*1000);

//-----------------------

// ($Cp1+$Cp2) = Konstanten in Anhängikeit der Pumpenregelung
//
// if $Bj bis 1994 than
//  ($Cp1+$Cp2) = 1.19; // bedeutet ungeregelte Pumpen laut Banz
// else
//  ($Cp1+$Cp2) = 1.13 // geregelte Pumpen laut Banz
//
//

//   $z= nach Tab. 45 in Abhängikeit von $calculations['nutzflaeche'] und Einfamilienhaus oder Mehrfamilienhaus

//  $Wwd=($PhydrTWW/1000)*365*$z*(1.25+((200/$PhydrTWW)^0.5))*2*($Cp1+$Cp2); // 365 Tage JAhr T12. Seite 90, $b=2 "pumpe nicht auf Bedarf ausgelegt" laut Banz zulässig,



//  if "Dezentrale Wasserversorgung" than
//   $Wwd=0.0;  //T12, S. 93
// else
//    $Wwd=$Wwd; //siehe oben
//-----------------------
//_____________________________________________________________________________

//// Berechnung der Hilfsenergie_Solarpumpe $WsolPumped
//
//  $WsolPumped=0.0;

//_____________________________________________________________________________

// Berechnung Hilfsenergie Speicherung Ws

// Berechnung Hilfsenergie Heizung Whs

//  if "Pufferspeicher nicht vorhanden" than
//    $Whs=0.0;
//  else
//     $Whs0=0.15*$calculations['nutzflaeche']+200;// Gl. 63, T12
//     $Whs=$Whs0*($calculations['ith,rl']/5000);

//---------------------------------
//// Berechnung Hilfsenergie Lüftung Wrvs

// $Wrvs=0.0; // laut Banz 9.2 und 9.3

//---------------------------------

//// Berechnung Hilfsenergie Speicherung Trinkwarmwasser Wws

// 
// $tpu=1.1*($calculations['Qwoutg']/$Pn);
//
//------------------------
// Bestimmung Hilfsenergieauswand $Wws0 für Ladepumpe TWW; Benötigt Speichervolumen TWW; Die Variabel wird in diesem Anschnitt umgenannt

// if "keine Solaranlage" than
//    $Vws=$Vsw
// else
//    $Vws=$Vsaux1+$Vssol1;
//
//  Tab 58 wird in Abhängigkeit von $Vws interpoliert Hilfsenergieaufwand Ladepumpe
//  if $Vws >1500 than
 //      $Wws1= Tab58 $Vws1 = 1500 //Hilfsenergie bestimmen
 //      $Wws2= Bedingung (3000liter-$Vws1) = Differnezwert in Tab 58 und Spalte Hilfsenergie Ladepumpe Stromverbrauch interpolieren
 //      $Wws0= $Wws1+$Wws2
 // else 
 //   $Wws0= Tab58 $Vws;  //Hilfsenergie bestimmen
 
 
 //-------------------------

// $Wws=$Wws0*($tpu/8760);
//-------------------------------
// Berechnung Hilfsenergie Solarpumpe Speicherung
//  $WsolPumpes=0.0;

// Berechnung Hilfsenergie dezentrale Durchlauferhitzer
//  $Vws=0.0;



//____________________________________________

// TODO: Berechnung Hilfsenergie Erzeugung Wg

//
// Hilfsenergie für Heizungsystem $Whg
//  a) konvenionelle Heizungssysteme
//   
//  if "Brennwertheizung", "Gasetagenheizung" und "Heizung Pellet, Stückholz, Hackschnitzel mit Baujahr ab 1995" than   
//     $fphgaux=1.0;
//  if "Standardkessel NT Kessel" "Feststoffkessel"than
//      $fphgaux = Tab.84, T12 in Anhängikeit $Pn und $ßhg;
//   if "Heizung Pellet, Stückholz, Hackschnitzel mit Baujahr älter 1995"
//      $fphgaux = Tab. 86 T12 in Anhängikeit $Pn und $ßhg;
// else????

//  if "Brennwertheizung", "Standardkessel - NT", "Feststoffkessel" than   
//     $Phgaux=nach Tab. 83 T12 in Abhängigkeit von $Pn und $hg;
//  if  "Heizung Pellet, Stückholz, Hackschnitzel mit Baujahr ab 1995" than
//      $Phgaux = Tab.85, T12 in Anhängikeit $Pn und $ßhg;
//   if "Gasetagenheizung"
//      $Phgaux = Tab. 88 T12 in Anhängikeit $Pn und $ßhg;
// else????
//    
//   if "Gasetagenheizung, Brennwertkessel, NT-Kessel, Festsoffkessel ab 1987" than
//         $PhauxP0=0.015; in kW;
//   if "Gasetagenheizung, Brennwertkessel, NT-Kessel, Festsoffkessel vor 1987" than
//         $PhauxP0=0.15; in kW;
//   if "Pelletheizung" than
//         $PhauxP0= Tabelle 87 T12 in Anhängigkeit $Pn in Spalte Pelletkessel;
//   if "Hackschnitzelkessel und Scheitholzkessel" than
//            $PhauxP0= Tabelle 87 T12 in Anhängigkeit $Pn in Spalte Hackschnitzel;
//   else??? 


// 
//    $twpn0= Tab 140, T12 in Anhägingkeit ($ewd*$ews) und "bei bestehenden Anlagen"
//    $twpn= $twpn0*(($calculations['nutzflaeche']*50*$qwb)/($Pn*1000*12.5));

//  

// $Whg= $fphgaux*$Phgaux*($calculations['ith,rl']-$twpn)+$PhauxP0*(8760-$calculations['ith,rl']);
 
//


//  b) Wärmepumpen
//     //T12. Seite 144 Defintion der NOrm, dass sowohl Heizung und TWW mit dem einen Wert aus Tab96 berücksichtigt ist. 

//    if Wärmepumpe && Luftwasserwärmepumpe than
//       $Whg=0.00;
//    else 
//       $Whg= nach Tab. 96  in Anhängikeit von $Pn und Heizgrenztemperatur=15°C (BanZ) 

//
//  c) zentral elektrisch beheizter Wärmeerzeuger
//
//     $Whg=0.0; //T12, S.148 Kap6.6.6.2


//  d) Fern- und Nahwärme

//    $Whg=120 //kWh/a //nach T12, Kap. 6.6.7.2  und  T8, S.97  // Da kine weiteren Infos in DIN setzten wir den höhren Wert für die Übergabestation an. geregelt Station

//  e) dezentraler Wärmeerzeuger (infrarotHeizflächen und Nachtspeicherheizungen)
//     $WHg = 0; T12, Seite 159, hier wird gesamt Wg = definiert (also sowohl Heizung wie auch TWW), Werden nur bei Übergabe berücksichtig siehe T5 S.157, Kommentar 6.5.8.1
//


// Hilfsenergie  für Lüftungsanlagen $Wrvg
// 
//  $fbetrieb=1; //laut Tab.125, T12, Faktor für Anlagenbetrieb; BAnZ 2.3  mechanische Lüftung (ganz JAhresBetrieb ohne Bedarfsführung) und BAnz 3.3
//  $fgr_exch=1.0 //Laut Tab 123, T12, BanZ 11.3 
//
//   if "Zu_ und  Abluftanlage" than
//        $fsup_decr=0,995; // T12, Tab124, Banz 11.3; Für uns sicheren Wert genommen;
//    if "Abluftanlage" than
//        $fsup_decr=1.0; // T12, Tab 124; Da keine Außenluft angesaugt wird ist keine Frostschutz notwendig
//   
//   $fbaujahr= Tab 122, T12, abhängig von Lüftungssystem und Baujahr; vereinfacht nach  Tab fbaujahr Ausarbeitung von Jan
//    
//   $fsystem= Tab 121, T12 i Anhängigkleit des Lüftungsystems und info bis 2009 alle AC ab 2010 DC siehe Tab fsystem von Jan

//    $Wfan0= Tab. 120, T12, in Anhängigkeit der $calculations['nutzflaeche' "nicht bedarfsgeführt" und Abhängi Spalte AC/DC (AC bis 2009 und DC ab 2010), ab 5000m² darf linear extrapoliert werden
//      


// $Wfan=$Wfan0*$fsystem*$fbaujahr*$fgr_exch*$fsup_decr*$fbetrieb

//
//  $Wc=0.0; // laut BanZ 
//  $Wpre_h=0.0// Laut BanZ

//

// $Wrvg=$Wfan+$Wc+$Wpre_h

//------------------------


// Hilfsenergie für TWW-system $Wwg
//  a) konvenionelle Heizungssysteme

// $Wwg=$fphgaux*$Phgaux*$twpn

// 
//  b) Wärmepumpen
//    
//    if Wärmepumpe && Luftwasserwärmepumpe than
//       $Wwg=0.00;
//    else 
//       $Wwg= 0,00 // Nach Seite 145, T 12 Defintion, dass nur einemal berückscihtig wird und das erfolgt bei uns in der Heizung 


//  c) zentral elektrisch beheizter Wärmeerzeuger

//    
////   $Wwg=0.00; //T12, S.178, Wg = null 6.6.6.2

//  d) Fern- und Nahwärme
//
//       $Wwg= 0,00; // In T8 S. 97 und T12  keine genaue Beschreibung zu den TWW-Verlusten . Analoge Betrachtung wie Wärmepumpe. 
//
//  e) dezentraler Wärmeerzeuger (Elektrische Durchlauferhitzer und GasDuchlauferhitzer)
//
//       $Wwg=0.0;

//-----------------------
// Hilfsenergie für Solarpumpe (thermische Solaranlagen) $WsolPumpeg

//   $WsolPumpeg=0.025*$Qwsola1;

//-------------------------------------------------------------------------------------------------------------

//  NOTE: Berechnung der gesamten Hilfsenergie (Ohne Anteile !!!), bisher nru für eine Heizunganlage mit einem Übertragungssystem später muss das anteilig berechnet werden


// //// if nur ein Kessel than
// $Wh=$Whce + $Whd + $Whs + $Whg1;   // >Heizsystem 
// $Ww=$Wwce + $Wwd + $Wws + $Wwg1;   // TWW-System     
// if zwei Kessel
// $Wh=$Whce + $Whd + $Whs + $Whg1 + $Whg2;   // >Heizsystem 
// $Ww=$Wwce + $Wwd + $Wws + $Wwg1 + $Wwg2   // TWW-System       
//if drei Kessel
// $Wh=$Whce + $Whd + $Whs + $Whg1 + $Whg2 + $Whg3;   // >Heizsystem 
// $Ww=$Wwce + $Wwd + $Wws + $Wwg1 + $Wwg2 + $Wwg3   // TWW-System 
// 
// else ???

// Bei Lüftung und Solar bleibt identisch also keine Änderung bei mehreren Erzeugern 
// $Wrv=$Wrvce + Wrvd + Wrvs + Wrvg;  // Lüftung 
// $WsolPumpe=$WsolPumpece + WsolPumped + WsolPumpes + WsolPumpeg;       // Solarpumpe 

// Gesamte ENergie 

// $Wges = $Wh+ $Ww+ $Wrv +$WsolPumpe;


//-----------------------------------------------------------------------------------------
// Definiton, wie vorher verlangt. 
// Kessel 1 = Energieträger =  
// Kessel 2 = Energieträger =  
// Kessel 3 = Energieträger =  
//

// TODO: Berechnung der Endenergie 
//

//  Hierbei wird keine PV berücksichtigt
// Schleife For each Anzahl der Kesselanlagen 1 bis 3 Stck.
//      Abhängigkeit von Energieträger ( Strom, Öl, Erdgas, Flüssiggas, Steinkohl, Braunkohle , Biogas, Bioöl, Holz ,Nahwärme KWK  (fossiler Brennstoff), Nahwärme Heizwerke  (fossiler Brennstoff)
//     Die Endenergieen $Qfgesn in Abhängigkeit der Energieträger ordnen
// case 1 Zentrale Treinkwassererwärmung
//    if 1 Erzeuger than
//       $Qfges1=$Qfhges1+$Qfwges1+$Wges
//                     
//    if 2 Erzeuger than
//       $Qfges1=$Qfhges1+$Qfwges1+$Wges
//       $Qfges2=$Qfhges2+$Qfwges2
//    if 3 Erzeuger than
//       $Qfges1=$Qfhges1+$Qfwges1+$Wges
//       $Qfges2=$Qfhges2+$Qfwges2
//       $Qfges3=$Qfhges3+$Qfwges3
//     else???

//     $Qfges= $Qfges1-3
//
// case 2  Dezentrale WW-Versorgung

// if 1 Erzeuger than
//       $Qfges1=$Qfhges1+$Qfwges1+$Wges
//                     
//    if 2 Erzeuger than
//       $Qfges1=$Qfhges1+$Qfwges1+$Wges
//       $Qfges2=$Qfhges2
//    if 3 Erzeuger than
//       $Qfges1=$Qfhges1+$Qfwges1+$Wges
//       $Qfges2=$Qfhges2
//       $Qfges3=$Qfhges3
//     else???

//     $Qfges= $Qfges1-3

//SchleifenEnde







//-----------------------------------------

// Berechnung des Strombedarfs in Abhängigkeit der Endenergie hier geht es nur um den Bedraf an Endenergie der den ENergieträger Strom nutz
// Schleife Anzahl Kesselanlagen 1_3 = n= 1_3 Wir haben hier ein n ageetzt da es sich hierbei um eine Schleife handelt. Damit nicht dreimal Wges berechent wird. Da bei der Berechnung der Hilfsenergie nur Wges berechnet wurde als summenwert.

// case 1 Zentrale Trinkwassererwärmung
//if Stromheizung  oder && Dezentrale Durchlauferhitzter Strom than
// $Qfstrom1-3 = $Qfhges1-3+$Qfwges1-3+($Wges/n)
// else
// $Qfstrom1-3 = ($Wges/n)              , Die gilt für alle fossilen Heizungen und den Gas-Durchlauferhitzter

// $Qfstrom= $Qfstrom1-3



//--------------------------------------------
// Berechnung der PV-Anlage Abfrage ja/ nein
// Ermittlung des Stromertrages/a der PV-Anlage auf Basis 18599/T12, Tab. 117 und TAb 115

// Bestimmung von $qfprodPV, interpolieren nach Tab. 115 mit folgenden Angaben:
// Abfrage Kunde: PV-Anlage   Ja/Nein
// Abfrage Kunde : Ausrichtung der PV-Solaranlage (Nord, Nordost etc.) Dropdown _Menue nach Tab 115
// Abfrage Kunde: Neigungswinkel (0, 30, 45, 60, 90°)
// Abfrage Kunde: Fläche der PV-Anlage, $APV

// Bestimmung Endenergie PV-Anlage im Jahr 
//
//
//  if PV-Anlage vorhanden than

//   $QfprodPV=$qfprodPV*$APV // kWh/a;

//   else 
//   $QfprodPV=0


// Bestimmung von $fPVHP (Ausnutzungsgrad der PV-Anlage zur Berechnung nutzbaren Stromertrages); in Abhängikeit der Ausrichtung nach Tab 118, T12 

// $WfPVHP=$QfprodPV*$fPVHP;




//----------------------------------------------



// Berechnung des ansetzbaren Strometrages aus der PV-Anlage 

//Damit wir nicht in den Minusbereich kommen und was wir max. ansetzbarer Stromertrag 
// if $Qfstrom < $WfPVHP than
//    $Pvans=$Qfstrom
// if $Qfstrom>= $WfPVHP than
//     $Pvans = $WfPVHP
// if Keine PV-Anlage vorhanden than
//   $Pvans=0
// else???
//  


//----------------------

// Berechnung des Endenergiebedarfs für den Energiepass (Endergebenis Endenergie)

// Berechnung der Endenergie unter Berücksichtigung des ansetztbaren Stromertrages der PV-Anlage (kWh/a)

// $Qf =   $Qfges -  $Pvans

// Berechnung der flächenbezogenen Endenergie (Brennwert)  (kwh/m^2a)

// $Qf =   $Qf/$calculations['nutzflaeche']



//-------------------------------

// Berechnung des Primärenergiebedarfs Hier geht es um die CO2 Äquivalenz 

// Berechnung der Primärenergie ohne PV-Einfluss

// case 1 Zentrale Versorgung (Heizung + WW)
// Beschreibung bzglwo kommen fp und fhsihi her 

// Definition von $fpn erfolgt in Abhängigkeit des jeweiligen Energieträgers nach Tab. A.1 T1 18599
// Definition von $fhshin erfolgt in Abhängigkeit des jeweiligen Energieträgers nach Tab. B.1 T1 18599


// if 1 Erzeuger than
//       $Qpges1=($Qfhges1+$Qfwges1)*($fp1/$fhshi1)+$Wges*1.8                   
//    if 2 Erzeuger than
//       $Qpges1=($Qfhges1+$Qfwges1)*($fp1/$fhshi1)+$Wges *1.8
//       $Qpges2=($Qfhges2+$Qfwges2)*($fp2/$fhshi2)
//    if 3 Erzeuger than
//       $Qpges1=($Qfhges1+$Qfwges1)*($fp1/$fhshi1)+$Wges *1.8
//       $Qpges2=($Qfhges2+$Qfwges2)*($fp2/$fhshi2)
//       $Qpges3=($Qfhges3+$Qfwges3)*($fp3/$fhshi3)
//     else???



//    $Qpges = $Qpges1-3

//case 2 Dezentrale Trinkwassererwärmung mit Gas oder Strom

// if Durchlauferhitzter Strom than 
//     $fpd= 1.8
//     $fhshid= 1.0

// else (Gas-Durchlauferhitzter) than 
//     $fpd= 1.1
//     $fhshid= 1.11



// if 1 Erzeuger than
//       $Qpges1=($Qfhges1)*($fp1/$fhshi1)+($Qfwges1)*($fpd/$fhshid)+$Wges*1.8                   
//    if 2 Erzeuger than
//       $Qpges1=($Qfhges1)*($fp1/$fhshi1)+($Qfwges1)*($fpd/$fhshid)+$Wges*1.8
//       $Qpges2=($Qfhges2)*($fp2/$fhshi2)
//    if 3 Erzeuger than
//       $Qpges1=($Qfhges1)*($fp1/$fhshi1)+($Qfwges1)*($fpd/$fhshid)+$Wges*1.8
//       $Qpges2=($Qfhges2)*($fp2/$fhshi2)
//       $Qpges3=($Qfhges3)*($fp3/$fhshi3)
//     else???
//   

//     $Qpges = $Qpges1-3





//-----------------------
// TODO: Berechnung der Primärenergie mit PV-Einfluss (kwh/a)


//       $Qp= $Qpges -  $Pvans *1.8              


// TODO: Berechnung der Primärenergie mit PV-Einfluss (kwh/m^2a)

//       $Qp= $Qp/$calculations['nutzflaeche']



























// ____________________________________________________________________________________

// Korrekturfaktoren alt entfernen
  // Korrekturfaktoren
  $calculations['monate'][ $monat ]['gamma'] = $calculations['monate'][ $monat ]['qg'] / ( $calculations['monate'][ $monat ]['ql'] > 0.0 ? $calculations['monate'][ $monat ]['ql'] : 1.0 );
  $calculations['monate'][ $monat ]['gamma_reference'] = $calculations['monate'][ $monat ]['qg_reference'] / ( $calculations['monate'][ $monat ]['ql_reference'] > 0.0 ? $calculations['monate'][ $monat ]['ql_reference'] : 1.0 );
  $calculations['monate'][ $monat ]['my'] = 0.0;
  $calculations['monate'][ $monat ]['my_reference'] = 0.0;
  if ( $calculations['monate'][ $monat ]['gamma'] == 1.0 ) {
    $calculations['monate'][ $monat ]['my'] = $calculations['faktor_a'] / ( $calculations['faktor_a'] + 1.0 );
  } else {
    $calculations['monate'][ $monat ]['my'] = ( 1.0 - pow( $calculations['monate'][ $monat ]['gamma'], $calculations['faktor_a'] ) ) / ( 1.0 - pow( $calculations['monate'][ $monat ]['gamma'], $calculations['faktor_a'] + 1.0 ) );
  }
  if ( $calculations['monate'][ $monat ]['gamma_reference'] == 1.0 ) {
    $calculations['monate'][ $monat ]['my_reference'] = $calculations['faktor_a_reference'] / ( $calculations['faktor_a_reference'] + 1.0 );
  } else {
    $calculations['monate'][ $monat ]['my_reference'] = ( 1.0 - pow( $calculations['monate'][ $monat ]['gamma_reference'], $calculations['faktor_a_reference'] ) ) / ( 1.0 - pow( $calculations['monate'][ $monat ]['gamma_reference'], $calculations['faktor_a_reference'] + 1.0 ) );
  }
//Heizwärmebedarf alt entfernen
    // Heizwärmebedarf Qh  // DIN4108 alte Formel für qh, Heizwärmebedarf monatbezogen, direkt unten
  $calculations['monate'][ $monat ]['qh'] = $calculations['monate'][ $monat ]['ql'] - $calculations['monate'][ $monat ]['my'] * $calculations['monate'][ $monat ]['qg']; // Qhm= Qim (monatliche Wärmeverlust, Transmission+Lüftung)-My(monatlicher Ausnutzunggrad der Wärmegeinne)*monatliche Wärmegeinne
  $calculations['monate'][ $monat ]['qh_reference'] = $calculations['monate'][ $monat ]['ql_reference'] - $calculations['monate'][ $monat ]['my_reference'] * $calculations['monate'][ $monat ]['qg_reference'];
  if ( $calculations['monate'][ $monat ]['qh'] < 0.0 ) {
    $calculations['monate'][ $monat ]['qh'] = 0.0;
  }
  if ( $calculations['monate'][ $monat ]['qh_reference'] < 0.0 ) {
    $calculations['monate'][ $monat ]['qh_reference'] = 0.0;
  }
//________________________________________________________________________________
  // Hier sollten alle neu berechneten Werte aufsummiert werden ; 15 Programmierzeilen unten können gelöscht werden
  $calculations['qh'] += $calculations['monate'][ $monat ]['qh']; //Jahresheizbedarf????????
  $calculations['qh_reference'] += $calculations['monate'][ $monat ]['qh_reference']; //Jahresheizstunden??
  $calculations['qt'] += $calculations['monate'][ $monat ]['qt'];
  $calculations['qt_reference'] += $calculations['monate'][ $monat ]['qt_reference']; //qt könnte der Wärmestrom sein (i68) Transmission
  $calculations['qv'] += $calculations['monate'][ $monat ]['qv'];//lÜFTUNG; Wärmesenker, max. Wärmestrom (H84)
  $calculations['qv_reference'] += $calculations['monate'][ $monat ]['qv_reference'];//
  $calculations['ql'] += $calculations['monate'][ $monat ]['ql'];//???? Das wäre eigentlich Jahressumme Trinkwasser oder Heizung. Vielleicht ist  es auch die Summe aus beidem. Summe AS179 und AW179 (Spaltensummenwerte)
  $calculations['ql_reference'] += $calculations['monate'][ $monat ]['ql_reference']; 
  $calculations['qi'] += $calculations['monate'][ $monat ]['qi'];
  $calculations['qi_reference'] += $calculations['monate'][ $monat ]['qi_reference'];//Summ Interne Wärmequellen, (hier gehören, InterneQeullen AG 179 + Solare Energiequelle AJ 179 +  InterneWärmequelle Summe aus Heizung & Wasser BA 179)
  $calculations['qs'] += $calculations['monate'][ $monat ]['qs'];
  $calculations['qs_reference'] += $calculations['monate'][ $monat ]['qs_reference']; //Jahresstrahlung Gesamtstrahlung kwh, Bzw. Solare Wärmequellen
  $calculations['qg'] += $calculations['monate'][ $monat ]['qg'];
  $calculations['qg_reference'] += $calculations['monate'][ $monat ]['qg_reference'];
}
//________________________________________________________________________________________________________________________________________________________________



// 12.5 -> Fixen wert austauschen gegen Werte aus den Tabelle 19
$calculations['qw'] = 12.5 * $calculations['nutzflaeche'];//Jahrewasser????/ Neu:  Tab. 19 T 12 Glg.(34), z.B. 20m² Wohnung Faktor 15,5 kWh/(m²*a) etc.. Anzahl Wohneinheiten nicht bekannte für Anfg = 80m² einzusetzen = 12,5; ist das auch Vereinfacht noch imme rmöglich?

$calculations['qw_reference'] = 12.5 * $calculations['nutzflaeche'];//kann hier 12,5 bleiben? sihe a8ch Tab P233 - Y248


$calculations['ql'] = 0.0;
$calculations['ql_reference'] = 0.0;

$calculations['qh_b'] = $calculations['qh'] / $calculations['nutzflaeche'];
$calculations['qh_b_reference'] = $calculations['qh_reference'] / $calculations['nutzflaeche'];
$calculations['qw_b'] = 12.5;
$calculations['qw_b_reference'] = 12.5;
$calculations['ql_b'] = 0.0;
$calculations['ql_b_reference'] = 0.0;

/*************************************************
 * ANLAGENDATEN
 *************************************************/
 //solar  
$calculations['anlagendaten'] = array();
$calculations['verteilung'] = array();
$calculations['speicherung'] = array();
$calculations['uebergabe'] = array();

$aaa = $energieausweis->h_erzeugung;

$h_energietraeger_name = 'h_energietraeger_' . $energieausweis->h_erzeugung;
$h_energietraeger_value = $energieausweis->$h_energietraeger_name;

$h_erzeugung = wpenon_get_table_results( $tableNames->h_erzeugung, array( 'bezeichnung' => array( 'value' => $energieausweis->h_erzeugung, 'compare' => '=' ) ), array(), true );
$h_energietraeger = wpenon_get_table_results( $tableNames->energietraeger, array( 'bezeichnung' => array( 'value' => $h_energietraeger_value, 'compare' => '=' ) ), array(), true );
$h_yearkey = wpenon_immoticket24_make_yearkey( $energieausweis->h_baujahr, $tableNames->h_erzeugung );
list( $h_ep150, $h_ep500, $h_ep2500 ) = wpenon_immoticket24_make_anlagenkeys( 'ep', $h_yearkey );
list( $h_he150, $h_he500, $h_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $h_yearkey );

$calculations['anlagendaten']['h'] = array(
  'name'                    => $h_erzeugung->name,
  'slug'                    => $h_erzeugung->bezeichnung,
  'art'                     => 'heizung',
  'typ'                     => $h_erzeugung->typ,
  'baujahr'                 => $energieausweis->h_baujahr,
  'energietraeger'          => $h_energietraeger->name,
  'energietraeger_slug'     => $h_energietraeger->bezeichnung,
  'energietraeger_primaer'  => $energieausweis->h_custom ? floatval( $energieausweis->h_custom_primaer ) : floatval( $h_energietraeger->primaer ),
  'energietraeger_co2'      => $energieausweis->h_custom_2 ? floatval( $energieausweis->h_custom_co2 ) : floatval( $h_energietraeger->co2 ),
  'speicher_slug'           => $h_erzeugung->speicher,
  'uebergabe_slug'          => $h_erzeugung->uebergabe,
  'heizkreistemperatur'     => $h_erzeugung->hktemp,
  'aufwandszahl'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $h_erzeugung->$h_ep150 ),
    array( 'keysize' => 500, 'value' => $h_erzeugung->$h_ep500 ),
    array( 'keysize' => 2500, 'value' => $h_erzeugung->$h_ep2500 ),
  ) ),
  'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $h_erzeugung->$h_he150 ),
    array( 'keysize' => 500, 'value' => $h_erzeugung->$h_he500 ),
    array( 'keysize' => 2500, 'value' => $h_erzeugung->$h_he2500 ),
  ) ),
  'deckungsanteil'          => 100,
);
$h_max_anteil = 'h';
$anteilsumme = 100;


// keysize 150, 500, 2500  (was ist mit mehr als 2500) beziehen sich auf die Netto ?-Grundfläche und dann wird in der Tab. der entsprechende Wert herausgesucht


if ( $energieausweis->h2_info ) {
  if ( $energieausweis->h_deckungsanteil > 0 ) {
    $calculations['anlagendaten']['h']['deckungsanteil'] = $energieausweis->h_deckungsanteil;
  } else {
    unset( $calculations['anlagendaten']['h'] );
  }

  $anteilsumme = $energieausweis->h_deckungsanteil;

	$h2_energietraeger_name = 'h2_energietraeger_' . $energieausweis->h2_erzeugung;
	$h2_energietraeger_value = $energieausweis->$h2_energietraeger_name;

  if ( $energieausweis->h2_deckungsanteil > 0 ) {
    $h2_erzeugung = wpenon_get_table_results( $tableNames->h_erzeugung, array( 'bezeichnung' => array( 'value' => $energieausweis->h2_erzeugung, 'compare' => '=' ) ), array(), true );
    $h2_energietraeger = wpenon_get_table_results( $tableNames->energietraeger, array( 'bezeichnung' => array( 'value' => $h2_energietraeger_value, 'compare' => '=' ) ), array(), true );

    $h2_yearkey = wpenon_immoticket24_make_yearkey( $energieausweis->h2_baujahr, $tableNames->h_erzeugung );

    list( $h2_ep150, $h2_ep500, $h2_ep2500 ) = wpenon_immoticket24_make_anlagenkeys( 'ep', $h2_yearkey );
    list( $h2_he150, $h2_he500, $h2_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $h2_yearkey );
    $calculations['anlagendaten']['h2'] = array(
      'name'                    => $h2_erzeugung->name,
      'slug'                    => $h2_erzeugung->bezeichnung,
      'art'                     => 'heizung',
      'typ'                     => $h2_erzeugung->typ,
      'baujahr'                 => $energieausweis->h2_baujahr,
      'energietraeger'          => $h2_energietraeger->name,
      'energietraeger_slug'     => $h2_energietraeger->bezeichnung,
      'energietraeger_primaer'  => $energieausweis->h2_custom ? floatval( $energieausweis->h2_custom_primaer ) : floatval( $h2_energietraeger->primaer ),
      'energietraeger_co2'      => $energieausweis->h2_custom_2 ? floatval( $energieausweis->h2_custom_co2 ) : floatval( $h2_energietraeger->co2 ),
      'speicher_slug'           => $h2_erzeugung->speicher,
      'uebergabe_slug'          => $h2_erzeugung->uebergabe,
      'heizkreistemperatur'     => $h2_erzeugung->hktemp,
      'aufwandszahl'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $h2_erzeugung->$h2_ep150 ),
        array( 'keysize' => 500, 'value' => $h2_erzeugung->$h2_ep500 ),
        array( 'keysize' => 2500, 'value' => $h2_erzeugung->$h2_ep2500 ),
      ) ),
      'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $h2_erzeugung->$h2_he150 ),
        array( 'keysize' => 500, 'value' => $h2_erzeugung->$h2_he500 ),
        array( 'keysize' => 2500, 'value' => $h2_erzeugung->$h2_he2500 ),
      ) ),
      'deckungsanteil'          => $energieausweis->h2_deckungsanteil,
    );

    $anteilsumme += $calculations['anlagendaten']['h2']['deckungsanteil'];

    if ( $calculations['anlagendaten']['h2']['deckungsanteil'] > $calculations['anlagendaten']['h']['deckungsanteil'] ) {
      $h_max_anteil = 'h2';
    }
  }

  if ( $energieausweis->h3_info && $energieausweis->h3_deckungsanteil > 0 ) {
	  $h3_energietraeger_name = 'h3_energietraeger_' . $energieausweis->h3_erzeugung;
	  $h3_energietraeger_value = $energieausweis->$h3_energietraeger_name;

  	$h3_erzeugung = wpenon_get_table_results( $tableNames->h_erzeugung, array( 'bezeichnung' => array( 'value' => $energieausweis->h3_erzeugung, 'compare' => '=' ) ), array(), true );
    $h3_energietraeger = wpenon_get_table_results( $tableNames->energietraeger, array( 'bezeichnung' => array( 'value' => $h3_energietraeger_value, 'compare' => '=' ) ), array(), true );

    $h3_yearkey = wpenon_immoticket24_make_yearkey( $energieausweis->h3_baujahr, $tableNames->h_erzeugung );

    list( $h3_ep150, $h3_ep500, $h3_ep2500 ) = wpenon_immoticket24_make_anlagenkeys( 'ep', $h3_yearkey );
    list( $h3_he150, $h3_he500, $h3_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $h3_yearkey );
    $calculations['anlagendaten']['h3'] = array(
      'name'                    => $h3_erzeugung->name,
      'slug'                    => $h3_erzeugung->bezeichnung,
      'art'                     => 'heizung',
      'typ'                     => $h3_erzeugung->typ,
      'baujahr'                 => $energieausweis->h3_baujahr,
      'energietraeger'          => $h3_energietraeger->name,
      'energietraeger_slug'     => $h3_energietraeger->bezeichnung,
      'energietraeger_primaer'  => $energieausweis->h3_custom ? floatval( $energieausweis->h3_custom_primaer ) : floatval( $h3_energietraeger->primaer ),
      'energietraeger_co2'      => $energieausweis->h3_custom_2 ? floatval( $energieausweis->h3_custom_co2 ) : floatval( $h3_energietraeger->co2 ),
      'speicher_slug'           => $h3_erzeugung->speicher,
      'uebergabe_slug'          => $h3_erzeugung->uebergabe,
      'heizkreistemperatur'     => $h3_erzeugung->hktemp,
      'aufwandszahl'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $h3_erzeugung->$h3_ep150 ),
        array( 'keysize' => 500, 'value' => $h3_erzeugung->$h3_ep500 ),
        array( 'keysize' => 2500, 'value' => $h3_erzeugung->$h3_ep2500 ),
      ) ),
      'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $h3_erzeugung->$h3_he150 ),
        array( 'keysize' => 500, 'value' => $h3_erzeugung->$h3_he500 ),
        array( 'keysize' => 2500, 'value' => $h3_erzeugung->$h3_he2500 ),
      ) ),
      'deckungsanteil'          => $energieausweis->h3_deckungsanteil,
    );

    $anteilsumme += $calculations['anlagendaten']['h3']['deckungsanteil']; //KOntrolle ab die Erzeugung der einzelnen Kessel den gesamt Ertrag bringen

    if ( $calculations['anlagendaten']['h3']['deckungsanteil'] > $calculations['anlagendaten']['h2']['deckungsanteil'] && $calculations['anlagendaten']['h3']['deckungsanteil'] > $calculations['anlagendaten']['h1']['deckungsanteil'] ) {
      $h_max_anteil = 'h3';
    }
  }
}
//falls Summe nicht 100% ergeben, dann  wird angepasst???

if ( $anteilsumme != 100 ) {
  foreach ( $calculations['anlagendaten'] as $slug => $data ) {
    $calculations['anlagendaten'][ $slug ]['deckungsanteil'] *= 100 / $anteilsumme;
  }
  unset( $data );
}

$h_uebergabe_slug = $calculations['anlagendaten'][ $h_max_anteil ]['uebergabe_slug'];
$h_uebergabe = wpenon_get_table_results( $tableNames->h_uebergabe , array( 'bezeichnung' => array( 'value' => $h_uebergabe_slug, 'compare' => '=' ) ), array(), true );
if ( $h_uebergabe ) {
  $hu_yearkey = wpenon_immoticket24_make_yearkey( $calculations['anlagendaten'][ $h_max_anteil ]['baujahr'], $tableNames->h_uebergabe  );
  list( $hu_wv150, $hu_wv500, $hu_wv2500 ) = wpenon_immoticket24_make_anlagenkeys( 'wv', $hu_yearkey );
  $calculations['uebergabe']['h'] = array(
    'name'                    => $h_uebergabe->name,
    'art'                     => 'heizung',
    'baujahr'                 => $calculations['anlagendaten'][ $h_max_anteil ]['baujahr'],
    'waermeverluste'          => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $h_uebergabe->$hu_wv150 ),
      array( 'keysize' => 500, 'value' => $h_uebergabe->$hu_wv500 ),
      array( 'keysize' => 2500, 'value' => $h_uebergabe->$hu_wv2500 ),
    ) ),
  );
}
//Verteilung der Energie

$h_verteilung_slug = $calculations['anlagendaten'][ $h_max_anteil ]['typ'];
if ( $h_verteilung_slug == 'zentral' ) {
  $h_verteilung_slug .= '_' . ( $calculations['anlagendaten'][ $h_max_anteil ]['heizkreistemperatur'] == '70/55°' ? '7055' : '5545' ); //Im Formular sehe ich die Eingabe 70/55° nicht!, Wo kommt also 'heizkreistemperatur' her?. Reicht es nach Norm aus nur 7055 und 5545 zuberücksichtigen?
}
$h_verteilung = wpenon_get_table_results( 'h_verteilung2019', array( 'bezeichnung' => array( 'value' => $h_verteilung_slug, 'compare' => '=' ) ), array(), true ); //wo finde ich h_verteilung2019????
if ( $h_verteilung ) {
  $hv_yearkey = wpenon_immoticket24_make_yearkey( $energieausweis->verteilung_baujahr, 'h_verteilung2019', $energieausweis->verteilung_gedaemmt );
  list( $hv_wv150, $hv_wv500, $hv_wv2500 ) = wpenon_immoticket24_make_anlagenkeys( 'wv', $hv_yearkey );
  list( $hv_he150, $hv_he500, $hv_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $hv_yearkey );
  $calculations['verteilung']['h'] = array(
    'name'                    => $h_verteilung->name,
    'art'                     => 'heizung',
    'baujahr'                 => $energieausweis->verteilung_baujahr,
    'waermeverluste'          => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $h_verteilung->$hv_wv150 ),
      array( 'keysize' => 500, 'value' => $h_verteilung->$hv_wv500 ),
      array( 'keysize' => 2500, 'value' => $h_verteilung->$hv_wv2500 ),
    ) ),
    'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $h_verteilung->$hv_he150 ),
      array( 'keysize' => 500, 'value' => $h_verteilung->$hv_he500 ),
      array( 'keysize' => 2500, 'value' => $h_verteilung->$hv_he2500 ),
    ) ),
  );
}

if ( $energieausweis->speicherung ) {
  $h_speicherung_slug = $calculations['anlagendaten'][ $h_max_anteil ]['speicher_slug'];
  $h_speicherung = wpenon_get_table_results( 'h_speicherung', array( 'bezeichnung' => array( 'value' => $h_speicherung_slug, 'compare' => '=' ) ), array(), true );
  if ( $h_speicherung ) {
    $hs_yearkey = wpenon_immoticket24_make_yearkey( $energieausweis->speicherung_baujahr, 'h_speicherung' );
    list( $hs_wv150, $hs_wv500, $hs_wv2500 ) = wpenon_immoticket24_make_anlagenkeys( 'wv', $hs_yearkey );
    list( $hs_he150, $hs_he500, $hs_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $hs_yearkey );
    $calculations['speicherung']['h'] = array(
      'art'                     => 'heizung',
      'name'                    => $h_speicherung->name,
      'baujahr'                 => $energieausweis->speicherung_baujahr,
      'waermeverluste'          => wpenon_interpolate( $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $h_speicherung->$hs_wv150 ),
        array( 'keysize' => 500, 'value' => $h_speicherung->$hs_wv500 ),
        array( 'keysize' => 2500, 'value' => $h_speicherung->$hs_wv2500 ),
      ) ),
      'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $h_speicherung->$hs_he150 ),
        array( 'keysize' => 500, 'value' => $h_speicherung->$hs_he500 ),
        array( 'keysize' => 2500, 'value' => $h_speicherung->$hs_he2500 ),
      ) ),
    );
  }
}

// wenn ich es richtig verstehe für Wassererzeuger, die  nicht bekannt sind. Es ist für mich unlogisch, Pass kann man nur durchführen wenn alles bekannt und im Formaular gibt es keinen button für "unbekannt".Oder bedeutet dass, dass man eine InfoBox erhält "fehlt noch"

if ( 'unbekannt' === $energieausweis->ww_info ) {
	// This kind of heater can't be set to pauschal, because there is no value for it in schema logic.
	if( ! wpenon_is_water_independend_heater( $energieausweis->h_erzeugung ) ) {
		$energieausweis->ww_info = 'h';
	}

	$prefix_ww = 'h';
} else {
	$prefix_ww = $energieausweis->ww_info;
}

//Bereich oben muss noch geklärt werden.



$ww_erzeugung = $prefix_ww . '_erzeugung';
$ww_energietraeger = $prefix_ww . '_energietraeger_' . $energieausweis->$ww_erzeugung;
$ww_baujahr = $prefix_ww . '_baujahr';

$ww_erzeugung = wpenon_get_table_results( 'ww_erzeugung2019', array( 'bezeichnung' => array( 'value' => $energieausweis->$ww_erzeugung, 'compare' => '=' ) ), array(), true );
$ww_energietraeger = wpenon_get_table_results( $tableNames->energietraeger, array( 'bezeichnung' => array( 'value' => $energieausweis->$ww_energietraeger, 'compare' => '=' ) ), array(), true );


$ww_yearkey = wpenon_immoticket24_make_yearkey( $energieausweis->$ww_baujahr, 'ww_erzeugung2019' );
list( $ww_ep150, $ww_ep500, $ww_ep2500 ) = wpenon_immoticket24_make_anlagenkeys( 'ep', $ww_yearkey );
list( $ww_he150, $ww_he500, $ww_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $ww_yearkey );
list( $ww_hwg150, $ww_hwg500, $ww_hwg2500 ) = wpenon_immoticket24_make_anlagenkeys( 'hwg', $ww_yearkey );
$calculations['anlagendaten']['ww'] = array(
  'name'                    => $ww_erzeugung->name,
  'slug'                    => $ww_erzeugung->bezeichnung,
  'art'                     => 'warmwasser',
  'typ'                     => $ww_erzeugung->typ,
  'baujahr'                 => $energieausweis->$ww_baujahr,
  'energietraeger'          => $ww_energietraeger->name,
  'energietraeger_slug'     => $ww_energietraeger->bezeichnung,
  'energietraeger_primaer'  => floatval( $ww_energietraeger->primaer ),
  'energietraeger_co2'      => floatval( $ww_energietraeger->co2 ),
  'speicher_slug'           => $ww_erzeugung->speicher,
  'aufwandszahl'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $ww_erzeugung->$ww_ep150 ),
    array( 'keysize' => 500, 'value' => $ww_erzeugung->$ww_ep500 ),
    array( 'keysize' => 2500, 'value' => $ww_erzeugung->$ww_ep2500 ),
  ) ),
  'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $ww_erzeugung->$ww_he150 ),
    array( 'keysize' => 500, 'value' => $ww_erzeugung->$ww_he500 ),
    array( 'keysize' => 2500, 'value' => $ww_erzeugung->$ww_he2500 ),
  ) ),
  'heizwaermegewinne'       => wpenon_interpolate( $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $ww_erzeugung->$ww_hwg150 ),
    array( 'keysize' => 500, 'value' => $ww_erzeugung->$ww_hwg500 ),
    array( 'keysize' => 2500, 'value' => $ww_erzeugung->$ww_hwg2500 ),
  ) ),
  'deckungsanteil'          => 100,
);
$ww_max_anteil = 'ww';

$ww_verteilung_slug = $calculations['anlagendaten'][ $ww_max_anteil ]['typ'];
if ( $ww_verteilung_slug == 'zentral' ) {
  $ww_verteilung_slug .= '_' . $energieausweis->verteilung_versorgung;
}
$ww_verteilung = wpenon_get_table_results( 'ww_verteilung', array( 'bezeichnung' => array( 'value' => $ww_verteilung_slug, 'compare' => '=' ) ), array(), true );
if ( $ww_verteilung ) {
  $wwv_yearkey = wpenon_immoticket24_make_yearkey( $energieausweis->verteilung_baujahr, 'ww_verteilung', $energieausweis->verteilung_gedaemmt );
  list( $wwv_wv150, $wwv_wv500, $wwv_wv2500 ) = wpenon_immoticket24_make_anlagenkeys( 'wv', $wwv_yearkey );
  list( $wwv_he150, $wwv_he500, $wwv_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $wwv_yearkey );
  list( $wwv_hwg150, $wwv_hwg500, $wwv_hwg2500 ) = wpenon_immoticket24_make_anlagenkeys( 'hwg', $wwv_yearkey );
  $calculations['verteilung']['ww'] = array(
    'name'                    => $ww_verteilung->name,
    'art'                     => 'warmwasser',
    'baujahr'                 => $energieausweis->verteilung_baujahr,
    'waermeverluste'          => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $ww_verteilung->$wwv_wv150 ),
      array( 'keysize' => 500, 'value' => $ww_verteilung->$wwv_wv500 ),
      array( 'keysize' => 2500, 'value' => $ww_verteilung->$wwv_wv2500 ),
    ) ),
    'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $ww_verteilung->$wwv_he150 ),
      array( 'keysize' => 500, 'value' => $ww_verteilung->$wwv_he500 ),
      array( 'keysize' => 2500, 'value' => $ww_verteilung->$wwv_he2500 ),
    ) ),
    'heizwaermegewinne'       => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $ww_verteilung->$wwv_hwg150 ),
      array( 'keysize' => 500, 'value' => $ww_verteilung->$wwv_hwg500 ),
      array( 'keysize' => 2500, 'value' => $ww_verteilung->$wwv_hwg2500 ),
    ) ),
  );
}

if ( $energieausweis->speicherung ) {
  $ww_speicherung_slug = $calculations['anlagendaten'][ $ww_max_anteil ]['speicher_slug'];
  if ( $ww_speicherung_slug == 'zentral' ) {
    $ww_speicherung_slug .= '_' . $energieausweis->speicherung_standort;
  }
  $ww_speicherung = wpenon_get_table_results( 'ww_speicherung', array( 'bezeichnung' => array( 'value' => $ww_speicherung_slug, 'compare' => '=' ) ), array(), true );
  if ( $ww_speicherung ) {
    $wws_yearkey = wpenon_immoticket24_make_yearkey( $energieausweis->speicherung_baujahr, 'ww_speicherung' );
    list( $wws_wv150, $wws_wv500, $wws_wv2500 ) = wpenon_immoticket24_make_anlagenkeys( 'wv', $wws_yearkey );
    list( $wws_he150, $wws_he500, $wws_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $wws_yearkey );
    list( $wws_hwg150, $wws_hwg500, $wws_hwg2500 ) = wpenon_immoticket24_make_anlagenkeys( 'hwg', $wws_yearkey );
    $calculations['speicherung']['ww'] = array(
      'name'                    => $ww_speicherung->name,
      'art'                     => 'warmwasser',
      'baujahr'                 => $energieausweis->speicherung_baujahr,
      'waermeverluste'          => wpenon_interpolate( $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $ww_speicherung->$wws_wv150 ),
        array( 'keysize' => 500, 'value' => $ww_speicherung->$wws_wv500 ),
        array( 'keysize' => 2500, 'value' => $ww_speicherung->$wws_wv2500 ),
      ) ),
      'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $ww_speicherung->$wws_he150 ),
        array( 'keysize' => 500, 'value' => $ww_speicherung->$wws_he500 ),
        array( 'keysize' => 2500, 'value' => $ww_speicherung->$wws_he2500 ),
      ) ),
      'heizwaermegewinne'       => wpenon_interpolate( $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $ww_speicherung->$wws_hwg150 ),
        array( 'keysize' => 500, 'value' => $ww_speicherung->$wws_hwg500 ),
        array( 'keysize' => 2500, 'value' => $ww_speicherung->$wws_hwg2500 ),
      ) ),
    );
  }
}

if ( $energieausweis->l_info == 'anlage' ) {
  $l_erzeugung = wpenon_get_table_results( $tableNames->l_erzeugung , array( 'bezeichnung' => array( 'value' => $energieausweis->l_erzeugung, 'compare' => '=' ) ), array(), true );
  $l_energietraeger = wpenon_get_table_results( $tableNames->energietraeger, array( 'bezeichnung' => array( 'value' => 'strom', 'compare' => '=' ) ), array(), true );
  $l_yearkey = wpenon_immoticket24_make_yearkey( $energieausweis->l_baujahr, $tableNames->l_erzeugung  );
  list( $l_he150, $l_he500, $l_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $l_yearkey );
  list( $l_hwg150, $l_hwg500, $l_hwg2500 ) = wpenon_immoticket24_make_anlagenkeys( 'hwg', $l_yearkey );
  $calculations['anlagendaten']['l'] = array(
    'name'                    => $l_erzeugung->name,
    'slug'                    => $l_erzeugung->bezeichnung,
    'art'                     => 'lueftung',
    'typ'                     => $l_erzeugung->bezeichnung,
    'baujahr'                 => $energieausweis->l_baujahr,
    'energietraeger'          => $l_energietraeger->name,
    'energietraeger_slug'     => $l_energietraeger->bezeichnung,
    'energietraeger_primaer'  => floatval( $l_energietraeger->primaer ),
    'energietraeger_co2'      => floatval( $l_energietraeger->co2 ),
    'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $l_erzeugung->$l_he150 ),
      array( 'keysize' => 500, 'value' => $l_erzeugung->$l_he500 ),
      array( 'keysize' => 2500, 'value' => $l_erzeugung->$l_he2500 ),
    ) ),
    'heizwaermegewinne'       => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $l_erzeugung->$l_hwg150 ),
      array( 'keysize' => 500, 'value' => $l_erzeugung->$l_hwg500 ),
      array( 'keysize' => 2500, 'value' => $l_erzeugung->$l_hwg2500 ),
    ) ),
    'deckungsanteil'          => 100,
  );

  $l_verteilung_slug = $calculations['anlagendaten']['l']['typ'];
  if ( $l_verteilung_slug == 'mitgewinnung' ) {
    $l_verteilung_slug .= '_' . $energieausweis->l_standort;
  }
  $l_verteilung = wpenon_get_table_results( $tableNames->l_verteilung , array( 'bezeichnung' => array( 'value' => $l_verteilung_slug, 'compare' => '=' ) ), array(), true );
  if ( $l_verteilung ) {
    $lv_yearkey = wpenon_immoticket24_make_yearkey( $energieausweis->l_baujahr, $tableNames->l_verteilung  );
    list( $lv_wv150, $lv_wv500, $lv_wv2500 ) = wpenon_immoticket24_make_anlagenkeys( 'wv', $lv_yearkey );
    list( $lv_he150, $lv_he500, $lv_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $lv_yearkey );
    $calculations['verteilung']['l'] = array(
      'name'                    => $l_verteilung->name,
      'art'                     => 'lueftung',
      'baujahr'                 => $energieausweis->l_baujahr,
      'waermeverluste'          => wpenon_interpolate( $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $l_verteilung->$lv_wv150 ),
        array( 'keysize' => 500, 'value' => $l_verteilung->$lv_wv500 ),
        array( 'keysize' => 2500, 'value' => $l_verteilung->$lv_wv2500 ),
      ) ),
      'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $l_verteilung->$lv_he150 ),
        array( 'keysize' => 500, 'value' => $l_verteilung->$lv_he500 ),
        array( 'keysize' => 2500, 'value' => $l_verteilung->$lv_he2500 ),
      ) ),
    );
  }
}

// Referenzgebäude
$calculations['anlagendaten_reference'] = array();
$calculations['verteilung_reference'] = array();
$calculations['speicherung_reference'] = array();
$calculations['uebergabe_reference'] = array();

$h_reference_erzeugung = wpenon_get_table_results( $tableNames->h_erzeugung, array( 'bezeichnung' => array( 'value' => 'brennwertkesselverbessert', 'compare' => '=' ) ), array(), true );
$h_reference_energietraeger = wpenon_get_table_results( $tableNames->energietraeger, array( 'bezeichnung' => array( 'value' => 'heizoel', 'compare' => '=' ) ), array(), true );
$h_reference_baujahr = absint( wpenon_get_reference_date( 'Y', $energieausweis ) );
$h_reference_yearkey = wpenon_immoticket24_make_yearkey( $h_reference_baujahr, $tableNames->h_erzeugung );
list( $h_reference_ep150, $h_reference_ep500, $h_reference_ep2500 ) = wpenon_immoticket24_make_anlagenkeys( 'ep', $h_reference_yearkey );
list( $h_reference_he150, $h_reference_he500, $h_reference_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $h_reference_yearkey );
$calculations['anlagendaten_reference']['h'] = array(
  'name'                    => $h_reference_erzeugung->name,
  'slug'                    => $h_reference_erzeugung->bezeichnung,
  'art'                     => 'heizung',
  'typ'                     => $h_reference_erzeugung->typ,
  'baujahr'                 => $h_reference_baujahr,
  'energietraeger'          => $h_reference_energietraeger->name,
  'energietraeger_slug'     => $h_reference_energietraeger->bezeichnung,
  'energietraeger_primaer'  => floatval( $h_reference_energietraeger->primaer ),
  'energietraeger_co2'      => floatval( $h_reference_energietraeger->co2 ),
  'speicher_slug'           => $h_reference_erzeugung->speicher,
  'uebergabe_slug'          => $h_reference_erzeugung->uebergabe,
  'heizkreistemperatur'     => $h_reference_erzeugung->hktemp,
  'aufwandszahl'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $h_reference_erzeugung->$h_reference_ep150 ),
    array( 'keysize' => 500, 'value' => $h_reference_erzeugung->$h_reference_ep500 ),
    array( 'keysize' => 2500, 'value' => $h_reference_erzeugung->$h_reference_ep2500 ),
  ) ),
  'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $h_reference_erzeugung->$h_reference_he150 ),
    array( 'keysize' => 500, 'value' => $h_reference_erzeugung->$h_reference_he500 ),
    array( 'keysize' => 2500, 'value' => $h_reference_erzeugung->$h_reference_he2500 ),
  ) ),
  'deckungsanteil'          => 100,
);

$h_uebergabe_reference_slug = $calculations['anlagendaten_reference']['h']['uebergabe_slug'];
$h_uebergabe_reference = wpenon_get_table_results( $tableNames->h_uebergabe , array( 'bezeichnung' => array( 'value' => $h_uebergabe_reference_slug, 'compare' => '=' ) ), array(), true );
if ( $h_uebergabe_reference ) {
  $hu_reference_baujahr = $h_reference_baujahr;
  $hu_reference_yearkey = wpenon_immoticket24_make_yearkey( $hu_reference_baujahr, $tableNames->h_uebergabe  );
  list( $hu_reference_wv150, $hu_reference_wv500, $hu_reference_wv2500 ) = wpenon_immoticket24_make_anlagenkeys( 'wv', $hu_reference_yearkey );
  $calculations['uebergabe_reference']['h'] = array(
    'name'                    => $h_uebergabe_reference->name,
    'art'                     => 'heizung',
    'baujahr'                 => $hu_reference_baujahr,
    'waermeverluste'          => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $h_uebergabe_reference->$hu_reference_wv150 ),
      array( 'keysize' => 500, 'value' => $h_uebergabe_reference->$hu_reference_wv500 ),
      array( 'keysize' => 2500, 'value' => $h_uebergabe_reference->$hu_reference_wv2500 ),
    ) ),
  );
}

$h_verteilung_reference_slug = $calculations['anlagendaten_reference']['h']['typ'];
if ( $h_verteilung_reference_slug == 'zentral' ) {
  $h_verteilung_reference_slug .= '_' . ( $calculations['anlagendaten_reference']['h']['heizkreistemperatur'] == '70/55°' ? '7055' : '5545' );
}
$h_verteilung_reference = wpenon_get_table_results( 'h_verteilung2019', array( 'bezeichnung' => array( 'value' => $h_verteilung_reference_slug, 'compare' => '=' ) ), array(), true ); //Wo finde ich h-verteilung2019
if ( $h_verteilung_reference ) {
  $hv_reference_baujahr = $h_reference_baujahr;
  $hv_reference_yearkey = wpenon_immoticket24_make_yearkey( $hv_reference_baujahr, 'h_verteilung2019', true );
  list( $hv_reference_wv150, $hv_reference_wv500, $hv_reference_wv2500 ) = wpenon_immoticket24_make_anlagenkeys( 'wv', $hv_reference_yearkey );
  list( $hv_reference_he150, $hv_reference_he500, $hv_reference_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $hv_reference_yearkey );
  $calculations['verteilung_reference']['h'] = array(
    'name'                    => $h_verteilung_reference->name,
    'art'                     => 'heizung',
    'baujahr'                 => $hv_reference_baujahr,
    'waermeverluste'          => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $h_verteilung_reference->$hv_reference_wv150 ),
      array( 'keysize' => 500, 'value' => $h_verteilung_reference->$hv_reference_wv500 ),
      array( 'keysize' => 2500, 'value' => $h_verteilung_reference->$hv_reference_wv2500 ),
    ) ),
    'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $h_verteilung_reference->$hv_reference_he150 ),
      array( 'keysize' => 500, 'value' => $h_verteilung_reference->$hv_reference_he500 ),
      array( 'keysize' => 2500, 'value' => $h_verteilung_reference->$hv_reference_he2500 ),
    ) ),
  );
}

$h_speicherung_reference_slug = $calculations['anlagendaten_reference']['h']['speicher_slug'];
$h_speicherung_reference = wpenon_get_table_results( 'h_speicherung', array( 'bezeichnung' => array( 'value' => $h_speicherung_reference_slug, 'compare' => '=' ) ), array(), true );
if ( $h_speicherung_reference ) {
  $hs_reference_baujahr = $h_reference_baujahr;
  $hs_reference_yearkey = wpenon_immoticket24_make_yearkey( $hs_reference_baujahr, 'h_speicherung' );
  list( $hs_reference_wv150, $hs_reference_wv500, $hs_reference_wv2500 ) = wpenon_immoticket24_make_anlagenkeys( 'wv', $hs_reference_yearkey );
  list( $hs_reference_he150, $hs_reference_he500, $hs_reference_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $hs_reference_yearkey );
  $calculations['speicherung_reference']['h'] = array(
    'art'                     => 'heizung',
    'name'                    => $h_speicherung_reference->name,
    'baujahr'                 => $hs_reference_baujahr,
    'waermeverluste'          => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $h_speicherung_reference->$hs_reference_wv150 ),
      array( 'keysize' => 500, 'value' => $h_speicherung_reference->$hs_reference_wv500 ),
      array( 'keysize' => 2500, 'value' => $h_speicherung_reference->$hs_reference_wv2500 ),
    ) ),
    'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $h_speicherung_reference->$hs_reference_he150 ),
      array( 'keysize' => 500, 'value' => $h_speicherung_reference->$hs_reference_he500 ),
      array( 'keysize' => 2500, 'value' => $h_speicherung_reference->$hs_reference_he2500 ),
    ) ),
  );
}

$ww_reference_erzeugung = wpenon_get_table_results( 'ww_erzeugung2019', array( 'bezeichnung' => array( 'value' => 'brennwertkesselverbessert', 'compare' => '=' ) ), array(), true );
$ww_reference_energietraeger = $h_reference_energietraeger;
$ww_reference_baujahr = $h_reference_baujahr;
$ww_reference_yearkey = wpenon_immoticket24_make_yearkey( $ww_reference_baujahr, 'ww_erzeugung2019' );
list( $ww_reference_ep150, $ww_reference_ep500, $ww_reference_ep2500 ) = wpenon_immoticket24_make_anlagenkeys( 'ep', $ww_reference_yearkey );
list( $ww_reference_he150, $ww_reference_he500, $ww_reference_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $ww_reference_yearkey );
list( $ww_reference_hwg150, $ww_reference_hwg500, $ww_reference_hwg2500 ) = wpenon_immoticket24_make_anlagenkeys( 'hwg', $ww_reference_yearkey );
$calculations['anlagendaten_reference']['ww'] = array(
  'name'                    => $ww_reference_erzeugung->name,
  'slug'                    => $ww_reference_erzeugung->bezeichnung,
  'art'                     => 'warmwasser',
  'typ'                     => $ww_reference_erzeugung->typ,
  'baujahr'                 => $ww_reference_baujahr,
  'energietraeger'          => $ww_reference_energietraeger->name,
  'energietraeger_slug'     => $ww_reference_energietraeger->bezeichnung,
  'energietraeger_primaer'  => floatval( $ww_reference_energietraeger->primaer ),
  'energietraeger_co2'      => floatval( $ww_reference_energietraeger->co2 ),
  'speicher_slug'           => $ww_reference_erzeugung->speicher,
  'aufwandszahl'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $ww_reference_erzeugung->$ww_reference_ep150 ),
    array( 'keysize' => 500, 'value' => $ww_reference_erzeugung->$ww_reference_ep500 ),
    array( 'keysize' => 2500, 'value' => $ww_reference_erzeugung->$ww_reference_ep2500 ),
  ) ),
  'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $ww_reference_erzeugung->$ww_reference_he150 ),
    array( 'keysize' => 500, 'value' => $ww_reference_erzeugung->$ww_reference_he500 ),
    array( 'keysize' => 2500, 'value' => $ww_reference_erzeugung->$ww_reference_he2500 ),
  ) ),
  'heizwaermegewinne'       => wpenon_interpolate( $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $ww_reference_erzeugung->$ww_reference_hwg150 ),
    array( 'keysize' => 500, 'value' => $ww_reference_erzeugung->$ww_reference_hwg500 ),
    array( 'keysize' => 2500, 'value' => $ww_reference_erzeugung->$ww_reference_hwg2500 ),
  ) ),
  'deckungsanteil'          => 100,
);

$ww_verteilung_reference_slug = $calculations['anlagendaten_reference']['ww']['typ'];
if ( $ww_verteilung_reference_slug == 'zentral' ) {
  $ww_verteilung_reference_slug .= '_mit';
}
$ww_verteilung_reference = wpenon_get_table_results( 'ww_verteilung', array( 'bezeichnung' => array( 'value' => $ww_verteilung_reference_slug, 'compare' => '=' ) ), array(), true );
if ( $ww_verteilung_reference ) {
  $wwv_reference_baujahr = $ww_reference_baujahr;
  $wwv_reference_yearkey = wpenon_immoticket24_make_yearkey( $wwv_reference_baujahr, 'ww_verteilung', true );
  list( $wwv_reference_wv150, $wwv_reference_wv500, $wwv_reference_wv2500 ) = wpenon_immoticket24_make_anlagenkeys( 'wv', $wwv_reference_yearkey );
  list( $wwv_reference_he150, $wwv_reference_he500, $wwv_reference_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $wwv_reference_yearkey );
  list( $wwv_reference_hwg150, $wwv_reference_hwg500, $wwv_reference_hwg2500 ) = wpenon_immoticket24_make_anlagenkeys( 'hwg', $wwv_reference_yearkey );
  $calculations['verteilung_reference']['ww'] = array(
    'name'                    => $ww_verteilung_reference->name,
    'art'                     => 'warmwasser',
    'baujahr'                 => $wwv_reference_baujahr,
    'waermeverluste'          => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $ww_verteilung_reference->$wwv_reference_wv150 ),
      array( 'keysize' => 500, 'value' => $ww_verteilung_reference->$wwv_reference_wv500 ),
      array( 'keysize' => 2500, 'value' => $ww_verteilung_reference->$wwv_reference_wv2500 ),
    ) ),
    'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $ww_verteilung_reference->$wwv_reference_he150 ),
      array( 'keysize' => 500, 'value' => $ww_verteilung_reference->$wwv_reference_he500 ),
      array( 'keysize' => 2500, 'value' => $ww_verteilung_reference->$wwv_reference_he2500 ),
    ) ),
    'heizwaermegewinne'       => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $ww_verteilung_reference->$wwv_reference_hwg150 ),
      array( 'keysize' => 500, 'value' => $ww_verteilung_reference->$wwv_reference_hwg500 ),
      array( 'keysize' => 2500, 'value' => $ww_verteilung_reference->$wwv_reference_hwg2500 ),
    ) ),
  );
}

$ww_speicherung_reference_slug = $calculations['anlagendaten_reference']['ww']['speicher_slug'];
if ( $ww_speicherung_reference_slug == 'zentral' ) {
  $ww_speicherung_reference_slug .= '_innerhalb';
}
$ww_speicherung_reference = wpenon_get_table_results( 'ww_speicherung', array( 'bezeichnung' => array( 'value' => $ww_speicherung_reference_slug, 'compare' => '=' ) ), array(), true );
if ( $ww_speicherung_reference ) {
  $wws_reference_baujahr = $ww_reference_baujahr;
  $wws_reference_yearkey = wpenon_immoticket24_make_yearkey( $wws_reference_baujahr, 'ww_speicherung' );
  list( $wws_reference_wv150, $wws_reference_wv500, $wws_reference_wv2500 ) = wpenon_immoticket24_make_anlagenkeys( 'wv', $wws_reference_yearkey );
  list( $wws_reference_he150, $wws_reference_he500, $wws_reference_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $wws_reference_yearkey );
  list( $wws_reference_hwg150, $wws_reference_hwg500, $wws_reference_hwg2500 ) = wpenon_immoticket24_make_anlagenkeys( 'hwg', $wws_reference_yearkey );
  $calculations['speicherung_reference']['ww'] = array(
    'name'                    => $ww_speicherung_reference->name,
    'art'                     => 'warmwasser',
    'baujahr'                 => $wws_reference_baujahr,
    'waermeverluste'          => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $ww_speicherung_reference->$wws_reference_wv150 ),
      array( 'keysize' => 500, 'value' => $ww_speicherung_reference->$wws_reference_wv500 ),
      array( 'keysize' => 2500, 'value' => $ww_speicherung_reference->$wws_reference_wv2500 ),
    ) ),
    'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $ww_speicherung_reference->$wws_reference_he150 ),
      array( 'keysize' => 500, 'value' => $ww_speicherung_reference->$wws_reference_he500 ),
      array( 'keysize' => 2500, 'value' => $ww_speicherung_reference->$wws_reference_he2500 ),
    ) ),
    'heizwaermegewinne'       => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $ww_speicherung_reference->$wws_reference_hwg150 ),
      array( 'keysize' => 500, 'value' => $ww_speicherung_reference->$wws_reference_hwg500 ),
      array( 'keysize' => 2500, 'value' => $ww_speicherung_reference->$wws_reference_hwg2500 ),
    ) ),
  );
}

$l_reference_erzeugung = wpenon_get_table_results( $tableNames->l_erzeugung , array( 'bezeichnung' => array( 'value' => 'mitgewinnung', 'compare' => '=' ) ), array(), true );
$l_reference_energietraeger = wpenon_get_table_results( $tableNames->energietraeger, array( 'bezeichnung' => array( 'value' => 'strom', 'compare' => '=' ) ), array(), true );
$l_reference_baujahr = $h_reference_baujahr;
$l_reference_yearkey = wpenon_immoticket24_make_yearkey( $l_reference_baujahr, $tableNames->l_erzeugung  );
list( $l_reference_he150, $l_reference_he500, $l_reference_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $l_reference_yearkey );
list( $l_reference_hwg150, $l_reference_hwg500, $l_reference_hwg2500 ) = wpenon_immoticket24_make_anlagenkeys( 'hwg', $l_reference_yearkey );
$calculations['anlagendaten_reference']['l'] = array(
  'name'                    => $l_reference_erzeugung->name,
  'slug'                    => $l_reference_erzeugung->bezeichnung,
  'art'                     => 'lueftung',
  'typ'                     => $l_reference_erzeugung->bezeichnung,
  'baujahr'                 => $l_reference_baujahr,
  'energietraeger'          => $l_reference_energietraeger->name,
  'energietraeger_slug'     => $l_reference_energietraeger->bezeichnung,
  'energietraeger_primaer'  => floatval( $l_reference_energietraeger->primaer ),
  'energietraeger_co2'      => floatval( $l_reference_energietraeger->co2 ),
  'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $l_reference_erzeugung->$l_reference_he150 ),
    array( 'keysize' => 500, 'value' => $l_reference_erzeugung->$l_reference_he500 ),
    array( 'keysize' => 2500, 'value' => $l_reference_erzeugung->$l_reference_he2500 ),
  ) ),
  'heizwaermegewinne'       => wpenon_interpolate( $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $l_reference_erzeugung->$l_reference_hwg150 ),
    array( 'keysize' => 500, 'value' => $l_reference_erzeugung->$l_reference_hwg500 ),
    array( 'keysize' => 2500, 'value' => $l_reference_erzeugung->$l_reference_hwg2500 ),
  ) ),
  'deckungsanteil'          => 100,
);

$l_verteilung_reference_slug = $calculations['anlagendaten_reference']['l']['typ'];
if ( $l_verteilung_reference_slug == 'mitgewinnung' ) {
  $l_verteilung_reference_slug .= '_innerhalb';
}
$l_verteilung_reference = wpenon_get_table_results( $tableNames->l_verteilung , array( 'bezeichnung' => array( 'value' => $l_verteilung_reference_slug, 'compare' => '=' ) ), array(), true );
if ( $l_verteilung_reference ) {
  $lv_reference_baujahr = $l_reference_baujahr;
  $lv_reference_yearkey = wpenon_immoticket24_make_yearkey( $lv_reference_baujahr, $tableNames->l_verteilung  );
  list( $lv_reference_wv150, $lv_reference_wv500, $lv_reference_wv2500 ) = wpenon_immoticket24_make_anlagenkeys( 'wv', $lv_reference_yearkey );
  list( $lv_reference_he150, $lv_reference_he500, $lv_reference_he2500 ) = wpenon_immoticket24_make_anlagenkeys( 'he', $lv_reference_yearkey );
  $calculations['verteilung_reference']['l'] = array(
    'name'                    => $l_verteilung_reference->name,
    'art'                     => 'lueftung',
    'baujahr'                 => $lv_reference_baujahr,
    'waermeverluste'          => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $l_verteilung_reference->$lv_reference_wv150 ),
      array( 'keysize' => 500, 'value' => $l_verteilung_reference->$lv_reference_wv500 ),
      array( 'keysize' => 2500, 'value' => $l_verteilung_reference->$lv_reference_wv2500 ),
    ) ),
    'hilfsenergie'            => wpenon_interpolate( $calculations['nutzflaeche'], array(
      array( 'keysize' => 150, 'value' => $l_verteilung_reference->$lv_reference_he150 ),
      array( 'keysize' => 500, 'value' => $l_verteilung_reference->$lv_reference_he500 ),
      array( 'keysize' => 2500, 'value' => $l_verteilung_reference->$lv_reference_he2500 ),
    ) ),
  );
}

/*************************************************
 * ANLAGENBERECHNUNGEN
 *************************************************/

$anlagentechnik = array( 'anlagendaten', 'uebergabe', 'verteilung', 'speicherung' );

$calculations['qh_a_b'] = $calculations['qh_b'];
$calculations['qw_a_b'] = $calculations['qw_b'];
$calculations['ql_a_b'] = $calculations['ql_b'];

$calculations['qh_he_b'] = 0.0;
$calculations['qw_he_b'] = 0.0;
$calculations['ql_he_b'] = 0.0;

foreach ( $anlagentechnik as $anlagentyp ) {
  if ( isset( $calculations[ $anlagentyp ] ) ) {
    foreach ( $calculations[ $anlagentyp ] as $slug => $data ) {
      $aslug = $heslug = '';
      switch ( $data['art'] ) {
        case 'heizung':
          $aslug = 'qh_a_b';
          $heslug = 'qh_he_b';
          break;
        case 'warmwasser':
          $aslug = 'qw_a_b';
          $heslug = 'qw_he_b';
          break;
        case 'lueftung':
          $aslug = 'ql_a_b';
          $heslug = 'ql_he_b';
          break;
        default:
          continue 2;
      }
      if ( isset( $data['waermeverluste'] ) ) {
        $calculations[ $aslug ] += $data['waermeverluste'];
      }
      if ( isset( $data['hilfsenergie'] ) ) {
        $calculations[ $heslug ] += $data['hilfsenergie'];
      }
      if ( isset( $data['heizwaermegewinne'] ) ) {
        $calculations['qh_a_b'] -= $data['heizwaermegewinne'];
      }
    }
    unset( $data );
  }
}

$calculations['qh_e_b'] = 0.0;
$calculations['qw_e_b'] = 0.0;
$calculations['ql_e_b'] = 0.0;
$calculations['qh_p_b'] = 0.0;
$calculations['qw_p_b'] = 0.0;
$calculations['ql_p_b'] = 0.0;
$calculations['qh_co2'] = 0.0;
$calculations['qw_co2'] = 0.0;
$calculations['ql_co2'] = 0.0;
foreach ( $calculations['anlagendaten'] as $slug => $data ) {
  $aslug = $eslug = $pslug = $cslug = '';
  switch ( $data['art'] ) {
    case 'heizung':
      $aslug = 'qh_a_b';
      $eslug = 'qh_e_b';
      $pslug = 'qh_p_b';
      $cslug = 'qh_co2';
      break;
    case 'warmwasser':
      $aslug = 'qw_a_b';
      $eslug = 'qw_e_b';
      $pslug = 'qw_p_b';
      $cslug = 'qw_co2';
      break;
    case 'lueftung':
      $aslug = 'ql_a_b';
      $eslug = 'ql_e_b';
      $pslug = 'ql_p_b';
      $cslug = 'ql_co2';
      break;
    default:
      continue 2;
  }
  $energietraeger_slug = $data['energietraeger_slug'];
  $energietraeger = $data['energietraeger'];
  $deckungsanteil = $data['deckungsanteil'] * 0.01;
  $aufwandszahl = isset( $data['aufwandszahl'] ) ? $data['aufwandszahl'] : 1.0;
  $primaerfaktor = isset( $data['energietraeger_primaer'] ) ? $data['energietraeger_primaer'] : 1.0;
  $co2faktor = isset( $data['energietraeger_co2'] ) ? $data['energietraeger_co2'] : 0.0;
  $result = $calculations[ $aslug ] * $deckungsanteil * $aufwandszahl;
  $calculations['energietraeger'][ $energietraeger_slug ]['name'] = $energietraeger;
  $calculations['energietraeger'][ $energietraeger_slug ]['slug'] = $energietraeger_slug;
  $calculations['energietraeger'][ $energietraeger_slug ]['q_e_b'] += $result;
  $calculations['energietraeger'][ $energietraeger_slug ][ $eslug ] += $result;
  $calculations[ $eslug ] += $result;
  $calculations['energietraeger'][ $energietraeger_slug ]['primaerfaktor'] = $primaerfaktor;
  $calculations['energietraeger'][ $energietraeger_slug ]['primaerenergie'] += $result * $primaerfaktor;
  $calculations[ $pslug ] += $result * $primaerfaktor;
  $calculations['energietraeger'][ $energietraeger_slug ]['co2'] += $result * $co2faktor;
  $calculations[ $cslug ] += $result * $co2faktor;
}
unset( $data );

$energietraeger_strom = wpenon_get_table_results( $tableNames->energietraeger, array( 'bezeichnung' => array( 'value' => 'strom', 'compare' => '=' ) ), array(), true );
$primaerfaktor_strom = $energietraeger_strom->primaer;
$co2faktor_strom = $energietraeger_strom->co2;

if ( 'solar' === $energieausweis->regenerativ_art || $energieausweis->regenerativ_aktiv ) {
  $calculations['qw_e_b'] -= wpenon_interpolate( $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => 13.3 ),
    array( 'keysize' => 500, 'value' => 10.4 ),
    array( 'keysize' => 2500, 'value' => 7.5 ),
  ) );
  $calculations['qw_he_b'] += wpenon_interpolate( $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => 0.8 ),
    array( 'keysize' => 500, 'value' => 0.4 ),
    array( 'keysize' => 2500, 'value' => 0.3 ),
  ) );
}

$calculations['qh_he_e_b'] = $calculations['qh_he_b'];
$calculations['qw_he_e_b'] = $calculations['qw_he_b'];
$calculations['ql_he_e_b'] = $calculations['ql_he_b'];
$calculations['qh_he_p_b'] = $calculations['qh_he_e_b'] * $primaerfaktor_strom;
$calculations['qw_he_p_b'] = $calculations['qw_he_e_b'] * $primaerfaktor_strom;
$calculations['ql_he_p_b'] = $calculations['ql_he_e_b'] * $primaerfaktor_strom;
$calculations['qh_he_co2'] = $calculations['qh_he_e_b'] * $co2faktor_strom;
$calculations['qw_he_co2'] = $calculations['qw_he_e_b'] * $co2faktor_strom;
$calculations['ql_he_co2'] = $calculations['ql_he_e_b'] * $co2faktor_strom;

$calculations['qhe_e_b'] = $calculations['qh_he_e_b'] + $calculations['qw_he_e_b'] + $calculations['ql_he_e_b'];
$calculations['qhe_p_b'] = $calculations['qh_he_p_b'] + $calculations['qw_he_p_b'] + $calculations['ql_he_p_b'];
$calculations['qhe_co2'] = $calculations['qh_he_co2'] + $calculations['qw_he_co2'] + $calculations['ql_he_co2'];

$calculations['endenergie'] = $calculations['qh_e_b'] + $calculations['qw_e_b'] + $calculations['ql_e_b'] + $calculations['qhe_e_b'];
$calculations['primaerenergie'] = $calculations['qh_p_b'] + $calculations['qw_p_b'] + $calculations['ql_p_b'] + $calculations['qhe_p_b'];
$calculations['co2_emissionen'] = $calculations['qh_co2'] + $calculations['qw_co2'] + $calculations['ql_co2'] + $calculations['qhe_co2'];

// Referenzgebäude
$anlagentechnik_reference = array( 'anlagendaten_reference', 'uebergabe_reference', 'verteilung_reference', 'speicherung_reference' );

$calculations['qh_a_b_reference'] = $calculations['qh_b_reference'];
$calculations['qw_a_b_reference'] = $calculations['qw_b_reference'];
$calculations['ql_a_b_reference'] = $calculations['ql_b_reference'];

$calculations['qh_he_b_reference'] = 0.0;
$calculations['qw_he_b_reference'] = 0.0;
$calculations['ql_he_b_reference'] = 0.0;

foreach ( $anlagentechnik_reference as $anlagentyp ) {
  if ( isset( $calculations[ $anlagentyp ] ) ) {
    foreach ( $calculations[ $anlagentyp ] as $slug => $data ) {
      $aslug = $heslug = '';
      switch ( $data['art'] ) {
        case 'heizung':
          $aslug = 'qh_a_b_reference';
          $heslug = 'qh_he_b_reference';
          break;
        case 'warmwasser':
          $aslug = 'qw_a_b_reference';
          $heslug = 'qw_he_b_reference';
          break;
        case 'lueftung':
          $aslug = 'ql_a_b_reference';
          $heslug = 'ql_he_b_reference';
          break;
        default:
          continue 2;
      }
      if ( isset( $data['waermeverluste'] ) ) {
        $calculations[ $aslug ] += $data['waermeverluste'];
      }
      if ( isset( $data['hilfsenergie'] ) ) {
        $calculations[ $heslug ] += $data['hilfsenergie'];
      }
      if ( isset( $data['heizwaermegewinne'] ) ) {
        $calculations['qh_a_b_reference'] -= $data['heizwaermegewinne'];
      }
    }
    unset( $data );
  }
}

$calculations['qh_e_b_reference'] = 0.0;
$calculations['qw_e_b_reference'] = 0.0;
$calculations['ql_e_b_reference'] = 0.0;
$calculations['qh_p_b_reference'] = 0.0;
$calculations['qw_p_b_reference'] = 0.0;
$calculations['ql_p_b_reference'] = 0.0;
$calculations['qh_co2_reference'] = 0.0;
$calculations['qw_co2_reference'] = 0.0;
$calculations['ql_co2_reference'] = 0.0;
foreach ( $calculations['anlagendaten_reference'] as $slug => $data ) {
  $aslug = $eslug = $pslug = $cslug = '';
  switch ( $data['art'] ) {
    case 'heizung':
      $aslug = 'qh_a_b_reference';
      $eslug = 'qh_e_b_reference';
      $pslug = 'qh_p_b_reference';
      $cslug = 'qh_co2_reference';
      break;
    case 'warmwasser':
      $aslug = 'qw_a_b_reference';
      $eslug = 'qw_e_b_reference';
      $pslug = 'qw_p_b_reference';
      $cslug = 'qw_co2_reference';
      break;
    case 'lueftung':
      $aslug = 'ql_a_b_reference';
      $eslug = 'ql_e_b_reference';
      $pslug = 'ql_p_b_reference';
      $cslug = 'ql_co2_reference';
      break;
    default:
      continue 2;
  }
  $deckungsanteil = $data['deckungsanteil'] * 0.01;
  $aufwandszahl = isset( $data['aufwandszahl'] ) ? $data['aufwandszahl'] : 1.0;
  $primaerfaktor = isset( $data['energietraeger_primaer'] ) ? $data['energietraeger_primaer'] : 1.0;
  $co2faktor = isset( $data['energietraeger_co2'] ) ? $data['energietraeger_co2'] : 0.0;
  $result = $calculations[ $aslug ] * $deckungsanteil * $aufwandszahl;
  $calculations[ $eslug ] += $result;
  $calculations[ $pslug ] += $result * $primaerfaktor;
  $calculations[ $cslug ] += $result * $co2faktor;
}
unset( $data );

$calculations['qh_he_e_b_reference'] = $calculations['qh_he_b_reference'];
$calculations['qw_he_e_b_reference'] = $calculations['qw_he_b_reference'];
$calculations['ql_he_e_b_reference'] = $calculations['ql_he_b_reference'];
$calculations['qh_he_p_b_reference'] = $calculations['qh_he_e_b_reference'] * $primaerfaktor_strom;
$calculations['qw_he_p_b_reference'] = $calculations['qw_he_e_b_reference'] * $primaerfaktor_strom;
$calculations['ql_he_p_b_reference'] = $calculations['ql_he_e_b_reference'] * $primaerfaktor_strom;
$calculations['qh_he_co2_reference'] = $calculations['qh_he_e_b_reference'] * $co2faktor_strom;
$calculations['qw_he_co2_reference'] = $calculations['qw_he_e_b_reference'] * $co2faktor_strom;
$calculations['ql_he_co2_reference'] = $calculations['ql_he_e_b_reference'] * $co2faktor_strom;

$calculations['qhe_e_b_reference'] = $calculations['qh_he_e_b_reference'] + $calculations['qw_he_e_b_reference'] + $calculations['ql_he_e_b_reference'];
$calculations['qhe_p_b_reference'] = $calculations['qh_he_p_b_reference'] + $calculations['qw_he_p_b_reference'] + $calculations['ql_he_p_b_reference'];
$calculations['qhe_co2_reference'] = $calculations['qh_he_co2_reference'] + $calculations['qw_he_co2_reference'] + $calculations['ql_he_co2_reference'];

$calculations['endenergie_reference'] = $calculations['qh_e_b_reference'] + $calculations['qw_e_b_reference'] + $calculations['ql_e_b_reference'] + $calculations['qhe_e_b_reference'];
$calculations['primaerenergie_reference'] = $calculations['qh_p_b_reference'] + $calculations['qw_p_b_reference'] + $calculations['ql_p_b_reference'] + $calculations['qhe_p_b_reference'];
$calculations['co2_emissionen_reference'] = $calculations['qh_co2_reference'] + $calculations['qw_co2_reference'] + $calculations['ql_co2_reference'] + $calculations['qhe_co2_reference'];

$calculations['ht_b'] = $calculations['ht'] / $calculations['huellflaeche'];
$calculations['ht_b_reference'] = 0.65;
if ( 'freistehend' === $energieausweis->gebaeudetyp ) {
  if ( $calculations['nutzflaeche'] > 350.0 ) {
    $calculations['ht_b_reference'] = 0.5;
  } else {
    $calculations['ht_b_reference'] = 0.4;
  }
} elseif ( 'reiheneckhaus' === $energieausweis->gebaeudetyp || 'doppelhaushaelfte' === $energieausweis->gebaeudetyp ) {
  $calculations['ht_b_reference'] = 0.45;
}

return $calculations;
