<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title><?=(!empty($ttl))?$ttl.' | ':'';?><?=@$title?>AMS</title>
	<link rel="stylesheet" href="default_adm.css" type="text/css">
	<meta http-equiv="imagetoolbar" content="no">
	<script language="JavaScript" type="text/javascript" src="fnc_adm.js"></script>
</head>
<body class="bg_fade" style="width: 100%; margin: 0; padding: 0;">
			<center>
			<table cellspacing="0" style="margin: 5px 0 0 0; " style="width: 300px;">
			<tr><td class="bg_blk wht bld cnt mid" height="30" style="padding-left: 10px;"><a href="javascript:window.close();" title="Stäng/Avbryt"><?=(!empty($ttl))?$ttl:'NOTIS';?></a></td></tr>
			<tr>
				<td style="padding: 20 10px 20px 10px;" class="cnt bg_gray"><table cellspacing="0"><tr><td class="txt_blk"><?=$msg?></td></tr></table></td>
			</tr>
			</table>
			</center>
<?
	if(!empty($js_sex)) {
?>
<script type="text/javascript">
	<?=$js_sex?>
</script>
<?
	}
	if(!empty($mv)) {
?>
<script type="text/javascript">
	window.setTimeout("document.location.href = '<?=$mv?>';", <?=(!empty($time))?$time:2000;?>);
</script>
<?
	}
	if(!empty($ex)) {
?>
<script type="text/javascript">
	window.setTimeout("<?=$ex?>", <?=(!empty($time))?$time:2000;?>);
</script>
<?
	}
?>
</body>
</html>