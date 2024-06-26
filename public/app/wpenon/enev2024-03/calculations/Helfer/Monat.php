<?php

namespace Enev\Schema202403\Calculations\Helfer;

/**
 * Klasse zur Darstellung eines Monats.
 */
class Monat
{
	/**
	 * @var string Der Slug des Monats (z.B. 'januar').
	 */
	protected $slug;

	/**
	 * @var int Die Anzahl der Tage im Monat.
	 */
	protected $tage;

	/**
	 * @var string Der vollständige Name des Monats (z.B. 'Januar').
	 */
	protected $name;

	/**
	 * Konstruktor für die Monat-Klasse.
	 *
	 * @param string $slug Der Slug des Monats.
	 * @param int    $tage Die Anzahl der Tage im Monat.
	 * @param string $name Der Name des Monats.
	 */
	public function __construct(string $slug, int $tage, string $name)
	{
		$this->slug = $slug;
		$this->tage = $tage;
		$this->name = $name;
	}

	/**
	 * Liefert den Slug des Monats.
	 *
	 * @return string Der Slug des Monats.
	 */
	public function slug()
	{
		return $this->slug;
	}

	/**
	 * Liefert die Anzahl der Tage im Monat.
	 *
	 * @return int Die Anzahl der Tage.
	 */
	public function tage()
	{
		return $this->tage;
	}

	/**
	 * Liefert den Namen des Monats.
	 *
	 * @return string Der Name des Monats.
	 */
	public function name()
	{
		return $this->name;
	}
}
