<?
/**
 * Convert times of different formats
 */

	if (!empty($_POST['ts'])) {
		echo 'RFC 2822: '.date('r', $_POST['ts']).'<br/>';
		echo 'RFC 8601: '.date('c', $_POST['ts']).'<br/>';
	}

?>

<form method="post" action="">
Unix timestamp: <input type="text" name="ts"/>
<input type="submit" class="button" value="Convert"/>
</form>
