<?php

namespace Enev\Schema202302\Calculations\Gebaeude;

/**
 * Die Klasse Grundriss repräsentiert einen Grundriss eines Anbaus.
 */
class Grundriss_Anbau extends Grundriss {
    /**
     * Initialisiert die Formen.
     *
     * @return void 
     */
    protected function init_formen()
    {
        $this->formen = array(
            'a' => array(
                'b'   => array( true, 0 ),
                't'   => array( true, 1 ),
                's1'  => array( true, 3 ),
                's2'  => array( 'b', 2 ),
                'fla' => array(
                    array( 'b', 't' ),
                ),                
            ),
            'b' => array(
                'b'   => array( true, 0 ),
                't'   => array( true, 1 ),
                's1'  => array( true, 3 ),
                's2'  => array( true, 2 ),
                'fla' => array(
                    array( 'b', 's2' ),
                    array( 't - s1', 's1' ),
                ),
            )
        );
    }
}