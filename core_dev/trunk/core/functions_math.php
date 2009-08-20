<?php
/**
 * $Id$
 *
 * Misc math functions
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */

/**
 * Finds the closest approximation of a division resulting in a float
 *
 * Example: Suppose we have the float 0.9883 and we suspect it
 * to be an approximation of a integer division (bytes)
 *
 * We then calculate as such:
 * $ints = findApproxDivision(0.9883, 256);
 */
function findApproxDivision($approx, $top = 256)
{
	$closest = 999999;
	$m1 = 0;
	$m2 = 0;

	for ($n1 = 1; $n1 <= $top; $n1++) {
		for ($n2 = 1; $n2 <= $top; $n2++) {

			$t = $n1 / $n2;
			$diff = ($t > $approx ? $t - $approx : $approx - $t);

			if ($diff < $closest) {
				$closest = $diff;
				$m1 = $n1;
				$m2 = $n2;
				echo $n1." / ".$n2." = ".$t." (".$diff.")\n";
			}
		}
	}
	return array($m1, $m2);
}

?>
