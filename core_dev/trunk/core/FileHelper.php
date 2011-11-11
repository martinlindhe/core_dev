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
     * @param $key array from $_FILES entry
     * @return file id
     */
    public static function import($type, &$key, $category = 0)
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
        $file->category = $category;
        $file->time_uploaded = sql_datetime( time() );
        $id = File::store($file);

        $dst_file = File::getUploadPath($id);

        if (!move_uploaded_file($key['tmp_name'], $dst_file))
            throw new Exception ('Failed to move file from '.$key['tmp_name'].' to '.$dst_file);

        chmod($dst_file, 0777);

        $key['name'] = $dst_file;
        $key['file_id'] = $id;

        return $id;
    }

    public static function passthru($id)
    {
        $path = File::getUploadPath($id);

        $f = File::get($id);

        // Displays the file in the browser, and assigns a filename for the browser's "save as..." features
        header('Content-Disposition: inline; filename="'.basename($f->name).'"');
        header('Content-Transfer-Encoding: binary');

        $page = XmlDocumentHandler::getInstance();

        $page->disableDesign();
        $page->setMimeType( $f->mimetype );

        if ($f->size)
            header('Content-Length: '. $f->size);

        readfile($path);
    }


    /** Updates tblFiles entry with current file size & mime type, useful after Image resize / rotate etc */
    public static function sync($id)
    {
        $name = File::getUploadPath($id);
        if (!file_exists($name))
            throw new Exception ('cant sync nonexisting file, what do???');

        $size = filesize($name);
        $mime = get_mimetype_of_file($name);

        $q =
        'UPDATE tblFiles'.
        ' SET size = ?, mimetype = ?'.
        ' WHERE id = ?';
        Sql::pUpdate($q, 'isi', $size, $mime, $id);
    }

}

?>
