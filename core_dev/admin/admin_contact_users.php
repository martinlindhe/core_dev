<?
	require_once('find_config.php');
	$session->requireSuperAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');
	if ($session->isSuperAdmin) {
		echo createMenu($super_admin_menu, 'blog_menu');
		echo createMenu($super_admin_tools_menu, 'blog_menu');
	}

	if (isset($_GET['send'])) {
		$how = isset($_POST['how'])?$_POST['how']:'';
		$all = isset($_POST['all'])?$_POST['all']:'0';
		$presvid = isset($_POST['presvid'])?$_POST['presvid']:'';
		$message = isset($_POST['message'])?$_POST['message']:'';
		$subject = isset($_POST['subject'])?$_POST['subject']:'';
		$logged_in_days = isset($_POST['logged_in_days'])?$_POST['logged_in_days']:'';
		$days = isset($_POST['days'])?$_POST['days']:'';

		$res = Users::getSearchResult($_POST);
		
		if ($how == 'mail') {
			contact_users($message, $subject, $all, $presvid, $logged_in_days, $days, $res);
		}
		else if ($how == 'sms') {
			// TODO:
		}
		
		echo 'Message sent';
	}
	
	echo '<h1>Contact users</h1>';

	Users::contact();

	require($project.'design_foot.php');
?>
