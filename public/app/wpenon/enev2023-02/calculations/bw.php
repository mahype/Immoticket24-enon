<?php
/**
 * Kalkulationen für den Bedarfsausweis.
 *
 * @package wpenon
 */

namespace Enev\Schema202302\Calculations;

use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202302\Calculations\Gebaeude\Grundriss;
use Enev\Schema202302\Calculations\Gebaeude\Anbau;
use Enev\Schema202302\Calculations\Gebaeude\Grundriss_Anbau;
use Enev\Schema202302\Calculations\Gebaeude\Keller;

use Enev\Schema202302\Calculations\Anlagentechnik\Heizungsanlage;
use Enev\Schema202302\Calculations\Anlagentechnik\Uebergabesystem;
use Enev\Schema202302\Calculations\Anlagentechnik\Wasserversorgung;
use Enev\Schema202302\Calculations\Bauteile\Anbauboden;
use Enev\Schema202302\Calculations\Bauteile\Anbaudecke;
use Enev\Schema202302\Calculations\Bauteile\Anbaufenster;
use Enev\Schema202302\Calculations\Bauteile\Anbauwand;
use Enev\Schema202302\Calculations\Bauteile\Boden;
use Enev\Schema202302\Calculations\Bauteile\Decke;
use Enev\Schema202302\Calculations\Bauteile\Fenster;
use Enev\Schema202302\Calculations\Bauteile\Flachdach;
use Enev\Schema202302\Calculations\Bauteile\Heizkoerpernische;
use Enev\Schema202302\Calculations\Bauteile\Kellerboden;
use Enev\Schema202302\Calculations\Bauteile\Kellerwand;
use Enev\Schema202302\Calculations\Bauteile\Pultdach;
use Enev\Schema202302\Calculations\Bauteile\Rolladenkasten;
use Enev\Schema202302\Calculations\Bauteile\Satteldach;
use Enev\Schema202302\Calculations\Bauteile\Walmdach;
use Enev\Schema202302\Calculations\Bauteile\Wand;
use Enev\Schema202302\Calculations\Tabellen\Luftwechsel;
use Enev\Schema202302\Calculations\Tabellen\Mittlere_Belastung_Korrekturfaktor;

use function Enev\Schema202302\Calculations\Helfer\berechne_fenster_flaeche;
use function Enev\Schema202302\Calculations\Helfer\berechne_heizkoerpernische_flaeche;
use function Enev\Schema202302\Calculations\Helfer\berechne_rolladenkasten_flaeche;
use function Enev\Schema202302\Calculations\Tabellen\uwert;

require_once __DIR__ . '/Helfer/Jahr.php';
require_once __DIR__ . '/Helfer/Math.php';
require_once __DIR__ . '/Helfer/Faktoren.php';
require_once __DIR__ . '/Helfer/Bauteile.php';

require_once __DIR__ . '/Tabellen/Ausnutzungsgrad.php';
require_once __DIR__ . '/Tabellen/Bilanz_Innentemperatur.php';
require_once __DIR__ . '/Tabellen/Mittlere_Belastung.php';
require_once __DIR__ . '/Tabellen/Luftwechsel.php';
require_once __DIR__ . '/Tabellen/Uwert.php';

require_once __DIR__ . '/Gebaeude/Gebaeude.php';
require_once __DIR__ . '/Gebaeude/Grundriss_Anbau.php';
require_once __DIR__ . '/Gebaeude/Anbau.php';
require_once __DIR__ . '/Gebaeude/Grundriss_Anbau.php';
require_once __DIR__ . '/Gebaeude/Keller.php';

require_once __DIR__ . '/Bauteile/Bauteile.php';
require_once __DIR__ . '/Bauteile/Bauteil.php';
require_once __DIR__ . '/Bauteile/Decke.php';
require_once __DIR__ . '/Bauteile/Boden.php';
require_once __DIR__ . '/Bauteile/Fenster_Sammlung.php';
require_once __DIR__ . '/Bauteile/Fenster.php';
require_once __DIR__ . '/Bauteile/Anbaufenster.php';
require_once __DIR__ . '/Bauteile/Anbauboden.php';
require_once __DIR__ . '/Bauteile/Anbaudecke.php';
require_once __DIR__ . '/Bauteile/Wand_Sammlung.php';
require_once __DIR__ . '/Bauteile/Wand.php';
require_once __DIR__ . '/Bauteile/Heizkoerpernische.php';
require_once __DIR__ . '/Bauteile/Rolladenkasten.php';
require_once __DIR__ . '/Bauteile/Kellerboden.php';
require_once __DIR__ . '/Bauteile/Kellerwand.php';
require_once __DIR__ . '/Bauteile/Anbauwand.php';
require_once __DIR__ . '/Bauteile/Flachdach.php';
require_once __DIR__ . '/Bauteile/Pultdach.php';
require_once __DIR__ . '/Bauteile/Satteldach.php';
require_once __DIR__ . '/Bauteile/Walmdach.php';

require_once __DIR__ . '/Tabellen/Mittlere_Belastung_Korrekturfaktor.php';

/**
 * Kalkulationen für den Bedarfsausweis.
 *
 * Die Variable $energieausweis enthält alle Daten, die für die Berechnung des Energieausweises benötigt werden.
 *
 * Daten aus dem Formular werden einfach als magische Variable aufgerufen, z.B. $energieausweis->baujahr.
 * Die Kalkulationen werden in der Variable $calculations gespeichert.
 *
 * Ab dieser Version werden die Berechnungen der DIN 18599 verwendet.
 */

$calculations = array();

/**
 * Anlegen des Grundrisses.
 */
$grundriss = new Grundriss( $energieausweis->grundriss_form, $energieausweis->grundriss_richtung );

foreach ( $grundriss->waende_manuell() as $wand ) {
	$wand_laenge_slug = 'wand_' . $wand . '_laenge';
	$wand_laenge      = $energieausweis->$wand_laenge_slug;
	$grundriss->wand_laenge( $wand, $wand_laenge );
}

/**
 * Gebäude.
 */
$gebaeude = new Gebaeude(
	grundriss: $grundriss,
	baujahr: $energieausweis->baujahr,
	geschossanzahl: $energieausweis->geschoss_zahl,
	geschosshoehe: $energieausweis->geschoss_hoehe,
	anzahl_wohnungen: $energieausweis->wohnungen,
	standort_heizsystem: $energieausweis->h_standort
);

$calculations['gebaeude'] = $gebaeude;

$gwert_fenster = wpenon_immoticket24_get_g_wert( $energieausweis->fenster_bauart );

/**
* Dach
*/
switch ( $energieausweis->dach ) {
	case 'beheizt':
		$kniestock_hoehe = isset( $energieausweis->kniestock_hoehe ) ? $energieausweis->kniestock_hoehe : 0.0;
		$daemmung_dach   = isset( $energieausweis->dach_daemmung ) ? $energieausweis->dach_daemmung : 0.0;
		$uwert_dach      = uwert( 'dach_' . $energieausweis->dach_bauart, $energieausweis->baujahr );

		switch ( $energieausweis->dach_form ) {
			case 'walmdach':
				$dach = new Walmdach(
					grundriss: $grundriss,
					name: __( 'Walmdach', 'wpenon' ),
					hoehe: $energieausweis->dach_hoehe,
					kniestock_hoehe: $kniestock_hoehe,
					uwert: $uwert_dach,
					daemmung: $daemmung_dach
				);
				break;
			case 'satteldach':
				$dach = new Satteldach(
					grundriss: $grundriss,
					name: __( 'Satteldach', 'wpenon' ),
					hoehe: $energieausweis->dach_hoehe,
					kniestock_hoehe: $kniestock_hoehe,
					uwert: $uwert_dach,
					daemmung: $daemmung_dach
				);
				break;
			case 'pultdach':
				$dach = new Pultdach(
					grundriss: $grundriss,
					name: __( 'Pultdach', 'wpenon' ),
					hoehe: $energieausweis->dach_hoehe,
					kniestock_hoehe: $kniestock_hoehe,
					uwert: $uwert_dach,
					daemmung: $daemmung_dach
				);
				break;
			default:
				throw new Calculation_Exception( 'Dachform nicht bekannt.' );
		}

		$gebaeude->bauteile()->hinzufuegen( $dach );

		break;
	case 'unbeheizt':
		$decke = new Decke(
			name: __( 'Oberste Geschossdecke', 'wpenon' ),
			grundriss: $grundriss,
			uwert: uwert( 'decke_' . $energieausweis->decke_bauart, $energieausweis->baujahr ),
			daemmung: $energieausweis->decke_daemmung,
		);

		$gebaeude->bauteile()->hinzufuegen( $decke );
		break;
	case 'nicht-vorhanden':
	default:
		$daemmung_dach = isset( $energieausweis->dach_daemmung ) ? $energieausweis->dach_daemmung : 0.0;
		$uwert_dach    = uwert( 'dach_' . $energieausweis->dach_bauart, $energieausweis->baujahr );

		$dach = new Flachdach(
			grundriss: $grundriss,
			name: __( 'Flachdach', 'wpenon' ),
			uwert: $uwert_dach,
			daemmung: $daemmung_dach,
		);

		$gebaeude->bauteile()->hinzufuegen( $dach );
}

/**
* Anbaus, falls vorhanden.
*
* Der Anbau wird zuerst hinzugefügt, um eventuelle Überlappungen mit dem Hauptgebäude zu berechnen.
*/
if ( $energieausweis->anbau ) {
	$grundriss_anbau = new Grundriss_Anbau( $energieausweis->anbau_form, $energieausweis->grundriss_richtung );

	// Hinzufügen der angegebenen Wandlängen zum Grundriss des Anbaus.
	foreach ( $grundriss_anbau->seiten_manuell() as $wand ) {
		$wand_laenge_slug = 'anbauwand_' . $wand . '_laenge';
		$wand_laenge       = $energieausweis->$wand_laenge_slug;
		$grundriss_anbau->wand_laenge( $wand, $wand_laenge );
	}

	$gebaeude->anbau( new Anbau( $grundriss_anbau, $energieausweis->anbau_hoehe ) );

	// Hinzufügen der Bauteile des Anbaus zum Gebäude.
	$anbauwand_bauart_feldname = 'anbauwand_bauart_' . $energieausweis->gebaeudekonstruktion;
	$anbauwand_bauart_name     = $energieausweis->$anbauwand_bauart_feldname;
	$uwert_anbau_wand    = uwert( 'wand_' . $anbauwand_bauart_name, $energieausweis->anbau_baujahr );
	$uwert_anbau_fenster = uwert( 'fenster_' . $energieausweis->fenster_bauart, $energieausweis->fenster_baujahr );

	foreach ( $gebaeude->anbau()->grundriss()->waende() as $wand ) {
		$anbauwand = new Anbauwand(
			name: sprintf( __( 'Anbauwand %s', 'wpenon' ), $wand ),
			seite: $wand,
			flaeche: $gebaeude->anbau()->wandseite_flaeche( $wand ),
			uwert: $uwert_anbau_wand,
			himmelsrichtung: $grundriss_anbau->wand_himmelsrichtung( $wand ),
			daemmung: $energieausweis->anbauwand_daemmung,
		);

		$fenster_flaeche = berechne_fenster_flaeche( $grundriss_anbau->wand_laenge( $wand ), $energieausweis->anbau_hoehe, $energieausweis->anbauwand_staerke / 100 );

		$fenster = new Anbaufenster(
			name: sprintf( __( 'Anbaufenster Wand %s', 'wpenon' ), $wand ),
			gwert: $gwert_fenster,
			uwert: $uwert_anbau_fenster,
			flaeche: $fenster_flaeche, // Hier die Lichte Höhe und nicht die Geschosshöhe verwenden um die Fenster zu berechnen.
			himmelsrichtung: $grundriss_anbau->wand_himmelsrichtung( $wand ),
			winkel: 90.0
		);

		$anbauwand->flaeche_reduzieren( $fenster->flaeche() );

		$gebaeude->bauteile()->hinzufuegen( $fenster );
		$gebaeude->bauteile()->hinzufuegen( $anbauwand );
	}

	$gebaeude->bauteile()->hinzufuegen(
		new Anbauboden(
			name: sprintf( __( 'Anbau-Boden', 'wpenon' ) ),
			flaeche: $grundriss->flaeche(),
			uwert: uwert( 'boden_' . $energieausweis->anbauboden_bauart, $energieausweis->anbau_baujahr ),
			daemmung: $energieausweis->anbauboden_daemmung,
		)
	);

	$gebaeude->bauteile()->hinzufuegen(
		new Anbaudecke(
			name: sprintf( __( 'Anbau-Dach', 'wpenon' ) ),
			grundriss: $grundriss_anbau,
			uwert: uwert( 'decke_' . $energieausweis->anbaudecke_bauart, $energieausweis->anbau_baujahr ),
			daemmung: $energieausweis->anbaudach_daemmung,
		)
	);
}

