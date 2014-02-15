<?php
/**
 * $Id$
 *
 * Private messages
 *
 * @author Martin Lindhe, 2007-2011 <martin@ubique.se>
 */

//STATUS: wip

//TODO: mark message as read

namespace cd;

require_once('SqlObject.php');

class Message
{
    var $id;
    var $from;
    var $to;
    var $time_sent;
    var $time_read;
    var $body;
    var $type;     /// PRIV_MSG or RECORDING_MSG (video msg, where body = video fileId)

    protected static $tbl_name = 'tblMessages';

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

    public function store()
    {
        return SqlObject::store($this, self::$tbl_name, 'id');
    }

    /**
     * @return message id
     */
    public static function send($to, $msg, $type = PRIV_MSG)
    {
        $session = SessionHandler::getInstance();

        $m = new Message();
        $m->to = $to;
        $m->from = $session->id;
        $m->body = $msg;
        $m->type = $type;
        $m->time_sent = sql_datetime( time() );
        return $m->store();
    }

}
