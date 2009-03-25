<?php
/**
 * $Id$
 *
 * Super admin tool to update tblFiles (and tblChecksums) according to data on disc.
 * Updates file size, mime types, checksums and media types
 *
 * FIXME: add "clear all thumbnails" feature
 */

require_once('find_config.php');
$h->session->requireSuperAdmin();

require('design_admin_head.php');

echo '<h1>File checker utility</h1>';

if (!is_dir($files->upload_dir)) {
	echo 'Fatal error: '.$files->upload_dir.' dont exist. Please adjust config.php for your project.';
	die;
}

if (!empty($_POST)) {
	$list = Files::getFiles();
	foreach ($list as $row) {
		if (isset($_POST[ 'fchk_'.$row['fileId'] ])) {
			switch ($_POST['fchk_'.$row['fileId']]) {
				case 'update':
					$files->updateFile($row['fileId']);
					break;

				case 'delete':
					$files->deleteFile($row['fileId']);
					break;

				default:
					die('fatal errror unknown code '.$_POST['fchk_'.$row['fileId']]);
			}
		}
	}
}

if (isset($_GET['update'])) {
	$list = Files::getFiles();

	echo 'Checking <b>'.count($list).'</b> file entries...<br/><br/>';

	$cleanup = array();

	foreach ($list as $row) {
		$filename = $files->findUploadPath($row['fileId']);
		if (file_exists($filename)) {

			$filesize = filesize($filename);
			if ($filesize != $row['fileSize']) {
				$cleanup[$row['fileId']]['err'][] = 'size changed from '.$row['fileSize'].' to '.$filesize;
				$cleanup[$row['fileId']]['action'] = 'update';
			}

			$mime_type = $files->lookupMimeType($filename);
			if ($mime_type != $row['fileMime']) {
				$cleanup[$row['fileId']]['err'][] = 'mime type changed from '.$row['fileMime'].' to '.$mime_type;
				$cleanup[$row['fileId']]['action'] = 'update';
			}

			$media_type = $files->lookupMediaType($filename);
			if ($media_type != $row['mediaType']) {
				$cleanup[$row['fileId']]['err'][] = 'media type changed from '.$row['mediaType'].' to '.$media_type;
				$cleanup[$row['fileId']]['action'] = 'update';
			}

			$oldsums = $files->checksums($row['fileId'], false, false);
			$newsums = $files->checksums($row['fileId'], true, false);
			if ($oldsums['sha1'] != $newsums['sha1']) {
				$cleanup[$row['fileId']]['err'][] = 'sha1 changed from '.$oldsums['sha1'].' to '.$newsums['sha1'];
				$cleanup[$row['fileId']]['action'] = 'update';
			}
		} else {
			$cleanup[$row['fileId']]['err'][] = 'file missing from disk';
			$cleanup[$row['fileId']]['action'] = 'delete';
		}
	}

	if (!empty($cleanup)) {
		echo '<form method="post" action="">';
		foreach ($cleanup as $id => $info) {
			echo xhtmlCheckbox('fchk_'.$id, $info['action'].' file '.$id, $info['action'], true).'<br/>';
			echo '<ul>';
			foreach ($info['err'] as $err) {
				echo '<li>'.$err.'</li>';
			}
			echo '</ul>';
			echo '<br/>';
		}
		echo xhtmlSubmit('Continue');
		echo '</form>';
	} else {
		echo 'Files on disk & database is synchronised';
	}

} else {

	$tot = Files::getFileCount();

	echo 'The database contains <b>'.$tot.'</b> files.<br/><br/>';

	echo '<a href="'.$_SERVER['PHP_SELF'].'?update">Update file information</a>';
}

require('design_admin_foot.php');
?>
