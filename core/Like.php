<?php
/**
 * $Id$
 *
 * A user's "like" of an object.
 *
 * @author Martin Lindhe, 2012 <martin@ubique.se>
 */

namespace cd;

class Like
{
    var $id;
    var $owner;  ///< reference to the object being liked
    var $type;   ///< type of object
    var $user;   ///< who likes the object?
    var $time;

    protected static $tbl_name = 'tblLikes';

    public static function isLiked($owner, $type, $user_id)
    {
        $q =
        'SELECT id FROM '.self::$tbl_name.
        ' WHERE owner = ? AND type = ? AND user = ?';
        $id = Sql::pSelectItem($q, 'iii', $owner, $type, $user_id);

        return $id ? true : false;
    }

    public static function set($owner, $type, $user_id)
    {
        $l = new Like();
        $l->owner = $owner;
        $l->type = $type;
        $l->user = $user_id;
        $l->time = sql_datetime( time() );
        $l->store();
    }

    /**
     * @return all likes of object, except by user_id
     */
    public static function getAllExcept($owner, $type, $user_id)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE owner = ? AND type = ? AND user != ?';
        $list = Sql::pSelect($q, 'iii', $owner, $type, $user_id);

        return SqlObject::loadObjects($list, __CLASS__);
    }

    public function store()
    {
        return SqlObject::store($this, self::$tbl_name, 'id');
    }

}
