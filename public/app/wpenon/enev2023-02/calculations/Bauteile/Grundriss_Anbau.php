<?php

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
                'a'   => array( true, 0 ),
                'b'   => array( true, 1 ),
                'c'   => array( 'a', 2 ),
                'd'   => array( 'b', 3 ),
                'fla' => array(
                    array( 'a', 'b' ),
                ),
            ),
            'b' => array(
                'a'   => array( true, 0 ),
                'b'   => array( true, 1 ),
                'c'   => array( true, 2 ),
                'd'   => array( true, 3 ),
                'e'   => array( 'a - c', 2 ),
                'f'   => array( 'b - d', 3 ),
                'fla' => array(
                    array( 'a', 'f' ),
                    array( 'c', 'd' ),
                ),
            ),
            'c' => array(
                'a'   => array( true, 0 ),
                'b'   => array( true, 1 ),
                'c'   => array( true, 2 ),
                'd'   => array( true, 1 ),
                'e'   => array( true, 2 ),
                'f'   => array( 'd', 3 ),
                'g'   => array( 'a - c - e', 2 ),
                'h'   => array( 'b', 3 ),
                'fla' => array(
                    array( 'a', 'b' ),
                    array( 'd', 'e' ),
                ),
            ),
            'd' => array(
                'a'   => array( true, 0 ),
                'b'   => array( true, 1 ),
                'c'   => array( true, 2 ),
                'd'   => array( true, 3 ),
                'e'   => array( true, 2 ),
                'f'   => array( true, 1 ),
                'g'   => array( 'a - c - e', 2 ),
                'h'   => array( 'b - d + f', 3 ),
                'fla' => array(
                    array( 'a', 'b - d' ),
                    array( 'c', 'd' ),
                    array( 'f', 'g' ),
                ),
            ),
        );
    }
}