<?php

namespace Enev\Schema202404\Calculations\Anlagentechnik;

use Enev\Schema202404\Calculations\Anlagentechnik\Heizungsanlagen\Fernwaerme;
use Enev\Schema202404\Calculations\Anlagentechnik\Heizungsanlagen\Dezentral;
use Enev\Schema202404\Calculations\Anlagentechnik\Heizungsanlagen\Konventioneller_Kessel;
use Enev\Schema202404\Calculations\Anlagentechnik\Heizungsanlagen\Waermepumpe;
use Enev\Schema202404\Calculations\Calculation_Exception;
use Enev\Schema202404\Calculations\Gebaeude\Gebaeude;

require_once __DIR__ . '/Heizungsanlage.php';
require_once __DIR__ . '/Heizungsanlagen/Konventioneller_Kessel.php';
require_once __DIR__ . '/Heizungsanlagen/Waermepumpe.php';
require_once __DIR__ . '/Heizungsanlagen/Fernwaerme.php';
require_once __DIR__ . '/Heizungsanlagen/Dezentral.php';

/**
 * Berechnung mehrerer Heizungsanlagen.
 */
class Heizungsanlagen
{
	/**
	 * Gebäude.
	 *
	 * @var Gebaeude
	 */
	protected Gebaeude $gebaeude;

	/**
	 * Liste der Heizungsanlagen.
	 *
	 * @var Heizungsanlage[]
	 */
	protected array $heizungsanlagen = array();

	/**
	 * Konstruktor.
	 *
	 * @param Gebaeude $gebaeude
	 * @return void
	 */
	public function __construct(Gebaeude $gebaeude)
	{
		$this->gebaeude = $gebaeude;
	}

	/**
	 * Hinzufügen einer Heizungsanlage.
	 *
	 * @var Heizungsanlage
	 */
	public function hinzufuegen(string $erzeuger, string $energietraeger, int $baujahr, int $prozentualer_anteil = 100, bool $evu_abschaltung = false, bool $einstufig = false, $h_waermepumpe_erde_typ = null, $fp, $fco2)
	{
		switch ($erzeuger) {
			case 'etagenheizung':
			case 'standardkessel':
			case 'niedertemperaturkessel':
			case 'brennwertkessel':
			case 'brennwertkesselverbessert': // ???
			case 'kleinthermeniedertemperatur':
			case 'kleinthermebrennwert':
				$this->heizungsanlagen[] = new Konventioneller_Kessel($this->gebaeude, $erzeuger, $energietraeger, $baujahr, $prozentualer_anteil, $fp, $fco2);
				break;
			case 'waermepumpeluft':
			case 'waermepumpewasser':
			case 'waermepumpeerde':
				$this->heizungsanlagen[] = new Waermepumpe($this->gebaeude, $erzeuger, $energietraeger, $baujahr, $prozentualer_anteil, $evu_abschaltung, $einstufig, $h_waermepumpe_erde_typ, $fp, $fco2);
				break;
			case 'fernwaerme':
				$this->heizungsanlagen[] = new Fernwaerme($this->gebaeude, $erzeuger, $energietraeger, $baujahr, $prozentualer_anteil, $fp, $fco2);
				break;
			case 'elektronachtspeicherheizung':
			case 'infrarotheizung':
				$this->heizungsanlagen[] = new Dezentral($this->gebaeude, $erzeuger, $energietraeger, $baujahr, $prozentualer_anteil, $fp, $fco2);
				break;
			default:
				throw new Calculation_Exception('Der Erzeuger "' . $erzeuger . '" ist nicht erlaubt.');
		}
	}

	/**
	 * Alle Heizungsanlagen.
	 *
	 * @return Heizungsanlage[]
	 */
	public function alle(): array
	{
		return $this->heizungsanlagen;
	}

	/**
	 * Anzahl der Heizungsanlagen.
	 *
	 * @return int
	 */
	public function anzahl(): int
	{
		return count($this->heizungsanlagen);
	}

	/**
	 * Validierung des prozentualen Anteils aller Heizungsanlagen.
	 *
	 * @return bool
	 */
	protected function validiere_prozent_gesamt(): bool
	{
		$prozent_gesamt = 0;

		foreach ($this->heizungsanlagen as $heizungsanlage) {
			$prozent_gesamt += $heizungsanlage->prozentualer_anteil();
		}

		return $prozent_gesamt === 100;
	}

	/**
	 * Bestimmung des Anteils nutzbarer Wärme von Heizungsanlagen.
	 * 
	 * @return float
	 */
	public function fa_h()
	{
		$fa_h = 0;

		// Validieren der prozentualen Anteile.
		if (!$this->validiere_prozent_gesamt()) {
			throw new Calculation_Exception('Die prozentualen Anteile aller Heizungsanlagen müssen zusammen 100% ergeben.');
		}

		foreach ($this->heizungsanlagen as $heizungsanlage) {
			$fa_h += $heizungsanlage->fa_h() * $heizungsanlage->prozentualer_anteil() / 100;
		}

		return $fa_h;
	}

	/**
	 * Ist eine Wärmepumpe vorhanden?
	 *
	 * @return bool
	 */
	public function waermepumpe_vorhanden(): bool
	{
		return $this->heizungstyp_vorhanden('waermepumpe');
	}

	/**
	 * Ist ein Biomassekessel vorhanden?
	 *
	 * @return bool
	 */
	public function biomassekessel_vorhanden(): bool
	{
		return $this->heizungstyp_vorhanden('biomassekessel');
	}

	/**
	 * Ist ein bestimmter Heizungstyp vorhanden?
	 *
	 * @param string $heizungstyp Folgende Heizungstypen können überprüft werden: standardkessel, niedertemperaturkessel, brennwertkessel, brennwertkesselverbessert, kleinthermeniedertemperatur, kleinthermebrennwert, waermepumpe, fernwaerme, elektronachtspeicherheizung, infrarotheizung.
	 *
	 * @return bool
	 */
	public function heizungstyp_vorhanden(string $heizungstyp)
	{
		foreach ($this->heizungsanlagen as $heizungsanlage) {
			if (strpos($heizungsanlage->typ(), $heizungstyp) === 0) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Qfhges für alle Heizungsanlagen.
	 *
	 * @return float
	 */
	public function Qfhges(): float
	{
		$Qfhges = 0;

		foreach ($this->heizungsanlagen as $heizungsanlage) {
			$Qfhges += $heizungsanlage->Qfhges();
		}

		return $Qfhges;
	}

	/**
	 * Qfwges für alle Heizungsanlagen.
	 *
	 * @return float
	 */
	public function Qfwges(): float
	{
		$Qfwges = 0;

		foreach ($this->heizungsanlagen as $heizungsanlage) {
			$Qfwges += $heizungsanlage->Qfwges();
		}

		return $Qfwges;
	}

	/**
	 * Qfstromges für alle Heizungsanlagen.
	 *
	 * @return float
	 */
	public function Qfstromges(): float
	{
		$Qfstromges = 0;

		foreach ($this->heizungsanlagen as $heizungsanlage) {
			if ($heizungsanlage->energietraeger() === 'strom') {
				$Qfstromges += $heizungsanlage->Qfhges() + $heizungsanlage->Qfwges();
			}
		}

		return $Qfstromges;
	}
}
