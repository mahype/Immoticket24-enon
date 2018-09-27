<?php

function wpenon_get_enev2013_xml_zusatzdatenerfassung_data( $context, $index = 0, $energieausweis = null, $data = array() ) {
  if ( isset( $data['mode'] ) ) {
    switch ( $data['mode'] ) {
      case 'occurrences':
        $min = $data['min'];
        $max = $data['max'];
        switch ( $context ) {
          case 'Modernisierungsvorschlag':
            $modernisierungsempfehlungen = wpenon_immoticket24_get_modernisierungsempfehlungen( $energieausweis );
            return count( $modernisierungsempfehlungen );
          case 'Wohnflaeche':
            return 1;
          case 'Keller-beheizt':
            return 1;
          case 'Energietraeger':
            $count = 1;
            if ( $energieausweis->mode == 'v' ) {
              if ( $energieausweis->h2_info ) {
                $count++;
                if ( $energieausweis->h3_info ) {
                  $count++;
                }
              }
              if ( $energieausweis->ww_info == 'ww' ) {
                $count++;
              }
            }
            return $count;
          case 'Verbrauchsperiode':
            return 3;
          case 'Verbrauchswert-kWh-Strom':
            if ( $energieausweis->building == 'n' ) {
              return 1;
            }
            return 0;
          case 'dezentrales-Warmwasser':
            /*if ( $energieausweis->ww_info == 'ww' ) {
              return 1;
            }*/
            return 0;
          case 'Wohngebaeude-gekuehlt':
            return 0;
          case 'Dach-Aussenluft':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['dach'] ) ) {
              return 1;
            }
            return 0;
          case 'Geschossdecke':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['decke'] ) ) {
              return 1;
            }
            return 0;
          case 'Wand-Aussenluft-Nord':
          case 'Wand-Aussenluft-Ost':
          case 'Wand-Aussenluft-Sued':
          case 'Wand-Aussenluft-West':
          case 'Wand-Aussenluft-Nordost':
          case 'Wand-Aussenluft-Suedost':
          case 'Wand-Aussenluft-Suedwest':
          case 'Wand-Aussenluft-Nordwest':
            $calculations = $energieausweis->calculate();
            $hr_mappings = wpenon_get_enev2013_himmelsrichtungen_mappings();
            $key = str_replace( 'Wand-Aussenluft-', '', $context );
            if ( isset( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ] ) ) {
              return count( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ] );
            }
            return 0;
          case 'Wand-Erdreich':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['kellerwand'] ) ) {
              return 1;
            }
            return 0;
          case 'Boden-andere-Temp':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['kellerdecke'] ) ) {
              return 1;
            }
            return 0;
          case 'Boden-Erdreich':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['boden'] ) ) {
              return 1;
            }
            return 0;
          case 'Fenster-Dach':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['fenster_dach'] ) ) {
              return 1;
            }
            return 0;
          case 'Fenster-Nord':
          case 'Fenster-Ost':
          case 'Fenster-Sued':
          case 'Fenster-West':
          case 'Fenster-Nordost':
          case 'Fenster-Suedost':
          case 'Fenster-Suedwest':
          case 'Fenster-Nordwest':
            $calculations = $energieausweis->calculate();
            $hr_mappings = wpenon_get_enev2013_himmelsrichtungen_mappings();
            $key = str_replace( 'Fenster-', '', $context );
            if ( isset( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ] ) ) {
              $count = 0;
              foreach ( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ] as $wand ) {
                if ( isset( $calculations['bauteile'][ 'fenster_' . $wand ] ) ) {
                  $count++;
                }
              }
              return $count;
            }
            return 0;
          case 'Heizanlage':
            $calculations = $energieausweis->calculate();
            $count = 0;
            foreach ( $calculations['anlagendaten'] as $anlage ) {
              if ( $anlage['art'] == 'heizung' ) {
                $count++;
              }
            }
            return $count;
          case 'Warmwasseranlage':
            $calculations = $energieausweis->calculate();
            $count = 0;
            foreach ( $calculations['anlagendaten'] as $anlage ) {
              if ( $anlage['art'] == 'warmwasser' ) {
                $count++;
              }
            }
            return $count;
          default:
        }
        break;
      case 'attribute':
        $attribute = $data['attribute'];
        switch ( $attribute['name'] ) {
          case 'EnEV-Version':
            return \WPENON\Model\EnergieausweisManager::instance()->getStandardDate( 'Y', $energieausweis );
          default:
        }
        break;
      case 'choice':
        $choices = $data['choices'];
        switch ( $context ) {
          case 'Energieausweis-Daten':
            if ( $energieausweis->building == 'n' ) {
              return $choices[1];
            }
            return $choices[0];
          case 'Wohngebaeude':
            if ( $energieausweis->mode == 'b' ) {
              return $choices[2];
            }
            return $choices[0];
          case 'Nichtwohngebaeude':
            if ( $energieausweis->mode == 'b' ) {
              return $choices[1];
            }
            return $choices[0];
          case 'Energietraeger':
            if ( in_array( 'Energietraegerbezeichnung', $choices ) ) {
              $counts = wpenon_get_enev2013_anlagen_counts( $energieausweis );
              if ( isset( $counts[ $index ] ) ) {
                $traeger_key = $counts[ $index ] . '_energietraeger';

                // Migrate old data to new Energietraeger table data.
                if ( false !== strpos( $energieausweis->$traeger_key, '_kwhheizwert' ) ) {
                  $energieausweis->$traeger_key = str_replace( '_kwhheizwert', '_kwh', $energieausweis->$traeger_key );
                } elseif ( false !== strpos( $energieausweis->$traeger_key, '_kwhbrennwert' ) ) {
                  $energieausweis->$traeger_key = str_replace( '_kwhbrennwert', '_kwh', $energieausweis->$traeger_key );
                }

                $traeger = $energieausweis->$traeger_key;

                $mappings = wpenon_get_enev2013_energietraeger_unit_mappings();
                if ( ! isset( $mappings[ $traeger ] ) || is_int( $mappings[ $traeger ] ) && $mappings[ $traeger ] < 0 ) {
                  return $choices[1];
                }
                return $choices[0];
              }
            }
            return false;
          case 'Verbrauchsdaten':
            if ( in_array( 'kein-Leerstand', $choices ) ) {
              if ( $energieausweis->verbrauch1_leerstand > 0 || $energieausweis->verbrauch2_leerstand > 0 || $energieausweis->verbrauch3_leerstand > 0 ) {
                return $choices[1];
              }
              return $choices[0];
            }
            return false;
          default:
        }
        break;
      case 'value':
        $item = $data['item'];
        switch ( $context ) {
          case 'Registriernummer':
            $registriernummer = $energieausweis->registriernummer;
            if ( ! empty( $registriernummer ) ) {
              return $registriernummer;
            }
            return 'AA-' . date( 'Y' ) . '-000000000';
          case 'Ausstellungsdatum':
            return \WPENON\Model\EnergieausweisManager::instance()->getReferenceDate( 'd.m.Y', $energieausweis );
          case 'Bundesland':
            if ( in_array( $energieausweis->adresse_bundesland, $item['options'] ) ) {
              return $energieausweis->adresse_bundesland;
            }
          case 'Postleitzahl':
            return substr( $energieausweis->adresse_plz, 0, 3 ) . 'XX';
          case 'Gebaeudeteil':
            $gemischt = $energieausweis->gebaeudetyp == 'gemischt';
            if ( $gemischt ) {
              if ( $energieausweis->building == 'n' ) {
                return 'Nichtwohnteil gemischt genutztes Gebäude';
              }
              return 'Wohnteil gemischt genutztes Gebäude';
            }
            return 'Ganzes Gebäude';
          case 'Baujahr-Gebaeude':
            return $energieausweis->baujahr;
          case 'Baujahr-Waermeerzeuger':
            $baujahre = array( $energieausweis->h_baujahr );
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
          case 'Altersklasse-Gebaeude':
          case 'Altersklasse-Waermeerzeuger':
            $baujahr = $energieausweis->baujahr;
            if ( $context == 'Altersklasse-Waermeerzeuger' ) {
              $baujahr = $energieausweis->h_baujahr;
            }
            $count = count( $item['options'] );
            foreach ( $item['options'] as $key => $option ) {
              if ( $key == 0 ) {
                if ( $baujahr <= intval( substr( $option, 4, 4 ) ) ) {
                  $index = $key;
                  break;
                }
              } elseif ( $key == $count - 1 ) {
                $index = $key;
                break;
              } else {
                if ( $baujahr <= intval( substr( $option, 7, 4 ) ) ) {
                  $index = $key;
                  break;
                }
              }
            }
            return $item['options'][ $index ];
          case 'wesentliche-Energietraeger':
            $calculations = $energieausweis->calculate();
            $energietraeger = array();
            foreach ( $calculations['anlagendaten'] as $key => $data ) {
              $energietraeger[] = $data['energietraeger'];
            }
            return implode( '; ', array_unique( $energietraeger ) );
          case 'Erneuerbare-Art':
            $art = $energieausweis->regenerativ_art;
            if ( strtolower( $art ) == 'unbekannt' || strtolower( $art ) == 'keine' ) {
              return 'Keine';
            }
            return str_replace( ',', ';', $art );
          case 'Erneuerbare-Verwendung':
            $nutzung = $energieausweis->regenerativ_nutzung;
            if ( strtolower( $nutzung ) == 'unbekannt' || strtolower( $nutzung ) == 'keine' ) {
              return 'Keine';
            }
            return str_replace( ',', ';', $nutzung );
          case 'Lueftungsart-Fensterlueftung':
            if ( $energieausweis->l_info == 'fenster' ) {
              return 'true';
            }
            return 'false';
          case 'Lueftungsart-Schachtlueftung':
            if ( $energieausweis->l_info == 'schacht' ) {
              return 'true';
            }
            return 'false';
          case 'Lueftungsart-Anlage-o-WRG':
            if ( $energieausweis->l_info == 'anlage' ) {
              if ( substr( $energieausweis->l_erzeugung, 0, 4 ) == 'ohne' ) {
                return 'true';
              }
            }
            return 'false';
          case 'Lueftungsart-Anlage-m-WRG':
            if ( $energieausweis->l_info == 'anlage' ) {
              if ( substr( $energieausweis->l_erzeugung, 0, 3 ) == 'mit' ) {
                return 'true';
              }
            }
            return 'false';
          case 'Anlage-zur-Kuehlung':
            if ( $energieausweis->k_info == 'vorhanden' ) {
              return 'true';
            }
            return 'false';
          case 'Ausstellungsanlass':
            $anlass = $energieausweis->anlass;
            $mappings = array( 'neubau', 'modernisierung', 'verkauf', 'aushang', 'sonstiges' );
            $key = array_search( $anlass, $mappings );
            if ( isset( $item['options'][ $key ] ) ) {
              return $item['options'][ $key ];
            }
            return false;
          case 'Datenerhebung-Aussteller':
            return 'false';
          case 'Datenerhebung-Eigentuemer':
            return 'true';
          case 'Empfehlungen-moeglich':
            $modernisierungsempfehlungen = wpenon_immoticket24_get_modernisierungsempfehlungen( $energieausweis );
            if ( count( $modernisierungsempfehlungen ) > 0 ) {
              return 'true';
            }
            return 'false';
          case 'Extrablatt-Modernisierung':
            return 'false';
          case 'Modernisierungsvorschlag::0_Nummer':
          case 'Modernisierungsvorschlag::1_Nummer':
          case 'Modernisierungsvorschlag::2_Nummer':
          case 'Modernisierungsvorschlag::3_Nummer':
          case 'Modernisierungsvorschlag::4_Nummer':
          case 'Modernisierungsvorschlag::5_Nummer':
          case 'Modernisierungsvorschlag::6_Nummer':
          case 'Modernisierungsvorschlag::7_Nummer':
          case 'Modernisierungsvorschlag::8_Nummer':
          case 'Modernisierungsvorschlag::9_Nummer':
            $parent_index = absint( str_replace( array( 'Modernisierungsvorschlag::', '_Nummer' ), '', $context ) );
            return $parent_index + 1;
          case 'Modernisierungsvorschlag::0_Bauteil-Anlagenteil':
          case 'Modernisierungsvorschlag::1_Bauteil-Anlagenteil':
          case 'Modernisierungsvorschlag::2_Bauteil-Anlagenteil':
          case 'Modernisierungsvorschlag::3_Bauteil-Anlagenteil':
          case 'Modernisierungsvorschlag::4_Bauteil-Anlagenteil':
          case 'Modernisierungsvorschlag::5_Bauteil-Anlagenteil':
          case 'Modernisierungsvorschlag::6_Bauteil-Anlagenteil':
          case 'Modernisierungsvorschlag::7_Bauteil-Anlagenteil':
          case 'Modernisierungsvorschlag::8_Bauteil-Anlagenteil':
          case 'Modernisierungsvorschlag::9_Bauteil-Anlagenteil':
            $parent_index = absint( str_replace( array( 'Modernisierungsvorschlag::', '_Bauteil-Anlagenteil' ), '', $context ) );
            $modernisierungsempfehlungen = wpenon_immoticket24_get_modernisierungsempfehlungen( $energieausweis );
            $mappings = array(
              'Dach'                      => 0,
              'Oberste Geschossdecke'     => 1,
              'Außenwände'                => 5,
              'Kellerdecke / Bodenplatte' => 13,
              'Fenster'                   => 6,
              'Solarthermie'              => 30,
            );
            if ( isset( $modernisierungsempfehlungen[ $parent_index ] ) ) {
              if ( isset( $mappings[ $modernisierungsempfehlungen[ $parent_index ]['bauteil'] ] ) ) {
                return $item['options'][ $mappings[ $modernisierungsempfehlungen[ $parent_index ]['bauteil'] ] ];
              }
            }
            return false;
          case 'Modernisierungsvorschlag::0_Massnahmenbeschreibung':
          case 'Modernisierungsvorschlag::1_Massnahmenbeschreibung':
          case 'Modernisierungsvorschlag::2_Massnahmenbeschreibung':
          case 'Modernisierungsvorschlag::3_Massnahmenbeschreibung':
          case 'Modernisierungsvorschlag::4_Massnahmenbeschreibung':
          case 'Modernisierungsvorschlag::5_Massnahmenbeschreibung':
          case 'Modernisierungsvorschlag::6_Massnahmenbeschreibung':
          case 'Modernisierungsvorschlag::7_Massnahmenbeschreibung':
          case 'Modernisierungsvorschlag::8_Massnahmenbeschreibung':
          case 'Modernisierungsvorschlag::9_Massnahmenbeschreibung':
            $parent_index = absint( str_replace( array( 'Modernisierungsvorschlag::', '_Massnahmenbeschreibung' ), '', $context ) );
            $modernisierungsempfehlungen = wpenon_immoticket24_get_modernisierungsempfehlungen( $energieausweis );
            if ( isset( $modernisierungsempfehlungen[ $parent_index ] ) ) {
              return $modernisierungsempfehlungen[ $parent_index ]['beschreibung'];
            }
            return false;
          case 'Modernisierungsvorschlag::0_Modernisierungskombination':
          case 'Modernisierungsvorschlag::1_Modernisierungskombination':
          case 'Modernisierungsvorschlag::2_Modernisierungskombination':
          case 'Modernisierungsvorschlag::3_Modernisierungskombination':
          case 'Modernisierungsvorschlag::4_Modernisierungskombination':
          case 'Modernisierungsvorschlag::5_Modernisierungskombination':
          case 'Modernisierungsvorschlag::6_Modernisierungskombination':
          case 'Modernisierungsvorschlag::7_Modernisierungskombination':
          case 'Modernisierungsvorschlag::8_Modernisierungskombination':
          case 'Modernisierungsvorschlag::9_Modernisierungskombination':
            $parent_index = absint( str_replace( array( 'Modernisierungsvorschlag::', '_Modernisierungskombination' ), '', $context ) );
            $modernisierungsempfehlungen = wpenon_immoticket24_get_modernisierungsempfehlungen( $energieausweis );
            if ( isset( $modernisierungsempfehlungen[ $parent_index ] ) ) {
              if ( $modernisierungsempfehlungen[ $parent_index ]['gesamt'] ) {
                return $item['options'][0];
              }
              return $item['options'][1];
            }
            return false;
          // Wohngebäude
          case 'Gebaeudetyp':
            return $energieausweis->formatted_gebaeudetyp;
          case 'Anzahl-Wohneinheiten':
            return $energieausweis->wohnungen;
          case 'Gebaeudenutzflaeche':
            $calculations = $energieausweis->calculate();
            return number_format( $calculations['nutzflaeche'], 0, ',', '' );
          // Verbrauchsdaten
          case 'Flaechenermittlung-AN-aus-Wohnflaeche':
            if ( $energieausweis->mode == 'b' ) {
              return 'false';
            }
            return 'true';
          case 'Wohnflaeche':
            return number_format( floatval( $energieausweis->flaeche ), 0, ',', '' );
          case 'Keller-beheizt':
            if ( $energieausweis->keller == 'beheizt' ) {
              return 'true';
            }
            return 'false';
          case 'kein-Leerstand':
            return 'Kein längerer Leerstand zu berücksichtigen.';
          case 'Endenergiekennwert-Verbrauch-AN':
            $calculations = $energieausweis->calculate();
            return number_format( $calculations['endenergie'], 1, ',', '' );
          case 'Primaerenergiekennwert-Verbrauch-AN':
            $calculations = $energieausweis->calculate();
            return number_format( $calculations['primaerenergie'], 1, ',', '' );
          case 'Energieeffizienzklasse':
            $calculations = $energieausweis->calculate();
            return wpenon_get_class( $calculations['primaerenergie'], $energieausweis->wpenon_type );
          // Energietraeger
          case 'Energietraeger::0_Energietraegerbezeichnung':
          case 'Energietraeger::1_Energietraegerbezeichnung':
          case 'Energietraeger::2_Energietraegerbezeichnung':
          case 'Energietraeger::3_Energietraegerbezeichnung':
            $parent_index = absint( str_replace( array( 'Energietraeger::', '_Energietraegerbezeichnung' ), '', $context ) );
            $counts = wpenon_get_enev2013_anlagen_counts( $energieausweis );
            if ( isset( $counts[ $parent_index ] ) ) {
              $traeger_key = $counts[ $parent_index ] . '_energietraeger';

              // Migrate old data to new Energietraeger table data.
              if ( false !== strpos( $energieausweis->$traeger_key, '_kwhheizwert' ) ) {
                $energieausweis->$traeger_key = str_replace( '_kwhheizwert', '_kwh', $energieausweis->$traeger_key );
              } elseif ( false !== strpos( $energieausweis->$traeger_key, '_kwhbrennwert' ) ) {
                $energieausweis->$traeger_key = str_replace( '_kwhbrennwert', '_kwh', $energieausweis->$traeger_key );
              }

              $traeger = $energieausweis->$traeger_key;

              $mappings = wpenon_get_enev2013_energietraeger_unit_mappings();
              if ( isset( $mappings[ $traeger ] ) ) {
                if ( is_array( $mappings[ $traeger ] ) ) {
                  $erzeugung_key = $counts[ $parent_index ] . '_erzeugung';
                  switch ( $energieausweis->$erzeugung_key ) {
                    case 'brennwertkessel':
                    case 'brennwertkesselverbessert':
                    case 'brennwerttherme':
                    case 'kleinthermebrennwert':
                      $mappings[ $traeger ] = $mappings[ $traeger ][1];
                      break;
                    default:
                      $mappings[ $traeger ] = $mappings[ $traeger ][0];
                  }
                }

                return $item['options'][ $mappings[ $traeger ] ];
              }
            }
            return false;
          case 'Energietraeger::0_Sonstiger-Energietraeger-Verbrauch':
          case 'Energietraeger::1_Sonstiger-Energietraeger-Verbrauch':
          case 'Energietraeger::2_Sonstiger-Energietraeger-Verbrauch':
          case 'Energietraeger::3_Sonstiger-Energietraeger-Verbrauch':
            $parent_index = absint( str_replace( array( 'Energietraeger::', '_Sonstiger-Energietraeger-Verbrauch' ), '', $context ) );
            $counts = wpenon_get_enev2013_anlagen_counts( $energieausweis );
            if ( isset( $counts[ $parent_index ] ) ) {
              $calculations = $energieausweis->calculate();
              $traeger = $calculations['anlagendaten'][ $counts[ $parent_index ] ];
              return $traeger['energietraeger'] . ' in ' . \WPENON\Util\Format::unit( $traeger['einheit'], false );
            }
            return false;
          case 'Energietraeger::0_Umrechnungsfaktor':
          case 'Energietraeger::1_Umrechnungsfaktor':
          case 'Energietraeger::2_Umrechnungsfaktor':
          case 'Energietraeger::3_Umrechnungsfaktor':
            $parent_index = absint( str_replace( array( 'Energietraeger::', '_Umrechnungsfaktor' ), '', $context ) );
            $counts = wpenon_get_enev2013_anlagen_counts( $energieausweis );
            if ( isset( $counts[ $parent_index ] ) ) {
              $calculations = $energieausweis->calculate();
              $traeger = $calculations['anlagendaten'][ $counts[ $parent_index ] ];
              return number_format( $traeger['energietraeger_mpk'], 2 );
            }
            return false;
          case 'Energietraeger::0_Primaerenergiefaktor':
          case 'Energietraeger::1_Primaerenergiefaktor':
          case 'Energietraeger::2_Primaerenergiefaktor':
          case 'Energietraeger::3_Primaerenergiefaktor':
            $parent_index = absint( str_replace( array( 'Energietraeger::', '_Primaerenergiefaktor' ), '', $context ) );
            $counts = wpenon_get_enev2013_anlagen_counts( $energieausweis );
            if ( isset( $counts[ $parent_index ] ) ) {
              $calculations = $energieausweis->calculate();
              $traeger = $calculations['anlagendaten'][ $counts[ $parent_index ] ];
              return number_format( $traeger['energietraeger_primaer'], 1, ',', '' );
            }
            return false;
          case 'Energietraeger::0_Verbrauchsperiode::0_Startdatum':
          case 'Energietraeger::0_Verbrauchsperiode::1_Startdatum':
          case 'Energietraeger::0_Verbrauchsperiode::2_Startdatum':
          case 'Energietraeger::1_Verbrauchsperiode::0_Startdatum':
          case 'Energietraeger::1_Verbrauchsperiode::1_Startdatum':
          case 'Energietraeger::1_Verbrauchsperiode::2_Startdatum':
          case 'Energietraeger::2_Verbrauchsperiode::0_Startdatum':
          case 'Energietraeger::2_Verbrauchsperiode::1_Startdatum':
          case 'Energietraeger::2_Verbrauchsperiode::2_Startdatum':
          case 'Energietraeger::3_Verbrauchsperiode::0_Startdatum':
          case 'Energietraeger::3_Verbrauchsperiode::1_Startdatum':
          case 'Energietraeger::3_Verbrauchsperiode::2_Startdatum':
            $parts = explode( '_', $context );
            $grandparent_index = absint( substr( $parts[0], -1 ) );
            $parent_index = absint( substr( $parts[1], -1 ) );
            $counts = wpenon_get_enev2013_anlagen_counts( $energieausweis );
            if ( isset( $counts[ $grandparent_index ] ) ) {
              $calculations = $energieausweis->calculate();
              $traeger = $calculations['anlagendaten'][ $counts[ $grandparent_index ] ];
              if ( isset( $traeger['verbrauch'][ $parent_index ] ) ) {
                return $traeger['verbrauch'][ $parent_index ]['start'];
              }
            }
            return false;
          case 'Energietraeger::0_Verbrauchsperiode::0_Enddatum':
          case 'Energietraeger::0_Verbrauchsperiode::1_Enddatum':
          case 'Energietraeger::0_Verbrauchsperiode::2_Enddatum':
          case 'Energietraeger::1_Verbrauchsperiode::0_Enddatum':
          case 'Energietraeger::1_Verbrauchsperiode::1_Enddatum':
          case 'Energietraeger::1_Verbrauchsperiode::2_Enddatum':
          case 'Energietraeger::2_Verbrauchsperiode::0_Enddatum':
          case 'Energietraeger::2_Verbrauchsperiode::1_Enddatum':
          case 'Energietraeger::2_Verbrauchsperiode::2_Enddatum':
          case 'Energietraeger::3_Verbrauchsperiode::0_Enddatum':
          case 'Energietraeger::3_Verbrauchsperiode::1_Enddatum':
          case 'Energietraeger::3_Verbrauchsperiode::2_Enddatum':
            $parts = explode( '_', $context );
            $grandparent_index = absint( substr( $parts[0], -1 ) );
            $parent_index = absint( substr( $parts[1], -1 ) );
            $counts = wpenon_get_enev2013_anlagen_counts( $energieausweis );
            if ( isset( $counts[ $grandparent_index ] ) ) {
              $calculations = $energieausweis->calculate();
              $traeger = $calculations['anlagendaten'][ $counts[ $grandparent_index ] ];
              if ( isset( $traeger['verbrauch'][ $parent_index ] ) ) {
                return $traeger['verbrauch'][ $parent_index ]['ende'];
              }
            }
            return false;
          case 'Energietraeger::0_Verbrauchsperiode::0_Verbrauchswert':
          case 'Energietraeger::0_Verbrauchsperiode::1_Verbrauchswert':
          case 'Energietraeger::0_Verbrauchsperiode::2_Verbrauchswert':
          case 'Energietraeger::1_Verbrauchsperiode::0_Verbrauchswert':
          case 'Energietraeger::1_Verbrauchsperiode::1_Verbrauchswert':
          case 'Energietraeger::1_Verbrauchsperiode::2_Verbrauchswert':
          case 'Energietraeger::2_Verbrauchsperiode::0_Verbrauchswert':
          case 'Energietraeger::2_Verbrauchsperiode::1_Verbrauchswert':
          case 'Energietraeger::2_Verbrauchsperiode::2_Verbrauchswert':
          case 'Energietraeger::3_Verbrauchsperiode::0_Verbrauchswert':
          case 'Energietraeger::3_Verbrauchsperiode::1_Verbrauchswert':
          case 'Energietraeger::3_Verbrauchsperiode::2_Verbrauchswert':
            $parts = explode( '_', $context );
            $grandparent_index = absint( substr( $parts[0], -1 ) );
            $parent_index = absint( substr( $parts[1], -1 ) );
            $counts = wpenon_get_enev2013_anlagen_counts( $energieausweis );
            if ( isset( $counts[ $grandparent_index ] ) ) {
              $calculations = $energieausweis->calculate();
              $traeger = $calculations['anlagendaten'][ $counts[ $grandparent_index ] ];
              if ( isset( $traeger['verbrauch'][ $parent_index ] ) ) {
                return number_format( $traeger['verbrauch'][ $parent_index ]['heizung'] + $traeger['verbrauch'][ $parent_index ]['warmwasser'], 0 );
              }
            }
            return false;
          case 'Energietraeger::0_Verbrauchsperiode::0_Verbrauchswert-kWh-Heizwert':
          case 'Energietraeger::0_Verbrauchsperiode::1_Verbrauchswert-kWh-Heizwert':
          case 'Energietraeger::0_Verbrauchsperiode::2_Verbrauchswert-kWh-Heizwert':
          case 'Energietraeger::1_Verbrauchsperiode::0_Verbrauchswert-kWh-Heizwert':
          case 'Energietraeger::1_Verbrauchsperiode::1_Verbrauchswert-kWh-Heizwert':
          case 'Energietraeger::1_Verbrauchsperiode::2_Verbrauchswert-kWh-Heizwert':
          case 'Energietraeger::2_Verbrauchsperiode::0_Verbrauchswert-kWh-Heizwert':
          case 'Energietraeger::2_Verbrauchsperiode::1_Verbrauchswert-kWh-Heizwert':
          case 'Energietraeger::2_Verbrauchsperiode::2_Verbrauchswert-kWh-Heizwert':
          case 'Energietraeger::3_Verbrauchsperiode::0_Verbrauchswert-kWh-Heizwert':
          case 'Energietraeger::3_Verbrauchsperiode::1_Verbrauchswert-kWh-Heizwert':
          case 'Energietraeger::3_Verbrauchsperiode::2_Verbrauchswert-kWh-Heizwert':
            $parts = explode( '_', $context );
            $grandparent_index = absint( substr( $parts[0], -1 ) );
            $parent_index = absint( substr( $parts[1], -1 ) );
            $counts = wpenon_get_enev2013_anlagen_counts( $energieausweis );
            if ( isset( $counts[ $grandparent_index ] ) ) {
              $calculations = $energieausweis->calculate();
              $traeger = $calculations['anlagendaten'][ $counts[ $grandparent_index ] ];
              if ( isset( $traeger['verbrauch'][ $parent_index ] ) ) {
                return number_format( $traeger['verbrauch'][ $parent_index ]['heizung'], 0 );
              }
            }
            return false;
          case 'Energietraeger::0_Verbrauchsperiode::0_Warmwasserwert':
          case 'Energietraeger::0_Verbrauchsperiode::1_Warmwasserwert':
          case 'Energietraeger::0_Verbrauchsperiode::2_Warmwasserwert':
          case 'Energietraeger::1_Verbrauchsperiode::0_Warmwasserwert':
          case 'Energietraeger::1_Verbrauchsperiode::1_Warmwasserwert':
          case 'Energietraeger::1_Verbrauchsperiode::2_Warmwasserwert':
          case 'Energietraeger::2_Verbrauchsperiode::0_Warmwasserwert':
          case 'Energietraeger::2_Verbrauchsperiode::1_Warmwasserwert':
          case 'Energietraeger::2_Verbrauchsperiode::2_Warmwasserwert':
          case 'Energietraeger::3_Verbrauchsperiode::0_Warmwasserwert':
          case 'Energietraeger::3_Verbrauchsperiode::1_Warmwasserwert':
          case 'Energietraeger::3_Verbrauchsperiode::2_Warmwasserwert':
            $parts = explode( '_', $context );
            $grandparent_index = absint( substr( $parts[0], -1 ) );
            $parent_index = absint( substr( $parts[1], -1 ) );
            $counts = wpenon_get_enev2013_anlagen_counts( $energieausweis );
            if ( isset( $counts[ $grandparent_index ] ) ) {
              $calculations = $energieausweis->calculate();
              $traeger = $calculations['anlagendaten'][ $counts[ $grandparent_index ] ];
              if ( isset( $traeger['verbrauch'][ $parent_index ] ) ) {
                return number_format( $traeger['verbrauch'][ $parent_index ]['warmwasser'], 0 );
              }
            }
            return false;
          case 'Energietraeger::0_Verbrauchsperiode::0_Warmwasserwertermittlung':
          case 'Energietraeger::0_Verbrauchsperiode::1_Warmwasserwertermittlung':
          case 'Energietraeger::0_Verbrauchsperiode::2_Warmwasserwertermittlung':
          case 'Energietraeger::1_Verbrauchsperiode::0_Warmwasserwertermittlung':
          case 'Energietraeger::1_Verbrauchsperiode::1_Warmwasserwertermittlung':
          case 'Energietraeger::1_Verbrauchsperiode::2_Warmwasserwertermittlung':
          case 'Energietraeger::2_Verbrauchsperiode::0_Warmwasserwertermittlung':
          case 'Energietraeger::2_Verbrauchsperiode::1_Warmwasserwertermittlung':
          case 'Energietraeger::2_Verbrauchsperiode::2_Warmwasserwertermittlung':
          case 'Energietraeger::3_Verbrauchsperiode::0_Warmwasserwertermittlung':
          case 'Energietraeger::3_Verbrauchsperiode::1_Warmwasserwertermittlung':
          case 'Energietraeger::3_Verbrauchsperiode::2_Warmwasserwertermittlung':
            if ( $energieausweis->ww_info == 'ww' ) {
              return $item['options'][0];
            }
            return $item['options'][1];
          case 'Energietraeger::0_Verbrauchsperiode::0_Witterungskorrekturfaktor':
          case 'Energietraeger::0_Verbrauchsperiode::1_Witterungskorrekturfaktor':
          case 'Energietraeger::0_Verbrauchsperiode::2_Witterungskorrekturfaktor':
          case 'Energietraeger::1_Verbrauchsperiode::0_Witterungskorrekturfaktor':
          case 'Energietraeger::1_Verbrauchsperiode::1_Witterungskorrekturfaktor':
          case 'Energietraeger::1_Verbrauchsperiode::2_Witterungskorrekturfaktor':
          case 'Energietraeger::2_Verbrauchsperiode::0_Witterungskorrekturfaktor':
          case 'Energietraeger::2_Verbrauchsperiode::1_Witterungskorrekturfaktor':
          case 'Energietraeger::2_Verbrauchsperiode::2_Witterungskorrekturfaktor':
          case 'Energietraeger::3_Verbrauchsperiode::0_Witterungskorrekturfaktor':
          case 'Energietraeger::3_Verbrauchsperiode::1_Witterungskorrekturfaktor':
          case 'Energietraeger::3_Verbrauchsperiode::2_Witterungskorrekturfaktor':
            $parts = explode( '_', $context );
            $grandparent_index = absint( substr( $parts[0], -1 ) );
            $parent_index = absint( substr( $parts[1], -1 ) );
            $counts = wpenon_get_enev2013_anlagen_counts( $energieausweis );
            if ( isset( $counts[ $grandparent_index ] ) ) {
              $calculations = $energieausweis->calculate();
              $traeger = $calculations['anlagendaten'][ $counts[ $grandparent_index ] ];
              if ( isset( $traeger['verbrauch'][ $parent_index ] ) ) {
                return number_format( $traeger['verbrauch'][ $parent_index ]['klima'], 2, ',', '' );
              }
            }
            return false;
          case 'Energietraeger::0_Verbrauchsperiode::0_Verbrauchswert-kWh-Strom':
          case 'Energietraeger::0_Verbrauchsperiode::1_Verbrauchswert-kWh-Strom':
          case 'Energietraeger::0_Verbrauchsperiode::2_Verbrauchswert-kWh-Strom':
          case 'Energietraeger::1_Verbrauchsperiode::0_Verbrauchswert-kWh-Strom':
          case 'Energietraeger::1_Verbrauchsperiode::1_Verbrauchswert-kWh-Strom':
          case 'Energietraeger::1_Verbrauchsperiode::2_Verbrauchswert-kWh-Strom':
          case 'Energietraeger::2_Verbrauchsperiode::0_Verbrauchswert-kWh-Strom':
          case 'Energietraeger::2_Verbrauchsperiode::1_Verbrauchswert-kWh-Strom':
          case 'Energietraeger::2_Verbrauchsperiode::2_Verbrauchswert-kWh-Strom':
          case 'Energietraeger::3_Verbrauchsperiode::0_Verbrauchswert-kWh-Strom':
          case 'Energietraeger::3_Verbrauchsperiode::1_Verbrauchswert-kWh-Strom':
          case 'Energietraeger::3_Verbrauchsperiode::2_Verbrauchswert-kWh-Strom':
            $parts = explode( '_', $context );
            $grandparent_index = absint( substr( $parts[0], -1 ) );
            $parent_index = absint( substr( $parts[1], -1 ) );
            $counts = wpenon_get_enev2013_anlagen_counts( $energieausweis );
            if ( isset( $counts[ $grandparent_index ] ) ) {
              $calculations = $energieausweis->calculate();
              $traeger = $calculations['anlagendaten'][ $counts[ $grandparent_index ] ];
              if ( isset( $traeger['verbrauch'][ $parent_index ] ) ) {
                return number_format( $traeger['verbrauch'][ $parent_index ]['strom'], 2, ',', '' );
              }
            }
            return false;
          // Leerstandskorrektur-nach-Bekanntmachung
          case 'Leerstandskorrektur::0_Startdatum':
            $calculations = $energieausweis->calculate();
            return $calculations['verbrauchsdaten'][0]['start'];
          case 'Leerstandskorrektur::0_Enddatum':
            $calculations = $energieausweis->calculate();
            return $calculations['verbrauchsdaten'][2]['ende'];
          case 'Leerstandsfaktor':
            $gesamt = ( ( $energieausweis->verbrauch1_leerstand + $energieausweis->verbrauch2_leerstand + $energieausweis->verbrauch3_leerstand ) / 3.0 ) * 0.01;
            return number_format( $gesamt, 2, ',', '' );
          case 'Leerstandszuschlag-kWh-gesamt':
            $calculations = $energieausweis->calculate();
            $gesamt = 0.0;
            foreach ( $calculations['verbrauchsdaten'] as $jahr ) {
              $gesamt += $jahr['gesamt'];
            }
            $gesamt = $gesamt / ( 1.0 - ( ( $energieausweis->verbrauch1_leerstand + $energieausweis->verbrauch2_leerstand + $energieausweis->verbrauch3_leerstand ) / 3.0 ) * 0.01 ) - $gesamt;
            return number_format( $gesamt, 0, ',', '' );
          case 'Leerstandszuschlag-kWh-TWW-Anteil':
            $calculations = $energieausweis->calculate();
            $gesamt = 0.0;
            foreach ( $calculations['verbrauchsdaten'] as $jahr ) {
              $gesamt += $jahr['warmwasser'];
            }
            $gesamt = $gesamt / ( 1.0 - ( ( $energieausweis->verbrauch1_leerstand + $energieausweis->verbrauch2_leerstand + $energieausweis->verbrauch3_leerstand ) / 3.0 ) * 0.01 ) - $gesamt;
            return number_format( $gesamt, 0, ',', '' );
          case 'Leerstandszuschlag-kWh-Heizanteil':
            $calculations = $energieausweis->calculate();
            $gesamt = 0.0;
            foreach ( $calculations['verbrauchsdaten'] as $jahr ) {
              $gesamt += $jahr['heizung'];
            }
            $gesamt = $gesamt / ( 1.0 - ( ( $energieausweis->verbrauch1_leerstand + $energieausweis->verbrauch2_leerstand + $energieausweis->verbrauch3_leerstand ) / 3.0 ) * 0.01 ) - $gesamt;
            return number_format( $gesamt, 0, ',', '' );
          // Bedarfsdaten
          case 'Wohngebaeude-Anbaugrad':
            $anbauart = 3;
            if ( $energieausweis->gebaeudetyp != 'gemischt' ) {
              $anbauart = 2;
              if ( strpos( $energieausweis->gebaeudetyp, 'freistehend' ) !== false ) {
                $anbauart = 0;
              } elseif ( strpos( $energieausweis->gebaeudetyp, 'einseitig' ) !== false ) {
                $anbauart = 1;
              }
            }
            return $item['options'][ $anbauart ];
          case 'Gebaeudevolumen':
            $calculations = $energieausweis->calculate();
            return number_format( $calculations['huellvolumen'], 0, ',', '' );
          case 'durchschnittliche-Geschosshoehe':
            return number_format( $energieausweis->geschoss_hoehe, 2, ',', '' );
          case 'Transmissionswaermeverlust':
            $calculations = $energieausweis->calculate();
            return number_format( $calculations['ht'], 0, ',', '' );
          case 'Luftdichtheit':
            if ( $energieausweis->dichtheit ) {
              return $item['options'][2];
            }
            return $item['options'][1];
          case 'Lueftungswaermeverlust':
            $calculations = $energieausweis->calculate();
            return number_format( $calculations['hv'], 0, ',', '' );
          case 'solare-Gewinne':
            $calculations = $energieausweis->calculate();
            return number_format( $calculations['qs'], 0, ',', '' );
          case 'innere-Gewinne':
            $calculations = $energieausweis->calculate();
            return number_format( $calculations['qi'], 0, ',', '' );
          case 'Pufferspeicher-Volumen':
            return 0;
          case 'Heizverteilung-Temperatur':
            $calculations = $energieausweis->calculate();
            $anteil_max = 0;
            $hktemp = '';
            foreach ( $calculations['anlagendaten'] as $anlage ) {
              if ( $anlage['art'] == 'heizung' ) {
                if ( $anlage['deckungsanteil'] > $anteil_max ) {
                  $anteil_max = $anlage['deckungsanteil'];
                  $hktemp = $anlage['heizkreistemperatur'];
                }
              }
            }
            if ( $hktemp == '70/55°' ) {
              return $item['options'][1];
            }
            return $item['options'][2];
          case 'Heizanlage-innerhalb-Huelle':
            if ( $energieausweis->speicherung_standort == 'innerhalb' ) {
              return 'true';
            }
            return 'false';
          case 'Warmwasserspeicher-Volumen':
            return 0;
          case 'Warmwasser-Zirkulation':
            if ( $energieausweis->verteilung_versorgung == 'mit' ) {
              return 'true';
            }
            return 'false';
          case 'Vereinfachte-Datenaufnahme':
            return 'true';
          case 'spezifischer-Transmissionsverlust-Ist':
            $calculations = $energieausweis->calculate();
            return number_format( $calculations['ht_b'], 2, ',', '' );
          case 'Endenergiekennwert-Waerme-AN':
            $calculations = $energieausweis->calculate();
            return number_format( $calculations['qh_e_b'], 1, ',', '' );
          case 'Endenergiekennwert-Hilfsenergie-AN':
            $calculations = $energieausweis->calculate();
            return number_format( $calculations['qhe_e_b'], 1, ',', '' );
          case 'Primaerenergiekennwert-Bedarf':
            $calculations = $energieausweis->calculate();
            return number_format( $calculations['primaerenergie'], 1, ',', '' );
          // Huellflaechendaten
          case 'Dach-Aussenluft::0_Flaechenbezeichnung':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['dach'] ) ) {
              return $calculations['bauteile']['dach']['name'];
            }
            return false;
          case 'Dach-Aussenluft::0_Flaeche':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['dach'] ) ) {
              return number_format( $calculations['bauteile']['dach']['a'], 0, ',', '' );
            }
            return false;
          case 'Dach-Aussenluft::0_U-Wert':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['dach'] ) ) {
              return number_format( $calculations['bauteile']['dach']['u'], 3, ',', '' );
            }
            return false;
          case 'Geschossdecke::0_Flaechenbezeichnung':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['decke'] ) ) {
              return $calculations['bauteile']['decke']['name'];
            }
            return false;
          case 'Geschossdecke::0_Flaeche':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['decke'] ) ) {
              return number_format( $calculations['bauteile']['decke']['a'], 0, ',', '' );
            }
            return false;
          case 'Geschossdecke::0_U-Wert':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['decke'] ) ) {
              return number_format( $calculations['bauteile']['decke']['u'], 3, ',', '' );
            }
            return false;
          case 'Wand-Aussenluft-Nord::0_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Nord::1_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Nord::2_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Nord::3_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Ost::0_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Ost::1_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Ost::2_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Ost::3_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Sued::0_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Sued::1_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Sued::2_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Sued::3_Flaechenbezeichnung':
          case 'Wand-Aussenluft-West::0_Flaechenbezeichnung':
          case 'Wand-Aussenluft-West::1_Flaechenbezeichnung':
          case 'Wand-Aussenluft-West::2_Flaechenbezeichnung':
          case 'Wand-Aussenluft-West::3_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Nordost::0_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Nordost::1_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Nordost::2_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Nordost::3_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Suedost::0_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Suedost::1_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Suedost::2_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Suedost::3_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Suedwest::0_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Suedwest::1_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Suedwest::2_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Suedwest::3_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Nordwest::0_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Nordwest::1_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Nordwest::2_Flaechenbezeichnung':
          case 'Wand-Aussenluft-Nordwest::3_Flaechenbezeichnung':
            $calculations = $energieausweis->calculate();
            $hr_mappings = wpenon_get_enev2013_himmelsrichtungen_mappings();
            $key = str_replace( array( 'Wand-Aussenluft-', '_Flaechenbezeichnung' ), '', $context );
            $key = explode( '::', $key );
            $i = absint( $key[1] );
            $key = $key[0];
            if ( isset( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ] ) ) {
              if ( isset( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ][ $i ] ) ) {
                return $calculations['bauteile'][ 'wand_' . $calculations['wandrichtungen'][ $hr_mappings[ $key ] ][ $i ] ]['name'];
              }
            }
            return false;
          case 'Wand-Aussenluft-Nord::0_Flaeche':
          case 'Wand-Aussenluft-Nord::1_Flaeche':
          case 'Wand-Aussenluft-Nord::2_Flaeche':
          case 'Wand-Aussenluft-Nord::3_Flaeche':
          case 'Wand-Aussenluft-Ost::0_Flaeche':
          case 'Wand-Aussenluft-Ost::1_Flaeche':
          case 'Wand-Aussenluft-Ost::2_Flaeche':
          case 'Wand-Aussenluft-Ost::3_Flaeche':
          case 'Wand-Aussenluft-Sued::0_Flaeche':
          case 'Wand-Aussenluft-Sued::1_Flaeche':
          case 'Wand-Aussenluft-Sued::2_Flaeche':
          case 'Wand-Aussenluft-Sued::3_Flaeche':
          case 'Wand-Aussenluft-West::0_Flaeche':
          case 'Wand-Aussenluft-West::1_Flaeche':
          case 'Wand-Aussenluft-West::2_Flaeche':
          case 'Wand-Aussenluft-West::3_Flaeche':
          case 'Wand-Aussenluft-Nordost::0_Flaeche':
          case 'Wand-Aussenluft-Nordost::1_Flaeche':
          case 'Wand-Aussenluft-Nordost::2_Flaeche':
          case 'Wand-Aussenluft-Nordost::3_Flaeche':
          case 'Wand-Aussenluft-Suedost::0_Flaeche':
          case 'Wand-Aussenluft-Suedost::1_Flaeche':
          case 'Wand-Aussenluft-Suedost::2_Flaeche':
          case 'Wand-Aussenluft-Suedost::3_Flaeche':
          case 'Wand-Aussenluft-Suedwest::0_Flaeche':
          case 'Wand-Aussenluft-Suedwest::1_Flaeche':
          case 'Wand-Aussenluft-Suedwest::2_Flaeche':
          case 'Wand-Aussenluft-Suedwest::3_Flaeche':
          case 'Wand-Aussenluft-Nordwest::0_Flaeche':
          case 'Wand-Aussenluft-Nordwest::1_Flaeche':
          case 'Wand-Aussenluft-Nordwest::2_Flaeche':
          case 'Wand-Aussenluft-Nordwest::3_Flaeche':
            $calculations = $energieausweis->calculate();
            $hr_mappings = wpenon_get_enev2013_himmelsrichtungen_mappings();
            $key = str_replace( array( 'Wand-Aussenluft-', '_Flaeche' ), '', $context );
            $key = explode( '::', $key );
            $i = absint( $key[1] );
            $key = $key[0];
            if ( isset( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ] ) ) {
              if ( isset( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ][ $i ] ) ) {
                return number_format( $calculations['bauteile'][ 'wand_' . $calculations['wandrichtungen'][ $hr_mappings[ $key ] ][ $i ] ]['a'], 0, ',', '' );
              }
            }
            return false;
          case 'Wand-Aussenluft-Nord::0_U-Wert':
          case 'Wand-Aussenluft-Nord::1_U-Wert':
          case 'Wand-Aussenluft-Nord::2_U-Wert':
          case 'Wand-Aussenluft-Nord::3_U-Wert':
          case 'Wand-Aussenluft-Ost::0_U-Wert':
          case 'Wand-Aussenluft-Ost::1_U-Wert':
          case 'Wand-Aussenluft-Ost::2_U-Wert':
          case 'Wand-Aussenluft-Ost::3_U-Wert':
          case 'Wand-Aussenluft-Sued::0_U-Wert':
          case 'Wand-Aussenluft-Sued::1_U-Wert':
          case 'Wand-Aussenluft-Sued::2_U-Wert':
          case 'Wand-Aussenluft-Sued::3_U-Wert':
          case 'Wand-Aussenluft-West::0_U-Wert':
          case 'Wand-Aussenluft-West::1_U-Wert':
          case 'Wand-Aussenluft-West::2_U-Wert':
          case 'Wand-Aussenluft-West::3_U-Wert':
          case 'Wand-Aussenluft-Nordost::0_U-Wert':
          case 'Wand-Aussenluft-Nordost::1_U-Wert':
          case 'Wand-Aussenluft-Nordost::2_U-Wert':
          case 'Wand-Aussenluft-Nordost::3_U-Wert':
          case 'Wand-Aussenluft-Suedost::0_U-Wert':
          case 'Wand-Aussenluft-Suedost::1_U-Wert':
          case 'Wand-Aussenluft-Suedost::2_U-Wert':
          case 'Wand-Aussenluft-Suedost::3_U-Wert':
          case 'Wand-Aussenluft-Suedwest::0_U-Wert':
          case 'Wand-Aussenluft-Suedwest::1_U-Wert':
          case 'Wand-Aussenluft-Suedwest::2_U-Wert':
          case 'Wand-Aussenluft-Suedwest::3_U-Wert':
          case 'Wand-Aussenluft-Nordwest::0_U-Wert':
          case 'Wand-Aussenluft-Nordwest::1_U-Wert':
          case 'Wand-Aussenluft-Nordwest::2_U-Wert':
          case 'Wand-Aussenluft-Nordwest::3_U-Wert':
            $calculations = $energieausweis->calculate();
            $hr_mappings = wpenon_get_enev2013_himmelsrichtungen_mappings();
            $key = str_replace( array( 'Wand-Aussenluft-', '_U-Wert' ), '', $context );
            $key = explode( '::', $key );
            $i = absint( $key[1] );
            $key = $key[0];
            if ( isset( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ] ) ) {
              if ( isset( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ][ $i ] ) ) {
                return number_format( $calculations['bauteile'][ 'wand_' . $calculations['wandrichtungen'][ $hr_mappings[ $key ] ][ $i ] ]['u'], 3, ',', '' );
              }
            }
            return false;
          case 'Wand-Erdreich::0_Flaechenbezeichnung':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['kellerwand'] ) ) {
              return $calculations['bauteile']['kellerwand']['name'];
            }
            return false;
          case 'Wand-Erdreich::0_Flaeche':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['kellerwand'] ) ) {
              return number_format( $calculations['bauteile']['kellerwand']['a'], 0, ',', '' );
            }
            return false;
          case 'Wand-Erdreich::0_U-Wert':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['kellerwand'] ) ) {
              return number_format( $calculations['bauteile']['kellerwand']['u'], 3, ',', '' );
            }
            return false;
          case 'Boden-andere-Temp::0_Flaechenbezeichnung':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['kellerdecke'] ) ) {
              return $calculations['bauteile']['kellerdecke']['name'];
            }
            return false;
          case 'Boden-andere-Temp::0_Flaeche':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['kellerdecke'] ) ) {
              return number_format( $calculations['bauteile']['kellerdecke']['a'], 0, ',', '' );
            }
            return false;
          case 'Boden-andere-Temp::0_U-Wert':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['kellerdecke'] ) ) {
              return number_format( $calculations['bauteile']['kellerdecke']['u'], 3, ',', '' );
            }
            return false;
          case 'Boden-Erdreich::0_Flaechenbezeichnung':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['boden'] ) ) {
              return $calculations['bauteile']['boden']['name'];
            }
            return false;
          case 'Boden-Erdreich::0_Flaeche':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['boden'] ) ) {
              return number_format( $calculations['bauteile']['boden']['a'], 0, ',', '' );
            }
            return false;
          case 'Boden-Erdreich::0_U-Wert':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['boden'] ) ) {
              return number_format( $calculations['bauteile']['boden']['u'], 3, ',', '' );
            }
            return false;
          case 'Fenster-Dach::0_Flaechenbezeichnung':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['fenster_dach'] ) ) {
              return $calculations['bauteile']['fenster_dach']['name'];
            }
            return false;
          case 'Fenster-Dach::0_Flaeche':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['fenster_dach'] ) ) {
              return number_format( $calculations['bauteile']['fenster_dach']['a'], 0, ',', '' );
            }
            return false;
          case 'Fenster-Dach::0_U-Wert':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['fenster_dach'] ) ) {
              return number_format( $calculations['bauteile']['fenster_dach']['u'], 3, ',', '' );
            }
            return false;
          case 'Fenster-Dach::0_g-Wert':
            $calculations = $energieausweis->calculate();
            if ( isset( $calculations['bauteile']['fenster_dach'] ) ) {
              $gwert = $calculations['bauteile']['fenster_dach']['bauart'] == 'holzeinfach' ? 0.87 : 0.6;
              return number_format( $gwert, 2, ',', '' );
            }
            return false;
          case 'Fenster-Nord::0_Flaechenbezeichnung':
          case 'Fenster-Nord::1_Flaechenbezeichnung':
          case 'Fenster-Nord::2_Flaechenbezeichnung':
          case 'Fenster-Nord::3_Flaechenbezeichnung':
          case 'Fenster-Ost::0_Flaechenbezeichnung':
          case 'Fenster-Ost::1_Flaechenbezeichnung':
          case 'Fenster-Ost::2_Flaechenbezeichnung':
          case 'Fenster-Ost::3_Flaechenbezeichnung':
          case 'Fenster-Sued::0_Flaechenbezeichnung':
          case 'Fenster-Sued::1_Flaechenbezeichnung':
          case 'Fenster-Sued::2_Flaechenbezeichnung':
          case 'Fenster-Sued::3_Flaechenbezeichnung':
          case 'Fenster-West::0_Flaechenbezeichnung':
          case 'Fenster-West::1_Flaechenbezeichnung':
          case 'Fenster-West::2_Flaechenbezeichnung':
          case 'Fenster-West::3_Flaechenbezeichnung':
          case 'Fenster-Nordost::0_Flaechenbezeichnung':
          case 'Fenster-Nordost::1_Flaechenbezeichnung':
          case 'Fenster-Nordost::2_Flaechenbezeichnung':
          case 'Fenster-Nordost::3_Flaechenbezeichnung':
          case 'Fenster-Suedost::0_Flaechenbezeichnung':
          case 'Fenster-Suedost::1_Flaechenbezeichnung':
          case 'Fenster-Suedost::2_Flaechenbezeichnung':
          case 'Fenster-Suedost::3_Flaechenbezeichnung':
          case 'Fenster-Suedwest::0_Flaechenbezeichnung':
          case 'Fenster-Suedwest::1_Flaechenbezeichnung':
          case 'Fenster-Suedwest::2_Flaechenbezeichnung':
          case 'Fenster-Suedwest::3_Flaechenbezeichnung':
          case 'Fenster-Nordwest::0_Flaechenbezeichnung':
          case 'Fenster-Nordwest::1_Flaechenbezeichnung':
          case 'Fenster-Nordwest::2_Flaechenbezeichnung':
          case 'Fenster-Nordwest::3_Flaechenbezeichnung':
            $calculations = $energieausweis->calculate();
            $hr_mappings = wpenon_get_enev2013_himmelsrichtungen_mappings();
            $key = str_replace( array( 'Fenster-', '_Flaechenbezeichnung' ), '', $context );
            $key = explode( '::', $key );
            $i = absint( $key[1] );
            $key = $key[0];
            if ( isset( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ] ) ) {
              $windows = array();
              foreach ( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ] as $wand ) {
                if ( isset( $calculations['bauteile'][ 'fenster_' . $wand ] ) ) {
                  $windows[] = $wand;
                }
              }
              if ( isset( $windows[ $i ] ) ) {
                return $calculations['bauteile'][ 'fenster_' . $windows[ $i ] ]['name'];
              }
            }
            return false;
          case 'Fenster-Nord::0_Flaeche':
          case 'Fenster-Nord::1_Flaeche':
          case 'Fenster-Nord::2_Flaeche':
          case 'Fenster-Nord::3_Flaeche':
          case 'Fenster-Ost::0_Flaeche':
          case 'Fenster-Ost::1_Flaeche':
          case 'Fenster-Ost::2_Flaeche':
          case 'Fenster-Ost::3_Flaeche':
          case 'Fenster-Sued::0_Flaeche':
          case 'Fenster-Sued::1_Flaeche':
          case 'Fenster-Sued::2_Flaeche':
          case 'Fenster-Sued::3_Flaeche':
          case 'Fenster-West::0_Flaeche':
          case 'Fenster-West::1_Flaeche':
          case 'Fenster-West::2_Flaeche':
          case 'Fenster-West::3_Flaeche':
          case 'Fenster-Nordost::0_Flaeche':
          case 'Fenster-Nordost::1_Flaeche':
          case 'Fenster-Nordost::2_Flaeche':
          case 'Fenster-Nordost::3_Flaeche':
          case 'Fenster-Suedost::0_Flaeche':
          case 'Fenster-Suedost::1_Flaeche':
          case 'Fenster-Suedost::2_Flaeche':
          case 'Fenster-Suedost::3_Flaeche':
          case 'Fenster-Suedwest::0_Flaeche':
          case 'Fenster-Suedwest::1_Flaeche':
          case 'Fenster-Suedwest::2_Flaeche':
          case 'Fenster-Suedwest::3_Flaeche':
          case 'Fenster-Nordwest::0_Flaeche':
          case 'Fenster-Nordwest::1_Flaeche':
          case 'Fenster-Nordwest::2_Flaeche':
          case 'Fenster-Nordwest::3_Flaeche':
            $calculations = $energieausweis->calculate();
            $hr_mappings = wpenon_get_enev2013_himmelsrichtungen_mappings();
            $key = str_replace( array( 'Fenster-', '_Flaeche' ), '', $context );
            $key = explode( '::', $key );
            $i = absint( $key[1] );
            $key = $key[0];
            if ( isset( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ] ) ) {
              $windows = array();
              foreach ( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ] as $wand ) {
                if ( isset( $calculations['bauteile'][ 'fenster_' . $wand ] ) ) {
                  $windows[] = $wand;
                }
              }
              if ( isset( $windows[ $i ] ) ) {
                return number_format( $calculations['bauteile'][ 'fenster_' . $windows[ $i ] ]['a'], 0, ',', '' );
              }
            }
            return false;
          case 'Fenster-Nord::0_U-Wert':
          case 'Fenster-Nord::1_U-Wert':
          case 'Fenster-Nord::2_U-Wert':
          case 'Fenster-Nord::3_U-Wert':
          case 'Fenster-Ost::0_U-Wert':
          case 'Fenster-Ost::1_U-Wert':
          case 'Fenster-Ost::2_U-Wert':
          case 'Fenster-Ost::3_U-Wert':
          case 'Fenster-Sued::0_U-Wert':
          case 'Fenster-Sued::1_U-Wert':
          case 'Fenster-Sued::2_U-Wert':
          case 'Fenster-Sued::3_U-Wert':
          case 'Fenster-West::0_U-Wert':
          case 'Fenster-West::1_U-Wert':
          case 'Fenster-West::2_U-Wert':
          case 'Fenster-West::3_U-Wert':
          case 'Fenster-Nordost::0_U-Wert':
          case 'Fenster-Nordost::1_U-Wert':
          case 'Fenster-Nordost::2_U-Wert':
          case 'Fenster-Nordost::3_U-Wert':
          case 'Fenster-Suedost::0_U-Wert':
          case 'Fenster-Suedost::1_U-Wert':
          case 'Fenster-Suedost::2_U-Wert':
          case 'Fenster-Suedost::3_U-Wert':
          case 'Fenster-Suedwest::0_U-Wert':
          case 'Fenster-Suedwest::1_U-Wert':
          case 'Fenster-Suedwest::2_U-Wert':
          case 'Fenster-Suedwest::3_U-Wert':
          case 'Fenster-Nordwest::0_U-Wert':
          case 'Fenster-Nordwest::1_U-Wert':
          case 'Fenster-Nordwest::2_U-Wert':
          case 'Fenster-Nordwest::3_U-Wert':
            $calculations = $energieausweis->calculate();
            $hr_mappings = wpenon_get_enev2013_himmelsrichtungen_mappings();
            $key = str_replace( array( 'Fenster-', '_U-Wert' ), '', $context );
            $key = explode( '::', $key );
            $i = absint( $key[1] );
            $key = $key[0];
            if ( isset( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ] ) ) {
              $windows = array();
              foreach ( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ] as $wand ) {
                if ( isset( $calculations['bauteile'][ 'fenster_' . $wand ] ) ) {
                  $windows[] = $wand;
                }
              }
              if ( isset( $windows[ $i ] ) ) {
                return number_format( $calculations['bauteile'][ 'fenster_' . $windows[ $i ] ]['u'], 3, ',', '' );
              }
            }
            return false;
          case 'Fenster-Nord::0_g-Wert':
          case 'Fenster-Nord::1_g-Wert':
          case 'Fenster-Nord::2_g-Wert':
          case 'Fenster-Nord::3_g-Wert':
          case 'Fenster-Ost::0_g-Wert':
          case 'Fenster-Ost::1_g-Wert':
          case 'Fenster-Ost::2_g-Wert':
          case 'Fenster-Ost::3_g-Wert':
          case 'Fenster-Sued::0_g-Wert':
          case 'Fenster-Sued::1_g-Wert':
          case 'Fenster-Sued::2_g-Wert':
          case 'Fenster-Sued::3_g-Wert':
          case 'Fenster-West::0_g-Wert':
          case 'Fenster-West::1_g-Wert':
          case 'Fenster-West::2_g-Wert':
          case 'Fenster-West::3_g-Wert':
          case 'Fenster-Nordost::0_g-Wert':
          case 'Fenster-Nordost::1_g-Wert':
          case 'Fenster-Nordost::2_g-Wert':
          case 'Fenster-Nordost::3_g-Wert':
          case 'Fenster-Suedost::0_g-Wert':
          case 'Fenster-Suedost::1_g-Wert':
          case 'Fenster-Suedost::2_g-Wert':
          case 'Fenster-Suedost::3_g-Wert':
          case 'Fenster-Suedwest::0_g-Wert':
          case 'Fenster-Suedwest::1_g-Wert':
          case 'Fenster-Suedwest::2_g-Wert':
          case 'Fenster-Suedwest::3_g-Wert':
          case 'Fenster-Nordwest::0_g-Wert':
          case 'Fenster-Nordwest::1_g-Wert':
          case 'Fenster-Nordwest::2_g-Wert':
          case 'Fenster-Nordwest::3_g-Wert':
            $calculations = $energieausweis->calculate();
            $hr_mappings = wpenon_get_enev2013_himmelsrichtungen_mappings();
            $key = str_replace( array( 'Fenster-', '_g-Wert' ), '', $context );
            $key = explode( '::', $key );
            $i = absint( $key[1] );
            $key = $key[0];
            if ( isset( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ] ) ) {
              $windows = array();
              foreach ( $calculations['wandrichtungen'][ $hr_mappings[ $key ] ] as $wand ) {
                if ( isset( $calculations['bauteile'][ 'fenster_' . $wand ] ) ) {
                  $windows[] = $wand;
                }
              }
              if ( isset( $windows[ $i ] ) ) {
                $gwert = $calculations['bauteile'][ 'fenster_' . $windows[ $i ] ]['bauart'] == 'holzeinfach' ? 0.87 : 0.6;
                return number_format( $gwert, 2, ',', '' );
              }
            }
            return false;
          case 'Waermebrueckenzuschlag':
            return number_format( 0.1, 2, ',', '' );
          // Heizanlage
          case 'Heizanlage::0_Waermeerzeuger-Bauweise':
          case 'Heizanlage::1_Waermeerzeuger-Bauweise':
          case 'Heizanlage::2_Waermeerzeuger-Bauweise':
            $calculations = $energieausweis->calculate();
            $key = absint( str_replace( array( 'Heizanlage::', '_Waermeerzeuger-Bauweise' ), '', $context ) );
            $count = 0;
            foreach ( $calculations['anlagendaten'] as $anlage ) {
              if ( $anlage['art'] == 'heizung' ) {
                if ( $key == $count ) {
                  $mappings = wpenon_get_enev2013_waermeerzeuger_mappings();
                  if ( isset( $mappings[ $anlage['slug'] ] ) && $mappings[ $anlage['slug'] ] > -1 ) {
                    return $item['options'][ $mappings[ $anlage['slug'] ] ];
                  }
                  return $item['options'][20];
                }
                $count++;
              }
            }
            return false;
          case 'Heizanlage::0_Nennleistung':
          case 'Heizanlage::1_Nennleistung':
          case 'Heizanlage::2_Nennleistung':
            $calculations = $energieausweis->calculate();
            $key = absint( str_replace( array( 'Heizanlage::', '_Nennleistung' ), '', $context ) );
            $count = 0;
            foreach ( $calculations['anlagendaten'] as $anlage ) {
              if ( $anlage['art'] == 'heizung' ) {
                if ( $key == $count ) {
                  return 0;
                }
                $count++;
              }
            }
            return false;
          case 'Heizanlage::0_Waermeerzeuger-Baujahr':
          case 'Heizanlage::1_Waermeerzeuger-Baujahr':
          case 'Heizanlage::2_Waermeerzeuger-Baujahr':
            $calculations = $energieausweis->calculate();
            $key = absint( str_replace( array( 'Heizanlage::', '_Waermeerzeuger-Baujahr' ), '', $context ) );
            $count = 0;
            foreach ( $calculations['anlagendaten'] as $anlage ) {
              if ( $anlage['art'] == 'heizung' ) {
                if ( $key == $count ) {
                  return $anlage['baujahr'];
                }
                $count++;
              }
            }
            return false;
          case 'Heizanlage::0_Anzahl-baugleiche':
          case 'Heizanlage::1_Anzahl-baugleiche':
          case 'Heizanlage::2_Anzahl-baugleiche':
            $calculations = $energieausweis->calculate();
            $key = absint( str_replace( array( 'Heizanlage::', '_Anzahl-baugleiche' ), '', $context ) );
            $count = 0;
            foreach ( $calculations['anlagendaten'] as $anlage ) {
              if ( $anlage['art'] == 'heizung' ) {
                if ( $key == $count ) {
                  return 1;
                }
                $count++;
              }
            }
            return false;
          case 'Heizanlage::0_Energietraeger':
          case 'Heizanlage::1_Energietraeger':
          case 'Heizanlage::2_Energietraeger':
            $calculations = $energieausweis->calculate();
            $key = absint( str_replace( array( 'Heizanlage::', '_Energietraeger' ), '', $context ) );
            $count = 0;
            foreach ( $calculations['anlagendaten'] as $anlage ) {
              if ( $anlage['art'] == 'heizung' ) {
                if ( $key == $count ) {
                  $mappings = wpenon_get_enev2013_energietraeger_mappings();
                  if ( isset( $mappings[ $anlage['energietraeger_slug'] ] ) && $mappings[ $anlage['energietraeger_slug'] ] > -1 ) {
                    return $item['options'][ $mappings[ $anlage['energietraeger_slug'] ] ];
                  }
                  return $item['options'][17];
                }
                $count++;
              }
            }
            return false;
          case 'Heizanlage::0_Primaerenergiefaktor':
          case 'Heizanlage::1_Primaerenergiefaktor':
          case 'Heizanlage::2_Primaerenergiefaktor':
            $calculations = $energieausweis->calculate();
            $key = absint( str_replace( array( 'Heizanlage::', '_Primaerenergiefaktor' ), '', $context ) );
            $count = 0;
            foreach ( $calculations['anlagendaten'] as $anlage ) {
              if ( $anlage['art'] == 'heizung' ) {
                if ( $key == $count ) {
                  return number_format( $anlage['energietraeger_primaer'], 1, ',', '' );
                }
                $count++;
              }
            }
            return false;
          // Warmwasseranlage
          case 'Warmwasseranlage::0_Warmwassererzeuger-Bauweise':
          case 'Warmwasseranlage::1_Warmwassererzeuger-Bauweise':
          case 'Warmwasseranlage::2_Warmwassererzeuger-Bauweise':
            $calculations = $energieausweis->calculate();
            $key = absint( str_replace( array( 'Warmwasseranlage::', '_Warmwassererzeuger-Bauweise' ), '', $context ) );
            $count = 0;
            foreach ( $calculations['anlagendaten'] as $anlage ) {
              if ( $anlage['art'] == 'warmwasser' ) {
                if ( $key == $count ) {
                  $mappings = wpenon_get_enev2013_warmwassererzeuger_mappings();
                  if ( isset( $mappings[ $anlage['slug'] ] ) && $mappings[ $anlage['slug'] ] > -1 ) {
                    return $item['options'][ $mappings[ $anlage['slug'] ] ];
                  }
                  if ( $energieausweis->ww_info != 'ww' ) {
                    return $item['options'][0];
                  }
                  return $item['options'][9];
                }
                $count++;
              }
            }
            return false;
          case 'Warmwasseranlage::0_Warmwassererzeuger-Baujahr':
          case 'Warmwasseranlage::1_Warmwassererzeuger-Baujahr':
          case 'Warmwasseranlage::2_Warmwassererzeuger-Baujahr':
            $calculations = $energieausweis->calculate();
            $key = absint( str_replace( array( 'Warmwasseranlage::', '_Warmwassererzeuger-Baujahr' ), '', $context ) );
            $count = 0;
            foreach ( $calculations['anlagendaten'] as $anlage ) {
              if ( $anlage['art'] == 'warmwasser' ) {
                if ( $key == $count ) {
                  return $anlage['baujahr'];
                }
                $count++;
              }
            }
            return false;
          case 'Warmwasseranlage::0_Anzahl-baugleiche':
          case 'Warmwasseranlage::1_Anzahl-baugleiche':
          case 'Warmwasseranlage::2_Anzahl-baugleiche':
            $calculations = $energieausweis->calculate();
            $key = absint( str_replace( array( 'Warmwasseranlage::', '_Anzahl-baugleiche' ), '', $context ) );
            $count = 0;
            foreach ( $calculations['anlagendaten'] as $anlage ) {
              if ( $anlage['art'] == 'warmwasser' ) {
                if ( $key == $count ) {
                  return 1;
                }
                $count++;
              }
            }
            return false;
          default:
        }
        break;
      default:
    }
  }

  return false;
}

