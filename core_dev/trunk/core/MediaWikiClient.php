<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: finish is_mediawiki_url()

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
/* FIXME implement

full_url examples: http://sv.wiktionary.org/wiki/bestick
                   http://en.wikipedia.org/wiki/Cutlery

           regexp: http(s)://lang.host.tld/wiki/alphanumeric
*/
    return true;
}

class MediaWikiClient
{
    public static function getArticleName($full_url)
    {
        $url = new Url($full_url);

        $x = explode('/', $url->getPath() );
        return array_pop($x);
    }

    /** @return 2-letter language code from MediaWiki url */
    public static function getArticleLanguage($full_url)
    {
        // XXX only works on xxx.wikipedia.org or xx.wiktionary.org url:s
        $url = new Url($full_url);

        $host = $url->getHost();
        $x = explode('.', $host);
        if (count($x) != 3)
            throw new Exception ('something wrong with the url '.$full_url);

        return $x[0];
    }

    /** @return raw unparsed article */
    public static function getArticle($full_url)
    {
        if (!is_url($full_url) || !is_mediawiki_url($full_url))
            throw new Exception ('need a mediawiki url... '.$full_url);

        $article_name = self::getArticleName($full_url);

        $url = new Url($full_url);

        $url->setPath('/w/api.php'.
        '?action=query'.
        '&format=json'.
        '&prop=revisions'.
        '&rvlimit=1'.
        '&rvprop=content'.
        '&titles='.urlencode($article_name)
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
        foreach ( $json->query->pages as $id => $p)
        {
            if ($id == '-1')
            {
                echo 'MEDIAWIKI FAIL: no page result for "'.$article_name.'"<br/>';
                return false;
            }

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
        if (!$article)
            throw new Exception ('failed to fetch article '.$full_url);

        $pos = strpos($article->content, '==');
        if ($pos === false)
            throw new Exception ('unexpected wiki format '.$article->content);

        $intro = substr($article->content, 0, $pos);

        $fmt = new MediaWikiFormatter();
        return $fmt->format($intro, $full_url);
    }

    public static function showArticle($full_url)
    {
        $res =
        '<div class="okay">'.    // XXX have some better css
        '<h3>'.
        'MediaWiki of '.ahref_blank($full_url, MediaWikiClient::getArticleName($full_url)).
        ' ('.MediaWikiClient::getArticleLanguage($full_url).')'.
        '</h3>'.
        MediaWikiClient::getArticleSummary( $full_url ).
        '</div>';

        return $res;
    }

}

?>
