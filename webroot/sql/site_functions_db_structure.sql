# MySQL-Front 3.2  (Build 13.20)

/*!40101 SET NAMES latin1 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='SYSTEM' */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES */;


/*!40101 SET NAMES utf8 */;
CREATE TABLE `tblaccessgroupflags` (
  `flagId` bigint(20) unsigned NOT NULL auto_increment,
  `flagName` varchar(40) NOT NULL default '',
  `flagDesc` blob NOT NULL,
  PRIMARY KEY  (`flagId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblaccessgroupmembers` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `groupId` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblaccessgroupsettings` (
  `groupId` bigint(20) unsigned NOT NULL default '0',
  `flagId` bigint(20) unsigned NOT NULL default '0',
  `value` tinyint(3) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblaccessgroups` (
  `groupId` bigint(20) unsigned NOT NULL auto_increment,
  `groupName` varchar(30) default '0',
  PRIMARY KEY  (`groupId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblcomments` (
  `commentId` bigint(20) unsigned NOT NULL auto_increment,
  `commentType` tinyint(1) unsigned NOT NULL default '0',
  `commentText` blob,
  `commentTime` bigint(20) unsigned NOT NULL default '0',
  `commentPrivate` tinyint(1) NOT NULL default '0',
  `deletedTime` bigint(20) unsigned NOT NULL default '0',
  `deletedBy` bigint(20) unsigned NOT NULL default '0',
  `ownerId` bigint(20) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0',
  `userIP` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`commentId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tblestorebrands` (
  `brandId` bigint(20) unsigned NOT NULL auto_increment,
  `brandName` varchar(50) NOT NULL default '',
  `imageId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`brandId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblestorecategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `imageId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblestorecategorydescs` (
  `categoryId` bigint(20) unsigned NOT NULL default '0',
  `categoryName` varchar(50) NOT NULL default '0',
  `lang` varchar(5) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblestoreobjectattributeoptions` (
  `attributeId` bigint(20) unsigned NOT NULL default '0',
  `optionId` bigint(20) unsigned NOT NULL auto_increment,
  `text` varchar(50) NOT NULL default '0',
  PRIMARY KEY  (`optionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblestoreobjectattributes` (
  `idx` bigint(20) unsigned NOT NULL auto_increment,
  `attributeId` bigint(20) unsigned NOT NULL default '0',
  `objectId` bigint(20) unsigned NOT NULL default '0',
  `lang` varchar(5) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`idx`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblestoreobjectdescs` (
  `descId` bigint(20) unsigned NOT NULL auto_increment,
  `objectId` bigint(20) unsigned NOT NULL default '0',
  `lang` varchar(5) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  `info` blob NOT NULL,
  `deliveryTime` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`descId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblestoreobjectimages` (
  `indexId` bigint(20) unsigned NOT NULL auto_increment,
  `objectId` bigint(20) unsigned NOT NULL default '0',
  `imageId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`indexId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblestoreobjects` (
  `objectId` bigint(20) unsigned NOT NULL auto_increment,
  `imageId` bigint(20) unsigned NOT NULL default '0',
  `categoryId` bigint(20) unsigned NOT NULL default '0',
  `brandId` bigint(20) unsigned NOT NULL default '0',
  `productCode` varchar(20) NOT NULL default '',
  `price` float NOT NULL default '0',
  `timeadded` bigint(20) unsigned NOT NULL default '0',
  `extraPrice` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`objectId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblforums` (
  `itemId` bigint(20) unsigned NOT NULL auto_increment,
  `itemType` tinyint(3) unsigned NOT NULL default '0',
  `authorId` bigint(20) unsigned NOT NULL default '0',
  `parentId` bigint(20) unsigned NOT NULL default '0',
  `itemSubject` varchar(100) character set latin1 NOT NULL default '0',
  `itemBody` blob NOT NULL,
  `fileId` bigint(20) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `itemDeleted` tinyint(3) unsigned NOT NULL default '0',
  `itemRead` bigint(20) unsigned NOT NULL default '0',
  `itemVote` bigint(3) unsigned NOT NULL default '0',
  `itemVoteCnt` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tblguestbooks` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `authorId` bigint(20) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `subject` varchar(255) NOT NULL default '',
  `body` blob,
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `entryDeleted` tinyint(3) unsigned NOT NULL default '0',
  `entryRead` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblloginstats` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `userId` bigint(20) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `userIp` varchar(15) NOT NULL default '0',
  `loggedOut` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblmatchmaking` (
  `itemId` bigint(20) unsigned NOT NULL auto_increment,
  `parentId` bigint(20) unsigned NOT NULL default '0',
  `itemText` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblmatchmakinganswers` (
  `itemId` bigint(20) unsigned NOT NULL default '0',
  `answerId` bigint(20) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblmessagefolders` (
  `folderId` bigint(20) unsigned NOT NULL auto_increment,
  `folderName` varchar(100) NOT NULL default '0',
  `ownerId` bigint(20) unsigned NOT NULL default '0',
  `folderType` tinyint(3) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `parentFolder` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`folderId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblnews` (
  `newsId` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `body` blob NOT NULL,
  `rss_enabled` tinyint(1) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0',
  `timecreated` bigint(20) unsigned NOT NULL default '0',
  `timeedited` bigint(20) unsigned NOT NULL default '0',
  `timetopublish` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`newsId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblphonebooks` (
  `userId` bigint(20) NOT NULL default '0',
  `phoneId` bigint(20) unsigned NOT NULL auto_increment,
  `phonenumber` varchar(30) NOT NULL default '',
  `name` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`phoneId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblquicklistgroups` (
  `userId` bigint(20) unsigned default NULL,
  `groupId` bigint(20) unsigned NOT NULL auto_increment,
  `groupName` varchar(50) default NULL,
  PRIMARY KEY  (`groupId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblquicklists` (
  `userId` bigint(20) unsigned default NULL,
  `data` varchar(30) NOT NULL default '',
  `type` tinyint(3) unsigned NOT NULL default '0',
  `groupId` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblstopwords` (
  `wordId` bigint(20) unsigned NOT NULL auto_increment,
  `wordText` varchar(50) default '0',
  `wordType` tinyint(3) unsigned NOT NULL default '0',
  `wordMatch` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`wordId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tbluserdata` (
  `fieldId` bigint(20) unsigned default NULL,
  `userId` bigint(20) unsigned default NULL,
  `value` blob
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tbluserdatafieldoptions` (
  `fieldId` bigint(20) unsigned NOT NULL default '0',
  `optionId` bigint(20) unsigned NOT NULL auto_increment,
  `optionName` varchar(50) default '0',
  PRIMARY KEY  (`optionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tbluserdatafields` (
  `fieldId` bigint(20) unsigned NOT NULL auto_increment,
  `fieldName` varchar(30) default NULL,
  `fieldType` tinyint(3) unsigned NOT NULL default '0',
  `fieldDefault` varchar(30) NOT NULL default '',
  `allowTags` tinyint(3) unsigned NOT NULL default '0',
  `fieldAccess` tinyint(3) unsigned NOT NULL default '2',
  `fieldPriority` tinyint(3) unsigned NOT NULL default '0',
  `regRequire` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fieldId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblvisitors` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `visitorId` bigint(20) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblblogs` (
  `blogId` bigint(20) unsigned NOT NULL auto_increment,
  `userId` bigint(20) unsigned NOT NULL default '0',
  `blogTitle` varchar(100) NOT NULL default '',
  `blogBody` blob NOT NULL,
  `timeCreated` bigint(20) unsigned NOT NULL default '0',
  `timeUpdated` bigint(20) unsigned NOT NULL default '0',
  `categoryId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`blogId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tblbugreports` (
  `bugId` bigint(20) unsigned NOT NULL auto_increment,
  `bugDesc` blob NOT NULL,
  `bugCreator` bigint(20) unsigned NOT NULL default '0',
  `reportMethod` tinyint(3) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `bugClosed` tinyint(3) unsigned NOT NULL default '0',
  `bugClosedReason` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bugId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tblcategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `categoryName` varchar(100) NOT NULL default '',
  `categoryType` tinyint(3) unsigned default '0',
  `globalCategory` tinyint(1) unsigned NOT NULL default '0',
  `timeCreated` bigint(20) unsigned NOT NULL default '0',
  `creatorId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tblfavoritegames` (
  `indexId` bigint(20) unsigned NOT NULL auto_increment,
  `gameId` bigint(20) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`indexId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tblfilecategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `fileType` tinyint(1) unsigned NOT NULL default '0',
  `ownerId` bigint(20) unsigned NOT NULL default '0',
  `categoryName` varchar(100) NOT NULL default '',
  `timeCreated` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tblfiles` (
  `fileId` bigint(20) unsigned NOT NULL auto_increment,
  `fileName` varchar(250) default NULL,
  `fileSize` bigint(20) unsigned NOT NULL default '0',
  `fileMime` varchar(100) default NULL,
  `ownerId` bigint(20) unsigned NOT NULL default '0',
  `categoryId` bigint(20) unsigned NOT NULL default '0',
  `uploaderId` bigint(20) unsigned NOT NULL default '0',
  `uploaderIP` bigint(20) unsigned NOT NULL default '0',
  `fileType` tinyint(1) unsigned NOT NULL default '0',
  `uploadTime` bigint(20) unsigned NOT NULL default '0',
  `cnt` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fileId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblfriendrequests` (
  `reqId` bigint(20) unsigned NOT NULL auto_increment,
  `senderId` bigint(20) unsigned NOT NULL default '0',
  `recieverId` bigint(20) unsigned NOT NULL default '0',
  `timeCreated` bigint(20) unsigned NOT NULL default '0',
  `msg` blob,
  PRIMARY KEY  (`reqId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tblgeoip` (
  `start` int(10) unsigned NOT NULL,
  `end` int(10) unsigned NOT NULL,
  `countryId` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`start`),
  UNIQUE KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblgeoipcities` (
  `start` int(10) unsigned NOT NULL default '0',
  `end` int(10) unsigned NOT NULL default '0',
  `cityId` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`start`),
  UNIQUE KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblgeoipcitynames` (
  `cityId` bigint(20) NOT NULL auto_increment,
  `cityName` varchar(50) NOT NULL default '',
  `countryId` int(3) unsigned NOT NULL default '0',
  `timeAdded` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`cityId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tblinfofields` (
  `fieldId` bigint(20) unsigned NOT NULL auto_increment,
  `fieldName` varchar(30) character set latin1 default NULL,
  `fieldText` blob,
  `editedTime` bigint(20) unsigned NOT NULL default '0',
  `editedBy` bigint(20) unsigned NOT NULL default '0',
  `hasFiles` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fieldId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tblinfofieldshistory` (
  `fieldId` bigint(20) unsigned NOT NULL,
  `fieldText` blob NOT NULL,
  `editedBy` bigint(20) unsigned NOT NULL default '0',
  `editedTime` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tbllogs` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `entryText` blob,
  `entryLevel` tinyint(1) unsigned NOT NULL default '0',
  `entryTime` bigint(20) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0',
  `userIP` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblmessages` (
  `messageId` bigint(20) unsigned NOT NULL auto_increment,
  `messageOwner` bigint(20) unsigned NOT NULL default '0',
  `messageSender` bigint(20) unsigned NOT NULL default '0',
  `messageReceiver` bigint(20) unsigned NOT NULL default '0',
  `messageSubject` varchar(100) NOT NULL default '0',
  `messageBody` blob NOT NULL,
  `messageStatus` tinyint(3) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `messageFile` bigint(20) unsigned NOT NULL default '0',
  `messageDeleted` tinyint(3) unsigned NOT NULL default '0',
  `messageFolder` bigint(20) unsigned NOT NULL default '0',
  `messageType` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`messageId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblmoderationqueue` (
  `queueId` bigint(20) unsigned NOT NULL auto_increment,
  `queueType` tinyint(3) unsigned NOT NULL default '0',
  `itemId` bigint(20) unsigned NOT NULL default '0',
  `timestamp` bigint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`queueId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblremoteusers` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `remoteUserId` bigint(20) unsigned NOT NULL default '0',
  `remoteUserGUID` varchar(36) NOT NULL default '',
  `randomPassword` bigint(20) unsigned NOT NULL default '0',
  `requestTime` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tblsettings` (
  `itemId` bigint(20) unsigned NOT NULL auto_increment,
  `userId` bigint(20) unsigned NOT NULL default '0',
  `settingName` varchar(50) NOT NULL,
  `settingValue` blob NOT NULL,
  `timeSaved` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tblsubscriptions` (
  `subscriptionId` bigint(20) unsigned NOT NULL auto_increment,
  `userId` bigint(20) unsigned NOT NULL default '0',
  `itemId` bigint(20) unsigned NOT NULL default '0',
  `subscriptionType` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`subscriptionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tbltimedsubscriptioncategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `parentId` bigint(20) unsigned NOT NULL default '0',
  `categoryName` varchar(50) NOT NULL,
  `timeCreated` bigint(20) NOT NULL,
  `creatorId` bigint(20) NOT NULL,
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tbltimedsubscriptions` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `remindType` tinyint(3) unsigned NOT NULL default '0',
  `remindMethod` tinyint(3) unsigned NOT NULL default '0',
  `remindDest` varchar(50) NOT NULL default '',
  `remindOption` bigint(20) unsigned NOT NULL default '0',
  `remindMsg` blob,
  `timeCreated` bigint(20) unsigned NOT NULL default '0',
  `timeStart` bigint(20) unsigned NOT NULL default '0',
  `userIP` bigint(20) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tbltodolistcategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `categoryName` varchar(50) NOT NULL,
  `parentId` bigint(20) unsigned NOT NULL default '0',
  `creatorId` bigint(20) unsigned NOT NULL default '0',
  `createdTime` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tbltodolistcomments` (
  `itemId` bigint(20) unsigned NOT NULL default '0',
  `itemComment` blob NOT NULL,
  `timestamp` bigint(20) NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tbltodolists` (
  `itemId` bigint(20) unsigned NOT NULL auto_increment,
  `categoryId` tinyint(3) unsigned NOT NULL default '0',
  `itemDesc` blob,
  `itemDetails` blob NOT NULL,
  `itemStatus` tinyint(3) unsigned NOT NULL default '0',
  `itemCategory` tinyint(3) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `itemCreator` bigint(20) NOT NULL default '0',
  `assignedTo` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tblusers` (
  `userId` bigint(20) unsigned NOT NULL auto_increment,
  `userName` varchar(20) NOT NULL,
  `userPass` varchar(40) NOT NULL default '',
  `userMode` tinyint(1) NOT NULL default '0',
  `createdTime` bigint(20) unsigned NOT NULL default '0',
  `lastLoginTime` bigint(20) unsigned NOT NULL default '0',
  `lastActive` bigint(20) unsigned NOT NULL default '0',
  `userStatus` blob,
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


/*!40101 SET NAMES latin1 */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
