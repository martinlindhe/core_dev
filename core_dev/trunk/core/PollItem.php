<?php
/**
 * $Id$
 *
 * Shows one poll and lets the user interact with it
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: untangle tblRatings stuff from here

require_once('constants.php');

require_once('CategoryList.php');
require_once('Yui3PieChart.php');

class PollItem
{
    var $id;
    var $type;  ///< currently only SITE is used
    var $text;
    var $time_start;
    var $time_end;
    var $time_created;
    var $time_deleted;
    var $owner;   ///< XXX currently unused
    var $created_by;
    var $deleted_by;

    protected static $tbl_name = 'tblPolls';

    static function get($id)
    {
        $q = 'SELECT * FROM tblPolls WHERE id = ? AND deleted_by = ?';
        $row = Sql::pSelectRow($q, 'ii', $id, 0);

        return SqlObject::loadObject($row, __CLASS__);
    }

    static function getPolls($type, $owner = 0)
    {
        $q =
        'SELECT * FROM tblPolls WHERE type = ? AND owner = ? AND deleted_by = ?'.
        ' ORDER BY time_start ASC,text ASC';
        return Sql::pSelect($q, 'iii', $type, $owner, 0);
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

    static function getActivePolls($type, $owner = 0)
    {
        $q =
        'SELECT * FROM tblPolls'.
        ' WHERE type = ? AND owner = ? AND deleted_by = ? AND NOW() BETWEEN time_start AND time_end'.
        ' ORDER BY time_start ASC,text ASC';
        $list = Sql::pSelect($q, 'iii', $type, $owner, 0);

        return SqlObject::loadObjects($list, __CLASS__);
    }

    /**
     * @param $duration_mode "1d", "2w", "2m"
     */
    static function add($type, $owner, $text, $duration_mode = '', $start_mode = '')
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
            'SELECT time_end FROM tblPolls'.
            ' WHERE owner = ? AND deleted_by = ?'.
            ' ORDER BY time_start DESC'.
            ' LIMIT 1';
            $data = Sql::pSelectRow($q, 'ii', $owner, 0);

            $timeStart = $data ? ts($data['timeEnd']) : time();
            break;

        default:
            throw new Exception ('eexp');
        }

        $timeEnd = $timeStart + $length;
        return self::addPollExactPeriod($type, $owner, $text, $timeStart, $timeEnd);
    }

    static function addPollExactPeriod($type, $owner, $text, $time_start, $time_end)
    {
        $session = SessionHandler::getInstance();

        $q =
        'INSERT INTO tblPolls SET type = ?, owner = ?,'.
        ' created_by = ?, text = ?, time_start = ?,'.
        ' time_end = ?, time_created = NOW()';
        return Sql::pInsert($q, 'iiisss', $type, $owner, $session->id, trim($text), sql_datetime($time_start), sql_datetime($time_end));
    }

    static function removePoll($id)
    {
        $session = SessionHandler::getInstance();
        $q = 'UPDATE tblPolls SET deleted_by = ?, time_deleted = NOW() WHERE id = ?';
        Sql::pUpdate($q, 'ii', $session->id, $id);
    }

    static function updatePoll($id, $text, $time_start = '', $time_end = '')
    {
        $add_string = '';

        if (!empty($time_start)) $add_string .= ', time_start = "'.$time_start.'"';
        if (!empty($time_end)) $add_string .= ', time_end = "'.$time_end.'"';

        $db = SqlHandler::getInstance();
        $q = 'UPDATE tblPolls SET text="'.$db->escape($text).'"'.$add_string.' WHERE id='.$id;
        $db->update($q);
    }

}

?>
