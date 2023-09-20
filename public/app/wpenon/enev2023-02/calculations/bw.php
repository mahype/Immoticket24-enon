<?php

require_once 'lib/Extension.php';
require_once 'lib/Extension_Form_A.php';
require_once 'lib/Extension_Form_B.php';

require_once 'lib/Luftwechsel.php';

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

$himmelsrichtungen = array_keys(wpenon_immoticket24_get_himmelsrichtungen());
$hr_nullrichtung = array_search($energieausweis->grundriss_richtung, $himmelsrichtungen);
$hr_mappings = array();
for ( $i = 0; $i < 4; $i++ ) {
    $hr_mappings[] = $himmelsrichtungen[ ( $hr_nullrichtung + 2 * $i ) % 8 ];
}

$wand_a_laenge = $wand_b_laenge = $wand_c_laenge = $wand_d_laenge = $wand_e_laenge = $wand_f_laenge = $wand_g_laenge = $wand_h_laenge = 0.0;

$grundriss_formen = wpenon_immoticket24_get_grundriss_formen();
$grundriss_form = $grundriss_formen['a'];
if (isset($grundriss_formen[ $energieausweis->grundriss_form ]) ) {
    $grundriss_form = $grundriss_formen[ $energieausweis->grundriss_form ];
}
$flaechenberechnungsformel = $grundriss_form['fla'];
unset($grundriss_form['fla']);

$to_calculate = array();
foreach ( $grundriss_form as $wand => $data ) {
    if ($data[0] === true ) {
        $l_slug = 'wand_' . $wand . '_laenge';
        $$l_slug = $energieausweis->$l_slug;
    } else {
        $to_calculate[ $wand ] = $data;
    }
}
unset($data);
foreach ( $to_calculate as $wand => $data ) {
    $laenge = 0.0;
    $current_operator = '+';
    $formel = explode(' ', $data[0]);
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
    if ($laenge > 0.0 ) {
        $l_slug = 'wand_' . $wand . '_laenge';
        $$l_slug = $laenge;
    }
}
unset($data);
unset($to_calculate);

$geschosshoehe = $energieausweis->geschoss_hoehe + 0.25;
$wandhoehe = $energieausweis->geschoss_zahl * $geschosshoehe + 0.25 + $this->energieausweis->kniestock_hoehe; // NEU

$grundflaeche = 0.0;
foreach ( $flaechenberechnungsformel as $index => $_produkt ) {
    $produkt = 1.0;
    for ( $i = 0; $i < 2; $i++ ) {
        $_faktor = $_produkt[ $i ];
        $faktor = 0.0;
        $current_operator = '+';
        $_faktor = explode(' ', $_faktor);
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
                    $faktor += $$l_slug;
                    break;
                case '-':
                    $faktor -= $$l_slug;
                    break;
                default:
                }
            }
        }
        if ($faktor < 0.0 ) {
            $faktor = 0.0;
        }
        $produkt *= $faktor;
    }
    $grundflaeche += $produkt;
}

$calculations['volumenteile']['grundriss'] = array(
  'name'          => __('Grundriss', 'wpenon'),
  'v'             => $grundflaeche * $wandhoehe,
);

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


$wandlaenge = 0.0;
$calculations['wandrichtungen'] = array();
foreach ( $grundriss_form as $wand => $data ) {
    $l_slug = 'wand_' . $wand . '_laenge';
    $n_slug = 'wand_' . $wand . '_nachbar';
    $wandlaenge += $$l_slug;    

    if (! $energieausweis->$n_slug ) {
        $flaeche = $$l_slug * $wandhoehe;

        if( isset( $dach_wand_flaeche[$wand] ) ) {
            $flaeche += $dach_wand_flaeche[$wand];
        }

        $d_slug = 'wand_' . $wand . '_daemmung';
        $calculations['bauteile'][ 'wand_' . $wand ] = array(
        'name'          => sprintf(__('Außenwand %s', 'wpenon'), $wand),
        'typ'           => 'wand',
        'modus'         => 'opak',
        'bauart'        => $wand_bauart,
        'baujahr'       => $energieausweis->baujahr,
        'richtung'      => $hr_mappings[ $data[1] ],
        'a'             => $flaeche,
        'd'             => $energieausweis->$d_slug,
        );
        if (! isset($calculations['wandrichtungen'][ $calculations['bauteile'][ 'wand_' . $wand ]['richtung'] ]) ) {
            $calculations['wandrichtungen'][ $calculations['bauteile'][ 'wand_' . $wand ]['richtung'] ] = array();
        }
        $calculations['wandrichtungen'][ $calculations['bauteile'][ 'wand_' . $wand ]['richtung'] ][] = $wand;
    }
}
unset($data);

if ($energieausweis->anbau ) {
    $anbauwand_b_laenge = $anbauwand_t_laenge = $anbauwand_s1_laenge = $anbauwand_s2_laenge = 0.0;

    $anbau_formen = wpenon_immoticket24_get_anbau_formen();
    $anbau_form = $anbau_formen['a'];
    if (isset($anbau_formen[ $energieausweis->anbau_form ]) ) {
        $anbau_form = $anbau_formen[ $energieausweis->anbau_form ];
    }
    $anbau_flaechenberechnungsformel = $anbau_form['fla'];
    unset($anbau_form['fla']);

    $to_calculate = array();
    foreach ( $anbau_form as $anbauwand => $data ) {
        if ($data[0] === true ) {
            $l_slug = 'anbauwand_' . $anbauwand . '_laenge';
            $$l_slug = $energieausweis->$l_slug;
        } else {
            $to_calculate[ $anbauwand ] = $data;
        }
    }
    unset($data);
    foreach ( $to_calculate as $anbauwand => $data ) {
        $laenge = 0.0;
        $current_operator = '+';
        $formel = explode(' ', $data[0]);
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
        if ($laenge > 0.0 ) {
            $l_slug = 'anbauwand_' . $anbauwand . '_laenge';
            $$l_slug = $laenge;
        }
    }
    unset($data);
    unset($to_calculate);

    $anbauwandhoehe = $energieausweis->anbau_hoehe + 0.25 * 2;

    $anbaugrundflaeche = 0.0;
    foreach ( $anbau_flaechenberechnungsformel as $index => $_produkt ) {
        $produkt = 1.0;
        for ( $i = 0; $i < 2; $i++ ) {
            $_faktor = $_produkt[ $i ];
            $faktor = 0.0;
            $current_operator = '+';
            $_faktor = explode(' ', $_faktor);
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
            if ($faktor < 0.0 ) {
                $faktor = 0.0;
            }
            $produkt *= $faktor;
        }
        $anbaugrundflaeche += $produkt;
    }

    $calculations['volumenteile']['anbau'] = array(
    'name'          => __('Anbau', 'wpenon'),
    'v'             => $anbaugrundflaeche * $anbauwandhoehe,
    );

    switch( $energieausweis->anbau_form ) {
    case 'a':
        $extension = new Extension_Form_A();
        $extension->set_height($energieausweis->anbau_hoehe);
        $extension->set_walls($energieausweis->anbauwand_s1_laenge, $energieausweis->anbauwand_t_laenge, $energieausweis->anbauwand_b_laenge);
        $surface_areas = $extension->get_surface_areas();
        break;
    case 'b':
        $extension = new Extension_Form_B();
        $extension->set_height($energieausweis->anbau_hoehe);
        $extension->set_walls($energieausweis->anbauwand_s1_laenge, $energieausweis->anbauwand_s2_laenge, $energieausweis->anbauwand_t_laenge, $energieausweis->anbauwand_b_laenge);
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

        $calculations['bauteile'][ 'anbauwand_' . $wand ] = array(
        'name'          => sprintf(__('Anbau-Wand %s', 'wpenon'), $wand),
        'typ'           => 'wand',
        'modus'         => 'opak',
        'bauart'        => $energieausweis->$anbauwand_bauart_field,
        'baujahr'       => $energieausweis->anbau_baujahr,
        'richtung'      => $hr_mappings[ $data[1] ],
        'a'             => $surface_areas[ $wand ],
        'd'             => $energieausweis->anbauwand_daemmung,
        );

        if (! isset($calculations['anbauwandrichtungen'][ $calculations['bauteile'][ 'anbauwand_' . $wand ]['richtung'] ]) ) {
            $calculations['anbauwandrichtungen'][ $calculations['bauteile'][ 'anbauwand_' . $wand ]['richtung'] ] = array();
        }
        $calculations['anbauwandrichtungen'][ $calculations['bauteile'][ 'anbauwand_' . $wand ]['richtung'] ][] = $wand;
    }
    unset($data);

    // Subtract Anbau overlap from Grundriss manually.
    if (! empty($calculations['wandrichtungen'][ $calculations['bauteile']['anbauwand_t']['richtung'] ]) ) {
        $grundrisswand = $calculations['wandrichtungen'][ $calculations['bauteile']['anbauwand_t']['richtung'] ][0];
        $calculations['bauteile'][ 'wand_' . $grundrisswand ]['a'] -= $calculations['bauteile']['anbauwand_t']['a'] - $calculations['bauteile']['anbauwand_s1']['a'];
        if ($calculations['bauteile'][ 'wand_' . $grundrisswand ]['a'] < 0.0 ) {
            $calculations['bauteile'][ 'wand_' . $grundrisswand ]['a'] = 0.0;
        }
    }
    if ($anbauwand_s2_laenge < $anbauwand_b_laenge && ! empty($calculations['wandrichtungen'][ $calculations['bauteile']['anbauwand_b']['richtung'] ]) ) {
        $grundrisswand = $calculations['wandrichtungen'][ $calculations['bauteile']['anbauwand_b']['richtung'] ][0];
        $calculations['bauteile'][ 'wand_' . $grundrisswand ]['a'] -= $calculations['bauteile']['anbauwand_b']['a'] - $calculations['bauteile']['anbauwand_s2']['a'];
        if ($calculations['bauteile'][ 'wand_' . $grundrisswand ]['a'] < 0.0 ) {
            $calculations['bauteile'][ 'wand_' . $grundrisswand ]['a'] = 0.0;
        }
    }

    $calculations['bauteile']['anbauboden'] = array(
    'name'          => __('Anbau-Boden', 'wpenon'),
    'typ'           => 'boden',
    'modus'         => 'opak',
    'bauart'        => $energieausweis->anbauboden_bauart,
    'baujahr'       => $energieausweis->anbau_baujahr,
    'a'             => $anbaugrundflaeche,
    'd'             => $energieausweis->anbauboden_daemmung,
    );

    $calculations['bauteile']['anbaudach'] = array(
    'name'          => __('Anbau-Dach', 'wpenon'),
    'typ'           => 'dach',
    'modus'         => 'dach',
    'bauart'        => $energieausweis->anbaudach_bauart,
    'baujahr'       => $energieausweis->anbau_baujahr,
    'a'             => $anbaugrundflaeche,
    'd'             => $energieausweis->anbaudach_daemmung,
    );
}

