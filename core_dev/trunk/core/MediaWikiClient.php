<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip - not finished

//TODO: need a is_mediawiki_url():
//          full_url examples: http://sv.wiktionary.org/wiki/bestick
//                             http://en.wikipedia.org/wiki/Cutlery
//                     regexp: http(s)://lang.host.tld/wiki/alphanumeric

require_once('HttpClient.php');
require_once('JSON.php');
require_once('MediaWikiFormatter.php');

class MediaWikiPage
{
    var $title;
    var $pageid; // id in mediawiki database
    var $content;
}

function is_mediawiki_url($url)
{
    // FIXME implement
    return true;
}

class MediaWikiClient
{

    /** @return raw unparsed article */
    public static function getArticle($full_url)
    {
        if (!is_url($full_url) || !is_mediawiki_url($full_url))
            throw new Exception ('need a mediawiki url... '.$full_url);

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

        $temp->set($key, serialize($res), '24h');
        return $res;
    }

    /** @return Article summary */
    public static function getArticleSummary($full_url)
    {
        //XXX strip all text inside {{blabla}}

        $article = self::getArticle($full_url);
//d($article->content);

        $pos = strpos($article->content, '==');
        if ($pos === false)
            throw new Exception ('unexpected wiki format '.$article->content);
            // return false;

        $intro = substr($article->content, 0, $pos);

        $fmt = new MediaWikiFormatter();
        return $fmt->format($intro, $full_url);
    }

}

?>
