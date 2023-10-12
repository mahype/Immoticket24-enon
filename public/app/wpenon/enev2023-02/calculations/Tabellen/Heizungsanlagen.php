<?php 

/**
 * Berechnen mehrerer Heizungsanlagen in einrm Gebäude.
 */
class Heizungsanlagen {
    /**
     * Liste der Heizungsanlagen.
     * 
     * @var array
     */
    protected array $heizungsanlagen = [];

    /**
     * Hinzufügen einer Heizungsanlage.
     * 
     * @var array
     */
    public function add( Heizungsanlage $heizungsanlage, int $prozentualer_anteil ) {
        $this->heizungsanlagen[] = [
            'heizungsanlage' => $heizungsanlage,
            'prozentualer_anteil' => $prozentualer_anteil
        ];
    }

    /**
     * Validierung des prozentualen Anteils aller Heizungsanlagen.
     * 
     * @return bool 
     */
    protected function validiere_prozent_gesamt(): bool {
        $prozent_gesamt = 0;

        foreach ($this->heizungsanlagen as $heizungsanlage) {
            $prozent_gesamt += $heizungsanlage['prozentualer_anteil'];
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
        if (!$this->validiere_prozent_gesamt()) {
            throw new Exception('Die prozentualen Anteile aller Heizungsanlagen müssen zusammen 100% ergeben.');
        }

        foreach ($this->heizungsanlagen as $heizungsanlage) {
            $fa_h += $heizungsanlage['heizungsanlage']->fa_h() * $heizungsanlage['prozentualer_anteil'] / 100;
        }

        return $fa_h;
    }

    /**
     * Berechnung der innernen Wärmegewinne.
     * 
     * @return float
     */
    public function qi(): float {

    }
}