<?
	/* SITE CONFIGURATION START */

	/* Include used functions files */
	include_once($config['path_functions'].'functions_db.php');
	include_once($config['path_functions'].'functions_log.php');
	include_once($config['path_functions'].'functions_user.php');
	include_once($config['path_functions'].'functions_session.php');

	include_once($config['path_functions'].'functions_comments.php');
	include_once($config['path_functions'].'functions_time.php');
	include_once($config['path_functions'].'functions_misc.php');
	include_once($config['path_functions'].'functions_tracker.php');
	include_once($config['path_functions'].'functions_geoip.php');
	include_once($config['path_functions'].'functions_dns_cache.php');
	include_once($config['path_functions'].'functions_whois.php');
	include_once($config['path_functions'].'functions_referrer.php');
	include_once($config['path_functions'].'functions_browserstats.php');
	include_once($config['path_functions'].'functions_subscriptions.php');
	include_once($config['path_functions'].'functions_settings.php');
	include_once($config['path_functions'].'functions_webtrends.php');
	include_once($config['path_functions'].'functions_google_pagerank.php');
	include_once($config['path_functions'].'class.phpmailer.php');
	
	$config['debug'] = true;
	$config['database_1']['server']   = 'localhost';
	$config['database_1']['port']     = 3306;
	$config['database_1']['username'] = 'root';
	$config['database_1']['password'] = '';
	$config['database_1']['database'] = 'dbTracker';
	$db = dbOpen($config['database_1']);
	
	//Database settings for GeoIP database
	$config['database_geoip'] = $config['database_1'];
	$config['database_geoip']['database'] = 'dbGeoIP';
	$geodb = dbOpen($config['database_geoip']);

	$config['path_functions'] = '../site_functions/';

	$config['login_sha1_key'] = 'sitecode_AB';	//used to encode passwords in database, to make brute forcing them more difficult
	$config['session_name'] = 'trackerSessID';	//name of session-id cookie
	$config['session_timeout'] = 3600*4;	//4h session timeout

	$config['start_page'] = '/tracker/index.php';

	$config['language'] = 'en';

	//tracker specific config:
	$config['dns_cache_expiration'] = 3600*24;	//24 hours expiration time of dns cache entries
	$config['dns_cache_autolookup'] = false;		//does not refresh DNS cache when manually using the site (script automatically updates it)
	
	$config['lowercase_queries']		= true;			//konverterar automatiskt alla sök-queries till lowercase
	$config['unique_ip_per_page']	= 35;					//max antal unika ip'n som ska visas per sida


	$config['smtp']['host'] = 'mailb1.surf-town.net';
	$config['smtp']['username'] = 'martin.lindhe@agentinteractive.se';
	$config['smtp']['password'] = 'martin789';
	$config['smtp']['sender']		= 'noreply@agentinteractive.se';
	$config['smtp']['mail_footer'] = 'D:/webroot/tracker/design/ai_logo.png';

	$config['google']['pr_cache_expiration'] = 3600*48;	//hold PR lookups for 48 hours


	$config['language'] = 'sv';
	include_once($config['path_functions'].'locales_time.php');	
	$config['time'] = $config['time'][ $config['language'] ];
	
	/* SITE CONFIGURATION END */
?>