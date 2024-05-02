<?php

namespace Enev\Schema202403\Calculations\Helfer;

/**
 * Interpolation of values.
 *
 * @param float $target_value The value to interpolate.
 * @param array $keys
 * @param array $values
 *
 * @return float
 */
function interpolate_value(float $target_value, array $keys, array $values): float
{
    // Initialisieren eines Indexes zur Bestimmung der Position des Zielwerts
    $index = 0;

    // Durchlaufen der Schlüssel (keys), um die Position des Zielwerts zu finden
    foreach ($keys as $key) {
        // Wenn der Zielwert kleiner als der aktuelle Schlüssel ist, wird die Schleife beendet
        if ($target_value < $key) {
            break;
        }
        // Inkrementieren des Indexes, um zur nächsten Position zu gehen
        ++$index;
    }

    // Wenn der Index 0 ist, wird der erste Wert in 'values' zurückgegeben
    // Dies tritt auf, wenn der Zielwert kleiner als der erste Schlüssel ist
    if ($index == 0) {
        return $values[0];
    }

    // Wenn der Index gleich der Anzahl der Schlüssel ist, wird der letzte Wert in 'values' zurückgegeben
    // Dies tritt auf, wenn der Zielwert größer als der letzte Schlüssel ist
    if ($index == count($keys)) {
        return $values[count($keys) - 1];
    }

    // Bestimmung der beiden Schlüsselwerte, zwischen denen interpoliert wird
    $x1 = $keys[$index - 1];
    $x2 = $keys[$index];
    // Bestimmung der beiden Werte, zwischen denen interpoliert wird
    $y1 = $values[$index - 1];
    $y2 = $values[$index];

    // Berechnung des interpolierten Wertes
    // Die Formel ist eine lineare Interpolation:
    // Der Basiswert (y1) plus der Anteil des Weges zwischen y1 und y2,
    // der durch die Differenz zwischen Zielwert und x1 im Verhältnis zur Distanz zwischen x1 und x2 bestimmt wird.
    return $y1 + ($target_value - $x1) * ($y2 - $y1) / ($x2 - $x1);
}
