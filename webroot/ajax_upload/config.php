<?
	/* SITE CONFIGURATION START */
	include_once($config['path_functions'].'functions_files.php');
	
	$config['debug'] = true;
	$config['database_1']['server']   = 'localhost';
	$config['database_1']['port']     = 3306;
	$config['database_1']['username'] = 'root';
	$config['database_1']['password'] = '';
	$config['database_1']['database'] = 'dbAJAXUpload';
	
	//Database settings for GeoIP database
	$config['database_geoip'] = $config['database_1'];
	$config['database_geoip']['database'] = 'dbGeoIP';

	$config['path_functions'] = '../site_functions/';

	$config['login_sha1_key'] = 'upload_8xiixi6dujdjhzgg2';	//used to encode passwords in database, to make brute forcing them more difficult
	$config['session_name'] = 'uploadSessID';	//name of session-id cookie
	$config['session_timeout'] = 3600*4;	//4h session timeout

	$config['start_page'] = '/ajax_upload/index.php';

	$config['site_admin'] = 'martin.lindhe@agentinteractive.se';

	$config['language'] = 'en';


	
	//file settings
	$config['upload_dir'] = 'files/';

	$config['allowed_audio_extensions'] = array('.mp3');
	$config['allowed_image_extensions'] = array('.jpg', '.png', '.gif');
	$config['allowed_image_mimetypes'] = array('image/jpeg', 'image/png');


	/* SITE CONFIGURATION END */
?>