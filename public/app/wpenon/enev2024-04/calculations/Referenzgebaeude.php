<?php

/**
 * Referenzgebaeude.
 *
 * @package wpenon
 */

namespace Enev\Schema202404\Calculations;

use Enev\Schema202404\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202404\Calculations\Gebaeude\Grundriss;
use Enev\Schema202404\Calculations\Gebaeude\Anbau;
use Enev\Schema202404\Calculations\Gebaeude\Grundriss_Anbau;
use Enev\Schema202404\Calculations\Gebaeude\Keller;

use Enev\Schema202404\Calculations\Anlagentechnik\Lueftung;
use Enev\Schema202404\Calculations\Anlagentechnik\Photovoltaik_Anlage;
use Enev\Schema202404\Calculations\Anlagentechnik\Uebergabesystem;
use Enev\Schema202404\Calculations\Anlagentechnik\Trinkwarmwasseranlage;
use Enev\Schema202404\Calculations\Bauteile\Anbauboden;
use Enev\Schema202404\Calculations\Bauteile\Anbaudecke;
use Enev\Schema202404\Calculations\Bauteile\Anbaufenster;
use Enev\Schema202404\Calculations\Bauteile\Anbauwand;
use Enev\Schema202404\Calculations\Bauteile\Boden;
use Enev\Schema202404\Calculations\Bauteile\Decke;
use Enev\Schema202404\Calculations\Bauteile\Fenster;
use Enev\Schema202404\Calculations\Bauteile\Flachdach;
use Enev\Schema202404\Calculations\Bauteile\Heizkoerpernische;
use Enev\Schema202404\Calculations\Bauteile\Kellerboden;
use Enev\Schema202404\Calculations\Bauteile\Kellerwand;
use Enev\Schema202404\Calculations\Bauteile\Pultdach;
use Enev\Schema202404\Calculations\Bauteile\Rolladenkasten;
use Enev\Schema202404\Calculations\Bauteile\Satteldach;
use Enev\Schema202404\Calculations\Bauteile\Walmdach;
use Enev\Schema202404\Calculations\Bauteile\Wand;

use function Enev\Schema202404\Calculations\Helfer\berechne_fenster_flaeche;
use function Enev\Schema202404\Calculations\Helfer\berechne_heizkoerpernische_flaeche;
use function Enev\Schema202404\Calculations\Helfer\berechne_rolladenkasten_flaeche;
use function Enev\Schema202404\Calculations\Tabellen\uwert;

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

class Referenzgebaeude
{

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

	public function __construct(Energieausweis $energieausweis)
	{
		$this->energieausweis = $energieausweis;
		$this->calculate();
	}

