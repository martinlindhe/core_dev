<?php

//TODO: drop this file, separate each view to their own file

switch ($this->view) {
case 'selftest':
    // returns a error code if anything is wrong
    // the idea is to have a external script fetch http://server/coredev/selftest
    // and warn if result != "STATUS:OK"
    $status = 'OK';

    $dir = $page->getUploadPath();
    if ($dir) {
        if (!is_dir($dir))
            $status = 'ERROR: upload dir dont exist';
        else if (!is_writable($dir))
            $status = 'ERROR: upload dir not writable';

        // low disk space in upload directories?
        $df = disk_free_space($dir);
        if ($df < 1024 * 1024 * 32) // 32mb
            $status = 'ERROR: not enough space in upload dir';
    }

    die('STATUS:'.$status);


default:
    throw new Exception ('no such view: '.$this->view);
}

?>
