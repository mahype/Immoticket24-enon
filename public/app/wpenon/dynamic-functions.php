<?php

function wpenon_immoticket24_display_grundriss_image() {
	$energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis();
	if ( $energieausweis !== null && 'b' === $energieausweis->mode ) {
		$form = $energieausweis->grundriss_form;
		if ( $form ) {
			echo '<p class="text-center"><img class="immoticket24-grundriss-bild" src="' . WPENON_DATA_URL . '/assets/grundriss_' . $form . '.png" alt="' . sprintf( __( 'Form %s', 'wpenon' ), strtoupper( $form ) ) . '"></p>';
		} else {
			echo '<p class="text-center"><img class="immoticket24-grundriss-bild" src="' . WPENON_DATA_URL . '/assets/grundrisse.png" alt="' . __( 'Formen', 'wpenon' ) . '"></p>';
		}
	}
}

add_action( 'wpenon_form_group_bauteile_basis_before', 'wpenon_immoticket24_display_grundriss_image' );
add_action( 'wpenon_form_field_fenster_manuell_after', 'wpenon_immoticket24_display_grundriss_image' );
add_action( 'wpenon_form_field_grundriss_form_after', 'wpenon_immoticket24_display_grundriss_image' );

function wpenon_immoticket24_display_anbau_clean_image() {
	$energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis();
	if ( $energieausweis !== null && 'b' === $energieausweis->mode ) {
		$form = $energieausweis->anbau_form;
		if ( $form ) {
			echo '<p class="text-center"><img class="immoticket24-anbau-bild" src="' . WPENON_DATA_URL . '/assets/anbau_' . $form . '_clean.png" alt="' . sprintf( __( 'Anbau-Form %s', 'wpenon' ), strtoupper( $form ) ) . '"></p>';
		} else {
			echo '<p class="text-center"><img class="immoticket24-anbau-bild" src="' . WPENON_DATA_URL . '/assets/anbauformen_clean.png" alt="' . __( 'Anbau-Formen', 'wpenon' ) . '"></p>';
		}
	}
}

function wpenon_immoticket24_display_anbau_image() {
	$energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis();
	if ( $energieausweis !== null && 'b' === $energieausweis->mode ) {
		$form = $energieausweis->anbau_form;
		if ( $form ) {
			echo '<p class="text-center"><img class="immoticket24-anbau-bild" src="' . WPENON_DATA_URL . '/assets/anbau_' . $form . '.png" alt="' . sprintf( __( 'Anbau-Form %s', 'wpenon' ), strtoupper( $form ) ) . '"></p>';
		} else {
			echo '<p class="text-center"><img class="immoticket24-anbau-bild" src="' . WPENON_DATA_URL . '/assets/anbauformen.png" alt="' . __( 'Anbau-Formen', 'wpenon' ) . '"></p>';
		}
	}
}

function wpenon_immoticket24_display_anbau_form_image() {
	$energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis();
	if ( $energieausweis !== null && 'b' === $energieausweis->mode ) {
		$form = $energieausweis->anbau_form;
		if ( $form ) {
			echo '<p class="text-center"><img class="immoticket24-anbau-bild" src="' . WPENON_DATA_URL . '/assets/anbau_' . $form . '_form.png" alt="' . sprintf( __( 'Anbau-Form %s', 'wpenon' ), strtoupper( $form ) ) . '"></p>';
		} else {
			echo '<p class="text-center"><img class="immoticket24-anbau-bild" src="' . WPENON_DATA_URL . '/assets/anbauformen_form.png" alt="' . __( 'Anbau-Formen', 'wpenon' ) . '"></p>';
		}
	}
}

add_action( 'wpenon_form_field_anbau_form_after', 'wpenon_immoticket24_display_anbau_clean_image' );

add_action( 'wpenon_form_field_anbauwand_headline_after', 'wpenon_immoticket24_display_anbau_form_image' );
add_action( 'wpenon_form_field_anbaufenster_headline_before', 'wpenon_immoticket24_display_anbau_image' );

function wpenon_immoticket24_show_wand( $grundriss, $wand, $nachbar = false ) {
	$nachbar = \WPENON\Util\Parse::boolean( $nachbar );
	if ( ! $nachbar ) {
		$formen = wpenon_immoticket24_get_grundriss_formen();
		if ( isset( $formen[ $grundriss ] ) && isset( $formen[ $grundriss ][ $wand ] ) ) {
			return true;
		}
	}

	return false;
}