function wpenon_get_enev2013_anlagen_counts( $energieausweis ) {
  $counts = array( 'h' );
  if ( $energieausweis->h2_info ) {
    $counts[] = 'h2';
    if ( $energieausweis->h3_info ) {
      $counts[] = 'h3';
    }
  }
  if ( $energieausweis->ww_info == 'ww' ) {
    $counts[] = 'ww';
  }

  return $counts;
}

function wpenon_get_enev2013_himmelsrichtungen_mappings() {
  return array(
    'Nord'      => 'n',
    'Ost'       => 'o',
    'Sued'      => 's',
    'West'      => 'w',
    'Nordost'   => 'no',
    'Suedost'   => 'so',
    'Suedwest'  => 'sw',
    'Nordwest'  => 'nw',
  );
}

function wpenon_get_enev2013_waermeerzeuger_mappings() {
  return array(
    'standardkessel'              => 0,
    'niedertemperaturkessel'      => 1,
    'brennwertkessel'             => 2,
    'brennwertkesselverbessert'   => 3,
    'fernwaerme'                  => 6,
    'waermepumpeluft'             => 8,
    'waermepumpewasser'           => 10,
    'waermepumpeerde'             => 11,
    'kleinthermeniedertemperatur' => 12,
    'kleinthermebrennwert'        => 13,
    'oelofen'                     => 14,
    'gasraumheizer'               => 15,
    'kohleholzofen'               => 16,
    'nachtspeicher'               => 17,
    'direktheizgeraet'            => 18,
    'solaranlage'                 => 19,
    'elektrospeicher'             => -1,
  );
}

