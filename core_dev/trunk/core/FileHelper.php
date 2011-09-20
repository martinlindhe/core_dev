<?php
/**
 * $Id$
 */

require_once('File.php');

class FileHelper
{
    /**
     * @param $key array from $_FILES entry
     * @return file id
     */
    static function import($type, &$key)
    {
        // ignore empty file uploads
        if (!$key['name'])
            return;

        if (!is_uploaded_file($key['tmp_name'])) {
            throw new Exception ('Upload failed for file '.$key['name'] );
            //$error->add('Upload failed for file '.$key['name'] );
            //return;
        }

        $session = SessionHandler::getInstance();
        $page = XmlDocumentHandler::getInstance();

        if (!$page->getUploadRoot())
            throw new Exception ('No upload root configured!');

        $file = new File();
        $file->type = $type;
        $file->uploader = $session->id;
        $file->uploader_ip = client_ip();
        $file->size = $key['size'];
        $file->name = $key['name'];
        $file->mimetype = $key['type'];
        $file->time_uploaded = sql_datetime( time() );
        $id = File::store($file);

        $dst_file = $page->getUploadRoot().'/'.$id;

        if (!move_uploaded_file($key['tmp_name'], $dst_file))
            throw new Exception ('Failed to move file from '.$key['tmp_name'].' to '.$dst_file);

        chmod($dst_file, 0777);

        $key['name'] = $dst_file;
        $key['file_id'] = $id;
    }

}

?>
