<?
	include('include_all.php');

	include('design_head.php');

	echo getInfoField($db, 'page_chat').'<br>';
	
	$swf['width'] = 424;
	$swf['height']= 264;
	$swf['name'] = 'flashchat';
	$swf['file'] = 'flashchat.php';
	//$swf['file'] = 'flashchat.swf';

	/*
		Note: För actionscript getURL() krävs <param name="allowScriptAccess" value="sameDomain" />
	*/
?>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="<?=$swf['width']?>" height="<?=$swf['height']?>" id="<?=$swf['name']?>" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="<?=$swf['file'].'?w='.$swf['width'].'&h='.$swf['height']?>" />
<param name="quality" value="high" />
<embed src="<?=$swf['file'].'?w='.$swf['width'].'&h='.$swf['height']?>" quality="high" width="<?=$swf['width']?>" height="<?=$swf['height']?>" name="<?=$swf['name']?>" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
<br>
<?
	include('design_foot.php');
?>