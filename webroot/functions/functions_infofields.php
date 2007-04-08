<?
	//infofield module settings:
	$config['infofield']['log_history'] = true;
	$config['infofield']['allow_comments'] = false;
	$config['infofield']['allow_files'] = false;
	$config['infofield']['allow_html'] = false;
	$config['infofield']['explain_words'] = false;

	/* functions_infofields.php																										*/
	/* --------------------------------------------------------------------------	*/
	/* Written by Martin Lindhe	<martin_lindhe@yahoo.se>													*/
	/*																																						*/
	/* Module-specific tables: tblInfoFields, tblInfoFieldsHistory								*/
	/* Module uses these tbls: tblUsers																						*/
	/* --------------------------------------------------------------------------	*/
	/* MODULE TODO's:																															*/
	/*	- Multi-language support																									*/
	/* ------------------------------------------------------------end of header-	*/
	
/*
	//Swedish:
	define('INFOFIELD_TABTEXT_HISTORY', 'Historik');
	define('INFOFIELD_TABTEXT_FILES',		'Filer');
	define('INFOFIELD_TABTEXT_HIDE', 		'G&ouml;m');
	
	define('INFOFIELD_TEXT_LASTEDITED',	'senast &auml;ndrad');

	//Norweigan:
	define('INFOFIELD_TABTEXT_HISTORY', 'Historikk');
	define('INFOFIELD_TABTEXT_FILES',		'Filer');
	define('INFOFIELD_TABTEXT_HIDE', 		'Gj&oslash;mm');
	
	define('INFOFIELD_TEXT_LASTEDITED',	'edited');
*/

	define('INFOFIELD_TABTEXT_HISTORY', 'History');
	define('INFOFIELD_TABTEXT_FILES',		'Files');
	define('INFOFIELD_TABTEXT_HIDE', 		'Hide');
	
	define('INFOFIELD_TEXT_LASTEDITED',	'edited');

	

	/* Optimization: Doesnt store identical entries if you hit Save button multiple times */
	function updateInfoField(&$db, $userId, $fieldName, $fieldText)
	{
		global $config;

		if (!is_numeric($userId)) return false;

		$fieldName = dbAddSlashes($db, trim($fieldName));
		if (!$fieldName || !$_SESSION['isSuperAdmin']) return false;

		$sql = 'SELECT fieldId,fieldText FROM tblInfoFields WHERE fieldName="'.$fieldName.'"';
		$data = dbOneResult($db, $sql);
		
		/* Aborts if we are trying to save a exact copy as the last one */
		if ($data['fieldText'] == $fieldText) return false;
		
		$fieldText = dbAddSlashes($db, trim($fieldText));
		
		if ($data['fieldId'])
		{
			if ($config['infofield']['log_history'])
			{
				//Stores backup entry in tblInfoFieldsHistory
				$sql  = 'INSERT INTO tblInfoFieldsHistory (fieldId,fieldText,editedBy,timeEdited) ';
				$sql .= 'SELECT fieldId,fieldText,'.$userId.' AS editedBy,timeEdited FROM tblInfoFields WHERE fieldId='.$data['fieldId'];
				dbQuery($db, $sql);
			}
			dbQuery($db, 'UPDATE tblInfoFields SET fieldText="'.$fieldText.'",timeEdited=NOW(),editedBy='.$userId.' WHERE fieldName="'.$fieldName.'"' );
		}
		else
		{
			dbQuery($db, 'INSERT INTO tblInfoFields SET fieldName="'.$fieldName.'", fieldText="'.$fieldText.'",timeEdited=NOW(),editedBy='.$userId );
		}
	}
	
	function getInfoFieldHistory($fieldId)
	{
		global $db;

		if (!is_numeric($fieldId) || !$_SESSION['isSuperAdmin']) return false;

		$sql  = 'SELECT t1.*,t2.userName AS editorName FROM tblInfoFieldsHistory AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.editedBy=t2.userId) ';
		$sql .= 'WHERE t1.fieldId='.$fieldId.' ';
		$sql .= 'ORDER BY t1.timeEdited DESC';

		return $db->getArray($sql);
	}
	
	function clearInfoFieldHistory(&$db)
	{
		if (!$_SESSION['isSuperAdmin']) return false;

		$sql  = 'DELETE FROM tblInfoFieldsHistory';
		dbQuery($db, $sql);
		return true;
	}

	function getInfoFieldHistoryCountAll(&$db)
	{
		if (!$_SESSION['isSuperAdmin']) return false;

		$sql  = 'SELECT COUNT(fieldId) FROM tblInfoFieldsHistory';

		return dbOneResultItem($db, $sql);
	}

	function formatInfoField(&$data, $fieldName)
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

				//ersätter [file123] med en htmlkod som visar fil-info
				$fileTag = '[file'.$list[$i]['fileId'].']';
				$pos = strpos($text, $fileTag);
				if ($pos !== false) {
					$fileblock = formatFileAttachment($db, $list[$i], '#F0F0F0', false, $fieldName);
					$text = str_replace($fileTag, $fileblock, $text);
					$list[$i]['replaced'] = 1;
				}
				
				//ersätter [image123] med htmlkod för thumbnail+popup full image javascript
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

			//listar filer som inte redan är enkodade i infofältet
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

	function getInfoField($fieldName)
	{
		//fixme: tillåt bara a-z, 0-9 och -_ tecken i $fieldName, samt mellanslag
		global $db, $config;

		$fieldName = $db->escape(trim($fieldName));
		if (!$fieldName) return false;

		if ($_SESSION['isSuperAdmin'] && !isset($_GET['hideinfopanel']) && ( !empty($_GET['infoedit']) || !empty($_GET['infohistory']) )
		) {
			$sql  = 'SELECT t1.fieldId,t1.fieldText,t1.hasFiles,t1.timeEdited,t2.userName AS editorName '.
							'FROM tblInfoFields AS t1 '.
							'INNER JOIN tblUsers AS t2 ON (t1.editedBy=t2.userId) '.
							'WHERE fieldName="'.$fieldName.'"';
		}
		else
		{
			$sql = 'SELECT fieldId,fieldText,hasFiles FROM tblInfoFields WHERE fieldName="'.$fieldName.'"';
		}
		
		$data = $db->getOneRow($sql);

		$fieldId = $data['fieldId'];
		$text = stripslashes($data['fieldText']);

		if ($_SESSION['isSuperAdmin'] && !isset($_GET['hideinfopanel']))
		{
			/* Display the infofield toolbar for super admins */
			if (!empty($_GET['infoedit']) && ($_GET['infoedit'] == $fieldName))
			{
				if (isset($_POST['infofield']))
				{
					//save changes to database
					updateInfoField($db, $_SESSION['userId'], $fieldName, $_POST['infofield']);
					$text = $_POST['infofield'];
					unset($_POST['infofield']);	//fixme: unsetta såhär på andra ställen med så dom inte körs flera gånger
					JS_Alert('Changes saved!');
				}

				$rows = 6+substr_count($text, "\n");
				if ($rows > 36) $rows = 36;

				$last_edited = 'never';
				if (!empty($data['timeEdited'])) $last_edited = $data['timeEdited'].' by '.$data['editorName'];

				$info = '<form method="post" name="editinfofieldform" action="'.AddToURL('infoedit', $fieldName, $_SERVER['PHP_SELF']).'">'.
								'<textarea name="infofield" cols="38" rows="'.$rows.'">'.$text.'</textarea><br/>'.
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
				}
				$info .= '</form>';				
			}
			elseif (!empty($_GET['infocomments']) && ($_GET['infocomments'] == $fieldName))
			{
				if (!empty($_POST['comment'])) {
					addComment($db, COMMENT_INFOFIELD, $fieldId, $_POST['comment']);
					unset($_POST['comment']);
				}

				$list = getComments($db, COMMENT_INFOFIELD, $fieldId);

				$info = '';
				for ($i=0; $i<count($list); $i++) {
					$info .= '"'.$list[$i]['commentText'].'", skrivet '.getDatestringShort($list[$i]['commentTime']).' av '.$list[$i]['userName'].'<br>';
				}
				$info .=
								'<br/>'.
								'Write a comment:<br/>'.
								'<form method="post" action="'.AddToURL('infocomments', $fieldName, $_SERVER['PHP_SELF']).'" name="infofieldcommentform">'.
								'<table width="100%" cellpadding="0" cellspacing="0" border="0">'.
									'<tr><td><textarea name="comment" cols="61" rows="4"></textarea><br/><img src="c.gif" width="1" height="5"></td></tr>'.
									'<tr><td><input type="submit" class="button" value="Send"></td></tr>'.
								'</table>'.
								'</form>';
			}
			elseif (!empty($_GET['infofiles']) && ($_GET['infofiles'] == $fieldName))
			{
				$info = showFileAttachments($db, $fieldId, FILETYPE_INFOFIELD, $fieldName);
			}
			elseif ($config['infofield']['log_history'] && !empty($_GET['infohistory']) && ($_GET['infohistory'] == $fieldName))
			{
				$info  = 'History of '.$fieldName.' (id # '.$fieldId.')<br/><br/>';

				$info .= 'Current version:<br/>';
				$info .= '<b><a href="#" onclick="return toggle_element_by_name(\'layer_history_current\')">Written by '.$data['editorName'].' at '.$data['timeEdited'].' ('.strlen($text).' bytes)</a></b><br/>';
				$info .= '<div id="layer_history_current" style="display: none; overflow:auto; width:100%; border: #000000 1px solid; background-color:#E0E0E0;">';

				$tmptext = nl2br(htmlentities($text, ENT_COMPAT, 'UTF-8'));
				$tmptext = str_replace("(mybr)", "\n", $tmptext);
				$info .= $tmptext;
				$info .= '</div>';

				$list = getInfoFieldHistory($fieldId);
				if ($list)
				{
					$info .= '<br/>Archived versions ('.count($list).' entries):<br/>';
					for ($i=0; $i<count($list); $i++)
					{
						$info .= '<br/><li>#'.($i+1).': <a href="#" onclick="return toggle_element_by_name(\'layer_history'.$i.'\')">Written by '.$list[$i]['editorName']. ' at '.$list[$i]['timeEdited'].' ('.strlen($list[$i]['fieldText']).' bytes)</a><br/>';
						$info .= '<div id="layer_history'.$i.'" style="display: none; overflow:auto; width:100%; border: #000000 1px solid; background-color:#E0E0E0;">';


						$tmptext = nl2br(htmlentities($list[$i]['fieldText'], ENT_COMPAT, 'UTF-8'));
						$tmptext = str_replace("(mybr)", "\n", $tmptext);
						$info .= $tmptext;

						$info .='</div>';
					}
				}
				else
				{
					$info .= '<br/><b>There is no edit history of this infofield in the database.</b><br/>';
				}
			}
			else
			{
				$info = formatInfoField($data, $fieldName);
			}

			if ($config['infofield']['allow_comments']) {
				$talkbackComments = getCommentsCount($db, COMMENT_INFOFIELD, $fieldId);
				if ($talkbackComments == 1) $talkback = 'Talkback: 1 comment';
				else $talkback = 'Talkback: '.$talkbackComments.' comments';
			}

			$current_tab = 'main';
			if (!empty($_GET['infohistory'])) $current_tab = 'history';
			if (!empty($_GET['infofiles'])) $current_tab = 'files';
			if (!empty($_GET['infoedit'])) $current_tab = 'edit';
			if (!empty($_GET['infocomments'])) $current_tab = 'comments';

			$js = '';

			$html = $js.
								'<div class="infofield">'.
									'<div class="infofield_head"><ul>'.
										'<li>'.($current_tab=='main'?'<strong>':'').		'<a href="'.$_SERVER['PHP_SELF'].'">'.$fieldName.'</a>'.																					($current_tab=='main'?'</strong>':'').'</li>'.
										'<li>'.($current_tab=='edit'?'<strong>':'').		'<a href="'.AddToURL('infoedit', $fieldName, $_SERVER['PHP_SELF']).'">Edit</a>'.	($current_tab=='edit'?'</strong>':'').'</li>'.
										'<li>'.($current_tab=='history'?'<strong>':'').	'<a href="'.AddToURL('infohistory', $fieldName, $_SERVER['PHP_SELF']).'">'.INFOFIELD_TABTEXT_HISTORY.'</a>'.	($current_tab=='history'?'</strong>':'').'</li>';
			if ($config['infofield']['allow_files']) {
				$html .=		'<li>'.($current_tab=='files'?'<strong>':'').		'<a href="'.AddToURL('infofiles', $fieldName, $_SERVER['PHP_SELF']).'">'.INFOFIELD_TABTEXT_FILES.'</a>'.			($current_tab=='files'?'</strong>':'').'</li>';
			}
			$html .= 			'<li><a href="'.AddToURL('hideinfopanel', '', $_SERVER['PHP_SELF']).'">'.INFOFIELD_TABTEXT_HIDE.'</a></li>'.
									'</ul></div>'.
									'<div class="infofield_body">'.$info.'</div>';

			if ($config['infofield']['allow_comments']) {
				$html .= 	'<div class="infofield_foot"><ul>'.
										'<li>'.($current_tab=='comments'?'<strong>':'').'<a href="'.AddToURL('infocomments', $fieldName, $_SERVER['PHP_SELF']).'">'.$talkback.'</a>'.($current_tab=='comments'?'</strong>':'').'</li>'.
									'</ul></div>';
			}
			$html .= 	'</div>';

			return $html;
		}

		/* Display the infofield for a normal user */
		return formatInfoField($data, $fieldName);
	}


	/* Adds one key=val entry to a url, returning the 'fixed' url */
	/* AddToURL() returns current URL with GET params intact */
	function AddToURL($varName = null, $varVal = null, $url = null)
	{
		if (is_null($url)) {//Piece together url string
			$beginning = $_SERVER['PHP_SELF'];
			$ending = (!empty($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';

			if (is_null($varName) && empty($ending)) return $beginning;
			if (is_null($varName)) return $beginning.'?'.$ending;
		} else {
			$qstart = strpos($url, '?');
			$beginning = $url;
			$ending = '';

			if ($qstart) {
				$beginning = substr($url, 0, $qstart);
				$ending = substr($url, $qstart);
			}
		}

		if (strlen($ending) > 0) {
			$vals = array();
			$ending = str_replace('?', '', $ending);
			parse_str($ending, $vals);
			$ending = '';

			if (empty($varVal)) {
				$vals[$varName] = 'NULLVALUE';
			} else {
				$vals[$varName] = $varVal;
			}

			$count = 0;
			foreach ($vals as $k => $v) {
				if ($count > 0) $ending .= '&';
				$count++;
				if ($v == 'NULLVALUE') {
					$ending .= $k;
				} else {
					$ending .= $k.'='.urlencode($v);
				}
			}
		} else {
			if (empty($varVal)) {
				$ending = $varName;
			} else {
				$ending = $varName.'='.urlencode($varVal);
			}
		}

		$result = $beginning.'?'.$ending;
		return $result;
	}





	/*
		$js =
				'<script type="text/javascript">'.
				'function SetTextSize(s){'.
					'spanInMain=document.getElementsByTagName("span");'.
					'for (var i=0;i<spanInMain.length;i++)'.
						'if (spanInMain[i].className=="infofield_body") spanInMain[i].style.fontSize=s+"%";'.
				'}'.
				'</script>';

				//Right-menu
				'<table width="100%" height=84 cellpadding=0 cellspacing=0 border=0 style="border-left-width:0; border-top-width:1px; border-right-width:1px; border-bottom-width:1px; border-color: #A0A0A0; border-style: solid;" bgcolor=#FAFAFA>'.
					'<tr><td valign="top" align="center">'.
						'<a href="#" onclick="SetTextSize(75);" style="font-size: 75%;" title="Change text size to 75%">1</a><br>'.
						'<a href="#" onclick="SetTextSize(100);" style="font-size: 100%;" title="Change text size to 100%">2</a><br>'.
						'<a href="#" onclick="SetTextSize(125);" style="font-size: 125%;" title="Change text size to 125%">3</a><br>'.
						'<img src="c.gif" width=1 height=4><br>'.
						'<a href="#"><img src="icons/print.gif" width=15 height=10 title="Printer friendly view" border=0></a><br>'.
						'<img src="c.gif" width=1 height=6><br>'.
						'<a href="#"><img src="file_icons/pdf16.png" width=16 height=16 title="Save as PDF" border=0></a><br>'.
						'<img src="c.gif" width=1 height=2><br>'.
					'</td></tr>'.
				'</table>';
	*/
?>