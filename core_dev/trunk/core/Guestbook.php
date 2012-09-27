<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

class Guestbook
{
    var $id;
    var $owner;        ///< userid owning the entry
    var $creator;      ///< userid who wrote the entry
    var $time_created;
    var $body;

    protected static $tbl_name = 'tblGuestbook';

    public static function getEntries($user_id)
    {
        if (!is_numeric($user_id))
            throw new Exception ('ehm');

        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE owner = ?'.
        ' ORDER BY time_created DESC';
        $list = Sql::pSelect($q, 'i', $user_id);

        return SqlObject::loadObjects($list, __CLASS__);
    }

    static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name, 'id');
    }

}

?>
