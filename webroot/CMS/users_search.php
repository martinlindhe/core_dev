<?
	include('include_all.php');

	include('design_head.php');
	include('design_forum_head.php');

	setUserStatus($db, 'S&ouml;ker efter anv&auml;ndare');
	
	$content = 'S&oslash;k etter bruker<br><br>';

	if (isset($_POST['c'])) {
		//$list = getUserSearchResult($db, $_POST);
		$list = getUserSearchResultOnNickname($db, $_POST['c']);

		$content .= 'S&oslash;keresultat p&aring; "'.$_POST['c'].'", ';
		if (count($list) == 1) $content .= '1 treff:';
		else $content .= count($list).' treff:';
		$content .= '<br><br>';
		
		$ids = '';
		for ($i=0; $i<count($list); $i++) {
			$content .= nameLink($list[$i]['userId'], $list[$i]['userName']).'<br>';
			$ids .= $list[$i]['userId'].';';
		}
		$content .= '<br>';
		$content .= '<a href="'.$_SERVER['PHP_SELF'].'">Nytt s&oslash;k</a><br>';
		/*
		if ($_SESSION['isAdmin']) {
			$content .= '<a href="mess_multi.php?ids='.$ids.'">Skicka meddelande till alla tr&auml;ffar</a><br>';
			$content .= '<a href="mail_multi.php?ids='.$ids.'">Skicka mail till alla tr&auml;ffar</a><br>';
		}*/
		$content .= '<br>';

	} else if (isset($_GET['l']) && $_GET['l']) {
		/* Lista alla användare som börjar på en bokstav */
		
		//$list = searchUsernameBeginsWith($db, $_GET['l']);
		$list = searchNicknameBeginsWith($db, $_GET['l']);

		$content .= 'S&oslash;keresultat p&aring; brukere som begynner med "'.$_GET['l'].'", ';

		if (count($list) == 1) $content .= '1 treff:';
		else $content .= count($list).' treff:';
		$content .= '<br><br>';
		
		for ($i=0; $i<count($list); $i++) {
			$content .= nameLink($list[$i]['userId'], $list[$i]['userName']).'<br>';
		}

		$content .= '<br>';
		$content .= '<a href="'.$_SERVER['PHP_SELF'].'">Nytt s&oslash;k</a><br><br>';

	} else {

		$content .= 'Sortere brukere som begynner p&aring;: ';
		for ($i=ord('A'); $i<=ord('Z'); $i++) {
			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?l='.chr($i).'">'.chr($i).'</a> ';
		}
		/*
		$content .= '<a href="'.$_SERVER['PHP_SELF'].'?l=&Aring;">&Aring;</a> ';
		$content .= '<a href="'.$_SERVER['PHP_SELF'].'?l=&Auml;">&Auml;</a> ';
		$content .= '<a href="'.$_SERVER['PHP_SELF'].'?l=&Ouml;">&Ouml;</a> ';
		*/
		$content .= '<a href="'.$_SERVER['PHP_SELF'].'?l=&AElig;">&AElig;</a> ';
		$content .= '<a href="'.$_SERVER['PHP_SELF'].'?l=&Oslash;">&Oslash;</a> ';
		$content .= '<a href="'.$_SERVER['PHP_SELF'].'?l=&Aring;">&Aring;</a> ';
		$content .= '<br><br>';
		
		$content .= getInfoField($db, 'sok anvandare');

		$content .= '<form name="searchusers" method="post" action="'.$_SERVER['PHP_SELF'].'">';
		$content .= '<table width="100%" cellpadding=0 cellspacing=0 border=0>';
		$content .= '<tr>';
			$content .= '<td width="15%" valign="top">Fritekst:</td>';
			$content .= '<td><input type="text" name="c" maxlength=20 size=20></td>';
		$content .= '</tr>';
		
		$list = getUserdataFields($db);
		for ($i=0; $i<count($list); $i++) {
			if (($list[$i]['fieldAccess'] == 2) || ($_SESSION['isAdmin'])) {

				switch ($list[$i]['fieldType']) {
					case USERDATA_TYPE_CHECKBOX:
						$content .= '<tr>';
							$content .= '<td valign="top">'.$list[$i]['fieldName'].':</td>';
							$content .= '<td><input name="'.$list[$i]['fieldId'].'" type="checkbox" value="1" class="checkbox"> Kr&auml;v</td>';
						$content .= '</tr>';
						break;
					
					case USERDATA_TYPE_RADIO:
						$content .= '<tr>';
							$content .= '<td valign="top">'.$list[$i]['fieldName'].'</td>';
							$content .= '<td>';

							$sublist = getUserdataFieldOptions($db, $list[$i]['fieldId']);
							$content .= '<input name="'.$list[$i]['fieldId'].'" type="radio" value="0" checked class="radiostyle">-kvittar-';
							for ($j=0; $j<count($sublist); $j++) {
								$content .= '<input type="radio" name="'.$list[$i]['fieldId'].'" value="'.$sublist[$j]['optionId'].'" class="radiostyle">'.$sublist[$j]['optionName'];
							}
							$content .= '</td>';
						$content .= '</tr>';
						break;

					case USERDATA_TYPE_SELECT:
						$content .= '<tr>';
							$content .= '<td valign="top">'.$list[$i]['fieldName'].'</td>';
							$content .= '<td>';

							$sublist = getUserdataFieldOptions($db, $list[$i]['fieldId']);
							$content .= '<select name="'.$list[$i]['fieldId'].'">';
							$content .= '<option value="0">-kvittar-';
							for ($j=0; $j<count($sublist); $j++) {
								$content .= '<option value="'.$sublist[$j]['optionId'].'">'.$sublist[$j]['optionName'];
							}
							$content .= '</select>';
							$content .= '</td>';
						$content .= '</tr>';
						break;
				}
			}
			
		}

		$content .= '<tr><td colspan=2><br><input type="submit" class="button" value="'.$config['text']['link_search'].'"></td></tr>';
		$content .= '</table>';
		$content .= '</form>';
	}

	echo '<div id="user_forum_content">';
	echo MakeBox('<a href="forum.php">Forum</a>|S&oslash;k etter bruker', $content, 500);
	echo '</div>';


	include('design_forum_foot.php');
	include('design_foot.php');
?>
<script type="text/javascript">
if (document.searchusers) document.searchusers.c.focus();
</script>