<?
	include(DESIGN.'head.php');

	echo '<div id="mainContent">';

		//Listar de senaste bloggarna
		$q = "SELECT b.*,u.* FROM {$t}userblog b ".
				"LEFT JOIN ${t}user u ON (b.user_id=u.id_id) ".
				"ORDER BY b.blog_date DESC LIMIT 5";
		$res = $sql->query($q, 0, 1);
		
		if (count($res)) {
			echo '<div style="float: left">';
			echo '<div class="mainHeader2" style="width: 292px"><h4>senast bloggarna</h4></div>';
			echo '<div class="mainBoxed2" style="width: 290px">';
			echo '<div style="padding: 5px 5px 4px 12px;">';
			foreach($res as $row) {
				echo '<a href="'.l('user','blog',$row['id_id'],$row['main_id']).'">'.$row['blog_title'].'</a> av '.$user->getstring($row, '', array('icons' => 1)).'<br/>';
			}
			echo '</div></div>';			
			echo '</div>';
		}

		//Listar de senaste blog-kommentarerna
		$res = $sql->query("SELECT * FROM {$t}userblogcmt ORDER BY c_date DESC LIMIT 6", 0, 1);
		
		if (count($res)) {
			echo '<div style="float: right">';
			echo '<div class="mainHeader2" style="width: 292px"><h4>senaste kommentarerna</h4></div>';
			echo '<div class="mainBoxed2" style="width: 290px">';
			echo '<div style="padding: 5px 5px 4px 12px;">';
			foreach($res as $row) {
				$msg = $row['c_msg'];
				if (strlen($msg) > 15) $msg = substr($msg, 0, 15).' [...]';
				echo $msg.' av '.$user->getstring($row, '', array('icons' => 1)).'<br/>';
			}
			echo '</div></div>';
			echo '</div>';
		}


		//Listar de senaste inloggade
		$res = $sql->query("SELECT u.id_id, u.u_alias, u.u_sex, u.u_birth, u.level_id, u.account_date, u_picid, u.u_picvalid, u.u_picd FROM {$t}userlogin s INNER JOIN {$t}user u ON u.id_id = s.id_id AND u.status_id = '1' ORDER BY s.main_id DESC LIMIT 11", 0, 1);
		if (count($res)) {
			echo '<div style="clear: both">';
			echo '<div class="mainHeader2"><h4>senast inloggade</h4></div>';
			echo '<div class="mainBoxed2">';
			echo '<div style="padding: 5px 5px 4px 12px;">';
			foreach($res as $row) {
				echo $user->getimg($row['id_id'].$row['u_picid'].$row['u_picd'].$row['u_sex'], $row['u_picvalid'], 0, array('text' => $user->getministring($row)));
			}
			echo '</div></div>';
			echo '</div>';
		}

		//Listar de senaste galleribilderna
		$res = $sql->query("SELECT main_id, picd, pht_cmt FROM {$t}userphoto WHERE view_id = '1' AND status_id = '1' AND hidden_id = '0' ORDER BY main_id DESC LIMIT 7", 0, 1);
		if (count($res)) {
			echo '<div class="mainHeader2"><h4>senaste galleribilder</h4></div>';
			echo '<div class="mainBoxed2">';
			echo '<div style="padding: 5px 5px 4px 12px;">';
			foreach($res as $row) {
				echo '<img alt="'.secureOUT($row['pht_cmt']).'" src="/'.USER_GALLERY.$row['picd'].'/'.$row['main_id'].'-tmb.jpg" style="margin-right: 10px;" onerror="this.style.display = \'none\';" />';
			}
			echo '</div></div>';
		}

	echo '</div>';
	require(DESIGN.'foot_info.php');
	require(DESIGN.'foot.php');
?>