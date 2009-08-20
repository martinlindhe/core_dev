<?php
/**
 * $Id$
 *
 *	core                                            tblWiki
 *	for history-support: atom_revisions.php         tblRevisions
 *	for files-support: files_default.php $h->files  tblFiles
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('atom_revisions.php');
require_once('functions_textformat.php');

//wiki module default settings:
$config['wiki']['log_history'] = true;
$config['wiki']['allow_html'] = false;
$config['wiki']['explain_words'] = false;
$config['wiki']['allow_edit'] = false;	//false = only allow admins to edit the wiki articles. true = allow all, even anonymous
$config['wiki']['allow_files'] = false;		//only accept the tabs specified in allowed_tabs
$config['wiki']['allowed_tabs'] =	array('Wiki', 'WikiEdit', 'WikiHistory', 'WikiFiles');
$config['wiki']['first_tab'] = 'Wiki';

/**
 * Update wiki entry
 */
function wikiUpdate($wikiName, $_text)
{
	global $h, $db, $config;

	$wikiName = $db->escape(trim($wikiName));
	if (!$wikiName) return false;

	$q = 'SELECT * FROM tblWiki WHERE wikiName="'.$wikiName.'"';
	$data = $db->getOneRow($q);

	//Aborts if we are trying to save a exact copy as the last one
	if (!empty($data) && $data['msg'] == $_text) return false;

	$_text = $db->escape(trim($_text));

	if (!empty($data) && $data['wikiId']) {
		if ($config['wiki']['log_history']) {
			addRevision(REVISIONS_WIKI, $data['wikiId'], $data['msg'], $data['timeCreated'], $data['createdBy'], REV_CAT_TEXT_CHANGED);
		}
		$db->update('UPDATE tblWiki SET msg="'.$_text.'",timeCreated=NOW(),createdBy='.$h->session->id.' WHERE wikiName="'.$wikiName.'"');
	} else {
		$q = 'INSERT INTO tblWiki SET wikiName="'.$wikiName.'",msg="'.$_text.'",createdBy='.$h->session->id.',timeCreated=NOW()';
		$db->insert($q);
	}
}

/**
 * Formats text for wiki output
 */
function wikiFormat($wikiName, $data)
{
	global $h, $db, $config;

	if (empty($data['msg'])) {
		echo '<div class="wiki">';
		echo '<div class="wiki_body">';
		echo t('The wiki').' "'.$wikiName.'" '.t('does not yet exist').'!<br/>';
		if ($h->session->id && $h->session->isWebmaster) {
			echo coreButton('Create', $_SERVER['PHP_SELF'].'?WikiEdit:'.$wikiName);
		}
		echo '</div>';
		echo '</div>';
		return '';
	}

	$text = stripslashes($data['msg']);
	$text = formatUserInputText($text, !$config['wiki']['allow_html']);

	if ($config['wiki']['explain_words']) {
		$text = dictExplainWords($text);
	}

	return $text;
}

/**
 * Display / edit wiki gadget
 *
 * Normally everyone can edit a wiki text and attach files to it (FIXME),
 * but you can override defaults with config settings.
 * Also, you can lock a specific wiki from editing by normal users.
 */
