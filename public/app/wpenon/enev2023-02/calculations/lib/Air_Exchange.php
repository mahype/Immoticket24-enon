<?php

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

    public function rate()
    {
        if($this->building_volume_net <= 1500 ) {                        
            $rate = $this->rate_small_buildings() * ( 1 - $this->correction_factor_small_buildings() + $this->correction_factor_small_buildings() * $this->correction_factor_seasonal() );
        } else {
            
        }

        return $rate;
    }

    public function volume()
    {
        return $this->rate() * 0.34 * $this->building_volume_net;
    }

    protected function rate_small_buildings()
    {
        $column_name = $this->column_name();

        $results = wpenon_get_table_results('l_luftwechsel_klein');        
        $rate = $results[$this->density_category]->{$column_name};

        return $rate;
    }

    protected function correction_factor_small_buildings()
    {
        $column_name = $this->column_name();

        $results = wpenon_get_table_results('l_luftwechsel_korrekturfaktor_klein');
        $correction_factor = $results[$this->density_category]->{$column_name};

        return $correction_factor;
    }

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
    protected function correction_factor_seasonal() : float
    {
        if ($this->building_year > 2002 ) {
            return 1.066;
        }

        return 0.979;
    }
    
}

// $tableNames->l_luftwechsel_klein                 = 'l_luftwechsel_klein';
// $tableNames->l_luftwechsel_korrekturfaktor_klein = 'l_luftwechsel_korrekturfaktor_klein';
// $tableNames->l_luftwechsel_gross                 = 'l_luftwechsel_gross';
// $tableNames->l_luftwechsel_korrekturfaktor_gross = 'l_luftwechsel_korrekturfaktor_gross';

/**
 * Calculations for Heat transfer coefficient due to ventilation.
 * 
 * We distinguish only between houses with blower door tests and without blower door tests. 
 * New buildings (without leakage test) are not taken into account, because we cannot offer them online. 
 */

/**
 * Wärmetransferkoeffizient infolge Lüftung.
 * 
 * (Heat transfer coefficient due to ventilation - 𝜙𝑣).
 * 
 * @param float $air_exchange_rate   Air exchange rate.
 * @param float $building_volume_net Building volume net.
 * 
 * @return float
 */
function heat_transfer_coefficient_ventilation(
    float $building_year,
    float $building_volume_net,
    string $air_system, 
    bool $blower_door_test,
    float $building_envelop_area = 0,
    bool $air_system_demand_based = false, 
    float $efficiency = 0 
) {
    $air_exchange_rate = air_exchange_rate( 
        $building_year,
        $building_volume_net,
        $air_system, 
        $blower_door_test,
        $building_envelop_area,
        $air_system_demand_based, 
        $efficiency
    );

    return $air_exchange_rate * 0.34 * $building_volume_net;
}

/**
 * Air exchange rate.
 * 
 * (Luftwechselrate - 𝑛0). 
 * 
 * @param  float     $building_volume_net 
 * @param  string    $air_system 
 * @param  bool      $blower_door_test 
 * @param  float|int $building_envelop_area 
 * @param  bool      $air_system_demand_based 
 * @param  float|int $efficiency 
 * @return float 
 * @throws Exception 
 */
function air_exchange_rate(
    int $building_year, 
    float $building_volume_net,
    string $air_system, 
    bool $blower_door_test,   
    float $building_envelop_area = 0,
    bool $air_system_demand_based = false, 
    float $efficiency = 0
): float {
    if($building_volume_net <= 1500 ) {
        $air_exchange_rate = air_exchange_rate_small_buildings(
            $air_system, 
            $blower_door_test, 
            $air_system_demand_based, 
            $efficiency
        );

        $air_exchange_rate_correction_factor = air_exchange_rate_correction_factor_small_buildings(
            $air_system, 
            $blower_door_test, 
            $air_system_demand_based, 
            $efficiency
        );
    } else {
        if($building_envelop_area == 0 ) {
            throw new Exception('Building envelop area is required for large buildings.');
        }
    
        $air_exchange_rate = air_exchange_rate_large_buildings(
            $air_system, 
            $blower_door_test, 
            $building_envelop_area, 
            $building_volume_net, 
            $air_system_demand_based, 
            $efficiency
        );

        $air_exchange_rate_correction_factor = air_exchange_rate_correction_factor_large_buildings(
            $air_system, 
            $blower_door_test, 
            $building_envelop_area, 
            $building_volume_net, 
            $air_system_demand_based, 
            $efficiency
        );
    }

    $air_exchange_rate_correction_factor_seasonal = air_exchange_rate_correction_factor_seasonal($building_year);

    return $air_exchange_rate * ( 1 - $air_exchange_rate_correction_factor + $air_exchange_rate_correction_factor * $air_exchange_rate_correction_factor_seasonal );
}

