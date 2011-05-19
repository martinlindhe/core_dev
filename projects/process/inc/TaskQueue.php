<?php
/**
 * Task queue class
 *
 */

//STATUS: early draft

//XXX: move to core_dev when matured


//define('TASK_AUDIO_RECODE',  10); ///< Enqueue this
//define('TASK_VIDEO_RECODE',  11); ///< fixme: use
//define('TASK_IMAGE_RECODE',  12); ///< Enqueue this file for recoding/converting to another image format

//define('TASK_UPLOAD',             19); ///< HTTP Post upload
define('TASK_FETCH',              20); ///< Ask the server to download remote media. Parameter is URL

//define('TASK_PARSE_AND_FETCH',    21); ///< Parse the content of the file for further resources (extract media links from html, or download torrent files from .torrent)
//define('TASK_CONVERT_TO_DEFAULT', 22); ///< Convert media to default format


//process order status modes
define('ORDER_NEW',         0);
define('ORDER_EXECUTING',   1);
define('ORDER_COMPLETED',   2);
define('ORDER_FAILED',      3);

class TaskQueue
{
    /**
     * Adds a task to the Task Queue
     *
     * @param $_type type of task
     * @param $param
     * @param $param2
     * @return process event id
     */
    static function addTask($type, $param, $param2 = '')
    {
        if (!is_numeric($type))
            return false;

        $db = SqlHandler::getInstance();
        $session = SessionHandler::getInstance();

        switch ($type)
        {
        case TASK_FETCH:
            // downloads media files; enqueue url for download and processing
            //    $param = url
            $q = 'INSERT INTO tblTaskQueue SET timeCreated=NOW(), creatorId = ?, orderType='.$type.',referId=0, orderStatus = ?, orderParams = ?';
            return $db->pInsert($q, 'iiis', $session->id, $type, ORDER_NEW, $param);
/*
        case PROCESS_UPLOAD:
            //handle HTTP post file upload. is not enqueued
            //    $param is the $_FILES[idx] array

            $exec_start = microtime(true);    //dont count the actual upload time, just the process time
            $newFileId = $h->files->handleUpload($param, FILETYPE_PROCESS);

            $h->files->checksums($newFileId);    //force generation of file checksums

            $exec_time = microtime(true) - $exec_start;
            $q = 'INSERT INTO tblTaskQueue SET timeCreated=NOW(),creatorId='.$session->id.',orderType='.$_type.',referId='.$newFileId.',orderStatus='.ORDER_COMPLETED.',orderParams="'.$db->escape(serialize($param)).'", timeExec="'.$exec_time.'",timeCompleted=NOW()';
            return $db->insert($q);

        case PROCESSQUEUE_AUDIO_RECODE:
        case PROCESSQUEUE_IMAGE_RECODE:
        case PROCESSQUEUE_VIDEO_RECODE:
            //enque file for recoding.
            //    $param = fileId
            //    $param2 = destination format (by extension)
            if (!is_numeric($param)) die;
            $q = 'INSERT INTO tblTaskQueue SET timeCreated=NOW(),creatorId='.$session->id.',orderType='.$_type.',referId='.$param.',orderStatus='.ORDER_NEW.',orderParams="'.$db->escape($param2).'"';
            return $db->insert($q);

        case PROCESS_CONVERT_TO_DEFAULT:
            if (!is_numeric($param)) return false;
            //convert some media to the default media type, can be used to enqueue a conversion of a PROCESSFETCH before the server
            //has fetched it & cant know the media type
            //  $param = eventId we refer to. from this we can extract the future fileId to process
            //    $param2 = array of additional parameters:
            //        'callback' = callback URL on process completion (optional)
            //        'watermark' = URL for watermark file (optional)
            $q = 'INSERT INTO tblTaskQueue SET timeCreated=NOW(),creatorId='.$session->id.',orderType='.$_type.',referId='.$param.',orderStatus='.ORDER_NEW.',orderParams="'.$db->escape(serialize($param2)).'"';
            return $db->insert($q);

        case PROCESS_PARSE_AND_FETCH:
            //parse this resource for further media resources and fetches them
            // $param = fileId
            // use to process a uploaded .torrent file & download it's content
            // or to process a webpage and extract video files from it (including youtube) and download them to the server
            die('not implemented PROCESS_PARSE_AND_FETCH');
            break;
*/
        default:
            die('unknown processqueue type');
            return false;
        }
    }

}

?>
