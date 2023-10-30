<?php

namespace Enev\Schema202302\Calculations\Bauteile;

use Enev\Schema202302\Calculations\Gebaeude\Grundriss;
use Enev\Schema202302\Calculations\Schnittstellen\Transmissionswaerme;

/**
 * Abstrakte Klasse für ein Dach.
 */
class Keller implements Transmissionswaerme {
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
        return $this->hoehe + 0.25;
    }

    /**
     * Gibt die Fläche der Kellerwand zurück.
     * 
     * @return float
     */
    public function wand_flaeche(): float {
        return $this->wand_laenge() * $this->wand_hoehe();
    }
}