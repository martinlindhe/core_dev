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


//TODO: post news into twitter feed: curl -u username:password -d status="your message here" http://twitter.com/statuses/update.json

//TODO: fetch user's twitter feed: curl -u username:password http://twitter.com/statuses/friends_timeline.xml

//TODO: authenticate user


//XXX: from http://apiwiki.twitter.com/Authentication :
// OAuth is the Twitter preferred method of authentication moving forward.
// While we have no plans in the near term to require OAuth, new applications
// should consider it best practice to develop for OAuth.
// We eventually would like to suspend Basic Auth support.


class twitter
{
	var $username, $password;

	var $api_url = 'http://twitter.com/';


	function __construct()
	{
	}

	/**
	 * Posts a message to your twitter feed
	 */
	function update($msg)
	{
	}

	function test()
	{
		$d = file_get_contents($this->api_url.'help/test.xml');

		if ($d != '<ok>true</ok>') return false;
		return true;
	}

}



$t = new twitter();
if (!$t->test()) echo 'FAIL 1';

//$t->update('helloouu');



?>
