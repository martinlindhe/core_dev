<?php
/**
 * $Id$
 *
 * Forum functions
 *
 * @author Martin Lindhe, 2007-2011 <martin@ubique.se>
 */

throw new \Exception ('NON WORKING NEED FULL REWRITE');

require_once('atom_subscriptions.php');    //for subscription functionality
require_once('functions_email.php');    //for email sharing of forum links
require_once('class.Users.php');    //for Users::link()

//forum module settings:
$config['forum']['rootname'] = 'Forum';
$config['forum']['path_separator'] = ' - ';
$config['forum']['allow_votes'] = false;
$config['forum']['maxsize_body'] = 5000;    //max number of characters in a forum post
$config['forum']['search_results_per_page'] = 5;
$config['forum']['topics_per_page'] = 20;
$config['forum']['posts_per_page'] = 25;
$config['forum']['moderation'] = false;

//Forum-itemtypes
define('FORUM_FOLDER',            1);
define('FORUM_MESSAGE',            2);


function getForumItem($itemId)
{
    global $db;
    if (!is_numeric($itemId)) return false;

    $q  = 'SELECT t1.*,t2.userName AS authorName ';
    $q .= 'FROM tblForums AS t1 ';
    $q .= 'LEFT JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
    $q .= 'WHERE t1.itemId='.$itemId.' AND t1.deletedBy=0';
    return $db->getOneRow($q);
}

function getForumName($itemId)
{
    global $db;
    if (!is_numeric($itemId)) return false;

    $q  = 'SELECT itemSubject FROM tblForums WHERE itemId='.$itemId;
    return $db->getOneItem($q);
}

/**
 * Returns all items inside $itemId
 */
function getForumItems($itemId = 0, $asc_order = true, $limit = '')
{
    global $db;
    if (!is_numeric($itemId) ||!is_bool($asc_order)) return false;

    $q  = 'SELECT t1.*,t2.userName AS authorName ';
    $q .= 'FROM tblForums AS t1 ';
    $q .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
    $q .= 'WHERE t1.parentId='.$itemId.' AND t1.deletedBy=0 ';
    $q .= 'ORDER BY t1.itemType ASC,t1.sticky DESC,';
    if ($asc_order) $q .= 't1.timeCreated ASC';
    else $q .= 't1.timeCreated DESC';
    $q .= ',t1.itemSubject ASC'.$limit;
    return $db->getArray($q);
}

/**
 * Return the number of messages inside $itemId, recursive (default)
 */
function getForumMessageCount($itemId, $recursive = true, $mecnt = 0)    //fixme: skicka med itemType param & rename function
{
    global $db;
    if (!is_numeric($itemId) || !is_numeric($mecnt)) return false;

    $q = 'SELECT itemId FROM tblForums WHERE parentId='.$itemId.' AND itemType='.FORUM_MESSAGE.' AND deletedBy=0';
    $arr = $db->getArray($q);

    foreach ($arr as $row) {
        $mecnt++;
        if ($recursive === true) {
            $mecnt = getForumMessageCount($row['itemId'], $recursive, $mecnt);
        }
    }
    return $mecnt;
}

/**
 * Return the number of items (folders & messages & discussions) inside $itemId, recursive
 */
function getForumItemCount($itemId, $mecnt = 0)
{
    global $db;
    if (!is_numeric($itemId) || !is_numeric($mecnt)) return false;

    $q = 'SELECT itemId FROM tblForums WHERE parentId='.$itemId.' AND deletedBy=0';
    $arr = $db->getArray($q);

    foreach ($arr as $row) {
        $mecnt++;
        $mecnt = getForumItemCount($row['itemId'], $mecnt);
    }
    return $mecnt;
}

/**
 * XXX
 */
function forumItemIsFolder($itemId)
{
    global $db;
    if (!is_numeric($itemId)) return false;

    if ($itemId == 0) return true; //root folder

    $q = 'SELECT itemType FROM tblForums WHERE itemId='.$itemId;
    $itemType = $db->getOneItem($q);

    if ($itemType == FORUM_FOLDER) return true;
    return false;
}

/**
 * Returns false if item is a message but parent is a folder (item is a discussion then)
 */
function forumItemIsMessage($itemId)
{
    global $db;
    if (!is_numeric($itemId)) return false;

    $q = 'SELECT itemType, parentId FROM tblForums WHERE itemId='.$itemId;
    $row = $db->getOneRow($q);

    if ($row['itemType'] == FORUM_MESSAGE) {
        if (forumItemIsFolder($row['parentId'])) return false;
        return true;
    }
    return false;
}

function forumItemIsDiscussion($itemId)
{
    global $db;
    if (!is_numeric($itemId)) return false;

    $q = 'SELECT itemType, parentId FROM tblForums WHERE itemId='.$itemId;
    $row = $db->getOneRow($q);

    if ($row['itemType'] == FORUM_MESSAGE) {
        //If the parentId is a folder and itemId is a message, then it is a discussion!
        if (forumItemIsFolder($row['parentId'])) return true;
        return false;
    }
    return false;
}

function setForumItemParent($itemId, $parentId)
{
    global $db;
    if (!is_numeric($itemId) || !is_numeric($parentId)) return false;

    $q = 'UPDATE tblForums SET parentId='.$parentId.' WHERE itemId='.$itemId;
    $db->update($q);
}

function addForumFolder($parentId, $folderName, $folderDesc = '')
{
    global $h, $db;
    if (!$h->session->id || !is_numeric($parentId)) return false;

    $folderDesc = strip_tags($folderDesc);
    $folderName = $db->escape(strip_tags($folderName));
    $folderDesc = $db->escape($folderDesc);

    $q = 'INSERT INTO tblForums SET itemType='.FORUM_FOLDER.',authorId='.$h->session->id.',parentId='.$parentId.',itemSubject="'.$folderName.'",itemBody="'.$folderDesc.'",timeCreated=NOW()';
    return $db->insert($q);
}

function addForumMessage($parentId, $subject, $body, $sticky = 0)
{
    global $h, $db, $config;
    if (!$h->session->id || !is_numeric($parentId) || !is_numeric($sticky)) return false;

    $body = strip_tags($body);
    $subject = $db->escape(strip_tags($subject));

    $body = substr($body, 0, $config['forum']['maxsize_body']);
    $body = $db->escape($body);

    $q = 'INSERT INTO tblForums SET itemType='.FORUM_MESSAGE.',authorId='.$h->session->id.',parentId='.$parentId.',itemSubject="'.$subject.'",itemBody="'.$body.'",timeCreated=NOW()';
    if ($sticky) $q .= ',sticky='.$sticky;
    $itemId = $db->insert($q);

    //Auto-moderate
    if (isSensitive($subject) || isSensitive($body)) addToModerationQueue(MODERATION_FORUM, $itemId, true);

    //Check if there is any users who should be notified about this new message
    if ($config['subscriptions']['notify']) {
        //TODO: Not ideal, would be neat to be able to insert text from this post in notification message
        notifySubscribers(SUBSCRIPTION_FORUM, $parentId, 0);
    }
    return $itemId;
}

