<?php
/**
 * Kalkulationen für den Bedarfsausweis.
 *
 * @package wpenon
 */

namespace Enev\Schema202401\Calculations;

use Enev\Schema202401\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202401\Calculations\Gebaeude\Grundriss;
use Enev\Schema202401\Calculations\Gebaeude\Anbau;
use Enev\Schema202401\Calculations\Gebaeude\Grundriss_Anbau;
use Enev\Schema202401\Calculations\Gebaeude\Keller;

use Enev\Schema202401\Calculations\Anlagentechnik\Lueftung;
use Enev\Schema202401\Calculations\Anlagentechnik\Photovoltaik_Anlage;
use Enev\Schema202401\Calculations\Anlagentechnik\Uebergabesystem;
use Enev\Schema202401\Calculations\Anlagentechnik\Trinkwarmwasseranlage;
use Enev\Schema202401\Calculations\Bauteile\Anbauboden;
use Enev\Schema202401\Calculations\Bauteile\Anbaudecke;
use Enev\Schema202401\Calculations\Bauteile\Anbaufenster;
use Enev\Schema202401\Calculations\Bauteile\Anbauwand;
use Enev\Schema202401\Calculations\Bauteile\Bauteile;
use Enev\Schema202401\Calculations\Bauteile\Boden;
use Enev\Schema202401\Calculations\Bauteile\Decke;
use Enev\Schema202401\Calculations\Bauteile\Fenster;
use Enev\Schema202401\Calculations\Bauteile\Flachdach;
use Enev\Schema202401\Calculations\Bauteile\Heizkoerpernische;
use Enev\Schema202401\Calculations\Bauteile\Kellerboden;
use Enev\Schema202401\Calculations\Bauteile\Kellerwand;
use Enev\Schema202401\Calculations\Bauteile\Pultdach;
use Enev\Schema202401\Calculations\Bauteile\Rolladenkasten;
use Enev\Schema202401\Calculations\Bauteile\Satteldach;
use Enev\Schema202401\Calculations\Bauteile\Walmdach;
use Enev\Schema202401\Calculations\Bauteile\Wand;

use function Enev\Schema202401\Calculations\Helfer\berechne_fenster_flaeche;
use function Enev\Schema202401\Calculations\Helfer\berechne_heizkoerpernische_flaeche;
use function Enev\Schema202401\Calculations\Helfer\berechne_rolladenkasten_flaeche;
use function Enev\Schema202401\Calculations\Tabellen\uwert;

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

