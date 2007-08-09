/*
SQLyog Community Edition- MySQL GUI v6.02
Host - 5.0.41-community-nt-log : Database - dbSample
*********************************************************************
Server version : 5.0.41-community-nt-log
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `tblBlogs` */

CREATE TABLE `tblBlogs` (
  `blogId` int(10) unsigned NOT NULL auto_increment,
  `userId` int(10) unsigned NOT NULL default '0',
  `blogTitle` varchar(200) default NULL,
  `blogBody` text,
  `timeCreated` datetime default NULL,
  `timeUpdated` datetime default NULL,
  `categoryId` bigint(20) unsigned NOT NULL default '0',
  `deletedBy` int(10) unsigned NOT NULL default '0',
  `timeDeleted` datetime default NULL,
  `rating` tinyint(3) unsigned NOT NULL default '0',
  `ratingCnt` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`blogId`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;

/*Table structure for table `tblCategories` */

CREATE TABLE `tblCategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `categoryName` varchar(200) default NULL,
  `categoryType` tinyint(3) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `creatorId` int(10) unsigned NOT NULL default '0',
  `categoryPermissions` tinyint(3) unsigned NOT NULL default '0',
  `ownerId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=utf8;

/*Table structure for table `tblComments` */

CREATE TABLE `tblComments` (
  `commentId` bigint(20) unsigned NOT NULL auto_increment,
  `commentType` tinyint(3) unsigned NOT NULL default '0',
  `commentText` text,
  `commentPrivate` tinyint(4) NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `timeDeleted` datetime default NULL,
  `deletedBy` smallint(5) unsigned NOT NULL default '0',
  `ownerId` bigint(20) unsigned NOT NULL default '0',
  `userId` smallint(5) unsigned NOT NULL default '0',
  `userIP` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`commentId`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

/*Table structure for table `tblContacts` */

CREATE TABLE `tblContacts` (
  `contactId` int(10) unsigned NOT NULL auto_increment,
  `contactType` tinyint(3) unsigned NOT NULL default '0',
  `groupId` int(10) unsigned NOT NULL default '0',
  `userId` int(10) unsigned NOT NULL default '0',
  `otherUserId` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  PRIMARY KEY  (`contactId`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Table structure for table `tblFAQ` */

CREATE TABLE `tblFAQ` (
  `faqId` int(10) unsigned NOT NULL auto_increment,
  `question` text,
  `answer` text,
  `createdBy` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  PRIMARY KEY  (`faqId`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `tblFeedback` */

CREATE TABLE `tblFeedback` (
  `feedbackId` int(10) unsigned NOT NULL auto_increment,
  `feedbackType` tinyint(3) unsigned NOT NULL default '0',
  `text` text,
  `text2` text,
  `userId` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `subjectId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`feedbackId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `tblFiles` */

CREATE TABLE `tblFiles` (
  `fileId` bigint(20) unsigned NOT NULL auto_increment,
  `fileName` varchar(200) default NULL,
  `fileSize` bigint(20) unsigned NOT NULL default '0',
  `fileMime` varchar(200) default NULL,
  `ownerId` int(10) unsigned NOT NULL default '0',
  `categoryId` int(10) unsigned NOT NULL default '0',
  `uploaderId` int(10) unsigned NOT NULL default '0',
  `uploaderIP` bigint(20) unsigned NOT NULL default '0',
  `fileType` tinyint(3) unsigned NOT NULL default '0',
  `timeUploaded` datetime default NULL,
  `cnt` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fileId`)
) ENGINE=MyISAM AUTO_INCREMENT=109 DEFAULT CHARSET=utf8;

/*Table structure for table `tblForums` */

CREATE TABLE `tblForums` (
  `itemId` bigint(20) unsigned NOT NULL auto_increment,
  `itemType` tinyint(1) unsigned NOT NULL default '0',
  `authorId` int(10) unsigned NOT NULL default '0',
  `parentId` bigint(20) unsigned NOT NULL default '0',
  `itemSubject` varchar(100) default NULL,
  `itemBody` text,
  `timeCreated` datetime default NULL,
  `deletedBy` int(10) unsigned NOT NULL default '0',
  `timeDeleted` datetime default NULL,
  `itemRead` bigint(20) unsigned NOT NULL default '0',
  `sticky` tinyint(1) unsigned NOT NULL default '0',
  `locked` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;

/*Table structure for table `tblFriendRequests` */

CREATE TABLE `tblFriendRequests` (
  `reqId` int(10) unsigned NOT NULL auto_increment,
  `senderId` int(10) unsigned NOT NULL default '0',
  `recieverId` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `categoryId` int(10) unsigned NOT NULL default '0',
  `msg` text,
  PRIMARY KEY  (`reqId`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

/*Table structure for table `tblGuestbooks` */

CREATE TABLE `tblGuestbooks` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `userId` int(10) unsigned NOT NULL default '0',
  `authorId` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `subject` varchar(200) default NULL,
  `body` text,
  `entryDeleted` tinyint(3) unsigned NOT NULL default '0',
  `timeDeleted` datetime default NULL,
  `entryRead` tinyint(3) unsigned NOT NULL default '0',
  `timeRead` datetime default NULL,
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Table structure for table `tblLogins` */

CREATE TABLE `tblLogins` (
  `mainId` int(10) unsigned NOT NULL auto_increment,
  `userId` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `IP` int(10) unsigned NOT NULL default '0',
  `userAgent` text,
  PRIMARY KEY  (`mainId`)
) ENGINE=MyISAM AUTO_INCREMENT=140 DEFAULT CHARSET=utf8;

/*Table structure for table `tblLogs` */

CREATE TABLE `tblLogs` (
  `entryId` int(10) unsigned NOT NULL auto_increment,
  `entryText` text,
  `entryLevel` tinyint(3) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `userId` smallint(5) unsigned NOT NULL default '0',
  `userIP` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=254 DEFAULT CHARSET=utf8;

/*Table structure for table `tblMessages` */

CREATE TABLE `tblMessages` (
  `msgId` bigint(20) unsigned NOT NULL auto_increment,
  `ownerId` int(10) unsigned NOT NULL default '0',
  `fromId` int(10) unsigned NOT NULL default '0',
  `toId` int(10) unsigned NOT NULL default '0',
  `subject` varchar(200) default NULL,
  `body` text,
  `timeCreated` datetime default NULL,
  `timeRead` datetime default NULL,
  `groupId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`msgId`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

/*Table structure for table `tblModeration` */

CREATE TABLE `tblModeration` (
  `queueId` bigint(20) unsigned NOT NULL auto_increment,
  `queueType` tinyint(3) unsigned NOT NULL default '0',
  `itemId` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `creatorId` int(10) unsigned NOT NULL default '0',
  `moderatedBy` int(10) unsigned NOT NULL default '0',
  `timeModerated` datetime default NULL,
  `autoTriggered` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`queueId`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Table structure for table `tblNews` */

CREATE TABLE `tblNews` (
  `newsId` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(200) default NULL,
  `body` text,
  `rss_enabled` tinyint(3) unsigned NOT NULL default '0',
  `creatorId` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `timeEdited` datetime default NULL,
  `editorId` int(10) unsigned NOT NULL default '0',
  `timeToPublish` datetime default NULL,
  `categoryId` int(10) unsigned NOT NULL default '0',
  `deletedBy` int(10) unsigned NOT NULL default '0',
  `timeDeleted` datetime default NULL,
  `rating` tinyint(3) unsigned NOT NULL default '0',
  `ratingCnt` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`newsId`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

/*Table structure for table `tblPollVotes` */

CREATE TABLE `tblPollVotes` (
  `voteId` bigint(20) unsigned NOT NULL auto_increment,
  `pollId` int(10) unsigned NOT NULL default '0',
  `userId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`voteId`)
) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=utf8;

/*Table structure for table `tblPolls` */

CREATE TABLE `tblPolls` (
  `pollId` int(10) unsigned NOT NULL auto_increment,
  `pollType` tinyint(1) unsigned NOT NULL,
  `pollText` text,
  `timeStart` datetime default NULL,
  `timeEnd` datetime default NULL,
  `ownerId` int(10) unsigned NOT NULL default '0',
  `createdBy` int(10) unsigned NOT NULL default '0',
  `deletedBy` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `timeDeleted` datetime default NULL,
  PRIMARY KEY  (`pollId`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

/*Table structure for table `tblRatings` */

CREATE TABLE `tblRatings` (
  `rateId` bigint(20) unsigned NOT NULL auto_increment,
  `type` tinyint(3) unsigned NOT NULL default '0',
  `itemId` bigint(20) unsigned NOT NULL default '0',
  `userId` int(10) unsigned NOT NULL default '0',
  `rating` tinyint(3) unsigned NOT NULL default '0',
  `timeRated` datetime default NULL,
  PRIMARY KEY  (`rateId`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Table structure for table `tblRevisions` */

CREATE TABLE `tblRevisions` (
  `indexId` int(10) unsigned NOT NULL auto_increment,
  `fieldId` bigint(20) unsigned NOT NULL default '0',
  `fieldType` tinyint(3) unsigned NOT NULL default '0',
  `fieldText` text,
  `createdBy` smallint(5) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `categoryId` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`indexId`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

/*Table structure for table `tblSettings` */

CREATE TABLE `tblSettings` (
  `settingId` bigint(20) unsigned NOT NULL auto_increment,
  `ownerId` smallint(5) unsigned NOT NULL default '0',
  `settingName` varchar(200) default NULL,
  `settingValue` text,
  `settingType` tinyint(3) unsigned NOT NULL default '0',
  `timeSaved` datetime default NULL,
  PRIMARY KEY  (`settingId`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

/*Table structure for table `tblStatistics` */

CREATE TABLE `tblStatistics` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `time` datetime default NULL,
  `logins` int(10) unsigned NOT NULL default '0',
  `registrations` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `tblStopwords` */

CREATE TABLE `tblStopwords` (
  `wordId` smallint(5) unsigned NOT NULL auto_increment,
  `wordText` varchar(200) default NULL,
  `wordType` tinyint(3) unsigned NOT NULL default '0',
  `wordMatch` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`wordId`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Table structure for table `tblSubscriptions` */

CREATE TABLE `tblSubscriptions` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `type` tinyint(1) unsigned default '0',
  `ownerId` int(10) unsigned NOT NULL default '0',
  `itemId` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Table structure for table `tblUserdata` */

CREATE TABLE `tblUserdata` (
  `fieldId` int(10) unsigned NOT NULL auto_increment,
  `fieldName` varchar(200) default NULL,
  `fieldType` tinyint(3) unsigned NOT NULL default '0',
  `fieldDefault` varchar(200) default NULL,
  `allowTags` tinyint(3) unsigned NOT NULL default '0',
  `private` tinyint(3) unsigned NOT NULL default '0',
  `fieldPriority` tinyint(3) unsigned NOT NULL default '0',
  `regRequire` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fieldId`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

/*Table structure for table `tblUsers` */

CREATE TABLE `tblUsers` (
  `userId` smallint(5) unsigned NOT NULL auto_increment,
  `userName` varchar(200) default NULL,
  `userPass` varchar(200) default NULL,
  `userMode` tinyint(4) NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `timeLastLogin` datetime default NULL,
  `timeLastActive` datetime default NULL,
  `timeLastLogout` datetime default NULL,
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Table structure for table `tblVisits` */

CREATE TABLE `tblVisits` (
  `visitId` int(10) unsigned NOT NULL auto_increment,
  `ownerId` int(10) unsigned NOT NULL default '0',
  `creatorId` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  PRIMARY KEY  (`visitId`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;

/*Table structure for table `tblWiki` */

CREATE TABLE `tblWiki` (
  `wikiId` bigint(20) unsigned NOT NULL auto_increment,
  `wikiName` varchar(200) default NULL,
  `msg` text,
  `timeCreated` datetime default NULL,
  `createdBy` smallint(5) unsigned NOT NULL default '0',
  `lockedBy` smallint(5) unsigned NOT NULL default '0',
  `timeLocked` datetime default NULL,
  `hasFiles` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`wikiId`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;