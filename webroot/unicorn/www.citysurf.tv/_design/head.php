<?
	include('top.php');
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

function blockRightClick(event) {
	if (!event.srcElement) return false;
  var tname=event.srcElement.tagName;
  if (event.button==2 && tname=="IMG") pressed="picture";
  if (pressed=="picture") window.alert("!");
  pressed=0;
}
</script>
</head>

<body>
	<div id="hoverCraft" style="position: absolute; z-index: 10; display: none; top: 0px; left: 0px;"></div>
	<div id="top">
		<div id="top_logo"></div>
		<div id="top_bg"></div>

		<div id="top_ad"><a href="#"><img src="/_gfx/ban/2_728x90.gif" alt="Ad" /></a></div>

		<div id="top_border"><img src="/_gfx/themes/head_border.png" alt=""/></div>

		<ul id="menu_main">
			<li><a href="/main/start/">start</a> | </li>
			<li><a href="/list/users/">leta</a> | </li>
			<li><a href="/forum/start/">forum</a> | </li>
			<li><a href="/main/thought/">tyck till</a> | </li>
			<li><a href="/main/surfcafe/">surfcafé</a> | </li>
			<!--<li><a href="/text/radio/">webbradio</a> | </li>-->
			<li><a href="/main/faq/">hjälp &amp; faq</a> | </li>
			<li><a href="/text/contact/">kontakt</a> | </li>
			<li><a href="/member/logout/">logga ut</a></li>
		</ul>

		<ul id="menu_user">
<?
	$menu_brev = ' brev '.@intval($_SESSION['data']['offsets']['mail_offset']);
	$menu_gb = ' gästbok '.@intval($_SESSION['data']['offsets']['gb_offset']);
	$menu_relations = ' relationer '.@intval($_SESSION['data']['offsets']['rel_offset']);

	if (@$l['id_id']) {
		//kolla om det finns olästa mail
		$chk = getUnreadMailCount();
		if ($chk) $menu_brev = ' <span style="color:#ff0000">brev '.$chk.'</span>';
		
		//olästa gästboksinlägg?
		$chk = gbCountUnread();
		if ($chk) $menu_gb = ' <span style="color:#ff0000">gästbok '.$chk.'</span>';
		
		$chk = getRelationRequestsToMe();
		if ($chk) $menu_relations = ' <span style="color:#ff0000">relationer '.count($chk).'</span>';
	}
?>
			<li><img align="absmiddle" src="/_gfx/icon_profil.png" alt="" /><a href="/user/view/">min profil</a> &nbsp;</li>
			<li><img align="absmiddle" src="/_gfx/icon_gb.png" alt="" /><a href="/user/gb/"><?=$menu_gb?></a> &nbsp;</li>
			<li><img align="absmiddle" src="/_gfx/icon_mail.png" alt="" /><a href="/user/mail/"><?=$menu_brev?></a> &nbsp;</li>
			<li><img align="absmiddle" src="/_gfx/icon_blog.png" alt="" /><a href="/user/blog/">blogg <?=@intval($_SESSION['data']['offsets']['blog_offset'])?></a> &nbsp;</li>
			<li><img align="absmiddle" src="/_gfx/icon_friends.png" alt="" /><a href="/user/relations/"><?=$menu_relations?></a> &nbsp;</li>
			<li><img align="absmiddle" src="/_gfx/icon_gallery.png" alt="" /><a href="/user/gallery/">galleri <?=@intval($_SESSION['data']['offsets']['gal_offset'])?></a> &nbsp;</li>
			<li><img align="absmiddle" src="/_gfx/icon_settings.png" alt="" /><a href="/member/settings/">inställningar</a> &nbsp;</li>
			<li><img align="absmiddle" src="/_gfx/icon_settings.png" alt="" /><a href="/main/upgrade/">uppgradera</a> &nbsp;</li>
		</ul>
	</div>

	<div id="contentContainer"><!-- holder for all content except footer -->
		<div id="leftMenu">

			<div id="quickchat_indicator" style="display: none;">
				<div class="quickchat_blink">
					<h4 class="cur">Någon vill prata med dig!</h4>
				</div>
				<br/>
			</div>
<?
	$contribute = $sql->queryLine("SELECT u.id_id, u.u_alias, u.account_date, u.u_sex, u.u_birth, u.level_id, c.con_msg FROM {$t}contribute c LEFT JOIN {$t}user u ON u.id_id = c.con_user AND u.status_id = '1' WHERE c.con_onday = NOW() AND c.status_id = '1' LIMIT 1", 1);
	$gotcon = (!empty($contribute) && count($contribute))?true:false;
?>
			<div class="smallHeader">se hit!</div>
			<div class="smallBody">
				<div class="bld">
				<?
					if ($contribute) {
						echo $user->getstring($contribute).'<br/><br/>';
						echo stripslashes($contribute['con_msg']);
					} else {
						echo 'Om du ser något fel, klicka på "tyck till" uppe i toppmenyn.';
					}
				?>
				</div>
<?
$isAdmin = (@$_SESSION['data']['level_id'] == '10'?true:false);
	if ($isAdmin) {
		echo '<input type="button" class="btn2_sml r" onclick="makeContribution();" value="skriv" /><br class="clr" />';
	}
?>
			</div><br/>



<?
	$online = gettxt('stat_online');
	$online = explode(':', $online);
?>
	<div class="smallHeader">inloggade</div>
	<div class="smallBody">

		<table summary="" cellspacing="0">
			<tr><td width="80"><a href="<?=l('list', 'users', '1')?>">Online</a></td><td><a href="<?=l('list', 'users', 1)?>"><?=@intval($online[0])?></a></td></tr>
			<tr><td><a href="<?=l('list', 'users', 'M')?>">Killar</a></td><td><a href="<?=l('list', 'users', 'M')?>"><?=@intval($online[1])?></a></td></tr>
			<tr><td><a href="<?=l('list', 'users', 'F')?>">Tjejer</a></td><td><a href="<?=l('list', 'users', 'F')?>"><?=@intval($online[2])?></a></td></tr>
		</table>
		<br/>
		<a href="<?=l('list', 'users')?>">Senast inloggade</a><br/>
		<a href="/list/userfind/1">Slumpa</a><br/>
		<div onmouseover="checkTime(1);">Snabbsök</div>

		<div id="userfind" style="display: none" onmouseover="checkTime(1);">
			<form action="/list/userfind" method="post">
			<input type="text" class="txt" id="userfind_inp" onfocus="checkTime(1);" onblur="checkTime(0);" name="a" value="" />
			</form>
		</div>
	</div><br/>
<?
	if ($l && !defined('NO_FOL')) {
?>
		<div id="friendsOnline">
			<div class="smallHeader" onclick="friendsToggle();">
				vänner online (<span id="friendsOnlineCount">0</span>)
			</div>
			<div id="friendsOnlineList" class="smallBody" style="display:none;"></div>
		</div>
		<br class="clr"/>
		<script type="text/javascript">
		executeTimeout();
		executeData('<?=@$_SESSION['data']['cachestr']?>');
		<?= (!empty($_COOKIE['friendsOnline'])?"friendsToggle();":'')?>
		</script>
<? }
?>

		</div>	<!-- end leftMenu -->

<?
//visar bred sida på startsida & sök
	if (basename($_SERVER['REQUEST_URI']) == 'start') {		//fulhax av martin /hide
		echo '<div id="bigContent">';
	} else {
		echo '<div id="mainContent">';
	}
?>