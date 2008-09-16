<?php
/**
 * $Id$
 *
 * \todo Implement forum "bookmarks", personal favorite list using this module
 *
 * tblSubscriptions
 *  id				subscription id
 *  type			subscription type, SUBSCRIPTION_FORUM, SUBSCRIPTION_BLOG
 *  ownerId			owner of the subscription (userId)
 *  itemId			id of the item we are subscribing to (tblForums.forumId, tblUsers.userId etc)
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

define('SUBSCRIPTION_FORUM',			1);
define('SUBSCRIPTION_BLOG',				2);
define('SUBSCRIPTION_FILES',			3);
define('SUBSCRIPTION_USER_CHATREQ',		4);	//itemid = userid of user who requested chat

$config['subscriptions']['notify'] = false;				//enables automatic notification of subscriptions
$config['subscriptions']['mail_notify'] = false;		//enables automatic notification of subscriptions via e-mail
$config['subscriptions']['subject']['forum'] = '';		//default text
$config['subscriptions']['subject']['blog'] = '';		//default text
$config['subscriptions']['subject']['files'] = '';		//default text
$config['subscriptions']['message']['forum'] = '';		//default text
$config['subscriptions']['message']['blog'] = '';		//default text
$config['subscriptions']['message']['files'] = '';		//default text

/**
 * Creates a subscription of $type on itemId
 *
 * \param $type type of subscription
 * \param $itemId id of item to subscribe to
 * \param $ownerId id of owner
 * \return id of created subscription
 */
function addSubscription($type, $itemId, $ownerId = 0)
{
	global $db, $session;
	if (!$session->id || !is_numeric($type) || !is_numeric($itemId)|| !is_numeric($ownerId)) return false;

	if ($ownerId == 0 && isSubscribed($type, $itemId)) return false;
	$q = 'INSERT INTO tblSubscriptions SET ownerId='.($ownerId==0?$session->id:$ownerId).', itemId='.$itemId.', type='.$type.', timeCreated=NOW()';
	return $db->insert($q);
}

/**
 * Deletes a subscription
 *
 * \param $type type of subscription
 * \param $itemId id to delete
 * \return >0 on success
 */
function removeSubscription($type, $itemId)
{
	global $db, $session;
	if (!$session->id || !is_numeric($type) || !is_numeric($itemId)) return false;

	$q = 'DELETE FROM tblSubscriptions WHERE itemId='.$itemId.' AND type='.$type.' AND ownerId='.$session->id;
	return $db->delete($q);
}

/**
 * Checks if the user is subscribed to this item
 *
 * \param $type type of subscription
 * \param $itemId id of item to check
 * \return true if user is subscribed
 */
function isSubscribed($type, $itemId)
{
	global $db, $session;
	if (!$session->id || !is_numeric($type) || !is_numeric($itemId)) return false;

	$q = 'SELECT id FROM tblSubscriptions WHERE ownerId='.$session->id.' AND type='.$type.' AND itemId='.$itemId;
	if ($db->getOneItem($q)) return true;
	return false;
}

/**
 * Returns array of subscriptions
 *
 * \param $type type of subscription
 * \return array of subscriptions
 */
function getSubscriptions($type)	//FIXME ta userId som parameter
{
	global $db, $session;
	if (!$session->id || !is_numeric($type)) return false;

	switch ($type) {
		case SUBSCRIPTION_FORUM:
			$q = 'SELECT t1.*,t2.itemSubject FROM tblSubscriptions AS t1 ';
			$q .= 'LEFT JOIN tblForums AS t2 ON (t1.itemId=t2.itemId) ';
			$q .= 'WHERE t1.type='.$type.' AND t1.ownerId='.$session->id;
			break;

		default:
			$q = 'SELECT * FROM tblSubscriptions WHERE type='.$type.' AND ownerId='.$session->id;
			break;
	}
	return $db->getArray($q);
}

/**
 * Returns all subscribers for $itemId, only of type $type if specified
 *
 * \param $type type of subscription
 * \param $ownerId owner of subscribable information
 * \return array of subscriptions
 */
function getSubscribers($type, $itemId)
{
	global $db, $session;
	if (!is_numeric($type) || !is_numeric($itemId)) return false;

	$q = 'SELECT * FROM tblSubscriptions WHERE itemId='.$itemId.' AND type='.$type;
	return $db->getArray($q);
}

function notifySubscribers($type, $itemId)
{
	global $db;
	if (!is_numeric($type) || !is_numeric($itemId)) return false;

	$subscribers = getSubscribers($type, $itemId);

	foreach ($subscribers as $subscriber) {
		switch ($type) {
			case SUBSCRIPTION_FORUM:
				$forum = getForumItem($subscriber['itemId']);
				$subject = $config['subscriptions']['subject']['forum'];
				$message = $config['subscriptions']['message']['forum'];
				$pattern = array('/__USERNAME__/', '/__FORUMID__/', '/__FORUMSUBJECT__/');
				$replacement = array(
					$forum['authorName'],
					$forum['itemId'],
					substr($forum['itemBody'],0,15).'...'
				);
				$message = preg_replace($pattern, $replacement, $message);
			case SUBSCRIPTION_BLOG:
				$blog = getBlog($subscriber['itemId']);
				$subject = $config['subscriptions']['subject']['blog'];
				$message = $config['subscriptions']['message']['blog'];
				$pattern = array('/__USERNAME__/', '/__BLOGID__/', '/__BLOGSUBJECT__/');
				$replacement = array(
					$blog['userName'],
					$blog['blogId'],
					$blog['subject']
				);
				$message = preg_replace($pattern, $replacement, $message);
				break;
			case SUBSCRIPTION_FILES:
				$file = getFile($subscriber['itemId']);
				$subject = $config['subscriptions']['subject']['file'];
				$message = $config['subscriptions']['message']['file'];
				$pattern = array('/__USERNAME__/', '/__FILEID__/', '__OWNERID__', '__CATID__');
				$replacement = array(
					Users::getName($file['ownerId']),
					$file['fileId'],
					$file['ownerId'],
					$file['categoryId']
				);
				$message = preg_replace($pattern, $replacement, $message);
				break;
			default: break;
		}
		systemMessage($subscriber['ownerId'], $subject, $message);
		
		if ($config['subscriptions']['mail_notify']) {
			smtp_mail(loadUserdataEmail($subscriber['ownerId']), $subject, $message);
		}
	}

	return true;
}
?>
