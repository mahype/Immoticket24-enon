<?php

use WPENON\Model\Energieausweis;

/**
 * Base data for Enev XML
 * 
 * @since 1.0.0
 */
abstract class DataEnev {
    /**
     * Energieausweis object
     * 
     * @var Energieausweis
     * 
     * @since 1.0.0
     */
    protected Energieausweis $energieausweis;

    protected string $MISSING = 'DATEN NEU!'; // @todo Remove later
    
    /**
     * Registriernummer
     * 
     * @var string
     * 
     * @since 1.0.0
     */
    private string $registriernummer;

    /**
     * Energieausweis constructor
     * 
     * @param Energieausweis
     * 
     * @since 1.0.0
     */
    public function __construct( Energieausweis $energieausweis )
    {
        $this->energieausweis = $energieausweis;
    }

    /**
     * Rechtsstand Grund
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function RechtsstandGrund() : string
    {
    }

    /**
     * Registriernummer
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Registriernummer() : string
    {
        if( empty( $this->registriernummer ) )
        {

        }

        return $this->registriernummer;
    }

    /**
     * Ausstellungsdatum
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Ausstellungsdatum() : string
    {
        if( empty( $this->registriernummer ) )
        {

        }

        return $this->registriernummer;
    }

    /**
     * PLZ
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function PLZ() : string
    {
        return $this->energieausweis->adresse_plz;
    }

    /**
     * Ort
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Ort() : string
    {
        return $this->energieausweis->adresse_ort;
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
        return $$this->energieausweis->baujahr;
    }

    /**
     * Baujahr Waermeerzeuger
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function BaujahrWaermeerzeuger() : string
    {
        $baujahre = array( $this->energieausweis->h_baujahr );
        if ( $this->energieausweis->h2_info ) {
            $baujahre[] = $this->energieausweis->h2_baujahr;
            if ( $this->energieausweis->h3_info ) {
                $baujahre[] = $this->energieausweis->h3_baujahr;
            }
        }
        if ( $this->energieausweis->ww_info == 'ww' ) {
            $baujahre[] = $this->energieausweis->ww_baujahr;
        }

        return implode( ', ', array_unique( $baujahre ) );
    }

    /**
     * Ort
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Bundesland() : string
    {
        return $this->energieausweis->adresse_bundesland;
    }

    /**
     * Gebäudeteil
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Gebauedeteil() : string
    {
        $gemischt = $this->energieausweis->gebaeudeteil === 'gemischt';
        if ( $gemischt ) {
            if (  $this->energieausweis->building == 'n' ) {
                return __( 'Gewerbeteil gemischt genutztes Gebäude', 'wpenon' );
            }

            return __( 'Wohnteil gemischt genutztes Gebäude', 'wpenon' );
        }

        return __( 'Gesamt', 'wpenon' );
    }

    /**
     * Altersklasse Gebäude
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function AltersklasseGebaeude() : string
    {        
        return $this->Altersklasse( $this->energieausweis->baujahr );
    }

    /**
     * Altersklasse Wärmeerzeiger
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function AltersklasseWaermeerzeuger() : string
    {        
        return $this->Altersklasse( $this->energieausweis->h_baujahr );
    }

    /**
     * Altersklasse
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    protected function Altersklasse( int $jahr ) : string
    {
        $altersklassen = [
            0 => [
                'start' => 0,
                'end'   => 1918,
                'value' => 'bis 1918'
            ],
            1 => [
                'start' => 1919,
                'end'   => 1948,
                'value' => '1919...1948'
            ],
            2 => [
                'start' => 1949,
                'end'   => 1957,
                'value' => '1949...1957'
            ],
            3 => [
                'start' => 1958,
                'end'   => 1968,
                'value' => '1958...1968'
            ],
            4 => [
                'start' => 1969,
                'end'   => 1978,
                'value' => '1969...1978'
            ],
            5 => [
                'start' => 1979,
                'end'   => 1983,
                'value' => '1979...1983'
            ],
            6 => [
                'start' => 1984,
                'end'   => 1994,
                'value' => '1984...1994'
            ],
            7 => [
                'start' => 1995,
                'end'   => 2002,
                'value' => '1995...2002'
            ],
            8 => [
                'start' => 2003,
                'end'   => 2009,
                'value' => '2003...2009'
            ],
            9 => [
                'start' => 2010,
                'end'   => 2016,
                'value' => '2010...2016'
            ],
            9 => [
                'start' => 2017,
                'end'   => 0,
                'value' => 'ab 2017'
            ],
        ];

        foreach( $altersklassen AS $altersklasse )
        {
            // First
            if( $jahr <= $altersklasse['start'] )
            {
                return $altersklasse['value'];
            }            

            // Any value between
            if( $jahr >= $altersklasse['start'] && $jahr <= $altersklasse['end'] )
            {
                return $altersklasse['value'];
            }

            // Last
            if( $jahr >= $altersklasse['start'] && $altersklasse['end'] === 0 )
            {
                return $altersklasse['value'];
            }
        }
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
        return $this->MISSING;
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
        return $this->MISSING;
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
        $arten = array(
            'keine' => 'keine',
            'solar' => 'Solaranlage',
        );
       
        return $arten[ $this->energieausweis->regenerativ_art ];
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
        $regenerativArten = array(
            'keine' => 'keine',
            'solar' => 'Solaranlage',
        );

        if( isset( $regenerativArten[ $this->energieausweis->regenerativ_nutzung ] ) )
        {
            return $regenerativArten[ $this->energieausweis->regenerativ_nutzung ];
        }
       
        return 'keine';
    }

    /**
     * Lüftungsart Fensterlüftung
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function LueftungsartFensterlueftung() : string
    {        
        if ( $this->energieausweis->l_info == 'fenster' ) {
            return 'true';
        }

        return 'false';
    }

    /**
     * Lüftungsart Schachtlüftung
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function LueftungsartSchachtlueftung() : string
    {        
        if ( $this->energieausweis->l_info == 'schacht' ) {
            return 'true';
        }

        return 'false';
    }

    /**
     * Lueftungsart-Anlage-o-WRG
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function LueftungsartAnlageOWRG() : string
    {        
        if ( $this->energieausweis->l_info == 'anlage' ) {
            if ( substr( $this->energieausweis->l_erzeugung, 0, 4 ) == 'ohne' ) {
                return 'true';
            }
        }

        return 'false';
    }


    /**
     * Lueftungsart-Anlage-m-WRG
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function LueftungsartAnlageMWRG() : string
    {        
        if ( $this->energieausweis->l_info == 'anlage' ) {
            if ( substr( $this->energieausweis->l_erzeugung, 0, 3 ) == 'mit' ) {
                return 'true';
            }
        }

        return 'false';
    }

    /**
     * Kuehlungsart-passive-Kuehlung
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function KuehlungsartPassiveKuehlung() : string
    {        
        return $this->MISSING; // BOOL
    }

    /**
     * Kuehlungsart-Strom
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function KuehlungsartStrom() : string
    {        
        return $this->MISSING; // BOOL
    }

    /**
     * Kuehlungsart-Waerme
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function KuehlungsartWaerme() : string
    {        
        return $this->MISSING; // BOOL
    }

    /**
     * Kuehlungsart-gelieferte-Kaelte
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function KuehlungsartGelieferteKaelte() : string
    {        
        return $this->MISSING; // BOOL
    }

    /**
     * Klimaanlage vorhanden?
     * 
     * @return bool
     * 
     * 
     * @since 1.0.0
     */
    public function KlimaanlageVorhanden() : bool
    {
        return $this->MISSING; // BOOL
    }

