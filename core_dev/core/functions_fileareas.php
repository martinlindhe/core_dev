<?php
/**
 * $Id$
 *
 * File area implementation
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('atom_rating.php');	//for file rating

/**
 * Displays a file, depending on file type
 */
function showFile($fileId, $mime = '', $title = '', $click = true)
{
	global $config, $files;
	if (!is_numeric($fileId)) return false;
	if (!$mime) {
		$data = Files::getFileInfo($fileId);
		$mime = $data['fileMime'];
	}

	if (in_array($mime, $files->image_mime_types)) {
		if ($click) echo '<div class="file_gadget_entry" id="file_'.$fileId.'" title="'.$title.'" onclick="zoomImage('.$fileId.',1,'.($files->allow_rating?'1':'0').');"><center>';
		echo showThumb($fileId);
		if ($click) echo '</center></div>';
	} else if (in_array($mime, $files->audio_mime_types)) {
		if ($click) echo '<div class="file_gadget_entry" id="file_'.$fileId.'" title="'.$title.'" onclick="zoomAudio('.$fileId.',\''.urlencode($title).'\');"><center>';
		echo '<img src="'.$config['core']['web_root'].'gfx/icon_file_audio.png" width="70" height="70" alt="Audio file"/>';
		if ($click) echo '</center></div>';
	} else if (in_array($mime, $files->video_mime_types)) {

		if ($click) echo '<div class="file_gadget_entry" id="file_'.$fileId.'" title="'.$title.'" onclick="zoomVideo('.$fileId.',\''.urlencode($title).'\');"><center>';
		echo '<table cellpadding="0" cellspacing="0" border="0"><tr>';
		echo '<td width="10" style="background: url(\''.$config['core']['web_root'].'gfx/video_left.png\')">&nbsp;</td>';
		echo '<td>';

		if ($files->process_callback && $mime != $files->default_video) {
			echo t('Video awaiting conversion.');
		} else {

			$vid_thumb = $files->getFiles(FILETYPE_CLONE_VIDEOTHUMB10, $fileId);
			if ($vid_thumb) {
				echo showThumb($vid_thumb[0]['fileId'], '', 64, 64);
			} else {
				echo '<img src="'.$config['core']['web_root'].'gfx/vid_thumb_missing.png" width="64" height="64" alt="Video file"/>';
			}
		}
		echo '</td>';
		echo '<td width="10" style="background: url(\''.$config['core']['web_root'].'gfx/video_right.png\')">&nbsp;</td>';
		echo '</tr></table>';
		if ($click) echo '</center></div>';

	} else if (in_array($mime, $files->document_mime_types)) {
		if ($click) echo '<div class="file_gadget_entry" id="file_'.$fileId.'" title="'.$title.'" onclick="zoomFile('.$fileId.');"><center>';
		echo '<img src="'.$config['core']['web_root'].'gfx/icon_file_document.png" width="40" height="49" alt="Document"/>';
		if ($click) echo '</center></div>';
	} else {
		if ($click) echo '<div class="file_gadget_entry" id="file_'.$fileId.'" title="'.$title.'" onclick="zoomFile('.$fileId.');"><center>';
		echo 'General file:<br/>';
		echo $mime.'<br/>';
		if ($click) echo '</center></div>';
	}
}

/**
 * Shows all files uploaded in a public file area (FILETYPE_FILEAREA_UPLOAD)
 * Or all files belonging to a wiki (FILETYPE_WIKI)
 *
 * \param $fileType type of files to show
 * \param $ownerId show files from this owner only
 * \param $categoryId show files from this category only
 */
