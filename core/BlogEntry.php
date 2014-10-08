<?php

/**
 * @author Martin Lindhe, 2009-2012 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

class BlogEntry
{
    var $id;
    var $owner;
    var $category;

    var $subject;
    var $body;

    var $time_created;
    var $time_published;
    var $time_updated;
    var $time_deleted;
    var $deleted_by;

    var $private;   ///< is entry private?

    protected static $tbl_name = 'tblBlogs';

    public static function get($id)
    {
        return SqlObject::getById($id, self::$tbl_name, __CLASS__);
    }

    /**
     * @return list of most recent blogs (by publish time)
     */
    public static function getRecent($count)
    {
        if (!is_numeric($count)) {
            return false;
        }

        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE time_published IS NOT NULL'.
        ' ORDER BY time_published DESC'.
        ' LIMIT '.$count;

        $list = Sql::pSelect($q);

        return SqlObject::loadObjects($list, __CLASS__);
    }

    public function store()
    {
        return SqlObject::store($this, self::$tbl_name, 'id');
    }
}
