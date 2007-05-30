-- MySQL dump 10.11
--
-- Host: localhost    Database: dbSample
-- ------------------------------------------------------
-- Server version	5.0.41-community-nt-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table tblBlogs
--

CREATE TABLE tblBlogs (
  blogId int unsigned NOT NULL auto_increment,
  userId int unsigned default NULL,
  blogTitle varchar(200) default NULL,
  blogBody text default NULL,
  timeCreated datetime default NULL,
  timeUpdated datetime default NULL,
  categoryId bigint unsigned default NULL,
  deletedBy int unsigned default NULL,
  timeDeleted datetime default NULL,
  rating tinyint unsigned default NULL,
  ratingCnt int unsigned default NULL,
  PRIMARY KEY (blogId)
);

--
-- Table structure for table tblCategories
--

CREATE TABLE tblCategories (
  categoryId bigint unsigned NOT NULL auto_increment,
  categoryName varchar(200) default NULL,
  categoryType tinyint unsigned default NULL,
  timeCreated datetime default NULL,
  creatorId int unsigned default NULL,
  categoryPermissions tinyint unsigned default NULL,
  ownerId bigint unsigned default NULL,
  PRIMARY KEY  (categoryId)
);

--
-- Table structure for table tblComments
--


CREATE TABLE tblComments (
  commentId bigint unsigned NOT NULL auto_increment,
  commentType tinyint unsigned default NULL,
  commentText text,
  commentPrivate tinyint default NULL,
  timeCreated datetime default NULL,
  timeDeleted datetime default NULL,
  deletedBy smallint unsigned default NULL,
  ownerId bigint unsigned default NULL,
  userId smallint unsigned default NULL,
  userIP bigint unsigned default NULL,
  PRIMARY KEY  (commentId)
);

--
-- Table structure for table tblContacts
--

CREATE TABLE tblContacts (
  contactId int unsigned NOT NULL auto_increment,
  contactType tinyint unsigned default NULL,
  groupId int unsigned default NULL,
  userId int unsigned default NULL,
  otherUserId int unsigned default NULL,
  timeCreated datetime default NULL,
  PRIMARY KEY  (contactId)
);

--
-- Table structure for table tblFAQ
--

CREATE TABLE tblFAQ (
  faqId int unsigned NOT NULL auto_increment,
  question text,
  answer text,
  createdBy int unsigned default NULL,
  timeCreated datetime default NULL,
  PRIMARY KEY  (faqId)
);

--
-- Table structure for table tblFeedback
--

CREATE TABLE tblFeedback (
  feedbackId int unsigned NOT NULL auto_increment,
  text text,
  userId int unsigned default NULL,
  timeCreated datetime default NULL,
  PRIMARY KEY  (feedbackId)
);

--
-- Table structure for table tblFiles
--

CREATE TABLE tblFiles (
  fileId bigint unsigned NOT NULL auto_increment,
  fileName varchar(200) character set utf8 default NULL,
  fileSize bigint unsigned default NULL,
  fileMime varchar(200) default NULL,
  ownerId int unsigned default NULL,
  categoryId int unsigned default NULL,
  uploaderId int unsigned default NULL,
  uploaderIP bigint unsigned default NULL,
  fileType tinyint unsigned default NULL,
  timeUploaded datetime default NULL,
  cnt int unsigned default NULL,
  PRIMARY KEY  (fileId)
);

--
-- Table structure for table tblFriendRequests
--

CREATE TABLE tblFriendRequests (
  reqId int unsigned NOT NULL auto_increment,
  senderId int unsigned default NULL,
  recieverId int unsigned default NULL,
  timeCreated datetime default NULL,
  categoryId int unsigned default NULL,
  PRIMARY KEY  (reqId)
);

--
-- Table structure for table tblGuestbooks
--

CREATE TABLE tblGuestbooks (
  entryId bigint unsigned NOT NULL auto_increment,
  userId bigint unsigned default NULL,
  authorId bigint unsigned default NULL,
  timeCreated datetime default NULL,
  subject varchar(200) default NULL,
  body text,
  entryDeleted tinyint unsigned default NULL,
  timeDeleted datetime default NULL,
  entryRead tinyint unsigned default NULL,
  timeRead datetime default NULL,
  PRIMARY KEY  (entryId)
);

--
-- Table structure for table tblLogins
--

CREATE TABLE tblLogins (
  mainId int unsigned NOT NULL auto_increment,
  userId int unsigned default NULL,
  timeCreated datetime default NULL,
  IP int unsigned default NULL,
  userAgent text,
  PRIMARY KEY  (mainId)
);

--
-- Table structure for table tblLogs
--

CREATE TABLE tblLogs (
  entryId mediumint unsigned NOT NULL auto_increment,
  entryText text default NULL,
  entryLevel tinyint unsigned default NULL,
  timeCreated datetime default NULL,
  userId smallint unsigned default NULL,
  userIP int unsigned default NULL,
  PRIMARY KEY  (entryId)
);

--
-- Table structure for table tblMessages
--

