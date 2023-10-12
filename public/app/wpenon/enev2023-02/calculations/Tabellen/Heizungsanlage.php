<?php

/**
 * Berechnungen für eine Heizungsanlage.
 */
class Heizungsanlage
{
    /**
     * Auslegungstemperaturen.
     * 
     * @var string
     */
    protected string $auslegungstemperaturen;


    /**
     * Welcher Teil der Anlage ist beheizt. Mögliche Werte: 'alles', 'nichts', 'verteilung' oder 'verteilung_erzeuger'.
     *
     * @var string
     */
    protected string $beheizung_anlage;

    /**
     * Konstruktor.
     * 
     * @param string $auslegungstemperaturen Auslegungstemperaturen der Heizungsanlage. Mögliche Werte: ' 90/70', '70/55', '55/45' oder '35/28'.
     * @param string $beheizung_anlage       Welcher Teil der Anlage ist beheizt. Mögliche Werte: 'alles', 'nichts', 'verteilung' oder 'verteilung_erzeuger'.
     */
    public function __construct( string $auslegungstemperaturen,  string $beheizung_anlage )
    {
        // Check der Auslegungstemperaturen.
        if ($auslegungstemperaturen !== '90/70' && $auslegungstemperaturen !== '70/55' && $auslegungstemperaturen !== '55/45' && $auslegungstemperaturen !== '35/28' ) {
            throw new Exception('Auslegungstemperaturen müssen entweder "90/70", "70/55", "55/45" oder "35/28" sein.');
        }

        // Check der Beheizung der Anlage.
        if ($beheizung_anlage !== 'alles' && $beheizung_anlage !== 'nichts' && $beheizung_anlage !== 'verteilung' && $beheizung_anlage !== 'verteilung_erzeuger' ) {
            throw new Exception('Beheizung der Anlage muss entweder "alles", "nichts", "verteilung" oder "verteilung_erzeuger" sein.');
        }

        $this->auslegungstemperaturen = $auslegungstemperaturen;
        $this->beheizung_anlage = $beheizung_anlage;
    }

    /**
     * Nutzbare Wärme.
     * 
     * @return float
     */
    public function fa_h()
    {
        // Wertzuweisungen je nach Auslegungstemperatur und Beheizung der Anlage.
        switch ($this->auslegungstemperaturen) {
            case '90/70':
                switch ($this->beheizung_anlage) {
                    case 'nichts':
                        return 0.039;
                    case 'verteilung':
                        return 0.078;
                    case 'alles':
                        return 0.123;
                    case 'verteilung_erzeuger':
                        return 0.118;
                }
            case '70/55':
                switch ($this->beheizung_anlage) {
                    case 'nichts':
                        return 0.028;
                    case 'verteilung':
                        return 0.055;
                    case 'alles':
                        return 0.099;
                    case 'verteilung_erzeuger':
                        return 0.095;
                }
            case '55/45':
                switch ($this->beheizung_anlage) {
                    case 'nichts':
                        return 0.02;
                    case 'verteilung':
                        return 0.038;
                    case 'alles':
                        return 0.082;
                    case 'verteilung_erzeuger':
                        return 0.077;
                }
            case '35/28':
                switch ($this->beheizung_anlage) {
                    case 'nichts':
                        return 0.008;
                    case 'verteilung':
                        return 0.015;
                    case 'alles':
                        return 0.057;
                    case 'verteilung_erzeuger':
                        return 0.053;
                }
        }
    }
}