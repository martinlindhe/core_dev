<?
session_start();
#ob_start();
#    ob_implicit_flush(0);
#    ob_start('ob_gzhandler');
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}

	$page = mysql_query("SELECT login_page FROM s_admin WHERE main_id = '".secureINS($_SESSION['u_i'])."' LIMIT 1");
	if(mysql_num_rows($page) > 0) {
		$page = mysql_result($page, 0, 'login_page');
	} else $page = 'gb';


	$pages = getEnumOptions($t.'admin', 'login_page');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title><?=$title?>AMS</title>
	<link rel="stylesheet" href="default_adm.css" type="text/css">
<script type="text/javaScript" src="fnc_adm.js"></script>
<script language="javascript" type="text/javascript">
isLoaded = true;
	var xmlGet = false;

	function reloadit() {
		window.setTimeout('reloadit()', 15000);
		getINFO();
	}
	window.setTimeout('reloadit()', 15000);

	function trim(str) {
		return str.replace(/^\s*|\s*$/g,"");
	}

	function DoCallback(url, type) {
		try {
			xmlGet = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				xmlGet = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (E) {
				xmlGet = false;
			}
		}
		if(!xmlGet && typeof XMLHttpRequest != 'undefined') {
			try {
				xmlGet = new XMLHttpRequest();
			} catch (e) {
				xmlGet = false;
			}
		}
		if(xmlGet) {
			//xmlGet.setRequestHeader('Content-type', 'application/x-www-form-urlencoded;charset=iso-8891-1');
			xmlGet.onreadystatechange = processGet;
			xmlGet.open("GET", url, true);
			xmlGet.send(null);
		} else return false;
	}

	function processGet() {
		if(xmlGet.readyState && xmlGet.readyState == 4 && xmlGet.status == 200) {
			outputINFO(xmlGet);
		}
	}

	function getINFO() {
		document.getElementById('reloader').className = 'txt_chead';
		DoCallback('foot_info.php');
	}

	function outputINFO(xmlGet) {
		if(xmlGet) {
			execINFO(trim(xmlGet.responseText));
		}
	}

	function makeit(title, cha_c, cha_id, tho_c, cmt_c, pht_c, mv_c, uph_c, scc_c, scg_c, str) {
		string = new Array();
		if(cha_c > 0) { string[string.length] = "<a target=\"<?=FRS?>head\" href=\"javascript:makePop('user_chat.php?id="+cha_id+"', 'MSG_"+cha_id+"', '', '', 1); reloadFoot();\" target=\"<?=FRS?>main\" class=\"wht\">" + 'CHAT <span class="txt_look">'+cha_c+'</span></a>'; }
		if(tho_c > 0) { string[string.length] = '<a href="obj.php?t&status=thought" target="<?=FRS?>main" class="wht">TYCK TILL <span class="txt_look">'+tho_c+'</span></a>'; }
		if(cmt_c > 0) { string[string.length] = '<a href="obj.php?t&status=cmt" target="<?=FRS?>main" class="wht">VIMMELKOMMENTAR <span class="txt_look">'+cmt_c+'</span></a>'; }
		if(pht_c > 0) { string[string.length] = '<a href="obj.php?t&status=photo" target="<?=FRS?>main" class="wht">FOTOALBUM <span class="txt_look">'+pht_c+'</span></a>'; }
		if(mv_c > 0) { string[string.length] = '<a href="obj.php?t&status=cmtmv" target="<?=FRS?>main" class="wht">FILMKOMMENTAR <span class="txt_look">'+mv_c+'</span></a>'; }
		if(uph_c > 0) { string[string.length] = '<a href="obj.php?t&status=img" target="<?=FRS?>main" class="wht">PROFILBILD <span class="txt_look">'+uph_c+'</span></a>'; }
		if(scc_c > 0) { string[string.length] = '<a href="obj.php?t&status=scc" target="<?=FRS?>main" class="wht">VISDOM <span class="txt_look">'+scc_c+'</span></a>'; }
		if(scg_c > 0) { string[string.length] = '<a href="obj.php?t&status=scc" target="<?=FRS?>main" class="wht">INGEN VISDOM IDAG</a>'; }
		if(parent.<?=FRS?>head && parent.<?=FRS?>head.isLoaded) {
			parent.<?=FRS?>head.document.getElementById('top_lnks').innerHTML = string.join(' | ');
		}

		top.document.title = title + '<?=$title?>AMS';
		document.getElementById('onlstr').innerHTML = str;
		document.getElementById('reloader').className = '';

	}

	function execINFO(info) {
		info = info.split(';;');
		if(info.length == 11) {
			makeit(info[0], info[1], info[2], info[3], info[4], info[5], info[6], info[7], info[8], info[9], info[10]);
		} else if(info && info == '.') {
			logout();
		}
	}

	function logout() {
		document.location.href = './';
	}
</script>
<base target="_parent">
</head>
<body onload="getINFO();" style="margin: 2px 10px 0 10px; padding: 0; background: #000;">
<form name="page_edit" action="top.php" method="post" target="<?=FRS?>head">
	<input type="hidden" name="page" value="<?=$page?>">
</form>
<table width="100%">
<tr>
	<td class="txt_wht"><a href="javascript:getINFO();" target="_self" id="reloader" onclick="this.blur();" title="Ladda om">LADDA OM</a> | <b><?=strtoupper($_SESSION['u_n'])?></b> - <b><?=$_SESSION['u_l']?></b></td>
	<td class="wht" align="right">ONLINE: <span class="bld" id="onlstr"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="./?check">LOGGA UT</a></td>
</tr>
</table>
</body>
</html>