/**
* Hinzufügen aller Wände des Hauptgebäudes.
*/

$wand_bauart_feld_name = 'wand_bauart_' . $energieausweis->gebaeudekonstruktion;
$wand_bauart           = $energieausweis->$wand_bauart_feld_name;
$uwert_wand            = uwert( 'wand_' . $wand_bauart, $energieausweis->baujahr );

foreach ( $grundriss->waende() as $wand ) {
	$nachbar_slug = 'wand_' . $wand . '_nachbar';

	if ( $energieausweis->$nachbar_slug ) { // Wenn es eine Wand zum Nachbar ist, dann wird diese nicht als Außenwand gewertet und entfällt.
		continue;
	}

	$daemmung_slug = 'wand_' . $wand . '_daemmung';

	$wand_laenge = $gebaeude->grundriss()->wand_laenge( $wand );
	$wand_hoehe = $gebaeude->geschosshoehe() * $gebaeude->geschossanzahl();
	$wand_flaeche = $wand_laenge * $wand_hoehe;

	$wand = new Wand(
		// translators: %s: Seite der Wand.
		name: sprintf( __( 'Außenwand %s', 'wpenon' ), $wand ),
		seite: $wand,
		flaeche: $wand_flaeche,
		uwert: $uwert_wand,
		himmelsrichtung: $gebaeude->grundriss()->wand_himmelsrichtung( $wand ),
		daemmung: $energieausweis->$daemmung_slug,
		grenzt_an_wohngebaeude: $energieausweis->$nachbar_slug
	);

	$gebaeude->bauteile()->hinzufuegen( $wand );
}

/**
* Hinzufügen der Bauteile Wände, Fenster, Heizkörpernischen und Rolladenkästen.
*/
foreach ( $gebaeude->bauteile()->waende()->alle() as $wand ) {
	if ( $wand->grenzt_an_wohngebaeude() ) {
		continue;
	}

	$fensterflaeche = $heizkoerpernische_flaeche = $rolladenkaesten_flaeche = 0.0;

	// Ist ein beheiztes Dachgeschoss vorhanden, muss das Mauerwerk für die Wand hinzugefügt werden.
	if ( $gebaeude->dach_vorhanden() ) {
		$dachwand_flaeche = $gebaeude->dach()->wandseite_flaeche( $wand->seite() );
		$dachwand_flaeche_kniestock = $gebaeude->dach()->kniestock_flaeche( $wand->seite() );
		$wand->flaeche_addieren( $dachwand_flaeche + $dachwand_flaeche_kniestock );
	}

	// Ist ein Anbau vorhanden, muss die überlappende Fläche vom Mauerwerk abgezogen werden.
	if ( $gebaeude->anbau_vorhanden() ) {
		$anbau_schnittflaeche = $gebaeude->anbau()->ueberlappung_flaeche_gebaeude( $wand->seite() );
		$wand->flaeche_reduzieren( $anbau_schnittflaeche );
	}

	/**
	 * Fenster
	 */	
	$wand_laenge = $gebaeude->grundriss()->wand_laenge( $wand->seite() );

	// TODO: Berechnung ggf. auch mit Abzug der Schnittfläche des Anbaus.
	// NOTE: Vorher ausgelassen, da dies in den originalen Berechnungen auch nicht berücksichtigt wurde.	
	// if( $gebaeude->anbau_vorhanden() ) {
	// 	$wand_laenge -= $gebaeude->anbau()->ueberlappung_laenge_wand( $wand->seite() );
	// }

	$fensterflaeche  = berechne_fenster_flaeche( $wand_laenge, $energieausweis->geschoss_hoehe, $energieausweis->wand_staerke / 100 ) * $energieausweis->geschoss_zahl;  // Hier die Lichte Höhe und nicht die Geschosshöhe verwenden um die Fenster zu berechnen.
	$uwert_fenster   = uwert( 'fenster_' . $energieausweis->fenster_bauart, $energieausweis->fenster_baujahr );
	$himmelsrichtung = $gebaeude->grundriss()->wand_himmelsrichtung( $wand->seite() );

	$fenster = new Fenster(
		name: sprintf( __( 'Fenster Wand %s', 'wpenon' ), $wand->name() ),
		gwert: $gwert_fenster,
		uwert: $uwert_fenster,
		flaeche: $fensterflaeche,
		himmelsrichtung: $himmelsrichtung,
		winkel: 90.0
	);

	$gebaeude->bauteile()->hinzufuegen( $fenster );

	// Reduzieren der Wandfläche um die Fensterfläche.
	$wand->flaeche_reduzieren( $fensterflaeche );

	/**
	 * Heizkörpernischen
	 */
	if ( $energieausweis->heizkoerpernischen === 'vorhanden' ) {
		$heizkoerpernische_flaeche = berechne_heizkoerpernische_flaeche( $fensterflaeche );

		$heizkoerpernische = new Heizkoerpernische(
			name: sprintf( __( 'Heizkörpernischen Wand %s', 'wpenon' ), $wand->seite() ),
			flaeche: $heizkoerpernische_flaeche,
			uwert_wand: $wand->uwert(),
			himmelsrichtung: $himmelsrichtung,
			daemmung: $wand->daemmung()
		);

		$gebaeude->bauteile()->hinzufuegen( $heizkoerpernische );
		$wand->flaeche_reduzieren( $heizkoerpernische_flaeche );
	}

	/**
	 * Rolladenkästen.
	 */
	if ( substr( $energieausweis->rollladenkaesten, 0, 6 ) === 'innen_' ) { // Wir nehmen nur innenliegende Rolladenkästen.
		$rolladenkaesten_flaeche = berechne_rolladenkasten_flaeche( $fensterflaeche );
		$daemmung                = substr( $energieausweis->rollladenkaesten, 6 );
		$uwert_rolladenkaesten   = uwert( 'rollladen_' . $daemmung, $energieausweis->fenster_baujahr );

		$rolladenkasten = new Rolladenkasten(
			// translators: % s: Seite der Wand .
			name: sprintf( __( 'Rolladenkasten Wand %s', 'wpenon' ), $wand->seite() ),
			flaeche: $rolladenkaesten_flaeche,
			uwert: $uwert_rolladenkaesten,
			himmelsrichtung: $himmelsrichtung
		);

		$gebaeude->bauteile()->hinzufuegen( $rolladenkasten );
		$wand->flaeche_reduzieren( $rolladenkaesten_flaeche );
	}
}

/**
 * Sammlung aller Bauteile des Kellers.
 */
switch ( $energieausweis->keller ) {
	case 'beheizt':
		$keller = new Keller( $grundriss, $energieausweis->keller_groesse, $energieausweis->keller_hoehe );
		$gebaeude->keller( $keller );

		$gebaeude->bauteile()->hinzufuegen(
			new Kellerwand(
				name: __( 'Kellerwand', 'wpenon' ),
				flaeche: $gebaeude->keller()->wandseite_flaeche(),
				uwert: uwert( 'wand_' . $energieausweis->keller_bauart, $energieausweis->baujahr ),
				daemmung: $energieausweis->keller_daemmung,
			)
		);

		$kellerflaeche = $gebaeude->grundriss()->flaeche() * $energieausweis->keller_groesse / 100;

		$gebaeude->bauteile()->hinzufuegen(
			new Kellerboden(
				name: sprintf( __( 'Kellerboden', 'wpenon' ) ),
				flaeche: $kellerflaeche,
				uwert: uwert( 'boden_' . $energieausweis->boden_bauart, $energieausweis->baujahr ),
				daemmung: $energieausweis->anbauboden_daemmung,
			)
		);

		if ( $energieausweis->keller_groesse < 100 ) {
			$gebaeude->bauteile()->hinzufuegen(
				new Boden(
					name: sprintf( __( 'Boden', 'wpenon' ) ),
					flaeche: $gebaeude->grundriss()->flaeche() - $kellerflaeche,
					uwert: uwert( 'boden_' . $energieausweis->boden_bauart, $energieausweis->baujahr ),
					daemmung: $energieausweis->anbauboden_daemmung,
				)
			);
		}

		break;
	case 'unbeheizt':
		$keller = new Keller( $grundriss, $energieausweis->keller_groesse, $energieausweis->keller_hoehe );
		$gebaeude->keller( $keller );

		$kellerflaeche = $gebaeude->grundriss()->flaeche() * $energieausweis->keller_groesse / 100;

		$gebaeude->bauteile()->hinzufuegen(
			new Kellerboden(
				name: sprintf( __( 'Kellerboden', 'wpenon' ) ),
				flaeche: $kellerflaeche,
				uwert: uwert( 'boden_' . $energieausweis->boden_bauart, $energieausweis->baujahr ),
				daemmung: $energieausweis->anbauboden_daemmung,
			)
		);

		if ( $energieausweis->keller_groesse < 100 ) {
			$gebaeude->bauteile()->hinzufuegen(
				new Boden(
					name: sprintf( __( 'Boden', 'wpenon' ) ),
					flaeche: $gebaeude->grundriss()->flaeche() - $kellerflaeche,
					uwert: uwert( 'boden_' . $energieausweis->boden_bauart, $energieausweis->baujahr ),
					daemmung: $energieausweis->anbauboden_daemmung,
				)
			);
		}

		break;
	case 'nicht-vorhanden':
	default:
		$gebaeude->bauteile()->hinzufuegen(
			new Boden(
				name: sprintf( __( 'Boden', 'wpenon' ) ),
				flaeche: $gebaeude->grundriss()->flaeche(),
				uwert: uwert( 'boden_' . $energieausweis->boden_bauart, $energieausweis->baujahr ),
				daemmung: $energieausweis->anbauboden_daemmung,
			)
		);
}

