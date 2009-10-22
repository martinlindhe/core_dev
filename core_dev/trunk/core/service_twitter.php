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

	function command($url, $params = array())
	{
		$h = new http($url);

		$h->setUsername($this->username);
		$h->setPassword($this->password);

		return $h->post($params);
	}

	function getTimeline($user = '')
	{
		if (!$user) $user = $this->username;
		$c = 'http://twitter.com/statuses/user_timeline.atom?screen_name='.$user; //&count=30

		$data = $this->command($c);

		$feed = new input_feed();

		return $feed->parse($data);
	}

	function getFriendsTimeline()
	{
		$c = 'http://twitter.com/statuses/friends_timeline.atom'; //&count=30

		$data = $this->command($c);
		$feed = new input_feed();

		return $feed->parse($data);
	}

	function getSearchResult($s)
	{
		$c = 'http://search.twitter.com/search.atom?q='.urlencode($s);
		$data = $this->command($c);

		$feed = new input_feed();

		return $feed->parse($data);
	}

	/**
	 * Posts a message to your twitter feed
	 */
	function post($msg)
	{
		$c = 'http://twitter.com/statuses/update.xml';
		$arr['status'] = $msg;
		$data = $this->command($c, $arr, true);

		return true;
	}

	function test()
	{
		$c = 'http://twitter.com/help/test.xml';
		$data = $this->command($c);

		if ($data != '<ok>true</ok>') return false;
		return true;
	}
}

?>
