<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip - not finished

require_once('HttpClient.php');
require_once('JSON.php');

class MediaWikiPage
{
    var $title;
    var $pageid; // id in mediawiki database
    var $content;
}

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
        '&rvprop=content'.
        '&titles='.urlencode($name);

        $key = 'MediaWikiClient/'.sha1( $url );

        $res = $temp->get($key);
        if ($res)
            return unserialize($res);

        $this->setUrl($url);

        // required: http://meta.wikimedia.org/wiki/User-Agent_policy
        $this->setUserAgent('core_dev 0.2');

        $raw = $this->getBody();
        $json = JSON::decode($raw);

        $pages = array();
        foreach ( $json->query->pages as $id => $p) {
            $o = new MediaWikiPage();
            $o->title   = $p->title;
            $o->pageid  = $p->pageid;
            $o->content = $p->revisions[0]->{'*'};
            $pages[] = $o;
        }
        $res = $pages[0]; // XXX only exports one article

        $temp->set($key, serialize($res), '24h');
        return $res;
    }

}

?>