$gebaeude->luftwechsel(
	new Luftwechsel(
		lueftungssystem: $energieausweis->l_info,
		bedarfsgefuehrt: $energieausweis->l_bedarfsgefuehrt,
		gebaeudedichtheit: $energieausweis->dichtheit ? 'din_4108_7' : 'andere',
		wirkunksgrad: (float) $energieausweis->l_wirkungsgrad
	)
);

$beheizte_bereiche = $energieausweis->h_standort === 'innerhalb' ? 'alles' : 'nichts';

switch ( $energieausweis->ww_info ) {
	case 'ww':
		$ww_zentral = false;
		$beheizte_bereiche = 'nichts';
		$mit_warmwasserspeicher = false;
		break;
	case 'h':
		$ww_zentral = true;

		$mit_warmwasserspeicher = false;
		break;
	
}

$heizung_im_beheizten_bereich = $energieausweis->h_standort === 'innerhalb' ? true : false;

$gebaeude->wasserversorgung(
	new Wasserversorgung(
		gebaeude: $gebaeude,
		zentral: $ww_zentral,
		heizung_im_beheizten_bereich: $heizung_im_beheizten_bereich,
		mit_warmwasserspeicher: $energieausweis->speicherung, // Neu - Ist ein Warmwasserspeicher vorhanden?
		mit_zirkulation: $energieausweis->verteilung_versorgung === 'mit' ? true : false,
	)
);

/**
* Heizsysteme
*/
$energietraeger_name = 'h_energietraeger_' . $energieausweis->h_erzeugung;
$energietraeger = $energieausweis->$energietraeger_name;

$gebaeude->heizsystem()->heizungsanlagen()->hinzufuegen(
	new Heizungsanlage(
		typ: $energieausweis->h_erzeugung,
		energietraeger: $energietraeger,
		auslegungstemperaturen: $energieausweis->h_auslegungstemperaturen,
		heizung_im_beheizten_bereich: $heizung_im_beheizten_bereich,
		prozentualer_anteil: $energieausweis->h_deckungsanteil ? $energieausweis->h_deckungsanteil : 100
	)
);

if ( $energieausweis->h2_erzeugung ) {
	$energietraeger_name = 'h2_energietraeger_' . $energieausweis->h2_erzeugung;
	$energietraeger = $energieausweis->$energietraeger_name;

	$gebaeude->heizsystem()->heizungsanlagen()->hinzufuegen(
		new Heizungsanlage(
			typ: $energieausweis->h2_erzeugung,
			energietraeger: $energietraeger,
			auslegungstemperaturen: $energieausweis->h2_auslegungstemperaturen,
			heizung_im_beheizten_bereich: $heizung_im_beheizten_bereich,
			prozentualer_anteil: $energieausweis->h2_deckungsanteil ? $energieausweis->h2_deckungsanteil : 100
		)
	);
}

if ( $energieausweis->h3_erzeugung ) {
	$energietraeger_name = 'h3_energietraeger_' . $energieausweis->h3_erzeugung;
	$energietraeger = $energieausweis->$energietraeger_name;

	$gebaeude->heizsystem()->heizungsanlagen()->hinzufuegen(
		new Heizungsanlage(
			typ: $energieausweis->h3_erzeugung,
			energietraeger: $energietraeger,
			auslegungstemperaturen: $energieausweis->h3_auslegungstemperaturen,
			heizung_im_beheizten_bereich: $heizung_im_beheizten_bereich,
			prozentualer_anteil: $energieausweis->h3_deckungsanteil ? $energieausweis->h2_deckungsanteil : 100
		)
	);
}

// Wir rechnen vorerst nur mit einem Übergabesystem.
if( $energieausweis->h_uebergabe === 'flaechenheizung' ){
	$gebaeude->heizsystem()->uebergabesysteme()->hinzufuegen(
		new Uebergabesystem(
			gebaeude: $gebaeude,
			typ: $energieausweis->h_uebergabe,
			auslegungstemperaturen: $energieausweis->h_uebergabe_auslegungstemperaturen,
			prozentualer_anteil: $energieausweis->h_uebergabe_anteil,
			mindestdaemmung: $energieausweis->h_uebergabe_mindestdaemmung
		)
	);		
} else {
	$gebaeude->heizsystem()->uebergabesysteme()->hinzufuegen(
		new Uebergabesystem(
			gebaeude: $gebaeude,
			typ: $energieausweis->h_uebergabe,
			auslegungstemperaturen: $energieausweis->h_uebergabe_auslegungstemperaturen,
			prozentualer_anteil: $energieausweis->h_uebergabe_anteil
		)
	);
}

// TODO: Bestimmung des Nutzenergiebedarfs (1135)
// TODO: Ermittlung der Summernwerte über 12 Monate (1136)
// TODO: Anlagentechnik (1137)
// TODO: Berechnung der Wirkungsgrade der Wärmeverluste (Aufwandszahlen) von Übergabe, Verteilung, Speicherung, Erzeuger (1138)
// TODO: Verteilung der Heizung (1139)
// TODO: Speicherung Heizung (1140)
// TODO: 1141 ???
// TODO: Trinkwarmwasser (1142)
// TODO: Berechnung der Erzeugernutzwärmeabgabe Qoutg (1143)
// TODO: Energieerzeuger (1144)


// $monate           = wpenon_get_table_results( 'monate' );
// $solar_gewinn_mpk = 0.9 * 1.0 * 0.9 * 0.7 * wpenon_immoticket24_get_g_wert( $energieausweis->fenster_bauart ); // Solar gewinn neu

// // Wie wird EHCE auf gesehen auf alle Übergabesysteme berechnet
// // $calculations['ehce'] = $gebaeude->heizsystem()->uebergabesysteme()->ehce();

// $calculations['monate'] = array();

// foreach ( $monate as $monat => $monatsdaten ) {
// $calculations['monate'][ $monat ] = array();

// $calculations['monate'][ $monat ]['name']       = $monatsdaten->name;
// $calculations['monate'][ $monat ]['tage']       = absint( $monatsdaten->tage );
// $calculations['monate'][ $monat ]['temperatur'] = floatval( $monatsdaten->temperatur );

// Umrechnungsfakor FUM
// $fum = fum( $monat );

// Solare Gewinne Qs
// $calculations['monate'][ $monat ]['qi_solar'] = 0.0;
// foreach ( $calculations['bauteile'] as $slug => $data ) {
// if ( $data['typ'] == 'fenster' ) {
// $winkel           = isset( $data['winkel'] ) ? $data['winkel'] : 90.0;
// $str90            = 'w_' . $data['richtung'] . '90';
// $strahlungsfaktor = $monatsdaten->$str90;

// $calculations['monate'][ $monat ]['qi_solar'] += $strahlungsfaktor * $solar_gewinn_mpk * $data['a'] * 0.024 * $calculations['monate'][ $monat ]['tage']; // Neu
// }
// }
// unset( $data );

// Wärmesenken als Leistung in W (Berechnung von Ph sink und Ph*sink)

// $calculations['monate'][ $monat ]['psh_sink'] = $gebaeude->ph_sink_monat( $monat ) - ( $gebaeude->qi_prozesse_monat( $monat ) + ( 0.5 * $calculations['monate'][ $monat ]['qi_solar'] ) * $fum );
// $calculations['monate'][ $monat ]['psh_sink'] = $calculations['monate'][ $monat ]['psh_sink'] < 0 ? 0 : $calculations['monate'][ $monat ]['psh_sink'];

// Interne Wärmequelle infolge von Heizung
// $calculations['monate'][ $monat ]['qi_heizung'] = $calculations['monate'][ $monat ]['psh_sink'] * ( $mittlere_belastung->ßem1( $monat ) / $mittlere_belastung->ßemMax() ) * $gebaeude->heizsystem()->fa_h() / $fum;

// Zusammenfassung aller internen Wärmequellen
// $calculations['monate'][ $monat ]['qi_gesamt'] = $gebaeude->qi_prozesse_monat( $monat ) + $calculations['monate'][ $monat ]['qi_solar'] + $calculations['monate'][ $monat ]['qi_wasser'] + $calculations['monate'][ $monat ]['qi_heizung'];

// Berechnung von PH Source (in den Dokumenten auch "pi" genannt).
// $calculations['monate'][ $monat ]['ph_source'] = $calculations['monate'][ $monat ]['qi_gesamt'] * $fum;

// Berechnung das monatlichen Wärmequellen-/Wärmesenken-Verhältnis ym.
// $calculations['monate'][ $monat ]['ym'] = $calculations['monate'][ $monat ]['ph_source'] / $gebaeude->ph_sink_monat( $monat );

// $calculations['monate'][ $monat ]['nm']    = ( new Ausnutzungsgrad( $gebaeude->tau(), $calculations['monate'][ $monat ]['ym'] ) )->nm();
// $calculations['monate'][ $monat ]['nm_ym'] = ( 1 - $calculations['monate'][ $monat ]['nm'] * $calculations['monate'][ $monat ]['ym'] );
// $calculations['monate'][ $monat ]['ßhm']   = $mittlere_belastung->ßem1( $monat ) * $calculations['monate'][ $monat ]['nm_ym'];

// Berechnung der Heizustunden pro Monat
// if ( $calculations['monate'][ $monat ]['ßhm'] > 0.05 ) {
// $calculations['monate'][ $monat ]['thm'] = $calculations['monate'][ $monat ]['tage'] * 24;
// } else {
// $calculations['monate'][ $monat ]['thm'] = ( $calculations['monate'][ $monat ]['ßhm'] / 0.05 ) * $calculations['monate'][ $monat ]['tage'] * 24;
// }

// Berechnung der Heizwärmebedarfs pro Monat
// $calculations['monate'][ $monat ]['qh'] = $gebaeude->ph_sink_monat( $monat ) * $calculations['monate'][ $monat ]['nm_ym'] * $calculations['monate'][ $monat ]['thm'] / 1000;

// **
// * Aufsummierung zu Jahresergebnissen
// */

// Wo ist sind die Wärmeverluste in folge von Transmission?
// Wo ist sind die Wärmeverluste in folge von Lüftung?

// $calculations['ßh'] += $calculations['monate'][ $monat ]['ßhm'];

// Berechnung des jährlichen Warmwasserbedarfs
// $calculations['qw_b'] += $gebaeude->qwb_monat( $monat );

// Interne Wärmequellen infolge von Solar
// $calculations['qi_solar'] += $calculations['monate'][ $monat ]['qi_solar'];

// Interne Wärmequellen infolge von Warmwasser
// $calculations['qi_wasser'] += $calculations['monate'][ $monat ]['qi_wasser'];

// Interne Wärmequellen infolge von Heizung
// $calculations['qi_heizung'] += $calculations['monate'][ $monat ]['qi_heizung'];

// Heizwärmebedarf / Nutzenergie im Jahr
// $calculations['qh'] += $calculations['monate'][ $monat ]['qh'];

// Heizstunden im Jahr
// $calculations['thm'] += $calculations['monate'][ $monat ]['thm'];

// if ( $gebaeude->anzahl_wohnungen() === 1 ) {
// $flna = 1;
// } else {
// $flna = 1 - ( ( 10 - $calculations['monate'][ $monat ]['temperatur'] ) / 22 );
// }

// $calculations['monate'][ $monat ]['trl'] = 24 - $flna * 7;

// if ( $calculations['monate'][ $monat ]['trl'] < 17 ) {
// $calculations['monate'][ $monat ]['trl'] = 17;
// }

