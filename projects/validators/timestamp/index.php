<?php
/**
 * Convert timestamps to different formats
 *
 * \todo Validate that NTP conversion is correct
 */

require_once('config.php');

require_once('class.Timestamp.php');

$ts = new Timestamp(1200685768);

if (!empty($_POST['ts'])) {
	$ts->set($_POST['ts']);

	echo 'Unix timestamp (RFC 2822): '.date('r', $ts->getUnix()).'<br/>';
	echo 'NTP timestamp (RFC 2822): '.date('r', $ts->getNTP() ).'<br/>';
	echo '<br/>';
}
?>

<form method="post" action="">
Timestamp: <input type="text" name="ts" size="15" value="<?=$ts->getUnix()?>"/>
<input type="submit" class="button" value="Convert"/>
</form>
