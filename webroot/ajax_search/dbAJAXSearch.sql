create database if not exists `dbAJAXSearch`;

USE `dbAJAXSearch`;

DROP TABLE IF EXISTS `tblText`;

CREATE TABLE `tblText` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `txt` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

insert  into `tblText`(`id`,`txt`) values (1,'some text here'),(2,'some more text'),(3,'and then some more'),(4,'even more text can be found here'),(5,'and yet some is to be found within here'),(6,'guess what? more text'),(7,'and then some');
