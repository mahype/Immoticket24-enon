<?php

namespace Enev\Schema202401\Calculations\Tabellen;

use function Enev\Schema202401\Calculations\Helfer\interpolate_value;

class Kessel_Nennleistung {
    /**
     * Nutzfäche des Gebäudes.
     * 
     * @var float
     */
    protected float $nutzflaeche;

    /**
     * Nutzwaermebedarf für Trinkwasser in kWh.
     * 
     * @var float
     */
    protected float $nutzwaermebedarf_trinkwasser;

    /**
	 * Tabellendaten aus Tabelle 139.
	 *
	 * @var array
	 */
	protected array $table_data;

    /**
     * Konstruktor.
     * 
     * @param float $nutzflaeche Netto-Nutzfläche des Gebäudes.
     * @param float $nutzwaermebedarf_trinkwasser Nutzwärmebedarf für Trinkwasser (qwb) in kWh/(ma).
     */
    public function __construct( float $nutzflaeche, float $nutzwaermebedarf_trinkwasser )
    {
        $this->nutzflaeche = $nutzflaeche;
        $this->nutzwaermebedarf_trinkwasser = $nutzwaermebedarf_trinkwasser;
        
        $this->table_data = $this->table_data = wpenon_get_table_results( 'kessel_nennleistung' );
    }

    /**
     * Nennleistung des Kessels.
     * 
     * @return float
     */
    public function nennleistung(): float
    {
        foreach ( $this->nutzwaermebedarf_trinkwasser_slugs() as $nutzwaermebedarf_trinkwasser_slug ) {
			$keys = $values = array(); // Reset key and value arrays.

			foreach ( $this->nutzflaeche_slugs() as $nutzflaeche_slug ) {			
				$keys[]   = (int) str_replace( '_qm', '', $nutzflaeche_slug );
				$values[] = (float) $this->table_data[ $nutzflaeche_slug ]->$nutzwaermebedarf_trinkwasser_slug;
			}

            $interpolated_value = interpolate_value( $this->nutzflaeche, $keys, $values );

			$nutzwaermebedarf_trinkwasser_keys[]   = (float) str_replace( 'kwh_', '', $nutzwaermebedarf_trinkwasser_slug ) / 10;
			$nutzwaermebedarf_trinkwasser_values[] = $interpolated_value;
		}

        $interpolated_value = interpolate_value( $this->nutzwaermebedarf_trinkwasser, $nutzwaermebedarf_trinkwasser_keys, $nutzwaermebedarf_trinkwasser_values );
		return $interpolated_value;
    }


    /**
     * Nutzfläche Slugs
     * 
     * @return array
     */
    protected function nutzflaeche_slugs(): array
    {
        if ( $this->nutzflaeche <= 100 ) {
			return array( '100_qm' );
		} elseif ( $this->nutzflaeche > 100 && $this->nutzflaeche <= 150 ) {
			return array( '100_qm', '150_qm' );
		} elseif ( $this->nutzflaeche > 150 && $this->nutzflaeche <= 200 ) {
			return array( '150_qm', '200_qm' );
		} elseif ( $this->nutzflaeche > 200 && $this->nutzflaeche <= 300 ) {
			return array( '200_qm', '300_qm' );
		} elseif ( $this->nutzflaeche > 300 && $this->nutzflaeche <= 400 ) {
			return array( '300_qm', '400_qm' );
		} elseif ( $this->nutzflaeche > 400 && $this->nutzflaeche <= 500 ) {
			return array( '400_qm', '500_qm' );
		} elseif ( $this->nutzflaeche > 500 && $this->nutzflaeche <= 600 ) {
			return array( '500_qm', '600_qm' );
		} elseif ( $this->nutzflaeche > 600 && $this->nutzflaeche <= 700 ) {
			return array( '600_qm', '700_qm' );
		} elseif ( $this->nutzflaeche > 700 && $this->nutzflaeche <= 800 ) {
			return array( '700_qm', '800_qm' );
		} elseif ( $this->nutzflaeche > 800 && $this->nutzflaeche <= 900 ) {
			return array( '800_qm', '900_qm' );
		} elseif ( $this->nutzflaeche > 900 && $this->nutzflaeche <= 1000 ) {
			return array( '900_qm', '1000_qm' );
		} elseif ( $this->nutzflaeche > 1000 && $this->nutzflaeche <= 2000 ) {
			return array( '1000_qm', '2000_qm' );
		} elseif ( $this->nutzflaeche > 2000 && $this->nutzflaeche <= 3000 ) {
			return array( '2000_qm', '3000_qm' );
		} elseif ( $this->nutzflaeche > 3000 && $this->nutzflaeche <= 4000 ) {
			return array( '3000_qm', '4000_qm' );
		} elseif ( $this->nutzflaeche > 4000 && $this->nutzflaeche <= 5000 ) {
			return array( '4000_qm', '5000_qm' );
		} else {
			return array( '5000_qm' );
		}
    }

