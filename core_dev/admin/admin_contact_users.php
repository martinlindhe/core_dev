<?php
/**
 * $Id$
 */

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

	echo 'Message has been sent to:<br/>';
		
	if ($how == 'mail') {
		contact_users($message, $subject, $all, $presvid, $logged_in_days, $days, $res);
	} else if ($how == 'sms') {
		// TODO:
	}
}
	
echo '<h1>Contact users</h1>';

echo'<form name="contact" method="post" action="'.$_SERVER['PHP_SELF'].'?send">';

echo 'How: <input type="radio" name="how" value="mail"> E-mail | ';
echo '<input type="radio" name="how" value="sms"> SMS';
echo '<br/><input type="checkbox" name="all" value="1"> Alla';

$list = getUserdataFields();
echo '<table>';
foreach ($list as $row) {
	if ($row['private'] && !$session->isAdmin) continue;

	echo '<tr'.($row['private']?' class="critical"':'').'>';
	echo getUserdataSearch($row);
	echo '</tr>';
	if ($row['private']) {
		echo '<tr><td colspan="2">';
		echo '<div class="critical"> * '.t('This field can only be used in searches by admins').'</div>';
		echo '</td></tr>';
	}
}
echo '</table>';

echo '<br/>To use this part you have to choose at least one option above. If you want to find ';
echo 'all users with a video presentation, enter 07 in "Mobile" field above.<br/><br/>';
echo 'Video presentation: <input type="radio" name="presvid" value="1"> Yes | ';
echo '<input type="radio" name="presvid" value="0"> No<br/>';

echo '<input type="radio" name="logged_in_days" value="0"> Not logged in for x days | ';
echo '<input type="radio" name="logged_in_days" value="1"> Logged in latest x days<br/>';
echo 'Days: '.xhtmlInput('days').'<br/>';
echo 'Subject: '.xhtmlInput('subject').'<br/>';
echo '<br/><br/>';
echo 'Message:<br/>';
echo xhtmlTextarea('message', '', 50, 10).'<br/>';
echo xhtmlSubmit('Send');
echo '</form>';

require($project.'design_foot.php');
?>
