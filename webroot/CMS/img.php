<?
	if (empty($_GET['pic'])) die;
	$pic_name = $_GET['pic'];
	
	$title = '';
	if (!empty($_GET['title'])) $title = strip_tags($_GET['title']);

	if (!$title) $title = basename($pic_name);
?>
<head>
<title><?=$title?></title>
<script type="text/javascript">
window.focus();
</script>
</head>
<body marginheight=0 marginwidth=0 topmargin=0 leftmargin=0 bottommargin=0 bgcolor=#FFFFFF>
<a href="javascript:window.close();"><img src="<?=$pic_name?>" border=0></a>
