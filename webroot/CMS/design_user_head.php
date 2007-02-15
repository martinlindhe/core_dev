<div style="width: 600px">
<?
	if (!isset($show)) {
		$show = $_SESSION['userId'];
		$showname = $_SESSION['userName'];
	}

	echo '<div id="user_holder">';	//start user page holder

			$c2 = '';

			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="user.php">Min profil</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="forum.php">Forum</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="forum_search.php">S&oslash;k i forum</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="forum_latest.php">Siste innlegg</a><br>';

			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="blogs.php">Blogger</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="photoalbums.php">Fotoalbum</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="user_relations.php">Mine venner</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="user_guestbook.php">Gjestebok</a><br>';

			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="users_online.php">Brukere online</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="users_search.php">S&oslash;k etter bruker</a><br>';

		echo '<table width="100%" cellpadding=0 cellspacing=0 border=0>';
		echo '<tr><td align="center"><a href="forum.php"><img src="esp_design/soip-logo.gif" width=496 height=30></a></td></tr>';
		echo '<tr><td align="center">'.$c2.'</td></tr>';
		echo '</table><br>';

	echo '<div style="float: left;">';	//start left holder

		$content = '';

		//Visa uppladdad avatar
		$userpic = getThumbnail($db, $show, 'Egen avatar', $config['thumbnail_width'], $config['thumbnail_height'], false);
		if ($userpic) {
			$content .= '<center>'.$userpic.'</center>';
		} else {
			//Visa vald avatar
			$choosen_avatar = getUserSetting($db, $show, 'avatar');
			if ($choosen_avatar && is_numeric($choosen_avatar)) {
				$content .= '<center><img src="avatars/'.$config['avatars'][$choosen_avatar].'" width=80 height=80></center>';
			} else {
				$content .= '<center><img src="gfx/nopict_text.jpg" title="Bild saknas" width=102 height=93></center>';
			}
		}

		$nickname = getUserdataByFieldname($db, $show, 'Nickname');
		if (!$nickname) $nickname = 'inget nickname';

		$content .= 'Nick: '.$nickname.'<br>';
		$user_age = getUserSetting($db, $show, 'age');
		if ($user_age) $content .= '&Aring;lder: '.$user_age.'<br>';
		$content .= 'Status: '.getUserStatus($db, $show);

		if ($show == $_SESSION['userId']) $content .= '|<a href="user_edit.php">Lag profil</a>';

		echo '<div id="user_profile">';
		$adminlink = '';
		if ($_SESSION['isAdmin']) $adminlink = '|<a href="admin.php">Admin</a>';
		echo MakeBox('<a href="user.php">Profil</a>'.$adminlink, $content);
		echo '</div>';



		$content = '';
		$list  = getFavoriteGames($db, $show, 4);
		for ($i=0; $i<count($list); $i++) {
			$content .= $list[$i]['categoryName'].'<br>';
		}
		if (!count($list)) $content = 'Brukeren har ingen favoritt spill.';
		echo '<div id="user_favorite_games">';
		echo MakeBox('Spill jeg liker', $content);
		echo '</div>';

		$list = getGuestbookItems($db, $show, 3);
		$content = '';
		for ($i=0; $i<count($list); $i++) {
			$text = $list[$i]['body'];
			if (mb_strlen($text) > 20) {
				$text = mb_substr($text, 0, 19).'...';
			}
			$content .= '<i><b>'.$text.'</b></i><br>fra '.nameLink($list[$i]['authorId'], $list[$i]['authorName']).'<br>';
		}
		if (!$content) $content = 'Gjesteboken er tom';
		$content .= '|<a href="user_guestbook.php?id='.$show.'">Mer ...</a>';
		echo '<div id="user_guestbook">';
		echo MakeBox('<a href="user_guestbook.php?id='.$show.'">Gjestebok</a>', $content);
		echo '</div>';




		$list = getBlogs($db, $show, 3);
		$content = '';
		for ($i=0; $i<count($list); $i++) {
			$title = $list[$i]['blogTitle'];
			if (mb_strlen($title) > 10) {
				$title = mb_substr($title, 0, 9).'...';
			}
			$content .= formatShortDate($list[$i]['timeCreated']).': <a href="blog_show.php?id='.$list[$i]['blogId'].'">'.$title.'</a><br>';
		}
		$content .= '<br>';

		for ($i=0; $i<3; $i++) {
			//den 1:a i aktuell månad:
			$month_timestamp = mktime(0, 0, 0, date('n')-$i, date('d'), date('Y'));
			$year = date('Y', $month_timestamp);
			$month = date('n', $month_timestamp);
			$content .= '<a href="blogs_archive.php?y='.$year.'&m='.$month.'">Arkiv '.formatShortMonth($month_timestamp).'</a><br>';
		}
		$content .=
			'|<a href="blogs.php?id='.$show.'">Mer ...</a>';
		echo '<div id="user_blogs">';
		echo MakeBox('<a href="blogs.php?id='.$show.'">Blogg</a>', $content);
		echo '</div>';
		
	echo '</div>';	//end left holder
	
	echo '<div style="float: left;">';	//start center holder
?>