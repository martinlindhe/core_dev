<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

define('ISSUE_OPEN',    0);
define('ISSUE_ASSIGNED',1);
define('ISSUE_CLOSED',  2);

$issue_status[ISSUE_OPEN]     = 'OPEN';
$issue_status[ISSUE_ASSIGNED] = 'ASSIGNED';
$issue_status[ISSUE_CLOSED]   = 'CLOSED';


/**
 * Creates a new issue ticket
 *
 * @param $userId user id
 * @param $categoryId category id
 * @param $desc description
 * @return id of the created issue or false on error
 */
function addIssue($userId, $categoryId, $desc)
{
	global $db;
	if (!is_numeric($userId) || !is_numeric($categoryId)) return false;

	$q = 'SELECT COUNT(id) FROM tblIssues WHERE description="'.$db->escape($desc).'" AND categoryId='.$categoryId.' AND creatorId='.$userId;
	if ($db->getOneItem($q)) return false;

	$q = 'INSERT INTO tblIssues SET description="'.$db->escape($desc).'",categoryId='.$categoryId.',creatorId='.$userId.',timeCreated=NOW()';
	return $db->insert($q);
}


/**
 * Returns a list of issues
 */
function getIssues($categoryId = 0, $status = 0)
{
	global $db;
	if (!is_numeric($categoryId) || !is_numeric($status)) return false;

	$q  = 'SELECT * FROM tblIssues';
	$q .= ' WHERE categoryId='.$categoryId;
	if ($status) $q .= ' AND status='.$status;
	$q .= ' ORDER By timeCreated ASC';
	return $db->getArray($q);
}


/**
 * Return a list of closed issues
 */
function getClosedIssues($categoryId = 0)
{
	return getIssues($categoryId, ISSUE_CLOSED);
}


/**
 * Returns number of issues
 */
function getIssueCount($categoryId = 0, $status = 0)
{
	global $db;
	if (!is_numeric($categoryId) || !is_numeric($status)) return false;

	$q  = 'SELECT COUNT(id) FROM tblIssues';
	$q .= ' WHERE categoryId='.$categoryId;
	if ($status) $q .= ' AND status='.$status;
	return $db->getOneItem($q);
}

/**
 * Returns number of closed issues
 */
function getClosedIssueCount($categoryId = 0)
{
	return getIssueCount($categoryId, ISSUE_CLOSED);
}





/*************************************
 *************************************
 * CODE BELOW IS NOT YET UPDATED!!!!!!
 *************************************
 ************************************/


$todo_item_category[0] = 'Missing feature';
$todo_item_category[1] = 'Bug';
$todo_item_category[2] = 'Code rewrite';
$todo_item_category[3] = 'Other';

define('CLOSE_BUG_BOGUS',        0);
define('CLOSE_BUG_ALREADYFIXED', 1);

$close_bug_reason[CLOSE_BUG_BOGUS]        = 'BOGUS';
$close_bug_reason[CLOSE_BUG_ALREADYFIXED] = 'ALREADY FIXED';


/**
 * XXX
 */
function getBugReport($bugId)
{
	global $db;
	if (!is_numeric($bugId)) return false;

	$q = 'SELECT tblBugReports.*,tblUsers.userName FROM tblBugReports ';
	$q .= 'INNER JOIN tblUsers ON (tblBugReports.bugCreator=tblUsers.userId) ';
	$q .= 'WHERE bugId='.$bugId;
	return $db->getOneRow($q);
}

/**
 * Flyttar buggrapporten från tblBugReports till tblTodoLists
 * Returnerar ID för det nya todo-itemet
 * userId : den som flyttar buggen, creator = den som skapat buggen
 */
function moveBugReport($bugId, $creator, $desc, $details, $timestamp, $category, $categoryId)
{
	global $h, $db;
	if (!$h->session->id || !is_numeric($bugId) || !is_numeric($creator) || !is_numeric($timestamp) || !is_numeric($category) || !is_numeric($categoryId)) return false;

	$desc = $db->escape($desc);
	$details = $db->escape($details);

	$comment = 'Imported by '.Users::getName($userId).' from a report by '.Users::getName($creator).'.';

	$q = 'INSERT INTO tblTodoLists SET categoryId='.$categoryId.',itemDesc="'.$desc.'",itemDetails="'.$details.'",itemCategory='.$category.',timestamp='.$timestamp.',itemCreator='.$creator;
	$itemId = $db->insert($q);

	$db->delete('DELETE FROM tblBugReports WHERE bugId='.$bugId);

	$q = 'INSERT INTO tblTodoListComments SET itemId='.$itemId.',itemComment="'.$comment.'",timestamp='.time().',userId=0';
	$db->insert($q);

	return $itemId;
}

/**
 * Mark specified bug report as closed
 */
function closeBugReport($bugId, $reason)
{
	global $db;
	if (!is_numeric($bugId) || !is_numeric($reason)) return false;

	$q = 'UPDATE tblBugReports SET bugClosed=1, bugClosedReason='.$reason.' WHERE bugId='.$bugId;
	$db->update($q);
}









/**
 * Ignoring CLOSED items
 */
function getTodoItems($categoryId)
{
	global $db;
	if (!is_numeric($categoryId)) return false;

	$q = 'SELECT * FROM tblTodoLists WHERE categoryId='.$categoryId.' AND itemStatus!='.TODO_ITEM_CLOSED;
	return $db->getArray($q);
}

/**
 * Only returns CLOSED items
 */