CREATE TABLE tblMessages (
  msgId bigint unsigned NOT NULL auto_increment,
  ownerId int unsigned default NULL,
  fromId int unsigned default NULL,
  toId int unsigned default NULL,
  subject varchar(200) default NULL,
  body text,
  timeCreated datetime default NULL,
  timeRead datetime default NULL,
  groupId int unsigned default NULL,
  PRIMARY KEY  (msgId)
);

--
-- Table structure for table tblModerationQueue
--

CREATE TABLE tblModerationQueue (
  queueId bigint unsigned NOT NULL auto_increment,
  queueType tinyint unsigned default NULL,
  itemId int unsigned default NULL,
  timeCreated datetime default NULL,
  creatorId int unsigned default NULL,
  moderatedBy int unsigned default NULL,
  timeModerated datetime default NULL,
  autoTriggered tinyint unsigned default NULL,
  PRIMARY KEY  (queueId)
);

--
-- Table structure for table tblNews
--

CREATE TABLE tblNews (
  newsId int unsigned NOT NULL auto_increment,
  title varchar(200) default NULL,
  body text default NULL,
  rss_enabled tinyint unsigned default NULL,
  creatorId int unsigned default NULL,
  timeCreated datetime default NULL,
  timeEdited datetime default NULL,
  editorId int unsigned default NULL,
  timeToPublish datetime default NULL,
  categoryId int unsigned default NULL,
  deletedBy int unsigned default NULL,
  timeDeleted datetime default NULL,
  rating tinyint unsigned default NULL,
  ratingCnt int unsigned default NULL,
  PRIMARY KEY  (newsId)
);

--
-- Table structure for table tblRatings
--

CREATE TABLE tblRatings (
  rateId bigint unsigned NOT NULL auto_increment,
  type tinyint unsigned default NULL,
  itemId bigint unsigned default NULL,
  userId int unsigned default NULL,
  rating tinyint unsigned default NULL,
  timeRated datetime default NULL,
  PRIMARY KEY  (rateId)
);

--
-- Table structure for table tblRevisions
--

CREATE TABLE tblRevisions (
  indexId int unsigned NOT NULL auto_increment,
  fieldId bigint unsigned default NULL,
  fieldType tinyint unsigned default NULL,
  fieldText text default NULL,
  createdBy smallint unsigned default NULL,
  timeCreated datetime default NULL,
  categoryId tinyint unsigned default NULL,
  PRIMARY KEY  (indexId)
);

--
-- Table structure for table tblSettings
--

CREATE TABLE tblSettings (
  settingId bigint unsigned NOT NULL auto_increment,
  ownerId smallint unsigned default NULL,
  settingName varchar(200) default NULL,
  settingValue text default NULL,
  settingType tinyint unsigned default NULL,
  timeSaved datetime default NULL,
  PRIMARY KEY  (settingId)
);

--
-- Table structure for table tblStatistics
--

CREATE TABLE tblStatistics (
  entryId bigint unsigned NOT NULL auto_increment,
  time datetime default NULL,
  logins int unsigned default NULL,
  registrations int unsigned default NULL,
  PRIMARY KEY  (entryId)
);

--
-- Table structure for table tblStopwords
--

CREATE TABLE tblStopwords (
  wordId smallint unsigned NOT NULL auto_increment,
  wordText varchar(200) default NULL,
  wordType tinyint unsigned default NULL,
  wordMatch tinyint unsigned default NULL,
  PRIMARY KEY  (wordId)
);

--
-- Table structure for table tblUserdata
--

CREATE TABLE tblUserdata (
  fieldId int unsigned NOT NULL auto_increment,
  fieldName varchar(200) default NULL,
  fieldType tinyint unsigned default NULL,
  fieldDefault varchar(200) default NULL,
  allowTags tinyint unsigned default NULL,
  private tinyint unsigned default NULL,
  fieldPriority tinyint unsigned default NULL,
  regRequire tinyint unsigned default NULL,
  PRIMARY KEY  (fieldId)
);

--
-- Table structure for table tblUsers
--

CREATE TABLE tblUsers (
  userId smallint unsigned NOT NULL auto_increment,
  userName varchar(200) default NULL,
  userPass varchar(200) default NULL,
  userMode tinyint default NULL,
  timeCreated datetime default NULL,
  timeLastLogin datetime default NULL,
  timeLastActive datetime default NULL,
  timeLastLogout datetime default NULL,
  PRIMARY KEY  (userId)
);

--
-- Table structure for table tblVisits
--

CREATE TABLE tblVisits (
  visitId int unsigned NOT NULL auto_increment,
  ownerId int unsigned default NULL,
  creatorId int unsigned default NULL,
  timeCreated datetime default NULL,
  PRIMARY KEY  (visitId)
);

--
-- Table structure for table tblWiki
--

CREATE TABLE tblWiki (
  wikiId bigint unsigned NOT NULL auto_increment,
  wikiName varchar(200) default NULL,
  msg text,
  timeCreated datetime default NULL,
  createdBy smallint unsigned default NULL,
  lockedBy smallint unsigned default NULL,
  timeLocked datetime default NULL,
  hasFiles tinyint unsigned default NULL,
  PRIMARY KEY  (wikiId)
);
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2007-05-30 20:50:26
