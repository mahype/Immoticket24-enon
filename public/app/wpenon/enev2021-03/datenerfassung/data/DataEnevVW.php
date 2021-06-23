<?php

require dirname( __FILE__ ) . '/DataEnev.php';
require dirname( dirname( dirname( __FILE__ ) ) ) . '/calculations/CalculationsCC.php';

/**
 * Data Enev Bedarsausweis
 * 
 * @since 1.0.0
 */
class DataEnevVW extends DataEnev {
    /**
     * Daten der Berechnungen
     * 
     * @var array
     * 
     * @since 1.0.0
     */
    private CalculationsCC $calculations;

    /**
     * Berechnungen Bedarfsausweis
     * 
     * @return mixed
     * 
     * @since 1.0.0
     */
    public function calculations() : CalculationsCC
    {
        if ( empty( $this->calculations ) )
        {
            $this->calculations = new CalculationsCC( $this->energieausweis );
        }

        return $this->calculations;
    }

    /**
     * Gebaeudenutzflaeche
     * 
     * @return float
     */
    public function Gebaeudenutzflaeche()
    {
        return $this->calculations()->getBuilding()->getUsefulArea();
    }
}