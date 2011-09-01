<?php
/**
 * $Id$
 *
 * Private messages
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: early draft. will replace functions_messages.php

require_once('SqlObject.php');

class Message
{
    var $id;
    var $from;
    var $to;
    var $time_sent;
    var $time_read;
    var $subject;
    var $body;

    protected static $tbl_name = 'tblMessages';

    static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name, 'id');
    }

    /** @return all messages in the Inbox of $id */
    static function getInbox($id)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE `to` = ?'.
        ' ORDER BY time_sent DESC';

        $list = Sql::pSelect($q, 'i', $id);

        return SqlObject::loadObjects($list, __CLASS__);
    }

    /** @return all messages in the Outbox of $id */
    static function getOutbox($id)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE `from` = ?'.
        ' ORDER BY time_sent DESC';

        $list = Sql::pSelect($q, 'i', $id);

        return SqlObject::loadObjects($list, __CLASS__);
    }


}

?>