// $calculations['monate'][ $monat ]['ith_rl'] = $calculations['monate'][ $monat ]['thm'] * 0.042 * $calculations['monate'][ $monat ]['trl'];
// $x = 0;


$mittlere_belastung_korrekturfaktor = new Mittlere_Belastung_Korrekturfaktor( true, 1, '90/70', 0.4 );
$test = $mittlere_belastung_korrekturfaktor->fßd();

// $calculations['ith_rl'] += $calculations['monate'][ $monat ]['ith_rl'];
// }

// $gebaeude->ph_sink_monat( 'november' );

// // Interne Wärmequellen infolge von Personen
// $calculations['qi_prozesse'] += $gebaeude->qi_prozesse();

// $calculations['qw']           = 12.5 * $gebaeude->nutzflaeche();
// $calculations['qw_reference'] = 12.5 * $gebaeude->nutzflaeche();

// $calculations['ql']           = 0.0;
// $calculations['ql_reference'] = 0.0;

// $calculations['qh_b'] = $calculations['qh'] / $gebaeude->nutzflaeche();
// $calculations['qw_b'] = 12.5;
// $calculations['ql_b'] = 0.0;

// /*************************************************
// * ANLAGENDATEN
// */

// $calculations['anlagendaten'] = array();
// $calculations['verteilung']   = array();
// $calculations['speicherung']  = array();
// $calculations['uebergabe']    = array();

// $aaa = $energieausweis->h_erzeugung;

// $h_energietraeger_name  = 'h_energietraeger_' . $energieausweis->h_erzeugung;
// $h_energietraeger_value = $energieausweis->$h_energietraeger_name;

// $h_erzeugung                        = wpenon_get_table_results(
// $tableNames->h_erzeugung,
// array(
// 'bezeichnung' => array(
// 'value'   => $energieausweis->h_erzeugung,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// $h_energietraeger                   = wpenon_get_table_results(
// $tableNames->energietraeger,
// array(
// 'bezeichnung' => array(
// 'value'   => $h_energietraeger_value,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// $h_yearkey                          = wpenon_immoticket24_make_yearkey( $energieausweis->h_baujahr, $tableNames->h_erzeugung );
// list($h_ep150, $h_ep500, $h_ep2500) = wpenon_immoticket24_make_anlagenkeys( 'ep', $h_yearkey );
// list($h_he150, $h_he500, $h_he2500) = wpenon_immoticket24_make_anlagenkeys( 'he', $h_yearkey );

// $calculations['anlagendaten']['h'] = array(
// 'name'                   => $h_erzeugung->name,
// 'slug'                   => $h_erzeugung->bezeichnung,
// 'art'                    => 'heizung',
// 'typ'                    => $h_erzeugung->typ,
// 'baujahr'                => $energieausweis->h_baujahr,
// 'energietraeger'         => $h_energietraeger->name,
// 'energietraeger_slug'    => $h_energietraeger->bezeichnung,
// 'energietraeger_primaer' => $energieausweis->h_custom ? floatval( $energieausweis->h_custom_primaer ) : floatval( $h_energietraeger->primaer ),
// 'energietraeger_co2'     => $energieausweis->h_custom_2 ? floatval( $energieausweis->h_custom_co2 ) : floatval( $h_energietraeger->co2 ),
// 'speicher_slug'          => $h_erzeugung->speicher,
// 'uebergabe_slug'         => $h_erzeugung->uebergabe,
// 'heizkreistemperatur'    => $h_erzeugung->hktemp,
// 'aufwandszahl'           => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h_erzeugung->$h_ep150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h_erzeugung->$h_ep500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h_erzeugung->$h_ep2500,
// ),
// )
// ),
// 'hilfsenergie'           => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h_erzeugung->$h_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h_erzeugung->$h_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h_erzeugung->$h_he2500,
// ),
// )
// ),
// 'deckungsanteil'         => 100,
// );
// $h_max_anteil                      = 'h';
// $anteilsumme                       = 100;

// if ( $energieausweis->h2_info ) {
// if ( $energieausweis->h_deckungsanteil > 0 ) {
// $calculations['anlagendaten']['h']['deckungsanteil'] = $energieausweis->h_deckungsanteil;
// } else {
// unset( $calculations['anlagendaten']['h'] );
// }

// $anteilsumme = $energieausweis->h_deckungsanteil;

// $h2_energietraeger_name  = 'h2_energietraeger_' . $energieausweis->h2_erzeugung;
// $h2_energietraeger_value = $energieausweis->$h2_energietraeger_name;

// if ( $energieausweis->h2_deckungsanteil > 0 ) {
// $h2_erzeugung      = wpenon_get_table_results(
// $tableNames->h_erzeugung,
// array(
// 'bezeichnung' => array(
// 'value'   => $energieausweis->h2_erzeugung,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// $h2_energietraeger = wpenon_get_table_results(
// $tableNames->energietraeger,
// array(
// 'bezeichnung' => array(
// 'value'   => $h2_energietraeger_value,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );

// $h2_yearkey = wpenon_immoticket24_make_yearkey( $energieausweis->h2_baujahr, $tableNames->h_erzeugung );

// list($h2_ep150, $h2_ep500, $h2_ep2500) = wpenon_immoticket24_make_anlagenkeys( 'ep', $h2_yearkey );
// list($h2_he150, $h2_he500, $h2_he2500) = wpenon_immoticket24_make_anlagenkeys( 'he', $h2_yearkey );
// $calculations['anlagendaten']['h2']    = array(
// 'name'                   => $h2_erzeugung->name,
// 'slug'                   => $h2_erzeugung->bezeichnung,
// 'art'                    => 'heizung',
// 'typ'                    => $h2_erzeugung->typ,
// 'baujahr'                => $energieausweis->h2_baujahr,
// 'energietraeger'         => $h2_energietraeger->name,
// 'energietraeger_slug'    => $h2_energietraeger->bezeichnung,
// 'energietraeger_primaer' => $energieausweis->h2_custom ? floatval( $energieausweis->h2_custom_primaer ) : floatval( $h2_energietraeger->primaer ),
// 'energietraeger_co2'     => $energieausweis->h2_custom_2 ? floatval( $energieausweis->h2_custom_co2 ) : floatval( $h2_energietraeger->co2 ),
// 'speicher_slug'          => $h2_erzeugung->speicher,
// 'uebergabe_slug'         => $h2_erzeugung->uebergabe,
// 'heizkreistemperatur'    => $h2_erzeugung->hktemp,
// 'aufwandszahl'           => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h2_erzeugung->$h2_ep150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h2_erzeugung->$h2_ep500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h2_erzeugung->$h2_ep2500,
// ),
// )
// ),
// 'hilfsenergie'           => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h2_erzeugung->$h2_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h2_erzeugung->$h2_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h2_erzeugung->$h2_he2500,
// ),
// )
// ),
// 'deckungsanteil'         => $energieausweis->h2_deckungsanteil,
// );

// $anteilsumme += $calculations['anlagendaten']['h2']['deckungsanteil'];

// if ( $calculations['anlagendaten']['h2']['deckungsanteil'] > $calculations['anlagendaten']['h']['deckungsanteil'] ) {
// $h_max_anteil = 'h2';
// }
// }

// if ( $energieausweis->h3_info && $energieausweis->h3_deckungsanteil > 0 ) {
// $h3_energietraeger_name  = 'h3_energietraeger_' . $energieausweis->h3_erzeugung;
// $h3_energietraeger_value = $energieausweis->$h3_energietraeger_name;

// $h3_erzeugung      = wpenon_get_table_results(
// $tableNames->h_erzeugung,
// array(
// 'bezeichnung' => array(
// 'value'   => $energieausweis->h3_erzeugung,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// $h3_energietraeger = wpenon_get_table_results(
// $tableNames->energietraeger,
// array(
// 'bezeichnung' => array(
// 'value'   => $h3_energietraeger_value,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );

// $h3_yearkey = wpenon_immoticket24_make_yearkey( $energieausweis->h3_baujahr, $tableNames->h_erzeugung );

// list($h3_ep150, $h3_ep500, $h3_ep2500) = wpenon_immoticket24_make_anlagenkeys( 'ep', $h3_yearkey );
// list($h3_he150, $h3_he500, $h3_he2500) = wpenon_immoticket24_make_anlagenkeys( 'he', $h3_yearkey );
// $calculations['anlagendaten']['h3']    = array(
// 'name'                   => $h3_erzeugung->name,
// 'slug'                   => $h3_erzeugung->bezeichnung,
// 'art'                    => 'heizung',
// 'typ'                    => $h3_erzeugung->typ,
// 'baujahr'                => $energieausweis->h3_baujahr,
// 'energietraeger'         => $h3_energietraeger->name,
// 'energietraeger_slug'    => $h3_energietraeger->bezeichnung,
// 'energietraeger_primaer' => $energieausweis->h3_custom ? floatval( $energieausweis->h3_custom_primaer ) : floatval( $h3_energietraeger->primaer ),
// 'energietraeger_co2'     => $energieausweis->h3_custom_2 ? floatval( $energieausweis->h3_custom_co2 ) : floatval( $h3_energietraeger->co2 ),
// 'speicher_slug'          => $h3_erzeugung->speicher,
// 'uebergabe_slug'         => $h3_erzeugung->uebergabe,
// 'heizkreistemperatur'    => $h3_erzeugung->hktemp,
// 'aufwandszahl'           => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h3_erzeugung->$h3_ep150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h3_erzeugung->$h3_ep500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h3_erzeugung->$h3_ep2500,
// ),
// )
// ),
// 'hilfsenergie'           => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h3_erzeugung->$h3_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h3_erzeugung->$h3_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h3_erzeugung->$h3_he2500,
// ),
// )
// ),
// 'deckungsanteil'         => $energieausweis->h3_deckungsanteil,
// );

// $anteilsumme += $calculations['anlagendaten']['h3']['deckungsanteil'];

// if ( $calculations['anlagendaten']['h3']['deckungsanteil'] > $calculations['anlagendaten']['h2']['deckungsanteil'] && $calculations['anlagendaten']['h3']['deckungsanteil'] > $calculations['anlagendaten']['h1']['deckungsanteil'] ) {
// $h_max_anteil = 'h3';
// }
// }
// }

// if ( $anteilsumme != 100 ) {
// foreach ( $calculations['anlagendaten'] as $slug => $data ) {
// $calculations['anlagendaten'][ $slug ]['deckungsanteil'] *= 100 / $anteilsumme;
// }
// unset( $data );
// }

// $h_uebergabe_slug = $calculations['anlagendaten'][ $h_max_anteil ]['uebergabe_slug'];
// $h_uebergabe      = wpenon_get_table_results(
// $tableNames->h_uebergabe,
// array(
// 'bezeichnung' => array(
// 'value'   => $h_uebergabe_slug,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// if ( $h_uebergabe ) {
// $hu_yearkey                            = wpenon_immoticket24_make_yearkey( $calculations['anlagendaten'][ $h_max_anteil ]['baujahr'], $tableNames->h_uebergabe );
// list($hu_wv150, $hu_wv500, $hu_wv2500) = wpenon_immoticket24_make_anlagenkeys( 'wv', $hu_yearkey );
// $calculations['uebergabe']['h']        = array(
// 'name'           => $h_uebergabe->name,
// 'art'            => 'heizung',
// 'baujahr'        => $calculations['anlagendaten'][ $h_max_anteil ]['baujahr'],
// 'waermeverluste' => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h_uebergabe->$hu_wv150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h_uebergabe->$hu_wv500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h_uebergabe->$hu_wv2500,
// ),
// )
// ),
// );
// }