$kellerflaeche = $grundflaeche;
switch ( $energieausweis->keller ) {
case 'beheizt':
    $keller_anteil = $energieausweis->keller_groesse * 0.01;
    $kellerwandhoehe = $energieausweis->keller_hoehe + 0.25;

    $calculations['bauteile']['kellerwand'] = array(
    'name'          => __('Kellerwand', 'wpenon'),
    'typ'           => 'wand',
    'modus'         => 'opak',
    'bauart'        => $energieausweis->keller_bauart,
    'baujahr'       => $energieausweis->baujahr,
    'a'             => $wandlaenge * $kellerwandhoehe * $keller_anteil,
    'd'             => $energieausweis->keller_daemmung,
    );
    $calculations['bauteile']['boden'] = array(
    'name'          => __('Boden', 'wpenon'),
    'typ'           => 'boden',
    'modus'         => 'opak',
    'bauart'        => $energieausweis->boden_bauart,
    'baujahr'       => $energieausweis->baujahr,
    'a'             => $kellerflaeche,
    'd'             => $energieausweis->boden_daemmung,
    );
    $calculations['volumenteile']['keller'] = array(
    'name'          => __('Kellergeschoss', 'wpenon'),
    'v'             => $grundflaeche * $kellerwandhoehe * $keller_anteil,
    );
    break;
case 'unbeheizt':
    $kellerflaeche *= $energieausweis->keller_groesse * 0.01;
    $calculations['bauteile']['kellerdecke'] = array(
    'name'          => __('Kellerdecke', 'wpenon'),
    'typ'           => 'boden',
    'modus'         => 'opak',
    'bauart'        => $energieausweis->boden_bauart,
    'baujahr'       => $energieausweis->baujahr,
    'a'             => $kellerflaeche,
    'd'             => $energieausweis->boden_daemmung,
    );
    if ($kellerflaeche < $grundflaeche ) {
        $calculations['bauteile']['boden'] = array(
        'name'          => __('Boden', 'wpenon'),
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
    'name'          => __('Boden', 'wpenon'),
    'typ'           => 'boden',
    'modus'         => 'opak',
    'bauart'        => $energieausweis->boden_bauart,
    'baujahr'       => $energieausweis->baujahr,
    'a'             => $kellerflaeche,
    'd'             => $energieausweis->boden_daemmung,
    );
}

$deckenflaeche = $grundflaeche;
$dachwinkel_formatted = 0.0;
switch ( $energieausweis->dach ) {
case 'beheizt':
    $dachhoehe = $energieausweis->dach_hoehe;
    $dachflaeche = $dachvolumen = 0.0;
    $dachwinkel = array();
    $dachwandflaechen = array();
    switch ( $energieausweis->dach_form ) {
    case 'walmdach':
        switch ( $energieausweis->grundriss_form ) {
        case 'a':
            if ($wand_a_laenge > $wand_b_laenge ) {
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
            $dach_sh = sqrt(pow($dachhoehe, 2) + pow($dach_b, 2));
            $dach_sw = sqrt(pow($dachhoehe, 2) + pow($dach_x, 2));
            array_push($dachwinkel, atan($dachhoehe / $dach_b), atan($dachhoehe / $dach_x));
            $dachflaeche += 2 * ( 0.5 * $dach_b * $dach_sw + 0.5 * ( $dach_th + $dach_f ) * $dach_sh );
            $dachvolumen += ( 1.0 / 3.0 ) * ( 2 * $dach_b ) * ( 2 * $dach_x ) * $dachhoehe + 0.5 * ( 2 * $dach_b ) * $dach_f * $dachhoehe;
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
            $dach_s1 = sqrt(pow($dachhoehe, 2) + pow($dach_b1, 2));
            $dach_s2 = sqrt(pow($dachhoehe, 2) + pow($dach_b2, 2));
            array_push($dachwinkel, atan($dachhoehe / $dach_b1), atan($dachhoehe / $dach_b2));
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
            $dach_s1 = sqrt(pow($dachhoehe, 2) + pow($dach_b1, 2));
            $dach_s2 = sqrt(pow($dachhoehe, 2) + pow($dach_b2, 2));
            array_push($dachwinkel, atan($dachhoehe / $dach_b1), atan($dachhoehe / $dach_b2));
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
            $dach_s1 = sqrt(pow($dachhoehe, 2) + pow($dach_b1, 2));
            $dach_s2 = sqrt(pow($dachhoehe, 2) + pow($dach_b2, 2));
            $dach_s3 = sqrt(pow($dachhoehe, 2) + pow($dach_b3, 2));
            array_push($dachwinkel, atan($dachhoehe / $dach_b1), atan($dachhoehe / $dach_b2), atan($dachhoehe / $dach_b3));
            $dachflaeche += 0.5 * $dach_b2_gross * $dach_s2 + 0.5 * $dach_b3_gross * $dach_s3 + 0.5 * ( $dach_t1 + $dach_f1 ) * $dach_s2 + 0.5 * ( $dach_t2 + $dach_f2 ) * $dach_s1 + 0.5 * ( $dach_t3 + $dach_f3 ) * $dach_s3 + $dach_t4 * $dach_s2 + 0.5 * ( $dach_t5 + $dach_f2 ) * $dach_s1 + $dach_t6 * $dach_s3;
            $dachvolumen += ( 1.0 / 3.0 ) * $dach_b1_gross * $dach_b1_gross * $dachhoehe + 0.5 * $dach_b1_gross * $dach_f2 * $dachhoehe + 0.5 * $dach_b2_gross * $dach_t4 * $dachhoehe + 0.5 * $dach_b3_gross * $dach_t6 * $dachhoehe;
            break;
        default:
        }
        break;
    case 'pultdach':
        switch ( $energieausweis->grundriss_form ) {
        case 'a':
            if ($wand_a_laenge > $wand_b_laenge ) {
                $dach_s = sqrt(pow($dachhoehe, 2) + pow($wand_b_laenge, 2));
                array_push($dachwinkel, atan($dachhoehe / $wand_b_laenge));
                $dachflaeche += $wand_a_laenge * $dach_s;
                $dachvolumen += 0.5 * $wand_a_laenge * $wand_b_laenge * $dachhoehe;
                $dachwandflaechen['b'] = 0.5 * $wand_b_laenge * $dachhoehe;
                $dachwandflaechen['d'] = 0.5 * $wand_d_laenge * $dachhoehe;
                $dachwandflaechen['c'] = $wand_a_laenge * $dachhoehe;
            } else {
                $dach_s = sqrt(pow($dachhoehe, 2) + pow($wand_a_laenge, 2));
                array_push($dachwinkel, atan($dachhoehe / $wand_a_laenge));
                $dachflaeche += $wand_b_laenge * $dach_s;
                $dachvolumen += 0.5 * $wand_a_laenge * $wand_b_laenge * $dachhoehe;
                $dachwandflaechen['a'] = 0.5 * $wand_a_laenge * $dachhoehe;
                $dachwandflaechen['c'] = 0.5 * $wand_c_laenge * $dachhoehe;
                $dachwandflaechen['d'] = $wand_b_laenge * $dachhoehe;
            }
            break;
        case 'b':
            if ($wand_a_laenge > $wand_b_laenge ) {
                $dach_s1 = sqrt(pow($dachhoehe, 2) + pow($wand_f_laenge, 2));
                $dach_s2 = sqrt(pow($dachhoehe, 2) + pow($wand_d_laenge, 2));
                array_push($dachwinkel, atan($dachhoehe / $wand_f_laenge), atan($dachhoehe / $wand_d_laenge));
                $dachflaeche += $wand_a_laenge * $dach_s1 + $wand_c_laenge * $dach_s2;
                $dachvolumen += 0.5 * $wand_a_laenge * $wand_f_laenge * $dachhoehe + 0.5 * $wand_c_laenge * $wand_d_laenge * $dachhoehe;
                $dachwandflaechen['b'] = 0.5 * $wand_f_laenge * $dachhoehe + 0.5 * $wand_d_laenge * $dachhoehe;
                $dachwandflaechen['d'] = 0.5 * $wand_d_laenge * $dachhoehe;
                $dachwandflaechen['e'] = $wand_e_laenge * $dachhoehe;
                $dachwandflaechen['f'] = 0.5 * $wand_f_laenge * $dachhoehe;
            } else {
                $dach_s1 = sqrt(pow($dachhoehe, 2) + pow($wand_c_laenge, 2));
                $dach_s2 = sqrt(pow($dachhoehe, 2) + pow($wand_e_laenge, 2));
                array_push($dachwinkel, atan($dachhoehe / $wand_c_laenge), atan($dachhoehe / $wand_e_laenge));
                $dachflaeche += $wand_b_laenge * $dach_s1 + $wand_f_laenge * $dach_s2;
                $dachvolumen += 0.5 * $wand_b_laenge * $wand_c_laenge * $dachhoehe + 0.5 * $wand_f_laenge * $wand_e_laenge * $dachhoehe;
                $dachwandflaechen['a'] = 0.5 * $wand_c_laenge * $dachhoehe + 0.5 * $wand_e_laenge * $dachhoehe;
                $dachwandflaechen['c'] = 0.5 * $wand_c_laenge * $dachhoehe;
                $dachwandflaechen['d'] = $wand_d_laenge * $dachhoehe;
                $dachwandflaechen['e'] = 0.5 * $wand_e_laenge * $dachhoehe;
            }
            break;
        case 'c':
            $dach_s1 = sqrt(pow($dachhoehe, 2) + pow($wand_b_laenge, 2));
            $dach_s2 = sqrt(pow($dachhoehe, 2) + pow($wand_d_laenge, 2));
            array_push($dachwinkel, atan($dachhoehe / $wand_b_laenge), atan($dachhoehe / $wand_d_laenge));
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
            $dach_s1 = sqrt(pow($dachhoehe, 2) + pow($wand_b_laenge - $wand_d_laenge, 2));
            $dach_s2 = sqrt(pow($dachhoehe, 2) + pow($wand_d_laenge, 2));
            $dach_s3 = sqrt(pow($dachhoehe, 2) + pow($wand_f_laenge, 2));
            array_push($dachwinkel, atan($dachhoehe / ( $wand_b_laenge - $wand_d_laenge )), atan($dachhoehe / $wand_d_laenge), atan($dachhoehe / $wand_f_laenge));
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
            if ($wand_a_laenge > $wand_b_laenge ) {
                $dach_s = sqrt(pow($dachhoehe, 2) + pow(0.5 * $wand_b_laenge, 2));
                array_push($dachwinkel, atan($dachhoehe / ( 0.5 * $wand_b_laenge )));
                $dachflaeche += $wand_a_laenge * $dach_s + $wand_c_laenge * $dach_s;
                $dachvolumen += 0.5 * $wand_a_laenge * $wand_b_laenge * $dachhoehe;
                $dachwandflaechen['b'] = 0.5 * $wand_b_laenge * $dachhoehe;
                $dachwandflaechen['d'] = 0.5 * $wand_d_laenge * $dachhoehe;
            } else {
                $dach_s = sqrt(pow($dachhoehe, 2) + pow(0.5 * $wand_a_laenge, 2));
                array_push($dachwinkel, atan($dachhoehe / ( 0.5 * $wand_a_laenge )));
                $dachflaeche += $wand_b_laenge * $dach_s + $wand_d_laenge * $dach_s;
                $dachvolumen += 0.5 * $wand_a_laenge * $wand_b_laenge * $dachhoehe;
                $dachwandflaechen['a'] = 0.5 * $wand_a_laenge * $dachhoehe;
                $dachwandflaechen['c'] = 0.5 * $wand_c_laenge * $dachhoehe;
            }
            break;
        case 'b':
            if ($wand_a_laenge > $wand_b_laenge ) {
                $dach_s1 = sqrt(pow($dachhoehe, 2) + pow(0.5 * $wand_f_laenge, 2));
                $dach_s2 = sqrt(pow($dachhoehe, 2) + pow(0.5 * $wand_c_laenge, 2));
                array_push($dachwinkel, atan($dachhoehe / ( 0.5 * $wand_f_laenge )), atan($dachhoehe / ( 0.5 * $wand_c_laenge )));
                $dachflaeche += 2 * ( $wand_a_laenge - 0.25 * $wand_c_laenge ) * $dach_s1 + 2 * ( $wand_d_laenge + 0.25 * $wand_f_laenge ) * $dach_s2;
                $dachvolumen += 0.5 * $wand_a_laenge * $wand_f_laenge * $dachhoehe + 0.5 * $wand_d_laenge * $wand_c_laenge * $dachhoehe + ( 1.0 / 3.0 ) * ( 0.5 * $wand_c_laenge * $dachhoehe ) * ( 0.5 * $wand_f_laenge );
                $dachwandflaechen['b'] = 0.5 * $wand_f_laenge * $dachhoehe;
                $dachwandflaechen['c'] = 0.5 * $wand_c_laenge * $dachhoehe;
                $dachwandflaechen['f'] = 0.5 * $wand_f_laenge * $dachhoehe;
            } else {
                $dach_s1 = sqrt(pow($dachhoehe, 2) + pow(0.5 * $wand_c_laenge, 2));
                $dach_s2 = sqrt(pow($dachhoehe, 2) + pow(0.5 * $wand_f_laenge, 2));
                array_push($dachwinkel, atan($dachhoehe / ( 0.5 * $wand_c_laenge )), atan($dachhoehe / ( 0.5 * $wand_f_laenge )));
                $dachflaeche += 2 * ( $wand_b_laenge - 0.25 * $wand_f_laenge ) * $dach_s1 + 2 * ( $wand_e_laenge + 0.25 * $wand_c_laenge ) * $dach_s2;
                $dachvolumen += 0.5 * $wand_b_laenge * $wand_c_laenge * $dachhoehe + 0.5 * $wand_e_laenge * $wand_f_laenge * $dachhoehe + ( 1.0 / 3.0 ) * ( 0.5 * $wand_f_laenge * $dachhoehe ) * ( 0.5 * $wand_c_laenge );
                $dachwandflaechen['a'] = 0.5 * $wand_c_laenge * $dachhoehe;
                $dachwandflaechen['c'] = 0.5 * $wand_c_laenge * $dachhoehe;
                $dachwandflaechen['f'] = 0.5 * $wand_f_laenge * $dachhoehe;
            }
            break;
        case 'c':
            $dach_s1 = sqrt(pow($dachhoehe, 2) + pow(0.5 * $wand_b_laenge, 2));
            $dach_s2 = sqrt(pow($dachhoehe, 2) + pow(0.5 * $wand_e_laenge, 2));
            array_push($dachwinkel, atan($dachhoehe / ( 0.5 * $wand_b_laenge )), atan($dachhoehe / ( 0.5 * $wand_e_laenge )));
            $dachflaeche += 2 * ( $wand_a_laenge - 0.25 * $wand_e_laenge ) * $dach_s1 + 2 * ( $wand_d_laenge + 0.25 * $wand_b_laenge ) * $dach_s2;
            $dachvolumen += 0.5 * $wand_a_laenge * $wand_b_laenge * $dachhoehe + 0.5 * $wand_e_laenge * $wand_d_laenge * $dachhoehe + ( 1.0 / 3.0 ) * ( 0.5 * $wand_e_laenge * $dachhoehe ) * ( 0.5 * $wand_b_laenge );
            $dachwandflaechen['b'] = 0.5 * $wand_b_laenge * $dachhoehe;
            $dachwandflaechen['e'] = 0.5 * $wand_e_laenge * $dachhoehe;
            $dachwandflaechen['h'] = 0.5 * $wand_h_laenge * $dachhoehe;
            break;
        case 'd':
            $dach_s1 = sqrt(pow($dachhoehe, 2) + pow(0.5 * ( $wand_b_laenge - $wand_d_laenge ), 2));
            $dach_s2 = sqrt(pow($dachhoehe, 2) + pow(0.5 * $wand_c_laenge, 2));
            $dach_s3 = sqrt(pow($dachhoehe, 2) + pow(0.5 * $wand_g_laenge, 2));
            array_push($dachwinkel, atan($dachhoehe / ( 0.5 * ( $wand_b_laenge - $wand_d_laenge ) )), atan($dachhoehe / ( 0.5 * $wand_c_laenge )), atan($dachhoehe / ( 0.5 * $wand_g_laenge )));
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

    $_dachwinkel = $dachwinkel;
    $dachwinkel = 0.0;
    foreach ( $_dachwinkel as $w ) {
        $dachwinkel += $w * 180.0 / pi();
    }
    $dachwinkel_formatted = $dachwinkel / count($_dachwinkel);
    $dachwinkel = $dachwinkel_formatted * pi() / 180.0;

    foreach ( $dachwandflaechen as $wand => $flaeche ) {
        if (isset($calculations['bauteile'][ 'wand_' . $wand ]) ) {
            $calculations['bauteile'][ 'wand_' . $wand ]['a'] += $flaeche;
        }
    }
    if( $energieausweis->kniestock_hoehe > 0 ) {    
        foreach( $grundriss_form as $wand => $data ) {
            $calculations['bauteile'][ 'wand_' . $wand ]['a'] += $energieausweis->kniestock_hoehe * $energieausweis->{'wand_' . $wand . '_laenge'};
        }    
    }

    $calculations['bauteile']['dach'] = array(
    'name'          => __('Dach', 'wpenon'),
    'typ'           => 'dach',
    'modus'         => 'dach',
    'bauart'        => $energieausweis->dach_bauart,
    'baujahr'       => $energieausweis->baujahr,
    'a'             => $dachflaeche,
    'd'             => $energieausweis->dach_daemmung,
    );

    $calculations['volumenteile']['dach'] = array(
    'name'          => __('Dachgeschoss', 'wpenon'),
    'v'             => $dachvolumen,
    );
    break;
case 'unbeheizt':
    $calculations['bauteile']['decke'] = array(
    'name'          => __('Oberste Geschossdecke', 'wpenon'),
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
    'name'          => __('Flachdach', 'wpenon'),
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

$calculations['huellvolumen'] = 0.0;
foreach ( $calculations['volumenteile'] as $slug => $data ) {
    $calculations['huellvolumen'] += $data['v'];
}
unset($data);

if ($geschosshoehe >= 2.5 && $geschosshoehe <= 3.0 ) {
    $calculations['nutzflaeche'] = $calculations['huellvolumen'] * 0.32;
} else {
    $calculations['nutzflaeche'] = $calculations['huellvolumen'] * ( 1.0 / $geschosshoehe - 0.04 );
}

/*************************************************
 * BAUTEILE TRANSPARENT etc.
 *************************************************/

foreach ( $grundriss_form as $wand => $data ) {
    if (isset($calculations['bauteile'][ 'wand_' . $wand ]) ) {
  
        // Automatische Berechnung
        $l_slug = 'wand_' . $wand . '_laenge';
        $n_slug = 'wand_' . $wand . '_nachbar';    

        if ($energieausweis->$n_slug === true ) {
            continue;
        }

        // Fensterfläche Wand a: 0,55 * (Wandlänge a - 2 * Wandstärke) * ((Geschosshöhe - 1,50 m) * Anzahl Vollgeschoss)
        $fensterflaeche = 0.55 * ( $energieausweis->$l_slug - 2 * $energieausweis->wand_staerke / 100 ) * ( ( $energieausweis->geschoss_hoehe - 1.5 ) * $energieausweis->geschoss_zahl );

        $calculations['bauteile'][ 'fenster_' . $wand ] = array(
        'name'          => sprintf(__('Fenster Wand %s', 'wpenon'), $wand),
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
  
    }

}
unset($data);

if ($energieausweis->anbau ) {
    foreach ( $anbau_form as $wand => $data ) {      
        if (isset($calculations['bauteile'][ 'anbauwand_' . $wand ]) ) {
            $a_slug = 'anbaufenster_' . $wand . '_flaeche';

            $l_slug = 'anbauwand_' . $wand . '_laenge';

            $b_laenge = $energieausweis->anbauwand_b_laenge;
            $t_laenge = $energieausweis->anbauwand_t_laenge;
            $s1_laenge = $energieausweis->anbauwand_s1_laenge;
            $s2_laenge = $energieausweis->anbauwand_s2_laenge;

            $anbauwand_staerke = $energieausweis->anbauwand_staerke / 100; // Umrechnen von cm in m
            $geschoss_hoehe     = $energieausweis->geschoss_hoehe;

            if ($energieausweis->anbau_form === 'a' ) {
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
                      // Fensterfläche Wand s2: 0,55 * (Anbau Schnittlänge s2 - 2 * Wandstärke Anbau) * (Höhe des Anbau - 1,50 m) 
                      $fensterflaeche = 0.55 * ( $s2_laenge - ( 2 * $anbauwand_staerke ) ) * ( $geschoss_hoehe - 1.5 );
                    break;
          
                }
            }

            if ($energieausweis->anbau_form === 'b' ) {
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

            if ($fensterflaeche < 0 ) {
                $fensterflaeche = 0;
            }

            $calculations['bauteile'][ 'anbaufenster_' . $wand ] = array(
            'name'          => sprintf(__('Anbau-Fenster Wand %s', 'wpenon'), $wand),
            'typ'           => 'fenster',
            'modus'         => 'transparent',
            'bauart'        => $energieausweis->anbaufenster_bauart,
            'baujahr'       => $energieausweis->anbaufenster_baujahr,
            'richtung'      => $calculations['bauteile'][ 'anbauwand_' . $wand ]['richtung'],
            'a'             => $fensterflaeche,
            'd'             => 0,
            'winkel'        => 90.0,
            );

            $calculations['bauteile'][ 'anbauwand_' . $wand ]['a'] -= $calculations['bauteile'][ 'anbaufenster_' . $wand ]['a']; 
        }
    }
    unset($data);
}


if ($energieausweis->heizkoerpernischen == 'vorhanden' ) {
    foreach ( $grundriss_form as $wand => $data ) {
        if (isset($calculations['bauteile'][ 'fenster_' . $wand ]) ) {
            $flaeche = $calculations['bauteile'][ 'fenster_' . $wand ]['a'] * 0.5;
            $calculations['bauteile'][ 'wand_' . $wand ]['a'] -= $flaeche;
            if ($calculations['bauteile'][ 'wand_' . $wand ]['a'] < 0.0 ) {
                $flaeche += $calculations['bauteile'][ 'wand_' . $wand ]['a'];
                $calculations['bauteile'][ 'wand_' . $wand ]['a'] -= $calculations['bauteile'][ 'wand_' . $wand ]['a'];
            }
            if ($flaeche > 0.0 ) {
                $calculations['bauteile'][ 'heizkoerpernischen_' . $wand ] = array(
                'name'          => sprintf(__('Heizkörpernischen Wand %s', 'wpenon'), $wand),
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
    unset($data);
}

if (substr($energieausweis->rollladenkaesten, 0, 6) == 'innen_' ) {
    $bauart = str_replace('innen_', '', $energieausweis->rollladenkaesten);
    foreach ( $grundriss_form as $wand => $data ) {
        if (isset($calculations['bauteile'][ 'fenster_' . $wand ]) ) {
            $flaeche = $calculations['bauteile'][ 'fenster_' . $wand ]['a'] * 0.1;
            $calculations['bauteile'][ 'wand_' . $wand ]['a'] -= $flaeche;
            if ($calculations['bauteile'][ 'wand_' . $wand ]['a'] < 0.0 ) {
                $flaeche += $calculations['bauteile'][ 'wand_' . $wand ]['a'];
                $calculations['bauteile'][ 'wand_' . $wand ]['a'] -= $calculations['bauteile'][ 'wand_' . $wand ]['a'];
            }
            if ($flaeche > 0.0 ) {
                $calculations['bauteile'][ 'rollladenkaesten_' . $wand ] = array(
                'name'          => sprintf(__('Rollladenkästen Wand %s', 'wpenon'), $wand),
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
    unset($data);
}

$fxwerte = array(
  'decke'           => 0.8,
  'kellerwand'      => 0.6,
  'boden'           => 0.6,
);
$uwerte = wpenon_get_table_results($tableNames->uwerte);
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
    if ($data['a'] < 0.0 ) {
        $data['a'] = 0.0;
    }

    $data['fx'] = isset($fxwerte[ $slug ]) ? $fxwerte[ $slug ] : 1.0;

    if ($data['typ'] == 'heizkoerpernischen' ) {
        $wand = str_replace('heizkoerpernischen_', '', $slug);
        $data['u'] = $calculations['bauteile'][ 'wand_' . $wand ]['d'] > 0.0 ? $calculations['bauteile'][ 'wand_' . $wand ]['u'] : 2 * $calculations['bauteile'][ 'wand_' . $wand ]['u'];
        $data['u_reference'] = 2 * $uwerte_reference['wand'];
    } else {
        $uslug = $data['typ'];
        if (! empty($data['bauart']) ) {
            $uslug .= '_' . $data['bauart'];
        }

        // Special wish (26.02.2020
        // Wenn Dach beheizt ist, dann immer die Uwerte von dach aus holz.
        if ('dach' === $slug && 'beheizt' === $energieausweis->dach ) {
            $uslug = 'dach_holz';
        }

        if (isset($uwerte[ $uslug ]) ) {
            $yearkey = wpenon_immoticket24_make_yearkey($data['baujahr'], $tableNames->uwerte);
            $data['u'] = $uwerte[ $uslug ]->$yearkey;
        } else {
            $data['u'] = 1.0;
        }

        if (( $daemmung = $data['d'] ) > 0 ) {
            $daemmung /= 100.0;
            $data['u'] = 1.0 / ( 1.0 / $data['u'] + $daemmung / 0.04 );
        }
        $data['u_reference'] = isset($uwerte_reference[ $data['typ'] ]) ? $uwerte_reference[ $data['typ'] ] : 1.0;
    }
}
unset($data);

/*************************************************
 * BAUTEILE ERGEBNIS
 *************************************************/

$calculations['huellflaeche'] = 0.0;
$calculations['ht'] = 0.0;
$calculations['ht_reference'] = 0.0;
$calculations['hw'] = 0.0;
$calculations['hw_reference'] = 0.0;
foreach ( $calculations['bauteile'] as $slug => $data ) {
    $calculations['huellflaeche'] += $data['a'];
    $calculations['ht'] += $data['fx'] * $data['u'] * $data['a'];
    $calculations['ht_reference'] += $data['fx'] * $data['u_reference'] * $data['a'];
    if (in_array($data['typ'], array( 'fenster', 'tuer' )) ) {
        $calculations['hw'] += $data['fx'] * $data['u'] * $data['a'];
        $calculations['hw_reference'] += $data['fx'] * $data['u_reference'] * $data['a'];
    }
}
unset($data);
$calculations['ht'] += 0.1 * $calculations['huellflaeche'];
$calculations['ht_reference'] += 0.1 * $calculations['huellflaeche'];

$calculations['ave_verhaeltnis'] = $calculations['huellflaeche'] / $calculations['huellvolumen'];

$calculations['hv'] = 0.0;
$calculations['hv_reference'] = 0.0;

$hv_mpk2 = 0.55;
if ($energieausweis->l_info != 'anlage' ) {
    if ($energieausweis->dichtheit ) {
        $hv_mpk2 = 0.6;
    } else {
        $hv_mpk2 = 0.7;
    }
}

/**
 * Luftwechsel neu
 */
$gebaeudedichtheit = 'andere';
if ($energieausweis->dichtheit ) {
    $gebaeudedichtheit = 'din_4108_7';
}

// Netto Hüllvolumen
$hv_net = $energieausweis->geschoss_zahl < 4 ? 0.76 * $calculations['huellvolumen']: 0.8 * $calculations['huellvolumen'];

$luftwechsel = new Luftwechsel(
    baujahr: $energieausweis->baujahr,
    huellflaeche: $calculations['huellflaeche'],
    nettovolumen: $hv_net,
    lueftungssystem: $energieausweis->l_info,
    bedarfsgefuehrt: $energieausweis->l_bedarfsgefuehrt, 
    gebaeudedichtheit: $gebaeudedichtheit,
    wirkunksgrad: (float) $energieausweis->l_wirkungsgrad
);

$calculations['n0'] = $luftwechsel->n0();
$calculations['n'] = $luftwechsel->n();
$calculations['hv_net'] = $hv_net;
$calculations['av_ratio'] = $luftwechsel->av_ratio();
$calculations['hv'] = $luftwechsel->hv();
$calculations['fwin1'] = $luftwechsel->fwin1();
$calculations['fwin2'] = $luftwechsel->fwin2();

$hv_mpk2 = $luftwechsel->hv();

// Ende Luftwechsel neu
// $calculations['ht_max'] = $luftwechsel->ht_max();
$calculations['hv_reference'] += $hv_mpk1 * $calculations['huellvolumen'] * 0.55 * 0.34;

$calculations['h'] = $calculations['ht'] + $calculations['hv'];
$calculations['h_reference'] = $calculations['ht_reference'] + $calculations['hv_reference'];

/*************************************************
 * HEIZWÄRMEBEDARF
 *************************************************/

$calculations['cwirk'] = ( $energieausweis->wand_bauart == 'holz' ? 15 : 50 ) * $calculations['huellvolumen'];
$calculations['cwirk_reference'] = 50 * $calculations['huellvolumen'];
$calculations['tau'] = $calculations['cwirk'] / $calculations['h'];
$calculations['tau_reference'] = $calculations['cwirk_reference'] / $calculations['h_reference'];
$calculations['faktor_a'] = 1.0 + $calculations['tau'] / 16.0;
$calculations['faktor_a_reference'] = 1.0 + $calculations['tau_reference'] / 16.0;

$monate = wpenon_get_table_results('monate');
$solar_gewinn_mpk = 0.9 * 1.0 * 0.9;
$solar_gewinn_mpk_reference = $solar_gewinn_mpk * 0.7;
if ($energieausweis->anlass == 'neubau' ) {
    $solar_gewinn_mpk *= 0.7;
} else {
    $solar_gewinn_mpk *= 0.6;
}

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
    $calculations['monate'][ $monat ]['tage'] = absint($monatsdaten->tage);
    $calculations['monate'][ $monat ]['temperatur'] = floatval($monatsdaten->temperatur);

    // Transmissionswärmeverluste Qt
    $calculations['monate'][ $monat ]['qt'] = $calculations['ht'] * 0.024 * ( 19.0 - $calculations['monate'][ $monat ]['temperatur'] ) * $calculations['monate'][ $monat ]['tage'];
    $calculations['monate'][ $monat ]['qt_reference'] = $calculations['ht_reference'] * 0.024 * ( 19.0 - $calculations['monate'][ $monat ]['temperatur'] ) * $calculations['monate'][ $monat ]['tage'];

    // Lüftungswärmeverluste Qv
    $calculations['monate'][ $monat ]['qv'] = $calculations['hv'] * 0.024 * ( 19.0 - $calculations['monate'][ $monat ]['temperatur'] ) * $calculations['monate'][ $monat ]['tage'];
    $calculations['monate'][ $monat ]['qv_reference'] = $calculations['hv_reference'] * 0.024 * ( 19.0 - $calculations['monate'][ $monat ]['temperatur'] ) * $calculations['monate'][ $monat ]['tage'];

    // Gesamtverluste Ql
    $calculations['monate'][ $monat ]['ql'] = $calculations['monate'][ $monat ]['qt'] + $calculations['monate'][ $monat ]['qv'];
    $calculations['monate'][ $monat ]['ql_reference'] = $calculations['monate'][ $monat ]['qt_reference'] + $calculations['monate'][ $monat ]['qv_reference'];

    // interne Gewinne Qi
    $calculations['monate'][ $monat ]['qi'] = 5.0 * $calculations['nutzflaeche'] * 0.024 * $calculations['monate'][ $monat ]['tage'];
    $calculations['monate'][ $monat ]['qi_reference'] = 5.0 * $calculations['nutzflaeche'] * 0.024 * $calculations['monate'][ $monat ]['tage'];

    // solare Gewinne Qs
    $calculations['monate'][ $monat ]['qs'] = 0.0;
    $calculations['monate'][ $monat ]['qs_reference'] = 0.0;
    foreach ( $calculations['bauteile'] as $slug => $data ) {
        if ($data['typ'] == 'fenster' ) {
            $winkel = isset($data['winkel']) ? $data['winkel'] : 90.0;
            $strahlungsfaktor = 0.0;
            if ($winkel > 0.0 && $winkel < 90.0 ) {
                $prefix = 'w_' . $data['richtung'];
                $str30 = $prefix . '30';
                $str45 = $prefix . '45';
                $str60 = $prefix . '60';
                $str90 = $prefix . '90';
                $strahlungsfaktor = wpenon_interpolate(
                    $winkel, array(
                    array(
                    'keysize'   => 0,
                    'value'     => $monatsdaten->w_0,
                    ),
                    array(
                    'keysize'   => 30,
                    'value'     => $monatsdaten->$str30,
                    ),
#                    'keysize'   => 45,
                    'value'     => $monatsdaten->$str45,
                    ),
                    array(
                    'keysize'   => 60,
                    'value'     => $monatsdaten->$str60,
                    ),
                    array(
                    'keysize'   => 90,
                    'value'     => $monatsdaten->$str90,
                    ),
                );
            } elseif ($winkel >= 90.0 ) {
                $str90 = 'w_' . $data['richtung'] . '90';
                $strahlungsfaktor = $monatsdaten->$str90;
            } else {
                $strahlungsfaktor = $monatsdaten->w_0;
            }
            $calculations['monate'][ $monat ]['qs'] += $strahlungsfaktor * $solar_gewinn_mpk * wpenon_immoticket24_get_g_wert($data['bauart']) * $data['a'] * 0.024 * $calculations['monate'][ $monat ]['tage'];
            $calculations['monate'][ $monat ]['qs_reference'] += $strahlungsfaktor * $solar_gewinn_mpk_reference * wpenon_immoticket24_get_g_wert($data['bauart'], true) * $data['a'] * 0.024 * $calculations['monate'][ $monat ]['tage'];
        }
    }
    unset($data);

    // Gesamtgewinne Qg
    $calculations['monate'][ $monat ]['qg'] = $calculations['monate'][ $monat ]['qi'] + $calculations['monate'][ $monat ]['qs'];
    $calculations['monate'][ $monat ]['qg_reference'] = $calculations['monate'][ $monat ]['qi_reference'] + $calculations['monate'][ $monat ]['qs_reference'];

    // Korrekturfaktoren
    $calculations['monate'][ $monat ]['gamma'] = $calculations['monate'][ $monat ]['qg'] / ( $calculations['monate'][ $monat ]['ql'] > 0.0 ? $calculations['monate'][ $monat ]['ql'] : 1.0 );
    $calculations['monate'][ $monat ]['gamma_reference'] = $calculations['monate'][ $monat ]['qg_reference'] / ( $calculations['monate'][ $monat ]['ql_reference'] > 0.0 ? $calculations['monate'][ $monat ]['ql_reference'] : 1.0 );
    $calculations['monate'][ $monat ]['my'] = 0.0;
    $calculations['monate'][ $monat ]['my_reference'] = 0.0;
    if ($calculations['monate'][ $monat ]['gamma'] == 1.0 ) {
        $calculations['monate'][ $monat ]['my'] = $calculations['faktor_a'] / ( $calculations['faktor_a'] + 1.0 );
    } else {
        $calculations['monate'][ $monat ]['my'] = ( 1.0 - pow($calculations['monate'][ $monat ]['gamma'], $calculations['faktor_a']) ) / ( 1.0 - pow($calculations['monate'][ $monat ]['gamma'], $calculations['faktor_a'] + 1.0) );
    }
    if ($calculations['monate'][ $monat ]['gamma_reference'] == 1.0 ) {
        $calculations['monate'][ $monat ]['my_reference'] = $calculations['faktor_a_reference'] / ( $calculations['faktor_a_reference'] + 1.0 );
    } else {
        $calculations['monate'][ $monat ]['my_reference'] = ( 1.0 - pow($calculations['monate'][ $monat ]['gamma_reference'], $calculations['faktor_a_reference']) ) / ( 1.0 - pow($calculations['monate'][ $monat ]['gamma_reference'], $calculations['faktor_a_reference'] + 1.0) );
    }

    // Heizwärmebedarf Qh
    $calculations['monate'][ $monat ]['qh'] = $calculations['monate'][ $monat ]['ql'] - $calculations['monate'][ $monat ]['my'] * $calculations['monate'][ $monat ]['qg'];
    $calculations['monate'][ $monat ]['qh_reference'] = $calculations['monate'][ $monat ]['ql_reference'] - $calculations['monate'][ $monat ]['my_reference'] * $calculations['monate'][ $monat ]['qg_reference'];
    if ($calculations['monate'][ $monat ]['qh'] < 0.0 ) {
        $calculations['monate'][ $monat ]['qh'] = 0.0;
    }
    if ($calculations['monate'][ $monat ]['qh_reference'] < 0.0 ) {
        $calculations['monate'][ $monat ]['qh_reference'] = 0.0;
    }

    // Hinzufügen zu globalen Ergebnissen
    $calculations['qh'] += $calculations['monate'][ $monat ]['qh'];
    $calculations['qh_reference'] += $calculations['monate'][ $monat ]['qh_reference'];
    $calculations['qt'] += $calculations['monate'][ $monat ]['qt'];
    $calculations['qt_reference'] += $calculations['monate'][ $monat ]['qt_reference'];
    $calculations['qv'] += $calculations['monate'][ $monat ]['qv'];
    $calculations['qv_reference'] += $calculations['monate'][ $monat ]['qv_reference'];
    $calculations['ql'] += $calculations['monate'][ $monat ]['ql'];
    $calculations['ql_reference'] += $calculations['monate'][ $monat ]['ql_reference'];
    $calculations['qi'] += $calculations['monate'][ $monat ]['qi'];
    $calculations['qi_reference'] += $calculations['monate'][ $monat ]['qi_reference'];
    $calculations['qs'] += $calculations['monate'][ $monat ]['qs'];
    $calculations['qs_reference'] += $calculations['monate'][ $monat ]['qs_reference'];
    $calculations['qg'] += $calculations['monate'][ $monat ]['qg'];
    $calculations['qg_reference'] += $calculations['monate'][ $monat ]['qg_reference'];
}

$calculations['qw'] = 12.5 * $calculations['nutzflaeche'];
$calculations['qw_reference'] = 12.5 * $calculations['nutzflaeche'];

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

$calculations['anlagendaten'] = array();
$calculations['verteilung'] = array();
$calculations['speicherung'] = array();
$calculations['uebergabe'] = array();

$aaa = $energieausweis->h_erzeugung;

$h_energietraeger_name = 'h_energietraeger_' . $energieausweis->h_erzeugung;
$h_energietraeger_value = $energieausweis->$h_energietraeger_name;

$h_erzeugung = wpenon_get_table_results($tableNames->h_erzeugung, array( 'bezeichnung' => array( 'value' => $energieausweis->h_erzeugung, 'compare' => '=' ) ), array(), true);
$h_energietraeger = wpenon_get_table_results($tableNames->energietraeger, array( 'bezeichnung' => array( 'value' => $h_energietraeger_value, 'compare' => '=' ) ), array(), true);
$h_yearkey = wpenon_immoticket24_make_yearkey($energieausweis->h_baujahr, $tableNames->h_erzeugung);
list( $h_ep150, $h_ep500, $h_ep2500 ) = wpenon_immoticket24_make_anlagenkeys('ep', $h_yearkey);
list( $h_he150, $h_he500, $h_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $h_yearkey);

$calculations['anlagendaten']['h'] = array(
  'name'                    => $h_erzeugung->name,
  'slug'                    => $h_erzeugung->bezeichnung,
  'art'                     => 'heizung',
  'typ'                     => $h_erzeugung->typ,
  'baujahr'                 => $energieausweis->h_baujahr,
  'energietraeger'          => $h_energietraeger->name,
  'energietraeger_slug'     => $h_energietraeger->bezeichnung,
  'energietraeger_primaer'  => $energieausweis->h_custom ? floatval($energieausweis->h_custom_primaer) : floatval($h_energietraeger->primaer),
  'energietraeger_co2'      => $energieausweis->h_custom_2 ? floatval($energieausweis->h_custom_co2) : floatval($h_energietraeger->co2),
  'speicher_slug'           => $h_erzeugung->speicher,
  'uebergabe_slug'          => $h_erzeugung->uebergabe,
  'heizkreistemperatur'     => $h_erzeugung->hktemp,
'aufwandszahl'            => wpenon_interpolate(
    $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $h_erzeugung->$h_ep150 ),
    array( 'keysize' => 500, 'value' => $h_erzeugung->$h_ep500 ),
    array( 'keysize' => 2500, 'value' => $h_erzeugung->$h_ep2500 ),
    ) 
),
'hilfsenergie'            => wpenon_interpolate(
    $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $h_erzeugung->$h_he150 ),
    array( 'keysize' => 500, 'value' => $h_erzeugung->$h_he500 ),
    array( 'keysize' => 2500, 'value' => $h_erzeugung->$h_he2500 ),
    ) 
),
  'deckungsanteil'          => 100,
);
$h_max_anteil = 'h';
$anteilsumme = 100;

if ($energieausweis->h2_info ) {
    if ($energieausweis->h_deckungsanteil > 0 ) {
        $calculations['anlagendaten']['h']['deckungsanteil'] = $energieausweis->h_deckungsanteil;
    } else {
        unset($calculations['anlagendaten']['h']);
    }

    $anteilsumme = $energieausweis->h_deckungsanteil;

    $h2_energietraeger_name = 'h2_energietraeger_' . $energieausweis->h2_erzeugung;
    $h2_energietraeger_value = $energieausweis->$h2_energietraeger_name;

    if ($energieausweis->h2_deckungsanteil > 0 ) {
        $h2_erzeugung = wpenon_get_table_results($tableNames->h_erzeugung, array( 'bezeichnung' => array( 'value' => $energieausweis->h2_erzeugung, 'compare' => '=' ) ), array(), true);
        $h2_energietraeger = wpenon_get_table_results($tableNames->energietraeger, array( 'bezeichnung' => array( 'value' => $h2_energietraeger_value, 'compare' => '=' ) ), array(), true);

        $h2_yearkey = wpenon_immoticket24_make_yearkey($energieausweis->h2_baujahr, $tableNames->h_erzeugung);

        list( $h2_ep150, $h2_ep500, $h2_ep2500 ) = wpenon_immoticket24_make_anlagenkeys('ep', $h2_yearkey);
        list( $h2_he150, $h2_he500, $h2_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $h2_yearkey);
        $calculations['anlagendaten']['h2'] = array(
        'name'                    => $h2_erzeugung->name,
        'slug'                    => $h2_erzeugung->bezeichnung,
        'art'                     => 'heizung',
        'typ'                     => $h2_erzeugung->typ,
        'baujahr'                 => $energieausweis->h2_baujahr,
        'energietraeger'          => $h2_energietraeger->name,
        'energietraeger_slug'     => $h2_energietraeger->bezeichnung,
        'energietraeger_primaer'  => $energieausweis->h2_custom ? floatval($energieausweis->h2_custom_primaer) : floatval($h2_energietraeger->primaer),
        'energietraeger_co2'      => $energieausweis->h2_custom_2 ? floatval($energieausweis->h2_custom_co2) : floatval($h2_energietraeger->co2),
        'speicher_slug'           => $h2_erzeugung->speicher,
        'uebergabe_slug'          => $h2_erzeugung->uebergabe,
        'heizkreistemperatur'     => $h2_erzeugung->hktemp,
        'aufwandszahl'            => wpenon_interpolate(
            $calculations['nutzflaeche'], array(
            array( 'keysize' => 150, 'value' => $h2_erzeugung->$h2_ep150 ),
            array( 'keysize' => 500, 'value' => $h2_erzeugung->$h2_ep500 ),
            array( 'keysize' => 2500, 'value' => $h2_erzeugung->$h2_ep2500 ),
            ) 
        ),
        'hilfsenergie'            => wpenon_interpolate(
            $calculations['nutzflaeche'], array(
            array( 'keysize' => 150, 'value' => $h2_erzeugung->$h2_he150 ),
            array( 'keysize' => 500, 'value' => $h2_erzeugung->$h2_he500 ),
            array( 'keysize' => 2500, 'value' => $h2_erzeugung->$h2_he2500 ),
            ) 
        ),
        'deckungsanteil'          => $energieausweis->h2_deckungsanteil,
        );

        $anteilsumme += $calculations['anlagendaten']['h2']['deckungsanteil'];

        if ($calculations['anlagendaten']['h2']['deckungsanteil'] > $calculations['anlagendaten']['h']['deckungsanteil'] ) {
            $h_max_anteil = 'h2';
        }
    }

    if ($energieausweis->h3_info && $energieausweis->h3_deckungsanteil > 0 ) {
        $h3_energietraeger_name = 'h3_energietraeger_' . $energieausweis->h3_erzeugung;
        $h3_energietraeger_value = $energieausweis->$h3_energietraeger_name;

        $h3_erzeugung = wpenon_get_table_results($tableNames->h_erzeugung, array( 'bezeichnung' => array( 'value' => $energieausweis->h3_erzeugung, 'compare' => '=' ) ), array(), true);
        $h3_energietraeger = wpenon_get_table_results($tableNames->energietraeger, array( 'bezeichnung' => array( 'value' => $h3_energietraeger_value, 'compare' => '=' ) ), array(), true);

        $h3_yearkey = wpenon_immoticket24_make_yearkey($energieausweis->h3_baujahr, $tableNames->h_erzeugung);

        list( $h3_ep150, $h3_ep500, $h3_ep2500 ) = wpenon_immoticket24_make_anlagenkeys('ep', $h3_yearkey);
        list( $h3_he150, $h3_he500, $h3_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $h3_yearkey);
        $calculations['anlagendaten']['h3'] = array(
        'name'                    => $h3_erzeugung->name,
        'slug'                    => $h3_erzeugung->bezeichnung,
        'art'                     => 'heizung',
        'typ'                     => $h3_erzeugung->typ,
        'baujahr'                 => $energieausweis->h3_baujahr,
        'energietraeger'          => $h3_energietraeger->name,
        'energietraeger_slug'     => $h3_energietraeger->bezeichnung,
        'energietraeger_primaer'  => $energieausweis->h3_custom ? floatval($energieausweis->h3_custom_primaer) : floatval($h3_energietraeger->primaer),
        'energietraeger_co2'      => $energieausweis->h3_custom_2 ? floatval($energieausweis->h3_custom_co2) : floatval($h3_energietraeger->co2),
        'speicher_slug'           => $h3_erzeugung->speicher,
        'uebergabe_slug'          => $h3_erzeugung->uebergabe,
        'heizkreistemperatur'     => $h3_erzeugung->hktemp,
        'aufwandszahl'            => wpenon_interpolate(
            $calculations['nutzflaeche'], array(
            array( 'keysize' => 150, 'value' => $h3_erzeugung->$h3_ep150 ),
            array( 'keysize' => 500, 'value' => $h3_erzeugung->$h3_ep500 ),
            array( 'keysize' => 2500, 'value' => $h3_erzeugung->$h3_ep2500 ),
            ) 
        ),
        'hilfsenergie'            => wpenon_interpolate(
            $calculations['nutzflaeche'], array(
            array( 'keysize' => 150, 'value' => $h3_erzeugung->$h3_he150 ),
            array( 'keysize' => 500, 'value' => $h3_erzeugung->$h3_he500 ),
            array( 'keysize' => 2500, 'value' => $h3_erzeugung->$h3_he2500 ),
            ) 
        ),
        'deckungsanteil'          => $energieausweis->h3_deckungsanteil,
        );

        $anteilsumme += $calculations['anlagendaten']['h3']['deckungsanteil'];

        if ($calculations['anlagendaten']['h3']['deckungsanteil'] > $calculations['anlagendaten']['h2']['deckungsanteil'] && $calculations['anlagendaten']['h3']['deckungsanteil'] > $calculations['anlagendaten']['h1']['deckungsanteil'] ) {
            $h_max_anteil = 'h3';
        }
    }
}

if ($anteilsumme != 100 ) {
    foreach ( $calculations['anlagendaten'] as $slug => $data ) {
        $calculations['anlagendaten'][ $slug ]['deckungsanteil'] *= 100 / $anteilsumme;
    }
    unset($data);
}

$h_uebergabe_slug = $calculations['anlagendaten'][ $h_max_anteil ]['uebergabe_slug'];
$h_uebergabe = wpenon_get_table_results($tableNames->h_uebergabe, array( 'bezeichnung' => array( 'value' => $h_uebergabe_slug, 'compare' => '=' ) ), array(), true);
if ($h_uebergabe ) {
    $hu_yearkey = wpenon_immoticket24_make_yearkey($calculations['anlagendaten'][ $h_max_anteil ]['baujahr'], $tableNames->h_uebergabe);
    list( $hu_wv150, $hu_wv500, $hu_wv2500 ) = wpenon_immoticket24_make_anlagenkeys('wv', $hu_yearkey);
    $calculations['uebergabe']['h'] = array(
    'name'                    => $h_uebergabe->name,
    'art'                     => 'heizung',
    'baujahr'                 => $calculations['anlagendaten'][ $h_max_anteil ]['baujahr'],
    'waermeverluste'          => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $h_uebergabe->$hu_wv150 ),
        array( 'keysize' => 500, 'value' => $h_uebergabe->$hu_wv500 ),
        array( 'keysize' => 2500, 'value' => $h_uebergabe->$hu_wv2500 ),
        ) 
    ),
    );
}

$h_verteilung_slug = $calculations['anlagendaten'][ $h_max_anteil ]['typ'];
if ($h_verteilung_slug == 'zentral' ) {
    $h_verteilung_slug .= '_' . ( $calculations['anlagendaten'][ $h_max_anteil ]['heizkreistemperatur'] == '70/55°' ? '7055' : '5545' );
}
$h_verteilung = wpenon_get_table_results('h_verteilung2019', array( 'bezeichnung' => array( 'value' => $h_verteilung_slug, 'compare' => '=' ) ), array(), true);
if ($h_verteilung ) {
    $hv_yearkey = wpenon_immoticket24_make_yearkey($energieausweis->verteilung_baujahr, 'h_verteilung2019', $energieausweis->verteilung_gedaemmt);
    list( $hv_wv150, $hv_wv500, $hv_wv2500 ) = wpenon_immoticket24_make_anlagenkeys('wv', $hv_yearkey);
    list( $hv_he150, $hv_he500, $hv_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $hv_yearkey);
    $calculations['verteilung']['h'] = array(
    'name'                    => $h_verteilung->name,
    'art'                     => 'heizung',
    'baujahr'                 => $energieausweis->verteilung_baujahr,
    'waermeverluste'          => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $h_verteilung->$hv_wv150 ),
        array( 'keysize' => 500, 'value' => $h_verteilung->$hv_wv500 ),
        array( 'keysize' => 2500, 'value' => $h_verteilung->$hv_wv2500 ),
        ) 
    ),
    'hilfsenergie'            => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $h_verteilung->$hv_he150 ),
        array( 'keysize' => 500, 'value' => $h_verteilung->$hv_he500 ),
        array( 'keysize' => 2500, 'value' => $h_verteilung->$hv_he2500 ),
        ) 
    ),
    );
}

