<?
	require_once('config.php');

	if ($user->id) reloadACT('start.php');

	$complete = 0;
	$error = array();
	$msg = array();
	$topic = 'forgotpass';
	if (!empty($_POST['do'])) {
		if(empty($_POST['a']) || (!valiField($_POST['a'], 'email') && !valiField($_POST['a'], 'user'))) {
			$error['a'] = true;
			$msg[] = 'Felaktigt alias eller e-post.';
		}
		if(empty($error['a'])) {
			$res = $db->getOneRow("SELECT status_id, u_alias, u_pass, u_email, id_id FROM s_user WHERE u_alias = '".$db->escape($_POST['a'])."' LIMIT 1");
			if ($res && ($res['status_id'] == '1' || $res['status_id'] == '3')) {
				$msg = sprintf(gettxt('email_forgot'), $res['u_alias'], $res['u_pass']);
				$complete = doMail($res['u_email'], 'Ditt alias: '.$res['u_alias'], $msg);
			} else if($res['status_id'] == 'F') {
				$start_code = $db->getOneItem("SELECT activate_code FROM s_userregfast WHERE id_id = '".$res['id_id']."' LIMIT 1");
				$msg = sprintf(gettxt('email_activate'), $start_code, substr(P2B, 0, -1).l('member', 'activate', secureOUT($res['u_email']), $start_code));
				$complete = doMail(secureOUT($res['u_email']), 'Din aktiveringskod: '.$start_code, $msg);
			} else {
				$res = $db->getOneRow("SELECT id_id, u_alias, level_id, u_pass, status_id, u_email FROM s_user WHERE u_email = '".$db->escape($_POST['a'])."' LIMIT 1");
				if ($res && $res['status_id'] == '1') {
					$msg = sprintf(gettxt('email_forgot'), $res['u_alias'], $res['u_pass']);
					$complete = doMail($res['u_email'], 'Ditt alias: '.$res['u_alias'], $msg);
				} else if ($res['status_id'] == 'F') {
					$start_code = $db->getOneItem("SELECT activate_code FROM s_userregfast WHERE id_id = '".$res['id_id']."' LIMIT 1");
					$msg = sprintf(gettxt('email_activate'), $start_code, substr(P2B, 0, -1).l('member', 'activate', secureOUT($res['u_email']), $start_code));
					$complete = doMail(secureOUT($res['u_email']), 'Din aktiveringskod: '.$start_code, $msg);
				} else {
					$error['a'] = true;
					$msg[] = 'Felaktigt alias eller e-post.';
				}
			}
		}
	}
	if($complete === true) {
		errorACT('Du har fått ett e-postmeddelande skickat till dig med dina användaruppgifter!<br /><input type="button" class="btn2_min r" onclick="goLoc(\'index.php\');" value="fortsätt &raquo;" /><br class="clr" />');
	} else if ($complete === false) {
		errorACT('Problem med mailutsskick. Var god försök igen');
	}
	include(DESIGN.'head.php');
?>
	<div id="mainContent">

		<div class="bigHeader">glömt lösenordet</div>
		<div class="bigBody">
			<form name="forgot" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="do" value="1" />
				<?=secureOUT(gettxt('register-forgot'))?>
				<br/><br/>

				<b>alias eller e-post</b><br />
				<input type="text" class="txt" name="a" value="<?=(!empty($_POST['a']))?secureOUT($_POST['a']):'';?>" />
				<?
					if (!empty($msg) && count($msg)) echo '<br /><br /><span class="bld">OBS!</span><br />'.implode('<br />', $msg);
				?>
				<input type="submit" value="fortsätt" class="btn2_sml"/>

				<? if (empty($_POST)) echo '<script type="text/javascript">document.forgot.a.focus();</script>'; ?>
			</form>
		</div>

	</div>
<?
	include(DESIGN.'foot.php');
?>
