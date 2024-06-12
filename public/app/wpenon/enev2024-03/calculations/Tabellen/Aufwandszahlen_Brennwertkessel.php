<?php

namespace Enev\Schema202403\Calculations\Tabellen;

use function Enev\Schema202403\Calculations\Helfer\interpolate_value;

require_once dirname(__DIR__) . '/Helfer/Math.php';

/**
 * Berechnung der Daten für die Aufwandszahlen von Brennwertkesseln aus Tablle 77.
 *
 * @package
 */
class Aufwandszahlen_Brennwertkessel
{
	/**
	 * Zielwert für die Spalte.
	 *
	 * @var string
	 */
	protected string $zeile_zielwert;

	/**
	 * Zielwert für die Zeile.
	 *
	 * @var float
	 */
	protected float $ßhg;

	/**
	 * Tabellendaten aus Tabelle 77.
	 *
	 * @var array
	 */
	protected array $table_data;

	/**
	 * Konstruktor.
	 *
	 * @param string $pn Pn.
	 * @param float  $ßhg ßhg.
	 *
	 * @return void
	 */
	public function __construct(float $pn, float $ßhg)
	{
		$this->zeile_zielwert = $pn;
		$this->ßhg            = $ßhg;
		$this->table_data     = wpenon_get_table_results('aufwandszahlen_brennwertkessel');
	}

	protected function interpolierter_wert(): float
	{
		$zeilen_keys   = array();
		$zeilen_values = array();

		foreach ($this->zeilen() as $zeile) {
			$spalten_keys   = array();
			$spalten_values = array();

			foreach ($this->spalten() as $spalte) {
				$spalten_keys[]   = $spalte;
				$spalten_teile    = explode('.', $spalte);

				if (!isset($spalten_teile[1])) {
					$spalten_teile[1] = 0;
				}

				$spalten_name     = 'bwk_' . $spalten_teile[0] . '_' . $spalten_teile[1];
				$spalten_values[] = $this->table_data[$zeile]->$spalten_name;
			}

			$zeilen_keys[]       = $zeile;
			$interpolierter_wert = interpolate_value($this->ßhg, $spalten_keys, $spalten_values);
			$zeilen_values[]     = $interpolierter_wert;
		}

		$interpolierter_wert = interpolate_value($this->zeile_zielwert, $zeilen_keys, $zeilen_values);
		return $interpolierter_wert;
	}

	public function eg0(): float
	{
		return $this->interpolierter_wert();
	}

	public function ewg0(): float
	{
		return $this->interpolierter_wert();
	}

	protected function zeilen(): array
	{
		if ($this->zeile_zielwert <= 5) {
			return array(5);
		} elseif ($this->zeile_zielwert > 5 && $this->zeile_zielwert <= 10) {
			return array(5, 10);
		} elseif ($this->zeile_zielwert > 10 && $this->zeile_zielwert <= 20) {
			return array(10, 20);
		} elseif ($this->zeile_zielwert > 20 && $this->zeile_zielwert <= 30) {
			return array(20, 30);
		} elseif ($this->zeile_zielwert > 30 && $this->zeile_zielwert <= 40) {
			return array(30, 40);
		} elseif ($this->zeile_zielwert > 40 && $this->zeile_zielwert <= 50) {
			return array(40, 50);
		} elseif ($this->zeile_zielwert > 50 && $this->zeile_zielwert <= 60) {
			return array(50, 60);
		} elseif ($this->zeile_zielwert > 60 && $this->zeile_zielwert <= 70) {
			return array(60, 70);
		} elseif ($this->zeile_zielwert > 70 && $this->zeile_zielwert <= 80) {
			return array(70, 80);
		} elseif ($this->zeile_zielwert > 80 && $this->zeile_zielwert <= 90) {
			return array(80, 90);
		} elseif ($this->zeile_zielwert > 90 && $this->zeile_zielwert <= 100) {
			return array(90, 100);
		} elseif ($this->zeile_zielwert > 100 && $this->zeile_zielwert <= 120) {
			return array(100, 120);
		} elseif ($this->zeile_zielwert > 120 && $this->zeile_zielwert <= 140) {
			return array(120, 140);
		} elseif ($this->zeile_zielwert > 140 && $this->zeile_zielwert <= 160) {
			return array(140, 160);
		} elseif ($this->zeile_zielwert > 160 && $this->zeile_zielwert <= 180) {
			return array(160, 180);
		} elseif ($this->zeile_zielwert > 180 && $this->zeile_zielwert <= 200) {
			return array(180, 200);
		} elseif ($this->zeile_zielwert > 200 && $this->zeile_zielwert <= 250) {
			return array(200, 250);
		} elseif ($this->zeile_zielwert > 250 && $this->zeile_zielwert <= 300) {
			return array(250, 300);
		} elseif ($this->zeile_zielwert > 300 && $this->zeile_zielwert <= 350) {
			return array(300, 350);
		} elseif ($this->zeile_zielwert > 350 && $this->zeile_zielwert <= 400) {
			return array(350, 400);
		} else {
			return array(400);
		}
	}

	protected function spalten(): array
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
