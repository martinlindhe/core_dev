<?php
/**
 * File upload view, used by Html5Uploader
 */

namespace cd;

$page->disableDesign();

switch ($this->owner) {
case 'album':
    // upload multiple images to photo album
    // child = album id
    // XXX verify im the owner of album

    //XXX SECURITY: verify that destination album is owned by current user

    // If the browser supports sendAsBinary () can use the array $_FILES
    if(count($_FILES)>0)
    {
        $fileId = File::importImage(USER, $_FILES['upload'], $this->child);
        echo 'OK-1:'.$fileId; // XXX debug output
    }
    else if(isset($_GET['up']))
    {
        // If the browser does not support sendAsBinary ()
        if(isset($_GET['base64'])) {
            $content = base64_decode(file_get_contents('php://input'));
        } else {
            $content = file_get_contents('php://input');
        }

        $headers = getallheaders();

        $tmp_file = tempnam('/tmp', 'fileup-');
        file_put_contents($tmp_file, $content);

        $key = array(
            'tmp_name'=>$tmp_file,
            'name'=>$headers['UP-FILENAME'],
            'type'=>$headers['UP-TYPE'],
            'size'=>$headers['UP-SIZE']
        );

        $fileId = File::importImage(USER, $key, $this->child, true);
        echo 'OK-2:'.$fileId; // XXX debug output
    }
    break;

default:
    throw new \Exception ('no such view: '.$this->owner);
}

?>
