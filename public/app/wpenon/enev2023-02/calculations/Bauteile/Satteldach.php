<?php

namespace Enev\Schema202302\Calculations\Bauteile;

use Enev\Schema202302\Calculations\Schnittstellen\Transmissionswaerme;

require_once __DIR__ . '/Dach.php';

/**
 * Diese Klasse entählt die Funktionen zur Berechnung eines Satteldachs.
 *
 * @package 
 */
class Satteldach extends Dach implements Transmissionswaerme
{
    /**
     * Berechnung des volumens und der Dachfläche-
     * 
     * @return void 
     * @throws Exception 
     */
    protected function berechnen(): void
    {
        $flaeche = 0;
        $volumen = 0;

        switch ( $this->grundriss->form() ) {
        case 'a':
            if ($$this->grundriss->wand_laenge('a') > $this->grundriss->wand_laenge('b') ) {
                $dach_s = sqrt(pow($this->hoehe, 2) + pow(0.5 * $this->grundriss->wand_laenge('b'), 2));
                array_push($dachwinkel, atan($this->hoehe / ( 0.5 * $this->grundriss->wand_laenge('b') )));
                $flaeche += $$this->grundriss->wand_laenge('a') * $dach_s + $this->grundriss->wand_laenge('c') * $dach_s;
                $volumen += 0.5 * $$this->grundriss->wand_laenge('a') * $this->grundriss->wand_laenge('b') * $this->hoehe;
                $this->wand_flaechen['b'] = 0.5 * $this->grundriss->wand_laenge('b') * $this->hoehe;
                $this->wand_flaechen['d'] = 0.5 * $this->grundriss->wand_laenge('d') * $this->hoehe;
            } else {
                $dach_s = sqrt(pow($this->hoehe, 2) + pow(0.5 * $$this->grundriss->wand_laenge('a'), 2));
                array_push($dachwinkel, atan($this->hoehe / ( 0.5 * $$this->grundriss->wand_laenge('a') )));
                $flaeche += $this->grundriss->wand_laenge('b') * $dach_s + $this->grundriss->wand_laenge('d') * $dach_s;
                $volumen += 0.5 * $$this->grundriss->wand_laenge('a') * $this->grundriss->wand_laenge('b') * $this->hoehe;
                $this->wand_flaechen['a'] = 0.5 * $$this->grundriss->wand_laenge('a') * $this->hoehe;
                $this->wand_flaechen['c'] = 0.5 * $this->grundriss->wand_laenge('c') * $this->hoehe;
            }
            break;
        case 'b':
            if ($$this->grundriss->wand_laenge('a') > $this->grundriss->wand_laenge('b') ) {
                $dach_s1 = sqrt(pow($this->hoehe, 2) + pow(0.5 * $this->grundriss->wand_laenge('f'), 2));
                $dach_s2 = sqrt(pow($this->hoehe, 2) + pow(0.5 * $this->grundriss->wand_laenge('c'), 2));
                array_push($dachwinkel, atan($this->hoehe / ( 0.5 * $this->grundriss->wand_laenge('f') )), atan($this->hoehe / ( 0.5 * $this->grundriss->wand_laenge('c') )));
                $flaeche += 2 * ( $$this->grundriss->wand_laenge('a') - 0.25 * $this->grundriss->wand_laenge('c') ) * $dach_s1 + 2 * ( $this->grundriss->wand_laenge('d') + 0.25 * $this->grundriss->wand_laenge('f') ) * $dach_s2;
                $volumen += 0.5 * $$this->grundriss->wand_laenge('a') * $this->grundriss->wand_laenge('f') * $this->hoehe + 0.5 * $this->grundriss->wand_laenge('d') * $this->grundriss->wand_laenge('c') * $this->hoehe + ( 1.0 / 3.0 ) * ( 0.5 * $this->grundriss->wand_laenge('c') * $this->hoehe ) * ( 0.5 * $this->grundriss->wand_laenge('f') );
                $this->wand_flaechen['b'] = 0.5 * $this->grundriss->wand_laenge('f') * $this->hoehe;
                $this->wand_flaechen['c'] = 0.5 * $this->grundriss->wand_laenge('c') * $this->hoehe;
                $this->wand_flaechen['f'] = 0.5 * $this->grundriss->wand_laenge('f') * $this->hoehe;
            } else {
                $dach_s1 = sqrt(pow($this->hoehe, 2) + pow(0.5 * $this->grundriss->wand_laenge('c'), 2));
                $dach_s2 = sqrt(pow($this->hoehe, 2) + pow(0.5 * $this->grundriss->wand_laenge('f'), 2));
                array_push($dachwinkel, atan($this->hoehe / ( 0.5 * $this->grundriss->wand_laenge('c') )), atan($this->hoehe / ( 0.5 * $this->grundriss->wand_laenge('f') )));
                $flaeche += 2 * ( $this->grundriss->wand_laenge('b') - 0.25 * $this->grundriss->wand_laenge('f') ) * $dach_s1 + 2 * ( $wand_e_laenge + 0.25 * $this->grundriss->wand_laenge('c') ) * $dach_s2;
                $volumen += 0.5 * $this->grundriss->wand_laenge('b') * $this->grundriss->wand_laenge('c') * $this->hoehe + 0.5 * $wand_e_laenge * $this->grundriss->wand_laenge('f') * $this->hoehe + ( 1.0 / 3.0 ) * ( 0.5 * $this->grundriss->wand_laenge('f') * $this->hoehe ) * ( 0.5 * $this->grundriss->wand_laenge('c') );
                $this->wand_flaechen['a'] = 0.5 * $this->grundriss->wand_laenge('c') * $this->hoehe;
                $this->wand_flaechen['c'] = 0.5 * $this->grundriss->wand_laenge('c') * $this->hoehe;
                $this->wand_flaechen['f'] = 0.5 * $this->grundriss->wand_laenge('f') * $this->hoehe;
            }
            break;
        case 'c':
            $dach_s1 = sqrt(pow($this->hoehe, 2) + pow(0.5 * $this->grundriss->wand_laenge('b'), 2));
            $dach_s2 = sqrt(pow($this->hoehe, 2) + pow(0.5 * $wand_e_laenge, 2));
            array_push($dachwinkel, atan($this->hoehe / ( 0.5 * $this->grundriss->wand_laenge('b') )), atan($this->hoehe / ( 0.5 * $wand_e_laenge )));
            $flaeche += 2 * ( $$this->grundriss->wand_laenge('a') - 0.25 * $wand_e_laenge ) * $dach_s1 + 2 * ( $this->grundriss->wand_laenge('d') + 0.25 * $this->grundriss->wand_laenge('b') ) * $dach_s2;
            $volumen += 0.5 * $$this->grundriss->wand_laenge('a') * $this->grundriss->wand_laenge('b') * $this->hoehe + 0.5 * $wand_e_laenge * $this->grundriss->wand_laenge('d') * $this->hoehe + ( 1.0 / 3.0 ) * ( 0.5 * $wand_e_laenge * $this->hoehe ) * ( 0.5 * $this->grundriss->wand_laenge('b') );
            $this->wand_flaechen['b'] = 0.5 * $this->grundriss->wand_laenge('b') * $this->hoehe;
            $this->wand_flaechen['e'] = 0.5 * $wand_e_laenge * $this->hoehe;
            $this->wand_flaechen['h'] = 0.5 * $this->grundriss->wand_laenge('h') * $this->hoehe;
            break;
        case 'd':
            $dach_s1 = sqrt(pow($this->hoehe, 2) + pow(0.5 * ( $this->grundriss->wand_laenge('b') - $this->grundriss->wand_laenge('d') ), 2));
            $dach_s2 = sqrt(pow($this->hoehe, 2) + pow(0.5 * $this->grundriss->wand_laenge('c'), 2));
            $dach_s3 = sqrt(pow($this->hoehe, 2) + pow(0.5 * $this->grundriss->wand_laenge('g'), 2));
            array_push($dachwinkel, atan($this->hoehe / ( 0.5 * ( $this->grundriss->wand_laenge('b') - $this->grundriss->wand_laenge('d') ) )), atan($this->hoehe / ( 0.5 * $this->grundriss->wand_laenge('c') )), atan($this->hoehe / ( 0.5 * $this->grundriss->wand_laenge('g') )));
            $flaeche += 2 * ( $$this->grundriss->wand_laenge('a') - 0.25 * ( $this->grundriss->wand_laenge('c') + $this->grundriss->wand_laenge('g') ) ) * $dach_s1 + 2 * ( $this->grundriss->wand_laenge('d') + 0.25 * ( $this->grundriss->wand_laenge('b') - $this->grundriss->wand_laenge('d') ) ) * $dach_s2 + 2 * ( $this->grundriss->wand_laenge('f') + 0.25 * ( $this->grundriss->wand_laenge('h') - $this->grundriss->wand_laenge('f') ) ) * $dach_s3;
            $volumen += 0.5 * $$this->grundriss->wand_laenge('a') * ( $this->grundriss->wand_laenge('b') - $this->grundriss->wand_laenge('d') ) * $this->hoehe + 0.5 * $this->grundriss->wand_laenge('c') * $this->grundriss->wand_laenge('d') * $this->hoehe + 0.5 * $this->grundriss->wand_laenge('g') * $this->grundriss->wand_laenge('f') * $this->hoehe + ( 1.0 / 3.0 ) * ( 0.5 * $this->grundriss->wand_laenge('c') * $this->hoehe ) * ( 0.5 * ( $this->grundriss->wand_laenge('b') - $this->grundriss->wand_laenge('d') ) ) + ( 1.0 / 3.0 ) * ( 0.5 * $this->grundriss->wand_laenge('g') * $this->hoehe ) * ( 0.5 * ( $this->grundriss->wand_laenge('h') - $this->grundriss->wand_laenge('f') ) );
            $this->wand_flaechen['b'] = 0.5 * ( $this->grundriss->wand_laenge('b') - $this->grundriss->wand_laenge('d') ) * $this->hoehe;
            $this->wand_flaechen['c'] = 0.5 * $this->grundriss->wand_laenge('c') * $this->hoehe;
            $this->wand_flaechen['g'] = 0.5 * $this->grundriss->wand_laenge('g') * $this->hoehe;
            $this->wand_flaechen['h'] = 0.5 * ( $this->grundriss->wand_laenge('h') - $this->grundriss->wand_laenge('f') ) * $this->hoehe;
            break;
        default:
        }

        $this->flaeche = $flaeche;
        $this->volumen = $volumen + $this->volumen_kniestock();
    }
}