function showFiles($fileType, $ownerId = 0, $categoryId = 0)
{
	global $session, $db, $config, $files;
	if (!is_numeric($fileType) || !is_numeric($categoryId)) return;

	if ($fileType == FILETYPE_FILEAREA_UPLOAD || $fileType == FILETYPE_USERFILE || $fileType == FILETYPE_WIKI) {
		if (!empty($_GET['file_category_id']) && is_numeric($_GET['file_category_id'])) $categoryId = $_GET['file_category_id'];
	}

	if (($session->id || $this->anon_uploads) && !empty($_FILES['file1'])) {
		$uploadedId = $files->handleUpload($_FILES['file1'], $fileType, $ownerId, $categoryId);
		unset($_FILES['file1']);	//to avoid further processing of this file upload elsewhere
		if (!empty($_POST['fdesc'])) {
			addComment(COMMENT_FILEDESC, $uploadedId, $_POST['fdesc']);
		}
		if ($fileType == FILETYPE_WIKI) {
			addRevision(REVISIONS_WIKI, $ownerId, 'File uploaded...', now(), $session->id, REV_CAT_FILE_UPLOADED);
		}
	}

	$userid = $session->id;
	$username = $session->username;

	$action = '';
	if ($categoryId) $action = '?file_category_id='.$categoryId;

	if ($session->error) $session->showError();

	echo '<div id="ajax_anim" style="display:none; float:right; background-color: #eee; padding: 5px; border: 1px solid #aaa;">';
	echo '<img id="ajax_anim_pic" alt="AJAX Loading ..." title="AJAX Loading ..." src="'.$config['core']['web_root'].'gfx/ajax_loading.gif"/></div>';

	echo '<div class="file_gadget">';

	echo '<div class="file_gadget_header">';

	switch ($fileType)
	{
		case FILETYPE_USERFILE:
			if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
				$userid = $_GET['id'];
				$username = Users::getName($userid);
			}
			if ($categoryId) echo '<h2>'.getCategoryName(CATEGORY_USERFILE, $categoryId).'</h2>';
			echo t('Show file category').': '.getCategoriesSelect(CATEGORY_USERFILE, 0, '', $categoryId, URLadd('file_category_id')).'<br/>';
			break;

		case FILETYPE_FILEAREA_UPLOAD:
			if (!$categoryId) echo 'File area - Root Level content<br/>';
			else echo ' - '.getCategoryName(CATEGORY_USERFILE, $categoryId).' content<br/>';
			break;

		case FILETYPE_WIKI:
			if (!$categoryId) echo 'Wiki files - Root Level content<br/>';
			echo 'Wiki attachments<br/>';
			echo getCategoriesSelect(CATEGORY_WIKIFILE, 0, '', $categoryId, URLadd('file_category_id')).'<br/>';
			break;

		case FILETYPE_BLOG:
			echo 'Blog attachments';
			break;

		case FILETYPE_NEWS:
			echo 'News article attachments';
			break;

		case FILETYPE_PROCESS:
			echo 'Process server content';
			break;

		case FILETYPE_CLONE_CONVERTED:
			echo 'Process server converted files';
			break;

		default:
			die('unknown filetype');
	}
	echo '</div>';

	$list = Files::getFiles($fileType, $ownerId, $categoryId);

	echo '<div id="filearea_mover" style="display:none">';
	echo '<form method="post" action=""/>';
	echo t('Move the file to').': ';
	echo getCategoriesSelect(CATEGORY_USERFILE, 0, 'filearea_move_to');
	echo '<input type="button" class="button" value="'.t('Move').'" onclick="filearea_move_file(this.form.filearea_move_to.value)"/>';
	echo '<input type="button" class="button" value="'.t('Cancel').'" onclick="filearea_mover_close()"/>';
	echo '</form>';
	echo '</div>';

	showImageGadgetXHTML($ownerId);
	showAudioGadgetXHTML($ownerId);
	showVideoGadgetXHTML($ownerId);
	showDocumentGadgetXHTML($ownerId);

	echo '<div id="zoom_fileinfo" style="display:none"></div>';

	echo '<div id="file_gadget_content">';

	foreach ($list as $row) {
		$title = htmlspecialchars($row['fileName']).' ('.formatDataSize($row['fileSize']).')';
		showFile($row['fileId'], $row['fileMime'], $title);
	}

	//FIXME: gör ett progress id av session id + random id, så en user kan ha flera paralella uploads
	//FIXME: stöd anon_uploads ! den ignoreras totalt idag, dvs anon uploads tillåts aldrig
	if (
		($fileType == FILETYPE_USERFILE && $session->id == $userid) ||
		($fileType == FILETYPE_NEWS && $session->isAdmin) ||
		($fileType == FILETYPE_WIKI) ||
		($fileType == FILETYPE_BLOG) ||
		($fileType == FILETYPE_FILEAREA_UPLOAD)
	) {
		$file_upload = true;
		if ($fileType == FILETYPE_BLOG) {
			$data = getBlog($ownerId);
			if ($data['userId'] != $session->id) $file_upload = false;
		}

		if ($file_upload) {
			echo '<div id="file_gadget_upload">';
			if ($files->allow_user_categories && (
					($fileType == FILETYPE_USERFILE && !$categoryId && $session->id == $userid) ||
					$fileType == FILETYPE_WIKI
			) ) {
				echo '<input type="button" class="button" value="'.t('New file category').'" onclick="show_element_by_name(\'file_gadget_category\'); hide_element_by_name(\'file_gadget_upload\');"/><br/>';
			}

			if ($files->apc_uploads) {
				echo '<form name="ajax_file_upload" method="post" action="'.$action.'" enctype="multipart/form-data" onsubmit="return submit_apc_upload('.$session->id.');">';
				echo '<input type="hidden" name="APC_UPLOAD_PROGRESS" value="'.$session->id.'"/>';
			} else {
				echo '<form name="ajax_file_upload" method="post" action="'.$action.'" enctype="multipart/form-data">';
			}
			echo t('Upload a file').':<br/>';
			echo '<input type="file" name="file1"/><br/>';
			echo t('Description').':<br/>';
			echo xhtmlTextarea('fdesc', '', 50, 4).'<br/>';
			echo '<input type="submit" class="button" value="'.t('Upload').'"/>';
			echo '</form>';
			echo '</div>';
			if ($files->apc_uploads) {
				echo '<div id="file_gadget_apc_progress" style="display:none">';
				echo '</div>';
			}
		}
	}

	if ($files->allow_user_categories && (
		($fileType == FILETYPE_USERFILE && !$categoryId) ||
		($fileType == FILETYPE_WIKI)
	) ) {
		echo '<div id="file_gadget_category" style="display: none;">';
		if ($fileType == FILETYPE_USERFILE) echo manageCategoriesDialog(CATEGORY_USERFILE);
		if ($fileType == FILETYPE_WIKI) echo manageCategoriesDialog(CATEGORY_WIKIFILE);
		echo '</div>';
	}
	echo '</div>'; //id="file_gadget_content"
	echo '</div>'; //class="file_gadget"
}

