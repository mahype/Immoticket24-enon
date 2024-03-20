<?php

namespace Enev\Schema202402\Calculations\Bauteile;

use Enev\Schema202402\Calculations\Schnittstellen\Transmissionswaerme;

/**
 * Anbauwand.
 *
 * Da diese genau wie die Wand nach Außen geht, erbt sie von dieser den fx-Wert.
 */
class Anbauwand extends Wand implements Transmissionswaerme {
}
