<?
	require_once('config.php');
	require('design_head.php');
	
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	$band_id = $_GET['id'];
		
	if (isset($_POST['title']))
	{
		if (!$session->isAdmin) {
			addPendingChange(MODERATIONCHANGE_BANDNAME, $band_id, $_POST['title']);
			echo 'Change added to moderation queue<br/>';
		} else {
			setBandName($band_id, $_POST['title']);
			echo 'Band name changed.<br/>';
		}
	}

	$band_name = getBandName($band_id);

	echo '<form name="editband" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$band_id.'">';

	echo '<b>'.$band_name.' - </b><input type="text" name="title" size="50" value="'.$band_name.'"/><br/>';

	echo '<input type="submit" class="button" value="Change band name"/>';
	echo '</form>';

	echo '<a href="show_band.php?id='.$band_id.'">Back to '.$band_name.' page</a>';

	require('design_foot.php');
?>