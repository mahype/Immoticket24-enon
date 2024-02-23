<?php
/**
 * Referenzgebaeude.
 *
 * @package wpenon
 */
namespace Enev\Schema202402\Calculations;

use Enev\Schema202402\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202402\Calculations\Gebaeude\Grundriss;
use Enev\Schema202402\Calculations\Gebaeude\Anbau;
use Enev\Schema202402\Calculations\Gebaeude\Grundriss_Anbau;
use Enev\Schema202402\Calculations\Gebaeude\Keller;

use Enev\Schema202402\Calculations\Anlagentechnik\Lueftung;
use Enev\Schema202402\Calculations\Anlagentechnik\Photovoltaik_Anlage;
use Enev\Schema202402\Calculations\Anlagentechnik\Uebergabesystem;
use Enev\Schema202402\Calculations\Anlagentechnik\Trinkwarmwasseranlage;
use Enev\Schema202402\Calculations\Bauteile\Anbauboden;
use Enev\Schema202402\Calculations\Bauteile\Anbaudecke;
use Enev\Schema202402\Calculations\Bauteile\Anbaufenster;
use Enev\Schema202402\Calculations\Bauteile\Anbauwand;
use Enev\Schema202402\Calculations\Bauteile\Boden;
use Enev\Schema202402\Calculations\Bauteile\Decke;
use Enev\Schema202402\Calculations\Bauteile\Fenster;
use Enev\Schema202402\Calculations\Bauteile\Flachdach;
use Enev\Schema202402\Calculations\Bauteile\Heizkoerpernische;
use Enev\Schema202402\Calculations\Bauteile\Kellerboden;
use Enev\Schema202402\Calculations\Bauteile\Kellerwand;
use Enev\Schema202402\Calculations\Bauteile\Pultdach;
use Enev\Schema202402\Calculations\Bauteile\Rolladenkasten;
use Enev\Schema202402\Calculations\Bauteile\Satteldach;
use Enev\Schema202402\Calculations\Bauteile\Walmdach;
use Enev\Schema202402\Calculations\Bauteile\Wand;

use function Enev\Schema202402\Calculations\Helfer\berechne_fenster_flaeche;
use function Enev\Schema202402\Calculations\Helfer\berechne_heizkoerpernische_flaeche;
use function Enev\Schema202402\Calculations\Helfer\berechne_rolladenkasten_flaeche;
use function Enev\Schema202402\Calculations\Tabellen\uwert;

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

use WPENON\Model\Energieausweis;

class Referenzgebaeude {

	protected Energieausweis $energieausweis;

	/**
	 * Heizwärmebedarf.
	 *
	 * @var float
	 */
	protected float $ht_strich;

	/**
	 * Gebäude.
	 *
	 * @var Gebaeude
	 */
	protected Gebaeude $gebaeude;

	public function __construct( Energieausweis $energieausweis ) {
		$this->energieausweis = $energieausweis;
		$this->calculate();
	}

