<?php
/**
 * $Id$
 *
  * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

/**
 * XXX
 */
function URLadd_DEPRECATED($_key, $_val = '', $_extra = '')    //FIXME: is this function even required???
{
    $curr_url = 'http://localhost'.$_SERVER['REQUEST_URI'];

    $arr = parse_url($curr_url);

    $wiki_link = false;
    $pos = strpos($_key, ':');
    if ($pos !== false) $wiki_link = substr($_key, $pos+1);

    if ($_val) {
        $keyval = $_key.'='.$_val;
    } else {
        $keyval = $_key;
    }

    if (empty($arr['query'])) return $arr['path'].'?'.$keyval.$_extra;

    $args = explode('&', $arr['query']);

    $out_args = '';

    for ($i=0; $i<count($args); $i++) {        //fixme: use foreach

        $vals = explode('=', $args[$i]);

        //Skip it here, $keyval will be added later
        if ($vals[0] == $_key) continue;

        //Wiki:Style links
        if ($wiki_link && strpos($vals[0], ':')) {
            if (substr($vals[0], strpos($vals[0], ':')+1) == $wiki_link) {
                $out_args .= $keyval.'&amp;';    //Replaces wiki link with current wiki link
                $keyval = '';
                continue;
            }
        }

        if (isset($vals[1])) {
            $out_args .= $vals[0].'='.urlencode($vals[1]).'&amp;';
        } else {
            $out_args .= $vals[0].'&amp;';
        }
    }

    if ($out_args && !$keyval && !$_extra) $out_args = substr($out_args, 0, -strlen('&amp;'));

    if ($out_args) {
        return $arr['path'].'?'.$out_args.$keyval.$_extra;
    } else {
        return $arr['path'].'?'.$keyval.$_extra;
    }
}

?>
