<?
	/* functions_wiki.php																													*/
	/* --------------------------------------------------------------------------	*/
	/* Written by Martin Lindhe	<martin_lindhe@yahoo.se>													*/

	require_once('functions_revisions.php');

	//infofield module settings:
	$config['infofield']['log_history'] = true;
	$config['infofield']['allow_comments'] = false;
	$config['infofield']['allow_files'] = false;
	$config['infofield']['allow_html'] = false;
	$config['infofield']['explain_words'] = false;

	/* Optimization: Doesnt store identical entries if you hit Save button multiple times */
	function wikiUpdate($fieldName, $fieldText)
	{
		global $db, $session, $config;

		if (!$session->isAdmin) return false;

		$fieldName = $db->escape(trim($fieldName));
		if (!$fieldName) return false;

		$sql = 'SELECT * FROM tblWiki WHERE fieldName="'.$fieldName.'"';
		$data = $db->getOneRow($sql);

		/* Aborts if we are trying to save a exact copy as the last one */
		if ($data['fieldText'] == $fieldText) return false;

		$fieldText = $db->escape(trim($fieldText));
		
		if ($data['fieldId'])
		{
			if ($config['infofield']['log_history'])
			{
				addRevision(REVISIONS_WIKI, $data['fieldId'], $data['fieldText'], $data['timeEdited'], $data['editedBy']);
			}
			$db->query('UPDATE tblWiki SET fieldText="'.$fieldText.'",timeEdited=NOW(),editedBy='.$session->id.' WHERE fieldName="'.$fieldName.'"');
		}
		else
		{
			$db->query('INSERT INTO tblWiki SET fieldName="'.$fieldName.'", fieldText="'.$fieldText.'",timeEdited=NOW(),editedBy='.$session->id);
		}
	}

	/* formats text for wiki output */
	function wikiFormat(&$data, $fieldName)
	{
		global $db, $config;
		
		$text = stripslashes($data['fieldText']);

		if ($config['infofield']['allow_html']) {
			$text = formatUserInputText($text, false);
		} else {
			$text = formatUserInputText($text, true);
		}
		
		if ($config['infofield']['explain_words']) {
			$text = dictExplainWords($text);
		}

		if ($config['infofield']['allow_files'] && $data['hasFiles']) {
			$list = getFiles($db, $data['fieldId'], FILETYPE_INFOFIELD);

			$has_unshowed_files = 0;
			for ($i=0; $i<count($list); $i++) {
				$list[$i]['replaced'] = 0;

				//ers�tter [file123] med en htmlkod som visar fil-info
				$fileTag = '[file'.$list[$i]['fileId'].']';
				$pos = strpos($text, $fileTag);
				if ($pos !== false) {
					$fileblock = formatFileAttachment($db, $list[$i], '#F0F0F0', false, $fieldName);
					$text = str_replace($fileTag, $fileblock, $text);
					$list[$i]['replaced'] = 1;
				}
				
				//ers�tter [image123] med htmlkod f�r thumbnail+popup full image javascript
				$imageTag = '[image'.$list[$i]['fileId'].']';
				$imageFilename = $config['upload_dir'].$list[$i]['fileId'];

				$pos = strpos($text, $imageTag);
				if (file_exists($imageFilename) && $pos !== false) {
					$img_size = getimagesize($imageFilename);
					$imageblock = '<a href="javascript:wnd_imgview('.$list[$i]['fileId'].','.$img_size[0].','.$img_size[1].')"><img src="file.php?id='.$list[$i]['fileId'].'&width='.$config['thumbnail_width'].'" width='.$config['thumbnail_width'].' title="'.$imageTag.'"></a>';

					$text = str_replace($imageTag, $imageblock, $text);
					$list[$i]['replaced'] = 1;
				}
				
				if ($list[$i]['replaced'] != 1) {
					$has_unshowed_files = 1;
				}
			}

			if (!$has_unshowed_files) {
				return $text;
			}

			//listar filer som inte redan �r enkodade i infof�ltet
			$text .= '<br><br><b>Attached files:</b><br>';

			$j = 0;
			for ($i=0; $i<count($list); $i++) {
				$bgcolor = '#F0F0F0';
				if ($j%2) $bgcolor = '#FEFEFE';
				if (!$list[$i]['replaced']) {
					$text .= formatFileAttachment($db, $list[$i], $bgcolor, true, $fieldName).'<br>';
					$j++;
				}
			}

		}

		return $text;
	}

	function wiki($fieldName = '')
	{
		global $db, $session;
		global $config;

		$current_tab = 'View';

		$allowed_tabs = array('View', 'Edit', 'History', 'Comments', 'Files', 'Hide');
		
		//print_r($_GET);

		foreach($_GET as $key => $val) {
			//echo 'key: '.$key.'<br/>';
			$arr = explode(':', $key);
			if (empty($arr[1]) || !in_array($arr[0], $allowed_tabs)) continue;
			$current_tab = $arr[0];
			if (!$fieldName) $fieldName = $arr[1];
			break;
		}

		//echo 'tab: '.$current_tab.'<br/>';
		//echo 'field: '.$fieldName.'<br/><br/>';

		$fieldName = trim($fieldName);
		if (!$fieldName) return false;

		if (!$session->isAdmin || $current_tab == 'Hide')
		{
			$sql = 'SELECT fieldId,fieldText,hasFiles FROM tblWiki WHERE fieldName="'.$db->escape($fieldName).'"';
		} else {
			$sql  = 'SELECT t1.fieldId,t1.fieldText,t1.hasFiles,t1.timeEdited,t2.userName AS editorName '.
							'FROM tblWiki AS t1 '.
							'INNER JOIN tblUsers AS t2 ON (t1.editedBy=t2.userId) '.
							'WHERE fieldName="'.$db->escape($fieldName).'"';
		}

		$data = $db->getOneRow($sql);

		$fieldId = $data['fieldId'];
		$text = stripslashes($data['fieldText']);

		if (!$session->isAdmin || $current_tab == 'Hide') {
			//Visa enbart texten
			echo wikiFormat($data, $fieldName);
			return true;
		}

		echo '<div class="infofield">'.
						'<div class="infofield_head"><ul>'.
							'<li>'.($current_tab=='view'?'<strong>':'').		'<a href="'.wikiURLadd('View', $fieldName).'">View:'.$fieldName.'</a>'.($current_tab=='view'?'</strong>':'').'</li>'.
							'<li>'.($current_tab=='edit'?'<strong>':'').		'<a href="'.wikiURLadd('Edit', $fieldName).'">Edit</a>'.				($current_tab=='edit'?'</strong>':'').'</li>'.
							'<li>'.($current_tab=='history'?'<strong>':'').	'<a href="'.wikiURLadd('History', $fieldName).'">History</a>'.	($current_tab=='history'?'</strong>':'').'</li>';
		if ($config['infofield']['allow_files']) {
			echo 		'<li>'.($current_tab=='files'?'<strong>':'').		'<a href="'.wikiURLadd('Files', $fieldName).'">Files</a>'.			($current_tab=='files'?'</strong>':'').'</li>';
		}
		echo 		'<li><a href="'.wikiURLadd('Hide', $fieldName).'">Hide</a></li>'.
					'</ul></div>'.
					'<div class="infofield_body">';
			
		/* Display the infofield toolbar for super admins */
		if ($current_tab == 'Edit')
		{
			if (isset($_POST['infofield_'.$fieldId]))
			{
				//save changes to database
				wikiUpdate($fieldName, $_POST['infofield_'.$fieldId]);
				$text = $_POST['infofield_'.$fieldId];
				unset($_POST['infofield_'.$fieldId]);
				//JS_Alert('Changes saved!');
			}

			$rows = 6+substr_count($text, "\n");
			if ($rows > 36) $rows = 36;

			$last_edited = 'never';
			if (!empty($data['timeEdited'])) $last_edited = $data['timeEdited'].' by '.$data['editorName'];

			echo '<form method="post" name="editinfofieldform" action="'.wikiURLadd('Edit', $fieldName).'">'.
					 '<textarea name="infofield_'.$fieldId.'" cols="70%" rows="'.$rows.'">'.$text.'</textarea><br/>'.
					 'Last edited '.$last_edited.'<br/>'.
					 '<input type="submit" class="button" value="Save"/>';

			if ($config['infofield']['allow_files']) {
				$filelist = getFiles($db, $fieldId, FILETYPE_INFOFIELD);
				
				$showedText = 0;
				$str = '';

				for ($i=0; $i<count($filelist); $i++) {
						
					$fileTag = '[file'.$filelist[$i]['fileId'].']';
					$imageTag = '[image'.$filelist[$i]['fileId'].']';
	
					$last_name = '';
					$pos = strrpos($filelist[$i]['fileName'], '.');
					if ($pos !== false) $last_name = strtolower(substr($filelist[$i]['fileName'], $pos));

					if (in_array($last_name, $config['allowed_image_extensions'])) {
						$useTag = $imageTag;
					} else {
						$useTag = $fileTag;
					}
	
					$pos1 = strpos($text, $fileTag);
					$pos2 = strpos($text, $imageTag);
	
					if (($pos1 === false) && ($pos2 === false)) {
						if (!$showedText) { $str = ' <b>unused files:</b> '; $showedText=1; }
						$str .= '<span onclick="document.editinfofieldform.infofield.value += \' '.$useTag.'\';">'.$useTag.'</span>, ';
					}
				}
				if (substr($str, -2) == ', ') $str = substr($str, 0, -2);
				echo $str;
			}
			echo '</form>';				
		}
		elseif ($current_tab == 'Comments')
		{
			if (!empty($_POST['comment_'.$fieldId])) {
				addComment($db, COMMENT_INFOFIELD, $fieldId, $_POST['comment_'.$fieldId]);
				unset($_POST['comment_'.$fieldId]);
			}

			$list = getComments($db, COMMENT_INFOFIELD, $fieldId);

			$info = '';
			for ($i=0; $i<count($list); $i++) {
				$info .= '"'.$list[$i]['commentText'].'", skrivet '.getDatestringShort($list[$i]['commentTime']).' av '.$list[$i]['userName'].'<br>';
			}
			echo 	'<br/>'.
						'Write a comment:<br/>'.
						'<form method="post" action="'.wikiURLadd('Comments', $fieldName).'" name="infofieldcommentform">'.
						'<table width="100%" cellpadding="0" cellspacing="0" border="0">'.
							'<tr><td><textarea name="comment_'.$fieldId.'" cols="61" rows="4"></textarea><br/><img src="c.gif" width="1" height="5" alt=""/></td></tr>'.
							'<tr><td><input type="submit" class="button" value="Send"/></td></tr>'.
						'</table>'.
						'</form>';
		}
		elseif ($current_tab == 'Files')
		{
			echo showFileAttachments($db, $fieldId, FILETYPE_INFOFIELD, $fieldName);
		}
		elseif ($config['infofield']['log_history'] && $current_tab == 'History')
		{
			echo 'History of '.$fieldName.' (id # '.$fieldId.')<br/><br/>';

			echo 'Current version:<br/>';
			echo '<b><a href="#" onclick="return toggle_element_by_name(\'layer_history_current\')">Written by '.$data['editorName'].' at '.$data['timeEdited'].' ('.strlen($text).' bytes)</a></b><br/>';
			echo '<div id="layer_history_current" style="display: none; overflow:auto; width:100%; border: #000000 1px solid; background-color:#E0E0E0;">';

			$tmptext = nl2br(htmlentities($text, ENT_COMPAT, 'UTF-8'));
			$tmptext = str_replace("(mybr)", "\n", $tmptext);
			echo $tmptext;
			echo '</div>';

			$list = getRevisions(REVISIONS_WIKI, $fieldId);
			if ($list)
			{
				echo '<br/>Archived versions ('.count($list).' entries):<br/>';
				for ($i=0; $i<count($list); $i++)
				{
					echo '<br/><li>#'.($i+1).': <a href="#" onclick="return toggle_element_by_name(\'layer_history'.$i.'\')">Written by '.$list[$i]['editorName']. ' at '.$list[$i]['timeEdited'].' ('.strlen($list[$i]['fieldText']).' bytes)</a><br/>';
					echo '<div id="layer_history'.$i.'" style="display: none; overflow:auto; width:100%; border: #000000 1px solid; background-color:#E0E0E0;">';

					$tmptext = nl2br(htmlentities($list[$i]['fieldText'], ENT_COMPAT, 'UTF-8'));
					$tmptext = str_replace("(mybr)", "\n", $tmptext);
					echo $tmptext;

					echo '</div>';
				}
			}
			else
			{
				echo '<br/><b>There is no edit history of this infofield in the database.</b><br/>';
			}
		}
		else
		{
			echo wikiFormat($data, $fieldName);
		}

		if ($config['infofield']['allow_comments']) {
			$talkbackComments = getCommentsCount($db, COMMENT_INFOFIELD, $fieldId);
			if ($talkbackComments == 1) $talkback = 'Talkback: 1 comment';
			else $talkback = 'Talkback: '.$talkbackComments.' comments';
		}

		echo 			'</div>';

		if ($config['infofield']['allow_comments']) {
			echo '<div class="infofield_foot"><ul>'.
							'<li>'.($current_tab=='comments'?'<strong>':'').'<a href="'.wikiURLadd('Comments', $fieldName).'">'.$talkback.'</a>'.($current_tab=='comments'?'</strong>':'').'</li>'.
					'</ul></div>';
		}
		echo '</div>';

		return true;
	}

	function wikiURLadd($_page, $_section)
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
			
			if (!isset($skipit[1])) {
				$out_args .= $vals[0].'='.$vals[1].'&amp;';
			}
		}

		if ($out_args) {
			return $arr['path'].'?'.urlencode($out_args).'&'.$_wikiURL;
		}
		return $arr['path'].'?'.$_wikiURL;
	}
?>