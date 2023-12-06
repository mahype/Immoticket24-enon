<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik\Heizungsanlagen;

use Enev\Schema202302\Calculations\Anlagentechnik\Heizungsanlage;
use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;

class Dezentral extends Heizungsanlage {
    /**
     * Gebaeude.
     * 
     * @var Gebaeude
     */
    protected Gebaeude $gebaeude;

    /**
     * Konstruktor.
     * 
     * @param Gebaeude $gebaeude
     * @param string $erzeuger 
     * @param string $energietraeger 
     * @param int $baujahr 
     * @param bool $heizung_im_beheizten_bereich 
     * @param string $energietraeger_warmwasser 
     * @param int $prozentualer_anteil 
     * @return void 
     * @throws Calculation_Exception 
     */
    public function __construct( Gebaeude $gebaeude, string $erzeuger, string $energietraeger, int $baujahr, bool $heizung_im_beheizten_bereich, int $prozentualer_anteil = 100 )
    {
        parent::__construct( $erzeuger, $energietraeger, $baujahr, $heizung_im_beheizten_bereich, $prozentualer_anteil );
        $this->gebaeude = $gebaeude;
    }

    /**
	 * Erlaubte Typen für konventionelle Kessel.
	 *
	 * @return array
	 */
	public static function erlaubte_erzeuger(): array {
		return array(
			'infrarotheizung'   => array(
				'typ'            => 'infrarotheizung',
				'energietraeger' => array(
					'strom' => 'Strom',
				),
			),
			'elektronachtspeicherheizung'   => array(
				'typ'            => 'elektronachtspeicherheizung',
				'energietraeger' => array(
					'strom' => 'Strom',
				),
			),
		);
	}

    /**
     * Erzeugung Korrekturfaktur für die Heizungsanlage.
     * 
     * @return float 
     */
    public function ehg(): float {
        return 1.0;
    }

    public function ewg(): float {
        //      if "Elektrodurchlauferhitzer" than  // Wird nur hydraulischer Durchlauferhitzer wird berücksichtigt (auf der sicheren Seite) // Gilt auch für Elektro-Kleinspeicher
        //           $ewg = 1.01;
        //      if "Gasdurchlauferhitzer"   Than
        //             $ewg = 1.26
        //      else??
        if( $this->gebaeude->trinkwarmwasseranlage()->dezentraler_erzeuger() === 'dezentralelektroerhitzer' ) {
            return 1.01;
        }

        return 1.26;
    }

    public function Whg(): float {
        // $Whg = 0; T12, Seite 159, hier wird gesamt Wg = definiert (also sowohl Heizung wie auch TWW), Werden nur bei Übergabe berücksichtig siehe T5 S.157, Kommentar 6.5.8.1
        return 0;
    }
}