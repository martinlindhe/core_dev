<?php
/**
 * $Id$
 *
 * Requires zend framework installed and php.ini paths configured!!!
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

/**
 * Uploads video to youtube
 */
function youtubeUpload($username, $password, $devkey, $filename, $filetype, $movie_title, $movie_desc, $keywords, $category_name, $coords = '')
{
	require_once('Zend/Loader.php'); // the Zend dir must be in your include_path
	Zend_Loader::loadClass('Zend_Gdata_YouTube');
	$yt = new Zend_Gdata_YouTube();
	Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

	$authenticationURL = 'https://www.google.com/youtube/accounts/ClientLogin';
	$httpClient = Zend_Gdata_ClientLogin::getHttpClient(
		$username,
		$password,
		$service = 'youtube',
		$client = null,
		$source = 'core_dev', // a short string identifying your application
		$loginToken = null,
		$loginCaptcha = null,
		$authenticationURL);

	$httpClient->setHeaders('X-GData-Key', "key=${devkey}");
	$yt = new Zend_Gdata_YouTube($httpClient);

	// create a new Zend_Gdata_YouTube_VideoEntry object
	$myVideoEntry = new Zend_Gdata_YouTube_VideoEntry();

	// create a new Zend_Gdata_App_MediaFileSource object
	$filesource = $yt->newMediaFileSource($filename);
	$filesource->setContentType($filetype);
	// set slug header
	$filesource->setSlug($filename);	//FIXME: vafan är en slug header???

	// add the filesource to the video entry
	$myVideoEntry->setMediaSource($filesource);

	// create a new Zend_Gdata_YouTube_Extension_MediaGroup object
	$mediaGroup = $yt->newMediaGroup();
	$mediaGroup->title = $yt->newMediaTitle()->setText($movie_title);
	$mediaGroup->description = $yt->newMediaDescription()->setText($movie_desc);

	// the category must be a valid YouTube category
	// optionally set some developer tags (see Searching by Developer Tags for more details)
	$mediaGroup->category = array(
		$yt->newMediaCategory()->setText($category_name)->setScheme('http://gdata.youtube.com/schemas/2007/categories.cat')
		//$yt->newMediaCategory()->setText('mydevelopertag')->setScheme('http://gdata.youtube.com/schemas/2007/developertags.cat'),
		//$yt->newMediaCategory()->setText('anotherdevelopertag')->setScheme('http://gdata.youtube.com/schemas/2007/developertags.cat')
	);

	// set keywords, please note that they cannot contain white-space
	$mediaGroup->keywords = $yt->newMediaKeywords()->setText($keywords);
	$myVideoEntry->mediaGroup = $mediaGroup;

	if ($coords) {
		// set video location
		$yt->registerPackage('Zend_Gdata_Geo');
		$yt->registerPackage('Zend_Gdata_Geo_Extension');
		$where = $yt->newGeoRssWhere();
		$position = $yt->newGmlPos($coords);
		$where->point = $yt->newGmlPoint($position);
		$myVideoEntry->setWhere($where);
	}

	// upload URL for the currently authenticated user
	$uploadUrl = 'http://uploads.gdata.youtube.com/feeds/users/default/uploads';

	try {
		$newEntry = $yt->insertEntry($myVideoEntry, $uploadUrl, 'Zend_Gdata_YouTube_VideoEntry');
	} catch (Zend_Gdata_App_Exception $e) {
		echo $e->getMessage()."\n";
	}
}

?>
