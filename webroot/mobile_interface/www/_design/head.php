<?
	include('top.php');
	if($l) echo '<script type="text/javascript" src="/_objects/ajax.js"></script>';
?>
</head>
<body>
	<div style="position: absolute; display: none; top: 0px; left: 0px;" id="hoverCraft"></div>
	<div style="position: relative; height: 131px; width: 982px; margin-bottom: 10px; background-image: url('/_objects/top.png'); background-repeat: no-repeat;">
		<a href="/main/start/"><img alt="" src="/_objects/top_logo.jpg" style="position: absolute; top: 6px; left: 5px;" /></a>
<?
	if($l) {
		$online = gettxt('stat_online');
		$online = explode(':', $online);
?>
<script type="text/javascript">
var omoTime;
function toggleFind(on) {
	try {
		document.getElementById('userfind').style.display = (on?'':'none');
		document.getElementById('userfind_inp').focus();
	} catch(Exception) {
		return;
	}
}
function checkTime(toggle) {
	window.clearTimeout(omoTime);
	if(!toggle) {
		omoTime = window.setTimeout('toggleFind(0)', 500);
	} else {
		omoTime = window.setTimeout('toggleFind(1)', 100);
	}
}
</script>
		<ul id="menu">
		<li><a href="/main/start/">start</a></li>
		<li><a href="/list/users/">sök</a></li>
		<li><a href="/forum/start/">forum</a></li>
		<li><a href="/main/thought/">tyck till</a></li>
		<li><a href="/main/public/">surfcafé</a></li>
		<li><a href="/text/radio/">webbradio</a></li>
		<li><a href="/main/faq/">hjälp & faq</a></li>
		<li><a href="/text/contact/">kontakt</a></li>
		</ul>
		<div style="position: absolute; top: 20px; left: 280px; border: 1px solid #b4b9b3;"><a href="#"><img src="/_objects/temp_ad.jpg" alt="Ad" style="margin: 1px;" /></a></div>
		<div class="cnt" style="position: absolute; top: 6px; left: 837px; width: 137px; height: 90px; background: url('/_objects/bg_online.png');">
		<table cellspacing="0" style="width: 127px;" class="cnti lft mrg">
		<tr><td><a href="<?=l('list', 'users', '1')?>">online</a></td><td class="bld rgt"><a href="<?=l('list', 'users', 1)?>"><?=@intval($online[0])?></a></td></tr>
		<tr><td><a href="<?=l('list', 'users', 'M')?>">män</a></td><td class="rgt"><a href="<?=l('list', 'users', 'M')?>" class="bld sexM"><?=@intval($online[1])?></a></td></tr>
		<tr><td><a href="<?=l('list', 'users', 'F')?>">kvinnor</a></td><td class="rgt"><a href="<?=l('list', 'users', 'F')?>" class="bld sexF"><?=@intval($online[2])?></a></td></tr>
		<tr><td colspan="2" class="cnt"><br /><a href="<?=l('list', 'users')?>">senast inloggade</a></td></tr>
		<tr><td colspan="2" class="bld cnt cur" onmouseover="checkTime(1);">snabbsök</td></tr>
		</table>
		</div>
		<form name="l" action="/list/userfind" method="post">
		<div id="userfind" onmouseover="checkTime(1);" style="display: none; position: absolute; top: 75px; left: 838px;"><input type="text" class="txt" id="userfind_inp" style="margin: 0 0 -4px 0;" onfocus="checkTime(1);" onblur="checkTime(0);" name="a" value="" /></div>
		</form>
		<form name="l" action="/member/logout" method="post">
		<div style="position: absolute; top: 103px; left: 958px;"><input type="image" src="/_objects/icon_logout.png" /></div>
		</form>
<?
	} else {
?>
		<ul id="menu">
		<li><a href="/main/start/">start</a></li>
		<li><a href="/main/faq/">hjälp</a></li>
		<li><a href="/member/register/">registrera dig!</a></li>
		</ul>
		<div style="position: absolute; top: 20px; left: 280px; border: 1px solid #b4b9b3;"><a href="#"><img src="/_objects/temp_ad.jpg" alt="Ad" style="margin: 1px;" /></a></div>
		<form name="l" action="/member/login" method="post">
		<div style="position: absolute; left: 770px; top: 8px;">
		<table cellspacing="0">
		<tr><td class="rgt" style="padding: 0 5px 3px 0;">alias: <input type="text" class="txt" name="a" style="margin-bottom: -4px;" value="frans" /></td></tr>
		<tr><td class="rgt" style="padding: 2px 5px 4px 0;">lösenord: <input type="password" class="pass" style="margin-bottom: -4px;" name="p" value="" /></td></tr>
		</table>
		</div>
		<script type="text/javascript">if(document.l.a.value.length > 0) document.l.p.focus(); else document.l.a.focus();</script>
		<input type="button" onclick="goLoc('<?=l('member', 'forgot')?>');" class="btn2_min" value="glömt?" style="position: absolute; top: 65px; left: 803px;" />
		<input type="submit" class="btn2_min" value="logga in!" style="position: absolute; top: 65px; left: 885px;" />
		</form>
<?
	}
