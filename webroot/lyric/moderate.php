<?
	require_once('config.php');
	
	if (!$session->isAdmin) die;

	require('design_head.php');
	
	if ($_POST) {

		while (list($orgkey, $val) = each($_POST)) {
			
			$key = explode('_',$orgkey);
			
			if ($_POST['action'] == 'add') {
				if ($val == 'accept') {
					echo 'Accepting '.$key[1].'<br/>';
					acceptNewAddition($key[0], $key[1]);
				} else if ($val == 'deny') {
					echo 'Denying '.$key[1].'<br/>';
					denyNewAddition($key[0], $key[1]);
				}
			} else if ($_POST['action'] == 'change') {
				if ($val == 'accept') {
					echo 'Accepting '.$key[1].'<br/>';
					acceptPendingChange($key[0], $key[1]);
				} else if ($val == 'deny') {
					echo 'Denying '.$key[1].'<br/>';
					denyPendingChange($key[0], $key[1]);
				}
			}
		}
	}
	
	$session->showInfo();
	
	$list = getNewAdditions();
	if (count($list)) {
		echo 'Moderate new additions ('.count($list).'):<br/>';
		echo '<form name="additions" method="post" action="'.$_SERVER['PHP_SELF'].'">';
		echo '<input type="hidden" name="action" value="add">';
		for ($i=0; $i<count($list); $i++) {
			$skip=false;

			switch ($list[$i]['type']) {
				case MODERATION_BAND:
					$band = getBandInfo($list[$i]['ID']);
					echo '<b>Add band:</b> ';
					echo '<a href="show_band.php?id='.$band['bandId'].'">'.$band['bandName'].'</a>, added by '.$band['userName'].'<br/>';
					echo '<br/>';
					break;

				case MODERATION_RECORD:
					$record = getRecordData($list[$i]['ID']);
					if (!$record) { $skip=true; break; }
					echo '<b>Add record:</b> ';
					echo '<a href="show_band.php?id='.$record['bandId'].'">'.$record['bandName'].'</a> - ';
					echo '<a href="show_record.php?id='.$record['recordId'].'">'.$record['recordName'].'</a> added by '.$record['userName'].'<br/>';
					echo '<br/>';
					break;

				case MODERATION_LYRIC:
					$lyric = getLyricData($list[$i]['ID']);
					echo '<b>Add lyric:</b> ';
					echo '<a href="show_lyric.php?id='.$lyric['lyricId'].'">'.$lyric['lyricName'].'</a>:<br/>';
					echo '<i>'.nl2br($lyric['lyricText']).'</i><br/>';
					echo 'For the band <a href="show_band.php?id='.$lyric['bandId'].'">'.$lyric['bandName'].'</a>, added by '.$lyric['userName'].'<br/>';
					echo '<br/>';
					break;
			}
			if ($skip == false) {
				echo '<input class="radio" type="radio" name="'.$list[$i]['type'].'_'.$list[$i]['ID'].'" value="accept">Accept';
				echo '<input class="radio" type="radio" name="'.$list[$i]['type'].'_'.$list[$i]['ID'].'" value="deny">Deny';
				echo '<hr/>';
			} else {
				removeNewAddition($list[$i]['type'], $list[$i]['ID']);
			}
		}
		echo '<input type="submit" value="Update" class="buttonstyle"/>';
		echo '</form>';
	}


	//--PENDING CHANGES
	
	$list = getPendingChanges();
	if (count($list)) {
		echo 'Moderate pending changes ('.count($list).'):<br/>';
		echo '<form name="changes" method="post" action="'.$_SERVER['PHP_SELF'].'">';
		echo '<input type="hidden" name="action" value="change">';
		for ($i=0; $i<count($list); $i++) {
			switch ($list[$i]['type']) {
				case MODERATIONCHANGE_LYRIC:
					echo '<b>Pending change in lyric, orginal to the left, suggested to the right:</b><br/>';
					echo '<table width="1000" cellpadding="0" cellspacing="0" border="1"><tr>';
						echo '<td valign="top" width="500">';
						$org = getLyricData($list[$i]['p1']);
						echo '<b>'.$org['lyricName'].'</b><br/>';
						echo nl2br(dbStripSlashes($org['lyricText']));
						echo '</td>';

						echo '<td valign="top">';
						echo '<b>'.$list[$i]['p2'].'</b><br/>';
						echo nl2br(dbStripSlashes($list[$i]['p3']));
						echo '</td>';
					echo '</tr></table>';
					break;

				case MODERATIONCHANGE_RECORDNAME:
					echo '<b>Pending change in record name</b><br/>';
					echo 'Old name: '.getRecordName($list[$i]['p1']).'<br/>';
					echo 'Suggested new name: '.$list[$i]['p2'].'<br/>';
					break;

				case MODERATIONCHANGE_LYRICLINK:
					echo '<b>Lyric linked to track</b><br/>';
					echo 'to track '.$list[$i]['p2'].' on record id '.$list[$i]['p1'].'<br/>';
					break;

				default:
					echo 'unimplemented type '.$list[$i]['type'].'<br/>';
			}
		
			echo '<input class="radio" type="radio" name="'.$list[$i]['type'].'_'.$list[$i]['p1'].'" value="accept"/>Accept';
			echo '<input class="radio" type="radio" name="'.$list[$i]['type'].'_'.$list[$i]['p1'].'" value="deny"/>Deny';
			echo '<hr>';
		}
		echo '<input type="submit" value="Update" class="buttonstyle"/>';
		echo '</form>';
	}

	echo '<a href="index.php">Back to main</a><br/>';
	
	require('design_foot.php');
?>