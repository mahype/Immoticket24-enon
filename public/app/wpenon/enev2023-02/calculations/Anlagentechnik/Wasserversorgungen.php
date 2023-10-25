<?php 

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
    protected array $wasserversorgungen = [];

    /**
     * Hinzufügen eines Uebergabesystems.
     * 
     * @var Wasserversorgung 
     */
    public function hinzufuegen( Wasserversorgung $wasserversorgung ) {
        $this->wasserversorgungen[] = $wasserversorgung;
    }

    /**
     * Hinzufügen einer Wasserversorgungen welche in einer Heizung enthalten ist.
     * 
     * @param Heizungsanlage $heizungsanlage 
     * @param bool $mit_warmwasserspeicher Hat die Wasserversorgung einen Warmwasserspeicher?
     * @param bool $mit_zirkulation Ist die Wasserversorgung mit Zirkulation?
     * @return void 
     */
    public function hinzufuegen_ueber_heizung( Heizungsanlage $heizungsanlage, bool $mit_warmwasserspeicher, bool $mit_zirkulation ) {
        $this->wasserversorgungen[] = new Wasserversorgung(
            zentral: true,
            beheizte_bereiche: $heizungsanlage->beheizung_anlage(),
            mit_warmwasserspeicher: $mit_warmwasserspeicher,
            mit_zirkulation: $mit_zirkulation,
            prozentualer_anteil: $heizungsanlage->prozentualer_anteil()
        );
    }

    /**
     * Alle Uebergabesysteme.
     * 
     * @return Uebergabesystem[]
     */
    public function alle(): array
    {
        return $this->wasserversorgungen;
    }

    /**
     * Validierung des prozentualen Anteils aller Uebergabesystemn.
     * 
     * @return bool 
     */
    protected function validiere_prozent_gesamt(): bool {
        $prozent_gesamt = 0;

        foreach ($this->wasserversorgungen as $wasserversorgung) {
            $prozent_gesamt += $wasserversorgung->prozentualer_anteil();
        }

        return $prozent_gesamt === 100;
    }
}