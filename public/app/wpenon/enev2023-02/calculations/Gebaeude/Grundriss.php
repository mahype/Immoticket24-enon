<?php

namespace Enev\Schema202302\Calculations\Gebaeude;

require_once dirname( __DIR__ ) . '/Calculation_Exception.php';

use Enev\Schema202302\Calculations\Calculation_Exception;

/**
 * Die Klasse Grundriss repräsentiert einen Grundriss eines Gebäudes.
 */
class Grundriss {

	/**
	 * Form des Grundrisses. Mögliche Werte: a, b, c, d.
	 *
	 * @var string
	 */
	protected string $form;

	/**
	 * Formen der Grundrisse.
	 *
	 * @var array
	 */
	protected array $formen;

	/**
	 * Himmelsrichtungen.
	 *
	 * @var string[]
	 */
	protected $himmelsrichtungen;

	/**
	 * Ausrichtung des Grundrisses.
	 *
	 * @var string
	 */
	protected string $ausrichtung;

	/**
	 * Längen der Wände.
	 *
	 * @var array
	 */
	protected array $waende;

	/**
	 * Zuordnung der Wände zu den Himmelsrichtungen.
	 *
	 * @var array
	 */
	protected array $waende_zu_himmelsrichtungen = array();

	/**
	 * Konstruktor.
	 *
	 * @param string $form        Form a, b, c oder d.
	 * @param string $ausrichtung Himmelsrichtung.
	 */
	public function __construct( string $form, string $ausrichtung ) {
		$this->init_formen();
		$this->init_himmelsrichtungen();

		if ( ! $this->form_existiert( $form ) ) {
			throw new Calculation_Exception( 'Ungültige Form' );
		}

		if ( ! $this->himmelsrichtung_existiert( $ausrichtung ) ) {
			throw new Calculation_Exception( 'Ungültige Himmelsrichtung' );
		}

		$this->form        = $form;
		$this->ausrichtung = $ausrichtung;
		$this->init_waende_zu_himmelsrichtungen();
	}

	/**
	 * Ausrichtung des Grundrisses.
	 * 
	 * @return string 
	 */
	public function ausrichtung(): string {
		return  $this->ausrichtung;
	}

	/**
	 * Initialisiert die Himmelsrichtungen.
	 *
	 * @return void
	 */
	private function init_himmelsrichtungen() {
		$this->himmelsrichtungen = array(
			's'  => 'Süden',
			'so' => 'Südosten',
			'o'  => 'Osten',
			'no' => 'Nordosten',
			'n'  => 'Norden',
			'nw' => 'Nordwesten',
			'w'  => 'Westen',
			'sw' => 'Südwesten',
		);
	}

	/**
	 * Initialisierre die Zuordnung der Wände zu den Himmelsrichtungen.
	 */
	private function init_waende_zu_himmelsrichtungen() {
		$himmelsrichtungen_keys = array_keys( $this->himmelsrichtungen );
		$nullrichtung           = array_search( $this->ausrichtung, $himmelsrichtungen_keys );

		for ( $i = 0; $i < 4; $i++ ) {
			$key                                 = ( $nullrichtung + 2 * $i ) % 8;
			$himmelsrichtung                     = $himmelsrichtungen_keys[ $key ];
			$this->waende_zu_himmelsrichtungen[] = $himmelsrichtung;
		}
	}