/**
 * Shows tumbnail overview of files
 * Click a thumbnail to show the whole image
 *
 * \param $fileType type of files to show
 * \param $categoryId category to display files from
 */
function showThumbnails($fileType, $categoryId)
{
	global $config, $session, $db;
	if (!is_numeric($fileType)) return false;

	$list = $db->getArray('SELECT * FROM tblFiles WHERE categoryId='.$categoryId.' AND fileType='.$fileType.' ORDER BY timeUploaded ASC');

	if (!$list) {
		echo 'No thumbnails to show!';
		return;
	}

	echo '<div id="image_thumbs_scroll_up" onclick="scroll_element_content(\'image_thumbs_scroller\', -'.($this->thumb_default_height*3).');"></div>';
	echo '<div id="image_thumbs_scroll_down" onclick="scroll_element_content(\'image_thumbs_scroller\', '.($this->thumb_default_height*3).');"></div>';
	echo '<div id="image_thumbs_scroller">';

	//show thumbnail of each image
	echo '<div class="thumbnails_gadget">';
	foreach ($list as $row) {
		if (in_array($row['fileMime'], $this->image_mime_types)) {
			echo '<div class="thumbnails_gadget_entry" id="thumb_'.$row['fileId'].'" onclick="loadImage('.$row['fileId'].', \'image_big\');"><center>';
			echo makeThumbLink($row['fileId'], $row['fileName']);
			echo '</center></div>';
		}
	}
	echo '</div>';
	echo '</div>'; //id="image_thumbs_scroller"

	echo '<div id="image_comments">';
	echo '<iframe id="image_comments_iframe" width="100%" height="100%" frameborder="0" marginheight="0" marginwidth="0" src="'.$config['core']['web_root'].'api/html_imgcomments.php?i='.$list[0]['fileId'].getProjectPath().'"></iframe>';
	echo '</div>';

	echo '<div id="image_big_holder">';
	echo '<div id="image_big">'.makeImageLink($list[0]['fileId'], $list[0]['fileName']).'</div>';
	echo '</div>';	//id="image_big_holder"
}

/**
 * Used by the ajax file core/ajax_fileinfo.php to show file details of currently selected file
 *
 * \param $_id fileId
 */
