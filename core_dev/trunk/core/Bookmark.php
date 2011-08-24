<?php
/**
 * $Id$
 *
 * A bookmark owned by a user, which points to a resource of some kind, such as another user (friend list)
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip

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
        return SqlObject::getById($id, self::$tbl_name, __CLASS__);
    }

    static function getList($owner, $type)
    {
        if (!is_numeric($owner) || !is_numeric($type))
            throw new Exception ('noo');

        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE owner = '.$owner.
        ' AND type = '.$type;

        return SqlObject::loadObjects($q, __CLASS__); // XXX pselect?
    }

    /**
     * Check if an object (owner id & type) is already bookmarked
     */
    static function exists($owner, $type)
    {
        $o = new Bookmark();
        $o->value = $owner;
        $o->type = $type;

        return SqlObject::exists($o, self::$tbl_name);
    }

    static function store($obj)
    {
        return SqlObject::storeUnique($obj, self::$tbl_name);
    }

    /**
     * Creates a new bookmark
     * @param $type
     * @param $object_id   the object who owns the bookmark
     * @param $owner       if not set, owner will be current user
     */
    static function create($type, $object_id, $owner = 0)
    {
        $session = SessionHandler::getInstance();

        $o = new Bookmark();
        $o->type = $type;
        $o->value = $object_id;
        $o->owner = $owner ? $owner : $session->id;
        self::store($o);
    }

    /**
     *
     * @param $type
     * @param $object_id   the object who owns the bookmark
     */
    static function remove($type, $object_id, $owner = 0)
    {
        if (!is_numeric($type) || !is_numeric($object_id) || !is_numeric($owner))
            throw new Exception ('noo');

        $session = SessionHandler::getInstance();

        $q =
        'DELETE FROM '.self::$tbl_name.
        ' WHERE owner = '.($owner ? $owner : $session->id).
        ' AND value = '.$object_id.
        ' AND type = '.$type;

        return Sql::pDelete($q);
    }

}

?>
