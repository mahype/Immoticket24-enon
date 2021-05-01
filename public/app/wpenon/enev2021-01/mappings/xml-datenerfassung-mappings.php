<?php

if( ! function_exists( 'wpenon_get_enev_xml_datenerfassung_data' ) ) {
	function wpenon_get_enev_xml_datenerfassung_data($context, $index = 0, $energieausweis = null, $data = array())
	{
		if (isset($data['mode'])) {
			switch ($data['mode']) {
				case 'occurrences':
					$min = $data['min'];
					$max = $data['max'];
					switch ($context) {
						case 'Energieausweis-Daten':
						case 'EnergInspektions-Daten':
							return 1;
						default:
					}
					break;
				case 'attribute':
					$attribute = $data['attribute'];
					switch ($context) {
						default:
					}
					break;
				case 'choice':
					$choices = $data['choices'];
					switch ($context) {
						case 'EnEV-Nachweis':
							return $choices[0];
						default:
					}
					break;
				case 'value':
					$item = $data['item'];
					switch ($context) {
						case 'Aussteller_ID_DIBT':
							$credentials = wpenon_get_dibt_credentials();
							return $credentials['user'];
						case 'Aussteller_PWD_DIBT':
							$credentials = wpenon_get_dibt_credentials();
							return $credentials['password'];
						case 'Ausstellungsdatum':
							return wpenon_get_reference_date('Y-m-d', $energieausweis);
						case 'Bundesland':
							return $energieausweis->adresse_bundesland;
						case 'Postleitzahl':
							return $energieausweis->adresse_plz;
						case 'Gesetzesgrundlage':
							return 'GEG';
						case 'Gebaeudeart':
							if ($energieausweis->gebaeudetyp == 'gemischt') {
								if ($energieausweis->building == 'n') {
									return $item['options'][3];
								}
								return $item['options'][2];
							} else {
								if ($energieausweis->building == 'n') {
									return $item['options'][1];
								}
								return $item['options'][0];
							}
						case 'Art':
							if ($energieausweis->mode == 'b') {
								return $item['options'][1];
							}
							return $item['options'][0];
						case 'Neubau':
							if ($energieausweis->anlass == 'neubau') {
								return $item['options'][1];
							}
							return $item['options'][0];
						default:
					}
					break;
				default:
			}
		}

		return false;
	}
}