function wpenon_get_enev2013_warmwassererzeuger_mappings() {
  return array(
    'standardkessel'              => 0,
    'niedertemperaturkessel'      => 0,
    'brennwertkessel'             => 0,
    'brennwertkesselverbessert'   => 0,
    'fernwaerme'                  => 0,
    'waermepumpeluft'             => 0,
    'waermepumpewasser'           => 0,
    'waermepumpeerde'             => 0,
    'kleinthermeniedertemperatur' => 4,
    'kleinthermebrennwert'        => 4,
    'dezentralkleinspeicher'      => 5,
    'dezentralelektroerhitzer'    => 6,
    'dezentralgaserhitzer'        => 7,
    'elektrospeicher'             => 1,
    'gasspeicher'                 => 2,
    'solaranlage'                 => 8,
  );
}

function wpenon_get_enev2013_energietraeger_mappings() {
  return array(
    'heizoel'                   => 0,
    'heizoelbiooel'             => 1,
    'biooel'                    => 2,
    'erdgas'                    => 3,
    'erdgasbiogas'              => 4,
    'biogas'                    => 5,
    'fluessiggas'               => 6,
    'steinkohle'                => 7,
    'koks'                      => 8,
    'braunkohle'                => 9,
    'stueckholz'                => 10,
    'holzhackschnitzel'         => 11,
    'holzpellets'               => 12,
    'strom'                     => 15,
    'fernwaermehzwfossil'       => -1,
    'fernwaermehzwregenerativ'  => -1,
    'fernwaermekwkfossil'       => -1,
    'fernwaermekwkregenerativ'  => -1,
    'sonneneinstrahlung'        => 16,
  );
}

