<?php
/**
 * $Id$
 */

//STATUS: wip

require_once('SqlObject.php');

class File
{
    // XXXX rename db columns
    var $fileId;
    var $fileName;
    var $fileSize;
    var $fileMime;
    var $ownerId;
    var $categoryId;
    var $uploaderId;
    var $uploaderIP;
    var $fileType;
    var $mediaType;
    var $timeUploaded;
    var $timeDeleted;

    static function get($id)
    {
        return SqlObject::getById($id, 'tblFiles', 'File');
    }

}

?>
