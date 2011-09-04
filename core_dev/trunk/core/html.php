<?php
/**
 * $Id$
 *
 * Various HTML, Javascript and CSS utility functions
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

require_once('XmlDocumentHandler.php');

//STATUS: wip

//TODO in js_reload() & js_redirect(): throw exception if server outputted errors during this page load. how to check that?

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
    if (substr($url, 0, 4) == 'http' || substr($url, 0, 1) == '/' || substr($url, 0, 1) == '?')
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
function ahref($url, $text, $target = '', $onclick = '', $class = '')
{
    return
    '<a href="'.relurl($url).'"'.
    ($class   ? ' class="'.$class.'"' : '').
    ($target  ? ' target="'.$target.'"' : '').
    ($onclick ? ' onclick="'.$onclick.'"' : ''). // executes javascript
    '>'.$text.'</a>';
}

/** Creates a clickable link that opens in a new window */
function ahref_blank($url, $text)
{
    return ahref($url, $text, '_blank');
}

function ahref_js($text, $js, $class = '')
{
    return ahref('#', $text, '', $js, $class);
}

function js_goback($title = 'Go back')
{
    return '<a href="javascript:history.go(-1)">'.$title.'</a>';
}

function js_goforward($title = 'Go forward')
{
    return '<a href="javascript:history.go(1)">'.$title.'</a>';
}

/** Creates "are you sure?" pages */
function confirmed($text)
{
    if (isset($_GET['cd_confirmed']))
        return true;

    echo $text.'<br/><br/>';

    echo '<a href="'.relurl_add(array('cd_confirmed'=>1)).'">Yes, I am sure</a><br/><br/>';
    echo js_goback('No, wrong button').'<br/>';
    return false;
}

/**
 * Reload current page after specified period of time
 *
 * @param $ms reload time in milliseconds (1/1000th second)
 */
function js_reload($ms)
{
    if (!is_numeric($ms))
        throw new Exception ('js_reload() requires numeric: '.$ms);

    return js_embed('setTimeout("location.reload();",'.$ms.');');
}

/**
 * Redirects the user to a different page
 */
function js_redirect($url)
{
    if (substr($url, 0, 1) != '/')
        $url = relurl($url);

    if (!$url) {
        $page = XmlDocumentHandler::getInstance();
        $url = $page->getRelativeUrl();
    }

    if (headers_sent()) {
        echo js_embed('document.location.href="'.$url.'";');
        die;
    } else {
        header('Location: '.$url);
        die;
    }
}

/**
 * Renders a Unix timestamp in Javascript format (american): MM/DD/YYYY
 */
function js_date($ts)
{
    return date('m/d/Y', $ts);
}

/** Embeds javascript snippet */
function js_embed($s)
{
    return '<script type="text/javascript">'.$s.'</script>';
}

function css_embed($s)
{
    return '<style type="text/css">'.$s.'</style>';
}

/**
 * Formats $s to a css size value
 * @param $s pixel value, or em value, or percentage
 */
function css_size($s)
{
    if (is_numeric($s))
        return $s.'px';

    // handles percentages; eg: "100%"
    if (substr($s, -1) == '%')
        return $s;

    throw new Exception ('fixme '.$s);

    //TODO: handle "40.5em"
}

?>
