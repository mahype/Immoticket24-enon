<?php 

namespace Enev\Schema202302\Calculations\Anlagentechnik;

require_once __DIR__ . '/Heizungsanlagen.php';
require_once __DIR__ . '/Uebergabesysteme.php';
require_once __DIR__ . '/Wasserversorgungen.php';


/**
 * Berechnung des Heizsystems.
 */
class Heizsystem
{
    /**
     * Liste der Heizungsanlagen.
     * 
     * @var Heizungsanlagen
     */
    protected Heizungsanlagen $heizungsanlagen;

    /**
     * Liste der Übergabesysteme.
     * 
     * @var Uebergabesysteme
     */
    protected Uebergabesysteme $uebergabesysteme;

    /**
     * Wasserversorgungen.
     * 
     * @var Wasserversorgungen
     */
    protected Wasserversorgungen $wasserversorgungen;

    /**
     * Konstruktor.
     */
    public function __construct()
    {
        $this->heizungsanlagen = new Heizungsanlagen();
        $this->uebergabesysteme = new Uebergabesysteme();
        $this->wasserversorgungen = new Wasserversorgungen();
    }

    /**
     * Heizungsanlagen.
     * 
     * @return Heizungsanlagen 
     */
    public function heizungsanlagen(): Heizungsanlagen
    {
        return $this->heizungsanlagen;
    }

    /**
     * Übergabesysteme.
     * 
     * @return Uebergabesysteme
     */
    public function uebergabesysteme(): Uebergabesysteme
    {
        return $this->uebergabesysteme;
    }

    /**
     * Wasserversorgungen.
     * 
     * @return Wasserversorgungen
     */
    public function wasserversorgungen(): Wasserversorgungen
    {
        return $this->wasserversorgungen;
    }

    /**
     * Berechnung von fh-a.
     *
     * @return float
     */
    public function fa_h(): float
    {
        $fa_h = 0;

        if( count( $this->heizungsanlagen()->alle() ) === 0 ) {
            throw new Exception( 'Keine Heizungsanlagen vorhanden' );
        }
        
        /**
         * Wir berecnen fh-a des Heizungsystems, indem wir alle Heizungsanlagen sowie Übergabesysteme durchlaufen  
         * und den fa-h Wert anhand der Auslegungstemperaturen des Übergabesystems ermitteln. Die Werte werden 
         * anteilig der einzelnen Heizungsanlagen und Übergabesysteme gewichtet.
         */
        foreach( $this->heizungsanlagen()->alle() as $heizungsanlage ) {
            if( count( $this->uebergabesysteme()->alle() ) === 0 ) {
                throw new Exception( 'Keine Übergabesysteme vorhanden' );
            }    

            foreach( $this->uebergabesysteme()->alle() as $uebergabesystem ) {
                $fa_h += $heizungsanlage->fa_h($uebergabesystem->auslegungstemperaturen()) * $heizungsanlage->prozentualer_faktor() * $uebergabesystem->prozentualer_faktor();
            }                        
        }

        return $fa_h;
    }
}