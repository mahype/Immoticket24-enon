<?php

/**
 * Endenergie Energietraeger
 * 
 * Vorrübergehend bis zur neuen Berechnng
 */
class EndenergieEnergietraeger {
    protected $data;

    protected $nutzflaeche;

    public function __construct( array $data, float $nutzflaeche )
    {
        $this->data = $data;
        $this->nutzflaeche = $nutzflaeche;
    }

    public function Energietraegerbezeichnung(){
        switch( $this->data['slug'] )
        {
            case 'heizoel':
                return 'Heizöl';
            case 'erdgas':
            case 'gas':
                return 'Erdgas';            
            case 'biooel':
                return 'Bioöl';
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
            case 'holzpellets': // ?
                return 'Holz';
            case 'holzhackschnitzel': // ?
                return 'Holz';
            case 'strom':
                return 'Strom netzbezogen';
            case 'fernwaermehzwfossil':
                return 'Nah-/Fernwärme aus Heizwerken, fossiler Brennstoff (Stein-/Braunkohle) bzw. Energieträger';
            case 'fernwaermehzwregenerativ':
                return 'Nah-/Fernwärme aus Heizwerken, erneuerbarer Brennstoff bzw. Energieträger';
            case 'fernwaermekwkfossil':
                return 'Nah-/Fernwärme aus KWK, fossiler Brennstoff (Stein-/Braunkohle) bzw. Energieträger';
            case 'fernwaermekwkregenerativ':
                return 'Nah-/Fernwärme aus KWK, erneuerbarer Brennstoff bzw. Energieträger in kWh';
        }
    }

    public function Primaerenergiefaktor(){
        return $this->data['primaerfaktor'];
    }

    public function EndenergiebedarfHeizungspezifisch(){
        return round( $this->data['qh_e_b'] / $this->nutzflaeche, 2 );
    }

    public function EndenergiebedarfKuehlungBefeuchtungspezifisch(){
        return 0;
    }

    public function EndenergiebedarfTrinkwarmwasserspezifisch(){
        return round( $this->data['qw_e_b'] / $this->nutzflaeche, 2 );
    }

    public function EndenergiebedarfBeleuchtungspezifisch(){
        return 0;
    }
    
    public function EndenergiebedarfLueftungspezifisch(){
        return round( $this->data['ql_e_b'] / $this->nutzflaeche, 2 );
    }

    public function EndenergiebedarfEnergietraegerGesamtgebaeudespezifisch(){
        return round( $this->data['q_e_b'] / $this->nutzflaeche, 2 );
    }
}