<?
	/*
		enkel webbaserad programmerings-kalkylator

		konceptuellt snarlik analogx' pcalc
		


		exempel input:					expempel output:
		0x200 << 1							0x400
	*/

	require_once('functions_calc.php');

	if (!empty($_GET['expr'])) {
		$expr = $_GET['expr'];
		
		$result = calcExpr($expr);
		
		echo '<pre>result: '; print_r($result);
	}
?>

<form method="get" action="">
	<input type="text" name="expr" value="0x200 << 1"/>
	<input type="submit" value="Calc"/>
</form>