if ($energieausweis->speicherung ) {
    $h_speicherung_slug = $calculations['anlagendaten'][ $h_max_anteil ]['speicher_slug'];
    $h_speicherung = wpenon_get_table_results('h_speicherung', array( 'bezeichnung' => array( 'value' => $h_speicherung_slug, 'compare' => '=' ) ), array(), true);
    if ($h_speicherung ) {
        $hs_yearkey = wpenon_immoticket24_make_yearkey($energieausweis->speicherung_baujahr, 'h_speicherung');
        list( $hs_wv150, $hs_wv500, $hs_wv2500 ) = wpenon_immoticket24_make_anlagenkeys('wv', $hs_yearkey);
        list( $hs_he150, $hs_he500, $hs_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $hs_yearkey);
        $calculations['speicherung']['h'] = array(
        'art'                     => 'heizung',
        'name'                    => $h_speicherung->name,
        'baujahr'                 => $energieausweis->speicherung_baujahr,
        'waermeverluste'          => wpenon_interpolate(
            $calculations['nutzflaeche'], array(
            array( 'keysize' => 150, 'value' => $h_speicherung->$hs_wv150 ),
            array( 'keysize' => 500, 'value' => $h_speicherung->$hs_wv500 ),
            array( 'keysize' => 2500, 'value' => $h_speicherung->$hs_wv2500 ),
            ) 
        ),
        'hilfsenergie'            => wpenon_interpolate(
            $calculations['nutzflaeche'], array(
            array( 'keysize' => 150, 'value' => $h_speicherung->$hs_he150 ),
            array( 'keysize' => 500, 'value' => $h_speicherung->$hs_he500 ),
            array( 'keysize' => 2500, 'value' => $h_speicherung->$hs_he2500 ),
            ) 
        ),
        );
    }
}

