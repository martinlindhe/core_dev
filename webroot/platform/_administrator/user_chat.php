<?
session_start();
ob_start();
    ob_implicit_flush(0);
    ob_start('ob_gzhandler');
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}
	$sql = &new sql();
	$colours = array("0" => '#525252', "1" => "#CC0000", "2" => "#336699");
	$got = false;
	if(!empty($_GET['id']) && is_md5($_GET['id'])) {
		$row = $sql->queryLine("SELECT * FROM {$tab['tab']}admin WHERE main_id = '".secureINS($_GET['id'])."' LIMIT 1", 1);
		if(!empty($row) && count($row)) {
			$got = true;
			$u_id = $row['main_id'];
		}
	}

	if(!$got) {
exit;
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title><?=secureOUT(ucfirst($row['user_name']))?> | <?=$title?>AMS</title>
	<link rel="stylesheet" href="default_adm.css" type="text/css">
	<meta http-equiv="imagetoolbar" content="no">
<script type="text/javascript">
  var gotFocus = new Boolean(true);
	//window.parent = false;
	//window.opener = false;
	window.onfocus = function() {
		textFocus();
		gotFocus = new Boolean(true);
	}
  
	window.onblur = function() {
		gotFocus = new Boolean(false);
	}

	function textFocus() {
		document.getElementById('msgTextbox').focus();
	}

	var id = '<?=$u_id?>';
	var oc = '<?=(empty($colours[$u_id]))?' color: '.$colours[0].';':' color: '.$colours[$u_id].';';?>';
	var sc = '<?=(empty($colours[$_SESSION['u_i']]))?' color: '.$colours[0].';':' color: '.$colours[$_SESSION['u_i']].';';?>';
	var xmlRet = false;
	var xmlGet = false;

	function reloadit() {
		window.setTimeout('reloadit()', 7500);
		GetMessages();
	}
	window.setTimeout('reloadit()', 7500);

	function trim(str) {
		return str.replace(/^\s*|\s*$/g,"");
	}

	function DoCallback(url) {
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
			xmlGet.onreadystatechange = ProcessChange;
			xmlGet.open("GET", url, false);
				xmlGet.send(null);
			
			return xmlGet;
		} else return false;
	}

	function ProcessChange() {
		if(xmlGet.readyState == 4 && xmlGet.status == 200) {

		}		
	}

	function GetMessages() {
		xmlGet = DoCallback('user_chat_relay.php?id=' + id);
		if(xmlGet) {
			var md5 = xmlGet.responseText.substr(0, 5);
			var array = xmlGet.responseText.split(md5);
			if(xmlGet.responseText && trim(xmlGet.responseText).length > 0 && !gotFocus) {
				//top.document.title = 'Oläst meddelande |' + 'CHAT';
				self.focus();
			}
			for(i = 1; i < array.length; i++) {
				if(trim(array[i]).length > 0) {
					var aliasLength = new Number(array[i].substr(0, 2));
					var alias = array[i].substr(2, aliasLength);
					var dateLength = array[i].substr(aliasLength + 2, 2);
					var datetime = array[i].substr(aliasLength + 4, dateLength);
					var msg = array[i].substr(parseInt(aliasLength) + parseInt(dateLength) + 4, array[i].length - parseInt(aliasLength) - parseInt(dateLength));
					SetMessage(msg, datetime, alias);
				}
			}

		}
	}

	function SendChatMessage() {
		if(trim(document.getElementById("msgTextbox").value).length > 0) {
			var msg = escape(document.getElementById("msgTextbox").value.substr(0, 250));
			xmlGet = DoCallback('user_chat_relay.php?id=' + id + '&msg=' + msg);
			if(xmlGet) {
				document.getElementById("msgTextbox").value = '';
				var md5 = xmlGet.responseText.substr(0, 5);
				var array = xmlGet.responseText.split(md5);
				for(i = 1; i < array.length; i++) {
					if(trim(array[i]).length > 0) {
						var aliasLength = new Number(array[i].substr(0, 2));
						var alias = array[i].substr(2, aliasLength);
						var dateLength = array[i].substr(aliasLength + 2, 2);
						var datetime = array[i].substr(aliasLength + 4, dateLength);
						var msg = array[i].substr(parseInt(aliasLength) + parseInt(dateLength) + 4, array[i].length - parseInt(aliasLength) + parseInt(dateLength));
						SetMessage(msg, datetime, alias);
					}
				}
			}
		}
		document.getElementById('msgTextbox').focus();
	}

	function SetMessage(msg, datetime, alias) {
		window.frames["messages"].document.getElementById("messageDiv").innerHTML += FormatMessage(msg, datetime, alias);
		window.frames["messages"].scrollTo(0, window.frames["messages"].document.body.scrollHeight);
	}
  
	function FormatMessage(msg, datetime, alias) {
		if(alias == '<?=secureOUT(ucfirst(rawurlencode($_SESSION['u_n'])))?>')
			var buffer = "<div style='margin: 5px;"+ sc + "'><b>"+ unescape(alias) + "</b><em> - " + unescape(datetime) + "</em><br>" + unescape(msg) + "</div>";
		else
			var buffer = "<div style='margin: 5px;"+ oc + "'><b>"+ unescape(alias) + "</b><em> - " + unescape(datetime) + "</em><br>" + unescape(msg) + "</div>";
		return buffer;
	}

	function ActivateByKey(e) {
		if(!e) var e=window.event;
		if (e['keyCode'] == 27) {
			window.close();
			return true;
		}
		if(e.ctrlKey && e['keyCode'] == 13) {
			SendChatMessage();
			return false;
		} else
		if(e['keyCode'] == 13) {
			SendChatMessage();
			return false;
		}
	}
	var lim = 250;
	function fixlimit(obj, id) {
		if(obj.value.length > lim) {
			obj.value = obj.value.substr(0, lim);
		}
		document.getElementById(id).innerHTML = lim - parseInt(obj.value.length);
	}

