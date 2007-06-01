<?
	require_once('config.php');
	$session->requireLoggedIn();

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	require('design_head.php');

	$band_id = $_GET['id'];

	if (isset($_POST['title']))
	{
		setBandName($band_id, $_POST['title']);
		echo 'Band name changed.<br/>';
	}

	$band_name = getBandName($band_id);

	echo '<form name="editband" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$band_id.'">';
	echo '<a href="show_band.php?id='.$band_id.'">'.$band_name.' - </a><input type="text" name="title" size="50" value="'.$band_name.'"/><br/>';
	echo '<input type="submit" class="button" value="Change band name"/>';
	echo '</form>';

	require('design_foot.php');
?>