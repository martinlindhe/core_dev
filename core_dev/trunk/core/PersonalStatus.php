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

}

?>
