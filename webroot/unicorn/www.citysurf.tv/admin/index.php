<?
	require_once('find_config.php');

	if (!empty($_GET['id'])) $page = $_GET['id']; else $page = 'changes.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?=$title?> admin</title>
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