	/**
	 * Initialisiert die Formen.
	 *
	 * @return void
	 */
	protected function init_formen() {
		$this->formen = array(
			'a' => array(
				'a'      => array( true, 0 ),
				'b'      => array( true, 1 ),
				'c'      => array( 'a', 2 ),
				'd'      => array( 'b', 3 ),
				'waende' => array( 'a', 'b', 'c', 'd' ),
				'fla'    => array(
					array( 'a', 'b' ),
				),
			),
			'b' => array(
				'a'      => array( true, 0 ),
				'b'      => array( true, 1 ),
				'c'      => array( true, 2 ),
				'd'      => array( true, 3 ),
				'e'      => array( 'a - c', 2 ),
				'f'      => array( 'b - d', 3 ),
				'waende' => array( 'a', 'b', 'c', 'd', 'e', 'f' ),
				'fla'    => array(
					array( 'a', 'f' ),
					array( 'c', 'd' ),
				),
			),
			'c' => array(
				'a'      => array( true, 0 ),
				'b'      => array( true, 1 ),
				'c'      => array( true, 2 ),
				'd'      => array( true, 1 ),
				'e'      => array( true, 2 ),
				'f'      => array( 'd', 3 ),
				'g'      => array( 'a - c - e', 2 ),
				'h'      => array( 'b', 3 ),
				'waende' => array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h' ),
				'fla'    => array(
					array( 'a', 'b' ),
					array( 'd', 'e' ),
				),
			),
			'd' => array(
				'a'      => array( true, 0 ),
				'b'      => array( true, 1 ),
				'c'      => array( true, 2 ),
				'd'      => array( true, 3 ),
				'e'      => array( true, 2 ),
				'f'      => array( true, 1 ),
				'g'      => array( 'a - c - e', 2 ),
				'h'      => array( 'b - d + f', 3 ),
				'waende' => array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h' ),
				'fla'    => array(
					array( 'a', 'b - d' ),
					array( 'c', 'd' ),
					array( 'f', 'g' ),
				),
			),
		);
	}

	/**
	 * Prüft, ob die Form existiert.
	 *
	 * @param  string $form Name der Form (a, b, c, d).
	 * @return bool True, wenn die Form existiert, sonst false.
	 */
	public function form_existiert( string $form ): bool {
		return array_key_exists( $form, $this->formen );
	}

	/**
	 * Prüft, ob die Himmelsrichtung existiert.
	 *
	 * @param  string $himmelsrichtung
	 * @return bool
	 */
	protected function himmelsrichtung_existiert( string $himmelsrichtung ): bool {
		return array_key_exists( $himmelsrichtung, $this->himmelsrichtungen );
	}

	/**
	 * Gibt alle Wände zurück.
	 *
	 * @return array
	 */
	public function waende(): array {
		return $this->formen[ $this->form ]['waende'];
	}

	/**
	 * Gibt alle Seiten zurück, die manuell eingestellt werden können.
	 * 
	 * @return array 
	 */
	public function seiten_manuell(): array {
		$seiten = array();

		foreach( $this->formen[$this->form()] as $seiten_name => $seite ) {
			if ( $this->wand_laenge_berechnet( $seiten_name ) || $seiten_name === 'waende' || $seiten_name === 'fla' ) {
				continue;
			}

			$seiten[] = $seiten_name;
		}

		return $seiten;
	}

	/**
	 * Gibt alle Wände zurück, die manuell eingestellt werden können.
	 *
	 * @return array
	 */
	public function waende_manuell(): array {
		foreach ( $this->waende() as $wand ) {
			if ( $this->wand_laenge_berechnet( $wand ) ) {
				continue;
			}

			$waende[] = $wand;
		}

		return $waende;
	}

	/**
	 * Gibt alle Wände zurück, die automatisch berechnet werden.
	 *
	 * @return array
	 */
	public function waende_berechnet(): array {
		foreach ( $this->waende() as $wand ) {
			if ( ! $this->wand_laenge_berechnet( $wand ) ) {
				continue;
			}

			$waende[] = $wand;
		}

		return $waende;
	}

	/**
	 * Wandlänge setzen oder abrufen.
	 *
	 * @param  string     $seite
	 * @param  float|null $laenge
	 * @return mixed
	 */
	public function wand_laenge( string $seite, float $laenge = null ) {
		if ( ! $this->wand_existiert( $seite ) ) {
			throw new Calculation_Exception( sprintf( 'Ungültige Wand %s', $seite ) );
		}

		if ( $laenge !== null && $this->wand_laenge_berechnet( $seite ) ) {
			throw new Calculation_Exception( 'Die Länge der Wand kann nicht gesetzt werden' );
		}

		if ( $this->wand_laenge_berechnet( $seite ) === true ) {
			return $this->wand_laenge_berechnen( $seite );
		}

		if ( $laenge !== null ) {
			$this->waende[ $seite ] = $laenge;
		}

		if ( ! isset( $this->waende[ $seite ] ) ) {
			throw new Calculation_Exception( sprintf( 'Die Länge der Wand %s wurde nicht gesetzt', $seite ) );
		}

		return $this->waende[ $seite ];
	}

