<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

/**
 * Calculates the expression & returns an array with the result
 *
 * @param $expr textual string representing math formula to solve
 * @return array with the resulting equations from the input
 */
function calcExpr($expr)
{
	//Prepare $expr for parsing, make sure there are white spaces between each part of the expression
	$expr = str_replace('+', ' + ', $expr);
	$expr = str_replace('-', ' - ', $expr);
	$expr = str_replace('*', ' * ', $expr);
	$expr = str_replace('/', ' / ', $expr);
	$expr = str_replace('<<', ' << ', $expr);
	$expr = str_replace('>>', ' >> ', $expr);

	$expr = str_replace('(', ' ( ', $expr);
	$expr = str_replace(')', ' ) ', $expr);
	$expr = str_replace('  ', ' ', $expr);

	$expr = trim($expr);

	echo 'Calculating "'.$expr.'" ...<br/>';

	$parts = explode(' ', $expr);

	//Perhaps we just need to convert to multiple data types
	if (count($parts) == 1) {
		$result['dec'] = toDec($parts[0]);
		$result['hex'] = dechex($result['dec']);
		$result['bin'] = decbin($result['dec']);
		$result['chr'] = chr($result['dec']);
		return $result;
	}

	//Evaluate the expression. Only handles the format: "n1 operator n2"
	$result['dec'] = evalExpr($parts);
	if ($result['dec'] === false) return false;

	$result['hex'] = dechex($result['dec']);
	$result['bin'] = decbin($result['dec']);

	if ($result['dec'] > 10 && $result['dec'] < 256) {
		$result['chr'] = chr($result['dec']);
	} else {
		$result['chr'] = '???';
	}

	return $result;
}

function evalExpr($parts)
{
	if (count($parts) != 3) {
		echo 'Unsupported forumla format';
		echo '<pre>'; print_r($parts);
		return false;
	}

	switch ($parts[1]) {
		case '<<': $result['dec'] = toDec($parts[0]) << toDec($parts[2]); break;
		case '>>': $result['dec'] = toDec($parts[0]) >> toDec($parts[2]); break;
		case '*': $result['dec'] = toDec($parts[0]) * toDec($parts[2]); break;
		case '/': $result['dec'] = toDec($parts[0]) / toDec($parts[2]); break;
		case '+': $result['dec'] = toDec($parts[0]) + toDec($parts[2]); break;
		case '-': $result['dec'] = toDec($parts[0]) - toDec($parts[2]); break;

		default:
			echo 'Unknown operator: '.$parts[1].'<br/>';
			return false;
	}

	return $result['dec'];
}

function toDec($v)
{
	if (substr($v, 0, 2) == '0x') {
		//Hexadecimal "0xff200" form
		return hexdec(substr($v, 2));
	} else if (substr($v, -1) == 'b') {
		//Binary "100101b" form
		return bindec(substr($v, 0, -1));
	}
	return $v;
}
?>
