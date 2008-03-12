<?
/**
 * ajax_rate.php - for rating
 *
 * $_GET['i'] object id
 * $_GET['t'] rating type (RATE_FILE, RATE_BLOG, RATE_NEWS)
 * $_GET['v'] vote score
 */

	require_once('find_config.php');

	if (!$session->id || empty($_GET['i']) || !is_numeric($_GET['i']) ||
		empty($_GET['t']) || !is_numeric($_GET['t']) ||
		empty($_GET['v']) || !is_numeric($_GET['v'])) die('bad');

	$_type = $_GET['t'];
	$_id = $_GET['i'];
	$_val = $_GET['v'];

	rateItem($_type, $_id, $_val);
	echo showRating($_type, $_id);
?>
