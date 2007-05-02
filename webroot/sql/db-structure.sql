
CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbpigskin` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbPigskin`;
CREATE TABLE `tblCategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `categoryName` varchar(100) NOT NULL default '',
  `categoryType` tinyint(1) unsigned default '0',
  `timeCreated` datetime NOT NULL,
  `creatorId` int(10) unsigned NOT NULL default '0',
  `categoryPermissions` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `tblFiles` (
  `fileId` bigint(20) unsigned NOT NULL auto_increment,
  `fileName` varchar(250) character set utf8 default NULL,
  `fileSize` bigint(20) unsigned NOT NULL default '0',
  `fileMime` varchar(100) character set utf8 default NULL,
  `ownerId` int(10) unsigned NOT NULL default '0',
  `categoryId` int(10) unsigned NOT NULL default '0',
  `uploaderId` int(10) unsigned NOT NULL default '0',
  `uploaderIP` bigint(20) unsigned NOT NULL default '0',
  `fileType` tinyint(1) unsigned NOT NULL default '0',
  `timeUploaded` datetime NOT NULL,
  `cnt` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fileId`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
CREATE TABLE `tblLogs` (
  `entryId` mediumint(8) unsigned NOT NULL auto_increment,
  `entryText` text character set utf8 NOT NULL,
  `entryLevel` tinyint(1) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `userId` smallint(5) unsigned NOT NULL default '0',
  `userIP` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` int(10) unsigned NOT NULL auto_increment,
  `userName` varchar(20) character set utf8 NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `timeLastLogin` datetime default NULL,
  `timeLastActive` datetime default NULL,
  `timeLastLogout` datetime default NULL,
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbadblock` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `dbadblock`;
CREATE TABLE `tblAdblockRules` (
  `ruleId` bigint(20) unsigned NOT NULL auto_increment,
  `ruleType` tinyint(1) unsigned NOT NULL default '0',
  `ruleText` varchar(240) character set utf8 default NULL,
  `creatorId` smallint(5) unsigned NOT NULL default '0',
  `editorId` smallint(5) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `timeEdited` datetime default NULL,
  `sampleUrl` varchar(200) character set utf8 default NULL,
  `deletedBy` smallint(5) unsigned NOT NULL default '0',
  `timeDeleted` datetime default NULL,
  PRIMARY KEY  (`ruleId`)
) ENGINE=MyISAM AUTO_INCREMENT=688 DEFAULT CHARSET=latin1;
CREATE TABLE `tblCategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `categoryName` varchar(100) NOT NULL default '',
  `categoryType` tinyint(1) unsigned default '0',
  `timeCreated` datetime default NULL,
  `creatorId` int(10) unsigned NOT NULL default '0',
  `categoryPermissions` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
CREATE TABLE `tblComments` (
  `commentId` bigint(20) unsigned NOT NULL auto_increment,
  `commentType` tinyint(1) unsigned NOT NULL default '0',
  `commentText` text,
  `commentPrivate` tinyint(1) NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `timeDeleted` datetime default NULL,
  `deletedBy` smallint(5) unsigned NOT NULL default '0',
  `ownerId` bigint(20) unsigned NOT NULL default '0',
  `userId` smallint(5) unsigned NOT NULL default '0',
  `userIP` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`commentId`)
) ENGINE=MyISAM AUTO_INCREMENT=415 DEFAULT CHARSET=utf8;
CREATE TABLE `tblFiles` (
  `fileId` bigint(20) unsigned NOT NULL auto_increment,
  `fileName` varchar(250) character set utf8 default NULL,
  `fileSize` bigint(20) unsigned NOT NULL default '0',
  `fileMime` varchar(100) character set utf8 default NULL,
  `ownerId` int(10) unsigned NOT NULL default '0',
  `categoryId` int(10) unsigned NOT NULL default '0',
  `uploaderId` int(10) unsigned NOT NULL default '0',
  `uploaderIP` bigint(20) unsigned NOT NULL default '0',
  `fileType` tinyint(1) unsigned NOT NULL default '0',
  `timeUploaded` datetime NOT NULL,
  `cnt` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fileId`)
) ENGINE=MyISAM AUTO_INCREMENT=125 DEFAULT CHARSET=latin1;
CREATE TABLE `tblLogs` (
  `entryId` mediumint(8) unsigned NOT NULL auto_increment,
  `entryText` text character set utf8 NOT NULL,
  `entryLevel` tinyint(1) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `userId` smallint(5) unsigned NOT NULL default '0',
  `userIP` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=569 DEFAULT CHARSET=latin1;
CREATE TABLE `tblNews` (
  `newsId` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) character set utf8 NOT NULL,
  `body` text character set utf8 NOT NULL,
  `rss_enabled` tinyint(1) unsigned NOT NULL default '0',
  `creatorId` int(10) unsigned NOT NULL,
  `timeCreated` datetime NOT NULL,
  `timeEdited` datetime NOT NULL default '0000-00-00 00:00:00',
  `editorId` int(10) unsigned default '0',
  `timeToPublish` datetime NOT NULL default '0000-00-00 00:00:00',
  `categoryId` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`newsId`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
CREATE TABLE `tblProblemSites` (
  `siteId` bigint(20) unsigned NOT NULL auto_increment,
  `userId` smallint(5) unsigned NOT NULL default '0',
  `userIP` bigint(20) unsigned NOT NULL default '0',
  `url` text,
  `type` tinyint(1) unsigned NOT NULL default '0',
  `comment` text,
  `timeCreated` datetime NOT NULL,
  `timeDeleted` datetime NOT NULL default '0000-00-00 00:00:00',
  `deletedBy` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`siteId`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
CREATE TABLE `tblRevisions` (
  `indexId` int(10) unsigned NOT NULL auto_increment,
  `fieldId` bigint(20) unsigned NOT NULL,
  `fieldType` tinyint(3) unsigned default NULL,
  `fieldText` text character set utf8 NOT NULL,
  `createdBy` smallint(5) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `categoryId` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`indexId`)
) ENGINE=MyISAM AUTO_INCREMENT=210 DEFAULT CHARSET=latin1;
CREATE TABLE `tblSettings` (
  `settingId` bigint(20) unsigned NOT NULL auto_increment,
  `ownerId` smallint(5) unsigned NOT NULL default '0',
  `settingName` varchar(50) character set utf8 NOT NULL,
  `settingValue` text character set utf8 NOT NULL,
  `settingType` tinyint(3) unsigned NOT NULL,
  `timeSaved` datetime NOT NULL,
  PRIMARY KEY  (`settingId`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` smallint(5) unsigned NOT NULL auto_increment,
  `userName` varchar(20) character set utf8 NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `timeLastLogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `timeLastActive` datetime NOT NULL default '0000-00-00 00:00:00',
  `timeLastLogout` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
CREATE TABLE `tblWiki` (
  `wikiId` bigint(20) unsigned NOT NULL auto_increment,
  `wikiName` varchar(30) default NULL,
  `msg` text,
  `timeCreated` datetime NOT NULL,
  `createdBy` smallint(5) unsigned NOT NULL default '0',
  `lockedBy` smallint(5) unsigned NOT NULL default '0',
  `timeLocked` datetime NOT NULL default '0000-00-00 00:00:00',
  `hasFiles` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`wikiId`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
DELIMITER ;;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbajaxchat` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbajaxchat`;
CREATE TABLE `tblChat` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `roomId` bigint(20) unsigned NOT NULL,
  `userId` bigint(20) unsigned NOT NULL,
  `timeCreated` datetime NOT NULL,
  `msg` blob,
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
CREATE TABLE `tblChatRooms` (
  `roomId` bigint(20) unsigned NOT NULL auto_increment,
  `roomName` varchar(50) default NULL,
  `timeCreated` datetime NOT NULL,
  `createdBy` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`roomId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
CREATE TABLE `tblChatUsers` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `roomId` bigint(20) unsigned NOT NULL,
  `userId` bigint(20) unsigned NOT NULL,
  `lastSeen` datetime NOT NULL,
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=32154 DEFAULT CHARSET=utf8;
CREATE TABLE `tblLogs` (
  `entryId` mediumint(8) unsigned NOT NULL auto_increment,
  `entryText` text character set utf8 NOT NULL,
  `entryLevel` tinyint(1) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `userId` smallint(5) unsigned NOT NULL default '0',
  `userIP` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` smallint(5) unsigned NOT NULL auto_increment,
  `userName` varchar(20) character set utf8 NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `timeLastLogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `timeLastActive` datetime NOT NULL default '0000-00-00 00:00:00',
  `timeLastLogout` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbajaxsearch` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbajaxsearch`;
CREATE TABLE `tblText` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `txt` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbajaxupload` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbajaxupload`;
CREATE TABLE `tblCategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `categoryName` varchar(100) NOT NULL default '',
  `categoryType` tinyint(3) unsigned default '0',
  `timeCreated` bigint(20) unsigned NOT NULL default '0',
  `creatorId` bigint(20) unsigned NOT NULL default '0',
  `globalCategory` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
CREATE TABLE `tblFiles` (
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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
CREATE TABLE `tblusers` (
  `userId` bigint(20) unsigned NOT NULL auto_increment,
  `userName` varchar(20) NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL default '0',
  `createdTime` bigint(20) unsigned NOT NULL default '0',
  `lastLoginTime` bigint(20) unsigned NOT NULL default '0',
  `lastActive` bigint(20) unsigned NOT NULL default '0',
  `userStatus` blob,
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbcms` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbcms`;
CREATE TABLE `tblAccessgroupFlags` (
  `flagId` bigint(20) unsigned NOT NULL auto_increment,
  `flagName` varchar(40) NOT NULL default '',
  `flagDesc` blob NOT NULL,
  PRIMARY KEY  (`flagId`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
CREATE TABLE `tblAccessgroupMembers` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `groupId` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblAccessgroupSettings` (
  `groupId` bigint(20) unsigned NOT NULL default '0',
  `flagId` bigint(20) unsigned NOT NULL default '0',
  `value` tinyint(3) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblAccessgroups` (
  `groupId` bigint(20) unsigned NOT NULL auto_increment,
  `groupName` varchar(30) default '0',
  PRIMARY KEY  (`groupId`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
CREATE TABLE `tblBlogs` (
  `blogId` bigint(20) unsigned NOT NULL auto_increment,
  `userId` bigint(20) unsigned NOT NULL default '0',
  `blogTitle` varchar(100) NOT NULL default '',
  `blogBody` blob NOT NULL,
  `timeCreated` bigint(20) unsigned NOT NULL default '0',
  `timeUpdated` bigint(20) unsigned NOT NULL default '0',
  `categoryId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`blogId`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;
CREATE TABLE `tblBugReports` (
  `bugId` bigint(20) unsigned NOT NULL auto_increment,
  `bugDesc` blob NOT NULL,
  `bugCreator` bigint(20) unsigned NOT NULL default '0',
  `reportMethod` tinyint(3) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `bugClosed` tinyint(3) unsigned NOT NULL default '0',
  `bugClosedReason` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bugId`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
CREATE TABLE `tblCategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `categoryName` varchar(100) NOT NULL default '',
  `categoryType` tinyint(3) unsigned default '0',
  `timeCreated` bigint(20) unsigned NOT NULL default '0',
  `creatorId` bigint(20) unsigned NOT NULL default '0',
  `globalCategory` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
CREATE TABLE `tblComments` (
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
) ENGINE=MyISAM AUTO_INCREMENT=294 DEFAULT CHARSET=utf8;
CREATE TABLE `tblEstoreBrands` (
  `brandId` bigint(20) unsigned NOT NULL auto_increment,
  `brandName` varchar(50) NOT NULL default '',
  `imageId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`brandId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreCategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `imageId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreCategoryDescs` (
  `categoryId` bigint(20) unsigned NOT NULL default '0',
  `categoryName` varchar(50) NOT NULL default '0',
  `lang` varchar(5) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjectAttributeOptions` (
  `attributeId` bigint(20) unsigned NOT NULL default '0',
  `optionId` bigint(20) unsigned NOT NULL auto_increment,
  `text` varchar(50) NOT NULL default '0',
  PRIMARY KEY  (`optionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjectAttributes` (
  `idx` bigint(20) unsigned NOT NULL auto_increment,
  `attributeId` bigint(20) unsigned NOT NULL default '0',
  `objectId` bigint(20) unsigned NOT NULL default '0',
  `lang` varchar(5) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`idx`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjectDescs` (
  `descId` bigint(20) unsigned NOT NULL auto_increment,
  `objectId` bigint(20) unsigned NOT NULL default '0',
  `lang` varchar(5) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  `info` blob NOT NULL,
  `deliveryTime` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`descId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjectImages` (
  `indexId` bigint(20) unsigned NOT NULL auto_increment,
  `objectId` bigint(20) unsigned NOT NULL default '0',
  `imageId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`indexId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjects` (
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
CREATE TABLE `tblFavoriteGames` (
  `indexId` bigint(20) unsigned NOT NULL auto_increment,
  `gameId` bigint(20) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`indexId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
CREATE TABLE `tblFileCategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `fileType` tinyint(1) unsigned NOT NULL default '0',
  `ownerId` bigint(20) unsigned NOT NULL default '0',
  `categoryName` varchar(100) NOT NULL default '',
  `timeCreated` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
CREATE TABLE `tblFiles` (
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
) ENGINE=MyISAM AUTO_INCREMENT=210 DEFAULT CHARSET=latin1;
CREATE TABLE `tblForums` (
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
  `sticky` tinyint(1) unsigned NOT NULL default '0',
  `locked` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;
CREATE TABLE `tblFriendRequests` (
  `reqId` bigint(20) unsigned NOT NULL auto_increment,
  `senderId` bigint(20) unsigned NOT NULL default '0',
  `recieverId` bigint(20) unsigned NOT NULL default '0',
  `timeCreated` bigint(20) unsigned NOT NULL default '0',
  `msg` blob,
  PRIMARY KEY  (`reqId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `tblGeoIP` (
  `start` int(10) unsigned NOT NULL,
  `end` int(10) unsigned NOT NULL,
  `countryId` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`start`),
  UNIQUE KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblGeoIPCities` (
  `start` int(10) unsigned NOT NULL default '0',
  `end` int(10) unsigned NOT NULL default '0',
  `cityId` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`start`),
  UNIQUE KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblGeoIPCityNames` (
  `cityId` bigint(20) NOT NULL auto_increment,
  `cityName` varchar(50) NOT NULL default '',
  `countryId` int(3) unsigned NOT NULL default '0',
  `timeAdded` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`cityId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
CREATE TABLE `tblGuestbooks` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `authorId` bigint(20) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `subject` varchar(255) NOT NULL default '',
  `body` blob,
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `entryDeleted` tinyint(3) unsigned NOT NULL default '0',
  `entryRead` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
CREATE TABLE `tblInfoFields` (
  `fieldId` bigint(20) unsigned NOT NULL auto_increment,
  `fieldName` varchar(30) character set latin1 default NULL,
  `fieldText` blob,
  `editedTime` bigint(20) unsigned NOT NULL default '0',
  `editedBy` bigint(20) unsigned NOT NULL default '0',
  `hasFiles` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fieldId`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
CREATE TABLE `tblInfoFieldsHistory` (
  `fieldId` bigint(20) unsigned NOT NULL,
  `fieldText` blob NOT NULL,
  `editedBy` bigint(20) unsigned NOT NULL default '0',
  `editedTime` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblLoginStats` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `userId` bigint(20) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `userIp` varchar(15) NOT NULL default '0',
  `loggedOut` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=398 DEFAULT CHARSET=latin1;
CREATE TABLE `tblLogs` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `entryText` blob,
  `entryLevel` tinyint(1) unsigned NOT NULL default '0',
  `entryTime` bigint(20) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0',
  `userIP` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=4477 DEFAULT CHARSET=latin1;
CREATE TABLE `tblMatchmaking` (
  `itemId` bigint(20) unsigned NOT NULL auto_increment,
  `parentId` bigint(20) unsigned NOT NULL default '0',
  `itemText` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblMatchmakingAnswers` (
  `itemId` bigint(20) unsigned NOT NULL default '0',
  `answerId` bigint(20) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblMessageFolders` (
  `folderId` bigint(20) unsigned NOT NULL auto_increment,
  `folderName` varchar(100) NOT NULL default '0',
  `ownerId` bigint(20) unsigned NOT NULL default '0',
  `folderType` tinyint(3) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `parentFolder` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`folderId`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
CREATE TABLE `tblMessages` (
  `messageId` bigint(20) unsigned NOT NULL auto_increment,
  `messageOwner` bigint(20) unsigned NOT NULL default '0',
  `messageSender` bigint(20) unsigned NOT NULL default '0',
  `messageReceiver` bigint(20) unsigned NOT NULL default '0',
  `messageSubject` varchar(100) NOT NULL default '0',
  `messageBody` text NOT NULL,
  `messageStatus` tinyint(3) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `messageFile` bigint(20) unsigned NOT NULL default '0',
  `messageDeleted` tinyint(3) unsigned NOT NULL default '0',
  `messageFolder` bigint(20) unsigned NOT NULL default '0',
  `messageType` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`messageId`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
CREATE TABLE `tblModerationQueue` (
  `queueId` bigint(20) unsigned NOT NULL auto_increment,
  `queueType` tinyint(3) unsigned NOT NULL default '0',
  `itemId` bigint(20) unsigned NOT NULL default '0',
  `timestamp` bigint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`queueId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Modereringskö med olika objekt som behöver ';
CREATE TABLE `tblNews` (
  `newsId` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `body` blob NOT NULL,
  `rss_enabled` tinyint(1) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0',
  `timecreated` bigint(20) unsigned NOT NULL default '0',
  `timeedited` bigint(20) unsigned NOT NULL default '0',
  `timetopublish` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`newsId`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
CREATE TABLE `tblPhonebooks` (
  `userId` bigint(20) NOT NULL default '0',
  `phoneId` bigint(20) unsigned NOT NULL auto_increment,
  `phonenumber` varchar(30) NOT NULL default '',
  `name` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`phoneId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblQuicklistGroups` (
  `userId` bigint(20) unsigned default NULL,
  `groupId` bigint(20) unsigned NOT NULL auto_increment,
  `groupName` varchar(50) default NULL,
  PRIMARY KEY  (`groupId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblQuicklists` (
  `userId` bigint(20) unsigned default NULL,
  `data` varchar(30) NOT NULL default '',
  `type` tinyint(3) unsigned NOT NULL default '0',
  `groupId` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblRemoteUsers` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `remoteUserId` bigint(20) unsigned NOT NULL default '0',
  `remoteUserGUID` varchar(36) NOT NULL default '',
  `randomPassword` bigint(20) unsigned NOT NULL default '0',
  `requestTime` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `tblSettings` (
  `itemId` bigint(20) unsigned NOT NULL auto_increment,
  `userId` bigint(20) unsigned NOT NULL default '0',
  `settingName` varchar(50) NOT NULL,
  `settingValue` blob NOT NULL,
  `timeSaved` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;
CREATE TABLE `tblStopwords` (
  `wordId` bigint(20) unsigned NOT NULL auto_increment,
  `wordText` varchar(50) default '0',
  `wordType` tinyint(3) unsigned NOT NULL default '0',
  `wordMatch` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`wordId`)
) ENGINE=MyISAM AUTO_INCREMENT=1318580895 DEFAULT CHARSET=latin1;
CREATE TABLE `tblSubscriptions` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `itemId` bigint(20) unsigned NOT NULL default '0',
  `subscriptionType` tinyint(3) unsigned NOT NULL default '0',
  `subscriptionId` bigint(20) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`subscriptionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblTimedSubscriptionCategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `parentId` bigint(20) unsigned NOT NULL default '0',
  `categoryName` varchar(50) NOT NULL,
  `timeCreated` bigint(20) NOT NULL,
  `creatorId` bigint(20) NOT NULL,
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
CREATE TABLE `tblTimedSubscriptions` (
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
CREATE TABLE `tblTodoListCategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `categoryName` varchar(50) NOT NULL,
  `parentId` bigint(20) unsigned NOT NULL default '0',
  `creatorId` bigint(20) unsigned NOT NULL default '0',
  `createdTime` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
CREATE TABLE `tblTodoListComments` (
  `itemId` bigint(20) unsigned NOT NULL default '0',
  `itemComment` blob NOT NULL,
  `timestamp` bigint(20) NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `tblTodoLists` (
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
) ENGINE=MyISAM AUTO_INCREMENT=86 DEFAULT CHARSET=utf8;
CREATE TABLE `tblUserData` (
  `fieldId` bigint(20) unsigned default NULL,
  `userId` bigint(20) unsigned default NULL,
  `value` blob
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblUserDatafieldOptions` (
  `fieldId` bigint(20) unsigned NOT NULL default '0',
  `optionId` bigint(20) unsigned NOT NULL auto_increment,
  `optionName` varchar(50) default '0',
  PRIMARY KEY  (`optionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblUserDatafields` (
  `fieldId` bigint(20) unsigned NOT NULL auto_increment,
  `fieldName` varchar(30) default NULL,
  `fieldType` tinyint(3) unsigned NOT NULL default '0',
  `fieldDefault` varchar(30) NOT NULL default '',
  `allowTags` tinyint(3) unsigned NOT NULL default '0',
  `fieldAccess` tinyint(3) unsigned NOT NULL default '2',
  `fieldPriority` tinyint(3) unsigned NOT NULL default '0',
  `regRequire` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fieldId`)
) ENGINE=MyISAM AUTO_INCREMENT=1356210290 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` bigint(20) unsigned NOT NULL auto_increment,
  `userName` varchar(20) NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL default '0',
  `createdTime` bigint(20) unsigned NOT NULL default '0',
  `lastLoginTime` bigint(20) unsigned NOT NULL default '0',
  `lastActive` bigint(20) unsigned NOT NULL default '0',
  `userStatus` blob,
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
CREATE TABLE `tblVisitors` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `visitorId` bigint(20) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbguildsite` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `dbguildsite`;
CREATE TABLE `tblCharacterFlags` (
  `userId` bigint(20) unsigned default '0',
  `flagId` bigint(20) unsigned default '0',
  `timestamp` bigint(20) default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblFlagCategories` (
  `categoryId` tinyint(3) unsigned NOT NULL auto_increment,
  `categoryName` varchar(30) NOT NULL default '0',
  `color` varchar(6) NOT NULL default '',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
CREATE TABLE `tblFlags` (
  `flagId` bigint(20) unsigned NOT NULL auto_increment,
  `flagName` varchar(255) default NULL,
  `flagNote` varchar(255) NOT NULL default '',
  `categoryId` tinyint(3) unsigned default '0',
  `ordernum` bigint(20) unsigned NOT NULL default '0',
  `optional` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`flagId`)
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` bigint(20) unsigned NOT NULL auto_increment,
  `username` varchar(30) NOT NULL default '',
  `password` varchar(60) NOT NULL default '',
  `mode` tinyint(1) unsigned NOT NULL default '0',
  `email` varchar(100) default NULL,
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbjanina` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbjanina`;
CREATE TABLE `tblCategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `categoryName` varchar(100) NOT NULL default '',
  `categoryType` tinyint(1) unsigned default '0',
  `timeCreated` datetime NOT NULL,
  `creatorId` int(10) unsigned NOT NULL default '0',
  `categoryPermissions` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
CREATE TABLE `tblFiles` (
  `fileId` bigint(20) unsigned NOT NULL auto_increment,
  `fileName` varchar(250) character set utf8 default NULL,
  `fileSize` bigint(20) unsigned NOT NULL default '0',
  `fileMime` varchar(100) character set utf8 default NULL,
  `ownerId` int(10) unsigned NOT NULL default '0',
  `categoryId` int(10) unsigned NOT NULL default '0',
  `uploaderId` int(10) unsigned NOT NULL default '0',
  `uploaderIP` bigint(20) unsigned NOT NULL default '0',
  `fileType` tinyint(1) unsigned NOT NULL default '0',
  `timeUploaded` datetime NOT NULL,
  `cnt` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fileId`)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=latin1;
CREATE TABLE `tblLogs` (
  `entryId` mediumint(8) unsigned NOT NULL auto_increment,
  `entryText` text character set utf8 NOT NULL,
  `entryLevel` tinyint(1) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `userId` smallint(5) unsigned NOT NULL default '0',
  `userIP` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` int(10) unsigned NOT NULL auto_increment,
  `userName` varchar(20) character set utf8 NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `timeLastLogin` datetime default NULL,
  `timeLastActive` datetime default NULL,
  `timeLastLogout` datetime default NULL,
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dblang` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dblang`;
CREATE TABLE `tblCategories` (
  `categoryId` smallint(5) unsigned NOT NULL auto_increment,
  `categoryName` varchar(100) NOT NULL default '',
  `categoryType` tinyint(3) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `creatorId` smallint(5) unsigned NOT NULL default '0',
  `globalCategory` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
CREATE TABLE `tblLogs` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `entryText` blob,
  `entryLevel` tinyint(1) unsigned NOT NULL default '0',
  `entryTime` bigint(20) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0',
  `userIP` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblSettings` (
  `settingId` int(10) unsigned NOT NULL auto_increment,
  `settingType` tinyint(3) unsigned NOT NULL,
  `ownerId` int(10) unsigned NOT NULL default '0',
  `settingName` varchar(50) character set utf8 NOT NULL,
  `settingValue` text character set utf8 NOT NULL,
  `timeSaved` datetime NOT NULL,
  PRIMARY KEY  (`settingId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` bigint(20) unsigned NOT NULL auto_increment,
  `userName` varchar(20) NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL default '0',
  `createdTime` datetime NOT NULL,
  `lastLoginTime` datetime NOT NULL,
  `lastActive` datetime NOT NULL,
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
CREATE TABLE `tblWords` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `lang` smallint(5) unsigned NOT NULL,
  `word` varchar(50) NOT NULL,
  `pron` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=462 DEFAULT CHARSET=utf8;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dblyrics` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `dblyrics`;
CREATE TABLE `tblBands` (
  `bandId` bigint(20) unsigned NOT NULL auto_increment,
  `bandName` varchar(40) character set utf8 NOT NULL,
  `bandInfo` text character set utf8,
  `creatorId` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  PRIMARY KEY  (`bandId`)
) ENGINE=MyISAM AUTO_INCREMENT=230 DEFAULT CHARSET=latin1;
CREATE TABLE `tblLogs` (
  `entryId` mediumint(8) unsigned NOT NULL auto_increment,
  `entryText` text character set utf8 NOT NULL,
  `entryLevel` tinyint(1) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `userId` smallint(5) unsigned NOT NULL default '0',
  `userIP` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;
CREATE TABLE `tblLyrics` (
  `lyricId` bigint(20) unsigned NOT NULL auto_increment,
  `lyricName` varchar(200) character set utf8 NOT NULL,
  `lyricText` text character set utf8 NOT NULL,
  `bandId` bigint(20) unsigned NOT NULL default '0',
  `creatorId` bigint(20) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  PRIMARY KEY  (`lyricId`)
) ENGINE=MyISAM AUTO_INCREMENT=4849 DEFAULT CHARSET=latin1;
CREATE TABLE `tblNewAdditions` (
  `ID` bigint(20) unsigned NOT NULL default '0',
  `type` tinyint(3) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblPendingChanges` (
  `type` tinyint(3) unsigned NOT NULL default '0',
  `p1` bigint(20) unsigned NOT NULL default '0',
  `p2` varchar(255) character set utf8 NOT NULL default '0',
  `p3` text character set utf8 NOT NULL,
  `timeCreated` datetime NOT NULL,
  `userId` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblRecords` (
  `recordId` bigint(20) unsigned NOT NULL auto_increment,
  `recordName` varchar(60) character set utf8 NOT NULL,
  `recordInfo` text character set utf8 NOT NULL,
  `bandId` bigint(20) NOT NULL default '0',
  `creatorId` bigint(20) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  PRIMARY KEY  (`recordId`)
) ENGINE=MyISAM AUTO_INCREMENT=597 DEFAULT CHARSET=latin1;
CREATE TABLE `tblTracks` (
  `recordId` bigint(20) NOT NULL default '0',
  `trackNumber` tinyint(3) unsigned NOT NULL default '0',
  `lyricId` bigint(20) unsigned NOT NULL default '0',
  `bandId` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `userName` varchar(30) character set utf8 NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) unsigned NOT NULL default '0',
  `timeLastActive` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbsvnhosting` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbsvnhosting`;
CREATE TABLE `tblLogs` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `entryText` blob,
  `entryLevel` tinyint(1) unsigned NOT NULL default '0',
  `entryTime` bigint(20) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0',
  `userIP` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblRevisions` (
  `indexId` int(10) unsigned NOT NULL auto_increment,
  `fieldId` bigint(20) unsigned NOT NULL,
  `fieldType` tinyint(3) unsigned default NULL,
  `fieldText` text character set utf8 NOT NULL,
  `createdBy` smallint(5) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `categoryId` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`indexId`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
CREATE TABLE `tblSettings` (
  `settingId` int(10) unsigned NOT NULL auto_increment,
  `settingType` tinyint(3) unsigned NOT NULL,
  `ownerId` int(10) unsigned NOT NULL default '0',
  `settingName` varchar(50) character set utf8 NOT NULL,
  `settingValue` text character set utf8 NOT NULL,
  `timeSaved` datetime NOT NULL,
  PRIMARY KEY  (`settingId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` bigint(20) unsigned NOT NULL auto_increment,
  `userName` varchar(20) NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `timeLastLogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `timeLastActive` datetime NOT NULL default '0000-00-00 00:00:00',
  `timeLastLogout` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
CREATE TABLE `tblWiki` (
  `wikiId` bigint(20) unsigned NOT NULL auto_increment,
  `wikiName` varchar(30) default NULL,
  `msg` text,
  `timeCreated` datetime NOT NULL,
  `createdBy` smallint(5) unsigned NOT NULL default '0',
  `lockedBy` smallint(5) unsigned NOT NULL default '0',
  `timeLocked` datetime NOT NULL,
  `hasFiles` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`wikiId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbtracker` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbtracker`;
CREATE TABLE `tblcomments` (
  `commentId` mediumint(8) unsigned NOT NULL auto_increment,
  `commentType` tinyint(1) unsigned NOT NULL default '0',
  `commentText` blob NOT NULL,
  `commentPrivate` tinyint(1) NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `timeDeleted` datetime NOT NULL default '1970-01-01 01:00:00',
  `deletedBy` smallint(5) unsigned NOT NULL default '0',
  `ownerId` bigint(20) unsigned NOT NULL default '0',
  `userId` smallint(5) unsigned NOT NULL default '0',
  `userIP` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`commentId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
CREATE TABLE `tbllocations` (
  `entryId` mediumint(8) unsigned NOT NULL auto_increment,
  `location` varchar(300) NOT NULL,
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=22995 DEFAULT CHARSET=utf8;
CREATE TABLE `tbllogs` (
  `entryId` mediumint(8) unsigned NOT NULL auto_increment,
  `entryText` blob NOT NULL,
  `entryLevel` tinyint(1) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `userId` smallint(5) unsigned NOT NULL default '0',
  `userIP` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
CREATE TABLE `tblreferrers` (
  `entryId` mediumint(8) unsigned NOT NULL auto_increment,
  `referrer` varchar(600) NOT NULL,
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=32703 DEFAULT CHARSET=utf8;
CREATE TABLE `tblsubscriptions` (
  `entryId` smallint(5) unsigned NOT NULL auto_increment,
  `creatorId` smallint(5) unsigned NOT NULL default '0',
  `itemId` smallint(5) unsigned NOT NULL default '0',
  `subscriptionType` tinyint(1) unsigned NOT NULL default '0',
  `recipient` varchar(150) NOT NULL,
  `timeCreated` datetime NOT NULL,
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tbltrackentries` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `trackerId` smallint(5) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `refId` mediumint(8) unsigned NOT NULL,
  `locId` mediumint(8) unsigned NOT NULL,
  `uaId` mediumint(8) unsigned NOT NULL,
  `IP` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=1623196 DEFAULT CHARSET=utf8;
CREATE TABLE `tbltrackpoints` (
  `trackerId` smallint(5) unsigned NOT NULL auto_increment,
  `location` varchar(300) NOT NULL default '',
  `siteId` smallint(5) unsigned NOT NULL default '0',
  `trackerNotes` blob NOT NULL,
  `creatorId` smallint(5) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `timeEdited` datetime NOT NULL,
  `editorId` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`trackerId`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
CREATE TABLE `tbltracksites` (
  `siteId` smallint(5) unsigned NOT NULL auto_increment,
  `creatorId` smallint(5) unsigned NOT NULL default '0',
  `siteName` varchar(300) NOT NULL,
  `siteNotes` blob NOT NULL,
  `timeCreated` datetime NOT NULL,
  `timeEdited` datetime NOT NULL,
  `editorId` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`siteId`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
CREATE TABLE `tbluseragents` (
  `entryId` mediumint(8) unsigned NOT NULL auto_increment,
  `UA` varchar(300) NOT NULL,
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=6318 DEFAULT CHARSET=utf8;
CREATE TABLE `tblusers` (
  `userId` smallint(5) unsigned NOT NULL auto_increment,
  `userName` varchar(20) NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `lastLoginTime` datetime NOT NULL,
  `lastActive` datetime NOT NULL,
  `userStatus` blob NOT NULL,
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbzine` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbzine`;
CREATE TABLE `tblAccessgroupFlags` (
  `flagId` bigint(20) unsigned NOT NULL auto_increment,
  `flagName` varchar(40) NOT NULL default '',
  `flagDesc` blob NOT NULL,
  PRIMARY KEY  (`flagId`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
CREATE TABLE `tblAccessgroupMembers` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `groupId` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblAccessgroupSettings` (
  `groupId` bigint(20) unsigned NOT NULL default '0',
  `flagId` bigint(20) unsigned NOT NULL default '0',
  `value` tinyint(3) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblAccessgroups` (
  `groupId` bigint(20) unsigned NOT NULL auto_increment,
  `groupName` varchar(30) default '0',
  PRIMARY KEY  (`groupId`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
CREATE TABLE `tblBlogs` (
  `blogId` bigint(20) unsigned NOT NULL auto_increment,
  `userId` bigint(20) unsigned NOT NULL default '0',
  `blogTitle` varchar(100) NOT NULL default '',
  `blogBody` blob NOT NULL,
  `timeCreated` bigint(20) unsigned NOT NULL default '0',
  `timeUpdated` bigint(20) unsigned NOT NULL default '0',
  `categoryId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`blogId`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;
CREATE TABLE `tblBugReports` (
  `bugId` bigint(20) unsigned NOT NULL auto_increment,
  `bugDesc` blob NOT NULL,
  `bugCreator` bigint(20) unsigned NOT NULL default '0',
  `reportMethod` tinyint(3) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `bugClosed` tinyint(3) unsigned NOT NULL default '0',
  `bugClosedReason` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bugId`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
CREATE TABLE `tblCategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `categoryName` varchar(100) NOT NULL default '',
  `categoryType` tinyint(3) unsigned default '0',
  `timeCreated` bigint(20) unsigned NOT NULL default '0',
  `creatorId` bigint(20) unsigned NOT NULL default '0',
  `globalCategory` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
CREATE TABLE `tblComments` (
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
) ENGINE=MyISAM AUTO_INCREMENT=294 DEFAULT CHARSET=utf8;
CREATE TABLE `tblEstoreBrands` (
  `brandId` bigint(20) unsigned NOT NULL auto_increment,
  `brandName` varchar(50) NOT NULL default '',
  `imageId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`brandId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreCategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `imageId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreCategoryDescs` (
  `categoryId` bigint(20) unsigned NOT NULL default '0',
  `categoryName` varchar(50) NOT NULL default '0',
  `lang` varchar(5) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjectAttributeOptions` (
  `attributeId` bigint(20) unsigned NOT NULL default '0',
  `optionId` bigint(20) unsigned NOT NULL auto_increment,
  `text` varchar(50) NOT NULL default '0',
  PRIMARY KEY  (`optionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjectAttributes` (
  `idx` bigint(20) unsigned NOT NULL auto_increment,
  `attributeId` bigint(20) unsigned NOT NULL default '0',
  `objectId` bigint(20) unsigned NOT NULL default '0',
  `lang` varchar(5) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`idx`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjectDescs` (
  `descId` bigint(20) unsigned NOT NULL auto_increment,
  `objectId` bigint(20) unsigned NOT NULL default '0',
  `lang` varchar(5) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  `info` blob NOT NULL,
  `deliveryTime` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`descId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjectImages` (
  `indexId` bigint(20) unsigned NOT NULL auto_increment,
  `objectId` bigint(20) unsigned NOT NULL default '0',
  `imageId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`indexId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjects` (
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
CREATE TABLE `tblFavoriteGames` (
  `indexId` bigint(20) unsigned NOT NULL auto_increment,
  `gameId` bigint(20) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`indexId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
CREATE TABLE `tblFileCategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `fileType` tinyint(1) unsigned NOT NULL default '0',
  `ownerId` bigint(20) unsigned NOT NULL default '0',
  `categoryName` varchar(100) NOT NULL default '',
  `timeCreated` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
CREATE TABLE `tblFiles` (
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
) ENGINE=MyISAM AUTO_INCREMENT=210 DEFAULT CHARSET=latin1;
CREATE TABLE `tblForums` (
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
  `sticky` tinyint(1) unsigned NOT NULL default '0',
  `locked` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;
CREATE TABLE `tblFriendRequests` (
  `reqId` bigint(20) unsigned NOT NULL auto_increment,
  `senderId` bigint(20) unsigned NOT NULL default '0',
  `recieverId` bigint(20) unsigned NOT NULL default '0',
  `timeCreated` bigint(20) unsigned NOT NULL default '0',
  `msg` blob,
  PRIMARY KEY  (`reqId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `tblGeoIP` (
  `start` int(10) unsigned NOT NULL,
  `end` int(10) unsigned NOT NULL,
  `countryId` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`start`),
  UNIQUE KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblGeoIPCities` (
  `start` int(10) unsigned NOT NULL default '0',
  `end` int(10) unsigned NOT NULL default '0',
  `cityId` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`start`),
  UNIQUE KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblGeoIPCityNames` (
  `cityId` bigint(20) NOT NULL auto_increment,
  `cityName` varchar(50) NOT NULL default '',
  `countryId` int(3) unsigned NOT NULL default '0',
  `timeAdded` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`cityId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
CREATE TABLE `tblGuestbooks` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `authorId` bigint(20) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `subject` varchar(255) NOT NULL default '',
  `body` blob,
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `entryDeleted` tinyint(3) unsigned NOT NULL default '0',
  `entryRead` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
CREATE TABLE `tblInfoFields` (
  `fieldId` bigint(20) unsigned NOT NULL auto_increment,
  `fieldName` varchar(30) character set latin1 default NULL,
  `fieldText` blob,
  `editedTime` bigint(20) unsigned NOT NULL default '0',
  `editedBy` bigint(20) unsigned NOT NULL default '0',
  `hasFiles` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fieldId`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
CREATE TABLE `tblInfoFieldsHistory` (
  `fieldId` bigint(20) unsigned NOT NULL,
  `fieldText` blob NOT NULL,
  `editedBy` bigint(20) unsigned NOT NULL default '0',
  `editedTime` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblLoginStats` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `userId` bigint(20) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `userIp` varchar(15) NOT NULL default '0',
  `loggedOut` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=398 DEFAULT CHARSET=latin1;
CREATE TABLE `tblLogs` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `entryText` blob,
  `entryLevel` tinyint(1) unsigned NOT NULL default '0',
  `entryTime` bigint(20) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0',
  `userIP` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=4480 DEFAULT CHARSET=latin1;
CREATE TABLE `tblMatchmaking` (
  `itemId` bigint(20) unsigned NOT NULL auto_increment,
  `parentId` bigint(20) unsigned NOT NULL default '0',
  `itemText` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblMatchmakingAnswers` (
  `itemId` bigint(20) unsigned NOT NULL default '0',
  `answerId` bigint(20) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblMessageFolders` (
  `folderId` bigint(20) unsigned NOT NULL auto_increment,
  `folderName` varchar(100) NOT NULL default '0',
  `ownerId` bigint(20) unsigned NOT NULL default '0',
  `folderType` tinyint(3) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `parentFolder` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`folderId`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
CREATE TABLE `tblMessages` (
  `messageId` bigint(20) unsigned NOT NULL auto_increment,
  `messageOwner` bigint(20) unsigned NOT NULL default '0',
  `messageSender` bigint(20) unsigned NOT NULL default '0',
  `messageReceiver` bigint(20) unsigned NOT NULL default '0',
  `messageSubject` varchar(100) NOT NULL default '0',
  `messageBody` text NOT NULL,
  `messageStatus` tinyint(3) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `messageFile` bigint(20) unsigned NOT NULL default '0',
  `messageDeleted` tinyint(3) unsigned NOT NULL default '0',
  `messageFolder` bigint(20) unsigned NOT NULL default '0',
  `messageType` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`messageId`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
CREATE TABLE `tblModerationQueue` (
  `queueId` bigint(20) unsigned NOT NULL auto_increment,
  `queueType` tinyint(3) unsigned NOT NULL default '0',
  `itemId` bigint(20) unsigned NOT NULL default '0',
  `timestamp` bigint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`queueId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Modereringskö med olika objekt som behöver ';
CREATE TABLE `tblNews` (
  `newsId` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `body` blob NOT NULL,
  `rss_enabled` tinyint(1) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0',
  `timecreated` bigint(20) unsigned NOT NULL default '0',
  `timeedited` bigint(20) unsigned NOT NULL default '0',
  `timetopublish` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`newsId`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
CREATE TABLE `tblPhonebooks` (
  `userId` bigint(20) NOT NULL default '0',
  `phoneId` bigint(20) unsigned NOT NULL auto_increment,
  `phonenumber` varchar(30) NOT NULL default '',
  `name` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`phoneId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblQuicklistGroups` (
  `userId` bigint(20) unsigned default NULL,
  `groupId` bigint(20) unsigned NOT NULL auto_increment,
  `groupName` varchar(50) default NULL,
  PRIMARY KEY  (`groupId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblQuicklists` (
  `userId` bigint(20) unsigned default NULL,
  `data` varchar(30) NOT NULL default '',
  `type` tinyint(3) unsigned NOT NULL default '0',
  `groupId` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblRemoteUsers` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `remoteUserId` bigint(20) unsigned NOT NULL default '0',
  `remoteUserGUID` varchar(36) NOT NULL default '',
  `randomPassword` bigint(20) unsigned NOT NULL default '0',
  `requestTime` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `tblSettings` (
  `itemId` bigint(20) unsigned NOT NULL auto_increment,
  `userId` bigint(20) unsigned NOT NULL default '0',
  `settingName` varchar(50) NOT NULL,
  `settingValue` blob NOT NULL,
  `timeSaved` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;
CREATE TABLE `tblStopwords` (
  `wordId` bigint(20) unsigned NOT NULL auto_increment,
  `wordText` varchar(50) default '0',
  `wordType` tinyint(3) unsigned NOT NULL default '0',
  `wordMatch` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`wordId`)
) ENGINE=MyISAM AUTO_INCREMENT=1318580895 DEFAULT CHARSET=latin1;
CREATE TABLE `tblSubscriptions` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `itemId` bigint(20) unsigned NOT NULL default '0',
  `subscriptionType` tinyint(3) unsigned NOT NULL default '0',
  `subscriptionId` bigint(20) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`subscriptionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblTimedSubscriptionCategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `parentId` bigint(20) unsigned NOT NULL default '0',
  `categoryName` varchar(50) NOT NULL,
  `timeCreated` bigint(20) NOT NULL,
  `creatorId` bigint(20) NOT NULL,
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
CREATE TABLE `tblTimedSubscriptions` (
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
CREATE TABLE `tblTodoListCategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `categoryName` varchar(50) NOT NULL,
  `parentId` bigint(20) unsigned NOT NULL default '0',
  `creatorId` bigint(20) unsigned NOT NULL default '0',
  `createdTime` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
CREATE TABLE `tblTodoListComments` (
  `itemId` bigint(20) unsigned NOT NULL default '0',
  `itemComment` blob NOT NULL,
  `timestamp` bigint(20) NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `tblTodoLists` (
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
) ENGINE=MyISAM AUTO_INCREMENT=86 DEFAULT CHARSET=utf8;
CREATE TABLE `tblUserData` (
  `fieldId` bigint(20) unsigned default NULL,
  `userId` bigint(20) unsigned default NULL,
  `value` blob
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblUserDatafieldOptions` (
  `fieldId` bigint(20) unsigned NOT NULL default '0',
  `optionId` bigint(20) unsigned NOT NULL auto_increment,
  `optionName` varchar(50) default '0',
  PRIMARY KEY  (`optionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblUserDatafields` (
  `fieldId` bigint(20) unsigned NOT NULL auto_increment,
  `fieldName` varchar(30) default NULL,
  `fieldType` tinyint(3) unsigned NOT NULL default '0',
  `fieldDefault` varchar(30) NOT NULL default '',
  `allowTags` tinyint(3) unsigned NOT NULL default '0',
  `fieldAccess` tinyint(3) unsigned NOT NULL default '2',
  `fieldPriority` tinyint(3) unsigned NOT NULL default '0',
  `regRequire` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fieldId`)
) ENGINE=MyISAM AUTO_INCREMENT=1356210290 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` bigint(20) unsigned NOT NULL auto_increment,
  `userName` varchar(20) NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL default '0',
  `createdTime` bigint(20) unsigned NOT NULL default '0',
  `lastLoginTime` bigint(20) unsigned NOT NULL default '0',
  `lastActive` bigint(20) unsigned NOT NULL default '0',
  `userStatus` blob,
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;
CREATE TABLE `tblVisitors` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `visitorId` bigint(20) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `online_game` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `online_game`;
CREATE TABLE `tblCharacterAbilityScores` (
  `charId` bigint(20) unsigned NOT NULL default '0',
  `charSTR` tinyint(3) unsigned NOT NULL default '0',
  `charDEX` tinyint(3) unsigned NOT NULL default '0',
  `charCON` tinyint(3) unsigned NOT NULL default '0',
  `charINT` tinyint(3) unsigned NOT NULL default '0',
  `charWIS` tinyint(3) unsigned NOT NULL default '0',
  `charCHA` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`charId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblCharacterCoords` (
  `charId` bigint(20) NOT NULL default '0',
  `zone` tinyint(3) unsigned NOT NULL default '0',
  `x` float NOT NULL default '0',
  `y` float NOT NULL default '0',
  `z` float NOT NULL default '0',
  PRIMARY KEY  (`charId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblCharacters` (
  `charId` bigint(20) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0',
  `charName` varchar(30) NOT NULL default '',
  `charGender` tinyint(1) unsigned NOT NULL default '0',
  `charRace` tinyint(1) unsigned NOT NULL default '0',
  `timeCreated` bigint(20) unsigned NOT NULL default '0',
  `timeLastPlayed` bigint(20) unsigned NOT NULL default '0',
  `timePlayed` bigint(20) unsigned NOT NULL default '0',
  `playedCount` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`charId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblCharactersessions` (
  `idx` bigint(20) unsigned NOT NULL auto_increment,
  `charId` bigint(20) unsigned NOT NULL default '0',
  `loggedin` bigint(20) unsigned NOT NULL default '0',
  `loggedout` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`idx`)
) ENGINE=MyISAM AUTO_INCREMENT=183 DEFAULT CHARSET=latin1 COMMENT='InnehÃ¥ller logintimestamp samt hur lÃ¤nge man spelade';
CREATE TABLE `tblGameServers` (
  `serverId` tinyint(1) unsigned NOT NULL auto_increment,
  `serverIP` varchar(15) NOT NULL default '',
  `serverName` varchar(25) NOT NULL default '',
  `serverOnline` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`serverId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
CREATE TABLE `tblGuildMembers` (
  `charId` bigint(20) unsigned NOT NULL default '0',
  `guildId` bigint(20) unsigned NOT NULL default '0',
  `timeJoinedGuild` bigint(20) unsigned NOT NULL default '0',
  `guildMemberType` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`charId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblGuilds` (
  `guildId` bigint(20) unsigned NOT NULL auto_increment,
  `guildName` varchar(40) NOT NULL default '',
  `creatorId` bigint(20) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`guildId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `online_site` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `online_site`;
CREATE TABLE `tblBugReports` (
  `bugId` bigint(20) unsigned NOT NULL auto_increment,
  `bugDesc` blob NOT NULL,
  `bugCreator` bigint(20) unsigned NOT NULL default '0',
  `reportMethod` tinyint(3) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `bugClosed` tinyint(3) unsigned NOT NULL default '0',
  `bugClosedReason` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bugId`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
CREATE TABLE `tblContentCodes` (
  `code` bigint(20) unsigned NOT NULL default '0',
  `months` tinyint(1) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `used` tinyint(1) unsigned NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0',
  `usedTimestamp` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblCountries` (
  `countryId` bigint(20) unsigned NOT NULL auto_increment,
  `countryName` varchar(50) NOT NULL default '',
  `countrySuffix` varchar(5) NOT NULL default '',
  `timezoneId` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`countryId`)
) ENGINE=MyISAM AUTO_INCREMENT=199 DEFAULT CHARSET=latin1;
CREATE TABLE `tblForums` (
  `itemId` bigint(20) unsigned NOT NULL auto_increment,
  `itemType` tinyint(3) unsigned NOT NULL default '0',
  `authorId` bigint(20) unsigned NOT NULL default '0',
  `parentId` bigint(20) unsigned NOT NULL default '0',
  `itemSubject` varchar(100) NOT NULL default '0',
  `itemBody` blob NOT NULL,
  `fileId` bigint(20) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `itemDeleted` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
CREATE TABLE `tblLoginAttempts` (
  `idx` bigint(20) unsigned NOT NULL auto_increment,
  `userId` bigint(20) unsigned NOT NULL default '0',
  `IP` varchar(30) NOT NULL default '',
  `loggedin` bigint(20) NOT NULL default '0',
  `loggedout` bigint(20) unsigned NOT NULL default '0',
  `bygame` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`idx`)
) ENGINE=MyISAM AUTO_INCREMENT=246 DEFAULT CHARSET=latin1 COMMENT='timeplayed anvÃ¤nds inte fÃ¶r sajtinlogg';
CREATE TABLE `tblMailActivation` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `activationCode` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblNews` (
  `itemId` int(10) unsigned NOT NULL auto_increment,
  `subject` varchar(200) NOT NULL default '',
  `body` blob NOT NULL,
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
CREATE TABLE `tblNewsletters` (
  `itemId` bigint(20) unsigned NOT NULL auto_increment,
  `subject` varchar(200) NOT NULL default '',
  `body` blob NOT NULL,
  `headers` blob NOT NULL,
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `recievers` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
CREATE TABLE `tblServerDowntimes` (
  `itemId` bigint(20) unsigned NOT NULL auto_increment,
  `info` blob NOT NULL,
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
CREATE TABLE `tblTimezones` (
  `zoneId` tinyint(3) unsigned NOT NULL default '0',
  `zoneName` varchar(40) NOT NULL default '',
  `zoneGMT` smallint(5) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblTodoListComments` (
  `itemId` bigint(20) unsigned NOT NULL default '0',
  `itemComment` blob NOT NULL,
  `timestamp` bigint(20) NOT NULL default '0',
  `userId` bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblTodoLists` (
  `itemId` bigint(20) unsigned NOT NULL auto_increment,
  `listId` tinyint(4) NOT NULL default '0',
  `itemDesc` varchar(100) NOT NULL default '',
  `itemDetails` blob NOT NULL,
  `itemStatus` tinyint(3) unsigned NOT NULL default '0',
  `itemCategory` tinyint(3) unsigned NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `itemCreator` bigint(20) NOT NULL default '0',
  `assignedTo` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUserAddress` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `timezone` tinyint(3) unsigned NOT NULL default '0',
  `realName` varchar(50) NOT NULL default '',
  `gender` tinyint(3) unsigned NOT NULL default '0',
  `userMail` varchar(50) NOT NULL default '',
  `userMailSecret` tinyint(3) unsigned NOT NULL default '0',
  `adrPhoneHome` varchar(20) NOT NULL default '',
  `adrCountry` tinyint(3) unsigned NOT NULL default '0',
  `adrCity` varchar(50) NOT NULL default '',
  `adrZipcode` varchar(10) NOT NULL default '',
  `adrStreet` varchar(60) NOT NULL default '',
  `newsletter` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblUserBilling` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `ccNumber` bigint(20) unsigned NOT NULL default '0',
  `ccExpireMonth` tinyint(2) unsigned NOT NULL default '0',
  `ccExpireYear` int(10) unsigned NOT NULL default '0',
  `ccExtraCode` varchar(11) NOT NULL default '',
  `ccOwnerName` varchar(60) NOT NULL default '',
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` bigint(20) unsigned NOT NULL auto_increment,
  `userName` varchar(20) NOT NULL default '',
  `userPass` varchar(32) NOT NULL default '',
  `userType` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUserstats` (
  `userId` bigint(20) unsigned NOT NULL default '0',
  `timeCreated` bigint(20) NOT NULL default '0',
  `timeActivated` bigint(20) unsigned NOT NULL default '0',
  `timeExpires` bigint(20) unsigned NOT NULL default '0',
  `timeLastLogin` bigint(20) unsigned NOT NULL default '0',
  `cntLogins` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `phpbttracker` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `phpbttracker`;
CREATE TABLE `adminusers` (
  `username` varchar(32) NOT NULL,
  `password` varchar(32) default NULL,
  `category` varchar(10) default NULL,
  `comment` varchar(200) default NULL,
  `enabled` enum('Y','N') NOT NULL default 'Y',
  `disable_reason` varchar(255) NOT NULL default '',
  `perm_add` enum('N','Y') NOT NULL default 'Y',
  `perm_addext` enum('N','Y') NOT NULL default 'N',
  `perm_mirror` enum('Y','N') NOT NULL default 'N',
  `perm_edit` enum('N','Y') NOT NULL default 'Y',
  `perm_delete` enum('N','Y') NOT NULL default 'Y',
  `perm_retire` enum('N','Y') NOT NULL default 'Y',
  `perm_unhide` enum('N','Y') NOT NULL default 'Y',
  `perm_peers` enum('N','Y') NOT NULL default 'Y',
  `perm_viewconf` enum('N','Y') NOT NULL default 'N',
  `perm_retiredmgmt` enum('N','Y') NOT NULL default 'Y',
  `perm_ipban` enum('N','Y') NOT NULL default 'N',
  `perm_usermgmt` enum('N','Y') NOT NULL default 'N',
  `perm_advsort` enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `ipbans` (
  `ban_id` bigint(20) unsigned NOT NULL auto_increment,
  `ip` varchar(16) NOT NULL,
  `iplong` int(10) NOT NULL default '0',
  `bandate` date NOT NULL default '0000-00-00',
  `reason` varchar(50) NOT NULL,
  `autoban` enum('Y','N') NOT NULL default 'N',
  `banlength` tinyint(3) unsigned NOT NULL default '0',
  `banexpiry` double NOT NULL default '0',
  `banautoexpires` enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY  (`ban_id`),
  KEY `bandate` (`bandate`,`autoban`,`banexpiry`,`banautoexpires`,`iplong`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `logins` (
  `id` int(11) NOT NULL auto_increment,
  `used` tinyint(1) NOT NULL default '0',
  `ipaddr` varchar(16) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
CREATE TABLE `namemap` (
  `info_hash` varchar(40) NOT NULL default '',
  `filename` varchar(250) NOT NULL default '',
  `url` varchar(250) NOT NULL default '',
  `mirrorurl` varchar(250) NOT NULL default '',
  `info` varchar(250) NOT NULL default '',
  `size` float NOT NULL default '0',
  `crc32` varchar(254) NOT NULL default '',
  `DateAdded` date NOT NULL default '0000-00-00',
  `category` varchar(10) NOT NULL default 'main',
  `sfvlink` varchar(250) default NULL,
  `md5link` varchar(250) default NULL,
  `infolink` varchar(250) default NULL,
  `DateToRemoveURL` date NOT NULL default '0000-00-00',
  `DateToHideTorrent` date NOT NULL default '0000-00-00',
  `addedby` varchar(32) NOT NULL default 'root',
  `grouping` int(5) unsigned NOT NULL default '0',
  `sorting` int(5) unsigned NOT NULL default '0',
  `comment` varchar(255) NOT NULL default '',
  `show_comment` enum('Y','N') NOT NULL default 'N',
  `tsAdded` bigint(20) NOT NULL default '0',
  `torrent_size` bigint(10) NOT NULL default '0',
  PRIMARY KEY  (`info_hash`),
  KEY `category` (`category`,`DateToHideTorrent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `retired` (
  `info_hash` varchar(40) NOT NULL,
  `filename` varchar(250) NOT NULL,
  `size` float NOT NULL default '0',
  `crc32` varchar(254) NOT NULL,
  `category` varchar(10) NOT NULL,
  `completed` int(11) NOT NULL default '0',
  `transferred` bigint(20) NOT NULL default '0',
  `dateadded` date NOT NULL default '0000-00-00',
  `dateretired` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`info_hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `subgrouping` (
  `group_id` bigint(10) unsigned NOT NULL auto_increment,
  `heading` text NOT NULL,
  `groupsort` int(5) unsigned NOT NULL default '0',
  `category` varchar(10) NOT NULL default 'main',
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `summary` (
  `info_hash` char(40) NOT NULL,
  `dlbytes` bigint(20) unsigned NOT NULL default '0',
  `seeds` int(10) unsigned NOT NULL default '0',
  `leechers` int(10) unsigned NOT NULL default '0',
  `finished` int(10) unsigned NOT NULL default '0',
  `lastcycle` int(10) unsigned NOT NULL default '0',
  `lastSpeedCycle` int(10) unsigned NOT NULL default '0',
  `lastAvgCycle` int(10) unsigned NOT NULL default '0',
  `speed` bigint(20) unsigned NOT NULL default '0',
  `hide_torrent` enum('N','Y') default 'N',
  `avgdone` float NOT NULL default '0',
  `external_torrent` enum('N','Y') default 'N',
  `ext_no_scrape_update` enum('N','Y') default 'N',
  PRIMARY KEY  (`info_hash`),
  KEY `hide_torrent` (`hide_torrent`,`external_torrent`,`ext_no_scrape_update`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `timestamps` (
  `info_hash` char(40) NOT NULL,
  `sequence` int(10) unsigned NOT NULL auto_increment,
  `bytes` bigint(20) unsigned NOT NULL,
  `delta` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`sequence`),
  KEY `SORTING` (`info_hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `torrents` (
  `info_hash` varchar(40) NOT NULL default '0',
  `name` varchar(255) NOT NULL,
  `metadata` longblob NOT NULL,
  PRIMARY KEY  (`info_hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `trk_ext` (
  `info_hash` char(40) NOT NULL,
  `scrape_url` varchar(255) NOT NULL,
  `last_update` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`info_hash`),
  KEY `scrape_url` (`scrape_url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
DELIMITER ;;
DELIMITER ;

