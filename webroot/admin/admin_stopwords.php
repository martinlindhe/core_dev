<?
	//todo: rensa upp html-tabellerna

	require_once('find_config.php');

	$session->requireAdmin();

	if (count($_POST)) {
		$list = getStopwords();

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
				removeStopword($id);
			} else
			
			/* Raderar även om man bara har tömt texten i fältet och tryckt Uppdatera */
			if (!$_POST[$chg]) {
				removeStopword($id);
			}
			
			/* Update has less priority */
			if (($_POST[$chg] != $list[$i]['wordText']) || ($full != $list[$i]['wordMatch'])) {
				setStopword($id, $_POST[$chg], $full);
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
				if (!addStopword($_POST[$word], $i, $full)) {
					echo 'Failed to add '.$_POST[$word].'<br>';
				}
			}
		}
	}

	require($project.'design_head.php');

	echo '<form name="update" method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo '<table width="100%" border=0 cellspacing=0 cellpadding=3>';
	echo '<tr>';
	
	for($x=1; $x<=3; $x++) {
		
		switch($x) {
			case STOPWORD_OBJECTIONABLE:
				$txt='Objectionable';
				$help = 'Inl&auml;gg inneh&aring;llande st&ouml;tande ord till&aring;ts inte att publiceras.';
				break;

			case STOPWORD_SENSITIVE:
				$txt='Sensitive';
				$help = 'Inl&auml;gg inneh&aring;llande k&auml;nsliga ord hamnar automatiskt i modereringsk&ouml;n utan att inl&auml;gget blockeras.<br/><b>Used in following modules: BLOG</b>';
				break;

			case STOPWORD_RESERVED_USERNAME:
				$txt='Reserved';
				$help = 'Reserved words are not allowed in user names.';
				break;
		}

		echo '<td valign="top" align="center">';
		echo '<table width="100%" border=0 cellspacing=0 cellpadding=1 bgcolor="#000000"><tr><td>';
		echo '<table width="100%" border=0 cellspacing=0 cellpadding=2 bgcolor="#FFFFFF">';
		echo '<tr><td colspan=3 height=25 valign="top"><b>'.$txt.'</b></td></tr>';
		echo '<tr><td colspan=3 height=25 valign="top">'.$help.'<br><br></td></tr>';
		echo '<tr><td>&nbsp;</td><td valign="bottom">Full match</td><td valign="bottom">Remove</td></tr>';

		$list = getStopwords($x);
		for ($i=0; $i<count($list); $i++) {
			$chg  = 'change_'.$list[$i]['wordId'];
			$full = 'full_'.$list[$i]['wordId'];
			$del  = 'del_'.$list[$i]['wordId'];

			echo '<tr>';
			echo '<td><input type="text" name="'.$chg.'" value="'.$list[$i]['wordText'].'" size=16></td>';
			echo '<td><input type="checkbox" class="checkbox" name="'.$full.'" value=1';
			if ($list[$i]['wordMatch']==1) echo ' checked';
			echo '></td>';
			echo '<td><input type="checkbox" class="checkbox" name="'.$del.'"></td>';
			echo '</tr>';
		}

		echo '<tr>';
			echo '<td><br><br>Add new word:<br><input type="text" name="newname_'.$x.'" size=16></td>';
			echo '<td><br><br><input type="checkbox" class="checkbox" value="1" name="newfull_'.$x.'"></td>';
			echo '<td>&nbsp;</td>';
		echo '</tr>';

		echo '</table>';
		echo '</td></tr></table>';

		if ($x<3) {
			echo '</td>';
		}
	}
	echo '</td></tr>';

	echo '<tr><td colspan=3 align="right">';
	echo '<input type="submit" class="button" value="Uppdatera">';
	echo '</td></tr>';
	echo '</table>';
	echo '</form>';

	require($project.'design_foot.php');
?>