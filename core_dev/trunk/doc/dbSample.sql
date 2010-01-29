/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblActivation` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `rnd` bigint(20) unsigned NOT NULL DEFAULT '0',
  `answer` varchar(10) DEFAULT NULL,
  `timeCreated` datetime DEFAULT NULL,
  `timeActivated` datetime DEFAULT NULL,
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblBlocks` (
  `ruleId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL,
  `rule` text CHARACTER SET utf8,
  `timeCreated` datetime DEFAULT NULL,
  `createdBy` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ruleId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblBlogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner` int(10) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(200) DEFAULT NULL,
  `body` text,
  `timeCreated` datetime DEFAULT NULL,
  `timePublished` datetime DEFAULT NULL,
  `timeUpdated` datetime DEFAULT NULL,
  `category` bigint(20) unsigned NOT NULL DEFAULT '0',
  `deletedBy` int(10) unsigned NOT NULL DEFAULT '0',
  `timeDeleted` datetime DEFAULT NULL,
  `rating` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `countRatings` int(10) unsigned NOT NULL DEFAULT '0',
  `countReads` int(11) NOT NULL DEFAULT '0',
  `private` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblCalendar` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL,
  `creatorId` bigint(20) unsigned NOT NULL,
  `ownerId` bigint(20) unsigned NOT NULL,
  `timeBegin` datetime DEFAULT NULL,
  `timeEnd` datetime DEFAULT NULL,
  `info` text,
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(200) DEFAULT NULL,
  `categoryType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  `creatorId` int(10) unsigned NOT NULL DEFAULT '0',
  `permissions` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ownerId` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblCcPay` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clientid` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `passcode` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `date` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `minutes` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `price` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `packagekey` int(10) unsigned NOT NULL,
  `remoteid` int(10) unsigned NOT NULL,
  `email` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `card` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `cvv` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `status` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `name` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `cardtype` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblChat` (
  `chatId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `authorId` int(11) NOT NULL,
  `msg` varchar(255) NOT NULL,
  `msgDate` datetime NOT NULL,
  `msgRead` tinyint(1) NOT NULL,
  PRIMARY KEY (`chatId`),
  KEY `user_and_author_ids` (`authorId`,`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblChecksums` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fileId` int(11) unsigned DEFAULT NULL,
  `sha1` varchar(40) DEFAULT NULL,
  `md5` varchar(32) DEFAULT NULL,
  `timeCreated` datetime DEFAULT NULL,
  `timeExec` float DEFAULT NULL,
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblComments` (
  `commentId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `commentType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `commentText` text,
  `commentPrivate` tinyint(4) NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  `timeDeleted` datetime DEFAULT NULL,
  `deletedBy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ownerId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userIP` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`commentId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblContacts` (
  `contactId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contactType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `groupId` int(10) unsigned NOT NULL DEFAULT '0',
  `userId` int(10) unsigned NOT NULL DEFAULT '0',
  `otherUserId` int(10) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  PRIMARY KEY (`contactId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblCustomers` (
  `customerId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customerName` varchar(30) DEFAULT NULL,
  `customerPass` varchar(70) DEFAULT NULL,
  `contactMail` text,
  `contactDetails` text,
  PRIMARY KEY (`customerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblEvents` (
  `eventId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL,
  `category` int(10) unsigned NOT NULL,
  `ownerId` bigint(20) unsigned NOT NULL,
  `refererId` bigint(20) unsigned NOT NULL,
  `timeCreated` datetime DEFAULT NULL,
  PRIMARY KEY (`eventId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblFAQ` (
  `faqId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question` text,
  `answer` text,
  `createdBy` int(10) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  PRIMARY KEY (`faqId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblFeedback` (
  `feedbackId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `feedbackType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `subj` text,
  `body` text,
  `userId` int(10) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  `subjectId` int(10) unsigned NOT NULL DEFAULT '0',
  `answer` text,
  `answeredBy` int(10) unsigned NOT NULL DEFAULT '0',
  `timeAnswered` datetime DEFAULT NULL,
  PRIMARY KEY (`feedbackId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblFiles` (
  `fileId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fileName` varchar(200) DEFAULT NULL,
  `fileSize` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fileMime` varchar(200) DEFAULT NULL,
  `ownerId` int(10) unsigned NOT NULL DEFAULT '0',
  `categoryId` int(10) unsigned NOT NULL DEFAULT '0',
  `uploaderId` int(10) unsigned NOT NULL DEFAULT '0',
  `uploaderIP` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fileType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timeUploaded` datetime DEFAULT NULL,
  `timeDeleted` datetime DEFAULT NULL,
  `mediaType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cnt` int(10) unsigned NOT NULL DEFAULT '0',
  `rating` tinyint(3) unsigned NOT NULL,
  `ratingCnt` int(10) unsigned NOT NULL,
  PRIMARY KEY (`fileId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblForums` (
  `itemId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `itemType` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `authorId` int(10) unsigned NOT NULL DEFAULT '0',
  `parentId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `itemSubject` varchar(100) DEFAULT NULL,
  `itemBody` text,
  `timeCreated` datetime DEFAULT NULL,
  `deletedBy` int(10) unsigned NOT NULL DEFAULT '0',
  `timeDeleted` datetime DEFAULT NULL,
  `itemRead` bigint(20) unsigned NOT NULL DEFAULT '0',
  `sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblFriendRequests` (
  `reqId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `senderId` int(10) unsigned NOT NULL DEFAULT '0',
  `recieverId` int(10) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  `categoryId` int(10) unsigned NOT NULL DEFAULT '0',
  `msg` text,
  PRIMARY KEY (`reqId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblGuestbooks` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL DEFAULT '0',
  `authorId` int(10) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `body` text,
  `timeDeleted` datetime DEFAULT NULL,
  `deletedBy` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeRead` datetime DEFAULT NULL,
  `answerId` int(11) NOT NULL,
  `isPrivate` tinyint(1) NOT NULL,
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblLocationCity` (
  `cityId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `regionId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`cityId`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblLocationRegion` (
  `regionId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`regionId`),
  KEY `st_lan` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblLocationZip` (
  `zip` int(5) NOT NULL DEFAULT '0',
  `cityId` int(10) unsigned NOT NULL,
  `regionId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`zip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblLogins` (
  `mainId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  `IP` int(10) unsigned NOT NULL DEFAULT '0',
  `userAgent` text,
  PRIMARY KEY (`mainId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblLogs` (
  `entryId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entryText` text,
  `entryLevel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  `userId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userIP` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblMessages` (
  `msgId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ownerId` int(10) unsigned NOT NULL DEFAULT '0',
  `fromId` int(10) unsigned NOT NULL DEFAULT '0',
  `toId` int(10) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(200) DEFAULT NULL,
  `body` text,
  `timeCreated` datetime DEFAULT NULL,
  `timeRead` datetime DEFAULT NULL,
  `timeDeleted` datetime DEFAULT NULL,
  `groupId` int(10) unsigned NOT NULL DEFAULT '0',
  `answerId` int(11) NOT NULL,
  PRIMARY KEY (`msgId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblModeration` (
  `queueId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queueType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `itemId` int(10) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  `creatorId` int(10) unsigned NOT NULL DEFAULT '0',
  `moderatedBy` int(10) unsigned NOT NULL DEFAULT '0',
  `timeModerated` datetime DEFAULT NULL,
  `autoTriggered` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`queueId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblNews` (
  `newsId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `body` text,
  `rss_enabled` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `creatorId` int(10) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  `timeEdited` datetime DEFAULT NULL,
  `editorId` int(10) unsigned NOT NULL DEFAULT '0',
  `timeToPublish` datetime DEFAULT NULL,
  `categoryId` int(10) unsigned NOT NULL DEFAULT '0',
  `deletedBy` int(10) unsigned NOT NULL DEFAULT '0',
  `timeDeleted` datetime DEFAULT NULL,
  `rating` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ratingCnt` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`newsId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblPhpFreeChat` (
  `server` varchar(32) NOT NULL DEFAULT '',
  `group` varchar(64) NOT NULL DEFAULT '',
  `subgroup` varchar(128) NOT NULL DEFAULT '',
  `leaf` varchar(128) NOT NULL DEFAULT '',
  `leafvalue` text NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`server`,`group`,`subgroup`,`leaf`),
  KEY `server` (`server`,`group`,`subgroup`,`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblPollVotes` (
  `voteId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pollId` int(10) unsigned NOT NULL DEFAULT '0',
  `userId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`voteId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblPolls` (
  `pollId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pollType` tinyint(1) unsigned NOT NULL,
  `pollText` text,
  `timeStart` datetime DEFAULT NULL,
  `timeEnd` datetime DEFAULT NULL,
  `ownerId` int(10) unsigned NOT NULL DEFAULT '0',
  `createdBy` int(10) unsigned NOT NULL DEFAULT '0',
  `deletedBy` int(10) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  `timeDeleted` datetime DEFAULT NULL,
  PRIMARY KEY (`pollId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblProcessQueue` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `referId` bigint(20) unsigned DEFAULT NULL,
  `timeCreated` datetime DEFAULT NULL,
  `creatorId` int(10) unsigned DEFAULT NULL,
  `orderType` tinyint(3) unsigned NOT NULL,
  `orderStatus` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `orderParams` text,
  `attempts` tinyint(3) unsigned NOT NULL,
  `timeExec` float unsigned DEFAULT NULL,
  `timeCompleted` datetime DEFAULT NULL,
  `callback_log` text,
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblRatings` (
  `rateId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `itemId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `userId` int(10) unsigned NOT NULL DEFAULT '0',
  `rating` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timeRated` datetime DEFAULT NULL,
  PRIMARY KEY (`rateId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblRevisions` (
  `indexId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fieldId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fieldType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fieldText` text,
  `createdBy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  `categoryId` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`indexId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblSettings` (
  `settingId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ownerId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `categoryId` int(10) unsigned NOT NULL DEFAULT '0',
  `settingName` varchar(200) DEFAULT NULL,
  `settingValue` text,
  `settingType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timeSaved` datetime DEFAULT NULL,
  PRIMARY KEY (`settingId`),
  KEY `ownerId` (`ownerId`,`settingName`,`settingType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblStatistics` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `timeStart` datetime DEFAULT NULL,
  `timeEnd` datetime DEFAULT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `value` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblStopwords` (
  `wordId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `wordText` varchar(200) DEFAULT NULL,
  `wordType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `wordMatch` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`wordId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblSubscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned DEFAULT '0',
  `ownerId` int(10) unsigned NOT NULL DEFAULT '0',
  `itemId` int(10) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblTodoLists` (
  `itemId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categoryId` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `itemDesc` blob,
  `itemDetails` blob NOT NULL,
  `itemStatus` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `itemCategory` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `itemCreator` bigint(20) NOT NULL DEFAULT '0',
  `assignedTo` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblUserdata` (
  `fieldId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fieldName` varchar(100) DEFAULT NULL,
  `fieldType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fieldDefault` varchar(100) DEFAULT NULL,
  `allowTags` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `private` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fieldPriority` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `regRequire` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblUsers` (
  `userId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(200) DEFAULT NULL,
  `userPass` varchar(200) DEFAULT NULL,
  `userMode` tinyint(4) NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  `timeLastLogin` datetime DEFAULT NULL,
  `timeLastActive` datetime DEFAULT NULL,
  `timeLastLogout` datetime DEFAULT NULL,
  `timeDeleted` datetime DEFAULT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblVisits` (
  `visitId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL,
  `ownerId` int(10) unsigned NOT NULL DEFAULT '0',
  `creatorId` int(10) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime DEFAULT NULL,
  PRIMARY KEY (`visitId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblWiki` (
  `wikiId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wikiName` varchar(200) DEFAULT NULL,
  `msg` text,
  `timeCreated` datetime DEFAULT NULL,
  `createdBy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `lockedBy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timeLocked` datetime DEFAULT NULL,
  `hasFiles` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`wikiId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
