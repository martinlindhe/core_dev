<?php
/**
 * $Id$
 *
 * Helper function to manage polls, used by admin_polls.php & functions_news.php
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip, half working...

//TODO: rework how addPoll handles start modes

//TODO later: ability to export results (csv, xls, ...?)


require_once('PollWidget.php');

require_once('CategoryList.php');

class PollManager extends PollWidget
{
    /**
     * @param $duration_mode "day", "week" or numerical number of days
     */
    static function addPoll($ownerId, $text, $duration_mode = '', $start_mode = '')
    {
        ///XXX FIXME: parse duration string in $duration_mode, eg "2w", "1m", "3d"
        switch ($duration_mode) {
        case 'day'; $length = 1; break;
        case 'week': $length = 7; break;
        case 'month': $length = 30; break;
        case '': break;
        default:
            if (is_numeric($duration_mode)) $length = $duration_mode;
            else die('eep addpoll');
        }

        switch ($start_mode) {
        case 'thismonday':
            $dayofweek = date('N');
            $mon = date('n');
            $day = date('j');
            $thismonday = mktime(6, 0, 0, $mon, $day - $dayofweek + 1);    //06:00 Monday current week
            $timeStart = ',timeStart="'.sql_datetime($thismonday).'"';
            $timeEnd = ',timeEnd=DATE_ADD(timeStart, INTERVAL '.$length.' DAY)';
            break;

        case 'nextmonday':
            $dayofweek = date('N');
            $mon = date('n');
            $day = date('j');
            $nextmonday = mktime(6, 0, 0, $mon, $day - $dayofweek + 1 + 7);    //06:00 Monday next week
            $timeStart = ',timeStart="'.sql_datetime($nextmonday).'"';
            $timeEnd = ',timeEnd=DATE_ADD(timeStart, INTERVAL '.$length.' DAY)';
            break;

        case 'nextfree':
            $q = 'SELECT timeEnd FROM tblPolls WHERE ownerId = ? AND deletedBy=0 ORDER BY timeStart DESC LIMIT 1';
            $data = $db->pSelectRow($q, 'i', $ownerId);

            $timeStart = ',timeStart='. $data ? '"'.$data['timeEnd'].'"' : 'NOW()';
            $timeEnd = ',timeEnd=DATE_ADD(timeStart, INTERVAL '.$length.' DAY)';
            break;

        case '':
            $timeStart = '';
            $timeEnd = '';
            break;

        default: die('eexp');
        }

        //XXX cleanup timestamps & call addPollExactPeriod() instead

        $session = SessionHandler::getInstance();

        $q = 'INSERT INTO tblPolls SET ownerId = ?, pollText = ?, createdBy = ?, timeCreated=NOW()'.$timeStart.$timeEnd;
        return SqlHandler::getInstance()->pInsert($q, 'isi', $ownerId, $text, $session->id);
    }

    static function addPollExactPeriod($ownerId, $text, $time_start, $time_end)
    {
        $text = trim($text);

        $time_start = sql_datetime($start));
        $time_end   = sql_datetime($end));

        $q = 'INSERT INTO tblPolls SET ownerId = ?, createdBy = ?, pollText = ?, timeStart = ?, timeEnd = ?, timeCreated=NOW()';
        SqlHandler::getInstance()->pInsert($q, 'iisss', $owner, $session->id, $text, $time_start, $time_end);
    }

    static function removePoll($id)
    {
        $session = SessionHandler::getInstance();
        $q = 'UPDATE tblPolls SET deletedBy = ?, timeDeleted=NOW() WHERE pollId = ?';
        SqlHandler::getInstance()->pUpdate($q, 'ii', $session->id, $id);
    }

    static function updatePoll($id, $_text, $timestart = '', $timeend = '')
    {
        $add_string = '';

        if (!empty($timestart)) $add_string .= ', timeStart = "'.$timestart.'"';
        if (!empty($timeend)) $add_string .= ', timeEnd = "'.$timeend.'"';

        $db = SqlHandler::getInstance();

        $q = 'UPDATE tblPolls SET pollText ="'.$db->escape($_text).'"'.$add_string.' WHERE pollId='.$id;
        $db->update($q);
    }

    function render()
    {
        $view = new ViewModel('views/admin_PollManager.php', $this);
        return $view->render();
    }

}

?>
