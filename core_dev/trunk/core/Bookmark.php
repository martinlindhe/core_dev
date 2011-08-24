<?php
/**
 * $Id$
 *
 * A bookmark owned by a user, which points to a resource of some kind, such as another user (friend list)
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO 1: easy method for "is object already bookmarked?"
//TODO 2: easy methdo for "remove bookmark for object"

// use these, or any numbers >= 100 for application specific needs
define('BOOKMARK_CUSTOM',  100);
define('BOOKMARK_CUSTOM2', 101);
define('BOOKMARK_CUSTOM3', 102);

class Bookmark
{
    var $id;
    var $owner;
    var $type;
    var $value;

    protected static $tbl_name = 'tblBookmarks';

    static function get($id)
    {
        return SqlObject::getById($id, self::$tbl_name, 'Bookmark');
    }

    static function getList($owner, $type)
    {
        if (!is_numeric($owner) || !is_numeric($type))
            throw new Exception ('noo');

        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE owner = '.$owner.
        ' AND type = '.$type;

        return SqlObject::loadObjects($q, 'Bookmark'); // XXX pselect?
    }

    static function store($obj)
    {
        return SqlObject::storeUnique($obj, self::$tbl_name);
    }

}

?>
