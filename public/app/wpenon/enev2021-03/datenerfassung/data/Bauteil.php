<?php

/**
 * Bauteil Klasse
 * 
 * Vorrübergehend bis zur neuen Berechnng
 */
class Bauteil {
    protected $key;
    protected $data;

    public function __construct( string $key, array $bauteilDaten )
    {
        $this->data = $bauteilDaten;
    }

    public function Flaechenbezeichnung()
    {
        return $this->data['name'];
    }

    public function Flaeche()
    {
        return round( $this->data['a'], 3 );
    }

    public function Uwert()
    {
        return round( $this->data['u'], 3 );
    }

    public function Ausrichtung()
    {
        return strtoupper( $this->data['richtung'] );
    }

    public function GrenztAn()
    {
        switch( $this->key )
        {
            case 'kellerwand':
            case 'boden':
                return 'Erdreich';
            case 'kellerdecke':
            case 'decke':
                return 'Raumluft';
            default:
                return 'Aussenluft';
        }
    }
}