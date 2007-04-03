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
		$sql = mysql_query("SELECT * FROM {$tab['admin']} WHERE main_id = '".secureINS($_GET['id'])."' LIMIT 1");
		if(mysql_num_rows($sql) > 0) {
			$got = true;
			$row = mysql_fetch_assoc($sql);
			$u_id = $row['main_id'];
		}
	}

	if(!$got) {
exit;
	}
print '<?xml version="1.0" encoding="ISO-8859-1" ?>';
?>
<html style="height: 100%; width: 100%">
	<head>
		<title><?=$title?>AMS</title>
<link rel="stylesheet" href="default_adm.css" type="text/css">
		<style type="text/css">
table { border-collapse: collapse; }
body {background: #F6F6F6; }
* { font-family: Verdana, Tahoma, Arial, Helvetica, Sans-Serif; font-size: 10px; }

</style>

	<body style="height: 100%; width: 100%; margin: 0; padding: 0; border: 0; border-spacing: 0;">
<div id="messageDiv" style="padding: 5px; overflow-x: hidden; width: 95%;"></div>
	<script language="javascript">

		window.onload = function() {parent.GetMessages();}
		window.onfocus = function() { parent.textFocus(); }
	</script>
	</body>
</html>