<?php

/**
 * Bauteil transparent Klasse
 * 
 * Vorrübergehend bis zur neuen Berechnng
 */
class BauteilTransparent extends Bauteil
{
    public function GWert()
    {
        switch ( $this->data['bauart'] ) {
			case 'holzeinfach':
				return 0.87;
			default:
				return 0.6;
		}
    }
}