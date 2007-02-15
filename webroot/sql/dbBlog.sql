/*Table structure for table `tblBlogs` */

DROP TABLE IF EXISTS `tblBlogs`;

CREATE TABLE `tblBlogs` (
  `blogId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `userId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `blogTitle` varchar(100) NOT NULL DEFAULT '',
  `blogBody` blob NOT NULL,
  `timeCreated` datetime NOT NULL,
  `timeUpdated` datetime DEFAULT NULL,
  `categoryId` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`blogId`)
) ENGINE=MyISAM;

/*Data for the table `tblBlogs` */

/*Table structure for table `tblCategories` */

DROP TABLE IF EXISTS `tblCategories`;

CREATE TABLE `tblCategories` (
  `categoryId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(100) NOT NULL DEFAULT '',
  `categoryType` tinyint(3) unsigned DEFAULT '0',
  `globalCategory` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `creatorId` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM;

/*Table structure for table `tblUsers` */

DROP TABLE IF EXISTS `tblUsers`;

CREATE TABLE `tblUsers` (
  `userId` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(20) NOT NULL,
  `userPass` varchar(40) NOT NULL,
  `userMode` tinyint(1) NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `lastLoginTime` datetime NOT NULL,
  `lastActive` datetime NOT NULL,
  `userStatus` blob NOT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM;

/*Data for the table `tblUsers` */

insert into `tblUsers` (`userId`,`userName`,`userPass`,`userMode`,`timeCreated`,`lastLoginTime`,`lastActive`,`userStatus`) values (6,'martin','e3491d5ae784f0d3f50deb36b4bef33bf0eff1c7',2,'0000-00-00 00:00:00','2006-11-29 16:34:16','2006-11-29 16:47:25','Bloggar');

CREATE TABLE `tblLogs` (                                    
	`entryId` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,  
	`entryText` blob NOT NULL,                                
	`entryLevel` tinyint(1) unsigned NOT NULL DEFAULT '0',    
	`timeCreated` datetime NOT NULL,                          
	`userId` smallint(5) unsigned NOT NULL DEFAULT '0',       
	`userIP` int(10) unsigned NOT NULL DEFAULT '0',           
	PRIMARY KEY (`entryId`)                                   
) ENGINE=MyISAM;