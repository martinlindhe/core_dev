<?php
/**
 * $Id$
 *
 * Helper functions for JavaScript generation
 *
  * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

/**
 * Reload current page after specified period of time
 *
 * @param $ms reload time in milliseconds (1/1000th second)
 */
function js_reload($ms)
{
    if (!is_numeric($ms))
        return false;

    // XXXX throw exception if server outputted errors during this page load. how to check that?

    return js_embed('setTimeout("location.reload();", '.$ms.');');
}

/**
 * Redirects the user to a different page
 */
function js_redirect($url)
{
    // XXXX throw exception if server outputted errors during this page load. how to check that?

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

function js_goback($title = 'Go back')
{
    return '<a href="javascript:history.go(-1)">'.$title.'</a>';
}

function js_goforward($title = 'Go forward')
{
    return '<a href="javascript:history.go(1)">'.$title.'</a>';
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

?>
