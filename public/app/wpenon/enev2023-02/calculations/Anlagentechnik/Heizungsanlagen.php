<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Calculation_Exception;

require_once __DIR__ . '/Heizungsanlage.php';

/**
 * Berechnung mehrerer Heizungsanlagen.
 */
class Heizungsanlagen {

	/**
	 * Liste der Heizungsanlagen.
	 *
	 * @var Heizungsanlage[]
	 */
	protected array $heizungsanlagen = array();

	/**
	 * Hinzufügen einer Heizungsanlage.
	 *
	 * @var Heizungsanlage
	 */
	public function hinzufuegen( Heizungsanlage $heizungsanlage ) {
		$this->heizungsanlagen[] = $heizungsanlage;
	}

	/**
	 * Alle Heizungsanlagen.
	 *
	 * @return Heizungsanlage[]
	 */
	public function alle(): array {
		return $this->heizungsanlagen;
	}

	/**
	 * Validierung des prozentualen Anteils aller Heizungsanlagen.
	 *
	 * @return bool
	 */
	protected function validiere_prozent_gesamt(): bool {
		$prozent_gesamt = 0;

		foreach ( $this->heizungsanlagen as $heizungsanlage ) {
			$prozent_gesamt += $heizungsanlage->prozentualer_anteil();
		}

		return $prozent_gesamt === 100;
	}

	/**
	 * Nutzbare Wärme.
	 *
	 * @return float
	 */
	public function fa_h() {
		$fa_h = 0;

		// Validieren der prozentualen Anteile.
		if ( ! $this->validiere_prozent_gesamt() ) {
			throw new Exception( 'Die prozentualen Anteile aller Heizungsanlagen müssen zusammen 100% ergeben.' );
		}

		foreach ( $this->heizungsanlagen as $heizungsanlage ) {
			$fa_h += $heizungsanlage->fa_h() * $heizungsanlage->prozentualer_anteil() / 100;
		}

		return $fa_h;
	}

	/**
	 * Ist eine Wärmepumpe vorhanden?
	 * 
	 * @return bool 
	 */
	public function waermepumpe_vorhanden(): bool {
		foreach ( $this->heizungsanlagen as $heizungsanlage ) {
			if ( strpos( $heizungsanlage->typ(), 'waermepumpe' ) === 0 ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Ist ein Biomassekessel vorhanden?
	 * 
	 * @return bool 
	 */
	public function biomassekessel_vorhanden(): bool {
		foreach ( $this->heizungsanlagen as $heizungsanlage ) {
			if ( strpos( $heizungsanlage->typ(), 'biomassekessel' ) === 0 ) {
				return true;
			}
		}

		return false;
	}
}