function getForumDepthHTML($type, $itemId)
{
    global $db, $config;
    if (!is_numeric($type) || !is_numeric($itemId)) return false;

    if (!$itemId) {
        $result = '<a href="forum.php">'.$config['forum']['rootname'].'</a>';
        return $result;
    }

    $q = 'SELECT itemSubject,parentId FROM tblForums WHERE itemId='.$itemId;
    $row = $db->getOneRow($q);

    switch ($type) {
        case FORUM_MESSAGE:
            $subject = $row['itemSubject'];
            if ($subject) {
                if (mb_strlen($subject) > 35) $subject = mb_substr($subject, 0, 35).'...';
                $result = ' - <a href="forum.php?id='.$itemId.'">'.($subject != '' ? $subject : '(No name)').'</a>';
            } else {
                $result = '';
            }
            break;

        case FORUM_FOLDER:
            $result = '';
            if ($row['itemSubject']) $result = $config['forum']['path_separator'].'<a href="forum.php?id='.$itemId.'">'.$row['itemSubject'].'</a>';
            break;

        default: die('aouuu');
    }

    return getForumDepthHTML($type, $row['parentId']).$result;
}

/**
 * Returns the $count last posts
 */
function getLastForumPosts($count)
{
    global $db;
    if (!is_numeric($count)) return false;

    $q  = 'SELECT t1.*,t2.userName AS authorName,t3.itemSubject AS parentSubject ';
    $q .= 'FROM tblForums AS t1 ';
    $q .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
    $q .= 'LEFT JOIN tblForums AS t3 ON (t1.itemSubject="" AND t1.parentId=t3.itemId) ';
    $q .= 'WHERE t1.itemType='.FORUM_MESSAGE.' AND t1.deletedBy=0 ';
    $q .= 'ORDER BY t1.timeCreated DESC ';
    $q .= 'LIMIT 0,'.$count;
    return $db->getArray($q);
}

function forumLockItem($itemId)
{
    global $h, $db;
    if (!$h->session->isAdmin || !is_numeric($itemId)) return false;

    $q = 'UPDATE tblForums SET locked=1 WHERE itemId='.$itemId;
    $db->update($q);
}

function forumUnlockItem($itemId)
{
    global $h, $db;
    if (!$h->session->isAdmin || !is_numeric($itemId)) return false;

    $q = 'UPDATE tblForums SET locked=0 WHERE itemId='.$itemId;
    $db->update($q);
}

/**
 * Saves changes to a forum entry
 */
function forumUpdateItem($itemId, $subject, $body, $sticky = 0)
{
    global $db;
    if (!is_numeric($itemId) || !is_numeric($sticky)) return false;

    $subject = $db->escape($subject);
    $body = $db->escape($body);

    $q = 'UPDATE tblForums SET itemSubject="'.$subject.'",itemBody="'.$body.'",sticky='.$sticky.' WHERE itemId='.$itemId;
    $db->update($q);
}

function getForumStructure()
{
    global $db;

    //root level categories
    $q = 'SELECT itemSubject,itemId FROM tblForums WHERE parentId=0 AND deletedBy=0 ORDER BY itemSubject';
    $list = $db->getArray($q);

    //forum categories
    foreach ($list as $row) {
        $q = 'SELECT itemSubject,itemId FROM tblForums WHERE parentId='.$row['itemId'].' AND deletedBy=0 ORDER BY itemSubject';
        $sub = $db->getArray($q);
        foreach ($sub as $subrow) {
            $arr[] = array('name' => $row['itemSubject'].' - '.$subrow['itemSubject'], 'itemId' => $subrow['itemId']);
        }
    }
    return $arr;
}

