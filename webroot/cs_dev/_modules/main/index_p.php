<?
	$login_err = true;
	if(empty($l) && !empty($_POST['a']) && !empty($_POST['p'])) {
		checkBan(1);
		$login_err = $user_auth->login($_POST['a'], $_POST['p']);
	}

	/*
	if ($l) {
		header('Location: /main/start/');
		die;
	}*/

	$theme_css = 'jord.css';
	require_once(DESIGN.'top.php');
?>
</head>
<body>

	<div class="mainContent">

		<center>

		<div style="width: 596px; text-align: left;"><img src="<?=$config['web_root']?>_gfx/themes/default_logo.png" alt=""/></div>

		<div class="bigHeader"></div>
		<div class="bigBody startPageBody">
			<table summary=""><tr>
				<td width="50%"><?=gettxt('index_text', 0, 1)?></td>
				<td align="right">
					<form name="l" action="" method="post">
						<? if ($login_err !== true) echo '<span style="font-weight:bold;font-size:16px;color: #ff2020;">'. $login_err.'</span><br/><br/>'; ?>

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
	$res = $db->getArray('SELECT u.id_id, u.u_alias, u.u_sex, u.u_birth, u.level_id, u.account_date, u_picid, u.u_picvalid, u.u_picd FROM s_userlogin s INNER JOIN s_user u ON u.id_id = s.id_id AND u.status_id = "1" ORDER BY s.main_id DESC LIMIT 11');
	if (count($res)) {
		echo '<div class="bigHeader" style="clear: both">senast inloggade</div>';
		echo '<div class="bigBody"><center>';
		foreach($res as $row) {
			echo $user->getimg($row['id_id'].$row['u_picid'].$row['u_picd'].$row['u_sex'], $row['u_picvalid'], 0, array('text' => $user->getministring($row)));
		}
		echo '</center></div><br/>';
	}
	$res = $db->getArray('SELECT main_id, picd, pht_name, pht_cmt FROM s_userphoto WHERE view_id = "1" AND status_id = "1" AND hidden_id = "0" ORDER BY main_id DESC LIMIT 5');
	if(count($res)) {
		echo '<div class="bigHeader">senaste galleribilder</div>';
		echo '<div class="bigBody"><center>';
		foreach($res as $row) {
			echo '<img alt="'.secureOUT($row['pht_cmt']).'" src="'.USER_GALLERY.$row['picd'].'/'.$row['main_id'].'-tmb.'.$row['pht_name'].'" style="margin-right: 10px;" onerror="this.style.display = \'none\';" />';
		}
		echo '</center></div>';
	}
?>
		</center>
	</div>

<?
	require(DESIGN.'foot.php');
?>
