<?php

namespace Enev\Schema202302\Calculations\Gebaeude;

require_once __DIR__ . '/Grundriss.php';

/**
 * Die Klasse Grundriss repräsentiert einen Grundriss eines Anbaus.
 */
class Grundriss_Anbau extends Grundriss {
	/**
	 * Initialisiert die Formen.
	 *
	 * @return void
	 */
	protected function init_formen() {
		$this->formen = array(
			'a' => array(
				'b'      => array( true, 0 ),
				't'      => array( true, 1 ),
				'b2'     => array( 'b', 2 ),
				't2'     => array( 't - s1', 3 ),
				's1'     => array( true ),								
				'waende' => array( 'b', 'b2', 't', 't2' ),
				'fla'    => array(
					array( 'b', 't' ),
				),
			),
			'b' => array(
				'b'   => array( true, 0  ),				
				't'   => array( true, 1  ),				
				'b2'  => array( 'b - s2', 2  ),
				't2'  => array( 't - s1', 3 ),				
				's1'  => array( true ),
				's2'  => array( true ),				
				'waende' => array( 'b', 'b2', 't', 't2' ),
				'fla' => array(
					array( 's2', 't2' ),
					array( 'b2', 't' ),
				),
			),
		);
	}
}
