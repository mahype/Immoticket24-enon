<?php

namespace Enev\Schema202402\Calculations\Gebaeude;

use Enev\Schema202402\Calculations\Gebaeude\Grundriss;

/**
 * Abstrakte Klasse für ein Dach.
 */
class Keller {
	/**
	 * Grundriss.
	 *
	 * @var Grundriss
	 */
	protected Grundriss $grundriss;

    /**
     * Anteil.
     * 
     * @var float
     */
    protected float $anteil;
    
    /**
     * Höhe des Kellers.
     * 
     * @var float
     */
    protected float $hoehe;

    /**
     * Konstruktor.
     * 
     * @param Grundriss $grundriss Grundriss.
     * @param float     $anteil    Anteil des Kellers am Gebäude in Prozent.
     * @param float     $hoehe     Höhe des Kellers.
     */
    public function __construct( Grundriss $grundriss, float $anteil, float $hoehe ) {
        $this->grundriss = $grundriss;
        $this->anteil = $anteil;
        $this->hoehe = $hoehe;
    }

    /**
     * Anteil der Unterkellerung.
     * 
     * @return float
     */
    public function anteil(): float {
        return $this->anteil;
    }

    /**
     * Gibt die Fläche des Kellerbodens zurück.
     * 
     * @return float 
     */
    public function boden_flaeche(): float {
        return $this->grundriss->flaeche() * ($this->anteil * 0.01);
    }

    /**
     * Gibt die Gesamtlänge der Kellerwand zurück.
     * 
     * @return float
     */
    public function wand_laenge(): float {
        return  sqrt( $this->boden_flaeche() ) * 4;    
    }

    /**
     * Gibt die Höhe der Kellerwand zurück.
     * 
     * @return float
     */
    public function wand_hoehe(): float {
        if ( $this->hoehe === 0.0 ) {
            return 0;
        }
        
        return $this->hoehe + 0.25;
    }

    /**
     * Gibt die Fläche der Kellerwand zurück.
     * 
     * @return float
     */
    public function wandseite_flaeche(): float {
        return $this->wand_laenge() * $this->wand_hoehe();
    }

    /**
     * Gibt das Volumen des Kellers zurück.
     * 
     * @return float
     */
    public function volumen(): float {
        return $this->boden_flaeche() * $this->wand_hoehe();
    }
}