    /**
     * Anzahl-Klimanlagen
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function AnzahlKlimaanlagen() : string
    {        
        return $this->MISSING; // INT
    }

    /**
     * Anlage-groesser-12kW-ohneGA
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function AnlageGroesser12kWohneGA() : string
    {        
        return $this->MISSING; // BOOL
    }

    /**
     * Anlage-groesser-12kW-mitGA
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function AnlageGroesser12kWmitGA() : string
    {        
        return $this->MISSING; // BOOL
    }

    /**
     * Anlage-groesser-70kW
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function AnlageGroesser70kW() : string
    {        
        return $this->MISSING; // BOOL
    }

    /**
     * Faelligkeitsdatum-Inspektion
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function FaelligkeitsdatumInspektion() : string
    {        
        return $this->MISSING; // DATE
    }

    /**
     * Treibhausgasemissionen
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Treibhausgasemissionen() : string
    {        
        return $this->MISSING; // DATE
    }

    /**
     * Ausstellungsanlass
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Ausstellungsanlass() : string
    {
        switch( $this->energieausweis->anlass )
        {
            case 'vermietung':
            case 'verkauf':
                return 'Vermietung-Verkauf';
            default:
                return 'Sonstiges';
        }
    }

    /**
     * Datenerhebung-Aussteller
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function DatenerhebungAussteller() : string
    {        
        return 'false';
    }

    /**
     * Datenerhebung-Eigentuemer
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function DatenerhebungEigentuemer() : string
    {        
        return 'true';
    }

    /**
     * Gebaeudetyp
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Gebaeudetyp() : string
    { 
        if ( 'gemischt' === $this->energieausweis->gebaeudeteil ) 
        {
            return 'Wohnteil gemischt genutztes Gebäude';
        }

        $wohnungen = (int) $this->energieausweis->wohnungen;

        if ( $wohnungen > 2 ) {
            return 'Mehrfamilienhaus';
        }
        if ( $wohnungen == $wohnungen ) {
            return 'Zweifamilienhaus';
        }

        return 'Einfamilienhaus';
    }

    /**
     * Anzahl-Wohneinheiten
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function AnzahlWohneinheiten() : int
    {        
        return (int) $this->energieausweis->wohnungen;
    }

    /**
     * Gebaeudenutzflaeche
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public abstract function Gebaeudenutzflaeche();

    /**
     * Wohnflaeche
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function Wohnflaeche() : int
    {        
        return (int) $this->energieausweis->flaeche;;
    }

    /**
     * Keller-beheizt
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function KellerBeheizt() : int
    {        
        if ( $this->energieausweis->keller == 'beheizt' ) {
            return 'true';
        }

        return 'false';
    }
}