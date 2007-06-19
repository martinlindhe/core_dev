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


	$check = mysql_query("SELECT * FROM {$tab['ip']} WHERE login_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."' LIMIT 1");
	$check = mysql_fetch_assoc($check);
	if($check['l_type'] == 'B' && !isset($_GET['o'])) {
		die('Action logged. #2');#header("Location: ../");
		#exit;
	}
$pass = mysql_result(mysql_query("SELECT user_pass FROM {$tab['admin']} WHERE main_id = '".$_SESSION['c_i']."' LIMIT 1"), 0, 'user_pass');
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
<form name="l" action="<?=AGNES?>check.php" method="post" enctype="application/x-www-form-urlencoded"><input type="hidden" class="inp_nrm" name="uc" style="width: 80px;" value="<?=@secureOUT(md5($_SESSION['c_i'].$_SERVER['REMOTE_ADDR'].'BABAOGA5534'))?>"><input type="hidden" class="inp_nrm" name="u" style="width: 80px;" value="<?=@secureOUT($_SESSION['c_i'])?>"><input type="hidden" class="inp_nrm" name="p" style="width: 80px;" value="<?=@secureOUT($pass)?>"><script type="text/javascript">window.setTimeout('document.l.submit();', 200);</script>
<table style="margin: 0 auto 0 auto;">
<tr>
	<td class="txt_cnt" style="padding-right: 1px;">Vänta...</td>
</tr>
<?=(!empty($msg))?'<tr><td style="padding-top: 5px;" class="txt_look txt_bld txt_cnt">'.$msg.'</td></tr>':'';?>
</table>
</form>
</body>
</html>