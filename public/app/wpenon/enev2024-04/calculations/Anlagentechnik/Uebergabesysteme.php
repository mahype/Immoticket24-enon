<?php

namespace Enev\Schema202404\Calculations\Anlagentechnik;

require __DIR__ . '/Uebergabesystem.php';

/**
 * Berechnung mehrerer Übergabesysteme (Heizkörper).
 */
class Uebergabesysteme
{
	/**
	 * Liste der Uebergabesystemn.
	 *
	 * @var Uebergabesystem[]
	 */
	protected array $uebergabesysteme = array();

	/**
	 * Hinzufügen eines Uebergabesystems.
	 *
	 * @var Uebergabesystem
	 */
	public function hinzufuegen(Uebergabesystem $uebergabesystem)
	{
		$this->uebergabesysteme[] = $uebergabesystem;
	}

	/**
	 * Alle Uebergabesysteme.
	 *
	 * @return Uebergabesystem[]
	 */
	public function alle(): array
	{
		return $this->uebergabesysteme;
	}

	/**
	 * Erstes Uebergabesystem (derzeit auch einziges Überabesystem).
	 * 
	 * @return Uebergabesystem 
	 */
	public function erstes(): Uebergabesystem
	{
		return $this->uebergabesysteme[0];
	}

	/**
	 * Validierung des prozentualen Anteils aller Uebergabesystemn.
	 *
	 * @return bool
	 */
	protected function validiere_prozent_gesamt(): bool
	{
		$prozent_gesamt = 0;

		foreach ($this->uebergabesysteme as $uebergabesystem) {
			$prozent_gesamt += $uebergabesystem->prozentualer_anteil();
		}

		return $prozent_gesamt === 100;
	}
}