function displayRootForumContent()
{
    $list = getForumItems();

    if (!count($list)) return;

    foreach ($list as $row) {
        $subject = $row['itemSubject'];
        if (strlen($subject)>35) $subject = substr($subject,0,35).'..';

        if (!$subject) $subject = '(No name)';

        echo '<div class="forum_overview_group">';
        echo '<a href="forum.php?id='.$row['itemId'].'">'.$subject.'</a><br/><br/>';
        $itemId = $row['itemId'];

        $data = getForumItem($itemId);
        $list = getForumItems($itemId);

        echo '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="forum_overview_table">';
        echo '<tr>';
        echo '<th width="40"></th>';    //for icons
        echo '<th>'.t('Forum').'</th>';
        echo '<th width="200" align="center">'.t('Last topic').'</th>';
        echo '<th width="70" align="center">'.t('Topics').'</th>';
        echo '<th width="70" align="center">'.t('Posts').'</th>';
        echo '</tr>';

        $i = 0;
        foreach ($list as $row) {
            $i++;

            $subject = $row['itemSubject'];
            if (strlen($subject) > 50) $subject = substr($subject, 0, 50).'..';
            if (!$subject) {
                $subject = '(Inget navn)';
            }

            echo '<tr class="forum_overview_item_'.($i%2?'even':'odd').'" >';
            echo '<td align="center">'.coreButton('Folder').'</td>';
            echo '<td class="forum_item_text">';
                echo '<a href="forum.php?id='.$row['itemId'].'">'.$subject.'</a><br/>';
                echo $row['itemBody'];
            echo '</td>';

            $data = getForumThreadContentLastPost($row['itemId']);
            echo '<td class="forum_item_text" width=200>';
            if ($data) {
                if ($data['itemSubject']) {
                    echo '<a href="forum.php?id='.$data['itemId'].'">'.$data['itemSubject'].'</a><br/>';
                } else {
                    echo '<a href="forum.php?id='.$data['parentId'].'#post'.$data['itemId'].'">'.$data['parentSubject'].'</a><br/>';
                }
                echo t('by').' '.Users::link($data['authorId'], $data['authorName']).'<br/>';
                echo formatTime($data['timeCreated']);
            } else {
                echo t('Never');
            }
            echo '</td>';
            echo '<td align="center">'.formatNumber(getForumItemCountFlat($row['itemId'])).'</td>';
            echo '<td align="center">'.formatNumber(getForumThreadContentCount($row['itemId'])).'</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div><br/>';    //class="forum_overview_group"
    }
}

/**
 * Returns item data for the last post in any of the threads with parentId=$itemId
 */
function getForumThreadContentLastPost($itemId)
{
    global $db;
    if (!is_numeric($itemId)) return false;

    $q = 'SELECT itemId FROM tblForums WHERE parentId='.$itemId.' AND deletedBy=0';;
    $list = $db->getArray($q);

    $newest_time = 0;

    for ($i=0; $i<count($list); $i++) {
        $q = 'SELECT itemId, timeCreated FROM tblForums';
        $q .= ' WHERE parentId='.$list[$i]['itemId'].' AND deletedBy=0';
        $q .= ' ORDER BY timeCreated DESC LIMIT 0,1';
        $data = $db->getOneRow($q);

        if ($data['timeCreated'] > $newest_time) {
            $newest_time = $data['timeCreated'];
            $newest_id = $data['itemId'];
        }
    }

    if ($newest_time) {
        $data = getForumItem($newest_id);
        if (!$data['itemSubject']) {
            //fills in parent's subject if subject is missing
            $parent_data = getForumItem($data['parentId']);
            $data['parentSubject'] = $parent_data['itemSubject'];
        }
        return $data;
    }

    return false;
}

/**
 * Returns the number of items with $itemId as parent, non-recursive
 */
function getForumItemCountFlat($itemId)
{
    global $db;
    if (!is_numeric($itemId)) return false;

    $q = 'SELECT COUNT(itemId) FROM tblForums WHERE parentId='.$itemId.' AND deletedBy=0';
    return $db->getOneItem($q);
}

/* Returns the total number of posts contained in all the threads with parentId=$itemId */
function getForumThreadContentCount($itemId)    //FIXME: maybe rename function?
{
    global $db;
    if (!is_numeric($itemId)) return false;

    $q = 'SELECT itemId FROM tblForums WHERE parentId='.$itemId.' AND deletedBy=0';
    $list = $db->getArray($q);

    $cnt = 0;
    for ($i=0; $i<count($list); $i++) {
        $q = 'SELECT COUNT(itemId) FROM tblForums WHERE parentId='.$list[$i]['itemId'].' AND deletedBy=0';
        $cnt += $db->getOneItem($q);
    }
    return $cnt;
}

/**
 * Displays the different forums of one root level category + activity details
 */
function displayForumContentFlat($itemId)
{
    global $db, $config;
    if (!is_numeric($itemId)) return false;

    echo '<div class="forum_overview_group">';

    $data = getForumItem($itemId);
    $tot_cnt = getForumItemCountFlat($itemId);

    $pager = makePager($tot_cnt, $config['forum']['topics_per_page']);
    $list = getForumItems($itemId, false, $pager['limit']);

    echo $pager['head'];

    echo '<div class="forum_header">'.getForumDepthHTML(FORUM_FOLDER, $itemId).'</div>';
    echo '<br/>';

    echo '<table width="100%" class="forum_overview_table">';
    echo '<tr class="forum_subheader">';
    echo '<th width=30></th>';
    if ($data['parentId'] == 0) {
        echo '<th>'.t('Forum').'</th>';
        echo '<th width=80>'.t('Author').'</th>';
        echo '<th width=70 align="center">'.t('Topics').'</th>';
        echo '<th width=70 align="center">'.t('Views').'</th>';
        echo '<th width=200>'.t('Last topic').'</th>';
    } else {
        echo '<th>'.t('Topic').'</th>';
        echo '<th width=80>'.t('Author').'</th>';
        echo '<th width=70 align="center">'.t('Posts').'</th>';
        echo '<th width=70 align="center">'.t('Views').'</th>';
        echo '<th width=200>'.t('Last post').'</th>';
    }
    echo '</tr>';

    $i = 0;
    foreach ($list as $row) {
        $i++;
        echo '<tr class="forum_overview_item_'.($i%2?'even':'odd').'">';

        echo '<td align="center">';    //icon

        if ($row['locked']) {
            echo '<img src="'.coredev_webroot().'gfx/icon_locked.png" alt="Locked" title="Locked"/><br/>';
        }
        if ($row['sticky'] == 1) {
            echo '<img src="'.coredev_webroot().'gfx/icon_forum_sticky.png" alt="Sticky" title="Sticky"/>';
        }

        if ($row['sticky'] == 2) {
            echo '<img src="'.coredev_webroot().'gfx/icon_forum_announcement.png" alt="Announcement" title="Announcement"/>';
        } else if ($data['parentId'] == 0) {
            echo coreButton('Folder');
        } else {
            echo '<img src="'.coredev_webroot().'gfx/icon_forum_topic.png" alt="Message" title="Message"/>';
        }
        echo '</td>';

        echo '<td class="forum_item_text">';    //topic/forum
            //if ($row['sticky'] == 1) echo '<b>Sticky: </b>';
            //if ($row['sticky'] == 2) echo '<b>Announcement: </b>';
            echo '<a href="forum.php?id='.$row['itemId'].'">'.$row['itemSubject'].'</a><br/>';
        echo '</td>';

        echo '<td>';    //author
            echo Users::link($row['authorId'], $row['authorName']);
            //echo ' '.$row['timeCreated'];
        echo '</td>';

        echo '<td align="center">'.formatNumber(getForumMessageCount($row['itemId'], false)).'</td>';
        echo '<td align="center">'.formatNumber($row['itemRead']).'</td>';

        $lastpost = getForumLastPost($row['itemId']);
        echo '<td class="forum_item_text">';    //last post/last topic
        if ($lastpost) {
            if ($data['parentId'] == 0) {
                //This is a topic
                $subject = $lastpost['itemSubject'];
                if (mb_strlen($subject) > 25) $subject = mb_substr($subject, 0, 25).'...';
                echo '<a href="forum.php?id='.$lastpost['itemId'].'#post'.$lastpost['itemId'].'">'.$subject.'</a><br/>';
            } else {
                //This is a post (a reply to a topic)
                echo '<a href="forum.php?id='.$row['itemId'].'#post'.$lastpost['itemId'].'"><img src="'.coredev_webroot().'gfx/icon_forum_post.png" alt="Post"/></a> ';
            }
            echo t('by').' '.Users::link($lastpost['userId'], $lastpost['userName']).'<br/>';
            echo formatTime($lastpost['timeCreated']);
        } else {
            if ($data['parentId'] == 0) {
                echo 'No topics';
            } else {
                echo 'No posts';
            }
        }
        echo '</td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '</div>';

    if ($data['parentId'] == 0) {
        echo '<br/>';
        echo '<div class="forum_overview_group">';
        echo '<b>Active topics</b><br/><br/>';
        echo 'fixme - list a few of the active topics within the above forums here';
        echo '</div>';
    }
}

function getForumLastPost($itemId)
{
    global $db;
    if (!is_numeric($itemId)) return false;

    //returns last post to the topic $itemId
    $q  = 'SELECT t1.*,t2.userId,t2.userName FROM tblForums AS t1 ';
    $q .= 'LEFT JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
    $q .= 'WHERE t1.parentId='.$itemId.' AND t1.deletedBy=0 ';
    $q .= 'ORDER BY t1.timeCreated DESC LIMIT 1';
    return $db->getOneRow($q);
}

function showForumPost($item, $islocked = false)
{
    global $h, $config;

    $subject = formatUserInputText($item['itemSubject']);
    $body = formatUserInputText($item['itemBody']);

    if (!$islocked) $islocked = $item['locked'];

    echo '<a name="post'.$item['itemId'].'" id="post'.$item['itemId'].'"></a>';

    echo '<table width="100%" class="forum_post_table">';
    echo '<tr class="forum_post_item">';
    echo '<td valign="top">';
    if ($subject) echo '<h1>'.$subject.'</h1><hr/>';

    echo '<div class="forum_post_details">';
    echo '<a href="forum.php?id='.$item['parentId'].'#post'.$item['itemId'].'">';
    echo '<img src="'.coredev_webroot().'gfx/icon_forum_post.png" alt="Post"/></a> ';
    echo t('by').' '.Users::link($item['authorId'], $item['authorName']).' '.formatTime($item['timeCreated']);
    echo '</div><br/>';

    echo $body;
    $signature = loadUserdataSetting($h->session->id, $config['settings']['default_signature']);
    if ($signature) echo '<hr/>'.$signature.'<br/>';

    $h->files->showAttachments(FILETYPE_FORUM, $item['itemId']);
    echo '</td>';

    echo '<td width="120" valign="top" class="forum_item_text">';
    echo Users::linkThumb($item['authorId'], $item['authorName']).'<br/><br/>';
    echo Users::getMode($item['authorId']).'<br/>';
    //echo 'Join date: '.getUserCreated($item['authorId']).'<br/>';
    echo t('Posts').': '.getForumPostsCount($item['authorId']);
    echo '</td>';

    echo '</tr>';

    if (!$h->session->id) {
        echo '</table><br/>';
        return;
    }

    echo '<tr class="forum_item">';
    echo '<td colspan="2" align="right">';

    if (!$islocked) {
        if (forumItemIsDiscussion($item['itemId'])) {
            echo '<a href="forum_new.php?id='.$item['itemId'].'&amp;q='.$item['itemId'].'">'.t('Quote').'</a> ';
        } else {
            echo '<a href="forum_new.php?id='.$item['parentId'].'&amp;q='.$item['itemId'].'">'.t('Quote').'</a> ';
        }

        if ($item['authorId'] == $h->session->id || $h->session->isAdmin) {
            echo '<a href="forum_edit.php?id='.$item['itemId'].'">'.t('Edit').'</a> ';
        }
    }

    if (!$islocked && $h->session->isAdmin) {
        echo '<a href="forum_delete.php?id='.$item['itemId'].'">'.t('Remove').'</a> ';
    }

    if (forumItemIsDiscussion($item['itemId'])) {
        echo '<a href="forum_tipsa.php?id='.$item['itemId'].'">'.t('Tell a friend').'</a> ';

        if ($h->session->isAdmin) {
            if (!$item['locked']) {
                echo '<a href="forum_lock.php?id='.$item['itemId'].'">'.t('Lock').'</a> ';
            } else {
                echo '<a href="forum_lock.php?id='.$item['itemId'].'&unlock">'.t('Unlock').'</a> ';
            }
            echo '<a href="forum_move.php?id='.$item['itemId'].'">'.t('Move').'</a> ';
        }
    }

    if ($h->session->id != $item['authorId']) {
        echo '<a href="forum_report.php?id='.$item['itemId'].'">'.t('Report').'</a> ';
    }

    echo '</td></tr>';
    echo '</table><br/>';
}

function displayTopicFlat($itemId)
{
    global $h, $db, $config;
    if (!is_numeric($itemId)) return false;

    echo '<div class="forum_overview_group">';

    $item = getForumItem($itemId);

    echo getForumDepthHTML(FORUM_MESSAGE, $item['parentId']).'<br/><br/>';

    showForumPost($item);

    if ($h->session->id) {
        if (!isSubscribed(SUBSCRIPTION_FORUM, $itemId)) {
            echo '<a href="?id='.$itemId.'&subscribe='.$itemId.'">Subscribe to topic</a><br/><br/>';
        } else {
            echo '<a href="?id='.$itemId.'&unsubscribe='.$itemId.'">Unsubscribe to topic</a><br/><br/>';
        }
    }

    $tot_cnt = getForumItemCountFlat($itemId);
    $pager = makePager($tot_cnt, $config['forum']['posts_per_page']);

    $list = getForumItems($itemId, true, $pager['limit']);    //get replies

    echo $pager['head'];

    if ($list) {
        foreach ($list as $row) {
            showForumPost($row, $item['locked']);
        }
    }
    echo '</div>';
}

/**
 * Returns a list of search results with forum items
 */
function getForumSearchResults($criteria, $method, $limit = '')
{
    global $db;
    if (!$criteria || !$method) return false;

    $criteria = $db->escape($criteria);

    $list = explode(' ', $criteria);

    $q  = 'SELECT t1.*,t2.userName AS authorName FROM tblForums AS t1 ';
    $q .= 'LEFT JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
    $q .= 'WHERE t1.deletedBy=0 AND '.getForumSearchQuery($list);

    switch ($method) {
        case 'mostread':
            $q .= 'ORDER BY t1.itemRead DESC '; break;

        case 'oldfirst':
            $q .= 'ORDER BY t1.timeCreated ASC '; break;

        case 'newfirst': default:
            $q .= 'ORDER BY t1.timeCreated DESC '; break;
    }

    $q .= $limit;
    return $db->getArray($q);
}

function getForumSearchResultsCount($criteria)
{
    global $db;
    if (!$criteria) return false;

    $criteria = $db->escape($criteria);

    $list = explode(' ', $criteria);

    $q  = 'SELECT COUNT(t1.itemId) FROM tblForums AS t1 ';
    $q .= 'WHERE t1.deletedBy=0 AND '.getForumSearchQuery($list);

    return $db->getOneItem($q);
}

/**
 * $list is an array with words to search for
 */
function getForumSearchQuery($list)
{
    $sql = '';
    for ($i=0; $i<count($list); $i++) {    //fixme: foreach

        $curr = $list[$i];
        if (substr($curr,0,1) == '+') {
            //require this

            $curr = substr($curr,1);
            if ($i>0) $sql .= 'AND ';
            $sql .= '(t1.itemSubject LIKE "%'.$curr.'%" OR t1.itemBody LIKE "%'.$curr.'%") ';

        } else if (substr($curr,0,1) == '-') {
            //skip this
            if (count($list)==1) { //dont allow search on everything but ONE word
                return;
            }

            $curr = substr($curr,1);
            if ($i>0) $sql .= 'AND ';
            $sql .= 'NOT (t1.itemSubject LIKE "%'.$curr.'%" OR t1.itemBody LIKE "%'.$curr.'%") ';
        } else {
            //optional (this OR something else)
            if ($i>0) $sql .= 'OR ';
            $sql .= '(t1.itemSubject LIKE "%'.$curr.'%" OR t1.itemBody LIKE "%'.$curr.'%") ';
        }
    }

    $sql .= 'AND t1.deletedBy=0 ';
    return $sql;
}

function deleteForumItem($itemId)
{
    global $h, $db;
    if (!$h->session->id || !is_numeric($itemId)) return false;

    $q = 'UPDATE tblForums SET timeDeleted=NOW(),deletedBy='.$h->session->id.' WHERE itemId='.$itemId;

    if ($db->update($q)) {
        removeFromModerationQueueByType(MODERATION_FORUM, $itemId);
    }
}

/**
 * Deletes itemId and everything below it. also deletes associated moderation queue entries
 */
function deleteForumItemRecursive($itemId, $loop = false)
{
    global $db;
    if (!is_numeric($itemId)) return false;

    $q = 'SELECT itemId FROM tblForums WHERE parentId='.$itemId.' AND deletedBy=0';
    $arr = $db->getArray($q);

    foreach ($arr as $row) {
        $q = 'DELETE FROM tblForums WHERE itemId='.$row['itemId'];
        if ($db->delete($q)) {
            removeFromModerationQueueByType(MODERATION_FORUM, $row['itemId']);
            deleteForumItemRecursive($row['itemId'], true);
        }
    }

    if ($loop != true) {
        $q = 'DELETE FROM tblForums WHERE itemId='.$itemId;
        if ($db->delete($q)) {
            removeFromModerationQueueByType(MODERATION_FORUM, $itemId);
        }
    }
}

/**
 * Returns the number of messages that $userId has written in the forums
 */
function getForumPostsCount($userId)
{
    global $db;
    if (!is_numeric($userId)) return false;

    $q = 'SELECT COUNT(itemId) FROM tblForums WHERE authorId='.$userId.' AND deletedBy=0 AND itemType='.FORUM_MESSAGE;
    return $db->getOneItem($q);
}

/**
 * Get the number of new forum "entries" (all types) during the specified time period
 */
function getForumEntriesCountPeriod($dateStart, $dateStop)
{
    global $db;

    $q = 'SELECT count(itemId) AS cnt FROM tblForums WHERE timeCreated BETWEEN "'.$db->escape($dateStart).'" AND "'.$db->escape($dateStop).'"';
    return $db->getOneItem($q);
}

function displayForum($_id)
{
    global $h;
    if (!is_numeric($_id)) return false;

    // Start/stop subscription
    if ($h->session->id) {
        if (!empty($_GET['subscribe'])) addSubscription(SUBSCRIPTION_FORUM, $_GET['subscribe']);
        if (!empty($_GET['unsubscribe'])) removeSubscription(SUBSCRIPTION_FORUM, $_GET['unsubscribe']);
    }

    /*
    if ($config['forum']['allow_votes'] && !empty($_POST['vote']) && !empty($_POST['voteId'])) {
        addForumVote($_POST['voteId'], $_POST['vote']);
    }*/

    if (!$_id) {
        //display root level
        echo displayRootForumContent();

        if ($h->session->isAdmin) echo '<a href="forum_new.php?id=0">'.t('Create new root level category').'</a>';
        return;
    }

    if (forumItemIsFolder($_id)) {
        //display content of a folder (parent = root)
        echo displayForumContentFlat($_id);

        echo '<a href="forum_new.php?id='.$_id.'">Create new forum here</a><br/>';
        if ($h->session->isAdmin) {
            echo '<a href="forum_edit.php?id='.$_id.'">Edit forum name</a><br/>';
            echo '<a href="forum_delete.php?id='.$_id.'">Delete forum</a><br/>';
        }
    } else {
        echo '<a href="forum_new.php?id='.$_id.'">'.t('Reply').'</a>';
        echo '<br/><br/>';

        //display flat discussion overview
        echo displayTopicFlat($_id);

        echo '<br/>';
        echo '<a href="forum_new.php?id='.$_id.'">'.t('Reply').'</a>';
    }
}

/**
 * Create new forum threads and posts
 *
 * The script takes one parameter "id", which specifies the parent ID.
 *
 * Parent ID = 0 means: create a top level category (admins only)
 * Parent ID = category means: create a forum (admins only)
 * Parent ID = forum means: create a thread (everyone)
 * Parent ID = thread means: create a post (everyone)
 *
 * Vocabulary:
 * root level category: A top level container, which contains forums
 * forum: A container inside a root level forum, which contains topics
 * topic: A whole discussion is refered as a "topic"
 * post: A discussion contains one or more posts
 *
 * if _GET['q'] is set, this is the forum post to quote
 */
//FIXME the header() usage here should be moved out /martin FIXED FIXME Fixa så inte goLoc behövs / linus
//FIXME is the function documentation correct?
function createForumCategory($itemId)
{
    global $h, $db, $config;

    if (!$itemId && !$h->session->isAdmin) return false;    //invalid request

    if ($itemId) {
        $item = getForumItem($itemId);
        $parent = getForumItem($item['parentId']);
        if (($itemId && !$item) || $item['locked']) die;    //block attempt to create item with nonexisting parent
    }

    $quoteId = 0;
    if (!empty($_GET['q']) && is_numeric($_GET['q'])) $quoteId = $_GET['q'];

    $writeSubject = '';
    $writeBody = '';

    if ($quoteId) {
        /* Quote another message */
        $quoteItem = getForumItem($quoteId);
        $quoteName = $quoteItem['authorName'];
        if ($quoteName && trim($quoteItem['itemBody'])) {
            $writeBody = '[quote name='.$quoteName.']'.$quoteItem['itemBody']."[/quote]\n\n";
        }
    }

    if (!empty($_POST['subject']))    $writeSubject = $_POST['subject'];
    if (!empty($_POST['body']))        $writeBody = $_POST['body'];

    $createdId = 0;

    $forum_error = '';

    if (!empty($_POST['subject']) || !empty($_POST['body'])) {

        if (strlen($writeBody) <= $config['forum']['maxsize_body']) {
            if ($h->session->isAdmin && ($itemId == 0 || $item['parentId'] == 0)) {
                //Create category or a forum
                if ($writeSubject) {
                    $createdId = addForumFolder($itemId, $writeSubject, $writeBody);
                    goLoc('forum.php?id='.$createdId);
                    die;

                } else {
                    $forum_error = 'You must write a topic!';
                }
            } else {
                //Create a thread or a post
                if ($parent['parentId'] == 0 && !$writeSubject) {
                    $forum_error = 'You must write a topic!';
                } else {
                    $sticky = 0;
                    if ($h->session->isAdmin && !empty($_POST['sticky'])) $sticky = $_POST['sticky'];
                    $createdId = addForumMessage($itemId, $writeSubject, $writeBody, $sticky);

                    if ($createdId) {
                        //attach all FILETYPE_FORUM ownerId =0 to this id
                        $q = 'UPDATE tblFiles SET ownerId='.$createdId.' WHERE fileType='.FILETYPE_FORUM.' AND ownerId=0 AND uploaderId='.$h->session->id;
                        $db->update($q);
                    }
                    goLoc('forum.php?id='.$itemId.'#post'.$createdId);
                    die;
                }
            }
        } else {
            $forum_error = 'The post is too long, the max allowed length are '.$config['forum']['maxsize_body'].' characters, please try to shorten down your text a bit.';
        }

        if (!$forum_error) {
            if (!empty($_POST['subscribehere'])) {
                //Start a subscription of the created topic
                //fixme: make sure we are creating a topic so users cant subscribe to whole forums
                addSubscription(SUBSCRIPTION_FORUM, $itemId);
            }
            if ($itemId == 0 || $item['parentId'] == 0) {
                header('Location: forum.php?id='.$itemId);
            } else {
                $item = getForumItem($itemId);
                header('Location: forum.php?id='.$item['parentId'].'#post'.$itemId);
            }
            die;
        }
    }

    $hide_subject = false;

    if (!empty($forum_error)) echo '<div class="critical">'.$forum_error.'</div>';

    echo '<form method="post" name="newpost" enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';

    if ($itemId == 0) {
        //Create root level category (admins only)
        echo 'Forum - Add new root level category<br/><br/>';
        echo t('Name').': '.xhtmlInput('subject', $writeSubject, 60, 50).'<br/>';

    } else if (!$item['parentId']) {
        //Create a category inside a "root level category" (admins only)
        echo 'Forum - Add new subcategory (under <b>'.getForumName($itemId).'</b>)<br/><br/>';

        echo t('Subject').': <input type="text" size="60" maxlength="50" name="subject" value="'.$writeSubject.'"/><br/>';
        echo t('Description').':<br/>';
        echo '<input type="text" name="body" size="60" value="'.$writeBody.'"/><br/><br/>';
    } else if ($parent['parentId'] == 0) {
        //Create a discussion thread (everyone)
        echo 'Add new discussion thread under '.getForumDepthHTML(FORUM_FOLDER, $itemId).'<br/><br/>';
        echo t('Subject').': <input type="text" size="60" maxlength="50" name="subject" value="'.$writeSubject.'"/><br/>';
        echo '<textarea name="body" cols="60" rows="14">'.$writeBody.'</textarea><br/><br/>';

        if ($h->session->isAdmin) {
            //Allow admins to create stickies & announcements
            echo '<input name="sticky" type="radio" class="radio" value="0" id="r0" checked="checked"/><label for="r0">Create a normal thread</label><br/>';
            echo '<input name="sticky" type="radio" class="radio" value="1" id="r1"/><label for="r1">Admin only: Make the thread a sticky</label><br/>';
            echo '<input name="sticky" type="radio" class="radio" value="2" id="r2"/><label for="r2">Admin only: Make the thread an announcement</label><br/>';
        }
    } else {
        //Create a post (everyone)
        echo getForumDepthHTML(FORUM_FOLDER, $itemId).' - Add a response to this post<br/><br/>';
        echo showForumPost($item, '', false);

        //handle file upload
        if (!empty($_FILES['file1'])) {
            $h->files->handleUpload($_FILES['file1'], FILETYPE_FORUM, 0);
        }

        $h->files->showAttachments(FILETYPE_FORUM, 0);

        echo '<div id="forum_new_attachment">';
        echo t('Attach a file').': ';
        echo '<input type="file" name="file1"/>';
        echo xhtmlSubmit('Upload');
        echo '</div>';
        echo '<textarea name="body" cols="60" rows="14">'.$writeBody.'</textarea><br/><br/>';
    }

    $item = getForumItem($itemId);

    echo '<br/>';

    echo xhtmlSubmit('Save');

    /*
    if (!isSubscribed($itemId, SUBSCRIBE_MAIL)) {
        $content .= '<input name="subscribehere" type="checkbox" class="checkbox">Subscribe to topic';
    } else {
        $content .= '<div class="critical">You are already subscribed to this topic</div>';
    }
    */
    echo '</form><br/>';

    echo '<script type="text/javascript">';
    echo 'if (document.newpost.subject) document.newpost.subject.focus();';
    echo 'else if (document.newpost.body) document.newpost.body.focus();';
    echo '</script>';
}

function forumEdit($itemId)    //FIXME använd inte header()
{
    global $h, $config;

    $item = getForumItem($itemId);

    if (!$item || $item['locked'] || (!$h->session->isAdmin && ($item['authorId'] != $h->session->id))) {
        header('Location: '.$config['start_page']);
        die;
    }

    $subject = '';
    $body = '';

    if (!empty($_POST['subject'])) $subject = $_POST['subject'];
    if (!empty($_POST['body'])) $body = $_POST['body'];

    if ($subject || $body) {
        $sticky = 0;
        if ($h->session->isAdmin && !empty($_POST['sticky'])) $sticky = 1;

        forumUpdateItem($itemId, $subject, $body, $sticky);

        if (forumItemIsMessage($itemId)) {
            header('Location: forum.php?id='.$item['parentId'].'#post'.$itemId);
        } else {
            header('Location: forum.php?id='.$itemId);
        }
        die;
    }

    echo 'Title: '.getForumDepthHTML(FORUM_FOLDER, $itemId).'<br/>';

    if (forumItemIsMessage($itemId)) {
        echo 'Edit post:';
    } else {
        echo 'Edit thread:';
    }

    echo '<br/><br/>';
    echo '<form name="change" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';

    echo 'Subject:<br/>';
    echo '<input name="subject" size="60" value="'.$item['itemSubject'].'"/><br/><br/>';

    if ($item['parentId'] && forumItemIsFolder($itemId)) {
        echo 'Description:<br/>';
        echo '<input type="text" name="body" size="60" value="'.$item['itemBody'].'"/><br/><br/>';
    } else if ($item['parentId']) {
        echo '<textarea name="body" cols="60" rows="14">'.$item['itemBody'].'</textarea><br/><br/>';
    }

    if ($h->session->isAdmin && forumItemIsDiscussion($itemId)) {
        echo '<input type="checkbox" class="checkbox" value="1" name="sticky"'.($item['sticky']?' checked="checked"':'').'/>';
        echo ' The thread is a sticky<br/><br/>';
    }
    echo '<input type="submit" class="button" value="Save"/>';
    echo '</form><br/><br/>';
}

//FIXME dont use goLoc()
function moveForum($itemId)
{
    global $h;
    if (!is_numeric($itemId)) return false;

    $item = getForumItem($itemId);

    if (!$item) {
        goLoc($h->session->start_page);
        die;
    }

    if (isset($_POST['destId'])) {
        setForumItemParent($itemId, $_POST['destId']);
        goLoc('forum.php?id='.$itemId);
        die;
    }

    echo '<h1>Move thread</h1>';

    echo 'This discussion thread will be moved:<br/><br/>';
    echo showForumPost($item, '', false).'<br/>';

    echo 'Where do you want to move the thread?<br/>';
    echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';

    $list = getForumStructure();

    echo '<select name="destId">';
    //echo '<option value="0">Flytta till roten';
    foreach ($list as $row) {
        echo '<option value="'.$row['itemId'].'">'.$row['name'];
    }
    echo '</select>';

    echo '<br/><br/>';
    echo xhtmlSubmit('Move');
    echo '</form><br/><br/>';
}

//FIXME dont use goLoc() !
function reportForumPost($itemId)
{
    if (!is_numeric($itemId)) return false;
    $item = getForumItem($itemId);
    if (!$item) return false;

    if (isset($_POST['motivation'])) {
        $queueId = addToModerationQueue(MODERATION_FORUM, $itemId);
        addComment(COMMENT_MODERATION, $queueId, $_POST['motivation']);

        goLoc('forum.php?id='.$item['parentId']);
        die;
    }

    echo showForumPost($item, '', false).'<br/>';

    echo xhtmlForm('abuse', $_SERVER['PHP_SELF'].'?id='.$itemId);
    echo t('Write a motivation').':<br/>';
    echo xhtmlTextarea('motivation', '', 50, 5).'<br/><br/>';
    echo xhtmlSubmit('Report');
    echo xhtmlFormClose().'<br/><br/>';
}

//FIXME make mail text configurable / finish translation
//FIXME make this function a generic "share content", for all kinds of sharing
//FIXME add ability to share with digg, del.icio.us, facebook:
//    digg:        http://digg.com/submit?phase=2&url=http://thinkprogress.org/2008/09/15/palin-cut-funding-for-alaska-special-olympics/&title=Palin%20cut%20funding%20for%20Alaska%20Special%20Olympics.
//    delicious:    http://del.icio.us/post?url=http://thinkprogress.org/2008/09/15/palin-cut-funding-for-alaska-special-olympics/&title=Palin%20cut%20funding%20for%20Alaska%20Special%20Olympics.
//    facebook:    http://www.facebook.com/share.php?u=http://thinkprogress.org/2008/09/15/palin-cut-funding-for-alaska-special-olympics/&t=Palin%20cut%20funding%20for%20Alaska%20Special%20Olympics.
//    myspace:    http://www.myspace.com/Modules/PostTo/Pages
function shareForumItem($itemId)
{
    global $h;
    if (!$h->session->id || !is_numeric($itemId)) return false;

    if (!empty($_POST['fshare_mail'])) {
        if (is_email($_POST['fshare_mail'])) {
            $item = getForumItem($itemId);

            if (!empty($_POST['fshare_name'])) {
                $mail = "Hej ".$_POST['fshare_name']."!\n\n";
            } else {
                $mail = "Hej!\n\n";
            }

            $mail .= $h->session->username." har skickat dig den här länken till dig från communityt\n";
            $mail .= "på vår sajt, ".xhtmlGetUrl('/').".\n\n";

            if ($item['authorId']) {
                $mail .= $item['itemSubject'].' av '.$item['authorName'].', '.formatTime($item['timeCreated']).":\n";
            } else {
                $mail .= $item['itemSubject'].' av gäst, '.formatTime($item['timeCreated'])."\n";
            }

            $mail .= "För att läsa inlägget i sin helhet, klicka på länken nedan:\n";
            $mail .= xhtmlGetUrl('forum.php?id='.$itemId.'#'.$itemId)."\n\n";

            if (!empty($_POST['fshare_comment'])) {
                $mail .= "\n";
                $mail .= "Din kompis lämnade även följande hälsning:\n";
                $mail .= $_POST['fshare_comment']."\n\n";
            }

            $subject = 'Meddelande från communityt';

            if (smtp_mail($_POST['fshare_mail'], $subject, $mail) == true) {
                echo 'Tipset ivägskickat<br/>';
            } else {
                echo 'Problem med utskicket<br/>';
            }
        } else {
            echo 'Ogiltig mailaddress!';
        }
        return;
    }

    $data = getForumItem($itemId);
    echo showForumPost($data).'<br/>';

    echo xhtmlForm('forum_share', $_SERVER['PHP_SELF'].'?id='.$itemId);
    echo 'Din kompis namn: '.xhtmlInput('fshare_name', '', 20, 30).'<br/>';
    echo t('E-mail').': '.xhtmlInput('fshare_mail', '', 40, 50).'<br/>';
    echo '<br/>';
    echo 'Hälsning:<br/>';
    echo xhtmlTextarea('fshare_comment', '', 40, 6).'<br/>';
    echo xhtmlSubmit('Share');
    echo xhtmlFormClose();
}

function formatUserInputText($text, $convert_html = true)   //XXXX DEPRECATE, use YuiRichedit instead
{
    $text = trim($text);

    //convert html tags to &lt; and &gt; etc:
    if ($convert_html) $text = htmlspecialchars($text);

    //convert dos line-endings to Unix format for easy handling
    $text = str_replace("\r\n", "\n", $text);

    /* [b]bold text[/b] */
    $text = str_ireplace('[b]', '<b>', $text);
    $text = str_ireplace('[/b]', '</b>', $text);

    /* [i]italic text[/i] */
    $text = str_ireplace('[i]', '<i>', $text);
    $text = str_ireplace('[/i]', '</i>', $text);

    /* [u]underlined text[/u] */
    $text = str_ireplace('[u]', '<u>', $text);
    $text = str_ireplace('[/u]', '</u>', $text);

    /* [s]strikethru text[/u] */
    $text = str_ireplace('[s]', '<del>', $text);
    $text = str_ireplace('[/s]', '</del>', $text);

    /* [h1]headline level 1[/h1] */
    $text = str_ireplace('[h1]', '<h1 class="bb_h1">', $text);
    $text = str_ireplace('[/h1]', '</h1>', $text);

    /* [h2]headline level 2[/h2] */
    $text = str_ireplace('[h2]', '<h2 class="bb_h2">', $text);
    $text = str_ireplace('[/h2]', '</h2>', $text);

    /* [h3]headline level 3[/h3] */
    $text = str_ireplace('[h3]', '<h3 class="bb_h3">', $text);
    $text = str_ireplace('[/h3]', '</h3>', $text);

    $text = str_ireplace("[hr]\n", '<hr/>', $text); //fixme: this is a hack. a better solution would be to trim all whitespace directly following a [hr] tag
    $text = str_ireplace('[hr]', '<hr/>', $text);

    //raw block, example: [raw]text text[/raw], interpret as written. FIXME: make possible to disable in config
    $text = str_ireplace("[/raw]\n", "[/raw]", $text);
    do {
        $pos1 = stripos($text, '[raw]');
        if ($pos1 === false) break;

        $pos2 = stripos($text, '[/raw]');
        if ($pos2 === false) break;
        $codeblock = trim(substr($text, $pos1+strlen('[raw]'), $pos2-$pos1-strlen('[raw]')));
        $codeblock = str_replace("\n", '(_br_)', $codeblock);

        $text = substr($text, 0, $pos1).$codeblock.substr($text, $pos2+strlen('[/raw]'));
    } while (1);

    //code block, example: [code]text text[/code]
    $text = str_ireplace("[/code]\n", "[/code]", $text);
    do {
        $pos1 = stripos($text, '[code]');
        if ($pos1 === false) break;

        $pos2 = stripos($text, '[/code]');
        if ($pos2 === false) break;
        $codeblock = trim(substr($text, $pos1+strlen('[code]'), $pos2-$pos1-strlen('[code]')));
        $codeblock = str_replace("\n", '(_br_)', $codeblock);

        $codeblock =
            '<div class="bb_code">'.
            '<div class="bb_code_head">code</div>'.
            '<div class="bb_code_body">'.$codeblock.'</div>'.
            '</div>';

        $text = substr($text, 0, $pos1) . $codeblock . substr($text, $pos2+strlen('[/code]'));
    } while (1);

    //quote block, example: [quote name=elvis]text text text[/quote]
    //or: [quote]text text text[/quote]
    do {
        $pos1 = stripos($text, '[quote');
        if ($pos1 === false) break;

        $pos2 = stripos($text, '[/quote]');
        if ($pos2 === false) break;

        $quoteblock = substr($text, $pos1+strlen('[quote'), $pos2-$pos1-strlen('[quote'));

        $qpos1 = stripos($quoteblock, 'name=');
        $qpos2 = strpos($quoteblock, ']');
        if ($qpos1 !== false) {
            $nameblock = substr($quoteblock, $qpos1+strlen('name='), $qpos2-$qpos1-strlen('name='));
            $quoteblock = substr($quoteblock, $qpos1+strlen('name=')+strlen($nameblock)+strlen(']'));
            if ($nameblock) $nameblock .= ' '.t('wrote');
            else $nameblock = t('Quote');
        } else {
            $nameblock = t('Quote');
            $quoteblock = substr($quoteblock, $qpos2+strlen(']'));
        }

        $quoteblock =
            '<div class="bb_quote">'.
            '<div class="bb_quote_head">'.$nameblock.':</div>'.
            '<div class="bb_quote_body">'.trim($quoteblock).'</div>'.
            '</div>';

        $text = substr($text, 0, $pos1) .$quoteblock. substr($text, $pos2+strlen('[/quote]'));
    } while (1);

    //wiki links, example [[wiki:About]] links to wiki.php?Wiki:About
    //example 2: [[wiki:About|read about us]] links to wiki.php?Wiki:About but "read about us" is link text
    //example 3: [[link:page.php|click here]] makes a clickable link
    do {
        $pos1 = strpos($text, '[[');
        if ($pos1 === false) break;

        $pos2 = strpos($text, ']]');
        if ($pos2 === false) break;

        $wiki_command = substr($text, $pos1+strlen('[['), $pos2-$pos1-strlen(']]'));

        $link = array();
        if (strpos($wiki_command, '|') !== false) {
            list($link['coded'], $link['title']) = explode('|', $wiki_command);
        } else {
            $link['coded'] = $wiki_command;
        }

        $arr = explode(':', $link['coded']);
        $link['cmd'] = $arr[0];
        $link['param'] = '';
        for ($i=1; $i<count($arr); $i++) {
            $link['param'] .= ($i>1?':':'').$arr[$i];
        }

        if (empty($link['cmd'])) continue;

        $result = '';

        switch ($link['cmd']) {
            case 'wiki':
                if (!empty($link['title'])) {
                    //[[wiki:About|read about us]] format
                    $result = '<a href="wiki.php?Wiki:'.$link['param'].'">'.$link['title'].'</a>';
                } else {
                    //[[wiki:About]] format
                    $result = '<a href="wiki.php?Wiki:'.$link['param'].'">'.$link['param'].'</a>';
                }
                break;

            case 'link':
                $result = '<a href="'.$link['param'].'">'.$link['title'].'</a>';
                break;

            case 'file':
                $result = makeImageLink($link['param']);
                break;

            case 'video':
                $url = '/video/'.$link['param'].'.flv';
                $result = embedFlashVideo($url, 176, 144, '', false);
                break;

            case 'audio':
                $url = '/audio/'.$link['param'].'.mp3';
                $result = embedFlashAudio($url, 176, 60, '', '/core_dev/gfx/voice_play.png', false);
                break;

            default:
                if (!empty($link['title'])) {
                    //[[About|read about us]] format
                    $result = '<a href="wiki.php?Wiki:'.$link['cmd'].'">'.$link['title'].'</a>';
                } else {
                    //[[About]] format
                    $result = '<a href="wiki.php?Wiki:'.$link['cmd'].'">'.$link['cmd'].'</a>';
                }
                break;
        }

        if (!$result) $result = '['.$wiki_command.']';

        $text = substr($text, 0, $pos1) .$result. substr($text, $pos2+strlen(']]'));
    } while (1);

    //TODO: add [img]url[/img] tagg för bildlänkning! och checka för intern länkning

    $text = replaceEMails($text);

    $text = nl2br($text);
    $text = str_replace('(_br_)', "\n", $text);

    return $text;
}

?>
