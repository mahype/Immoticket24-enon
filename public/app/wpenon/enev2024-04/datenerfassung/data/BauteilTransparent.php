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
		$bauart = isset( $this->data['bauart'] ) ? $this->data['bauart'] : '';

        switch ( $bauart ) {
			case 'holzeinfach':
				return 0.87;
			default:
				return 0.6;
		}
    }
}