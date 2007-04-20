<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');
?>
<h1>Admin menu</h1><br/><br/>

<a href="admin_events.php?pr=<?=($_GET['pr'])?>">Event log</a><br/>
<a href="admin_phpinfo.php?pr=<?=($_GET['pr'])?>">PHP info</a><br/>

<a href="admin_compat_check.php?pr=<?=($_GET['pr'])?>">Compatiblity check</a><br/>


<?
	require($project.'design_foot.php');

?>