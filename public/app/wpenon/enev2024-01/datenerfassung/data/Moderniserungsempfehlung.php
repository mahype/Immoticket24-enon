<?php

/**
 * Empfehlung Klasse
 * 
 * Vorrübergehend bis zur neuen Berechnng
 */
class Moderniserungsempfehlung {
    protected $data;

    public function __construct( array $empfehlungDaten )
    {
        $this->data = $empfehlungDaten;
    }

    public function BauteilAnlagenteil()
    {
        return $this->data['dibt_value'];
    }

    public function Massnahmenbeschreibung()
    {
        return html_entity_decode( $this->data['beschreibung'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public function Modernisierungskombination()
    {
        if( $this->data['gesamt'] )
        {
            return 'in Zusammenhang mit größerer Modernisierung';
        }

        return 'als Einzelmaßnahme';
    }
}