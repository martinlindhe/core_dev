<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip - not finished. only returns json-result as a object

require_once('HttpClient.php');
require_once('JSON.php');

class MediaWikiClient extends HttpClient
{
    function getArticle($name)
    {
        if (!$this->getUrl())
            throw new Exception ('set a mediawiki site url in constructor');

        $temp = TempStore::getInstance();

        $url = $this->getUrl().'w/api.php'.
        '?action=query'.
        '&format=json'.
        '&prop=revisions'.
        '&rvlimit=1'.
        '&rvprop=content|timestamp'.
        '&titles='.$name;

        $key = 'MediaWikiClient/'.sha1( $url );

        $res = $temp->get($key);
        if ($res)
            return unserialize($res);

        $this->setUrl($url);

        // required: http://meta.wikimedia.org/wiki/User-Agent_policy
        $this->setUserAgent('core_dev 0.2');

        $raw = $this->getBody();
        $json = JSON::decode($raw);

        $temp->set($key, serialize($json), '4h');
        return $json;
    }

}

/*
$x = new MediaWikiClient('http://sv.wikipedia.org/');

$res = $x->getArticle('sten');
d($res);
*/

?>
