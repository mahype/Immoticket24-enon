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
            case 'standardkessel': // Mit Michael klären!
                switch( $this->data['energietraeger'] ) 
                {
                    case 'heizoel':  
                        return 'Standard-Heizkessel als Gebläsekessel mit Brennertausch';                         
                    case 'erdgas':                        
                    case 'fluessiggas':                        
                    case 'biogas':
                        return 'Standard-Heizkessel als Gas-Spezial-Heizkessel';                    
                    case 'holzhackschnitzel':
                        return 'Standard-Heizkessel als Hackschnitzelkessel';
                    case 'holzpellets':                        
                    case 'stueckholz':
                        return 'Standard-Heizkessel als Pelletkessel';                      
                    case 'steinkohle':                        
                    case 'braunkohle':                        
                        return 'Standard-Heizkessel als Feststoffkessel (fossiler und biogener Brennstoff)';
                }
            case 'niedertemperaturkessel': // Mit Michael klären!
                switch( $this->data['energietraeger'] )
                {                                                             
                    case 'erdgas':                        
                    case 'fluessiggas':                        
                    case 'biogas':
                    case 'heizoel':
                        return 'Niedertemperatur-Heizkessel als Umlaufwasserheizer';
                }

            case 'brennwertkessel':
                switch( $this->data['energietraeger'] )
                {
                    case 'heizoel':                        
                    case 'erdgas':                        
                    case 'fluessiggas':                        
                    case 'biogas':
                        return 'Brennwertkessel (Öl/Gas)';
                    case 'holzpellets':                        
                    case 'holzhackschnitzel':
                    case 'stueckholz':
                        return 'Brennwertkessel (Pellet)';
                }
            case 'fernwaerme':
                return 'Fern-/Nahwärme';
            case 'etagenheizung':
                return 'Brennwertkessel (Öl/Gas)';
            case 'waermepumpeluft':
                return 'Elektrisch angetriebene Luft/Wasser-Wärmepumpe';
            case 'waermepumpewasser':
                return 'Elektrisch angetriebene Wasser/Wasser-Wärmepumpe';                                    
            case 'waermepumpeerde':
                return 'Elektrisch angetriebene Sole/Wasser-Wärmepumpe';                    
            case 'dezentralelektroerhitzer':
                return 'Elektro-Durchlauferhitzer'; 
            case 'dezentralgaserhitzer':
                return 'Gas-Durchlauferhitzer';            
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