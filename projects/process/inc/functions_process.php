<?php
/**
 * $Id$
 *
 * IMPORTANT: for this to work, the sql user need to have LOCK & UNLOCK privilegies
 *
 * mencoder, ffmpeg (recent svn), ffprobe and imagemagick needs to be available
 *
 * this module requires:
 * - php_soap.dll extension (included in windows php dist, disabled by default)
 * - allow_url_fopen = On
 * - always_populate_raw_post_data = On (required by php_soap.dll)
 *
 * suggested config:
 * soap.wsdl_cache_enabled=1
 * soap.wsdl_cache_ttl=172800
 *
 * @author Martin Lindhe, 2008-2011 <martin@startwars.org>
 */

// require_once('atom_customers.php');
// require_once('functions_fileareas.php');
// require_once('functions_image.php');
require_once('HttpClient.php');


//how many enqued items to process at max each time the process_queue.php script is called
//WARNING: keep this a low number unless you are sure what the consequences are
$config['process']['process_limit'] = 3;
$config['process']['retry_limit']   = 50;


/**
 * Updates timeCreated for the entry, to have it retry again later
 *
 * @param $_id entry
 * @param $_delay in seconds
 */
function retryQueueEntry($_id, $_delay)
{
    global $db, $config;
    if (!is_numeric($_id) || !is_numeric($_delay)) return false;

    $curr = TaskQueue::getEntry($_id);
    if ($curr['attempts'] >= $config['process']['retry_limit']) {
        echo "***************************************************************\n";
        echo "***************************************************************\n";
        echo "****** GAVE UP entry ".$_id." after ".$curr['attempts']." attempts  *************\n";
        echo "***************************************************************\n";
        echo "***************************************************************\n";
        markQueue($_id, ORDER_FAILED);
        return false;
    }

    $q = 'UPDATE tblProcessQueue';
    $q .= ' SET timeCreated = DATE_ADD(NOW(), INTERVAL '.$_delay.' SECOND),';
    $q .= ' orderStatus='.ORDER_NEW.',';
    $q .= ' attempts=attempts+1';
    $q .= ' WHERE entryId='.$_id;
    $db->update($q);

    echo "***************************************************************\n";
    echo "***************************************************************\n";
    echo "****** RETRYING entry ".$_id." in ".$_delay." seconds  *************\n";
    echo "***************************************************************\n";
    echo "***************************************************************\n";
}

function storeCallbackData($entryId, $data, $newParams = '')
{
    global $db;
    if (!is_numeric($entryId)) return false;

    $q = 'UPDATE tblProcessQueue555 SET callback_log="'.$db->escape($data).'"';
    if ($newParams) $q .= ', orderParams="'.$db->escape(serialize($newParams)).'"';
    $q .= ' WHERE entryId='.$entryId;
    $db->update($q);
}

/**
 * Takes some work orders from the process queue and performs them
 */
