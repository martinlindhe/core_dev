<?
	$r = $config['site']['web_root'];

	$rss_tags = '';
	if (!empty($meta_rss)) {
		foreach ($meta_rss as $feed) {
			if (!empty($feed['category']) && is_numeric($feed['category'])) $extra = '?c='.$feed['category'].getProjectPath();
			else $extra = getProjectPath(false);
			$rss_tags .= 	"\t".'<link rel="alternate" type="application/rss+xml" title="'.$feed['title'].'" href="/core/rss_'.$feed['name'].'.php'.$extra.'"/>'."\n";
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>sample site</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="/css/functions.css" type="text/css"/>
	<link rel="stylesheet" href="<?=$r?>css/site.css" type="text/css"/>
<?=$rss_tags?>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
	<script type="text/javascript" src="/js/ajax.js"></script>
	<script type="text/javascript" src="/js/functions.js"></script>
</head>
<body>
<script type="text/javascript">
var _ext_ref='<?=getProjectPath()?>';
</script>
<div id="left-bg"></div>
<div id="left-sep"></div>
<div id="header">
	<div id="header-logo">
		<a href="<?=$r?>index.php"><img src="<?=$r?>gfx/logo.png" alt="Filterset Database"/></a>
	</div>
	<div id="header-items">
		<a href="<?=$r?>wiki.php?View:About">About</a>
		<a href="<?=$r?>wiki.php?View:Help">Help</a>
	</div>
</div>
<div id="leftmenu">
	<ul class="side-nav">
		<? $cur = basename($_SERVER['SCRIPT_NAME']); ?>
		<li><?=($cur=='index.php'?'<strong>':'');?><a href="<?=$r?>index.php">Home</a><?=($cur=='index.php'?'</strong>':'')?></li>
		<li><?=($cur=='news.php'?'<strong>':'')?><a href="<?=$r?>news.php">News</a><?=($cur=='news.php'?'</strong>':'')?></li>
		<li><?=($cur=='files.php'?'<strong>':'')?><a href="<?=$r?>files.php">Files</a><?=($cur=='files.php'?'</strong>':'')?></li>
<?
	if ($session->isAdmin) {
		//admin menu if logged in
		echo '<li>'.($cur=='admin.php'?'<strong>':'').'<a href="/admin/admin.php'.getProjectPath(false).'">Admin</a>'.($cur=='admin.php'?'</strong>':'').'</li>';
	}

	if ($session->id) {
		echo '<li>'.($cur=='settings.php'?'<strong>':'').'<a href="'.$r.'settings.php">Settings</a>'.($cur=='settings.php'?'</strong>':'').'</li>';
		echo '<li><a href="'.$_SERVER['PHP_SELF'].'?logout">Logout</a></li>';
	}
?>
	</ul>
</div>

<div id="middle">