function wpenon_get_enev2013_energietraeger_unit_mappings() {
  return array(
    'heizoel_l'                   => 0,
    'heizoel_kwh'                 => array( 1, 2 ),
    'heizoelbiooel_l'             => 3,
    'heizoelbiooel_kwh'           => array( 4, 5 ),
    'biooel_l'                    => 6,
    'biooel_kwh'                  => array( 7, 8 ),
    'erdgas_m3'                   => 9,
    'erdgas_kwh'                  => array( 10, 11 ),
    'erdgasbiogas_m3'             => 12,
    'erdgasbiogas_kwh'            => array( 13, 14 ),
    'biogas_m3'                   => 15,
    'biogas_kwh'                  => array( 16, 17 ),
    'fluessiggas_l'               => 19,
    'fluessiggas_m3'              => 18,
    'fluessiggas_kg'              => 20,
    'fluessiggas_kwh'             => array( 21, 22 ),
    'steinkohle_kg'               => 23,
    'steinkohle_kwh'              => 24,
    'braunkohle_kg'               => 27,
    'braunkohle_kwh'              => 28,
    'stueckholz_m3'               => 29,
    'stueckholz_kg'               => 30,
    'stueckholz_kwh'              => array( 31, 32 ),
    'holzpellets_kg'              => 38,
    'holzpellets_kwh'             => array( 39, 40 ),
    'strom_kwh'                   => 43,
    'fernwaermehzwfossil_kwh'     => -1,
    'fernwaermehzwregenerativ_kwh'=> -1,
    'fernwaermekwkfossil_kwh'     => -1,
    'fernwaermekwkregenerativ_kwh'=> -1,
  );
}
