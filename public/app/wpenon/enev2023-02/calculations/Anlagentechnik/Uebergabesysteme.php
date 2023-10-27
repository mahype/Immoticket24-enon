<?php 

namespace Enev\Schema202302\Calculations\Anlagentechnik;

require __DIR__ . '/Uebergabesystem.php';

/**
 * Berechnung mehrerer Übergabesysteme (Heizkörper).
 */
class Uebergabesysteme {
    /**
     * Liste der Uebergabesystemn.
     * 
     * @var Uebergabesystem[]
     */
    protected array $uebergabesysteme = [];

    /**
     * Hinzufügen eines Uebergabesystems.
     * 
     * @var Uebergabesystem
     */
    public function hinzufuegen( Uebergabesystem $uebergabesystem ) {
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
     * Validierung des prozentualen Anteils aller Uebergabesystemn.
     * 
     * @return bool 
     */
    protected function validiere_prozent_gesamt(): bool {
        $prozent_gesamt = 0;

        foreach ($this->uebergabesysteme as $uebergabesystem) {
            $prozent_gesamt += $uebergabesystem->prozentualer_anteil();
        }

        return $prozent_gesamt === 100;
    }
}