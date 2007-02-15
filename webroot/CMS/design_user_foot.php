<?
	echo '</div>';	//end center holder

	echo '<div style="float: left;">';	//start right holder

		$content = '';
		$fileCatId = 0;
			
		if (!empty($_GET['fcid']) && is_numeric($_GET['fcid'])) {
			$fileCatId = $_GET['fcid'];
			$fileCatName = getFileCategoryName($db, FILETYPE_PHOTOALBUM, $fileCatId);
		} else {
			$temp = getFileCategories($db, FILETYPE_PHOTOALBUM, $show, true);
			if ($temp) {
				$fileCat = $temp[0];
				$fileCatId = $fileCat['categoryId'];
				$fileCatName = $fileCat['categoryName'];
			}
		}
		if ($fileCatId) {

			$temp = getFilesByCategory($db, FILETYPE_PHOTOALBUM, $show, $fileCatId);
			$currentFileId = 0;
			if (!empty($_GET['fid']) && is_numeric($_GET['fid'])) $currentFileId = $_GET['fid'];
			if ($currentFileId < 0 || $currentFileId >= count($temp)) $currentFileId = 0;
			$file = $temp[$currentFileId];

			if ($file) {
				$file_name = $config['upload_dir'].$file['fileId'];
				list($t_width, $t_height) = resizeImageCalc($file_name, $config['thumbnail_width'], $config['thumbnail_height']);

				$content .= 
						'<div id="user_minifotoalbum">'.
						'<a href="photos_show.php?id='.$file['fileId'].'">'.
						'<img src="file.php?id='.$file['fileId'].'&width='.$t_width.'" width='.$t_width.' height='.$t_height.' title="'.$file['fileName'].'">'.
						'</a></div>';
							
				$content .= '<table width="100%" cellpadding=0 cellspacing=0 border=0><tr>';
				$content .= '<td align="center">';
				if ($currentFileId && (count($temp)>1)) {
					$content .= '<a href="'.$_SERVER['PHP_SELF'].'?fcid='.$fileCatId.'&fid='.($currentFileId-1).'"><img src="icons/arrow_prev.png" width=11 height=12 title="Previous"></a>';
				} else {
					$content .= '<img src="icons/arrow_prev_gray.png" width=11 height=12></a>';
				}
				$content .= '</td>';

				$content .= '<td align="center">';
				if ((count($temp)-1) <= $currentFileId) {
					$content .= '<img src="icons/arrow_next_gray.png" width=11 height=12>';
				} else {
					$content .= '<a href="'.$_SERVER['PHP_SELF'].'?fcid='.$fileCatId.'&fid='.($currentFileId+1).'"><img src="icons/arrow_next.png" width=11 height=12 title="Next"></a>';
				}
				$content .= '</td>';
				$content .= '</tr></table>';
	
				$comment = getLastComment($db, COMMENT_FILE_DESC, $file['fileId']);
				if ($comment) {
					$comment = $comment['commentText'];
					if (mb_strlen($comment) > 20) {
						$comment = mb_substr($comment, 0, 20).'...';
					}
				} else {
					$comment = 'Ingen bildetekst';
				}
				$content .= '<b>'.$comment.'</b><br>';
				$content .= '<a href="photos_comments.php?id='.$file['fileId'].'">Legg inn kommentar</a><br>';

				if (mb_strlen($fileCatName) > 10) {
					$fileCatName = mb_substr($fileCatName, 0, 10).'...';
				}
				$content .= 'Album: <a href="photoalbums_show.php?id='.$fileCatId.'">'.$fileCatName.'</a>';
			} else {
				if ($_SESSION['userId'] == $show) {
					$content = '<a href="photoalbums.php">Last opp bilde</a>';
				} else {
					$content = 'Brukeren har ikke laddat upp n&aring;gra foton.';
				}
			}
		} else {
			if ($_SESSION['userId'] == $show) {
					$content = '<a href="photoalbums.php">Last opp bilde</a>';
			} else {
				$content = 'Brukeren har ikke noe fotoalbum.';
			}
		}
		$content .= '|<a href="photoalbums.php?id='.$show.'">Velg album ...</a>';

		echo '<div id="user_fotoalbum">';
		echo MakeBox('<a href="photoalbums.php?id='.$show.'">Fotoalbum</a>', $content);
		echo '</div>';




		$content = '<a href="#" onClick="return toggle_help_box();">Klikk for &aring; l&aelig;r mer</a>';
		echo '<div id="user_minihelp_box">';
		echo MakeBox('<a href="#" onClick="return toggle_help_box();">L&aelig;r mer</a>', $content);
		echo '</div>';




		$content = '';
		$list = getUserFriendsFlat($db, $show, 5);
		if (count($list)) {
			$content .= '<table width="100%" cellpadding=0 cellspacing=0 border=0>';
			for ($i=0; $i<count($list); $i++) {
				$content .= '<tr>';
				$content .= '<td valign="top">'.nameLink($list[$i]['friendId'], $list[$i]['friendName']).'</td>';
				$content .= '<td width=14><a href="mess_new.php?id='.$list[$i]['friendId'].'"><img src="gfx/brev_send.gif" title="Send melding" width=14 height=10></a></td>';
				$content .= '</tr>';
			}
			$content .= '</table>';
		}

		if (!$content) {
			$content = 'Ingen venner p&aring; listen';
		}
		
		if ($show != $_SESSION['userId']) {
			$content .= '<br><br><a href="mess_history.php?id='.$show.'">Meldings historikk</a><br>';
			if (!isFriend($db, $show)) {
				$content .= '<br>';
				$content .= '<a href="mess_new.php?id='.$show.'">Send melding</a><br><br>';
				$content .= '<a href="user_edit_relation.php?id='.$show.'">Legg til venner</a>';
			} else {
				$content .= '<br><br>Ni er venner,<br><a href="user_edit_relation.php?id='.$show.'">Avbryt</a>';
			}
		} else {
			/* Visa nya meddelanden */
			$cnt = userHasNewMessages($db, $_SESSION['userId']);
			if ($cnt == 1) {
				$content .= '<br><a href="mess_show_unread.php">'.$cnt.' nytt meddelande</a><br><br>';
			} else {
				if ($cnt) $content .= '<br><a href="mess_show_unread.php">'.$cnt.' nya meddelanden</a><br><br>';
			}
			
			/* Visa p&aring;g&aring;ende friend-requests */
			$list = getSentFriendRequests($db, $show);
			if (count($list)) $content .= '<br><br>Sende foresp&oslash;rsel:<br>';
			for ($i=0; $i<count($list); $i++) {
				$content .= nameLink($list[$i]['recieverId'], $list[$i]['recieverName']).'<br>';
			}

			$list = getRecievedFriendRequests($db, $show);
			if (count($list)) $content .= '<br><br>Mottatte foresp&oslash;rsler:<br>';
			for ($i=0; $i<count($list); $i++) {
				$content .= '<a href="user_relationrequest.php?id='.$list[$i]['reqId'].'">'.$list[$i]['senderName'].'</a><br>';
			}
		}
		$content .= '|<a href="user_relations.php?id='.$show.'">Mer ...</a>';

		echo '<div id="user_friendlist">';
		echo MakeBox('<a href="user_relations.php?id='.$show.'">Venner</a>', $content);
		echo '</div>';

	echo '</div>';	//end right holder

	echo '<div id="user_help_holder" style="float: left; display: none;">';	//start help holder
		$content =
			'<b>Finn en venn</b><br>'.
			'Surfer du rundt p&aring; andre brukeres profiler, kan du klikke p&aring; linken "Venner", og deretter velge "Legg til venner"<br><br>'.

			'<b>Last opp bilder i fotoalbumet ditt</b><br>'.
			'Klikk p&aring; linken "fotoalbum". F&oslash;r du kan laste opp et nytt bilde, m&aring; du lage et album som bildet kan h&oslash;re hjemme i. Klikk p&aring; linken "Lage nytt album" og skriv inn navnet p&aring; albumet i det tomme tekstfeltet. N&aring; kan du klikke p&aring; linken "Laste opp bilde", velge bilde og starte opplastingen. Har du laget flere fotoalbum, kan du velge mellom disse i menyen p&aring; opplastingssiden.<br><br>'.

			'<b>Skrive blogger</b><br>'.
			'For &aring; skrive blogger klikker du p&aring; linken "Blogg" nederst i venstre hj&oslash;rne.<br>'.
			'Her kan du velge "Lage ny blogg" og skrive ned meldingen din. Er du ekstra ivrig kan du ogs&aring; lage kategorier til bloggene dine ved &aring; klikke p&aring; linken "Lage ny kategori".';
		
		echo '<div id="user_help_box">';
		echo MakeBox('Hjelp|<a href="#" onClick="return toggle_help_box();"><img src="gfx/x.png" width=9 height=9></a>', $content);
		echo '</div>';
	echo '</div>';	//end help holder

	echo '</div>';	//end user page holder

	echo '<div id="user_ads">'; //reklam holder
		echo '<img src="esp_design/reklame.gif">';
	echo '</div>';

?>
</div>