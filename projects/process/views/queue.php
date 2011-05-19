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

        $eventId = TaskQueue::addTask(PROCESS_FETCH, $p['url']);
//        $eventId = addProcessEvent(PROCESS_FETCH, $p['url']);

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
    $tot_cnt = getProcessQueueCount(0, isset($_GET['completed']) ? ORDER_COMPLETED : ORDER_NEW);

    $list = getProcessQueue(0, $pager['limit'], isset($_GET['completed']) ? ORDER_COMPLETED : ORDER_NEW);

    if (!empty($list)) {
        foreach ($list as $row) {
            echo '<div class="item">';
            echo '<h2>#'.$row['entryId'].': ';

            switch ($row['orderType']) {
                case PROCESSQUEUE_AUDIO_RECODE:
                    echo 'Audio recode to <b>"'.$row['orderParams'].'"</b></h2>';
                    break;

                case PROCESSQUEUE_IMAGE_RECODE:
                    echo 'Image recode to <b>"'.$row['orderParams'].'"</b></h2>';
                    break;

                case PROCESSQUEUE_VIDEO_RECODE:
                    echo 'Video recode to <b>"'.$row['orderParams'].'"</b></h2>';
                    break;

                case PROCESS_FETCH:
                    echo 'Fetch remote media</h2>';
                    echo 'from <b>'.$row['orderParams'].'</b><br/>';
                    break;

                case PROCESS_UPLOAD:
                    echo 'Uploaded remote media from client</h2>';
                    break;

                case PROCESS_CONVERT_TO_DEFAULT:
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
            echo $row['timeCreated'].' added by '.getCustomerName($row['creatorId']).'<br/><br/>';
            echo 'Attempts: '.$row['attempts'].'<br/><br/>';

            if ($row['orderType'] != PROCESS_CONVERT_TO_DEFAULT) {
                if ($row['referId']) {
                    echo '<a href="show_file_status.php?id='.$row['referId'].'">Show file status</a><br/>';
                }

                $file = $h->files->getFileInfo($row['referId']);
                if ($file) {
                    echo '<h3>Source file:</h3>';
                    echo 'Mime: '.$file['fileMime'].'<br/>';
                    echo 'Size: '.formatDataSize($file['fileSize']).'<br/>';
                    echo 'SHA1: '.$h->files->sha1($row['referId']).'<br/>';
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
    //require_once('config.php');
    //$config['debug'] = false;

    $limit = 10;    //do a few encodings each time the script is run

    for ($i = 0; $i < $limit; $i++) {
        processQueue();
        sleep(1);
        echo '.';
    }
    break;

case 'show':
    // owner = event id

    $eventId = $this->owner;

    $event = getProcessQueueEntry($eventId);
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
        $added = addProcessEvent(PROCESS_CONVERT_TO_DEFAULT, $h->session->id, $eventId);
    }

    if ($added) {
        echo 'Work order has been enqueued.<br/><br/>';

        echo '<a href="show_file_status.php?id='.$fileId.'">Show file status</a><br/><br/>';
        echo '<a href="show_queue.php">Show active queue</a>';

        require('design_foot.php');
        die;
    }

    if ($event['orderType'] == PROCESS_FETCH) {
        echo '<h1>convert unfetched media</h1>';

        echo 'The following order has not yet been processed and media type cannot be determined.<br/><br/>';

        echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$eventId.'">';
        echo 'Select preferred action: ';

        echo '<select name="unfetched_process">';
        echo '<option value="convert">Convert to default media type</option>';
        echo '</select>';

        echo '<input type="submit" value="Continue"/>';
        echo '</form>';
    } else {
        showFileInfo($fileId);

        $data = $h->files->getFileInfo($fileId);

        if (in_array($data['fileMime'], $h->files->audio_mime_types)) {
            echo '<h1>convert audio</h1>';

            echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$eventId.'">';
            echo 'Select output format: ';

            echo '<select name="dst_audio_fmt">';
            foreach ($h->files->audio_mime_types as $key => $val) {
                echo '<option value="'.$key.'">'.$val.'</option>';
            }
            echo '</select>';

            echo '<input type="submit" value="Continue"/>';
            echo '</form>';
        } else if (in_array($data['fileMime'], $h->files->image_mime_types)) {

            echo '<h1>convert image</h1>';

            echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$eventId.'">';
            echo 'Select output format: ';

            echo '<select name="dst_image_fmt">';
            foreach ($h->files->image_mime_types as $key => $val) {
                echo '<option value="'.$key.'">'.$val.'</option>';
            }
            echo '</select>';

            echo '<input type="submit" value="Continue"/>';
            echo '</form><br/>';

            echo 'Image view:<br/>';
            echo makeThumbLink($fileId);

        } else if (in_array($data['fileMime'], $h->files->video_mime_types)) {

            echo '<h1>convert video</h1>';

            echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$eventId.'">';
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



default:
    echo 'No handler for view '.$this->view;

}

?>
