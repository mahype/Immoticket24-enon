<?php

use WPENON\Model\Energieausweis;

require dirname( __FILE__ ) . '/DataEnev.php';

/**
 * Bedarfsausweis-Spezifische Daten für Enev
 * 
 * @since 1.0.0
 */
class DataEnevBW extends DataEnev {
    /**
     * Daten der Berechnungen
     * 
     * @var array
     * 
     * @since 1.0.0
     */
    private $calculations;

    /**
     * Berechnungen Bedarfsausweis
     * 
     * @return mixed
     * 
     * @since 1.0.0
     */
    public function calculations( string $slug )
    {
        if ( empty( $this->calculations ) )
        {
            $this->calculations = $this->energieausweis->calculate();
        }

        $this->calculations( $slug );
    }

    /**
     * Gebaeudenutzflaeche
     * 
     * @return float
     * 
     * @since 1.0.0
     */
    public function Gebaeudenutzflaeche()
    {
        return $this->calculations( 'nutzflaeche' );
    }

    /**
     * Baujahr Gebäude
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function BaujahrGebaeude() : string
    {
        $baujahre = parent::BaujahrGebaeude();
        
        if ( $this->energieausweis->anbau && ! empty( $this->energieausweis->anbau_baujahr ) ) {
            $baujahre .= ', ' . $this->energieausweis->anbau_baujahr . ' (Anbau)';
        }

        return $baujahre;
    }

    /**
     * Wohngebaeude-Anbaugrad
     * 
     * @return float
     * 
     * @since 1.0.0
     */
    public function WohngebaeudeAnbaugrad() : string
    {
        switch ( $this->energieausweis->gebaeudetyp )
        {
            case 'reihenhaus':
                return 'zweiseitig angebaut';
            case 'reiheneckhaus':
            case 'doppelhaushaelfte':
                return 'einseitig angebaut';
            default:
                return 'freistehend';
        }
    }

    /**
     * Bruttovolumen
     * 
     * @return float
     * 
     * @since 1.0.0
     */
    public function Bruttovolumen()
    {
        return (int) $this->calculations( 'huellvolumen' );
    }

    /**
     * durchschnittliche-Geschosshoehe
     * 
     * @return float
     * 
     * @since 1.0.0
     */
    public function DurchschnittlicheGeschosshoehe()
    {
        return round( (float) $this->energieausweis->geschoss_hoehe, 2 );
    }
}