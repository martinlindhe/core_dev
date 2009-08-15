<?php
/**
 * $Id$
 *
 * Service API for twitter.com
 *
 * Documentation:
 * http://apiwiki.twitter.com/Twitter-API-Documentation
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//TODO: fetch user's twitter feed: curl -u username:password http://twitter.com/statuses/friends_timeline.xml


//XXX: from http://apiwiki.twitter.com/Authentication :
// OAuth is the Twitter preferred method of authentication moving forward.
// While we have no plans in the near term to require OAuth, new applications
// should consider it best practice to develop for OAuth.
// We eventually would like to suspend Basic Auth support.

require_once('input_feed.php');
require_once('input_http.php');

class twitter
{
	var $username, $password;

	var $api_url = 'http://twitter.com/';


	function __construct($username = '', $password = '')
	{
		$this->username = $username;
		$this->password = $password;
	}

	function command($url_part, $post_params = array(), $auth = false)
	{
		$url = $this->api_url.$url_part;

		$cache = new cache();	//XXX cache should be in url_handler
		$key = 'url://'.$url;

		//cache if command dont submit data
		if (!$post_params) {
			$data = $cache->get($key);
			if ($data)
				return $data;
		}

		$ch = curl_init($url);

		//curl_setopt($ch, CURLOPT_USERAGENT, "core_dev twitter module"); //XXX 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		if ($auth) {
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);//XXX add http auth to url_handler and use that instead
			curl_setopt($ch, CURLOPT_USERPWD, $this->username.':'.$this->password);
		}

		if ($post_params) { //XXX add HTTP POST to url_handler and use that
			$var = '';
			foreach($post_params as $key => $val)
				$var .= '&'. $key .'='. urlencode($val);

			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, trim($var, '&'));
		}

		$res = curl_exec($ch);
		//$headers = curl_getinfo($ch);

		curl_close($ch);

		$cache->set($key, $res, 60);
		return $res;
	}

	function getTimeline($user = '')
	{
		if (!$user) $user = $this->username;
		$c = 'statuses/user_timeline.rss?screen_name='.$user.'&count=20';
		
		$data = $this->command($c);

		$feed = new input_feed();
		$res = $feed->parse($data);

		return $res;
	}

	/**
	 * Posts a message to your twitter feed
	 */
	function post($msg)
	{
		$c = 'statuses/update.xml';
		$arr['status'] = $msg;
		$data = $this->command($c, $arr, true);
		
		print_r($data);
		
	}

	function test()
	{
		$d = $this->command('help/test.xml');

		if ($d != '<ok>true</ok>') return false;
		return true;
	}

}


?>
