<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
<title>Agent Interactive tracker</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="css/main.css" type="text/css">
<link rel="shortcut icon" href="design/ai_icon.ico" type="image/x-icon">
<script type="text/javascript" src="js/functions.js"></script>
<script type="text/javascript" src="js/calendar.js"></script>
<script type="text/javascript" src="js/domLib.js"></script>
<script type="text/javascript" src="js/domTT.js"></script>
<script type="text/javascript" src="js/formatDate.js"></script>
<!-- <script type="text/javascript" src="js/ieerbug/ieerbug.js"></script> -->
</head>

<body onLoad="ImagePreload();">
	
<div id="menu_holder">
	<div id="menu_left">
		<center><a href="<?=$config['start_page']?>"><img src="design/ai_logo.png" alt="Agent Interactive" width=131 height=68></a></center><br>
<?
		if (!$_SESSION['loggedIn']) {
			echo 'You are not logged in.<br><br>';
			echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
			echo 'Username: <input type="text" name="usr" size=10><br><br>';
			echo 'Password: <input type="password" name="pwd" size=10><br><br>';
			echo '<input type="submit" class="button" value="Log in">';
			echo '</form>';
		} else {
			
			//select a track site
			//$list = getTrackSites($db, $_SESSION['userId']);
			$list = getTrackSites($db);
			echo 'Track sites:<br>';
			for ($i=0; $i<count($list); $i++) {
				echo '&nbsp;&raquo; <a href="admin_show_tracksite.php?id='.$list[$i]['siteId'].'">'.$list[$i]['siteName'].' ('.$list[$i]['cnt'].')</a><br>';
			}

			$script = basename($_SERVER['SCRIPT_NAME']);

			if ($_SESSION['isSuperAdmin']) {
				echo '&nbsp;&raquo; ';
				if ($script == 'admin_new_tracksite.php') echo '<b>';
				echo '<a href="admin_new_tracksite.php">New track site</a><br>';
				if ($script == 'admin_new_tracksite.php') echo '</b>';
				echo '<br>';
			}

			if (!empty($siteId)) {
				echo '&nbsp;&raquo; ';
					if ($script == 'admin_show_tracksite.php') echo '<b>';
					echo '<a href="admin_show_tracksite.php?id='.$siteId.'&amp;show">Track site overview</a><br>';
					if ($script == 'admin_show_tracksite.php') echo '</b>';

				echo '&nbsp;&raquo; ';
				if ($script == 'admin_subscribe_tracksite.php') echo '<b>';
				echo '<a href="admin_subscribe_tracksite.php?id='.$siteId.'">Subscriptions</a><br>';
				if ($script == 'admin_subscribe_tracksite.php') echo '</b>';

				echo '&nbsp;&raquo; ';
				if ($script == 'admin_edit_tracksite.php') echo '<b>';
				echo '<a href="admin_edit_tracksite.php?id='.$siteId.'">Edit track site</a><br>';
				if ($script == 'admin_edit_tracksite.php') echo '</b>';

				//show selected track site's track points:
				echo '<br>';
				$list = getTrackPoints($db, $siteId);
				if (count($list)) {
					echo 'Track points:<br>';
					for ($i=0; $i<count($list); $i++) {
						echo '&nbsp;&raquo; <a href="admin_show_trackpoint.php?id='.$list[$i]['trackerId'].'">'.$list[$i]['location'].'</a><br>';
					}
				} else {
					echo '<b>No track points exists!</b><br>';
				}
				echo '&nbsp;&raquo; ';
				if ($script == 'admin_new_trackpoint.php') echo '<b>';
				echo '<a href="admin_new_trackpoint.php?id='.$siteId.'">New track point</a><br><br>';
				if ($script == 'admin_new_trackpoint.php') echo '</b>';
	
				if (!empty($trackId)) {
					
					echo '&nbsp;&raquo; ';
					if ($script == 'admin_show_trackpoint.php') echo '<b>';
					echo '<a href="admin_show_trackpoint.php?id='.$trackId.'">Track point overview</a><br>';
					if ($script == 'admin_show_trackpoint.php') echo '</b>';

					echo '<br>';

					echo '&nbsp;&raquo; ';
					if ($script == 'admin_edit_trackpoint.php') echo '<b>';
					echo '<a href="admin_edit_trackpoint.php?id='.$trackId.'">Edit track point</a><br>';
					if ($script == 'admin_edit_trackpoint.php') echo '</b>';

					echo '&nbsp;&raquo; ';
					if ($script == 'admin_delete_trackpoint.php') echo '<b>';
					echo '<a href="admin_delete_trackpoint.php?id='.$trackId.'">Delete track point</a><br><br>';
					if ($script == 'admin_delete_trackpoint.php') echo '</b>';
				}
				
			}

			if ($_SESSION['isSuperAdmin']) {
				if ($script == 'admin_tasks.php') echo '<b>';
				echo '&nbsp;&raquo; <a href="admin_tasks.php">Administrative tasks</a><br>';
				if ($script == 'admin_tasks.php') echo '</b>';
			}
			
			echo '&nbsp;&raquo; <a href="'.$_SERVER['PHP_SELF'].'?logout">Log out</a>';
		}
?>
		<br>
	</div><!-- end menu_left -->
	<div id="menu_middle">
<!-- head end -->