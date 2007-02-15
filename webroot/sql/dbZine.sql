/*
SQLyog Enterprise - MySQL GUI v5.17
Host - 5.1.12-beta-community-nt : Database - dbZine
*********************************************************************
Server version : 5.1.12-beta-community-nt
*/

SET NAMES utf8;

SET SQL_MODE='';

create database if not exists `dbZine`;

USE `dbZine`;

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `tblAccessgroupFlags` */

DROP TABLE IF EXISTS `tblAccessgroupFlags`;

CREATE TABLE `tblAccessgroupFlags` (
  `flagId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `flagName` varchar(40) NOT NULL DEFAULT '',
  `flagDesc` blob NOT NULL,
  PRIMARY KEY (`flagId`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

/*Data for the table `tblAccessgroupFlags` */

/*Table structure for table `tblAccessgroupMembers` */

DROP TABLE IF EXISTS `tblAccessgroupMembers`;

CREATE TABLE `tblAccessgroupMembers` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `groupId` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblAccessgroupMembers` */

/*Table structure for table `tblAccessgroupSettings` */

DROP TABLE IF EXISTS `tblAccessgroupSettings`;

CREATE TABLE `tblAccessgroupSettings` (
  `groupId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `flagId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `value` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblAccessgroupSettings` */

/*Table structure for table `tblAccessgroups` */

DROP TABLE IF EXISTS `tblAccessgroups`;

CREATE TABLE `tblAccessgroups` (
  `groupId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `groupName` varchar(30) DEFAULT '0',
  PRIMARY KEY (`groupId`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `tblAccessgroups` */

/*Table structure for table `tblBlogs` */

DROP TABLE IF EXISTS `tblBlogs`;

CREATE TABLE `tblBlogs` (
  `blogId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `blogTitle` varchar(100) NOT NULL DEFAULT '',
  `blogBody` blob NOT NULL,
  `timeCreated` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeUpdated` bigint(20) unsigned NOT NULL DEFAULT '0',
  `categoryId` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`blogId`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

/*Data for the table `tblBlogs` */

insert into `tblBlogs` (`blogId`,`userId`,`blogTitle`,`blogBody`,`timeCreated`,`timeUpdated`,`categoryId`) values (52,31,'Jag gnÃ¤ller fÃ¶r jag kan','bläbläblä\r\n22\r\n[quote]bajs[/quote]',1156326074,1156332490,9);

/*Table structure for table `tblBugReports` */

DROP TABLE IF EXISTS `tblBugReports`;

CREATE TABLE `tblBugReports` (
  `bugId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bugDesc` blob NOT NULL,
  `bugCreator` bigint(20) unsigned NOT NULL DEFAULT '0',
  `reportMethod` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `bugClosed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bugClosedReason` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`bugId`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Data for the table `tblBugReports` */

/*Table structure for table `tblCategories` */

DROP TABLE IF EXISTS `tblCategories`;

CREATE TABLE `tblCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(100) NOT NULL DEFAULT '',
  `categoryType` tinyint(3) unsigned DEFAULT '0',
  `timeCreated` bigint(20) unsigned NOT NULL DEFAULT '0',
  `creatorId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `globalCategory` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Data for the table `tblCategories` */

insert into `tblCategories` (`categoryId`,`categoryName`,`categoryType`,`timeCreated`,`creatorId`,`globalCategory`) values (9,'GnÃ¤ll',10,1156325850,31,1);
insert into `tblCategories` (`categoryId`,`categoryName`,`categoryType`,`timeCreated`,`creatorId`,`globalCategory`) values (10,'Skitsnack',10,1156332613,31,1);

/*Table structure for table `tblComments` */

DROP TABLE IF EXISTS `tblComments`;

CREATE TABLE `tblComments` (
  `commentId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `commentType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `commentText` blob,
  `commentTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `commentPrivate` tinyint(1) NOT NULL DEFAULT '0',
  `deletedTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `deletedBy` bigint(20) unsigned NOT NULL DEFAULT '0',
  `ownerId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userIP` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`commentId`)
) ENGINE=MyISAM AUTO_INCREMENT=294 DEFAULT CHARSET=utf8;

/*Data for the table `tblComments` */

/*Table structure for table `tblEstoreBrands` */

DROP TABLE IF EXISTS `tblEstoreBrands`;

CREATE TABLE `tblEstoreBrands` (
  `brandId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brandName` varchar(50) NOT NULL DEFAULT '',
  `imageId` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`brandId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `tblEstoreBrands` */

/*Table structure for table `tblEstoreCategories` */

DROP TABLE IF EXISTS `tblEstoreCategories`;

CREATE TABLE `tblEstoreCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `imageId` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `tblEstoreCategories` */

/*Table structure for table `tblEstoreCategoryDescs` */

DROP TABLE IF EXISTS `tblEstoreCategoryDescs`;

CREATE TABLE `tblEstoreCategoryDescs` (
  `categoryId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `categoryName` varchar(50) NOT NULL DEFAULT '0',
  `lang` varchar(5) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblEstoreCategoryDescs` */

/*Table structure for table `tblEstoreObjectAttributeOptions` */

DROP TABLE IF EXISTS `tblEstoreObjectAttributeOptions`;

CREATE TABLE `tblEstoreObjectAttributeOptions` (
  `attributeId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `optionId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`optionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblEstoreObjectAttributeOptions` */

/*Table structure for table `tblEstoreObjectAttributes` */

DROP TABLE IF EXISTS `tblEstoreObjectAttributes`;

CREATE TABLE `tblEstoreObjectAttributes` (
  `idx` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attributeId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `objectId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lang` varchar(5) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`idx`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblEstoreObjectAttributes` */

/*Table structure for table `tblEstoreObjectDescs` */

DROP TABLE IF EXISTS `tblEstoreObjectDescs`;

CREATE TABLE `tblEstoreObjectDescs` (
  `descId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `objectId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lang` varchar(5) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `info` blob NOT NULL,
  `deliveryTime` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`descId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblEstoreObjectDescs` */

/*Table structure for table `tblEstoreObjectImages` */

DROP TABLE IF EXISTS `tblEstoreObjectImages`;

CREATE TABLE `tblEstoreObjectImages` (
  `indexId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `objectId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `imageId` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`indexId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblEstoreObjectImages` */

/*Table structure for table `tblEstoreObjects` */

DROP TABLE IF EXISTS `tblEstoreObjects`;

CREATE TABLE `tblEstoreObjects` (
  `objectId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `imageId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `categoryId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `brandId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `productCode` varchar(20) NOT NULL DEFAULT '',
  `price` float NOT NULL DEFAULT '0',
  `timeadded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `extraPrice` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`objectId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblEstoreObjects` */

/*Table structure for table `tblFavoriteGames` */

DROP TABLE IF EXISTS `tblFavoriteGames`;

CREATE TABLE `tblFavoriteGames` (
  `indexId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gameId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`indexId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `tblFavoriteGames` */

/*Table structure for table `tblFileCategories` */

DROP TABLE IF EXISTS `tblFileCategories`;

CREATE TABLE `tblFileCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fileType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ownerId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `categoryName` varchar(100) NOT NULL DEFAULT '',
  `timeCreated` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Data for the table `tblFileCategories` */

/*Table structure for table `tblFiles` */

DROP TABLE IF EXISTS `tblFiles`;

CREATE TABLE `tblFiles` (
  `fileId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fileName` varchar(250) DEFAULT NULL,
  `fileSize` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fileMime` varchar(100) DEFAULT NULL,
  `ownerId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `categoryId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uploaderId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uploaderIP` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fileType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `uploadTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `cnt` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fileId`)
) ENGINE=MyISAM AUTO_INCREMENT=210 DEFAULT CHARSET=latin1;

/*Data for the table `tblFiles` */

/*Table structure for table `tblForums` */

DROP TABLE IF EXISTS `tblForums`;

CREATE TABLE `tblForums` (
  `itemId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `itemType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `authorId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `parentId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemSubject` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `itemBody` blob NOT NULL,
  `fileId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemDeleted` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `itemRead` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemVote` bigint(3) unsigned NOT NULL DEFAULT '0',
  `itemVoteCnt` bigint(20) unsigned NOT NULL DEFAULT '0',
  `sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;

/*Data for the table `tblForums` */

/*Table structure for table `tblFriendRequests` */

DROP TABLE IF EXISTS `tblFriendRequests`;

CREATE TABLE `tblFriendRequests` (
  `reqId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `senderId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `recieverId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeCreated` bigint(20) unsigned NOT NULL DEFAULT '0',
  `msg` blob,
  PRIMARY KEY (`reqId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tblFriendRequests` */

/*Table structure for table `tblGeoIP` */

DROP TABLE IF EXISTS `tblGeoIP`;

CREATE TABLE `tblGeoIP` (
  `start` int(10) unsigned NOT NULL,
  `end` int(10) unsigned NOT NULL,
  `countryId` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`start`),
  UNIQUE KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblGeoIP` */

/*Table structure for table `tblGeoIPCities` */

DROP TABLE IF EXISTS `tblGeoIPCities`;

CREATE TABLE `tblGeoIPCities` (
  `start` int(10) unsigned NOT NULL DEFAULT '0',
  `end` int(10) unsigned NOT NULL DEFAULT '0',
  `cityId` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`start`),
  UNIQUE KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblGeoIPCities` */

/*Table structure for table `tblGeoIPCityNames` */

DROP TABLE IF EXISTS `tblGeoIPCityNames`;

CREATE TABLE `tblGeoIPCityNames` (
  `cityId` bigint(20) NOT NULL AUTO_INCREMENT,
  `cityName` varchar(50) NOT NULL DEFAULT '',
  `countryId` int(3) unsigned NOT NULL DEFAULT '0',
  `timeAdded` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cityId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `tblGeoIPCityNames` */

/*Table structure for table `tblGuestbooks` */

DROP TABLE IF EXISTS `tblGuestbooks`;

CREATE TABLE `tblGuestbooks` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `authorId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `body` blob,
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entryDeleted` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `entryRead` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `tblGuestbooks` */

/*Table structure for table `tblInfoFields` */

DROP TABLE IF EXISTS `tblInfoFields`;

CREATE TABLE `tblInfoFields` (
  `fieldId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fieldName` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `fieldText` blob,
  `editedTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `editedBy` bigint(20) unsigned NOT NULL DEFAULT '0',
  `hasFiles` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldId`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

/*Data for the table `tblInfoFields` */

/*Table structure for table `tblInfoFieldsHistory` */

DROP TABLE IF EXISTS `tblInfoFieldsHistory`;

CREATE TABLE `tblInfoFieldsHistory` (
  `fieldId` bigint(20) unsigned NOT NULL,
  `fieldText` blob NOT NULL,
  `editedBy` bigint(20) unsigned NOT NULL DEFAULT '0',
  `editedTime` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblInfoFieldsHistory` */

/*Table structure for table `tblLoginStats` */

DROP TABLE IF EXISTS `tblLoginStats`;

CREATE TABLE `tblLoginStats` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userIp` varchar(15) NOT NULL DEFAULT '0',
  `loggedOut` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=398 DEFAULT CHARSET=latin1;

/*Data for the table `tblLoginStats` */

/*Table structure for table `tblLogs` */

DROP TABLE IF EXISTS `tblLogs`;

CREATE TABLE `tblLogs` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entryText` blob,
  `entryLevel` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `entryTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userIP` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=4480 DEFAULT CHARSET=latin1;

/*Data for the table `tblLogs` */

insert into `tblLogs` (`entryId`,`entryText`,`entryLevel`,`entryTime`,`userId`,`userIP`) values (4477,'Login failed: wrong info',1,1156323716,0,2130706433);
insert into `tblLogs` (`entryId`,`entryText`,`entryLevel`,`entryTime`,`userId`,`userIP`) values (4478,'Login failed: wrong info',1,1156323717,0,2130706433);
insert into `tblLogs` (`entryId`,`entryText`,`entryLevel`,`entryTime`,`userId`,`userIP`) values (4479,'Login failed: wrong info',1,1156323721,0,2130706433);

/*Table structure for table `tblMatchmaking` */

DROP TABLE IF EXISTS `tblMatchmaking`;

CREATE TABLE `tblMatchmaking` (
  `itemId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemText` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblMatchmaking` */

/*Table structure for table `tblMatchmakingAnswers` */

DROP TABLE IF EXISTS `tblMatchmakingAnswers`;

CREATE TABLE `tblMatchmakingAnswers` (
  `itemId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `answerId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblMatchmakingAnswers` */

/*Table structure for table `tblMessageFolders` */

DROP TABLE IF EXISTS `tblMessageFolders`;

CREATE TABLE `tblMessageFolders` (
  `folderId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `folderName` varchar(100) NOT NULL DEFAULT '0',
  `ownerId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `folderType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `parentFolder` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`folderId`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

/*Data for the table `tblMessageFolders` */

/*Table structure for table `tblMessages` */

DROP TABLE IF EXISTS `tblMessages`;

CREATE TABLE `tblMessages` (
  `messageId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `messageOwner` bigint(20) unsigned NOT NULL DEFAULT '0',
  `messageSender` bigint(20) unsigned NOT NULL DEFAULT '0',
  `messageReceiver` bigint(20) unsigned NOT NULL DEFAULT '0',
  `messageSubject` varchar(100) NOT NULL DEFAULT '0',
  `messageBody` text NOT NULL,
  `messageStatus` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `messageFile` bigint(20) unsigned NOT NULL DEFAULT '0',
  `messageDeleted` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `messageFolder` bigint(20) unsigned NOT NULL DEFAULT '0',
  `messageType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`messageId`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

/*Data for the table `tblMessages` */

/*Table structure for table `tblModerationQueue` */

DROP TABLE IF EXISTS `tblModerationQueue`;

CREATE TABLE `tblModerationQueue` (
  `queueId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queueType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `itemId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`queueId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Modereringsk� med olika objekt som beh�ver ';

/*Data for the table `tblModerationQueue` */

/*Table structure for table `tblNews` */

DROP TABLE IF EXISTS `tblNews`;

CREATE TABLE `tblNews` (
  `newsId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `body` blob NOT NULL,
  `rss_enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timecreated` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeedited` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timetopublish` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`newsId`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

/*Data for the table `tblNews` */

/*Table structure for table `tblPhonebooks` */

DROP TABLE IF EXISTS `tblPhonebooks`;

CREATE TABLE `tblPhonebooks` (
  `userId` bigint(20) NOT NULL DEFAULT '0',
  `phoneId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `phonenumber` varchar(30) NOT NULL DEFAULT '',
  `name` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`phoneId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblPhonebooks` */

/*Table structure for table `tblQuicklistGroups` */

DROP TABLE IF EXISTS `tblQuicklistGroups`;

CREATE TABLE `tblQuicklistGroups` (
  `userId` bigint(20) unsigned DEFAULT NULL,
  `groupId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `groupName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`groupId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblQuicklistGroups` */

/*Table structure for table `tblQuicklists` */

DROP TABLE IF EXISTS `tblQuicklists`;

CREATE TABLE `tblQuicklists` (
  `userId` bigint(20) unsigned DEFAULT NULL,
  `data` varchar(30) NOT NULL DEFAULT '',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `groupId` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblQuicklists` */

/*Table structure for table `tblRemoteUsers` */

DROP TABLE IF EXISTS `tblRemoteUsers`;

CREATE TABLE `tblRemoteUsers` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `remoteUserId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `remoteUserGUID` varchar(36) NOT NULL DEFAULT '',
  `randomPassword` bigint(20) unsigned NOT NULL DEFAULT '0',
  `requestTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tblRemoteUsers` */

/*Table structure for table `tblSettings` */

DROP TABLE IF EXISTS `tblSettings`;

CREATE TABLE `tblSettings` (
  `itemId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `settingName` varchar(50) NOT NULL,
  `settingValue` blob NOT NULL,
  `timeSaved` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;

/*Data for the table `tblSettings` */

/*Table structure for table `tblStopwords` */

DROP TABLE IF EXISTS `tblStopwords`;

CREATE TABLE `tblStopwords` (
  `wordId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wordText` varchar(50) DEFAULT '0',
  `wordType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `wordMatch` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`wordId`)
) ENGINE=MyISAM AUTO_INCREMENT=1318580895 DEFAULT CHARSET=latin1;

/*Data for the table `tblStopwords` */

/*Table structure for table `tblSubscriptions` */

DROP TABLE IF EXISTS `tblSubscriptions`;

CREATE TABLE `tblSubscriptions` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `subscriptionType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `subscriptionId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`subscriptionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblSubscriptions` */

/*Table structure for table `tblTimedSubscriptionCategories` */

DROP TABLE IF EXISTS `tblTimedSubscriptionCategories`;

CREATE TABLE `tblTimedSubscriptionCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `categoryName` varchar(50) NOT NULL,
  `timeCreated` bigint(20) NOT NULL,
  `creatorId` bigint(20) NOT NULL,
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

/*Data for the table `tblTimedSubscriptionCategories` */

/*Table structure for table `tblTimedSubscriptions` */

DROP TABLE IF EXISTS `tblTimedSubscriptions`;

CREATE TABLE `tblTimedSubscriptions` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `remindType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `remindMethod` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `remindDest` varchar(50) NOT NULL DEFAULT '',
  `remindOption` bigint(20) unsigned NOT NULL DEFAULT '0',
  `remindMsg` blob,
  `timeCreated` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeStart` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userIP` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `tblTimedSubscriptions` */

/*Table structure for table `tblTodoListCategories` */

DROP TABLE IF EXISTS `tblTodoListCategories`;

CREATE TABLE `tblTodoListCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(50) NOT NULL,
  `parentId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `creatorId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `createdTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

/*Data for the table `tblTodoListCategories` */

/*Table structure for table `tblTodoListComments` */

DROP TABLE IF EXISTS `tblTodoListComments`;

CREATE TABLE `tblTodoListComments` (
  `itemId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemComment` blob NOT NULL,
  `timestamp` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tblTodoListComments` */

/*Table structure for table `tblTodoLists` */

DROP TABLE IF EXISTS `tblTodoLists`;

CREATE TABLE `tblTodoLists` (
  `itemId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categoryId` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `itemDesc` blob,
  `itemDetails` blob NOT NULL,
  `itemStatus` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `itemCategory` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemCreator` bigint(20) NOT NULL DEFAULT '0',
  `assignedTo` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=86 DEFAULT CHARSET=utf8;

/*Data for the table `tblTodoLists` */

/*Table structure for table `tblUserData` */

DROP TABLE IF EXISTS `tblUserData`;

CREATE TABLE `tblUserData` (
  `fieldId` bigint(20) unsigned DEFAULT NULL,
  `userId` bigint(20) unsigned DEFAULT NULL,
  `value` blob
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblUserData` */

/*Table structure for table `tblUserDatafieldOptions` */

DROP TABLE IF EXISTS `tblUserDatafieldOptions`;

CREATE TABLE `tblUserDatafieldOptions` (
  `fieldId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `optionId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `optionName` varchar(50) DEFAULT '0',
  PRIMARY KEY (`optionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblUserDatafieldOptions` */

/*Table structure for table `tblUserDatafields` */

DROP TABLE IF EXISTS `tblUserDatafields`;

CREATE TABLE `tblUserDatafields` (
  `fieldId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fieldName` varchar(30) DEFAULT NULL,
  `fieldType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fieldDefault` varchar(30) NOT NULL DEFAULT '',
  `allowTags` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fieldAccess` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `fieldPriority` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `regRequire` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldId`)
) ENGINE=MyISAM AUTO_INCREMENT=1356210290 DEFAULT CHARSET=latin1;

/*Data for the table `tblUserDatafields` */

/*Table structure for table `tblUsers` */

DROP TABLE IF EXISTS `tblUsers`;

CREATE TABLE `tblUsers` (
  `userId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(20) NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL DEFAULT '0',
  `createdTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lastLoginTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lastActive` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userStatus` blob,
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

/*Data for the table `tblUsers` */

insert into `tblUsers` (`userId`,`userName`,`userPass`,`userMode`,`createdTime`,`lastLoginTime`,`lastActive`,`userStatus`) values (31,'martin','f069e18878ebd5a7cb7e081df29fd56c37206c87',2,0,1156323741,1156347799,'Bloggar');

/*Table structure for table `tblVisitors` */

DROP TABLE IF EXISTS `tblVisitors`;

CREATE TABLE `tblVisitors` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `visitorId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `tblVisitors` */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
