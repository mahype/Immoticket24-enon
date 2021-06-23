<?php

use WPENON\Model\Energieausweis;

require dirname( __FILE__ ) . '/DataEnev.php';
require dirname( __FILE__ ) . '/Bauteil.php';
require dirname( __FILE__ ) . '/Heizungsanlage.php';

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

    /**
     * Opak Bauteile
     * 
     * @return Bauteil[]
     * 
     * @since 1.0.0
     */
    public function BauteileOpak() : array
    {
        $bauteile = [];
        foreach( $this->calculations( 'bauteile' ) AS $key => $bauteil )
        {
            if( $bauteil['modus'] === 'opak' )
            {
                $bauteile[] = new Bauteil( $key, $bauteil );
            }
        }

        return $bauteile;
    }

    /**
     * Transparente Bauteile
     * 
     * @return BauteilTransparent[]
     * 
     * @since 1.0.0
     */
    public function BauteileTransparent() : array
    {
        $bauteile = [];
        foreach( $this->calculations( 'bauteile' ) AS $key => $bauteil )
        {
            if( $bauteil['modus'] === 'transparent' )
            {
                $bauteile[] = new BauteilTransparent( $key, $bauteil );
            }
        }

        return $bauteile;
    }

    /**
     * Dach Bauteile
     * 
     * @return Bauteil[]
     * 
     * @since 1.0.0
     */
    public function BauteileDach() : array
    {
        $bauteile = [];
        foreach( $this->calculations( 'bauteile' ) AS $key => $bauteil )
        {
            if( $bauteil['modus'] === 'transparent' )
            {
                $bauteile[] = new Bauteil( $key, $bauteil );
            }
        }

        return $bauteile;
    }

    /**
     * Waermebrueckenzuschlag
     * 
     * @return float
     * 
     * @since 1.0.0
     */
    public function Waermebrueckenzuschlag()
    {
        return (float) 0.1;
    }

    /**
     * Transmissionswaermeverlust
     * 
     * @return float
     * 
     * @since 1.0.0
     */
    public function Transmissionswaermeverlust()
    {
        return (int) $this->calculations( 'qt' );
    }

    /**
     * Luftdichtheit
     * 
     * @return float
     * 
     * @since 1.0.0
     */
    public function Luftdichtheit()
    {
        if ( $this->energieausweis->dichtheit ) {
            return 'geprüft';
        }

        return 'normal';
    }

    /**
     * Lueftungswaermeverlust
     * 
     * @return float
     * 
     * @since 1.0.0
     */
    public function Lueftungswaermeverlust()
    {
        return (int) $this->calculations( 'qv' );
    }

    /**
     * Solare-Waermegewinne
     * 
     * @return float
     * 
     * @since 1.0.0
     */
    public function SolareWaermegewinne()
    {
        return (int) $this->calculations( 'qs' );
    }

    /**
     * Interne-Waermegewinne
     * 
     * @return float
     * 
     * @since 1.0.0
     */
    public function InterneWaermegewinne()
    {
        return (int) $this->calculations( 'qi' );
    }

    /**
     * Heizungsanlagem
     * 
     * @return Heizungsanlage[]
     * 
     * @since 1.0.0
     */
    public function Heizungsanlagen() : array
    {
        $anlagen = [];
        foreach( $this->calculations( 'anlagendaten' ) AS $key => $anlage )
        {
            if( $anlagen['modus'] === 'opak' )
            {
                $anlagen[] = new Heizung( $key, $anlagen );
            }
        }

        return $anlagen;
    }
}