function showFileInfo($_id)
{
	global $session, $files;

	$file = $files->getFileInfo($_id);
	if (!$file) return false;

	$list = getComments(COMMENT_FILEDESC, $_id);
	if ($list && $list[0]['commentText']) {
		echo '<b>'.t('Description').':</b><br/>';
		echo nl2br(strip_tags($list[0]['commentText'])).'<br/><br/>';
	} else {
		echo '<b><i>'.t('No description').'</i></b><br/><br/>';
	}

	echo t('Uploaded at').': '.formatTime($file['timeUploaded']).' ('.ago($file['timeUploaded']).')<br/>';
	echo t('Filename').': '.strip_tags($file['fileName']).'<br/>';
	echo t('Filesize').': '.formatDataSize($file['fileSize']).' ('.$file['fileSize'].' bytes)<br/>';
	if ($files->count_file_views) echo t('Downloaded').': '.$file['cnt'].' '.t('times').'<br/>';

	if (!$session->isAdmin) return;
	echo t('Uploader').': '.htmlentities($file['uploaderName']).'<br/>';
	echo 'Mime type: '.$file['fileMime'].'<br/>';

	if (in_array($file['fileMime'], $files->image_mime_types)) {
		//Show additional information for image files
		list($img_width, $img_height) = getimagesize($files->findUploadPath($_id));
		echo t('Width').': '.$img_width.', '.t('Height').': '.$img_height.'<br/>';
		echo makeThumbLink($_id);
	} else if (in_array($file['fileMime'], $files->audio_mime_types) && extension_loaded('id3')) {
		//Show additional information for audio files
		echo '<h3>id3 tag</h3>';
		$id3 = @id3_get_tag($this->findUploadPath($_id), ID3_V2_2);	//XXX: the warning suppress was because the wip plugin caused a warning sometime on parsing id. maybe unneeded when you read this
		d($id3);
	}

	//display checksums, if any
	$arr = $files->checksums($_id);
	echo '<h3>Checksums</h3>';
	echo '<pre>';
	echo 'sha1: '.$arr['sha1']."\n";
	echo 'md5:  '.$arr['md5']."\n";
	echo '</pre>';
	echo 'Generated at '.$arr['timeCreated'].' in '.$arr['timeExec'].' sec<br/>';
}

/**
 * Generates image gadget
 *
 * \param $ownerId owner of this image
 */
function showImageGadgetXHTML($ownerId)
{
	global $config, $session, $files;

	echo '<div id="zoom_image_layer" style="display:none">';
	//echo 	'<center>';
	echo 		'<input type="button" class="button_bold" value="'.t('Close').'" onclick="zoom_hide_elements()"/>';
	//FIXME: make it possible to configure what buttons to hide
	if ($session->isAdmin) {
		echo	'<input type="button" class="button" value="'.t('Download').'" onclick="download_selected_file()"/>';
		echo	'<input type="button" class="button" value="'.t('Pass thru').'" onclick="passthru_selected_file()"/>';
	}
	if ($session->id == $ownerId || $session->isAdmin) {
		echo	'<input type="button" class="button" value="'.t('View log').'" onclick="viewlog_selected_file()"/>';
	}
	if ($session->id != $ownerId) {
		echo	'<input type="button" class="button" value="'.t('Report').'" onclick="report_selected_file()"/>';
	}
	echo	'<input type="button" class="button" value="'.t('Comments').'" onclick="comment_selected_file()"/><br/>';

	if ($session->id == $ownerId || $session->isAdmin) {
		echo '<div id="slider_toolbar" style="display:none; clear: both">';
		echo	'<p align="left">';
		echo	'<div id="resize_slider" style="width:200px;background-color:#aaa;height:5px;margin:10px;">';
		echo	'<div id="resize_slider_handle" style="width:5px;height:10px;background-color:#f00;cursor:move;"> </div>';
		echo	'</div>';
		echo	'<input type="button" class="button" value="'.t('Save').'" onclick="resize_selection()"/>';
		echo	'<input type="button" class="button" value="'.t('Cancel').'" onclick="cancel_resizer()"/>';
		echo	'</p>';
		echo '</div>';
	}

	echo '<div id="zoom_image_holder">';
	echo '<img id="zoom_image" src="'.$config['core']['web_root'].'gfx/ajax_loading.gif" alt="Image"/>';
	echo '</div>';

	if ($session->id == $ownerId || $session->isAdmin) {
		echo '<div id="cropper_toolbar" style="display:none">';
		echo	'<input type="button" class="button" value="'.t('Crop selection').'" onclick="crop_selection()"/>';
		echo	'<input type="button" class="button" value="'.t('Cancel').'" onclick="hide_cropper()"/>';
		echo '</div>';

		echo	'<input type="button" class="button" value="'.t('Crop').'" onclick="crop_selected_file()"/>';
		echo	'<input type="button" class="button" value="'.t('Resize').'" onclick="resize_selected_file()"/>';
		echo	'<input type="button" class="button" value="'.t('Rotate left').'" onclick="rotate_selected_file(90)"/>';
		echo	'<input type="button" class="button" value="'.t('Rotate right').'" onclick="rotate_selected_file(-90)"/>';
		echo	'<input type="button" class="button" value="'.t('Move').'" onclick="move_selected_file()"/>';
		echo	'<input type="button" class="button" value="'.t('Delete image').'" onclick="delete_selected_file()"/>';
	} else {
		echo	'<input type="button" class="button" value="'.t('Report').'" onclick="report_selected_file()"/>';
	}

	if ($files->allow_rating) {
		echo '<div id="rate_file"></div>';
	}
	//echo	'</center>';
	echo '</div>';
}

