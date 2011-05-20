<?php
/**
 * Task queue class
 *
 */

//STATUS: early draft

//XXX: move to core_dev when matured

require_once('constants.php');


define('TASK_AUDIO_RECODE',  10); ///< Enqueue this
define('TASK_VIDEO_RECODE',  11); ///< fixme: use
define('TASK_IMAGE_RECODE',  12); ///< Enqueue this file for recoding/converting to another image format

define('TASK_UPLOAD',             19); ///< HTTP Post upload
define('TASK_FETCH',              20); ///< Ask the server to download remote media. Parameter is URL

//define('TASK_PARSE_AND_FETCH',    21); ///< Parse the content of the file for further resources (extract media links from html, or download torrent files from .torrent)
define('TASK_CONVERT_TO_DEFAULT', 22); ///< Convert media to default format


// order status
define('ORDER_NEW',         0);
define('ORDER_EXECUTING',   1);
define('ORDER_COMPLETED',   2);
define('ORDER_FAILED',      3);

class TaskQueue
{

    /**
     * Returns the oldest work orders still active for processing
     */
    static function getList($orderType = 0, $orderStatus = ORDER_NEW)
    {
        global $db;
        if (!is_numeric($orderType) || !is_numeric($orderStatus))
            return false;

        $q = 'SELECT * FROM tblTaskQueue';
        $q .= ' WHERE orderStatus = '.$orderStatus;
        if ($orderType) $q .= ' AND orderType = '.$orderType;
        $q .= ' ORDER BY timeCreated DESC';

        return $db->getArray($q);
    }

    /**
     * Returns a process queue entry
     *
     * @param $_id entryId
     */
    static function getEntry($id)
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblTaskQueue WHERE entryId = ?';
        return $db->pSelectRow($q, 'i', $id);
    }

    /**
     * Returns past process queue entries for specified file
     *
     * @param $_id file id
     */
    static function getLog($id)
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblTaskQueue WHERE referId = ? AND orderType != ?';
        return $db->pSelect($q, 'ii', $id, TASK_CONVERT_TO_DEFAULT);
    }

    /**
     * Returns a list of currently enqueued actions to do for referId $id
     * (can be tblFiles.fileId or tblTaskQueue.eventId)
     */
    static function getQueuedTasks($id)
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblTaskQueue WHERE referId = ? AND orderStatus = ? ORDER BY timeCreated ASC';
        return $db->pSelect($q, 'ii', $id, ORDER_NEW);
    }

    /**
     * Returns the number of items in process queue of specified status
     * Particulary useful to see how many orders are currently being processed (ORDER_EXECUTING)
     *
     * @param $_order_status status code
     * @return number of entries of specified status
     */
    static function getTaskQueueStatusCnt($order_status)
    {
        global $db;

        $q = 'SELECT COUNT(*) FROM tblTaskQueue WHERE orderStatus = ?';
        return $db->pSelectItem($q, 'i', $order_status);
    }

     /**
     * Returns the oldest task marked as ORDER_NEW
     */
    static function getOldestEntry()
    {
        global $db;

        //XXX: this little delay is needed because huge uploaded files (100mb+)
        //     is'nt ready for process yet on test box. even after move_uploaded_file()
        //     hopefully it can be removed later on
        $q =
        'SELECT * FROM tblTaskQueue WHERE orderStatus = ?'.
        ' AND timeCreated <= DATE_SUB(NOW(), INTERVAL 5 SECOND)'.
        ' ORDER BY timeCreated ASC,entryId ASC LIMIT 1';

        return $db->pSelectRow($q, 'i', ORDER_NEW);
    }

    /**
     * Marks an object in the process queue with specified status code
     *
     * @param $entryId entry id
     * @param $status status code
     */
    static function markTask($entryId, $status)
    {
        global $db;

        $q = 'UPDATE tblTaskQueue SET orderStatus = ? WHERE entryId = ?';
        $db->pUpdate($q, 'ii', $status, $entryId);
    }

    /**
     * Marks an object in the process queue as completed
     *
     * @param $entryId entry id
     * @param $exec_time time it took to execute this task
     * @param $referId optional, specify if we now refer to a file, used when the process event was to fetch a file
     */
    static function markTaskCompleted($entryId, $exec_time, $referId = 0)
    {
        global $db;
        if (!is_numeric($entryId) || !is_float($exec_time) || !is_numeric($referId)) return false;

        $q = 'UPDATE tblTaskQueue SET orderStatus = ?, timeCompleted=NOW(), timeExec = ?, referId = ? WHERE entryId = ?';
        $db->pUpdate($q, 'isii', ORDER_COMPLETED, $exec_time, $referId, $entryId);
    }

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
            $q = 'INSERT INTO tblTaskQueue SET timeCreated = NOW(), creatorId = ?, orderType = ?, referId = ?, orderStatus = ?, orderParams = ?';
            return $db->pInsert($q, 'iiiis', $session->id, $type, 0, ORDER_NEW, $param);

        case TASK_UPLOAD:
            // handle HTTP post file upload. is not enqueued
            //    $param is the $_FILES[idx] array

            $exec_time = 0 ; // XXXX FIXME measure

            // THE UPLOAD IS ALREADY PROCESSED BY XhtmlForm upload handler
            $fileId = $param['file_id'];

            $q = 'INSERT INTO tblTaskQueue SET timeCreated = NOW(), creatorId = ?, orderType = ?, referId = ?, orderStatus = ?, orderParams = ?, timeExec = ?, timeCompleted = NOW()';
            return $db->pInsert($q, 'iiiiss', $session->id, $type, $fileId, ORDER_COMPLETED, serialize($param), $exec_time );

/*
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
