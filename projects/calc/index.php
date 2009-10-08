<?php
/**
 * Simple web-based programmers calcylator
 *
 * Conceptually similar to AnalogX's pcalc
 *
 *
 * Example input:					Output:
 * 0x200 << 1							0x400
 */

require_once('functions_calc.php');

$expr = '0x200 << 1';

if (!empty($_GET['expr'])) {
	$expr = $_GET['expr'];

	$result = calcExpr($expr);
}
?>

<form method="get" name="calc" action="">
	<input type="text" name="expr" value="<?php echo $expr?>"/>
	<input type="submit" value="Calc"/>
</form>

<?php
if (!empty($result)) {
	echo '<pre>';
	echo 'Decimal: '.$result['dec']."\n";
	echo 'Hex    : 0x'.$result['hex']."\n";
	echo 'Binary : '.$result['bin']."b\n";
	echo 'Ascii  : '.$result['chr']."\n";
	echo '</pre>';
}
?>

<script type="text/javascript">
document.calc.expr.focus();
</script>