require_once __DIR__ . '/Anlagentechnik/Lueftung.php';
require_once __DIR__ . '/Anlagentechnik/Photovoltaik_Anlage.php';

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
			uwert: uwert( 'decke_' . $energieausweis->anbaudach_bauart, $energieausweis->anbau_baujahr ),
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
				daemmung: $energieausweis->boden_daemmung,
			)
		);

		if ( $energieausweis->keller_groesse < 100 ) {
			$gebaeude->bauteile()->hinzufuegen(
				new Boden(
					name: sprintf( __( 'Boden', 'wpenon' ) ),
					flaeche: $gebaeude->grundriss()->flaeche() - $kellerflaeche,
					uwert: uwert( 'boden_' . $energieausweis->boden_bauart, $energieausweis->baujahr ),
					daemmung: $energieausweis->boden_daemmung,
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
				daemmung: $energieausweis->boden_daemmung,
			)
		);

		if ( $energieausweis->keller_groesse < 100 ) {
			$gebaeude->bauteile()->hinzufuegen(
				new Boden(
					name: sprintf( __( 'Boden', 'wpenon' ) ),
					flaeche: $gebaeude->grundriss()->flaeche() - $kellerflaeche,
					uwert: uwert( 'boden_' . $energieausweis->boden_bauart, $energieausweis->baujahr ),
					daemmung: $energieausweis->boden_daemmung,
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
				daemmung: $energieausweis->boden_daemmung,
			)
		);
}

$gebaeude->lueftung(
	new Lueftung(
		gebaeude: $gebaeude,
		lueftungssystem: $energieausweis->l_info,
		// art: $energieausweis->l_art, // NOTE: Unterschied ist zu marginal, daher wird mit dezentral (schlechterer Wert gerechnet) (Christian: 2023-12-20)
		art: 'dezentral',
		// bedarfsgefuehrt: $energieausweis->l_bedarfsgefuehrt, // NOTE: Lüftungsanlage wird aus Vereinfachung immmer bedarfsgeführt betrachtet (Christian: Mail vom 2023-12-21)
		bedarfsgefuehrt: true,
		gebaeudedichtheit: $energieausweis->dichtheit ? 'din_4108_7' : 'andere',
		// wirkungsgrad: (float) $energieausweis->l_wirkungsgrad
		wirkungsgrad: 0 // NOTE: Wirkungsgrad wird in die Spalte des schlechtesten Wertes geschoben (Michael: 2023-12-20) 
		// TODO: Feld im Backend erstellen, damit der Wert angepasst werden kann.
	)
);


$heizung_im_beheizten_bereich = $energieausweis->h_standort === 'innerhalb' ? true : false;

switch ( $energieausweis->ww_info ) {
	// Eigene Warmwasserbereitung.
	case 'ww':
		$ww_zentral = false;
		$mit_zirkulation = false;
		$mit_warmwasserspeicher = false;
		$ww_erzeuger = $energieausweis->ww_erzeugung;
		$ww_energietraeger = $energieausweis->ww_energietraeger;
		$ww_baujahr = $energieausweis->ww_baujahr;
		break;
	// Zentrale Warmwasserbereitung über die Heizungsanlage.
	case 'h':
		$ww_zentral = true;
		$mit_zirkulation = $energieausweis->verteilung_versorgung === 'mit' ? true : false;
		$mit_warmwasserspeicher = false;
		$ww_erzeuger = $energieausweis->h_erzeugung;
		$ww_energietraeger = $energieausweis->h_energietraeger;
		$ww_baujahr = $energieausweis->h_baujahr;
		break;
	case 'h2':
		$ww_zentral = true;
		$mit_zirkulation = $energieausweis->verteilung_versorgung === 'mit' ? true : false;
		$mit_warmwasserspeicher = false;
		$ww_erzeuger = $energieausweis->h2_erzeugung;
		$ww_energietraeger = $energieausweis->h2_energietraeger;
		$ww_baujahr = $energieausweis->h2_baujahr;
		break;
	case 'h3':
		$ww_zentral = true;
		$mit_zirkulation = $energieausweis->verteilung_versorgung === 'mit' ? true : false;
		$mit_warmwasserspeicher = false;
		$ww_erzeuger = $energieausweis->h3_erzeugung;
		$ww_energietraeger = $energieausweis->h3_energietraeger;
		$ww_baujahr = $energieausweis->h3_baujahr;
		break;
}

$gebaeude->trinkwarmwasseranlage(
	new Trinkwarmwasseranlage(
		gebaeude: $gebaeude,
		zentral: $ww_zentral,
		erzeuger: $ww_erzeuger,
		heizung_im_beheizten_bereich: $heizung_im_beheizten_bereich,
		mit_warmwasserspeicher: true,
		mit_zirkulation: $mit_zirkulation,
		mit_solarthermie: $energieausweis->solarthermie_info === 'vorhanden' ? true : false,
		solarthermie_neigung: $energieausweis->solarthermie_info === 'vorhanden' ? $energieausweis->solarthermie_neigung : null,
		solarthermie_richtung: $energieausweis->solarthermie_info === 'vorhanden' ? $energieausweis->solarthermie_richtung : null,
		solarthermie_baujahr:$energieausweis->solarthermie_info === 'vorhanden' ? $energieausweis->solarthermie_baujahr : null	
	)
);

/**
 * Heizsysteme
 */
$energietraeger_name = 'h_energietraeger_' . $energieausweis->h_erzeugung;
$energietraeger = $energieausweis->$energietraeger_name;
$h_prozentualer_anteil = ! isset( $energieausweis->h_deckungsanteil ) || $energieausweis->h_deckungsanteil === 0 ? 100 : $energieausweis->h_deckungsanteil;

// if( $_GET['debug'] ) {
// 	$file = fopen( __DIR__ . '/debug.txt', 'w' );
// 	fwrite( $file, print_r( 'Prozentualer Anteil (energieausweis->h_deckungsanteil): ' . $h_prozentualer_anteil, true ) );
// 	fwrite( $file, print_r( 'Prozentualer Anteil (h_prozentualer_anteil): ' . $h_prozentualer_anteil, true ) );
// 	fwrite( $file, print_r( $energieausweis, true ) );
// 	fclose( $file );
// }

if( $energieausweis->h_erzeugung === 'waermepumpeluft' || $energieausweis->h_erzeugung === 'waermepumpewasser' || $energieausweis->h_erzeugung === 'waermepumpeerde' ) {
	// $h_evu_abschaltung = $energieausweis->h_evu_abschaltung === 'ja' ? true : false;
	$h_evu_abschaltung = true; // NOTE: EVU wird immer auf true gesetzt, damit weniger Fragen aufkommen. Die Werte sollten dadurch schlechter werden (Michael: 2023-12-20)

	// NOTE: Wärmepumpen werden aufgrund der Vereinfachung immer Einstufig gerechnet (Michael & Crhistian: 2023-12-21)
	// if( $energieausweis->h_erzeugung === 'waermepumpeluft' && $energieausweis->h_waermepumpe_luft_stufen === 'einstufig' ) {
	// 	$h_waermepumpe_luft_einstufig = true;
	// } else {
	// 	$h_waermepumpe_luft_einstufig = false;
	// }

	$h_waermepumpe_luft_einstufig = true;

	$h_waermepumpe_erde_typ = $energieausweis->h_erzeugung === 'waermepumpeerde' ? $energieausweis->h_waermepumpe_erde_typ : null;

	$gebaeude->heizsystem()->heizungsanlagen()->hinzufuegen( $energieausweis->h_erzeugung, $energietraeger, $energieausweis->h_baujahr, $h_prozentualer_anteil, $h_evu_abschaltung, $h_waermepumpe_luft_einstufig, $h_waermepumpe_erde_typ );
} else {
	$gebaeude->heizsystem()->heizungsanlagen()->hinzufuegen( $energieausweis->h_erzeugung, $energietraeger, $energieausweis->h_baujahr, $h_prozentualer_anteil );
}

if ( $energieausweis->h2_info ) {
	$energietraeger_name = 'h2_energietraeger_' . $energieausweis->h2_erzeugung;
	$energietraeger = $energieausweis->$energietraeger_name;

	if( $energieausweis->h2_erzeugung === 'waermepumpeluft' && $energieauswies->h2_waermepumpe_luft_stufen === 'einstufig' ) {
		$h2_waermepumpe_luft_einstufig = true;
	} else {
		$h2_waermepumpe_luft_einstufig = false;	
	}

	$h2_waermepumpe_erde_typ = $energieausweis->h2_erzeugung === 'waermepumpeerde' ? $energieausweis->h2_waermepumpe_erde_typ : null;

	if( $energieausweis->h2_erzeugung === 'waermepumpeluft' || $energieausweis->h2_erzeugung === 'waermepumpewasser' || $energieausweis->h2_erzeugung === 'waermepumpeerde' ) {
		$h2_evu_abschaltung = $energieausweis->h2_evu_abschaltung === 'ja' ? true : false;	
		$gebaeude->heizsystem()->heizungsanlagen()->hinzufuegen( $energieausweis->h2_erzeugung, $energietraeger, $energieausweis->h2_baujahr, $energieausweis->h2_deckungsanteil, $h2_evu_abschaltung, $h2_waermepumpe_luft_einstufig, $h2_waermepumpe_erde_typ );
	} else {
		$gebaeude->heizsystem()->heizungsanlagen()->hinzufuegen( $energieausweis->h2_erzeugung, $energietraeger, $energieausweis->h2_baujahr, $energieausweis->h2_deckungsanteil );
	}

	if ( $energieausweis->h3_info ) {
		$energietraeger_name = 'h3_energietraeger_' . $energieausweis->h3_erzeugung;
		$energietraeger = $energieausweis->$energietraeger_name;
	
		if( $energieausweis->h3_erzeugung === 'waermepumpeluft' && $energieauswies->h3_waermepumpe_luft_stufen === 'einstufig' ) {
			$h3_waermepumpe_luft_einstufig = true;
		} else {
			$h3_waermepumpe_luft_einstufig = false;	
		}
	
		$h3_waermepumpe_erde_typ = $energieausweis->h3_erzeugung === 'waermepumpeerde' ? $energieausweis->h3_waermepumpe_erde_typ : null;
	
		if( $energieausweis->h3_erzeugung === 'waermepumpeluft' || $energieausweis->h3_erzeugung === 'waermepumpewasser' || $energieausweis->h3_erzeugung === 'waermepumpeerde' ) {
			$h3_evu_abschaltung = $energieausweis->h3_evu_abschaltung === 'ja' ? true : false;	
			$gebaeude->heizsystem()->heizungsanlagen()->hinzufuegen( $energieausweis->h3_erzeugung, $energietraeger, $energieausweis->h3_baujahr, $energieausweis->h3_deckungsanteil, $h3_evu_abschaltung, $h3_waermepumpe_luft_einstufig, $h3_waermepumpe_erde_typ );
		} else {
			$gebaeude->heizsystem()->heizungsanlagen()->hinzufuegen( $energieausweis->h3_erzeugung, $energietraeger, $energieausweis->h3_baujahr, $energieausweis->h3_deckungsanteil );
		}
	}
}

if( ! function_exists( 'Enev\Schema202401\Calculations\wpenon_temperatur_flaechenheizungen' ) )  {
	function wpenon_temperatur_flaechenheizungen($flaechenheizungstyp) {
		switch ($flaechenheizungstyp) {
			case 'fussbodenheizung':
			case 'wandheizung':
				return '35/28';
			case 'deckenheizung':
				return '55/45';
			default:
				throw new Calculation_Exception('Flächenheizungstyp nicht bekannt.');
		}
	}
}

if( ! function_exists( 'Enev\Schema202401\Calculations\wpenon_auslegungstemperaturen' ) )  {
	function wpenon_auslegungstemperaturen($erzeuger, $uebergabe, $flaechenheizungstyp) {
		if( empty( $uebergabe ) ) {
			return null;
		}

		switch ($erzeuger) {
			case 'standardkessel':
				return ($uebergabe === 'heizkoerper') ? '90/70' : wpenon_temperatur_flaechenheizungen($flaechenheizungstyp);

			case 'niedertemperaturkessel':
			case 'etagenheizung':
				return ($uebergabe === 'heizkoerper') ? '70/55' : wpenon_temperatur_flaechenheizungen($flaechenheizungstyp);

			case 'brennwertkessel':
			case 'waermepumpeluft':
			case 'waermepumpewasser':
			case 'waermepumpeerde':
			case 'fernwaerme':
				return ($uebergabe === 'heizkoerper') ? '55/45' : wpenon_temperatur_flaechenheizungen($flaechenheizungstyp);

			default:
				return null;
		}
	}
}

if( ! function_exists( 'Enev\Schema202401\Calculations\wpenon_auslegungstemperatur' ) )  {
	function wpenon_auslegungstemperatur( $heizungen ) {
		$auslegungstemperaturen = array();

		foreach( $heizungen as $heizung ) {		
			// Ermittle alle Auslegungstemperaturen.
			$auslegungstemperatur = wpenon_auslegungstemperaturen( $heizung['erzeugung'], $heizung['uebergabe'], $heizung['flaechenheizungstyp'] );

			if( $auslegungstemperatur === null ) {
				continue;
			}
			
			$temperaturen = explode ( '/',  $auslegungstemperatur );
			$auslegungstemperaturen[ $temperaturen[0] ] = $auslegungstemperatur;
		}

		if( empty( $auslegungstemperaturen ) ) {
			return null;
		}

		// Ermittle die niedrigste Auslegungstemperatur.
		$auslegungstemperatur = min( array_keys( $auslegungstemperaturen ) );

		return $auslegungstemperaturen[ $auslegungstemperatur ];
	}
}

$heizungen[] = array(
	'uebergabe' => $energieausweis->h_uebergabe,
	'flaechenheizungstyp' => $energieausweis->h_uebergabe === 'flaechenheizung' ? $energieausweis->h_uebergabe_flaechenheizungstyp : null,
	'erzeugung' => $energieausweis->h_erzeugung,
);

if( $energieausweis->h2_info ) {
	$heizungen[] = array(
		'uebergabe' => $energieausweis->h_uebergabe,
		'flaechenheizungstyp' => $energieausweis->h_uebergabe === 'flaechenheizung' ? $energieausweis->h_uebergabe_flaechenheizungstyp : null,
		'erzeugung' => $energieausweis->h2_erzeugung,
	);

	if( $energieausweis->h3_info ) {
		$heizungen[] = array(
			'uebergabe' => $energieausweis->h_uebergabe,
			'flaechenheizungstyp' => $energieausweis->h_uebergabe === 'flaechenheizung' ? $energieausweis->h_uebergabe_flaechenheizungstyp : null,
			'erzeugung' => $energieausweis->h3_erzeugung,
		);
	}
}

$auslegungstemperaturen = wpenon_auslegungstemperatur( $heizungen );

// $auslegungstemperaturen = '70/55';

// Wir rechnen vorerst nur mit einem Übergabesystem.
if( $energieausweis->h_uebergabe === 'flaechenheizung' ){
	$gebaeude->heizsystem()->uebergabesysteme()->hinzufuegen(
		new Uebergabesystem(
			gebaeude: $gebaeude,
			typ: $energieausweis->h_uebergabe,
			auslegungstemperaturen: $auslegungstemperaturen,
			prozentualer_anteil: 100, // Erst 100%, später dann anteilmäßig mit $energieausweis->h_uebergabe_anteil
			flaechenheizungstyp: $energieausweis->h_uebergabe_flaechenheizungstyp,
			// mindestdaemmung: $energieausweis->h_uebergabe_mindestdaemmung
			mindestdaemmung: true
		)
	);		
} else {
	$h2_info = $energieausweis->h2_info;
	$h3_info = $energieausweis->h2_info;
	
	$h_erzeugung = $energieausweis->h_erzeugung;
	$h2_erzeugung = $energieausweis->h2_erzeugung;
	$h3_erzeugung = $energieausweis->h3_erzeugung;

	if( ! wpenon_erzeuger_mit_uebergabe_vorhanden( $h_erzeugung, $h2_erzeugung, $h3_erzeugung, $h2_info, $h3_info ) ) {
		$uebergabe_typ = 'elektroheizungsflaechen';
	} else {
		$uebergabe_typ =  $energieausweis->h_uebergabe;
	}

	$gebaeude->heizsystem()->uebergabesysteme()->hinzufuegen(
		new Uebergabesystem(
			gebaeude: $gebaeude,
			typ: $uebergabe_typ,
			auslegungstemperaturen: $auslegungstemperaturen,
			prozentualer_anteil: 100 // Erst 100%, später dann anteilmäßig mit $energieausweis->h_uebergabe_anteil
		)
	);
}

if( $energieausweis->pv_info === 'vorhanden' ) {
	$gebaeude->photovoltaik_anlage( new Photovoltaik_Anlage(
		gebaeude: $gebaeude,
		richtung: $energieausweis->pv_richtung,
		neigung: $energieausweis->pv_neigung,
		flaeche: floatval( $energieausweis->pv_flaeche ),
		baujahr: intval( $energieausweis->pv_baujahr ),
	));
}


$calculations['bauteile'] = array();

// Opake Bauteile
foreach( $gebaeude->bauteile()->opak()->alle() AS $bauteil ) {	
	$data = array(
		'modus' => 'opak',
		'key' => $bauteil->name(),
		'name' => $bauteil->name(),
		'a' => $bauteil->flaeche(),
		'u' => $bauteil->uwert(),
	);

	if ( method_exists( $bauteil, 'himmelsrichtung' ) ) {
		$data['richtung'] = strtoupper( $bauteil->himmelsrichtung() );
	}	

	$calculations['bauteile'][] = $data;
}

// Transparente Bauteile
foreach( $gebaeude->bauteile()->transparent()->alle() AS $bauteil ) {	
	$data = array(
		'modus' => 'transparent',
		'key' => $bauteil->name(),
		'name' => $bauteil->name(),
		'a' => $bauteil->flaeche(),
		'u' => $bauteil->uwert(),
	);

	if ( method_exists( $bauteil, 'himmelsrichtung' ) ) {
		$data['richtung'] = strtoupper( $bauteil->himmelsrichtung() );
	}	

	$calculations['bauteile'][] = $data;
}

// Dach
foreach( $gebaeude->bauteile()->dach()->alle() AS $bauteil ) {	
	$data = array(
		'modus' => 'dach',
		'key' => $bauteil->name(),
		'name' => $bauteil->name(),
		'a' => $bauteil->flaeche(),
		'u' => $bauteil->uwert(),
	);

	if ( method_exists( $bauteil, 'himmelsrichtung' ) ) {
		$data['richtung'] = strtoupper( $bauteil->himmelsrichtung() );
	}	

	$calculations['bauteile'][] = $data;
}


// Heizungsanlagen
$calculations['anlagendaten'] = array();
foreach( $gebaeude->heizsystem()->heizungsanlagen()->alle() AS $heizungsanlage ) {
	$anlage = array();
	$anlage['art'] = 'heizung';
	$anlage['slug'] = $heizungsanlage->erzeuger();
	$anlage['baujahr'] = $heizungsanlage->baujahr();
	$anlage['energietraeger_slug'] = $heizungsanlage->energietraeger();
	$anlage['energietraeger_primaer'] = $heizungsanlage->fp();
	$anlage['energietraeger_co2'] = $heizungsanlage->MCO2();
	$anlage['emissionsfaktor'] = $heizungsanlage->co2_energietraeger();

	$calculations['anlagendaten'][] = $anlage;
}

$calculations['anlagendaten'][] = array(
	'art' => 'warmwasser',
	'slug' => $ww_erzeuger,
	'baujahr' => $ww_baujahr,
	'energietraeger' => $ww_energietraeger,
);

$calculations['reference'] = 125; // Übernommen aus alter bw.php
$calculations['nutzflaeche'] = $gebaeude->nutzflaeche();

$calculations['energietraeger'] = array();

foreach( $gebaeude->heizsystem()->heizungsanlagen()->alle() AS $heizungsanlage ) {
	$energietraeger = $heizungsanlage->energietraeger();

	if( ! isset( $calculations['energietraeger'][ $energietraeger ] ) ) {
		$calculations['energietraeger'][ $energietraeger ] = array(
			'primaerfaktor' => 0,
			'qh_e_b' => 0,	
			'qw_e_b' => 0,
			'ql_e_b' => 0,
			'q_e_b' => 0,
		);
	}

	$calculations['energietraeger'][ $energietraeger ]['slug'] = $heizungsanlage->energietraeger();
	$calculations['energietraeger'][ $energietraeger ]['primaerfaktor'] += $heizungsanlage->fp();
	$calculations['energietraeger'][ $energietraeger ]['qh_e_b'] += $heizungsanlage->Qfhges(); // Endenergie Heizungsspezifisc
	$calculations['energietraeger'][ $energietraeger ]['q_e_b'] += $heizungsanlage->Qfhges(); // Endenergie Heizungsspezifisch

	if( $gebaeude->trinkwarmwasseranlage()->zentral() ) {
		$calculations['energietraeger'][ $energietraeger ]['qw_e_b'] += $heizungsanlage->Qfwges(); // Endenergie Warmwasserspezifisch
		$calculations['energietraeger'][ $energietraeger ]['q_e_b'] += $heizungsanlage->Qfwges(); // Endenergie Warmwasserspezifisch
	}
}

// Trinkwarmwasseranlage
if( ! $gebaeude->trinkwarmwasseranlage()->zentral() ) {
	if( ! isset( $calculations['energietraeger'][ $ww_energietraeger ] ) ) {
		$calculations['energietraeger'][ $ww_energietraeger ] = array(
			'qh_e_b' => 0,	
			'qw_e_b' => 0,
			'ql_e_b' => 0,
			'q_e_b' => 0,
		);
	}

	$calculations['energietraeger'][ $ww_energietraeger ]['slug'] = $gebaeude->trinkwarmwasseranlage()->energietraeger();
	$calculations['energietraeger'][ $ww_energietraeger ]['primaerfaktor'] = $gebaeude->trinkwarmwasseranlage()->fp();
	$calculations['energietraeger'][ $energietraeger ]['qw_e_b'] += $gebaeude->trinkwarmwasseranlage()->Qfwges(); // Endenergie Warmwasserspezifisch
	$calculations['energietraeger'][ $energietraeger ]['q_e_b'] += $gebaeude->trinkwarmwasseranlage()->Qfwges(); // Endenergie Warmwasserspezifisch
}

// Lüftung
if( $gebaeude->lueftung()->Wrvg() > 0 ){
	if( ! isset( $calculations['energietraeger']['strom'] ) ) {
		$calculations['energietraeger']['strom'] = array(
			'slug' => 'strom',
			'primaerfaktor' => 1.8,
			'qh_e_b' => 0,	
			'qw_e_b' => 0,
			'ql_e_b' => 0,
			'q_e_b' => 0,
		);
	}

	$calculations['energietraeger']['strom']['ql_e_b'] += $gebaeude->lueftung()->Wrvg();
	$calculations['energietraeger']['strom']['q_e_b'] += $gebaeude->lueftung()->Wrvg();
}

$calculations['huellvolumen'] = $gebaeude->huellvolumen();
$calculations['endenergie'] = $gebaeude->Qf();
$calculations['primaerenergie'] = $gebaeude->Qp();
$calculations['co2_emissionen'] = $gebaeude->MCO2a();
$calculations['ht_strich'] = $gebaeude->ht_strich();
$calculations['qfh_ges'] = $gebaeude->Qfhges();
$calculations['qfw_ges'] = $gebaeude->Qfwges();
$calculations['w_ges'] = $gebaeude->hilfsenergie()->Wges();

$calculations['auslegungstemperatur'] = $auslegungstemperaturen;
$calculations['V_s'] =  $gebaeude->heizsystem()->pufferspeicher_vorhanden() ? $gebaeude->heizsystem()->pufferspeicher()->volumen(): 0; // Pufferspeicher Nenninhalt in L

$calculations['ht'] = $gebaeude->bauteile()->ht();
$calculations['hv'] = $gebaeude->lueftung()->hv();

$calculations['qt'] = $gebaeude->bauteile()->ht(); 
$calculations['qs'] = $gebaeude->qi_solar();
$calculations['qi'] = $gebaeude->qi();
$calculations['qh'] = $gebaeude->qh();

$calculations['photovoltaik'] = array();

if( $gebaeude->photovoltaik_anlage_vorhanden() ) {
	$calculations['photovoltaik']['ertrag'] = round( $gebaeude->photovoltaik_anlage()->Pvans( $gebaeude->Qfstrom() ) / $gebaeude->nutzflaeche() );
}


return $calculations;