/**
 * Envelop volume ratio.
 * 
 * @param float $building_envelop_area Building envelop area.
 * @param float $building_volume_net   Building volume net.
 * 
 * @return float
 */
function envelop_volume_ratio( float $building_envelop_area, float $building_volume_net ) : float
{
    return $building_envelop_area / $building_volume_net;
}

/**
 * Air exchange rate for small buildings (up to 1500m³).
 * 
 * (Luftwechselrate bei kleinen Gebäuden bis 1500m³ - 𝑛0).
 * 
 * Din V 18599-12 Tab. 12
 * 
 * @param int   $air_system              Type of air system. Allowed values are 'none', 'intake_and_exhaust' and 'exhaust'.
 * @param bool  $blower_door_test        Whether a blower door test was carried out.
 * @param bool  $air_system_demand_based Whether the air system is demand based
 *                                       (Bedarfsgeführt). 
 * @param float $efficiency              The efficiency of the air system (only relevant for 'intake_and_exhaust' air systems).
 * 
 * @return float
 */
function air_exchange_rate_small_buildings( string $air_system, bool $blower_door_test, bool $air_system_demand_based = false, float $efficiency = 0 ) : float
{
    switch ( $air_system ) {            
    case 'intake_and_exhaust':
        if($efficiency < 60 ) {
            if($blower_door_test ) {
                if($air_system_demand_based ) {
                    return 0.52;
                }
                return 0.57;
            }
        
            if($air_system_demand_based ) {
                return 0.87;
            }
        
            return 0.92;
        } elseif($efficiency < 80 ) {
            if($blower_door_test ) {
                if($air_system_demand_based ) {
                    return 0.31;
                }
                return 0.33;
            }
        
            if($air_system_demand_based ) {
                return 0.66;
            }
        
            return 0.68;
        } elseif ($efficiency <= 100 ) {
            if($blower_door_test ) {
                if($air_system_demand_based ) {
                    return 0.24;
                }
                return 0.25;
            }
        
            if($air_system_demand_based ) {
                return 0.59;
            }
        
            return 0.60;
        } else {
            throw new Exception('Invalid efficiency.');
        }
    case 'exhaust':
        if($blower_door_test ) {
            if($air_system_demand_based ) {
                return 0.48;
            }
            return 0.52;
        }

        if($air_system_demand_based ) {
            return 0.72;
        }

        return 0.73;
    case 'none':
        if($blower_door_test ) {
            return 0.6;
        } 
        return 0.79;
    default:
        throw new Exception(sprintf('Invalid air system. %s', $air_system));
    }
}

/**
 * Correction factor for air exchange rate for small buildings (up to 1500m³).
 * 
 * (Temperaturkorrekturfaktor für Luftwechselrate bei kleinen Gebäuden bis 1500m³ - fwin1).
 * 
 * Din V 18599-12 Tab. 13
 * 
 * @param int   $air_system              Type of air system. Allowed values are 'none', 'intake_and_exhaust' and 'exhaust'.
 * @param bool  $blower_door_test        Whether a blower door test was carried out.
 * @param bool  $air_system_demand_based Whether the air system is demand based (Bedarfsgeführt). 
 * @param float $efficiency              The efficiency of the air system (only relevant for 'intake_and_exhaust' air systems).
 * 
 * @return float
 */
