<?php

namespace Enev\Schema202403\Calculations\Tabellen;

/**
 * Jährliche Endenergie aus Photovoltaikanlagen (flächenbezogen) aus Tabelle 115.
 *
 * @package
 */
class Endenergie_Photovoltaikanlagen
{
	/**
	 * Neigung.
	 *
	 * @var float
	 */
	protected float $neigung;

	/**
	 * Ausrichtung.
	 *
	 * @var string
	 */
	protected string $ausrichtung;

	/**
	 * Tabellendaten aus Tabelle 115.
	 *
	 * @var array
	 */
	protected array $table_data;

	/**
	 * Konstruktor.
	 *
	 * @param float  $neigung Neigung in Grad (0, 30, 45, 60, 90).
	 * @param string $ausrichtung Ausrichtung. (n, no, o, so, s, sw, w, nw).
	 */
	public function __construct(float $neigung, string $ausrichtung)
	{
		$this->neigung     = $neigung;
		$this->ausrichtung = $ausrichtung;
		$this->table_data  = wpenon_get_table_results('endenergie_photovoltaikanlagen');
	}

	/**
	 * qf,Prod,PV,i
	 *
	 * @return float
	 */
	public function qfProdPVi0(): float
	{ // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$column_name = $this->ausrichtung;
		return $this->table_data[$this->neigung]->$column_name;
	}
}
