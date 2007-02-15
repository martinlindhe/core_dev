<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?
//	if ($_SESSION['browser']['name'] == 'MSIE') {
//		$browser_css = 'main_ie.css';
//	} else {
		$browser_css = 'main_ff.css';
//	}
?>
<title>SoIP Games on Demand</title>
<link rel="stylesheet" href="<?=$browser_css?>" type="text/css">
<link rel="stylesheet" href="functions.css" type="text/css">
<link rel="stylesheet" href="esp.css" type="text/css">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
</head>
<body>

<script type="text/javascript">
function wnd_url(u,x,y){w=window.open(u,'','width='+x+',height='+y+',toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,directories=no,status=no');w.focus;}
function wnd_imgview(i,x,y){var w=window.open('file_show_image.php?id='+i,'','width='+x+',height='+y+',toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,directories=no,status=no');w.focus;}
function wnd_imgview_all(i,x,y){var w=window.open('file_show_image.php?id='+i+'&browse','','width='+x+',height='+(y+40)+',toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,directories=no,status=no');w.focus;}
function wnd_audplay(i){var w=window.open('file_play_audio.php?id='+i,'','width=400,height=90,toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,directories=no,status=no');w.focus;}
function toggle_div(o){var e=document.getElementById(o);if(e.style.display!="none")e.style.display="none"; else e.style.display="";}
function show_div(o){var e=document.getElementById(o);e.style.display="";}
function hide_div(o){var e=document.getElementById(o);e.style.display="none";}
function toggle_help_box(){toggle_div('user_help_holder');}
</script>
<?
	if (empty($_SESSION['browser']) || !$_SESSION['browser']['width']) {
		/* Learn the current sessions screen resolution */
?>
<script type="text/javascript">
function SetCookie(n,v){document.cookie=n+"="+escape(v);}
if (screen.width && screen.height) {
	SetCookie('BrowserWidth', screen.width);
	SetCookie('BrowserHeight', screen.height);
}
</script>
<?
	}

	if ($config['debug']) {
		if (!$_SESSION['loggedIn']) {
			echo '<a href="login.php">Logga in</a>';
		}

		if ($config['debug'] && $_SESSION['loggedIn'] && $_SESSION['isAdmin']) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?logout">Logga ut</a>';
		}
	}

?>

<div id="middle">
<center>