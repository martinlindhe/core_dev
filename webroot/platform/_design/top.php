<?
	function makeButton($bool, $js, $img, $text, $number = false)
	{
		if ($bool) $class = 'btnSelected';
		else $class = 'btnNormal';

		echo '<div class="'.$class.'" onclick="'.$js.'">';
		echo '<table summary="" cellpadding="0" cellspacing="0">';
		echo '<tr>';
			echo '<td width="3"><img src="/_gfx/btn_c1.png" alt=""/></td>';
			echo '<td style="background: url(\'/_gfx/btn_head.png\');"></td>';
			echo '<td width="3"><img src="/_gfx/btn_c2.png" alt=""/></td>';
		echo '</tr>';

		echo '<tr style="height: 18px">';
			echo '<td width="3" style="background: url(\'/_gfx/btn_left.png\');"></td>';
			echo '<td style="padding-left: 19px; padding-right: 4px; padding-top: 1px;">';
			if ($img) echo '<img src="/_gfx/'.$img.'" style="position: absolute; top: 5px; left: 4px;" alt=""/> ';
			echo $text;
			if ($number !== false) echo '&nbsp;&nbsp;'.$number;
			echo '</td>';
			echo '<td width="3" style="background: url(\'/_gfx/btn_right.png\');"></td>';
		echo '</tr>';

		echo '<tr>';
			echo '<td width="3"><img src="/_gfx/btn_c3.png" alt=""/></td>';
			echo '<td style="background: url(\'/_gfx/btn_foot.png\');"></td>';
			echo '<td width="3"><img src="/_gfx/btn_c4.png" alt=""/></td>';
		echo '</tr>';

		echo '</table>';
		echo '</div>';
	}
	
	if ($html4_head) {
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
	} else {
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">';
	}
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<title><?=$NAME_TITLE?></title>
<meta name="description" content=""/>
<meta http-equiv="imagetoolbar" content="no"/>
<meta http-equiv="content-language" content="se"/>
<meta name="keywords" content=""/>
<meta name="author" content=""/>
<meta name="robots" content="follow,index"/>
<meta name="language" content="sv-SE"/>
<link rel="stylesheet" type="text/css" title="default" media="screen" href="<?=CS?>_objects/_styles/screen.css"/>
<link rel="stylesheet" type="text/css" href="/_gfx/site.css"/>
<link rel="shortcut icon" href="<?=CS?>favicon.ico"/>
<script src="<?=CS?>_objects/main1.js" type="text/javascript"></script>
<script src="<?=CS?>_objects/swfobject.js" type="text/javascript"></script>