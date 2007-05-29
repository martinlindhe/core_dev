<?
	/*
	if(!empty($id) && $id == '1') {
		cookieSET('a65', '');
		$_COOKIE['a65'] = '';
		unset($_COOKIE['a65']);
		reloadACT(l('main', 'index'));
	}
	*/

	$theme_css = 'jord.css';
	include(DESIGN.'top.php');

/*
	$online = gettxt('stat_online');
	$online = explode(':', $online);

	<div class="cnt" style="position: relative; top: 16px; left: 450px; width: 137px; height: 54px; background: url('/_objects/bg_online_small.png');">
		<table summary="" cellspacing="0" style="width: 127px;" class="cnti lft mrg">
			<tr><td>online</td><td class="bld rgt"><?=@intval($online[0])?></td></tr>
			<tr><td>män</td><td class="rgt bld sexM"><?=@intval($online[1])?></td></tr>
			<tr><td>kvinnor</td><td class="rgt bld sexF"><?=@intval($online[2])?></td></tr>
		</table>
	</div>
*/
?>
</head>
<body>

	<div class="mainContent">

		<center>

		<div style="width: 596px; text-align: left;"><img src="/_gfx/themes/default_logo.png" alt=""/></div>

		<div class="bigHeader"></div>
		<div class="bigBody" style="min-height: 300px; background: url('/_gfx/themes/start_gfx.png'); background-repeat: no-repeat; background-position: 100% 100%;">
			<table summary=""><tr>
				<td width="50%"><?=gettxt('index_text', 0, 1)?></td>
				<td align="right">
					<form name="l" action="/member/login" method="post">

						<table summary="" cellspacing="0">
							<tr><td class="rgt" style="padding: 0 5px 3px 0;">alias: <input type="text" class="txt" name="a" style="margin-bottom: -4px;" /></td></tr>
							<tr><td class="rgt" style="padding: 2px 5px 4px 0;">lösenord: <input type="password" class="pass" style="margin-bottom: -4px;" name="p" /></td></tr>
						</table>

						<script type="text/javascript">if(document.l.a.value.length > 0) document.l.p.focus(); else document.l.a.focus();</script>
						<input type="button" onclick="goLoc('/member/register/');" class="btn2_min" value="bli medlem" style="margin-top: 3px;"/>
						<input type="button" onclick="goLoc('/member/forgot/');" class="btn2_min" value="glömt lösen" style="margin-top: 3px;"/>
						<input type="submit" class="btn2_min" value="logga in"/>
					</form>
					<br/>
				</td>
			</tr></table>
		</div><br/>
<?
	$res = $sql->query("SELECT u.id_id, u.u_alias, u.u_sex, u.u_birth, u.level_id, u.account_date, u_picid, u.u_picvalid, u.u_picd FROM {$t}userlogin s INNER JOIN {$t}user u ON u.id_id = s.id_id AND u.status_id = '1' ORDER BY s.main_id DESC LIMIT 11", 0, 1);
	if (count($res)) {
		echo '<div class="bigHeader" style="clear: both">senast inloggade</div>';
		echo '<div class="bigBody"><center>';
		foreach($res as $row) {
			echo $user->getimg($row['id_id'].$row['u_picid'].$row['u_picd'].$row['u_sex'], $row['u_picvalid'], 0, array('text' => $user->getministring($row)));
		}
		echo '</center></div><br/>';
	}
	$res = $sql->query("SELECT main_id, picd, pht_cmt FROM {$t}userphoto WHERE view_id = '1' AND status_id = '1' AND hidden_id = '0' ORDER BY main_id DESC LIMIT 7", 0, 1);
	if(count($res)) {
		echo '<div class="bigHeader">senaste galleribilder</div>';
		echo '<div class="bigBody"><center>';
		foreach($res as $row) {
			echo '<img alt="'.secureOUT($row['pht_cmt']).'" src="/'.USER_GALLERY.$row['picd'].'/'.$row['main_id'].'-tmb.jpg" style="margin-right: 10px;" onerror="this.style.display = \'none\';" />';
		}
		echo '</center></div>';
	}
?>
		</center>
	</div>

<?
	require(DESIGN.'foot.php');
?>
