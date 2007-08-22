<?
	require_once('find_config.php');

	if (!empty($_GET['id'])) $page = $_GET['id']; else $page = 'changes.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title><?=$title?>AMS</title>
	<link rel="stylesheet" href="default_adm.css" type="text/css">
<script type="text/javascript">
</script>
<frameset rows="18,*, 18" framespacing="0" frameborder="no" border="0"">
	<frame name="<?=FRS?>head" src="top.php" marginwidth="0" marginheight="0" scrolling="no" frameborder="no" noresize>
	<frame name="<?=FRS?>main" src="<?=$page?>" marginwidth="0" marginheight="0" scrolling="auto" frameborder="no" noresize>
	<frame name="<?=FRS?>foot" src="foot.php" marginwidth="0" marginheight="0" scrolling="no" frameborder="no" noresize>
</frameset>
<noframes></noframes>
</html>