	private function calculate() {
		$calculations = array();

		/**
		 * Anlegen des Grundrisses.
		 */
		$grundriss = new Grundriss( $this->energieausweis->grundriss_form, $this->energieausweis->grundriss_richtung );

		foreach ( $grundriss->waende_manuell() as $wand ) {
			$wand_laenge_slug = 'wand_' . $wand . '_laenge';
			$wand_laenge      = $this->energieausweis->$wand_laenge_slug;
			$grundriss->wand_laenge( $wand, $wand_laenge );
		}

		/**
		 * Gebäude.
		 */
		$gebaeude = new Gebaeude(
			grundriss: $grundriss,
			baujahr: $this->energieausweis->baujahr,
			geschossanzahl: $this->energieausweis->geschoss_zahl,
			geschosshoehe: $this->energieausweis->geschoss_hoehe,
			anzahl_wohnungen: $this->energieausweis->wohnungen,
			standort_heizsystem: $this->energieausweis->h_standort,
			waermebruecken_zuschlag: 0.05
		);

		$calculations['gebaeude'] = $gebaeude;

		$gwert_fenster = wpenon_immoticket24_get_g_wert( $this->energieausweis->fenster_bauart );

		/**
		 * Referenz U-Werte.
		 */
		$referenz_uwert_aussenwand_luft = 0.28;
		$referenz_uwert_aussenwand_erde = 0.35;
		$referenz_uwert_dach            = 0.2;
		$referenz_uwert_fenster         = 0.6;

		switch ( $this->energieausweis->dach ) {
			case 'beheizt':
				$kniestock_hoehe = isset( $this->energieausweis->kniestock_hoehe ) ? $this->energieausweis->kniestock_hoehe : 0.0;
				$daemmung_dach   = isset( $this->energieausweis->dach_daemmung ) ? $this->energieausweis->dach_daemmung : 0.0;

				switch ( $this->energieausweis->dach_form ) {
					case 'walmdach':
						$dach = new Walmdach(
							grundriss: $grundriss,
							name: __( 'Walmdach', 'wpenon' ),
							hoehe: $this->energieausweis->dach_hoehe,
							kniestock_hoehe: $kniestock_hoehe,
							uwert: $referenz_uwert_dach,
							daemmung: $daemmung_dach
						);
						break;
					case 'satteldach':
						$dach = new Satteldach(
							grundriss: $grundriss,
							name: __( 'Satteldach', 'wpenon' ),
							hoehe: $this->energieausweis->dach_hoehe,
							kniestock_hoehe: $kniestock_hoehe,
							uwert: $referenz_uwert_dach,
							daemmung: $daemmung_dach
						);
						break;
					case 'pultdach':
						$dach = new Pultdach(
							grundriss: $grundriss,
							name: __( 'Pultdach', 'wpenon' ),
							hoehe: $this->energieausweis->dach_hoehe,
							kniestock_hoehe: $kniestock_hoehe,
							uwert: $referenz_uwert_dach,
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
					uwert: $referenz_uwert_dach,
					daemmung: $this->energieausweis->decke_daemmung,
				);

				$gebaeude->bauteile()->hinzufuegen( $decke );
				break;
			case 'nicht-vorhanden':
			default:
				$daemmung_dach = isset( $this->energieausweis->dach_daemmung ) ? $this->energieausweis->dach_daemmung : 0.0;
				$uwert_dach    = $referenz_uwert_dach;

				$dach = new Flachdach(
					grundriss: $grundriss,
					name: __( 'Flachdach', 'wpenon' ),
					uwert: $uwert_dach,
					daemmung: $daemmung_dach,
				);

				$gebaeude->bauteile()->hinzufuegen( $dach );
		}

		if ( $this->energieausweis->anbau ) {
			$grundriss_anbau = new Grundriss_Anbau( $this->energieausweis->anbau_form, $this->energieausweis->grundriss_richtung );

			// Hinzufügen der angegebenen Wandlängen zum Grundriss des Anbaus.
			foreach ( $grundriss_anbau->seiten_manuell() as $wand ) {
				$wand_laenge_slug = 'anbauwand_' . $wand . '_laenge';
				$wand_laenge      = $this->energieausweis->$wand_laenge_slug;
				$grundriss_anbau->wand_laenge( $wand, $wand_laenge );
			}

			$gebaeude->anbau( new Anbau( $grundriss_anbau, $this->energieausweis->anbau_hoehe ) );

			// Hinzufügen der Bauteile des Anbaus zum Gebäude.
			$anbauwand_bauart_feldname = 'anbauwand_bauart_' . $this->energieausweis->gebaeudekonstruktion;
			$anbauwand_bauart_name     = $this->energieausweis->$anbauwand_bauart_feldname;
			$uwert_anbau_wand          = uwert( 'wand_' . $anbauwand_bauart_name, $this->energieausweis->anbau_baujahr );

			$uwert_anbau_fenster = $this->energieausweis->anbaufenster_uwert_info ? $this->energieausweis->anbaufenster_uwert : uwert( 'fenster_' . $this->energieausweis->fenster_bauart, $this->energieausweis->fenster_baujahr );

			foreach ( $gebaeude->anbau()->grundriss()->waende() as $wand ) {
				$anbauwand = new Anbauwand(
					name: sprintf( __( 'Anbauwand %s', 'wpenon' ), $wand ),
					seite: $wand,
					flaeche: $gebaeude->anbau()->wandseite_flaeche( $wand ),
					uwert: $referenz_uwert_aussenwand_luft,
					himmelsrichtung: $grundriss_anbau->wand_himmelsrichtung( $wand ),
					daemmung: $this->energieausweis->anbauwand_daemmung,
				);

				$fenster_flaeche = berechne_fenster_flaeche( $grundriss_anbau->wand_laenge( $wand ), $this->energieausweis->anbau_hoehe, $this->energieausweis->anbauwand_staerke / 100 );

				$fenster = new Anbaufenster(
					name: sprintf( __( 'Anbaufenster Wand %s', 'wpenon' ), $wand ),
					gwert: $gwert_fenster,
					uwert: $referenz_uwert_fenster,
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
					uwert: $referenz_uwert_aussenwand_erde,
					daemmung: $this->energieausweis->anbauboden_daemmung,
				)
			);

			$gebaeude->bauteile()->hinzufuegen(
				new Anbaudecke(
					name: sprintf( __( 'Anbau-Dach', 'wpenon' ) ),
					grundriss: $grundriss_anbau,
					uwert: $referenz_uwert_dach,
					daemmung: $this->energieausweis->anbaudach_daemmung,
				)
			);
		}

		$wand_bauart_feld_name = 'wand_bauart_' . $this->energieausweis->gebaeudekonstruktion;
		$wand_bauart           = $this->energieausweis->$wand_bauart_feld_name;
		$uwert_wand            = uwert( 'wand_' . $wand_bauart, $this->energieausweis->baujahr );

		foreach ( $grundriss->waende() as $wand ) {
			$nachbar_slug = 'wand_' . $wand . '_nachbar';

			if ( $this->energieausweis->$nachbar_slug ) { // Wenn es eine Wand zum Nachbar ist, dann wird diese nicht als Außenwand gewertet und entfällt.
				continue;
			}

			$daemmung_slug = 'wand_' . $wand . '_daemmung';

			$wand_laenge  = $gebaeude->grundriss()->wand_laenge( $wand );
			$wand_hoehe   = $gebaeude->geschosshoehe() * $gebaeude->geschossanzahl();
			$wand_flaeche = $wand_laenge * $wand_hoehe;

			$wand = new Wand(
				// translators: %s: Seite der Wand.
				name: sprintf( __( 'Außenwand %s', 'wpenon' ), $wand ),
				seite: $wand,
				flaeche: $wand_flaeche,
				uwert: $referenz_uwert_aussenwand_luft,
				himmelsrichtung: $gebaeude->grundriss()->wand_himmelsrichtung( $wand ),
				daemmung: $this->energieausweis->$daemmung_slug,
				grenzt_an_wohngebaeude: $this->energieausweis->$nachbar_slug
			);

			$gebaeude->bauteile()->hinzufuegen( $wand );
		}

		foreach ( $gebaeude->bauteile()->waende()->alle() as $wand ) {
			if ( $wand->grenzt_an_wohngebaeude() ) {
				continue;
			}

			$fensterflaeche        = $heizkoerpernische_flaeche = $rolladenkaesten_flaeche = 0.0;
			$wand_ursprungsflaeche = $wand->flaeche();

			// Ist ein beheiztes Dachgeschoss vorhanden, muss das Mauerwerk für die Wand hinzugefügt werden.
			if ( $gebaeude->dach_vorhanden() ) {
				$dachwand_flaeche           = $gebaeude->dach()->wandseite_flaeche( $wand->seite() );
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

			if ( $gebaeude->anbau_vorhanden() ) {
				$wand_laenge_uberlappung       = $gebaeude->anbau()->ueberlappung_laenge_wand( $wand->seite() );
				$wand_laenge_ohne_ueberlappung = $wand_laenge - $wand_laenge_uberlappung;

				// Bei wie vielen Stockwerken entsteht eine Überlappung mit dem Anbau?
				$anzahl_ueberlappende_geschosse = ceil( $gebaeude->anbau()->hoehe() / $gebaeude->geschosshoehe() );
				$anzahl_ueberlappende_geschosse = $anzahl_ueberlappende_geschosse > $gebaeude->geschossanzahl() ? $gebaeude->geschossanzahl() : $anzahl_ueberlappende_geschosse;

				$anzahl_nicht_ueberlappende_geschosse = ( $gebaeude->geschossanzahl() - $anzahl_ueberlappende_geschosse ) > 0 ? $gebaeude->geschossanzahl() - $anzahl_ueberlappende_geschosse : 0;

				// Berechne Fensterflächen an Wänden, wo eine Überlappung vorhanden ist
				$fensterflaeche_1 = berechne_fenster_flaeche( $wand_laenge_ohne_ueberlappung, $this->energieausweis->geschoss_hoehe, $this->energieausweis->wand_staerke / 100 ) * $anzahl_ueberlappende_geschosse;

				// Berechne Fensterflächen an Wänden, wo keine Überlappung vorhanden ist
				$fensterflaeche_2 = berechne_fenster_flaeche( $wand_laenge, $this->energieausweis->geschoss_hoehe, $this->energieausweis->wand_staerke / 100 ) * $anzahl_nicht_ueberlappende_geschosse;

				$fensterflaeche = $fensterflaeche_1 + $fensterflaeche_2;

			} else {
				$fensterflaeche = berechne_fenster_flaeche( $wand_laenge, $this->energieausweis->geschoss_hoehe, $this->energieausweis->wand_staerke / 100 ) * $this->energieausweis->geschoss_zahl;  // Hier die Lichte Höhe und nicht die Geschosshöhe verwenden um die Fenster zu berechnen.
			}

			$uwert_fenster   = $this->energieausweis->fenster_uwert_info ? $this->energieausweis->fenster_uwert : uwert( 'fenster_' . $this->energieausweis->fenster_bauart, $this->energieausweis->fenster_baujahr );
			$himmelsrichtung = $gebaeude->grundriss()->wand_himmelsrichtung( $wand->seite() );

			$fenster = new Fenster(
				name: sprintf( __( 'Fenster Wand %s', 'wpenon' ), $wand->name() ),
				gwert: $gwert_fenster,
				uwert: $referenz_uwert_fenster,
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
			if ( $this->energieausweis->heizkoerpernischen === 'vorhanden' ) {
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
			if ( substr( $this->energieausweis->rollladenkaesten, 0, 6 ) === 'innen_' ) { // Wir nehmen nur innenliegende Rolladenkästen.
				$rolladenkaesten_flaeche = berechne_rolladenkasten_flaeche( $fensterflaeche );
				$daemmung                = substr( $this->energieausweis->rollladenkaesten, 6 );
				$uwert_rolladenkaesten   = uwert( 'rollladen_' . $daemmung, $this->energieausweis->fenster_baujahr );

				$rolladenkasten = new Rolladenkasten(
					// translators: % s: Seite der Wand .
					name: sprintf( __( 'Rolladenkasten Wand %s', 'wpenon' ), $wand->seite() ),
					flaeche: $rolladenkaesten_flaeche,
					uwert: $referenz_uwert_aussenwand_luft,
					himmelsrichtung: $himmelsrichtung
				);

				$gebaeude->bauteile()->hinzufuegen( $rolladenkasten );
				$wand->flaeche_reduzieren( $rolladenkaesten_flaeche );
			}
		}

		/**
		 * Sammlung aller Bauteile des Kellers.
		 */
		switch ( $this->energieausweis->keller ) {
			case 'beheizt':
				$keller = new Keller( $grundriss, $this->energieausweis->keller_groesse, $this->energieausweis->keller_hoehe );
				$gebaeude->keller( $keller );

				$gebaeude->bauteile()->hinzufuegen(
					new Kellerwand(
						name: __( 'Kellerwand', 'wpenon' ),
						flaeche: $gebaeude->keller()->wandseite_flaeche(),
						uwert: $referenz_uwert_aussenwand_erde,
						daemmung: $this->energieausweis->keller_daemmung,
					)
				);

				$kellerflaeche = $gebaeude->grundriss()->flaeche() * $this->energieausweis->keller_groesse / 100;

				$gebaeude->bauteile()->hinzufuegen(
					new Kellerboden(
						name: sprintf( __( 'Kellerboden', 'wpenon' ) ),
						flaeche: $kellerflaeche,
						uwert: $referenz_uwert_aussenwand_erde,
						daemmung: $this->energieausweis->boden_daemmung,
					)
				);

				if ( $this->energieausweis->keller_groesse < 100 ) {
					$gebaeude->bauteile()->hinzufuegen(
						new Boden(
							name: sprintf( __( 'Boden', 'wpenon' ) ),
							flaeche: $gebaeude->grundriss()->flaeche() - $kellerflaeche,
							uwert: $referenz_uwert_aussenwand_erde,
							daemmung: $this->energieausweis->boden_daemmung,
						)
					);
				}

				break;
			case 'unbeheizt':
				$keller = new Keller( $grundriss, $this->energieausweis->keller_groesse, 0 );
				$gebaeude->keller( $keller );

				$kellerflaeche = $gebaeude->grundriss()->flaeche() * $this->energieausweis->keller_groesse / 100;

				$gebaeude->bauteile()->hinzufuegen(
					new Kellerboden(
						name: sprintf( __( 'Kellerboden', 'wpenon' ) ),
						flaeche: $kellerflaeche,
						uwert: $referenz_uwert_aussenwand_erde,
						daemmung: $this->energieausweis->boden_daemmung,
					)
				);

				if ( $this->energieausweis->keller_groesse < 100 ) {
					$gebaeude->bauteile()->hinzufuegen(
						new Boden(
							name: sprintf( __( 'Boden', 'wpenon' ) ),
							flaeche: $gebaeude->grundriss()->flaeche() - $kellerflaeche,
							uwert: $referenz_uwert_aussenwand_erde,
							daemmung: $this->energieausweis->boden_daemmung,
						)
					);
				}

				break;
			case 'nicht-vorhanden':
			case 'unbeheizt':
			default:
				$gebaeude->bauteile()->hinzufuegen(
					new Boden(
						name: sprintf( __( 'Boden', 'wpenon' ) ),
						flaeche: $gebaeude->grundriss()->flaeche(),
						uwert: $referenz_uwert_aussenwand_erde,
						daemmung: $this->energieausweis->boden_daemmung,
					)
				);
		}

		// NOTE: Vereinfachung vom 08.02.2024 - Issue #618
		if ( $this->energieausweis->l_info === 'vorhanden' ) {
			$lueftungssystem   = 'zu_abluft';
			$gebaeudedichtheit = 'din_4108_7';
			$bedarfsgefuehrt   = true;

			if ( $gebaeude->baujahr() <= 1999 ) {
				$wirkungsgrad = 54;
			} elseif ( $gebaeude->baujahr() >= 2000 && $gebaeude->baujahr() <= 2009 ) {
				$wirkungsgrad = 60;
			} else {
				$wirkungsgrad = 80;
			}
		} else {
			$lueftungssystem   = 'ohne';
			$gebaeudedichtheit = 'andere';
			$wirkungsgrad      = 0;
			$bedarfsgefuehrt   = false;
		}

        $gebaeudedichtheit = 'din_4108_7'; // Referenzwert;

		$gebaeude->lueftung(
			new Lueftung(
				gebaeude: $gebaeude,
				lueftungssystem: 'abluft',
				art: 'zentral', // Referenzwert
				bedarfsgefuehrt: false, // Referenzwert
				gebaeudedichtheit: $gebaeudedichtheit,
				wirkungsgrad: $wirkungsgrad,
			)
		);

		$heizung_im_beheizten_bereich = $gebaeude->nutzflaeche() <= 500 ? true : false; // Referenzwert

		$gebaeude->trinkwarmwasseranlage(
			new Trinkwarmwasseranlage(
				gebaeude: $gebaeude,
				zentral: true, // Referenzwert
				erzeuger: 'brennwertkessel', // Referenzwert
				heizung_im_beheizten_bereich: $heizung_im_beheizten_bereich,
				mit_warmwasserspeicher: true,
				mit_zirkulation: true, // Referenzwert
				mit_solarthermie: $this->energieausweis->solarthermie_info === 'vorhanden' ? true : false,
				solarthermie_neigung: $this->energieausweis->solarthermie_info === 'vorhanden' ? $this->energieausweis->solarthermie_neigung : null,
				solarthermie_richtung: $this->energieausweis->solarthermie_info === 'vorhanden' ? $this->energieausweis->solarthermie_richtung : null,
				solarthermie_baujahr:$this->energieausweis->solarthermie_info === 'vorhanden' ? $this->energieausweis->solarthermie_baujahr : null
			)
		);

		/**
		 * Heizsysteme
		 */
		$energietraeger_name   = 'h_energietraeger_' . $this->energieausweis->h_erzeugung;
		$h_prozentualer_anteil = ! isset( $this->energieausweis->h_deckungsanteil ) || $this->energieausweis->h_deckungsanteil === 0 ? 100 : $this->energieausweis->h_deckungsanteil;        
		$gebaeude->heizsystem()->heizungsanlagen()->hinzufuegen( 'brennwertkessel', 'erdgas', $this->energieausweis->h_baujahr, $h_prozentualer_anteil, false, false, null, null, null );

		if ( $this->energieausweis->h2_info ) {
			$gebaeude->heizsystem()->heizungsanlagen()->hinzufuegen( 'brennwertkessel', 'erdgas', $this->energieausweis->h2_baujahr, $this->energieausweis->h2_deckungsanteil, false, false, null, null, null );

			if ( $this->energieausweis->h3_info ) {
				$gebaeude->heizsystem()->heizungsanlagen()->hinzufuegen( 'brennwertkessel', 'erdgas', $this->energieausweis->h3_baujahr, $this->energieausweis->h3_deckungsanteil, false, false, null, null, null );
			}
		}

		$heizungen[] = array(
			'uebergabe'           => $this->energieausweis->h_uebergabe,
			'flaechenheizungstyp' => $this->energieausweis->h_uebergabe === 'flaechenheizung' ? $this->energieausweis->h_uebergabe_flaechenheizungstyp : null,
			'erzeugung'           => $this->energieausweis->h_erzeugung,
		);

		if ( $this->energieausweis->h2_info ) {
			$heizungen[] = array(
				'uebergabe'           => $this->energieausweis->h_uebergabe,
				'flaechenheizungstyp' => $this->energieausweis->h_uebergabe === 'flaechenheizung' ? $this->energieausweis->h_uebergabe_flaechenheizungstyp : null,
				'erzeugung'           => $this->energieausweis->h2_erzeugung,
			);

			if ( $this->energieausweis->h3_info ) {
				$heizungen[] = array(
					'uebergabe'           => $this->energieausweis->h_uebergabe,
					'flaechenheizungstyp' => $this->energieausweis->h_uebergabe === 'flaechenheizung' ? $this->energieausweis->h_uebergabe_flaechenheizungstyp : null,
					'erzeugung'           => $this->energieausweis->h3_erzeugung,
				);
			}
		}

		$auslegungstemperaturen = '55/45'; // Referenzwert

        $gebaeude->heizsystem()->uebergabesysteme()->hinzufuegen(
            new Uebergabesystem(
                gebaeude: $gebaeude,
                typ: 'heizkoerper', // Referenzwert
                auslegungstemperaturen: $auslegungstemperaturen,
                prozentualer_anteil: 100 // Erst 100%, später dann anteilmäßig mit $this->energieausweis->h_uebergabe_anteil
            )
        );

		if ( $this->energieausweis->pv_info === 'vorhanden' ) {
			$gebaeude->photovoltaik_anlage(
				new Photovoltaik_Anlage(
					gebaeude: $gebaeude,
					richtung: $this->energieausweis->pv_richtung,
					neigung: $this->energieausweis->pv_neigung,
					flaeche: floatval( $this->energieausweis->pv_flaeche ),
					baujahr: intval( $this->energieausweis->pv_baujahr ),
				)
			);
		}	

		$this->gebaeude = $gebaeude;
	}

	/**
	 * Transmissionswärmeverluste.
	 * 
	 * @return float 
	 */
	public function ht_ref_geb(): float {
		$watt = 0;

		if ( $this->energieausweis->gebaeudetyp === 'freistehend' ) {
			if ( $this->gebaeude->nutzflaeche() < 350 ) {
				$watt = 0.4;
			} else {
				$watt = 0.5;
			}
		} elseif ( $this->energieausweis->gebaeudetyp === 'reiheneckhaus' ) {
			$watt = 0.45;
		} else {
			$watt = 0.65;
		}

		return 1.4 * $watt;
	}

	/**
	 * Primärenergiebedarf.
	 * 
	 * @return float 
	 */
	public function Qp_ref_geb(): float {
		return 1.4 * $this->gebaeude->Qp();
	}

	/**
	 * Gebäude.
	 * 
	 * @return Gebaeude 	 
	 */
	public function gebaeude(): Gebaeude {
		return $this->gebaeude;
	}
}
