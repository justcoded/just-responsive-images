<?php

/**
 * Calculate the greatest common divider for 2 integers.
 *
 * @param int $a First integer.
 * @param int $b Second integer.
 *
 * @return int
 */
function jri_greatest_commod_divisor( $a, $b ) {
	$a = (int) $a;
	$b = (int) $b;

	return ( 0 === $b ) ? $a : jri_greatest_commod_divisor( $b, $a % $b );
}