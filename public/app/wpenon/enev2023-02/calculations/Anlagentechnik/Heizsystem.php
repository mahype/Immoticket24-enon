<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;

require_once __DIR__ . '/Heizungsanlage.php';
require_once __DIR__ . '/Heizungsanlagen.php';
require_once __DIR__ . '/Uebergabesysteme.php';
require_once __DIR__ . '/Wasserversorgungen.php';
require_once __DIR__ . '/Pufferspeicher.php';


/**
 * Berechnung des Heizsystems.
 */
class Heizsystem {

	/**
	 * Gebaeude.
	 * 
	 * @var Gebaeude 
	 */
	protected Gebaeude $gebaeude;

	/**
	 * Liste der Heizungsanlagen.
	 *
	 * @var Heizungsanlagen
	 */
	protected Heizungsanlagen $heizungsanlagen;

	/**
	 * Liste der Übergabesysteme.
	 *
	 * @var Uebergabesysteme
	 */
	protected Uebergabesysteme $uebergabesysteme;

	/**
	 * Wasserversorgungen.
	 *
	 * @var Wasserversorgungen
	 */
	protected Wasserversorgungen $wasserversorgungen;

	/**
	 * Pufferspeicher.
	 * 
	 * @var Pufferspeicher 
	 */
	protected Pufferspeicher $pufferspeicher;

	/**
	 * Konstruktor.
	 */
	public function __construct( Gebaeude $gebaeude ) {
		$this->gebaeude		 	  = $gebaeude;
		$this->heizungsanlagen    = new Heizungsanlagen();
		$this->uebergabesysteme   = new Uebergabesysteme();
		$this->wasserversorgungen = new Wasserversorgungen();
		// $this->pufferspeicher     = new Pufferspeicher( $gebaeude );
	}

	/**
	 * Heizungsanlagen.
	 *
	 * @return Heizungsanlagen
	 */
	public function heizungsanlagen(): Heizungsanlagen {
		return $this->heizungsanlagen;
	}

	/**
	 * Übergabesysteme.
	 *
	 * @return Uebergabesysteme
	 */
	public function uebergabesysteme(): Uebergabesysteme {
		return $this->uebergabesysteme;
	}

	/**
	 * Wasserversorgungen.
	 *
	 * @return Wasserversorgungen
	 */
	public function wasserversorgungen(): Wasserversorgungen {
		return $this->wasserversorgungen;
	}

	/**
	 * Pufferspeicher.
	 * 
	 * @return Pufferspeicher 
	 */
	public function pufferspeicher(): Pufferspeicher {
		$this->pufferspeicher;
	}

	/**
	 * Berechnung von fh-a.
	 *
	 * @return float
	 */
	public function fa_h(): float {
		$fa_h = 0;

		if ( count( $this->heizungsanlagen()->alle() ) === 0 ) {
			throw new Calculation_Exception( 'Keine Heizungsanlagen vorhanden' );
		}

		/**
		 * Wir berecnen fh-a des Heizungsystems, indem wir alle Heizungsanlagen sowie Übergabesysteme durchlaufen
		 * und den fa-h Wert anhand der Auslegungstemperaturen des Übergabesystems ermitteln. Die Werte werden
		 * anteilig der einzelnen Heizungsanlagen und Übergabesysteme gewichtet.
		 */
		foreach ( $this->heizungsanlagen()->alle() as $heizungsanlage ) {
			if ( count( $this->uebergabesysteme()->alle() ) === 0 ) {
				throw new Calculation_Exception( 'Keine Übergabesysteme vorhanden' );
			}

			foreach ( $this->uebergabesysteme()->alle() as $uebergabesystem ) {
				$fa_h += $heizungsanlage->fa_h( $uebergabesystem->auslegungstemperaturen() ) * $heizungsanlage->prozentualer_faktor() * $uebergabesystem->prozentualer_faktor();
			}
		}

		return $fa_h;
	}

	/**
	 * Heizungsanlage mit groesstem Anteil.
	 * 
	 * @return Heizungsanlage 
	 */
	public function heizungasanlage_mit_groesstem_anteil(): Heizungsanlage {
		// Finde Heizugnsanlage mit groesstem Anteil
		foreach( $this->heizungsanlagen()->alle() as $heizungsanlage ) {
			if( !isset( $max ) ) {
				$max = $heizungsanlage;
			} else {
				if( $heizungsanlage->prozentualer_faktor() > $max->prozentualer_faktor() ) {
					$max = $heizungsanlage;
				}
			}
		}

		return $max;		
	}
}
