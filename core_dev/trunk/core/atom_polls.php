<?php
/**
 * $Id$
 *
 * Implements a couple of different types of polling functionality
 * used by "site polls" and "polls attached to news articles"
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: refactoring into PollWidget, PollManager




/**
 * XXX
 */
function addPollExactPeriod($_type, $ownerId, $text, $_start, $_end)
{
    global $h, $db;
    if (!is_numeric($_type) || !is_numeric($ownerId)) return false;

    $text = $db->escape(trim($text));

    $_start = sql_datetime(strtotime($_start));
    $_end = sql_datetime(strtotime($_end));

    $q = 'INSERT INTO tblPolls SET ownerId='.$ownerId.',pollType='.$_type.',pollText="'.$text.'",createdBy='.$h->session->id.',timeCreated=NOW(),timeStart="'.$db->escape($_start).'",timeEnd="'.$db->escape($_end).'"';
    $db->insert($q);
}

/**
 * Update poll
 */
function updatePoll($_type, $_id, $_text, $timestart = '', $timeend = '')
{
    global $db;
    if (!is_numeric($_type) || !is_numeric($_id)) return false;

    $add_string = '';

    if (!empty($timestart)) $add_string .= ', timeStart = "'.$timestart.'"';
    if (!empty($timeend)) $add_string .= ', timeEnd = "'.$timeend.'"';

    $q = 'UPDATE tblPolls SET pollText="'.$db->escape($_text).'"'.$add_string.' WHERE pollType='.$_type.' AND pollId='.$_id;
    $db->update($q);
}

/**
 * Get active polls
 */
function getActivePolls($_type, $ownerId = 0, $limit = 0)
{
    global $db;
    if (!is_numeric($_type) || !is_numeric($ownerId) || !is_numeric($limit)) return false;

    $q = 'SELECT * FROM tblPolls WHERE pollType='.$_type.' AND ownerId='.$ownerId.' AND deletedBy=0 AND NOW() BETWEEN timeStart AND timeEnd ORDER BY timeStart ASC,pollText ASC';
    if ($limit) $q .= ' LIMIT 0,'.$limit;
    return $db->getArray($q);
}

/**
 * Add poll vote
 */
function addPollVote($_id, $voteId)
{
    global $h, $db;
    if (!is_numeric($_id) || !is_numeric($voteId)) return false;

    $q = 'SELECT userId FROM tblPollVotes WHERE pollId='.$_id.' AND userId='.$h->session->id;
    if ($db->getOneItem($q)) return false;

    $q = 'INSERT INTO tblPollVotes SET userId='.$h->session->id.',pollId='.$_id.',voteId='.$voteId;
    $db->insert($q);
    return true;
}

/**
 * Has current user answered specified poll?
 */
function hasAnsweredPoll($_id)
{
    global $h, $db;
    if (!is_numeric($_id) || !$h->session->id) return false;

    $q = 'SELECT pollId FROM tblPollVotes WHERE userId='.$h->session->id.' AND pollId='.$_id;
    if ($db->getOneItem($q)) return true;
    return false;
}

/**
 * Get statistics for specified poll
 */
function getPollStats($_id)
{
    global $db;
    if (!is_numeric($_id)) return false;

    $q  = 'SELECT t1.categoryName, ';
    $q .= '(SELECT COUNT(*) FROM tblPollVotes WHERE voteId=t1.categoryId) AS cnt ';
    $q .= 'FROM tblCategories AS t1 ';
    $q .= 'WHERE t1.ownerId='.$_id.' AND t1.categoryType='.CATEGORY_POLL;
    return $db->getArray($q);
}

/**
 * Remove poll
 */
function removePoll($_type, $_id)
{
    global $h, $db;
    if (!$h->session->isAdmin || !is_numeric($_type) || !is_numeric($_id)) return false;

    $q = 'UPDATE tblPolls SET deletedBy='.$h->session->id.',timeDeleted=NOW() WHERE pollType='.$_type.' AND pollId='.$_id;
    $db->update($q);
}

/**
 * Shows active polls of specified type
 *
 * @param $_type type of poll
 */
function showPolls($_type)
{
    global $db;
    if (!is_numeric($_type)) return false;

    $list = getActivePolls($_type);

    if (!$list) {
        echo t('No polls are currently active');
        return;
    }

    foreach ($list as $row) {
        echo poll($row['pollType'], $row['pollId']);
    }
}

/**
 * Helper function for displaying polls attached to a news article
 */
function showAttachedPolls($_type, $_owner)
{
    global $h, $db;
    if (!$h->session->isAdmin || !is_numeric($_type) || !is_numeric($_owner)) return false;

    $list = getPolls($_type, $_owner);
    if (!$list) return false;

    $res = '';

    foreach ($list as $row) {
        $res .= '<div class="poll_attached">';
        $res .= '<b>'.$row['pollText'].'</b><br/>';
        $answers = getCategories(CATEGORY_POLL, $row['pollId']);
        foreach ($answers as $an) {
            $res .= $an['categoryName'].' ';
        }
        $res .= '<a href="">See results</a>';
        $res .= '</div>';
    }

    return $res;
}
?>
