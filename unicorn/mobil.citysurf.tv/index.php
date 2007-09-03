<?
	require_once('config.php');
	require('design_head.php');

	if (!$user->id) {

		echo '<a href="login.php">LOGGA IN</a><br/><br/>';

		echo 'Välkommen till första versionen av Citysurf i mobilen. Vi tar gärna emot synpunkter via tyck till på Huvudsajten.<br/>';

	} else {
		echo '<a href="relations.php"><img src="gfx/btn_friends.png" alt="Vänner" width="44" height="44"/></a>&nbsp;';
		echo '<a href="users_last_online.php"><img src="gfx/btn_online.png" alt="Senast online" width="44" height="44"/></a>&nbsp;';
		echo '<a href="search_users.php"><img src="gfx/btn_search.png" alt="Sök användare" width="44" height="44"/></a><br/>';

		echo '<a href="surftalk.php"><img src="gfx/btn_surftalk.png" alt="Surftalk" width="44" height="44"/></a>&nbsp;';
		echo '<a href="info.php"><img src="gfx/btn_info.png" alt="Info" width="44" height="44"/></a>&nbsp;';
		echo '<a href="logout.php"><img src="gfx/btn_logout.png" alt="Logga ut" width="44" height="44"/></a><br/>';

		//echo '<a href="gb.php">DIN GÄSTBOK</a> ('.gbCountUnread().' olästa)<br/>';
		//echo '<a href="mail.php">DIN MAIL</a>('.getUnreadMailCount().' olästa)<br/>';
		//echo '<a href="friends.php">DINA VÄNNER</a>('.relationsOnlineCount().' online)<br/>';
		//echo '<a href="blocked.php">DINA BLOCKERINGAR</a><br/>';
		//echo '<a href="user.php">DIN PROFIL</a><br/>';
		//echo '<a href="search_users.php">SÖK ANVÄNDARE</a><br/>';
		//echo '<a href="users_last_online.php">SENAST ONLINE</a><br/>';
		//echo '<br/>';
		//echo '<a href="logout.php">LOGGA UT</a><br/>';
	}

	require('design_foot.php');
?>
