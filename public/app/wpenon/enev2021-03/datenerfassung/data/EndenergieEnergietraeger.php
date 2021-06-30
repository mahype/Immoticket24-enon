<?php

/**
 * Endenergie Energietraeger
 * 
 * Vorrübergehend bis zur neuen Berechnng
 */
class EndenergieEnergietraeger {
    protected $data;

    public function __construct( array $data )
    {
        $this->data = $data;
    }

    public function Energietraegerbezeichnung(){
        return $this->data['name'];
    }

    public function Primaerenergiefaktor(){
        return $this->data['primaerfaktor'];
    }

    public function EndenergiebedarfHeizungspezifisch(){
        return round( $this->data['qh_e_b'], 2 );
    }

    public function EndenergiebedarfKuehlungBefeuchtungspezifisch(){
        return 0;
    }

    public function EndenergiebedarfTrinkwarmwasserspezifisch(){
        return round( $this->data['qw_e_b'], 2 );
    }

    public function EndenergiebedarfBeleuchtungspezifisch(){
        return 0;
    }
    
    public function EndenergiebedarfLueftungspezifisch(){
        return round( $this->data['ql_e_b'], 2 );
    }

    public function EndenergiebedarfEnergietraegerGesamtgebaeudespezifisch(){
        return round( $this->data['q_e_b'], 2 );
    }
}