<?php

/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

use Enon\Enon\Standards\Schema;
use Exception;

class EnergieausweisPDF2024 extends \WPENON\Util\UFPDI
{
	private $wpenon_title = '';
	private $wpenon_type = 'bw';
	private $wpenon_standard = 'enev2013';
	private $wpenon_preview = false;

	private $wpenon_img_path = '';
	private $wpenon_pdf_path = '';

	private $wpenon_width = 210;
	private $wpenon_height = 295;
	private $wpenon_margin_h = 3.5;
	private $wpenon_margin_v = 0;

	private $wpenon_energieausweis = null;
	private $wpenon_seller_meta = array();

	public function __construct($title, $type, $standard, $preview = false)
	{
		$this->wpenon_title    = $title;
		$this->wpenon_type     = $type;
		$this->wpenon_standard = $standard;
		$this->wpenon_preview  = $preview;

		$this->wpenon_img_path = WPENON_PATH . '/assets/img/pdf/';
		$this->wpenon_pdf_path = WPENON_PATH . '/assets/pdf/';
		$this->wpenon_colors   = array(
			'background' => array(255, 255, 255),
			'text'       => array(0, 0, 0),
			'gray'       => array(222, 222, 222),
			'bright'     => array(255, 218, 162),
			'dark'       => array(210, 124, 1),
			'blue'       => array(0, 0, 255),
			'red'        => array(255, 0, 0),
		);
		$this->wpenon_fonts    = array(
			'default'    => array('Arial', '', 11, 5.54),
			'small'      => array('Arial', '', 8, 4),
			'marker'     => array('Arial', 'B', 7, 6.64),
			'enev_datum' => array('Arial', '', 11, 5),
			'registrier' => array('Arial', '', 7, 5),
			'aussteller' => array('Arial', '', 10, 5.5),
			'skala'      => array('Arial', 'B', 11, 7),
			'klassen'    => array('Arial', '', 14, 6.64),
			'klassen_b'  => array('Arial', 'B', 20, 6.64),
			'kennwerte'  => array('Arial', 'B', 12, 6),
			'preview'    => array('Arial', 'B', 144, 100),
		);

		$this->wpenon_colors = apply_filters('wpenon_pdf_colors', $this->wpenon_colors);
		$this->wpenon_fonts  = apply_filters('wpenon_pdf_fonts', $this->wpenon_fonts);

		parent::__construct('P', 'mm', 'A4');

		$this->SetTitle($this->wpenon_title);
		$this->SetAutoPageBreak(false);
		$this->SetMargins($this->wpenon_margin_h, $this->wpenon_margin_v, $this->wpenon_margin_h);
	}

	public function __isset($name)
	{
		if (strpos($name, 'wpenon_') !== 0) {
			$name = 'wpenon_' . $name;
		}

		return property_exists($this, $name);
	}

	public function __get($name)
	{
		if (strpos($name, 'wpenon_') !== 0) {
			$name = 'wpenon_' . $name;
		}
		if (property_exists($this, $name)) {
			return $this->$name;
		}

		return null;
	}

	public function create($energieausweis)
	{
		if (is_a($energieausweis, '\WPENON\Model\Energieausweis')) {
			$this->wpenon_energieausweis = $energieausweis;
			$this->wpenon_type           = $this->wpenon_energieausweis->wpenon_type;
			$this->wpenon_standard       = $this->wpenon_energieausweis->wpenon_standard;
			$payment_id                  = $this->wpenon_energieausweis->getPayment();
			if ($payment_id !== null) {
				$payment_id = $payment_id->ID;
			}
			$paymentmeta              = \WPENON\Util\PaymentMeta::instance();
			$this->wpenon_seller_meta = $paymentmeta->getSellerMeta($payment_id);

			\WPENON\Model\EnergieausweisManager::loadMappings('pdf', $this->wpenon_standard);
		} else {
			$this->wpenon_energieausweis = null;
			$paymentmeta                 = \WPENON\Util\PaymentMeta::instance();
			$this->wpenon_seller_meta    = $paymentmeta->getSellerMeta();
		}

		$sourceFile = $this->wpenon_pdf_path . 'energieausweis_' . substr($this->wpenon_type, 1, 1) . '_2024.pdf';
		$this->setSourceFile($sourceFile);

		for ($i = 0; $i < 5; $i++) {
			$this->addPage('P', array($this->wpenon_width, $this->wpenon_height));
			$this->useTemplate($this->importPage($i + 1, '/MediaBox'), $this->wpenon_margin_h, $this->wpenon_margin_v, $this->wpenon_width - 2 * $this->wpenon_margin_h);
			$this->renderPage($i + 1);
		}

		if (file_exists($this->wpenon_pdf_path . 'energieausweis_' . substr($this->wpenon_type, 1, 1) . '_aushang.pdf')) {
			$this->addPage('P', array($this->wpenon_width, $this->wpenon_height));
			$this->setSourceFile($this->wpenon_pdf_path . 'energieausweis_' . substr($this->wpenon_type, 1, 1) . '_aushang.pdf');
			$this->useTemplate($this->importPage(1, '/MediaBox'), $this->wpenon_margin_h, $this->wpenon_margin_v, $this->wpenon_width - 2 * $this->wpenon_margin_h);
			$this->renderPage(6);
		}
	}

