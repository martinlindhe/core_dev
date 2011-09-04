<?php
/**
 * $Id$
 *
 */

//STATUS: early!! will eventually replace atom_moderation.php

// ModerationObject types:
define('MODERATE_CHANGE_USERNAME', 1);

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

    static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name, 'id');
    }

}

?>
