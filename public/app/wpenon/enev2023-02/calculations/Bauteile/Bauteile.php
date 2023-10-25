<?php
/**
 * Temporäre Klasse zur Aufnahme der Daten. Später sollen die Bauteile, Transmissions usw. hier berechnet werden.
 */
class Bauteile {
    /**
     * Sammlung aller Bauteile.
     * 
     * @var array
     */
    protected array $elemente = [];

    /**
     * Konstruktor.
     * 
     * @param array $elemente Bauteile welche hinzugefügt werden sollen.
     */
    public function __construct( array $elemente = [] )
    {
        foreach( $elemente AS $element ) {
            $this->hinzufuegen( $element );
        }        
    }

    /**
     * Fügt ein Bauteil hinzu.
     * 
     * @param Bauteil $bauteil
     */
    public function hinzufuegen( Bauteil $bauteil ) {
        $this->elemente[] = $bauteil;
    }

    /**
     * Gibt alle Bauteile zurück.
     * 
     * @return Bauteile[]
     */
    public function alle(): array
    {
        return $this->elemente;
    }

    /**
     * Gibt das erste Bauteil der Sammlung zurück.
     * 
     * @return Bauteil
     */
    public function erstes(): Bauteil
    {
        return $this->elemente[0];
    }

    /**
     * Filtern der Bauteile.
     * 
     * @param string $typ 
     * 
     * @return Bauteile 
     */
    public function filter( string $typ = null, string $himmelsrichtung = null ): Bauteile
    {
        $elemente = array_filter( $this->elemente, function( $bauteil ) use ( $typ ) {
            $reflect = new ReflectionClass($bauteil );
            $bauteil_typ = $reflect->getShortName();

            return $bauteil_typ === $typ;
        } );

        return new Bauteile( $elemente );
    }

    /**
     * Gibt die Flaeche aller Bauteile zurück.
     * 
     * @return float
     */
    public function flaeche(): float
    {
        $flaeche = 0.0;

        foreach( $this->elemente AS $bauteil ) {
            $flaeche += $bauteil->flaeche();
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
        $ht = 0;

        foreach( $this->elemente AS $bauteil ) {
            $ht += $bauteil->ht();
        }

        return $ht;
    }

    public function hw(): float
    {
        return $this->hw;
    }
}