if ('unbekannt' === $energieausweis->ww_info ) {
    // This kind of heater can't be set to pauschal, because there is no value for it in schema logic.
    if(! wpenon_is_water_independend_heater($energieausweis->h_erzeugung) ) {
        $energieausweis->ww_info = 'h';
    }

    $prefix_ww = 'h';
} else {
    $prefix_ww = $energieausweis->ww_info;
}

$ww_erzeugung = $prefix_ww . '_erzeugung';
$ww_energietraeger = $prefix_ww . '_energietraeger_' . $energieausweis->$ww_erzeugung;
$ww_baujahr = $prefix_ww . '_baujahr';

$ww_erzeugung = wpenon_get_table_results('ww_erzeugung2019', array( 'bezeichnung' => array( 'value' => $energieausweis->$ww_erzeugung, 'compare' => '=' ) ), array(), true);
$ww_energietraeger = wpenon_get_table_results($tableNames->energietraeger, array( 'bezeichnung' => array( 'value' => $energieausweis->$ww_energietraeger, 'compare' => '=' ) ), array(), true);


$ww_yearkey = wpenon_immoticket24_make_yearkey($energieausweis->$ww_baujahr, 'ww_erzeugung2019');
list( $ww_ep150, $ww_ep500, $ww_ep2500 ) = wpenon_immoticket24_make_anlagenkeys('ep', $ww_yearkey);
list( $ww_he150, $ww_he500, $ww_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $ww_yearkey);
list( $ww_hwg150, $ww_hwg500, $ww_hwg2500 ) = wpenon_immoticket24_make_anlagenkeys('hwg', $ww_yearkey);
$calculations['anlagendaten']['ww'] = array(
  'name'                    => $ww_erzeugung->name,
  'slug'                    => $ww_erzeugung->bezeichnung,
  'art'                     => 'warmwasser',
  'typ'                     => $ww_erzeugung->typ,
  'baujahr'                 => $energieausweis->$ww_baujahr,
  'energietraeger'          => $ww_energietraeger->name,
  'energietraeger_slug'     => $ww_energietraeger->bezeichnung,
  'energietraeger_primaer'  => floatval($ww_energietraeger->primaer),
  'energietraeger_co2'      => floatval($ww_energietraeger->co2),
  'speicher_slug'           => $ww_erzeugung->speicher,
'aufwandszahl'            => wpenon_interpolate(
    $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $ww_erzeugung->$ww_ep150 ),
    array( 'keysize' => 500, 'value' => $ww_erzeugung->$ww_ep500 ),
    array( 'keysize' => 2500, 'value' => $ww_erzeugung->$ww_ep2500 ),
    ) 
),
'hilfsenergie'            => wpenon_interpolate(
    $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $ww_erzeugung->$ww_he150 ),
    array( 'keysize' => 500, 'value' => $ww_erzeugung->$ww_he500 ),
    array( 'keysize' => 2500, 'value' => $ww_erzeugung->$ww_he2500 ),
    ) 
),
'heizwaermegewinne'       => wpenon_interpolate(
    $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $ww_erzeugung->$ww_hwg150 ),
    array( 'keysize' => 500, 'value' => $ww_erzeugung->$ww_hwg500 ),
    array( 'keysize' => 2500, 'value' => $ww_erzeugung->$ww_hwg2500 ),
    ) 
),
  'deckungsanteil'          => 100,
);
$ww_max_anteil = 'ww';

