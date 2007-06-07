<?
session_start();
ob_start();
    ob_implicit_flush(0);
    ob_start('ob_gzhandler');
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	if(notallowed()) {
		header("Location: ./");
		exit;
	}

	$got = false;
	if(!empty($_GET['id']) && is_md5($_GET['id'])) {
		$sql = mysql_query("SELECT * FROM {$tab['tab']}admin WHERE main_id = '".secureINS($_GET['id'])."' LIMIT 1");
		if(mysql_num_rows($sql) > 0) {
			$got = true;
			$row = mysql_fetch_assoc($sql);
			$u_id = $row['main_id'];
		}
	}

	if(!$got) {
exit;
	}


	$length = strlen($_SESSION['u_n']);
	if(strlen($length) == '1') $length = '0'.$length;

	$sql = mysql_query("SELECT c.user_id, u.user_name, c.sent_date, c.sent_cmt, c.sender_id FROM {$tab['tab']}adminchat c LEFT JOIN {$tab['admin']} u ON u.main_id = c.sender_id WHERE c.user_id = '".secureINS($_SESSION['u_i'])."' AND c.sender_id = '".secureINS($u_id)."' AND c.user_read = '0' ORDER BY c.sent_date ASC");

	if(!empty($_GET['msg'])) {
		$str = substr($_GET['msg'], 0, 250);
		@mysql_query("INSERT INTO {$tab['tab']}adminchat SET
		sender_id = '".secureINS($_SESSION['u_i'])."',
		user_id = '".secureINS($u_id)."',
		sent_cmt = '".secureINS($str)."',
		sent_date = NOW(),
		user_read = '0'");
		$len = strlen(rawurlencode($_SESSION['u_n']));
		if(strlen($len) == '1') $len = '0'.$len;
		$date = secureOUT(rawurlencode(niceDate(date("Y-m-d H:i:s"))));
		$dlen = strlen($date);
		if(strlen($dlen) == '1') $dlen = '0'.$dlen;

		$s_id = sprintf("%05d", $_SESSION['u_i']);
		echo $s_id.$len.secureOUT(rawurlencode(ucfirst($_SESSION['u_n']))).$dlen.$date.rawurlencode(secureOUT(stripslashes($str)));
	} else {
		while($row = mysql_fetch_row($sql)) {
			$len = strlen(rawurlencode($row[1]));
			if(strlen($len) == '1') $len = '0'.$len;
			$row[2] = secureOUT(rawurlencode(niceDate($row[2])));
			$dlen = strlen($row[2]);
			if(strlen($dlen) == '1') $dlen = '0'.$dlen;
			$row[0] = sprintf("%05d", $row[0]);

			echo $row[0].$len.secureOUT(rawurlencode(ucfirst($row[1]))).$dlen.$row[2].rawurlencode(secureOUT(stripslashes($row[3])));
		}
		@mysql_query("UPDATE {$tab['tab']}adminchat SET user_read = '1' WHERE user_id = '".secureINS($_SESSION['u_i'])."' AND sender_id = '".secureINS($u_id)."'");
	}
?> 