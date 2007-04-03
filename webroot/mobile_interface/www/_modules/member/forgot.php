<?
	if($l) reloadACT(l('main', 'start'));
	$complete = false;
	$error = array();
	$msg = array();
	$topic = 'forgotpass';
	if(!empty($_POST['do'])) {
		if(empty($_POST['a']) || (!valiField($_POST['a'], 'email') && !valiField($_POST['a'], 'user'))) {
			$error['a'] = true;
			$msg[] = 'Felaktigt alias eller e-post.';
		}
		if(empty($error['a'])) {
			$res = $sql->queryLine("SELECT status_id, u_alias, u_pass, u_email, id_id FROM {$t}user WHERE u_alias = '".secureINS($_POST['a'])."' LIMIT 1");
			if(!empty($res[0]) && ($res[0] == '1' || $res[0] == '3')) {
				$msg = sprintf(gettxt('email_forgot'), $res[1], $res[2]);
				doMail($res[3], 'Ditt alias: '.$res[1], $msg);
				$complete = true;
			} elseif($res[0] == 'F') {
				$start_code = $sql->queryResult("SELECT activate_code FROM {$t}userregfast WHERE id_id = '".$res[4]."' LIMIT 1");
				$msg = sprintf(gettxt('email_activate'), $start_code, substr(P2B, 0, -1).l('member', 'activate', secureOUT($res[3]), $start_code));
				doMail(secureOUT($res[3]), 'Din aktiveringskod: '.$start_code, $msg);
				$complete = true;
			} else {
				$res = $sql->queryLine("SELECT id_id, u_alias, level_id, u_pass, status_id, u_email FROM {$t}user WHERE u_email = '".secureINS($_POST['a'])."' LIMIT 1");
				if(!empty($res) && count($res) && !empty($res[4]) && $res[4] == '1') {
					$msg = sprintf(gettxt('email_forgot'), $res[1], $res[3]);
					doMail($res[5], 'Ditt alias: '.$res[1], $msg);
					$complete = true;
				} elseif($res[4] == 'F') {
					$start_code = $sql->queryResult("SELECT activate_code FROM {$t}userregfast WHERE id_id = '".$res[0]."' LIMIT 1");
					$msg = sprintf(gettxt('email_activate'), $start_code, substr(P2B, 0, -1).l('member', 'activate', secureOUT($res[5]), $start_code));
					doMail(secureOUT($res[5]), 'Din aktiveringskod: '.$start_code, $msg);
					$complete = true;
				} else {
					$error['a'] = true;
					$msg[] = 'Felaktigt alias eller e-post.';
				}
			}
		}
	}
	if($complete) {
		errorACT('Du har fått ett e-postmeddelande skickat till dig med dina användaruppgifter!<br /><input type="button" class="btn2_med r" onclick="goLoc(\''.l('main', 'start').'\');" value="till startsidan!" /><br class="clr" />');
	}
	include(DESIGN.'head.php');
?>
			<div id="mainContent">
			<div class="mainHeader2"><h4>glömt lösenordet</h4></div>
			<div class="mainBoxed2">
			<form name="l" method="post" action="<?=l('member', 'forgot')?>">
			<input type="hidden" name="do" value="1" />
				<p>
				<?=safeOUT(gettxt('register-forgot'))?>
				</p><br />
				<div style="padding: 5px;">
				<span class="bld<?=(isset($error['a']))?'_red':'';?>">alias eller e-post</span><br /><input type="text" class="txt" name="a" value="<?=(!empty($_POST['a']))?secureOUT($_POST['a']):'';?>" /><script type="text/javascript"><?=(empty($_POST) || !count($_POST))?'document.l.a.focus();':'';?></script>
<?=(!empty($msg) && count($msg))?'<br /><br /><span class="bld">OBS!</span><br />'.implode('<br />', $msg):'';?>
				<input type="submit" value="fortsätt" class="btn2_med r">
				</div>
			</form>
			</div>
			</div>
<?
	include(DESIGN.'foot_info.php');
	include(DESIGN.'foot.php');
?>
