
CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dbadblock` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE dbadblock;
CREATE TABLE tblAdblockRules (
  ruleId bigint(20) unsigned NOT NULL auto_increment,
  ruleType tinyint(1) unsigned NOT NULL default '0',
  ruleText varchar(240) character set utf8 default NULL,
  creatorId smallint(5) unsigned NOT NULL default '0',
  editorId smallint(5) unsigned NOT NULL default '0',
  timeCreated datetime NOT NULL,
  timeEdited datetime default NULL,
  sampleUrl varchar(200) character set utf8 default NULL,
  deletedBy smallint(5) unsigned NOT NULL default '0',
  timeDeleted datetime default NULL,
  PRIMARY KEY  (ruleId)
) ENGINE=MyISAM AUTO_INCREMENT=705 DEFAULT CHARSET=latin1;
CREATE TABLE tblCategories (
  categoryId bigint(20) unsigned NOT NULL auto_increment,
  categoryName varchar(100) NOT NULL default '',
  categoryType tinyint(1) unsigned default '0',
  timeCreated datetime default NULL,
  creatorId int(10) unsigned NOT NULL default '0',
  categoryPermissions tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (categoryId)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
CREATE TABLE tblComments (
  commentId bigint(20) unsigned NOT NULL auto_increment,
  commentType tinyint(1) unsigned NOT NULL default '0',
  commentText text,
  commentPrivate tinyint(1) NOT NULL default '0',
  timeCreated datetime NOT NULL,
  timeDeleted datetime default NULL,
  deletedBy smallint(5) unsigned NOT NULL default '0',
  ownerId bigint(20) unsigned NOT NULL default '0',
  userId smallint(5) unsigned NOT NULL default '0',
  userIP bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (commentId)
) ENGINE=MyISAM AUTO_INCREMENT=434 DEFAULT CHARSET=utf8;
CREATE TABLE tblFiles (
  fileId bigint(20) unsigned NOT NULL auto_increment,
  fileName varchar(250) character set utf8 default NULL,
  fileSize bigint(20) unsigned NOT NULL default '0',
  fileMime varchar(100) character set utf8 default NULL,
  ownerId int(10) unsigned NOT NULL default '0',
  categoryId int(10) unsigned NOT NULL default '0',
  uploaderId int(10) unsigned NOT NULL default '0',
  uploaderIP bigint(20) unsigned NOT NULL default '0',
  fileType tinyint(1) unsigned NOT NULL default '0',
  timeUploaded datetime NOT NULL,
  cnt int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (fileId)
) ENGINE=MyISAM AUTO_INCREMENT=140 DEFAULT CHARSET=latin1;
CREATE TABLE tblLogins (
  mainId int(10) unsigned NOT NULL auto_increment,
  userId int(10) unsigned NOT NULL,
  timeCreated datetime default NULL,
  IP int(10) unsigned NOT NULL,
  userAgent text,
  PRIMARY KEY  (mainId)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
CREATE TABLE tblLogs (
  entryId mediumint(8) unsigned NOT NULL auto_increment,
  entryText text character set utf8 NOT NULL,
  entryLevel tinyint(1) unsigned NOT NULL default '0',
  timeCreated datetime NOT NULL,
  userId smallint(5) unsigned NOT NULL default '0',
  userIP int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (entryId)
) ENGINE=MyISAM AUTO_INCREMENT=748 DEFAULT CHARSET=latin1;
CREATE TABLE tblNews (
  newsId int(10) unsigned NOT NULL auto_increment,
  title varchar(100) character set utf8 NOT NULL,
  body text character set utf8 NOT NULL,
  rss_enabled tinyint(1) unsigned NOT NULL default '0',
  creatorId int(10) unsigned NOT NULL,
  timeCreated datetime NOT NULL,
  timeEdited datetime NOT NULL default '0000-00-00 00:00:00',
  editorId int(10) unsigned default '0',
  timeToPublish datetime NOT NULL default '0000-00-00 00:00:00',
  categoryId int(10) unsigned NOT NULL,
  PRIMARY KEY  (newsId)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
CREATE TABLE tblProblemSites (
  siteId bigint(20) unsigned NOT NULL auto_increment,
  userId smallint(5) unsigned NOT NULL default '0',
  userIP bigint(20) unsigned NOT NULL default '0',
  url text,
  `type` tinyint(1) unsigned NOT NULL default '0',
  `comment` text,
  timeCreated datetime NOT NULL,
  timeDeleted datetime NOT NULL default '0000-00-00 00:00:00',
  deletedBy bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (siteId)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
CREATE TABLE tblRevisions (
  indexId int(10) unsigned NOT NULL auto_increment,
  fieldId bigint(20) unsigned NOT NULL,
  fieldType tinyint(3) unsigned default NULL,
  fieldText text character set utf8 NOT NULL,
  createdBy smallint(5) unsigned NOT NULL default '0',
  timeCreated datetime NOT NULL,
  categoryId tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (indexId)
) ENGINE=MyISAM AUTO_INCREMENT=226 DEFAULT CHARSET=latin1;
CREATE TABLE tblSettings (
  settingId bigint(20) unsigned NOT NULL auto_increment,
  ownerId smallint(5) unsigned NOT NULL default '0',
  settingName varchar(50) character set utf8 NOT NULL,
  settingValue text character set utf8 NOT NULL,
  settingType tinyint(3) unsigned NOT NULL,
  timeSaved datetime NOT NULL,
  PRIMARY KEY  (settingId)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
CREATE TABLE tblUsers (
  userId smallint(5) unsigned NOT NULL auto_increment,
  userName varchar(20) character set utf8 NOT NULL,
  userPass varchar(40) NOT NULL,
  userMode tinyint(1) NOT NULL default '0',
  timeCreated datetime NOT NULL,
  timeLastLogin datetime NOT NULL default '0000-00-00 00:00:00',
  timeLastActive datetime NOT NULL default '0000-00-00 00:00:00',
  timeLastLogout datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (userId)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
CREATE TABLE tblWiki (
  wikiId bigint(20) unsigned NOT NULL auto_increment,
  wikiName varchar(30) default NULL,
  msg text,
  timeCreated datetime NOT NULL,
  createdBy smallint(5) unsigned NOT NULL default '0',
  lockedBy smallint(5) unsigned NOT NULL default '0',
  timeLocked datetime NOT NULL default '0000-00-00 00:00:00',
  hasFiles tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (wikiId)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
DELIMITER ;;
/*!50003 SET SESSION SQL_MODE=""*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=root@localhost*/ /*!50003 PROCEDURE getUser(
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

CREATE DATABASE /*!32312 IF NOT EXISTS*/ dbajaxchat /*!40100 DEFAULT CHARACTER SET utf8 */;

USE dbajaxchat;
CREATE TABLE tblChat (
  entryId bigint(20) unsigned NOT NULL auto_increment,
  roomId bigint(20) unsigned NOT NULL,
  userId bigint(20) unsigned NOT NULL,
  timeCreated datetime NOT NULL,
  msg blob,
  PRIMARY KEY  (entryId)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
CREATE TABLE tblChatRooms (
  roomId bigint(20) unsigned NOT NULL auto_increment,
  roomName varchar(50) default NULL,
  timeCreated datetime NOT NULL,
  createdBy bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (roomId)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
CREATE TABLE tblChatUsers (
  entryId bigint(20) unsigned NOT NULL auto_increment,
  roomId bigint(20) unsigned NOT NULL,
  userId bigint(20) unsigned NOT NULL,
  lastSeen datetime NOT NULL,
  PRIMARY KEY  (entryId)
) ENGINE=MyISAM AUTO_INCREMENT=32158 DEFAULT CHARSET=utf8;
CREATE TABLE tblLogins (
  mainId int(10) unsigned NOT NULL auto_increment,
  userId int(10) unsigned NOT NULL,
  timeCreated datetime default NULL,
  IP int(10) unsigned NOT NULL,
  userAgent text,
  PRIMARY KEY  (mainId)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
CREATE TABLE tblLogs (
  entryId mediumint(8) unsigned NOT NULL auto_increment,
  entryText text character set utf8 NOT NULL,
  entryLevel tinyint(1) unsigned NOT NULL default '0',
  timeCreated datetime NOT NULL,
  userId smallint(5) unsigned NOT NULL default '0',
  userIP int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (entryId)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
CREATE TABLE tblUsers (
  userId smallint(5) unsigned NOT NULL auto_increment,
  userName varchar(20) character set utf8 NOT NULL,
  userPass varchar(40) NOT NULL,
  userMode tinyint(1) NOT NULL default '0',
  timeCreated datetime NOT NULL,
  timeLastLogin datetime NOT NULL default '0000-00-00 00:00:00',
  timeLastActive datetime NOT NULL default '0000-00-00 00:00:00',
  timeLastLogout datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (userId)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ dbjanina /*!40100 DEFAULT CHARACTER SET utf8 */;

USE dbjanina;
CREATE TABLE tblCategories (
  categoryId bigint(20) unsigned NOT NULL auto_increment,
  categoryName varchar(100) NOT NULL default '',
  categoryType tinyint(1) unsigned default '0',
  timeCreated datetime NOT NULL,
  creatorId int(10) unsigned NOT NULL default '0',
  categoryPermissions tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (categoryId)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
CREATE TABLE tblFiles (
  fileId bigint(20) unsigned NOT NULL auto_increment,
  fileName varchar(250) character set utf8 default NULL,
  fileSize bigint(20) unsigned NOT NULL default '0',
  fileMime varchar(100) character set utf8 default NULL,
  ownerId int(10) unsigned NOT NULL default '0',
  categoryId int(10) unsigned NOT NULL default '0',
  uploaderId int(10) unsigned NOT NULL default '0',
  uploaderIP bigint(20) unsigned NOT NULL default '0',
  fileType tinyint(1) unsigned NOT NULL default '0',
  timeUploaded datetime NOT NULL,
  cnt int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (fileId)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=latin1;
CREATE TABLE tblLogins (
  mainId int(10) unsigned NOT NULL auto_increment,
  userId int(10) unsigned NOT NULL,
  timeCreated datetime default NULL,
  IP int(10) unsigned NOT NULL,
  userAgent text,
  PRIMARY KEY  (mainId)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
CREATE TABLE tblLogs (
  entryId mediumint(8) unsigned NOT NULL auto_increment,
  entryText text character set utf8 NOT NULL,
  entryLevel tinyint(1) unsigned NOT NULL default '0',
  timeCreated datetime NOT NULL,
  userId smallint(5) unsigned NOT NULL default '0',
  userIP int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (entryId)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
CREATE TABLE tblUsers (
  userId int(10) unsigned NOT NULL auto_increment,
  userName varchar(20) character set utf8 NOT NULL,
  userPass varchar(40) NOT NULL,
  userMode tinyint(1) NOT NULL default '0',
  timeCreated datetime NOT NULL,
  timeLastLogin datetime default NULL,
  timeLastActive datetime default NULL,
  timeLastLogout datetime default NULL,
  PRIMARY KEY  (userId)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ dblang /*!40100 DEFAULT CHARACTER SET utf8 */;

USE dblang;
CREATE TABLE tblCategories (
  categoryId smallint(5) unsigned NOT NULL auto_increment,
  categoryName varchar(100) NOT NULL default '',
  categoryType tinyint(3) unsigned NOT NULL default '0',
  timeCreated datetime NOT NULL,
  creatorId smallint(5) unsigned NOT NULL default '0',
  categoryPermissions tinyint(1) unsigned NOT NULL default '0',
  ownerId int(10) unsigned NOT NULL,
  PRIMARY KEY  (categoryId)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
CREATE TABLE tblLogins (
  mainId int(10) unsigned NOT NULL auto_increment,
  userId int(10) unsigned NOT NULL,
  timeCreated datetime default NULL,
  IP int(10) unsigned NOT NULL,
  userAgent text,
  PRIMARY KEY  (mainId)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
CREATE TABLE tblLogs (
  entryId mediumint(8) unsigned NOT NULL auto_increment,
  entryText text character set utf8 NOT NULL,
  entryLevel tinyint(1) unsigned NOT NULL default '0',
  timeCreated datetime NOT NULL,
  userId smallint(5) unsigned NOT NULL default '0',
  userIP int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (entryId)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
CREATE TABLE tblUsers (
  userId smallint(5) unsigned NOT NULL auto_increment,
  userName varchar(20) character set utf8 NOT NULL,
  userPass varchar(40) NOT NULL,
  userMode tinyint(1) NOT NULL default '0',
  timeCreated datetime NOT NULL,
  timeLastLogin datetime NOT NULL default '0000-00-00 00:00:00',
  timeLastActive datetime NOT NULL default '0000-00-00 00:00:00',
  timeLastLogout datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (userId)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE tblWiki (
  wikiId bigint(20) unsigned NOT NULL auto_increment,
  wikiName varchar(30) default NULL,
  msg text,
  timeCreated datetime NOT NULL,
  createdBy smallint(5) unsigned NOT NULL default '0',
  lockedBy smallint(5) unsigned NOT NULL default '0',
  timeLocked datetime NOT NULL default '0000-00-00 00:00:00',
  hasFiles tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (wikiId)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE tblWords (
  id int(10) unsigned NOT NULL auto_increment,
  lang smallint(5) unsigned NOT NULL,
  word varchar(50) NOT NULL,
  pron varchar(50) NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM AUTO_INCREMENT=463 DEFAULT CHARSET=utf8;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ dblyrics /*!40100 DEFAULT CHARACTER SET latin1 */;

USE dblyrics;
CREATE TABLE tblBands (
  bandId bigint(20) unsigned NOT NULL auto_increment,
  bandName varchar(40) character set utf8 NOT NULL,
  bandInfo text character set utf8,
  creatorId int(10) unsigned NOT NULL default '0',
  timeCreated datetime NOT NULL,
  PRIMARY KEY  (bandId)
) ENGINE=MyISAM AUTO_INCREMENT=235 DEFAULT CHARSET=latin1;
CREATE TABLE tblLogins (
  mainId int(10) unsigned NOT NULL auto_increment,
  userId int(10) unsigned NOT NULL,
  timeCreated datetime default NULL,
  IP int(10) unsigned NOT NULL,
  userAgent text,
  PRIMARY KEY  (mainId)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
CREATE TABLE tblLogs (
  entryId mediumint(8) unsigned NOT NULL auto_increment,
  entryText text character set utf8 NOT NULL,
  entryLevel tinyint(1) unsigned NOT NULL default '0',
  timeCreated datetime NOT NULL,
  userId smallint(5) unsigned NOT NULL default '0',
  userIP int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (entryId)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=latin1;
CREATE TABLE tblLyrics (
  lyricId bigint(20) unsigned NOT NULL auto_increment,
  lyricName varchar(200) character set utf8 NOT NULL,
  lyricText text character set utf8 NOT NULL,
  bandId bigint(20) unsigned NOT NULL default '0',
  creatorId bigint(20) unsigned NOT NULL default '0',
  timeCreated datetime NOT NULL,
  PRIMARY KEY  (lyricId)
) ENGINE=MyISAM AUTO_INCREMENT=4897 DEFAULT CHARSET=latin1;
CREATE TABLE tblNewAdditions (
  ID bigint(20) unsigned NOT NULL default '0',
  `type` tinyint(3) unsigned NOT NULL default '0',
  timeCreated datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE tblPendingChanges (
  `type` tinyint(3) unsigned NOT NULL default '0',
  p1 bigint(20) unsigned NOT NULL default '0',
  p2 varchar(255) character set utf8 NOT NULL default '0',
  p3 text character set utf8 NOT NULL,
  timeCreated datetime NOT NULL,
  userId bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE tblRecords (
  recordId bigint(20) unsigned NOT NULL auto_increment,
  recordName varchar(60) character set utf8 NOT NULL,
  recordInfo text character set utf8 NOT NULL,
  bandId bigint(20) NOT NULL default '0',
  creatorId bigint(20) unsigned NOT NULL default '0',
  timeCreated datetime NOT NULL,
  PRIMARY KEY  (recordId)
) ENGINE=MyISAM AUTO_INCREMENT=600 DEFAULT CHARSET=latin1;
CREATE TABLE tblTracks (
  recordId bigint(20) NOT NULL default '0',
  trackNumber tinyint(3) unsigned NOT NULL default '0',
  lyricId bigint(20) unsigned NOT NULL default '0',
  bandId bigint(20) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE tblUsers (
  userId bigint(20) unsigned NOT NULL default '0',
  userName varchar(30) character set utf8 NOT NULL,
  userPass varchar(40) NOT NULL,
  userMode tinyint(1) unsigned NOT NULL default '0',
  timeLastActive datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DELIMITER ;;
DELIMITER ;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ dbsample /*!40100 DEFAULT CHARACTER SET utf8 */;

USE dbSample;
CREATE TABLE tblBlogs (
  blogId int(10) unsigned NOT NULL auto_increment,
  userId int(10) unsigned NOT NULL default '0',
  blogTitle varchar(100) NOT NULL default '',
  blogBody text NOT NULL,
  timeCreated datetime default NULL,
  timeUpdated datetime default NULL,
  categoryId bigint(20) unsigned NOT NULL default '0',
  deletedBy int(10) unsigned NOT NULL default '0',
  timeDeleted datetime default NULL,
  rating tinyint(3) unsigned NOT NULL default '0',
  ratingCnt int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (blogId)
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=utf8;
CREATE TABLE tblCategories (
  categoryId bigint(20) unsigned NOT NULL auto_increment,
  categoryName varchar(100) NOT NULL default '',
  categoryType tinyint(1) unsigned default '0',
  timeCreated datetime default NULL,
  creatorId int(10) unsigned NOT NULL default '0',
  categoryPermissions tinyint(1) unsigned NOT NULL default '0',
  ownerId bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (categoryId)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
CREATE TABLE tblComments (
  commentId bigint(20) unsigned NOT NULL auto_increment,
  commentType tinyint(1) unsigned NOT NULL default '0',
  commentText text,
  commentPrivate tinyint(1) NOT NULL default '0',
  timeCreated datetime NOT NULL,
  timeDeleted datetime default NULL,
  deletedBy smallint(5) unsigned NOT NULL default '0',
  ownerId bigint(20) unsigned NOT NULL default '0',
  userId smallint(5) unsigned NOT NULL default '0',
  userIP bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (commentId)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
CREATE TABLE tblContacts (
  contactId int(10) unsigned NOT NULL auto_increment,
  contactType tinyint(1) unsigned NOT NULL default '0',
  groupId int(10) unsigned NOT NULL default '0',
  userId int(10) unsigned NOT NULL default '0',
  otherUserId int(10) unsigned NOT NULL default '0',
  timeCreated datetime default NULL,
  PRIMARY KEY  (contactId)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
CREATE TABLE tblFAQ (
  faqId int(10) unsigned NOT NULL auto_increment,
  question text,
  answer text,
  createdBy int(10) unsigned NOT NULL default '0',
  timeCreated datetime default NULL,
  PRIMARY KEY  (faqId)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
CREATE TABLE tblFeedback (
  feedbackId int(10) unsigned NOT NULL auto_increment,
  `text` text,
  userId int(10) unsigned NOT NULL default '0',
  timeCreated datetime default NULL,
  PRIMARY KEY  (feedbackId)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
CREATE TABLE tblFiles (
  fileId bigint(20) unsigned NOT NULL auto_increment,
  fileName varchar(250) character set utf8 default NULL,
  fileSize bigint(20) unsigned NOT NULL default '0',
  fileMime varchar(100) character set utf8 default NULL,
  ownerId int(10) unsigned NOT NULL default '0',
  categoryId int(10) unsigned NOT NULL default '0',
  uploaderId int(10) unsigned NOT NULL default '0',
  uploaderIP bigint(20) unsigned NOT NULL default '0',
  fileType tinyint(1) unsigned NOT NULL default '0',
  timeUploaded datetime NOT NULL,
  cnt int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (fileId)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
CREATE TABLE tblFriendRequests (
  reqId int(10) unsigned NOT NULL auto_increment,
  senderId int(10) unsigned NOT NULL default '0',
  recieverId int(10) unsigned NOT NULL default '0',
  timeCreated datetime NOT NULL,
  categoryId int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (reqId)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
CREATE TABLE tblGuestbooks (
  userId bigint(20) unsigned NOT NULL default '0',
  authorId bigint(20) unsigned NOT NULL default '0',
  timeCreated datetime default NULL,
  `subject` varchar(200) character set utf8 NOT NULL,
  body text,
  entryId bigint(20) unsigned NOT NULL auto_increment,
  entryDeleted tinyint(1) unsigned NOT NULL default '0',
  timeDeleted datetime default NULL,
  entryRead tinyint(1) unsigned NOT NULL default '0',
  timeRead datetime default NULL,
  PRIMARY KEY  (entryId)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
CREATE TABLE tblLogins (
  mainId int(10) unsigned NOT NULL auto_increment,
  userId int(10) unsigned NOT NULL,
  timeCreated datetime default NULL,
  IP int(10) unsigned NOT NULL,
  userAgent text,
  PRIMARY KEY  (mainId)
) ENGINE=MyISAM AUTO_INCREMENT=86 DEFAULT CHARSET=utf8;
CREATE TABLE tblLogs (
  entryId mediumint(8) unsigned NOT NULL auto_increment,
  entryText text character set utf8 NOT NULL,
  entryLevel tinyint(1) unsigned NOT NULL default '0',
  timeCreated datetime NOT NULL,
  userId smallint(5) unsigned NOT NULL default '0',
  userIP int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (entryId)
) ENGINE=MyISAM AUTO_INCREMENT=156 DEFAULT CHARSET=latin1;
CREATE TABLE tblMessages (
  msgId bigint(20) unsigned NOT NULL auto_increment,
  ownerId int(10) unsigned NOT NULL default '0',
  fromId int(10) unsigned NOT NULL default '0',
  toId int(10) unsigned NOT NULL,
  `subject` varchar(200) default NULL,
  body text,
  timeCreated datetime default NULL,
  timeRead datetime default NULL,
  groupId int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (msgId)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
CREATE TABLE tblModerationQueue (
  queueId bigint(20) unsigned NOT NULL auto_increment,
  queueType tinyint(3) unsigned NOT NULL default '0',
  itemId int(10) unsigned NOT NULL default '0',
  timeCreated datetime default NULL,
  creatorId int(10) unsigned NOT NULL default '0',
  moderatedBy int(10) unsigned NOT NULL default '0',
  timeModerated datetime default NULL,
  autoTriggered tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (queueId)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Modereringskö med olika objekt som behöver ';
CREATE TABLE tblNews (
  newsId int(10) unsigned NOT NULL auto_increment,
  title varchar(100) character set utf8 NOT NULL,
  body text character set utf8 NOT NULL,
  rss_enabled tinyint(1) unsigned NOT NULL default '0',
  creatorId int(10) unsigned NOT NULL,
  timeCreated datetime NOT NULL,
  timeEdited datetime NOT NULL default '0000-00-00 00:00:00',
  editorId int(10) unsigned default '0',
  timeToPublish datetime NOT NULL default '0000-00-00 00:00:00',
  categoryId int(10) unsigned NOT NULL,
  deletedBy int(10) unsigned NOT NULL default '0',
  timeDeleted datetime default NULL,
  rating tinyint(3) unsigned NOT NULL default '0',
  ratingCnt int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (newsId)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
CREATE TABLE tblRatings (
  rateId bigint(20) unsigned NOT NULL auto_increment,
  `type` tinyint(3) unsigned NOT NULL default '0',
  itemId bigint(20) unsigned NOT NULL default '0',
  userId int(10) unsigned NOT NULL default '0',
  rating tinyint(3) unsigned NOT NULL default '0',
  timeRated datetime default NULL,
  PRIMARY KEY  (rateId)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
CREATE TABLE tblRevisions (
  indexId int(10) unsigned NOT NULL auto_increment,
  fieldId bigint(20) unsigned NOT NULL,
  fieldType tinyint(3) unsigned default NULL,
  fieldText text character set utf8 NOT NULL,
  createdBy smallint(5) unsigned NOT NULL default '0',
  timeCreated datetime NOT NULL,
  categoryId tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (indexId)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
CREATE TABLE tblSettings (
  settingId bigint(20) unsigned NOT NULL auto_increment,
  ownerId smallint(5) unsigned NOT NULL default '0',
  settingName varchar(50) character set utf8 NOT NULL,
  settingValue text character set utf8 NOT NULL,
  settingType tinyint(3) unsigned NOT NULL,
  timeSaved datetime NOT NULL,
  PRIMARY KEY  (settingId)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
CREATE TABLE tblStatistics (
  entryId bigint(20) unsigned NOT NULL auto_increment,
  `time` datetime default NULL,
  logins int(10) unsigned NOT NULL default '0',
  registrations int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (entryId)
) ENGINE=MyISAM AUTO_INCREMENT=721 DEFAULT CHARSET=utf8;
CREATE TABLE tblStopwords (
  wordId smallint(10) unsigned NOT NULL auto_increment,
  wordText varchar(30) character set utf8 default NULL,
  wordType tinyint(3) unsigned NOT NULL default '0',
  wordMatch tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (wordId)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
CREATE TABLE tblUserdata (
  fieldId int(10) unsigned NOT NULL auto_increment,
  fieldName varchar(30) character set utf8 default NULL,
  fieldType tinyint(3) unsigned NOT NULL default '0',
  fieldDefault varchar(30) character set utf8 NOT NULL,
  allowTags tinyint(3) unsigned NOT NULL default '0',
  private tinyint(1) unsigned NOT NULL default '0',
  fieldPriority tinyint(3) unsigned NOT NULL default '0',
  regRequire tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (fieldId)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
CREATE TABLE tblUsers (
  userId smallint(5) unsigned NOT NULL auto_increment,
  userName varchar(20) character set utf8 NOT NULL,
  userPass varchar(40) NOT NULL,
  userMode tinyint(1) NOT NULL default '0',
  timeCreated datetime NOT NULL,
  timeLastLogin datetime NOT NULL default '0000-00-00 00:00:00',
  timeLastActive datetime NOT NULL default '0000-00-00 00:00:00',
  timeLastLogout datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (userId)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
CREATE TABLE tblVisits (
  visitId int(10) unsigned NOT NULL auto_increment,
  ownerId int(10) unsigned NOT NULL default '0',
  creatorId int(10) unsigned NOT NULL default '0',
  timeCreated datetime default NULL,
  PRIMARY KEY  (visitId)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
CREATE TABLE tblWiki (
  wikiId bigint(20) unsigned NOT NULL auto_increment,
  wikiName varchar(30) default NULL,
  msg text,
  timeCreated datetime NOT NULL,
  createdBy smallint(5) unsigned NOT NULL default '0',
  lockedBy smallint(5) unsigned NOT NULL default '0',
  timeLocked datetime NOT NULL default '0000-00-00 00:00:00',
  hasFiles tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (wikiId)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
DELIMITER ;;
DELIMITER ;
