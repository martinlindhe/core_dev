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

require_once('io_newsfeed.php');
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

	/**
	 * Executes a Twitter API function
	 */
	private function exec($url, $params = array())
	{
		$h = new HttpClient($url);

		$h->setUsername($this->username);
		$h->setPassword($this->password);

		return $h->post($params);
	}

	function getTimeline($user = '')
	{
		if (!$user) $user = $this->username;
		$c = 'http://twitter.com/statuses/user_timeline.atom?screen_name='.$user; //&count=30

		$data = $this->exec($c);

		$feed = new NewsFeed();

		$feed->load($data);
		return $feed->getItems();
	}

	function getFriendsTimeline()
	{
		$c = 'http://twitter.com/statuses/friends_timeline.atom'; //&count=30

		$data = $this->exec($c);
		$feed = new NewsFeed();

		$feed->load($data);
		return $feed->getItems();
	}

	function getSearchResult($s)
	{
		$c = 'http://search.twitter.com/search.atom?q='.urlencode($s);
		$data = $this->exec($c);

		$feed = new NewsFeed();

		$feed->load($data);
		return $feed->getItems();
	}

	/**
	 * Posts a message to your twitter feed
	 */
	function post($msg)
	{
		$c = 'http://twitter.com/statuses/update.xml';
		$arr['status'] = $msg;
		$data = $this->exec($c, $arr, true);

		return true;
	}

	function test()
	{
		$c = 'http://twitter.com/help/test.xml';
		$data = $this->exec($c);

		if ($data != '<ok>true</ok>') return false;
		return true;
	}
}

?>
