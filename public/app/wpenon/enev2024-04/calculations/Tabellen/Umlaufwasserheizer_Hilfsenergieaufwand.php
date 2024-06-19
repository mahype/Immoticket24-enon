<?php

namespace Enev\Schema202404\Calculations\Tabellen;

use function Enev\Schema202404\Calculations\Helfer\interpolate_value;

require_once dirname(__DIR__) . '/Helfer/Math.php';

/**
 * Berechnungen zum Laufzeit der Zirkulatuionspumpe.
 *
 * @package
 */
class Umlaufwasserheizer_Hilfsenergieaufwand
{
	/**
	 * pn
	 *
	 * @var float
	 */
	protected float $pn;

	/**
	 * ßhg?
	 *
	 * @var float
	 */
	protected float $ßhg;

	/**
	 * Tabellendaten aus Tabelle 88.
	 *
	 * @var array
	 */
	protected array $table_data;

	public function __construct(float $pn, float $ßhg)
	{
		$this->pn         = $pn;
		$this->ßhg        = $ßhg;
		$this->table_data = wpenon_get_table_results('umlaufwasserheizer_hilfsenergieaufwand');
	}

	public function Phgaux(): float
	{
		$zeilen_keys   = array();
		$zeilen_values = array();

		foreach ($this->pn_slugs() as $pn_slug) {
			$spalten_keys   = array();
			$spalten_values = array();
			$zeilen_name    = 'uwh_' . $pn_slug;

			foreach ($this->ßhg_slugs() as $ßhg_slug) {
				$spalten_keys[] = $ßhg_slug;
				$spalten_teile  = explode('.', $ßhg_slug);
				if (!isset($spalten_teile[1])) {
					$spalten_teile[1] = '0';
				}

				$spalten_name     = 'bhg_' . $spalten_teile[0] . '_' . $spalten_teile[1];
				$spalten_values[] = $this->table_data[$zeilen_name]->$spalten_name;
			}

			$zeilen_keys[]       = $pn_slug;
			$interpolierter_wert = interpolate_value($this->ßhg, $spalten_keys, $spalten_values);
			$zeilen_values[]     = $interpolierter_wert;
		}

		$interpolierter_wert = interpolate_value($this->pn, $zeilen_keys, $zeilen_values);
		return $interpolierter_wert;
	}

	protected function pn_slugs(): array
	{
		if ($this->pn <= 11) {
			return array(11);
		} elseif ($this->pn > 11 && $this->pn < 18) {
			return array(11, 18);
		} elseif ($this->pn == 18) {
			return array(18);
		} elseif ($this->pn >= 18 && $this->pn <= 24) {
			return array(18, 24);
		} elseif ($this->pn > 24) {
			return array(24);
		}
	}

	protected function ßhg_slugs(): array
	{
		if ($this->ßhg <= 0.1) {
			return array(0.1);
		} elseif ($this->ßhg > 0.1 && $this->ßhg <= 0.2) {
			return array(0.1, 0.2);
		} elseif ($this->ßhg > 0.2 && $this->ßhg <= 0.3) {
			return array(0.2, 0.3);
		} elseif ($this->ßhg > 0.3 && $this->ßhg <= 0.4) {
			return array(0.3, 0.4);
		} elseif ($this->ßhg > 0.4 && $this->ßhg <= 0.5) {
			return array(0.4, 0.5);
		} elseif ($this->ßhg > 0.5 && $this->ßhg <= 0.6) {
			return array(0.5, 0.6);
		} elseif ($this->ßhg > 0.6 && $this->ßhg <= 0.7) {
			return array(0.6, 0.7);
		} elseif ($this->ßhg > 0.7 && $this->ßhg <= 0.8) {
			return array(0.7, 0.8);
		} elseif ($this->ßhg > 0.8 && $this->ßhg <= 0.9) {
			return array(0.8, 0.9);
		} elseif ($this->ßhg > 0.9 && $this->ßhg <= 1.0) {
			return array(0.9, 1.0);
		} else {
			return array(1.0);
		}
	}
}
