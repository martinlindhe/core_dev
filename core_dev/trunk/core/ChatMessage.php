<?php
/**
 * $Id$
 *
 */

// STATUS: wip

class ChatMessage
{
    var $id;
    var $room;
    var $from;
    var $msg;
    var $microtime;

    protected static $tbl_name = 'tblChat';

    public static function getRecent($room, $microtime, $limit)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE room = ? AND microtime > ?'.
        ' ORDER BY microtime DESC'.
        ' LIMIT ?';

        $list = Sql::pSelect($q, 'idi', $room, $microtime, $limit);

        return SqlObject::loadObjects($list, __CLASS__);
    }

    static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name, 'id');
    }

}

?>
