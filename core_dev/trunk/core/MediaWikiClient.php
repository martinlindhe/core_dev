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

class MediaWikiClient
{
    static function getArticle($full_url)
    {
        if (!is_url($full_url))
            throw new Exception ('need a url... '.$full_url);

        //XXX verify url against is_mediawiki_url() regexp: http(s)://lang.host.tld/wiki/alphanumeric


        // full_url examples: http://sv.wiktionary.org/wiki/bestick
        //                    http://en.wikipedia.org/wiki/Cutlery

        $url = new Url($full_url);

        $x = explode('/', $url->getPath() );
        $name = array_pop($x);

        $url->setPath('/w/api.php'.
        '?action=query'.
        '&format=json'.
        '&prop=revisions'.
        '&rvlimit=1'.
        '&rvprop=content'.
        '&titles='.urlencode($name)
        );

        $http = new HttpClient();

        $key = 'MediaWikiClient/'.sha1( $url->get() );

        $temp = TempStore::getInstance();
        $res = $temp->get($key);
        if ($res)
            return unserialize($res);

        $http->setUrl($url);

        $raw = $http->getBody();
        $json = JSON::decode($raw);

        $pages = array();
        foreach ( $json->query->pages as $id => $p) {
            $o = new MediaWikiPage();
            $o->title   = $p->title;
            $o->pageid  = $p->pageid;
            $o->content = $p->revisions[0]->{'*'};
            $pages[] = $o;
        }
        $res = $pages[0]; // XXX only exports first article

        $temp->set($key, serialize($res), '48h');
        return $res;
    }

}

?>
