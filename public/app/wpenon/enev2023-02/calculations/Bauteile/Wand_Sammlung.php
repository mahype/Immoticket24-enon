<?php 

/**
 * Sammlung aller Wände.
 * 
 * @package 
 */
class Wand_Sammlung {
    /**
     * Sammlung aller Wände.
     * 
     * @var Wand[]
     */
    private array $elemente = [];

    /**
     * Konstruktor
     * 
     * @param Wand[] $elemente 
     * @return void 
     */
    public function __construct( array $elemente = [] ) {
        foreach( $elemente AS $wand ) {
            $this->hinzufuegen( $wand );
        }
    }

    /**
     * Fügt eine Wand hinzu.
     * 
     * @param Wand $wand 
     * @return void 
     */
    public function hinzufuegen( Wand $wand ) {
        $this->elemente[] = $wand;
    }

    /**
     * Gibt alle Bauteile zurück.
     * 
     * @return Wand[]
     */
    public function alle(): array
    {
        return $this->elemente;
    }

    /**
     * Gibt das erste Bauteil der Sammlung zurück.
     * 
     * @return Wand
     */
    public function erstes(): Wand
    {
        return $this->elemente[0];
    }

    /**
     * Filtert die Wände.
     * 
     * @param string $seite
     * 
     * @return Wand_Sammlung 
     */
    public function filter( string $seite = null, string $himmelsrichtung = null ): Wand_Sammlung
    {
        $elemente = array_filter( $this->elemente, function( Wand $element ) use ( $himmelsrichtung, $seite ) {
            $found = false;

            if( $himmelsrichtung !== null && $element->himmelsrichtung() !== $himmelsrichtung ) {
                $found = false;
            }

            if( $seite !== null && $element->seite() !== $seite ) {
                $found = false;
            }

            return $found;
        } );      
        
        return new Wand_Sammlung( $elemente );
    }

    /**
     * Gibt die Summe der Flächen zurück.
     * 
     * @return float 
     */
    public function flaeche(): float
    {
        $flaeche = 0.0;

        foreach( $this->elemente AS $element ) {
            $flaeche += $element->flaeche();
        }

        return $flaeche;
    }

    /**
     * Transmissionswärmeverlust aller Bauteile.
     * 
     * @return float 
     */
    public function ht(): float
    {
        $ht = 0.0;

        foreach( $this->elemente AS $element ) {
            $ht += $element->ht();
        }

        return $ht;
    }
}