$ww_verteilung_slug = $calculations['anlagendaten'][ $ww_max_anteil ]['typ'];
if ($ww_verteilung_slug == 'zentral' ) {
    $ww_verteilung_slug .= '_' . $energieausweis->verteilung_versorgung;
}
$ww_verteilung = wpenon_get_table_results('ww_verteilung', array( 'bezeichnung' => array( 'value' => $ww_verteilung_slug, 'compare' => '=' ) ), array(), true);
if ($ww_verteilung ) {
    $wwv_yearkey = wpenon_immoticket24_make_yearkey($energieausweis->verteilung_baujahr, 'ww_verteilung', $energieausweis->verteilung_gedaemmt);
    list( $wwv_wv150, $wwv_wv500, $wwv_wv2500 ) = wpenon_immoticket24_make_anlagenkeys('wv', $wwv_yearkey);
    list( $wwv_he150, $wwv_he500, $wwv_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $wwv_yearkey);
    list( $wwv_hwg150, $wwv_hwg500, $wwv_hwg2500 ) = wpenon_immoticket24_make_anlagenkeys('hwg', $wwv_yearkey);
    $calculations['verteilung']['ww'] = array(
    'name'                    => $ww_verteilung->name,
    'art'                     => 'warmwasser',
    'baujahr'                 => $energieausweis->verteilung_baujahr,
    'waermeverluste'          => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $ww_verteilung->$wwv_wv150 ),
        array( 'keysize' => 500, 'value' => $ww_verteilung->$wwv_wv500 ),
        array( 'keysize' => 2500, 'value' => $ww_verteilung->$wwv_wv2500 ),
        ) 
    ),
    'hilfsenergie'            => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $ww_verteilung->$wwv_he150 ),
        array( 'keysize' => 500, 'value' => $ww_verteilung->$wwv_he500 ),
        array( 'keysize' => 2500, 'value' => $ww_verteilung->$wwv_he2500 ),
        ) 
    ),
    'heizwaermegewinne'       => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $ww_verteilung->$wwv_hwg150 ),
        array( 'keysize' => 500, 'value' => $ww_verteilung->$wwv_hwg500 ),
        array( 'keysize' => 2500, 'value' => $ww_verteilung->$wwv_hwg2500 ),
        ) 
    ),
    );
}

