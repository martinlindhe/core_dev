<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

// STATUS: wip

namespace cd;

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

    public static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name, 'id');
    }

    /**
     * Removes all chat messages from a chatroom
     */
    public static function deleteByRoom($id)
    {
        $q =
        'DELETE FROM '.self::$tbl_name.
        ' WHERE room = ?';
        Sql::pDelete($q, 'i', $id);
    }

}

?>
