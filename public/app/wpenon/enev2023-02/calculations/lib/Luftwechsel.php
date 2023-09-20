<?php

require_once __DIR__ . '/Math.php';

/**
 * Berechnungen zum Luftwechsel.
 * 
 * @package 
 */
class Luftwechsel
{
    /**
     * Building year.
     * 
     * @var float
     */
    protected float $baujahr;

    /**
     * Building volume net.
     * 
     * @var float
     */
    protected float $nettovolumen;

    /**
     * Building envelop area.
     * 
     * @var float
     */
    protected float $huellflaeche;


    /**
     * Air system.
     * 
     * @var string
     */
    protected string $lueftungssystem;

    /**
     * Air system demand based.
     * 
     * @var bool
     */
    protected bool $bedarfsgefuehrt;

    /**
     * Category of density.
     * 
     * @var bool
     */
    protected string $gebaeudedichtheit;

    /**
     * Efficiency.
     * 
     * @var float
     */
    protected float $wirkunksgrad;

    /**
     * Constructor.
     * 
     * @param float     $baujahr           Baujahr des Gebäudes.
     * @param float     $nettovolumen      Nettovoltaum des Gebäudes.
     * @param string    $lueftungssystem   Lüftungsyystemn (zu_abluft, abluft,ohne).
     * @param string    $gebaeudedichtheit Kategorie der Gebäudedichtheit (din_4108_7, ohne, andere, undichtheiten).
     * @param float|int $huellflaeche      Hüllfläche des Gebäudes.
     * @param bool      $bedarfsgefuehrt   Ist das Lüftungssystem bedarfsgeführt?
     * @param float|int $wirkunksgrad      Der Wirklungsgrad der wärmerückgewinnung (nur bei Zu- und Abluft)
     * 
     * @throws Exception 
     */
    public function __construct(
        float $baujahr,
        float $nettovolumen,
        string $lueftungssystem,
        string $gebaeudedichtheit,
        float $huellflaeche = 0,
        bool $bedarfsgefuehrt = false, 
        float $wirkunksgrad = 0 
    ) {
        $this->baujahr = $baujahr;
        $this->nettovolumen = $nettovolumen;
        $this->huellflaeche = $huellflaeche;
        $this->lueftungssystem = $lueftungssystem;
        $this->bedarfsgefuehrt = $bedarfsgefuehrt;
        $this->gebaeudedichtheit = $gebaeudedichtheit;
        $this->wirkunksgrad = $wirkunksgrad;
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
        return $this->n() * 0.34 * $this->nettovolumen;
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
        return $this->huellflaeche / $this->nettovolumen;
    }

    /**
     * Air exchange rate.
     * 
     * @return float
     */
    public function n0(): float
    {
        if($this->nettovolumen <= 1500 ) {                        
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
        $rate = $results[$this->gebaeudedichtheit]->{$column_name};

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
            $ratios[] = $results[ $this->gebaeudedichtheit . '_' . $ratio_key]->{$column_name};
        }

        $rate = interpolate_value(
            $this->av_ratio(),
            [0.2, 0.4, 0.6, 0.8],
            $ratios
        );

        return $rate;
    }


    public function ht_max(): float
    {
       
    }

    /**
     * Correction factor. 
     * 
     * @return float
     */
    public function fwin1() : float
    {
        if($this->nettovolumen <= 1500 ) {                        
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
        $correction_factor = $results[$this->gebaeudedichtheit]->{$column_name};

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
            $ratios[] = $results[ $this->gebaeudedichtheit . '_' . $ratio_key]->{$column_name};
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
        switch( $this->lueftungssystem ) {
        case 'zu_abluft':
            $column_name = 'zu_abluft';
            break;
        case 'abluft':
            $column_name = 'abluft';
            break;
        case 'ohne':
            return 'ohne';
                break;
        default:
            throw new Exception(sprintf('Invalid air system: %s.', $this->lueftungssystem));
        }

        if($this->bedarfsgefuehrt ) {
            $column_name .= '_bedarfsgefuehrt';
        } else {
            $column_name .= '_nichtbedarfsgefuehrt';
        }

        if($this->lueftungssystem === 'abluft' ) {
            return $column_name;
        }

        if($this->wirkunksgrad < 60 ) {
            $column_name .= '_ab_0';
        } elseif($this->wirkunksgrad < 80 ) {
            $column_name .= '_ab_60';
        } elseif($this->wirkunksgrad <= 100 ) {
            $column_name .= '_ab_80';
        } else {
            throw new Exception('Invalid wirkunksgrad.');
        }

        return $column_name;
    }

    /**
     * Correction factor seasonal.
     * 
     * (Temperaturkorrekturfaktor für Luftwechselrate saisonal - fwin2).
     * 
     * @param int $baujahr Building year of the building.
     * 
     * @return float 
     */
    public function fwin2() : float
    {
        if ($this->baujahr > 2002 ) {
            return 1.066;
        }

        return 0.979;
    }
    
}