function air_exchange_rate_correction_factor_small_buildings( string $air_system, bool $blower_door_test, bool $air_system_demand_based = false, float $efficiency = 0 ) : float
{
    switch ( $air_system ) {            
    case 'intake_and_exhaust':
        if($efficiency < 60 ) {
            if($blower_door_test ) {
                if($air_system_demand_based ) {
                    return 0.192;
                }
                return 0.175;
            }
        
            if($air_system_demand_based ) {
                return 0.115;
            }
        
            return 0.109;
        } elseif($efficiency < 80 ) {
            if($blower_door_test ) {
                if($air_system_demand_based ) {
                    return 0.323;
                }
                return 0.303;
            }
        
            if($air_system_demand_based ) {
                return 0.152;
            }
        
            return 0.147;
        } elseif ($efficiency <= 100 ) {
            if($blower_door_test ) {
                if($air_system_demand_based ) {
                    return 0.417;
                }
                return 0.4;
            }
        
            if($air_system_demand_based ) {
                return 0.169;
            }
        
            return 0.167;
        } else {
            throw new Exception('Invalid efficiency.');
        }
    case 'exhaust':
        if($blower_door_test ) {
            if($air_system_demand_based ) {
                return 0.21;
            }
            return 0.193;
        }
 
        if($air_system_demand_based ) {
            return 0.139;
        }

        return 0.137;
    case 'none':
        if($blower_door_test ) {
            return 0.766;
        } 
        return 0.471;
    default:
        throw new Exception('Invalid air system.');
    }
}

/**
 * Air exchange rate for large buildings (larger than 1500m³).
 * 
 * (Luftwechselrate bei großen Gebäuden größer 1500m³ - 𝑛0
 * 
 * Din V 18599-12 Tab. 14
 * 
 * @param int   $air_system              Type of air system. Allowed values are 'none', 'intake_and_exhaust' and 'exhaust'.
 * @param bool  $blower_door_test        Whether a blower door test was carried out.
 * @param float $building_envelop_area   Building envelop area.
 * @param float $building_volume_net     Building volume net. 
 * @param bool  $air_system_demand_based Whether the air system is demand based (Bedarfsgeführt).
 * @param float $efficiency              The efficiency of the air system (only relevant for 'intake_and_exhaust' air systems).
 * 
 * @return float
 */
function air_exchange_rate_large_buildings(string $air_system, bool $blower_door_test, float $building_envelop_area, float $building_volume_net, bool $air_system_demand_based = false, float $efficiency = 0) : float
{
    $envelop_volume_ratio = envelop_volume_ratio($building_envelop_area, $building_volume_net);

    switch ( $air_system ) {            
    case 'intake_and_exhaust':
        if($efficiency < 60 ) {
            if($blower_door_test ) {
                if($air_system_demand_based ) {
                    return interpolate_value(
                        $envelop_volume_ratio,
                        [0.2, 0.4, 0.6, 0.8],
                        [0.48, 0.51, 0.53, 0.56]
                    );
                }
                return interpolate_value(
                    $envelop_volume_ratio,
                    [0.2, 0.4, 0.6, 0.8],
                    [0.53, 0.56, 0.58, 0.61]
                );
            }
            
            if($air_system_demand_based ) {
                return interpolate_value(
                    $envelop_volume_ratio,
                    [0.2, 0.4, 0.6, 0.8],
                    [0.58, 0.7, 0.83, 0.95]
                );
            }
            
            return interpolate_value(
                $envelop_volume_ratio,
                [0.2, 0.4, 0.6, 0.8],
                [0.63, 0.75, 0.88, 1.0]
            );
        } elseif($efficiency < 80 ) {
            if($blower_door_test ) {
                if($air_system_demand_based ) {
                    return interpolate_value(
                        $envelop_volume_ratio,
                        [0.2, 0.4, 0.6, 0.8],
                        [0.27, 0.3, 0.32, 0.35]
                    );
                }

                return interpolate_value(
                    $envelop_volume_ratio,
                    [0.2, 0.4, 0.6, 0.8],
                    [0.29, 0.32, 0.34, 0.37]
                );
            }
            
            if($air_system_demand_based ) {
                return interpolate_value(
                    $envelop_volume_ratio,
                    [0.2, 0.4, 0.6, 0.8],
                    [0.37, 0.49, 0.62, 0.74]
                );
            }
            
            return interpolate_value(
                $envelop_volume_ratio,
                [0.2, 0.4, 0.6, 0.8],
                [0.39, 0.51, 0.64, 0.76]
            );
        } elseif ($efficiency <= 100 ) {
            if($blower_door_test ) {
                if($air_system_demand_based ) {
                    return interpolate_value(
                        $envelop_volume_ratio,
                        [0.2, 0.4, 0.6, 0.8],
                        [0.2, 0.23, 0.25, 0.28]
                    );
                }

                return interpolate_value(
                    $envelop_volume_ratio,
                    [0.2, 0.4, 0.6, 0.8],
                    [0.21, 0.24, 0.26, 0.29]
                );
            }
            
            if($air_system_demand_based ) {
                return interpolate_value(
                    $envelop_volume_ratio,
                    [0.2, 0.4, 0.6, 0.8],
                    [0.3, 0.42, 0.55, 0.67]
                );
            }
            
            return interpolate_value(
                $envelop_volume_ratio,
                [0.2, 0.4, 0.6, 0.8],
                [0.31, 0.43, 0.56, 0.68]
            );
        } else {
            throw new Exception(sprintf('Invalid efficiency. %s', $efficiency));
        }
    case 'exhaust':
        if($blower_door_test ) {
            if($air_system_demand_based ) {
                return interpolate_value(
                    $envelop_volume_ratio,
                    [0.2, 0.4, 0.6, 0.8],
                    [0.46, 0.47, 0.48, 0.49]
                );
            }

            return interpolate_value(
                $envelop_volume_ratio,
                [0.2, 0.4, 0.6, 0.8],
                [0.51, 0.52, 0.52, 0.53]
            );
        }
    
        if($air_system_demand_based ) {
            return interpolate_value(
                $envelop_volume_ratio,
                [0.2, 0.4, 0.6, 0.8],
                [0.5, 0.58, 0.68, 0.79]
            );
        }
    
        return interpolate_value(
            $envelop_volume_ratio,
            [0.2, 0.4, 0.6, 0.8],
            [0.54, 0.61, 0.7, 0.79]
        );
    case 'none':
        if($blower_door_test ) {
            return interpolate_value(
                $envelop_volume_ratio,
                [0.2, 0.4, 0.6, 0.8],
                [0.53, 0.56, 0.59, 0.62]
            );
        } 
        return interpolate_value(
            $envelop_volume_ratio,
            [0.2, 0.4, 0.6, 0.8],
            [0.59, 0.67, 0.77, 0.85]
        );
    default:
        throw new Exception('Invalid air system.');
    }
}