function processQueue()
{
    global $db, $config;

    //Only allows a few work orders being executed at once, so we can do this very often
    if (TaskQueue::getTaskQueueStatusCnt(ORDER_EXECUTING) >= $config['process']['process_limit']) {
        echo "TOO MUCH ACTIVE WORK, ABORTING\n";
        return;
    }

    $job = TaskQueue::getOldestEntry();
    if (!$job)
        return;

    //mark current job as "IN PROGRESS" so another process won't start on it aswell
    TaskQueue::markTask($job['entryId'], ORDER_EXECUTING);

    echo "\n\n-------------\n";
    switch ($job['orderType'])
    {
    case TASK_IMAGE_RECODE:
        echo 'IMAGE RECODE<br/>';
        if (!in_array($job['orderParams'], $h->files->image_mime_types)) {
            echo 'error: invalid mime type<br/>';
            $h->session->log('Process queue error - image conversion destination mimetype not supported: '.$job['orderParams'], LOGLEVEL_ERROR);
            break;
        }
        $newId = $h->files->cloneFile($job['referId'], FILETYPE_CLONE_CONVERTED);

        $exec_start = microtime(true);
        $check = convertImage($h->files->findUploadPath($job['referId']), $h->files->findUploadPath($newId), $job['orderParams']);
        $exec_time = microtime(true) - $exec_start;
        echo 'Execution time: '.shortTimePeriod($exec_time).'<br/>';

        if (!$check) {
            $h->session->log('#'.$job['entryId'].': IMAGE CONVERT failed! format='.$job['orderParams'], LOGLEVEL_ERROR);
            echo 'Error: Image convert failed!<br/>';
            break;
        }
        $h->files->updateFile($newId, $job['orderParams']);
        markQueueCompleted($job['entryId'], $exec_time);
        break;

    case TASK_AUDIO_RECODE:
        //Recodes source audio file into orderParams destination format

        $dst_audio_ok = array('ogg', 'wma', 'mp3');    //FIXME: config item or $h->files->var
        if (!in_array($job['orderParams'], $dst_audio_ok)) {
            echo 'error: invalid mime type<br/>';
            $h->session->log('Process queue error - audio conversion destination mimetype not supported: '.$job['orderParams'], LOGLEVEL_ERROR);
            break;
        }

        $file = $h->files->getFileInfo($job['referId']);
        if (!$file) {
            echo 'Error: no fileentry existed for fileId '.$job['referId'];
            break;
        }
        $newId = $h->files->cloneFile($job['referId'], FILETYPE_CLONE_CONVERTED);

        echo 'Recoding source audio of "'.$file['fileName'].'" ('.$file['fileMime'].') to format '.$job['orderParams']." ...\n";

        switch ($job['orderParams']) {
            case 'application/x-ogg':
                //FIXME hur anger ja dst-format utan filändelse? tvingas göra det i 2 steg nu
                $dst_file = 'tmpfile.ogg';
                $c = '/usr/local/bin/ffmpeg -i "'.$h->files->findUploadPath($job['referId']).'" '.$dst_file;
                break;

            case 'audio/x-ms-wma':
                $dst_file = 'tmpfile.wma';
                $c = '/usr/local/bin/ffmpeg -i "'.$h->files->findUploadPath($job['referId']).'" '.$dst_file;
                break;

            case 'audio/mpeg':
            case 'audio/x-mpeg':
                //fixme: source & destination should not be able to be the same!
                $dst_file = 'tmpfile.mp3';
                $c = '/usr/local/bin/ffmpeg -i "'.$h->files->findUploadPath($job['referId']).'" '.$dst_file;
                break;

            default:
                die('unknown destination audio format: '.$job['orderParams']);
        }

        echo 'Executing: '.$c."\n";
        $exec_time = exectime($c);

        echo 'Execution time: '.shortTimePeriod($exec_time)."\n";

        if (!file_exists($dst_file)) {
            echo '<b>FAILED - dst file '.$dst_file." dont exist!\n";
            break;
        }

        //FIXME: behöver inget rename-steg. kan skriva till rätt output fil i första steget
        rename($dst_file, $h->files->upload_dir.$newId);

        $h->files->updateFile($newId);
        markQueueCompleted($job['entryId'], $exec_time);
        break;

    case TASK_VIDEO_RECODE:
        echo "VIDEO RECODE:\n";

        $exec_start = microtime(true);
        if (convertVideo($job['referId'], $job['orderParams']) === false) {
            markQueue($job['entryId'], ORDER_FAILED);
        } else {
            markQueueCompleted($job['entryId'], microtime(true) - $exec_start);
        }
        break;

    case TASK_FETCH:
        echo "FETCH CONTENT\n";

        $fileName = basename($job['orderParams']); //extract filename part of url, used as "filename" in database

        $http = new HttpClient($job['orderParams']);
        $http->getHead();

        if ($http->getStatus() != 200) {
            // retry in 20 seconds if file is not yet ready
            retryQueueEntry($job['entryId'], 20);
            break;
        }

        $newFileId = FileList::createEntry(FILETYPE_PROCESS, 0, 0, $fileName);

        $c = 'wget '.escapeshellarg($job['orderParams']).' -O '.FileInfo::getUploadPath($newFileId);
        echo "$ ".$c."\n";
        $retval = 0;
        $exec_time = exectime($c, $retval);
        if (!$retval) {
            //TODO: process html document for media links if it is a html document
            TaskQueue::markTaskCompleted($job['entryId'], $exec_time, $newFileId);
            FileInfo::updateData($newFileId);
        } else {
            //wget failed somehow, delay work for 1 minute
            retryQueueEntry($job['entryId'], 60);
            $files->deleteFile($newFileId, 0, true);    //remove failed local file entry
        }
        break;

    case TASK_CONVERT_TO_DEFAULT:
        echo "CONVERT TO DEFAULT\n";
        //referId is entryId of previous proccess queue order
        $params = unserialize($job['orderParams']);
        $prev_job = TaskQueue::getEntry($job['referId']);

        if ($prev_job['orderStatus'] != ORDER_COMPLETED) {
            retryQueueEntry($job['entryId'], 60);
            break;
        }

        $file = $files->getFileInfo($prev_job['referId']);

        $exec_start = microtime(true);

        $newId = false;
        switch ($file['mediaType']) {
            case MEDIATYPE_VIDEO:
                $newId = convertVideo(
                    $prev_job['referId'],    //what file
                    $h->files->default_video,    //destination format (flv)
                    (!empty($params['callback']) ? false : true),//no thumbs on callback files
                    (!empty($params['watermark']) ? $params['watermark'] : '')//specify watermark file to use
                );
                break;

            case MEDIATYPE_AUDIO:
                $newId = convertAudio(
                    $prev_job['referId'],    //what file
                    $h->files->default_audio    //destination format (mp3)
                );
                break;

            default:
                echo "UNKNOWN MEDIA TYPE ".$file['mediaType'].", MIME TYPE ".$file['fileMime'].", CANNOT CONVERT MEDIA!!!\n";
                break;
        }

        if (!$newId) {
            markQueue($job['entryId'], ORDER_FAILED);
            return false;
        }

        markQueueCompleted($job['entryId'], microtime(true) - $exec_start);

        if (empty($params['callback'])) break;

        //'uri' isnt known before the new file is created so it is added at this point
        $uri = $config['core']['full_url'].'api/file.php?id='.$newId;

        $params['callback'] .= (strpos($params['callback'], '?') !== false ? '&' : '?').'uri='.urlencode($uri);

        $data = file_get_contents($params['callback']);

        echo "Performing callback: ".$params['callback']."\n\n";
        echo "Callback script returned:\n".$data;
        storeCallbackData($job['entryId'], $data, $params);
        break;

    default:
        echo "Unknown ordertype: ".$job['orderType']."\n";
        d($job);
        die;
    }

}