?>
	</div>
	<div id="contentContainer">
		<div class="smallContent">
			<div class="smallHeader1"><h4>min meny</h4></div>
			<div class="smallFilled2" class="wht">
<ul class="user_menu">
<li><a href="/user/view/" class="wht none"><img src="/_objects/icon_mail.gif" alt="" />profil</a></li>
<li><a href="/user/gb/" class="wht none"><span class="i"><?=@intval($_SESSION['data']['offsets']['gb_offset'])?> <span class="bld about" id="Xg"></span></span><img src="/_objects/icon_mail.gif" alt="" />gästbok</a></li>
<li><a href="/user/gallery/" class="wht none"><span class="i"><?=@intval($_SESSION['data']['offsets']['gal_offset'])?></span><img src="/_objects/icon_mail.gif" alt="" />galleri</a></li>
<li><a href="/user/blog/" class="wht none"><span class="i"><?=@intval($_SESSION['data']['offsets']['blog_offset'])?></span><img src="/_objects/icon_mail.gif" alt="" />blogg</a></li>
<li><a href="/user/relations/" class="wht none"><span class="i"><?=@intval($_SESSION['data']['offsets']['rel_offset'])?> <span class="bld about" id="Xr"></span></span><img src="/_objects/icon_mail.gif" alt="" />vänner</a></li>
<li><a href="/user/mail/" class="wht none"><span class="i"><?=@intval($_SESSION['data']['offsets']['mail_offset'])?> <span class="bld about" id="Xm"></span></span><img src="/_objects/icon_mail.gif" alt="" />brev</a></li>
<li><a href="/member/settings/" class="wht none"><img src="/_objects/icon_mail.gif" alt="" />inställningar</a></li>
</ul>
</div>
<?
	if($l) {
?>
			<?=(!defined('NO_FOL')?'<div id="friendsOnline" style="height: 27px; width: 300px; overflow-y: hidden; margin-bottom: 10px;">
			<div class="smallHeader3"><b id="friendsTog" class="cur" onclick="friendsToggle();" style="line-height: 23px; float: right; margin-right: 5px;">öppna</b><h4 ondblclick="return friendsToggle();" class="cur">vänner online (<span id="friendsOnlineCount">0</span>)</h4></div>
				<div class="smallBoxed3" style="background: #fff;">
					<div id="friendsOnlineList"></div>
				</div>
			</div>':'')?>
			<?=(!defined('NO_FOL')?'<script type="text/javascript" src="/_objects/fol.js" /></script>':'')?>
			<script type="text/javascript">
			executeTimeout();
			executeData('<?=@$_SESSION['data']['cachestr']?>');
			<?=(!defined('NO_FOL')?'friendsSetPos(\''.@secureOUT($_COOKIE['friendsOnline']).'\');':'')?>
			</script>	
<?
	}
if(defined('U_GBWRITE')) echo '
<form name="msg" action="'.l('user', 'gbwrite', $s['id_id']).'main=1" method="post" onsubmit="if(trim(this.ins_cmt.value).length > 1) { return true; } else { alert(\'Felaktigt meddelande: Minst 2 tecken!\'); this.ins_cmt.select(); return false; }">
			<div class="smallHeader1"><h4>skriv gästboksinlägg</h4></div>
			<div class="smallFilled2 cnt pdg_t">
				<textarea class="txt" name="ins_cmt" style="width: 160px; height: 100px;"></textarea>
				<input type="submit" class="btn2_sml r" value="skicka!" style="margin-top: 5px;" /><br class="clr" />
			</div>
</form>
';
/*if(defined('U_GALLERY')) echo '
			<div class="smallHeader1"><h4>nytt foto</h4></div>
			<div class="smallFilled2 cnt pdg_t">
				<input type="button" onclick="makeUpload(\''.$l['id_id'].'\');" class="btn2_med r" value="ladda upp!" style="margin-top: 5px;" /><br class="clr" />
			</div>
';*/
	$contribute = $sql->queryLine("SELECT ".CH." u.id_id, u.u_alias, u.account_date, u.u_sex, u.u_birth, u.level_id, c.con_msg FROM {$t}contribute c LEFT JOIN {$t}user u ON u.id_id = c.con_user AND u.status_id = '1' WHERE c.con_onday = NOW() AND c.status_id = '1' LIMIT 1", 1);
	$gotcon = (!empty($contribute) && count($contribute))?true:false;
?>
			<div class="smallHeader2"><h4>titta här!</h4></div>
			<div class="smallBoxed2">
				<?=($gotcon?'<div style="width: 162px;" class="pdg">'.$user->getstring($contribute).'</div>':'')?>
				<div class="smallMiniFilled1">
					<p class="wht bld pdg" style="border-bottom: 1px solid #d0d3cf;"><?=($gotcon?secureOUT($contribute['con_msg'], 1):'Vidsom finns ej för idag.')?></a></p>
					<p class="wht pdg sml">Varje dag publicerar CitySurf en ny visdom, skicka in en du också!</p>
					<input type="button" class="btn2_sml r" onclick="makeContribution();" value="skriv!" /><br class="clr" />
				</div>
			</div>
		</div>