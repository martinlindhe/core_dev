<?
	error_reporting(E_ALL);

	$config['image_width']				= 1024;

	//storlek på thumbnails i referensobjekten:
	$config['thumb_width']				= 181;
	$config['thumb_height']				= 181;

	$config['max_images_per_object']	= 1;

	$config['max_objects'] = 45;		//max antal objekt i textfilerna för alexander & co

	$config['cache_expire_time']	= (3600*24)*120;		//keep cached images for 120 days
	$config['server_cache_path']	= '../sfd_cache/';	//how sfd_alex1.php sees the cache directory
	$config['client_cache_path']	= 'sfd_cache/';			//how the client sees the cache directory
?>