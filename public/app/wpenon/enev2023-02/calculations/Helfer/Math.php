<?php

namespace Enev\Schema202302\Calculations\Helfer;

/**
 * Interpolation of values.
 *
 * @param float $target_value
 * @param array $keys
 * @param array $values
 *
 * @return float
 */
function interpolate_value( float $target_value, array $keys, array $values ): float {
	$index = 0;

	foreach ( $keys as $key ) {
		if ( $target_value < $key ) {
			break;
		}
		++$index;
	}

	if ( $index == 0 ) {
		return $values[0];
	}

	if ( $index == count( $keys ) ) {
		return $values[ count( $keys ) - 1 ];
	}

	$x1 = $keys[ $index - 1 ];
	$x2 = $keys[ $index ];
	$y1 = $values[ $index - 1 ];
	$y2 = $values[ $index ];

	return $y1 + ( $target_value - $x1 ) * ( $y2 - $y1 ) / ( $x2 - $x1 );
}
