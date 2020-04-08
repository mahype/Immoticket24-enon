<?php
if ( ! function_exists( 'wpenon_get_enev_xml_zusatzdatenerfassung_data' ) ) {
	function wpenon_get_enev_xml_zusatzdatenerfassung_data( $context, $index = 0, $energieausweis = null, $data = array() ) {
		if ( isset( $data['mode'] ) ) {
			switch ( $data['mode'] ) {
				case 'occurrences':
					$min = $data['min'];
					$max = $data['max'];
					switch ( $context ) {
						case 'Modernisierungsempfehlungen':
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
									$count ++;
									if ( $energieausweis->h3_info ) {
										$count ++;
									}
								}
								if ( $energieausweis->ww_info == 'ww' ) {
									$count ++;
								}
							}

							return $count;
						case 'Zeitraum':
							return 3;
						case 'Verbrauchswert-kWh-Strom':
							if ( $energieausweis->building == 'n' ) {
								return 1;
							}

							return 0;
						case 'Warmwasserzuschlag':
							$calculations = $energieausweis->calculate();
							if ( ! empty( $calculations['warmwasser_zuschlag'] ) ) {
								return 1;
							}

							return 0;
						case 'Kuehlzuschlag':
							$calculations = $energieausweis->calculate();
							if ( ! empty( $calculations['kuehlung_zuschlag'] ) ) {
								return 1;
							}

							return 0;
						case 'Bauteil-Opak':
							$calculations  = $energieausweis->calculate();
							$bauteile_opak = wp_list_filter( $calculations['bauteile'], array( 'modus' => 'opak' ) );

							return count( $bauteile_opak );
						case 'Bauteil-Transparent':
							$calculations         = $energieausweis->calculate();
							$bauteile_transparent = wp_list_filter( $calculations['bauteile'], array( 'modus' => 'transparent' ) );

							return count( $bauteile_transparent );
						case 'Bauteil-Dach':
							$calculations  = $energieausweis->calculate();
							$bauteile_dach = wp_list_filter( $calculations['bauteile'], array( 'modus' => 'dach' ) );

							return count( $bauteile_dach );
						case 'Heizungsanlage':
							$calculations = $energieausweis->calculate();
							$count        = 0;
							foreach ( $calculations['anlagendaten'] as $anlage ) {
								if ( $anlage['art'] == 'heizung' ) {
									$count ++;
								}
							}

							return $count;
						case 'Trinkwarmwasseranlage':
							$calculations = $energieausweis->calculate();
							$count        = 0;
							foreach ( $calculations['anlagendaten'] as $anlage ) {
								if ( $anlage['art'] == 'warmwasser' ) {
									$count ++;
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
							return ( new \Enon\Enon\Standards\Schema( $energieausweis->wpenon_standard ) )->get_date( 'Y' );
						case 'Rechtsstand':
							return \WPENON\Model\EnergieausweisManager::instance()->getReferenceDate( 'Y-m-d', $energieausweis );
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
						case 'Heizungsanlage':
						case 'Trinkwarmwasseranlage':
							return $choices[1];
						case 'Energietraeger':
							if ( in_array( 'Energietraegerbezeichnung', $choices ) ) {
								$counts = wpenon_get_enev_anlagen_counts( $energieausweis );
								if ( isset( $counts[ $index ] ) ) {
									$traeger_key = $counts[ $index ] . '_energietraeger';

									// Migrate old data to new Energietraeger table data.
									if ( false !== strpos( $energieausweis->$traeger_key, '_kwhheizwert' ) ) {
										$energieausweis->$traeger_key = str_replace( '_kwhheizwert', '_kwh', $energieausweis->$traeger_key );
									} elseif ( false !== strpos( $energieausweis->$traeger_key, '_kwhbrennwert' ) ) {
										$energieausweis->$traeger_key = str_replace( '_kwhbrennwert', '_kwh', $energieausweis->$traeger_key );
									}

									$traeger = $energieausweis->$traeger_key;

									$mappings = wpenon_get_enev_energietraeger_unit_mappings();
									if ( ! isset( $mappings[ $traeger ] ) || is_int( $mappings[ $traeger ] ) && $mappings[ $traeger ] < 0 ) {
										return $choices[1];
									}

									return $choices[0];
								}
							}

							return false;
						case 'Leerstandszuschlag-Heizung':
						case 'Leerstandszuschlag-Warmwasser':
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
							return \WPENON\Model\EnergieausweisManager::instance()->getReferenceDate( 'Y-m-d', $energieausweis );
						case 'Bundesland':
							if ( in_array( $energieausweis->adresse_bundesland, $item['options'] ) ) {
								return $energieausweis->adresse_bundesland;
							}
						case 'Postleitzahl':
							return substr( $energieausweis->adresse_plz, 0, 3 ) . 'XX';
						case 'Gebaeudeteil':
							$gemischt = $energieausweis->gebaeudeteil == 'gemischt';
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
							$calculations   = $energieausweis->calculate();
							$energietraeger = array();
							foreach ( $calculations['anlagendaten'] as $key => $data ) {
								$energietraeger[] = $data['energietraeger'];
							}

							return implode( '; ', array_unique( $energietraeger ) );
						case 'Erneuerbare-Art':
							$art =  wpenon_immoticket24_get_regenerativ_art_name( $energieausweis->regenerativ_art );
							return $art;
						case 'Erneuerbare-Verwendung':
							if( 'keine' !== $energieausweis->regenerativ_art ) {
								return wpenon_immoticket24_get_regenerativ_nutzung_name( $energieausweis->regenerativ_nutzung );
							}
							return 'Keine';
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
							$anlass   = 'vermietung' === $energieausweis->anlass ? 'verkauf' : $energieausweis->anlass;
							$mappings = array( 'neubau', 'modernisierung', 'verkauf', 'aushang', 'sonstiges' );
							$key      = array_search( $anlass, $mappings );
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
						case 'Modernisierungsempfehlungen::0_Nummer':
						case 'Modernisierungsempfehlungen::1_Nummer':
						case 'Modernisierungsempfehlungen::2_Nummer':
						case 'Modernisierungsempfehlungen::3_Nummer':
						case 'Modernisierungsempfehlungen::4_Nummer':
						case 'Modernisierungsempfehlungen::5_Nummer':
						case 'Modernisierungsempfehlungen::6_Nummer':
						case 'Modernisierungsempfehlungen::7_Nummer':
						case 'Modernisierungsempfehlungen::8_Nummer':
						case 'Modernisierungsempfehlungen::9_Nummer':
							$parent_index = absint( str_replace( array(
								'Modernisierungsempfehlungen::',
								'_Nummer'
							), '', $context ) );

							return $parent_index + 1;
						case 'Modernisierungsempfehlungen::0_Bauteil-Anlagenteil':
						case 'Modernisierungsempfehlungen::1_Bauteil-Anlagenteil':
						case 'Modernisierungsempfehlungen::2_Bauteil-Anlagenteil':
						case 'Modernisierungsempfehlungen::3_Bauteil-Anlagenteil':
						case 'Modernisierungsempfehlungen::4_Bauteil-Anlagenteil':
						case 'Modernisierungsempfehlungen::5_Bauteil-Anlagenteil':
						case 'Modernisierungsempfehlungen::6_Bauteil-Anlagenteil':
						case 'Modernisierungsempfehlungen::7_Bauteil-Anlagenteil':
						case 'Modernisierungsempfehlungen::8_Bauteil-Anlagenteil':
						case 'Modernisierungsempfehlungen::9_Bauteil-Anlagenteil':
							$parent_index                = absint( str_replace( array(
								'Modernisierungsempfehlungen::',
								'_Bauteil-Anlagenteil'
							), '', $context ) );
							$modernisierungsempfehlungen = wpenon_immoticket24_get_modernisierungsempfehlungen( $energieausweis );
							$mappings                    = array(
								'Dach'                      => 0,
								'Oberste Geschossdecke'     => 1,
								'Außenwände'                => 5,
								'Kellerdecke / Bodenplatte' => 13,
								'Fenster'                   => 6,
								'Solarthermie'              => 30,
							);

							if ( array_key_exists( $parent_index, $modernisierungsempfehlungen ) ) {
								$modernisierungsempfehlung = $modernisierungsempfehlungen[ $parent_index ];
								$bauteil =  $modernisierungsempfehlung['bauteil' ];

								if ( array_key_exists( $bauteil, $mappings ) ) {
									return $item['options'][ $mappings[ $bauteil ] ];
								}
							}

							return false;
						case 'Modernisierungsempfehlungen::0_Massnahmenbeschreibung':
						case 'Modernisierungsempfehlungen::1_Massnahmenbeschreibung':
						case 'Modernisierungsempfehlungen::2_Massnahmenbeschreibung':
						case 'Modernisierungsempfehlungen::3_Massnahmenbeschreibung':
						case 'Modernisierungsempfehlungen::4_Massnahmenbeschreibung':
						case 'Modernisierungsempfehlungen::5_Massnahmenbeschreibung':
						case 'Modernisierungsempfehlungen::6_Massnahmenbeschreibung':
						case 'Modernisierungsempfehlungen::7_Massnahmenbeschreibung':
						case 'Modernisierungsempfehlungen::8_Massnahmenbeschreibung':
						case 'Modernisierungsempfehlungen::9_Massnahmenbeschreibung':
							$parent_index                = absint( str_replace( array(
								'Modernisierungsempfehlungen::',
								'_Massnahmenbeschreibung'
							), '', $context ) );
							$modernisierungsempfehlungen = wpenon_immoticket24_get_modernisierungsempfehlungen( $energieausweis );
							if ( isset( $modernisierungsempfehlungen[ $parent_index ] ) ) {
								return $modernisierungsempfehlungen[ $parent_index ]['beschreibung'];
							}

							return false;
						case 'Modernisierungsempfehlungen::0_Modernisierungskombination':
						case 'Modernisierungsempfehlungen::1_Modernisierungskombination':
						case 'Modernisierungsempfehlungen::2_Modernisierungskombination':
						case 'Modernisierungsempfehlungen::3_Modernisierungskombination':
						case 'Modernisierungsempfehlungen::4_Modernisierungskombination':
						case 'Modernisierungsempfehlungen::5_Modernisierungskombination':
						case 'Modernisierungsempfehlungen::6_Modernisierungskombination':
						case 'Modernisierungsempfehlungen::7_Modernisierungskombination':
						case 'Modernisierungsempfehlungen::8_Modernisierungskombination':
						case 'Modernisierungsempfehlungen::9_Modernisierungskombination':
							$parent_index                = absint( str_replace( array(
								'Modernisierungsempfehlungen::',
								'_Modernisierungskombination'
							), '', $context ) );
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
							if ( 'gemischt' === $energieausweis->gebaeudeteil ) {
								return $item['options'][3];
							}
							$wohnungen = (int) $energieausweis->wohnungen;
							if ( 2 < $wohnungen ) {
								return $item['options'][2];
							}
							if ( 2 === $wohnungen ) {
								return $item['options'][1];
							}

							return $item['options'][0];
						case 'Anzahl-Wohneinheiten':
							return $energieausweis->wohnungen;
						case 'Gebaeudenutzflaeche':
							$calculations = $energieausweis->calculate();

							return (int) $calculations['nutzflaeche'];
						// Verbrauchswerte
						case 'Flaechenermittlung-AN-aus-Wohnflaeche':
							if ( $energieausweis->mode == 'b' ) {
								return 'false';
							}

							return 'true';
						case 'Wohnflaeche':
							return (int) $energieausweis->flaeche;
						case 'Keller-beheizt':
							if ( $energieausweis->keller == 'beheizt' ) {
								return 'true';
							}

							return 'false';
						case 'Warmwasserzuschlag::0_Startdatum':
							$calculations = $energieausweis->calculate();

							return date( 'Y-m-d', strtotime( $calculations['verbrauchsdaten'][0]['start'] ) );
						case 'Warmwasserzuschlag::0_Enddatum':
							$calculations = $energieausweis->calculate();

							return date( 'Y-m-d', strtotime( $calculations['verbrauchsdaten'][2]['ende'] ) );
						case 'Warmwasserzuschlag::0_Primaerenergiefaktor':
							$calculations = $energieausweis->calculate();

							return $calculations['anlagendaten']['h']['energietraeger_primaer'];
						case 'Warmwasserzuschlag::0_Warmwasserzuschlag-kWh':
							$calculations = $energieausweis->calculate();

							return $calculations['warmwasser_zuschlag'];
						case 'Kuehlzuschlag::0_Startdatum':
							$calculations = $energieausweis->calculate();

							return date( 'Y-m-d', strtotime( $calculations['verbrauchsdaten'][0]['start'] ) );
						case 'Kuehlzuschlag::0_Enddatum':
							$calculations = $energieausweis->calculate();

							return date( 'Y-m-d', strtotime( $calculations['verbrauchsdaten'][2]['ende'] ) );
						case 'Kuehlzuschlag::0_Gebaeudenutzflaeche-gekuehlt':
							$calculations     = $energieausweis->calculate();
							$kuehlung_flaeche = $energieausweis->k_flaeche ? floatval( $energieausweis->k_flaeche ) * $calculations['nutzflaeche_mpk'] : $calculations['nutzflaeche'];

							return $kuehlung_flaeche;
						case 'Kuehlzuschlag::0_Primaerenergiefaktor':
							$kuehlung_energietraeger = wpenon_get_table_results( 'energietraeger202001', array(
								'bezeichnung' => array(
									'value'   => 'strom',
									'compare' => '='
								)
							), array(), true );

							return $kuehlung_energietraeger->primaer;
						case 'Kuehlzuschlag::0_Kuehlzuschlag-kWh':
							$calculations = $energieausweis->calculate();

							return $calculations['kuehlung_zuschlag'];
						case 'Mittlerer-Endenergieverbrauch':
							$calculations = $energieausweis->calculate();

							return round( (float) $calculations['endenergie'], 1 );
						case 'Mittlerer-Primaerenergieverbrauch':
							$calculations = $energieausweis->calculate();

							return round( (float) $calculations['primaerenergie'], 1 );
						case 'Energieeffizienzklasse':
							$calculations = $energieausweis->calculate();

							return wpenon_get_class( $calculations['primaerenergie'], $energieausweis->wpenon_type );
						// Energietraeger
						case 'Energietraeger::0_Energietraeger-Verbrauch':
						case 'Energietraeger::1_Energietraeger-Verbrauch':
						case 'Energietraeger::2_Energietraeger-Verbrauch':
						case 'Energietraeger::3_Energietraeger-Verbrauch':
							$parent_index = absint( str_replace( array(
								'Energietraeger::',
								'_Energietraeger-Verbrauch'
							), '', $context ) );
							$counts       = wpenon_get_enev_anlagen_counts( $energieausweis );
							if ( isset( $counts[ $parent_index ] ) ) {
								$traeger_key = $counts[ $parent_index ] . '_energietraeger';

								// Migrate old data to new Energietraeger table data.
								if ( false !== strpos( $energieausweis->$traeger_key, '_kwhheizwert' ) ) {
									$energieausweis->$traeger_key = str_replace( '_kwhheizwert', '_kwh', $energieausweis->$traeger_key );
								} elseif ( false !== strpos( $energieausweis->$traeger_key, '_kwhbrennwert' ) ) {
									$energieausweis->$traeger_key = str_replace( '_kwhbrennwert', '_kwh', $energieausweis->$traeger_key );
								}

								$traeger = $energieausweis->$traeger_key;

								$mappings = wpenon_get_enev_energietraeger_unit_mappings();
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
							$parent_index = absint( str_replace( array(
								'Energietraeger::',
								'_Sonstiger-Energietraeger-Verbrauch'
							), '', $context ) );
							$counts       = wpenon_get_enev_anlagen_counts( $energieausweis );
							if ( isset( $counts[ $parent_index ] ) ) {
								$calculations = $energieausweis->calculate();
								$traeger      = $calculations['anlagendaten'][ $counts[ $parent_index ] ];

								return $traeger['energietraeger'] . ' in ' . \WPENON\Util\Format::unit( $traeger['einheit'], false );
							}

							return false;
						case 'Energietraeger::0_Unterer-Heizwert':
						case 'Energietraeger::1_Unterer-Heizwert':
						case 'Energietraeger::2_Unterer-Heizwert':
						case 'Energietraeger::3_Unterer-Heizwert':
							$parent_index = absint( str_replace( array(
								'Energietraeger::',
								'_Unterer-Heizwert'
							), '', $context ) );
							$counts       = wpenon_get_enev_anlagen_counts( $energieausweis );
							if ( isset( $counts[ $parent_index ] ) ) {
								$calculations = $energieausweis->calculate();
								$traeger      = $calculations['anlagendaten'][ $counts[ $parent_index ] ];

								return round( (float) $traeger['energietraeger_mpk'], 2 );
							}

							return false;
						case 'Energietraeger::0_Primaerenergiefaktor':
						case 'Energietraeger::1_Primaerenergiefaktor':
						case 'Energietraeger::2_Primaerenergiefaktor':
						case 'Energietraeger::3_Primaerenergiefaktor':
							$parent_index = absint( str_replace( array(
								'Energietraeger::',
								'_Primaerenergiefaktor'
							), '', $context ) );
							$counts       = wpenon_get_enev_anlagen_counts( $energieausweis );
							if ( isset( $counts[ $parent_index ] ) ) {
								$calculations = $energieausweis->calculate();
								$traeger      = $calculations['anlagendaten'][ $counts[ $parent_index ] ];

								return round( (float) $traeger['energietraeger_primaer'], 1 );
							}

							return false;
						case 'Energietraeger::0_Zeitraum::0_Startdatum':
						case 'Energietraeger::0_Zeitraum::1_Startdatum':
						case 'Energietraeger::0_Zeitraum::2_Startdatum':
						case 'Energietraeger::1_Zeitraum::0_Startdatum':
						case 'Energietraeger::1_Zeitraum::1_Startdatum':
						case 'Energietraeger::1_Zeitraum::2_Startdatum':
						case 'Energietraeger::2_Zeitraum::0_Startdatum':
						case 'Energietraeger::2_Zeitraum::1_Startdatum':
						case 'Energietraeger::2_Zeitraum::2_Startdatum':
						case 'Energietraeger::3_Zeitraum::0_Startdatum':
						case 'Energietraeger::3_Zeitraum::1_Startdatum':
						case 'Energietraeger::3_Zeitraum::2_Startdatum':
							$parts             = explode( '_', $context );
							$grandparent_index = absint( substr( $parts[0], - 1 ) );
							$parent_index      = absint( substr( $parts[1], - 1 ) );
							$counts            = wpenon_get_enev_anlagen_counts( $energieausweis );
							if ( isset( $counts[ $grandparent_index ] ) ) {
								$calculations = $energieausweis->calculate();
								$traeger      = $calculations['anlagendaten'][ $counts[ $grandparent_index ] ];
								if ( isset( $traeger['verbrauch'][ $parent_index ] ) ) {
									return date( 'Y-m-d', strtotime( $traeger['verbrauch'][ $parent_index ]['start'] ) );
								}
							}

							return false;
						case 'Energietraeger::0_Zeitraum::0_Enddatum':
						case 'Energietraeger::0_Zeitraum::1_Enddatum':
						case 'Energietraeger::0_Zeitraum::2_Enddatum':
						case 'Energietraeger::1_Zeitraum::0_Enddatum':
						case 'Energietraeger::1_Zeitraum::1_Enddatum':
						case 'Energietraeger::1_Zeitraum::2_Enddatum':
						case 'Energietraeger::2_Zeitraum::0_Enddatum':
						case 'Energietraeger::2_Zeitraum::1_Enddatum':
						case 'Energietraeger::2_Zeitraum::2_Enddatum':
						case 'Energietraeger::3_Zeitraum::0_Enddatum':
						case 'Energietraeger::3_Zeitraum::1_Enddatum':
						case 'Energietraeger::3_Zeitraum::2_Enddatum':
							$parts             = explode( '_', $context );
							$grandparent_index = absint( substr( $parts[0], - 1 ) );
							$parent_index      = absint( substr( $parts[1], - 1 ) );
							$counts            = wpenon_get_enev_anlagen_counts( $energieausweis );
							if ( isset( $counts[ $grandparent_index ] ) ) {
								$calculations = $energieausweis->calculate();
								$traeger      = $calculations['anlagendaten'][ $counts[ $grandparent_index ] ];
								if ( isset( $traeger['verbrauch'][ $parent_index ] ) ) {
									return date( 'Y-m-d', strtotime( $traeger['verbrauch'][ $parent_index ]['ende'] ) );
								}
							}

							return false;
						case 'Energietraeger::0_Zeitraum::0_Verbrauchte-Menge':
						case 'Energietraeger::0_Zeitraum::1_Verbrauchte-Menge':
						case 'Energietraeger::0_Zeitraum::2_Verbrauchte-Menge':
						case 'Energietraeger::1_Zeitraum::0_Verbrauchte-Menge':
						case 'Energietraeger::1_Zeitraum::1_Verbrauchte-Menge':
						case 'Energietraeger::1_Zeitraum::2_Verbrauchte-Menge':
						case 'Energietraeger::2_Zeitraum::0_Verbrauchte-Menge':
						case 'Energietraeger::2_Zeitraum::1_Verbrauchte-Menge':
						case 'Energietraeger::2_Zeitraum::2_Verbrauchte-Menge':
						case 'Energietraeger::3_Zeitraum::0_Verbrauchte-Menge':
						case 'Energietraeger::3_Zeitraum::1_Verbrauchte-Menge':
						case 'Energietraeger::3_Zeitraum::2_Verbrauchte-Menge':
							$parts             = explode( '_', $context );
							$grandparent_index = absint( substr( $parts[0], - 1 ) );
							$parent_index      = absint( substr( $parts[1], - 1 ) );
							$counts            = wpenon_get_enev_anlagen_counts( $energieausweis );
							if ( isset( $counts[ $grandparent_index ] ) ) {
								$calculations = $energieausweis->calculate();
								$traeger      = $calculations['anlagendaten'][ $counts[ $grandparent_index ] ];
								if ( isset( $traeger['verbrauch'][ $parent_index ] ) ) {
									$verbrauch          = !empty($traeger['verbrauch'][$parent_index]) ? $traeger['verbrauch'][$parent_index] : null;
									$heizung            = !empty($verbrauch['heizung']) ? $verbrauch['heizung'] : null;
									$warmwasser         = !empty($verbrauch['warmwasser']) ? $verbrauch['warmwasser'] : null;
									$energietraeger_mpk = !empty($traeger['energietraeger_mpk']) ? $traeger['energietraeger_mpk'] : null;

									if(!$heizung || $warmwasser || $energietraeger_mpk){
										return (int) 0;
									}

									$menge = ( $heizung + $warmwasser ) / $energietraeger_mpk;

									return (int) $menge;
								}
							}

							return false;
						case 'Energietraeger::0_Zeitraum::0_Energieverbrauch':
						case 'Energietraeger::0_Zeitraum::1_Energieverbrauch':
						case 'Energietraeger::0_Zeitraum::2_Energieverbrauch':
						case 'Energietraeger::1_Zeitraum::0_Energieverbrauch':
						case 'Energietraeger::1_Zeitraum::1_Energieverbrauch':
						case 'Energietraeger::1_Zeitraum::2_Energieverbrauch':
						case 'Energietraeger::2_Zeitraum::0_Energieverbrauch':
						case 'Energietraeger::2_Zeitraum::1_Energieverbrauch':
						case 'Energietraeger::2_Zeitraum::2_Energieverbrauch':
						case 'Energietraeger::3_Zeitraum::0_Energieverbrauch':
						case 'Energietraeger::3_Zeitraum::1_Energieverbrauch':
						case 'Energietraeger::3_Zeitraum::2_Energieverbrauch':
							$parts             = explode( '_', $context );
							$grandparent_index = absint( substr( $parts[0], - 1 ) );
							$parent_index      = absint( substr( $parts[1], - 1 ) );
							$counts            = wpenon_get_enev_anlagen_counts( $energieausweis );
							if ( isset( $counts[ $grandparent_index ] ) ) {
								$calculations = $energieausweis->calculate();
								$traeger      = $calculations['anlagendaten'][ $counts[ $grandparent_index ] ];
								if ( isset( $traeger['verbrauch'][ $parent_index ] ) ) {
									$verbrauch = $traeger['verbrauch'][ $parent_index ]['heizung'] + $traeger['verbrauch'][ $parent_index ]['warmwasser'];

									return (int) $verbrauch;
								}
							}

							return false;
						case 'Energietraeger::0_Zeitraum::0_Energieverbrauchsanteil-Warmwasser-zentral':
						case 'Energietraeger::0_Zeitraum::1_Energieverbrauchsanteil-Warmwasser-zentral':
						case 'Energietraeger::0_Zeitraum::2_Energieverbrauchsanteil-Warmwasser-zentral':
						case 'Energietraeger::1_Zeitraum::0_Energieverbrauchsanteil-Warmwasser-zentral':
						case 'Energietraeger::1_Zeitraum::1_Energieverbrauchsanteil-Warmwasser-zentral':
						case 'Energietraeger::1_Zeitraum::2_Energieverbrauchsanteil-Warmwasser-zentral':
						case 'Energietraeger::2_Zeitraum::0_Energieverbrauchsanteil-Warmwasser-zentral':
						case 'Energietraeger::2_Zeitraum::1_Energieverbrauchsanteil-Warmwasser-zentral':
						case 'Energietraeger::2_Zeitraum::2_Energieverbrauchsanteil-Warmwasser-zentral':
						case 'Energietraeger::3_Zeitraum::0_Energieverbrauchsanteil-Warmwasser-zentral':
						case 'Energietraeger::3_Zeitraum::1_Energieverbrauchsanteil-Warmwasser-zentral':
						case 'Energietraeger::3_Zeitraum::2_Energieverbrauchsanteil-Warmwasser-zentral':
							$parts             = explode( '_', $context );
							$grandparent_index = absint( substr( $parts[0], - 1 ) );
							$parent_index      = absint( substr( $parts[1], - 1 ) );
							$counts            = wpenon_get_enev_anlagen_counts( $energieausweis );
							if ( isset( $counts[ $grandparent_index ] ) ) {
								$calculations = $energieausweis->calculate();
								$traeger      = $calculations['anlagendaten'][ $counts[ $grandparent_index ] ];
								if ( isset( $traeger['verbrauch'][ $parent_index ] ) ) {
									return (int) $traeger['verbrauch'][ $parent_index ]['warmwasser'];
								}
							}

							return false;
						case 'Energietraeger::0_Zeitraum::0_Warmwasserwertermittlung':
						case 'Energietraeger::0_Zeitraum::1_Warmwasserwertermittlung':
						case 'Energietraeger::0_Zeitraum::2_Warmwasserwertermittlung':
						case 'Energietraeger::1_Zeitraum::0_Warmwasserwertermittlung':
						case 'Energietraeger::1_Zeitraum::1_Warmwasserwertermittlung':
						case 'Energietraeger::1_Zeitraum::2_Warmwasserwertermittlung':
						case 'Energietraeger::2_Zeitraum::0_Warmwasserwertermittlung':
						case 'Energietraeger::2_Zeitraum::1_Warmwasserwertermittlung':
						case 'Energietraeger::2_Zeitraum::2_Warmwasserwertermittlung':
						case 'Energietraeger::3_Zeitraum::0_Warmwasserwertermittlung':
						case 'Energietraeger::3_Zeitraum::1_Warmwasserwertermittlung':
						case 'Energietraeger::3_Zeitraum::2_Warmwasserwertermittlung':
							if ( $energieausweis->ww_info == 'ww' ) {
								return $item['options'][0];
							}

							return $item['options'][1];
						case 'Energietraeger::0_Zeitraum::0_Klimafaktor':
						case 'Energietraeger::0_Zeitraum::1_Klimafaktor':
						case 'Energietraeger::0_Zeitraum::2_Klimafaktor':
						case 'Energietraeger::1_Zeitraum::0_Klimafaktor':
						case 'Energietraeger::1_Zeitraum::1_Klimafaktor':
						case 'Energietraeger::1_Zeitraum::2_Klimafaktor':
						case 'Energietraeger::2_Zeitraum::0_Klimafaktor':
						case 'Energietraeger::2_Zeitraum::1_Klimafaktor':
						case 'Energietraeger::2_Zeitraum::2_Klimafaktor':
						case 'Energietraeger::3_Zeitraum::0_Klimafaktor':
						case 'Energietraeger::3_Zeitraum::1_Klimafaktor':
						case 'Energietraeger::3_Zeitraum::2_Klimafaktor':
							$parts             = explode( '_', $context );
							$grandparent_index = absint( substr( $parts[0], - 1 ) );
							$parent_index      = absint( substr( $parts[1], - 1 ) );
							$counts            = wpenon_get_enev_anlagen_counts( $energieausweis );
							if ( isset( $counts[ $grandparent_index ] ) ) {
								$calculations = $energieausweis->calculate();
								$traeger      = $calculations['anlagendaten'][ $counts[ $grandparent_index ] ];
								if ( isset( $traeger['verbrauch'][ $parent_index ] ) ) {
									return round( (float) $traeger['verbrauch'][ $parent_index ]['klima'], 2 );
								}
							}

							return false;
						case 'Energietraeger::0_Zeitraum::0_Verbrauchswert-kWh-Strom':
						case 'Energietraeger::0_Zeitraum::1_Verbrauchswert-kWh-Strom':
						case 'Energietraeger::0_Zeitraum::2_Verbrauchswert-kWh-Strom':
						case 'Energietraeger::1_Zeitraum::0_Verbrauchswert-kWh-Strom':
						case 'Energietraeger::1_Zeitraum::1_Verbrauchswert-kWh-Strom':
						case 'Energietraeger::1_Zeitraum::2_Verbrauchswert-kWh-Strom':
						case 'Energietraeger::2_Zeitraum::0_Verbrauchswert-kWh-Strom':
						case 'Energietraeger::2_Zeitraum::1_Verbrauchswert-kWh-Strom':
						case 'Energietraeger::2_Zeitraum::2_Verbrauchswert-kWh-Strom':
						case 'Energietraeger::3_Zeitraum::0_Verbrauchswert-kWh-Strom':
						case 'Energietraeger::3_Zeitraum::1_Verbrauchswert-kWh-Strom':
						case 'Energietraeger::3_Zeitraum::2_Verbrauchswert-kWh-Strom':
							$parts             = explode( '_', $context );
							$grandparent_index = absint( substr( $parts[0], - 1 ) );
							$parent_index      = absint( substr( $parts[1], - 1 ) );
							$counts            = wpenon_get_enev_anlagen_counts( $energieausweis );
							if ( isset( $counts[ $grandparent_index ] ) ) {
								$calculations = $energieausweis->calculate();
								$traeger      = $calculations['anlagendaten'][ $counts[ $grandparent_index ] ];
								if ( isset( $traeger['verbrauch'][ $parent_index ] ) ) {
									return (int) $traeger['verbrauch'][ $parent_index ]['strom'];
								}
							}

							return false;
						// Leerstand Heizung + Warmwasser + Strom
						case 'Leerstandszuschlag-Heizung::0_kein-Leerstand':
							return 'Kein längerer Leerstand Heizung zu berücksichtigen.';
						case 'keine-Nutzung-von-WW':
							return 'false';
						case 'Leerstandszuschlag-Warmwasser::0_kein-Leerstand':
							return 'Kein längerer Leerstand Warmwasser zu berücksichtigen.';
						case 'Leerstandszuschlag-Strom::0_kein-Leerstand':
							return 'Kein längerer Leerstand Strom zu berücksichtigen.';
						// Leerstandskorrektur-nach-Bekanntmachung
						case 'Leerstandszuschlag-nach-Bekanntmachung::0_Startdatum':
							$calculations = $energieausweis->calculate();

							return date( 'Y-m-d', strtotime( $calculations['verbrauchsdaten'][0]['start'] ) );
						case 'Leerstandszuschlag-nach-Bekanntmachung::0_Enddatum':
							$calculations = $energieausweis->calculate();

							return date( 'Y-m-d', strtotime( $calculations['verbrauchsdaten'][2]['ende'] ) );
						case 'Leerstandsfaktor':
							$gesamt = ( ( $energieausweis->verbrauch1_leerstand + $energieausweis->verbrauch2_leerstand + $energieausweis->verbrauch3_leerstand ) / 3.0 ) * 0.01;

							return round( (float) $gesamt, 2 );
						case 'Leerstandszuschlag-Heizung::0_Leerstandszuschlag-nach-Bekanntmachung::0_Leerstandszuschlag-kWh':
							$calculations = $energieausweis->calculate();
							$gesamt       = 0.0;
							foreach ( $calculations['verbrauchsdaten'] as $jahr ) {
								$gesamt += $jahr['heizung'];
							}
							$gesamt = $gesamt / ( 1.0 - ( ( $energieausweis->verbrauch1_leerstand + $energieausweis->verbrauch2_leerstand + $energieausweis->verbrauch3_leerstand ) / 3.0 ) * 0.01 ) - $gesamt;

							return (int) $gesamt;
						case 'Leerstandszuschlag-Heizung::0_Leerstandszuschlag-nach-Bekanntmachung::0_Primaerenergiefaktor':
							$calculations = $energieausweis->calculate();

							return $calculations['anlagendaten']['h']['energietraeger_primaer'];
						case 'Leerstandszuschlag-Warmwasser::0_Leerstandszuschlag-nach-Bekanntmachung::0_Leerstandszuschlag-kWh':
							$calculations = $energieausweis->calculate();
							$gesamt       = 0.0;
							foreach ( $calculations['verbrauchsdaten'] as $jahr ) {
								$gesamt += $jahr['warmwasser'];
							}
							$gesamt = $gesamt / ( 1.0 - ( ( $energieausweis->verbrauch1_leerstand + $energieausweis->verbrauch2_leerstand + $energieausweis->verbrauch3_leerstand ) / 3.0 ) * 0.01 ) - $gesamt;

							return (int) $gesamt;
						case 'Leerstandszuschlag-Warmwasser::0_Leerstandszuschlag-nach-Bekanntmachung::0_Primaerenergiefaktor':
							$calculations = $energieausweis->calculate();
							if ( isset( $calculations['anlagendaten']['ww'] ) ) {
								return $calculations['anlagendaten']['ww']['energietraeger_primaer'];
							}

							return $calculations['anlagendaten']['h']['energietraeger_primaer'];
						// Bedarfswerte-4108-4701
						case 'Wohngebaeude-Anbaugrad':
							$gebauedetyp = $energieausweis->gebaeudetyp;
							if ( 'reihenhaus' === $gebaeudetyp ) {
								return $item['options'][2];
							}
							if ( 'reiheneckhaus' === $gebaeudetyp || 'doppelhaushaelfte' === $gebaeudetyp ) {
								return $item['options'][1];
							}

							return $item['options'][0];
						case 'Bruttovolumen':
							$calculations = $energieausweis->calculate();

							return (int) $calculations['huellvolumen'];
						case 'durchschnittliche-Geschosshoehe':
							return round( (float) $energieausweis->geschoss_hoehe, 2 );
						case 'Waermebrueckenzuschlag':
							return round( (float) 0.1, 3 );
						case 'Transmissionswaermeverlust':
							$calculations = $energieausweis->calculate();

							return (int) $calculations['qt'];
						case 'Luftdichtheit':
							if ( $energieausweis->dichtheit ) {
								return $item['options'][2];
							}

							return $item['options'][1];
						case 'Lueftungswaermeverlust':
							$calculations = $energieausweis->calculate();

							return (int) $calculations['qv'];
						case 'Solare-Waermegewinne':
							$calculations = $energieausweis->calculate();

							return (int) $calculations['qs'];
						case 'Interne-Waermegewinne':
							$calculations = $energieausweis->calculate();

							return (int) $calculations['qi'];
						case 'Pufferspeicher-Nenninhalt':
							return 0;
						case 'Heizkreisauslegungstemperatur':
							$calculations = $energieausweis->calculate();
							$anteil_max   = 0;
							$hktemp       = '';
							foreach ( $calculations['anlagendaten'] as $anlage ) {
								if ( $anlage['art'] == 'heizung' ) {
									if ( $anlage['deckungsanteil'] > $anteil_max ) {
										$anteil_max = $anlage['deckungsanteil'];
										$hktemp     = $anlage['heizkreistemperatur'];
									}
								}
							}
							if ( $hktemp == '70/55°' ) {
								return $item['options'][1];
							}

							return $item['options'][2];
						case 'Heizungsanlage-innerhalb-Huelle':
							if ( $energieausweis->speicherung_standort == 'innerhalb' ) {
								return 'true';
							}

							return 'false';
						case 'Trinkwarmwasserspeicher-Nenninhalt':
							return 0;
						case 'Trinkwarmwasserverteilung-Zirkulation':
							if ( $energieausweis->verteilung_versorgung == 'mit' ) {
								return 'true';
							}

							return 'false';
						case 'Vereinfachte-Datenaufnahme':
							return 'true';
						case 'spezifischer-Transmissionswaermeverlust-Ist':
							$calculations = $energieausweis->calculate();

							return round( (float) $calculations['ht_b'], 2 );
						case 'Endenergiebedarf-Waerme-AN':
							$calculations = $energieausweis->calculate();

							return round( (float) $calculations['qh_e_b'] + $calculations['qw_e_b'], 1 );
						case 'Endenergiebedarf-Hilfsenergie-AN':
							$calculations = $energieausweis->calculate();

							return round( (float) $calculations['qhe_e_b'], 1 );
						case 'Primaerenergiebedarf':
							$calculations = $energieausweis->calculate();

							return round( (float) $calculations['primaerenergie'], 1 );
						case 'Primaerenergiebedarf-Hoechstwert-Neubau':
							$calculations = $energieausweis->calculate();

							return round( (float) $calculations['primaerenergie_reference'], 1 );
						// Bauteil-Opak
						case 'Bauteil-Opak::0_Flaechenbezeichnung':
						case 'Bauteil-Opak::1_Flaechenbezeichnung':
						case 'Bauteil-Opak::2_Flaechenbezeichnung':
						case 'Bauteil-Opak::3_Flaechenbezeichnung':
						case 'Bauteil-Opak::4_Flaechenbezeichnung':
						case 'Bauteil-Opak::5_Flaechenbezeichnung':
						case 'Bauteil-Opak::6_Flaechenbezeichnung':
						case 'Bauteil-Opak::7_Flaechenbezeichnung':
						case 'Bauteil-Opak::8_Flaechenbezeichnung':
						case 'Bauteil-Opak::9_Flaechenbezeichnung':
						case 'Bauteil-Opak::10_Flaechenbezeichnung':
						case 'Bauteil-Opak::11_Flaechenbezeichnung':
						case 'Bauteil-Opak::12_Flaechenbezeichnung':
						case 'Bauteil-Opak::13_Flaechenbezeichnung':
						case 'Bauteil-Opak::14_Flaechenbezeichnung':
						case 'Bauteil-Opak::15_Flaechenbezeichnung':
						case 'Bauteil-Opak::16_Flaechenbezeichnung':
						case 'Bauteil-Opak::17_Flaechenbezeichnung':
						case 'Bauteil-Opak::18_Flaechenbezeichnung':
						case 'Bauteil-Opak::19_Flaechenbezeichnung':
						case 'Bauteil-Opak::20_Flaechenbezeichnung':
						case 'Bauteil-Opak::21_Flaechenbezeichnung':
						case 'Bauteil-Opak::22_Flaechenbezeichnung':
						case 'Bauteil-Opak::23_Flaechenbezeichnung':
						case 'Bauteil-Opak::24_Flaechenbezeichnung':
							$calculations  = $energieausweis->calculate();
							$bauteile_opak = wp_list_filter( $calculations['bauteile'], array( 'modus' => 'opak' ) );
							$index         = absint( str_replace( array(
								'Bauteil-Opak::',
								'_Flaechenbezeichnung'
							), '', $context ) );
							$key           = array_keys( $bauteile_opak )[ $index ];

							return $calculations['bauteile'][ $key ]['name'];
						case 'Bauteil-Opak::0_Flaeche':
						case 'Bauteil-Opak::1_Flaeche':
						case 'Bauteil-Opak::2_Flaeche':
						case 'Bauteil-Opak::3_Flaeche':
						case 'Bauteil-Opak::4_Flaeche':
						case 'Bauteil-Opak::5_Flaeche':
						case 'Bauteil-Opak::6_Flaeche':
						case 'Bauteil-Opak::7_Flaeche':
						case 'Bauteil-Opak::8_Flaeche':
						case 'Bauteil-Opak::9_Flaeche':
						case 'Bauteil-Opak::10_Flaeche':
						case 'Bauteil-Opak::11_Flaeche':
						case 'Bauteil-Opak::12_Flaeche':
						case 'Bauteil-Opak::13_Flaeche':
						case 'Bauteil-Opak::14_Flaeche':
						case 'Bauteil-Opak::15_Flaeche':
						case 'Bauteil-Opak::16_Flaeche':
						case 'Bauteil-Opak::17_Flaeche':
						case 'Bauteil-Opak::18_Flaeche':
						case 'Bauteil-Opak::19_Flaeche':
						case 'Bauteil-Opak::20_Flaeche':
						case 'Bauteil-Opak::21_Flaeche':
						case 'Bauteil-Opak::22_Flaeche':
						case 'Bauteil-Opak::23_Flaeche':
						case 'Bauteil-Opak::24_Flaeche':
							$calculations  = $energieausweis->calculate();
							$bauteile_opak = wp_list_filter( $calculations['bauteile'], array( 'modus' => 'opak' ) );
							$index         = absint( str_replace( array(
								'Bauteil-Opak::',
								'_Flaeche'
							), '', $context ) );
							$key           = array_keys( $bauteile_opak )[ $index ];

							return (int) $calculations['bauteile'][ $key ]['a'];
						case 'Bauteil-Opak::0_U-Wert':
						case 'Bauteil-Opak::1_U-Wert':
						case 'Bauteil-Opak::2_U-Wert':
						case 'Bauteil-Opak::3_U-Wert':
						case 'Bauteil-Opak::4_U-Wert':
						case 'Bauteil-Opak::5_U-Wert':
						case 'Bauteil-Opak::6_U-Wert':
						case 'Bauteil-Opak::7_U-Wert':
						case 'Bauteil-Opak::8_U-Wert':
						case 'Bauteil-Opak::9_U-Wert':
						case 'Bauteil-Opak::10_U-Wert':
						case 'Bauteil-Opak::11_U-Wert':
						case 'Bauteil-Opak::12_U-Wert':
						case 'Bauteil-Opak::13_U-Wert':
						case 'Bauteil-Opak::14_U-Wert':
						case 'Bauteil-Opak::15_U-Wert':
						case 'Bauteil-Opak::16_U-Wert':
						case 'Bauteil-Opak::17_U-Wert':
						case 'Bauteil-Opak::18_U-Wert':
						case 'Bauteil-Opak::19_U-Wert':
						case 'Bauteil-Opak::20_U-Wert':
						case 'Bauteil-Opak::21_U-Wert':
						case 'Bauteil-Opak::22_U-Wert':
						case 'Bauteil-Opak::23_U-Wert':
						case 'Bauteil-Opak::24_U-Wert':
							$calculations  = $energieausweis->calculate();
							$bauteile_opak = wp_list_filter( $calculations['bauteile'], array( 'modus' => 'opak' ) );
							$index         = absint( str_replace( array(
								'Bauteil-Opak::',
								'_U-Wert'
							), '', $context ) );
							$key           = array_keys( $bauteile_opak )[ $index ];

							return round( (float) $calculations['bauteile'][ $key ]['u'], 3 );
						case 'Bauteil-Opak::0_Ausrichtung':
						case 'Bauteil-Opak::1_Ausrichtung':
						case 'Bauteil-Opak::2_Ausrichtung':
						case 'Bauteil-Opak::3_Ausrichtung':
						case 'Bauteil-Opak::4_Ausrichtung':
						case 'Bauteil-Opak::5_Ausrichtung':
						case 'Bauteil-Opak::6_Ausrichtung':
						case 'Bauteil-Opak::7_Ausrichtung':
						case 'Bauteil-Opak::8_Ausrichtung':
						case 'Bauteil-Opak::9_Ausrichtung':
						case 'Bauteil-Opak::10_Ausrichtung':
						case 'Bauteil-Opak::11_Ausrichtung':
						case 'Bauteil-Opak::12_Ausrichtung':
						case 'Bauteil-Opak::13_Ausrichtung':
						case 'Bauteil-Opak::14_Ausrichtung':
						case 'Bauteil-Opak::15_Ausrichtung':
						case 'Bauteil-Opak::16_Ausrichtung':
						case 'Bauteil-Opak::17_Ausrichtung':
						case 'Bauteil-Opak::18_Ausrichtung':
						case 'Bauteil-Opak::19_Ausrichtung':
						case 'Bauteil-Opak::20_Ausrichtung':
						case 'Bauteil-Opak::21_Ausrichtung':
						case 'Bauteil-Opak::22_Ausrichtung':
						case 'Bauteil-Opak::23_Ausrichtung':
						case 'Bauteil-Opak::24_Ausrichtung':
							$calculations  = $energieausweis->calculate();
							$bauteile_opak = wp_list_filter( $calculations['bauteile'], array( 'modus' => 'opak' ) );
							$index         = absint( str_replace( array(
								'Bauteil-Opak::',
								'_Ausrichtung'
							), '', $context ) );
							$key           = array_keys( $bauteile_opak )[ $index ];
							if ( ! empty( $calculations['bauteile'][ $key ]['richtung'] ) ) {
								return strtoupper( $calculations['bauteile'][ $key ]['richtung'] );
							}

							return 'HOR';
						case 'Bauteil-Opak::0_grenztAn':
						case 'Bauteil-Opak::1_grenztAn':
						case 'Bauteil-Opak::2_grenztAn':
						case 'Bauteil-Opak::3_grenztAn':
						case 'Bauteil-Opak::4_grenztAn':
						case 'Bauteil-Opak::5_grenztAn':
						case 'Bauteil-Opak::6_grenztAn':
						case 'Bauteil-Opak::7_grenztAn':
						case 'Bauteil-Opak::8_grenztAn':
						case 'Bauteil-Opak::9_grenztAn':
						case 'Bauteil-Opak::10_grenztAn':
						case 'Bauteil-Opak::11_grenztAn':
						case 'Bauteil-Opak::12_grenztAn':
						case 'Bauteil-Opak::13_grenztAn':
						case 'Bauteil-Opak::14_grenztAn':
						case 'Bauteil-Opak::15_grenztAn':
						case 'Bauteil-Opak::16_grenztAn':
						case 'Bauteil-Opak::17_grenztAn':
						case 'Bauteil-Opak::18_grenztAn':
						case 'Bauteil-Opak::19_grenztAn':
						case 'Bauteil-Opak::20_grenztAn':
						case 'Bauteil-Opak::21_grenztAn':
						case 'Bauteil-Opak::22_grenztAn':
						case 'Bauteil-Opak::23_grenztAn':
						case 'Bauteil-Opak::24_grenztAn':
							$calculations  = $energieausweis->calculate();
							$bauteile_opak = wp_list_filter( $calculations['bauteile'], array( 'modus' => 'opak' ) );
							$index         = absint( str_replace( array(
								'Bauteil-Opak::',
								'_grenztAn'
							), '', $context ) );
							$key           = array_keys( $bauteile_opak )[ $index ];
							if ( 'kellerwand' === $key || 'boden' === $key ) {
								return $item['options'][2];
							}
							if ( 'kellerdecke' === $key || 'decke' === $key ) {
								return $item['options'][1];
							}

							return $item['options'][0];
						// Bauteil-Transparent
						case 'Bauteil-Transparent::0_Flaechenbezeichnung':
						case 'Bauteil-Transparent::1_Flaechenbezeichnung':
						case 'Bauteil-Transparent::2_Flaechenbezeichnung':
						case 'Bauteil-Transparent::3_Flaechenbezeichnung':
						case 'Bauteil-Transparent::4_Flaechenbezeichnung':
						case 'Bauteil-Transparent::5_Flaechenbezeichnung':
						case 'Bauteil-Transparent::6_Flaechenbezeichnung':
						case 'Bauteil-Transparent::7_Flaechenbezeichnung':
						case 'Bauteil-Transparent::8_Flaechenbezeichnung':
						case 'Bauteil-Transparent::9_Flaechenbezeichnung':
						case 'Bauteil-Transparent::10_Flaechenbezeichnung':
						case 'Bauteil-Transparent::11_Flaechenbezeichnung':
						case 'Bauteil-Transparent::12_Flaechenbezeichnung':
						case 'Bauteil-Transparent::13_Flaechenbezeichnung':
						case 'Bauteil-Transparent::14_Flaechenbezeichnung':
						case 'Bauteil-Transparent::15_Flaechenbezeichnung':
							$calculations         = $energieausweis->calculate();
							$bauteile_transparent = wp_list_filter( $calculations['bauteile'], array( 'modus' => 'transparent' ) );
							$index                = absint( str_replace( array(
								'Bauteil-Transparent::',
								'_Flaechenbezeichnung'
							), '', $context ) );
							$key                  = array_keys( $bauteile_transparent )[ $index ];

							return $calculations['bauteile'][ $key ]['name'];
						case 'Bauteil-Transparent::0_Flaeche':
						case 'Bauteil-Transparent::1_Flaeche':
						case 'Bauteil-Transparent::2_Flaeche':
						case 'Bauteil-Transparent::3_Flaeche':
						case 'Bauteil-Transparent::4_Flaeche':
						case 'Bauteil-Transparent::5_Flaeche':
						case 'Bauteil-Transparent::6_Flaeche':
						case 'Bauteil-Transparent::7_Flaeche':
						case 'Bauteil-Transparent::8_Flaeche':
						case 'Bauteil-Transparent::9_Flaeche':
						case 'Bauteil-Transparent::10_Flaeche':
						case 'Bauteil-Transparent::11_Flaeche':
						case 'Bauteil-Transparent::12_Flaeche':
						case 'Bauteil-Transparent::13_Flaeche':
						case 'Bauteil-Transparent::14_Flaeche':
						case 'Bauteil-Transparent::15_Flaeche':
							$calculations         = $energieausweis->calculate();
							$bauteile_transparent = wp_list_filter( $calculations['bauteile'], array( 'modus' => 'transparent' ) );
							$index                = absint( str_replace( array(
								'Bauteil-Transparent::',
								'_Flaeche'
							), '', $context ) );
							$key                  = array_keys( $bauteile_transparent )[ $index ];

							return (int) $calculations['bauteile'][ $key ]['a'];
						case 'Bauteil-Transparent::0_U-Wert':
						case 'Bauteil-Transparent::1_U-Wert':
						case 'Bauteil-Transparent::2_U-Wert':
						case 'Bauteil-Transparent::3_U-Wert':
						case 'Bauteil-Transparent::4_U-Wert':
						case 'Bauteil-Transparent::5_U-Wert':
						case 'Bauteil-Transparent::6_U-Wert':
						case 'Bauteil-Transparent::7_U-Wert':
						case 'Bauteil-Transparent::8_U-Wert':
						case 'Bauteil-Transparent::9_U-Wert':
						case 'Bauteil-Transparent::10_U-Wert':
						case 'Bauteil-Transparent::11_U-Wert':
						case 'Bauteil-Transparent::12_U-Wert':
						case 'Bauteil-Transparent::13_U-Wert':
						case 'Bauteil-Transparent::14_U-Wert':
						case 'Bauteil-Transparent::15_U-Wert':
							$calculations         = $energieausweis->calculate();
							$bauteile_transparent = wp_list_filter( $calculations['bauteile'], array( 'modus' => 'transparent' ) );
							$index                = absint( str_replace( array(
								'Bauteil-Transparent::',
								'_U-Wert'
							), '', $context ) );
							$key                  = array_keys( $bauteile_transparent )[ $index ];

							return round( (float) $calculations['bauteile'][ $key ]['u'], 3 );
						case 'Bauteil-Transparent::0_g-Wert':
						case 'Bauteil-Transparent::1_g-Wert':
						case 'Bauteil-Transparent::2_g-Wert':
						case 'Bauteil-Transparent::3_g-Wert':
						case 'Bauteil-Transparent::4_g-Wert':
						case 'Bauteil-Transparent::5_g-Wert':
						case 'Bauteil-Transparent::6_g-Wert':
						case 'Bauteil-Transparent::7_g-Wert':
						case 'Bauteil-Transparent::8_g-Wert':
						case 'Bauteil-Transparent::9_g-Wert':
						case 'Bauteil-Transparent::10_g-Wert':
						case 'Bauteil-Transparent::11_g-Wert':
						case 'Bauteil-Transparent::12_g-Wert':
						case 'Bauteil-Transparent::13_g-Wert':
						case 'Bauteil-Transparent::14_g-Wert':
						case 'Bauteil-Transparent::15_g-Wert':
							$calculations         = $energieausweis->calculate();
							$bauteile_transparent = wp_list_filter( $calculations['bauteile'], array( 'modus' => 'transparent' ) );
							$index                = absint( str_replace( array(
								'Bauteil-Transparent::',
								'_g-Wert'
							), '', $context ) );
							$key                  = array_keys( $bauteile_transparent )[ $index ];

							return round( (float) wpenon_immoticket24_get_g_wert( $calculations['bauteile'][ $key ]['bauart'] ), 3 );
						case 'Bauteil-Transparent::0_Ausrichtung':
						case 'Bauteil-Transparent::1_Ausrichtung':
						case 'Bauteil-Transparent::2_Ausrichtung':
						case 'Bauteil-Transparent::3_Ausrichtung':
						case 'Bauteil-Transparent::4_Ausrichtung':
						case 'Bauteil-Transparent::5_Ausrichtung':
						case 'Bauteil-Transparent::6_Ausrichtung':
						case 'Bauteil-Transparent::7_Ausrichtung':
						case 'Bauteil-Transparent::8_Ausrichtung':
						case 'Bauteil-Transparent::9_Ausrichtung':
						case 'Bauteil-Transparent::10_Ausrichtung':
						case 'Bauteil-Transparent::11_Ausrichtung':
						case 'Bauteil-Transparent::12_Ausrichtung':
						case 'Bauteil-Transparent::13_Ausrichtung':
						case 'Bauteil-Transparent::14_Ausrichtung':
						case 'Bauteil-Transparent::15_Ausrichtung':
							$calculations         = $energieausweis->calculate();
							$bauteile_transparent = wp_list_filter( $calculations['bauteile'], array( 'modus' => 'transparent' ) );
							$index                = absint( str_replace( array(
								'Bauteil-Transparent::',
								'_Ausrichtung'
							), '', $context ) );
							$key                  = array_keys( $bauteile_transparent )[ $index ];
							if ( ! empty( $calculations['bauteile'][ $key ]['richtung'] ) ) {
								return strtoupper( $calculations['bauteile'][ $key ]['richtung'] );
							}

							return 'HOR';
						// Bauteil-Dach
						case 'Bauteil-Dach::0_Flaechenbezeichnung':
						case 'Bauteil-Dach::1_Flaechenbezeichnung':
						case 'Bauteil-Dach::2_Flaechenbezeichnung':
						case 'Bauteil-Dach::3_Flaechenbezeichnung':
						case 'Bauteil-Dach::4_Flaechenbezeichnung':
						case 'Bauteil-Dach::5_Flaechenbezeichnung':
						case 'Bauteil-Dach::6_Flaechenbezeichnung':
						case 'Bauteil-Dach::7_Flaechenbezeichnung':
						case 'Bauteil-Dach::8_Flaechenbezeichnung':
						case 'Bauteil-Dach::9_Flaechenbezeichnung':
						case 'Bauteil-Dach::10_Flaechenbezeichnung':
						case 'Bauteil-Dach::11_Flaechenbezeichnung':
						case 'Bauteil-Dach::12_Flaechenbezeichnung':
						case 'Bauteil-Dach::13_Flaechenbezeichnung':
						case 'Bauteil-Dach::14_Flaechenbezeichnung':
						case 'Bauteil-Dach::15_Flaechenbezeichnung':
							$calculations  = $energieausweis->calculate();
							$bauteile_dach = wp_list_filter( $calculations['bauteile'], array( 'modus' => 'dach' ) );
							$index         = absint( str_replace( array(
								'Bauteil-Dach::',
								'_Flaechenbezeichnung'
							), '', $context ) );
							$key           = array_keys( $bauteile_dach )[ $index ];

							return $calculations['bauteile'][ $key ]['name'];
						case 'Bauteil-Dach::0_Flaeche':
						case 'Bauteil-Dach::1_Flaeche':
						case 'Bauteil-Dach::2_Flaeche':
						case 'Bauteil-Dach::3_Flaeche':
						case 'Bauteil-Dach::4_Flaeche':
						case 'Bauteil-Dach::5_Flaeche':
						case 'Bauteil-Dach::6_Flaeche':
						case 'Bauteil-Dach::7_Flaeche':
						case 'Bauteil-Dach::8_Flaeche':
						case 'Bauteil-Dach::9_Flaeche':
						case 'Bauteil-Dach::10_Flaeche':
						case 'Bauteil-Dach::11_Flaeche':
						case 'Bauteil-Dach::12_Flaeche':
						case 'Bauteil-Dach::13_Flaeche':
						case 'Bauteil-Dach::14_Flaeche':
						case 'Bauteil-Dach::15_Flaeche':
							$calculations  = $energieausweis->calculate();
							$bauteile_dach = wp_list_filter( $calculations['bauteile'], array( 'modus' => 'dach' ) );
							$index         = absint( str_replace( array(
								'Bauteil-Dach::',
								'_Flaeche'
							), '', $context ) );
							$key           = array_keys( $bauteile_dach )[ $index ];

							return (int) $calculations['bauteile'][ $key ]['a'];
						case 'Bauteil-Dach::0_U-Wert':
						case 'Bauteil-Dach::1_U-Wert':
						case 'Bauteil-Dach::2_U-Wert':
						case 'Bauteil-Dach::3_U-Wert':
						case 'Bauteil-Dach::4_U-Wert':
						case 'Bauteil-Dach::5_U-Wert':
						case 'Bauteil-Dach::6_U-Wert':
						case 'Bauteil-Dach::7_U-Wert':
						case 'Bauteil-Dach::8_U-Wert':
						case 'Bauteil-Dach::9_U-Wert':
						case 'Bauteil-Dach::10_U-Wert':
						case 'Bauteil-Dach::11_U-Wert':
						case 'Bauteil-Dach::12_U-Wert':
						case 'Bauteil-Dach::13_U-Wert':
						case 'Bauteil-Dach::14_U-Wert':
						case 'Bauteil-Dach::15_U-Wert':
							$calculations  = $energieausweis->calculate();
							$bauteile_dach = wp_list_filter( $calculations['bauteile'], array( 'modus' => 'dach' ) );
							$index         = absint( str_replace( array(
								'Bauteil-Dach::',
								'_U-Wert'
							), '', $context ) );
							$key           = array_keys( $bauteile_dach )[ $index ];

							return round( (float) $calculations['bauteile'][ $key ]['u'], 3 );
						// Heizungsanlage
						case 'Heizungsanlage::0_Waermeerzeuger-Bauweise-4701':
						case 'Heizungsanlage::1_Waermeerzeuger-Bauweise-4701':
						case 'Heizungsanlage::2_Waermeerzeuger-Bauweise-4701':
							$calculations = $energieausweis->calculate();
							$key          = absint( str_replace( array(
								'Heizungsanlage::',
								'_Waermeerzeuger-Bauweise-4701'
							), '', $context ) );
							$count        = 0;
							foreach ( $calculations['anlagendaten'] as $anlage ) {
								if ( $anlage['art'] == 'heizung' ) {
									if ( $key == $count ) {
										$mappings = wpenon_get_enev_waermeerzeuger_mappings();
										if ( isset( $mappings[ $anlage['slug'] ] ) ) {
											if ( is_array( $mappings[ $anlage['slug'] ] ) ) {
												if ( (int) $anlage['baujahr'] >= 1995 ) {
													return $item['options'][ $mappings[ $anlage['slug'] ][1] ];
												}

												return $item['options'][ $mappings[ $anlage['slug'] ][0] ];
											}
											if ( $mappings[ $anlage['slug'] ] > - 1 ) {
												return $item['options'][ $mappings[ $anlage['slug'] ] ];
											}
										}

										return $item['options'][31];
									}
									$count ++;
								}
							}

							return false;
						case 'Heizungsanlage::0_Nennleistung':
						case 'Heizungsanlage::1_Nennleistung':
						case 'Heizungsanlage::2_Nennleistung':
							$calculations = $energieausweis->calculate();
							$key          = absint( str_replace( array(
								'Heizungsanlage::',
								'_Nennleistung'
							), '', $context ) );
							$count        = 0;
							foreach ( $calculations['anlagendaten'] as $anlage ) {
								if ( $anlage['art'] == 'heizung' ) {
									if ( $key == $count ) {
										return 0;
									}
									$count ++;
								}
							}

							return false;
						case 'Heizungsanlage::0_Waermeerzeuger-Baujahr':
						case 'Heizungsanlage::1_Waermeerzeuger-Baujahr':
						case 'Heizungsanlage::2_Waermeerzeuger-Baujahr':
							$calculations = $energieausweis->calculate();
							$key          = absint( str_replace( array(
								'Heizungsanlage::',
								'_Waermeerzeuger-Baujahr'
							), '', $context ) );
							$count        = 0;
							foreach ( $calculations['anlagendaten'] as $anlage ) {
								if ( $anlage['art'] == 'heizung' ) {
									if ( $key == $count ) {
										return $anlage['baujahr'];
									}
									$count ++;
								}
							}

							return false;
						case 'Heizungsanlage::0_Anzahl-baugleiche':
						case 'Heizungsanlage::1_Anzahl-baugleiche':
						case 'Heizungsanlage::2_Anzahl-baugleiche':
							$calculations = $energieausweis->calculate();
							$key          = absint( str_replace( array(
								'Heizungsanlage::',
								'_Anzahl-baugleiche'
							), '', $context ) );
							$count        = 0;
							foreach ( $calculations['anlagendaten'] as $anlage ) {
								if ( $anlage['art'] == 'heizung' ) {
									if ( $key == $count ) {
										return 1;
									}
									$count ++;
								}
							}

							return false;
						case 'Heizungsanlage::0_Energietraeger':
						case 'Heizungsanlage::1_Energietraeger':
						case 'Heizungsanlage::2_Energietraeger':
							$calculations = $energieausweis->calculate();
							$key          = absint( str_replace( array(
								'Heizungsanlage::',
								'_Energietraeger'
							), '', $context ) );
							$count        = 0;
							foreach ( $calculations['anlagendaten'] as $anlage ) {
								if ( $anlage['art'] == 'heizung' ) {
									if ( $key == $count ) {
										$mappings = wpenon_get_enev_energietraeger_mappings();
										if ( isset( $mappings[ $anlage['energietraeger_slug'] ] ) && $mappings[ $anlage['energietraeger_slug'] ] > - 1 ) {
											return $item['options'][ $mappings[ $anlage['energietraeger_slug'] ] ];
										}

										return $item['options'][20];
									}
									$count ++;
								}
							}

							return false;
						case 'Heizungsanlage::0_Primaerenergiefaktor':
						case 'Heizungsanlage::1_Primaerenergiefaktor':
						case 'Heizungsanlage::2_Primaerenergiefaktor':
							$calculations = $energieausweis->calculate();
							$key          = absint( str_replace( array(
								'Heizungsanlage::',
								'_Primaerenergiefaktor'
							), '', $context ) );
							$count        = 0;
							foreach ( $calculations['anlagendaten'] as $anlage ) {
								if ( $anlage['art'] == 'heizung' ) {
									if ( $key == $count ) {
										return round( (float) $anlage['energietraeger_primaer'], 1 );
									}
									$count ++;
								}
							}

							return false;
						// Trinkwarmwasseranlage
						case 'Trinkwarmwasseranlage::0_Trinkwarmwassererzeuger-Bauweise-4701':
						case 'Trinkwarmwasseranlage::1_Trinkwarmwassererzeuger-Bauweise-4701':
						case 'Trinkwarmwasseranlage::2_Trinkwarmwassererzeuger-Bauweise-4701':
							$calculations = $energieausweis->calculate();
							$key          = absint( str_replace( array(
								'Trinkwarmwasseranlage::',
								'_Trinkwarmwassererzeuger-Bauweise-4701'
							), '', $context ) );
							$count        = 0;
							foreach ( $calculations['anlagendaten'] as $anlage ) {
								if ( $anlage['art'] == 'warmwasser' ) {
									if ( $key == $count ) {
										$mappings = wpenon_get_enev_warmwassererzeuger_mappings();
										if ( isset( $mappings[ $anlage['slug'] ] ) && $mappings[ $anlage['slug'] ] > - 1 ) {
											return $item['options'][ $mappings[ $anlage['slug'] ] ];
										}
										if ( $energieausweis->ww_info != 'ww' ) {
											return $item['options'][0];
										}

										return $item['options'][5];
									}
									$count ++;
								}
							}

							return false;
						case 'Trinkwarmwasseranlage::0_Trinkwarmwassererzeuger-Baujahr':
						case 'Trinkwarmwasseranlage::1_Trinkwarmwassererzeuger-Baujahr':
						case 'Trinkwarmwasseranlage::2_Trinkwarmwassererzeuger-Baujahr':
							$calculations = $energieausweis->calculate();
							$key          = absint( str_replace( array(
								'Trinkwarmwasseranlage::',
								'_Trinkwarmwassererzeuger-Baujahr'
							), '', $context ) );
							$count        = 0;
							foreach ( $calculations['anlagendaten'] as $anlage ) {
								if ( $anlage['art'] == 'warmwasser' ) {
									if ( $key == $count ) {
										return $anlage['baujahr'];
									}
									$count ++;
								}
							}

							return false;
						case 'Trinkwarmwasseranlage::0_Anzahl-baugleiche':
						case 'Trinkwarmwasseranlage::1_Anzahl-baugleiche':
						case 'Trinkwarmwasseranlage::2_Anzahl-baugleiche':
							$calculations = $energieausweis->calculate();
							$key          = absint( str_replace( array(
								'Trinkwarmwasseranlage::',
								'_Anzahl-baugleiche'
							), '', $context ) );
							$count        = 0;
							foreach ( $calculations['anlagendaten'] as $anlage ) {
								if ( $anlage['art'] == 'warmwasser' ) {
									if ( $key == $count ) {
										return 1;
									}
									$count ++;
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
}

function wpenon_get_enev_anlagen_counts( $energieausweis ) {
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

function wpenon_get_enev_waermeerzeuger_mappings() {
	return array(
		'standardkessel'              => array( 2, 5 ),
		'niedertemperaturkessel'      => array( 6, 10 ),
		'brennwertkessel'             => array( 11, 12 ),
		'brennwertkesselverbessert'   => 13,
		'fernwaerme'                  => 15,
		'waermepumpeluft'             => 17,
		'waermepumpewasser'           => 19,
		'waermepumpeerde'             => 20,
		'kleinthermeniedertemperatur' => - 1,
		'kleinthermebrennwert'        => - 1,
		'oelofen'                     => 24,
		'gasraumheizer'               => 22,
		'kohleholzofen'               => 25,
		'nachtspeicher'               => 29,
		'direktheizgeraet'            => 28,
		'solaranlage'                 => 30,
		'elektrospeicher'             => - 1,
	);
}

function wpenon_get_enev_warmwassererzeuger_mappings() {
	return array(
		'standardkessel'              => 0,
		'niedertemperaturkessel'      => 0,
		'brennwertkessel'             => 0,
		'brennwertkesselverbessert'   => 0,
		'fernwaerme'                  => 0,
		'waermepumpeluft'             => 0,
		'waermepumpewasser'           => 0,
		'waermepumpeerde'             => 0,
		'kleinthermeniedertemperatur' => - 1,
		'kleinthermebrennwert'        => - 1,
		'dezentralkleinspeicher'      => 1,
		'dezentralelektroerhitzer'    => 3,
		'dezentralgaserhitzer'        => 3,
		'elektrospeicher'             => 1,
		'gasspeicher'                 => 2,
		'solaranlage'                 => 4,
	);
}

function wpenon_get_enev_energietraeger_mappings() {
	return array(
		'heizoel'                  => 0,
		'heizoelbiooel'            => 1,
		'biooel'                   => 2,
		'erdgas'                   => 3,
		'erdgasbiogas'             => 4,
		'biogas'                   => 5,
		'fluessiggas'              => 6,
		'steinkohle'               => 7,
		'koks'                     => - 1,
		'braunkohle'               => 8,
		'stueckholz'               => 9,
		'holzhackschnitzel'        => 9,
		'holzpellets'              => 9,
		'strom'                    => 14,
		'fernwaermehzwfossil'      => 12,
		'fernwaermehzwregenerativ' => 13,
		'fernwaermekwkfossil'      => 10,
		'fernwaermekwkregenerativ' => 11,
		'sonneneinstrahlung'       => 15,
	);
}

function wpenon_get_enev_energietraeger_unit_mappings() {
	return array(
		'heizoel_l'                    => 0,
		'heizoel_kwh'                  => array( 1, 2 ),
		'heizoelbiooel_l'              => 35,
		'heizoelbiooel_kwh'            => array( 33, 34 ),
		'biooel_l'                     => 18,
		'biooel_kwh'                   => array( 19, 20 ),
		'erdgas_m3'                    => 3,
		'erdgas_kwh'                   => array( 4, 5 ),
		'erdgasbiogas_m3'              => 35,
		'erdgasbiogas_kwh'             => array( 33, 34 ),
		'biogas_m3'                    => 15,
		'biogas_kwh'                   => array( 16, 17 ),
		'fluessiggas_l'                => 7,
		'fluessiggas_m3'               => 6,
		'fluessiggas_kg'               => 8,
		'fluessiggas_kwh'              => array( 9, 10 ),
		'steinkohle_kg'                => 11,
		'steinkohle_kwh'               => 12,
		'braunkohle_kg'                => 13,
		'braunkohle_kwh'               => 14,
		'stueckholz_m3'                => 21,
		'stueckholz_kg'                => 22,
		'stueckholz_kwh'               => array( 23, 24 ),
		'holzpellets_kg'               => 22,
		'holzpellets_kwh'              => array( 23, 24 ),
		'strom_kwh'                    => 30,
		'fernwaermehzwfossil_kwh'      => 28,
		'fernwaermehzwregenerativ_kwh' => 29,
		'fernwaermekwkfossil_kwh'      => 26,
		'fernwaermekwkregenerativ_kwh' => 27,
	);
}