// $h_verteilung_slug = $calculations['anlagendaten'][ $h_max_anteil ]['typ'];
// if ( $h_verteilung_slug == 'zentral' ) {
// $h_verteilung_slug .= '_' . ( $calculations['anlagendaten'][ $h_max_anteil ]['heizkreistemperatur'] == '70/55°' ? '7055' : '5545' );
// }
// $h_verteilung = wpenon_get_table_results(
// 'h_verteilung2019',
// array(
// 'bezeichnung' => array(
// 'value'   => $h_verteilung_slug,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// if ( $h_verteilung ) {
// $hv_yearkey                            = wpenon_immoticket24_make_yearkey( $energieausweis->verteilung_baujahr, 'h_verteilung2019', $energieausweis->verteilung_gedaemmt );
// list($hv_wv150, $hv_wv500, $hv_wv2500) = wpenon_immoticket24_make_anlagenkeys( 'wv', $hv_yearkey );
// list($hv_he150, $hv_he500, $hv_he2500) = wpenon_immoticket24_make_anlagenkeys( 'he', $hv_yearkey );
// $calculations['verteilung']['h']       = array(
// 'name'           => $h_verteilung->name,
// 'art'            => 'heizung',
// 'baujahr'        => $energieausweis->verteilung_baujahr,
// 'waermeverluste' => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h_verteilung->$hv_wv150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h_verteilung->$hv_wv500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h_verteilung->$hv_wv2500,
// ),
// )
// ),
// 'hilfsenergie'   => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h_verteilung->$hv_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h_verteilung->$hv_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h_verteilung->$hv_he2500,
// ),
// )
// ),
// );
// }

// if ( $energieausweis->speicherung ) {
// $h_speicherung_slug = $calculations['anlagendaten'][ $h_max_anteil ]['speicher_slug'];
// $h_speicherung      = wpenon_get_table_results(
// 'h_speicherung',
// array(
// 'bezeichnung' => array(
// 'value'   => $h_speicherung_slug,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// if ( $h_speicherung ) {
// $hs_yearkey                            = wpenon_immoticket24_make_yearkey( $energieausweis->speicherung_baujahr, 'h_speicherung' );
// list($hs_wv150, $hs_wv500, $hs_wv2500) = wpenon_immoticket24_make_anlagenkeys( 'wv', $hs_yearkey );
// list($hs_he150, $hs_he500, $hs_he2500) = wpenon_immoticket24_make_anlagenkeys( 'he', $hs_yearkey );
// $calculations['speicherung']['h']      = array(
// 'art'            => 'heizung',
// 'name'           => $h_speicherung->name,
// 'baujahr'        => $energieausweis->speicherung_baujahr,
// 'waermeverluste' => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h_speicherung->$hs_wv150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h_speicherung->$hs_wv500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h_speicherung->$hs_wv2500,
// ),
// )
// ),
// 'hilfsenergie'   => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h_speicherung->$hs_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h_speicherung->$hs_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h_speicherung->$hs_he2500,
// ),
// )
// ),
// );
// }
// }

// if ( 'unbekannt' === $energieausweis->ww_info ) {
// This kind of heater can't be set to pauschal, because there is no value for it in schema logic.
// if ( ! wpenon_is_water_independend_heater( $energieausweis->h_erzeugung ) ) {
// $energieausweis->ww_info = 'h';
// }

// $prefix_ww = 'h';
// } else {
// $prefix_ww = $energieausweis->ww_info;
// }

// $ww_erzeugung      = $prefix_ww . '_erzeugung';
// $ww_energietraeger = $prefix_ww . '_energietraeger_' . $energieausweis->$ww_erzeugung;
// $ww_baujahr        = $prefix_ww . '_baujahr';

// $ww_erzeugung      = wpenon_get_table_results(
// 'ww_erzeugung2019',
// array(
// 'bezeichnung' => array(
// 'value'   => $energieausweis->$ww_erzeugung,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// $ww_energietraeger = wpenon_get_table_results(
// $tableNames->energietraeger,
// array(
// 'bezeichnung' => array(
// 'value'   => $energieausweis->$ww_energietraeger,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );


// $ww_yearkey                               = wpenon_immoticket24_make_yearkey( $energieausweis->$ww_baujahr, 'ww_erzeugung2019' );
// list($ww_ep150, $ww_ep500, $ww_ep2500)    = wpenon_immoticket24_make_anlagenkeys( 'ep', $ww_yearkey );
// list($ww_he150, $ww_he500, $ww_he2500)    = wpenon_immoticket24_make_anlagenkeys( 'he', $ww_yearkey );
// list($ww_hwg150, $ww_hwg500, $ww_hwg2500) = wpenon_immoticket24_make_anlagenkeys( 'hwg', $ww_yearkey );
// $calculations['anlagendaten']['ww']       = array(
// 'name'                   => $ww_erzeugung->name,
// 'slug'                   => $ww_erzeugung->bezeichnung,
// 'art'                    => 'warmwasser',
// 'typ'                    => $ww_erzeugung->typ,
// 'baujahr'                => $energieausweis->$ww_baujahr,
// 'energietraeger'         => $ww_energietraeger->name,
// 'energietraeger_slug'    => $ww_energietraeger->bezeichnung,
// 'energietraeger_primaer' => floatval( $ww_energietraeger->primaer ),
// 'energietraeger_co2'     => floatval( $ww_energietraeger->co2 ),
// 'speicher_slug'          => $ww_erzeugung->speicher,
// 'aufwandszahl'           => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_erzeugung->$ww_ep150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_erzeugung->$ww_ep500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_erzeugung->$ww_ep2500,
// ),
// )
// ),
// 'hilfsenergie'           => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_erzeugung->$ww_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_erzeugung->$ww_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_erzeugung->$ww_he2500,
// ),
// )
// ),
// 'heizwaermegewinne'      => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_erzeugung->$ww_hwg150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_erzeugung->$ww_hwg500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_erzeugung->$ww_hwg2500,
// ),
// )
// ),
// 'deckungsanteil'         => 100,
// );
// $ww_max_anteil                            = 'ww';

// $ww_verteilung_slug = $calculations['anlagendaten'][ $ww_max_anteil ]['typ'];
// if ( $ww_verteilung_slug == 'zentral' ) {
// $ww_verteilung_slug .= '_' . $energieausweis->verteilung_versorgung;
// }
// $ww_verteilung = wpenon_get_table_results(
// 'ww_verteilung',
// array(
// 'bezeichnung' => array(
// 'value'   => $ww_verteilung_slug,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// if ( $ww_verteilung ) {
// $wwv_yearkey                                 = wpenon_immoticket24_make_yearkey( $energieausweis->verteilung_baujahr, 'ww_verteilung', $energieausweis->verteilung_gedaemmt );
// list($wwv_wv150, $wwv_wv500, $wwv_wv2500)    = wpenon_immoticket24_make_anlagenkeys( 'wv', $wwv_yearkey );
// list($wwv_he150, $wwv_he500, $wwv_he2500)    = wpenon_immoticket24_make_anlagenkeys( 'he', $wwv_yearkey );
// list($wwv_hwg150, $wwv_hwg500, $wwv_hwg2500) = wpenon_immoticket24_make_anlagenkeys( 'hwg', $wwv_yearkey );
// $calculations['verteilung']['ww']            = array(
// 'name'              => $ww_verteilung->name,
// 'art'               => 'warmwasser',
// 'baujahr'           => $energieausweis->verteilung_baujahr,
// 'waermeverluste'    => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_verteilung->$wwv_wv150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_verteilung->$wwv_wv500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_verteilung->$wwv_wv2500,
// ),
// )
// ),
// 'hilfsenergie'      => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_verteilung->$wwv_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_verteilung->$wwv_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_verteilung->$wwv_he2500,
// ),
// )
// ),
// 'heizwaermegewinne' => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_verteilung->$wwv_hwg150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_verteilung->$wwv_hwg500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_verteilung->$wwv_hwg2500,
// ),
// )
// ),
// );
// }

// if ( $energieausweis->speicherung ) {
// $ww_speicherung_slug = $calculations['anlagendaten'][ $ww_max_anteil ]['speicher_slug'];
// if ( $ww_speicherung_slug == 'zentral' ) {
// $ww_speicherung_slug .= '_' . $energieausweis->speicherung_standort;
// }
// $ww_speicherung = wpenon_get_table_results(
// 'ww_speicherung',
// array(
// 'bezeichnung' => array(
// 'value'   => $ww_speicherung_slug,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// if ( $ww_speicherung ) {
// $wws_yearkey                                 = wpenon_immoticket24_make_yearkey( $energieausweis->speicherung_baujahr, 'ww_speicherung' );
// list($wws_wv150, $wws_wv500, $wws_wv2500)    = wpenon_immoticket24_make_anlagenkeys( 'wv', $wws_yearkey );
// list($wws_he150, $wws_he500, $wws_he2500)    = wpenon_immoticket24_make_anlagenkeys( 'he', $wws_yearkey );
// list($wws_hwg150, $wws_hwg500, $wws_hwg2500) = wpenon_immoticket24_make_anlagenkeys( 'hwg', $wws_yearkey );
// $calculations['speicherung']['ww']           = array(
// 'name'              => $ww_speicherung->name,
// 'art'               => 'warmwasser',
// 'baujahr'           => $energieausweis->speicherung_baujahr,
// 'waermeverluste'    => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_speicherung->$wws_wv150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_speicherung->$wws_wv500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_speicherung->$wws_wv2500,
// ),
// )
// ),
// 'hilfsenergie'      => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_speicherung->$wws_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_speicherung->$wws_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_speicherung->$wws_he2500,
// ),
// )
// ),
// 'heizwaermegewinne' => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_speicherung->$wws_hwg150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_speicherung->$wws_hwg500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_speicherung->$wws_hwg2500,
// ),
// )
// ),
// );
// }
// }

// if ( $energieausweis->l_info == 'anlage' ) {
// $l_erzeugung                           = wpenon_get_table_results(
// $tableNames->l_erzeugung,
// array(
// 'bezeichnung' => array(
// 'value'   => $energieausweis->l_erzeugung,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// $l_energietraeger                      = wpenon_get_table_results(
// $tableNames->energietraeger,
// array(
// 'bezeichnung' => array(
// 'value'   => 'strom',
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// $l_yearkey                             = wpenon_immoticket24_make_yearkey( $energieausweis->l_baujahr, $tableNames->l_erzeugung );
// list($l_he150, $l_he500, $l_he2500)    = wpenon_immoticket24_make_anlagenkeys( 'he', $l_yearkey );
// list($l_hwg150, $l_hwg500, $l_hwg2500) = wpenon_immoticket24_make_anlagenkeys( 'hwg', $l_yearkey );
// $calculations['anlagendaten']['l']     = array(
// 'name'                   => $l_erzeugung->name,
// 'slug'                   => $l_erzeugung->bezeichnung,
// 'art'                    => 'lueftung',
// 'typ'                    => $l_erzeugung->bezeichnung,
// 'baujahr'                => $energieausweis->l_baujahr,
// 'energietraeger'         => $l_energietraeger->name,
// 'energietraeger_slug'    => $l_energietraeger->bezeichnung,
// 'energietraeger_primaer' => floatval( $l_energietraeger->primaer ),
// 'energietraeger_co2'     => floatval( $l_energietraeger->co2 ),
// 'hilfsenergie'           => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $l_erzeugung->$l_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $l_erzeugung->$l_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $l_erzeugung->$l_he2500,
// ),
// )
// ),
// 'heizwaermegewinne'      => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $l_erzeugung->$l_hwg150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $l_erzeugung->$l_hwg500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $l_erzeugung->$l_hwg2500,
// ),
// )
// ),
// 'deckungsanteil'         => 100,
// );

