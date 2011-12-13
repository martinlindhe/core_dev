<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip

class FeedbackItem
{
    var $id;
    var $subject;
    var $body;
    var $from;   ///< user id
    var $time_created;
    var $time_answered;
    var $answered_by;  ///< user id

    protected static $tbl_name = 'tblFeedback';

    public static function get($id)
    {
        return SqlObject::getById($id, self::$tbl_name, __CLASS__);
    }

    public static function getUnanswered()
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE time_answered IS NULL';
        return SqlObject::loadObjects($q, __CLASS__);
    }

    public static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name);
    }

}

?>
