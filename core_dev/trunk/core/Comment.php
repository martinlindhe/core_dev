<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: wip

// see views/comments.php for a default comment list view

require_once('SqlObject.php');

class CommentViewer
{
    public static function render($type, $owner)
    {
        $view = new ViewModel('views/comments.php');
        $view->registerVar('type', $type);
        $view->registerVar('owner', $owner);
        return $view->render();
    }
}

class Comment
{
    var $id;
    var $type;
    var $msg;
    var $private;       ///< bool
    var $time_created;
    var $time_deleted;
    var $deleted_by;
    var $owner;         ///< object that the comment belongs to
    var $creator;       ///< userId of creator
    var $creator_ip;    ///< IP of creator

    protected static $tbl_name = 'tblComments';

    public static function get($type, $owner)
    {
        if (!is_numeric($type) || !is_numeric($owner))
            throw new Exception ('hmmm');

        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE type = ? AND owner = ?'.
        ' ORDER BY time_created DESC';

        $list = Sql::pSelect($q, 'ii', $type, $owner);
        return SqlObject::loadObjects($list, __CLASS__);
    }

    public static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name, 'id');
    }

}

?>
