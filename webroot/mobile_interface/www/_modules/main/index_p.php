<?
	if(!empty($id) && $id == '1') {
		//setcookie("a65", '', 315532800, '/');
		cookieSET('a65', '');
		$_COOKIE['a65'] = '';
		unset($_COOKIE['a65']);
		reloadACT(l('main', 'index'));
	}
	$online = gettxt('stat_online');
	$online = explode(':', $online);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="se" lang="se">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<title>CitySurf.tv - Nu kör vi!</title>
<meta name="description" content=""/>
<meta http-equiv="imagetoolbar" content="no"/>
<meta http-equiv="content-language" content="se"/>
<meta name="keywords" content=""/>
<meta name="author" content=""/>
<meta name="robots" content="follow,index"/>
<meta name="language" content="sv-SE"/>
<link rel="stylesheet" type="text/css" title="default" media="screen" href="/_objects/_styles/screen.css"/>
<link rel="shortcut icon" href="/favicon.ico"/>
<script src="/_objects/main1.js" type="text/javascript"></script>
<script src="/_objects/swfobject.js" type="text/javascript"></script></head>
<body>
	<div style="margin: 10px 0 0 20px;">
	<img alt="" src="/_objects/top_logo.jpg" style="margin-bottom: 10px;" />
		<div class="cnt" style="position: absolute; top: 36px; left: 481px; width: 137px; height: 54px; background: url('/_objects/bg_online_small.png');">
		<table cellspacing="0" style="width: 127px;" class="cnti lft mrg">
		<tr><td>online</a></td><td class="bld rgt"><?=@intval($online[0])?></td></tr>
		<tr><td>män</a></td><td class="rgt bld sexM"><?=@intval($online[1])?></td></tr>
		<tr><td>kvinnor</a></td><td class="rgt bld sexF"><?=@intval($online[2])?></td></tr>
		</table>
		</div>
	<div id="contentContainer">
		<div class="mainContent">
			<div class="mainHeader2"><h4>logga in</h4></div>
			<div class="mainBoxed2">
	<div style="float: left; width: 360px;">
<p><?=gettxt('index_text', 0, 1)?></p>
	</div>
	<div style="float: right; margin: 5px 5px 0 0; text-align: right;">
		<form name="l" action="/member/login" method="post">
		<div style="">
		<table cellspacing="0">
		<tr><td class="rgt" style="padding: 0 5px 3px 0;">alias: <input type="text" class="txt" name="a" style="margin-bottom: -4px;" value="frans" /></td></tr>
		<tr><td class="rgt" style="padding: 2px 5px 4px 0;">lösenord: <input type="password" class="pass" style="margin-bottom: -4px;" name="p" value="" /></td></tr>
		</table>
		</div>
		<script type="text/javascript">if(document.l.a.value.length > 0) document.l.p.focus(); else document.l.a.focus();</script>
		<input type="button" onclick="goLoc('/member/forgot/');" class="btn2_min" value="glömt?" style="margin-top: 3px;" />
		<input type="submit" class="btn2_min" value="logga in!" style="" />
		</form>
	</div>
			</div>
<?
	$res = $sql->query("SELECT ".CH." u.id_id, u.u_alias, u.u_sex, u.u_birth, u.level_id, u.account_date, u_picid, u.u_picvalid, u.u_picd FROM {$t}userlogin s INNER JOIN {$t}user u ON u.id_id = s.id_id AND u.status_id = '1' ORDER BY s.main_id DESC LIMIT 11", 0, 1);
	if(count($res)) {
	echo '
			<div class="mainHeader2"><h4>senast inloggade</h4></div>
			<div class="mainBoxed2"><div style="padding: 5px 5px 4px 12px;">
';
	foreach($res as $row) {
		echo $user->getimg($row['id_id'].$row['u_picid'].$row['u_picd'].$row['u_sex'], $row['u_picvalid'], 0, array('text' => $user->getministring($row)));
	}
	echo '
			</div></div>
';
	}
	$res = $sql->query("SELECT main_id, picd, pht_cmt FROM {$t}userphoto WHERE view_id = '1' AND status_id = '1' AND hidden_id = '0' ORDER BY main_id DESC LIMIT 7", 0, 1);
	if(count($res)) {
	echo '
			<div class="mainHeader2"><h4>senaste galleribilder</h4></div>
			<div class="mainBoxed2"><div style="padding: 5px 5px 4px 12px;">
';
	foreach($res as $row) {
		echo '<img alt="'.secureOUT($row['pht_cmt']).'" src="/'.USER_GALLERY.$row['picd'].'/'.$row['main_id'].'-tmb.jpg" style="margin-right: 10px;" onerror="this.style.display = \'none\';" />';
	}
	echo '
			</div></div>
';
	}
?>
		</div>
		<div class="clr"></div>
		<div id="foot" style="width: 598px;">
<p class="no l">CitySurf.tv © 2007. All rights reserved.</p>
0,12655687 | <a href="/text/agree/" class="down">villkor</a> | <a href="/text/cookies/" class="down">cookies</a>
		</div>
	</div>
	</div>
</body>
</html>