function getClosedTodoItems($categoryId)
{
	global $db;
	if (!is_numeric($categoryId)) return false;

	$q = 'SELECT * FROM tblTodoLists WHERE categoryId='.$categoryId.' AND itemStatus='.TODO_ITEM_CLOSED;
	return $db->getArray($q);
}

/**
 * XXX
 */
function addTodoItem($categoryId, $desc, $details, $category)
{
	global $h, $db;
	if (!$h->session->id || !is_numeric($categoryId) || !is_numeric($category)) return false;

	$q = 'INSERT INTO tblTodoLists SET categoryId='.$categoryId.',itemCreator='.$h->session->id.',itemDesc="'.$db->escape($desc).'",itemDetails="'.$db->escape($details).'",itemCategory='.$category.',timeCreated=NOW()';
	$db->insert($q);
}

/**
 * Delete all todo items in a whole category
 */
function deleteTodoItems($categoryId)
{
	global $db;
	if (!is_numeric($categoryId)) return false;

	$q = 'DELETE FROM tblTodoLists WHERE categoryId='.$categoryId;
	$db->delete($q);
	return true;
}

/**
 * XXX
 */
function getTodoItem($itemId)
{
	global $db;
	if (!is_numeric($itemId)) return false;

	if (substr(strtoupper($itemId), 0, 2) == 'PR') $itemId = substr($itemId, 2);

	$q  = 'SELECT tblTodoLists.*,tblUsers.userName FROM tblTodoLists ';
	$q .= 'LEFT JOIN tblsers ON (tblTodoLists.itemCreator = tblUsers.userId) ';
	$q .= 'WHERE itemId='.$itemId;
	return $db->getOneRow($q);
}

/**
 * Move $itemId to category $categoryId
 */
function moveTodoItem($itemId, $categoryId)
{
	global $db;
	if (!is_numeric($itemId) || !is_numeric($categoryId)) return false;

	$q = 'UPDATE tblTodoLists SET categoryId='.$categoryId.' WHERE itemId='.$itemId;
	$db->update($q);
}

/**
 * XXX
 */
function setTodoItemStatus($itemId, $status)
{
	global $db;
	if (!is_numeric($itemId) || !is_numeric($status)) return false;

	$db->update('UPDATE tblTodoLists SET itemStatus='.$status.' WHERE itemId='.$itemId);
}

/**
 * XXX
 */
function assignTodoItem($itemId, $assignedId)
{
	global $db;
	if (!is_numeric($itemId) || !is_numeric($assignedId)) return false;

	$db->update('UPDATE tblTodoLists SET assignedTo='.$assignedId.', itemStatus='.TODO_ITEM_ASSIGNED.' WHERE itemId='.$itemId);
}

/**
 * XXX
 */
function unassignTodoItem($itemId)
{
	global $db;
	if (!is_numeric($itemId)) return false;

	$q = 'UPDATE tblTodoLists SET assignedTo=0, itemStatus='.TODO_ITEM_OPEN.' WHERE itemId='.$itemId;
	$db->update($q);
}

/**
 * Returns only assigned tasks that is not CLOSED
 */
function getAssignedTasks($userId)
{
	global $db;
	if (!is_numeric($userId)) return false;

	$q = 'SELECT * FROM tblTodoLists WHERE assignedTo='.$userId.' AND itemStatus!='.TODO_ITEM_CLOSED.' ORDER BY timestamp ASC';
	return $db->getArray($q);
}

/**
 * XXX
 */
function getAssignedTasksCount($userId)
{
	global $db;
	if (!is_numeric($userId)) return false;

	$q = 'SELECT COUNT(itemId) FROM tblTodoLists WHERE assignedTo='.$userId.' AND itemStatus!='.TODO_ITEM_CLOSED;
	return $db->getOneItem($q);
}

/**
 * Returns only CLOSED assigned tasks
 */
function getClosedAssignedTasks($userId)
{
	global $db;
	if (!is_numeric($userId)) return false;

	$q = 'SELECT * FROM tblTodoLists WHERE assignedTo='.$userId.' AND itemStatus='.TODO_ITEM_CLOSED.' ORDER BY timestamp ASC';
	return $db->getArray($q);
}

/**
 * XXX
 */
function getClosedAssignedTasksCount($userId)
{
	global $db;
	if (!is_numeric($userId)) return false;

	$q = 'SELECT COUNT(itemId) FROM tblTodoLists WHERE assignedTo='.$userId.' AND itemStatus='.TODO_ITEM_CLOSED;
	return $db->getOneItem($q);
}

/**
 * Returns the number of items in a todo category that is not CLOSED
 */
function getTodoCategoryItemsCount($categoryId)
{
	global $db;
	if (!is_numeric($categoryId)) return false;

	$q = 'SELECT COUNT(itemId) FROM tblTodoLists WHERE categoryId='.$categoryId.' AND itemStatus!='.TODO_ITEM_CLOSED;
	return $db->getOneItem($q);
}

/**
 * Returns the number of items in all todo categories that is not CLOSED
 */
function getTodoItemsCount()
{
	global $db;
	$q = 'SELECT COUNT(itemId) FROM tblTodoLists WHERE itemStatus!='.TODO_ITEM_CLOSED;
	return $db->getOneItem($q);
}

/**
 * XXX
 */
function getClosedTodoCategoryItems($categoryId)
{
	global $db;
	if (!is_numeric($categoryId)) return false;

	$q = 'SELECT COUNT(itemId) FROM tblTodoLists WHERE categoryId='.$categoryId.' AND itemStatus='.TODO_ITEM_CLOSED;
	return $db->getOneItem($q);
}


?>
