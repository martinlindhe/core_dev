<?
 	//print_r($_GET);
	if($action != 'view' && !$l) {
		loginACT();
	}
	if(!is_numeric($id)) $id = false;
	if($l) {
		$own = (!$id || $id == $l['id_id'])?true:false;
		$no_user_error = array('mail', 'mailread', 'galleryupload');
		if(!$own && $action != 'relay') {
			#verify that user is OK
			$s = $user->getuser($id);
			if(!in_array($action, $no_user_error) && !$s) errorACT('Användaren existerar inte längre.');
			$user->blocked($id);
		} else $s = $l;
	} else {
		if(!$id) loginACT();
		$own = false;
		$s = $user->getuser($id);
	}
	if($action == 'gb') {
		include('gb.php');
		exit;
	} else if($action == 'gbwrite') {
		include('gbwrite.php');
		exit;
	} else if($action == 'chat') {
		include('chat.php');
		exit;
	} else if($action == 'spy') {
		include('spy.php');
		exit;
	} else if($action == 'chatwin') {
		include('chatwin.php');
		exit;
	} else if($action == 'relay') {
		include('chatrelay.php');
		exit;
	} else if($action == 'mail') {
		include('mail.php');
		exit;
	} else if($action == 'mailwrite') {
		include('mailwrite.php');
		exit;
	} else if($action == 'mailread') {
		include('mailread.php');
		exit;
	} else if($action == 'gallery') {
		include('gallery.php');
		exit;
	} else if($action == 'gallerycomment') {
		include('gallery_comment.php');
		exit;
	} else if($action == 'galleryupload') {
		include('galleryupload.php');
		exit;
	} else if($action == 'relations') {
		include('relations.php');
		exit;
	} else if($action == 'block') {
		include('block.php');
		exit;
	} else if($action == 'blog') {
		include('blog.php');
		exit;
	} else if($action == 'blogcomment') {
		include('blog_comment.php');
		exit;
	} else if($action == 'visit') {
		include('visit.php');
		exit;
	} else {
		include('view.php');
		exit;
	}
?>
