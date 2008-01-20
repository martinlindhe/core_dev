<?
/**
 * Convert timestamps to different formats
 *
 * \todo Validate that NTP conversion is correct
 */

	require_once('config.php');

	$ts = 1200685768;

	if (!empty($_POST['ts'])) {
		$ts = $_POST['ts'];

		echo 'Unix timestamp (RFC 2822): '.date('r', $ts).'<br/>';
		echo 'NTP timestamp (RFC 2822): '.date('r', ntptime_to_unixtime($ts) ).'<br/>';
		echo '<br/>';
	}
?>

<form method="post" action="">
Timestamp: <input type="text" name="ts" size="15" value="<?=$ts?>"/>
<input type="submit" class="button" value="Convert"/>
</form>