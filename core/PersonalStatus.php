<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2012 <martin@ubique.se>
 */

namespace cd;

class PersonalStatus
{
    var $id;
    var $owner;
    var $text;
    var $time_saved;

    protected static $tbl_name = 'tblPersonalStatus';

    public static function get($id)
    {
        return SqlObject::getById($id, self::$tbl_name, __CLASS__);
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
        return $status->store();
    }

    public function store()
    {
        return SqlObject::store($this, self::$tbl_name, 'id');
    }

    public static function delete($id)
    {
        SqlObject::deleteById($id, self::$tbl_name, 'id');
    }

}
