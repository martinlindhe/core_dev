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
    var $from;          ///< user id
    var $time_created;
    var $time_answered;
    var $answered_by;   ///< user id
    var $message;       ///< message id of response from admin

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

    public static function remove($id)
    {
        return SqlObject::deleteById($id, self::$tbl_name);
    }

    /**
     * Mark feedback item as handled
     * @param $message_id optionally refer to a response message
     */
    public static function markHandled($id, $message_id = 0)
    {
        $session = SessionHandler::getInstance();

        $i = FeedbackItem::get($id);
        $i->time_answered = sql_datetime( time() );
        $i->answered_by = $session->id;
        $i->message = $message_id;
        FeedbackItem::store($i);
    }

}

?>