	public function finalize($output_mode = 'I')
	{
		return $this->Output($this->wpenon_title . '.pdf', $output_mode);
	}


	private function renderPage($index)
	{
		$override = apply_filters('wpenon_override_energieausweis_pdf_' . $index, false, $this);

		$schema = new Schema($this->wpenon_standard);
		$standard_date = $schema->get_date('d.m.Y');

		if (!$override) {
			switch ($index) {
				case 1:
					$this->SetXY(121.5, 19.5);
					$this->SetPageFont('enev_datum');
					$this->WriteCell($standard_date, 'R', 0, 23);

					$this->SetPageFont('registrier');

					$this->SetXY(162.5, 30);
					$this->WriteCell($this->GetData('registriernummer', 0, true), 'C', 0, 24);

					$this->SetPageFont('small');

					$this->SetXY(24, 35.5);
					$this->WriteCell(\WPENON\Model\EnergieausweisManager::instance()->getExpirationDate('d.m.Y', $this->energieausweis), 'L', 0, 24);

					/**
					 * Gebäudedaten
					 */
					$this->SetPageFont('default');

					$this->SetXY(71, 53.7);

					// Generell
					$this->WriteCell($this->GetData('gebaeudetyp'), 'L', 2, 90.3);
					$this->WriteCell($this->GetData('adresse', 0, true), 'L', 2, 90.3);
					$this->WriteCell($this->GetData('gebaeudeteil'), 'L', 2, 90.3);
					$this->WriteCell($this->GetData('baujahr'), 'L', 2, 90.3);
					$this->WriteCell($this->GetData('baujahr_erzeuger'), 'L', 2, 90.3);
					$this->WriteCell($this->GetData('wohnungen'), 'L', 2, 90.3);

					if ($this->GetData('nutzflaeche_aus_wohnflaeche')) {
						$x = $this->GetX();
						$y = $this->GetY();
						$this->WriteCell($this->GetData('nutzflaeche') . \WPENON\Util\Format::pdfEncode(' m&sup2;'), 'L', 0, 24);
						$this->CheckBox($x + 25.5, $y + 3.2);
						$this->Ln();
						$this->SetX($x);
						$this->SetPageFont('default');
					} else {
						$x = $this->GetX();
						$this->WriteCell($this->GetData('nutzflaeche') . \WPENON\Util\Format::pdfEncode(' m&sup2;'), 'L', 0, 99.3);
						$this->Ln();
						$this->SetX($x);
					}

					// Heizung / Warmwasser
					$this->WriteCell($this->GetData('energietraeger_heizung'), 'L', 2, 99.3, 5.5);
					$this->WriteCell($this->GetData('energietraeger_warmwasser'), 'L', 2, 99.3, 5.5);
					$x = $this->GetX();

					$this->SetY($this->GetY() + 0.64);
					$this->SetX($x + 6);
					$this->WriteCell($this->GetData('regenerativ_art'), 'L', 0, 63, $this->wpenon_fonts['default'][3] - 0.64);
					$this->SetX($x + 91);
					$this->WriteCell($this->GetData('regenerativ_nutzung'), 'L', 2, 45, $this->wpenon_fonts['default'][3] - 0.64);
					$this->SetX($x);

					$y            = $this->GetY();
					$lueftungsart = $this->GetData('lueftungsart');

					switch ($lueftungsart) {
						case 'ohne':
						case 'fenster':
						case 'nicht_vorhanden':
							$this->CheckBox($x + 2.4, $y + 3.3);
							break;
						case 'schacht': // TODO: Gibt es nicht mehr zur Auswahl
							$this->CheckBox($x + 2.4, $y + 7.0);
							break;
						case 'zu_abluft':
						case 'mitgewinnung':
							$this->CheckBox($x + 34.7, $y + 3.4);
							break;
						case 'abluft':
						case 'ohnegewinnung':
							$this->CheckBox($x + 34.7, $y + 7);
							break;
						default:
							break;
					}

					if ($this->GetData('kuehlung')) {
						$this->CheckBox($x + 34.7, $y + 12.4);

						if ($this->GetData('inspektionspflichtige_klimaanlagen')) {
							$y = $this->getY();
							$this->SetY($y + 20.0);
							$this->SetX($x + 15);
							$this->WriteCell(1, 'L', 2, 100, $this->wpenon_fonts['default'][3] - 0.64);

							$this->SetY($y + 22.0);
							$this->SetX($x + 36.0);
							$this->WriteCell($this->GetData('inspektion_faelligkeit'), 'L', 2, 0, $this->wpenon_fonts['default'][3] - 0.64);
						}
					}

					$y      += 21.5;
					$anlass = $this->GetData('anlass');
					switch ($anlass) {
						case 'neubau':
							$this->CheckBox($x + 2.4, $y + 9.4);
							break;
						case 'modernisierung':
							$this->CheckBox($x + 45, $y + 9.4);
							break;
						case 'verkauf':
							$this->CheckBox($x + 2.2, $y + 13);
							break;
						case 'sonstiges':
						default:
							$this->CheckBox($x + 94.3, $y + 9.4);
							break;
					}

					$imageOld = $this->GetData('thumbnail_id', 0, true);
					$imageNew = $this->GetData('gebauedefoto', 0, true);

					$image = '';

					if (!empty($imageNew)) {
						$image = \WPENON\Util\ThumbnailHandler::urlToPath($imageNew);
					} elseif ($imageOld) {
						$image = \WPENON\Util\ThumbnailHandler::getImagePath($imageOld, 'enon-energieausweiss-image');
					}

					if ($image) {
						$this->SetPageFillColor('bright');
						$this->Rect(161.5, 53.8, 41, 49.2, 'F');
						try {
							$this->WriteBoundedImage($image, 161.2, 53.3, 41.8, 50.4);
						} catch (Exception $e) {
							$text = "Das Foto kann nicht geladen werden. Bitte laden Sie das Foto in einem anderen Format hoch.";
							$this->SetY(65.0);
							$this->SetX(162.0);
							$this->WriteMultiCell($text, 'C', 1, 40, 5);
						}

						$this->SetPageFillColor('background');
					}

					if (substr($this->wpenon_type, 0, 1) == 'b') {
						$this->CheckBox(9.8, 185.0);
					} else {
						$this->CheckBox(9.8, 194.0);
					}

					if ($this->GetData('datenerhebung_durch_aussteller')) {
						$this->CheckBox(123.7, 206.1);
					} else {
						$this->CheckBox(78.8, 206.1);
					}

					if ($this->GetData('zusatzinformationen_beigefuegt')) {
						$this->CheckBox(10.0, 211.9);
					}


					$this->SetPageFont('small');
					$this->SetXY(110, 260);
					$this->WriteCell(\WPENON\Model\EnergieausweisManager::instance()->getReferenceDate('d.m.Y', $this->energieausweis), 'L', 1, 20);

					if (!$this->wpenon_preview) {
						$this->SetPageFont('aussteller');
						$this->SetXY(25, 247);

						$aussteller_firma = $this->GetData('sellermeta_firmenname');
						$aussteller_firma = apply_filters('wpenon_pdf_seller_company_name', $aussteller_firma, $this);
						$aussteller_daten = $this->GetData('sellermeta_strassenr') . "\n" . $this->GetData('sellermeta_plz') . ' ' . $this->GetData('sellermeta_ort') . "\n" . __('Telefon:', 'wpenon') . ' ' . $this->GetData('sellermeta_telefon');
						$aussteller_daten = apply_filters('wpenon_pdf_seller_meta', $aussteller_daten, $this);
						$this->SetStyle('B', true);
						$this->WriteCell($aussteller_firma, 'C', 2, 77);
						$this->SetStyle('B', false);
						$this->WriteMultiCell($aussteller_daten, 'C', 1, 77);

						if (file_exists(WPENON_DATA_PATH . '/pdf-signature.png')) {
							$this->Image(WPENON_DATA_PATH . '/pdf-signature.png', 150, 245, 45);
						}
					}
					break;
				case 2:
					$this->SetXY(121.5, 19.5);
					$this->SetPageFont('enev_datum');
					$this->WriteCell($standard_date, 'R', 0, 23);
					$this->SetPageFont('registrier');
					$this->SetXY(161, 29.5);
					$this->WriteCell($this->GetData('registriernummer', 0, true), 'C', 0, 24);
					if (substr($this->wpenon_type, 0, 1) == 'v') {
						$this->DrawEnergyBar(8, 61, $this->GetData('reference'), 'bedarf');
					} else {
						$this->SetPageFont('small');
						$this->SetXY(140, 54);
						$this->WriteCell($this->GetData('co2_emissionen'), 'R', 0, 11, 6.2);
						$this->DrawEnergyBar(8, 61, $this->GetData('reference'), 'bedarf', $this->GetData('endenergie'), $this->GetData('primaerenergie'));
						$this->SetPageFont('small');

						$anlass = $this->GetData('anlass');

						if ($anlass !== 'verkauf' && $anlass !== 'vermietung') {
							$this->SetXY(22, 125);
							$this->WriteCell($this->GetData('primaerenergie'), 'R', 0, 11, 5);
							$this->SetX(77);
							$this->WriteCell($this->GetData('primaerenergie_reference'), 'R', 0, 10, 5); // NOTE: Woher bekomme ich primaerenergie_reference?
							$this->SetXY(22, 136.3);
							$this->WriteCell($this->GetData('ht'), 'R', 0, 11, 6);
							$this->SetX(77);
							$this->WriteCell($this->GetData('ht_reference'), 'R', 0, 10, 6);  // NOTE: Woher bekomme ich ht_reference?
						}

						$verfahren = $this->GetData('verfahren');
						switch ($verfahren) {
							case 'din-v-18599':
								$this->CheckBox(114, 134.5);
								break;
							case 'din-v-4108-6':
							default:
								$this->CheckBox(114, 127.9);
								break;
						}
						if ($this->GetData('regelung_absatz5')) {
							$this->CheckBox(114, 140.4);
						}
						if ($this->GetData('verfahren_vereinfacht')) {
							$this->CheckBox(114, 147);
						}
						if ($this->GetData('waermeschutz_eingehalten')) {
							$this->CheckBox(70.6, 147);
						}
						$this->SetXY(173, 151.5);
						$this->SetPageFont('kennwerte');
						$this->WriteCell($this->GetData('endenergie'), 'R', 0, 29);
					}
					break;
				case 3:
					$this->SetXY(121.5, 19.5);
					$this->SetPageFont('enev_datum');
					$this->WriteCell($standard_date, 'R', 0, 23);
					$this->SetPageFont('registrier');
					$this->SetXY(161, 29.5);
					$this->WriteCell($this->GetData('registriernummer', 0, true), 'C', 0, 24);

					$this->SetXY(131.0, 53.9);
					$this->SetPageFont('kennwerte');
					$this->WriteCell($this->GetData('co2_emissionen'), 'C', 0, 24);

					if (substr($this->wpenon_type, 0, 1) == 'b') {
						$this->DrawEnergyBar(8, 55, $this->GetData('reference'), 'verbrauch');
					} else {
						$this->DrawEnergyBar(8, 60, $this->GetData('reference'), 'verbrauch', $this->GetData('endenergie'), $this->GetData('primaerenergie'));
						$this->SetXY(166, 112.8);
						$this->SetPageFont('kennwerte');
						$this->WriteCell($this->GetData('endenergie'), 'R', 0, 36);
						$this->SetY(148.4);
						$this->SetPageFont('small');
						$verbrauchserfassung = $this->GetData('verbrauchserfassung');
						foreach ($verbrauchserfassung as $jahr) {
							$this->SetX(8);
							$this->WriteCell($jahr['start'], 'C', 0, 20.5, 6.65);
							$this->WriteCell($jahr['ende'], 'C', 0, 21.8, 6.65);
							$this->WriteCell($jahr['energietraeger'], 'C', 0, 29.8, 6.65);
							$this->WriteCell($jahr['primaer'], 'R', 0, 19.8, 6.65);
							$this->WriteCell($jahr['gesamt'], 'R', 0, 32.8, 6.65);
							$this->WriteCell($jahr['warmwasser'], 'R', 0, 24, 6.65);
							$this->WriteCell($jahr['heizung'], 'R', 0, 25.3, 6.65);
							$this->WriteCell($jahr['klima'], 'R', 1, 20, 6.65);
						}
					}
					break;
				case 4:
					$this->SetXY(121.5, 19.5);
					$this->SetPageFont('enev_datum');
					$this->WriteCell($standard_date, 'R', 0, 23);
					$this->SetPageFont('registrier');
					$this->SetXY(161, 29.5);
					$this->WriteCell($this->GetData('registriernummer', 0, true), 'C', 0, 24);
					$modernisierungsempfehlungen = \WPENON\Util\Parse::arr($this->GetData('modernisierungsempfehlungen'));
					if (count($modernisierungsempfehlungen) > 0) {
						$this->CheckBox(141.4, 59.5);
					} else {
						$this->CheckBox(173, 59.5);
					}
					$counter = 0;
					$y       = 100.5;
					if (substr($this->wpenon_type, 1, 1) == 'n') {
						foreach ($modernisierungsempfehlungen as $modernisierung) {
							$counter++;
							$this->SetPageFont('default');
							$this->SetXY(8, $y);
							$this->WriteCell($counter, 'R', 0, 10, 8);
							$this->SetPageFont('small');
							$this->WriteMultiCell($modernisierung['bauteil'], 'L', 0, 34.2, 4, false, 2);
							$this->SetXY(52.2, $y);
							$this->WriteMultiCell($modernisierung['beschreibung'], 'L', 0, 65.7, 4, false, 2);
							if (isset($modernisierung['gesamt']) && $modernisierung['gesamt']) {
								$this->CheckBox(131.8, $y + 4.5);
							}
							if (isset($modernisierung['einzeln']) && $modernisierung['einzeln']) {
								$this->CheckBox(154.7, $y + 4.5);
							}
							$this->SetXY(164, $y);
							$this->SetPageFont('default');
							$this->WriteCell($modernisierung['amortisation'], 'R', 0, 19, 8);
							$this->WriteCell($modernisierung['kosten'], 'R', 1, 20, 8);
							$y += 8;
						}
					} else {
						foreach ($modernisierungsempfehlungen as $modernisierung) {
							$counter++;
							$this->SetPageFont('default');
							$this->SetXY(8, $y);
							$this->WriteCell($counter, 'R', 0, 10, 8);
							$this->SetPageFont('small');
							$this->WriteMultiCell($modernisierung['bauteil'], 'L', 0, 34.2, 4, false, 2);
							$this->SetXY(52.2, $y);
							$this->WriteMultiCell($modernisierung['beschreibung'], 'L', 0, 65.7, 4, false, 2);
							if (isset($modernisierung['gesamt']) && $modernisierung['gesamt']) {
								$this->CheckBox(130.5, $y + 4.5);
							}
							if (isset($modernisierung['einzeln']) && $modernisierung['einzeln']) {
								$this->CheckBox(152.1, $y + 4.5);
							}
							$this->SetXY(160.8, $y);
							$this->SetPageFont('default');
							$this->WriteCell($modernisierung['amortisation'], 'R', 0, 19, 8);
							$this->WriteCell($modernisierung['kosten'], 'R', 1, 23, 8);
							$y += 8;
						}
					}
					$this->SetXY(90, 201.5);
					$this->SetPageFont('default');
					$modernisierungsempfehlungen_info = $this->GetData('sellermeta_firmenname') . ' - ' . $this->GetData('sellermeta_strassenr') . ', ' . $this->GetData('sellermeta_plz') . ' ' . $this->GetData('sellermeta_ort') . "\n" . __('Telefon:', 'wpenon') . ' ' . $this->GetData('sellermeta_telefon');
					$this->WriteMultiCell($modernisierungsempfehlungen_info, 'L', 0, 112, null, false, 2);
					break;
				case 5:
					$this->SetXY(121.5, 19.5);
					$this->SetPageFont('enev_datum');
					$this->WriteCell($standard_date, 'R', 0, 23);
					break;
				case 6:
					if (substr($this->wpenon_type, 1, 1) == 'n') {
						$this->CreatePage(6);
						$this->SetXY(122.5, 19.5);
						$this->SetPageFont('enev_datum');
						$this->WriteCell($standard_date, 'R', 0, 23);
						$this->SetPageFont('registrier');
						$this->SetXY(140, 29.5);
						$this->WriteCell($this->GetData('registriernummer', 0, true), 'C', 0, 24);
						$this->SetPageFont('small');
						$this->SetXY(24, 35);
						$this->WriteCell(\WPENON\Model\EnergieausweisManager::instance()->getExpirationDate('d.m.Y', $this->energieausweis), 'L', 0, 24);
						$this->SetPageFont('default');
						$this->SetXY(60.8, 53.8);
						$this->WriteMultiCell($this->GetData('gebaeudetyp'), 'L', 2, 104.5, 5.5, false, 2);
						$this->SetXY(60.8, 64.8);
						$this->WriteCell($this->GetData('adresse', 0, true), 'L', 2, 104.5);
						$this->WriteCell($this->GetData('gebaeudeteil'), 'L', 2, 104.5);
						$this->WriteCell($this->GetData('baujahr'), 'L', 2, 104.5);
						$this->WriteCell($this->GetData('nutzflaeche') . \WPENON\Util\Format::pdfEncode(' m&sup2;'), 'L', 2, 104.5);
						$this->WriteCell($this->GetData('energietraeger_heizung'), 'L', 2, 104.5, 5.5);
						$this->WriteCell($this->GetData('energietraeger_warmwasser'), 'L', 2, 104.5, 5.5);
						$x = $this->GetX();
						$this->SetY($this->GetY() + 0.64);



						$this->SetX($x + 8);
						$this->WriteCell($this->GetData('regenerativ_art'), 'L', 0, 63, $this->wpenon_fonts['default'][3] - 0.64);
						$this->SetX($x + 96);
						$this->WriteCell($this->GetData('regenerativ_nutzung'), 'L', 2, 45, $this->wpenon_fonts['default'][3] - 0.64);

						$this->SetX($x);
						$image = $this->GetData('thumbnail_id', 0, true);
						if ($image) {
							$this->SetPageFillColor('bright');
							$this->Rect(166.2, 54.7, 35, 47, 'F');
							$this->WriteBoundedImage(\WPENON\Util\ThumbnailHandler::getImagePath($image, 'enon-energieausweiss-image'), 165.2, 53.7, 37, 49);
							$this->SetPageFillColor('background');
						}

						$this->DrawEnergyBar(8, 124, $this->GetData('reference'), 'verbrauch', $this->GetData('endenergie'));
						if ($this->GetData('warmwasser_enthalten')) {
							$this->CheckBox(9.7, 171.7);
						}
						$this->DrawEnergyBar(8, 177, $this->GetData('s_reference'), 'strom', $this->GetData('s_endenergie'));
						$s_nutzung = \WPENON\Util\Parse::arr($this->GetData('s_nutzung'));
						if (in_array('heizung', $s_nutzung)) {
							$this->CheckBox(9.7, 230.7);
						}
						if (in_array('warmwasser', $s_nutzung)) {
							$this->CheckBox(45.6, 230.7);
						}
						if (in_array('lueftung', $s_nutzung)) {
							$this->CheckBox(80, 230.7);
						}
						if (in_array('beleuchtung', $s_nutzung)) {
							$this->CheckBox(104.2, 230.7);
						}
						if (in_array('kuehlung', $s_nutzung)) {
							$this->CheckBox(154.2, 230.7);
						}
						if (in_array('sonstiges', $s_nutzung)) {
							$this->CheckBox(178.6, 230.7);
						}
						$this->SetXY(165.5, 236);
						$this->SetPageFont('kennwerte');
						$this->WriteCell($this->GetData('primaerenergie'), 'R', 0, 19.5);

						$this->SetPageFont('small');
						$this->SetXY(112, 269);
						$this->WriteCell(\WPENON\Model\EnergieausweisManager::instance()->getReferenceDate('d.m.Y', $this->energieausweis), 'L', 1, 20);

						if (!$this->wpenon_preview) {
							$this->SetPageFont('aussteller');
							$this->SetXY(24, 252);
							$aussteller_firma = $this->GetData('sellermeta_firmenname');
							$aussteller_firma = apply_filters('wpenon_pdf_seller_company_name', $aussteller_firma, $this);
							$aussteller_daten = $this->GetData('sellermeta_strassenr') . "\n" . $this->GetData('sellermeta_plz') . ' ' . $this->GetData('sellermeta_ort') . "\n" . __('Telefon:', 'wpenon') . ' ' . $this->GetData('sellermeta_telefon');
							$aussteller_daten = apply_filters('wpenon_pdf_seller_meta', $aussteller_daten, $this);
							$this->SetStyle('B', true);
							$this->WriteCell($aussteller_firma, 'C', 2, 77);
							$this->SetStyle('B', false);
							$this->WriteMultiCell($aussteller_daten, 'C', 1, 77);

							if (file_exists(WPENON_DATA_PATH . '/pdf-signature.png')) {
								$this->Image(WPENON_DATA_PATH . '/pdf-signature.png', 137, 251, 45);
							}
						}
					}
					break;
				default:
					break;
			}
		}

		if ($this->wpenon_preview) {
			$this->DrawPreviewText();
		}
	}

