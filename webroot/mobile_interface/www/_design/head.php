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
		<map name="topmap">
			<area href="/member/logout" shape="rect" coords="956,102,972,118" alt="Logga ut">
		</map>
		<img alt="" src="/_gfx/head_bg.png" id="top_img" usemap="#topmap" />

		<div id="top_ad"><a href="#"><img src="/_objects/temp_ad.jpg" alt="Ad" /></a></div>

		<ul id="top_menu">
			<li><a href="/main/start/">start</a> | </li>
			<li><a href="/list/users/">sök</a> | </li>
			<li><a href="/forum/start/">forum</a> | </li>
			<li><a href="/main/thought/">tyck till</a> | </li>
			<li><a href="/main/surfcafe/">surfcafe</a> | </li>
			<li><a href="/text/radio/">webbradio</a> | </li>
			<li><a href="/main/faq/">hjälp &amp; faq</a> | </li>
			<li><a href="/text/contact/">kontakt</a></li>
		</ul>
<?
	if($l) {
		$online = gettxt('stat_online');
		$online = explode(':', $online);
?>
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
		//ej inloggad
?>
		<div id="top_online_off">
			<form name="l" action="/member/login" method="post">

			<table summary="" cellspacing="0">
				<tr><td>alias:</td><td><input type="text" class="txt" style="width: 70px" name="a" /></td></tr>
				<tr><td>lösenord:</td><td><input type="password" class="pass" style="width: 70px" name="p" /></td></tr>
				<tr><td colspan="2">
					<input type="submit" class="btn2_min" value="logga in" id="login" />
				</td></tr>
			</table>

			<script type="text/javascript">if(document.l.a.value.length > 0) document.l.p.focus(); else document.l.a.focus();</script>
			</form>
		</div>
<?
	}
?>
	</div>

	<div id="contentContainer">
		<div id="leftMenu">

			<div class="leftMenuHeader">min meny</div>

			<div class="leftMenuBodyGreen">
				<ul class="user_menu">
					<li><a href="/user/view/" class="wht none"><img src="/_gfx/icon_profil.png" alt="" />profil</a></li>
					<li><a href="/user/gb/" class="wht none"><span class="i"><?=@intval($_SESSION['data']['offsets']['gb_offset'])?> <span class="bld about" id="Xg"></span></span><img src="/_gfx/icon_gb.png" alt="" />gästbok</a></li>
					<li><a href="/user/mail/" class="wht none"><span class="i"><?=@intval($_SESSION['data']['offsets']['mail_offset'])?> <span class="bld about" id="Xm"></span></span><img src="/_gfx/icon_mail.png" alt="" />brev</a></li>
					<li><a href="/user/blog/" class="wht none"><span class="i"><?=@intval($_SESSION['data']['offsets']['blog_offset'])?></span><img src="/_gfx/icon_blog.png" alt="" />blogg</a></li>
					<li><a href="/user/relations/" class="wht none"><span class="i"><?=@intval($_SESSION['data']['offsets']['rel_offset'])?> <span class="bld about" id="Xr"></span></span><img src="/_gfx/icon_friends.png" alt="" />relationer</a></li>
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
	<? if (!defined('NO_FOL')) { ?>
			<div id="friendsOnline">
				<div class="leftMenuHeaderRed" onclick="friendsToggle();">
					vänner online (<span id="friendsOnlineCount">0</span>)
				</div>
				<div class="leftMenuBodyRed">
					<div id="friendsOnlineList" style="display:none;"></div>
				</div>
			</div>
			<script type="text/javascript" src="/_objects/fol.js"></script>
			<script type="text/javascript">
			executeTimeout();
			executeData('<?=@$_SESSION['data']['cachestr']?>');
			<?= (!empty($_COOKIE['friendsOnline'])?"friendsToggle();":'')?>
			</script>
	<? } ?>
<?
	}

	$contribute = $sql->queryLine("SELECT u.id_id, u.u_alias, u.account_date, u.u_sex, u.u_birth, u.level_id, c.con_msg FROM {$t}contribute c LEFT JOIN {$t}user u ON u.id_id = c.con_user AND u.status_id = '1' WHERE c.con_onday = NOW() AND c.status_id = '1' LIMIT 1", 1);
	$gotcon = (!empty($contribute) && count($contribute))?true:false;
?>
			<div class="leftMenuHeader">titta hit</div>
			<div class="leftMenuBodyWhite">
				<?=($gotcon?'<div class="pdg">'.$user->getstring($contribute).'</div>':'')?>
				<div class="leftMenuBodyWhite">
					<p class="bld pdg brd_btm"><?=($gotcon?secureOUT($contribute['con_msg'], 1):'Visdom finns ej för idag.')?></p>
					<p class="pdg sml">Varje dag publicerar CitySurf en ny visdom, skicka in en du också!</p>
					<input type="button" class="btn2_sml r" onclick="makeContribution();" value="skriv" /><br class="clr" />
				</div>
			</div>
			<div class="leftMenuFooterGray"></div>

		</div>	<!-- end smallContent -->
	</div>
	
	<!-- holder for all content except footer -->
	<!-- <div style="background-color: #aaee00"> -->
	<div>
