<?
	/* functions_wiki.php
		------------------------------------------------------------
		Written by Martin Lindhe, 2007 <martin@startwars.org>

		core																				tblWiki
		for history-support: atom_revisions.php			tblRevisions
		for files-support: $files objekt						tblFiles
	*/

	require_once('atom_revisions.php');

	//wiki module default settings:
	$config['wiki']['log_history'] = true;
	$config['wiki']['allow_html'] = false;
	$config['wiki']['explain_words'] = false;

	$config['wiki']['allow_edit'] = false;	//false = only allow admins to edit the wiki articles. true = allow all, even anonymous

	$config['wiki']['allow_files'] = false;		//only acceptera bara de tabbar som finns i allowed_tabs

	$config['wiki']['allowed_tabs'] =	array('Wiki', 'WikiEdit', 'WikiHistory', 'WikiFiles');
	$config['wiki']['first_tab'] = 'Wiki';

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
			$db->insert($q);
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

		//Looks for formatted wiki section commands, like: Wiki:Page, WikiEdit:Page, WikiHistory:Page, WikiFiles:Page
		$cmd = fetchSpecialParams($config['wiki']['allowed_tabs']);
		if ($cmd) list($current_tab, $wikiName) = $cmd;
		if (!$wikiName) return false;

		$wikiName = str_replace(' ', '_', $wikiName);

		$q ='SELECT t1.*,t2.userName AS creatorName, t3.userName AS lockerName '.
				'FROM tblWiki AS t1 '.
				'LEFT JOIN tblUsers AS t2 ON (t1.createdBy=t2.userId) '.
				'LEFT JOIN tblUsers AS t3 ON (t1.lockedBy=t3.userId) '.
				'WHERE t1.wikiName="'.$db->escape($wikiName).'"';

		$data = $db->getOneRow($q);

		$text = stripslashes($data['msg']);

		if (!$session->isAdmin && !$config['wiki']['allow_edit']) {
			/* Only display the text for normal visitors */
			echo '<div class="wiki">';
			echo '<div class="wiki_body">';
			echo wikiFormat($wikiName, $data);
			echo '</div>';
			echo '</div>';
			return true;
		}

		if (in_array('WikiFiles', $config['wiki']['allowed_tabs'])) {	//Show files tab?
			$wiki_menu = array(
			$_SERVER['PHP_SELF'].'?Wiki:'.$wikiName => 'Wiki:'.str_replace('_', ' ', $wikiName),
			$_SERVER['PHP_SELF'].'?WikiEdit:'.$wikiName => 'Edit',
			$_SERVER['PHP_SELF'].'?WikiHistory:'.$wikiName => 'History',
			$_SERVER['PHP_SELF'].'?WikiFiles:'.$wikiName => 'Files ('.$files->getFileCount(FILETYPE_WIKI, $data['wikiId']).')');
		} else {
			$wiki_menu = array(
			$_SERVER['PHP_SELF'].'?Wiki:'.$wikiName => 'Wiki:'.str_replace('_', ' ', $wikiName),
			$_SERVER['PHP_SELF'].'?WikiEdit:'.$wikiName => 'Edit',
			$_SERVER['PHP_SELF'].'?WikiHistory:'.$wikiName => 'History');
		}

		echo '<div class="wiki">';
		createMenu($wiki_menu, 'wiki_menu');
		echo '<div class="wiki_body">';

		/* Display the wiki toolbar for super admins */
		if ($current_tab == 'WikiEdit' && ($session->isAdmin || !$data['lockedBy']))
		{
			if (isset($_POST['wiki_'.$data['wikiId']]))
			{
				//save changes to database
				wikiUpdate($wikiName, $_POST['wiki_'.$data['wikiId']]);
				$text = $_POST['wiki_'.$data['wikiId']];
				unset($_POST['wiki_'.$data['wikiId']]);
				//JS_Alert('Changes saved!');
			}

			if ($session->isAdmin && isset($_GET['wiki_lock'])) {
				$q = 'UPDATE tblWiki SET lockedBy='.$session->id.',timeLocked=NOW() WHERE wikiId='.$data['wikiId'];
				$db->query($q);
				$data['lockedBy'] = $session->id;
				$data['lockerName'] = $session->username;
				addRevision(REVISIONS_WIKI, $data['wikiId'], 'The wiki has been locked', now(), $session->id, REV_CAT_LOCKED);
			}

			if ($session->isAdmin && isset($_GET['wiki_unlock'])) {
				$q = 'UPDATE tblWiki SET lockedBy=0 WHERE wikiId='.$data['wikiId'];
				$db->query($q);
				$data['lockedBy'] = 0;
				addRevision(REVISIONS_WIKI, $data['wikiId'], 'The wiki has been unlocked', now(), $session->id, REV_CAT_UNLOCKED);
			}

			$rows = 6+substr_count($text, "\n");
			if ($rows > 36) $rows = 36;

			$last_edited = 'never';
			if (!empty($data['timeCreated'])) $last_edited = $data['timeCreated'].' by '.$data['creatorName'];

			echo
          '<script type="text/javascript">initToolbar("tool__bar","wiki__text",toolbar);</script>'.
          '<div id="tool_bar">'.
					'<form method="post" name="wiki_edit" action="'.URLadd('WikiEdit:'.$wikiName).'">'.
					'<input type="button" class="button" value="B" style="font-weight: bold"/>'.
					'<br/>'.
					'<textarea name="wiki_'.$data['wikiId'].'" cols="70%" rows="'.$rows.'">'.$text.'</textarea><br/>'.
					'Last edited '.$last_edited.'<br/>'.
					'<input type="submit" class="button" value="Save"/>';

			if ($session->isAdmin) {
				if ($data['lockedBy']) {
					echo '<input type="button" class="button" value="Unlock" onclick="location.href=\''.URLadd('WikiEdit:'.$wikiName, '&amp;wiki_unlock').'\'"/>';
					echo '<img src="'.$config['core_web_root'].'gfx/icon_locked.png" width="16" height="16" alt="Locked" title="This wiki is currently locked"/>';
					echo '<b>Locked by '.$data['lockerName'].' at '.$data['timeLocked'].'</b><br/>';
				} else {
					echo '<input type="button" class="button" value="Lock" onclick="location.href=\''.URLadd('WikiEdit:'.$wikiName, '&amp;wiki_lock').'\'"/>';
					echo '<img src="'.$config['core_web_root'].'gfx/icon_unlocked.png" width="16" height="16" alt="Unlocked" title="This wiki is currently open for edit by anyone"/>';
				}
			}

			//List "unused files" for this Wiki when in edit mode
			if ($config['wiki']['allow_files']) {
				$filelist = $files->getFiles(FILETYPE_WIKI, $data['wikiId']);

				$str = '';

				foreach ($filelist as $row) {
					$temp = explode('.', $row['fileName']);

					$showTag = $linkTag = '[[file:'.$row['fileId'].']]';

					if (in_array($row['fileMime'], $files->image_mime_types)) {
						$showTag = makeThumbLink($row['fileId'], $showTag);
					}

					if (strpos($text, $linkTag) === false) {
						$str .= '<span onclick="document.wiki_edit.wiki_'.$data['wikiId'].'.value += \' '.$linkTag.'\';">'.$showTag.'</span>, ';
					}
				}
				if (substr($str, -2) == ', ') $str = substr($str, 0, -2);
				if ($str) {
					echo '<b>Unused files:</b> '.$str;
				}
			}
			echo '</form>';
			
			echo '</div>';	//id="tool_bar"
		}
		elseif ($current_tab == 'WikiFiles')
		{
			echo $files->showFiles(FILETYPE_WIKI, $data['wikiId']);
		}
		elseif ($config['wiki']['log_history'] && $current_tab == 'WikiHistory')
		{
			echo 'Current version:<br/>';
			echo '<b><a href="#" onclick="return toggle_element_by_name(\'layer_history_current\')">Written by '.$data['creatorName'].' at '.$data['timeCreated'].' ('.strlen($text).' bytes)</a></b><br/>';
			echo '<div id="layer_history_current" class="revision_entry">';
			echo nl2br(htmlentities($text, ENT_COMPAT, 'UTF-8'));
			echo '</div>';

			showRevisions(REVISIONS_WIKI, $data['wikiId'], $wikiName);
		}
		else
		{
			if ($data['lockedBy']) {
				echo '<div class="wiki_locked">LOCKED - This wiki can currently not be edited</div>';
			}
			echo wikiFormat($wikiName, $data);
		}

		echo 	'</div>';

		echo '</div>';

		return true;
	}
?>