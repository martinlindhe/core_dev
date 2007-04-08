/*
SQLyog Enterprise - MySQL GUI v5.23 Beta 2
Host - 5.1.15-beta-community-nt : Database - dbJanina
*********************************************************************
Server version : 5.1.15-beta-community-nt
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

create database if not exists `dbJanina`;

USE `dbJanina`;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `tblCategories` */

DROP TABLE IF EXISTS `tblCategories`;

CREATE TABLE `tblCategories` (
  `categoryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(100) NOT NULL DEFAULT '',
  `categoryType` tinyint(1) unsigned DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `ownerId` int(10) unsigned NOT NULL DEFAULT '0',
  `globalCategory` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `tblCategories` */

insert  into `tblCategories`(`categoryId`,`categoryName`,`categoryType`,`timeCreated`,`ownerId`,`globalCategory`) values (1,'In Front',1,'2007-03-28 16:51:07',1,1),(2,'Behind',1,'2007-03-28 16:52:12',1,1),(3,'Projects',1,'2007-03-28 16:52:32',1,1),(4,'Wallpaper',1,'2007-03-28 16:52:39',1,1),(5,'My Art',1,'2007-03-28 16:52:39',1,1);

/*Table structure for table `tblFiles` */

DROP TABLE IF EXISTS `tblFiles`;

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
) ENGINE=MyISAM AUTO_INCREMENT=77 DEFAULT CHARSET=latin1;

/*Data for the table `tblFiles` */

insert  into `tblFiles`(`fileId`,`fileName`,`fileSize`,`fileMime`,`ownerId`,`categoryId`,`uploaderId`,`uploaderIP`,`fileType`,`timeUploaded`,`cnt`) values (34,'_MG_1441.jpg',98167,'image/jpeg',1,1,1,2130706433,105,'2007-03-28 17:16:24',0),(35,'_MG_6278v4.jpg',170751,'image/jpeg',1,1,1,2130706433,105,'2007-03-28 17:16:28',0),(36,'arven.jpg',115955,'image/jpeg',1,1,1,2130706433,105,'2007-03-28 17:16:30',0),(37,'Assent.jpg',351598,'image/jpeg',1,1,1,2130706433,105,'2007-03-28 17:16:33',0),(38,'Avalanche-stor.jpg',106268,'image/jpeg',1,1,1,2130706433,105,'2007-03-28 17:16:36',0),(39,'backlash_full_II.jpg',165559,'image/jpeg',1,1,1,2130706433,105,'2007-03-28 17:16:39',0),(41,'Cherry.jpg',135500,'image/jpeg',1,1,1,2130706433,105,'2007-03-28 17:16:45',0),(42,'dads_girl.jpg',132572,'image/jpeg',1,1,1,2130706433,105,'2007-03-28 17:16:48',0),(43,'Darling.jpg',225758,'image/jpeg',1,1,1,2130706433,105,'2007-03-28 17:16:50',0),(44,'FaitAccompli.jpg',184904,'image/jpeg',1,1,1,2130706433,105,'2007-03-28 17:16:53',0),(45,'Fashion-Cat.jpg',97732,'image/jpeg',1,1,1,2130706433,105,'2007-03-28 17:16:57',0),(47,'glam4VII.jpg',386816,'image/jpeg',1,1,1,2130706433,105,'2007-03-28 17:17:07',0),(48,'Hottie.jpg',234638,'image/jpeg',1,1,1,2130706433,105,'2007-03-28 17:17:10',0),(49,'my_pussy.jpg',162331,'image/jpeg',1,1,1,2130706433,105,'2007-03-28 17:17:13',0),(50,'Pam.jpg',193217,'image/jpeg',1,1,1,2130706433,105,'2007-03-28 17:17:16',0),(51,'08.jpg',429214,'image/jpeg',1,2,1,2130706433,105,'2007-03-28 17:17:35',0),(52,'dread.jpg',353087,'image/jpeg',1,2,1,2130706433,105,'2007-03-28 17:17:38',0),(53,'Latino-Lover.jpg',341955,'image/jpeg',1,2,1,2130706433,105,'2007-03-28 17:17:41',0),(54,'Le_Pettre.jpg',366539,'image/jpeg',1,2,1,2130706433,105,'2007-03-28 17:17:43',0),(55,'Scarface.jpg',220864,'image/jpeg',1,2,1,2130706433,105,'2007-03-28 17:17:46',0),(56,'contact_03.gif',1906,'image/gif',1,3,1,2130706433,105,'2007-03-28 17:18:23',0),(57,'Galleri-face.jpg',217235,'image/jpeg',1,3,1,2130706433,105,'2007-03-28 17:18:26',0),(58,'janinamagnusson-logga.jpg',16027,'image/jpeg',1,3,1,2130706433,105,'2007-03-28 17:18:28',0),(59,'Randy.jpg',41578,'image/jpeg',1,3,1,2130706433,105,'2007-03-28 17:18:30',0),(60,'Another_janina.jpg',162607,'image/jpeg',1,4,1,2130706433,105,'2007-03-28 17:18:41',0),(61,'Beauty.jpg',243230,'image/jpeg',1,4,1,2130706433,105,'2007-03-28 17:18:44',0),(62,'bikini.jpg',244067,'image/jpeg',1,4,1,2130706433,105,'2007-03-28 17:18:46',0),(63,'lady_teilAb.jpg',452434,'image/jpeg',1,4,1,2130706433,105,'2007-03-28 17:18:49',0),(64,'5.JPG',367263,'image/jpeg',1,5,1,2130706433,105,'2007-03-28 17:19:04',0),(65,'Flicka.jpg',518334,'image/jpeg',1,5,1,2130706433,105,'2007-03-28 17:19:06',0),(66,'Horatio.jpg',320130,'image/jpeg',1,5,1,2130706433,105,'2007-03-28 17:19:09',0),(67,'Janina.jpg',400641,'image/jpeg',1,5,1,2130706433,105,'2007-03-28 17:19:11',0),(68,'Paris.jpg',342710,'image/jpeg',1,5,1,2130706433,105,'2007-03-28 17:19:14',0),(69,'pingla.jpg',412740,'image/jpeg',1,5,1,2130706433,105,'2007-03-28 17:19:17',0),(70,'Subhumans.jpg',370152,'image/jpeg',1,5,1,2130706433,105,'2007-03-28 17:19:20',0),(71,'Twisted.jpg',596734,'image/jpeg',1,5,1,2130706433,105,'2007-03-28 17:19:23',0),(74,'01 4 promille - Im NÃ¤chsten Leben.mp3',3263899,'audio/x-mpeg',1,0,1,2130706433,105,'2007-03-28 22:19:26',0),(76,'31-Los_Crudos-31_-_Thats_Right_Were_That_Spic_Band-rH.mp3',907058,'audio/x-mpeg',1,0,1,2130706433,105,'2007-03-28 23:42:15',0);

/*Table structure for table `tblLogs` */

DROP TABLE IF EXISTS `tblLogs`;

CREATE TABLE `tblLogs` (
  `entryId` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `entryText` text CHARACTER SET utf8 NOT NULL,
  `entryLevel` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timeCreated` datetime NOT NULL,
  `userId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userIP` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

/*Data for the table `tblLogs` */

insert  into `tblLogs`(`entryId`,`entryText`,`entryLevel`,`timeCreated`,`userId`,`userIP`) values (1,'failed login attempt: username martin',0,'2007-03-19 15:56:12',0,0),(2,'user logged out',0,'2007-03-19 15:56:45',0,0),(3,'Session timed out after 17950 (timeout is 1800)',0,'2007-03-19 21:13:32',0,0),(4,'Session timed out after 8936 (timeout is 1800)',0,'2007-03-22 19:59:02',0,0),(5,'user logged out',0,'2007-03-28 12:46:31',0,0),(6,'Attempt to upload too big file',0,'2007-03-28 13:04:51',1,2130706433),(7,'Attempt to upload too big file',0,'2007-03-28 13:04:57',1,2130706433),(8,'Attempt to upload too big file',0,'2007-03-28 13:05:58',1,2130706433),(9,'user logged out',0,'2007-03-28 15:26:21',0,0),(10,'user logged out',0,'2007-03-28 17:26:42',0,0),(11,'user logged out',0,'2007-03-28 17:28:30',0,0),(12,'Session timed out after 3830 (timeout is 1800)',0,'2007-03-28 18:34:33',0,0),(13,'Session timed out after 6716 (timeout is 1800)',0,'2007-03-28 21:13:44',0,0),(14,'Session timed out after 2882 (timeout is 1800)',0,'2007-03-28 22:13:25',0,0),(15,'Session timed out after 2832 (timeout is 1800)',0,'2007-03-28 23:39:44',0,0);

/*Table structure for table `tblUsers` */

DROP TABLE IF EXISTS `tblUsers`;

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

/*Data for the table `tblUsers` */

insert  into `tblUsers`(`userId`,`userName`,`userPass`,`userMode`,`timeCreated`,`timeLastLogin`,`timeLastActive`,`timeLastLogout`) values (1,'martin','e0c86be17d64250aa360c685f84ff502be043819',2,'0000-00-00 00:00:00','2007-04-03 23:28:52','2007-04-03 23:33:49','2007-03-28 23:39:44');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
