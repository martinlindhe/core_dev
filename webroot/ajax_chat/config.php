<?
	/* SITE CONFIGURATION START */
	
	//GRANT SELECT,INSERT,UPDATE,DELETE ON dbCMS.* TO 'cms_restricted'@'localhost' IDENTIFIED BY 'pwdCMS'
	$config['database_1']['server']   = 'localhost';
	$config['database_1']['port']     = 3306;
	$config['database_1']['username'] = 'root';
	$config['database_1']['password'] = '';
	$config['database_1']['database'] = 'dbMMO';

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

	$config['login_sha1_key'] = 'sitecode_MMO';	//used to encode passwords in database, to make brute forcing them more difficult
	$config['session_name'] = 'MMOsessID';	//name of session-id cookie
	$config['session_timeout'] = 3600*3;
	
	$config['start_page'] = '/mmo/';

	$config['site_admin'] = 'martin.lindhe@agentinteractive.se';

	$config['language'] = 'en';
	
	
	//chat config options
	$config['chat']['max_text_length']	= 100;	//max number of characters allowed to input in a chat line
	$config['chat']['buffer_lines']			= 15;		//number of lines to read into the chat buffer for a chat channel
	$config['chat']['idle_timeout']			=	3;		//idle timeout, in seconds, too low value will cause lots of join/left spam in channel (10 or more recommended)

	/* SITE CONFIGURATION END */
?>