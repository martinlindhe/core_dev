<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011-2013 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

require_once('constants.php');

class Feedback
{
    var $id;
    var $type;
    var $subject;
    var $body;
    var $from;          ///< user id
    var $time_created;
    var $time_answered;
    var $answered_by;   ///< user id
    var $reference;     ///< message id of response from admin (USER)

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

    public static function getUnansweredCount()
    {
        $q =
        'SELECT COUNT(*) FROM '.self::$tbl_name.
        ' WHERE time_answered IS NULL';
        return Sql::pSelectItem($q);
    }

    public static function ReferenceExists($type, $ref)
    {
        $q =
        'SELECT COUNT(*) FROM '.self::$tbl_name.
        ' WHERE type = ? AND reference = ?';
        $cnt = Sql::pSelectItem($q, 'ii', $type, $ref);
        return $cnt ? true : false;
    }

    public static function SubjectExists($type, $subject)
    {
        $q =
        'SELECT COUNT(*) FROM '.self::$tbl_name.
        ' WHERE type = ? AND subject = ?';
        $cnt = Sql::pSelectItem($q, 'is', $type, $subject);
        return $cnt ? true : false;
    }

    public function store()
    {
        return SqlObject::store($this, self::$tbl_name);
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

        $i = self::get($id);
        $i->time_answered = sql_datetime( time() );
        $i->answered_by = $session->id;
        $i->message = $message_id;
        $i->store();
    }

}
