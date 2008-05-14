<?php

require_once('config.php');
$session->requireLoggedIn();
	
if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;	//invalid request
$itemId = $_GET['id'];

$item = getForumItem($itemId);
		
if (!$item) {
	header('Location: '.$config['start_page']);
	die;
}

if (isset($_POST['destId'])) {
	setForumItemParent($itemId, $_POST['destId']);
	header('Location: forum.php?id='.$itemId);
	die;
}

require('design_head.php');
	
echo '<h1>Move thread</h1>';

echo 'This discussion thread will be moved:<br/><br/>';
echo showForumPost($item, '', false).'<br/>';

echo 'Where do you want to move the thread?<br/>';
echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';

$list = getForumStructure();

echo '<select name="destId">';
//echo '<option value="0">Flytta till roten';
foreach ($list as $row) {
	echo '<option value="'.$row['itemId'].'">'.$row['name'];
}
echo '</select>';

echo '<br/><br/>';
echo '<input type="submit" class="button" value="Move"/>';
echo '</form><br/><br/>';
echo '<a href="javascript:history.go(-1);">Return</a>';	

require('design_foot.php');
?>
