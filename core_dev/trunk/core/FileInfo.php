<?php
/**
 * $Id$
 *
 * Shows details of a uploaded file
 *
 */

//STATUS: wip

require_once('constants.php');
require_once('Comment.php');

class FileInfo
{
    public static $image_mime_types = array(
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/bmp'
    ); ///<FIXME remove

    public static $audio_mime_types = array(
    'audio/x-mpeg', 'audio/mpeg',       //.mp3 file. FF2 = 'audio/x-mpeg', IE7 = 'audio/mpeg'
    'audio/x-ms-wma',                   //.wma file. FF2 & IE7 sends this
    'application/x-ogg'                 //.ogg file     - FIXME: IE7 sends mime header 'application/octet-stream' for .ogg
    ); ///<FIXME remove

    public static $video_mime_types = array(
    'video/mpeg',           // .mpg file
    'video/avi',            // .avi file
    'video/x-msvideo',      // .avi file
    'video/x-ms-wmv',       // Microsoft .wmv file
    'video/3gpp',           // .3gp video file
    'video/x-flv',          // Flash video
    'video/mp4',            // MPEG-4 video
    'application/ogg'       // Ogg video
    ); ///<FIXME remove

    public static $document_mime_types = array(
    'text/plain',           // normal text file
    'application/msword',   // Microsoft .doc file
    'application/pdf'       // Adobe .pdf file
    ); ///<FIXME remove

    /**
     * Updates tblFiles data according to file written on disk
     */
    static function updateData_XXXXXXXXXXXXX($id)
    {
        $file = self::getUploadPath($id);
        if (!file_exists($file))
            throw new Exception ('file not found');

        $size = filesize($file);
        $mime = self::getMimeType($file);

        // XXX FIXME: update mediaType according to mime type

        $q = 'UPDATE tblFiles SET fileSize = ?, fileMime = ? WHERE fileId = ?';
        return SqlHandler::getInstance()->pInsert($q, 'isi', $size, $mime, $id);
    }

    static function getMimeType($filename)
    {
    if (!file_exists($filename))
            return false;

    $c = 'file -bi '.escapeshellarg($filename);
    $res = exec($c);

        // $ file -bi file.flv
        // video/x-flv; charset=binary
        $x = explode(';', $res);

    //XXX: use mediaprobe to distinguish between wmv/wma files.
    //FIXME: enhance mediaprobe to handle all media detection and stop use "file"
/*
    if ($x[0] == 'video/x-ms-wmv') {
        $c = 'mediaprobe '.escapeshellarg($filename);
        return exec($c);
    }
*/

    return $x[0];
    }

    static function render($id)
    {
        $file = self::get($id);
        if (!$file)
            return false;

        $session = SessionHandler::getInstance();

        $res =
        'Uploaded at: '.formatTime($file['timeUploaded']).' ('.ago($file['timeUploaded']).')<br/>'.
        'Filename: '.strip_tags($file['fileName']).'<br/>'.
        'Filesize: '.formatDataSize($file['fileSize'], true).'<br/>';

        if (!$session->isAdmin)
            return;

        $res .=
        'Uploader: '.User::get($file['uploaderId'])->name.'<br/>'.
        'Mime type: '.$file['fileMime'].'<br/>';

        if (in_array($file['fileMime'], self::$image_mime_types))
        {
            // Show additional information for image files
            list($img_width, $img_height) = getimagesize( self::getUploadPath($id) );
            $res .= 'Width: '.$img_width.', Height: '.$img_height.'<br/>'. showThumb($id);
        }
        else if (in_array($file['fileMime'], self::$audio_mime_types) && extension_loaded('id3'))
        {
            // Show additional information for audio files
            $res .= '<h3>id3 tag</h3>';
            $id3 = id3_get_tag($this->findUploadPath($_id), ID3_V2_2);
            d($id3);
        }

        $res .= '<br/>';

    $res .= CommentViewer::render(FILE, $id);

/*
        // display checksums, if any
        $arr = $h->files->checksums($id);
        echo '<h3>Checksums</h3>';
        echo '<pre>';
        echo 'sha1: '.$arr['sha1']."\n";
        echo 'md5:  '.$arr['md5']."\n";
        echo '</pre>';
        echo 'Generated at '.$arr['timeCreated'].' in '.$arr['timeExec'].' sec<br/>';
*/

        return $res;
    }

}

function makeThumbLink($id, $title = '', $w = 50, $h = 50)
{
    if (!is_numeric($id))
        return false;

    $str  = '<a href="#" onclick="popup_imgview('.$id.')">';
    $str .= showThumb($id, $title, $w, $h);
    $str .= '</a>';
    return $str;
}

function showThumb($id, $title = '', $w = 50, $h = 50)
{
    $str = '<img src="'.getThumbUrl($id, $w, $h).'" alt="'.strip_tags($title).'" title="'.strip_tags($title).'"/>';
    return $str;
}

function getThumbUrl($id, $width = 50, $height = 50)
{
    if (!is_numeric($width) || !is_numeric($height))
       return;

    if (is_float($width))  $width  = floor($width);
    if (is_float($height)) $height = floor($height);

    $page = XmlDocumentHandler::getInstance();

    return $page->getRelativeUrl().'coredev/image/'.$id.'?w='.$width.'&h='.$height;
}

/*
function makeImageLink($_id, $_title = '')
{
    if (!is_numeric($_id)) return false;

    return '<img id="img_'.$_id.'" src="'.coredev_webroot().'api/file.php?id='.$_id.'" alt="Image" title="'.strip_tags($_title).'"/>';
}
*/

?>
