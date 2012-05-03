<?php
/**
 * $Id$
 *
 * Various HTML, Javascript and CSS utility functions
 *
 * @author Martin Lindhe, 2007-2012 <martin@startwars.org>
 */

require_once('XmlDocumentHandler.php');

require_once('XhtmlComponentA.php');

//STATUS: wip

//TODO in js_reload() & js_redirect(): throw exception if server outputted errors during this page load. how to check that?

function is_html_color($s)
{
    $regexp = '/^\#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/';

    preg_match($regexp, $s, $matches);

    if ($matches && $matches[0] == $s)
        return true;

    return false;
}

/** Decodes html entities from input string */
function htmlchars_decode($s)
{
    return html_entity_decode($s, ENT_QUOTES, 'UTF-8');
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

/** Like htmlentities() but only handles predefined xml entities */
function xmlentities($s)
{
    $from = array('<',    '>',    "'",      '"',      '&');
    $to   = array('&lt;', '&gt;', '&apos;', '&quot;', '&amp;');

    return str_replace($from, $to, $s);
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

/**
 * Creates a clickable link
 * @param $target example "_blank"
 */
function ahref($url, $text, $target = '', $onclick = '', $class = '')
{
    $a = new XhtmlComponentA();
    $a->href = relurl($url);
    $a->content = $text;
    $a->target = $target;
    $a->onClick($onclick);
    $a->class = $class;

    return $a->render();
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
        throw new Exception ('not a numeric: '.$ms);

    return js_embed('setTimeout("location.reload();",'.$ms.');');
}

/** Redirects the user to a different page */
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

/** Renders a Unix timestamp in Javascript format (american): MM/DD/YYYY */
function js_date($ts)
{
    return date('m/d/Y', $ts);
}

/** Embeds javascript snippet */
function js_embed($s)
{
    return '<script type="text/javascript">'.$s.'</script>';
}

/** Embeds string as a XHTML CDATA block */
function cdata_embed($s)
{
    return '<![CDATA['.trim($s).']]>';
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

/**
 * Embeds SWF using js/swfobject.js, see http://code.google.com/p/swfobject/
 * @flashvars array of key=>val pairs
 */
function js_swfobject($swf, $render_div, $width = 300, $height = 120, $flashvars = '', $params = '', $attributes = '')
{
    $page = XmlDocumentHandler::getInstance();

    $header = XhtmlHeader::getInstance();
    $header->includeJs( $page->getRelativeCoreDevUrl().'js/swfobject.js');

    $min_version = '9.0.115'; // first version to support MP4

    $js =
    'var flashvars='.json_encode($flashvars).';'.
    'var params='.json_encode($params).';'.
    'var attributes='.json_encode($attributes).';'.
    'swfobject.embedSWF("'.$swf.'","'.$render_div.'","'.$width.'","'.$height.'","'.$min_version.'","false",flashvars,params,attributes);';

    return js_embed($js);
}

/**
 * Displays one of core_dev's default action buttons
 */
function coreButton($name, $link = '', $title = '')
{
    switch ($name) {
        case 'Edit':   $img = 'icon_create.png'; break;
        case 'Create': $img = 'icon_create.png'; break;
        case 'Delete': $img = 'icon_delete.png'; break;
        case 'Folder': $img = 'icon_folder.png'; break;
        case 'Add':    $img = 'icon_add.png'; break;
        case 'Error':  $img = 'icon_error.png'; break;

        default:
            echo '<h1>ERROR unknown coreButton '.$name.'</h1>';
            return;
    }

    $out = '';
    if ($link) $out .= '<a href="'.$link.'">';
    if (!$title) $title = t($name);

    $out .= '<img src="'.relurl('core_dev/gfx/'.$img).'" alt="'.$title.'" title="'.$title.'"/>';
    if ($link) $out .= '</a>';
    return $out;
}

function countryFlag($s)
{
    if (is_numeric($s))
        $s = getCountryCode($s);
    else
    {
        if (!is_alphanumeric($s))
            throw new Exception ('hey');

        if (strlen($s) == 2)
            $s = country_2_to_3_letters($s);

        $s = strtoupper($s);
    }

    $locale = LocaleHandler::getInstance();

    $title = getCountryName($s);

    if (!$title)
        throw new Exception ('unhandled country flag code '.$s);

    return '<img src="'.relurl('core_dev/gfx/flags/'.$s.'.png').'" alt="'.$title.'" title="'.$title.'"/>';
}

/**
 * Uses FLV Player Maxi from http://code.google.com/p/flvplayer/ to play requested video clip
 */
function embed_flv($vid_id)
{
    $pres_div = 'pres_div'.mt_rand();

    $file = File::get($vid_id);

    $flashvars  = array(
    'flv' => '/coredev/file/'.$vid_id,
    'autoload' => 0, 'showstop' => 1, 'showvolume' => 1, 'shortcut' => 0,
    'buffermessage' => '',
    'margin' => 2,
    'bgcolor1' => '454545', 'bgcolor2' => '454545', 'playercolor' => '454545');

    $thumbs = File::getByCategory(THUMB, $vid_id, $file->uploader);
    if (count($thumbs) == 1)
        $flashvars['startimage'] = '/coredev/file/'.$thumbs[0]->id;

    return
    '<div id="'.$pres_div.'"></div>'.
    js_swfobject('/swf/player_flv_maxi.swf', $pres_div, 240, 182, $flashvars);
}

?>
