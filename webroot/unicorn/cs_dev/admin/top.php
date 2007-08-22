<?
	require_once('find_config.php');

	$adm_cnt = gettxt('admcnt');
	if(!empty($_GET['k']) && !empty($_GET['k1']) && $isCrew) {
		$sql->queryUpdate("UPDATE s_admin SET kick_now = '1' WHERE main_id = '".secureINS($_GET['k'])."' LIMIT 1");
		echo '<script type="text/javascript">alert(\''.secureOUT($_GET['k1']).' kickad.\');</script>';
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title><?=$title?> admin</title>
	<link rel="stylesheet" href="default_adm.css" type="text/css">
<script type="text/javaScript" src="fnc_adm.js"></script>
<script type="text/javascript">
isLoaded = true;
function openWin(url) {
	window.open(url, 'ban', 'width=420, height=350, location=no, menubar=no, scrollbars=no, resizable=no, status=no, toolbar=no');
}
//document.onmousedown = doRET;
var count = 0;
function startCNT() {
	var d = parent.head.document;
	if(d.getElementById('cnt_dwn') == null) logOUT(); else doCNT();		
}
function doRET() {
	var d = parent.head.document;
	var box = d.getElementById('cnt_dwn');
	if(!box) logOUT();
	box.width = '100';
}
function doCNT() {
	var d = parent.head.document;
	var box = d.getElementById('cnt_dwn');
	if(box.width <= 1) { logOUT(); box.width = 0; } else { box.width--; window.setTimeout("doCNT()", 4000); }
//	if(box.innerHTML <= 1) { logOUT(); box.innerHTML = 0; } else { box.innerHTML--; window.setTimeout("doCNT()", 1000); }
}
function logOUT() {
	document.location.href = './';
	window.location.replace('./');
}
function reloadFoot() {
	if(parent.foot && parent.foot.isLoaded) {
		window.setTimeout('parent.foot.getINFO();', 900);
	}
}
</script>
<script type="text/javascript">
function hide(obj) {
	if(obj.parentNode.className != 'menu')
		obj.parentNode.className = 'menu';
}
function show(obj) {
	if(obj.parentNode.className != 'menusel')
		obj.parentNode.className = 'menusel';
}
<?
	if($isCrew) echo 'var buttons = new Array(\'user\', \'obj\', \'news\', \'search\', \'stat\', \'log\');'."\n";
	else {
		$arr = array();
		$arr[] = 'log';
		$sites = explode(',', $_SESSION['u_a'][1]);
		foreach($sites as $val) {
			if($val != 'poll') {
				if(strpos($val, '_') !== false) {
					$val = explode('_', $val);
					$val = $val[0];
				}
				$arr[] = $val;
			}
		}
		$arr = array_unique($arr);
		echo 'var buttons = new Array(\''.implode('\',\'', $arr).'\');'."\n";
	}
?>
function show_active(name) {
	if(name == 'changes' || name == 'settings') name = 'log';
	if(name == 'pics') {
		//window.setTimeout("d.getElementById('pics').href = '#';", 2000);
		d.getElementById('pics').innerHTML = '<span class="txt_look">AKTIV</span>';
	} else {
		//d.getElementById('pics').href = 'agnes.php';
		if(d.getElementById('pics')) d.getElementById('pics').innerHTML = 'VIMMEL<span style="font-size: 8px;">/</span>FILM';
	}
	submenu(name);
}
var d = document;
function submenu(id) {
	for(i = 0; i < buttons.length; i++) {
		if(id == buttons[i]) {
			hide(d.getElementById(id));
		} else {
			show(d.getElementById(buttons[i]));
		}
	}
	if(d.getElementById(id)) d.getElementById(id).blur();
}
function getname(str) {
	str = str.split("/");
	str = str[str.length - 1];
	str = str.split(".");
	str = str[0];
	if(!str) str = 'gb';
	return str;
}
</script>
</head>

<body style="background: #000">
<table cellspacing="0" cellpadding="0">
<tr>
<?
	if($isCrew || !empty($_SESSION['u_a'][1])) {
		if($isCrew || strpos($_SESSION['u_a'][1], 'obj') !== false) echo '<td><a href="obj.php" target="'.FRS.'main" onclick="show_active(this.id)" id="obj">OBJEKT IN</a></td>';
		if($isCrew || (strpos($_SESSION['u_a'][1], 'news') !== false && !empty($menu_S))) echo '<td class="menu"><a href="'.($isCrew?'news.php':$menu_S).'" target="'.FRS.'main" onclick="show_active(this.id)" id="news">INFO UTÅT</a></td>';
		if($isCrew || strpos($_SESSION['u_a'][1], 'user') !== false) echo '<td><a href="user.php" target="'.FRS.'main" onclick="show_active(this.id)" id="user">USER</a></td>';
		if($isCrew || strpos($_SESSION['u_a'][1], 'search') !== false) echo '<td><a href="search.php" target="'.FRS.'main" onclick="show_active(this.id)" id="search">SÖK</a></td>';
		if($isCrew || strpos($_SESSION['u_a'][1], 'stat') !== false) echo '<td><a href="stat.php" target="'.FRS.'main" onclick="show_active(this.id)" id="stat">STATISTIK</a></td>';
		if($isCrew || strpos($_SESSION['u_a'][1], 'log') !== false) echo '<td><a href="changes.php" target="'.FRS.'main" onclick="show_active(this.id)" id="log">LOGG</a></td>';
	}
?>
</tr>
</table>
<div style="position: absolute; top: 2px; left: 630px;" class="wht bld" id="top_lnks"></div>
<script type="text/javascript">show_active(getname(parent.<?=FRS?>main.location.href));</script>
</body>
</html>
