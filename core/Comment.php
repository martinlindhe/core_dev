<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2011 <martin@ubique.se>
 */

//STATUS: wip

// see views/user/comments.php for a default comment list view

namespace cd;

require_once('SqlObject.php');
require_once('constants.php');

class CommentViewer
{
    public static function render($type, $owner)
    {
        $view = new ViewModel('views/user/comments.php');
        $view->registerVar('type', $type);
        $view->registerVar('owner', $owner);
        return $view->render();
    }
}

class Comment
{
    var $id;
    var $type;          ///< see constants.php
    var $msg;
    var $private;       ///< bool
    var $time_created;
    var $time_deleted;
    var $deleted_by;
    var $owner;         ///< object that the comment belongs to
    var $creator;       ///< userId of creator
    var $creator_ip;    ///< IP of creator (string)

    protected static $tbl_name = 'tblComments';

    public static function getAll()
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' ORDER BY time_created DESC';

        $list = Sql::pSelect($q);

        return SqlObject::ListToObjects($list, __CLASS__);
    }

    public static function getByTypeAndOwner($type, $owner)
    {
        if (!is_numeric($type) || !is_numeric($owner))
            throw new \Exception ('hmmm');

        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE type = ? AND owner = ?'.
        ' ORDER BY time_created DESC';

        $list = Sql::pSelect($q, 'ii', $type, $owner);

        return SqlObject::ListToObjects($list, __CLASS__);
    }

    public static function getByType($type)
    {
        if (!is_numeric($type))
            throw new \Exception ('hmmm');

        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE type = ?'.
        ' ORDER BY time_created DESC';

        $list = Sql::pSelect($q, 'i', $type);

        return SqlObject::ListToObjects($list, __CLASS__);
    }

    /**
     * Helper function to create new comments
     */
    public static function create($type, $owner, $msg, $private = false)
    {
        $session = SessionHandler::getInstance();

        $c = new Comment();
        $c->type = $type;
        $c->owner = $owner;
        $c->msg = $msg;
        $c->private = $private;
        $c->creator = $session->id;
        $c->creator_ip = client_ip();
        $c->time_created = sql_datetime( time() );
        return $c->store();
    }

    public function store()
    {
        return SqlObject::store($this, self::$tbl_name, 'id');
    }

    public static function deleteByOwner($type, $owner)
    {
        $session = SessionHandler::getInstance();

        $q =
        'UPDATE '.self::$tbl_name.
        ' SET deleted_by = ?, time_deleted = NOW()'.
        ' WHERE type = ? AND owner = ?';
        Sql::pUpdate($q, 'iii', $session->id, $type, $owner);
    }

}
