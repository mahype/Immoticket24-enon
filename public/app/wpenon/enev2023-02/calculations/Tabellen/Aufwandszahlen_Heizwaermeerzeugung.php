<?php

namespace Enev\Schema202302\Calculations\Tabellen;

use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Berechnung der Daten zur Mittleren Belastung aus Tablle 79, 80 und 81.
 *
 * @package
 */
class Aufwandszahlen_Heizwaermeerzeugung {
    /**
     * Typ der Heizung.
     * 
     * @var string
     */
    protected string $heizung;

    /**
     * Energieträger.
     * 
     * @var string
     */
    protected string $energietraeger;

    /**
     * Übergabe auslegungstemperatur.
     * 
     * @var float
     */
    protected float $uebergabe_auslegungstemperatur;

    /**
     * ßhg.
     * 
     * @var float
     */
    protected float $ßhg;

    /**
     * Heizung im beheizten Bereich.
     * 
     * @var bool
     */
    protected bool $heizung_im_beheizten_bereich;

	/**
	 * Tabellendaten aus Tabelle 79, 80 und 81.
	 *
	 * @var array
	 */
	protected array $table_data;

	/**
	 * Konstruktor.
	 * 
     * @param string $heizung Typ der Heizung (standardkessel, niedertemperaturkessel, brennwertkessel, kleinthermeniedertemperatur, kleinthermebrennwert, pelletfeuerung, gasraumheizer, oelofenverdampfungsverbrenner).
     * @param string $energietraeger Energieträger (heizoel, erdgas, fluessiggas, biogas, holzpellets, holzhackschnitzel)
	 * @param float $uebergabe_auslegungstemperatur Übergabe Auslegungstemperatur des Übertragunngssystems. 
	 * @param float $ßhg ßhg.
     * @param bool $heizung_im_beheizten_bereich Heizung im beheizten Bereich, ja/nein.
     *  
	 * @return void 
	 */
	public function __construct ( string $heizung, string $energietraeger, float $uebergabe_auslegungstemperatur, float $ßhg, bool $heizung_im_beheizten_bereich ) {
        $this->heizung = $heizung;
        $this->energietraeger = $energietraeger;
        $this->uebergabe_auslegungstemperatur = $uebergabe_auslegungstemperatur;
        $this->ßhg = $ßhg;
        $this->heizung_im_beheizten_bereich = $heizung_im_beheizten_bereich;

		$this->table_data = wpenon_get_table_results( 'ausnutzungsgrad' );
	}

    
}