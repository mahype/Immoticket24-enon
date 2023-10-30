<?php

namespace Enev\Schema202302\Calculations\Gebaeude;

/**
 * Diese Klasse repräsentiert einen Anbau für ein Gebäude.
 */
class Anbau {

	/**
	 * Grundriss des Anbaus.
	 *
	 * @var Grundriss_Anbau
	 */
	private Grundriss_Anbau $grundriss;

	/**
	 * Höhe des Anbaus.
	 *
	 * @var float
	 */
	private float $hoehe;

	/**
	 * Formeln zur Berechnung der Überlappungen mit dem Hauptgebäude.
	 */
	private array $ueberlappungen = array();

	/**
	 * Konstruktor.
	 *
	 * @param Grundriss_Anbau $grundriss Grundriss des Anbaus.
	 * @param float           $hoehe     Höhe des Anbaus.
	 */
	public function __construct( Grundriss_Anbau $grundriss, float $hoehe ) {
		$this->grundriss = $grundriss;
		$this->hoehe     = $hoehe;

		$this->ueberlappungen = array(
			'a' => array(
				'mapping' => array(
					'b' => 's1',
				),
				'fla'     => array(
					array( 's1', 'hoehe' ),
				),
			),
			'b' => array(
				'mapping' => array(
					'a' => 's2',
					'b' => 's1',
				),
				'fla'     => array(
					array( 's1', 'hoehe' ),
					array( 's2', 'hoehe' ),
				),
			),
		);
	}

	/**
	 * Grundriss des Anbaus.
	 *
	 * @return Grundriss_Anbau
	 */
	public function grundriss(): Grundriss_Anbau {
		return $this->grundriss;
	}

	/**
	 * Höhe des Anbaus.
	 *
	 * @return float
	 */
	public function hoehe(): float {
		return $this->hoehe + 0.25;
	}

	/**
	 * Gibt die Flächenberechnungsformem für die Überlappungen des Anbaus mit dem Hauptgebäude zurück.
	 *
	 * @return mixed
	 */
	protected function flaechenberechnungsformel() {
		return $this->ueberlappungen[ $this->grundriss->form() ]['fla'];
	}

	/**
	 * Mapping der sich überlappenden Flächen.
	 *
	 * @return mixed
	 */
	protected function mapping() {
		return $this->ueberlappungen[ $this->grundriss->form() ]['mapping'];
	}

	/**
	 * Berechnet die Fläche einer Wand des Anbaus.
	 *
	 * @param string $wand Name der Wand des Anbaus.
	 * @return float
	 *
	 * @throws Exception
	 */
	public function wand_flaeche( string $wand ) {
		return $this->grundriss->wand_laenge( $wand ) * $this->hoehe() - $this->ueberlappung_flaeche( $wand );
	}

	/**
	 * Berechnet die Fläche der Überlappung des Anbaus mit dem Hauptgebäude.
	 *
	 * @return float
	 */
	public function ueberlappung_flaeche( string $wand = null ): float {
		$grundflaeche               = 0.0;
		$wand_ueberlappung_flaechen = array();

		foreach ( $this->flaechenberechnungsformel() as $wand_formel ) {
			$laenge = $this->grundriss()->wand_laenge( $wand_formel[0] );
			$hoehe  = $this->hoehe();

			$wand_ueberlappung_flaechen[ $wand_formel[0] ] = $laenge * $hoehe;
			$grundflaeche                                 += $laenge * $hoehe;
		}

		if ( $wand !== null ) {
			return $wand_ueberlappung_flaechen[ $wand ];
		}

		return $grundflaeche;
	}

	/**
	 * Berechnet die Fläche der Überlappung einer Wand des Hauptgebäudes mit dem Anbau.
	 *
	 * @param  string $wand Wand des Gebäudes.
	 * @return float Überlappende Fläche
	 *
	 * @throws Exception
	 */
	public function ueberlappung_flaeche_gebaeude( string $wand ): float {
		if ( ! array_key_exists( $wand, $this->mapping() ) ) {
			return 0;
		}

		$wand_anbau = $this->mapping()[ $wand ];
		return $this->ueberlappung_flaeche( $wand_anbau );
	}

	/**
	 * Berechnet das Volumen des Anbaus.
	 *
	 * @return float
	 * @throws Exception
	 */
	public function volumen(): float {
		return $this->grundriss->flaeche() * $this->hoehe();
	}
}
