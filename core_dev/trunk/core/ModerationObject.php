<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip! will  replace atom_moderation.php

// ModerationObject types:
define('MODERATE_CHANGE_USERNAME', 1);   // data holds new username
define('MODERATE_UPLOAD',          2);   // data is a tblFiles.id


function getModerationTypes()
{
    return array(
    MODERATE_CHANGE_USERNAME => 'Change username',
    MODERATE_UPLOAD          => 'Uploaded file',
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
    var $data;
    var $data2;

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

    static function add($type, $data, $data2 = '')
    {
        $session = SessionHandler::getInstance();

        $c = new ModerationObject();
        $c->type         = $type;
        $c->owner        = $session->id;
        $c->time_created = sql_datetime( time() );
        $c->data         = $data;
        $c->data2        = $data2;

        self::store($c);
    }

}

?>
