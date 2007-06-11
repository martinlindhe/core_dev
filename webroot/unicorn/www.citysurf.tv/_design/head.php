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

		<div id="top_ad"><a href="#"><img src="/_objects/temp_ad.jpg" alt="Ad"  /></a></div>

		<div id="top_border"><img src="/_gfx/themes/head_border.png" alt=""/></div>

		<div id="top_logout"><a href="/member/logout"><img src="/_gfx/themes/head_logout.png" alt=""/></a></div>

		<ul id="menu_main">
			<li><a href="/main/start/">start</a> | </li>
			<li><a href="/list/users/">leta</a> | </li>
			<li><a href="/forum/start/">forum</a> | </li>
			<li><a href="/main/thought/">tyck till</a> | </li>
			<li><a href="/main/surfcafe/">surfcafé</a> | </li>
			<!--<li><a href="/text/radio/">webbradio</a> | </li>-->
			<li><a href="/main/faq/">hjälp &amp; faq</a> | </li>
			<li><a href="/text/contact/">kontakt</a></li>
		</ul>

		<ul id="menu_user">
			<li><img align="absmiddle" src="/_gfx/icon_profil.png" alt="" /><a href="/user/view/">min profil</a> &nbsp;</li>
			<li><img align="absmiddle" src="/_gfx/icon_gb.png" alt="" /><a href="/user/gb/">gästbok <?=@intval($_SESSION['data']['offsets']['gb_offset'])?></a> &nbsp;</li>
			<li><img align="absmiddle" src="/_gfx/icon_mail.png" alt="" /><a href="/user/mail/">brev <?=@intval($_SESSION['data']['offsets']['mail_offset'])?></a> &nbsp;</li>
			<li><img align="absmiddle" src="/_gfx/icon_blog.png" alt="" /><a href="/user/blog/">blogg <?=@intval($_SESSION['data']['offsets']['blog_offset'])?></a> &nbsp;</li>
			<li><img align="absmiddle" src="/_gfx/icon_friends.png" alt="" /><a href="/user/relations/">relationer <?=@intval($_SESSION['data']['offsets']['rel_offset'])?></a> &nbsp;</li>
			<li><img align="absmiddle" src="/_gfx/icon_gallery.png" alt="" /><a href="/user/gallery/">galleri <?=@intval($_SESSION['data']['offsets']['gal_offset'])?></a> &nbsp;</li>
			<li><img align="absmiddle" src="/_gfx/icon_settings.png" alt="" /><a href="/member/settings/">inställningar</a> &nbsp;</li>
			<li><img align="absmiddle" src="/_gfx/icon_settings.png" alt="" /><a href="/main/upgrade/">uppgradera</a> &nbsp;</li>
		</ul>
	</div>

	<div id="contentContainer"><!-- holder for all content except footer -->
		<div id="leftMenu">

