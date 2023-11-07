<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

require_once __DIR__ . '/Wasserversorgung.php';

/**
 * Berechnung mehrerer Übergabesysteme (Heizkörper).
 */
class Wasserversorgungen {
	/**
	 * Liste der Uebergabesystemn.
	 *
	 * @var Wasserversorgung[]
	 */
	protected array $wasserversorgungen = array();

	/**
	 * Hinzufügen eines Uebergabesystems.
	 *
	 * @var Wasserversorgung
	 */
	public function hinzufuegen( Wasserversorgung $wasserversorgung ) {
		$this->wasserversorgungen[] = $wasserversorgung;
	}

	/**
	 * Alle Uebergabesysteme.
	 *
	 * @return Uebergabesystem[]
	 */
	public function alle(): array {
		return $this->wasserversorgungen;
	}

	/**
	 * Validierung des prozentualen Anteils aller Uebergabesystemn.
	 *
	 * @return bool
	 */
	protected function validiere_prozent_gesamt(): bool {
		$prozent_gesamt = 0;

		foreach ( $this->wasserversorgungen as $wasserversorgung ) {
			$prozent_gesamt += $wasserversorgung->prozentualer_anteil();
		}

		return $prozent_gesamt === 100;
	}
}
