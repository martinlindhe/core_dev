<?php
/**
 * $Id$
 *
 * Helper functions dealing with imdb.com
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

namespace cd;

class Imdb
{
    static function getIdFromUrl($url)
    {
        $pattern = "((http://www.imdb.com/title/)((tt|ch|nm|co)([0-9]){7}))";

        preg_match($pattern, $url, $res);
        return !empty($res[2]) ? $res[2] : false;
    }

    /**
     * @param $id imdb id
     * @return true if $id is a imdb ib
     */
    static function isValidId($id)
    {
        $pattern = "((tt|ch|nm|co)([0-9]){7})";

        if (preg_match($pattern, $id))
            return true;

        return false;
    }

}

/**
 * Validates a IMDB url
 * @param $uri string
 * @return true if $uri is a IMDB url
 */
function is_imdb_url($url)
{
    if (Imdb::getIdFromUrl($url))
        return true;

    return false;
}

?>
