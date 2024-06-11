<?php

namespace Enev\Schema202404\Calculations\Tabellen;

use function Enev\Schema202404\Calculations\Helfer\interpolate_value;

require_once dirname(__DIR__) . '/Helfer/Math.php';

/**
 * Berechnungen zum Immeritierender_Pumpenbetrieb.
 *
 * @package
 */
class Intermittierender_Pumpenbetrieb
{
    /**
     * ITH_RL.
     * 
     * @var float
     */
    protected float $ith_rl;

    /**
     * Th.
     * 
     * @var float
     */
    protected float $th;

    /**
     * Tabellendaten aus Tabelle 39.
     *
     * @var array
     */
    protected array $table_data;


    public function __construct(float $ith_rl, float $th)
    {
        $this->ith_rl = $ith_rl;
        $this->th = $th;
        $this->table_data = $this->table_data = wpenon_get_table_results('intermittierender_pumpenbetrieb');
    }

    public function fint(): float
    {
        $keys = $values = array(); // Reset key and value arrays.

        foreach ($this->ith_rl_th_slugs() as $ith_rl_th_slug) {
            $keys[]   = floatval($this->table_data[$ith_rl_th_slug]->th);
            $values[] = floatval($this->table_data[$ith_rl_th_slug]->f_int);
        }

        $interpolated_value = interpolate_value($this->ith_rl_th(), $keys, $values);

        return $interpolated_value;
    }

    public function ith_rl_th(): float
    {
        return $this->ith_rl / $this->th;
    }

    public function ith_rl_th_slugs(): array
    {
        $ith_rl_th = $this->ith_rl_th();

        if ($ith_rl_th <= 0.1) {
            return array('th_0_1');
        } elseif ($ith_rl_th > 0.1 && $ith_rl_th <= 0.2) {
            return array('th_0_1', 'th_0_2');
        } elseif ($ith_rl_th > 0.2 && $ith_rl_th <= 0.3) {
            return array('th_0_2', 'th_0_3');
        } elseif ($ith_rl_th > 0.3 && $ith_rl_th <= 0.4) {
            return array('th_0_3', 'th_0_4');
        } elseif ($ith_rl_th > 0.4 && $ith_rl_th <= 0.5) {
            return array('th_0_4', 'th_0_5');
        } elseif ($ith_rl_th > 0.5 && $ith_rl_th <= 0.6) {
            return array('th_0_5', 'th_0_6');
        } elseif ($ith_rl_th > 0.6 && $ith_rl_th <= 0.7) {
            return array('th_0_6', 'th_0_7');
        } elseif ($ith_rl_th > 0.7 && $ith_rl_th <= 0.8) {
            return array('th_0_7', 'th_0_8');
        } elseif ($ith_rl_th > 0.8 && $ith_rl_th <= 0.9) {
            return array('th_0_8', 'th_0_9');
        } elseif ($ith_rl_th > 0.9 && $ith_rl_th <= 1) {
            return array('th_0_9', 'th_1');
        } elseif ($ith_rl_th > 1) {
            return array('th_1_0');
        }
    }
}
