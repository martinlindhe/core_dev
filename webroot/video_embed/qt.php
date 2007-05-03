<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">
<head>
<title>xx</title>
</head>
<body>
<?
	$width = 176; $height = 144;	//tough_taxi_driver, mike_vallely_vs_4_idiots
	
	//$width = 128; $height = 96;		//VIDEO_00007.3gp
	
	$show_controls = 'false';		//show play/pause buttons

	$filename = 'tough_taxi_driver.3gp';
	//$filename = 'VIDEO_00007.3gp';
	
	//$filename = 'mike_vallely_vs_4_idiots.3gp';
?>

<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="<?=$width?>" height="<?=$height?>" codebase="http://www.apple.com/qtactivex/qtplugin.cab">
<param name="SRC" value="<?=$filename?>">
<param name="AUTOPLAY" value="true">
<param NAME="type" value="video/quicktime">
<param name="CONTROLLER" value="<?=$show_controls?>">
<embed src="<?=$filename?>" width="<?=$width?>" height="<?=$height?>" autoplay="true" controller="<?=$show_controls?>" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/">
</embed>
</object>


</body>
</html>