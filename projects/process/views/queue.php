<?php

$session->requireLoggedIn();

switch ($this->view) {

case 'add':
    // From here you ask the server to fetch a remote media for later processing

    function uploadSubmit($p)
    {
        if (!is_url($p['url'])) {
            $error = ErrorHandler::getInstance();
            $error->add('Not an url');
            return false;
        }

        $eventId = TaskQueue::addTask(TASK_FETCH, $p['url']);

        echo '<div class="okay">URL to process has been enqueued.</div><br/>';
        echo ahref('queue/show/'.$eventId, 'Click here').' to perform further actions on this file.';
    }

    $url = 'http://processtest.x/kaos.mp3';

    echo 'Enter resource URL:<br/>';

    $form = new XhtmlForm('ul_pdf');
    $form->addInput('url', 'URL', $url, 60);
    $form->addSubmit('Add');
    $form->setHandler('uploadSubmit');
    echo $form->render();
    break;

case 'overview':

    //FIXME show failed & in progress aswell

    $list = TaskQueue::getList(0, isset($_GET['completed']) ? ORDER_COMPLETED : ORDER_NEW );

//d($list);die;

    if (!empty($list)) {
        foreach ($list as $row) {
            echo '<div class="item">';
            echo '<h2>#'.$row['entryId'].': ';

            switch ($row['orderType']) {
/*
            case TASK_AUDIO_RECODE:
                echo 'Audio recode to <b>"'.$row['orderParams'].'"</b></h2>';
                break;

            case TASK_IMAGE_RECODE:
                echo 'Image recode to <b>"'.$row['orderParams'].'"</b></h2>';
                break;

            case TASK_VIDEO_RECODE:
                echo 'Video recode to <b>"'.$row['orderParams'].'"</b></h2>';
                break;
*/
            case TASK_FETCH:
                echo 'Fetch remote media</h2>';
                echo 'from <b>'.$row['orderParams'].'</b><br/>';
                break;

            case TASK_UPLOAD:
                echo 'Uploaded remote media from client</h2>';
                break;

            case TASK_CONVERT_TO_DEFAULT:
                echo 'Convert media to default type for entry <b>#'.$row['referId'].'</b></h2>';
                if ($row['orderParams']) {
                    $params = unserialize($row['orderParams']);
                    if (!empty($params['callback']))  echo 'Callback: <b>'. urldecode($params['callback']). '</b><br/><br/>';
                    if (!empty($params['watermark'])) echo 'Watermark: <b>'.urldecode($params['watermark']).'</b><br/><br/>';
                }
                if ($row['callback_log']) {
                    echo 'Callback returned:<br/>';
                    echo '<b>'.$row['callback_log'].'</b><br/><br/>';
                }
                break;

            default:
                die('unknown processqueue type: '.$row['orderType']);
            }
            $creator = new User($row['creatorId']);
            echo $row['timeCreated'].' added by '.$creator->render().'<br/><br/>';
            echo 'Attempts: '.$row['attempts'].'<br/><br/>';

            if ($row['orderType'] != TASK_CONVERT_TO_DEFAULT) {
                if ($row['referId']) {
                    echo ahref('queue/status/'.$row['referId'], 'Show file status').'<br/>';
                }

                $file = FileInfo::get($row['referId']);
                if ($file) {
                    echo '<h3>Source file:</h3>';
                    echo 'Mime: '.$file['fileMime'].'<br/>';
                    echo 'Size: '.formatDataSize($file['fileSize']).'<br/>';
                }
            }

            if ($row['orderStatus'] == ORDER_COMPLETED) {
                echo '<b>Order completed</b><br/>';
                echo 'Exec time: '.round($row['timeExec'], 3).'s<br/>';
            }

            echo '</div>';
        }
    } else {
        echo 'Queue is empty.<br/>';
    }

    if (!isset($_GET['completed'])) {
        echo '<a href="?completed">Show completed queue items</a>';
    } else {
        echo '<a href="?">Show pending queue items</a>';
    }
    break;

case 'process':
    // This is intended to be called from cron every minute
//    $session->requireSuperAdmin();

    set_time_limit(0);    //no time limit
    //$config['no_session'] = true;    //force session "last active" update to be skipped
    //$config['debug'] = false;

    $limit = 10;    //do a few encodings each time the script is run

    for ($i = 0; $i < $limit; $i++)
    {
        processQueue();
//        sleep(1);
        echo '.';
    }
    die;

case 'show':
    // owner = event id

    $event = TaskQueue::getEntry($this->owner);
    $fileId = $event['referId'];

    $added = false;
    if (!empty($_POST['dst_audio_fmt'])) {
        $added = addProcessEvent(PROCESSQUEUE_AUDIO_RECODE, $h->session->id, $fileId, $_POST['dst_audio_fmt']);
    } else if (!empty($_POST['dst_image_fmt'])) {
        $added = addProcessEvent(PROCESSQUEUE_IMAGE_RECODE, $h->session->id, $fileId, $_POST['dst_image_fmt']);
    } else if (!empty($_POST['dst_video_fmt'])) {
        $added = addProcessEvent(PROCESSQUEUE_VIDEO_RECODE, $h->session->id, $fileId, $_POST['dst_video_fmt']);
    } else if (isset($_GET['process'])) {
        $added = addProcessEvent(PROCESSPARSE_AND_FETCH, $h->session->id, $fileId);
    } else if (!empty($_POST['unfetched_process']) && $_POST['unfetched_process'] == 'convert') {
        $added = addProcessEvent(PROCESS_CONVERT_TO_DEFAULT, $h->session->id, $this->owner);
    }

    if ($added) {
        echo 'Work order has been enqueued.<br/><br/>';

        echo ahref('queue/status/'.$fileId, 'Show file status').'<br/><br/>';
        echo '<a href="show_queue.php">Show active queue</a>';
        return;
    }

    if ($event['orderType'] == TASK_FETCH) {
        echo '<h1>convert unfetched media</h1>';

        echo 'The following order has not yet been processed and media type cannot be determined.<br/><br/>';

        echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$this->owner.'">';
        echo 'Select preferred action: ';

        echo '<select name="unfetched_process">';
        echo '<option value="convert">Convert to default media type</option>';
        echo '</select>';

        echo '<input type="submit" value="Continue"/>';
        echo '</form>';
    } else {
        echo FileInfo::render($fileId);

        $data = FileInfo::get($fileId);

        if (in_array($data['fileMime'], FileInfo::$audio_mime_types)) {
            echo '<h1>convert audio</h1>';

            echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$this->owner.'">';
            echo 'Select output format: ';

            echo '<select name="dst_audio_fmt">';
            foreach ($h->files->audio_mime_types as $key => $val) {
                echo '<option value="'.$key.'">'.$val.'</option>';
            }
            echo '</select>';

            echo '<input type="submit" value="Continue"/>';
            echo '</form>';
        } else if (in_array($data['fileMime'], FileInfo::$image_mime_types)) {

            echo '<h1>convert image</h1>';

            echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$this->owner.'">';
            echo 'Select output format: ';

            echo '<select name="dst_image_fmt">';
            foreach ($h->files->image_mime_types as $key => $val) {
                echo '<option value="'.$key.'">'.$val.'</option>';
            }
            echo '</select>';

            echo '<input type="submit" value="Continue"/>';
            echo '</form><br/>';

        } else if (in_array($data['fileMime'], FileInfo::$video_mime_types)) {

            echo '<h1>convert video</h1>';

            echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$this->owner.'">';
            echo 'Select output format: ';

            echo '<select name="dst_video_fmt">';
            foreach ($h->files->video_mime_types as $key => $val) {
                echo '<option value="'.$key.'">'.$val.'</option>';
            }
            echo '</select>';

            echo '<input type="submit" value="Continue"/>';
            echo '</form><br/>';

        } else if ($data['fileMime'] == 'application/x-bittorrent') {
            //bittorrent download!
            echo '<h1>bittorent download</h1>';

            //todo: only allow this once. if torrent file already has been downloaded show its content instead
            echo 'Download and store the content of this torrent file?<br/><br/>';
            echo '<a href="?id='.$fileId.'&process">Yes</a><br/><br/>';

            echo '<a href="">No</a>';
        } else if ($data['fileMime'] == 'text/html') {
            //extract video links from the html
            echo '<h1>extract videos from html</h1>';

            echo 'todo: show found video links from html and allow user to choose which ones to queue for download';

            $arr = extract_filenames(file_get_contents($h->files->getFileInfo($fileId)));
            d($arr);
        } else {
            echo 'Dont know how to handle mimetype: '.$data['fileMime'];
        }
    }
    break;

case 'status':
    // show file status

    //TODO: ability to force recalculation of checksums. verify that the file is on disk

    $fileId = $this->owner;

    $data = FileInfo::get($fileId);
    if (!$data) {
        echo '<h1>File dont exist</h1>';
        return;
    }

    $list = TaskQueue::getQueuedTasks($fileId);

    if (!empty($list)) {
        echo '<h1>'.count($list).' queued actions</h1>';
        foreach ($list as $row) {
            echo '<h3>Was enqueued '.ago($row['timeCreated']).' by '.Users::link($row['creatorId']);
            echo ' type='.$row['orderType'].', params='.$row['orderParams'];
            echo '</h3>';
        }
    } else {
        echo '<h1>No queued action</h1>';
    }

    echo 'Process log:<br/>';
    $list = TaskQueue::getLog($fileId);

    echo '<table border="1">';
    echo '<tr>';
    echo '<th>Added</th>';
    echo '<th>Completed</th>';
    echo '<th>Exec time</th>';
    echo '<th>Type</th>';
    echo '<th>Created by</th>';
    echo '</tr>';
    foreach ($list as $row) {
        echo '<tr>';
        echo '<td>'.$row['timeCreated'].'</td>';
        if ($row['orderStatus'] == ORDER_COMPLETED) {
            echo '<td>'.$row['timeCompleted'].'</td>';
            echo '<td>'.round($row['timeExec'], 3).'s</td>';
        } else {
            echo '<td>not done</td>';
            echo '<td>?</td>';
        }
        echo '<td>'.$row['orderType'].'</td>';
        $creator = new User($row['creatorId']);
        echo '<td>'.$creator->render().'</td>';
        //echo $row['orderParams'];
        echo '</tr>';
    }
    echo '</table>';

    echo FileInfo::render($fileId);

    $file = FileInfo::get($fileId);
    if ($file['fileType'] == FILETYPE_CLONE_CONVERTED) {
        echo 'This file is a converted version of the orginal file <a href="'.$_SERVER['PHP_SELF'].'?id='.$file['ownerId'].'">'.$file['ownerId'].'</a><br/>';
    }

/*
    $list = $h->files->getFileList(FILETYPE_CLONE_CONVERTED, $fileId);
    if ($list) echo '<h1>Conversions based on this file</h1>';
    foreach ($list as $row) {
        echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$row['fileId'].'">'.$row['fileId'].'</a> '.formatDataSize($row['fileSize']).' '.$row['fileMime'].'<br/>';
    }
    echo '<br/>';
*/

    echo ahref('queue/show/'.$fileId, 'Create process (media conversion, or further processing)');
    break;


default:
    echo 'No handler for view '.$this->view;

}

?>
