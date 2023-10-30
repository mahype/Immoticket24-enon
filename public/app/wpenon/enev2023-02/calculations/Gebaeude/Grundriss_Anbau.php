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
	protected function init_formen() {
		$this->formen = array(
			'a' => array(
				'b'      => array( true ),
				't'      => array( true ),
				'b2'     => array( 'b' ),
				't2'     => array( 't - s1' ),
				's1'     => array( true ),								
				'waende' => array( 'b', 'b2', 't', 't2' ),
				'fla'    => array(
					array( 'b', 't' ),
				),
			),
			'b' => array(
				'b'   => array( true ),				
				't'   => array( true ),				
				'b2'  => array( 'b - s2' ),
				's1'  => array( true ),
				's2'  => array( true ),
				't2'  => array( 't - s1' ),				
				'waende' => array( 'b', 'b2', 't', 't2' ),
				'fla' => array(
					array( 's2', 't2' ),
					array( 'b2', 't' ),
				),
			),
		);
	}
}
