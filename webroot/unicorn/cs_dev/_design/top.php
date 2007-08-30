<?
	if ($user->id) $theme_css = $user->getinfo($user->id, 'det_tema');
	if (empty($theme_css)) $theme_css = 'default.css';

	if (empty($NAME_TITLE)) $NAME_TITLE = 'CitySurf';

	if (empty($charset)) $charset = 'UTF-8'; //annars ISO-8859-1
	
	@header('Content-Type: text/html; charset='.$charset);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$charset?>"/>
<title><?=$NAME_TITLE?></title>
<meta name="description" content=""/>
<meta http-equiv="imagetoolbar" content="no"/>
<meta http-equiv="content-language" content="se"/>
<meta name="keywords" content=""/>
<meta name="author" content=""/>
<meta name="robots" content="follow,index"/>
<meta name="language" content="sv-SE"/>
<link rel="stylesheet" type="text/css" href="<?=$config['web_root']?>_gfx/themes/screen.css"/>
<link rel="stylesheet" type="text/css" href="<?=$config['web_root']?>_gfx/themes/common.css"/>
<link rel="stylesheet" type="text/css" href="<?=$config['web_root']?>_gfx/themes/<?=$theme_css?>"/>
<link rel="shortcut icon" href="<?=$config['web_root']?>favicon.ico"/>
<script type="text/javascript" src="<?=$config['web_root']?>js/main1.js"></script>
<script type="text/javascript" src="<?=$config['web_root']?>js/flash_obj.js"></script>
<script type="text/javascript" src="<?=$config['web_root']?>js/fol.js"></script>
<script type="text/javascript" src="<?=$config['web_root']?>js/ajax.js"></script>
