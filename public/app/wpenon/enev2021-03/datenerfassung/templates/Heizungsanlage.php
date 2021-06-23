<?php

class Heizung {
    protected array $data;

    public function __construct( $dataHeizungsanlage )
    {
        $this->data = $dataHeizungsanlage;
    }
    
    public function WaermeerzeugerBauweise4701()
    {
        switch ( $this->data['slug'] )
        {
            case 'standardkessel':
                return $this->data['baujahr'] >=  1995 ? 'Standard-Heizkessel (ab 1995)': 'Standard-Heizkessel als Gas-Spezial-Heizkessel';
            case 'niedertemperaturkessel':
                return $this->data['baujahr'] >=  1995 ? 'Niedertemperatur-Heizkessel (ab 1995)': 'Niedertemperatur-Heizkessel als Gas-Spezial-Heizkessel';
            case 'brennwertkessel':
                return $this->data['baujahr'] >=  1995 ? 'Brennwertkessel (ab 1995)': 'Brennwertkessel (bis 1994)';
            case 'brennwertkesselverbessert':
                return 'Brennwertkessel-verbessert';
            case 'fernwaerme':
                return 'Fern-/Nahwärme';
            case 'waermepumpeluft':
                return 'Elektrisch betriebene Luft/Wasser-Heizungswärmepumpe';
            case 'waermepumpewasser':
                return 'Elektrisch betriebene Wasser/Wasser-Heizungswärmepumpe';
            case 'waermepumpeerde':
                return 'Elektrisch betriebene Sole/Wasser-Heizungswärmepumpe';
            case 'oelofen':
                return 'Ölbefeuerter Einzelofen';
            case 'gasraumheizer':
                return 'Gasraumheizer, schornsteingebunden';
            case 'kohleholzofen':
                return 'Kachelofen';
            case 'nachtspeicher':
                return 'Zentral elektrisch beheizte Wärmeerzeuger';
            case 'direktheizgeraet':
                return 'Dezentrales elektrisches Direktheizgerät';
            case 'solaranlage':
                return 'Solare Heizungsunterstützung';
            case 'kleinthermeniedertemperatur':
            case 'kleinthermebrennwert':
            case 'elektrospeicher':
                return 'Sonstiges';
        }
    }

    public function Nennleistung()
    {
        return 0;
    }

    public function WaermeerzeugerBaujahr()
    {
        return $this->data['baujahr'];
    }

    public function AnzahlBaugleiche()
    {
        return 1;
    }
}