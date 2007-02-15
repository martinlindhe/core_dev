<?
	include('include_all.php');

	if (!$_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}

	setUserStatus($db, 'Redigerer sin side');

	/* Post-data för personliga inställningar */
	$chg_error = '';
	if (isset($_POST['userinfo'])) {
		$list = getUserdataFields($db);
		for ($i=0; $i<count($list); $i++) {
			if ($list[$i]['fieldType'] == USERDATA_TYPE_IMAGE) {
				/* handle file-upload */

				if ($_FILES[$list[$i]['fieldId']]['name']) {
					$fileId = handleFileUpload($db, $_SESSION['userId'], FILETYPE_USERDATAFIELD, $_FILES[ $list[$i]['fieldId']]);
					if (is_numeric($fileId)) {
						setUserdata($db, $_SESSION['userId'], $list[$i]['fieldId'], $fileId);
					} else {
						$chg_error .= '<li><b>Kunne ikke laste opp bilde: '.$fileId.'</b><br><br>';
					}
				}

				if (isset($_POST[$list[$i]['fieldId'].'_remove'])) {
					/* Remove the file */
					deleteFile($db, getUserdataValue($db, $list[$i]['fieldId']));
					setUserdata($db, $_SESSION['userId'], $list[$i]['fieldId'], '');
				}

			} else if ($list[$i]['fieldType'] == USERDATA_TYPE_DATE) {
				
				/* Handle date-type fields */
				if (isset($_POST[$list[$i]['fieldId'].'_day']) &&
					isset($_POST[$list[$i]['fieldId'].'_month']) &&
					isset($_POST[$list[$i]['fieldId'].'_year']) &&
					$_POST[$list[$i]['fieldId'].'_day'] &&
					$_POST[$list[$i]['fieldId'].'_month'] &&
					$_POST[$list[$i]['fieldId'].'_year'])
				{
					
					$save_day   = $_POST[$list[$i]['fieldId'].'_day'];
					$save_month = $_POST[$list[$i]['fieldId'].'_month'];
					$save_year = $_POST[$list[$i]['fieldId'].'_year'];
					
					if (is_numeric($save_day) && is_numeric($save_month) && is_numeric($save_year) && checkdate($save_month,$save_day,$save_year)) {
						$datestamp = $save_year.$save_month.$save_day;

						setUserdata($db, $_SESSION['userId'], $list[$i]['fieldId'], $datestamp);
					} else {
						$chg_error .= '<li><b>Ogiltigt datum</b><br>';
					}
				}

			} else if ($list[$i]['fieldName'] == 'Nickname') {
				/* special case för ESP */
				
				if (isset($_POST[$list[$i]['fieldId']])) {
					if (isReservedUsername($db, $_POST[$list[$i]['fieldId']])) {
						$chg_error .= 'Brukernavnet er ikke aksepteret!<br>';
					} else {
						//hack för att kolla om nickname är unikt:
						$value = dbAddSlashes($db, $_POST[$list[$i]['fieldId']]);
						$sql  = 'SELECT COUNT(t1.userId) FROM tblUserdata AS t1 ';
						$sql .= 'INNER JOIN tblUserdataFields AS t2 ON (t1.fieldId=t2.fieldId) ';
						$sql .= 'WHERE t2.fieldName="Nickname" AND t1.value="'.$value.'" AND t1.userId!='.$_SESSION['userId'];
						$check = dbOneResultItem($db, $sql);
						if ($check) {
							$chg_error .= 'Brukernavnet er opptatt!<br>';
						} else {
							setUserdata($db, $_SESSION['userId'], $list[$i]['fieldId'], $_POST[$list[$i]['fieldId']]);
						}
					}
				}

			} else if (isset($_POST[$list[$i]['fieldId']])) {
				
				/*
				if ($list[$i]['fieldName'] == USERFIELD_EMAIL) {
					if (ValidEmail($_POST[$list[$i]['fieldId']])) {
						setUserdata($db, $_SESSION['userId'], $list[$i]['fieldId'], $_POST[$list[$i]['fieldId']]);
					} else if ($_POST[$list[$i]['fieldId']]) {
						$chg_error .= '<li><b>Ogiltig epost-address!</b><br>';
					}
				}
				*/
				if (strlen($_POST[$list[$i]['fieldId']]) <= $config['userdata']['maxsize_text']) {
					setUserdata($db, $_SESSION['userId'], $list[$i]['fieldId'], $_POST[$list[$i]['fieldId']]);
				} else {
					$chg_error .= '<li><b>F&auml;ltet "'.$list[$i]['fieldName'].'" f&aring;r inte inneh&aring;lla mer &auml;n '.$config['userdata']['maxsize_text'].' tecken! var got korta ner inneh&aring;llet</b><br>';
				}
			} else if ($list[$i]['fieldType'] == USERDATA_TYPE_CHECKBOX) {
				/* turn off checkbox */
				setUserdata($db, $_SESSION['userId'], $list[$i]['fieldId'], 'off');
			}
		}

	}



	/*
	//Avbryt bevakningar
	if (isset($_GET['unsubscribe'])) {
		removeSubscription($db, $_GET['unsubscribe'], SUBSCRIBE_MAIL);
	}
	if (isset($_GET['unsubscribeall']) && $_GET['unsubscribeall']) {
		removeAllSubscriptions($db, $_SESSION['userId'], SUBSCRIBE_MAIL);
	}*/

	include('design_head.php');
	include('design_user_head.php');

	$content = '';

		$content = '<div style="float:right; width:150px;">';
			$c2  = '<a href="user_edit_presentation.php">Endre presentasjon</a><br><br>';
			$c2 .= '<a href="user_edit_avatar.php">Velg avatar</a><br><br>';
			$c2 .= '<a href="user_edit_games.php">Velg spill jeg liker</a><br><br>';
			if ($_SESSION['isAdmin']) $c2 .= '<a href="user_edit_password.php">&Auml;ndra l&ouml;senord</a><br><br>';
			$c2 .= '<a href="user_show.php?id='.$_SESSION['userId'].'">Se p&aring; min side</a>';
			$content .= MakeBox('|Valg', $c2);
		$content .= '</div>';

	if ($chg_error) {
		$content .= '<span class="objectCritical">'.$chg_error.'</span><br>';
	}
	
	$content .= 'Her kan du redigere din profil. I ruten til h&oslash;yre ser du linker til andre deler av profilen.<br><br>';
	$content .= 'Forumsignaturen vil synes p&aring; innleggene dine in forumet.<br><br>';
	
	$content .= getUserDatafieldsHTMLEdit($db, $_SESSION['userId']).'<br>';



	//Debattövervakningar
	/*
	$list = getUserSubscriptions($db, $_SESSION['userId']);
	$content .= '<a name="subscriptions"></a>';
	$content .= '<b>Debattoverv&aring;king</b><br><br>';
	$content .= '<table width="100%" cellpadding=0 cellspacing=0 border=0>';

	if (count($list)) {
		for ($i=0; $i<count($list); $i++) {
			$content .= '<tr>';
			$content .= '<td>'.getForumItemDepthHTML($db, $list[$i]['itemId']).'</td>';
			$content .= '<td align="right"><a href="'.$_SERVER['PHP_SELF'].'?id='.$_SESSION['userId'].'&unsubscribe='.$list[$i]['itemId'].'#subscriptions">Avbryt</td>';
			$content .= '</tr>';
		}

		$content .= '<tr><td colspan=2 align="right"><a href="'.$_SERVER['PHP_SELF'].'?id='.$_SESSION['userId'].'&unsubscribeall=1">Avbryt alla</a></td></tr>';
		
	} else {
		$content .= '<tr><td>Du overv&aring;ker ingen debatter.</td></tr>';
	}
	$content .= '</table>';
	*/

		echo '<div id="user_misc_content">';
		echo MakeBox('<a href="'.$_SERVER['PHP_SELF'].'">Lag profil</a>', $content);
		echo '</div>';

	include('design_user_foot.php');
	include('design_foot.php');
?>