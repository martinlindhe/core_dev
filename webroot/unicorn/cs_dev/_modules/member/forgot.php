<?
	if($l) reloadACT(l('main', 'start'));
	$complete = 0;
	$error = array();
	$msg = array();
	$topic = 'forgotpass';
	if(!empty($_POST['do'])) {
		if(empty($_POST['a']) || (!valiField($_POST['a'], 'email') && !valiField($_POST['a'], 'user'))) {
			$error['a'] = true;
			$msg[] = 'Felaktigt alias eller e-post.';
		}
		if(empty($error['a'])) {
			$res = $sql->queryLine("SELECT status_id, u_alias, u_pass, u_email, id_id FROM s_user WHERE u_alias = '".secureINS($_POST['a'])."' LIMIT 1");
			if(!empty($res[0]) && ($res[0] == '1' || $res[0] == '3')) {
				$msg = sprintf(gettxt('email_forgot'), $res[1], $res[2]);
				$complete = doMail($res[3], 'Ditt alias: '.$res[1], $msg);
			} elseif($res[0] == 'F') {
				$start_code = $sql->queryResult("SELECT activate_code FROM s_userregfast WHERE id_id = '".$res[4]."' LIMIT 1");
				$msg = sprintf(gettxt('email_activate'), $start_code, substr(P2B, 0, -1).l('member', 'activate', secureOUT($res[3]), $start_code));
				$complete = doMail(secureOUT($res[3]), 'Din aktiveringskod: '.$start_code, $msg);
			} else {
				$res = $sql->queryLine("SELECT id_id, u_alias, level_id, u_pass, status_id, u_email FROM s_user WHERE u_email = '".secureINS($_POST['a'])."' LIMIT 1");
				if(!empty($res) && count($res) && !empty($res[4]) && $res[4] == '1') {
					$msg = sprintf(gettxt('email_forgot'), $res[1], $res[3]);
					$complete = doMail($res[5], 'Ditt alias: '.$res[1], $msg);
				} elseif($res[4] == 'F') {
					$start_code = $sql->queryResult("SELECT activate_code FROM s_userregfast WHERE id_id = '".$res[0]."' LIMIT 1");
					$msg = sprintf(gettxt('email_activate'), $start_code, substr(P2B, 0, -1).l('member', 'activate', secureOUT($res[5]), $start_code));
					$complete = doMail(secureOUT($res[5]), 'Din aktiveringskod: '.$start_code, $msg);
				} else {
					$error['a'] = true;
					$msg[] = 'Felaktigt alias eller e-post.';
				}
			}
		}
	}
	if($complete === true) {
		errorACT('Du har fått ett e-postmeddelande skickat till dig med dina användaruppgifter!<br /><input type="button" class="btn2_min r" onclick="goLoc(\''.l('main', 'start').'\');" value="fortsätt &raquo;" /><br class="clr" />');
	} else if ($complete === false) {
		errorACT('Problem med mailutsskick. Var god försök igen');
	}
	include(DESIGN.'head.php');
?>
	<div id="mainContent">

		<div class="bigHeader">glömt lösenordet</div>
		<div class="bigBody">
			<form name="forgot" method="post" action="<?=l('member', 'forgot')?>">
			<input type="hidden" name="do" value="1" />
				<?=safeOUT(gettxt('register-forgot'))?>
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
