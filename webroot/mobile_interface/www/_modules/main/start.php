<?
	include(DESIGN.'head.php');

	echo '<div id="mainContent">';

		//Listar de senaste bloggarna
		$q = "SELECT b.*,u.* FROM {$t}userblog b ".
				"LEFT JOIN ${t}user u ON (b.user_id=u.id_id) ".
				"ORDER BY b.blog_date DESC LIMIT 5";
		$res = $sql->query($q, 0, 1);
		
		if (count($res)) {
			echo '<div class="mainHeader2"><h4>senast bloggarna</h4></div>';
			echo '<div class="mainBoxed2">';
			echo '<div style="padding: 5px 5px 4px 12px;">';
			foreach($res as $row) {
				echo '<a href="'.l('user','blog',$row['id_id'],$row['main_id']).'">'.$row['blog_title'].'</a> av '.$user->getstring($row, '', array('icons' => 1)).'<br/>';
			}
			echo '</div></div>';			
		}

		//Listar de senaste kommentarerna
		//$res = $sql->query("SELECT * FROM {$t}userblog ORDER BY blog_date DESC LIMIT 5", 0, 1);
		
		//if (count($res)) {
			echo '<div class="mainHeader2"><h4>senaste kommentarerna</h4></div>';
			echo '<div class="mainBoxed2">';
			echo '<div style="padding: 5px 5px 4px 12px;">';
			//foreach($res as $row) {
				//echo $row['blog_title'].' av '.$row['user_id'].' vid '.$row['blog_date'].'<br/>';
			//}
			echo '</div></div>';			
		//}


		//Listar de senaste inloggade - todo ha detta på oinloggade startsidan
		$res = $sql->query("SELECT u.id_id, u.u_alias, u.u_sex, u.u_birth, u.level_id, u.account_date, u_picid, u.u_picvalid, u.u_picd FROM {$t}userlogin s INNER JOIN {$t}user u ON u.id_id = s.id_id AND u.status_id = '1' ORDER BY s.main_id DESC LIMIT 11", 0, 1);
		if (count($res)) {
			echo '<div class="mainHeader2"><h4>senast inloggade</h4></div>';
			echo '<div class="mainBoxed2">';
			echo '<div style="padding: 5px 5px 4px 12px;">';
			foreach($res as $row) {
				echo $user->getimg($row['id_id'].$row['u_picid'].$row['u_picd'].$row['u_sex'], $row['u_picvalid'], 0, array('text' => $user->getministring($row)));
			}
			echo '</div></div>';
		}


	echo '</div>';
	require(DESIGN.'foot_info.php');
	require(DESIGN.'foot.php');
?>