	public function GetData($context, $index = 0, $override = false)
	{
		$data = '';
		if (strpos($context, 'sellermeta_') === 0) {
			$key = str_replace('sellermeta_', '', $context);
			if (isset($this->wpenon_seller_meta[$key])) {
				$data = $this->wpenon_seller_meta[$key];
			}
		} elseif ($this->energieausweis !== null) {
			if ($override) {
				$formatted_context = 'formatted_' . $context;
				if (isset($this->energieausweis->$formatted_context)) {
					$data = $this->energieausweis->$formatted_context;
				} elseif (isset($this->energieausweis->$context)) {
					$data = $this->energieausweis->$context;
				}
			} else {
				$data = call_user_func('wpenon_get_enev_pdf_data', $context, $index, $this->energieausweis);
			}
		}

		if (!empty($data)) {
			$data = \WPENON\Util\Format::pdfEncode($data);
		} else {
			$data = '';
		}

		return $data;
	}

	public function DrawEnergyBar($x, $y, $reference_value, $type = 'verbrauch', $value1 = null, $value2 = null)
	{
		$old_font = $this->wpenon_current_font;

		$border_width        = 4.5;
		$border_left_offset  = 12.5;
		$border_right_offset = 16.7;

		$offset_divisor = $reference_value / 80.128205;

		if ($value1 !== null) {
			$value1 = \WPENON\Util\Parse::float($value1);
		}

		if ($value2 !== null) {
			$value2 = \WPENON\Util\Parse::float($value2);
		}

		//$this->Rect( $x, $y, $this->wpenon_width - 2 * $this->wpenon_margin_h - 2 * $border_width, 52, 'D' );
		$this->Image($this->wpenon_img_path . 'energy-bar.png', $x, $y, $this->wpenon_width - 2 * $this->wpenon_margin_h - 2 * $border_width);

		if ($type != 'strom') {
			$effizienzklassen = \WPENON\Model\EnergieausweisManager::getAvailableClasses($this->wpenon_type);
			if (count($effizienzklassen) > 0) {
				$this->SetXY($x + $border_left_offset, $y + 17);
				$this->SetPageFont('klassen');
				$fettgedruckt    = false;
				$vorheriger_wert = 0;
				$gesamt_offset   = 0.0;
				foreach ($effizienzklassen as $klasse => $wert) {
					if (isset($value1) && $value1 < $wert && $fettgedruckt == false) {
						$this->SetPageFont('klassen_b');
					}
					$offset        = ($wert - $vorheriger_wert) / $offset_divisor;
					$border        = 'R';
					$gesamt_offset += $offset;
					if ($x + $border_left_offset + $gesamt_offset > $this->wpenon_width - $this->wpenon_margin_h - $border_width - $border_right_offset) {
						$offset = $this->wpenon_width - $this->wpenon_margin_h - $border_width - $border_right_offset - ($x + $border_left_offset + $gesamt_offset - $offset);
						$border = 0;
					}
					$this->Cell($offset, 6.5, $klasse, $border, 0, 'C');
					$vorheriger_wert = $wert;
					if (isset($value1) && $value1 < $wert && $fettgedruckt == false) {
						$this->SetPageFont('klassen');
						$fettgedruckt = true;
					}
					if ($x + $border_left_offset + $gesamt_offset > $this->wpenon_width - $this->wpenon_margin_h - $border_width - $border_right_offset) {
						break;
					}
				}
			}
		}

		$this->SetXY($x + $border_left_offset - 7.4, $y + 23.5);
		$this->SetPageFont('skala');
		for ($i = 0; $i <= 10; $i++) {
			$text = absint(($reference_value / 5) * $i);
			if ($i == 10) {
				$text = '>' . $text;
			}
			$this->WriteCell($text, 'C', 0, 16);
		}

		$this->SetPageFont('kennwerte');
		$this->SetPageFillColor('gray');

		if ($value1 !== null) {
			$value1_move = $value1;
			if ($value1 > $reference_value * 2) {
				$value1_move = $reference_value * 2;
			} elseif ($value1 < 0.0) {
				$value1_move = 0.0;
			}

			$this->Image($this->wpenon_img_path . 'arrow-down.png', $x + $border_left_offset + $value1_move / $offset_divisor - 6, $y + 4, 12);
			if ($type == 'bedarf') {
				$text = 'Endenergiebedarf dieses Geb&auml;udes';
			} elseif ($type == 'strom') {
				$text = 'Endenergieverbrauch Strom';
			} elseif ($type == 'verbrauch' && substr($this->wpenon_type, 1, 1) == 'n') {
				$text = 'Endenergieverbrauch W&auml;rme';
			} else {
				$text = 'Endenergieverbrauch dieses Geb&auml;udes';
			}

			if ($value1 > $reference_value) {
				$this->SetXY($x + $border_left_offset, $y + 4);
				$this->WriteCell(\WPENON\Util\Format::pdfEncode($text), 'R', 2, $value1_move / $offset_divisor - 6);
				$this->WriteCell('', 'R', 0, $value1_move / $offset_divisor - 6 - 40);
				$this->WriteCell(\WPENON\Util\Format::float($value1), 'C', 0, 15, null, true);
				$this->WriteCell(\WPENON\Util\Format::pdfEncode('kWh/(m&sup2;*a)'), 'R', 2, 25);
			} else {
				$this->SetXY($x + $border_left_offset + $value1 / $offset_divisor + 6, $y + 4);
				$this->WriteCell(\WPENON\Util\Format::pdfEncode($text), 'L', 2, 90);
				$this->WriteCell(\WPENON\Util\Format::float($value1), 'C', 0, 15, null, true);
				$this->WriteCell(\WPENON\Util\Format::pdfEncode('kWh/(m&sup2;*a)'), 'L', 2, 25);
			}
		}

		if ($value2 !== null && substr($this->wpenon_type, 1, 1) == 'w') {
			$value2_move = $value2;
			if ($value2 > $reference_value * 2) {
				$value2_move = $reference_value * 2;
			} elseif ($value2 < 0.0) {
				$value2_move = 0.0;
			}

			$this->Image($this->wpenon_img_path . 'arrow-up.png', $x + $border_left_offset + $value2_move / $offset_divisor - 6, $y + 37.5, 12);
			if ($type == 'bedarf') {
				$text = 'Prim&auml;renergiebedarf dieses Geb&auml;udes';
			} else {
				$text = 'Prim&auml;renergieverbrauch dieses Geb&auml;udes';
			}

			if ($value2 > $reference_value) {
				$this->SetXY($x + $border_left_offset, $y + 37.5);
				$this->WriteCell('', 'R', 0, $value2_move / $offset_divisor - 6 - 40);
				$this->WriteCell(\WPENON\Util\Format::float($value2), 'C', 0, 15, null, true);
				$this->WriteCell(\WPENON\Util\Format::pdfEncode('kWh/(m&sup2;*a)'), 'R', 2, 25);
				$this->SetX($x + $border_left_offset);
				$this->WriteCell(\WPENON\Util\Format::pdfEncode($text), 'R', 2, $value2_move / $offset_divisor - 6);
			} else {
				$this->SetXY($x + $border_left_offset + $value2_move / $offset_divisor + 6, $y + 37.5);
				$this->WriteCell(\WPENON\Util\Format::float($value2), 'C', 0, 15, null, true);
				$this->WriteCell(\WPENON\Util\Format::pdfEncode('kWh/(m&sup2;*a)'), 'L', 2, 25);
				$this->SetX($x + $border_left_offset + $value2_move / $offset_divisor + 6);
				$this->WriteCell(\WPENON\Util\Format::pdfEncode($text), 'L', 2, 90);
			}
		}

		if (substr($this->wpenon_type, 1, 1) == 'n') {
			if ($type == 'verbrauch') {
				$this->Image($this->wpenon_img_path . 'comparison-waerme.png', $x + $border_left_offset + $reference_value / $offset_divisor - 1, $y + 37.5, 65);
			} elseif ($type == 'strom') {
				$this->Image($this->wpenon_img_path . 'comparison-strom.png', $x + $border_left_offset + $reference_value / $offset_divisor - 1, $y + 37.5, 65);
			}
		}

		$this->SetXY($x, $y + 46.5);
		$this->SetPageFont($old_font);
		$this->SetPageFillColor('background');
	}

	public function DrawPreviewText()
	{
		$orig_font = $this->wpenon_current_font;

		$this->SetPageFont('preview');
		$this->SetPageTextColor('red');

		$this->Rotate(55, 40, 280);
		$this->Text(40, 280, 'VORSCHAU');
		$this->Rotate(0);

		$this->SetPageFont($orig_font);
		$this->SetPageTextColor('text');
	}

	/**
	 * UTILITY FUNCTIONS
	 */

	public function CheckBox($x, $y)
	{
		$orig_font = $this->wpenon_current_font;

		$this->SetPageFont('marker');
		$this->Text($x, $y, 'x');

		$this->SetPageFont($orig_font);
	}
}