<?
	if (!empty($own)) {
		if(@$l['id_id'] == @$s['id_id']) {
			if(@intval($info['gb_offset'][1]) != @intval($_SESSION['data']['offsets']['gb_offset'])) $_SESSION['data']['offsets']['gb_offset'] = @intval($info['gb_offset'][1]);
			if(@intval($info['gal_offset'][1]) != @intval($_SESSION['data']['offsets']['gal_offset'])) $_SESSION['data']['offsets']['gal_offset'] = @intval($info['gal_offset'][1]);
			if(@intval($info['blog_offset'][1]) != @intval($_SESSION['data']['offsets']['blog_offset'])) $_SESSION['data']['offsets']['blog_offset'] = @intval($info['blog_offset'][1]);
			if(@intval($info['rel_offset'][1]) != @intval($_SESSION['data']['offsets']['rel_offset'])) $_SESSION['data']['offsets']['rel_offset'] = @intval($info['rel_offset'][1]);
			if(@intval($info['mail_offset'][1]) != @intval($_SESSION['data']['offsets']['mail_offset'])) $_SESSION['data']['offsets']['mail_offset'] = @intval($info['mail_offset'][1]);
		}
	}
	
	if (!empty($s) && $s['id_id']) {
		echo '<div class="smallHeader">profil</div>';
		echo '<div class="smallBody">';
			echo '• civilstånd: '.(!empty($info['det_civil'][1])?secureOUT($info['det_civil'][1]):@$det_type[$info['det_civil_type'][1]]).'<br />';
			echo '• attityd: '.@$info['det_attitude'][1].'<br />';
			echo '• musik: '.@$info['det_music'][1].'<br />';
			echo '• vill ha: '.@$info['det_wants'][1].'<br />';
			echo '• alkohol: '.@$info['det_alcohol'][1].'<br />';
			echo '• tobak: '.@$info['det_tobacco'][1].'<br />';
			echo '• sexliv: '.@$info['det_sex'][1].'<br />';
			echo '• barn: '.@$info['det_children'][1].'<br />';
			echo '• längd: '.@$info['det_length'][1].'<br />';
		echo '</div><br/>';
	}

	if (empty($own) && !empty($s) && $s['id_id']) {
		$txt = array('age' => 'Ålder', 'sex' => 'Sexliv', 'children' => 'Barn', 'music' => 'Musiksmak', 'tobacco' => 'Tobak', 'alcohol' => 'Alkohol', 'wants' => 'Vill ha', 'civil' => 'Civilstatus', 'attitude' => 'Attityd');
		$myinfo = $user->getcontent($l['id_id'], 'user_head');
		
		echo '<div class="smallHeader">matchmaking</div>';
		echo '<div class="smallBody">';
			echo '<table summary="" cellspacing="0" id="diverse">';
			echo '<tr>';
					echo '<td class="cnt" colspan="3"><div class="usr">jag vs. '.$user->getstring($s, '', array('noage' => 1)).'</div></td>';
				echo '</tr>';
				echo '<tr title="'.$txt['age'].'">';
					echo '<td class="rgt"><div>'.$user->doage($l['u_birth']).' år</div></td>';
					echo '<td class="cnt">•</td>';
					echo '<td class="lft"><div>'.$user->doage($s['u_birth']).' år</div></td>';
				echo '</tr>';

				if (@$det_type[$info['det_civil_type'][1]] && @$det_type[$myinfo['det_civil_type'][1]]) {
					echo '<tr title="'.$txt['civil'].'">';
					echo '<td class="rgt"><div>'.(!empty($myinfo['det_civil'][1])?secureOUT($myinfo['det_civil'][1]):(@$det_type[$myinfo['det_civil_type'][1]]?@$det_type[$myinfo['det_civil_type'][1]]:'-')).'</div></td>';
					echo '<td class="cnt">•</td>';
					echo '<td class="lft"><div>'.(!empty($info['det_civil'][1])?secureOUT($info['det_civil'][1]):(@$det_type[$info['det_civil_type'][1]]?@$det_type[$info['det_civil_type'][1]]:'-')).'</div></td>';
					echo '</tr>';
				}
				if (@$info['det_attitude'][1] && @$myinfo['det_attitude'][1]) {
					echo '<tr title="'.$txt['attitude'].'">';
					echo '<td class="rgt"><div>'.(@$myinfo['det_attitude'][1]?@$myinfo['det_attitude'][1]:'-').'</div></td>';
					echo '<td class="cnt">•</td>';
					echo '<td class="lft"><div>'.(@$info['det_attitude'][1]?@$info['det_attitude'][1]:'-').'</div></td>';
					echo '</tr>';
				}
				if (@$info['det_wants'][1] && @$myinfo['det_wants'][1]) {
					echo '<tr title="'.$txt['wants'].'">';
					echo '<td class="rgt"><div>'.(@$myinfo['det_wants'][1]?@$myinfo['det_wants'][1]:'-').'</div></td>';
					echo '<td class="cnt">•</td>';
					echo '<td class="lft"><div>'.(@$info['det_wants'][1]?@$info['det_wants'][1]:'-').'</div></td>';
					echo '</tr>';
				}
				if (@$info['det_alcohol'][1] && @$myinfo['det_alcohol'][1]) {
					echo '<tr title="'.$txt['alcohol'].'">';
					echo '<td class="rgt"><div>'.(@$myinfo['det_alcohol'][1]?@$myinfo['det_alcohol'][1]:'-').'</div></td>';
					echo '<td class="cnt">•</td>';
					echo '<td class="lft"><div>'.(@$info['det_alcohol'][1]?@$info['det_alcohol'][1]:'-').'</div></td>';
					echo '</tr>';
				}
				if (@$myinfo['det_tobacco'][1] && @$info['det_tobacco'][1]) {
					echo '<tr title="'.$txt['tobacco'].'">';
					echo '<td class="rgt"><div>'.(@$myinfo['det_tobacco'][1]?@$myinfo['det_tobacco'][1]:'-').'</div></td>';
					echo '<td class="cnt">•</td>';
					echo '<td class="lft"><div>'.(@$info['det_tobacco'][1]?@$info['det_tobacco'][1]:'-').'</div></td>';
					echo '</tr>';
				}
				if (@$myinfo['det_children'][1] && @$info['det_children'][1]) {
					echo '<tr title="'.$txt['children'].'">';
					echo '<td class="rgt"><div>'.(@$myinfo['det_children'][1]?@$myinfo['det_children'][1]:'-').'</div></td>';
					echo '<td class="cnt">•</td>';
					echo '<td class="lft"><div>'.(@$info['det_children'][1]?@$info['det_children'][1]:'-').'</div></td>';
					echo '</tr>';
				}
				if (@$myinfo['det_music'][1] && @$info['det_music'][1]) {
					echo '<tr title="'.$txt['music'].'">';
					echo '<td class="rgt"><div>'.(@$myinfo['det_music'][1]?@$myinfo['det_music'][1]:'-').'</div></td>';
					echo '<td class="cnt">•</td>';
					echo '<td class="lft"><div>'.(@$info['det_music'][1]?@$info['det_music'][1]:'-').'</div></td>';
					echo '</tr>';
				}
				if (@$info['det_sex'][1] && @$myinfo['det_sex'][1]) {
					echo '<tr title="'.$txt['sex'].'">';
					echo '<td class="rgt"><div>'.(@$myinfo['det_sex'][1]?@$myinfo['det_sex'][1]:'-').'</div></td>';
					echo '<td class="cnt">•</td>';
					echo '<td class="lft"><div>'.(@$info['det_sex'][1]?@$info['det_sex'][1]:'-').'</div></td>';
					echo '</tr>';
				}
				echo '</table>';
			echo '</div><br/>';
	}
?>
			<div id="quickchat_indicator" style="display: none;">
				<div class="smallHeader3">
					<h4 class="cur">Någon vill prata med dig!</h4>
				</div>
				<br/><br/>
			</div>
<?
	$contribute = $sql->queryLine("SELECT u.id_id, u.u_alias, u.account_date, u.u_sex, u.u_birth, u.level_id, c.con_msg FROM {$t}contribute c LEFT JOIN {$t}user u ON u.id_id = c.con_user AND u.status_id = '1' WHERE c.con_onday = NOW() AND c.status_id = '1' LIMIT 1", 1);
	$gotcon = (!empty($contribute) && count($contribute))?true:false;
?>
			<div class="smallHeader">megafonen</div>
			<div class="smallBody">
				<div class="bld">
				<?=($contribute?$user->getstring($contribute).secureOUT($contribute['con_msg'], 1):'Visdom finns ej för idag.')?>
				</div>
				<br/>Varje dag publicerar CitySurf en ny visdom, skicka in en du också!<br/><br/>
				<input type="button" class="btn2_sml r" onclick="makeContribution();" value="skriv" /><br class="clr" />
			</div>

		</div>	<!-- end leftMenu -->
		
		<div id="mainContent">