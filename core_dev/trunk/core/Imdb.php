<?php
/**
 * $Id$
 *
 * Helper functions dealing with imdb.com
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

class Imdb
{
    /**
     * @param $id imdb id
     * @return true if $id is a imdb ib
     */
    static function isValidId($id)
    {
        if (strpos($id, ' '))
            return false;

        $pattern = "((tt|ch|nm|co)([0-9]){7})";

        if (preg_match($pattern, $id))
            return true;

        return false;
    }

}

?>
