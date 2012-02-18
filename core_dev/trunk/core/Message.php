<?php
/**
 * $Id$
 *
 * Private messages
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: mark message as read

require_once('SqlObject.php');

class Message
{
    var $id;
    var $from;
    var $to;
    var $time_sent;
    var $time_read;
    var $body;

    protected static $tbl_name = 'tblMessages';

    public static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name, 'id');
    }

    /** @return number of unread messages in the Inbox */
    public static function getUnreadCount($user_id)
    {
        $q =
        'SELECT COUNT(*) FROM '.self::$tbl_name.
        ' WHERE `to` = ? AND time_read IS NULL'.
        ' ORDER BY time_sent DESC';

        return Sql::pSelectItem($q, 'i', $user_id);
    }

    /** @return all messages in the Inbox */
    public static function getInbox($user_id)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE `to` = ?'.
        ' ORDER BY time_sent DESC';

        $list = Sql::pSelect($q, 'i', $user_id);

        return SqlObject::loadObjects($list, __CLASS__);
    }

    /** @return all messages in the Outbox */
    public static function getOutbox($user_id)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE `from` = ?'.
        ' ORDER BY time_sent DESC';

        $list = Sql::pSelect($q, 'i', $user_id);

        return SqlObject::loadObjects($list, __CLASS__);
    }

    /**
     * @return message id
     */
    public static function send($to, $msg)
    {
        $session = SessionHandler::getInstance();

        $m = new Message();
        $m->to = $to;
        $m->from = $session->id;
        $m->body = $msg;
        $m->time_sent = sql_datetime( time() );
        return self::store($m);
    }

}

?>
