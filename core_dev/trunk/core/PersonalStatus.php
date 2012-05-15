<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2012 <martin@startwars.org>
 */

class PersonalStatus
{
    var $id;
    var $owner;
    var $text;
    var $time_saved;

    protected static $tbl_name = 'tblPersonalStatus';

    public static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name, 'id');
    }

    public static function delete($id)
    {
        SqlObject::deleteById($id, self::$tbl_name, 'id');
    }

    public static function getByOwner($id)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE owner = ?'.
        ' ORDER BY time_saved DESC'.
        ' LIMIT 1';

        $res = Sql::pSelectRow($q, 'i', $id);

        return SqlObject::loadObject($res, __CLASS__);
    }

    public static function setStatus($user_id, $text)
    {
        $status = new PersonalStatus();
        $status->owner = $user_id;
        $status->text = $text;
        $status->time_saved = sql_datetime( time() );
        self::store($status);
    }

}

?>
