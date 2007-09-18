<?
	//Logs the termination of a call

	if (empty($_GET['id'])) die;

	require_once('config.php');

	terminateCall($_GET['id']);
	
	require_once('vxml_head.php');
?>

  <!-- Quit block -->
	<form id="quit">
		<block>
			<exit/>
		</block>
	</form>

<?
	require_once('vxml_foot.php');
?>
