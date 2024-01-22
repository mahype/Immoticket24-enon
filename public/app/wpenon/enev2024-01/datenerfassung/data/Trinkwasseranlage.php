<?php

class Trinkwasseranlage {
    protected array $data;

    public function __construct( $dataTrinkwarmwasseranlage )
    {
        $this->data = $dataTrinkwarmwasseranlage;
    }

    public function TrinkwarmwassererzeugerBauweise4701()
    {
        switch ( $this->data['slug'] )
        {
            case 'standardkessel':
                return 'über Heizungsanlage beheizter Speicher';
            case 'niedertemperaturkessel':
                return 'über Heizungsanlage beheizter Speicher';
            case 'brennwertkessel':
                return 'über Heizungsanlage beheizter Speicher';                                    
            case 'brennwertkesselverbessert':
                return 'über Heizungsanlage beheizter Speicher'; 
            case 'fernwaerme':
                return 'über Heizungsanlage beheizter Speicher';
            case 'waermepumpeluft':
                return 'über Heizungsanlage beheizter Speicher';
            case 'waermepumpewasser':
                return 'über Heizungsanlage beheizter Speicher';                                    
            case 'waermepumpeerde':
                return 'über Heizungsanlage beheizter Speicher';  
            case 'kleinthermeniedertemperatur':
                return 'Sonstiges';
            case 'kleinthermebrennwert':
                return 'Sonstiges';
            case 'dezentralkleinspeicher':
                return 'über Heizungsanlage beheizter Speicher';                                    
            case 'dezentralelektroerhitzer':
                return 'Elektro-Durchlauferhitzer'; 
            case 'dezentralgaserhitzer':
                return 'Elektro-Durchlauferhitzer';
            case 'elektrospeicher':
                return 'Elektro-Speicher';
            case 'gasspeicher':
                return 'Direkt beheizter Trinkwarmwasserspeicher (Gas)';                                    
            case 'solaranlage':
                return 'Solare Trinkwarmwasserbereitung';                           
        }
    }
    
    public function TrinkwarmwassererzeugerBauweise18599()
    {
        switch ( $this->data['slug'] )
        {
            case 'standardkessel':
                switch( $this->data['energietraeger'] ) {
                    case 'heizoel':
                    case 'stueckholz':
                        return 'Standard-Heizkessel als Umstell-/Wechselbrandkessel';
                    case 'erdgas':
                    case 'fluessiggas':
                    case 'biogas':
                        return 'Standard-Heizkessel als Gas-Spezial-Heizkessel';
                    case 'holzpellets':                      
                        return 'Standard-Heizkessel als Pelletkessel';
                    case 'holzhackschnitzel':
                        return 'Standard-Heizkessel als Hackschnitzelkessel';                    
                    case 'steinkohle':
                    case 'braunkohle':
                        return 'Standard-Heizkessel als Feststoffkessel (fossiler und biogener Brennstoff)';
                    default:
                        return;             
                }
            case 'niedertemperaturkessel':
                switch( $this->data['energietraeger'] ) {
                    case 'heizoel':                        
                    case 'erdgas':
                    case 'fluessiggas':
                    case 'biogas':
                        return 'Niedertemperatur-Heizkessel als Gebläsekessel';
                    default:
                        return ''; 
                }
            case 'brennwertkessel':
                switch( $this->data['energietraeger'] ) {
                    case 'heizoel':
                    case 'erdgas':
                    case 'fluessiggas':
                    case 'biogas':
                        return 'Brennwertkessel (Öl/Gas)';
                    case 'holzpellets':                      
                        return 'Brennwertkessel (Pellet)';
                    case 'holzhackschnitzel':                        
                    case 'stueckholz':
                        return 'Heizungsanlage zur Nutzung von Biomasse';  // Gibt es nicht für Trinkwarmwasser          
                    default:
                        return '';
                }
            case 'fernwaerme':
                return 'Fern-/Nahwärme';
            case 'waermepumpeluft':
                return 'Elektrisch angetriebene Luft/Wasser-Wärmepumpe';
            case 'waermepumpewasser':
                return 'Elektrisch angetriebene Wasser/Wasser-Wärmepumpe';
            case 'waermepumpeerde':
                return 'Elektrisch angetriebene Sole/Wasser-Wärmepumpe';
            case 'etagenheizung':
                return 'Niedertemperatur-Heizkessel als Umlaufwasserheizer';
            case 'dezentralelektroerhitzer':
                return 'Elektro-Durchlauferhitzer';
            case 'dezentralgaserhitzer':
                return 'Gas-Durchlauferhitzer';
            default:
                return 'Sonstiges';
        }
    }
    
    public function TrinkwarmwassererzeugerBaujahr()
    {
        return $this->data['baujahr'];
    }
    
    public function AnzahlBaugleiche()
    {
        return 1;
    }
}