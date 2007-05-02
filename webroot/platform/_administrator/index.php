<?
session_start();
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");

	$msg = '';
	$tries = array("0", "1", "2", "B");

	if(!empty($_POST['u']) && !empty($_POST['u'])) {
		$try = mysql_query("SELECT * FROM {$t}admin WHERE BINARY user_user = '".secureINS($_POST['u'])."' AND user_pass = '".secureINS($_POST['p'])."' AND status_id = '1'");
		if (mysql_num_rows($try) == '1') {
			$row = mysql_fetch_assoc($try);

			$_SESSION['u_i'] = $row['main_id'];
			$_SESSION['u_u'] = $row['user_user'];
			$_SESSION['u_n'] = $row['user_name'];
			$_SESSION['u_c'] = $row['u_crew'];
			$_SESSION['u_p'] = $row['login_page'];
			$_SESSION['u_l'] = $row['login_good'] + 1;
			$_SESSION['u_a'] = array($row['city_id'], $row['pos_all']);
			mysql_query("UPDATE {$t}admin SET login_good = login_good + 1, u_date = NOW() WHERE BINARY user_user = '".secureINS($_POST['u'])."' AND user_pass = '".secureINS($_POST['p'])."'");
			$try = mysql_query("INSERT INTO {$t}adminlog SET login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."', l_type = '0', login_date = NOW(), login_name = '".secureINS($_POST['u'])."'");
			if(!$try) {
				mysql_query("UPDATE {$t}adminlog SET l_type = '0', login_pass = '', login_date = NOW(), login_name = '".secureINS($_POST['u'])."', login_pass = '' WHERE login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."'");
			}
			header("Location: frameset.php");
			exit;
		} else {
			$get_o = 0;
			$try = mysql_query("INSERT INTO {$t}adminlog SET login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."', l_type = '1', login_name = '".secureINS($_POST['u'])."', login_pass = '".secureINS($_POST['p'])."', login_date = NOW()");
			if(!$try) {

				$get = mysql_result(mysql_query("SELECT l_type FROM {$t}adminlog WHERE login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."'"), 0, 'l_type');
				$user = ($get == '0')?"'".secureINS($_POST['u'])."'":"CONCAT(login_name, ':', '".secureINS($_POST['u'])."')";
				$pass = ($get == '0')?"'".secureINS($_POST['p'])."'":"CONCAT(login_pass, ':', '".secureINS($_POST['p'])."')";

				$get_o = @array_search($get, $tries);
				if($get_o == '4') $do = 'B'; else $do = $tries[($get_o + 1)];
				mysql_query("UPDATE {$t}adminlog SET l_type = '$do', login_name = $user, login_pass = $pass, login_date = NOW() WHERE login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."'");
			}
			$msg = 'Fel. Du har '.((count($tries) - 1) - ($get_o + 1)).' försök kvar.';
		}
	}

	$check = mysql_query("SELECT * FROM {$t}adminlog WHERE login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."' LIMIT 1");
	$check = mysql_fetch_assoc($check);
	if($check['l_type'] == 'B' && !isset($_GET['o'])) {
		header("Location: ../");
		exit;
	}

	if(!empty($_SESSION['u_i'])) {
		mysql_query("UPDATE {$t}admin SET u_date = '' WHERE main_id = '".secureINS($_SESSION['u_i'])."'");
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
<script type="text/javascript">
tries = 0;
if(window != window.top) {
	window.top.location = window.location;
}
function testkey(e) {
	if (!e) var e=window.event;
	if (e.ctrlKey && e['keyCode'] == 13) testit();
}

function testit() {
	tries++;
	if(tries == '2' && document.l.u.value != '' && document.l.p.value != '') document.l.submit();
	return false;
}

function startCNT() {
	var d = document;
	if(d.getElementById('cnt_dwn') == null) logOUT(); else doCNT();		
}
function doCNT() {
	var d = document;
	var box = d.getElementById('cnt_dwn');
	if(box.width <= 1) { logOUT(); box.width = 0; } else { box.width = box.width - 2; window.setTimeout("doCNT()", 200); }
}
function logOUT() {
	document.location.href = '../';
	window.location.replace('../');
}
document.onkeydown = testkey;
</script>
</head>
<body<?=(!isset($_GET['o']))?' onload="startCNT();"':'';?> style="height: 90px; margin: 0; padding: 0; background: #000;"><img src="top_img.jpg" style="margin: 20px 0 20px 50px;" ondblclick="testit();" onclick="testit();" /><br />
<form name="l" action="./index.php" method="post" enctype="application/x-www-form-urlencoded">
<table style="margin: 0 0 0 90px;">
<tr><td colspan="2" class="txt_cnt"><img src="_img/rlr.gif" id="cnt_dwn" width="100" style="height: 7px; margin: 0 0 3px 0;"></td></tr>
<tr>
	<td style="padding-right: 1px;"><input type="text" class="inp_nrm" name="u" style="width: 80px;"><script type="text/javascript">document.l.u.focus();</script></td>
	<td><input type="password" class="inp_nrm" name="p" style="width: 80px;"></td>
</tr>
<?=(!empty($msg))?'<tr><td colspan="2" style="padding-top: 5px;" class="txt_look txt_bld txt_cnt">'.$msg.'</td></tr>':'';?>
</table>
</form>
</body>
</html>