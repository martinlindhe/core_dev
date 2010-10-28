<?php
/**
 * $Id$
 *
 * Service API for twitter.com
 *
 * Documentation:
 * http://apiwiki.twitter.com/Twitter-API-Documentation
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

//STATUS: WIP

//TODO: implement Twitter OAuth authentication so TwitterClient can create new tweets and also so core_dev apps can allow Twitter users to login to the site,
//      see http://apiwiki.twitter.com/OAuth-Examples

require_once('AtomReader.php');
require_once('HttpClient.php');

require_once('Cache.php');

class TwitterClient
{
    /**
     * Executes a Twitter API function
     */
    private function exec($url, $params = array())
    {
        if (!$params) {
            $cache = new Cache();
            $cache->setCacheTime(60*5); //5 min

            $key = 'twitter//'.sha1( $url );

            $full = $cache->get($key);
            if ($full)
                return $full;
        }

        $http = new HttpClient($url);

        $res = $http->post($params);

        if (!$params)
            $cache->set($key, $res);

        return $res;
    }

    function getTimeline($user = '')
    {
        $c = 'http://twitter.com/statuses/user_timeline.atom?screen_name='.urlencode($user);

        $data = $this->exec($c);

        $feed = new AtomReader();
        $feed->parse($data);

        return $feed->getItems();
    }

    function getSearchResult($s)
    {
        $c = 'http://search.twitter.com/search.atom?q='.urlencode($s);

        $data = $this->exec($c);

        $feed = new AtomReader();
        $feed->parse($data);

        return $feed->getItems();
    }

/*
    function getFriendsTimeline() ///XXX need OAuth to work
    {
        $c = 'http://twitter.com/statuses/friends_timeline.atom'; //&count=30

        $data = $this->exec($c);

        $feed = new AtomReader();
        $feed->parse($data);

        return $feed->getItems();
    }
*/

    /**
     * Posts a message to your twitter feed
     */
/*
    function post($msg)   //FIXME: needs OAuth support to work
    {
        $c = 'http://twitter.com/statuses/update.xml';
        $arr['status'] = $msg;
        $data = $this->exec($c, $arr, true);

        return true;
    }

*/
    function test()
    {
        $c = 'http://twitter.com/help/test.xml';
        $data = $this->exec($c);

        if ($data != '<ok>true</ok>') return false;
        return true;
    }
}

?>
