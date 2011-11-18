<?php
/**
 * $Id$
 *
 * A bookmark owned by a user, which points to a resource of some kind,
 * such as another user (favorites), blocked users
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip

define('BOOKMARK_USERBLOCK',    1);  ///< blocked user
define('BOOKMARK_FAVORITEUSER', 2);  ///< "favorite user" (like friends but no friend-request)

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

    static function getList($type, $owner)
    {
        if (!is_numeric($owner) || !is_numeric($type))
            throw new Exception ('noo');

        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE owner = ? AND type = ?';

        $list = Sql::pSelect($q, 'ii', $owner, $type);

        return SqlObject::loadObjects($list, __CLASS__);
    }

    /**
     * Check if an object (owner id & type) is already bookmarked
     */
    static function exists($type, $value, $owner = 0)
    {
        $o = new Bookmark();
        $o->type  = $type;
        $o->value = $value;
        $o->owner = $owner;

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
        $o->type  = $type;
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
        ' WHERE owner = ?'.
        ' AND value = ?'.
        ' AND type = ?';

        return Sql::pDelete($q, 'iii', ($owner ? $owner : $session->id), $object_id, $type );
    }

}

?>
