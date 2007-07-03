<?
	include(DESIGN.'head.php');

	echo '<div id="bigContent">';

	echo '<table cellpadding="0" cellspacing="0" border="0"><tr><td width="602">';

		//Listar de senaste bloggarna
		$q = "SELECT b.*,u.* FROM {$t}userblog b ".
				"LEFT JOIN ${t}user u ON (b.user_id=u.id_id) ".
				"ORDER BY b.blog_date DESC LIMIT 5";
		$res = $sql->query($q, 0, 1);
		
		if (count($res)) {
			echo '<div style="float: left">';
			echo '<div class="mediumHeader">senaste bloggarna</div>';
			echo '<div class="mediumBody">';
			foreach($res as $row) {
				$title = stripslashes($row['blog_title']);
				if (!$title) $title = 'Ingen rubrik';
				if (strlen($title) >= 20) $title = substr($title, 0, 20).'[...]';

				echo '<a href="'.l('user','blog',$row['id_id'],$row['main_id']).'">'.$title.'</a> av '.$user->getstring($row, '', array('icons' => 1)).'<br/>';
			}
			echo '</div></div>';
		}

		//Listar de senaste blog-kommentarerna
		$res = $sql->query("SELECT * FROM {$t}userblogcmt WHERE status_id = '1' ORDER BY c_date DESC LIMIT 5", 0, 1);
		
		if (count($res)) {
			echo '<div style="float: right">';
			echo '<div class="mediumHeader">senaste kommentarerna</div>';
			echo '<div class="mediumBody">';
			foreach($res as $row) {
				$msg = $row['c_msg'];
				if (strlen($msg) >= 14) $msg = substr($msg, 0, 12).'[...]';
				echo '<a href="'.l('user','blog',$row['user_id'],$row['blog_id']).'">'.$msg.'</a> av '.$user->getstring($row['id_id'], '', array('icons' => 1)).'<br/>';
			}
			echo '</div></div>';
		}
		echo '<br class="clr" /><br/>';

		//Listar de senaste inloggade
		$res = $sql->query("SELECT u.* FROM s_userlogin s INNER JOIN {$t}user u ON u.id_id = s.id_id AND u.status_id = '1' ORDER BY s.main_id DESC LIMIT 11", 0, 1);
		if (count($res)) {
			echo '<div style="clear: both">';
			echo '<div class="bigHeader">senast inloggade</div>';
			echo '<div class="bigBody">';
			foreach($res as $row) {
				echo $user->getimg($row['id_id'].$row['u_picid'].$row['u_picd'].$row['u_sex'], $row['u_picvalid'], 0, array('text' => $user->getministring($row)));
			}
			echo '</div>';
			echo '</div><br/>';
		}

		//Listar de senaste galleribilderna
		$q = "SELECT main_id, user_id, picd, pht_name, pht_cmt FROM {$t}userphoto WHERE status_id = '1' AND hidden_id = '0' AND pht_name != '' ORDER BY main_id DESC LIMIT 11";
		$res = $sql->query($q, 0, 1);
		if (count($res)) {
			echo '<div class="bigHeader">senaste galleribilder</div>';
			echo '<div class="bigBody">';
			foreach($res as $row) {
				echo '<a href="'.l('user','gallery',$row['user_id'],$row['main_id']).'">';
				echo '<img alt="'.secureOUT($row['pht_cmt']).'" src="/'.USER_GALLERY.$row['picd'].'/'.$row['main_id'].'-tmb.'.$row['pht_name'].'" style="margin-right: 10px;" />';
				echo '</a>';
			}
			echo '</div><br/>';
		}

		//Visa den senaste krönikan
		$res = $sql->query("SELECT * FROM s_editorial WHERE status_id = '1' ORDER BY ad_date DESC LIMIT 1", 0, 1);
		if(count($res)) {
			echo '<div class="bigHeader">krönika</div>';
			echo '<div class="bigBody">';
			echo nl2br(stripslashes($res[0]['ad_cmt']));
			echo '</div>';
		}

	echo '</td><td width="10">&nbsp;</td><td>';
?>
<script type="text/javascript">
var bnum=new Number(Math.floor(99999999 * Math.random())+1);
document.write('<SCR'+'IPT LANGUAGE="JavaScript" ');
document.write('SRC="http://servedby.advertising.com/site=737464/size=160600/bnum='+bnum+'/optn=1"></SCR'+'IPT>');
</script>
<?
	echo '</td></tr></table>';

	echo '</div>';	//id="mainContent"

	require(DESIGN.'foot.php');
?>