<?php

if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
$band_id = $_GET['id'];

require_once('config.php');
$session->requireLoggedIn();

require('design_head.php');

if (isset($_GET['delete']) && confirmed('Are you sure you want to delete this band?', 'delete', $band_id)) {
	deleteBand($band_id);
	echo 'Band deleted';
	require('design_foot.php');
	die;
}

if (isset($_POST['title'])) {
	setBandName($band_id, $_POST['title']);
	echo 'Band name changed.<br/>';
}

$band_name = getBandName($band_id);

echo '<form name="editband" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$band_id.'">';
echo 'Current band name: <a href="show_band.php?id='.$band_id.'">'.$band_name.'</a><br/><br/>';
echo 'New band name: <input type="text" name="title" size="50" value="'.$band_name.'"/><br/><br/>';
echo '<input type="submit" class="button" value="Change band name"/>';
echo '</form><br/>';

echo '<a href="?id='.$band_id.'&amp;delete">Delete band</a><br/><br/>';

require('design_foot.php');
?>