function wpenon_immoticket24_show_anbauwand( $grundriss, $wand, $anbau = false ) {
	$anbau = \WPENON\Util\Parse::boolean( $anbau );
	if ( $anbau ) {
		$formen = wpenon_immoticket24_get_anbau_formen();
		if ( isset( $formen[ $grundriss ] ) && isset( $formen[ $grundriss ][ $wand ] ) ) {
			return true;
		}
	}

	return false;
}

function wpenon_immoticket24_show_wand_porenbeton_bedarf( $grundriss, $a = 0, $b = 0, $c = 0, $d = 0, $e = 0, $f = 0, $g = 0, $h = 0 ) {
	$daemmungen = compact( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h' );

	$formen = wpenon_immoticket24_get_grundriss_formen();
	if ( ! isset( $formen[ $grundriss ] ) ) {
		return false;
	}

	$form = $formen[ $grundriss ];
	foreach ( $daemmungen as $wand => $daemmung ) {
		if ( isset( $form[ $wand ] ) && absint( $daemmung ) > 0 ) {
			return false;
		}
	}

	return true;
}

function wpenon_immoticket24_show_wand_porenbeton_verbrauch( $daemmung ) {
	if ( absint( $daemmung ) === 0 ) {
		return true;
	}

	return false;
}


function wpenon_immoticket24_show_jahr_daemmung( $daemmung, $dach = 'nicht-vorhanden' ) {
	if ( absint( $daemmung ) === 0 || $dach === 'beheizt' ) {
		return false;
	}

	return true;
}


function wpenon_immoticket24_show_wand_bauart( $gebaeudekonstruktion, $bauart ) {
	if ( $gebaeudekonstruktion === $bauart ) {
		return true;
	}

	return false;
}


function wpenon_immoticket24_show_anbauwand_bauart( $anbau, $gebaeudekonstruktion, $bauart ) {
	if ( $gebaeudekonstruktion === $bauart && wpenon_show_on_bool_compare( $anbau, true ) ) {
		return true;
	}

	return false;
}


function wpenon_immoticket24_show_unterkellerung( $keller ) {
	if ( $keller === 'nicht-vorhanden' ) {
		return false;
	}

	return true;
}

function wpenon_immoticket24_calculate_wand( $grundriss, $wand = '', $a = 0.0, $b = 0.0, $c = 0.0, $d = 0.0, $e = 0.0, $f = 0.0, $g = 0.0, $h = 0.0 ) {
	$formen = wpenon_immoticket24_get_grundriss_formen();
	if ( isset( $formen[ $grundriss ] ) && isset( $formen[ $grundriss ][ $wand ] ) ) {
		$formel = $formen[ $grundriss ][ $wand ][0];
		if ( $formel !== true ) {
			$formel           = explode( ' ', $formel );
			$rechnung         = 0.0;
			$current_operator = '+';
			foreach ( $formel as $formel_part ) {
				switch ( $formel_part ) {
					case '+':
					case '-':
						$current_operator = $formel_part;
						break;
					default:
						switch ( $current_operator ) {
							case '+':
								$rechnung += \WPENON\Util\Parse::float( $$formel_part );
								break;
							case '-':
								$rechnung -= \WPENON\Util\Parse::float( $$formel_part );
								break;
							default:
						}
				}
			}
			if ( $rechnung < 0.0 ) {
				$rechnung = 0.0;
			}

			return $rechnung;
		}
	}

	return null;
}

function wpenon_immoticket24_calculate_anbauwand( $grundriss, $wand = '', $b = 0.0, $t = 0.0, $s1 = 0.0, $s2 = 0.0 ) {
	$formen = wpenon_immoticket24_get_anbau_formen();
	if ( isset( $formen[ $grundriss ] ) && isset( $formen[ $grundriss ][ $wand ] ) ) {
		$formel = $formen[ $grundriss ][ $wand ][0];
		if ( $formel !== true ) {
			$formel           = explode( ' ', $formel );
			$rechnung         = 0.0;
			$current_operator = '+';
			foreach ( $formel as $formel_part ) {
				switch ( $formel_part ) {
					case '+':
					case '-':
						$current_operator = $formel_part;
						break;
					default:
						switch ( $current_operator ) {
							case '+':
								$rechnung += \WPENON\Util\Parse::float( $$formel_part );
								break;
							case '-':
								$rechnung -= \WPENON\Util\Parse::float( $$formel_part );
								break;
							default:
						}
				}
			}
			if ( $rechnung < 0.0 ) {
				$rechnung = 0.0;
			}

			return $rechnung;
		}
	}

	return null;
}

function wpenon_immoticket24_show_fenster( $grundriss, $fenster, $nachbar = false, $flaeche = 1.0 ) {
	$flaeche = \WPENON\Util\Parse::float( $flaeche );
	if ( $flaeche > 0.0 ) {
		return wpenon_immoticket24_show_wand( $grundriss, $fenster, $nachbar );
	}

	return false;
}

function wpenon_immoticket24_show_dachfenster( $dach, $flaeche = 1.0 ) {
	$flaeche = \WPENON\Util\Parse::float( $flaeche );
	if ( $dach == 'beheizt' && $flaeche > 0.0 ) {
		return true;
	}

	return false;
}

function wpenon_immoticket24_show_specific_fenster( $fenster_manuell, $grundriss, $fenster, $nachbar = false, $flaeche = 1.0 ) {
	if ( $fenster_manuell ) {
		$flaeche = \WPENON\Util\Parse::float( $flaeche );
		if ( $flaeche > 0.0 ) {
			return wpenon_immoticket24_show_wand( $grundriss, $fenster, $nachbar );
		}
	}

	return false;
}

function wpenon_immoticket24_show_specific_dachfenster( $fenster_manuell, $dach, $flaeche = 1.0 ) {
	if ( $fenster_manuell ) {
		$flaeche = \WPENON\Util\Parse::float( $flaeche );
		if ( $dach == 'beheizt' && $flaeche > 0.0 ) {
			return true;
		}
	}

	return false;
}

function wpenon_immoticket24_show_specific_anbaufenster( $fenster_manuell, $anbau, $grundriss, $fenster, $flaeche = 1.0 ) {
	if ( $fenster_manuell && $anbau ) {
		$flaeche = \WPENON\Util\Parse::float( $flaeche );
		if ( $flaeche > 0.0 ) {
			return wpenon_immoticket24_show_anbauwand( $grundriss, $fenster, $anbau );
		}
	}

	return false;
}

function wpenon_immoticket24_get_ww_info( $h2_info = false, $h3_info = false, $h_erzeuger = false, $h2_erzeuger = false, $h3_erzeuger = false, $show_unbekannt = false ) {
	$info = array();

	$h_erzeuger = \WPENON\Util\Parse::boolean( $h_erzeuger );

	if ( ! $h_erzeuger || wpenon_immoticket24_is_h_erzeuger_ww( $h_erzeuger ) ) {
		$info['h'] = __( 'pauschal in Heizungsanlage enthalten', 'wpenon' );
	}

	$h2_info = \WPENON\Util\Parse::boolean( $h2_info );
	$h3_info = \WPENON\Util\Parse::boolean( $h3_info );

	$h2_erzeuger = \WPENON\Util\Parse::boolean( $h2_erzeuger );
	$h3_erzeuger = \WPENON\Util\Parse::boolean( $h3_erzeuger );

	if ( $h2_info ) {
		if ( ! $h2_erzeuger || wpenon_immoticket24_is_h_erzeuger_ww( $h2_erzeuger ) ) {
			$info['h2'] = __( 'pauschal in 2. Heizungsanlage enthalten', 'wpenon' );
		}
		if ( $h3_info ) {
			if ( ! $h3_erzeuger || wpenon_immoticket24_is_h_erzeuger_ww( $h3_erzeuger ) ) {
				$info['h3'] = __( 'pauschal in 3. Heizungsanlage enthalten', 'wpenon' );
			}
		}
	}
	$info['ww'] = __( 'separat angegeben', 'wpenon' );

	if ( $show_unbekannt ) {
		$info['unbekannt'] = __( 'unbekannt', 'wpenon' );
	}

	return $info;
}

function wpenon_immoticket24_is_h_erzeuger_ww( $erzeuger ) {
	$ww = wpenon_get_table_results( 'ww_erzeugung', array(
		'bezeichnung' => array(
			'value'   => $erzeuger,
			'compare' => '='
		)
	), array( 'name' ), true );
	if ( $ww ) {
		return true;
	}

	return false;
}

function wpenon_immoticket24_get_zeitraum_headline( $datum, $index = 0 ) {
	if ( empty( $datum ) ) {
		return __( 'Verbrauchsdaten', 'wpenon' );
	}

	$date1 = wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $datum, $index, false, 'data' );
	$date2 = wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $datum, $index, true, 'data' );

	return sprintf( __( 'Verbrauchsdaten für %1$s - %2$s', 'wpenon' ), $date1, $date2 );
}