/**
 * Converts a video to another video format
 *
 * @return file id of the newly converted video, or false on error
 */
function convertVideo($fileId, $mime, $thumbs = true, $watermark = '')
{
    global $h, $config;
    if (!is_numeric($fileId)) return false;

    //FIXME dont convert uploaded flashvideo TO flashvideo
    $newId = $h->files->cloneFile($fileId, FILETYPE_CLONE_CONVERTED);

    switch ($mime) {
    case 'video/x-flv':
        //Flash video. Confirmed working
        $c = '/usr/local/bin/ffmpeg -i '.$h->files->findUploadPath($fileId).' -f flv -ac 2 -ar 22050 ';
        //XXX: vhook is disabled in ffmpeg, replacement in libavfilter is not yet committed in ffmpeg-svn (2009-09-01)
        ///if ($watermark) $c .= '-vhook "/usr/lib/vhook/watermark.so -m 1 -f '.$watermark.'" ';
        $c .= $h->files->findUploadPath($newId);
        break;

    case 'video/avi':
        //default profile: mpeg4 video (DivX 3) + mp3 audio. should play on any windows/linux/mac without codecs
        $c = 'mencoder '.$h->files->findUploadPath($fileId).' -o '.$h->files->findUploadPath($newId).' -ovc lavc -oac mp3lame -ffourcc DX50 -lavcopts vcodec=msmpeg4';
        die('verify to video/avi');
        break;

    case 'video/mpeg':
        //mpeg2 video, should be playable anywhere
        $c = 'mencoder '.$h->files->findUploadPath($fileId).' -o '.$h->files->findUploadPath($newId).' -ovc lavc -oac mp3lame -lavcopts vcodec=mpeg2video -ofps 25';
        die('verify to video/mpeg');
        break;

    case 'video/x-ms-wmv':
        //Windows Media Video, version 2 (AKA WMV8)
        $c = 'mencoder '.$h->files->findUploadPath($fileId).' -o '.$h->files->findUploadPath($newId).' -ovc lavc -oac mp3lame -lavcopts vcodec=wmv2';
        die('verify to video/x-ms-wmv');
        break;

    case 'video/3gpp':
        //3gp video
        die('add to video/3gpp');
        break;

    default:
        die('unknown destination video format: '.$mime);
    }

    echo "$ ".$c."\n";
    exec($c);

    if (!file_exists($h->files->findUploadPath($newId)) || !filesize($h->files->findUploadPath($newId))) {
        echo "convertVideo() FAILED - dst file ".$h->files->findUploadPath($newId)." dont exist!\n";
        $h->files->deleteFile($newId);
        return false;
    }

    if ($thumbs) {
        generateVideoThumbs($newId);
    }

    $h->files->updateFile($newId);
    return $newId;
}