if ($energieausweis->speicherung ) {
    $ww_speicherung_slug = $calculations['anlagendaten'][ $ww_max_anteil ]['speicher_slug'];
    if ($ww_speicherung_slug == 'zentral' ) {
        $ww_speicherung_slug .= '_' . $energieausweis->speicherung_standort;
    }
    $ww_speicherung = wpenon_get_table_results('ww_speicherung', array( 'bezeichnung' => array( 'value' => $ww_speicherung_slug, 'compare' => '=' ) ), array(), true);
    if ($ww_speicherung ) {
        $wws_yearkey = wpenon_immoticket24_make_yearkey($energieausweis->speicherung_baujahr, 'ww_speicherung');
        list( $wws_wv150, $wws_wv500, $wws_wv2500 ) = wpenon_immoticket24_make_anlagenkeys('wv', $wws_yearkey);
        list( $wws_he150, $wws_he500, $wws_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $wws_yearkey);
        list( $wws_hwg150, $wws_hwg500, $wws_hwg2500 ) = wpenon_immoticket24_make_anlagenkeys('hwg', $wws_yearkey);
        $calculations['speicherung']['ww'] = array(
        'name'                    => $ww_speicherung->name,
        'art'                     => 'warmwasser',
        'baujahr'                 => $energieausweis->speicherung_baujahr,
        'waermeverluste'          => wpenon_interpolate(
            $calculations['nutzflaeche'], array(
            array( 'keysize' => 150, 'value' => $ww_speicherung->$wws_wv150 ),
            array( 'keysize' => 500, 'value' => $ww_speicherung->$wws_wv500 ),
            array( 'keysize' => 2500, 'value' => $ww_speicherung->$wws_wv2500 ),
            ) 
        ),
        'hilfsenergie'            => wpenon_interpolate(
            $calculations['nutzflaeche'], array(
            array( 'keysize' => 150, 'value' => $ww_speicherung->$wws_he150 ),
            array( 'keysize' => 500, 'value' => $ww_speicherung->$wws_he500 ),
            array( 'keysize' => 2500, 'value' => $ww_speicherung->$wws_he2500 ),
            ) 
        ),
        'heizwaermegewinne'       => wpenon_interpolate(
            $calculations['nutzflaeche'], array(
            array( 'keysize' => 150, 'value' => $ww_speicherung->$wws_hwg150 ),
            array( 'keysize' => 500, 'value' => $ww_speicherung->$wws_hwg500 ),
            array( 'keysize' => 2500, 'value' => $ww_speicherung->$wws_hwg2500 ),
            ) 
        ),
        );
    }
}

if ($energieausweis->l_info == 'anlage' ) {
    $l_erzeugung = wpenon_get_table_results($tableNames->l_erzeugung, array( 'bezeichnung' => array( 'value' => $energieausweis->l_erzeugung, 'compare' => '=' ) ), array(), true);
    $l_energietraeger = wpenon_get_table_results($tableNames->energietraeger, array( 'bezeichnung' => array( 'value' => 'strom', 'compare' => '=' ) ), array(), true);
    $l_yearkey = wpenon_immoticket24_make_yearkey($energieausweis->l_baujahr, $tableNames->l_erzeugung);
    list( $l_he150, $l_he500, $l_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $l_yearkey);
    list( $l_hwg150, $l_hwg500, $l_hwg2500 ) = wpenon_immoticket24_make_anlagenkeys('hwg', $l_yearkey);
    $calculations['anlagendaten']['l'] = array(
    'name'                    => $l_erzeugung->name,
    'slug'                    => $l_erzeugung->bezeichnung,
    'art'                     => 'lueftung',
    'typ'                     => $l_erzeugung->bezeichnung,
    'baujahr'                 => $energieausweis->l_baujahr,
    'energietraeger'          => $l_energietraeger->name,
    'energietraeger_slug'     => $l_energietraeger->bezeichnung,
    'energietraeger_primaer'  => floatval($l_energietraeger->primaer),
    'energietraeger_co2'      => floatval($l_energietraeger->co2),
    'hilfsenergie'            => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $l_erzeugung->$l_he150 ),
        array( 'keysize' => 500, 'value' => $l_erzeugung->$l_he500 ),
        array( 'keysize' => 2500, 'value' => $l_erzeugung->$l_he2500 ),
        ) 
    ),
    'heizwaermegewinne'       => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $l_erzeugung->$l_hwg150 ),
        array( 'keysize' => 500, 'value' => $l_erzeugung->$l_hwg500 ),
        array( 'keysize' => 2500, 'value' => $l_erzeugung->$l_hwg2500 ),
        ) 
    ),
    'deckungsanteil'          => 100,
    );

    $l_verteilung_slug = $calculations['anlagendaten']['l']['typ'];
    if ($l_verteilung_slug == 'mitgewinnung' ) {
        $l_verteilung_slug .= '_' . $energieausweis->l_standort;
    }
    $l_verteilung = wpenon_get_table_results($tableNames->l_verteilung, array( 'bezeichnung' => array( 'value' => $l_verteilung_slug, 'compare' => '=' ) ), array(), true);
    if ($l_verteilung ) {
        $lv_yearkey = wpenon_immoticket24_make_yearkey($energieausweis->l_baujahr, $tableNames->l_verteilung);
        list( $lv_wv150, $lv_wv500, $lv_wv2500 ) = wpenon_immoticket24_make_anlagenkeys('wv', $lv_yearkey);
        list( $lv_he150, $lv_he500, $lv_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $lv_yearkey);
        $calculations['verteilung']['l'] = array(
        'name'                    => $l_verteilung->name,
        'art'                     => 'lueftung',
        'baujahr'                 => $energieausweis->l_baujahr,
        'waermeverluste'          => wpenon_interpolate(
            $calculations['nutzflaeche'], array(
            array( 'keysize' => 150, 'value' => $l_verteilung->$lv_wv150 ),
            array( 'keysize' => 500, 'value' => $l_verteilung->$lv_wv500 ),
            array( 'keysize' => 2500, 'value' => $l_verteilung->$lv_wv2500 ),
            ) 
        ),
        'hilfsenergie'            => wpenon_interpolate(
            $calculations['nutzflaeche'], array(
            array( 'keysize' => 150, 'value' => $l_verteilung->$lv_he150 ),
            array( 'keysize' => 500, 'value' => $l_verteilung->$lv_he500 ),
            array( 'keysize' => 2500, 'value' => $l_verteilung->$lv_he2500 ),
            ) 
        ),
        );
    }
}

