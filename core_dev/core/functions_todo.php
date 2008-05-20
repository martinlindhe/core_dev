<?php
/**
 * $Id$
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

define('TODO_ITEM_OPEN',    0); $todo_item_status[TODO_ITEM_OPEN]     = 'OPEN';
define('TODO_ITEM_ASSIGNED',1); $todo_item_status[TODO_ITEM_ASSIGNED] = 'ASSIGNED';
define('TODO_ITEM_CLOSED',  2); $todo_item_status[TODO_ITEM_CLOSED]   = 'CLOSED';

$todo_item_category[0] = 'Missing feature';
$todo_item_category[1] = 'Bug';
$todo_item_category[2] = 'Code rewrite';
$todo_item_category[3] = 'Other';

define('CLOSE_BUG_BOGUS',        0); $close_bug_reason[CLOSE_BUG_BOGUS]        = 'BOGUS';
define('CLOSE_BUG_ALREADYFIXED', 1); $close_bug_reason[CLOSE_BUG_ALREADYFIXED] = 'ALREADY FIXED';


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
	global $db, $session;
	if (!$session->id || !is_numeric($categoryId) || !is_numeric($category)) return false;

	$q = 'INSERT INTO tblTodoLists SET categoryId='.$categoryId.',itemCreator='.$session->id.',itemDesc="'.$db->escape($desc).'",itemDetails="'.$db->escape($details).'",itemCategory='.$category.',timeCreated=NOW()';
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

/**
 * XXX
 */
function addBugReport($desc)
{
	global $db, $session;
	if (!$session->id) return false;
	$desc = $db->escape(strip_tags($desc));

	$q = 'INSERT INTO tblBugReports SET bugDesc="'.$desc.'",bugCreator='.$session->id.',reportMethod=0,timestamp='.time();
	$db->insert($q);
}

/**
 * XXX
 */
function getBugReports()
{
	global $db;
	$q  = 'SELECT tblBugReports.*,tblUsers.userName FROM tblBugReports ';
	$q .= 'LEFT OUTER JOIN tblUsers ON (tblBugReports.bugCreator=tblUsers.userId) ';
	$q .= 'WHERE bugClosed=0 ';
	$q .= 'ORDER By tblBugReports.timestamp ASC';
	return $db->getArray($q);
}

/**
 * XXX
 */
function getClosedBugReports()
{
	global $db;
	$q  = 'SELECT tblBugReports.*,tblUsers.userName FROM tblBugReports ';
	$q .= 'LEFT OUTER JOIN tblUsers ON (tblBugReports.bugCreator=tblUsers.userId) ';
	$q .= 'WHERE bugClosed=1 ';
	$q .= 'ORDER By tblBugReports.timestamp ASC';
	return $db->getArray($q);
}

/**
 * XXX
 */
function getClosedBugReportsCount()
{
	global $db;
	$q = 'SELECT COUNT(bugId) FROM tblBugReports WHERE bugClosed=1';
	return $db->getOneItem($q);
}

/**
 * Returns number of OPEN bugs
 */
function getBugReportsCount()
{
	global $db;
	$q = 'SELECT COUNT(bugId) FROM tblBugReports WHERE bugClosed=0';
	return $db->getOneItem($q);
}

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
	global $db, $session;
	if (!$session->id) return false;
	if (!is_numeric($bugId) || !is_numeric($creator) || !is_numeric($timestamp) || !is_numeric($category) || !is_numeric($categoryId)) return false;

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
?>
