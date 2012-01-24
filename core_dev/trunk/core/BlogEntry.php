<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2012 <martin@startwars.org>
 */

//STATUS: wip

require_once('SqlObject.php');

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
     * @return list of most recent blogs
     */
    public static function getRecent($count)
    {
        if (!is_numeric($count))
            return false;

        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' ORDER BY time_created DESC'.
        ' LIMIT '.$count;

        $list = Sql::pSelect($q);

        return SqlObject::loadObjects($list, __CLASS__);
    }

}

?>
