<?php

require dirname( __FILE__ ) . '/DataEnev.php';
require dirname( __FILE__ ) . '/Bauteil.php';
require dirname( __FILE__ ) . '/BauteilTransparent.php';
require dirname( __FILE__ ) . '/Heizungsanlage.php';
require dirname( __FILE__ ) . '/Trinkwasseranlage.php';
require dirname( __FILE__ ) . '/Moderniserungsempfehlung.php';
require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/modernizations/BW_Modernizations.php';

use Enev\Schema202103\Modernizations\BW_Modernizations;

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

        return $this->calculations[ $slug ];
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
            if( $bauteil['modus'] === 'dach' )
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
        foreach( $this->calculations( 'anlagendaten' ) AS $anlage )
        {
            if( $anlagen['art'] === 'heizung' )
            {
                $anlagen[] = new Heizungsanlage( $anlage );
            }
        }

        return $anlagen;
    }

    /**
     * Pufferspeicher-Nenninhalt
     * 
     * @return int
     * 
     * @since 1.0.0
     */
    public function PufferspeicherNenninhalt() : int
    {
        return 0;
    }

    /**
     * Heizkreisauslegungstemperatur
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Heizkreisauslegungstemperatur() : string
    {
        $anteil_max   = 0;
        $hktemp       = '';
        foreach( $this->calculations( 'anlagendaten' ) AS $key => $anlage )
        {
            if ( $anlage['art'] == 'heizung' ) {
                if ( $anlage['deckungsanteil'] > $anteil_max ) {
                    $anteil_max = $anlage['deckungsanteil'];
                    $hktemp     = $anlage['heizkreistemperatur'];
                }
            }
        }

        if ( $hktemp == '70/55°' ) {
            return $hktemp;
        }

		return '55/45';
    }

    /**
     * Heizungsanlage-innerhalb-Huelle
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function HeizungsanlageInnerhalbHuelle() : string
    {
        if ( $this->energieausweis->speicherung_standort == 'innerhalb' ) {
            return 'true';
        }

        return 'false';
    }

    /**
     * Trinkwasseranlagen
     * 
     * @return Trinkwasseranlage[]
     * 
     * @since 1.0.0
     */
    public function Trinkwasseranlagen() : array
    {
        $anlagen = [];
        foreach( $this->calculations( 'anlagendaten' ) AS $anlage )
        {
            if( $anlagen['art'] === 'warmwasser' )
            {
                $anlagen[] = new Trinkwasseranlage( $anlage );
            }
        }

        return $anlagen;
    }

    /**
     * Trinkwarmwasserspeicher-Nenninhalt
     * 
     * @return int
     * 
     * @since 1.0.0
     */
    public function TrinkwarmwasserspeicherNenninhalt() : int
    {
        return 0;
    }

    /**
     * Trinkwarmwasserverteilung-Zirkulation
     * 
     * @return int
     * 
     * @since 1.0.0
     */
    public function TrinkwarmwasserverteilungZirkulation()
    {
        if ( $this->energieausweis->verteilung_versorgung == 'mit' ) {
            return 'true';
        }

        return 'false';
    }

    /**
     * Vereinfachte-Datenaufnahme
     * 
     * @return int
     * 
     * @since 1.0.0
     */
    public function VereinfachteDatenaufnahme()
    {
        return 'true';
    }

    /**
     * spezifischer-Transmissionswaermeverlust-Ist
     * 
     * @return float
     * 
     * @since 1.0.0
     */
    public function SpezifischerTransmissionswaermeverlustIst()
    {
        return round( (float) $this->calculations('ht_b'), 2 );
    }

    /**
     * Innovationsklausel
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Innovationsklausel()
    {
        return $this->MISSING; // Neu - Bool
    }

    /**
     * Quartiersregelung
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Quartiersregelung()
    {
        return $this->MISSING; // Neu - Bool
    }
    

    /**
     * Primaerenergiebedarf-Hoechstwert-Bestand
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function PrimaerenergiebedarfHoechstwertBestand()
    {
        return 0; // Neu - Float
    }
    

    /**
     * Endenergiebedarf-Hoechstwert-Bestand
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function EndenergiebedarfHoechstwertBestand()
    {
        return 0; // Neu - Float
    }
    

    /**
     * Treibhausgasemissionen-Hoechstwert-Bestand
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function TreibhausgasemissionenHoechstwertBestand()
    {
        return 0; // Neu - Float
    }

    /**
     * Endenergiebedarf-Waerme-AN
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function EndenergiebedarfWaermeAN()
    {
        return round( (float) $this->calculations( 'qh_e_b' ) + $this->calculations( 'qw_e_b' ), 1 );
    }

    /**
     * Endenergiebedarf-Hilfsenergie-AN
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function EndenergiebedarfHilfsenergieAN()
    {
        return round( (float) $this->calculations( 'qh_e_b' ), 1 );
    }

    /**
     * Endenergiebedarf-Gesamt
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function EndenergiebedarfGesamt()
    {
        return $this->MISSING; // NEU
    }

    /**
     * Primaerenergiebedarf
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Primaerenergiebedarf()
    {
        return round( (float) $this->calculations( 'primaerenergie' ), 1 );
    }

    /**
     * Energieeffizienzklasse
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Energieeffizienzklasse()
    {
        return wpenon_get_class( $this->calculations( 'primaerenergie' ), 'bw' );
    }
    
    /**
     * Empfehlungen-moeglich
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function EmpfehlungenMoeglich()
    {
        $empfehlungen = new BW_Modernizations();
        return $empfehlungen->get_modernizations( array(), $this->energieausweis ) > 0 ? true : false;
    }

    /**
     * Modernisierungsempfehlungen
     * 
     * @return Moderniserungsempfehlung[]
     * 
     * @since 1.0.0
     */
    public function Modernisierungsempfehlungen()
    {
        $modernisierungen = new BW_Modernizations();

        $empfehlungen = [];
        foreach( $modernisierungen->get_modernizations( array(), $this->energieausweis ) AS $empfehlung )
        {
            $empfehlungen[] = new Moderniserungsempfehlung( $empfehlung );
        }

        return $empfehlungen;
    }
}