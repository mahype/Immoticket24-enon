<?php

require dirname( __FILE__ ) . '/DataEnev.php';

/**
 * Data Enev
 * 
 * @since 1.0.0
 */
class DataEnevBW extends DataEnev {
    /**
     * Baujahr Gebäude
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function BaujahrGebaeude() : string
    {
        $baujahre = parent::BaujahrGebaeude();
        
        if ( $this->energieausweis->anbau && ! empty( $this->energieausweis->anbau_baujahr ) ) {
            $baujahre .= ', ' . $this->energieausweis->anbau_baujahr . ' (Anbau)';
        }

        return $baujahre;
    }
}