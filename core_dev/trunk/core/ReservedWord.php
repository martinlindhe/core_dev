<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: early wip

namespace cd;

// types:
define('RESERVED_USERNAME', 1);

class ReservedWord
{
    var $id;
    var $type;
    var $value;

    protected static $tbl_name = 'tblReservedWords';

    public static function getAll($type)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE type = ?'.
        ' ORDER BY value ASC';

        $res = Sql::pSelect($q, 'i', $type);

        return SqlObject::loadObjects($res, __CLASS__);
    }

    public static function get($id)
    {
        return SqlObject::getById($id, self::$tbl_name, __CLASS__);
    }

    public static function isReservedUsername($s)
    {
        $s = trim($s);

        $q =
        'SELECT COUNT(*) FROM '.self::$tbl_name.
        ' WHERE type = ?'.
        ' AND value = ?';
        $val = Sql::pSelectItem($q, 'is', RESERVED_USERNAME, $s);
        if ($val)
            return true;

        return false;
    }

}

?>
