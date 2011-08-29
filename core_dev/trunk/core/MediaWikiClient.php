<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: automatically expire mediaiwiki pages (fetch new version when needed)
//      finish $use_db_cache by respecting max-age parameter (currently ignored,
//      only first version of page is ever fetched)

//TODO: finish is_mediawiki_url()

require_once('HttpClient.php');
require_once('JSON.php');
require_once('MediaWikiFormatter.php');

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
        if (!$o->time_saved)
            $o->time_saved = sql_datetime( time() );

// XXXX: delete all articles with same $url before storing (?)
        return SqlObject::store($o, self::$tbl_name, 'url');
    }
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
    public static function getArticleTitle($full_url)
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

    /**
     * @param $use_db_cache false to disable; set to a time period, like "6m" or "6 months" to cache articles in local db
     * @return raw unparsed article
     */
    public static function getArticle($full_url, $use_db_cache = '6 months')
    {
        if (!is_url($full_url) || !is_mediawiki_url($full_url))
            throw new Exception ('need a mediawiki url... '.$full_url);

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
        else
        {
            $key = 'MediaWikiClient/'.sha1( $url->get() );
            $temp = TempStore::getInstance();
            $res = $temp->get($key);
            if ($res)
                return unserialize($res);
        }

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

            $o->url     = $full_url;
            $o->lang    = self::getArticleLanguage($full_url);
            $o->title   = $p->title;
            $o->pageid  = $p->pageid;
            $o->content = $p->revisions[0]->{'*'};

            $pages[] = $o;
        }
        $res = $pages[0]; // XXX only exports first article

        if ($use_db_cache)
            MediaWikiPage::store($res);
        else
            $temp->set($key, serialize($res), '24h');

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
                throw new Exception ('failed to fetch article '.$full_url);
        }
        else
            throw new Exception ('wierd input: '.$full_url);

        $pos = strpos($article->content, '==');
        if ($pos === false)
            throw new Exception ('unexpected wiki format '.$article->content);

        $intro = substr($article->content, 0, $pos);

        //XXX strip all text inside {{blabla}}

        $fmt = new MediaWikiFormatter();
        return $fmt->format($intro, $article->url);
    }

    public static function showArticle($full_url)
    {
        $o = self::getArticle( $full_url );

        $res =
        '<div class="okay">'.    // XXX have some better css
        '<h3>'.
        'MediaWiki of '.ahref_blank($full_url, $o->title).
        ' ('.$o->lang.')'.
        ' as of '.$o->time_saved.
        '</h3>'.
        MediaWikiClient::getArticleSummary( $o ).
        '</div>';

        return $res;
    }

}

?>
