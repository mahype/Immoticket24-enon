<?php

require_once dirname( dirname( __FILE__ ) ) . '/calculations/CalculationsCC.php';

function wpenon_get_enev_pdf_data( $context, $index = 0, $energieausweis = null, $data = array() ) {
	if( isset( $energieausweis ) && $energieausweis->mode == 'v' )
	{		
		$calcCC = new Enev\Schema202302\Calculations\CalculationsCC( $energieausweis );
	}

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
			if ( $energieausweis->mode == 'v' ) 
			{
				return $calcCC->getBuilding()->getUsefulArea();
			}

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
				$energietraeger[] = wpenon_immoticket24_get_energietraeger_name_2021( $energieausweis->h_energietraeger );
				if ( $energieausweis->h2_info ) {
					$energietraeger[] = wpenon_immoticket24_get_energietraeger_name_2021( $energieausweis->h2_energietraeger );
					if ( $energieausweis->h3_info ) {
						$energietraeger[] = wpenon_immoticket24_get_energietraeger_name_2021( $energieausweis->h3_energietraeger );
					}
				}
			} else {
				$energietraeger[] = wpenon_immoticket24_get_energietraeger_name_2021( $energieausweis->h_energietraeger, true );
				if ( $energieausweis->h2_info ) {
					$energietraeger[] = wpenon_immoticket24_get_energietraeger_name_2021( $energieausweis->h2_energietraeger, true );
					if ( $energieausweis->h3_info ) {
						$energietraeger[] = wpenon_immoticket24_get_energietraeger_name_2021( $energieausweis->h3_energietraeger, true );
					}
				}
			}

			return implode( ', ', array_unique( $energietraeger ) );
		case 'energietraeger_warmwasser':
			$has_unit = true;
			if ( $energieausweis->mode == 'b' ) {
				$has_unit = false;
			}
			switch($energieausweis->ww_info) {
				case 'ww':
					return wpenon_immoticket24_get_energietraeger_name_2021( $energieausweis->ww_energietraeger, $has_unit );
				case 'h':
					return wpenon_immoticket24_get_energietraeger_name_2021( $energieausweis->h_energietraeger, $has_unit );
				case 'h2':
					return wpenon_immoticket24_get_energietraeger_name_2021( $energieausweis->h2_energietraeger, $has_unit );
				case 'h3':
					return wpenon_immoticket24_get_energietraeger_name_2021( $energieausweis->h3_energietraeger, $has_unit );
				default:
					return '';
			}	
		case 'regenerativ_art':
			if ( $energieausweis->mode == 'v' ) {
				return wpenon_immoticket24_get_regenerativ_art_name( $energieausweis->regenerativ_art );
			}

			$erneuerbare_energien = array();

			if( $energieausweis->solarthermie_info == 'vorhanden' ) {
				$erneuerbare_energien[] = 'Solarthermie';
			}

			if( $energieausweis->pv_info == 'vorhanden' ) {
				$erneuerbare_energien[] = 'Photovoltaik';
			}

			return count( $erneuerbare_energien ) > 0 ? implode( ', ', $erneuerbare_energien ) : 'Keine';		
		case 'regenerativ_nutzung':
			if( $energieausweis->mode == 'v') {
				if( 'keine' !== $energieausweis->regenerativ_art ) {
					return wpenon_immoticket24_get_regenerativ_nutzung_name( $energieausweis->regenerativ_nutzung );
				}
				
				return 'Keine';
			}

			if( $energieausweis->solarthermie_info == 'vorhanden' ) {
				$erneuerbare_energien[] = 'Warmwasser';
			}

			if( $energieausweis->pv_info == 'vorhanden' ) {
				$erneuerbare_energien[] = 'Strom';
			}

			return count( $erneuerbare_energien ) > 0 ? implode( ', ', $erneuerbare_energien ) : 'Keine';
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
		case 'inspektionspflichtige_klimaanlagen':
			if( $energieausweis->k_leistung != 'groesser' )
			{
				return false;				
			}

			return true;
		case 'inspektion_faelligkeit':
			if( $energieausweis->k_leistung !== 'groesser' )
			{
				return '';				
			}

			$k_baujahr = explode( '/', $energieausweis->k_baujahr );
			$k_baujahr = $k_baujahr[1] . '-' . $k_baujahr[0];
			$k_baujahr = new DateTime( $k_baujahr );

			$baujahr_limit = new DateTime( '2008-10' );			

			if ( $k_baujahr < $baujahr_limit ) {
				return '12/2022';
			}

			if( $energieausweis->k_automation === 'yes' )
			{
				return 'Keine Inspektion nötig, da Gebäudeautomation';
			}

			if( ! empty ( $energieausweis->k_inspektion ) ) {
				$k_inspektion = explode( '/', $energieausweis->k_inspektion );
				$k_inspektion = $k_inspektion[1] . '-' . $k_inspektion[0];
				$k_inspektion = new DateTime( $k_inspektion );
				$k_inspektion->add( new DateInterval('P10Y') );

				return $k_inspektion->format('m/Y');
			}
			
			$k_baujahr->add( new DateInterval('P10Y') );
			return $k_baujahr->format('m/Y');
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
			if( $energieausweis->mode == 'v') {
				return 125;
			}
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['reference'] ) ) {
				return $calculations['reference'];
			}

			return null;
		case 'endenergie':
			if( $energieausweis->mode == 'v') {
				return $calcCC->getBuilding()->getFinalEnergy();
			}

			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['endenergie'] ) ) {
				return $calculations['endenergie'];
			}

			return null;
		case 'primaerenergie':
			if( $energieausweis->mode == 'v') {
				return $calcCC->getBuilding()->getPrimaryEnergy();
			}

			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['primaerenergie'] ) ) {
				return $calculations['primaerenergie'];
			}

			return null;
		case 'co2_emissionen':
			if( $energieausweis->mode == 'v') {
				return $calcCC->getBuilding()->getCo2Emissions();
			}

			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['co2_emissionen'] ) ) {
				return $calculations['co2_emissionen'];
			}

			return null;			
		// Für Nichtwohngebäude
		case 's_reference':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['s_reference'] ) ) {
				return $calculations['s_reference'];
			}

			return null;
		// Für Nichtwohngebäude
		case 's_endenergie':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['s_endenergie'] ) ) {
				return $calculations['s_endenergie'];
			}

			return null;
		// Für Nichtwohngebäude
		case 's_primaerenergie':
			$calculations = $energieausweis->calculate();
			if ( isset( $calculations['s_primaerenergie'] ) ) {
				return $calculations['s_primaerenergie'];
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
			if( $energieausweis->mode == 'v') {
				return 'din-v-4108-6';
			}
			return 'din-v-18599';
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
			if( $energieausweis->mode == 'v') {
				return $calcCC->getConsumptionDataList();
			}

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
