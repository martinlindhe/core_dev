<?
	//<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">
<head>
<title>CitySurf mobil</title>
<link rel="stylesheet" href="css/mobile.css" type="text/css"/>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
</head>
<body>

<div id="top">

<a href="index.php"><img src="gfx/logo_256.png" alt="citysurf.tv"/></a><br/>
<?
	if (!empty($s['id_id'])) {
		echo '<a href="user.php"><img src="gfx/btn_profile.png" alt="Profil" width="44" height="44"/></a> ';
		echo '<a href="mail.php"><img src="btn.php?mail" alt="Mail" width="44" height="44"/></a> ';
		echo '<a href="gb.php"><img src="btn.php?gb" alt="Gästbok" width="44" height="44"/></a><br/>';
	}

	if (basename($_SERVER['SCRIPT_NAME']) != 'index.php') {
		echo '</div><div id="main">';
	}	
?>