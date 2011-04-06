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

function ahref($url, $title, $target = '')
{
    return '<a href="'.relurl($url).'"'.($target ? ' target="'.$target.'"' : '').'>'.$title.'</a>';
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