document.onkeydown = ActivateByKey;
</script>
	<style type="text/css">
/*
	a { color: #000; text-decoration: none; }
	a:hover { text-decoration: underline; }
	form { margin: 0; padding: 0; }
	table { border: 0; border-collapse: collapse; }
	body {
		background: #FFF;
		color: #000;
		margin: 0px;
		padding: 0;
	}
	.wht { color: #FFF; }
	.bld { font-weight: bold; }
	img.brd { border: 1px solid #000; }
	* { font-family: Verdana, Tahoma, Arial, Helvetica, Sans-Serif; font-size: 10px; }
*/
	</style>
	</head>
	<body style="height: 100%; width: 100%; background-color: transparent;">
<table cellspacing="0" style="width: 100%; height: 100%;">
<tr><td style="padding: 10px 0 5px 12px; height: 14px;" class="bld">Till: <span style="<?=(empty($colours[$u_id]))?'color: '.$colours[0].';':'color: '.$colours[$u_id].';';?>"><?=secureOUT(ucfirst($row['user_name']))?></span></td></tr>
<tr>
	<td style="height: 100%; text-align: center;"><center>
		<table width="92%" style="height: 91%; margin-top: 5px;">
		<tr>
			<td width="100%" style="height: 100%; border: 1px solid #999999;"><iframe name="messages" src="user_chat_window.php?id=<?=$u_id?>" allowtransparency="true" frameborder="no" scrolling="auto" style="width: 100%; height: 100%; min-height: 230px;"></iframe></td>
		</tr>
		</table>
	</center></td>
</tr>
<tr style="height: 80px;">
	<td align="left" valign="bottom" style="padding: 0 0 5px 15px; height: 80px;">
		<table cellspacing="0">
		<tr>
			<td style="padding-left: 3px;" colspan="2"><b>Meddelande:</b>&nbsp;&nbsp;&nbsp;(<span id="cha_lim">250</span> tecken kvar)</td>
		</tr>
		<tr>
			<td style="padding: 3px 0 3px 3px;"><textarea name="msgTextbox" class="inp_nrm" style="width: 208px; height: 50px; overflow-y: auto;" id="msgTextbox" onblur="gotFocus = false;" onfocus="top.document.title = '<?=secureOUT(ucfirst($row['user_name']))?> | CHAT';" style="height: 50px; width: 312px;" onkeyup="fixlimit(this, 'cha_lim');" onkeydown="fixlimit(this, 'cha_lim');" onchange="fixlimit(this, 'cha_lim');"></textarea></td>
			<td style="padding: 3px 0 0 0;" valign="top"><input type="button" class="inp_orgbtn" onclick="javascript:SendChatMessage();" accesskey="s" style="margin-top: 1px; width: 50px; height: 50px;" value="Skicka" /></td>
		</tr>
		</table>
	</td>
</tr>
</table>
</body>
</html>