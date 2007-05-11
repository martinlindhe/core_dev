<?
	if(!empty($_POST['a'])) {
		$src = $_POST['a'];
	}
	if(!empty($src)) {
		if(is_md5($src)) reloadACT(l('user', 'view', $src));
		$other = false;
		if(strpos($src, ':') !== false) {
			$other = true;
			$src = explode(':', $src);
			$type = $src[0];
			$src = $src[1];
		}
		$res = $sql->queryLine("SELECT id_id, u_alias FROM {$t}user WHERE u_alias = '".secureINS($src)."' AND status_id = '1' LIMIT 1");
		$exact = false;
		if(empty($res) || !count($res)) {
			if(!$exact)
				$res = $sql->queryLine("SELECT id_id, u_alias FROM {$t}user WHERE u_alias LIKE '%".secureINS($src)."%' AND status_id = '1' LIMIT 1");
			else errorACT('Felaktig användare.');
		} else $exact = true;

		if(!empty($res) && count($res) || $exact) {
			if($other) {
				switch($type) {
				case 'g':
					$p = 'gb';
				break;
				case 'f':
					$p = 'gallery';
				break;
				case 'b':
					$p = 'spy';
				break;
				case 'm':
					$p = 'mail';
				break;
				case 'v':
					$p = 'relation';
				break;
				default:
					$p = 'view';
				break;
				}
			} else
				$p = 'view';
			reloadACT(l('user', $p, $res[0]));
		} elseif(count($res) > 1) {
			reloadACT(l('list', 'users')); #reloadACT('list_user.php?do=1&alias='.$src);
		} else {
			reloadACT(l('list', 'users')); #reloadACT('list_user.php?do=1&alias='.$src);
		}
	} elseif(isset($_GET['id'])) {
		$r = $user->getinfo($l['id_id'], 'random');
		if(!$r) {
			$sexs = array('M' => 'F', 'F' => 'M', '' => 'F');
			$sexs = $sexs[$l['u_sex']];
		} else {
			if($r == 'B') $sexs = false;
			else $sexs = $r;
		}
		if($sexs) {
			$c = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userlevel WHERE MATCH(level_id) AGAINST('+VALID +SEX".$sexs."' IN BOOLEAN MODE)");
			$c = mt_rand(0, $c);
			$res = $sql->queryResult("SELECT id_id FROM {$t}userlevel WHERE MATCH(level_id) AGAINST('+VALID +SEX".$sexs."' IN BOOLEAN MODE) LIMIT $c, 1");
		} else {
			$c = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userlevel WHERE MATCH(level_id) AGAINST('+VALID' IN BOOLEAN MODE)");
			$c = mt_rand(0, $c);
			$res = $sql->queryResult("SELECT id_id FROM {$t}userlevel WHERE MATCH(level_id) AGAINST('+VALID' IN BOOLEAN MODE) LIMIT $c, 1");
		}
		if(!empty($res)) {
			if($res == $l['id_id']) {
				$res = $sql->queryResult("SELECT id_id FROM {$t}uservalid WHERE id_id != '".$l['id_id']."' AND status_id = '".$sexs."' ORDER BY RAND() LIMIT 1");
			}
			reloadACT(l('user', 'view', $res));
		} else {
			$res = $sql->queryResult("SELECT id_id FROM {$t}user WHERE id_id != '".$l['id_id']."' AND status_id = '1' AND u_sex = '".$sexs."' ORDER BY RAND() LIMIT 1");
			reloadACT(l('user', 'view', $res));
		}
	} else {
		reloadACT(l('list', 'users'));
	}
?>