function wpenon_immoticket24_get_energietraeger_unit( $energietraeger ) {
	return wpenon_get_table_results( 'energietraeger_umrechnungen', array(
		'bezeichnung' => array(
			'value'   => $energietraeger,
			'compare' => '='
		)
	), array( 'einheit' ), true );
}

function wpenon_immoticket24_get_energietraeger_by_erzeugung(
	$erzeugung,
	$energietraeger_standardkessel,
	$energietraeger_niedertemperaturkessel,
	$energietraeger_brennwertkessel,
	$energietraeger_brennwertkesselverbessert,
	$energietraeger_kleinthermeniedertemperatur,
	$energietraeger_kleinthermebrennwert,
	$energietraeger_fernwaerme,
	$energietraeger_waermepumpeluft,
	$energietraeger_waermepumpewasser,
	$energietraeger_waermepumpeerde,
	$energietraeger_elektronachtspeicherheizung,
	$energietraeger_elektrodirektheizgeraet,
	$energietraeger_pelletfeuerung,
	$energietraeger_kohleholzofen,
	$energietraeger_gasraumheizer,
	$energietraeger_oelofenverdampfungsbrenner
) {
	if ( empty( $erzeugung ) ) {
		return;
	}

	switch ( $erzeugung ) {
		case 'standardkessel':
			$energietraeger = $energietraeger_standardkessel;
			break;
		case 'niedertemperaturkessel':
			$energietraeger = $energietraeger_niedertemperaturkessel;
			break;
		case 'brennwertkessel':
			$energietraeger = $energietraeger_brennwertkessel;
			break;
		case 'brennwertkesselverbessert':
			$energietraeger = $energietraeger_brennwertkesselverbessert;
			break;
		case 'kleinthermeniedertemperatur':
			$energietraeger = $energietraeger_kleinthermeniedertemperatur;
			break;
		case 'kleinthermebrennwert':
			$energietraeger = $energietraeger_kleinthermebrennwert;
			break;
		case 'fernwaerme':
			$energietraeger = $energietraeger_fernwaerme;
			break;
		case 'waermepumpeluft':
			$energietraeger = $energietraeger_waermepumpeluft;
			break;
		case 'waermepumpewasser':
			$energietraeger = $energietraeger_waermepumpewasser;
			break;
		case 'waermepumpeerde':
			$energietraeger = $energietraeger_waermepumpeerde;
			break;
		case 'elektronachtspeicherheizung':
			$energietraeger = $energietraeger_elektronachtspeicherheizung;
			break;
		case 'elektrodirektheizgeraet':
			$energietraeger = $energietraeger_elektrodirektheizgeraet;
			break;
		case 'pelletfeuerung':
			$energietraeger = $energietraeger_pelletfeuerung;
			break;
		case 'kohleholzofen':
			$energietraeger = $energietraeger_kohleholzofen;
			break;
		case 'gasraumheizer':
			$energietraeger = $energietraeger_gasraumheizer;
			break;
		case 'oelofenverdampfungsbrenner':
			$energietraeger = $energietraeger_oelofenverdampfungsbrenner;
			break;
		default:
			break;
	}

	return $energietraeger;
}

function wpenon_immoticket24_get_ww_energietraeger_by_erzeugung(
	$erzeugung,
	$energietraeger_dezentralelektroerhitzer,
	$energietraeger_dezentralkleinspeicher,
	$energietraeger_dezentralgaserhitzer
) {
	if ( empty( $erzeugung ) ) {
		return;
	}

	switch ( $erzeugung ) {
		case 'dezentralelektroerhitzer':
			$energietraeger = $energietraeger_dezentralelektroerhitzer;
			break;
		case 'dezentralkleinspeicher':
			$energietraeger = $energietraeger_dezentralkleinspeicher;
			break;
		case 'dezentralgaserhitzer':
			$energietraeger = $energietraeger_dezentralgaserhitzer;
			break;
		default:
			break;
	}

	return $energietraeger;
}

function wpenon_immoticket24_show_verteilung_gedaemmt( $erzeugung, $blacklist, $baujahr, $baujahr_limit ) {
	if ( ! wpenon_show_on_array_blacklist( $erzeugung, $blacklist ) ) {
		return false;
	}
	if ( ! wpenon_show_on_number_lower( $baujahr, $baujahr_limit ) ) {
		return false;
	}

	return true;
}


