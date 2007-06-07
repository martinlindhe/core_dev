<?
	if($action == 'start') {
		include('forum.php');
		exit;
	} elseif($action == 'list') {
		include('forum_list.php');
		exit;
	} elseif($action == 'read') {
		include('forum_read.php');
		exit;
	} elseif($action == 'write') {
		include('forum_write.php');
		exit;
	}	elseif($action == 'answer') {
		include('forum_answer.php');
		exit;
	}
?>
