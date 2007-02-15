<?
	include('include_all.php');
	
	if (empty($_GET['id']) || !$_SESSION['userId']) {
		header('Location: '.$config['start_page']);
		die;
	}

	$to = $_GET['id'];
	$toname = getUserName($db, $to);
	if (!$toname) {
		header('Location: '.$config['start_page']);
		die;
	}

	$msgBody = '';
	
	if (isset($_POST['msg']) && $_POST['msg'] && (strlen($_POST['msg']) <= $config['messages']['maxsize_body'])) {
		sendMessageInstant($db, $_SESSION['userId'], $to, '', $_POST['msg']);
		header('Location: mess_history.php?id='.$to);
		die;
	} else if ((isset($_POST['msg']) && (strlen($_POST['msg']) > $config['messages']['maxsize_body']))) {
		$send_error = 'Meddelandet du försökte skicka är för långt, max tillåtna tecken är '.$config['messages']['maxsize_body'].', försök att korta ner meddelandet lite.';
		$msgBody = $_POST['msg'];
		//Alert
	}

	include('design_head.php');
	include('design_user_head.php');

	
	$content  = 'Send en melding til '.nameLink($to, $toname).'.<br>';
	$content .= 'Meldingen kan maksimalt v&aelig;re p&aring; '.$config['messages']['maxsize_body'].' tegn.<br><br>';
	if (isset($send_error) && $send_error) {
		$content .= '<span class="objectCritical">'.$send_error.'</span><br><br>';
	}
	$content .= '<form name="mess" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$to.'">';
	$content .= '<textarea cols=40 rows=8 name="msg">'.$msgBody.'</textarea><br><br>';
	$content .= '<input type="submit" class="button" value="Send">';
	$content .= '</form><br>';

	$content .= '<a href="mess_history.php?id='.$to.'">Meldingshistorikk</a>';

		echo '<div id="user_misc_content">';
		echo MakeBox('Send melding', $content);
		echo '</div>';	

	include('design_user_foot.php');
	include('design_foot.php');
?>

<script type="text/javascript">
document.mess.msg.focus();
</script>