/**
 * Converts a audio file to another audio format
 *
 * @return file id of the newly converted audio, or false on error
 */
function convertAudio($fileId, $mime)
{
    global $h, $config;
    if (!is_numeric($fileId)) return false;

    //FIXME dont convert uploaded mp3 TO mp3
    $newId = $h->files->cloneFile($fileId, FILETYPE_CLONE_CONVERTED);

    switch ($mime) {
    case 'audio/x-mpeg':
        //MP3 audio
        $c = '/usr/local/bin/ffmpeg -i '.$h->files->findUploadPath($fileId).' -f mp3 -ac 2 -ar 22050 ';
        $c .= $h->files->findUploadPath($newId);
        break;

    default:
        die('unknown destination audio format: '.$mime);
    }

    echo "$ ".$c."\n";
    exec($c);

    if (!file_exists($h->files->findUploadPath($newId)) || !filesize($h->files->findUploadPath($newId))) {
        echo "convertAudio() FAILED - dst file ".$h->files->findUploadPath($newId)." dont exist!\n";
        $h->files->deleteFile($newId);
        return false;
    }

    $h->files->updateFile($newId);
    return $newId;
}

/**
 * Generates image thumbnails from specified video file
 */
function generateVideoThumbs($fileId)
{
    global $h;
    if (!is_numeric($fileId)) return false;

    if ($h->files->lookupMediaType($fileId) != MEDIATYPE_VIDEO) return false;

    $c = 'ffprobe -show_files '.$h->files->findUploadPath($fileId).' 2> /dev/null | grep duration | cut -d= -f2';
    //echo "Executing: ".$c."\n";
    $duration = exec($c);

    $pos10 = $duration * 0.10;
    $pos25 = $duration * 0.25;
    $pos50 = $duration * 0.50;
    $pos75 = $duration * 0.75;
    $pos90 = $duration * 0.90;

    $newId = $h->files->cloneFile($fileId, FILETYPE_CLONE_VIDEOTHUMB10);

    $c = '/usr/local/bin/ffmpeg -i '.$h->files->findUploadPath($fileId).' -ss '.$pos10.' -vframes 1 -f image2 '.$h->files->findUploadPath($newId).' 2> /dev/null';
    echo "$ ".$c."\n";
    exec($c);

    $h->files->updateFile($newId);
}

?>