	private function calculate()
	{
		$calculations = array();

		/**
		 * Anlegen des Grundrisses.
		 */
		$grundriss = new Grundriss($this->energieausweis->grundriss_form, $this->energieausweis->grundriss_richtung);

		foreach ($grundriss->waende_manuell() as $wand) {
			$wand_laenge_slug = 'wand_' . $wand . '_laenge';
			$wand_laenge      = $this->energieausweis->$wand_laenge_slug;
			$grundriss->wand_laenge($wand, $wand_laenge);
		}

		/**
		 * Gebäude.
		 */
		$gebaeude = new Gebaeude(
			grundriss: $grundriss,
			baujahr: $this->energieausweis->baujahr,
			gebaeudetyp: $this->energieausweis->gebaeudetyp,
			geschossanzahl: $this->energieausweis->geschoss_zahl,
			geschosshoehe: $this->energieausweis->geschoss_hoehe,
			anzahl_wohnungen: $this->energieausweis->wohnungen,
			standort_heizsystem: $this->energieausweis->h_standort,
			waermebruecken_zuschlag: 0.05,
			referenzgebaeude: true
		);

		$calculations['gebaeude'] = $gebaeude;

		$gwert_fenster = 0.6;

		/**
		 * Referenz U-Werte.
		 */
		$referenz_uwert_aussenwand_luft = 0.28;
		$referenz_uwert_aussenwand_erde = 0.35;
		$referenz_uwert_dach            = 0.2;
		$referenz_uwert_fenster         = 1.3;

		switch ($this->energieausweis->dach) {
			case 'beheizt':
				$kniestock_hoehe = isset($this->energieausweis->kniestock_hoehe) ? $this->energieausweis->kniestock_hoehe : 0.0;
				$daemmung_dach   = 0.0;

				switch ($this->energieausweis->dach_form) {
					case 'walmdach':
						$dach = new Walmdach(
							grundriss: $grundriss,
							name: __('Walmdach', 'wpenon'),
							hoehe: $this->energieausweis->dach_hoehe,
							kniestock_hoehe: $kniestock_hoehe,
							uwert: $referenz_uwert_dach,
							daemmung: $daemmung_dach
						);
						break;
					case 'satteldach':
						$dach = new Satteldach(
							grundriss: $grundriss,
							name: __('Satteldach', 'wpenon'),
							hoehe: $this->energieausweis->dach_hoehe,
							kniestock_hoehe: $kniestock_hoehe,
							uwert: $referenz_uwert_dach,
							daemmung: $daemmung_dach
						);
						break;
					case 'pultdach':
						$dach = new Pultdach(
							grundriss: $grundriss,
							name: __('Pultdach', 'wpenon'),
							hoehe: $this->energieausweis->dach_hoehe,
							kniestock_hoehe: $kniestock_hoehe,
							uwert: $referenz_uwert_dach,
							daemmung: $daemmung_dach
						);
						break;
					default:
						throw new Calculation_Exception('Dachform nicht bekannt.');
				}

				$gebaeude->bauteile()->hinzufuegen($dach);

				break;
			case 'unbeheizt':
				$decke = new Decke(
					name: __('Oberste Geschossdecke', 'wpenon'),
					grundriss: $grundriss,
					uwert: $referenz_uwert_dach,
					daemmung: 0,
				);

				$gebaeude->bauteile()->hinzufuegen($decke);
				break;
			case 'nicht-vorhanden':
			default:
				$daemmung_dach = 0.0;
				$uwert_dach    = $referenz_uwert_dach;

				$dach = new Flachdach(
					grundriss: $grundriss,
					name: __('Flachdach', 'wpenon'),
					uwert: $uwert_dach,
					daemmung: $daemmung_dach,
				);

				$gebaeude->bauteile()->hinzufuegen($dach);
		}

		if ($this->energieausweis->anbau) {
			$grundriss_anbau = new Grundriss_Anbau($this->energieausweis->anbau_form, $this->energieausweis->grundriss_richtung);

			// Hinzufügen der angegebenen Wandlängen zum Grundriss des Anbaus.
			foreach ($grundriss_anbau->seiten_manuell() as $wand) {
				$wand_laenge_slug = 'anbauwand_' . $wand . '_laenge';
				$wand_laenge      = $this->energieausweis->$wand_laenge_slug;
				$grundriss_anbau->wand_laenge($wand, $wand_laenge);
			}

			$gebaeude->anbau(new Anbau($grundriss_anbau, $this->energieausweis->anbau_hoehe));

			// Hinzufügen der Bauteile des Anbaus zum Gebäude.
			$anbauwand_bauart_feldname = 'anbauwand_bauart_' . $this->energieausweis->gebaeudekonstruktion;

			foreach ($gebaeude->anbau()->grundriss()->waende() as $wand) {
				$anbauwand = new Anbauwand(
					name: sprintf(__('Anbauwand %s', 'wpenon'), $wand),
					seite: $wand,
					flaeche: $gebaeude->anbau()->wandseite_flaeche($wand),
					uwert: $referenz_uwert_aussenwand_luft,
					himmelsrichtung: $grundriss_anbau->wand_himmelsrichtung($wand),
					daemmung: 0,
				);

				$fenster_flaeche = berechne_fenster_flaeche($grundriss_anbau->wand_laenge($wand), $this->energieausweis->anbau_hoehe, $this->energieausweis->anbauwand_staerke / 100);

				$fenster = new Anbaufenster(
					name: sprintf(__('Anbaufenster Wand %s', 'wpenon'), $wand),
					gwert: $gwert_fenster,
					uwert: $referenz_uwert_fenster,
					flaeche: $fenster_flaeche, // Hier die Lichte Höhe und nicht die Geschosshöhe verwenden um die Fenster zu berechnen.
					himmelsrichtung: $grundriss_anbau->wand_himmelsrichtung($wand),
					winkel: 90.0
				);

				$anbauwand->flaeche_reduzieren($fenster->flaeche());

				$gebaeude->bauteile()->hinzufuegen($fenster);
				$gebaeude->bauteile()->hinzufuegen($anbauwand);
			}

			$gebaeude->bauteile()->hinzufuegen(
				new Anbauboden(
					name: sprintf(__('Anbau-Boden', 'wpenon')),
					flaeche: $grundriss->flaeche(),
					uwert: $referenz_uwert_aussenwand_erde,
					daemmung: 0,
				)
			);

			$gebaeude->bauteile()->hinzufuegen(
				new Anbaudecke(
					name: sprintf(__('Anbau-Dach', 'wpenon')),
					grundriss: $grundriss_anbau,
					uwert: $referenz_uwert_dach,
					daemmung: 0,
				)
			);
		}

		$wand_bauart_feld_name = 'wand_bauart_' . $this->energieausweis->gebaeudekonstruktion;
		$wand_bauart           = $this->energieausweis->$wand_bauart_feld_name;
		$uwert_wand            = uwert('wand_' . $wand_bauart, $this->energieausweis->baujahr);

		foreach ($grundriss->waende() as $wand) {
			$nachbar_slug = 'wand_' . $wand . '_nachbar';

			if ($this->energieausweis->$nachbar_slug) { // Wenn es eine Wand zum Nachbar ist, dann wird diese nicht als Außenwand gewertet und entfällt.
				continue;
			}

			$daemmung_slug = 'wand_' . $wand . '_daemmung';

			$wand_laenge  = $gebaeude->grundriss()->wand_laenge($wand);
			$wand_hoehe   = $gebaeude->geschosshoehe() * $gebaeude->geschossanzahl();
			$wand_flaeche = $wand_laenge * $wand_hoehe;

			$wand = new Wand(
				// translators: %s: Seite der Wand.
				name: sprintf(__('Außenwand %s', 'wpenon'), $wand),
				seite: $wand,
				flaeche: $wand_flaeche,
				uwert: $referenz_uwert_aussenwand_luft,
				himmelsrichtung: $gebaeude->grundriss()->wand_himmelsrichtung($wand),
				daemmung: 0,
				grenzt_an_wohngebaeude: $this->energieausweis->$nachbar_slug
			);

			$gebaeude->bauteile()->hinzufuegen($wand);
		}

		foreach ($gebaeude->bauteile()->waende()->alle() as $wand) {
			if ($wand->grenzt_an_wohngebaeude()) {
				continue;
			}

			$fensterflaeche        = $heizkoerpernische_flaeche = $rolladenkaesten_flaeche = 0.0;
			$wand_ursprungsflaeche = $wand->flaeche();

			// Ist ein beheiztes Dachgeschoss vorhanden, muss das Mauerwerk für die Wand hinzugefügt werden.
			if ($gebaeude->dach_vorhanden()) {
				$dachwand_flaeche           = $gebaeude->dach()->wandseite_flaeche($wand->seite());
				$dachwand_flaeche_kniestock = $gebaeude->dach()->kniestock_flaeche($wand->seite());
				$wand->flaeche_addieren($dachwand_flaeche + $dachwand_flaeche_kniestock);
			}

			// Ist ein Anbau vorhanden, muss die überlappende Fläche vom Mauerwerk abgezogen werden.
			if ($gebaeude->anbau_vorhanden()) {
				$anbau_schnittflaeche = $gebaeude->anbau()->ueberlappung_flaeche_gebaeude($wand->seite());
				$wand->flaeche_reduzieren($anbau_schnittflaeche);
			}

			/**
			 * Fenster
			 */
			$wand_laenge = $gebaeude->grundriss()->wand_laenge($wand->seite());

			if ($gebaeude->anbau_vorhanden()) {
				$wand_laenge_uberlappung       = $gebaeude->anbau()->ueberlappung_laenge_wand($wand->seite());
				$wand_laenge_ohne_ueberlappung = $wand_laenge - $wand_laenge_uberlappung;

				// Bei wie vielen Stockwerken entsteht eine Überlappung mit dem Anbau?
				$anzahl_ueberlappende_geschosse = ceil($gebaeude->anbau()->hoehe() / $gebaeude->geschosshoehe());
				$anzahl_ueberlappende_geschosse = $anzahl_ueberlappende_geschosse > $gebaeude->geschossanzahl() ? $gebaeude->geschossanzahl() : $anzahl_ueberlappende_geschosse;

				$anzahl_nicht_ueberlappende_geschosse = ($gebaeude->geschossanzahl() - $anzahl_ueberlappende_geschosse) > 0 ? $gebaeude->geschossanzahl() - $anzahl_ueberlappende_geschosse : 0;

				// Berechne Fensterflächen an Wänden, wo eine Überlappung vorhanden ist
				$fensterflaeche_1 = berechne_fenster_flaeche($wand_laenge_ohne_ueberlappung, $this->energieausweis->geschoss_hoehe, $this->energieausweis->wand_staerke / 100) * $anzahl_ueberlappende_geschosse;

				// Berechne Fensterflächen an Wänden, wo keine Überlappung vorhanden ist
				$fensterflaeche_2 = berechne_fenster_flaeche($wand_laenge, $this->energieausweis->geschoss_hoehe, $this->energieausweis->wand_staerke / 100) * $anzahl_nicht_ueberlappende_geschosse;

				$fensterflaeche = $fensterflaeche_1 + $fensterflaeche_2;
			} else {
				$fensterflaeche = berechne_fenster_flaeche($wand_laenge, $this->energieausweis->geschoss_hoehe, $this->energieausweis->wand_staerke / 100) * $this->energieausweis->geschoss_zahl;  // Hier die Lichte Höhe und nicht die Geschosshöhe verwenden um die Fenster zu berechnen.
			}

			$himmelsrichtung = $gebaeude->grundriss()->wand_himmelsrichtung($wand->seite());

			$fenster = new Fenster(
				name: sprintf(__('Fenster Wand %s', 'wpenon'), $wand->name()),
				gwert: $gwert_fenster,
				uwert: $referenz_uwert_fenster,
				flaeche: $fensterflaeche,
				himmelsrichtung: $himmelsrichtung,
				winkel: 90.0
			);

			$gebaeude->bauteile()->hinzufuegen($fenster);

			// Reduzieren der Wandfläche um die Fensterfläche.
			$wand->flaeche_reduzieren($fensterflaeche);

			/**
			 * Heizkörpernischen
			 */
			if ($this->energieausweis->heizkoerpernischen === 'vorhanden') {
				$heizkoerpernische_flaeche = berechne_heizkoerpernische_flaeche($fensterflaeche);

				$heizkoerpernische = new Heizkoerpernische(
					name: sprintf(__('Heizkörpernischen Wand %s', 'wpenon'), $wand->seite()),
					flaeche: $heizkoerpernische_flaeche,
					uwert_wand: $wand->uwert(),
					himmelsrichtung: $himmelsrichtung,
					daemmung: 0
				);

				$gebaeude->bauteile()->hinzufuegen($heizkoerpernische);
				$wand->flaeche_reduzieren($heizkoerpernische_flaeche);
			}

			/**
			 * Rolladenkästen.
			 */
			if (substr($this->energieausweis->rollladenkaesten, 0, 6) === 'innen_') { // Wir nehmen nur innenliegende Rolladenkästen.
				$rolladenkaesten_flaeche = berechne_rolladenkasten_flaeche($fensterflaeche);
				$daemmung                = substr($this->energieausweis->rollladenkaesten, 6);
				$uwert_rolladenkaesten   = uwert('rollladen_' . $daemmung, $this->energieausweis->fenster_baujahr);

				$rolladenkasten = new Rolladenkasten(
					// translators: % s: Seite der Wand .
					name: sprintf(__('Rolladenkasten Wand %s', 'wpenon'), $wand->seite()),
					flaeche: $rolladenkaesten_flaeche,
					uwert: $referenz_uwert_aussenwand_luft,
					himmelsrichtung: $himmelsrichtung
				);

				$gebaeude->bauteile()->hinzufuegen($rolladenkasten);
				$wand->flaeche_reduzieren($rolladenkaesten_flaeche);
			}
		}

		/**
		 * Sammlung aller Bauteile des Kellers.
		 */
		switch ($this->energieausweis->keller) {
			case 'beheizt':
				$keller = new Keller($grundriss, $this->energieausweis->keller_groesse, $this->energieausweis->keller_hoehe);
				$gebaeude->keller($keller);

				$gebaeude->bauteile()->hinzufuegen(
					new Kellerwand(
						name: __('Kellerwand', 'wpenon'),
						flaeche: $gebaeude->keller()->wandseite_flaeche(),
						uwert: $referenz_uwert_aussenwand_erde,
						daemmung: 0,
					)
				);

				$kellerflaeche = $gebaeude->grundriss()->flaeche() * $this->energieausweis->keller_groesse / 100;

				$gebaeude->bauteile()->hinzufuegen(
					new Kellerboden(
						name: sprintf(__('Kellerboden', 'wpenon')),
						flaeche: $kellerflaeche,
						uwert: $referenz_uwert_aussenwand_erde,
						daemmung: 0,
					)
				);

				if ($this->energieausweis->keller_groesse < 100) {
					$gebaeude->bauteile()->hinzufuegen(
						new Boden(
							name: sprintf(__('Boden', 'wpenon')),
							flaeche: $gebaeude->grundriss()->flaeche() - $kellerflaeche,
							uwert: $referenz_uwert_aussenwand_erde,
							daemmung: 0,
						)
					);
				}

				break;
			case 'unbeheizt':
				$keller = new Keller($grundriss, $this->energieausweis->keller_groesse, 0);
				$gebaeude->keller($keller);

				$kellerflaeche = $gebaeude->grundriss()->flaeche() * $this->energieausweis->keller_groesse / 100;

				$gebaeude->bauteile()->hinzufuegen(
					new Kellerboden(
						name: sprintf(__('Kellerboden', 'wpenon')),
						flaeche: $kellerflaeche,
						uwert: $referenz_uwert_aussenwand_erde,
						daemmung: 0,
					)
				);

				if ($this->energieausweis->keller_groesse < 100) {
					$gebaeude->bauteile()->hinzufuegen(
						new Boden(
							name: sprintf(__('Boden', 'wpenon')),
							flaeche: $gebaeude->grundriss()->flaeche() - $kellerflaeche,
							uwert: $referenz_uwert_aussenwand_erde,
							daemmung: 0,
						)
					);
				}

				break;
			case 'nicht-vorhanden':
			case 'unbeheizt':
			default:
				$gebaeude->bauteile()->hinzufuegen(
					new Boden(
						name: sprintf(__('Boden', 'wpenon')),
						flaeche: $gebaeude->grundriss()->flaeche(),
						uwert: $referenz_uwert_aussenwand_erde,
						daemmung: 0,
					)
				);
		}

		$gebaeude->lueftung(
			new Lueftung(
				gebaeude: $gebaeude,
				lueftungssystem: 'abluft',
				art: 'zentral',
				bedarfsgefuehrt: false,
				gebaeudedichtheit: 'din_4108_7',
				baujahr: $gebaeude->baujahr()
			)
		);

		$heizung_im_beheizten_bereich = $gebaeude->nutzflaeche() <= 500 ? true : false;

		$gebaeude->trinkwarmwasseranlage(
			new Trinkwarmwasseranlage(
				gebaeude: $gebaeude,
				zentral: true,
				erzeuger: 'brennwertkessel',
				heizung_im_beheizten_bereich: $heizung_im_beheizten_bereich,
				mit_warmwasserspeicher: true,
				mit_zirkulation: true,
				mit_solarthermie: true,
				solarthermie_neigung: 45,
				solarthermie_richtung: 's',
				solarthermie_baujahr: 1999
			)
		);

		/**
		 * Heizsysteme
		 */
		$gebaeude->heizsystem()->heizungsanlagen()->hinzufuegen('brennwertkessel', 'erdgas', $this->energieausweis->h_baujahr, 100, false, false, null, null, null);

		$gebaeude->heizsystem()->uebergabesysteme()->hinzufuegen(
			new Uebergabesystem(
				gebaeude: $gebaeude,
				typ: 'heizkoerper', // Referenzwert
				auslegungstemperaturen: '55/45',
				prozentualer_anteil: 100, // Erst 100%, später dann anteilmäßig mit $this->energieausweis->h_uebergabe_anteil
			)
		);

		$this->gebaeude = $gebaeude;
	}

	/**
	 * Gebäude.
	 * 
	 * @return Gebaeude 	 
	 */
	public function gebaeude(): Gebaeude
	{
		return $this->gebaeude;
	}
}
