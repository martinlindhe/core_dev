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
