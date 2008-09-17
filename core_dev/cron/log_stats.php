<?php
	/* Log stats to be able to present it in the admin interface
	*/

	require_once('find_config.php');

/*

 * IMPLEMENTERA BLOGBESÖKARE!! define('STAT_VIEWS_BLOGS',			31);	///< number of views of all blogs						FIXME IMPLEMENT


define('STAT_COMMENTS_BLOGS',		40);	///< number of comments written to blogs				FIXME IMPLEMENT
define('STAT_COMMENTS_FILES',		41);	///< number of comments written to files/photos			FIXME IMPLEMENT

*/
	$startDate = sql_datetime(strtotime('yesterday'));
	$endDate = sql_datetime(strtotime('yesterday')+86399); // 86399s = 24h - 1s

	$login = Users::getLoginCountPeriod($startDate,$endDate);
	$loginsDistinct = Users::getDistinctLoginCountPeriod($startDate,$endDate);
	$newUser = Users::getNewCountPeriod($startDate,$endDate);
	$newBlog = getBlogsCountPeriod($startDate,$endDate);
	$newGb = getGuestbookCountPeriod($startDate,$endDate);
	$newMail = getMessageCountPeriod($startDate,$endDate);
	$newChat = getChatMessagesCountPeriod($startDate,$endDate);
	$newForumEntry = getForumEntriesCountPeriod($startDate,$endDate);
	$newFiles = Files::getFilesCountPeriod($startDate,$endDate);
	$newFeedback = getFeedbackCountPeriod($startDate,$endDate);
	$newSubscriptionForum = getSubscriptionsNewCountPeriod($startDate,$endDate, SUBSCRIPTION_FORUM);
	$newSubscriptionBlog = getSubscriptionsNewCountPeriod($startDate,$endDate, SUBSCRIPTION_BLOG);
	$newSubscriptionFile = getSubscriptionsNewCountPeriod($startDate,$endDate, SUBSCRIPTION_FILES);
	$newViewsFile = getVisitsNewCountPeriod($startDate,$endDate, VISIT_FILE);
	$newViewsBlog = getVisitsNewCountPeriod($startDate,$endDate, VISIT_BLOG);
	$newViewsUserPage = getVisitsNewCountPeriod($startDate,$endDate, VISIT_USERPAGE);
	$newCommentBlog = getCommentsCountPeriod($startDate,$endDate, COMMENT_BLOG);
	$newCommentFile = getCommentsCountPeriod($startDate,$endDate, COMMENT_FILE);

	saveStat(STAT_TOTAL_LOGINS, $login, $startDate, $endDate);
	saveStat(STAT_UNIQUE_LOGINS, $loginsDistinct, $startDate, $endDate);
	saveStat(STAT_NEW_USERS, $newUser, $startDate, $endDate);
	saveStat(STAT_NEW_BLOGS, $newBlog, $startDate, $endDate);
	saveStat(STAT_NEW_GUESTBOOK, $newGb, $startDate, $endDate);
	saveStat(STAT_NEW_MESSAGES, $newMail, $startDate, $endDate);
	saveStat(STAT_NEW_CHATMESSAGES, $newChat, $startDate, $endDate);
	saveStat(STAT_NEW_FORUMPOSTS, $newForumEntry, $startDate, $endDate);
	saveStat(STAT_NEW_FILES, $newFiles, $startDate, $endDate);
	saveStat(STAT_NEW_FEEDBACK, $newFeedback, $startDate, $endDate);
	saveStat(STAT_SUBSCRIPTIONS_FORUMS, $newSubscriptionForum, $startDate, $endDate);
	saveStat(STAT_SUBSCRIPTIONS_BLOGS, $newSubscriptionBlog, $startDate, $endDate);
	saveStat(STAT_SUBSCRIPTIONS_FILES, $newSubscriptionFile, $startDate, $endDate);
	saveStat(STAT_VIEWS_FILES, $newViewsFile, $startDate, $endDate);
	saveStat(STAT_VIEWS_BLOGS, $newViewsBlog, $startDate, $endDate);
	saveStat(STAT_VIEWS_PROFILES, $newViewsUserPage, $startDate, $endDate);
	saveStat(STAT_COMMENTS_BLOGS, $newCommentBlog, $startDate, $endDate);
	saveStat(STAT_COMMENTS_FILES, $newCommentFile, $startDate, $endDate);
	
?>
