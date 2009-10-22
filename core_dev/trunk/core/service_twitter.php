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

require_once('input_feed.php');
require_once('client_http.php');

class Twitter
{
	private $username, $password;

	function __construct($username = '', $password = '')
	{
		$this->username = $username;
		$this->password = $password;
	}

	function setUsername($username) { $this->username = $username; }
	function setPassword($password) { $this->password = $password; }

	function command($url_part, $params = array())
	{
		$h = new http('http://twitter.com/'.$url_part);

		$h->setUsername($this->username);
		$h->setPassword($this->password);

		return $h->post($params);
	}

	function getTimeline($user = '')
	{
		if (!$user) $user = $this->username;
		$c = 'statuses/user_timeline.rss?screen_name='.$user; //&count=30

		$data = $this->command($c);

		$feed = new input_feed();

		return $feed->parse($data);
	}

	function getFriendsTimeline()
	{
		$c = 'statuses/friends_timeline.rss'; //&count=30

		$data = $this->command($c);
		$feed = new input_feed();

		return $feed->parse($data);
	}

	/**
	 * Posts a message to your twitter feed
	 */
	function post($msg)
	{
		$c = 'statuses/update.xml';
		$arr['status'] = $msg;
		$data = $this->command($c, $arr, true);

		return true;
	}

	function test()
	{
		$c = 'help/test.xml';
		$data = $this->command($c);

		if ($data != '<ok>true</ok>') return false;
		return true;
	}
}

?>
