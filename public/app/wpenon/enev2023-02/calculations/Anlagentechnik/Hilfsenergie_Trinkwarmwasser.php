<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;

class Hilfsenergie_Trinkwarmwasser {


    /**
     * Gebäude.
     *
     * @param Gebaeude
     */
    public function __construct()
    {
    }
}


// Hilfsenergie für TWW-system $Wwg
//  a) konvenionelle Heizungssysteme

// $Wwg=$fphgaux*$Phgaux*$twpn

// 
//  b) Wärmepumpen
//    
//    if Wärmepumpe && Luftwasserwärmepumpe than
//       $Wwg=0.00;
//    else 
//       $Wwg= 0,00 // Nach Seite 145, T 12 Defintion, dass nur einemal berückscihtig wird und das erfolgt bei uns in der Heizung 


//  c) zentral elektrisch beheizter Wärmeerzeuger

//    
////   $Wwg=0.00; //T12, S.178, Wg = null 6.6.6.2

//  d) Fern- und Nahwärme
//
//       $Wwg= 0,00; // In T8 S. 97 und T12  keine genaue Beschreibung zu den TWW-Verlusten . Analoge Betrachtung wie Wärmepumpe. 
//
//  e) dezentraler Wärmeerzeuger (Elektrische Durchlauferhitzer und GasDuchlauferhitzer)
//
//       $Wwg=0.0;

//-----------------------
// Hilfsenergie für Solarpumpe (thermische Solaranlagen) $WsolPumpeg

//   $WsolPumpeg=0.025*$Qwsola1;