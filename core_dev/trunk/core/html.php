<?php
/**
 * $Id$
 *
 * HTML utility functions
 */

//STATUS: wip

/**
 * Decodes html entities from input string
 */
function htmlchars_decode($s)
{
    return html_entity_decode($s, ENT_COMPAT, 'UTF-8');
}

/** similar to strip_tags() but also removes all javascript / css inside <script> or <style> tags */
function strip_html($s)
{
    $search = array(
    '@<(script|style)[^>]*?>.*?</(script|style)>@si', // javascript, css
    '@<[\/\!]*?[^<>]*?>@si',                          // HTML tags
    '@<![\s\S]*?--[ \t\n\r]*>@',    // strip multi-line comments including CDATA
    );

    $s = preg_replace($search, '', $s);

    $s = htmlchars_decode($s);
    return $s;
}

/** @param $dst redirects user to destination url relative to website base url */
function redir($dst)
{
    $page = XmlDocumentHandler::getInstance();
    header('Location: '.$page->getRelativeUrl().$dst);
    die;
}

/** @param $url partial url to generate a url relative website base */
function relurl($url)
{
    if (substr($url, 0, 4) == 'http' || substr($url, 0, 1) == '/')
        return $url;

    $page = XmlDocumentHandler::getInstance();
    return $page->getRelativeUrl().$url;
}

/**
 * Modifies parameters to current request URI
 * @param $p array of key=>val pairs
 */
function relurl_add($p)
{
    $page = XmlDocumentHandler::getInstance();

    $u = new Url( $page->getUrl() );
    $u->setPath($_SERVER['REQUEST_URI']);

    foreach ($p as $key => $val)
        $u->setParam($key, $val);

    return $u->getPath();
}

/** Creates a clickable link */
function ahref($url, $text, $target = '', $onclick = '')
{
    return
    '<a href="'.relurl($url).'"'.
    ($target ? ' target="'.$target.'"' : '').
    ($onclick ? ' onclick="'.$onclick.'"' : ''). // executes javascript
    '>'.$text.'</a>';
}

/** Creates a clickable link that opens in a new window */
function ahref_blank($url, $text)
{
    return ahref($url, $text, '_blank');
}

function ahref_js($text, $js)
{
    return ahref('#', $text, '', $js);
}

/** Creates "are you sure?" pages */
function confirmed($text)
{
    if (isset($_GET['cd_confirmed']))
        return true;

    echo $text.'<br/><br/>';

    echo '<a href="'.relurl_add(array('cd_confirmed'=>1)).'">Yes, I am sure</a><br/><br/>';
    echo '<a href="javascript:history.go(-1);">No, wrong button</a><br/>';
    return false;
}

?>
