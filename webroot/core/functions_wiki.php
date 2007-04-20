<?
	/* functions_wiki.php
		------------------------------------------------------------
		Written by Martin Lindhe, 2007 <martin_lindhe@yahoo.se>

		core																				tblWiki	
		för history-stöd: functions_revisions.php		tblRevisions
		för files-stöd: $files objekt								tblFiles
	*/
	
	require_once('functions_revisions.php');

	//wiki module default settings:
	$config['wiki']['log_history'] = true;
	$config['wiki']['allow_html'] = false;
	$config['wiki']['explain_words'] = false;
	
	$config['wiki']['allow_edit'] = false;	//false = only allow admins to edit the wiki articles. true = allow all, even anonymous


	$config['wiki']['allow_comments'] = false;		//todo - försök att slipp allow_comments & allow_files,
	$config['wiki']['allow_files'] = false;				//			acceptera bara de tabbar som finns i allowed_tabs

	$config['wiki']['allowed_tabs'] =	array('View', 'Edit', 'History', 'Comments', 'Files');
	$config['wiki']['first_tab'] = 'View';

	
	

	/* Optimization: Doesnt store identical entries if you hit Save button multiple times */
	function wikiUpdate($wikiName, $_text)
	{
		global $db, $session, $config;
		
		$wikiName = $db->escape(trim($wikiName));
		if (!$wikiName) return false;

		$q = 'SELECT * FROM tblWiki WHERE wikiName="'.$wikiName.'"';
		$data = $db->getOneRow($q);

		/* Aborts if we are trying to save a exact copy as the last one */
		if (!empty($data) && $data['msg'] == $_text) return false;

		$_text = $db->escape(trim($_text));
		
		if (!empty($data) && $data['wikiId'])
		{
			if ($config['wiki']['log_history'])
			{
				addRevision(REVISIONS_WIKI, $data['wikiId'], $data['msg'], $data['timeCreated'], $data['createdBy'], REV_CAT_TEXT_CHANGED);
			}
			$db->query('UPDATE tblWiki SET msg="'.$_text.'",timeCreated=NOW(),createdBy='.$session->id.' WHERE wikiName="'.$wikiName.'"');
		}
		else
		{
			$q = 'INSERT INTO tblWiki SET wikiName="'.$wikiName.'",msg="'.$_text.'",createdBy='.$session->id.',timeCreated=NOW()';
			$db->query($q);
		}
	}

	/* formats text for wiki output */
	function wikiFormat($wikiName, $data)
	{
		global $db, $files, $config;
		
		$text = stripslashes($data['msg']);

		$text = formatUserInputText($text, !$config['wiki']['allow_html']);
		
		if ($config['wiki']['explain_words']) {
			$text = dictExplainWords($text);
		}

		return $text;
	}

	/*
		Visar en wiki för användaren. Normalt kan vem som helst redigera den samt ladda upp filer till den,
		men en admin kan låsa wikin från att bli redigerad av vanliga användare
	*/
	function wiki($wikiName = '')
	{
		global $db, $files, $session, $config;
		
		$current_tab = $config['wiki']['first_tab'];

		//Looks for formatted wiki section commands, like: View:Page, Edit:Page, History:Page, Files:Page
		foreach($_GET as $key => $val) {
			$arr = explode(':', $key);
			if (empty($arr[1]) || !in_array($arr[0], $config['wiki']['allowed_tabs'])) continue;
			$current_tab = $arr[0];
			if (!$wikiName) $wikiName = $arr[1];
			break;
		}

		$wikiName = trim($wikiName);
		if (!$wikiName) return false;
		
		$q =	'SELECT t1.wikiId,t1.msg,t1.hasFiles,t1.timeCreated,t1.lockedBy,t1.timeLocked,t2.userName AS creatorName, t3.userName AS lockerName '.
					'FROM tblWiki AS t1 '.
					'LEFT JOIN tblUsers AS t2 ON (t1.createdBy=t2.userId) '.
					'LEFT JOIN tblUsers AS t3 ON (t1.lockedBy=t3.userId) '.
					'WHERE t1.wikiName="'.$db->escape($wikiName).'"';

		$data = $db->getOneRow($q);

		$wikiId = $data['wikiId'];
		$text = stripslashes($data['msg']);
		
		if (!$session->isAdmin && !$config['wiki']['allow_edit']) {
			/* Only display the text for normal visitors */
			echo wikiFormat($wikiName, $data);
			return true;
		}
		

		echo '<div class="wiki">'.
						'<div class="wiki_head"><ul>'.
							'<li>'.($current_tab=='view'?'<strong>':'').		'<a href="'.URLadd('View:'.$wikiName).'">View:'.$wikiName.'</a>'.($current_tab=='view'?'</strong>':'').'</li>';
		if ($session->isAdmin || !$data['lockedBy']) {
			echo 		'<li>'.($current_tab=='edit'?'<strong>':'').		'<a href="'.URLadd('Edit:'.$wikiName).'">Edit</a>'.				($current_tab=='edit'?'</strong>':'').'</li>';
		}
			echo		'<li>'.($current_tab=='history'?'<strong>':'').	'<a href="'.URLadd('History:'.$wikiName).'">History</a>'.	($current_tab=='history'?'</strong>':'').'</li>';
		if ($config['wiki']['allow_files']) {
			echo 		'<li>'.($current_tab=='files'?'<strong>':'').		'<a href="'.URLadd('Files:'.$wikiName).'">Files</a>'.			($current_tab=='files'?'</strong>':'').'</li>';
		}
		echo		'</ul></div>'.
					'<div class="wiki_body">';
			
		/* Display the wiki toolbar for super admins */
		if ($current_tab == 'Edit' && ($session->isAdmin || !$data['lockedBy']))
		{
			if (isset($_POST['wiki_'.$wikiId]))
			{
				//save changes to database
				wikiUpdate($wikiName, $_POST['wiki_'.$wikiId]);
				$text = $_POST['wiki_'.$wikiId];
				unset($_POST['wiki_'.$wikiId]);
				//JS_Alert('Changes saved!');
			}
			
			if ($session->isAdmin && isset($_GET['wiki_lock'])) {
				$q = 'UPDATE tblWiki SET lockedBy='.$session->id.',timeLocked=NOW() WHERE wikiId='.$wikiId;
				$db->query($q);
				$data['lockedBy'] = $session->id;
				$data['lockerName'] = $session->username;
				addRevision(REVISIONS_WIKI, $data['wikiId'], 'The wiki has been locked', now(), $session->id, REV_CAT_LOCKED);
			}

			if ($session->isAdmin && isset($_GET['wiki_unlock'])) {
				$q = 'UPDATE tblWiki SET lockedBy=0 WHERE wikiId='.$wikiId;
				$db->query($q);
				$data['lockedBy'] = 0;
				addRevision(REVISIONS_WIKI, $data['wikiId'], 'The wiki has been unlocked', now(), $session->id, REV_CAT_UNLOCKED);
			}

			$rows = 6+substr_count($text, "\n");
			if ($rows > 36) $rows = 36;

			$last_edited = 'never';
			if (!empty($data['timeCreated'])) $last_edited = $data['timeCreated'].' by '.$data['creatorName'];

			echo '<form method="post" name="wiki_edit" action="'.URLadd('Edit:'.$wikiName).'">'.
					 '<textarea name="wiki_'.$wikiId.'" cols="70%" rows="'.$rows.'">'.$text.'</textarea><br/>'.
					 'Last edited '.$last_edited.'<br/>'.
					 '<input type="submit" class="button" value="Save"/>';

			if ($session->isAdmin) {
				if ($data['lockedBy']) {
					echo '<input type="button" class="button" value="Unlock" onclick="location.href=\''.URLadd('Edit:'.$wikiName, '&amp;wiki_unlock').'\'"/>';
					echo '<img src="/gfx/icon_locked.png" width="16" height="16" alt="Locked" title="This wiki is currently locked"/>';
					echo '<b>Locked by '.$data['lockerName'].' at '.$data['timeLocked'].'</b><br/>';
				} else {
					echo '<input type="button" class="button" value="Lock" onclick="location.href=\''.URLadd('Edit:'.$wikiName, '&amp;wiki_lock').'\'"/>';
					echo '<img src="/gfx/icon_unlocked.png" width="16" height="16" alt="Unlocked" title="This wiki is currently open for edit by anyone"/>';
				}
			}

			//List "unused files" for this Wiki when in edit mode
			if ($config['wiki']['allow_files']) {
				$filelist = $files->getFilesByCategory(FILETYPE_WIKI, $wikiId);
				
				$str = '';

				for ($i=0; $i<count($filelist); $i++)
				{
					$temp = explode('.', $filelist[$i]['fileName']);
					$last_name = strtolower($temp[1]);

					$showTag = $linkTag = '[[file:'.$filelist[$i]['fileId'].']]';
					
					if (in_array($last_name, $files->allowed_image_types)) {
						$showTag = makeThumbLink($filelist[$i]['fileId'], $showTag);
					}

					if (strpos($text, $linkTag) === false) {
						$str .= '<span onclick="document.wiki_edit.wiki_'.$wikiId.'.value += \' '.$linkTag.'\';">'.$showTag.'</span>, ';
					}
				}
				if (substr($str, -2) == ', ') $str = substr($str, 0, -2);
				if ($str) {
					echo '<b>Unused files:</b> '.$str;
				}
			}
			echo '</form>';				
		}
		elseif ($config['wiki']['allow_comments'] && $current_tab == 'Comments')
		{
			if (!empty($_POST['comment_'.$wikiId])) {
				addComment($db, COMMENT_INFOFIELD, $wikiId, $_POST['comment_'.$wikiId]);
				unset($_POST['comment_'.$wikiId]);
			}

			$list = getComments($db, COMMENT_INFOFIELD, $wikiId);

			$info = '';
			for ($i=0; $i<count($list); $i++) {
				$info .= '"'.$list[$i]['commentText'].'", skrivet '.getDatestringShort($list[$i]['commentTime']).' av '.$list[$i]['userName'].'<br>';
			}
			echo 	'<br/>'.
						'Write a comment:<br/>'.
						'<form method="post" action="'.URLadd('Comments:'.$wikiName).'" name="wiki_comment">'.
						'<table width="100%" cellpadding="0" cellspacing="0" border="0">'.
							'<tr><td><textarea name="comment_'.$wikiId.'" cols="61" rows="4"></textarea><br/><img src="c.gif" width="1" height="5" alt=""/></td></tr>'.
							'<tr><td><input type="submit" class="button" value="Send"/></td></tr>'.
						'</table>'.
						'</form>';
		}
		elseif ($config['wiki']['allow_files'] && $current_tab == 'Files')
		{
			echo $files->showFiles(FILETYPE_WIKI, $wikiId);
		}
		elseif ($config['wiki']['log_history'] && $current_tab == 'History')
		{
			echo 'Current version:<br/>';
			echo '<b><a href="#" onclick="return toggle_element_by_name(\'layer_history_current\')">Written by '.$data['creatorName'].' at '.$data['timeCreated'].' ('.strlen($text).' bytes)</a></b><br/>';
			echo '<div id="layer_history_current" class="revision_entry">';
			echo nl2br(htmlentities($text, ENT_COMPAT, 'UTF-8'));
			echo '</div>';

			showRevisions(REVISIONS_WIKI, $wikiId, $wikiName);
		}
		else
		{
			if ($data['lockedBy']) {
				echo '<div class="wiki_info_locked">';
				echo '<img src="/gfx/icon_locked.png" width="16" height="16" alt="Locked" title="This wiki is currently locked"/>';
				echo 'LOCKED - This wiki can currently not be edited</div>';
			}
			echo wikiFormat($wikiName, $data);
		}

		echo 	'</div>';

		if ($config['wiki']['allow_comments']) {
			$talkbackComments = getCommentsCount($db, COMMENT_INFOFIELD, $wikiId);
			if ($talkbackComments == 1) $talkback = 'Talkback: 1 comment';
			else $talkback = 'Talkback: '.$talkbackComments.' comments';

			echo '<div class="wiki_foot"><ul>'.
							'<li>'.($current_tab=='comments'?'<strong>':'').'<a href="'.URLadd('Comments:'.$wikiName).'">'.$talkback.'</a>'.($current_tab=='comments'?'</strong>':'').'</li>'.
					'</ul></div>';
		}
		echo '</div>';

		return true;
	}
?>