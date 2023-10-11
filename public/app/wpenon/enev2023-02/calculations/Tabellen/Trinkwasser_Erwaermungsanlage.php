<?php

/**
 * Bestimmung des Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen
 * über Tabelle 142 & 143 Abschnitt 12.
 */
class Trinkwasser_Erwaermungsanlage
{
    /**
     * Handelt sich um eine zentrale Wasserversorgung (true) oder um eine dezentrale (false)?
     * 
     * @var bool
     */
    protected bool $zentrale_wasserversorgung;

    /**
     * Welcher Teil der Anlage ist beheizt. Mögliche Werte: 'alles', 'nichts' oder 'verteilung'.
     *
     * @var string
     */
    protected string $beheizung_anlage;

    /**
     * Liegt eine Warmwasserspeicher vor?
     * 
     * @var bool $warmwasserspeicher
     */
    protected bool $warmwasserspeicher;

    /**
     *
     * 
     * @var bool $zirkulation
     */
    protected bool $zirkulation;

    /**
     * Liegt eine Warmwasserspeicher vor 
     * 
     * @param bool $zentrale_wasserversorgung Handelt sich um eine zentrale Wasserversorgung (true) oder um eine dezentrale (false)?
     * @param bool $beheizung_anlage          Welcher Teil der Anlage ist beheizt. Mögliche Werte: 'alles', 'nichts' oder 'verteilung'.
     * @param bool $warmwasserspeicher        Liegt eine Warmwasserspeicher vor?
     * @param bool $zirkulation               Trinkwasserverteilung mit Zirkulation (true) oder ohne (false).
     */
    public function __construct( 
        bool $zentrale_wasserversorgung,
        bool $beheizung_anlage = 'alles',
        bool $warmwasserspeicher = false,
        bool $zirkulation = false        
    ) {
        // Beheizung der Anlage überprüfen und wenn falsch angegeben, Fehler werfen.
        if ($beheizung_anlage !== 'alles' && $beheizung_anlage !== 'nichts' && $beheizung_anlage !== 'verteilung' ) {
            throw new Exception('Beheizung der Anlage muss entweder "alles", "nichts" oder "verteilung" sein.');
        }
        
        if($zirkulation && !$zentrale_wasserversorgung) {
            throw new Exception('Zirkulation ist nur bei zentraler Wasserversorgung möglich.');
        }
            
        $this->zentrale_wasserversorgung = $zentrale_wasserversorgung;
        $this->beheizung_anlage = $beheizung_anlage;
        $this->warmwasserspeicher = $warmwasserspeicher;
        $this->zirkulation = $zirkulation;
    }

    /**
     * Bestimmung des Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen.
     * 
     * @return float
     */
    public function f(): float
    {
        // There is 
        if(! $this->zentrale_wasserversorgung ) {
            return 0.193;
        }

        if(! $this->warmwasserspeicher ) {
            return $this->f_ohne_warmwasserspeicher();
        }

        return $this->f_mit_warmwasserspeicher();
    }

    /**
     * Bestimmung des Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen mit Warmwasserspeicher.
     * 
     * @return float 
     */
    protected function f_mit_warmwasserspeicher(): float
    {
        // Werte aus Tabelle 142 & 143 nach den drei
        // Möglichkeiten der Beheizung der Anlage aufgeteilt,
        // je nachdem ob mit oder ohne Zirkulation.
        switch( $this->beheizung_anlage ) {
        case 'alles':
            return $this->zirkulation ? 1.554 : 0.647;
        case 'nichts':
            return $this->zirkulation ? 0.815 : 0.335;
        case 'verteilung':
            return $this->zirkulation ? 1.321 : 0.451;            
        }
    }

    /**
     * Bestimmung des Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen ohne Warmwasserspeicher.
     * 
     * @return float 
     */
    protected function f_ohne_warmwasserspeicher(): float {
        // Werte aus Tabelle 142 & 143 ohne Warmwasserspeicher
        // je nachdem ob mit oder ohne Zirkulation.
        // Es wird der schlechtere Wert der beidem beheizten Varianten genommen.
        if($this->zirkulation) {
            return 1.321;
        }

        return 0.451;
    }
}