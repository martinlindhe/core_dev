<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011-2012 <martin@startwars.org>
 */

//STATUS: wip

//TODO: automatically expire mediaiwiki pages (fetch new version when needed)
//      finish $use_db_cache by respecting max-age parameter (currently ignored,
//      only first version of page is ever fetched)

namespace cd;

require_once('HttpClient.php');
require_once('JSON.php');
require_once('MediaWikiParser.php');

class MediaWikiPage
{
    var $id;          ///< internal db id
    var $url;
    var $title;
    var $lang;        ///< 2-letter language code
    var $pageid;      ///< unsigned integer id in mediawiki database
    var $content;
    var $time_saved;

    protected static $tbl_name = 'tblMediaWikiPages';

    static function get($url)
    {
// XXXX: only get latest article
        return SqlObject::getByField($url, self::$tbl_name, __CLASS__, 'url');
    }

    public static function store($o)
    {
// XXXX: delete all articles with same $url before storing (?)
        return SqlObject::store($o, self::$tbl_name, 'url');
    }
}

function is_mediawiki_url($url)
{
    $pattern =
    '('.
        '(https?){1}://'.
//        '([\w+]).'.   // XXX improve check to require 2-3 ASCII letters language code
        '([en|sv].wikipedia.org/wiki/){1}'.
        '('.
            // XXX improve match???
            '[\w+]'.
        ')?'.
    ')i';
/*
    $pattern =
    '('.
        'https?://'.
        '([-\w\.]+)+'.
        '(wikipedia.org/wiki/){1}'.
        '(/([\w/_\.]*(\?\S+)?)?)+'.
    ')';
*/

    if (is_url($url))
        return true;

// XXX FINISH THE REGEXP!!!

    preg_match($pattern, $url, $matches);
d($matches);
    if ($matches && $matches[0] == $url)
        return true;

    return false;
}

class MediaWikiClient
{
    public static function getArticleTitle($full_url)
    {
        $url = new Url($full_url);

        $x = explode('/', $url->getPath() );
        $t = urldecode( array_pop($x) );
        return $t;
    }

    /** @return 2-letter language code from MediaWiki url */
    public static function getArticleLanguage($full_url)
    {
        // XXX only works on xxx.wikipedia.org or xx.wiktionary.org url:s
        $url = new Url($full_url);

        $host = $url->getHost();
        $x = explode('.', $host);
        if (count($x) != 3)
            throw new \Exception ('something wrong with the url '.$full_url);

        return $x[0];
    }

    /**
     * @param $use_db_cache false to disable; set to a time period, like "6m" or "6 months" to cache articles in local db
     * @return raw unparsed article
     */
    public static function getArticle($full_url, $use_db_cache = '30 days')
    {
        if (!is_url($full_url) || !is_mediawiki_url($full_url))
            throw new \Exception ('need a mediawiki url... '.$full_url);

        $article_name = self::getArticleTitle($full_url);

        $url = new Url($full_url);

        $url->setPath('/w/api.php'.
        '?action=query'.
        '&format=json'.
        '&prop=revisions'.
        '&rvlimit=1'.
        '&rvprop=content'.
        '&titles='.urlencode($article_name)
        );

        if ($use_db_cache)
        {
            // XXX is $use_db_cache a time-span?
            // XXXX is article recent enough?

            $res = MediaWikiPage::get($full_url);
            if ($res)
                return $res;
        }

        $key = 'MediaWikiClient/'.sha1( $url->get() );
        $temp = TempStore::getInstance();
        $res = $temp->get($key);
        if ($res)
            return unserialize($res);

        $http = new HttpClient($url);

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

            $o->url        = $full_url;
            $o->lang       = self::getArticleLanguage($full_url);
            $o->title      = $p->title;
            $o->pageid     = $p->pageid;
            $o->content    = $p->revisions[0]->{'*'};
            $o->time_saved = sql_datetime( time() );

            $pages[] = $o;
        }
        $res = $pages[0]; // XXX only exports first article

        $temp->set($key, serialize($res), '24h');

        if ($use_db_cache) {
            if (strpos($res->content, "\xF0\x90") !== false)
                echo '<div class="critical">WARNING: This MediaWiki article contains utf8-v5 string, requires mysql5.5 / utf8mb4 data type</div>'."\n";

            $res->store();
        }

        return $res;
    }

    /** @return Article summary */
    public static function getArticleSummary($full_url)
    {
        if ($full_url instanceof MediaWikiPage)
        {
            $article = $full_url;
        }
        else if (is_url($full_url))
        {
            $article = self::getArticle($full_url);
            if (!$article)
                throw new \Exception ('failed to fetch article '.$full_url);
        }
        else
            return false;
            //throw new \Exception ('wierd input: '.$full_url);

        $article = MediaWikiParser::parseArticle($article->content);
        return $article->summary;
    }

    public static function showArticle($full_url)  /// XXXX MAKE THIS INTO A VIEW, WHICH CAN HANDLE "FETCH NEW VERSION OF ARTICLE" CAPSLOCKFTW
    {
//d($full_url);die;
        $o = self::getArticle( $full_url );
        if (!$o) {
            echo "WARNING: no such article in cache: ".$full_url."<br/>";
            return false;
        }

        $res =
        '<div class="okay">'.    // XXX have some better css
        '<h3>'.
        'MediaWiki of '.ahref_blank($full_url, $o->title).
        ' ('.$o->lang.')'.
        '</h3>'.
        '<i>Retrieved '.ago($o->time_saved).'</i><br/>'.
        MediaWikiClient::getArticleSummary( $o ).
        '</div>';

        return $res;
    }

}

?>
