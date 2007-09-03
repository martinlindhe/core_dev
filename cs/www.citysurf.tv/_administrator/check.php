<?
session_start();
    ob_start();
    ob_implicit_flush(0);
    ob_start('ob_gzhandler');
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
if(empty($_SESSION['c_i']) || !is_md5($_SESSION['c_i'])) {
die('Action logged.');
}
	$msg = '';
	$tries = array("0", "1", "2", "B");
	if(!empty($_SESSION['c_i']) && !empty($_POST['u']) && $_POST['u'] == $_SESSION['c_i']) {
		$try = mysql_query("SELECT id_id, status_id, u_pass FROM {$tab['user']} WHERE id_id = '".secureINS($_SESSION['c_i'])."'");
		$try = @mysql_fetch_assoc($try);
		if(!empty($try) && count($try) && ($try['status_id'] == '1' || $try['status_id'] == '4')) {
			$trys = $try['status_id'];
			$try2 = mysql_query("SELECT * FROM {$tab['admin']} WHERE main_id = '".secureINS($_SESSION['c_i'])."' AND status_id = '1'");
			if (mysql_num_rows($try2) == '1') {
				$row = mysql_fetch_assoc($try2);
				if(1) { #$try['u_pass'] == $row['user_pass']) {
					$_SESSION['u_i'] = $row['main_id'];
					$_SESSION['u_u'] = $row['user_user'];
					$_SESSION['u_n'] = $row['user_name'];
					$_SESSION['u_c'] = $row['u_crew'];
					$_SESSION['u_p'] = $row['login_page'];
					$_SESSION['u_l'] = $row['login_good'] + 1;
					$_SESSION['u_a'] = array($row['city_id'], $row['pos_all']);
					mysql_query("UPDATE {$tab['admin']} SET login_good = login_good + 1, u_date = NOW() WHERE BINARY user_user = '".secureINS($row['user_user'])."' AND user_pass = '".secureINS($row['user_pass'])."'");
					$try = mysql_query("INSERT INTO {$tab['ip']} SET login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."', l_type = '0', login_date = NOW(), login_name = '".secureINS($row['user_user'])."'");
					if(!$try) {
						mysql_query("UPDATE {$tab['ip']} SET l_type = '0', login_pass = '', login_date = NOW(), login_name = '".secureINS($row['user_user'])."', login_pass = '' WHERE login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."'");
					}
					if(!empty($_GET['id']))
						header("Location: frameset.php?id=".$_GET['id']);
					else {
						if($trys == '4')
							errorNEW('Här är det ännu viktigare att inte röra något. Allt är skarpt och lämna inga fotavtryck. ALLT LOGGAS! Vänta...', 'frameset.php', 'KOM IHÅG!');
						else
							header("Location: frameset.php");
					}
					exit;
				}
			}
		}
		#$try = mysql_query("INSERT INTO {$tab['ip']} SET login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."', l_type = '1', login_name = '".secureINS($_POST['u'])."', login_pass = '".secureINS($_POST['p'])."', login_date = NOW()");
		$try = mysql_query("INSERT INTO {$tab['ip']} SET login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."', l_type = '1', login_date = NOW()");
		if(!$try) {
			$get = mysql_result(mysql_query("SELECT l_type FROM {$tab['ip']} WHERE login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."'"), 0, 'l_type');
			$user = ($get == '0')?"'".secureINS($_POST['u'])."'":"CONCAT(login_name, ':', '".secureINS($_POST['u'])."')";
			$pass = ($get == '0')?"'".secureINS($_POST['p'])."'":"CONCAT(login_pass, ':', '".secureINS($_POST['p'])."')";
			#$user = '';
			#$pass = '';
			$get_o = @array_search($get, $tries);
			if($get_o == '4') $do = 'B'; else $do = $tries[($get_o + 1)];
			mysql_query("UPDATE {$tab['ip']} SET l_type = '$do', login_name = $user, login_pass = $pass, login_date = NOW() WHERE login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."'");
		}
		$msg = 'Fel. Du har '.((count($tries) - 1) - ($get_o + 1)).' försök kvar.';
	}

	$check = mysql_query("SELECT * FROM {$tab['ip']} WHERE login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."' LIMIT 1");
	$check = mysql_fetch_assoc($check);
	if($check['l_type'] == 'B' && !isset($_GET['o'])) {
		die('Action logged. #2');#header("Location: ../");
		#exit;
	}

	$adm_cnt = 10;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title><?=$title?>AMS</title>
	<style type="text/css">
/* MAIN */
* { margin: 0; padding: 0; border: 0; font-size: 10px; font-family: Verdana, Arial, sans-serif; }
body { color: #525252; background: url('_img/main_bg.jpg'); background-color: #FFF; padding-bottom: 20px; }
.ibody { background: #FFF; margin: 0; padding: 0; width: 535px; overflow-x: hidden; }
img { border: 0; margin: 0; }
table { border: 0; border-collapse: collapse; border-spacing: 0; }
td { vertical-align: top; }
input.inp_nrm, select.inp_nrm { border: 1px solid #999999; width: 160px; height: 22px; padding: 4px 0 0 2px; }
form { margin: 0; padding: 0; }

/* TEXT */
.txt_cnt {text-align: center; }
.txt_look { color: #FF0066; }
.txt_bld { font-weight: bold; }
	</style>
</head>
<body style="height: 90px; margin: 0; text-align: center; padding: 200px 0 0 0; color: #FFF; background: #000;"><img src="top_img.gif" style="margin: 20px auto 20px auto;" /><br />
<form name="l" action="./check.php?<?=(@$_GET['id']?'id='.$_GET['id']:'')?>" method="post" enctype="application/x-www-form-urlencoded"><input type="hidden" class="inp_nrm" name="u" style="width: 80px;" value="<?=@secureOUT($_SESSION['c_i'])?>"><script type="text/javascript">window.setTimeout('document.l.submit();', 200);</script>
<table style="margin: 0 auto 0 auto;">
<tr>
	<td class="txt_cnt" style="padding-right: 1px;">Vänta...</td>
</tr>
<?=(!empty($msg))?'<tr><td style="padding-top: 5px;" class="txt_look txt_bld txt_cnt">'.$msg.'</td></tr>':'';?>
</table>
</form>
</body>
</html>