<?
	require_once('config.php');
	require('design_head.php');
	
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('Bad id');

	$record_id = $_GET['id'];
	$band_id = getBandIdFromRecordId($record_id);
		
	if (isset($_POST['title']))
	{
		//if (!$session->isAdmin) {
			addPendingChange(MODERATIONCHANGE_RECORDNAME, $record_id, $_POST['title']);
			echo 'Change added to moderation queue<br/>';
		//} else {
			//updateRecord($record_id, $_POST['title']);
			//echo 'Title changed.<br/>';
		//}
	}

	if (isset($_POST['band']) && $_POST['band'] && is_numeric($_POST['band'])) {
		if ($band_id != $_POST['band']) {
			changeRecordOwner($record_id, $_POST['band']);
			echo 'Band changed.<br/>';
		}
	}

	$band_name = getBandName($band_id);
	$record_name = getRecordName($record_id);

	echo 'If you added this record by mistake to the wrong band, you can set it to a different band here.<br/><br/>';
	
	echo '<form name="editrecord" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$record_id.'">';

	echo '<b>'.$band_name.' - </b><input type="text" name="title" size="50" value="'.$record_name.'"/><br/>';

	echo 'Change band: <select name="band">';
	$list = getBands();
	for ($i=0; $i<count($list); $i++)
	{
		echo '<option value="'.$list[$i]['bandId'].'"';
		if ($band_id == $list[$i]['bandId']) echo ' selected="selected"';
		echo '>'.$list[$i]['bandName'].'</option>';
	}
	echo '</select><br/>';

	echo '<input type="submit" value="Update changes" class="buttonstyle"/>';
	echo '</form>';

	echo '<a href="show_band.php?id='.$band_id.'">Back to '.$band_name.' page</a>';

	require('design_foot.php');
?>