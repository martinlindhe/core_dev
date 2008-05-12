<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireSuperAdmin();

if (!empty($_GET['del'])) removeStopword($_GET['del']);
	
if (count($_POST)) {
	$list = getStopwords();

	for($i=0; $i<count($list); $i++) {
		$id = $list[$i]['wordId'];

		$del = 'del_'.$id;
		$chg = 'change_'.$id;
		$full = 0;
		if (isset($_POST['full_'.$id])) $full = $_POST['full_'.$id];	

		//Update has less priority
		if (($_POST[$chg] != $list[$i]['wordText']) || ($full != $list[$i]['wordMatch'])) {
			setStopword($id, $_POST[$chg], $full);
		}
	}
		
	for($i=1; $i<4; $i++) {
		$word = 'newname_'.$i;
		$full = 0;
		if (isset($_POST['newfull_'.$i])) $full = $_POST['newfull_'.$i];

		if (!empty($_POST[$word])) {
			if (!addStopword($i, $_POST[$word], $full)) {
				echo 'Failed to add '.$_POST[$word].'<br/>';
			}
		}
	}
}

require($project.'design_head.php');

echo createMenu($admin_menu, 'blog_menu');
echo createMenu($super_admin_menu, 'blog_menu');
echo createMenu($super_admin_tools_menu, 'blog_menu');

echo '<form name="update" method="post" action="">';

for($x=1; $x<=3; $x++) {
	echo '<div class="admin_stopword">';
	switch($x) {
		case STOPWORD_OBJECTIONABLE:
			$txt='Objectionable';
			$help = 'Offensive words, that arent allowed to be published.<br/><b>Currently not enabled</b>.';
			break;

		case STOPWORD_SENSITIVE:
			$txt='Sensitive';
			$help = 'Inl&auml;gg inneh&aring;llande k&auml;nsliga ord hamnar automatiskt i modereringsk&ouml;n utan att inl&auml;gget blockeras.<br/><b>Used in the following modules: BLOG, GUESTBOOK</b>';
			break;

		case STOPWORD_RESERVED_USERNAME:
			$txt='Reserved';
			$help = 'Reserved words are not allowed in user names.';
			break;
	}

	echo $txt.'<br/>'.$help.'<br/>';
		
	$list = getStopwords($x);
	foreach ($list as $row) {
		echo '<input type="text" name="change_'.$row['wordId'].'" value="'.$row['wordText'].'" size="16"/>';
		echo '<input type="checkbox" class="checkbox" name="full_'.$row['wordId'].'" id="full_'.$row['wordId'].'" value="1"'.($row['wordMatch']==1?' checked="checked"':'').'/>';
		echo '<label for="full_'.$row['wordId'].'">Full</label> ';
		echo '<a href="?del='.$row['wordId'].getProjectPath().'"><img src="'.$config['core']['web_root'].'gfx/icon_delete.png" alt="Delete"/></a><br/>';
	}

	echo '<br/><br/>Add new word:<br/><input type="text" name="newname_'.$x.'" size="16"/>';
	echo '<input type="checkbox" class="checkbox" value="1" name="newfull_'.$x.'" id="newfull_'.$x.'"/>';
	echo '<label for="newfull_'.$x.'">Full</label>';

	echo '</div>'; //class="admin_stopword"
}
echo '<input type="submit" class="button" value="Update"/>';
echo '</form>';

require($project.'design_foot.php');
?>
