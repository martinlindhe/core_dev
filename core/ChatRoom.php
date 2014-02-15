<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

require_once('ChatMessage.php');

class ChatRoom
{
    var $id;
    var $name;
    var $locked_by;   ///< tblUsers.id
    var $time_locked;

    protected static $tbl_name = 'tblChatRooms';

    public static function get($id)
    {
        return SqlObject::getById($id, self::$tbl_name, __CLASS__);
    }

    public static function getByName($s)
    {
        if (!$s)
            return false;

        return SqlObject::getAllByField('name', $s, self::$tbl_name, __CLASS__);
    }

    public static function getList()
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' ORDER BY name ASC';
        return SqlObject::loadObjects($q, __CLASS__);
    }

    public function store()
    {
        return SqlObject::store($this, self::$tbl_name);
    }

    public static function remove($id)
    {
        return SqlObject::deleteById($id, self::$tbl_name);
    }

}
