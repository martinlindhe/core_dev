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

    /**
     * @param $key array from $_FILES entry
     * @return file id
     */
    static function importUpload($type, &$key)
    {
        // ignore empty file uploads
        if (!$key['name'])
            return;

        if (!is_uploaded_file($key['tmp_name'])) {
            throw new Exception ('Upload failed for file '.$key['name'] );
            //$error->add('Upload failed for file '.$key['name'] );
            //return;
        }

        $page = XmlDocumentHandler::getInstance();
        $session = SessionHandler::getInstance();
        $db = SqlHandler::getInstance();

        $q = 'INSERT INTO tblFiles SET timeUploaded = NOW(), fileType = ?, uploaderId = ?, fileSize = ?, fileName = ?, fileMime = ?';
        $id = $db->pInsert($q, 'iiiss', $type, $session->id, $key['size'], $key['name'], $key['type']);

        $dst_file = $page->getUploadRoot().'/'.$id;

        if (!move_uploaded_file($key['tmp_name'], $dst_file))
            throw new Exception ('Failed to move file from '.$key['tmp_name'].' to '.$dst_file);

        chmod($dst_file, 0777);

        $key['name'] = $dst_file;
        $key['file_id'] = $id;
    }

}

?>
