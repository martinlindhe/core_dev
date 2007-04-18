
CREATE DATABASE /*!32312 IF NOT EXISTS*/ `phpbttracker` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `PHPBTTracker`;
CREATE TABLE `adminusers` (
  `username` varchar(32) NOT NULL,
  `password` varchar(32) DEFAULT NULL,
  `category` varchar(10) DEFAULT NULL,
  `comment` varchar(200) DEFAULT NULL,
  `enabled` enum('Y','N') NOT NULL DEFAULT 'Y',
  `disable_reason` varchar(255) NOT NULL DEFAULT '',
  `perm_add` enum('N','Y') NOT NULL DEFAULT 'Y',
  `perm_addext` enum('N','Y') NOT NULL DEFAULT 'N',
  `perm_mirror` enum('Y','N') NOT NULL DEFAULT 'N',
  `perm_edit` enum('N','Y') NOT NULL DEFAULT 'Y',
  `perm_delete` enum('N','Y') NOT NULL DEFAULT 'Y',
  `perm_retire` enum('N','Y') NOT NULL DEFAULT 'Y',
  `perm_unhide` enum('N','Y') NOT NULL DEFAULT 'Y',
  `perm_peers` enum('N','Y') NOT NULL DEFAULT 'Y',
  `perm_viewconf` enum('N','Y') NOT NULL DEFAULT 'N',
  `perm_retiredmgmt` enum('N','Y') NOT NULL DEFAULT 'Y',
  `perm_ipban` enum('N','Y') NOT NULL DEFAULT 'N',
  `perm_usermgmt` enum('N','Y') NOT NULL DEFAULT 'N',
  `perm_advsort` enum('N','Y') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `ipbans` (
  `ban_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(16) NOT NULL,
  `iplong` int(10) NOT NULL DEFAULT '0',
  `bandate` date NOT NULL DEFAULT '0000-00-00',
  `reason` varchar(50) NOT NULL,
  `autoban` enum('Y','N') NOT NULL DEFAULT 'N',
  `banlength` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `banexpiry` double NOT NULL DEFAULT '0',
  `banautoexpires` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`ban_id`),
  KEY `bandate` (`bandate`,`autoban`,`banexpiry`,`banautoexpires`,`iplong`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `ipaddr` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
CREATE TABLE `namemap` (
  `info_hash` varchar(40) NOT NULL DEFAULT '',
  `filename` varchar(250) NOT NULL DEFAULT '',
  `url` varchar(250) NOT NULL DEFAULT '',
  `mirrorurl` varchar(250) NOT NULL DEFAULT '',
  `info` varchar(250) NOT NULL DEFAULT '',
  `size` float NOT NULL DEFAULT '0',
  `crc32` varchar(254) NOT NULL DEFAULT '',
  `DateAdded` date NOT NULL DEFAULT '0000-00-00',
  `category` varchar(10) NOT NULL DEFAULT 'main',
  `sfvlink` varchar(250) DEFAULT NULL,
  `md5link` varchar(250) DEFAULT NULL,
  `infolink` varchar(250) DEFAULT NULL,
  `DateToRemoveURL` date NOT NULL DEFAULT '0000-00-00',
  `DateToHideTorrent` date NOT NULL DEFAULT '0000-00-00',
  `addedby` varchar(32) NOT NULL DEFAULT 'root',
  `grouping` int(5) unsigned NOT NULL DEFAULT '0',
  `sorting` int(5) unsigned NOT NULL DEFAULT '0',
  `comment` varchar(255) NOT NULL DEFAULT '',
  `show_comment` enum('Y','N') NOT NULL DEFAULT 'N',
  `tsAdded` bigint(20) NOT NULL DEFAULT '0',
  `torrent_size` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`info_hash`),
  KEY `category` (`category`,`DateToHideTorrent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `retired` (
  `info_hash` varchar(40) NOT NULL,
  `filename` varchar(250) NOT NULL,
  `size` float NOT NULL DEFAULT '0',
  `crc32` varchar(254) NOT NULL,
  `category` varchar(10) NOT NULL,
  `completed` int(11) NOT NULL DEFAULT '0',
  `transferred` bigint(20) NOT NULL DEFAULT '0',
  `dateadded` date NOT NULL DEFAULT '0000-00-00',
  `dateretired` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`info_hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `subgrouping` (
  `group_id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `heading` text NOT NULL,
  `groupsort` int(5) unsigned NOT NULL DEFAULT '0',
  `category` varchar(10) NOT NULL DEFAULT 'main',
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `summary` (
  `info_hash` char(40) NOT NULL,
  `dlbytes` bigint(20) unsigned NOT NULL DEFAULT '0',
  `seeds` int(10) unsigned NOT NULL DEFAULT '0',
  `leechers` int(10) unsigned NOT NULL DEFAULT '0',
  `finished` int(10) unsigned NOT NULL DEFAULT '0',
  `lastcycle` int(10) unsigned NOT NULL DEFAULT '0',
  `lastSpeedCycle` int(10) unsigned NOT NULL DEFAULT '0',
  `lastAvgCycle` int(10) unsigned NOT NULL DEFAULT '0',
  `speed` bigint(20) unsigned NOT NULL DEFAULT '0',
  `hide_torrent` enum('N','Y') DEFAULT 'N',
  `avgdone` float NOT NULL DEFAULT '0',
  `external_torrent` enum('N','Y') DEFAULT 'N',
  `ext_no_scrape_update` enum('N','Y') DEFAULT 'N',
  PRIMARY KEY (`info_hash`),
  KEY `hide_torrent` (`hide_torrent`,`external_torrent`,`ext_no_scrape_update`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `timestamps` (
  `info_hash` char(40) NOT NULL,
  `sequence` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bytes` bigint(20) unsigned NOT NULL,
  `delta` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`sequence`),
  KEY `SORTING` (`info_hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `torrents` (
  `info_hash` varchar(40) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `metadata` longblob NOT NULL,
  PRIMARY KEY (`info_hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `trk_ext` (
  `info_hash` char(40) NOT NULL,
  `scrape_url` varchar(255) NOT NULL,
  `last_update` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`info_hash`),
  KEY `scrape_url` (`scrape_url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `cluster` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `cluster`;
CREATE TABLE `binlog_index` (
  `Position` bigint(20) unsigned NOT NULL,
  `File` varchar(255) NOT NULL,
  `epoch` bigint(20) unsigned NOT NULL,
  `inserts` bigint(20) unsigned NOT NULL,
  `updates` bigint(20) unsigned NOT NULL,
  `deletes` bigint(20) unsigned NOT NULL,
  `schemaops` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`epoch`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbajaxchat` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbAJAXChat`;
CREATE TABLE `tblChat` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `roomId` bigint(20) unsigned NOT NULL,
  `userId` bigint(20) unsigned NOT NULL,
  `timeCreated` datetime NOT NULL,
  `msg` blob,
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
CREATE TABLE `tblChatRooms` (
  `roomId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `roomName` varchar(50) DEFAULT NULL,
  `timeCreated` datetime NOT NULL,
  `createdBy` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`roomId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
CREATE TABLE `tblChatUsers` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `roomId` bigint(20) unsigned NOT NULL,
  `userId` bigint(20) unsigned NOT NULL,
  `lastSeen` datetime NOT NULL,
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=31884 DEFAULT CHARSET=utf8;
CREATE TABLE `tblLogs` (
  `entryId` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `entryText` text CHARACTER SET utf8 NOT NULL,
  `entryLevel` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `userId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userIP` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(20) CHARACTER SET utf8 NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `timeLastLogin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timeLastActive` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timeLastLogout` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbajaxsearch` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbAJAXSearch`;
CREATE TABLE `tblText` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `txt` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbajaxupload` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbAJAXUpload`;
CREATE TABLE `tblCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(100) NOT NULL DEFAULT '',
  `categoryType` tinyint(3) unsigned DEFAULT '0',
  `timeCreated` bigint(20) unsigned NOT NULL DEFAULT '0',
  `creatorId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `globalCategory` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
CREATE TABLE `tblusers` (
  `userId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(20) NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL DEFAULT '0',
  `createdTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lastLoginTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lastActive` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userStatus` blob,
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbadblock` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `dbAdblock`;
CREATE TABLE `tblAdblockRules` (
  `ruleId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ruleType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ruleText` varchar(240) CHARACTER SET utf8 DEFAULT NULL,
  `creatorId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `editorId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `timeEdited` datetime DEFAULT NULL,
  `sampleUrl` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `deletedBy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timeDeleted` datetime DEFAULT NULL,
  PRIMARY KEY (`ruleId`)
) ENGINE=MyISAM AUTO_INCREMENT=681 DEFAULT CHARSET=latin1;
CREATE TABLE `tblComments` (
  `commentId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `commentType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `commentText` text,
  `commentPrivate` tinyint(1) NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `timeDeleted` datetime DEFAULT NULL,
  `deletedBy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ownerId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userIP` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`commentId`)
) ENGINE=MyISAM AUTO_INCREMENT=405 DEFAULT CHARSET=utf8;
CREATE TABLE `tblFiles` (
  `fileId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fileName` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `fileSize` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fileMime` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `ownerId` int(10) unsigned NOT NULL DEFAULT '0',
  `categoryId` int(10) unsigned NOT NULL DEFAULT '0',
  `uploaderId` int(10) unsigned NOT NULL DEFAULT '0',
  `uploaderIP` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fileType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timeUploaded` datetime NOT NULL,
  `cnt` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fileId`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;
CREATE TABLE `tblLogs` (
  `entryId` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `entryText` text CHARACTER SET utf8 NOT NULL,
  `entryLevel` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `userId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userIP` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=452 DEFAULT CHARSET=latin1;
CREATE TABLE `tblNews` (
  `newsId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 NOT NULL,
  `body` text CHARACTER SET utf8 NOT NULL,
  `rss_enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `creatorId` int(10) unsigned NOT NULL,
  `timeCreated` datetime NOT NULL,
  `timeEdited` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editorId` int(10) unsigned DEFAULT '0',
  `timeToPublish` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`newsId`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
CREATE TABLE `tblProblemSites` (
  `siteId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userIP` bigint(20) unsigned NOT NULL DEFAULT '0',
  `url` text,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `comment` text,
  `timeCreated` datetime NOT NULL,
  `timeDeleted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deletedBy` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`siteId`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
CREATE TABLE `tblRevisions` (
  `indexId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fieldId` bigint(20) unsigned NOT NULL,
  `fieldType` tinyint(3) unsigned DEFAULT NULL,
  `fieldText` text CHARACTER SET utf8 NOT NULL,
  `createdBy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `categoryId` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`indexId`)
) ENGINE=MyISAM AUTO_INCREMENT=128 DEFAULT CHARSET=latin1;
CREATE TABLE `tblSettings` (
  `settingId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ownerId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `settingName` varchar(50) CHARACTER SET utf8 NOT NULL,
  `settingValue` text CHARACTER SET utf8 NOT NULL,
  `settingType` tinyint(3) unsigned NOT NULL,
  `timeSaved` datetime NOT NULL,
  PRIMARY KEY (`settingId`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(20) CHARACTER SET utf8 NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `timeLastLogin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timeLastActive` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timeLastLogout` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
CREATE TABLE `tblWiki` (
  `wikiId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wikiName` varchar(30) DEFAULT NULL,
  `msg` text,
  `timeCreated` datetime NOT NULL,
  `createdBy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `lockedBy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timeLocked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hasFiles` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`wikiId`,`timeLocked`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
DELIMITER ;;
/*!50003 SET SESSION SQL_MODE=""*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `getUser`(
	usr varchar(50),
	pwd varchar(50)
)
BEGIN
	/* Returns user info if supplied user&pwd is correct, else it returns an empty result */
	SELECT userId,userMode 
	FROM tblUsers
	WHERE userName = usr and userPass = pwd;
END */;;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE*/;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbcms` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbCMS`;
CREATE TABLE `tblAccessgroupFlags` (
  `flagId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `flagName` varchar(40) NOT NULL DEFAULT '',
  `flagDesc` blob NOT NULL,
  PRIMARY KEY (`flagId`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
CREATE TABLE `tblAccessgroupMembers` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `groupId` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblAccessgroupSettings` (
  `groupId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `flagId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `value` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblAccessgroups` (
  `groupId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `groupName` varchar(30) DEFAULT '0',
  PRIMARY KEY (`groupId`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
CREATE TABLE `tblBlogs` (
  `blogId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `blogTitle` varchar(100) NOT NULL DEFAULT '',
  `blogBody` blob NOT NULL,
  `timeCreated` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeUpdated` bigint(20) unsigned NOT NULL DEFAULT '0',
  `categoryId` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`blogId`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;
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
CREATE TABLE `tblCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(100) NOT NULL DEFAULT '',
  `categoryType` tinyint(3) unsigned DEFAULT '0',
  `timeCreated` bigint(20) unsigned NOT NULL DEFAULT '0',
  `creatorId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `globalCategory` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
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
CREATE TABLE `tblEstoreBrands` (
  `brandId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brandName` varchar(50) NOT NULL DEFAULT '',
  `imageId` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`brandId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `imageId` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreCategoryDescs` (
  `categoryId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `categoryName` varchar(50) NOT NULL DEFAULT '0',
  `lang` varchar(5) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjectAttributeOptions` (
  `attributeId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `optionId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`optionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjectAttributes` (
  `idx` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attributeId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `objectId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lang` varchar(5) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`idx`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjectDescs` (
  `descId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `objectId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lang` varchar(5) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `info` blob NOT NULL,
  `deliveryTime` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`descId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjectImages` (
  `indexId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `objectId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `imageId` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`indexId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
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
CREATE TABLE `tblFavoriteGames` (
  `indexId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gameId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`indexId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
CREATE TABLE `tblFileCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fileType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ownerId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `categoryName` varchar(100) NOT NULL DEFAULT '',
  `timeCreated` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
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
CREATE TABLE `tblFriendRequests` (
  `reqId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `senderId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `recieverId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeCreated` bigint(20) unsigned NOT NULL DEFAULT '0',
  `msg` blob,
  PRIMARY KEY (`reqId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `tblGeoIP` (
  `start` int(10) unsigned NOT NULL,
  `end` int(10) unsigned NOT NULL,
  `countryId` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`start`),
  UNIQUE KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblGeoIPCities` (
  `start` int(10) unsigned NOT NULL DEFAULT '0',
  `end` int(10) unsigned NOT NULL DEFAULT '0',
  `cityId` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`start`),
  UNIQUE KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblGeoIPCityNames` (
  `cityId` bigint(20) NOT NULL AUTO_INCREMENT,
  `cityName` varchar(50) NOT NULL DEFAULT '',
  `countryId` int(3) unsigned NOT NULL DEFAULT '0',
  `timeAdded` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cityId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
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
CREATE TABLE `tblInfoFields` (
  `fieldId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fieldName` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `fieldText` blob,
  `editedTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `editedBy` bigint(20) unsigned NOT NULL DEFAULT '0',
  `hasFiles` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldId`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
CREATE TABLE `tblInfoFieldsHistory` (
  `fieldId` bigint(20) unsigned NOT NULL,
  `fieldText` blob NOT NULL,
  `editedBy` bigint(20) unsigned NOT NULL DEFAULT '0',
  `editedTime` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblLoginStats` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userIp` varchar(15) NOT NULL DEFAULT '0',
  `loggedOut` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=398 DEFAULT CHARSET=latin1;
CREATE TABLE `tblLogs` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entryText` blob,
  `entryLevel` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `entryTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userIP` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=4477 DEFAULT CHARSET=latin1;
CREATE TABLE `tblMatchmaking` (
  `itemId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemText` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblMatchmakingAnswers` (
  `itemId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `answerId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblMessageFolders` (
  `folderId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `folderName` varchar(100) NOT NULL DEFAULT '0',
  `ownerId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `folderType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `parentFolder` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`folderId`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
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
CREATE TABLE `tblModerationQueue` (
  `queueId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queueType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `itemId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`queueId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Modereringskö med olika objekt som behöver ';
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
CREATE TABLE `tblPhonebooks` (
  `userId` bigint(20) NOT NULL DEFAULT '0',
  `phoneId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `phonenumber` varchar(30) NOT NULL DEFAULT '',
  `name` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`phoneId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblQuicklistGroups` (
  `userId` bigint(20) unsigned DEFAULT NULL,
  `groupId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `groupName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`groupId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblQuicklists` (
  `userId` bigint(20) unsigned DEFAULT NULL,
  `data` varchar(30) NOT NULL DEFAULT '',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `groupId` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblRemoteUsers` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `remoteUserId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `remoteUserGUID` varchar(36) NOT NULL DEFAULT '',
  `randomPassword` bigint(20) unsigned NOT NULL DEFAULT '0',
  `requestTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `tblSettings` (
  `itemId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `settingName` varchar(50) NOT NULL,
  `settingValue` blob NOT NULL,
  `timeSaved` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;
CREATE TABLE `tblStopwords` (
  `wordId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wordText` varchar(50) DEFAULT '0',
  `wordType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `wordMatch` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`wordId`)
) ENGINE=MyISAM AUTO_INCREMENT=1318580895 DEFAULT CHARSET=latin1;
CREATE TABLE `tblSubscriptions` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `subscriptionType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `subscriptionId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`subscriptionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblTimedSubscriptionCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `categoryName` varchar(50) NOT NULL,
  `timeCreated` bigint(20) NOT NULL,
  `creatorId` bigint(20) NOT NULL,
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
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
CREATE TABLE `tblTodoListCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(50) NOT NULL,
  `parentId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `creatorId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `createdTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
CREATE TABLE `tblTodoListComments` (
  `itemId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemComment` blob NOT NULL,
  `timestamp` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
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
CREATE TABLE `tblUserData` (
  `fieldId` bigint(20) unsigned DEFAULT NULL,
  `userId` bigint(20) unsigned DEFAULT NULL,
  `value` blob
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblUserDatafieldOptions` (
  `fieldId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `optionId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `optionName` varchar(50) DEFAULT '0',
  PRIMARY KEY (`optionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
CREATE TABLE `tblVisitors` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `visitorId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbgeoip` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbGeoIP`;
CREATE TABLE `import_geo_cc` (
  `ci` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `cc` char(2) DEFAULT NULL,
  `cn` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ci`)
) ENGINE=MyISAM DEFAULT CHARSET=latin7 ROW_FORMAT=FIXED;
CREATE TABLE `import_geo_csv` (
  `start_ip` char(15) DEFAULT NULL,
  `end_ip` char(15) DEFAULT NULL,
  `start` int(10) unsigned NOT NULL,
  `end` int(10) unsigned NOT NULL,
  `cc` char(2) DEFAULT NULL,
  `cn` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin7 ROW_FORMAT=REDUNDANT;
CREATE TABLE `tblDNSCache` (
  `IP` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeCreated` bigint(20) unsigned NOT NULL DEFAULT '0',
  `host` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblGeoIP` (
  `start` int(10) unsigned NOT NULL,
  `end` int(10) unsigned NOT NULL,
  `ci` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`start`),
  UNIQUE KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblWHOIS` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `timeUpdated` bigint(20) unsigned NOT NULL,
  `geoIP_start` bigint(20) unsigned NOT NULL,
  `geoIP_end` bigint(20) unsigned NOT NULL,
  `source` varchar(10) DEFAULT NULL,
  `privateRange` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `name` varchar(250) NOT NULL,
  `address` blob NOT NULL,
  `phone` varbinary(20) NOT NULL,
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=159 DEFAULT CHARSET=utf8;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbguildsite` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `dbGuildSite`;
CREATE TABLE `tblCharacterFlags` (
  `userId` bigint(20) unsigned DEFAULT '0',
  `flagId` bigint(20) unsigned DEFAULT '0',
  `timestamp` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblFlagCategories` (
  `categoryId` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(30) NOT NULL DEFAULT '0',
  `color` varchar(6) NOT NULL DEFAULT '',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
CREATE TABLE `tblFlags` (
  `flagId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `flagName` varchar(255) DEFAULT NULL,
  `flagNote` varchar(255) NOT NULL DEFAULT '',
  `categoryId` tinyint(3) unsigned DEFAULT '0',
  `ordernum` bigint(20) unsigned NOT NULL DEFAULT '0',
  `optional` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`flagId`)
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '',
  `password` varchar(60) NOT NULL DEFAULT '',
  `mode` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbjanina` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbJanina`;
CREATE TABLE `tblCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(100) NOT NULL DEFAULT '',
  `categoryType` tinyint(1) unsigned DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `ownerId` int(10) unsigned NOT NULL DEFAULT '0',
  `globalCategory` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
CREATE TABLE `tblFiles` (
  `fileId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fileName` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `fileSize` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fileMime` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `ownerId` int(10) unsigned NOT NULL DEFAULT '0',
  `categoryId` int(10) unsigned NOT NULL DEFAULT '0',
  `uploaderId` int(10) unsigned NOT NULL DEFAULT '0',
  `uploaderIP` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fileType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timeUploaded` datetime NOT NULL,
  `cnt` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fileId`)
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=latin1;
CREATE TABLE `tblLogs` (
  `entryId` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `entryText` text CHARACTER SET utf8 NOT NULL,
  `entryLevel` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `userId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userIP` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(20) CHARACTER SET utf8 NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `timeLastLogin` datetime NOT NULL,
  `timeLastActive` datetime NOT NULL,
  `timeLastLogout` datetime NOT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dblang` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbLang`;
CREATE TABLE `tblCategories` (
  `categoryId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(100) NOT NULL DEFAULT '',
  `categoryType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `creatorId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `globalCategory` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
CREATE TABLE `tblLogs` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entryText` blob,
  `entryLevel` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `entryTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userIP` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblSettings` (
  `settingId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `settingType` tinyint(3) unsigned NOT NULL,
  `ownerId` int(10) unsigned NOT NULL DEFAULT '0',
  `settingName` varchar(50) CHARACTER SET utf8 NOT NULL,
  `settingValue` text CHARACTER SET utf8 NOT NULL,
  `timeSaved` datetime NOT NULL,
  PRIMARY KEY (`settingId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(20) NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL DEFAULT '0',
  `createdTime` datetime NOT NULL,
  `lastLoginTime` datetime NOT NULL,
  `lastActive` datetime NOT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
CREATE TABLE `tblWords` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lang` smallint(5) unsigned NOT NULL,
  `word` varchar(50) NOT NULL,
  `pron` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=462 DEFAULT CHARSET=utf8;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dblyrics` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `dbLyrics`;
CREATE TABLE `tblBands` (
  `bandId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bandName` varchar(40) CHARACTER SET utf8 NOT NULL,
  `bandInfo` text CHARACTER SET utf8,
  `creatorId` int(10) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  PRIMARY KEY (`bandId`)
) ENGINE=MyISAM AUTO_INCREMENT=228 DEFAULT CHARSET=latin1;
CREATE TABLE `tblLogs` (
  `entryId` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `entryText` text CHARACTER SET utf8 NOT NULL,
  `entryLevel` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `userId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userIP` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
CREATE TABLE `tblLyrics` (
  `lyricId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lyricName` varchar(200) CHARACTER SET utf8 NOT NULL,
  `lyricText` text CHARACTER SET utf8 NOT NULL,
  `bandId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `creatorId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  PRIMARY KEY (`lyricId`)
) ENGINE=MyISAM AUTO_INCREMENT=4758 DEFAULT CHARSET=latin1;
CREATE TABLE `tblNewAdditions` (
  `ID` bigint(20) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblPendingChanges` (
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `p1` bigint(20) unsigned NOT NULL DEFAULT '0',
  `p2` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `p3` text CHARACTER SET utf8 NOT NULL,
  `timeCreated` datetime NOT NULL,
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblRecords` (
  `recordId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `recordName` varchar(60) CHARACTER SET utf8 NOT NULL,
  `recordInfo` text CHARACTER SET utf8 NOT NULL,
  `bandId` bigint(20) NOT NULL DEFAULT '0',
  `creatorId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  PRIMARY KEY (`recordId`)
) ENGINE=MyISAM AUTO_INCREMENT=592 DEFAULT CHARSET=latin1;
CREATE TABLE `tblTracks` (
  `recordId` bigint(20) NOT NULL DEFAULT '0',
  `trackNumber` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `lyricId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `bandId` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userName` varchar(30) CHARACTER SET utf8 NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timeLastActive` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dboophp` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbOOPHP`;
CREATE TABLE `tblFiles` (
  `fileId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fileName` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `fileSize` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fileMime` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `ownerId` int(10) unsigned NOT NULL DEFAULT '0',
  `categoryId` int(10) unsigned NOT NULL DEFAULT '0',
  `uploaderId` int(10) unsigned NOT NULL DEFAULT '0',
  `uploaderIP` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fileType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timeUploaded` datetime NOT NULL,
  `cnt` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fileId`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
CREATE TABLE `tblLogs` (
  `entryId` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `entryText` text CHARACTER SET utf8 NOT NULL,
  `entryLevel` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `userId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userIP` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=161 DEFAULT CHARSET=latin1;
CREATE TABLE `tblSettings` (
  `settingId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ownerId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `settingName` varchar(50) CHARACTER SET utf8 NOT NULL,
  `settingValue` text CHARACTER SET utf8 NOT NULL,
  `settingType` tinyint(3) unsigned NOT NULL,
  `timeSaved` datetime NOT NULL,
  PRIMARY KEY (`settingId`)
) ENGINE=MyISAM AUTO_INCREMENT=160 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(20) CHARACTER SET utf8 NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `timeLastLogin` datetime NOT NULL,
  `timeLastActive` datetime NOT NULL,
  `timeLastLogout` datetime NOT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbsvnhosting` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbSvnHosting`;
CREATE TABLE `tblLogs` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entryText` blob,
  `entryLevel` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `entryTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userIP` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblRevisions` (
  `indexId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fieldId` bigint(20) unsigned NOT NULL,
  `fieldType` tinyint(3) unsigned DEFAULT NULL,
  `fieldText` text CHARACTER SET utf8 NOT NULL,
  `createdBy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `categoryId` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`indexId`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
CREATE TABLE `tblSettings` (
  `settingId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `settingType` tinyint(3) unsigned NOT NULL,
  `ownerId` int(10) unsigned NOT NULL DEFAULT '0',
  `settingName` varchar(50) CHARACTER SET utf8 NOT NULL,
  `settingValue` text CHARACTER SET utf8 NOT NULL,
  `timeSaved` datetime NOT NULL,
  PRIMARY KEY (`settingId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(20) NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `timeLastLogin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timeLastActive` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timeLastLogout` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
CREATE TABLE `tblWiki` (
  `wikiId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wikiName` varchar(30) DEFAULT NULL,
  `msg` text,
  `timeCreated` datetime NOT NULL,
  `createdBy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `lockedBy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timeLocked` datetime NOT NULL,
  `hasFiles` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`wikiId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbzine` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbZine`;
CREATE TABLE `tblAccessgroupFlags` (
  `flagId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `flagName` varchar(40) NOT NULL DEFAULT '',
  `flagDesc` blob NOT NULL,
  PRIMARY KEY (`flagId`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
CREATE TABLE `tblAccessgroupMembers` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `groupId` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblAccessgroupSettings` (
  `groupId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `flagId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `value` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblAccessgroups` (
  `groupId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `groupName` varchar(30) DEFAULT '0',
  PRIMARY KEY (`groupId`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
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
CREATE TABLE `tblCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(100) NOT NULL DEFAULT '',
  `categoryType` tinyint(3) unsigned DEFAULT '0',
  `timeCreated` bigint(20) unsigned NOT NULL DEFAULT '0',
  `creatorId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `globalCategory` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
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
CREATE TABLE `tblEstoreBrands` (
  `brandId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brandName` varchar(50) NOT NULL DEFAULT '',
  `imageId` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`brandId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `imageId` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreCategoryDescs` (
  `categoryId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `categoryName` varchar(50) NOT NULL DEFAULT '0',
  `lang` varchar(5) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjectAttributeOptions` (
  `attributeId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `optionId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`optionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjectAttributes` (
  `idx` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attributeId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `objectId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lang` varchar(5) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`idx`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjectDescs` (
  `descId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `objectId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lang` varchar(5) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `info` blob NOT NULL,
  `deliveryTime` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`descId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblEstoreObjectImages` (
  `indexId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `objectId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `imageId` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`indexId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
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
CREATE TABLE `tblFavoriteGames` (
  `indexId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gameId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`indexId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
CREATE TABLE `tblFileCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fileType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ownerId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `categoryName` varchar(100) NOT NULL DEFAULT '',
  `timeCreated` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
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
CREATE TABLE `tblFriendRequests` (
  `reqId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `senderId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `recieverId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeCreated` bigint(20) unsigned NOT NULL DEFAULT '0',
  `msg` blob,
  PRIMARY KEY (`reqId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `tblGeoIP` (
  `start` int(10) unsigned NOT NULL,
  `end` int(10) unsigned NOT NULL,
  `countryId` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`start`),
  UNIQUE KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblGeoIPCities` (
  `start` int(10) unsigned NOT NULL DEFAULT '0',
  `end` int(10) unsigned NOT NULL DEFAULT '0',
  `cityId` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`start`),
  UNIQUE KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblGeoIPCityNames` (
  `cityId` bigint(20) NOT NULL AUTO_INCREMENT,
  `cityName` varchar(50) NOT NULL DEFAULT '',
  `countryId` int(3) unsigned NOT NULL DEFAULT '0',
  `timeAdded` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cityId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
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
CREATE TABLE `tblInfoFields` (
  `fieldId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fieldName` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `fieldText` blob,
  `editedTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `editedBy` bigint(20) unsigned NOT NULL DEFAULT '0',
  `hasFiles` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldId`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
CREATE TABLE `tblInfoFieldsHistory` (
  `fieldId` bigint(20) unsigned NOT NULL,
  `fieldText` blob NOT NULL,
  `editedBy` bigint(20) unsigned NOT NULL DEFAULT '0',
  `editedTime` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblLoginStats` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userIp` varchar(15) NOT NULL DEFAULT '0',
  `loggedOut` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=398 DEFAULT CHARSET=latin1;
CREATE TABLE `tblLogs` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entryText` blob,
  `entryLevel` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `entryTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userIP` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=4480 DEFAULT CHARSET=latin1;
CREATE TABLE `tblMatchmaking` (
  `itemId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemText` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblMatchmakingAnswers` (
  `itemId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `answerId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblMessageFolders` (
  `folderId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `folderName` varchar(100) NOT NULL DEFAULT '0',
  `ownerId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `folderType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `parentFolder` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`folderId`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
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
CREATE TABLE `tblModerationQueue` (
  `queueId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queueType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `itemId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`queueId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Modereringskö med olika objekt som behöver ';
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
CREATE TABLE `tblPhonebooks` (
  `userId` bigint(20) NOT NULL DEFAULT '0',
  `phoneId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `phonenumber` varchar(30) NOT NULL DEFAULT '',
  `name` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`phoneId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblQuicklistGroups` (
  `userId` bigint(20) unsigned DEFAULT NULL,
  `groupId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `groupName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`groupId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblQuicklists` (
  `userId` bigint(20) unsigned DEFAULT NULL,
  `data` varchar(30) NOT NULL DEFAULT '',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `groupId` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblRemoteUsers` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `remoteUserId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `remoteUserGUID` varchar(36) NOT NULL DEFAULT '',
  `randomPassword` bigint(20) unsigned NOT NULL DEFAULT '0',
  `requestTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `tblSettings` (
  `itemId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `settingName` varchar(50) NOT NULL,
  `settingValue` blob NOT NULL,
  `timeSaved` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;
CREATE TABLE `tblStopwords` (
  `wordId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wordText` varchar(50) DEFAULT '0',
  `wordType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `wordMatch` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`wordId`)
) ENGINE=MyISAM AUTO_INCREMENT=1318580895 DEFAULT CHARSET=latin1;
CREATE TABLE `tblSubscriptions` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `subscriptionType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `subscriptionId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`subscriptionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblTimedSubscriptionCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `categoryName` varchar(50) NOT NULL,
  `timeCreated` bigint(20) NOT NULL,
  `creatorId` bigint(20) NOT NULL,
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
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
CREATE TABLE `tblTodoListCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(50) NOT NULL,
  `parentId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `creatorId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `createdTime` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
CREATE TABLE `tblTodoListComments` (
  `itemId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemComment` blob NOT NULL,
  `timestamp` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
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
CREATE TABLE `tblUserData` (
  `fieldId` bigint(20) unsigned DEFAULT NULL,
  `userId` bigint(20) unsigned DEFAULT NULL,
  `value` blob
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblUserDatafieldOptions` (
  `fieldId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `optionId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `optionName` varchar(50) DEFAULT '0',
  PRIMARY KEY (`optionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
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
CREATE TABLE `tblVisitors` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `visitorId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbtracker` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dbtracker`;
CREATE TABLE `tblcomments` (
  `commentId` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `commentType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `commentText` blob NOT NULL,
  `commentPrivate` tinyint(1) NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  `timeDeleted` datetime NOT NULL DEFAULT '1970-01-01 01:00:00',
  `deletedBy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ownerId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userIP` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`commentId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
CREATE TABLE `tbllocations` (
  `entryId` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `location` varchar(300) NOT NULL,
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=22995 DEFAULT CHARSET=utf8;
CREATE TABLE `tbllogs` (
  `entryId` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `entryText` blob NOT NULL,
  `entryLevel` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `userId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userIP` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
CREATE TABLE `tblreferrers` (
  `entryId` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `referrer` varchar(600) NOT NULL,
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=32703 DEFAULT CHARSET=utf8;
CREATE TABLE `tblsubscriptions` (
  `entryId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `creatorId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `itemId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `subscriptionType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `recipient` varchar(150) NOT NULL,
  `timeCreated` datetime NOT NULL,
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tbltrackentries` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `trackerId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `refId` mediumint(8) unsigned NOT NULL,
  `locId` mediumint(8) unsigned NOT NULL,
  `uaId` mediumint(8) unsigned NOT NULL,
  `IP` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=1623196 DEFAULT CHARSET=utf8;
CREATE TABLE `tbltrackpoints` (
  `trackerId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `location` varchar(300) NOT NULL DEFAULT '',
  `siteId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `trackerNotes` blob NOT NULL,
  `creatorId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `timeEdited` datetime NOT NULL,
  `editorId` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`trackerId`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
CREATE TABLE `tbltracksites` (
  `siteId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `creatorId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `siteName` varchar(300) NOT NULL,
  `siteNotes` blob NOT NULL,
  `timeCreated` datetime NOT NULL,
  `timeEdited` datetime NOT NULL,
  `editorId` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`siteId`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
CREATE TABLE `tbluseragents` (
  `entryId` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `UA` varchar(300) NOT NULL,
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=6318 DEFAULT CHARSET=utf8;
CREATE TABLE `tblusers` (
  `userId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(20) NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `lastLoginTime` datetime NOT NULL,
  `lastActive` datetime NOT NULL,
  `userStatus` blob NOT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `mysql` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `mysql`;
CREATE TABLE `columns_priv` (
  `Host` char(60) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Db` char(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `User` char(16) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Table_name` char(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Column_name` char(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Column_priv` set('Select','Insert','Update','References') CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`Host`,`Db`,`User`,`Table_name`,`Column_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column privileges';
CREATE TABLE `db` (
  `Host` char(60) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Db` char(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `User` char(16) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Select_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Insert_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Update_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Delete_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Create_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Drop_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Grant_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `References_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Index_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Alter_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Create_tmp_table_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Lock_tables_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Create_view_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Show_view_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Create_routine_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Alter_routine_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Execute_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Event_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Trigger_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  PRIMARY KEY (`Host`,`Db`,`User`),
  KEY `User` (`User`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database privileges';
CREATE TABLE `event` (
  `db` char(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `name` char(64) NOT NULL DEFAULT '',
  `body` longblob NOT NULL,
  `definer` char(77) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `execute_at` datetime DEFAULT NULL,
  `interval_value` int(11) DEFAULT NULL,
  `interval_field` enum('YEAR','QUARTER','MONTH','DAY','HOUR','MINUTE','WEEK','SECOND','MICROSECOND','YEAR_MONTH','DAY_HOUR','DAY_MINUTE','DAY_SECOND','HOUR_MINUTE','HOUR_SECOND','MINUTE_SECOND','DAY_MICROSECOND','HOUR_MICROSECOND','MINUTE_MICROSECOND','SECOND_MICROSECOND') DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_executed` datetime DEFAULT NULL,
  `starts` datetime DEFAULT NULL,
  `ends` datetime DEFAULT NULL,
  `status` enum('ENABLED','DISABLED') NOT NULL DEFAULT 'ENABLED',
  `on_completion` enum('DROP','PRESERVE') NOT NULL DEFAULT 'DROP',
  `sql_mode` set('REAL_AS_FLOAT','PIPES_AS_CONCAT','ANSI_QUOTES','IGNORE_SPACE','NOT_USED','ONLY_FULL_GROUP_BY','NO_UNSIGNED_SUBTRACTION','NO_DIR_IN_CREATE','POSTGRESQL','ORACLE','MSSQL','DB2','MAXDB','NO_KEY_OPTIONS','NO_TABLE_OPTIONS','NO_FIELD_OPTIONS','MYSQL323','MYSQL40','ANSI','NO_AUTO_VALUE_ON_ZERO','NO_BACKSLASH_ESCAPES','STRICT_TRANS_TABLES','STRICT_ALL_TABLES','NO_ZERO_IN_DATE','NO_ZERO_DATE','INVALID_DATES','ERROR_FOR_DIVISION_BY_ZERO','TRADITIONAL','NO_AUTO_CREATE_USER','HIGH_NOT_PRECEDENCE') NOT NULL DEFAULT '',
  `comment` char(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`db`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Events';
CREATE TABLE `func` (
  `name` char(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ret` tinyint(1) NOT NULL DEFAULT '0',
  `dl` char(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `type` enum('function','aggregate') CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User defined functions';
CREATE TABLE `general_log` (
  `event_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_host` mediumtext,
  `thread_id` int(11) DEFAULT NULL,
  `server_id` int(11) DEFAULT NULL,
  `command_type` varchar(64) DEFAULT NULL,
  `argument` mediumtext
) ENGINE=CSV DEFAULT CHARSET=utf8 COMMENT='General log';
CREATE TABLE `help_category` (
  `help_category_id` smallint(5) unsigned NOT NULL,
  `name` char(64) NOT NULL,
  `parent_category_id` smallint(5) unsigned DEFAULT NULL,
  `url` char(128) NOT NULL,
  PRIMARY KEY (`help_category_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='help categories';
CREATE TABLE `help_keyword` (
  `help_keyword_id` int(10) unsigned NOT NULL,
  `name` char(64) NOT NULL,
  PRIMARY KEY (`help_keyword_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='help keywords';
CREATE TABLE `help_relation` (
  `help_topic_id` int(10) unsigned NOT NULL,
  `help_keyword_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`help_keyword_id`,`help_topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='keyword-topic relation';
CREATE TABLE `help_topic` (
  `help_topic_id` int(10) unsigned NOT NULL,
  `name` char(64) NOT NULL,
  `help_category_id` smallint(5) unsigned NOT NULL,
  `description` text NOT NULL,
  `example` text NOT NULL,
  `url` char(128) NOT NULL,
  PRIMARY KEY (`help_topic_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='help topics';
CREATE TABLE `host` (
  `Host` char(60) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Db` char(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Select_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Insert_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Update_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Delete_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Create_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Drop_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Grant_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `References_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Index_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Alter_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Create_tmp_table_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Lock_tables_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Create_view_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Show_view_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Create_routine_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Alter_routine_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Execute_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Trigger_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  PRIMARY KEY (`Host`,`Db`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Host privileges;  Merged with database privileges';
CREATE TABLE `plugin` (
  `name` char(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `dl` char(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='MySQL plugins';
CREATE TABLE `proc` (
  `db` char(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `name` char(64) NOT NULL DEFAULT '',
  `type` enum('FUNCTION','PROCEDURE') NOT NULL,
  `specific_name` char(64) NOT NULL DEFAULT '',
  `language` enum('SQL') NOT NULL DEFAULT 'SQL',
  `sql_data_access` enum('CONTAINS_SQL','NO_SQL','READS_SQL_DATA','MODIFIES_SQL_DATA') NOT NULL DEFAULT 'CONTAINS_SQL',
  `is_deterministic` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `security_type` enum('INVOKER','DEFINER') NOT NULL DEFAULT 'DEFINER',
  `param_list` blob NOT NULL,
  `returns` char(64) NOT NULL DEFAULT '',
  `body` longblob NOT NULL,
  `definer` char(77) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sql_mode` set('REAL_AS_FLOAT','PIPES_AS_CONCAT','ANSI_QUOTES','IGNORE_SPACE','NOT_USED','ONLY_FULL_GROUP_BY','NO_UNSIGNED_SUBTRACTION','NO_DIR_IN_CREATE','POSTGRESQL','ORACLE','MSSQL','DB2','MAXDB','NO_KEY_OPTIONS','NO_TABLE_OPTIONS','NO_FIELD_OPTIONS','MYSQL323','MYSQL40','ANSI','NO_AUTO_VALUE_ON_ZERO','NO_BACKSLASH_ESCAPES','STRICT_TRANS_TABLES','STRICT_ALL_TABLES','NO_ZERO_IN_DATE','NO_ZERO_DATE','INVALID_DATES','ERROR_FOR_DIVISION_BY_ZERO','TRADITIONAL','NO_AUTO_CREATE_USER','HIGH_NOT_PRECEDENCE') NOT NULL DEFAULT '',
  `comment` char(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`db`,`name`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Stored Procedures';
CREATE TABLE `procs_priv` (
  `Host` char(60) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Db` char(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `User` char(16) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Routine_name` char(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Routine_type` enum('FUNCTION','PROCEDURE') COLLATE utf8_bin NOT NULL,
  `Grantor` char(77) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Proc_priv` set('Execute','Alter Routine','Grant') CHARACTER SET utf8 NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`Host`,`Db`,`User`,`Routine_name`,`Routine_type`),
  KEY `Grantor` (`Grantor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Procedure privileges';
CREATE TABLE `slow_log` (
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_host` mediumtext NOT NULL,
  `query_time` time NOT NULL,
  `lock_time` time NOT NULL,
  `rows_sent` int(11) NOT NULL,
  `rows_examined` int(11) NOT NULL,
  `db` varchar(512) DEFAULT NULL,
  `last_insert_id` int(11) DEFAULT NULL,
  `insert_id` int(11) DEFAULT NULL,
  `server_id` int(11) DEFAULT NULL,
  `sql_text` mediumtext NOT NULL
) ENGINE=CSV DEFAULT CHARSET=utf8 COMMENT='Slow log';
CREATE TABLE `tables_priv` (
  `Host` char(60) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Db` char(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `User` char(16) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Table_name` char(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Grantor` char(77) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Table_priv` set('Select','Insert','Update','Delete','Create','Drop','Grant','References','Index','Alter','Create View','Show view','Trigger') CHARACTER SET utf8 NOT NULL DEFAULT '',
  `Column_priv` set('Select','Insert','Update','References') CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`Host`,`Db`,`User`,`Table_name`),
  KEY `Grantor` (`Grantor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table privileges';
CREATE TABLE `time_zone` (
  `Time_zone_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Use_leap_seconds` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`Time_zone_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Time zones';
CREATE TABLE `time_zone_leap_second` (
  `Transition_time` bigint(20) NOT NULL,
  `Correction` int(11) NOT NULL,
  PRIMARY KEY (`Transition_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Leap seconds information for time zones';
CREATE TABLE `time_zone_name` (
  `Name` char(64) NOT NULL,
  `Time_zone_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Time zone names';
CREATE TABLE `time_zone_transition` (
  `Time_zone_id` int(10) unsigned NOT NULL,
  `Transition_time` bigint(20) NOT NULL,
  `Transition_type_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Time_zone_id`,`Transition_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Time zone transitions';
CREATE TABLE `time_zone_transition_type` (
  `Time_zone_id` int(10) unsigned NOT NULL,
  `Transition_type_id` int(10) unsigned NOT NULL,
  `Offset` int(11) NOT NULL DEFAULT '0',
  `Is_DST` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Abbreviation` char(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`Time_zone_id`,`Transition_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Time zone transition types';
CREATE TABLE `user` (
  `Host` char(60) COLLATE utf8_bin NOT NULL DEFAULT '',
  `User` char(16) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Password` char(41) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `Select_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Insert_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Update_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Delete_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Create_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Drop_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Reload_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Shutdown_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Process_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `File_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Grant_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `References_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Index_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Alter_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Show_db_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Super_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Create_tmp_table_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Lock_tables_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Execute_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Repl_slave_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Repl_client_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Create_view_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Show_view_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Create_routine_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Alter_routine_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Create_user_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Event_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `Trigger_priv` enum('N','Y') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `ssl_type` enum('','ANY','X509','SPECIFIED') CHARACTER SET utf8 NOT NULL DEFAULT '',
  `ssl_cipher` blob NOT NULL,
  `x509_issuer` blob NOT NULL,
  `x509_subject` blob NOT NULL,
  `max_questions` int(11) unsigned NOT NULL DEFAULT '0',
  `max_updates` int(11) unsigned NOT NULL DEFAULT '0',
  `max_connections` int(11) unsigned NOT NULL DEFAULT '0',
  `max_user_connections` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Host`,`User`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and global privileges';
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `online_game` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `online_game`;
CREATE TABLE `tblCharacterAbilityScores` (
  `charId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `charSTR` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `charDEX` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `charCON` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `charINT` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `charWIS` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `charCHA` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`charId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblCharacterCoords` (
  `charId` bigint(20) NOT NULL DEFAULT '0',
  `zone` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `x` float NOT NULL DEFAULT '0',
  `y` float NOT NULL DEFAULT '0',
  `z` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`charId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblCharacters` (
  `charId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `charName` varchar(30) NOT NULL DEFAULT '',
  `charGender` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `charRace` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timeCreated` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeLastPlayed` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timePlayed` bigint(20) unsigned NOT NULL DEFAULT '0',
  `playedCount` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`charId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblCharactersessions` (
  `idx` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `charId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `loggedin` bigint(20) unsigned NOT NULL DEFAULT '0',
  `loggedout` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idx`)
) ENGINE=MyISAM AUTO_INCREMENT=183 DEFAULT CHARSET=latin1 COMMENT='InnehÃ¥ller logintimestamp samt hur lÃ¤nge man spelade';
CREATE TABLE `tblGameServers` (
  `serverId` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `serverIP` varchar(15) NOT NULL DEFAULT '',
  `serverName` varchar(25) NOT NULL DEFAULT '',
  `serverOnline` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`serverId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
CREATE TABLE `tblGuildMembers` (
  `charId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `guildId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeJoinedGuild` bigint(20) unsigned NOT NULL DEFAULT '0',
  `guildMemberType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`charId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblGuilds` (
  `guildId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `guildName` varchar(40) NOT NULL DEFAULT '',
  `creatorId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`guildId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `online_site` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `online_site`;
CREATE TABLE `tblBugReports` (
  `bugId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bugDesc` blob NOT NULL,
  `bugCreator` bigint(20) unsigned NOT NULL DEFAULT '0',
  `reportMethod` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `bugClosed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bugClosedReason` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`bugId`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
CREATE TABLE `tblContentCodes` (
  `code` bigint(20) unsigned NOT NULL DEFAULT '0',
  `months` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `used` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `usedTimestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblCountries` (
  `countryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `countryName` varchar(50) NOT NULL DEFAULT '',
  `countrySuffix` varchar(5) NOT NULL DEFAULT '',
  `timezoneId` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`countryId`)
) ENGINE=MyISAM AUTO_INCREMENT=199 DEFAULT CHARSET=latin1;
CREATE TABLE `tblForums` (
  `itemId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `itemType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `authorId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `parentId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemSubject` varchar(100) NOT NULL DEFAULT '0',
  `itemBody` blob NOT NULL,
  `fileId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemDeleted` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
CREATE TABLE `tblLoginAttempts` (
  `idx` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `IP` varchar(30) NOT NULL DEFAULT '',
  `loggedin` bigint(20) NOT NULL DEFAULT '0',
  `loggedout` bigint(20) unsigned NOT NULL DEFAULT '0',
  `bygame` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idx`)
) ENGINE=MyISAM AUTO_INCREMENT=246 DEFAULT CHARSET=latin1 COMMENT='timeplayed anvÃ¤nds inte fÃ¶r sajtinlogg';
CREATE TABLE `tblMailActivation` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `activationCode` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblNews` (
  `itemId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(200) NOT NULL DEFAULT '',
  `body` blob NOT NULL,
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
CREATE TABLE `tblNewsletters` (
  `itemId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(200) NOT NULL DEFAULT '',
  `body` blob NOT NULL,
  `headers` blob NOT NULL,
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `recievers` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
CREATE TABLE `tblServerDowntimes` (
  `itemId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `info` blob NOT NULL,
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
CREATE TABLE `tblTimezones` (
  `zoneId` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `zoneName` varchar(40) NOT NULL DEFAULT '',
  `zoneGMT` smallint(5) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblTodoListComments` (
  `itemId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemComment` blob NOT NULL,
  `timestamp` bigint(20) NOT NULL DEFAULT '0',
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblTodoLists` (
  `itemId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `listId` tinyint(4) NOT NULL DEFAULT '0',
  `itemDesc` varchar(100) NOT NULL DEFAULT '',
  `itemDetails` blob NOT NULL,
  `itemStatus` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `itemCategory` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timestamp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemCreator` bigint(20) NOT NULL DEFAULT '0',
  `assignedTo` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemId`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUserAddress` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timezone` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `realName` varchar(50) NOT NULL DEFAULT '',
  `gender` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `userMail` varchar(50) NOT NULL DEFAULT '',
  `userMailSecret` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `adrPhoneHome` varchar(20) NOT NULL DEFAULT '',
  `adrCountry` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `adrCity` varchar(50) NOT NULL DEFAULT '',
  `adrZipcode` varchar(10) NOT NULL DEFAULT '',
  `adrStreet` varchar(60) NOT NULL DEFAULT '',
  `newsletter` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblUserBilling` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `ccNumber` bigint(20) unsigned NOT NULL DEFAULT '0',
  `ccExpireMonth` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `ccExpireYear` int(10) unsigned NOT NULL DEFAULT '0',
  `ccExtraCode` varchar(11) NOT NULL DEFAULT '',
  `ccOwnerName` varchar(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblUsers` (
  `userId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(20) NOT NULL DEFAULT '',
  `userPass` varchar(32) NOT NULL DEFAULT '',
  `userType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
CREATE TABLE `tblUserstats` (
  `userId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeCreated` bigint(20) NOT NULL DEFAULT '0',
  `timeActivated` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeExpires` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeLastLogin` bigint(20) unsigned NOT NULL DEFAULT '0',
  `cntLogins` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `test` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `test`;
DELIMITER ;;
DELIMITER ;
