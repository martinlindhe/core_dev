<?
session_start();
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	
	$msg = '';
	$tries = array("0", "1", "2", "3", "B");

	if(!empty($_POST['u']) && !empty($_POST['u'])) {
		$try = mysql_query("SELECT * FROM s_admin WHERE BINARY user_user = '".secureINS($_POST['u'])."' AND user_pass = '".secureINS($_POST['p'])."' AND status_id = '1'");
		if (mysql_num_rows($try) == '1') {
			$row = mysql_fetch_assoc($try);

			$_SESSION['u_i'] = $row['main_id'];
			$_SESSION['u_u'] = $row['user_user'];
			$_SESSION['u_n'] = $row['user_name'];
			$_SESSION['u_c'] = $row['u_crew'];
			$_SESSION['u_p'] = $row['login_page'];
			$_SESSION['u_l'] = $row['login_good'] + 1;
			$_SESSION['u_a'] = array($row['city_id'], $row['pos_all']);
			mysql_query("UPDATE s_admin SET login_good = login_good + 1, u_date = NOW() WHERE BINARY user_user = '".secureINS($_POST['u'])."' AND user_pass = '".secureINS($_POST['p'])."'");
			$try = mysql_query("INSERT INTO s_adminlog SET login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."', l_type = '0', login_date = NOW(), login_name = '".secureINS($_POST['u'])."'");
			if(!$try) {
				mysql_query("UPDATE s_adminlog SET l_type = '0', login_pass = '', login_date = NOW(), login_name = '".secureINS($_POST['u'])."', login_pass = '' WHERE login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."'");
			}
			header("Location: frameset.php");
			exit;
		} else {
			$get_o = 0;
			$try = mysql_query("INSERT INTO s_adminlog SET login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."', l_type = '1', login_name = '".secureINS($_POST['u'])."', login_pass = '".secureINS($_POST['p'])."', login_date = NOW()");
			if(!$try) {

				$get = mysql_result(mysql_query("SELECT l_type FROM s_adminlog WHERE login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."'"), 0, 'l_type');
				$user = ($get == '0')?"'".secureINS($_POST['u'])."'":"CONCAT(login_name, ':', '".secureINS($_POST['u'])."')";
				$pass = ($get == '0')?"'".secureINS($_POST['p'])."'":"CONCAT(login_pass, ':', '".secureINS($_POST['p'])."')";

				$get_o = @array_search($get, $tries);
				if($get_o == '4') $do = 'B'; else $do = $tries[($get_o + 1)];
				mysql_query("UPDATE s_adminlog SET l_type = '$do', login_name = $user, login_pass = $pass, login_date = NOW() WHERE login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."'");
			}
			$msg = 'Fel. Du har '.((count($tries) - 1) - ($get_o + 1)).' försök kvar.';
		}
	}

	$check = mysql_query("SELECT * FROM s_adminlog WHERE login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."' LIMIT 1");
	$check = mysql_fetch_assoc($check);
	if($check['l_type'] == 'B' && !isset($_GET['o'])) {
		header("Location: ../");
		exit;
	}

	if(!empty($_SESSION['u_i'])) {
		mysql_query("UPDATE s_admin SET u_date = '' WHERE main_id = '".secureINS($_SESSION['u_i'])."'");
		$_SESSION['u_i'] = '';
		$_SESSION['u_u'] = '';
		$_SESSION['u_n'] = '';
		$_SESSION['u_c'] = '';
		$_SESSION['u_p'] = '';
		$_SESSION['u_l'] = '';
		unset($_SESSION['u_i']); unset($_SESSION['u_u']); unset($_SESSION['u_n']);
		unset($_SESSION['u_c']); unset($_SESSION['u_p']); unset($_SESSION['u_l']);
		#@session_unset(); 
		#@session_destroy(); 
		#unset($_SESSION);
		#header("Location: ./"); 
		#exit;
		print '<script type="text/javascript">window.close();</script>';
		exit;
	}

	$adm_cnt = 10;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title><?=$title?> admin</title>
<style type="text/css">
/* TEXT */
.txt_cnt {text-align: center; }
.txt_look { color: #FF0066; }
.txt_bld { font-weight: bold; }
</style>

<script type="text/javascript">
if(window != window.top) {
	window.top.location = window.location;
}
function testit() {
	if(document.l.u.value != '' && document.l.p.value != '') document.l.submit();
	else return false;
}
</script>

</head>

<body>
	<img src="top_img.jpg" ondblclick="testit();" onclick="testit();" /><br/><br/>

	<form name="l" action="index.php" method="post">

	<table>
		<tr><td>Användare:</td><td><input type="text" class="inp_nrm" name="u" style="width: 120px;"></td></tr>
		<tr><td>Lösenord:</td><td><input type="password" class="inp_nrm" name="p" style="width: 120px;"></td></tr>
		<tr><td colspan="2"><input type="submit" value="Logga in"></td></tr>
		<? if (!empty($msg)) echo '<tr><td colspan="2" style="padding-top: 5px;" class="txt_look txt_bld txt_cnt">'.$msg.'</td></tr>'; ?>
	</table>

	</form>

<script type="text/javascript">document.l.u.focus();</script>

</body>
</html>