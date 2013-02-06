<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2013 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

require_once('WikiViewer.php');

class Wiki
{
    var $id;
    var $name;
    var $text;
    var $time_created;
    var $time_edited;
    var $edited_by;         ///< tblUsers.id
    var $locked_by;
    var $time_locked;
    var $revision;          ///< counter

    protected static $tbl_name = 'tblWiki';

    public static function getByName($name)
    {
        return SqlObject::getByField($name, self::$tbl_name, __CLASS__, 'name');
    }

    public static function store($o)
    {
        return SqlObject::store($o, self::$tbl_name, 'name');
    }

}
