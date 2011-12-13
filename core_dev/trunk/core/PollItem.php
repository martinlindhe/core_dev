<?php
/**
 * $Id$
 *
 * Shows one poll and lets the user interact with it
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: cleanup internal class to use SqlObject
//TODO: rename table columns

require_once('constants.php');

require_once('CategoryList.php');
require_once('Yui3PieChart.php');

class PollItem
{
    static function get($id)
    {
        $q = 'SELECT * FROM tblPolls WHERE pollId = ? AND deletedBy = ?';
        return Sql::pSelectRow($q, 'ii', $id, 0);
    }

    static function getPolls($type, $ownerId = 0)
    {
        $q =
        'SELECT * FROM tblPolls WHERE type = ? AND ownerId = ? AND deletedBy = ?'.
        ' ORDER BY timeStart ASC,pollText ASC';
        return Sql::pSelect($q, 'iii', $type, $ownerId, 0);
    }

    /** Get statistics for specified poll */
    static function getPollStats($id)
    {
        $q  =
        'SELECT t1.categoryName, '.
            '(SELECT COUNT(*) FROM tblRatings WHERE value=t1.categoryId) AS cnt'.
        ' FROM tblCategories AS t1'.
        ' WHERE t1.ownerId = ?';
        return Sql::pSelectMapped($q, 'i', $id);
    }

    /** Has current user answered specified poll? */
    static function hasAnsweredPoll($id)
    {
        $session = SessionHandler::getInstance();
        if (!$session->id)
            return true;

        $q = 'SELECT owner FROM tblRatings WHERE userId = ? AND owner = ?';
        if (Sql::pSelectItem($q, 'ii', $session->id, $id))
            return true;

        return false;
    }

    /** Votes for a poll */
    static function addVote($id, $value)
    {
        $session = SessionHandler::getInstance();
        if (!$session->id)
            return false;

        if (self::hasAnsweredPoll($id))
            return false;

        $q = 'INSERT INTO tblRatings SET type = ?, owner = ?, userId = ?, value = ?, timestamp = NOW()';
        Sql::pInsert($q, 'iiii', POLL, $id, $session->id, $value);
        return true;
    }

    static function getActivePolls($type, $ownerId = 0)
    {
        $q =
        'SELECT * FROM tblPolls'.
        ' WHERE type = ? AND ownerId = ? AND deletedBy = ? AND NOW() BETWEEN timeStart AND timeEnd'.
        ' ORDER BY timeStart ASC,pollText ASC';
        return Sql::pSelect($q, 'iii', $type, $ownerId, 0);
    }

    /**
     * @param $duration_mode "1d", "2w", "2m"
     */
    static function add($type, $ownerId, $text, $duration_mode = '', $start_mode = '')
    {
        $length = parse_duration($duration_mode);
        if (!$length)
            throw new Exception ('odd duration '.$duration_mode);

        switch ($start_mode) {
        case 'thismonday':
            $dayofweek = date('N');
            $mon = date('n');
            $day = date('j');
            $timeStart = mktime(6, 0, 0, $mon, $day - $dayofweek + 1);    //06:00 Monday current week
            break;

        case 'nextmonday':
            $dayofweek = date('N');
            $mon = date('n');
            $day = date('j');
            $timeStart = mktime(6, 0, 0, $mon, $day - $dayofweek + 1 + 7);    //06:00 Monday next week
            break;

        case 'nextfree':
            $q =
            'SELECT timeEnd FROM tblPolls'.
            ' WHERE ownerId = ? AND deletedBy = ?'.
            ' ORDER BY timeStart DESC'.
            ' LIMIT 1';
            $data = Sql::pSelectRow($q, 'ii', $ownerId, 0);

            $timeStart = $data ? ts($data['timeEnd']) : time();
            break;

        default:
            throw new Exception ('eexp');
        }

        $timeEnd = $timeStart + $length;
        return self::addPollExactPeriod($type, $ownerId, $text, $timeStart, $timeEnd);
    }

    static function addPollExactPeriod($type, $owner, $text, $time_start, $time_end)
    {
        $session = SessionHandler::getInstance();

        $q =
        'INSERT INTO tblPolls SET type = ?, ownerId = ?,'.
        ' createdBy = ?, pollText = ?, timeStart = ?,'.
        ' timeEnd = ?, timeCreated=NOW()';
        return Sql::pInsert($q, 'iiisss', $type, $owner, $session->id, trim($text), sql_datetime($time_start), sql_datetime($time_end));
    }

    static function removePoll($id)
    {
        $session = SessionHandler::getInstance();
        $q = 'UPDATE tblPolls SET deletedBy = ?, timeDeleted=NOW() WHERE pollId = ?';
        Sql::pUpdate($q, 'ii', $session->id, $id);
    }

    static function updatePoll($id, $_text, $timestart = '', $timeend = '')
    {
        $add_string = '';

        if (!empty($timestart)) $add_string .= ', timeStart = "'.$timestart.'"';
        if (!empty($timeend)) $add_string .= ', timeEnd = "'.$timeend.'"';

        $db = SqlHandler::getInstance();
        $q = 'UPDATE tblPolls SET pollText="'.$db->escape($_text).'"'.$add_string.' WHERE pollId='.$id;
        $db->update($q);
    }

}

?>
