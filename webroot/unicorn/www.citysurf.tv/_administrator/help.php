<?
session_start();

/*    ob_start();
    ob_implicit_flush(0);
    ob_start('ob_gzhandler');*/
	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');

	require("./set_onl.php");

	if(notallowed()) {
		header("Location: ./");
		exit;
	}

	$sql = mysql_query("SELECT * FROM $text_tab WHERE main_id = 'help_".secureINS($_GET['id'])."' LIMIT 1");
	if(mysql_num_rows($sql)) {
		$ttl = 'HJÄLP';
		$msg = nl2br(mysql_result($sql, 0, 'text_cmt'));
		require("./_tpl/notice_apopup.php");
		exit;
	} else {
		$msg = 'Hjälptext finns inte.';
		require("./_tpl/notice_apopup.php");
		exit;
	}
?>