/**
 * Generates audio gadget
 */
function showAudioGadgetXHTML($ownerId)
{
	global $session;

	echo '<div id="zoom_audio_layer" style="display:none">';
	echo	'<center>';
	echo	'<input type="button" class="button_bold" value="'.t('Close').'" onclick="zoom_hide_elements()"/>';
	//FIXME: make it possible to configure what buttons to hide
	if ($session->isAdmin) {
		echo	'<input type="button" class="button" value="'.t('Download').'" onclick="download_selected_file()"/>';
		echo	'<input type="button" class="button" value="'.t('Pass thru').'" onclick="passthru_selected_file()"/>';
	}

	echo	'<div id="zoom_audio" style="width: 180px; height: 45px;"></div>';

	if ($session->id == $ownerId  || $session->isAdmin) {
		echo	'<input type="button" class="button" value="'.t('View log').'" onclick="viewlog_selected_file()"/>';
		echo '<input type="button" class="button" value="'.t('Move').'" onclick="move_selected_file()"/>';
		echo '<input type="button" class="button" value="'.t('Delete').'" onclick="delete_selected_file()"/>';
	}
	if ($session->id != $ownerId) {
		echo	'<input type="button" class="button" value="'.t('Report').'" onclick="report_selected_file()"/>';
	}
	echo	'<input type="button" class="button" value="'.t('Comments').'" onclick="comment_selected_file()"/><br/>';
	echo	'</center>';
	echo '</div>';
}

/**
 * Generates video gadget
 */
function showVideoGadgetXHTML($ownerId)
{
	global $session;

	echo '<div id="zoom_video_layer" style="display:none">';
	echo	'<center>';
	echo	'<input type="button" class="button_bold" value="'.t('Close').'" onclick="zoom_hide_elements()"/>';
	//FIXME: make it possible to configure what buttons to hide
	if ($session->isAdmin) {
		echo	'<input type="button" class="button" value="'.t('Download').'" onclick="download_selected_file()"/>';
	}

	echo	'<div id="zoom_video">';
	echo	'<a href="http://www.macromedia.com/go/getflashplayer">You need Flash Player</a>.';
	echo	'</div>';

	if ($session->id == $ownerId || $session->isAdmin) {
		echo	'<input type="button" class="button" value="'.t('View log').'" onclick="viewlog_selected_file()"/>';
		echo	'<input type="button" class="button" value="'.t('Move').'" onclick="move_selected_file()"/>';
		echo '<input type="button" class="button" value="'.t('Delete').'" onclick="delete_selected_file()"/>';
	}
	if ($session->id != $ownerId) {
		echo	'<input type="button" class="button" value="'.t('Report').'" onclick="report_selected_file()"/>';
	}
	echo	'<input type="button" class="button" value="'.t('Comments').'" onclick="comment_selected_file()"/><br/>';
	echo '</center>';
	echo '</div>';
}

/**
 * Generates document gadget
 */
function showDocumentGadgetXHTML($ownerId)
{
	global $session;

	echo '<div id="zoom_file_layer" style="display:none">';
	echo	'<center>';
	echo	'<input type="button" class="button_bold" value="'.t('Close').'" onclick="zoom_hide_elements()"/> ';
	//FIXME: make it possible to configure what buttons to hide
	if ($session->isAdmin) {
		echo	'<input type="button" class="button" value="'.t('Download').'" onclick="download_selected_file()"/>';
		echo	'<input type="button" class="button" value="'.t('Pass thru').'" onclick="passthru_selected_file()"/>';
	}
	if ($session->id == $ownerId || $session->isAdmin) {
		echo '<input type="button" class="button" value="'.t('View log').'" onclick="viewlog_selected_file()"/><br/>';
		echo '<input type="button" class="button" value="'.t('Move').'" onclick="move_selected_file()"/>';
		echo '<input type="button" class="button" value="'.t('Delete').'" onclick="delete_selected_file()"/>';
	}
	if ($session->id != $ownerId) {
		echo	'<input type="button" class="button" value="'.t('Report').'" onclick="report_selected_file()"/>';
	}
	echo	'<input type="button" class="button" value="'.t('Comments').'" onclick="comment_selected_file()"/><br/>';
	echo '</center>';
	echo '</div>';
}

/**
 * XXX
 */
function showFileViewlog($fileId)
{
	global $session, $files;
	//FIXME kolla filägaren

	$list = getVisits(VISIT_FILE, $fileId, 0);
	d($list);
}

?>