	/**
	 * Himmelsrichtung der Wand.
	 * 
	 * @param string $wand Name der Wand (a, b, c, d, e, f, g, h).
	 * @return string 
	 */
	public function wand_himmelsrichtung( string $wand ): string {
		$himmesrichtung_index = $this->formen[ $this->form ][$wand][1];
		return $this->waende_zu_himmelsrichtungen[ $himmesrichtung_index ];
	}

	/**
	 * Check ob alle Wandlängen gesetzt sind.
	 * 
	 * @return bool 
	 */
	public function wand_laengen_komplett(): bool {
		$form = $this->formen[ $this->form ];

		foreach ( $form as $wand => $data ) {
			if ( $this->wand_laenge_berechnet( $wand ) ) {
				continue;
			}

			if ( ! array_key_exists( $wand, $this->waende ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Wandlänge berechnen.
	 *
	 * @param  string $seite Name der Wand (a, b, c, d, e, f, g, h).
	 * @return float Wandlänge. 
	 *
	 * @throws Exception
	 */
	protected function wand_laenge_berechnen( string $seite ) {
		if ( ! $this->wand_laengen_komplett() ) {
			throw new Calculation_Exception( 'Die Längen der Wände sind nicht komplett' );
		}

		$form = $this->formen[ $this->form ];

		$laenge           = 0.0;
		$current_operator = '+';

		$formel = explode( ' ', $form[ $seite ][0] );

		foreach ( $formel as $t ) {
			switch ( $t ) {
				case '+':
				case '-':
					$current_operator = $t;
					break;
				default:
					switch ( $current_operator ) {
						case '+':
							$laenge += $this->wand_laenge( $t );
							break;
						case '-':
							$laenge -= $this->wand_laenge( $t );
							break;
						default:
					}
			}
		}

		return $laenge;
	}

	/**
	 * Prüft, ob die Wand für die Form existiert.
	 *
	 * @param  string $wand Name der Wand (a, b, c, d, e, f, g, h).
	 * @return bool True, wenn die Wand existiert, sonst false.
	 */
	public function wand_existiert( string $wand ): bool {
 		return array_key_exists( $wand, $this->formen[ $this->form ] );
	}

	/**
	 * Prüft ob die länge der Wand gesetzt werden darf, oder automatisch berechnet wird.
	 *
	 * @param  string $wand Name der Wand (a, b, c, d, e, f, g, h).
	 * @return bool True, wenn die Länge gesetzt werden darf, sonst false.
	 */
	public function wand_laenge_berechnet( string $wand ): bool {
		return $this->formen[ $this->form ][ $wand ][0] === true ? false : true;
	}

	/**
	 * Gibt die Form des Grundrisses zurück.
	 *
	 * @return array
	 */
	public function form(): string {
		return $this->form;
	}

	/**
	 * Gibt die Formel zur Berechnung der Fläche zurück.
	 *
	 * @return array
	 */
	public function flaechenberechnungsformel(): array {
		return $this->formen[ $this->form ]['fla'];
	}

	/**
	 * Fläche des Grundrisses berechnen.
	 *
	 * @return float
	 *
	 * @throws Exception
	 */
	public function flaeche(): float {
		$grundflaeche = 0.0;
		foreach ( $this->flaechenberechnungsformel() as $_produkt ) {
			$produkt = 1.0;
			for ( $i = 0; $i < 2; $i++ ) {
				$_faktor          = $_produkt[ $i ];
				$faktor           = 0.0;
				$current_operator = '+';
				$_faktor          = explode( ' ', $_faktor );

				foreach ( $_faktor as $t ) {
					switch ( $t ) {
						case '+':
						case '-':
							$current_operator = $t;
							break;
						default:
							switch ( $current_operator ) {
								case '+':
									$faktor += $this->wand_laenge( $t );
									break;
								case '-':
									$faktor -= $this->wand_laenge( $t );
									break;
								default:
							}
					}
				}

				if ( $faktor < 0.0 ) {
					$faktor = 0.0;
				}

				$produkt *= $faktor;
			}
			$grundflaeche += $produkt;
		}

		return $grundflaeche;
	}

	/**
	 * Länge des Grundrisses berechnen.
	 *
	 * @return float
	 *
	 * @throws Exception
	 */
	public function wand_laenge_gesamt(): float {
		$laenge = 0;
		foreach ( $this->waende() as $wand ) {
			$laenge += $this->wand_laenge( $wand );
		}

		return $laenge;
	}
}
