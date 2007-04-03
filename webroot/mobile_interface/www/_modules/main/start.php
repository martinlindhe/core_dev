<?
	include(DESIGN.'head.php');
?>
		<div id="mainContent">
<?
	$lpsdl = $sql->query("SELECT ".CH." ad_img, ad_url, ad_type, main_id, ad_start, ad_stop, ad_hidden, city_id FROM {$t}news WHERE status_id = '1' AND ad_level = '0' ORDER BY ad_pos ASC");
	foreach($lpsdl as $row) {
		if(strtotime($row[4]) > time() || strtotime($row[5]) < time()) continue;
		#if(!empty($_COOKIE['main_id']) && strpos($_COOKIE['main_id'], '='.$row[3]) !== false) continue();
		if(!$row[6])
		print '
			<div class="mb">'.(($row[2] == 'swf' || $row[2] == 'event')?stripslashes($row[0]):((!empty($row[1]))?'<a href="'.stripslashes($row[1]).'"><img src="'.NEWS.$row[0].'"></a>':'<img src="'.NEWS.$row[0].'">')).'</div>
		';
		else
		print '<div style="position: absolute; left: -5px; top: -2px;">'.(($row[2] == 'swf' || $row[2] == 'event')?stripslashes($row[0]):((!empty($row[1]))?'<a href="'.stripslashes($row[1]).'"><img src="'.NEWS.$row[0].'" width="658"></a>':'<img src="'.NEWS_DIR.$row[0].'" width="658">')).'</div>';
	}
	unset($lpsdl);
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
	echo '		</div>';
	require(DESIGN.'foot_info.php');
	require(DESIGN.'foot.php');
?>