    /**
     * Nutzwaermebedarf für Trinkwasser Slugs
     * 
     * @return array 
     */
    protected function nutzwaermebedarf_trinkwasser_slugs(): array
    {
        if ( $this->nutzwaermebedarf_trinkwasser >= 16  ) {
            return array( 'kwh_160' );
        } elseif ( $this->nutzwaermebedarf_trinkwasser < 16 && $this->nutzwaermebedarf_trinkwasser >= 15.5 ) {
            return array( 'kwh_160', 'kwh_155' );
        } elseif ( $this->nutzwaermebedarf_trinkwasser < 15.5 && $this->nutzwaermebedarf_trinkwasser >= 15.0 ) {
            return array( 'kwh_155', 'kwh_150' );
        } elseif ( $this->nutzwaermebedarf_trinkwasser < 15.0 && $this->nutzwaermebedarf_trinkwasser >= 14.5 ) {
            return array( 'kwh_150', 'kwh_145' );
        } elseif ( $this->nutzwaermebedarf_trinkwasser < 14.5 && $this->nutzwaermebedarf_trinkwasser >= 14.0 ) {
            return array( 'kwh_145', 'kwh_140' );
        } elseif ( $this->nutzwaermebedarf_trinkwasser < 14.0 && $this->nutzwaermebedarf_trinkwasser >= 13.5 ) {
            return array( 'kwh_140', 'kwh_135' );
        } elseif ( $this->nutzwaermebedarf_trinkwasser < 13.5 && $this->nutzwaermebedarf_trinkwasser >= 13.0 ) {
            return array( 'kwh_135', 'kwh_130' );
        } elseif ( $this->nutzwaermebedarf_trinkwasser < 13.0 && $this->nutzwaermebedarf_trinkwasser >= 12.5 ) {
            return array( 'kwh_130', 'kwh_125' );
        } elseif ( $this->nutzwaermebedarf_trinkwasser < 12.5 && $this->nutzwaermebedarf_trinkwasser >= 12.0 ) {
            return array( 'kwh_125', 'kwh_120' );
        } elseif ( $this->nutzwaermebedarf_trinkwasser < 12.0 && $this->nutzwaermebedarf_trinkwasser >= 11.5 ) {
            return array( 'kwh_120', 'kwh_115' );
        } elseif ( $this->nutzwaermebedarf_trinkwasser < 11.5 && $this->nutzwaermebedarf_trinkwasser >= 11.0 ) {
            return array( 'kwh_115', 'kwh_110' );
        } elseif ( $this->nutzwaermebedarf_trinkwasser < 11.0 && $this->nutzwaermebedarf_trinkwasser >= 10.5 ) {
            return array( 'kwh_110', 'kwh_105' );
        } elseif ( $this->nutzwaermebedarf_trinkwasser < 10.5 && $this->nutzwaermebedarf_trinkwasser >= 10.0 ) {
            return array( 'kwh_105', 'kwh_100' );
        } elseif ( $this->nutzwaermebedarf_trinkwasser < 10.0 && $this->nutzwaermebedarf_trinkwasser >= 9.5 ) {
            return array( 'kwh_100', 'kwh_95' );
        } elseif ( $this->nutzwaermebedarf_trinkwasser < 9.5 && $this->nutzwaermebedarf_trinkwasser >= 9.0 ) {
            return array( 'kwh_95', 'kwh_90' );
        } elseif ( $this->nutzwaermebedarf_trinkwasser < 9.0 && $this->nutzwaermebedarf_trinkwasser >= 8.5 ) {
            return array( 'kwh_90', 'kwh_85' );
        } elseif ( $this->nutzwaermebedarf_trinkwasser < 8.5 ) {
            return array( 'kwh_85' );
        }
    }
}