<?
	if(!empty($_POST['a'])) {
		$src = $_POST['a'];
	}
	if(!empty($src)) {
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
	} else {
		reloadACT(l('list', 'users'));
	}
?>
