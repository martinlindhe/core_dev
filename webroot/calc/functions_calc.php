<?

	/* Calculates the expression & returns an array with the result */
	function calcExpr($expr)
	{
		//1. Prepare $expr for parsing, make sure there are white spaces between each part of the expression
		$expr = str_replace('+', ' + ', $expr);
		$expr = str_replace('-', ' - ', $expr);
		$expr = str_replace('*', ' * ', $expr);
		$expr = str_replace('/', ' / ', $expr);
		$expr = str_replace('<<', ' << ', $expr);
		$expr = str_replace('>>', ' >> ', $expr);

		$expr = str_replace('(', ' ( ', $expr);
		$expr = str_replace(')', ' ) ', $expr);
		$expr = str_replace('  ', ' ', $expr);

		echo 'Calculating "'.$expr.'" ...<br/>';

		$parts = explode(' ', $expr);

		//echo '<pre>'; print_r($parts);

		//2. Konvertera alla hexadecimala tal till decimala tal
		for ($i=0; $i<count($parts); $i++) {
			if (substr($parts[$i], 0, 2) == '0x') {
				$parts[$i] = hexdec(substr($parts[$i], 2));
			}
		}

		//3. Evaluera uttrycket. stödjer enbart formatet: "tal1 operator tal2"
		if (count($parts) != 3) {
			echo 'Unsupported forumla format';
			return false;
		}

		switch ($parts[1]) {
			case '<<': $result['dec'] = $parts[0] << $parts[2]; break;
			case '>>': $result['dec'] = $parts[0] >> $parts[2]; break;
			case '*': $result['dec'] = $parts[0] * $parts[2]; break;
			case '/': $result['dec'] = $parts[0] / $parts[2]; break;
			case '+': $result['dec'] = $parts[0] + $parts[2]; break;
			case '-': $result['dec'] = $parts[0] - $parts[2]; break;

			default:
				echo 'unknown operator: '.$parts[1].'<br/>';
				return false;
		}

		$result['hex'] = dechex($result['dec']);

		return $result;
	}

?>