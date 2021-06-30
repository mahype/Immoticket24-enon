<?php

class Heizungsanlage {
    protected array $data;

    public function __construct( $dataHeizungsanlage )
    {
        $this->data = $dataHeizungsanlage;
    }
    
    public function WaermeerzeugerBauweise4701()
    {
        switch ( $this->data['slug'] )
        {
            case 'direktheizgeraet':
                return 'Dezentrales elektrisches Direktheizgerät';
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
            case 'solaranlage':
                return 'Solare Heizungsunterstützung';
            case 'kleinthermeniedertemperatur':
            case 'kleinthermebrennwert':
            default:
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

    public function Energietraeger()
    {
        array(
            'heizoel'                  => 0,
            'biooel'                   => 2,
            'erdgas'                   => 3,
            'erdgasbiogas'             => 4,
            'biogas'                   => 5,
            'fluessiggas'              => 6,
            'steinkohle'               => 7,
            'koks'                     => - 1,
            'braunkohle'               => 8,
            'stueckholz'               => 9,
            'holzhackschnitzel'        => 9,
            'holzpellets'              => 9,
            'strom'                    => 14,
            'fernwaermehzwfossil'      => 12,
            'fernwaermehzwregenerativ' => 13,
            'fernwaermekwkfossil'      => 10,
            'fernwaermekwkregenerativ' => 11,
            'sonneneinstrahlung'       => 15,
        );

        switch ( $this->data['energietraeger_slug'] )
        {
            case 'heizoel':
                return 'Heizöl';
            case 'biooel':
                return 'Bioöl';
            case 'erdgas':
                return 'Erdgas'; 
            case 'erdgasbiogas':
                return 'biogenes Flüssiggas'; // Neu
            case 'biogas':
                return 'Biogas';
            case 'fluessiggas':
                return 'Flüssiggas';
            case 'steinkohle':
                return 'Steinkohle';                                                     
            case 'braunkohle':
                return 'Braunkohle';
            case 'stueckholz':
                return 'Holz';
            case 'holzhackschnitzel':
                return 'Holz';
            case 'holzpellets':
                return 'Holz';
            case 'strom':
                return 'Strom netzbezogen'; // NEU
            case 'fernwaermehzwfossil':
                return 'Nah-/Fernwärme aus Heizwerken, fossiler Brennstoff (Gasförmige und flüssige Brennstoffe) bzw. Energieträger'; // NEU
            case 'sonneneinstrahlung':
                return 'Wärme (Erdwärme, Geothermie, Solarthermie, Umgebungswärme)'; // NEU
            case 'koks':
                return 'Sonstiges';                                         
        }
    }

    public function Primaerenergiefaktor()
    {
        return (float) $this->data['energietraeger_primaer'];
    }

    public function Emissionsfaktor()
    {
        return $this->data['energietraeger_co2'] * 1000; // Neu
    }
}