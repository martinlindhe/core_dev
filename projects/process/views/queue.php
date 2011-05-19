<?php

$session->requireLoggedIn();

switch ($this->view) {
case 'show':

    //FIXME show failed & in progress aswell
    $tot_cnt = getProcessQueueCount(0, isset($_GET['completed']) ? ORDER_COMPLETED : ORDER_NEW);

    $pager = makePager($tot_cnt, 10);
    echo $pager['head'];

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

case 'add':
    // From here you ask the server to fetch a remote media for later processing

    if (!empty($_POST['url'])) {
        //FIXME: en isURL() funktion som kollar om strängen är en valid url
        $eventId = addProcessEvent(PROCESS_FETCH, $h->session->id, $_POST['url']);

        echo '<div class="okay">URL to process has been enqueued.</div><br/>';
        echo '<a href="http_enqueue.php?id='.$eventId.'">Click here</a> to perform further actions on this file.';
        require('design_foot.php');
        die;
    }

    $url = 'http://localhost/sample.3gp';
    echo 'Enter resource URL:<br/>';
    echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
    echo '<input type="text" name="url" size="60" id="url" value="'.$url.'"/>';
    echo '<img src="'.$config['core']['web_root'].'gfx/arrow_next.png" align="absmiddle" onclick="expand_input(\'url\')"/><br/>';
    echo '<input type="submit" class="button" value="Add"/>';
    echo '</form>';
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


default:
    echo 'No handler for view '.$this->view;

}

?>
