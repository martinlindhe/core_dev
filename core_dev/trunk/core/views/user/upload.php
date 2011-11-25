<?php
/**
 * File upload view, used by Html5Uploader
 */


$page->disableDesign();

switch ($this->owner) {
case 'album':
    // upload multiple images to photo album
    // child = album id
    // XXX verify im the owner of album
    // XXX allow only images

    //XXX SECURITY: verify that destination album is owned by current user

    // If the browser supports sendAsBinary () can use the array $_FILES
    if(count($_FILES)>0)
    {
        $fileId = File::importImage(USER, $_FILES['upload'], $this->child);
        echo 'OK:'.$fileId;
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
        $headers = array_change_key_case($headers, CASE_UPPER);
d($headers);
throw new Exception ('XXX anyone need this still?');
        //if(file_put_contents($upload_folder.'/'.$headers['UP-FILENAME'], $content)) {
        //  echo 'done';
        //}
    }
    break;

default:
    throw new Exception ('no such view: '.$this->owner);
}

?>
