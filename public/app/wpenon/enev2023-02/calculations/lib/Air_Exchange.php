<?php

require_once __DIR__ . '/Math.php';

/**
 * Class for calculating the air exchange rate.
 * 
 * @package 
 */
class Air_Exchange
{
    /**
     * Building year.
     * 
     * @var float
     */
    protected float $building_year;

    /**
     * Building volume net.
     * 
     * @var float
     */
    protected float $building_volume_net;

    /**
     * Building envelop area.
     * 
     * @var float
     */
    protected float $building_envelop_area;


    /**
     * Air system.
     * 
     * @var string
     */
    protected string $air_system;

    /**
     * Air system demand based.
     * 
     * @var bool
     */
    protected bool $air_system_demand_based;

    /**
     * Category of density.
     * 
     * @var bool
     */
    protected string $density_category;

    /**
     * Efficiency.
     * 
     * @var float
     */
    protected float $efficiency;

    /**
     * Constructor.
     * 
     * @param float     $building_year           Building year.
     * @param float     $building_volume_net     Building volume net.
     * @param string    $air_system              Air system (none, intake_and_exhaust, exhaust).
     * @param string    $density_category        Category of density (din_4108_7, ohne, andere, undichtheiten).
     * @param float|int $building_envelop_area   Building envelop area.
     * @param bool      $air_system_demand_based Whether the air system is demand based (Bedarfsgeführt).
     * @param float|int $efficiency              The efficiency of the air system (only relevant for 'intake_and_exhaust' air systems).
     * 
     * @throws Exception 
     */
    public function __construct(
        float $building_year,
        float $building_volume_net,
        string $air_system,
        string $density_category,
        float $building_envelop_area = 0,
        bool $air_system_demand_based = false, 
        float $efficiency = 0 
    ) {
        $this->building_year = $building_year;
        $this->building_volume_net = $building_volume_net;
        $this->building_envelop_area = $building_envelop_area;
        $this->air_system = $air_system;
        $this->air_system_demand_based = $air_system_demand_based;
        $this->density_category = $density_category;
        $this->efficiency = $efficiency;
    }

     /**
      * Air exchange volumen (Hv ges = 𝑛 × 𝑐 × 𝑝 × 𝑉).
      * 
      * @return float
      *  
      * @throws Exception 
      */
    public function hv()
    {
        return $this->n() * 0.34 * $this->building_volume_net;
    }

    /**
     * Air exchange rate (Hv).
     * 
     * @return float
     *  
     * @throws Exception 
     */
    public function n()
    {
        return $this->n0() * ( 1 - $this->fwin1() + $this->fwin1() * $this->fwin2() );
    }

    /**
     * Envelope volume ratio.
     * 
     * @return float 
     */
    public function av_ratio(): float
    {
        return $this->building_envelop_area / $this->building_volume_net;
    }

    /**
     * Air exchange rate.
     * 
     * @return float
     */
    public function n0(): float
    {
        if($this->building_volume_net <= 1500 ) {                        
            return $this->n0_small_buildings();
        } else {
            return $this->n0_large_buildings();
        }
    }

    /**
     * Air exchange rate for small buildings (up to 1500m³).
     * 
     * @return float
     */
    protected function n0_small_buildings(): float
    {
        $column_name = $this->column_name();

        $results = wpenon_get_table_results('l_luftwechsel_klein');
        $rate = $results[$this->density_category]->{$column_name};

        return $rate;
    }

    /**
     * Air exchange rate for large buildings (larger than 1500m³).
     * 
     * @return float 
     */
    protected function n0_large_buildings() : float
    {
        $column_name = $this->column_name();

        $results = wpenon_get_table_results('l_luftwechsel_gross');

        $ratio_keys = ['02','04', '06', '08'];
        $ratios = [];

        foreach($ratio_keys as $ratio_key) {
            $ratios[] = $results[ $this->density_category . '_' . $ratio_key]->{$column_name};
        }

        $rate = interpolate_value(
            $this->av_ratio(),
            [0.2, 0.4, 0.6, 0.8],
            $ratios
        );

        return $rate;
    }

    /**
     * Correction factor. 
     * 
     * @return float
     */
    public function fwin1() : float
    {
        if($this->building_volume_net <= 1500 ) {                        
            return $this->fwin1_small_buildings();
        } else {
            return $this->fwin1_large_buildings();
        }
    }

    /**
     * Correction factor for air exchange rate for small buildings (up to 1500m³).
     * 
     * @throws Exception 
     */
    protected function fwin1_small_buildings() : float
    {
        $column_name = $this->column_name();

        $results = wpenon_get_table_results('l_luftwechsel_korrekturfaktor_klein');
        $correction_factor = $results[$this->density_category]->{$column_name};

        return $correction_factor;
    }

    /**
     * Correction factor for air exchange rate for large buildings (larger than 1500m³).
     * 
     * @return float 
     */
    protected function fwin1_large_buildings()
    {
        $column_name = $this->column_name();

        $results = wpenon_get_table_results('l_luftwechsel_korrekturfaktor_gross');
        
        $ratio_keys = ['02','04', '06', '08'];
        $ratios = [];

        foreach($ratio_keys as $ratio_key) {
            $ratios[] = $results[ $this->density_category . '_' . $ratio_key]->{$column_name};
        }

        $factor = interpolate_value(
            $this->av_ratio(),
            [0.2, 0.4, 0.6, 0.8],
            $ratios
        );

        return $factor;
    }

    /**
     * Get column name for the given air system.
     * 
     * @return string 
     * @throws Exception 
     */
    protected function column_name()
    {
        switch( $this->air_system ) {
        case 'intake_and_exhaust':
            $column_name = 'zu_abluft';
            break;
        case 'exhaust':
            $column_name = 'abluft';
            break;
        case 'none':
            return 'ohne';
                break;
        default:
            throw new Exception(sprintf('Invalid air system: %s.', $this->air_system));
        }

        if($this->air_system_demand_based ) {
            $column_name .= '_bedarfsgefuehrt';
        } else {
            $column_name .= '_nichtbedarfsgefuehrt';
        }

        if($this->air_system === 'exhaust' ) {
            return $column_name;
        }

        if($this->efficiency < 60 ) {
            $column_name .= '_ab_0';
        } elseif($this->efficiency < 80 ) {
            $column_name .= '_ab_60';
        } elseif($this->efficiency <= 100 ) {
            $column_name .= '_ab_80';
        } else {
            throw new Exception('Invalid efficiency.');
        }

        return $column_name;
    }

    /**
     * Correction factor seasonal.
     * 
     * (Temperaturkorrekturfaktor für Luftwechselrate saisonal - fwin2).
     * 
     * @param int $building_year Building year of the building.
     * 
     * @return float 
     */
    public function fwin2() : float
    {
        if ($this->building_year > 2002 ) {
            return 1.066;
        }

        return 0.979;
    }
    
}