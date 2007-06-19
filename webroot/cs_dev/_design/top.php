<?
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">';
	
	if (@$l) $theme_css = $user->getinfo($l['id_id'], 'det_tema');
	if (empty($theme_css)) $theme_css = 'default.css';
	$theme_css = secureOUT($theme_css);

	if (empty($NAME_TITLE)) $NAME_TITLE = 'CitySurf';
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
<link rel="stylesheet" type="text/css" title="default" media="screen" href="<?=OBJ?>_styles/screen.css"/>
<link rel="stylesheet" type="text/css" href="<?=$config['web_root']?>_gfx/themes/common.css"/>
<link rel="stylesheet" type="text/css" href="<?=$config['web_root']?>_gfx/themes/<?=$theme_css?>"/>
<link rel="shortcut icon" href="<?=OBJ?>favicon.ico"/>
<script type="text/javascript" src="<?=OBJ?>main1.js"></script>
<script type="text/javascript" src="<?=OBJ?>swfobject.js"></script>
<script type="text/javascript" src="<?=OBJ?>fol.js"></script>
<script type="text/javascript" src="<?=OBJ?>ajax.js"></script>