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
$session->requireSuperAdmin();

require($project.'design_head.php');

echo createMenu($admin_menu, 'blog_menu');
echo createMenu($super_admin_menu, 'blog_menu');
echo createMenu($super_admin_tools_menu, 'blog_menu');

echo '<h2>File checker utility</h2>';

if (!is_dir($files->upload_dir)) {
	echo 'Fatal error: '.$files->upload_dir.' dont exist. Please adjust config.php for your project.';
	die;
}

if (isset($_GET['update'])) {
	$list = Files::getFiles();
	foreach ($list as $row) {
		$filename = $files->findUploadPath($row['fileId']);
		if (file_exists($filename)) {

			$filesize = filesize($filename);
			if ($filesize != $row['fileSize']) {
				echo '<h2>File '.$row['fileId'].' size changed from '.$row['fileSize'].' to '.$filesize.'</h2>';
				//FIXME option to update file entry
			}

			$mime_type = $files->lookupMimeType($filename);
			if ($mime_type != $row['fileMime']) {
				echo '<h2>File '.$row['fileId'].' mime type changed from '.$row['fileMime'].' to '.$mime_type.'</h2>';
				//FIXME option to update file entry
			}

			$media_type = $files->lookupMediaType($filename);
			if ($media_type != $row['mediaType']) {
				echo '<h2>File '.$row['fileId'].' media type changed from '.$row['mediaType'].' to '.$media_type.'</h2>';
				//FIXME option to update file entry
			}

			$oldsums = $files->checksums($row['fileId'], false, false);
			$newsums = $files->checksums($row['fileId'], true, false);
			if ($oldsums['sha1'] != $newsums['sha1']) {
				echo '<h2>File '.$row['fileId'].' sha1 changed from '.$oldsums['sha1'].' to '.$newsums['sha1'].'</h2>';
				//FIXME option to update file entry
			}
		} else {
			echo '<h2>File '.$row['fileId'].' is missing from disk!</h2>';
			//FIXME option to delete file entry
		}
	}
}

$tot = Files::getFileCount();

echo 'The database contains <b>'.$tot.'</b> files.<br/><br/>';

echo '<a href="'.$_SERVER['PHP_SELF'].'?update">Update file information</a>';

require($project.'design_foot.php');
?>