// $l_verteilung_slug = $calculations['anlagendaten']['l']['typ'];
// if ( $l_verteilung_slug == 'mitgewinnung' ) {
// $l_verteilung_slug .= '_' . $energieausweis->l_standort;
// }
// $l_verteilung = wpenon_get_table_results(
// $tableNames->l_verteilung,
// array(
// 'bezeichnung' => array(
// 'value'   => $l_verteilung_slug,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// if ( $l_verteilung ) {
// $lv_yearkey                            = wpenon_immoticket24_make_yearkey( $energieausweis->l_baujahr, $tableNames->l_verteilung );
// list($lv_wv150, $lv_wv500, $lv_wv2500) = wpenon_immoticket24_make_anlagenkeys( 'wv', $lv_yearkey );
// list($lv_he150, $lv_he500, $lv_he2500) = wpenon_immoticket24_make_anlagenkeys( 'he', $lv_yearkey );
// $calculations['verteilung']['l']       = array(
// 'name'           => $l_verteilung->name,
// 'art'            => 'lueftung',
// 'baujahr'        => $energieausweis->l_baujahr,
// 'waermeverluste' => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $l_verteilung->$lv_wv150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $l_verteilung->$lv_wv500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $l_verteilung->$lv_wv2500,
// ),
// )
// ),
// 'hilfsenergie'   => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $l_verteilung->$lv_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $l_verteilung->$lv_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $l_verteilung->$lv_he2500,
// ),
// )
// ),
// );
// }
// }

// // Referenzgebäude
// $calculations['anlagendaten_reference'] = array();
// $calculations['verteilung_reference']   = array();
// $calculations['speicherung_reference']  = array();
// $calculations['uebergabe_reference']    = array();

// $h_reference_erzeugung      = wpenon_get_table_results(
// $tableNames->h_erzeugung,
// array(
// 'bezeichnung' => array(
// 'value'   => 'brennwertkesselverbessert',
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// $h_reference_energietraeger = wpenon_get_table_results(
// $tableNames->energietraeger,
// array(
// 'bezeichnung' => array(
// 'value'   => 'heizoel',
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// $h_reference_baujahr        = absint( wpenon_get_reference_date( 'Y', $energieausweis ) );
// $h_reference_yearkey        = wpenon_immoticket24_make_yearkey( $h_reference_baujahr, $tableNames->h_erzeugung );
// list($h_reference_ep150, $h_reference_ep500, $h_reference_ep2500) = wpenon_immoticket24_make_anlagenkeys( 'ep', $h_reference_yearkey );
// list($h_reference_he150, $h_reference_he500, $h_reference_he2500) = wpenon_immoticket24_make_anlagenkeys( 'he', $h_reference_yearkey );
// $calculations['anlagendaten_reference']['h']                      = array(
// 'name'                   => $h_reference_erzeugung->name,
// 'slug'                   => $h_reference_erzeugung->bezeichnung,
// 'art'                    => 'heizung',
// 'typ'                    => $h_reference_erzeugung->typ,
// 'baujahr'                => $h_reference_baujahr,
// 'energietraeger'         => $h_reference_energietraeger->name,
// 'energietraeger_slug'    => $h_reference_energietraeger->bezeichnung,
// 'energietraeger_primaer' => floatval( $h_reference_energietraeger->primaer ),
// 'energietraeger_co2'     => floatval( $h_reference_energietraeger->co2 ),
// 'speicher_slug'          => $h_reference_erzeugung->speicher,
// 'uebergabe_slug'         => $h_reference_erzeugung->uebergabe,
// 'heizkreistemperatur'    => $h_reference_erzeugung->hktemp,
// 'aufwandszahl'           => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h_reference_erzeugung->$h_reference_ep150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h_reference_erzeugung->$h_reference_ep500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h_reference_erzeugung->$h_reference_ep2500,
// ),
// )
// ),
// 'hilfsenergie'           => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h_reference_erzeugung->$h_reference_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h_reference_erzeugung->$h_reference_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h_reference_erzeugung->$h_reference_he2500,
// ),
// )
// ),
// 'deckungsanteil'         => 100,
// );

// $h_uebergabe_reference_slug = $calculations['anlagendaten_reference']['h']['uebergabe_slug'];
// $h_uebergabe_reference      = wpenon_get_table_results(
// $tableNames->h_uebergabe,
// array(
// 'bezeichnung' => array(
// 'value'   => $h_uebergabe_reference_slug,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// if ( $h_uebergabe_reference ) {
// $hu_reference_baujahr = $h_reference_baujahr;
// $hu_reference_yearkey = wpenon_immoticket24_make_yearkey( $hu_reference_baujahr, $tableNames->h_uebergabe );
// list($hu_reference_wv150, $hu_reference_wv500, $hu_reference_wv2500) = wpenon_immoticket24_make_anlagenkeys( 'wv', $hu_reference_yearkey );
// $calculations['uebergabe_reference']['h']                            = array(
// 'name'           => $h_uebergabe_reference->name,
// 'art'            => 'heizung',
// 'baujahr'        => $hu_reference_baujahr,
// 'waermeverluste' => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h_uebergabe_reference->$hu_reference_wv150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h_uebergabe_reference->$hu_reference_wv500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h_uebergabe_reference->$hu_reference_wv2500,
// ),
// )
// ),
// );
// }

// $h_verteilung_reference_slug = $calculations['anlagendaten_reference']['h']['typ'];
// if ( $h_verteilung_reference_slug == 'zentral' ) {
// $h_verteilung_reference_slug .= '_' . ( $calculations['anlagendaten_reference']['h']['heizkreistemperatur'] == '70/55°' ? '7055' : '5545' );
// }
// $h_verteilung_reference = wpenon_get_table_results(
// 'h_verteilung2019',
// array(
// 'bezeichnung' => array(
// 'value'   => $h_verteilung_reference_slug,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// if ( $h_verteilung_reference ) {
// $hv_reference_baujahr = $h_reference_baujahr;
// $hv_reference_yearkey = wpenon_immoticket24_make_yearkey( $hv_reference_baujahr, 'h_verteilung2019', true );
// list($hv_reference_wv150, $hv_reference_wv500, $hv_reference_wv2500) = wpenon_immoticket24_make_anlagenkeys( 'wv', $hv_reference_yearkey );
// list($hv_reference_he150, $hv_reference_he500, $hv_reference_he2500) = wpenon_immoticket24_make_anlagenkeys( 'he', $hv_reference_yearkey );
// $calculations['verteilung_reference']['h']                           = array(
// 'name'           => $h_verteilung_reference->name,
// 'art'            => 'heizung',
// 'baujahr'        => $hv_reference_baujahr,
// 'waermeverluste' => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h_verteilung_reference->$hv_reference_wv150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h_verteilung_reference->$hv_reference_wv500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h_verteilung_reference->$hv_reference_wv2500,
// ),
// )
// ),
// 'hilfsenergie'   => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h_verteilung_reference->$hv_reference_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h_verteilung_reference->$hv_reference_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h_verteilung_reference->$hv_reference_he2500,
// ),
// )
// ),
// );
// }

// $h_speicherung_reference_slug = $calculations['anlagendaten_reference']['h']['speicher_slug'];
// $h_speicherung_reference      = wpenon_get_table_results(
// 'h_speicherung',
// array(
// 'bezeichnung' => array(
// 'value'   => $h_speicherung_reference_slug,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// if ( $h_speicherung_reference ) {
// $hs_reference_baujahr = $h_reference_baujahr;
// $hs_reference_yearkey = wpenon_immoticket24_make_yearkey( $hs_reference_baujahr, 'h_speicherung' );
// list($hs_reference_wv150, $hs_reference_wv500, $hs_reference_wv2500) = wpenon_immoticket24_make_anlagenkeys( 'wv', $hs_reference_yearkey );
// list($hs_reference_he150, $hs_reference_he500, $hs_reference_he2500) = wpenon_immoticket24_make_anlagenkeys( 'he', $hs_reference_yearkey );
// $calculations['speicherung_reference']['h']                          = array(
// 'art'            => 'heizung',
// 'name'           => $h_speicherung_reference->name,
// 'baujahr'        => $hs_reference_baujahr,
// 'waermeverluste' => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h_speicherung_reference->$hs_reference_wv150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h_speicherung_reference->$hs_reference_wv500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h_speicherung_reference->$hs_reference_wv2500,
// ),
// )
// ),
// 'hilfsenergie'   => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $h_speicherung_reference->$hs_reference_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $h_speicherung_reference->$hs_reference_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $h_speicherung_reference->$hs_reference_he2500,
// ),
// )
// ),
// );
// }

// $ww_reference_erzeugung      = wpenon_get_table_results(
// 'ww_erzeugung2019',
// array(
// 'bezeichnung' => array(
// 'value'   => 'brennwertkesselverbessert',
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// $ww_reference_energietraeger = $h_reference_energietraeger;
// $ww_reference_baujahr        = $h_reference_baujahr;
// $ww_reference_yearkey        = wpenon_immoticket24_make_yearkey( $ww_reference_baujahr, 'ww_erzeugung2019' );
// list($ww_reference_ep150, $ww_reference_ep500, $ww_reference_ep2500)    = wpenon_immoticket24_make_anlagenkeys( 'ep', $ww_reference_yearkey );
// list($ww_reference_he150, $ww_reference_he500, $ww_reference_he2500)    = wpenon_immoticket24_make_anlagenkeys( 'he', $ww_reference_yearkey );
// list($ww_reference_hwg150, $ww_reference_hwg500, $ww_reference_hwg2500) = wpenon_immoticket24_make_anlagenkeys( 'hwg', $ww_reference_yearkey );
// $calculations['anlagendaten_reference']['ww']                           = array(
// 'name'                   => $ww_reference_erzeugung->name,
// 'slug'                   => $ww_reference_erzeugung->bezeichnung,
// 'art'                    => 'warmwasser',
// 'typ'                    => $ww_reference_erzeugung->typ,
// 'baujahr'                => $ww_reference_baujahr,
// 'energietraeger'         => $ww_reference_energietraeger->name,
// 'energietraeger_slug'    => $ww_reference_energietraeger->bezeichnung,
// 'energietraeger_primaer' => floatval( $ww_reference_energietraeger->primaer ),
// 'energietraeger_co2'     => floatval( $ww_reference_energietraeger->co2 ),
// 'speicher_slug'          => $ww_reference_erzeugung->speicher,
// 'aufwandszahl'           => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_reference_erzeugung->$ww_reference_ep150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_reference_erzeugung->$ww_reference_ep500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_reference_erzeugung->$ww_reference_ep2500,
// ),
// )
// ),
// 'hilfsenergie'           => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_reference_erzeugung->$ww_reference_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_reference_erzeugung->$ww_reference_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_reference_erzeugung->$ww_reference_he2500,
// ),
// )
// ),
// 'heizwaermegewinne'      => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_reference_erzeugung->$ww_reference_hwg150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_reference_erzeugung->$ww_reference_hwg500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_reference_erzeugung->$ww_reference_hwg2500,
// ),
// )
// ),
// 'deckungsanteil'         => 100,
// );

