<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik\Heizungsanlagen;

use Enev\Schema202302\Calculations\Anlagentechnik\Heizungsanlage;
use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202302\Calculations\Tabellen\COP;
use Enev\Schema202302\Calculations\Helfer\Jahr;

require_once dirname( dirname( __DIR__ ) ) . '/Tabellen/COP.php';
require_once dirname( dirname( __DIR__ ) ) . '/Helfer/Jahr.php';

class Zentral_Elektrisch_Beheizte_Waermeerzeuger extends Heizungsanlage {
    /**
	 * Erlaubte Typen für konventionelle Kessel.
	 *
	 * @return array
	 */
	public static function erlaubte_erzeuger(): array {
		return array(
			'waermepumpeluft'   => array(
				'typ'            => 'waermepumpe',
				'energietraeger' => array(
					'strom' => 'Strom',
				),
			),
		);
	}
}