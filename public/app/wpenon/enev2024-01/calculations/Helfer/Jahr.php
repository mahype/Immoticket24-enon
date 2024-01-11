<?php

namespace Enev\Schema202401\Calculations\Helfer;

use Enev\Schema202401\Calculations\Calculation_Exception;

require_once __DIR__ . '/Monat.php';

/**
 * Klasse zur Darstellung eines Jahres und seiner Monate.
 */
class Jahr {
	/**
	 * @var array Liste der Monate mit deren Details
	 */
	protected static $monateListe = array(
		'januar'    => array(
			'tage' => 31,
			'name' => 'Januar',
		),
		'februar'   => array(
			'tage' => 28,
			'name' => 'Februar',
		),
		'maerz'     => array(
			'tage' => 31,
			'name' => 'März',
		),
		'april'     => array(
			'tage' => 30,
			'name' => 'April',
		),
		'mai'       => array(
			'tage' => 31,
			'name' => 'Mai',
		),
		'juni'      => array(
			'tage' => 30,
			'name' => 'Juni',
		),
		'juli'      => array(
			'tage' => 31,
			'name' => 'Juli',
		),
		'august'    => array(
			'tage' => 31,
			'name' => 'August',
		),
		'september' => array(
			'tage' => 30,
			'name' => 'September',
		),
		'oktober'   => array(
			'tage' => 31,
			'name' => 'Oktober',
		),
		'november'  => array(
			'tage' => 30,
			'name' => 'November',
		),
		'dezember'  => array(
			'tage' => 31,
			'name' => 'Dezember',
		),
	);

	/**
	 * @var int|null Das aktuelle Jahr
	 */
	protected $jahr;

	/**
	 * Konstruktor für die Jahr-Klasse.
	 *
	 * @param int|null $jahr Das zu repräsentierende Jahr. Wenn null, wird Schaltjahr nicht berücksichtigt.
	 */
	public function __construct( int $jahr = null ) {
		$this->jahr = $jahr;
		if ( $this->istSchaltjahr() && isset( self::$monateListe['februar'] ) ) {
			self::$monateListe['februar']['tage'] = 29;
		}
	}

	/**
	 * Überprüft, ob das aktuelle Jahr ein Schaltjahr ist.
	 *
	 * @return bool True, wenn Schaltjahr, sonst false.
	 */
	protected function istSchaltjahr() {
		if ( $this->jahr === null ) {
			return false;
		}
		return ( $this->jahr % 4 === 0 && $this->jahr % 100 !== 0 ) || $this->jahr % 400 === 0;
	}

	/**
	 * Liefert eine Liste von Monat-Objekten für das Jahr.
	 *
	 * @return Monat[] Eine Liste von Monat-Objekten.
	 */
	public function monate() {
		$result = array();
		foreach ( self::$monateListe as $slug => $details ) {
			$result[] = new Monat( $slug, $details['tage'], $details['name'] );
		}
		return $result;
	}

	/**
	 * Liefert ein Monat-Objekt basierend auf dem gegebenen Monatsnamen.
	 *
	 * @param string $monatSlug Der Slug des Monats (z.B. 'januar').
	 * @return Monat Das Monat-Objekt.
	 * @throws Exception Wenn der Monatsname ungültig ist.
	 */
	public function monat( string $monatSlug ) {
		if ( isset( self::$monateListe[ $monatSlug ] ) ) {
			$details = self::$monateListe[ $monatSlug ];
			return new Monat( $monatSlug, $details['tage'], $details['name'] );
		}
		throw new Calculation_Exception( 'Ungültiger Monatsname' );
	}
}
