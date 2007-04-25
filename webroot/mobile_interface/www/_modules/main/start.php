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
			echo '<div class="centerMenuHeaderSmall">senast bloggarna</div>';
			echo '<div class="centerMenuBodySmallWhite">';
		foreach($res as $row) {
				echo '<a href="'.l('user','blog',$row['id_id'],$row['main_id']).'">'.$row['blog_title'].'</a> av '.$user->getstring($row, '', array('icons' => 1)).'<br/>';
			}
			echo '</div></div>';			
		}

		//Listar de senaste blog-kommentarerna
		$res = $sql->query("SELECT * FROM {$t}userblogcmt WHERE status_id = '1' ORDER BY c_date DESC LIMIT 5", 0, 1);
		
		if (count($res)) {
			echo '<div style="float: right">';
			echo '<div class="centerMenuHeaderSmall">senaste kommentarerna</div>';
			echo '<div class="centerMenuBodySmallWhite">';
			foreach($res as $row) {
				$msg = $row['c_msg'];
				if (strlen($msg) >= 14) $msg = substr($msg, 0, 12).'[...]';
				echo '<a href="'.l('user','blog',$row['user_id'],$row['blog_id']).'">'.$msg.'</a> av '.$user->getstring($row['id_id'], '', array('icons' => 1)).'<br/>';
			}
			echo '</div></div>';
		}
		echo '<br/><br/><br/>';


		//Listar de senaste inloggade
		$res = $sql->query("SELECT u.id_id, u.u_alias, u.u_sex, u.u_birth, u.level_id, u.account_date, u_picid, u.u_picvalid, u.u_picd FROM {$t}userlogin s INNER JOIN {$t}user u ON u.id_id = s.id_id AND u.status_id = '1' ORDER BY s.main_id DESC LIMIT 11", 0, 1);
		if (count($res)) {
			echo '<div style="clear: both">';
			echo '<div class="centerMenuHeader">senast inloggade</div>';
			echo '<div class="centerMenuBodyWhite">';
			echo '<div style="padding: 5px 5px 4px 12px;">';
			foreach($res as $row) {
				echo $user->getimg($row['id_id'].$row['u_picid'].$row['u_picd'].$row['u_sex'], $row['u_picvalid'], 0, array('text' => $user->getministring($row)));
			}
			echo '</div></div>';
			echo '</div><br/>';
		}

		//Listar de senaste galleribilderna
		$res = $sql->query("SELECT main_id, user_id, picd, pht_cmt FROM {$t}userphoto WHERE view_id = '1' AND status_id = '1' AND hidden_id = '0' ORDER BY main_id DESC LIMIT 7", 0, 1);
		if (count($res)) {
			echo '<div class="centerMenuHeader">senaste galleribilder</div>';
			echo '<div class="centerMenuBodyWhite">';
			echo '<div style="padding: 5px 5px 4px 12px;">';
			foreach($res as $row) {
				echo '<a href="'.l('user','gallery',$row['user_id'],$row['main_id']).'">';
				echo '<img alt="'.secureOUT($row['pht_cmt']).'" src="/'.USER_GALLERY.$row['picd'].'/'.$row['main_id'].'-tmb.jpg" style="margin-right: 10px;" />';
				echo '</a>';
			}
			echo '</div></div>';
		}

	echo '</div>';
	require(DESIGN.'foot.php');
?>