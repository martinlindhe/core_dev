<?php
/**
 *
 */

//STATUS: early draft


define('FILETYPE_PROCESS',				50);
define('FILETYPE_CLONE_CONVERTED',      51);

class FileList
{
    var $type;

    function __construct($type = 0)
    {
        $this->type = $type;
    }

    function get()
    {
        $q = 'SELECT * FROM tblFiles WHERE fileType = ?';
        return SqlHandler::getInstance()->pSelect($q, 'i', $this->type);
    }

}

?>