// $ww_verteilung_reference_slug = $calculations['anlagendaten_reference']['ww']['typ'];
// if ( $ww_verteilung_reference_slug == 'zentral' ) {
// $ww_verteilung_reference_slug .= '_mit';
// }
// $ww_verteilung_reference = wpenon_get_table_results(
// 'ww_verteilung',
// array(
// 'bezeichnung' => array(
// 'value'   => $ww_verteilung_reference_slug,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// if ( $ww_verteilung_reference ) {
// $wwv_reference_baujahr = $ww_reference_baujahr;
// $wwv_reference_yearkey = wpenon_immoticket24_make_yearkey( $wwv_reference_baujahr, 'ww_verteilung', true );
// list($wwv_reference_wv150, $wwv_reference_wv500, $wwv_reference_wv2500)    = wpenon_immoticket24_make_anlagenkeys( 'wv', $wwv_reference_yearkey );
// list($wwv_reference_he150, $wwv_reference_he500, $wwv_reference_he2500)    = wpenon_immoticket24_make_anlagenkeys( 'he', $wwv_reference_yearkey );
// list($wwv_reference_hwg150, $wwv_reference_hwg500, $wwv_reference_hwg2500) = wpenon_immoticket24_make_anlagenkeys( 'hwg', $wwv_reference_yearkey );
// $calculations['verteilung_reference']['ww']                                = array(
// 'name'              => $ww_verteilung_reference->name,
// 'art'               => 'warmwasser',
// 'baujahr'           => $wwv_reference_baujahr,
// 'waermeverluste'    => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_verteilung_reference->$wwv_reference_wv150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_verteilung_reference->$wwv_reference_wv500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_verteilung_reference->$wwv_reference_wv2500,
// ),
// )
// ),
// 'hilfsenergie'      => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_verteilung_reference->$wwv_reference_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_verteilung_reference->$wwv_reference_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_verteilung_reference->$wwv_reference_he2500,
// ),
// )
// ),
// 'heizwaermegewinne' => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_verteilung_reference->$wwv_reference_hwg150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_verteilung_reference->$wwv_reference_hwg500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_verteilung_reference->$wwv_reference_hwg2500,
// ),
// )
// ),
// );
// }

// $ww_speicherung_reference_slug = $calculations['anlagendaten_reference']['ww']['speicher_slug'];
// if ( $ww_speicherung_reference_slug == 'zentral' ) {
// $ww_speicherung_reference_slug .= '_innerhalb';
// }
// $ww_speicherung_reference = wpenon_get_table_results(
// 'ww_speicherung',
// array(
// 'bezeichnung' => array(
// 'value'   => $ww_speicherung_reference_slug,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// if ( $ww_speicherung_reference ) {
// $wws_reference_baujahr = $ww_reference_baujahr;
// $wws_reference_yearkey = wpenon_immoticket24_make_yearkey( $wws_reference_baujahr, 'ww_speicherung' );
// list($wws_reference_wv150, $wws_reference_wv500, $wws_reference_wv2500)    = wpenon_immoticket24_make_anlagenkeys( 'wv', $wws_reference_yearkey );
// list($wws_reference_he150, $wws_reference_he500, $wws_reference_he2500)    = wpenon_immoticket24_make_anlagenkeys( 'he', $wws_reference_yearkey );
// list($wws_reference_hwg150, $wws_reference_hwg500, $wws_reference_hwg2500) = wpenon_immoticket24_make_anlagenkeys( 'hwg', $wws_reference_yearkey );
// $calculations['speicherung_reference']['ww']                               = array(
// 'name'              => $ww_speicherung_reference->name,
// 'art'               => 'warmwasser',
// 'baujahr'           => $wws_reference_baujahr,
// 'waermeverluste'    => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_speicherung_reference->$wws_reference_wv150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_speicherung_reference->$wws_reference_wv500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_speicherung_reference->$wws_reference_wv2500,
// ),
// )
// ),
// 'hilfsenergie'      => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_speicherung_reference->$wws_reference_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_speicherung_reference->$wws_reference_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_speicherung_reference->$wws_reference_he2500,
// ),
// )
// ),
// 'heizwaermegewinne' => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $ww_speicherung_reference->$wws_reference_hwg150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $ww_speicherung_reference->$wws_reference_hwg500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $ww_speicherung_reference->$wws_reference_hwg2500,
// ),
// )
// ),
// );
// }

// $l_reference_erzeugung      = wpenon_get_table_results(
// $tableNames->l_erzeugung,
// array(
// 'bezeichnung' => array(
// 'value'   => 'mitgewinnung',
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// $l_reference_energietraeger = wpenon_get_table_results(
// $tableNames->energietraeger,
// array(
// 'bezeichnung' => array(
// 'value'   => 'strom',
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// $l_reference_baujahr        = $h_reference_baujahr;
// $l_reference_yearkey        = wpenon_immoticket24_make_yearkey( $l_reference_baujahr, $tableNames->l_erzeugung );
// list($l_reference_he150, $l_reference_he500, $l_reference_he2500)    = wpenon_immoticket24_make_anlagenkeys( 'he', $l_reference_yearkey );
// list($l_reference_hwg150, $l_reference_hwg500, $l_reference_hwg2500) = wpenon_immoticket24_make_anlagenkeys( 'hwg', $l_reference_yearkey );
// $calculations['anlagendaten_reference']['l']                         = array(
// 'name'                   => $l_reference_erzeugung->name,
// 'slug'                   => $l_reference_erzeugung->bezeichnung,
// 'art'                    => 'lueftung',
// 'typ'                    => $l_reference_erzeugung->bezeichnung,
// 'baujahr'                => $l_reference_baujahr,
// 'energietraeger'         => $l_reference_energietraeger->name,
// 'energietraeger_slug'    => $l_reference_energietraeger->bezeichnung,
// 'energietraeger_primaer' => floatval( $l_reference_energietraeger->primaer ),
// 'energietraeger_co2'     => floatval( $l_reference_energietraeger->co2 ),
// 'hilfsenergie'           => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $l_reference_erzeugung->$l_reference_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $l_reference_erzeugung->$l_reference_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $l_reference_erzeugung->$l_reference_he2500,
// ),
// )
// ),
// 'heizwaermegewinne'      => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $l_reference_erzeugung->$l_reference_hwg150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $l_reference_erzeugung->$l_reference_hwg500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $l_reference_erzeugung->$l_reference_hwg2500,
// ),
// )
// ),
// 'deckungsanteil'         => 100,
// );

// $l_verteilung_reference_slug = $calculations['anlagendaten_reference']['l']['typ'];
// if ( $l_verteilung_reference_slug == 'mitgewinnung' ) {
// $l_verteilung_reference_slug .= '_innerhalb';
// }
// $l_verteilung_reference = wpenon_get_table_results(
// $tableNames->l_verteilung,
// array(
// 'bezeichnung' => array(
// 'value'   => $l_verteilung_reference_slug,
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// if ( $l_verteilung_reference ) {
// $lv_reference_baujahr = $l_reference_baujahr;
// $lv_reference_yearkey = wpenon_immoticket24_make_yearkey( $lv_reference_baujahr, $tableNames->l_verteilung );
// list($lv_reference_wv150, $lv_reference_wv500, $lv_reference_wv2500) = wpenon_immoticket24_make_anlagenkeys( 'wv', $lv_reference_yearkey );
// list($lv_reference_he150, $lv_reference_he500, $lv_reference_he2500) = wpenon_immoticket24_make_anlagenkeys( 'he', $lv_reference_yearkey );
// $calculations['verteilung_reference']['l']                           = array(
// 'name'           => $l_verteilung_reference->name,
// 'art'            => 'lueftung',
// 'baujahr'        => $lv_reference_baujahr,
// 'waermeverluste' => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $l_verteilung_reference->$lv_reference_wv150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $l_verteilung_reference->$lv_reference_wv500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $l_verteilung_reference->$lv_reference_wv2500,
// ),
// )
// ),
// 'hilfsenergie'   => wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => $l_verteilung_reference->$lv_reference_he150,
// ),
// array(
// 'keysize' => 500,
// 'value'   => $l_verteilung_reference->$lv_reference_he500,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => $l_verteilung_reference->$lv_reference_he2500,
// ),
// )
// ),
// );
// }

// /*************************************************
// * ANLAGENBERECHNUNGEN
// */

// $anlagentechnik = array( 'anlagendaten', 'uebergabe', 'verteilung', 'speicherung' );

// $calculations['qh_a_b'] = $calculations['qh_b'];
// $calculations['qw_a_b'] = $calculations['qw_b'];
// $calculations['ql_a_b'] = $calculations['ql_b'];

// $calculations['qh_he_b'] = 0.0;
// $calculations['qw_he_b'] = 0.0;
// $calculations['ql_he_b'] = 0.0;

// foreach ( $anlagentechnik as $anlagentyp ) {
// if ( isset( $calculations[ $anlagentyp ] ) ) {
// foreach ( $calculations[ $anlagentyp ] as $slug => $data ) {
// $aslug = $heslug = '';
// switch ( $data['art'] ) {
// case 'heizung':
// $aslug  = 'qh_a_b';
// $heslug = 'qh_he_b';
// break;
// case 'warmwasser':
// $aslug  = 'qw_a_b';
// $heslug = 'qw_he_b';
// break;
// case 'lueftung':
// $aslug  = 'ql_a_b';
// $heslug = 'ql_he_b';
// break;
// default:
// continue 2;
// }
// if ( isset( $data['waermeverluste'] ) ) {
// $calculations[ $aslug ] += $data['waermeverluste'];
// }
// if ( isset( $data['hilfsenergie'] ) ) {
// $calculations[ $heslug ] += $data['hilfsenergie'];
// }
// if ( isset( $data['heizwaermegewinne'] ) ) {
// $calculations['qh_a_b'] -= $data['heizwaermegewinne'];
// }
// }
// unset( $data );
// }
// }

