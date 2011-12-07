<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip, will replace atom_moderation.php

// ModerationObject types:
define('MODERATE_CHANGE_USERNAME', 1);   // data holds new username
define('MODERATE_UPLOAD',          2);   // reference is a tblFiles.id
define('MODERATE_USER',            3);   // reported user, reference is a tblUsers.id
define('MODERATE_PHOTO',           4);   // reported photo, reference is a tblFiles.id


function getModerationTypes()
{
    return array(
    MODERATE_CHANGE_USERNAME => 'Change username',
    MODERATE_UPLOAD          => 'Uploaded file',
    MODERATE_USER            => 'Reported user',
    MODERATE_PHOTO           => 'Reported photo',
    );
}

class ModerationObject
{
    var $id;
    var $type;
    var $owner;
    var $time_created;
    var $time_handled;
    var $handled_by;
    var $approved;
    var $data;          ///< text  (for UPLOAD=fileId)
    var $reference;     ///< (numeric) used to refer to external object id (if needed)

    protected static $tbl_name = 'tblModerationObjects';

    static function get($id)
    {
        return SqlObject::getById($id, self::$tbl_name, __CLASS__);
    }

    static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name, 'id');
    }

    static function getUnhandled()
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE time_handled IS NULL'.
        ' ORDER BY time_created ASC';

        return SqlObject::loadObjects($q, __CLASS__);
    }

    static function getApproved()
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE time_handled IS NOT NULL AND approved = ?';

        $list = Sql::pSelect($q, 'i', 1);

        return SqlObject::loadObjects($list, __CLASS__);
    }

    static function getDenied()
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE time_handled IS NOT NULL AND approved = ?';

        $list = Sql::pSelect($q, 'i', 0);

        return SqlObject::loadObjects($list, __CLASS__);
    }

    static function getStatusByReference($type, $reference)
    {
        $q =
        'SELECT approved FROM '.self::$tbl_name.
        ' WHERE type = ? AND reference = ?';

        return Sql::pSelectItem($q, 'ii', $type, $reference);
    }

    static function getByReference($type, $reference)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE type = ? AND reference = ?';

        $row = Sql::pSelectRow($q, 'ii', $type, $reference);

        return SqlObject::loadObject($row, __CLASS__);
    }

    static function deleteByReference($type, $reference)
    {
        $q =
        'DELETE FROM '.self::$tbl_name.
        ' WHERE type = ? AND reference = ?';

        return Sql::pDelete($q, 'ii', $type, $reference);
    }

    static function add($type, $reference = 0, $data = '')
    {
        $session = SessionHandler::getInstance();

        $c = new ModerationObject();
        $c->type         = $type;
        $c->owner        = $session->id;
        $c->time_created = sql_datetime( time() );
        $c->data         = $data;
        $c->reference    = $reference;

        self::store($c);
    }

}

?>
