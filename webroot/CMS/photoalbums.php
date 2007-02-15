<?
	include('include_all.php');

	if (!$_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}

	$show = '';
	if (isset($_GET['id'])) {
		$show = $_GET['id'];
		$showname = getUserName($db, $show);
		if (!$showname) {
			header('Location: '.$config['start_page']);
			die;
		}
	} else {
		$show = $_SESSION['userId'];
		$showname = $_SESSION['userName'];
	}

	if (substr($showname, -1) == 's') {
		$niceshowname = $showname."'";
	} else {
		$niceshowname = $showname.'s';
	}

	if ($show == $_SESSION['userId']) {
		setUserStatus($db, 'Ser p&aring; sina fotoalbum');
	} else {
		setUserStatus($db, 'Ser p&aring; '.$niceshowname.' fotoalbum');
		logVisitor($db, $show);
	}

	include('design_head.php');
	include('design_user_head.php');

		$content = '';

		$list = getFileCategories($db, FILETYPE_PHOTOALBUM, $show);

		if ($show == $_SESSION['userId']) {
			$content .= '<div style="float:right; width:150px;">';
				$c2 = '<a href="photoalbums_categories.php?id='.$show.'">Lage ett nytt album</a><br><br>';
				if (count($list)) {
					$c2 .= '<a href="photoalbums_upload.php">Laste opp et bilde</a>';
				} else {
					$c2 .= 'Du m&aring; lage et fotoalbum f&oslash;r du kan laste opp bilder.';
				}
				$content .= MakeBox('|Valg', $c2);
			$content .= '</div>';

			$content .=
				'Her kan du laste opp og sortere dine bilder i album, '.
				's&aring; du enkelt kan vise dem til dine venner.<br><br><br>';
		} else {
			$content .=
				//'Detta &auml;r en annan anv&auml;ndares fotoalbum. Om du vill bes&ouml;ka ditt eget album klickar du <a href="photoalbums.php">h&auml;r</a>.<br><br><br>';
				'Dette er en annen brukers fotoalbum. Om du vil bes&oslash;ke ditt eget album klikker du <a href="photoalbums.php">her</a>.<br><br><br>';

		}

		if (count($list)) {
			$content .= '<b>'.$niceshowname.' fotoalbum:</b><br>';
			for ($i=0; $i<count($list); $i++) {
				$content .= '<a href="photoalbums_show.php?id='.$list[$i]['categoryId'].'">'.$list[$i]['categoryName'].'</a> ';
				$content .= '('.$list[$i]['fileCount'];
				if ($list[$i]['fileCount'] == 1) $content.= ' bild)<br>';
				else  $content.= ' bilder)<br>';
			}
		} else {
			$content .= '<span class="objectCritical">Brukeren har ikke laget noe fotoalbum.</span>';
		}

		echo '<div id="user_fotoalbum_content">';
		echo MakeBox('<a href="photoalbums.php?id='.$show.'">Fotoalbum</a>', $content);
		echo '</div>';

	include('design_photos_foot.php');
	include('design_foot.php');

?>