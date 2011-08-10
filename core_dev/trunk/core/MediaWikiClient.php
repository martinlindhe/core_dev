<?php
/**
 * $Id$
 *
 * MediaWiki formatting code based on
 * http://johbuc6.coconia.net/doku.php/mediawiki2html_machine/code
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip - not finished

//TODO: need a is_mediawiki_url():
//          full_url examples: http://sv.wiktionary.org/wiki/bestick
//                             http://en.wikipedia.org/wiki/Cutlery
//                     regexp: http(s)://lang.host.tld/wiki/alphanumeric

//TODO: format [[link]], and [[Blinka lilla stjärna#Grammatik|minnesramsa]] tags in text, auto insert <a href="" target="_blank">


require_once('HttpClient.php');
require_once('JSON.php');

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

        $temp->set($key, serialize($res), '48h');
        return $res;
    }

    public static function getArticleIntro($full_url)
    {
        //XXX strip all text inside {{blabla}}

        $article = self::getArticle($full_url);
//d($article->content);

        $pos = strpos($article->content, '==');
        if ($pos === false)
            throw new Exception ('unexpected wiki format '.$article->content);
            // return false;

        $intro = substr($article->content, 0, $pos);
        return self::formatText($intro, $full_url);
    }

    /** Attempt to format MediaWiki markup code */
    public static function formatText($html, $page_url)
    {

//XXX: how to set full url for helper_interwikilinks() while called static and as callback?
        $html = str_replace('&ndash;', '-', $html);
        $html = str_replace('&quot;', '"', $html);
        $html = preg_replace('/\&amp;(nbsp);/', '&${1};', $html);

        //formatting
        // bold
        $html = preg_replace('/\'\'\'([^\n\']+)\'\'\'/', '<strong>${1}</strong>', $html);
        // emphasized
        $html = preg_replace('/\'\'([^\'\n]+)\'\'?/', '<em>${1}</em>', $html);
        //interwiki links
        $html = preg_replace_callback('/\[\[([^\|\n\]:]+)[\|]([^\]]+)\]\]/', 'self::helper_interwikilinks', $html);
        // without text
        $html = preg_replace_callback('/\[\[([^\|\n\]:]+)\]\]/', 'self::helper_interwikilinks', $html);

        //$html = preg_replace('/{{([^}]+)+}}/', 'Interwiki: ${1}+${2}+${3}', $html);
        $html = preg_replace('/{{([^\|\n\}]+)([\|]?([^\}]+))+\}\}/', 'Interwiki: ${1} &raquo; ${3}', $html);
        // Template
        //$html = preg_replace('/{{([^}]*)}}/', ' ', $html);
        // categories
        //$html = preg_replace('/\[\[([^\|\n\]]+)([\|]([^\]]+))?\]\]/', '', $html);
        $html = preg_replace('/\[\[([^\|\n\]]{2})([\:]([^\]]+))?\]\]/', 'Translation: ${1} &raquo; ${3}', $html);
        $html = preg_replace('/\[\[([^\|\n\]]+)([\:]([^\]]+))?\]\]/', 'Category: ${1} - ${2}', $html);
        // image
        $html = preg_replace('/\[\[([^\|\n\]]+)([\|]([^\]]+))+\]\]/', 'Image: ${0}+${1}+${2}+${3}', $html);

        //links
        //$html = preg_replace('/\[([^\[\]\|\n\': ]+)\]/', '<a href="${1}">${1}</a>', $html);
        $html = preg_replace_callback('/\[([^\[\]\|\n\': ]+)\]/', 'self::helper_externlinks', $html);
        // with text
        //$html = preg_replace('/\[([^\[\]\|\n\' ]+)[\| ]([^\]\']+)\]/', '<a href="${1}">${2}</a>', $html);
        $html = preg_replace_callback('/\[([^\[\]\|\n\' ]+)[\| ]([^\]\']+)\]/', 'self::helper_externlinks', $html);

        // allowed tags
        $html = preg_replace('/&lt;(\/?)(small|sup|sub|u)&gt;/', '<${1}${2}>', $html);

        $html = preg_replace('/\n*&lt;br *\/?&gt;\n*/', "\n", $html);
        $html = preg_replace('/&lt;(\/?)(math|pre|code|nowiki)&gt;/', '<${1}pre>', $html);
        $html = preg_replace('/&lt;!--/', '<!--', $html);
        $html = preg_replace('/--&gt;/', ' -->', $html);

        // headings
        for ($i=7; $i>0; $i--)
            $html = preg_replace(
                '/\n+[=]{'.$i.'}([^=]+)[=]{'.$i.'}\n*/',
                '<h'.$i.'>${1}</h'.$i.'>',
                $html
            );

        //lists
        $html = preg_replace(
            '/(\n[ ]*[^#* ][^\n]*)\n(([ ]*[*]([^\n]*)\n)+)/',
            '${1}<ul>'."\n".'${2}'.'</ul>'."\n",
            $html
        );
        $html = preg_replace(
            '/(\n[ ]*[^#* ][^\n]*)\n(([ ]*[#]([^\n]*)\n)+)/',
            '${1}<ol>'."\n".'${2}'.'</ol>'."\n",
            $html
        );
        $html = preg_replace('/\n[ ]*[\*#]+([^\n]*)/', '<li>${1}</li>', $html);

        $html = preg_replace('/----/', '<hr />', $html);

        // line breaks
        $html = preg_replace('/[\n\r]{4}/', '<br/><br/>', $html);
        $html = preg_replace('/[\n\r]{2}/', '<br/>', $html);

        $html = preg_replace('/[>]<br\/>[<]/', '><', $html);

        return $html;
    }

    protected static function helper_externlinks($matches)
    {
        $target = $matches[1];
        $text = empty($matches[2])?$matches[1]:$matches[2];
        return '<a href="'.$target.'">'.$text.'</a>';
    }

    protected static function helper_interwikilinks($matches)
    {
        $target = $matches[1];
        $text = empty($matches[2])?$matches[1]:$matches[2];
        return '<a  href="?page='.$target.'">'.$text.'</a>';
    }

}

?>
