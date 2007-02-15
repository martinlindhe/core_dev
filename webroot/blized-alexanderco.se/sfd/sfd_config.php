	<?
	error_reporting(E_ALL);

	//$config['image_width']				= 1280;
	$config['image_width']				= 1024;

	$config['cache_expire_time']	= (3600*24)*120;				//keep cached images for 120 days
	$config['server_cache_path']	= '../sfd_cache/';	//how sfd_alex1.php sees the cache directory
	$config['client_cache_path']	= 'sfd_cache/';		//how the client sees the cache directory
?>