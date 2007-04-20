<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');
?>
<h1>Admin menu</h1><br/><br/>

<a href="admin_events.php?pr=<?=($_GET['pr'])?>">Event log</a><br/>
<a href="admin_phpinfo.php?pr=<?=($_GET['pr'])?>">PHP info</a><br/>

<hr/>
maintainance:<br/>
<a href="admin_compat_check.php?pr=<?=($_GET['pr'])?>">Compatiblity check</a><br/>

<a href="admin_db_info.php?pr=<?=($_GET['pr'])?>">DB driver config</a><br/>
<a href="admin_session_info.php?pr=<?=($_GET['pr'])?>">Session info</a><br/>


<?
	require($project.'design_foot.php');

?>