// Referenzgebäude
$calculations['anlagendaten_reference'] = array();
$calculations['verteilung_reference'] = array();
$calculations['speicherung_reference'] = array();
$calculations['uebergabe_reference'] = array();

$h_reference_erzeugung = wpenon_get_table_results($tableNames->h_erzeugung, array( 'bezeichnung' => array( 'value' => 'brennwertkesselverbessert', 'compare' => '=' ) ), array(), true);
$h_reference_energietraeger = wpenon_get_table_results($tableNames->energietraeger, array( 'bezeichnung' => array( 'value' => 'heizoel', 'compare' => '=' ) ), array(), true);
$h_reference_baujahr = absint(wpenon_get_reference_date('Y', $energieausweis));
$h_reference_yearkey = wpenon_immoticket24_make_yearkey($h_reference_baujahr, $tableNames->h_erzeugung);
list( $h_reference_ep150, $h_reference_ep500, $h_reference_ep2500 ) = wpenon_immoticket24_make_anlagenkeys('ep', $h_reference_yearkey);
list( $h_reference_he150, $h_reference_he500, $h_reference_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $h_reference_yearkey);
$calculations['anlagendaten_reference']['h'] = array(
  'name'                    => $h_reference_erzeugung->name,
  'slug'                    => $h_reference_erzeugung->bezeichnung,
  'art'                     => 'heizung',
  'typ'                     => $h_reference_erzeugung->typ,
  'baujahr'                 => $h_reference_baujahr,
  'energietraeger'          => $h_reference_energietraeger->name,
  'energietraeger_slug'     => $h_reference_energietraeger->bezeichnung,
  'energietraeger_primaer'  => floatval($h_reference_energietraeger->primaer),
  'energietraeger_co2'      => floatval($h_reference_energietraeger->co2),
  'speicher_slug'           => $h_reference_erzeugung->speicher,
  'uebergabe_slug'          => $h_reference_erzeugung->uebergabe,
  'heizkreistemperatur'     => $h_reference_erzeugung->hktemp,
'aufwandszahl'            => wpenon_interpolate(
    $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $h_reference_erzeugung->$h_reference_ep150 ),
    array( 'keysize' => 500, 'value' => $h_reference_erzeugung->$h_reference_ep500 ),
    array( 'keysize' => 2500, 'value' => $h_reference_erzeugung->$h_reference_ep2500 ),
    ) 
),
'hilfsenergie'            => wpenon_interpolate(
    $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $h_reference_erzeugung->$h_reference_he150 ),
    array( 'keysize' => 500, 'value' => $h_reference_erzeugung->$h_reference_he500 ),
    array( 'keysize' => 2500, 'value' => $h_reference_erzeugung->$h_reference_he2500 ),
    ) 
),
  'deckungsanteil'          => 100,
);

$h_uebergabe_reference_slug = $calculations['anlagendaten_reference']['h']['uebergabe_slug'];
$h_uebergabe_reference = wpenon_get_table_results($tableNames->h_uebergabe, array( 'bezeichnung' => array( 'value' => $h_uebergabe_reference_slug, 'compare' => '=' ) ), array(), true);
if ($h_uebergabe_reference ) {
    $hu_reference_baujahr = $h_reference_baujahr;
    $hu_reference_yearkey = wpenon_immoticket24_make_yearkey($hu_reference_baujahr, $tableNames->h_uebergabe);
    list( $hu_reference_wv150, $hu_reference_wv500, $hu_reference_wv2500 ) = wpenon_immoticket24_make_anlagenkeys('wv', $hu_reference_yearkey);
    $calculations['uebergabe_reference']['h'] = array(
    'name'                    => $h_uebergabe_reference->name,
    'art'                     => 'heizung',
    'baujahr'                 => $hu_reference_baujahr,
    'waermeverluste'          => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $h_uebergabe_reference->$hu_reference_wv150 ),
        array( 'keysize' => 500, 'value' => $h_uebergabe_reference->$hu_reference_wv500 ),
        array( 'keysize' => 2500, 'value' => $h_uebergabe_reference->$hu_reference_wv2500 ),
        ) 
    ),
    );
}

$h_verteilung_reference_slug = $calculations['anlagendaten_reference']['h']['typ'];
if ($h_verteilung_reference_slug == 'zentral' ) {
    $h_verteilung_reference_slug .= '_' . ( $calculations['anlagendaten_reference']['h']['heizkreistemperatur'] == '70/55°' ? '7055' : '5545' );
}
$h_verteilung_reference = wpenon_get_table_results('h_verteilung2019', array( 'bezeichnung' => array( 'value' => $h_verteilung_reference_slug, 'compare' => '=' ) ), array(), true);
if ($h_verteilung_reference ) {
    $hv_reference_baujahr = $h_reference_baujahr;
    $hv_reference_yearkey = wpenon_immoticket24_make_yearkey($hv_reference_baujahr, 'h_verteilung2019', true);
    list( $hv_reference_wv150, $hv_reference_wv500, $hv_reference_wv2500 ) = wpenon_immoticket24_make_anlagenkeys('wv', $hv_reference_yearkey);
    list( $hv_reference_he150, $hv_reference_he500, $hv_reference_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $hv_reference_yearkey);
    $calculations['verteilung_reference']['h'] = array(
    'name'                    => $h_verteilung_reference->name,
    'art'                     => 'heizung',
    'baujahr'                 => $hv_reference_baujahr,
    'waermeverluste'          => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $h_verteilung_reference->$hv_reference_wv150 ),
        array( 'keysize' => 500, 'value' => $h_verteilung_reference->$hv_reference_wv500 ),
        array( 'keysize' => 2500, 'value' => $h_verteilung_reference->$hv_reference_wv2500 ),
        ) 
    ),
    'hilfsenergie'            => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $h_verteilung_reference->$hv_reference_he150 ),
        array( 'keysize' => 500, 'value' => $h_verteilung_reference->$hv_reference_he500 ),
        array( 'keysize' => 2500, 'value' => $h_verteilung_reference->$hv_reference_he2500 ),
        ) 
    ),
    );
}

$h_speicherung_reference_slug = $calculations['anlagendaten_reference']['h']['speicher_slug'];
$h_speicherung_reference = wpenon_get_table_results('h_speicherung', array( 'bezeichnung' => array( 'value' => $h_speicherung_reference_slug, 'compare' => '=' ) ), array(), true);
if ($h_speicherung_reference ) {
    $hs_reference_baujahr = $h_reference_baujahr;
    $hs_reference_yearkey = wpenon_immoticket24_make_yearkey($hs_reference_baujahr, 'h_speicherung');
    list( $hs_reference_wv150, $hs_reference_wv500, $hs_reference_wv2500 ) = wpenon_immoticket24_make_anlagenkeys('wv', $hs_reference_yearkey);
    list( $hs_reference_he150, $hs_reference_he500, $hs_reference_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $hs_reference_yearkey);
    $calculations['speicherung_reference']['h'] = array(
    'art'                     => 'heizung',
    'name'                    => $h_speicherung_reference->name,
    'baujahr'                 => $hs_reference_baujahr,
    'waermeverluste'          => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $h_speicherung_reference->$hs_reference_wv150 ),
        array( 'keysize' => 500, 'value' => $h_speicherung_reference->$hs_reference_wv500 ),
        array( 'keysize' => 2500, 'value' => $h_speicherung_reference->$hs_reference_wv2500 ),
        ) 
    ),
    'hilfsenergie'            => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $h_speicherung_reference->$hs_reference_he150 ),
        array( 'keysize' => 500, 'value' => $h_speicherung_reference->$hs_reference_he500 ),
        array( 'keysize' => 2500, 'value' => $h_speicherung_reference->$hs_reference_he2500 ),
        ) 
    ),
    );
}

$ww_reference_erzeugung = wpenon_get_table_results('ww_erzeugung2019', array( 'bezeichnung' => array( 'value' => 'brennwertkesselverbessert', 'compare' => '=' ) ), array(), true);
$ww_reference_energietraeger = $h_reference_energietraeger;
$ww_reference_baujahr = $h_reference_baujahr;
$ww_reference_yearkey = wpenon_immoticket24_make_yearkey($ww_reference_baujahr, 'ww_erzeugung2019');
list( $ww_reference_ep150, $ww_reference_ep500, $ww_reference_ep2500 ) = wpenon_immoticket24_make_anlagenkeys('ep', $ww_reference_yearkey);
list( $ww_reference_he150, $ww_reference_he500, $ww_reference_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $ww_reference_yearkey);
list( $ww_reference_hwg150, $ww_reference_hwg500, $ww_reference_hwg2500 ) = wpenon_immoticket24_make_anlagenkeys('hwg', $ww_reference_yearkey);
$calculations['anlagendaten_reference']['ww'] = array(
  'name'                    => $ww_reference_erzeugung->name,
  'slug'                    => $ww_reference_erzeugung->bezeichnung,
  'art'                     => 'warmwasser',
  'typ'                     => $ww_reference_erzeugung->typ,
  'baujahr'                 => $ww_reference_baujahr,
  'energietraeger'          => $ww_reference_energietraeger->name,
  'energietraeger_slug'     => $ww_reference_energietraeger->bezeichnung,
  'energietraeger_primaer'  => floatval($ww_reference_energietraeger->primaer),
  'energietraeger_co2'      => floatval($ww_reference_energietraeger->co2),
  'speicher_slug'           => $ww_reference_erzeugung->speicher,
'aufwandszahl'            => wpenon_interpolate(
    $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $ww_reference_erzeugung->$ww_reference_ep150 ),
    array( 'keysize' => 500, 'value' => $ww_reference_erzeugung->$ww_reference_ep500 ),
    array( 'keysize' => 2500, 'value' => $ww_reference_erzeugung->$ww_reference_ep2500 ),
    ) 
),
'hilfsenergie'            => wpenon_interpolate(
    $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $ww_reference_erzeugung->$ww_reference_he150 ),
    array( 'keysize' => 500, 'value' => $ww_reference_erzeugung->$ww_reference_he500 ),
    array( 'keysize' => 2500, 'value' => $ww_reference_erzeugung->$ww_reference_he2500 ),
    ) 
),
'heizwaermegewinne'       => wpenon_interpolate(
    $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $ww_reference_erzeugung->$ww_reference_hwg150 ),
    array( 'keysize' => 500, 'value' => $ww_reference_erzeugung->$ww_reference_hwg500 ),
    array( 'keysize' => 2500, 'value' => $ww_reference_erzeugung->$ww_reference_hwg2500 ),
    ) 
),
  'deckungsanteil'          => 100,
);

