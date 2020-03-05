<?php

function wpenon_get_enev_pdf_data( $context, $index = 0, $energieausweis = null, $data = array() ) {
	switch ( $context ) {
		case 'gebaeudetyp':
			if ( 'freistehend' === $energieausweis->gebaeudetyp ) {
				$wohnungen = (int) $energieausweis->wohnungen;
				if ( 2 < $wohnungen ) {
					return __( 'freistehendes Mehrfamilienhaus', 'wpenon' );
				}
				if ( 2 === $wohnungen ) {
					return __( 'freistehendes Zweifamilienhaus', 'wpenon' );
				}

				return __( 'freistehendes Einfamilienhaus', 'wpenon' );
			}
			if ( 'sonstiges' === $energieausweis->gebaeudetyp && 'gesamt' === $energieausweis->gebaeudeteil ) {
				$wohnungen = (int) $energieausweis->wohnungen;
				if ( 2 < $wohnungen ) {
					return __( 'Mehrfamilienhaus', 'wpenon' );
				}
				if ( 2 === $wohnungen ) {
					return __( 'Zweifamilienhaus', 'wpenon' );
				}

				return __( 'Einfamilienhaus', 'wpenon' );
			}

			return $energieausweis->formatted_gebaeudetyp;
		case 'gebaeudeteil':
			$gemischt = $energieausweis->gebaeudeteil === 'gemischt';
			if ( $gemischt ) {
				if ( $energieausweis->building == 'n' ) {
					return __( 'Gewerbeteil gemischt genutztes Gebäude', 'wpenon' );
				}

				return __( 'Wohnteil gemischt genutztes Gebäude', 'wpenon' );
			}

			return __( 'Gesamt', 'wpenon' );
		case 'baujahr':
			$baujahre = '' . $energieausweis->baujahr;
			if ( 'b' === $energieausweis->mode && $energieausweis->anbau && ! empty( $energieausweis->anbau_baujahr ) ) {
				$baujahre .= ', ' . $energieausweis->anbau_baujahr . ' (Anbau)';
			}

			return $baujahre;
		case 'baujahr_erzeuger':
			$baujahre   = array();
			$baujahre[] = $energieausweis->h_baujahr;
			if ( $energieausweis->h2_info ) {
				$baujahre[] = $energieausweis->h2_baujahr;
				if ( $energieausweis->h3_info ) {
					$baujahre[] = $energieausweis->h3_baujahr;
				}
			}
			if ( $energieausweis->ww_info == 'ww' ) {
				$baujahre[] = $energieausweis->ww_baujahr;
			}

			return implode( ', ', array_unique( $baujahre ) );
		case 'wohnungen':
			return $energieausweis->formatted_wohnungen;
		case 'nutzflaeche':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['nutzflaeche'] ) ) {
				return $calculations['nutzflaeche'];
			}

			return 0.00;
		case 'nutzflaeche_aus_wohnflaeche':
			if ( $energieausweis->mode == 'b' ) {
				return false;
			}

			return true;
		case 'energietraeger_heizung':
			$energietraeger = array();
			if ( $energieausweis->mode == 'b' ) {
				$energietraeger[] = $energieausweis->formatted_h_energietraeger;
				if ( $energieausweis->h2_info ) {
					$energietraeger[] = $energieausweis->formatted_h2_energietraeger;
					if ( $energieausweis->h3_info ) {
						$energietraeger[] = $energieausweis->formatted_h3_energietraeger;
					}
				}
			} else {
				$energietraeger[] = wpenon_immoticket24_get_energietraeger_name( $energieausweis->h_energietraeger, true );
				if ( $energieausweis->h2_info ) {
					$energietraeger[] = wpenon_immoticket24_get_energietraeger_name( $energieausweis->h2_energietraeger, true );
					if ( $energieausweis->h3_info ) {
						$energietraeger[] = wpenon_immoticket24_get_energietraeger_name( $energieausweis->h3_energietraeger, true );
					}
				}
			}

			return implode( ', ', array_unique( $energietraeger ) );
		case 'energietraeger_warmwasser':
			if ( $energieausweis->ww_info == 'ww' ) {
				if ( $energieausweis->mode == 'b' ) {
					return $energieausweis->formatted_ww_energietraeger;
				} else {
					return wpenon_immoticket24_get_energietraeger_name( $energieausweis->ww_energietraeger, true );
				}
			}

			return '';
		case 'regenerativ_art':
			return $energieausweis->regenerativ_art;
		case 'regenerativ_nutzung':
			return $energieausweis->regenerativ_nutzung;
		case 'lueftungsart':
			$l_info = $energieausweis->l_info;
			if ( $l_info == 'anlage' ) {
				return $energieausweis->l_erzeugung;
			}

			return $l_info;
		case 'kuehlung':
			if ( $energieausweis->k_info == 'vorhanden' ) {
				return true;
			}

			return false;
		case 'anlass':
			if ( 'vermietung' === $energieausweis->anlass ) {
				return 'verkauf';
			}

			return $energieausweis->anlass;
		case 'datenerhebung_durch_aussteller':
			return false;
		case 'zusatzinformationen_beigefuegt':
			return false;
		case 'reference':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['reference'] ) ) {
				return $calculations['reference'];
			}

			return null;
		case 'endenergie':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['endenergie'] ) ) {
				return $calculations['endenergie'];
			}

			return null;
		case 'primaerenergie':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['primaerenergie'] ) ) {
				return $calculations['primaerenergie'];
			}

			return null;
		case 's_reference':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['s_reference'] ) ) {
				return $calculations['s_reference'];
			}

			return null;
		case 's_endenergie':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['s_endenergie'] ) ) {
				return $calculations['s_endenergie'];
			}

			return null;
		case 's_primaerenergie':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['s_primaerenergie'] ) ) {
				return $calculations['s_primaerenergie'];
			}

			return null;
		case 'co2_emissionen':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['co2_emissionen'] ) ) {
				return $calculations['co2_emissionen'];
			}

			return null;
		case 'ht':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['ht_b'] ) ) {
				return $calculations['ht_b'];
			}

			return null;
		case 'ht_reference':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['ht_b_reference'] ) ) {
				return $calculations['ht_b_reference'];
			}

			return null;
		case 'endenergie_reference':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['endenergie_reference'] ) ) {
				return $calculations['endenergie_reference'];
			}

			return null;
		case 'primaerenergie_reference':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['primaerenergie_reference'] ) ) {
				return $calculations['primaerenergie_reference'];
			}

			return null;
		case 'verfahren':
			return 'din-v-4108-6';
		case 'regelung_absatz5':
			return false;
		case 'verfahren_vereinfacht':
			return true;
		case 'waermeschutz_eingehalten':
			if ( $energieausweis->anlass == 'neubau' ) {
				$calculations = $energieausweis->calculate();
				if ( isset( $calculations['ht'] ) && isset( $calculations['ht_reference'] ) ) {
					return $calculations['ht'] <= $calculations['ht_reference'];
				}
			}

			return false;
		case 'warmwasser_enthalten':
			return true;
		case 's_nutzung':
			if ( isset( $energieausweis->s_nutzung ) ) {
				return $energieausweis->s_nutzung;
			}

			return array();
		case 'verbrauchserfassung':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['verbrauchsdaten'] ) ) {
				return $calculations['verbrauchsdaten'];
			}

			return array();
		case 'gebaeudebereiche':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['gebaeudebereiche'] ) ) {
				return $calculations['gebaeudebereiche'];
			}

			return array();
		case 'modernisierungsempfehlungen':
			return wpenon_immoticket24_get_modernisierungsempfehlungen( $energieausweis );
		default:
	}

	return '';
}