// $calculations['qh_e_b'] = 0.0;
// $calculations['qw_e_b'] = 0.0;
// $calculations['ql_e_b'] = 0.0;
// $calculations['qh_p_b'] = 0.0;
// $calculations['qw_p_b'] = 0.0;
// $calculations['ql_p_b'] = 0.0;
// $calculations['qh_co2'] = 0.0;
// $calculations['qw_co2'] = 0.0;
// $calculations['ql_co2'] = 0.0;
// foreach ( $calculations['anlagendaten'] as $slug => $data ) {
// $aslug = $eslug = $pslug = $cslug = '';
// switch ( $data['art'] ) {
// case 'heizung':
// $aslug = 'qh_a_b';
// $eslug = 'qh_e_b';
// $pslug = 'qh_p_b';
// $cslug = 'qh_co2';
// break;
// case 'warmwasser':
// $aslug = 'qw_a_b';
// $eslug = 'qw_e_b';
// $pslug = 'qw_p_b';
// $cslug = 'qw_co2';
// break;
// case 'lueftung':
// $aslug = 'ql_a_b';
// $eslug = 'ql_e_b';
// $pslug = 'ql_p_b';
// $cslug = 'ql_co2';
// break;
// default:
// continue 2;
// }
// $energietraeger_slug = $data['energietraeger_slug'];
// $energietraeger      = $data['energietraeger'];
// $deckungsanteil      = $data['deckungsanteil'] * 0.01;
// $aufwandszahl        = isset( $data['aufwandszahl'] ) ? $data['aufwandszahl'] : 1.0;
// $primaerfaktor       = isset( $data['energietraeger_primaer'] ) ? $data['energietraeger_primaer'] : 1.0;
// $co2faktor           = isset( $data['energietraeger_co2'] ) ? $data['energietraeger_co2'] : 0.0;
// $result              = $calculations[ $aslug ] * $deckungsanteil * $aufwandszahl;
// $calculations['energietraeger'][ $energietraeger_slug ]['name']    = $energietraeger;
// $calculations['energietraeger'][ $energietraeger_slug ]['slug']    = $energietraeger_slug;
// $calculations['energietraeger'][ $energietraeger_slug ]['q_e_b']  += $result;
// $calculations['energietraeger'][ $energietraeger_slug ][ $eslug ] += $result;
// $calculations[ $eslug ] += $result;
// $calculations['energietraeger'][ $energietraeger_slug ]['primaerfaktor']   = $primaerfaktor;
// $calculations['energietraeger'][ $energietraeger_slug ]['primaerenergie'] += $result * $primaerfaktor;
// $calculations[ $pslug ]                                        += $result * $primaerfaktor;
// $calculations['energietraeger'][ $energietraeger_slug ]['co2'] += $result * $co2faktor;
// $calculations[ $cslug ]                                        += $result * $co2faktor;
// }
// unset( $data );

// $energietraeger_strom = wpenon_get_table_results(
// $tableNames->energietraeger,
// array(
// 'bezeichnung' => array(
// 'value'   => 'strom',
// 'compare' => '=',
// ),
// ),
// array(),
// true
// );
// $primaerfaktor_strom  = $energietraeger_strom->primaer;
// $co2faktor_strom      = $energietraeger_strom->co2;

// if ( 'solar' === $energieausweis->regenerativ_art || $energieausweis->regenerativ_aktiv ) {
// $calculations['qw_e_b']  -= wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => 13.3,
// ),
// array(
// 'keysize' => 500,
// 'value'   => 10.4,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => 7.5,
// ),
// )
// );
// $calculations['qw_he_b'] += wpenon_interpolate(
// $gebaeude->nutzflaeche(),
// array(
// array(
// 'keysize' => 150,
// 'value'   => 0.8,
// ),
// array(
// 'keysize' => 500,
// 'value'   => 0.4,
// ),
// array(
// 'keysize' => 2500,
// 'value'   => 0.3,
// ),
// )
// );
// }

// $calculations['qh_he_e_b'] = $calculations['qh_he_b'];
// $calculations['qw_he_e_b'] = $calculations['qw_he_b'];
// $calculations['ql_he_e_b'] = $calculations['ql_he_b'];
// $calculations['qh_he_p_b'] = $calculations['qh_he_e_b'] * $primaerfaktor_strom;
// $calculations['qw_he_p_b'] = $calculations['qw_he_e_b'] * $primaerfaktor_strom;
// $calculations['ql_he_p_b'] = $calculations['ql_he_e_b'] * $primaerfaktor_strom;
// $calculations['qh_he_co2'] = $calculations['qh_he_e_b'] * $co2faktor_strom;
// $calculations['qw_he_co2'] = $calculations['qw_he_e_b'] * $co2faktor_strom;
// $calculations['ql_he_co2'] = $calculations['ql_he_e_b'] * $co2faktor_strom;

// $calculations['qhe_e_b'] = $calculations['qh_he_e_b'] + $calculations['qw_he_e_b'] + $calculations['ql_he_e_b'];
// $calculations['qhe_p_b'] = $calculations['qh_he_p_b'] + $calculations['qw_he_p_b'] + $calculations['ql_he_p_b'];
// $calculations['qhe_co2'] = $calculations['qh_he_co2'] + $calculations['qw_he_co2'] + $calculations['ql_he_co2'];

// $calculations['endenergie']     = $calculations['qh_e_b'] + $calculations['qw_e_b'] + $calculations['ql_e_b'] + $calculations['qhe_e_b'];
// $calculations['primaerenergie'] = $calculations['qh_p_b'] + $calculations['qw_p_b'] + $calculations['ql_p_b'] + $calculations['qhe_p_b'];
// $calculations['co2_emissionen'] = $calculations['qh_co2'] + $calculations['qw_co2'] + $calculations['ql_co2'] + $calculations['qhe_co2'];

// // Referenzgebäude
// $anlagentechnik_reference = array( 'anlagendaten_reference', 'uebergabe_reference', 'verteilung_reference', 'speicherung_reference' );

// $calculations['qh_a_b_reference'] = $calculations['qh_b_reference'];
// $calculations['qw_a_b_reference'] = $calculations['qw_b_reference'];
// $calculations['ql_a_b_reference'] = $calculations['ql_b_reference'];

// $calculations['qh_he_b_reference'] = 0.0;
// $calculations['qw_he_b_reference'] = 0.0;
// $calculations['ql_he_b_reference'] = 0.0;

// foreach ( $anlagentechnik_reference as $anlagentyp ) {
// if ( isset( $calculations[ $anlagentyp ] ) ) {
// foreach ( $calculations[ $anlagentyp ] as $slug => $data ) {
// $aslug = $heslug = '';
// switch ( $data['art'] ) {
// case 'heizung':
// $aslug  = 'qh_a_b_reference';
// $heslug = 'qh_he_b_reference';
// break;
// case 'warmwasser':
// $aslug  = 'qw_a_b_reference';
// $heslug = 'qw_he_b_reference';
// break;
// case 'lueftung':
// $aslug  = 'ql_a_b_reference';
// $heslug = 'ql_he_b_reference';
// break;
// default:
// continue 2;
// }
// if ( isset( $data['waermeverluste'] ) ) {
// $calculations[ $aslug ] += $data['waermeverluste'];
// }
// if ( isset( $data['hilfsenergie'] ) ) {
// $calculations[ $heslug ] += $data['hilfsenergie'];
// }
// if ( isset( $data['heizwaermegewinne'] ) ) {
// $calculations['qh_a_b_reference'] -= $data['heizwaermegewinne'];
// }
// }
// unset( $data );
// }
// }

// $calculations['qh_e_b_reference'] = 0.0;
// $calculations['qw_e_b_reference'] = 0.0;
// $calculations['ql_e_b_reference'] = 0.0;
// $calculations['qh_p_b_reference'] = 0.0;
// $calculations['qw_p_b_reference'] = 0.0;
// $calculations['ql_p_b_reference'] = 0.0;
// $calculations['qh_co2_reference'] = 0.0;
// $calculations['qw_co2_reference'] = 0.0;
// $calculations['ql_co2_reference'] = 0.0;
// foreach ( $calculations['anlagendaten_reference'] as $slug => $data ) {
// $aslug = $eslug = $pslug = $cslug = '';
// switch ( $data['art'] ) {
// case 'heizung':
// $aslug = 'qh_a_b_reference';
// $eslug = 'qh_e_b_reference';
// $pslug = 'qh_p_b_reference';
// $cslug = 'qh_co2_reference';
// break;
// case 'warmwasser':
// $aslug = 'qw_a_b_reference';
// $eslug = 'qw_e_b_reference';
// $pslug = 'qw_p_b_reference';
// $cslug = 'qw_co2_reference';
// break;
// case 'lueftung':
// $aslug = 'ql_a_b_reference';
// $eslug = 'ql_e_b_reference';
// $pslug = 'ql_p_b_reference';
// $cslug = 'ql_co2_reference';
// break;
// default:
// continue 2;
// }
// $deckungsanteil          = $data['deckungsanteil'] * 0.01;
// $aufwandszahl            = isset( $data['aufwandszahl'] ) ? $data['aufwandszahl'] : 1.0;
// $primaerfaktor           = isset( $data['energietraeger_primaer'] ) ? $data['energietraeger_primaer'] : 1.0;
// $co2faktor               = isset( $data['energietraeger_co2'] ) ? $data['energietraeger_co2'] : 0.0;
// $result                  = $calculations[ $aslug ] * $deckungsanteil * $aufwandszahl;
// $calculations[ $eslug ] += $result;
// $calculations[ $pslug ] += $result * $primaerfaktor;
// $calculations[ $cslug ] += $result * $co2faktor;
// }
// unset( $data );

// $calculations['qh_he_e_b_reference'] = $calculations['qh_he_b_reference'];
// $calculations['qw_he_e_b_reference'] = $calculations['qw_he_b_reference'];
// $calculations['ql_he_e_b_reference'] = $calculations['ql_he_b_reference'];
// $calculations['qh_he_p_b_reference'] = $calculations['qh_he_e_b_reference'] * $primaerfaktor_strom;
// $calculations['qw_he_p_b_reference'] = $calculations['qw_he_e_b_reference'] * $primaerfaktor_strom;
// $calculations['ql_he_p_b_reference'] = $calculations['ql_he_e_b_reference'] * $primaerfaktor_strom;
// $calculations['qh_he_co2_reference'] = $calculations['qh_he_e_b_reference'] * $co2faktor_strom;
// $calculations['qw_he_co2_reference'] = $calculations['qw_he_e_b_reference'] * $co2faktor_strom;
// $calculations['ql_he_co2_reference'] = $calculations['ql_he_e_b_reference'] * $co2faktor_strom;

// $calculations['qhe_e_b_reference'] = $calculations['qh_he_e_b_reference'] + $calculations['qw_he_e_b_reference'] + $calculations['ql_he_e_b_reference'];
// $calculations['qhe_p_b_reference'] = $calculations['qh_he_p_b_reference'] + $calculations['qw_he_p_b_reference'] + $calculations['ql_he_p_b_reference'];
// $calculations['qhe_co2_reference'] = $calculations['qh_he_co2_reference'] + $calculations['qw_he_co2_reference'] + $calculations['ql_he_co2_reference'];

// $calculations['endenergie_reference']     = $calculations['qh_e_b_reference'] + $calculations['qw_e_b_reference'] + $calculations['ql_e_b_reference'] + $calculations['qhe_e_b_reference'];
// $calculations['primaerenergie_reference'] = $calculations['qh_p_b_reference'] + $calculations['qw_p_b_reference'] + $calculations['ql_p_b_reference'] + $calculations['qhe_p_b_reference'];
// $calculations['co2_emissionen_reference'] = $calculations['qh_co2_reference'] + $calculations['qw_co2_reference'] + $calculations['ql_co2_reference'] + $calculations['qhe_co2_reference'];

// $calculations['ht_b']           = $calculations['ht'] / $calculations['huellflaeche'];
// $calculations['ht_b_reference'] = 0.65;
// if ( 'freistehend' === $energieausweis->gebaeudetyp ) {
// if ( $gebaeude->nutzflaeche() > 350.0 ) {
// $calculations['ht_b_reference'] = 0.5;
// } else {
// $calculations['ht_b_reference'] = 0.4;
// }
// } elseif ( 'reiheneckhaus' === $energieausweis->gebaeudetyp || 'doppelhaushaelfte' === $energieausweis->gebaeudetyp ) {
// $calculations['ht_b_reference'] = 0.45;
// }

return $calculations;
