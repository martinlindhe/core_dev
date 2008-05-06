SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblActivation` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `userId` bigint(20) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL default '0',
  `rnd` bigint(20) unsigned NOT NULL default '0',
  `answer` varchar(10) default NULL,
  `timeCreated` datetime default NULL,
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblBlocks` (
  `ruleId` int(10) unsigned NOT NULL auto_increment,
  `type` tinyint(3) unsigned NOT NULL,
  `rule` text character set utf8,
  `timeCreated` datetime default NULL,
  `createdBy` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`ruleId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblCategories` (
  `categoryId` bigint(20) unsigned NOT NULL auto_increment,
  `categoryName` varchar(200) default NULL,
  `categoryType` tinyint(3) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `creatorId` int(10) unsigned NOT NULL default '0',
  `permissions` tinyint(3) unsigned NOT NULL default '0',
  `ownerId` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`categoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblCcPay` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `clientid` varchar(45) character set utf8 NOT NULL default '',
  `passcode` varchar(45) character set utf8 NOT NULL default '',
  `date` varchar(45) character set utf8 NOT NULL default '',
  `minutes` varchar(45) character set utf8 NOT NULL default '',
  `price` varchar(45) character set utf8 NOT NULL default '',
  `packagekey` int(10) unsigned NOT NULL,
  `remoteid` int(10) unsigned NOT NULL,
  `email` varchar(45) character set utf8 NOT NULL default '',
  `card` varchar(45) character set utf8 NOT NULL default '',
  `cvv` varchar(45) character set utf8 NOT NULL default '',
  `status` varchar(45) character set utf8 NOT NULL default '',
  `name` varchar(45) character set utf8 NOT NULL default '',
  `cardtype` varchar(45) character set utf8 NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblChecksums` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `fileId` int(11) unsigned default NULL,
  `sha1` varchar(40) default NULL,
  `md5` varchar(32) default NULL,
  `timeCreated` datetime default NULL,
  `timeExec` float default NULL,
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblContacts` (
  `contactId` int(10) unsigned NOT NULL auto_increment,
  `contactType` tinyint(3) unsigned NOT NULL default '0',
  `groupId` int(10) unsigned NOT NULL default '0',
  `userId` int(10) unsigned NOT NULL default '0',
  `otherUserId` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  PRIMARY KEY  (`contactId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblEvents` (
  `eventId` bigint(20) unsigned NOT NULL auto_increment,
  `type` tinyint(3) unsigned NOT NULL,
  `category` tinyint(3) unsigned NOT NULL,
  `ownerId` bigint(20) unsigned NOT NULL,
  `refererId` bigint(20) unsigned NOT NULL,
  `timeCreated` datetime default NULL,
  PRIMARY KEY  (`eventId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblFAQ` (
  `faqId` int(10) unsigned NOT NULL auto_increment,
  `question` text,
  `answer` text,
  `createdBy` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  PRIMARY KEY  (`faqId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblFeedback` (
  `feedbackId` int(10) unsigned NOT NULL auto_increment,
  `feedbackType` tinyint(3) unsigned NOT NULL default '0',
  `text` text,
  `text2` text,
  `userId` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `subjectId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`feedbackId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  `rating` tinyint(3) unsigned NOT NULL,
  `ratingCnt` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`fileId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblFriendRequests` (
  `reqId` int(10) unsigned NOT NULL auto_increment,
  `senderId` int(10) unsigned NOT NULL default '0',
  `recieverId` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `categoryId` int(10) unsigned NOT NULL default '0',
  `msg` text,
  PRIMARY KEY  (`reqId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblLocationCity` (
  `cityId` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `regionId` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`cityId`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblLocationRegion` (
  `regionId` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY  (`regionId`),
  KEY `st_lan` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblLocationZip` (
  `zip` int(5) NOT NULL default '0',
  `cityId` int(10) unsigned NOT NULL,
  `regionId` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`zip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblLogins` (
  `mainId` int(10) unsigned NOT NULL auto_increment,
  `userId` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `IP` int(10) unsigned NOT NULL default '0',
  `userAgent` text,
  PRIMARY KEY  (`mainId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblLogs` (
  `entryId` int(10) unsigned NOT NULL auto_increment,
  `entryText` text,
  `entryLevel` tinyint(3) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `userId` smallint(5) unsigned NOT NULL default '0',
  `userIP` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblMessages` (
  `msgId` bigint(20) unsigned NOT NULL auto_increment,
  `ownerId` int(10) unsigned NOT NULL default '0',
  `fromId` int(10) unsigned NOT NULL default '0',
  `toId` int(10) unsigned NOT NULL default '0',
  `subject` varchar(200) default NULL,
  `body` text,
  `timeCreated` datetime default NULL,
  `timeRead` datetime default NULL,
  `timeDeleted` datetime default NULL,
  `groupId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`msgId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblPollVotes` (
  `voteId` bigint(20) unsigned NOT NULL auto_increment,
  `pollId` int(10) unsigned NOT NULL default '0',
  `userId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`voteId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblProcessQueue` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `referId` bigint(20) unsigned default NULL,
  `timeCreated` datetime default NULL,
  `creatorId` int(10) unsigned default NULL,
  `orderType` tinyint(3) unsigned NOT NULL,
  `orderStatus` tinyint(1) unsigned NOT NULL default '0',
  `orderParams` text,
  `timeExec` float unsigned default NULL,
  `timeCompleted` datetime default NULL,
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblRatings` (
  `rateId` bigint(20) unsigned NOT NULL auto_increment,
  `type` tinyint(3) unsigned NOT NULL default '0',
  `itemId` bigint(20) unsigned NOT NULL default '0',
  `userId` int(10) unsigned NOT NULL default '0',
  `rating` tinyint(3) unsigned NOT NULL default '0',
  `timeRated` datetime default NULL,
  PRIMARY KEY  (`rateId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblRevisions` (
  `indexId` int(10) unsigned NOT NULL auto_increment,
  `fieldId` bigint(20) unsigned NOT NULL default '0',
  `fieldType` tinyint(3) unsigned NOT NULL default '0',
  `fieldText` text,
  `createdBy` smallint(5) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `categoryId` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`indexId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblSettings` (
  `settingId` bigint(20) unsigned NOT NULL auto_increment,
  `ownerId` smallint(5) unsigned NOT NULL default '0',
  `settingName` varchar(200) default NULL,
  `settingValue` text,
  `settingType` tinyint(3) unsigned NOT NULL default '0',
  `timeSaved` datetime default NULL,
  PRIMARY KEY  (`settingId`),
  KEY `ownerId` (`ownerId`,`settingName`,`settingType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblStatistics` (
  `entryId` bigint(20) unsigned NOT NULL auto_increment,
  `time` datetime default NULL,
  `logins` int(10) unsigned NOT NULL default '0',
  `registrations` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblStopwords` (
  `wordId` smallint(5) unsigned NOT NULL auto_increment,
  `wordText` varchar(200) default NULL,
  `wordType` tinyint(3) unsigned NOT NULL default '0',
  `wordMatch` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`wordId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblSubscriptions` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `type` tinyint(1) unsigned default '0',
  `ownerId` int(10) unsigned NOT NULL default '0',
  `itemId` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblTodoLists` (
  `itemId` bigint(20) unsigned NOT NULL auto_increment,
  `categoryId` tinyint(3) unsigned NOT NULL default '0',
  `itemDesc` blob,
  `itemDetails` blob NOT NULL,
  `itemStatus` tinyint(3) unsigned NOT NULL default '0',
  `itemCategory` tinyint(3) unsigned NOT NULL default '0',
  `timeCreated` datetime NOT NULL,
  `itemCreator` bigint(20) NOT NULL default '0',
  `assignedTo` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblUserdata` (
  `fieldId` int(10) unsigned NOT NULL auto_increment,
  `fieldName` varchar(100) default NULL,
  `fieldType` tinyint(3) unsigned NOT NULL default '0',
  `fieldDefault` varchar(100) default NULL,
  `allowTags` tinyint(3) unsigned NOT NULL default '0',
  `private` tinyint(3) unsigned NOT NULL default '0',
  `fieldPriority` tinyint(3) unsigned NOT NULL default '0',
  `regRequire` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fieldId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblUsers` (
  `userId` smallint(5) unsigned NOT NULL auto_increment,
  `userName` varchar(200) default NULL,
  `userPass` varchar(200) default NULL,
  `userMode` tinyint(4) NOT NULL default '0',
  `timeCreated` datetime default NULL,
  `timeLastLogin` datetime default NULL,
  `timeLastActive` datetime default NULL,
  `timeLastLogout` datetime default NULL,
  `timeDeleted` datetime default NULL,
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tblVisits` (
  `visitId` int(10) unsigned NOT NULL auto_increment,
  `type` tinyint(1) unsigned NOT NULL,
  `ownerId` int(10) unsigned NOT NULL default '0',
  `creatorId` int(10) unsigned NOT NULL default '0',
  `timeCreated` datetime default NULL,
  PRIMARY KEY  (`visitId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
