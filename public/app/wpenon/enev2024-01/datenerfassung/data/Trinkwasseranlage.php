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
    
    public function TrinkwarmwassererzeugerBaujahr()
    {
        return $this->data['baujahr'];
    }
    
    public function AnzahlBaugleiche()
    {
        return 1;
    }
}