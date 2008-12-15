<?php
/**
 * Sample program to expose functions_validate_cc.php features
 */

require_once('config.php');

createXHTMLHeader();

$cc = '1234 1234 1234 1234';	//FIXME: add a fake "valid" number

if (!empty($_POST['cc'])) {
	$cc = $_POST['cc'];

	echo '<h1>Analysis of credit card '.CCprintNumber($cc).'</h1>';
	echo 'Type of card: '.$cc_name[ CCgetType($cc) ].'<br/>';
	if (CCvalidateMod10($cc)) {
		echo 'Valid cc number<br/>';
	} else {
		echo '<b>Invalid cc number!!!</b><br/>';
	}
}
?>

Enter a credit card number:

<form name="ccvalidate" method="post" action="">
	<input name="cc" type="text" size="22" value="<?=$cc?>"/>
	<br/>
	<input type="submit" class="button" value="Validate"/>
</form>

</body>
</html>
