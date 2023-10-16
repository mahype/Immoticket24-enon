<?php 

/**
 * Berechnung eines Übergabeystems (Heizkörper).
 */
class Uebergabesystem {
    /**
     * Auslegungstemperaturen.
     * 
     * @var string
     */
    protected string $auslegungstemperaturen;

    /**
     * Prozentualer Anteil der Heizungsanlage im Heizsystem
     * 
     * @var int
     */
    protected int $prozentualer_anteil;

    /**
     * Konstruktor.
     * 
     * @param string $auslegungstemperaturen Auslegungstemperaturen der Heizungsanlage. Mögliche Werte: ' 90/70', '70/55', '55/45' oder '35/28'.
     * @param string $prozentualer_anteil    Prozentualer Anteil des Übergabesystems im Heizsystem
     */
    public function __construct( string $auslegungstemperaturen, int $prozentualer_anteil = 100 )
    {
        // Check der Auslegungstemperaturen.
        if ($auslegungstemperaturen !== '90/70' && $auslegungstemperaturen !== '70/55' && $auslegungstemperaturen !== '55/45' && $auslegungstemperaturen !== '35/28' ) {
            throw new Exception('Auslegungstemperaturen müssen entweder "90/70", "70/55", "55/45" oder "35/28" sein.');
        }

        $this->auslegungstemperaturen = $auslegungstemperaturen;
        $this->prozentualer_anteil = $prozentualer_anteil;
    }

    /**
     * Auslegungstemperaturen.
     * 
     * @return string
     */
    public function auslegungstemperaturen(): string
    {
        return $this->auslegungstemperaturen;
    }

    /**
     * Prozentualer Anteil.
     * 
     * @return int
     */
    public function prozentualer_anteil(): int
    {
        return $this->prozentualer_anteil;
    }

    /**
     * Prozentualer Faktor.
     * 
     * @return float
     */
    public function prozentualer_faktor(): float
    {
        return $this->prozentualer_anteil() / 100;
    }
}