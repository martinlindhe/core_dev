<?php

require_once('config.php');

$session->requireLoggedIn();

if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

$record_id = $_GET['id'];
$band_id = getBandIdFromRecordId($record_id);

if (isset($_POST['title'])) {
	updateRecord($record_id, $_POST['title'], $_POST['info']);

	if (isset($_POST['band']) && $_POST['band'] && is_numeric($_POST['band'])) {
		if ($band_id != $_POST['band']) {
			changeRecordOwner($record_id, $_POST['band']);
		}
	}
	header('Location: show_record.php?id='.$record_id);
	die;
}

require('design_head.php');

$band_name = getBandName($band_id);
$record_name = getRecordName($record_id);

echo 'If you added this record by mistake to the wrong band, you can set it to a different band here.<br/><br/>';
echo '<form name="editrecord" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$record_id.'">';
echo '<a href="show_band.php?id='.$band_id.'">'.htmlspecialchars($band_name).' - </a><input type="text" name="title" size="50" value="'.$record_name.'"/><br/><br/>';
echo 'Record info:<br/><textarea name="info" cols="40" rows="6">'.getRecordInfo($record_id).'</textarea><br/><br/>';
echo 'Change band: <select name="band">';

$list = getBands();
for ($i=0; $i<count($list); $i++) {	//FIXME: foreach?
	echo '<option value="'.$list[$i]['bandId'].'"';
	if ($band_id == $list[$i]['bandId']) echo ' selected="selected"';
	echo '>'.htmlspecialchars($list[$i]['bandName']).'</option>';
}
echo '</select><br/><br/>';

echo '<input type="submit" class="button" value="Update changes"/>';
echo '</form>';

require('design_foot.php');
?>
