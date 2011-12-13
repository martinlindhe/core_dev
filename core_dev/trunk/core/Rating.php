<?php
/**
 * $Id$
 */

class Rating
{
    /** Count current average of the rating */
    public static function getAverage($type, $id)
    {
        $q = 'SELECT AVG(value) FROM tblRatings WHERE type = ? AND owner = ?';
        return SqlHandler::getInstance()->pSelectItem($q, 'ii', $type, $id);
    }
}

?>