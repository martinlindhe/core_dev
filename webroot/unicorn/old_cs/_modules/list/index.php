<?
	if($action == 'users') {
		include('users.php');
		exit;
	} elseif($action == 'userfind') {
		include('userfind.php');
		exit;
	} elseif($action == 'messages') {
		include('messages.php');
		exit;
	} elseif($action == 'logins') {
		include('logins.php');
		exit;
	} elseif($action == 'objects') {
		include('objects.php');
		exit;
	}
	reloadACT(l());
?>
