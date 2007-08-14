<?
	$str = @$_POST['last'];
	if(substr($str, 0, 1) == '/') {
		reloadACT($str);
	}

	$error = false;
	if(empty($_POST['val']) || !is_numeric($_POST['val']) || empty($_POST['id'])) {
		$error = 'Du valde inget alternativ.';
	}
	$is_poll = false;
	$did_poll = false;
	if(!$error) {
		$pollid = @$sql->queryResult("SELECT ".CH." main_id FROM {$t}poll WHERE main_id = '".secureINS($_POST['id'])."' AND poll_month = '".date("Y-W")."' AND poll_ans".secureINS($_POST['val'])." != '' LIMIT 1");
		if($pollid) {
			if(!empty($_COOKIE['b']) && $_COOKIE['b'] == $pollid) {
				$is_poll = true;
				$did_poll = true;
			} else {
				$is_poll = true;
				$ans = $_POST['val'];

			$ins = $sql->queryInsert("INSERT INTO {$t}pollvisit SET
				sess_ip = '".secureINS($_SERVER['REMOTE_ADDR'])."',
				category_id = '".secureINS($pollid)."',
				unique_id = '".secureINS($ans)."',
				date_snl = NOW(),
				date_cnt = NOW()");
				if(!empty($ans) && is_numeric($ans)) {
					if($ins) {
						$id = $ins;
						$sql->logADD($pollid, $ins, 'POLL_VOTE');
						$sql->queryUpdate("UPDATE {$t}poll SET poll_res$ans = poll_res$ans + 1 WHERE poll_month = '".date("Y-W")."' AND main_id = '".secureINS($_POST['id'])."' LIMIT 1");
					} //else $error = 'Du har redan röstat.';
					cookieSET("b", rawurlencode($pollid), time() + 20 * 24 * 60 * 60);
				}
			}
		} else $error = 'Fel.';
	}

	if(!$error && !$is_poll) $error = 'Det finns ingen aktuell röstning.';
	if(!$error && $did_poll) $error = 'Du har redan röstat.';

	if($error) {
		#echo '<div style="width: 80%; margin: 65px 0 0 0;" class="bld drk">'.$error.'</div>';
		errorACT($error, $str);
	} else {
		reloadACT($str);
	}
?>