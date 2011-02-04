<?php

/**
 * Formats $s to a css size value
 * @param $s pixel value, or em value, or percentage
 */
function css_size($s)
{
    if (is_numeric($s))
        return $s.'px';

    // handle "100%"
    if (substr($s, -1) == '%')
        return $s;

    throw new Exception ('fixme '.$s);

    //TODO: handle "40.5em"
}

?>
