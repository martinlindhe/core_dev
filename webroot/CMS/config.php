<?
	/* SITE CONFIGURATION START */

	$config['system_delete'] = 'del /q';			//system delete command, used to clear a whole directory, for linux would need 'rm --recursive --force'

	
	//GRANT SELECT,INSERT,UPDATE,DELETE ON dbCMS.* TO 'cms_restricted'@'localhost' IDENTIFIED BY 'pwdCMS'
	$config['database_1']['server']   = 'localhost';
	$config['database_1']['port']     = 3306;
	$config['database_1']['username'] = 'root';
	$config['database_1']['password'] = '';
	$config['database_1']['database'] = 'dbCommunity2';

	//Database settings for GeoIP database
	$config['database_geoip'] = $config['database_1'];
	$config['database_geoip']['database'] = 'dbGeoIP';

	$config['upload_dir'] = 'D:/webroot_upload/';
	$config['thumbs_dir'] = 'D:/webroot_upload/thumbs/';
	$config['path_functions'] = 'D:/webroot/site_functions/';

	//$config['phpmyadmin'] = '';
	
	$config['debug'] = true;

	$config['url_rewrite_length'] = 55;		//max length of visible url's after rewrite to hyperlinks

	$config['thumbnail_width']	= 120;		//pixel width on thumbnails
	$config['thumbnail_height']	= 110;	

	$config['image_max_width']	= 1024;		//bigger images will be resized to this size
	$config['image_max_height']	= 768;

	$config['login_sha1_key'] = 'sitecode_AB';	//used to encode passwords in database, to make brute forcing them more difficult
	$config['session_name'] = 'AIsessID';	//name of session-id cookie
	$config['session_timeout'] = 3600*3;
	
	$config['start_page'] = '/CMS/user.php';

	$config['site_admin'] = 'martin.lindhe@agentinteractive.se';



	
	$config['avatars'] = Array(
		'',	//must be empty
		'avatar_0000_takeoff.jpg',
		'avatar_0001_city.jpg',
		'avatar_0002_pinkness.jpg',
		'avatar_0003_moon.jpg',
		'avatar_0004_biker.jpg',
		'avatar_0005_dog.jpg',
		'avatar_0006_ball.jpg',
		'avatar_0007_what.jpg',
		'avatar_0008_wreck.jpg',
		'avatar_0009_flower.jpg',
		'avatar_0010_star.jpg',
		'avatar_0011_frog.jpg',
		'avatar_0012_telephone.jpg',
		'avatar_0013_frogman.jpg',
		'avatar_0014_dude.jpg',
		'avatar_0015_needles.jpg',
		'avatar_0016_flight.jpg',
		'avatar_0017_click.jpg',
		'avatar_0018_beach.jpg',
		'avatar_0019_palm.jpg',
		'avatar_0020_green.jpg',
		'avatar_0021_drive.jpg',
		'avatar_0022_stop.jpg',
		'avatar_0023_shoot.jpg',
		'avatar_0024_snowb.jpg'
	);



	$config['language'] = 'no';

	/* SITE CONFIGURATION END */
?>