/**
 * Air exchange rate correction for large buildings (larger than 1500m³).
 * 
 * (Luftwechselrate korrekturfaktoren bei Gebäuden größer 1500m³ - 𝑛0).
 * 
 * Din V 18599-12 Tab. 15
 * 
 * @param int   $air_system              Type of air system. Allowed values are 'none', 'intake_and_exhaust' and 'exhaust'.
 * @param bool  $blower_door_test        Whether a blower door test was carried out.
 * @param float $building_envelop_area   Building envelop area.
 * @param float $building_volume_net     Building volume net. 
 * @param bool  $air_system_demand_based Whether the air system is demand based (Bedarfsgeführt).
 * @param float $efficiency              The efficiency of the air system (only relevant for 'intake_and_exhaust' air systems).
 * 
 * @return float
 */
function air_exchange_rate_correction_factor_large_buildings(string $air_system, bool $blower_door_test, float $building_envelop_area, float $building_volume_net, bool $air_system_demand_based = false, float $efficiency = 0) : float
{
    $envelop_volume_ratio = envelop_volume_ratio($building_envelop_area, $building_volume_net);

    switch ( $air_system ) {            
    case 'intake_and_exhaust':
        if($efficiency < 60 ) {
            if($blower_door_test ) {
                if($air_system_demand_based ) {
                    return interpolate_value(
                        $envelop_volume_ratio,
                        [0.2, 0.4, 0.6, 0.8],
                        [0.209, 0.198, 0.187, 0.178]
                    );
                }
                return interpolate_value(
                    $envelop_volume_ratio,
                    [0.2, 0.4, 0.6, 0.8],
                    [0.189, 0.180, 0.171, 0.163]
                );
            }
            
            if($air_system_demand_based ) {
                return interpolate_value(
                    $envelop_volume_ratio,
                    [0.2, 0.4, 0.6, 0.8],
                    [0.174, 0.142, 0.121, 0.105]
                );
            }
            
            return interpolate_value(
                $envelop_volume_ratio,
                [0.2, 0.4, 0.6, 0.8],
                [0.160, 0.133, 0.114, 0.1]
            );
        } elseif($efficiency < 80 ) {
            if($blower_door_test ) {
                if($air_system_demand_based ) {
                    return interpolate_value(
                        $envelop_volume_ratio,
                        [0.2, 0.4, 0.6, 0.8],
                        [0.373, 0.338, 0.309, 0.284]
                    );
                }

                return interpolate_value(
                    $envelop_volume_ratio,
                    [0.2, 0.4, 0.6, 0.8],
                    [0.347, 0.316, 0.291, 0.269]
                );
            }
            
            if($air_system_demand_based ) {
                return interpolate_value(
                    $envelop_volume_ratio,
                    [0.2, 0.4, 0.6, 0.8],
                    [0.273, 0.203, 0.162, 0.134]
                );
            }
            
            return interpolate_value(
                $envelop_volume_ratio,
                [0.2, 0.4, 0.6, 0.8],
                [0.259, 0.195, 0.157, 0.131]
            );
        } elseif ($efficiency <= 100 ) {
            if($blower_door_test ) {
                if($air_system_demand_based ) {
                    return interpolate_value(
                        $envelop_volume_ratio,
                        [0.2, 0.4, 0.6, 0.8],
                        [0.505, 0.442, 0.394, 0.355]
                    );
                }

                return interpolate_value(
                    $envelop_volume_ratio,
                    [0.2, 0.4, 0.6, 0.8],
                    [0.481, 0.424, 0.379, 0.342]
                );
            }
            
            if($air_system_demand_based ) {
                return interpolate_value(
                    $envelop_volume_ratio,
                    [0.2, 0.4, 0.6, 0.8],
                    [0.338, 0.237, 0.182, 0.148]
                );
            }
            
            return interpolate_value(
                $envelop_volume_ratio,
                [0.2, 0.4, 0.6, 0.8],
                [0.327, 0.231, 0.179, 0.146]
            );
        } else {
            throw new Exception('Invalid efficiency.');
        }
    case 'exhaust':
        if($blower_door_test ) {
            if($air_system_demand_based ) {
                return interpolate_value(
                    $envelop_volume_ratio,
                    [0.2, 0.4, 0.6, 0.8],
                    [0.216, 0.213, 0.208, 0.203]
                );
            }

            return interpolate_value(
                $envelop_volume_ratio,
                [0.2, 0.4, 0.6, 0.8],
                [0.197, 0.194, 0.191, 0.188]
            );
        }
    
        if($air_system_demand_based ) {
            return interpolate_value(
                $envelop_volume_ratio,
                [0.2, 0.4, 0.6, 0.8],
                [0.2, 0.171, 0.146, 0.127]
            );
        }
    
        return interpolate_value(
            $envelop_volume_ratio,
            [0.2, 0.4, 0.6, 0.8],
            [0.186, 0.165, 0.144, 0.126]
        );
    case 'none':
        if($blower_door_test ) {
            return interpolate_value(
                $envelop_volume_ratio,
                [0.2, 0.4, 0.6, 0.8],
                [0.921, 0.850, 0.786, 0.728]
            );
        } 
        return interpolate_value(
            $envelop_volume_ratio,
            [0.2, 0.4, 0.6, 0.8],
            [0.786, 0.627, 0.506, 0.409]
        );
    default:
        throw new Exception('Invalid air system.');
    }
}

/**
 * Air exchange rate correction factor seasonal.
 * 
 * (Temperaturkorrekturfaktor für Luftwechselrate saisonal - fwin2).
 * 
 * @param int $building_year Building year of the building.
 * 
 * @return float 
 */
function air_exchange_rate_correction_factor_seasonal( int $building_year ) : float
{
    if ($building_year > 2002 ) {
        return 1.066;
    }

    return 0.979;
}

/**
 * Interpolation of values.
 * 
 * @param float $target_value 
 * @param array $keys 
 * @param array $values 
 * 
 * @return float 
 */
function interpolate_value( float $target_value, array $keys, array $values ) : float
{
    $index = 0;

    foreach($keys as $key) {
        if($target_value < $key) {
            break;
        }
        $index++;
    }

    if($index == 0) {
        return $values[0];
    }

    if($index == count($keys)) {
        return $values[count($keys) - 1];
    }

    $x1 = $keys[$index - 1];
    $x2 = $keys[$index];
    $y1 = $values[$index - 1];
    $y2 = $values[$index];

    return $y1 + ($target_value - $x1) * ($y2 - $y1) / ($x2 - $x1);
}