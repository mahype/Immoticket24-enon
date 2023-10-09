<?php

/**
 * Jährlicher Nutzwaermebedarf für Trinkwasser (qwb).
 * 
 * Aufgrund der Einfachheit nicht in der Datenbank gespeichert.
 * 
 * Teil 12 - Tabelle 19.
 * 
 * @param float $nutzflaeche Netto-Nutzfläche des Gebäudes.
 * 
 * @return float 
 */
function jaehrlicher_nutzwaermebedarf_trinkwasser( float $nutzflaeche ): float {
    if ($nutzflaeche < 10) {
        return 16.5;
    } elseif ($nutzflaeche >= 10) {
        return 16;
    } elseif ($nutzflaeche >= 20) {
        return 15.5;
    } elseif ($nutzflaeche >= 30) {
        return 15;
    } elseif ($nutzflaeche >= 40) {
        return 14.5;
    } elseif ($nutzflaeche >= 50) {
        return 14;
    } elseif ($nutzflaeche >= 60) {
        return 13.5;
    } elseif ($nutzflaeche >= 70) {
        return 13;
    } elseif ($nutzflaeche >= 80) {
        return 12.5;
    } elseif ($nutzflaeche >= 90) {
        return 12;
    } elseif ($nutzflaeche >= 100) {
        return 11.5;
    } elseif ($nutzflaeche >= 110) {
        return 11;
    } elseif ($nutzflaeche >= 120) {
        return 10.5;
    } elseif ($nutzflaeche >= 130) {
        return 10;
    } elseif ($nutzflaeche >= 140) {
        return 9.5;
    } elseif ($nutzflaeche >= 150) {
        return 9;
    } elseif ($nutzflaeche >= 160) {
        return 8.5;
    }
}