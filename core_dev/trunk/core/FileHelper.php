<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('File.php');

class FileHelper
{
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

        $file = new File();
        $file->type = $type;
        $file->uploader = $session->id;
        $file->uploader_ip = client_ip();
        $file->size = $key['size'];
        $file->name = $key['name'];
        $file->mimetype = $key['type'];
        $file->time_uploaded = sql_datetime( time() );
        $id = File::store($file);

        $dst_file = self::getUploadPath($id);

        if (!move_uploaded_file($key['tmp_name'], $dst_file))
            throw new Exception ('Failed to move file from '.$key['tmp_name'].' to '.$dst_file);

        chmod($dst_file, 0777);

        $key['name'] = $dst_file;
        $key['file_id'] = $id;

        return $id;
    }

    static function passthru($id)
    {
        $path = self::getUploadPath($id);

        $f = File::get($id);

        // Displays the file in the browser, and assigns a filename for the browser's "save as..." features
        header('Content-Disposition: inline; filename="'.basename($f->name).'"');
        header('Content-Transfer-Encoding: binary');

        header('Content-Type: '.$f->mimetype);

        if ($f->size)
            header('Content-Length: '. $f->size);

        readfile($path);
    }

    static function delete($id)
    {
        File::delete($id);
        $path = self::getUploadPath($id);
        unlink($path);
    }

}

?>
