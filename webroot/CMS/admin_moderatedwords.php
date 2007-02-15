<?
	//todo: rensa upp html-tabellerna

	include_once('include_all.php');

	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (count($_POST)) {
		$list = getStopwords($db);

		for($i=0; $i<count($list); $i++) {
			$id = $list[$i]['wordId'];

			$del  = 'del_'.$id;
			$chg  = 'change_'.$id;
			if (isset($_POST['full_'.$id])) {
				$full = $_POST['full_'.$id];	
			} else {
				$full=0;
			}

			/* Remove */
			if (isset($_POST[$del])) {
				removeStopword($db, $id);
			} else
			
			/* Raderar även om man bara har tömt texten i fältet och tryckt Uppdatera */
			if (!$_POST[$chg]) {
				removeStopword($db, $id);
			}
			
			/* Update has less priority */
			if (($_POST[$chg] != $list[$i]['wordText']) || ($full != $list[$i]['wordMatch'])) {
				setStopword($db, $id, $_POST[$chg], $full);
			}
		}
		
		for($i=1; $i<4; $i++) {
			$word = 'newname_'.$i;
			if (isset($_POST['newfull_'.$i])) {
				$full = $_POST['newfull_'.$i];
			} else {
				$full=0;
			}
			
			if (!empty($_POST[$word])) {
				if (!addStopword($db, $_POST[$word], $i, $full)) {
					echo 'Failed to add '.$_POST[$word].'<br>';
				}
			}
		}
	}

	include('design_head.php');
	include('design_user_head.php');

	$content  = '<form name="update" method="post" action="'.$_SERVER['PHP_SELF'].'">';
	$content .= '<table width="100%" border=0 cellspacing=0 cellpadding=3>';
	$content .= '<tr>';
	
	//gå från $x=1 för att ta med STOPWORD_OBJECTIONABLE (inaktiverade)
	//gå till $x<=3 för att ta med STOPWORD_RESERVED
	for($x=2; $x<=3; $x++) {
		
		switch($x) {
			case STOPWORD_OBJECTIONABLE:
				$txt='St&ouml;tande';
				$help = 'Inl&auml;gg inneh&aring;llande st&ouml;tande ord till&aring;ts inte att publiceras.<br><br><b>Denna funktion &auml;r inaktiverad.</b>';
				break;

			case STOPWORD_SENSITIVE:
				$txt='K&auml;nsliga';
				$help = 'Inl&auml;gg inneh&aring;llande k&auml;nsliga ord hamnar automatiskt i modereringsk&ouml;n utan att inl&auml;gget blockeras.';
				break;

			case STOPWORD_RESERVED:
				$txt='Reserverade';
				$help = 'Reserverade ord &auml;r inte till&aring;tna i anv&auml;ndarnamn.';
				break;
		}

		$content .= '<td valign="top" align="center">';
		$content .= '<table width="100%" border=0 cellspacing=0 cellpadding=1 bgcolor="#000000"><tr><td>';
		$content .= '<table width="100%" border=0 cellspacing=0 cellpadding=2 bgcolor="#FFFFFF">';
		$content .= '<tr><td colspan=3 height=25 valign="top"><b>'.$txt.'</b></td></tr>';
		$content .= '<tr><td colspan=3 height=25 valign="top">'.$help.'<br><br></td></tr>';
		$content .= '<tr><td>&nbsp;</td><td valign="bottom">hela</td><td valign="bottom">'.$config['text']['link_remove'].'</td></tr>';

		$list = getStopwords($db, $x);
		for ($i=0; $i<count($list); $i++) {
			$chg  = 'change_'.$list[$i]['wordId'];
			$full = 'full_'.$list[$i]['wordId'];
			$del  = 'del_'.$list[$i]['wordId'];

			$content .= '<tr>';
			$content .= '<td><input type="text" name="'.$chg.'" value="'.$list[$i]['wordText'].'" size=16></td>';
			$content .= '<td><input type="checkbox" class="checkbox" name="'.$full.'" value=1';
			if ($list[$i]['wordMatch']==1) $content .= ' checked';
			$content .= '></td>';
			$content .= '<td><input type="checkbox" class="checkbox" name="'.$del.'"></td>';
			$content .= '</tr>';
		}
		
		$content .= '<tr>';
			$content .= '<td><br><br>L&auml;gg till nytt ord:<br><input type="text" name="newname_'.$x.'" size=16></td>';
			$content .= '<td><br><br><input type="checkbox" class="checkbox" value="1" name="newfull_'.$x.'"></td>';
			$content .= '<td>&nbsp;</td>';
		$content .= '</tr>';
		
		$content .= '</table>';
		$content .= '</td></tr></table>';
			
		if ($x<3) {
			$content .= '</td>';
		}
	}
	$content .= '</td></tr>';

	$content .= '<tr><td colspan=3 align="right">';
	$content .= '<input type="submit" class="button" value="Uppdatera">';
	$content .= '</td></tr>';
	$content .= '</table>';
	$content .= '</form>';

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Redigera stoppord', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>