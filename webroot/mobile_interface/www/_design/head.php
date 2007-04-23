<?
	include('top.php');
	if($l) echo '<script type="text/javascript" src="/_objects/ajax.js"></script>';
?>
<script type="text/javascript">
var omoTime;
function toggleFind(on) {
	if (on) {
		show_element_by_name('userfind');
		document.getElementById('userfind_inp').focus();
	}
	else hide_element_by_name('userfind');
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
</head>

<body>
	<div id="hoverCraft" style="position: absolute; display: none; top: 0px; left: 0px;"></div>
	<div id="top">
		<a href="/main/start/"><img alt="" src="/_gfx/head_bg.png" id="top_img" /></a>

		<div id="top_ad"><a href="#"><img src="/_objects/temp_ad.jpg" alt="Ad" /></a></div>
<?
	if($l) {
		$online = gettxt('stat_online');
		$online = explode(':', $online);
?>
<!--
	<ul id="menu">
		<li><a href="/main/start/">START</a></li>
		<li><a href="/list/users/">SÖK</a></li>
		<li><a href="/forum/start/">FORUM</a></li>
		<li><a href="/main/thought/">TYCK TILL</a></li>
		<li><a href="/main/public/">SURFCAFE</a></li>
		<li><a href="/text/radio/">WEBBRADIO</a></li>
		<li><a href="/main/faq/">HJÄLP &amp; FAQ</a></li>
		<li><a href="/text/contact/">KONTAKT</a></li>
	</ul>

		<div id="logout">
			<form action="/member/logout" method="post">
			<input type="image" src="/_objects/icon_logout.png" />
			</form>
		</div>
-->
		<div id="top_menu">
			<map name="topmenu">
				<area href="/main/start/" alt="Start" coords="10,1,61,27">
				<area href="/list/users/" alt="Sök" coords="62,1,106,27">
				
				<area href="x" alt="prata privat" coords="107,1,211,27">				
				<area href="/main/public/" alt="Surfcafe" coords="212,1,298,27">

				<area href="/text/radio/" alt="Webbradio" coords="299,1,396,27">
				<area href="/main/faq/" alt="Hjälp &amp; FAQ" coords="398,1,502,27">
				<area href="/text/contact/" alt="Kontakt" coords="504,1,578,27">
				
				<area href="/member/logout" alt="Logga ut" coords="953,6,969,22">				

			</map>
			<img src="/_gfx/head_menu_raw.png" usemap="#topmenu" alt="Menu"/>
		</div>
-->

		<div id="top_online">
		<table summary="" cellspacing="0">
		<tr><td><a href="<?=l('list', 'users', '1')?>">online</a></td><td class="bld rgt"><a href="<?=l('list', 'users', 1)?>"><?=@intval($online[0])?></a></td></tr>
		<tr><td><a href="<?=l('list', 'users', 'M')?>">killar</a></td><td class="rgt"><a href="<?=l('list', 'users', 'M')?>" class="bld sexM"><?=@intval($online[1])?></a></td></tr>
		<tr><td><a href="<?=l('list', 'users', 'F')?>">tjejer</a></td><td class="rgt"><a href="<?=l('list', 'users', 'F')?>" class="bld sexF"><?=@intval($online[2])?></a></td></tr>
		<tr><td colspan="2" class="cnt"><br /><a href="<?=l('list', 'users')?>">senast inloggade</a></td></tr>
		<tr><td colspan="2" class="bld cnt cur" onmouseover="checkTime(1);">snabbsök</td></tr>
		</table>
		</div>

		<div id="userfind" style="display: none" onmouseover="checkTime(1);">
			<form action="/list/userfind" method="post">
			<input type="text" class="txt" id="userfind_inp" onfocus="checkTime(1);" onblur="checkTime(0);" name="a" value="" />
			</form>
		</div>


<?
	} else {
		//ej inloggad meny: todo!!!1
?>
		<ul id="menu">
		<li><a href="/main/start/">start</a></li>
		<li><a href="/main/faq/">hjälp</a></li>
		<li><a href="/member/register/">registrera dig!</a></li>
		</ul>

		<form name="l" action="/member/login" method="post">
		<div id="top_online_off">
		<table cellspacing="0" summary="">
		<tr><td class="top">alias: <input type="text" class="txt" name="a" style="margin-bottom: -4px;" /></td></tr>
		<tr><td class="bottom">lösenord: <input type="password" class="pass" style="margin-bottom: -4px;" name="p" /></td></tr>
		</table>
		</div>
		<script type="text/javascript">if(document.l.a.value.length > 0) document.l.p.focus(); else document.l.a.focus();</script>
		<input type="button" onclick="goLoc('<?=l('member', 'forgot')?>');" class="btn2_min" value="glömt?" id="forgot" />
		<input type="submit" class="btn2_min" value="logga in!" id="login" />
		</form>
<?
	}
	
	//vänstermenyn här:
	
?>
	</div>
	<div id="contentContainer">
		<div class="smallContent">

			<div class="leftMenuHeader">min meny</div>

			<div class="leftMenuBodyGreen">
				<ul class="user_menu">
					<li><a href="/user/view/" class="wht none"><img src="/_gfx/icon_profil.png" alt="" />profil</a></li>
					<li><a href="/user/gb/" class="wht none"><span class="i"><?=@intval($_SESSION['data']['offsets']['gb_offset'])?> <span class="bld about" id="Xg"></span></span><img src="/_gfx/icon_gb.png" alt="" />gästbok</a></li>
					<li><a href="/user/mail/" class="wht none"><span class="i"><?=@intval($_SESSION['data']['offsets']['mail_offset'])?> <span class="bld about" id="Xm"></span></span><img src="/_gfx/icon_mail.png" alt="" />brev</a></li>
					<li><a href="/user/blog/" class="wht none"><span class="i"><?=@intval($_SESSION['data']['offsets']['blog_offset'])?></span><img src="/_gfx/icon_blog.png" alt="" />blogg</a></li>
					<li><a href="/user/relations/" class="wht none"><span class="i"><?=@intval($_SESSION['data']['offsets']['rel_offset'])?> <span class="bld about" id="Xr"></span></span><img src="/_gfx/icon_friends.png" alt="" />vänner</a></li>
					<li><a href="/user/gallery/" class="wht none"><span class="i"><?=@intval($_SESSION['data']['offsets']['gal_offset'])?></span><img src="/_gfx/icon_gallery.png" alt="" />galleri</a></li>
					<li><a href="/member/settings/" class="wht none"><img src="/_gfx/icon_settings.png" alt="" />inställningar</a></li>
					<li>&nbsp;</li>
				</ul>
			</div>
			<div class="leftMenuFooter"></div>

<div id="quickchat_indicator" style="display: none;">
	<div class="smallHeader3">
		<h4 class="cur">Någon vill prata med dig!</h4>
	</div>
	<br/><br/>
</div>

<?
	if($l) {
?>
			<?=(!defined('NO_FOL')?'<div id="friendsOnline">
			<div class="smallHeader3"><b id="friendsTog" class="cur" onclick="friendsToggle();">öppna</b><h4 ondblclick="return friendsToggle();" class="cur">vänner online (<span id="friendsOnlineCount">0</span>)</h4></div>
				<div class="smallBoxed3">
					<div id="friendsOnlineList"></div>
				</div>
			</div>':'')?>
			<?=(!defined('NO_FOL')?'<script type="text/javascript" src="/_objects/fol.js"></script>':'')?>
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
				<textarea class="txt msgWrite" name="ins_cmt"></textarea>
				<input type="submit" class="btn2_sml r" value="skicka!" /><br class="clr" />
			</div>
</form>';

	$contribute = $sql->queryLine("SELECT ".CH." u.id_id, u.u_alias, u.account_date, u.u_sex, u.u_birth, u.level_id, c.con_msg FROM {$t}contribute c LEFT JOIN {$t}user u ON u.id_id = c.con_user AND u.status_id = '1' WHERE c.con_onday = NOW() AND c.status_id = '1' LIMIT 1", 1);
	$gotcon = (!empty($contribute) && count($contribute))?true:false;
?>

	<div class="leftMenuHeader">dagens visdom</div>
	<div class="leftMenuBodyGray">
		<?=($gotcon?'<div class="smallBoxed pdg">'.$user->getstring($contribute).'</div>':'')?>
		<div class="leftMenuBodyGray">
			<p class="wht bld pdg brd_btm"><?=($gotcon?secureOUT($contribute['con_msg'], 1):'Visdom finns ej för idag.')?></p>
			<p class="wht pdg sml">Varje dag publicerar CitySurf en ny visdom, skicka in en du också!</p>
			<input type="button" class="btn2_sml r" onclick="makeContribution();" value="skriv" /><br class="clr" />
		</div>
	</div>
	<div class="leftMenuFooterGray"></div>
</div>