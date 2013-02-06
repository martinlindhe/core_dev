<?php
/**
 * $Id$
 *
 * Implement revisioned documents, currently only used by Wiki class
 *
 * @author Martin Lindhe, 2007-2013 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

//revision events:
define('EVENT_TEXT_CHANGED',  1);
define('EVENT_FILE_UPLOADED', 2);
define('EVENT_FILE_DELETED',  3);
define('EVENT_LOCKED',        4);
define('EVENT_UNLOCKED',      5);

class Revision
{
    var $id;
    var $type;              ///< eg. WIKI
    var $owner;             ///< depending on type, eg tblWiki.id
    var $value;
    var $created_by;
    var $time_created;
    var $event;

    protected static $tbl_name = 'tblRevision';

    public static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name);
    }

    public static function getCount($type, $owner)
    {
        $q =
        'SELECT COUNT(*) FROM '.self::$tbl_name.
        ' WHERE type = ? AND owner = ?';
        return Sql::pSelectItem($q, 'ii', $type, $owner);
    }

    public static function getAll($type, $owner)
    {
        $q  =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE type = ? AND owner = ?'.
        ' ORDER BY time_created DESC';
        $list = Sql::pSelect($q, 'ii', $type, $owner);

        return SqlObject::loadObjects($list, __CLASS__);
    }

}
