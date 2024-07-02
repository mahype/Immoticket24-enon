<?php

namespace Enev\Schema202403\Calculations\Bauteile;

use Enev\Schema202403\Calculations\Schnittstellen\Transmissionswaerme;

/**
 * Anbauwand.
 *
 * Da diese genau wie die Wand nach Außen geht, erbt sie von dieser den fx-Wert.
 */
class Anbauwand extends Wand implements Transmissionswaerme
{
}
