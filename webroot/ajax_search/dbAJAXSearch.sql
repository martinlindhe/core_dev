/*
SQLyog Enterprise - MySQL GUI v5.23 Beta 2
Host - 5.1.14-beta-community-nt : Database - dbAJAXSearch
*********************************************************************
Server version : 5.1.14-beta-community-nt
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

create database if not exists `dbAJAXSearch`;

USE `dbAJAXSearch`;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `tblText` */

DROP TABLE IF EXISTS `tblText`;

CREATE TABLE `tblText` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `txt` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `tblText` */

insert  into `tblText`(`id`,`txt`) values (1,'some text here'),(2,'some more text '),(3,'and then some more'),(4,'even more text can be found here'),(5,'and yet some is to be found within here'),(6,'guess what? more text'),(7,'and then some');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
