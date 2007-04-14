<?
	/* functions_wiki.php																													*/
	/* --------------------------------------------------------------------------	*/
	/* Written by Martin Lindhe	<martin_lindhe@yahoo.se>													*/
	/*
		core																				tblWiki	
		för history-stöd: functions_revisions.php		tblRevisions
		för files-stöd: $files objekt								tblFiles
	*/
	
	require_once('functions_revisions.php');
	require_once('functions_files.php');

	//wiki module settings:
	$config['wiki']['log_history'] = true;
	$config['wiki']['allow_html'] = false;
	$config['wiki']['explain_words'] = false;


	$config['wiki']['allow_comments'] = false;		//todo - försök att slipp allow_comments & allow_files,
	$config['wiki']['allow_files'] = true;				//			acceptera bara de tabbar som finns i allowed_tabs

	$config['wiki']['allowed_tabs'] =	array('View', 'Edit', 'History', 'Comments', 'Files', 'Hide');
	$config['wiki']['first_tab'] = 'View';

	
	

	/* Optimization: Doesnt store identical entries if you hit Save button multiple times */
	function wikiUpdate($wikiName, $_text)
	{
		global $db, $session, $config;

		$wikiName = $db->escape(trim($wikiName));

		if (!$wikiName) return false;

		$sql = 'SELECT * FROM tblWiki WHERE fieldName="'.$wikiName.'"';
		$data = $db->getOneRow($sql);

		/* Aborts if we are trying to save a exact copy as the last one */
		if ($data['fieldText'] == $_text) return false;

		$_text = $db->escape(trim($_text));
		
		if ($data['fieldId'])
		{
			if ($config['wiki']['log_history'])
			{
				addRevision(REVISIONS_WIKI, $data['fieldId'], $data['fieldText'], $data['timeCreated'], $data['createdBy'], REV_CAT_TEXT_CHANGED);
			}
			$db->query('UPDATE tblWiki SET fieldText="'.$_text.'",timeCreated=NOW(),createdBy='.$session->id.' WHERE fieldName="'.$wikiName.'"');
		}
		else
		{
			$db->query('INSERT INTO tblWiki SET fieldName="'.$wikiName.'", fieldText="'.$_text.'",timeCreated=NOW(),createdBy='.$session->id);
		}
	}

	/* formats text for wiki output */
	function wikiFormat($wikiName, $data)
	{
		global $db, $files, $config;
		
		$text = stripslashes($data['fieldText']);

		if ($config['wiki']['allow_html']) {
			$text = formatUserInputText($text, false);
		} else {
			$text = formatUserInputText($text, true);
		}
		
		if ($config['wiki']['explain_words']) {
			$text = dictExplainWords($text);
		}

		if ($config['wiki']['allow_files'] && $data['hasFiles']) {
			$list = $files->getFiles($data['fieldId'], FILETYPE_WIKI);

			$has_unshowed_files = 0;
			for ($i=0; $i<count($list); $i++) {
				$list[$i]['replaced'] = 0;

				//ersätter [file123] med en htmlkod som visar fil-info
				$fileTag = '[file'.$list[$i]['fileId'].']';
				$pos = strpos($text, $fileTag);
				if ($pos !== false) {
					$fileblock = formatFileAttachment($db, $list[$i], '#F0F0F0', false, $wikiName);
					$text = str_replace($fileTag, $fileblock, $text);
					$list[$i]['replaced'] = 1;
				}
				
				//ersätter [image123] med htmlkod för thumbnail
				$imageTag = '[image'.$list[$i]['fileId'].']';
				$imageFilename = $config['upload_dir'].$list[$i]['fileId'];

				$pos = strpos($text, $imageTag);
				if (file_exists($imageFilename) && $pos !== false) {
					$img_size = getimagesize($imageFilename);
					$imageblock = '<img src="/core/file.php?id='.$list[$i]['fileId'].'&amp;w='.$config['thumbnail_width'].getProjectPath().'" width='.$config['thumbnail_width'].' title="'.$imageTag.'">';

					$text = str_replace($imageTag, $imageblock, $text);
					$list[$i]['replaced'] = 1;
				}
				
				if ($list[$i]['replaced'] != 1) {
					$has_unshowed_files = 1;
				}
			}

			if (!$has_unshowed_files) return $text;

			//listar filer som inte redan är enkodade i wikin som bilagor
			$text .= '<br><br><b>Attached files:</b><br>';

			$j = 0;
			for ($i=0; $i<count($list); $i++) {
				$bgcolor = '#F0F0F0';
				if ($j%2) $bgcolor = '#FEFEFE';
				if (!$list[$i]['replaced']) {
					$text .= formatFileAttachment($list[$i], $bgcolor, true, $wikiName).'<br>';
					$j++;
				}
			}

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

		//Looks for formatted wiki section commands, like: View:Page, Edit:Page, History:Page
		foreach($_GET as $key => $val) {
			$arr = explode(':', $key);
			if (empty($arr[1]) || !in_array($arr[0], $config['wiki']['allowed_tabs'])) continue;
			$current_tab = $arr[0];
			if (!$wikiName) $wikiName = $arr[1];
			break;
		}

		$wikiName = trim($wikiName);
		if (!$wikiName) return false;
		
		$q =	'SELECT t1.fieldId,t1.fieldText,t1.hasFiles,t1.timeCreated,t1.lockedBy,t1.timeLocked,t2.userName AS creatorName, t3.userName AS lockerName '.
					'FROM tblWiki AS t1 '.
					'LEFT JOIN tblUsers AS t2 ON (t1.createdBy=t2.userId) '.
					'LEFT JOIN tblUsers AS t3 ON (t1.lockedBy=t3.userId) '.
					'WHERE t1.fieldName="'.$db->escape($wikiName).'"';

		$data = $db->getOneRow($q);

		$wikiId = $data['fieldId'];
		$text = stripslashes($data['fieldText']);

		//Visa enbart texten
		if ($current_tab == 'Hide') {
			echo wikiFormat($wikiName, $data);
			return true;
		}
		
		//kollar om den är låst
		if (!$session->isAdmin && $data['lockedBy'])  {
			echo 'WIKI IS CURRENTLY LOCKED FROM EDITING!<br/>';
			echo wikiFormat($wikiName, $data);
			return true;
		}

		echo '<div class="wiki">'.
						'<div class="wiki_head"><ul>'.
							'<li>'.($current_tab=='view'?'<strong>':'').		'<a href="'.wikiURLadd('View', $wikiName).'">View:'.$wikiName.'</a>'.($current_tab=='view'?'</strong>':'').'</li>'.
							'<li>'.($current_tab=='edit'?'<strong>':'').		'<a href="'.wikiURLadd('Edit', $wikiName).'">Edit</a>'.				($current_tab=='edit'?'</strong>':'').'</li>'.
							'<li>'.($current_tab=='history'?'<strong>':'').	'<a href="'.wikiURLadd('History', $wikiName).'">History</a>'.	($current_tab=='history'?'</strong>':'').'</li>';
		if ($config['wiki']['allow_files']) {
			echo 		'<li>'.($current_tab=='files'?'<strong>':'').		'<a href="'.wikiURLadd('Files', $wikiName).'">Files</a>'.			($current_tab=='files'?'</strong>':'').'</li>';
		}
		echo 		'<li><a href="'.wikiURLadd('Hide', $wikiName).'">Hide</a></li>'.
					'</ul></div>'.
					'<div class="wiki_body">';
			
		/* Display the wiki toolbar for super admins */
		if ($current_tab == 'Edit')
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
				$q = 'UPDATE tblWiki SET lockedBy='.$session->id.',timeLocked=NOW() WHERE fieldId='.$wikiId;
				$db->query($q);
				$data['lockedBy'] = $session->id;
				$data['lockerName'] = $session->username;
				addRevision(REVISIONS_WIKI, $data['fieldId'], 'The wiki has been locked', now(), $session->id, REV_CAT_LOCKED);
			}

			if ($session->isAdmin && isset($_GET['wiki_unlock'])) {
				$q = 'UPDATE tblWiki SET lockedBy=0 WHERE fieldId='.$wikiId;
				$db->query($q);
				$data['lockedBy'] = 0;
				addRevision(REVISIONS_WIKI, $data['fieldId'], 'The wiki has been unlocked', now(), $session->id, REV_CAT_UNLOCKED);
			}

			$rows = 6+substr_count($text, "\n");
			if ($rows > 36) $rows = 36;

			$last_edited = 'never';
			if (!empty($data['timeCreated'])) $last_edited = $data['timeCreated'].' by '.$data['creatorName'];

			echo '<form method="post" name="wiki_edit" action="'.wikiURLadd('Edit', $wikiName).'">'.
					 '<textarea name="wiki_'.$wikiId.'" cols="70%" rows="'.$rows.'">'.$text.'</textarea><br/>'.
					 'Last edited '.$last_edited.'<br/>'.
					 '<input type="submit" class="button" value="Save"/>';

			if ($session->isAdmin) {
				if ($data['lockedBy']) {
					echo '<input type="button" class="button" value="Unlock" onclick="location.href=\''.wikiURLadd('Edit', $wikiName, '&amp;wiki_unlock').'\'"/>';
					echo '<img src="/gfx/icon_locked.png" width="16" height="16" alt="Locked" title="This wiki is currently locked"/>';
					echo '<b>Locked by '.$data['lockerName'].' at '.$data['timeLocked'].'</b>';
				} else {
					echo '<input type="button" class="button" value="Lock" onclick="location.href=\''.wikiURLadd('Edit', $wikiName, '&amp;wiki_lock').'\'"/>';
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
						$showTag = '<img src="/core/file.php?id='.$filelist[$i]['fileId'].'&amp;w=60&amp;h=60'.getProjectPath().'" alt=""/>';
					}

					if (strpos($text, $linkTag) === false) {
						$str .= '<span onclick="document.wiki_edit.wiki_'.$wikiId.'.value += \' '.$linkTag.'\';">'.$showTag.'</span>, ';
					}
				}
				if (substr($str, -2) == ', ') $str = substr($str, 0, -2);
				if ($str) {
					echo '<b>unused files:</b> '.$str;
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
						'<form method="post" action="'.wikiURLadd('Comments', $wikiName).'" name="wiki_comment">'.
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
			echo wikiFormat($wikiName, $data);
		}

		echo 	'</div>';

		if ($config['wiki']['allow_comments']) {
			$talkbackComments = getCommentsCount($db, COMMENT_INFOFIELD, $wikiId);
			if ($talkbackComments == 1) $talkback = 'Talkback: 1 comment';
			else $talkback = 'Talkback: '.$talkbackComments.' comments';

			echo '<div class="wiki_foot"><ul>'.
							'<li>'.($current_tab=='comments'?'<strong>':'').'<a href="'.wikiURLadd('Comments', $wikiName).'">'.$talkback.'</a>'.($current_tab=='comments'?'</strong>':'').'</li>'.
					'</ul></div>';
		}
		echo '</div>';

		return true;
	}

	function wikiURLadd($_page, $_section, $_extra = '')
	{
		$_wikiURL = $_page.':'.urlencode($_section);
		
		$arr = parse_url($_SERVER['REQUEST_URI']);
		if (empty($arr['query'])) {
			return $arr['path'].'?'.$_wikiURL;
		}
		$args = explode('&', $arr['query']);
		
		$out_args = '';

		for ($i=0; $i<count($args); $i++) {
			$vals = explode('=', $args[$i]);
			
			$skipit = explode(':', $vals[0]);
			
			if (!isset($skipit[1]) && isset($vals[1])) {
				$out_args .= $vals[0].'='.urlencode($vals[1]).'&amp;';
			}
		}

		if ($out_args) {
			return $arr['path'].'?'.$out_args.'&amp;'.$_wikiURL.$_extra;
		}
		return $arr['path'].'?'.$_wikiURL.$_extra;
	}
?>