$ww_verteilung_reference_slug = $calculations['anlagendaten_reference']['ww']['typ'];
if ($ww_verteilung_reference_slug == 'zentral' ) {
    $ww_verteilung_reference_slug .= '_mit';
}
$ww_verteilung_reference = wpenon_get_table_results('ww_verteilung', array( 'bezeichnung' => array( 'value' => $ww_verteilung_reference_slug, 'compare' => '=' ) ), array(), true);
if ($ww_verteilung_reference ) {
    $wwv_reference_baujahr = $ww_reference_baujahr;
    $wwv_reference_yearkey = wpenon_immoticket24_make_yearkey($wwv_reference_baujahr, 'ww_verteilung', true);
    list( $wwv_reference_wv150, $wwv_reference_wv500, $wwv_reference_wv2500 ) = wpenon_immoticket24_make_anlagenkeys('wv', $wwv_reference_yearkey);
    list( $wwv_reference_he150, $wwv_reference_he500, $wwv_reference_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $wwv_reference_yearkey);
    list( $wwv_reference_hwg150, $wwv_reference_hwg500, $wwv_reference_hwg2500 ) = wpenon_immoticket24_make_anlagenkeys('hwg', $wwv_reference_yearkey);
    $calculations['verteilung_reference']['ww'] = array(
    'name'                    => $ww_verteilung_reference->name,
    'art'                     => 'warmwasser',
    'baujahr'                 => $wwv_reference_baujahr,
    'waermeverluste'          => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $ww_verteilung_reference->$wwv_reference_wv150 ),
        array( 'keysize' => 500, 'value' => $ww_verteilung_reference->$wwv_reference_wv500 ),
        array( 'keysize' => 2500, 'value' => $ww_verteilung_reference->$wwv_reference_wv2500 ),
        ) 
    ),
    'hilfsenergie'            => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $ww_verteilung_reference->$wwv_reference_he150 ),
        array( 'keysize' => 500, 'value' => $ww_verteilung_reference->$wwv_reference_he500 ),
        array( 'keysize' => 2500, 'value' => $ww_verteilung_reference->$wwv_reference_he2500 ),
        ) 
    ),
    'heizwaermegewinne'       => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $ww_verteilung_reference->$wwv_reference_hwg150 ),
        array( 'keysize' => 500, 'value' => $ww_verteilung_reference->$wwv_reference_hwg500 ),
        array( 'keysize' => 2500, 'value' => $ww_verteilung_reference->$wwv_reference_hwg2500 ),
        ) 
    ),
    );
}

$ww_speicherung_reference_slug = $calculations['anlagendaten_reference']['ww']['speicher_slug'];
if ($ww_speicherung_reference_slug == 'zentral' ) {
    $ww_speicherung_reference_slug .= '_innerhalb';
}
$ww_speicherung_reference = wpenon_get_table_results('ww_speicherung', array( 'bezeichnung' => array( 'value' => $ww_speicherung_reference_slug, 'compare' => '=' ) ), array(), true);
if ($ww_speicherung_reference ) {
    $wws_reference_baujahr = $ww_reference_baujahr;
    $wws_reference_yearkey = wpenon_immoticket24_make_yearkey($wws_reference_baujahr, 'ww_speicherung');
    list( $wws_reference_wv150, $wws_reference_wv500, $wws_reference_wv2500 ) = wpenon_immoticket24_make_anlagenkeys('wv', $wws_reference_yearkey);
    list( $wws_reference_he150, $wws_reference_he500, $wws_reference_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $wws_reference_yearkey);
    list( $wws_reference_hwg150, $wws_reference_hwg500, $wws_reference_hwg2500 ) = wpenon_immoticket24_make_anlagenkeys('hwg', $wws_reference_yearkey);
    $calculations['speicherung_reference']['ww'] = array(
    'name'                    => $ww_speicherung_reference->name,
    'art'                     => 'warmwasser',
    'baujahr'                 => $wws_reference_baujahr,
    'waermeverluste'          => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $ww_speicherung_reference->$wws_reference_wv150 ),
        array( 'keysize' => 500, 'value' => $ww_speicherung_reference->$wws_reference_wv500 ),
        array( 'keysize' => 2500, 'value' => $ww_speicherung_reference->$wws_reference_wv2500 ),
        ) 
    ),
    'hilfsenergie'            => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $ww_speicherung_reference->$wws_reference_he150 ),
        array( 'keysize' => 500, 'value' => $ww_speicherung_reference->$wws_reference_he500 ),
        array( 'keysize' => 2500, 'value' => $ww_speicherung_reference->$wws_reference_he2500 ),
        ) 
    ),
    'heizwaermegewinne'       => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $ww_speicherung_reference->$wws_reference_hwg150 ),
        array( 'keysize' => 500, 'value' => $ww_speicherung_reference->$wws_reference_hwg500 ),
        array( 'keysize' => 2500, 'value' => $ww_speicherung_reference->$wws_reference_hwg2500 ),
        ) 
    ),
    );
}

$l_reference_erzeugung = wpenon_get_table_results($tableNames->l_erzeugung, array( 'bezeichnung' => array( 'value' => 'mitgewinnung', 'compare' => '=' ) ), array(), true);
$l_reference_energietraeger = wpenon_get_table_results($tableNames->energietraeger, array( 'bezeichnung' => array( 'value' => 'strom', 'compare' => '=' ) ), array(), true);
$l_reference_baujahr = $h_reference_baujahr;
$l_reference_yearkey = wpenon_immoticket24_make_yearkey($l_reference_baujahr, $tableNames->l_erzeugung);
list( $l_reference_he150, $l_reference_he500, $l_reference_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $l_reference_yearkey);
list( $l_reference_hwg150, $l_reference_hwg500, $l_reference_hwg2500 ) = wpenon_immoticket24_make_anlagenkeys('hwg', $l_reference_yearkey);
$calculations['anlagendaten_reference']['l'] = array(
  'name'                    => $l_reference_erzeugung->name,
  'slug'                    => $l_reference_erzeugung->bezeichnung,
  'art'                     => 'lueftung',
  'typ'                     => $l_reference_erzeugung->bezeichnung,
  'baujahr'                 => $l_reference_baujahr,
  'energietraeger'          => $l_reference_energietraeger->name,
  'energietraeger_slug'     => $l_reference_energietraeger->bezeichnung,
  'energietraeger_primaer'  => floatval($l_reference_energietraeger->primaer),
  'energietraeger_co2'      => floatval($l_reference_energietraeger->co2),
'hilfsenergie'            => wpenon_interpolate(
    $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $l_reference_erzeugung->$l_reference_he150 ),
    array( 'keysize' => 500, 'value' => $l_reference_erzeugung->$l_reference_he500 ),
    array( 'keysize' => 2500, 'value' => $l_reference_erzeugung->$l_reference_he2500 ),
    ) 
),
'heizwaermegewinne'       => wpenon_interpolate(
    $calculations['nutzflaeche'], array(
    array( 'keysize' => 150, 'value' => $l_reference_erzeugung->$l_reference_hwg150 ),
    array( 'keysize' => 500, 'value' => $l_reference_erzeugung->$l_reference_hwg500 ),
    array( 'keysize' => 2500, 'value' => $l_reference_erzeugung->$l_reference_hwg2500 ),
    ) 
),
  'deckungsanteil'          => 100,
);

$l_verteilung_reference_slug = $calculations['anlagendaten_reference']['l']['typ'];
if ($l_verteilung_reference_slug == 'mitgewinnung' ) {
    $l_verteilung_reference_slug .= '_innerhalb';
}
$l_verteilung_reference = wpenon_get_table_results($tableNames->l_verteilung, array( 'bezeichnung' => array( 'value' => $l_verteilung_reference_slug, 'compare' => '=' ) ), array(), true);
if ($l_verteilung_reference ) {
    $lv_reference_baujahr = $l_reference_baujahr;
    $lv_reference_yearkey = wpenon_immoticket24_make_yearkey($lv_reference_baujahr, $tableNames->l_verteilung);
    list( $lv_reference_wv150, $lv_reference_wv500, $lv_reference_wv2500 ) = wpenon_immoticket24_make_anlagenkeys('wv', $lv_reference_yearkey);
    list( $lv_reference_he150, $lv_reference_he500, $lv_reference_he2500 ) = wpenon_immoticket24_make_anlagenkeys('he', $lv_reference_yearkey);
    $calculations['verteilung_reference']['l'] = array(
    'name'                    => $l_verteilung_reference->name,
    'art'                     => 'lueftung',
    'baujahr'                 => $lv_reference_baujahr,
    'waermeverluste'          => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $l_verteilung_reference->$lv_reference_wv150 ),
        array( 'keysize' => 500, 'value' => $l_verteilung_reference->$lv_reference_wv500 ),
        array( 'keysize' => 2500, 'value' => $l_verteilung_reference->$lv_reference_wv2500 ),
        ) 
    ),
    'hilfsenergie'            => wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => $l_verteilung_reference->$lv_reference_he150 ),
        array( 'keysize' => 500, 'value' => $l_verteilung_reference->$lv_reference_he500 ),
        array( 'keysize' => 2500, 'value' => $l_verteilung_reference->$lv_reference_he2500 ),
        ) 
    ),
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
    if (isset($calculations[ $anlagentyp ]) ) {
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
            if (isset($data['waermeverluste']) ) {
                $calculations[ $aslug ] += $data['waermeverluste'];
            }
            if (isset($data['hilfsenergie']) ) {
                $calculations[ $heslug ] += $data['hilfsenergie'];
            }
            if (isset($data['heizwaermegewinne']) ) {
                $calculations['qh_a_b'] -= $data['heizwaermegewinne'];
            }
        }
        unset($data);
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
    $aufwandszahl = isset($data['aufwandszahl']) ? $data['aufwandszahl'] : 1.0;
    $primaerfaktor = isset($data['energietraeger_primaer']) ? $data['energietraeger_primaer'] : 1.0;
    $co2faktor = isset($data['energietraeger_co2']) ? $data['energietraeger_co2'] : 0.0;
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
unset($data);

$energietraeger_strom = wpenon_get_table_results($tableNames->energietraeger, array( 'bezeichnung' => array( 'value' => 'strom', 'compare' => '=' ) ), array(), true);
$primaerfaktor_strom = $energietraeger_strom->primaer;
$co2faktor_strom = $energietraeger_strom->co2;

if ('solar' === $energieausweis->regenerativ_art || $energieausweis->regenerativ_aktiv ) {
    $calculations['qw_e_b'] -= wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => 13.3 ),
        array( 'keysize' => 500, 'value' => 10.4 ),
        array( 'keysize' => 2500, 'value' => 7.5 ),
        ) 
    );
    $calculations['qw_he_b'] += wpenon_interpolate(
        $calculations['nutzflaeche'], array(
        array( 'keysize' => 150, 'value' => 0.8 ),
        array( 'keysize' => 500, 'value' => 0.4 ),
        array( 'keysize' => 2500, 'value' => 0.3 ),
        ) 
    );
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
    if (isset($calculations[ $anlagentyp ]) ) {
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
            if (isset($data['waermeverluste']) ) {
                $calculations[ $aslug ] += $data['waermeverluste'];
            }
            if (isset($data['hilfsenergie']) ) {
                $calculations[ $heslug ] += $data['hilfsenergie'];
            }
            if (isset($data['heizwaermegewinne']) ) {
                $calculations['qh_a_b_reference'] -= $data['heizwaermegewinne'];
            }
        }
        unset($data);
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
    $aufwandszahl = isset($data['aufwandszahl']) ? $data['aufwandszahl'] : 1.0;
    $primaerfaktor = isset($data['energietraeger_primaer']) ? $data['energietraeger_primaer'] : 1.0;
    $co2faktor = isset($data['energietraeger_co2']) ? $data['energietraeger_co2'] : 0.0;
    $result = $calculations[ $aslug ] * $deckungsanteil * $aufwandszahl;
    $calculations[ $eslug ] += $result;
    $calculations[ $pslug ] += $result * $primaerfaktor;
    $calculations[ $cslug ] += $result * $co2faktor;
}
unset($data);

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
if ('freistehend' === $energieausweis->gebaeudetyp ) {
    if ($calculations['nutzflaeche'] > 350.0 ) {
        $calculations['ht_b_reference'] = 0.5;
    } else {
        $calculations['ht_b_reference'] = 0.4;
    }
} elseif ('reiheneckhaus' === $energieausweis->gebaeudetyp || 'doppelhaushaelfte' === $energieausweis->gebaeudetyp ) {
    $calculations['ht_b_reference'] = 0.45;
}

return $calculations;
