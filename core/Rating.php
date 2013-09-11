<?php
/**
 * $Id$
 */

namespace cd;

class Rating
{
     protected static $tbl_name = 'tblRatings';

    /** Count current average of the rating */
    public static function getAverage($type, $id)
    {
        $q =
        'SELECT AVG(value) FROM '.self::$tbl_name.
        ' WHERE type = ? AND owner = ?';
        return Sql::pSelectItem($q, 'ii', $type, $id);
    }

    /** Get statistics for specified poll */
    static function getStats($type, $id)
    {
        $q  =
        'SELECT t1.categoryName, '.
            '(SELECT COUNT(*) FROM '.self::$tbl_name.
            ' WHERE type = ? AND value = t1.categoryId) AS cnt'.
        ' FROM tblCategories AS t1'.
        ' WHERE t1.ownerId = ?';
        return Sql::pSelectMapped($q, 'ii', $type, $id);
    }

    /** Has current user rated item/answered poll? */
    static function hasAnswered($type, $id)
    {
        $session = SessionHandler::getInstance();
        if (!$session->id)
            return true;

        $q =
        'SELECT owner FROM '.self::$tbl_name.
        ' WHERE type = ? AND userId = ? AND owner = ?';
        if (Sql::pSelectItem($q, 'iii', $type, $session->id, $id))
            return true;

        return false;
    }

    /** Votes for a poll */
    static function addVote($type, $id, $value)
    {
        $session = SessionHandler::getInstance();
        if (!$session->id)
            return false;

        if (self::hasAnswered($type, $id))
            return false;

        $q =
        'INSERT INTO '.self::$tbl_name.
        ' SET type = ?, owner = ?, userId = ?, value = ?, timestamp = NOW()';
        Sql::pInsert($q, 'iiii', $type, $id, $session->id, $value);
        return true;
    }


}

?>
