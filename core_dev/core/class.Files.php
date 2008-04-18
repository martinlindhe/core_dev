<?
/**
 * $Id$
 *
 * Files class - Handle file upload, image manipulating, file management
 *
 * Uses tblFiles
 * Uses php_id3.dll if enabled, to show more details of mp3s in the file module
 *
 * \todo rename tblFiles.timeUploaded to tblFiles.timeCreated
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('atom_comments.php');		//for image comments support
require_once('atom_categories.php');	//for file categories support
require_once('atom_subscriptions.php');	//for userfile area subscriptions
require_once('functions_image.php');
require_once('functions_time.php');		//for ago()
require_once('functions_process.php');	//for process_client_fetchAndConvert()

define('FILETYPE_WIKI',					1);		// The file is a wiki attachment
define('FILETYPE_BLOG',					2);		// The file is a blog attachment
define('FILETYPE_NEWS',					3);		// The file is a news attachment
define('FILETYPE_FILEAREA_UPLOAD',		4);		// File is uploaded to a file area
define('FILETYPE_USERFILE',				5);		// File is uploaded to the user's own file area
define('FILETYPE_USERDATA',				6);		// File is uploaded to a userdata field
define('FILETYPE_FORUM',				7);		// File is attached to a forum post
define('FILETYPE_PROCESS',				8);		// File uploaded to be processed

define('FILETYPE_VIDEOBLOG',			10);	// video clip representing a user submitted blog
define('FILETYPE_VIDEOPRES',			11);	// video clip representing a presentation of the user
define('FILETYPE_VIDEOMESSAGE',			12);	// video clip respresenting a private message
define('FILETYPE_VIDEOCHATREQUEST',		13);	// video clip representing a live videochat request

define('FILETYPE_GENERIC',				20); // generic file type, for application specific file type

define('FILETYPE_CLONE_CONVERTED',		30);	//converted from orginal file format (image/video/audio/document)
define('FILETYPE_CLONE_VIDEOTHUMB10',	31);	//video thumbnail of video 10% into the clip

//for future use:
define('MEDIATYPE_IMAGE',		1);
define('MEDIATYPE_VIDEO',		2);
define('MEDIATYPE_AUDIO',		3);
define('MEDIATYPE_DOCUMENT',	4);
define('MEDIATYPE_WEBRESOURCE',	5);	//webresources can/will contain other files. those files will refer to this file entry as their owner

class Files
{
	public $image_mime_types = array(
		'image/jpeg',
		'image/png',
		'image/gif',
		'image/bmp'
	); ///<FIXME remove

	public $audio_mime_types	= array(
		'audio/x-mpeg',	'audio/mpeg',		//.mp3 file. FF2 = 'audio/x-mpeg', IE7 = 'audio/mpeg'
		'audio/x-ms-wma',								//.wma file. FF2 & IE7 sends this
		'application/x-ogg'							//.ogg file		- FIXME: IE7 sends mime header 'application/octet-stream' for .ogg
	); ///<FIXME remove

	public $video_mime_types = array(
		'video/mpeg',			//.mpg file
		'video/avi',			//.avi file
		'video/x-msvideo',	//.avi file
		'video/x-ms-wmv',	//Microsoft .wmv file
		'video/3gpp',			//.3gp video file
		'video/x-flv',		//Flash video
		'video/mp4',			//MPEG-4 video
		'application/ogg'	//Ogg video
	); ///<FIXME remove

	public $document_mime_types = array(
		'text/plain',					//normal text file
		'application/msword',	//Microsoft .doc file
		'application/pdf'			//Adobe .pdf file
	); ///<FIXME remove

	public $media_types = array(
		'png' => array(MEDIATYPE_IMAGE, 'image/png', 'PNG Image'),
		'jpg' => array(MEDIATYPE_IMAGE, 'image/jpeg', 'JPEG Image'),
		'gif' => array(MEDIATYPE_IMAGE, 'image/gif', 'GIF Image'),
		'bmp' => array(MEDIATYPE_IMAGE, 'image/bmp', 'BMP Image'),

		'wmv' => array(MEDIATYPE_VIDEO, 'video/x-ms-wmv', 'Windows Media Video'),
		'avi' => array(MEDIATYPE_VIDEO, 'video/avi', 'DivX 3 Video'),
		'mpg' => array(MEDIATYPE_VIDEO, 'video/mpeg', 'MPEG-2 Video'),
		'3gp' => array(MEDIATYPE_VIDEO, 'video/3gpp', '3GP Video (cellphones)'),
		'flv' => array(MEDIATYPE_VIDEO, 'video/x-flv', 'Flash Video'),
		'mp4' => array(MEDIATYPE_VIDEO, 'video/mp4', 'MPEG-4 Video'),

		'wma' => array(MEDIATYPE_AUDIO, 'audio/x-ms-wma', 'Windows Media Audio'),
		'mp3' => array(MEDIATYPE_AUDIO, 'audio/x-mpeg', 'MP3 Audio'),
		'ogg' => array(MEDIATYPE_AUDIO, 'application/x-ogg', 'OGG Audio'),

		'txt' => array(MEDIATYPE_DOCUMENT, 'text/plain', 'Text Document'),
		'doc' => array(MEDIATYPE_DOCUMENT, 'application/msword', 'Word Document'),
		'pdf' => array(MEDIATYPE_DOCUMENT, 'application/pdf', 'PDF Document'),

		'html'		=> array(MEDIATYPE_WEBRESOURCE,	'text/html', 'HTML Page'),
		'torrent'	=> array(MEDIATYPE_WEBRESOURCE,	'application/x-bittorrent', 'BitTorrent File')
	); ///<File extension to mimetype & media type mapping. WIP! not used yet. FIXME should replace the above mimetypestuff eventually

	/* User configurable settings */
	public $upload_dir = '/webupload/';				///< Default upload directory

	public $tmp_dir = '/tmp/';								///< temp directory

	public $thumb_default_width		= 80;				///< Default width of thumbnails
	public $thumb_default_height	= 80;				///< Default height of thumbnails

	private $image_max_width		= 900;			///< bigger images will be resized to this size
	private $image_max_height		= 800;

	public $anon_uploads			= false;		///< allow unregisterd users to upload files
	public $count_file_views		= false;		///< FIXME REMOVE! auto increments the "cnt" in tblFiles in each $files->sendFile() call
	public $apc_uploads				= false;		///< enable support for php_apc + php_uploadprogress calls
	public $image_convert			= true;			///< use imagemagick to handle exotic image formats

	public $process_callback		= false;		///< script to callback on process server completition (optional)

	public $allow_rating			= true;			///< allow file rating?

	public $default_video = 'video/x-flv';	///< FLV = default fileformat to convert video to
	public $default_audio = 'audio/x-mpeg';	///< MP3 = default fileformat to convert audio to

	/**
	 * Constructor. Initializes class configuration
	 *
	 * \param $config array of config options for the Files class
	 * \return nothing
	 */
	function __construct(array $config)
	{
		global $session;

		if (isset($config['upload_dir'])) $this->upload_dir = $config['upload_dir'];
		if (isset($config['tmp_dir'])) $this->tmp_dir = $config['tmp_dir'];

		if (isset($config['image_max_width'])) $this->image_max_width = $config['image_max_width'];
		if (isset($config['image_max_height'])) $this->image_max_height = $config['image_max_height'];
		if (isset($config['thumb_default_width'])) $this->thumb_default_width = $config['thumb_default_width'];
		if (isset($config['thumb_default_height'])) $this->thumb_default_height = $config['thumb_default_height'];

		if (isset($config['count_file_views'])) $this->count_file_views = $config['count_file_views'];
		if (isset($config['anon_uploads'])) $this->anon_uploads = $config['anon_uploads'];
		if (isset($config['apc_uploads'])) $this->apc_uploads = $config['apc_uploads'];
		if (isset($config['image_convert'])) $this->image_convert = $config['image_convert'];

		if (isset($config['allow_rating'])) $this->allow_rating = $config['allow_rating'];

		if (isset($config['process_callback'])) $this->process_callback = $config['process_callback'];
	}

	/**
	 * Returns files uploaded of the type specified
	 *
	 * \param $fileType filetype
	 * \param $ownerId optionally select by owner also
	 * \param $order optionally specify sort order
	 * \param $count optionally specify how many to return
	 * \return list of files
	 */
	function getFileList($fileType, $ownerId = 0, $order = 'ASC', $count = 0)
	{
		global $db;
		if (!is_numeric($fileType) || !is_numeric($ownerId) || !is_numeric($count) || ($order != 'ASC' && $order != 'DESC')) return false;

		$q = 'SELECT * FROM tblFiles WHERE fileType='.$fileType;
		if ($ownerId) $q .= ' AND ownerId='.$ownerId;
		$q .= ' ORDER BY timeUploaded '.$order.($count ? ' LIMIT '.$count : '');
		return $db->getArray($q);
	}

	/**
	 * Performs mimetype lookup using GNU file utility
	 *
	 * \param $filename name of file to check
	 */
	function lookupMimeType($filename)
	{
		if (!file_exists($filename)) return false;

		$c = 'file -bi '.escapeshellarg($filename);
		return exec($c);
	}

	/**
	 * Moves a file to a different file category
	 *
	 * \param $_category category to move to 
	 * \param $_id fileId to move
	 * \return true on success
	 */
	function moveFile($_category, $_id)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($_category) || !is_numeric($_id)) return false;

		$q = 'UPDATE tblFiles SET categoryId='.$_category.' WHERE fileId='.$_id;
		if (!$session->isAdmin) $q.= ' AND uploaderId='.$session->id;
		$db->update($q);

		return true;
	}

	/**
	 * Deletes a file from disk & database
	 *
	 * \param $_id fileId to delete
	 * \param $ownerId optionally specify owner of file
	 * \return true on success
	 */
	function deleteFile($_id, $ownerId = 0)
	{
		if (!is_numeric($_id) || !is_numeric($ownerId)) return false;

		if (!$this->deleteFileEntry($_id, $ownerId)) return false;

		//physically remove the file from disk
		unlink($this->findUploadPath($_id));
		$this->clearThumbs($_id);
		return true;
	}

	/**
	 * Deletes a file entry from database
	 *
	 * \param $_id fileId to delete
	 * \param $ownerId optionally specify owner of file
	 * \return true on success
	 */
	function deleteFileEntry($_id, $ownerId = 0)
	{
		global $db;
		if (!is_numeric($_id) || !is_numeric($ownerId)) return false;

		$q = 'DELETE FROM tblFiles WHERE fileId='.$_id;
		if ($ownerId) $q .= ' AND ownerId='.$ownerId;
		if ($db->delete($q)) return true;
		return false;
	}

	/**
	 * Deletes all file entries for specified owner
	 *
	 * \param $type type of file (0 for all)
	 * \param $ownerId user Id
	 * \return number of files deleted
	 */
	function deleteFileEntries($_type, $ownerId)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($ownerId)) return false;

		$q = 'DELETE FROM tblFiles WHERE ownerId='.$ownerId;
		if ($_type) $q .= ' AND fileType='.$_type;
		return $db->delete($q);
	}

	/**
	 * Deletes all thumbnails for this file ID
	 *
	 * \param $_id file id
	 * \return true on success
	 */
	function clearThumbs($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$thumbs_dir = dirname($this->findThumbPath($_id));

		$dir = scandir($thumbs_dir);
		foreach ($dir as $name)
		{
			if (strpos($name, $_id.'_') !== false) {
				unlink($thumbs_dir.'/'.$name);
			}
		}
		return true;
	}

	/**
	 * Stores uploaded file associated to $session->id
	 *
	 * \param $FileData array of php internal file data from file upload
	 * \param $fileType type of file
	 * \param $ownerId file owner
	 * \param $categoryId category where to store the file
	 * \return fileId of the newly imported file
	 */
	function handleUpload($FileData, $fileType, $ownerId = 0, $categoryId = 0)
	{
		global $db, $session, $auth;
		if ((!$session->id && !$this->anon_uploads) || !is_numeric($fileType) || !is_numeric($ownerId) || !is_numeric($categoryId)) return false;

		//ignore empty file uploads
		if (!$FileData['name']) return;

		if (!is_uploaded_file($FileData['tmp_name'])) {
			$session->error = t('Uploaded file is too big');
			$session->log('Attempt to upload too big file');
			return false;
		}

		$FileData['type'] = $this->lookupMimeType($FileData['tmp_name']);	//internal mimetype sucks!

		$fileId = $this->addFileEntry($fileType, $categoryId, $ownerId, $FileData['name']);

		//Identify and handle various types of files
		if (in_array($FileData['type'], $this->image_mime_types)) {
			$this->handleImageUpload($fileId, $FileData);
		} else if (in_array($FileData['type'], $this->video_mime_types)) {
			$this->handleVideoUpload($fileId, $FileData);
		} else if (in_array($FileData['type'], $this->audio_mime_types)) {
			//FIXME audio conversion with process server
			$this->moveUpload($FileData['tmp_name'], $fileId);
		} else {
			$this->moveUpload($FileData['tmp_name'], $fileId);
		}

		$this->updateFile($fileId);	//force update of filesize, mimetype & checksum

		if ($fileType == FILETYPE_USERFILE) {
			addToModerationQueue(MODERATION_FILE, $fileId);

			//notify subscribers
			$check = getCategoryPermissions(CATEGORY_USERFILE, $categoryId);

			$subs = getSubscribers(SUBSCRIPTION_FILES, $ownerId);
			foreach ($subs as $sub) {
				if (($check & CAT_PERM_PUBLIC) || (($check & CAT_PERM_PRIVATE) && isFriends($sub['ownerId'])) ) {
					$dst_adr = loadUserdataEmail($sub['ownerId']);
					$subj = t('Updated gallery');
					$msg = t('The user').' '.Users::getName($ownerId).' '.t('has uploaded files to their file area.');
					$auth->SmtpSend($dst_adr, $subj, $msg);
					systemMessage($sub['ownerId'], $subj, $msg);
				}			
			}
		}			
		if ($fileType == FILETYPE_USERDATA) {
			addToModerationQueue(MODERATION_PRES_IMAGE, $fileId);
		}
		
		return $fileId;
	}

	/**
	 * Adds a new entry for a new file in the database
	 *
	 * \param $fileType
	 * \param $categoryId
	 * \param $ownerId
	 * \param $fileName
	 * \param $content
	 * \return fileId from the database entry created, or false on failure
	 */
	function addFileEntry($fileType, $categoryId, $ownerId, $fileName, $content = '')
	{
		global $db, $session;
		if (!is_numeric($fileType) || !is_numeric($categoryId) || !is_numeric($ownerId)) return false;

		$fileSize = 0;
		$fileMime = '';
		$fileName = basename(strip_tags($fileName));

		if ($session) {
	  	$q = 'INSERT INTO tblFiles SET fileName="'.$db->escape($fileName).'",ownerId='.$ownerId.',uploaderId='.$session->id.',uploaderIP='.$session->ip.',timeUploaded=NOW(),fileType='.$fileType.',categoryId='.$categoryId;
		} else {
			$q = 'INSERT INTO tblFiles SET fileName="'.$db->escape($fileName).'",ownerId='.$ownerId.',uploaderId=0,uploaderIP=0,timeUploaded=NOW(),fileType='.$fileType.',categoryId='.$categoryId;
		}
		$newFileId = $db->insert($q);

		if ($content) {
			//echo 'addFileEntry(): Writing file to '.$this->upload_dir.$newFileId;
			file_put_contents($this->findUploadPath($newFileId), $content);
			clearstatcache();	//needed to get current filesize()
		}

		$this->updateFile($newFileId);

	  	return $newFileId;
	}

	/**
	 * Moves uploaded file to correct directory
	 */
	function moveUpload($tmp_name, $fileId)
	{
		global $session;

		//Move the uploaded file to upload directory
		$uploadfile = $this->findUploadPath($fileId);
		if (move_uploaded_file($tmp_name, $uploadfile)) {
			chmod($uploadfile, 0777);
			return true;
		}
		$session->log('Failed to move file from '.$tmp_name.' to '.$uploadfile);
		return false;
	}

	/**
	 * Finds out where to store the file in filesystem, creating directories when nessecary
	 */
	function findUploadPath($fileId, $mkdir = true, $base_dir = '')
	{
		$subdir = floor($fileId / 10000) * 10000;

		if (!$base_dir) $base_dir = 'org/';
		$dir = $this->upload_dir.$base_dir.$subdir;

		if ($mkdir && !is_dir($this->upload_dir.$base_dir)) {
			mkdir($this->upload_dir.$base_dir);
			chmod($this->upload_dir.$base_dir, 0777);
		}

		if ($mkdir && !is_dir($dir)) {
			mkdir($dir);
			chmod($dir, 0777);
		}

		return $dir.'/'.$fileId;
	}

	function findThumbPath($fileId)
	{
		return $this->findUploadPath($fileId, true, 'thumb/');
	}

	/**
	 * If server is configured for process server use,
	 * enqueue the video for conversion, otherwise just store it away
	 */
	function handleVideoUpload($fileId, $FileData)
	{
		global $db, $config;

		$this->moveUpload($FileData['tmp_name'], $fileId);

		if ($this->default_video) {
			if ($FileData['type'] != $this->default_video) {

				$uri = $config['app']['full_url'].$config['core']['web_root'].'api/file.php?id='.$fileId;
				$refId = process_client_fetchAndConvert($uri, $this->process_callback.'?id='.$fileId);
				if (!$refId) {
					echo 'Failed to add order!';
				} else {
					//echo 'Order added successfully. Order ID is '.$refId;
				}
			}
		}

		return true;
	}

	/**
	 * Handle image upload, used internally only
	 *
	 * \param $fileId file id to deal with
	 * \param $FileData array of php internal file data from file upload
	 */
	function handleImageUpload($fileId, $FileData)
	{
		global $db, $session;

		switch ($FileData['type']) {
			case 'image/bmp':	//IE 7, Firefox 2, Opera 9.2
				if (!$this->image_convert) break;
				$out_tempfile = $this->tmp_dir.'core_outfile.jpg';
				$check = convertImage($FileData['tmp_name'], $out_tempfile, 'image/jpeg');
				if (!$check) {
					$session->log('Failed to convert bmp to jpeg!');
					break;
				}

				unlink($FileData['tmp_name']);
				rename($out_tempfile, $FileData['tmp_name']);
				$filesize = filesize($FileData['tmp_name']);
				$q = 'UPDATE tblFiles SET fileMime="image/jpeg", fileName="'.$db->escape(basename(strip_tags($FileData['name']))).'.jpg",fileSize='.$filesize.' WHERE fileId='.$fileId;
				$db->query($q);
				break;
		}

		list($img_width, $img_height) = getimagesize($FileData['tmp_name']);

		//Resize the image if it is too big, overwrite the uploaded file
		if (($img_width > $this->image_max_width) || ($img_height > $this->image_max_height))
		{
			resizeImageExact($FileData['tmp_name'], $FileData['tmp_name'], $this->image_max_width, $this->image_max_height, $fileId);
		}

		$this->moveUpload($FileData['tmp_name'], $fileId);
		$this->makeThumbnail($fileId);
		return true;
	}

	/**
	 * Generates a thumbnail for given image file
	 */
	function makeThumbnail($fileId)
	{
		if (!is_numeric($fileId)) return false;

		//create default sized thumbnail
		$thumb_filename = $this->findThumbPath($fileId).'_'.$this->thumb_default_width.'x'.$this->thumb_default_height;
		resizeImageExact($this->findUploadPath($fileId), $thumb_filename, $this->thumb_default_width, $this->thumb_default_height);
	}

	/**
	 * Returns checksums for specified file.
	 * If checksums were already generated, it fetches them from tblChecksums
	 *
	 * \param $_id fileId to get checksums for
	 * \param $force if set to true the db cache of checksums is ignored
	 * \return checksums in array
	 */
	function checksums($_id, $force = false)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$data = $db->getOneRow('SELECT * FROM tblFiles WHERE fileId='.$_id);
		if (!$data) die;

		if (!$force) {
			$q = 'SELECT * FROM tblChecksums WHERE fileId='.$_id;
			$cached = $db->getOneRow($q);
			if ($cached) return $cached;
		}

		$filename = $this->findUploadPath($_id);

		if (!file_exists($filename)) {
			die('tried to generate checksums of nonexisting file '.$filename);
		}

		$exec_start = microtime(true);
		$new['sha1'] = $db->escape(hash_file('sha1', $filename));	//40-character hex string
		$new['md5'] = $db->escape(hash_file('md5', $filename));		//32-character hex string
		$new['timeCreated'] = now();
		$exec_time = microtime(true) - $exec_start;
		$new['timeExec'] = $exec_time;

		$q = 'DELETE FROM tblChecksums WHERE fileId='.$_id;
		$db->delete($q);

		$q = 'INSERT INTO tblChecksums SET fileId='.$_id.', sha1="'.$new['sha1'].'", md5="'.$new['md5'].'", timeExec="'.$new['timeExec'].'", timeCreated=NOW()';
		$db->insert($q);

		return $new;
	}

	/**
	 * Returns sha1 checksum of file $_id. forces checksum generation if missing
	 *
	 * \param $_id fileId
	 * \return sha1-sum
	 */
	function sha1($_id)
	{
		$sums = $this->checksums($_id);
		return $sums['sha1'];
	}

	/**
	 * Used for file processing. generates a new file entry referencing to entry $_id. returns new id
	 *
	 * \param $_id fileId
	 * \param $_clone_type filetype of clone
	 * \return fileId of the clone
	 */
	function cloneFile($_id, $_clone_type)
	{
		global $db;
		if (!is_numeric($_id) || !is_numeric($_clone_type)) return false;

		$file = $this->getFileInfo($_id);
		if (!$file) return false;

		$q = 'INSERT INTO tblFiles SET ownerId='.$_id.',fileType='.$_clone_type.',uploaderId='.$file['uploaderId'].',timeUploaded=NOW()';
		return $db->insert($q);
	}

	/**
	 * Forces recalculation of filesize, mimetype and checksums
	 *
	 * \param $_id
	 * \return numeric value on true, else 0 or false
	 */
	function updateFile($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$filename = $this->findUploadPath($_id, false);
		if (!file_exists($filename)) return false;

		$size = filesize($filename);

		if (!is_numeric($size) || !$size) {
			echo "updateFile(): file ".$filename." dont exist\n";
			return false;
		}
		$mime = $this->lookupMimeType($filename);

		//force calculation of checksums
		$this->checksums($_id, true);

		//parse result such as: text/plain; charset=us-ascii
		$arr = explode(';', $mime);
		$mime = $arr[0];

		$q = 'UPDATE tblFiles SET fileMime="'.$db->escape($mime).'",fileSize='.$size.' WHERE fileId='.$_id;
		return $db->update($q);
	}

	/**
	 * These headers allows the browser to cache the output for 30 days. Works with MSIE6 and Firefox 1.5
	 */
	function setCachedHeaders()
	{
		header('Expires: ' . date("D, j M Y H:i:s", time() + (86400 * 30)) . ' UTC');
		header('Cache-Control: Public');
		header('Pragma: Public');
	}

	/**
	 * Force browser to not cache content
	 */
	function setNoCacheHeaders()
	{
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	}

	function imageResize($_id, $_pct)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($_id) || !is_numeric($_pct)) return false;

		$data = $db->getOneRow('SELECT * FROM tblFiles WHERE fileId='.$_id);
		if (!$data) die;

		if (!in_array($data['fileMime'], $this->image_mime_types)) return false;

		header('Content-Type: '.$data['fileMime']);
		header('Content-Disposition: inline; filename="'.basename($data['fileName']).'"');
		header('Content-Transfer-Encoding: binary');

		$filename = $this->findUploadPath($_id);

		resizeImage($filename, $filename, $_pct);
		$this->setNoCacheHeaders();
		$this->sendImage($_id);

		$this->clearThumbs($_id);
		$this->makeThumbnail($_id);
	}

	function imageCrop($_id, $x1, $y1, $x2, $y2)
	{
		global $session;
		if (!$session->id || !is_numeric($_id) || !is_numeric($x1) || !is_numeric($y1) || !is_numeric($x2) || !is_numeric($y2)) return false;

		$filename = $this->findUploadPath($_id);
		cropImage($filename, $filename, $x1, $y1, $x2, $y2);
		$this->setNoCacheHeaders();
		$this->sendImage($_id);

		$this->clearThumbs($_id);
		$this->makeThumbnail($_id);
	}

	/**
	 * Performs an image rotation and then pass on the result to the user
	 *
	 * \param $_id fileId
	 * \param $_angle how much to rotate the image
	 */
	function imageRotate($_id, $_angle)
	{
		global $db, $session;
		if (!$session->id || !is_numeric($_id) || !is_numeric($_angle)) return false;

		$data = $db->getOneRow('SELECT * FROM tblFiles WHERE fileId='.$_id);
		if (!$data) die;

		if (!in_array($data['fileMime'], $this->image_mime_types)) return false;

		header('Content-Type: '.$data['fileMime']);
		header('Content-Disposition: inline; filename="'.basename($data['fileName']).'"');
		header('Content-Transfer-Encoding: binary');

		$filename = $this->findUploadPath($_id);

		rotateImage($filename, $filename, $_angle);
		$this->setNoCacheHeaders();
		$this->sendImage($_id);

		$this->clearThumbs($_id);
		$this->makeThumbnail($_id);
	}

	/**
	 * Takes get parameter 'dl' to send the file as an attachment
	 *
	 * \param $_id fileId
	 * \param $force_mime
	 */
	function sendFile($_id, $force_mime = false)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$data = $db->getOneRow('SELECT * FROM tblFiles WHERE fileId='.$_id);
		if (!$data) die;

		if (isset($_GET['dl'])) {
			/* Prompts the user to save the file */
			header('Content-Disposition: attachment; filename="'.basename($data['fileName']).'"');
		} else {
			/* Displays the file in the browser, and assigns a filename for the browser's "save as..." features */
			header('Content-Disposition: inline; filename="'.basename($data['fileName']).'"');
		}

		header('Content-Transfer-Encoding: binary');

			//Serves the file differently depending on what kind of file it is
		if (in_array($data['fileMime'], $this->image_mime_types)) {
			//Generate resized image if needed
			$this->sendImage($_id);
		} else {
			$this->setCachedHeaders();

			/* This sends files without extension etc as plain text if you didnt specify to download them */
			if ((!$force_mime && !isset($_GET['dl']) || $data['fileMime'] == 'application/octet-stream')) {
				header('Content-Type: text/plain');
			} else {
				header('Content-Type: '.$data['fileMime']);
			}

			//Just delivers the file as-is
			header('Content-Length: '. $data['fileSize']);

			readfile($this->findUploadPath($_id));
		}

		//Count the file downloads
		if ($this->count_file_views) {
			$db->query('UPDATE tblFiles SET cnt=cnt+1 WHERE fileId='.$_id);
		}

		die;
	}

	/**
	 * Sends text file to user
	 *
	 * \param $filename name of file to send
	 */
	function sendTextfile($filename)
	{
		//required for IE6:
		header('Cache-Control: cache, must-revalidate');

		//header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($realFileName)) . ' GMT');
		header('Content-Length: '.filesize($filename));
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename="'.basename($filename).'"');

		readfile($filename);
	}

	/**
	 * Send image to user
	 *
	 * Optional parametera:
	 * $_GET['w'] width
	 * $_GET['h'] height
	 *
	 * \param $_id fileId
	 */
	function sendImage($_id)
	{
		global $session;

		$filename = $this->findUploadPath($_id);
		if (!file_exists($filename)) die('file not found');

		$temp = getimagesize($filename);

		$img_width = $temp[0];
		$img_height = $temp[1];
		$mime_type = $temp['mime'];

		$width = 0;
		$log = true;
		if (!empty($_GET['w']) && is_numeric($_GET['w'])) {
			$log = false;
			$width = $_GET['w'];
		}
		if (($width < 10) || ($width > 1500)) $width = 0;

		$height = 0;
		if (!empty($_GET['h']) && is_numeric($_GET['h'])) {
			$log = false;
			$height = $_GET['h'];
		}
		if (($height < 10) || ($height > 1500)) $height = 0;

		if ($width && (($width < $img_width) || ($height < $img_height)) )  {
			/* Look for cached thumbnail */

			$out_filename = $this->findThumbPath($_id).'_'.$width.'x'.$height;

			if (!file_exists($out_filename)) {
				//Thumbnail of this size dont exist, create one
				resizeImageExact($filename, $out_filename, $width, $height);
			}
		} else {
			$out_filename = $filename;
		}

		if (filemtime($out_filename) < $session->started) {
			$this->setCachedHeaders();
		} else {
			$this->setNoCacheHeaders();
		}
		header('Content-Type: '.$mime_type);
		header('Content-Length: '.filesize($out_filename));

		readfile($out_filename);

		if ($log) {
			logVisit(VISIT_FILE, $_id);
		}
	}

	/**
	 * Selects all files for specified type & owner
	 *
	 * \param $fileType type of files
	 * \param $ownerId owner of the files
	 * \param $categoryId category of the files
	 * \param $_limit optional limit the result
	 * \param $_order optional return order ASC or DESC (timeUploaded ASC default)
	 */
	function getFiles($fileType, $ownerId = 0, $categoryId = 0, $_limit = 0, $_order = 'ASC')
	{
		global $db, $session;
		if (!is_numeric($fileType) || !is_numeric($ownerId) || !is_numeric($categoryId) || !is_numeric($_limit)) return array();
		if ($_order != 'ASC' && $_order != 'DESC') return false;

		if ($fileType == FILETYPE_CLONE_VIDEOTHUMB10) {
			$q  = 'SELECT * FROM tblFiles';
			$q .= ' WHERE fileType='.$fileType;
			if ($ownerId) $q .= ' AND ownerId='.$ownerId;
			$q .= ' ORDER BY timeUploaded '.$_order;

		} else if ($session->id && $fileType == FILETYPE_FORUM) {
			$q  = 'SELECT * FROM tblFiles';
			$q .= ' WHERE fileType='.$fileType.' AND uploaderId='.$session->id;
			if ($ownerId) $q .= ' AND ownerId='.$ownerId;
			$q .= ' ORDER BY timeUploaded '.$_order;

		} else {
			$q  = 'SELECT t1.*,t2.userName AS uploaderName FROM tblFiles AS t1 ';
			$q .= ' LEFT JOIN tblUsers AS t2 ON (t1.uploaderId=t2.userId)';
			$q .= ' WHERE t1.categoryId='.$categoryId;
			if ($ownerId) $q .= ' AND t1.ownerId='.$ownerId;
			$q .= ' AND t1.fileType='.$fileType;
			$q .= ' ORDER BY t1.timeUploaded '.$_order;
		}

		if ($_limit) $q .= ' LIMIT 0,'.$_limit;

		return $db->getArray($q);
	}

	/**
	 * Get file count
	 *
	 * \param $fileType type of files
	 * \param $ownerId owner of the files (optional)
	 * \param $categoryId category of the files (optional)
	 */
	function getFileCount($fileType, $ownerId = 0, $categoryId = 0)
	{
		global $db;
		if (!is_numeric($fileType) || !is_numeric($ownerId) || !is_numeric($categoryId)) return 0;

		$q = 'SELECT COUNT(fileId) FROM tblFiles WHERE fileType='.$fileType;
		if ($categoryId) $q .= ' AND categoryId='.$categoryId;
		if ($ownerId) $q .= ' AND ownerId='.$ownerId;
		return $db->getOneItem($q, true);
	}

	/**
	 * Retrieves detailed info about the specified file
	 *
	 * \param $_id fileId
	 */
	function getFileInfo($_id)
	{
		global $db;
		if (!is_numeric($_id) || !$_id) return false;

		$q = 'SELECT t1.*,t2.userName AS uploaderName FROM tblFiles AS t1 '.
					'LEFT JOIN tblUsers AS t2 ON (t1.uploaderId=t2.userId) '.
					'WHERE t1.fileId='.$_id;

		return $db->getOneRow($q);
	}

	/**
	 * Retrieves info about the specified file
	 *
	 * \param $_id fileId
	 */
	function getFile($_id)
	{
		global $db;
		if (!is_numeric($_id) || !$_id) return false;

		$q = 'SELECT * FROM tblFiles WHERE fileId='.$_id;
		return $db->getOneRow($q);
	}

	/**
	 * Shows attachments. used to show files attached to a forum post
	 *
	 * \param $_type type of file
	 * \param $_owner owner of the files
	 */
	function showAttachments($_type, $_owner)
	{
		global $config;

		$list = $this->getFiles($_type, $_owner);

		if (count($list)) {
			echo '<hr/>';
			echo 'Attached files:<br/>';
			foreach ($list as $row) {
				$show_text = $row['fileName'].' ('.formatDataSize($row['fileSize']).')';
				echo '<a href="'.$config['core']['web_root'].'api/file_pt.php?id='.$row['fileId'].getProjectPath().'" target="_blank">';
				if (in_array($row['fileMime'], $this->image_mime_types)) {
					echo makeThumbLink($row['fileId'], $show_text).'</a> ';
				} else {
					echo $show_text.'</a><br/>';
				}
			}
		}
	}

	/**
	 * Returns user who uploaded specified file
	 *
	 * \param $_id fileId
	 */
	function getUploader($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT uploaderId FROM tblFiles WHERE fileId='.$_id;
		return $db->getOneItem($q);
	}

	/**
	 * Returns owner of specified file
	 *
	 * \param $_id fileId
	 */
	function getOwner($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT ownerId FROM tblFiles WHERE fileId='.$_id;
		return $db->getOneItem($q);
	}

}
?>
