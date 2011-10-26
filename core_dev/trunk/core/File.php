<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('SqlObject.php');

define('FILETYPE_PROCESS',            50);
define('FILETYPE_CLONE_CONVERTED',    51);

class File
{
    var $id;
    var $type;
    var $name;
    var $size;
    var $mimetype;
    var $owner;
    var $category;
    var $uploader;
    var $uploader_ip;
    var $time_uploaded;
    var $time_deleted;

    protected static $tbl_name = 'tblFiles';

    /**
     * @return full local path to uploaded file
     */
    public static function getUploadPath($id)
    {
        $page = XmlDocumentHandler::getInstance();

        if (!$page->getUploadRoot())
            throw new Exception ('No upload root configured!');

        return $page->getUploadRoot().'/'.$id;
    }

    public static function get($id)
    {
        return SqlObject::getById($id, self::$tbl_name, __CLASS__, 'id');
    }

    public static function getList()
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' ORDER BY time_uploaded ASC';
        return SqlObject::loadObjects($q, __CLASS__);
    }

    public static function getByType($type)
    {
        $q = 'SELECT * FROM '.self::$tbl_name.' WHERE type = ?';
        $list = SqlHandler::getInstance()->pSelect($q, 'i', $type);

        return SqlObject::loadObjects($list, __CLASS__);
    }

    public static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name, 'id');
    }

    public static function delete($id)
    {
        SqlObject::deleteById($id, self::$tbl_name, 'id');
    }
}

?>
