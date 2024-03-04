<?php

require_once dirname( __FILE__ ) . '/DataEnev.php';
require_once dirname( __FILE__ ) . '/Bauteil.php';
require_once dirname( __FILE__ ) . '/BauteilTransparent.php';
require_once dirname( __FILE__ ) . '/EndenergieEnergietraeger.php';
require_once dirname( __FILE__ ) . '/Heizungsanlage.php';
require_once dirname( __FILE__ ) . '/Trinkwasseranlage.php';
require_once dirname( __FILE__ ) . '/Moderniserungsempfehlung.php';
require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/modernizations/BW_Modernizations.php';

use Enev\Schema202401\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202401\Modernizations\BW_Modernizations;

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
     * Gebäude Objekt
     * 
     * @return Gebaeude
     * 
     * @since 1.0.0
     */
    public function gebaeude() : Gebaeude
    {
        return $this->calculations( 'gebaeude' );
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
        return round( $this->calculations( 'nutzflaeche' ), 0 );
    }

    /**
     * Wesentliche Energieträger Heizunbg
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function WesentlicheEnergietraegerHeizung() : string
    {        
        $energietraeger = array();

        $energietraeger[] = wpenon_immoticket24_get_energietraeger_name_2021( $this->energieausweis->h_energietraeger );
        if ( $this->energieausweis->h2_info ) {
            $energietraeger[] = wpenon_immoticket24_get_energietraeger_name_2021( $this->energieausweis->h2_energietraeger );
            if ( $this->energieausweis->h3_info ) {
                $energietraeger[] = wpenon_immoticket24_get_energietraeger_name_2021( $this->energieausweis->h3_energietraeger );
            }
        }

        return implode( ', ', array_unique( $energietraeger ) );
    }

    /**
     * Wesentliche Energieträger Heizunbg
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function WesentlicheEnergietraegerWarmWasser() : string
    {
        if ( $this->energieausweis->ww_info == 'ww' ) {
            $energietraeger_feld_name = 'ww_energietraeger_' . $this->energieausweis->ww_erzeugung;
		    $ww_energietraeger = $this->energieausweis->$energietraeger_feld_name;
            return wpenon_immoticket24_get_energietraeger_name_2021( $ww_energietraeger );                        
        } else if ( $this->energieausweis->ww_info == 'h'  ) {
            $energietraeger = wpenon_immoticket24_get_energietraeger_name_2021( $this->energieausweis->h_energietraeger );
            return $energietraeger;
        } else if ( $this->energieausweis->ww_info == 'h2'  ) {
            $energietraeger = wpenon_immoticket24_get_energietraeger_name_2021( $this->energieausweis->h2_energietraeger );
            return $energietraeger;
        } else if ( $this->energieausweis->ww_info == 'h3'  ) {
            $energietraeger = wpenon_immoticket24_get_energietraeger_name_2021( $this->energieausweis->h3_energietraeger );
            return $energietraeger;
        } else {
            return '';
        }
    }

    /**
     * Erneuerbare Art
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function ErneuerbareArt() : string
    {
        $erneuerbare_energien = array();

        if( $this->energieausweis->solarthermie_info == 'vorhanden' ) {
            $erneuerbare_energien[] = 'Solarthermie';
        }

        if( $this->energieausweis->pv_info == 'vorhanden' ) {
            $erneuerbare_energien[] = 'Photovoltaik';
        }

        return count( $erneuerbare_energien ) > 0 ? implode( ', ', $erneuerbare_energien ) : 'Keine';		
    }

    /**
     * Erneuerbare Verwendung
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function ErneuerbareVerwendung() : string
    {
        $erneuerbare_energien = [];
        
        if( $this->energieausweis->solarthermie_info == 'vorhanden' ) {
            $erneuerbare_energien[] = 'Warmwasser';
        }

        if( $this->energieausweis->pv_info == 'vorhanden' ) {
            $erneuerbare_energien[] = 'Strom';
        }

        return count( $erneuerbare_energien ) > 0 ? implode( ', ', $erneuerbare_energien ) : 'Keine';
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
            return 'Gebäudekategorie I';
        }

        return 'Gebäudekategorie III';
    }

    /**
     * Nutzung.
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Nutzung()
    {
        if( $this->energieausweis->wohnungen  <= 2 ) {
            return '42:Wohnen (EFH)';
        } else {
            return '43:Wohnen (MFH)';
        }
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
        return (int) $this->calculations( 'hv' );
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
            if( $anlage['art'] === 'heizung' )
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
    public function Auslegungstemperatur() : string
    {
        if( ! empty( $this->calculations( 'auslegungstemperatur' ) ) ) {
            return $this->calculations( 'auslegungstemperatur' ); 
        }

		return 'nur Einzelraum-Heizgeräte';
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
        if ( $this->energieausweis->h_standort == 'innerhalb' ) {
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
            if( $anlage['art'] === 'warmwasser' )
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

    public function Transmissionswaermetransferkoeffizient()
    {
        $gebaeude = $this->calculations( 'gebaeude' );
        return round( $gebaeude->ht_strich(), 2 );
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
     * Endenergie-Energietraeger
     * 
     * @return EndenergieEnergietraeger[]
     * 
     * @since 1.0.0
     */
    public function EndenergieEnergietraeger()
    {
        $daten = [];
        foreach( $this->calculations('energietraeger') AS $energietraeger )
        {
            $daten[] = new EndenergieEnergietraeger( $energietraeger, $this->gebaeude()->nutzflaeche() );
        }

        return $daten;
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
        return round( (float) ( $this->calculations( 'qfh_ges' ) + $this->calculations( 'qfw_ges' ) ) / $this->gebaeude()->nutzflaeche()  , 1 );
    }

    /**
     * Endenergiebedarf Heizung.
     * 
     * @return string
     */
    public function EndenergiebedarfHeizung()
    {
        return round( (float) $this->calculations( 'qh' ), 1 );
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
        return round( (float) $this->calculations( 'w_ges' ) /  $this->gebaeude()->nutzflaeche(), 1 );
    }

    /**
     * Endenergiebedarf-Gesamt
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function EndenergiebedarfGesamt() : float
    {
        return round( $this->calculations( 'endenergie' ), 2 );
    }
    
    /**
     * Primaerenergiebedarf
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Primaerenergiebedarf() : float
    {
        return round( (float) $this->calculations( 'primaerenergie' ), 1 );
    }

    /**
     * Treibhausgasemissionen
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Treibhausgasemissionen() : float
    {        
        return round( $this->calculations( 'co2_emissionen' ), 2 );
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