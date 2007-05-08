/*
SQLyog Community Edition- MySQL GUI v5.30
Host - 5.1.16-beta-community-nt : Database - dbProcess
*********************************************************************
Server version : 5.1.16-beta-community-nt
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

create database if not exists `dbProcess`;

USE `dbProcess`;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `tblLogins` */

DROP TABLE IF EXISTS `tblLogins`;

CREATE TABLE `tblLogins` (
  `mainId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL,
  `timeCreated` datetime DEFAULT NULL,
  `IP` int(10) unsigned NOT NULL,
  `userAgent` text,
  PRIMARY KEY (`mainId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

/*Table structure for table `tblOrders` */

DROP TABLE IF EXISTS `tblOrders`;

CREATE TABLE `tblOrders` (
  `entryId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `timeCreated` datetime DEFAULT NULL,
  `ownerId` int(10) unsigned DEFAULT NULL,
  `orderType` tinyint(3) unsigned NOT NULL,
  `orderParams` text,
  PRIMARY KEY (`entryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `tblUsers` */

DROP TABLE IF EXISTS `tblUsers`;

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

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