function wiki($wikiName = '')
{
	global $h, $db, $config;

	$current_tab = $config['wiki']['first_tab'];

	//Looks for formatted wiki section commands, like: Wiki:Page, WikiEdit:Page, WikiHistory:Page, WikiFiles:Page
	$cmd = fetchSpecialParams($config['wiki']['allowed_tabs']);
	if ($cmd) list($current_tab, $wikiName) = $cmd;
	if (!$wikiName) return false;

	$wikiName = str_replace(' ', '_', $wikiName);

	$q = 'SELECT t1.*,t2.userName AS creatorName, t3.userName AS lockerName';
	$q .= ' FROM tblWiki AS t1';
	$q .= ' LEFT JOIN tblUsers AS t2 ON (t1.createdBy=t2.userId)';
	$q .= ' LEFT JOIN tblUsers AS t3 ON (t1.lockedBy=t3.userId)';
	$q .= ' WHERE t1.wikiName="'.$db->escape($wikiName).'"';
	$data = $db->getOneRow($q);

	if (!empty($data['msg'])) {
		$text = stripslashes($data['msg']);
	} else {
		//This wiki doesnt yet exist
		$text = '';
	}

	if (!$h->session->isAdmin && !$config['wiki']['allow_edit']) {
		//Only display the text for normal visitors
		echo '<div class="wiki">';
		echo '<div class="wiki_body">';
		echo wikiFormat($wikiName, $data);
		echo '</div>';
		echo '</div>';
		return true;
	}

	if (in_array('WikiFiles', $config['wiki']['allowed_tabs']) && $data) {	//Show files tab? also hide files tab if wiki isn't yet created
		$wiki_menu = array(
			$_SERVER['PHP_SELF'].'?Wiki:'.$wikiName => 'Wiki:'.str_replace('_', ' ', $wikiName),
			$_SERVER['PHP_SELF'].'?WikiEdit:'.$wikiName => t('Edit'),
			$_SERVER['PHP_SELF'].'?WikiHistory:'.$wikiName => t('History'),
			$_SERVER['PHP_SELF'].'?WikiFiles:'.$wikiName => t('Files').' ('.$h->files->getFileCount(FILETYPE_WIKI, $data['wikiId']).')'
		);
	} else {
		$wiki_menu = array(
			$_SERVER['PHP_SELF'].'?Wiki:'.$wikiName => 'Wiki:'.str_replace('_', ' ', $wikiName),
			$_SERVER['PHP_SELF'].'?WikiEdit:'.$wikiName => t('Edit'),
			$_SERVER['PHP_SELF'].'?WikiHistory:'.$wikiName => t('History')
		);
	}

	echo '<div class="wiki">';
	createMenu($wiki_menu, 'wiki_menu');
	echo '<div class="wiki_body">';

	//Display the wiki toolbar for super admins
	if ($current_tab == 'WikiEdit' && ($h->session->isAdmin || !$data['lockedBy'])) {
		if (isset($_POST['wiki_'.$data['wikiId']])) {
			//save changes to database
			wikiUpdate($wikiName, $_POST['wiki_'.$data['wikiId']]);
			$text = $_POST['wiki_'.$data['wikiId']];
			unset($_POST['wiki_'.$data['wikiId']]);
			//JS_Alert('Changes saved!');
		}

		if ($h->session->isAdmin && isset($_GET['wiki_lock'])) {
			$q = 'UPDATE tblWiki SET lockedBy='.$h->session->id.',timeLocked=NOW() WHERE wikiId='.$data['wikiId'];
			$db->update($q);
			$data['lockedBy'] = $h->session->id;
			$data['lockerName'] = $h->session->username;
			addRevision(REVISIONS_WIKI, $data['wikiId'], 'The wiki has been locked', now(), $h->session->id, REV_CAT_LOCKED);
		}

		if ($h->session->isAdmin && isset($_GET['wiki_unlock'])) {
			$q = 'UPDATE tblWiki SET lockedBy=0 WHERE wikiId='.$data['wikiId'];
			$db->update($q);
			$data['lockedBy'] = 0;
			addRevision(REVISIONS_WIKI, $data['wikiId'], 'The wiki has been unlocked', now(), $h->session->id, REV_CAT_UNLOCKED);
		}

		$rows = 6+substr_count($text, "\n");
		if ($rows > 36) $rows = 36;

		$last_edited = t('never');
		if (!empty($data['timeCreated'])) $last_edited = $data['timeCreated'].' '.t('by').' '.$data['creatorName'];

		echo '<form method="post" name="wiki_edit" action="'.URLadd('WikiEdit:'.$wikiName).'">';

		$wikiRandId = 'wiki_'.$data['wikiId'].'_'.rand(0, 9999999);

		echo
			'<input type="button" class="button" value="[h1]" onclick="insertTags(\''.$wikiRandId.'\',\'[h1]\',\'[/h1]\',\'headline level 1\')"/>'.
			'<input type="button" class="button" value="[h2]" onclick="insertTags(\''.$wikiRandId.'\',\'[h2]\',\'[/h2]\',\'headline level 2\')"/>'.
			'<input type="button" class="button" value="[h3]" onclick="insertTags(\''.$wikiRandId.'\',\'[h3]\',\'[/h3]\',\'headline level 3\')"/>'.
			'<input type="button" class="button" value="B" style="font-weight: bold" onclick="insertTags(\''.$wikiRandId.'\',\'[b]\',\'[/b]\',\'bold text\')"/>'.
			'<input type="button" class="button" value="I" style="font-style: italic" onclick="insertTags(\''.$wikiRandId.'\',\'[i]\',\'[/i]\',\'italic text\')"/>'.
			'<input type="button" class="button" value="U" style="text-decoration: underline" onclick="insertTags(\''.$wikiRandId.'\',\'[u]\',\'[/u]\',\'underlined text\')"/>'.
			'<input type="button" class="button" value="S" style="text-decoration: line-through" onclick="insertTags(\''.$wikiRandId.'\',\'[s]\',\'[/s]\',\'strikethru text\')"/>'.
			//'<input type="button" class="button" value="[hr] (broken)" onclick="insertTags(\''.$wikiRandId.'\',\'[hr]\')"/>'.
			'<input type="button" class="button" value="[code]" onclick="insertTags(\''.$wikiRandId.'\',\'[code]\',\'[/code]\',\'code block\')"/>'.
			'<input type="button" class="button" value="[quote]" onclick="insertTags(\''.$wikiRandId.'\',\'[quote name=]\',\'[/quote]\',\'quote\')"/>'.
			'<br/>';

		echo '<textarea name="wiki_'.$data['wikiId'].'" id="'.$wikiRandId.'" cols="60" rows="'.$rows.'">'.$text.'</textarea><br/>';
		echo t('Last edited').': '.$last_edited.'<br/>';
		echo xhtmlSubmit('Save');

		if ($h->session->isAdmin) {
			if ($data['lockedBy']) {
				echo '<input type="button" class="button" value="'.t('Unlock').'" onclick="location.href=\''.URLadd('WikiEdit:'.$wikiName, '&amp;wiki_unlock').'\'"/>';
				echo '<img src="'.$config['core']['web_root'].'gfx/icon_locked.png" width="16" height="16" alt="Locked" title="This wiki is currently locked"/>';
				echo '<b>Locked by '.$data['lockerName'].' at '.$data['timeLocked'].'</b><br/>';
			} else {
				if ($data) {
					echo '<input type="button" class="button" value="'.t('Lock').'" onclick="location.href=\''.URLadd('WikiEdit:'.$wikiName, '&amp;wiki_lock').'\'"/>';
					echo '<img src="'.$config['core']['web_root'].'gfx/icon_unlocked.png" width="16" height="16" alt="Unlocked" title="This wiki is currently open for edit by anyone"/>';
				}
			}
		}

		//List "unused files" for this Wiki when in edit mode
		if ($config['wiki']['allow_files']) {
			$filelist = $h->files->getFiles(FILETYPE_WIKI, $data['wikiId']);
			if ($filelist) {
				$str = '';

				foreach ($filelist as $row) {
					$temp = explode('.', $row['fileName']);

					$showTag = $linkTag = '[[file:'.$row['fileId'].']]';

					if (in_array($row['fileMime'], $h->files->image_mime_types)) {
						$showTag = showThumb($row['fileId'], $showTag);
					}

					if (strpos($text, $linkTag) === false) {
						$str .= '<span onclick="document.wiki_edit.wiki_'.$data['wikiId'].'.value += \' '.$linkTag.'\';">'.$showTag.'</span>, ';
					}
				}
				if (substr($str, -2) == ', ') $str = substr($str, 0, -2);
				if ($str) {
					echo '<b>'.t('Unused files').':</b> '.$str;
				}
			}
		}
		echo '</form>';
	} else if ($current_tab == 'WikiFiles') {
		echo showFiles(FILETYPE_WIKI, $data['wikiId']);
	} else if ($config['wiki']['log_history'] && $current_tab == 'WikiHistory') {
		if ($data) {
			echo 'Current version:<br/>';
			echo '<b><a href="#" onclick="return toggle_element_by_name(\'layer_history_current\')">Written by '.$data['creatorName'].' at '.$data['timeCreated'].' ('.strlen($text).' bytes)</a></b><br/>';
			echo '<div id="layer_history_current" class="revision_entry">';
			echo nl2br(htmlentities($text, ENT_COMPAT, 'UTF-8'));
			echo '</div>';

			showRevisions(REVISIONS_WIKI, $data['wikiId'], $wikiName);
		} else {
			echo 'There is no history for this wiki.';
		}
	} else {
		if (!empty($data['lockedBy'])) {
			echo '<div class="wiki_locked">LOCKED - This wiki can currently not be edited</div>';
		}
		echo wikiFormat($wikiName, $data);
	}

	echo 	'</div>';
	echo '</div>';

	return true;
}
?>