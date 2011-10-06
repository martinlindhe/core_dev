<?php
/**
 * $Id$
 */

//STATUS: wip

class ChatRoom
{
    var $id;
    var $name;

    protected static $tbl_name = 'tblChatRooms';

    static function get($id)
    {
        return SqlObject::getById($id, self::$tbl_name, __CLASS__);
    }

    static function getByName($s)
    {
        if (!$s)
            return false;

        return SqlObject::getAllByField('name', $s, self::$tbl_name, __CLASS__);
    }

    static function getList()
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' ORDER BY name ASC';
        return SqlObject::loadObjects($q, __CLASS__);
    }

}

?>
