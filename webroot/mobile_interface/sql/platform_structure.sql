/*
SQLyog Community Edition- MySQL GUI v5.27
Host - 5.1.16-beta-community-nt : Database - platform
*********************************************************************
Server version : 5.1.16-beta-community-nt
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

create database if not exists `platform`;

USE `platform`;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `a_cachetest` */

DROP TABLE IF EXISTS `a_cachetest`;

CREATE TABLE `a_cachetest` (
  `id` varchar(255) NOT NULL DEFAULT '',
  `id_id` varchar(32) NOT NULL DEFAULT '',
  `last_used_by` varchar(32) NOT NULL DEFAULT '',
  `times_used` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `aaa_test` */

DROP TABLE IF EXISTS `aaa_test`;

CREATE TABLE `aaa_test` (
  `main_id` bigint(1) NOT NULL AUTO_INCREMENT,
  `content_type` varchar(32) NOT NULL DEFAULT '',
  `content` int(11) NOT NULL DEFAULT '0',
  `content_more` text NOT NULL,
  `owner_id` varchar(50) NOT NULL DEFAULT '',
  `sender_id` varchar(32) NOT NULL DEFAULT '',
  `state_id` bigint(1) NOT NULL DEFAULT '0',
  `section_id` enum('0','1','2','3','4','5','6','7','8','9') NOT NULL DEFAULT '0',
  `obj_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  KEY `content_type` (`content_type`,`owner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=109526 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_aadata` */

DROP TABLE IF EXISTS `s_aadata`;

CREATE TABLE `s_aadata` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `data_s` text NOT NULL,
  PRIMARY KEY (`main_id`)
) ENGINE=MyISAM AUTO_INCREMENT=45784 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_aalog` */

DROP TABLE IF EXISTS `s_aalog`;

CREATE TABLE `s_aalog` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `data_s` text NOT NULL,
  PRIMARY KEY (`main_id`)
) ENGINE=MyISAM AUTO_INCREMENT=41210 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_ad` */

DROP TABLE IF EXISTS `s_ad`;

CREATE TABLE `s_ad` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `city_id` set('100','110','120','130','140','150','160','170','180') NOT NULL DEFAULT '',
  `ad_pos` enum('326_inside_highlight','140_inside_main','140_inside_profile','468_inside_popup','468_static_top','140_static_right','326_inside_nopic','326_inside_noupgrade') NOT NULL DEFAULT '326_inside_highlight',
  `ad_order` smallint(1) NOT NULL DEFAULT '0',
  `ad_name` varchar(255) NOT NULL DEFAULT '',
  `ad_img` text,
  `ad_id` varchar(32) NOT NULL DEFAULT '',
  `ad_url` varchar(255) NOT NULL DEFAULT '',
  `ad_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ad_stop` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ad_show` int(1) NOT NULL DEFAULT '0',
  `ad_showlimit` int(11) NOT NULL DEFAULT '0',
  `ad_clicklimit` int(11) NOT NULL DEFAULT '0',
  `ad_click` int(1) NOT NULL DEFAULT '0',
  `ad_tclick` int(1) NOT NULL DEFAULT '0',
  `status_id` enum('1','2') NOT NULL DEFAULT '2',
  `ad_type` enum('pic','swf','event') NOT NULL DEFAULT 'pic',
  `ad_size_x` smallint(1) NOT NULL DEFAULT '0',
  `ad_size_y` smallint(1) NOT NULL DEFAULT '0',
  `ad_target` enum('_blank','commain') NOT NULL DEFAULT '_blank',
  PRIMARY KEY (`main_id`),
  KEY `ad_pos` (`status_id`,`ad_pos`,`ad_order`)
) ENGINE=MyISAM AUTO_INCREMENT=176 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_admin` */

DROP TABLE IF EXISTS `s_admin`;

CREATE TABLE `s_admin` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `user_user` varchar(20) NOT NULL DEFAULT '',
  `user_name` varchar(20) NOT NULL DEFAULT '',
  `user_pass` varchar(32) NOT NULL DEFAULT '',
  `login_good` int(11) NOT NULL DEFAULT '0',
  `login_bad` int(11) NOT NULL DEFAULT '0',
  `login_page` enum('gb','pics','settings','changes') NOT NULL DEFAULT 'changes',
  `u_owner` int(1) NOT NULL,
  `u_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `u_crew` enum('0','1') NOT NULL DEFAULT '0',
  `status_id` enum('0','1','2','Z') NOT NULL DEFAULT '0',
  `city_id` set('100','110','120','130','140','150') NOT NULL DEFAULT '',
  `pos_all` set('obj_tho','obj_pcm','obj_mcm','obj_party','obj_full','obj_ue','obj_pimg','obj_tele','obj_pho','obj_event','obj_sms','obj_gb','obj_mail','obj_chat','obj_blog','poll','news_notice','news_send','pics','search_s','search_ss','search_sss','stat','log') NOT NULL DEFAULT '',
  `kick_now` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  UNIQUE KEY `user_user` (`user_user`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_adminchat` */

DROP TABLE IF EXISTS `s_adminchat`;

CREATE TABLE `s_adminchat` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(1) NOT NULL,
  `sender_id` int(1) NOT NULL,
  `user_read` enum('0','1') NOT NULL DEFAULT '0',
  `sent_cmt` text NOT NULL,
  `sent_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  KEY `key2_idx` (`sender_id`),
  KEY `key_idx` (`user_id`),
  KEY `user_idx` (`user_read`,`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=362 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_admindolog` */

DROP TABLE IF EXISTS `s_admindolog`;

CREATE TABLE `s_admindolog` (
  `main_id` varchar(32) NOT NULL DEFAULT '',
  `owner_id` int(1) NOT NULL,
  `string_info` text NOT NULL,
  `about_id` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_adminlog` */

DROP TABLE IF EXISTS `s_adminlog`;

CREATE TABLE `s_adminlog` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `login_ip` varchar(15) NOT NULL DEFAULT '',
  `login_name` varchar(63) NOT NULL DEFAULT '',
  `login_pass` varchar(99) NOT NULL DEFAULT '',
  `l_type` enum('0','1','2','B') NOT NULL DEFAULT '0',
  `login_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  UNIQUE KEY `login_ip` (`login_ip`)
) ENGINE=MyISAM AUTO_INCREMENT=157 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_advisit` */

DROP TABLE IF EXISTS `s_advisit`;

CREATE TABLE `s_advisit` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(1) NOT NULL DEFAULT '0',
  `sess_id` varchar(32) NOT NULL DEFAULT '',
  `sess_ip` varchar(20) NOT NULL DEFAULT '',
  `date_snl` date NOT NULL DEFAULT '0000-00-00',
  `date_cnt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  UNIQUE KEY `unique_id_2` (`sess_id`,`ad_id`,`sess_ip`),
  KEY `unique_id` (`ad_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4245 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_ban` */

DROP TABLE IF EXISTS `s_ban`;

CREATE TABLE `s_ban` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `ban_ip` varchar(20) NOT NULL DEFAULT '',
  `ban_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ban_reason` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`main_id`),
  UNIQUE KEY `ban_ip` (`ban_ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `s_calcount` */

DROP TABLE IF EXISTS `s_calcount`;

CREATE TABLE `s_calcount` (
  `day_cnt` date NOT NULL DEFAULT '0000-00-00',
  `cnt` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`day_cnt`),
  KEY `cnt` (`cnt`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_calendar` */

DROP TABLE IF EXISTS `s_calendar`;

CREATE TABLE `s_calendar` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(1) NOT NULL,
  `did_cmt` text NOT NULL,
  `html_cmt` enum('0','1') NOT NULL DEFAULT '0',
  `day_cnt` date NOT NULL DEFAULT '0000-00-00',
  `date_cnt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status_id` enum('0','1','2') NOT NULL DEFAULT '1',
  `view_id` enum('0','1','2') NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  KEY `day_cnt` (`day_cnt`,`status_id`),
  KEY `view_id` (`view_id`)
) ENGINE=MyISAM AUTO_INCREMENT=64538 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_calread` */

DROP TABLE IF EXISTS `s_calread`;

CREATE TABLE `s_calread` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `todo_id` date NOT NULL DEFAULT '0000-00-00',
  `user_id` varchar(32) NOT NULL DEFAULT '',
  `read_cnt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  UNIQUE KEY `todo_id` (`todo_id`,`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=64321 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_changes` */

DROP TABLE IF EXISTS `s_changes`;

CREATE TABLE `s_changes` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(1) NOT NULL,
  `c_type` enum('c','t') NOT NULL DEFAULT 'c',
  `c_done` enum('0','1') NOT NULL DEFAULT '0',
  `chg_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `chg_text` text NOT NULL,
  `chg_all` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`main_id`)
) ENGINE=MyISAM AUTO_INCREMENT=446 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_contribute` */

DROP TABLE IF EXISTS `s_contribute`;

CREATE TABLE `s_contribute` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `con_msg` text NOT NULL,
  `city_id` enum('100','110','120','130','140','150','160','170') NOT NULL DEFAULT '100',
  `con_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `con_user` int(1) NOT NULL,
  `status_id` enum('0','1','2') NOT NULL DEFAULT '0',
  `con_onday` date NOT NULL,
  PRIMARY KEY (`main_id`),
  KEY `con_onday` (`con_onday`,`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_editorial` */

DROP TABLE IF EXISTS `s_editorial`;

CREATE TABLE `s_editorial` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_title` varchar(255) NOT NULL,
  `ad_cmt` text NOT NULL,
  `ad_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status_id` enum('1','2') NOT NULL DEFAULT '2',
  PRIMARY KEY (`main_id`),
  KEY `status_id` (`status_id`,`ad_date`)
) ENGINE=MyISAM AUTO_INCREMENT=390 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_extra` */

DROP TABLE IF EXISTS `s_extra`;

CREATE TABLE `s_extra` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_name` varchar(255) NOT NULL DEFAULT '',
  `ad_pos` enum('0','1','2','3') NOT NULL DEFAULT '0',
  `ad_img` varchar(255) NOT NULL DEFAULT '',
  `ad_head` varchar(255) NOT NULL DEFAULT '',
  `ad_cell` varchar(255) NOT NULL DEFAULT '',
  `ad_email` varchar(255) NOT NULL DEFAULT '',
  `ad_extra` varchar(255) NOT NULL DEFAULT '',
  `ad_cmt` text NOT NULL,
  `status_id` enum('1','2') NOT NULL DEFAULT '2',
  `order_id` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  KEY `status_id` (`status_id`,`ad_pos`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_f` */

DROP TABLE IF EXISTS `s_f`;

CREATE TABLE `s_f` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(5) NOT NULL DEFAULT '0',
  `subject_id` enum('1','2','3') NOT NULL DEFAULT '1',
  `top_id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `sender_id` int(1) NOT NULL,
  `status_id` enum('1','2') NOT NULL DEFAULT '1',
  `sent_ttl` varchar(255) NOT NULL DEFAULT '',
  `sent_cmt` text NOT NULL,
  `sent_html` enum('0','1') NOT NULL DEFAULT '0',
  `sent_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `change_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `view_id` enum('1','2') NOT NULL DEFAULT '1',
  `check_id` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  UNIQUE KEY `status_idx` (`status_id`,`main_id`),
  KEY `sender_idx` (`sender_id`,`status_id`,`main_id`,`view_id`),
  KEY `check_id` (`check_id`),
  KEY `parent_id` (`parent_id`),
  KEY `topic_idx` (`topic_id`,`parent_id`,`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_faq` */

DROP TABLE IF EXISTS `s_faq`;

CREATE TABLE `s_faq` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `item_type` enum('F','U') NOT NULL DEFAULT 'F',
  `order_id` int(1) NOT NULL DEFAULT '0',
  `item_q` text NOT NULL,
  `item_a` text NOT NULL,
  PRIMARY KEY (`main_id`),
  KEY `item_type` (`item_type`,`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_ftopic` */

DROP TABLE IF EXISTS `s_ftopic`;

CREATE TABLE `s_ftopic` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `main_ttl` varchar(255) NOT NULL DEFAULT '',
  `main_cmt` text NOT NULL,
  `status_id` enum('1','2') NOT NULL DEFAULT '1',
  `order_id` int(3) NOT NULL DEFAULT '555',
  `subjects` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`main_id`,`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_log` */

DROP TABLE IF EXISTS `s_log`;

CREATE TABLE `s_log` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `sess_id` varchar(32) NOT NULL DEFAULT '',
  `sess_ip` varchar(15) NOT NULL DEFAULT '',
  `category_id` varchar(150) NOT NULL DEFAULT '',
  `unique_id` varchar(32) NOT NULL DEFAULT '',
  `type_inf` varchar(255) NOT NULL DEFAULT 'S',
  `date_cnt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  KEY `type_inf` (`type_inf`),
  KEY `date_idx` (`date_cnt`),
  KEY `sess_ip` (`sess_ip`)
) ENGINE=MyISAM AUTO_INCREMENT=5830927 DEFAULT CHARSET=latin1;

/*Table structure for table `s_logfilter` */

DROP TABLE IF EXISTS `s_logfilter`;

CREATE TABLE `s_logfilter` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(32) NOT NULL DEFAULT '',
  `status_id` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`main_id`),
  UNIQUE KEY `unique_id` (`unique_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4900 DEFAULT CHARSET=latin1;

/*Table structure for table `s_loginfo` */

DROP TABLE IF EXISTS `s_loginfo`;

CREATE TABLE `s_loginfo` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `sess_id` varchar(32) NOT NULL DEFAULT '',
  `sess_ip` varchar(20) NOT NULL DEFAULT '',
  `date_snl` date NOT NULL DEFAULT '0000-00-00',
  `date_cnt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  UNIQUE KEY `sess_id` (`sess_id`,`sess_ip`,`date_snl`),
  KEY `date_idx` (`date_snl`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `s_logobject` */

DROP TABLE IF EXISTS `s_logobject`;

CREATE TABLE `s_logobject` (
  `date_cnt` date NOT NULL DEFAULT '0000-00-00',
  `data_s` text NOT NULL,
  PRIMARY KEY (`date_cnt`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `s_logobject_old` */

DROP TABLE IF EXISTS `s_logobject_old`;

CREATE TABLE `s_logobject_old` (
  `date_cnt` date NOT NULL DEFAULT '0000-00-00',
  `data_s` text NOT NULL,
  PRIMARY KEY (`date_cnt`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `s_logreferer` */

DROP TABLE IF EXISTS `s_logreferer`;

CREATE TABLE `s_logreferer` (
  `type_referer` varchar(255) NOT NULL DEFAULT '',
  `type_cnt` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`type_referer`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `s_logreferer_old` */

DROP TABLE IF EXISTS `s_logreferer_old`;

CREATE TABLE `s_logreferer_old` (
  `type_referer` varchar(255) NOT NULL DEFAULT '',
  `type_cnt` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`type_referer`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `s_logstat` */

DROP TABLE IF EXISTS `s_logstat`;

CREATE TABLE `s_logstat` (
  `date_cnt` date NOT NULL DEFAULT '0000-00-00',
  `type_inf` enum('u','t','0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23') NOT NULL DEFAULT 'u',
  `type_cnt` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`date_cnt`,`type_inf`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `s_logvisit` */

DROP TABLE IF EXISTS `s_logvisit`;

CREATE TABLE `s_logvisit` (
  `sess_id` varchar(32) NOT NULL DEFAULT '',
  `sess_ip` varchar(20) NOT NULL DEFAULT '',
  `date_snl` date NOT NULL DEFAULT '0000-00-00',
  `date_cnt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_string` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `sess_id` (`sess_id`,`sess_ip`,`date_snl`),
  KEY `date_idx` (`date_snl`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `s_news` */

DROP TABLE IF EXISTS `s_news`;

CREATE TABLE `s_news` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_level` enum('0','1','2','3','4','5') NOT NULL DEFAULT '0',
  `city_id` set('100','110','120','130','140','150','160','170') NOT NULL DEFAULT '',
  `ad_pos` smallint(1) NOT NULL DEFAULT '0',
  `ad_name` varchar(255) NOT NULL DEFAULT '',
  `ad_img` text NOT NULL,
  `ad_cmt` text NOT NULL,
  `ad_id` varchar(32) NOT NULL DEFAULT '',
  `ad_url` varchar(255) NOT NULL DEFAULT '',
  `ad_hidden` enum('0','1') NOT NULL DEFAULT '0',
  `ad_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ad_stop` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status_id` enum('1','2') NOT NULL DEFAULT '2',
  `ad_type` enum('pic','swf','event') NOT NULL DEFAULT 'pic',
  PRIMARY KEY (`main_id`),
  KEY `status_id` (`ad_pos`,`status_id`,`ad_level`,`city_id`)
) ENGINE=MyISAM AUTO_INCREMENT=265 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_newsevent` */

DROP TABLE IF EXISTS `s_newsevent`;

CREATE TABLE `s_newsevent` (
  `main_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `e_id` int(1) NOT NULL DEFAULT '0',
  `e_user` varchar(32) NOT NULL DEFAULT '',
  `e_name` varchar(255) NOT NULL DEFAULT '',
  `e_cell` varchar(255) NOT NULL DEFAULT '',
  `e_email` varchar(255) NOT NULL DEFAULT '',
  `e_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  KEY `status_id` (`e_id`)
) ENGINE=MyISAM AUTO_INCREMENT=167 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_newsnotice` */

DROP TABLE IF EXISTS `s_newsnotice`;

CREATE TABLE `s_newsnotice` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_cmt` text NOT NULL,
  `city_id` enum('100','110','120','130','140','150','160','170') NOT NULL DEFAULT '100',
  `ad_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status_id` enum('1','2') NOT NULL DEFAULT '2',
  PRIMARY KEY (`main_id`),
  KEY `status_id` (`status_id`,`ad_date`)
) ENGINE=MyISAM AUTO_INCREMENT=389 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_obj` */

DROP TABLE IF EXISTS `s_obj`;

CREATE TABLE `s_obj` (
  `main_id` bigint(1) NOT NULL AUTO_INCREMENT,
  `content_type` varchar(32) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `content_more` text NOT NULL,
  `owner_id` int(1) NOT NULL,
  `sender_id` int(1) NOT NULL,
  `state_id` bigint(1) NOT NULL DEFAULT '0',
  `section_id` enum('0','1','2','3','4','5','6','7','8','9') NOT NULL DEFAULT '0',
  `obj_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  KEY `content_type` (`content_type`,`owner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=250495 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_objrel` */

DROP TABLE IF EXISTS `s_objrel`;

CREATE TABLE `s_objrel` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `content_type` varchar(50) NOT NULL DEFAULT '',
  `owner_id` int(1) NOT NULL,
  `object_id` int(1) NOT NULL DEFAULT '0',
  `obj_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  KEY `content_type` (`owner_id`,`content_type`)
) ENGINE=MyISAM AUTO_INCREMENT=250496 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_poll` */

DROP TABLE IF EXISTS `s_poll`;

CREATE TABLE `s_poll` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_user` varchar(32) NOT NULL,
  `poll_quest` varchar(255) NOT NULL DEFAULT '',
  `poll_text` text NOT NULL,
  `poll_month` varchar(7) NOT NULL DEFAULT '0000-00',
  `poll_ans1` varchar(255) NOT NULL DEFAULT '',
  `poll_res1` int(1) NOT NULL DEFAULT '0',
  `poll_ans2` varchar(255) NOT NULL DEFAULT '',
  `poll_res2` int(1) NOT NULL DEFAULT '0',
  `poll_ans3` varchar(255) NOT NULL DEFAULT '',
  `poll_res3` int(1) NOT NULL DEFAULT '0',
  `poll_ans4` varchar(255) NOT NULL DEFAULT '',
  `poll_res4` int(1) NOT NULL DEFAULT '0',
  `poll_ans5` varchar(255) NOT NULL DEFAULT '',
  `poll_res5` int(1) NOT NULL DEFAULT '0',
  `poll_ans6` varchar(255) NOT NULL DEFAULT '',
  `poll_res6` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  KEY `poll_month` (`poll_month`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_pollcmt` */

DROP TABLE IF EXISTS `s_pollcmt`;

CREATE TABLE `s_pollcmt` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `poll_id` int(1) NOT NULL,
  `logged_in` int(1) NOT NULL,
  `gb_msg` text NOT NULL,
  `gb_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  KEY `status_id` (`poll_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3174 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_pollvisit` */

DROP TABLE IF EXISTS `s_pollvisit`;

CREATE TABLE `s_pollvisit` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `sess_id` varchar(32) NOT NULL DEFAULT '',
  `sess_ip` varchar(20) NOT NULL DEFAULT '',
  `category_id` varchar(5) NOT NULL DEFAULT '',
  `unique_id` varchar(5) NOT NULL DEFAULT '',
  `date_snl` date NOT NULL DEFAULT '0000-00-00',
  `date_cnt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_enabled` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`main_id`),
  UNIQUE KEY `sess_id` (`sess_ip`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_psms2` */

DROP TABLE IF EXISTS `s_psms2`;

CREATE TABLE `s_psms2` (
  `main_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `pic_id` int(11) NOT NULL DEFAULT '0',
  `sess_id` varchar(32) NOT NULL DEFAULT '',
  `sess_ip` varchar(20) NOT NULL DEFAULT '',
  `s_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_pst` */

DROP TABLE IF EXISTS `s_pst`;

CREATE TABLE `s_pst` (
  `st_pst` int(5) NOT NULL DEFAULT '0',
  `st_ort` varchar(60) NOT NULL DEFAULT '',
  `st_kommun` varchar(100) NOT NULL DEFAULT '',
  `st_lan` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`st_pst`),
  KEY `st_lan_idx` (`st_lan`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_pst_item` */

DROP TABLE IF EXISTS `s_pst_item`;

CREATE TABLE `s_pst_item` (
  `main_id` smallint(1) NOT NULL AUTO_INCREMENT,
  `st_item` varchar(60) NOT NULL DEFAULT '',
  `st_type` enum('I','G') NOT NULL DEFAULT 'I',
  `st_group` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  KEY `st_group` (`st_group`)
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_pstlan` */

DROP TABLE IF EXISTS `s_pstlan`;

CREATE TABLE `s_pstlan` (
  `main_id` smallint(1) NOT NULL AUTO_INCREMENT,
  `st_lan` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`main_id`),
  KEY `st_lan` (`st_lan`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_pstort` */

DROP TABLE IF EXISTS `s_pstort`;

CREATE TABLE `s_pstort` (
  `st_ort` varchar(100) NOT NULL DEFAULT '',
  `st_lan` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`st_ort`),
  KEY `st_lan` (`st_lan`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_sms` */

DROP TABLE IF EXISTS `s_sms`;

CREATE TABLE `s_sms` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `alias` varchar(50) NOT NULL DEFAULT '',
  `str` text NOT NULL,
  `sess_ip` varchar(30) NOT NULL DEFAULT '',
  `sess_nmb` varchar(50) NOT NULL DEFAULT '',
  `sess_prn` varchar(50) NOT NULL DEFAULT '',
  `sess_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_cnt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `data_s` text NOT NULL,
  `rate` varchar(20) NOT NULL DEFAULT '',
  `currency` varchar(10) NOT NULL DEFAULT '',
  `operator` varchar(50) NOT NULL DEFAULT '',
  `cc` varchar(10) NOT NULL DEFAULT '',
  `moid` varchar(50) NOT NULL DEFAULT '',
  `status_id` enum('0','1','2') NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  KEY `moid` (`moid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_smsin` */

DROP TABLE IF EXISTS `s_smsin`;

CREATE TABLE `s_smsin` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `str` text NOT NULL,
  `level_id` char(3) NOT NULL DEFAULT '',
  `user_id` varchar(32) NOT NULL DEFAULT '',
  `tracking_id` varchar(40) NOT NULL DEFAULT '',
  `tracking_status` varchar(20) NOT NULL DEFAULT '',
  `shortcode` varchar(20) NOT NULL DEFAULT '',
  `sess_ip` varchar(30) NOT NULL DEFAULT '',
  `sess_sender` varchar(50) NOT NULL DEFAULT '',
  `sess_id` varchar(50) NOT NULL DEFAULT '',
  `sess_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `data_s` text NOT NULL,
  `status_id` enum('0','1','2') NOT NULL DEFAULT '0',
  `country` varchar(10) NOT NULL DEFAULT '',
  `operator` varchar(20) NOT NULL DEFAULT '',
  `resultit` text NOT NULL,
  `type_id` varchar(5) NOT NULL DEFAULT 'upgr',
  `price_id` int(11) NOT NULL DEFAULT '0',
  `is_comp` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  UNIQUE KEY `sess_id` (`sess_id`),
  KEY `tracking_id` (`tracking_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3021 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_smslog` */

DROP TABLE IF EXISTS `s_smslog`;

CREATE TABLE `s_smslog` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `status_id` char(1) NOT NULL DEFAULT '',
  `post_d` text NOT NULL,
  `moid` int(1) NOT NULL DEFAULT '0',
  `cell` varchar(40) NOT NULL DEFAULT '',
  `ok_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_smsout` */

DROP TABLE IF EXISTS `s_smsout`;

CREATE TABLE `s_smsout` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(32) NOT NULL DEFAULT '',
  `sender_id` varchar(32) NOT NULL DEFAULT '',
  `user_cell` varchar(25) NOT NULL DEFAULT '',
  `sendto` enum('gb','cell') NOT NULL DEFAULT 'cell',
  `sender_msg` text NOT NULL,
  `sender_date` date NOT NULL DEFAULT '0000-00-00',
  `status_id` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`main_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_smssend` */

DROP TABLE IF EXISTS `s_smssend`;

CREATE TABLE `s_smssend` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `cell_id` varchar(32) NOT NULL DEFAULT '',
  `city_id` enum('100','110','120') NOT NULL DEFAULT '110',
  PRIMARY KEY (`main_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4181 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_smstele` */

DROP TABLE IF EXISTS `s_smstele`;

CREATE TABLE `s_smstele` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `str` text NOT NULL,
  `sess_ip` varchar(30) NOT NULL DEFAULT '',
  `sess_nmb` varchar(50) NOT NULL DEFAULT '',
  `sess_prn` varchar(50) NOT NULL DEFAULT '',
  `sess_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_cnt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `data_s` text NOT NULL,
  `rate` varchar(20) NOT NULL DEFAULT '',
  `currency` varchar(10) NOT NULL DEFAULT '',
  `ratetype` varchar(10) NOT NULL DEFAULT '',
  `status_id` enum('0','1','2') NOT NULL DEFAULT '0',
  `country` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`main_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1408 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_smstransfer` */

DROP TABLE IF EXISTS `s_smstransfer`;

CREATE TABLE `s_smstransfer` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `moid` varchar(50) NOT NULL DEFAULT '',
  `sms_id` int(1) NOT NULL DEFAULT '0',
  `user_id` varchar(32) NOT NULL DEFAULT '',
  `level` enum('3','5','6') NOT NULL DEFAULT '3',
  `date_cnt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status_id` enum('0','1','2') NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  KEY `status_id` (`status_id`),
  KEY `moid` (`moid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_text` */

DROP TABLE IF EXISTS `s_text`;

CREATE TABLE `s_text` (
  `main_id` varchar(25) NOT NULL DEFAULT '',
  `option_id` enum('0','1','2') NOT NULL DEFAULT '0',
  `text_cmt` text NOT NULL,
  `text_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status_id` enum('0','1') NOT NULL DEFAULT '0',
  `auto_line` enum('0','1') NOT NULL DEFAULT '1',
  `special_do` enum('0','1') NOT NULL DEFAULT '0',
  `special_type` enum('0','r') NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`,`option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_textsettings` */

DROP TABLE IF EXISTS `s_textsettings`;

CREATE TABLE `s_textsettings` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `text_cmt` text NOT NULL,
  `type_id` enum('r','m','civil','alcohol','attitude','children','sex','tobacco','drink','music','length') NOT NULL,
  PRIMARY KEY (`main_id`,`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=149 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_thought` */

DROP TABLE IF EXISTS `s_thought`;

CREATE TABLE `s_thought` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `status_id` enum('0','1','2') NOT NULL DEFAULT '0',
  `p_city` enum('100','110','120','130','140','150','160','170') NOT NULL DEFAULT '100',
  `view_id` enum('0','1') NOT NULL DEFAULT '0',
  `sess_id` varchar(32) NOT NULL DEFAULT '',
  `sess_ip` varchar(15) NOT NULL DEFAULT '',
  `logged_in` int(1) NOT NULL,
  `gb_name` varchar(50) NOT NULL DEFAULT '',
  `gb_email` varchar(100) NOT NULL DEFAULT '',
  `gb_msg` text NOT NULL,
  `gb_anon` enum('0','1') NOT NULL DEFAULT '0',
  `gb_html` enum('0','1') NOT NULL DEFAULT '0',
  `gb_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `answer_msg` text NOT NULL,
  `answer_id` int(1) NOT NULL,
  `answer_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_tips` text NOT NULL,
  PRIMARY KEY (`main_id`),
  KEY `view_id` (`view_id`),
  KEY `status_id` (`status_id`,`p_city`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_user` */

DROP TABLE IF EXISTS `s_user`;

CREATE TABLE `s_user` (
  `id_id` int(1) NOT NULL AUTO_INCREMENT,
  `id_id2` varchar(32) NOT NULL,
  `status_id` enum('F','1','2','3','4') NOT NULL DEFAULT 'F',
  `level_id` enum('1','2','3','4','5','6','7','8','9','10') NOT NULL DEFAULT '1',
  `level_enddate` date NOT NULL DEFAULT '0000-00-00',
  `level_pending` enum('0','1') NOT NULL DEFAULT '0',
  `level_oldlevel` enum('1','2','3','4','5','6','7','8','9','10') NOT NULL DEFAULT '1',
  `account_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastonl_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastlog_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `u_alias` varchar(20) NOT NULL DEFAULT '',
  `u_pass` varchar(15) NOT NULL DEFAULT '',
  `u_email` varchar(255) NOT NULL DEFAULT '',
  `location_id` enum('100','110','120','130','140','150','160','170') NOT NULL DEFAULT '100',
  `u_regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `u_sex` enum('0','M','F') NOT NULL DEFAULT '0',
  `u_birth` date NOT NULL DEFAULT '0000-00-00',
  `u_birth_x` varchar(4) NOT NULL DEFAULT '',
  `u_picid` char(2) NOT NULL DEFAULT '01',
  `u_picvalid` enum('0','1','2','3') NOT NULL DEFAULT '0',
  `u_picd` char(2) NOT NULL DEFAULT '01',
  `u_picdate` date NOT NULL DEFAULT '0000-00-00',
  `u_oldpic` varchar(64) NOT NULL,
  `u_pstort` varchar(120) NOT NULL DEFAULT '',
  `u_pstlan_id` smallint(1) NOT NULL DEFAULT '0',
  `u_pstlan` varchar(120) NOT NULL DEFAULT '',
  `view_id` enum('0','1') NOT NULL DEFAULT '0',
  `beta` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_id`),
  KEY `u_alias` (`u_alias`,`status_id`),
  KEY `u_email` (`u_email`),
  KEY `status_id` (`status_id`),
  KEY `account_date` (`account_date`)
) ENGINE=MyISAM AUTO_INCREMENT=194713 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userbirth` */

DROP TABLE IF EXISTS `s_userbirth`;

CREATE TABLE `s_userbirth` (
  `id_id` int(1) NOT NULL,
  `level_id` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id_id`,`level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userblock` */

DROP TABLE IF EXISTS `s_userblock`;

CREATE TABLE `s_userblock` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(1) NOT NULL,
  `friend_id` int(1) NOT NULL,
  `rel_id` enum('u','f') NOT NULL DEFAULT 'u',
  `activated_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  KEY `key_idx` (`user_id`),
  KEY `friend_idx` (`friend_id`,`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1191 DEFAULT CHARSET=latin1;

/*Table structure for table `s_userblog` */

DROP TABLE IF EXISTS `s_userblog`;

CREATE TABLE `s_userblog` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `user_id` int(1) NOT NULL,
  `status_id` enum('1','2') NOT NULL DEFAULT '1',
  `hidden_id` enum('0','1','2') NOT NULL DEFAULT '0',
  `blog_title` varchar(100) NOT NULL DEFAULT '',
  `blog_cmt` text NOT NULL,
  `blog_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `blog_idx` date NOT NULL DEFAULT '0000-00-00',
  `blog_visit` int(1) NOT NULL DEFAULT '0',
  `blog_cmts` int(1) NOT NULL DEFAULT '0',
  `view_id` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  KEY `key_idx` (`user_id`,`status_id`),
  KEY `view_id` (`view_id`),
  KEY `blog_idx` (`blog_idx`,`user_id`,`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userblogcmt` */

DROP TABLE IF EXISTS `s_userblogcmt`;

CREATE TABLE `s_userblogcmt` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `blog_id` int(1) NOT NULL DEFAULT '0',
  `user_id` int(1) NOT NULL,
  `c_msg` text NOT NULL,
  `c_html` enum('0','1') NOT NULL DEFAULT '0',
  `c_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `id_id` int(1) NOT NULL,
  `status_id` enum('0','1','2') NOT NULL DEFAULT '0',
  `private_id` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  KEY `unique_id` (`blog_id`,`status_id`),
  KEY `status_id` (`status_id`),
  KEY `user_id` (`user_id`,`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userbloglink` */

DROP TABLE IF EXISTS `s_userbloglink`;

CREATE TABLE `s_userbloglink` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `diary_id` int(1) NOT NULL DEFAULT '0',
  `photo_id` int(1) NOT NULL DEFAULT '0',
  `status_id` enum('1','2') NOT NULL DEFAULT '1',
  PRIMARY KEY (`main_id`),
  KEY `key_idx` (`diary_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `s_userblogspy` */

DROP TABLE IF EXISTS `s_userblogspy`;

CREATE TABLE `s_userblogspy` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(32) NOT NULL DEFAULT '',
  `blogger_id` varchar(32) NOT NULL DEFAULT '',
  `status_id` enum('1','2') NOT NULL DEFAULT '1',
  PRIMARY KEY (`main_id`),
  KEY `key_idx` (`user_id`,`status_id`),
  KEY `blogger_id` (`blogger_id`,`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=323 DEFAULT CHARSET=latin1;

/*Table structure for table `s_userblogvisit` */

DROP TABLE IF EXISTS `s_userblogvisit`;

CREATE TABLE `s_userblogvisit` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `visitor_id` int(1) NOT NULL,
  `blog_id` int(1) NOT NULL DEFAULT '0',
  `status_id` enum('1','2') NOT NULL DEFAULT '1',
  `visit_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  UNIQUE KEY `visitor_id` (`visitor_id`,`blog_id`),
  KEY `blog_id` (`blog_id`,`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userchat` */

DROP TABLE IF EXISTS `s_userchat`;

CREATE TABLE `s_userchat` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(1) NOT NULL,
  `sender_id` int(1) NOT NULL,
  `user_read` enum('0','1') NOT NULL DEFAULT '0',
  `sent_cmt` text NOT NULL,
  `sent_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  KEY `user_read` (`user_read`,`user_id`),
  KEY `user_id` (`user_id`,`sender_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_usergb` */

DROP TABLE IF EXISTS `s_usergb`;

CREATE TABLE `s_usergb` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(1) NOT NULL,
  `sender_id` int(1) NOT NULL,
  `is_answered` enum('0','1') NOT NULL DEFAULT '0',
  `status_id` enum('1','2') NOT NULL DEFAULT '1',
  `private_id` enum('0','1') NOT NULL DEFAULT '0',
  `deleted_id` int(1) NOT NULL,
  `user_read` enum('0','1') NOT NULL DEFAULT '0',
  `sent_cmt` text NOT NULL,
  `sent_html` enum('0','1') NOT NULL DEFAULT '0',
  `sent_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `extra_info` varchar(12) NOT NULL DEFAULT '',
  PRIMARY KEY (`main_id`),
  KEY `sender_idx` (`sender_id`,`status_id`),
  KEY `user_id` (`user_id`,`status_id`),
  KEY `status_id` (`status_id`,`user_id`,`sender_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

/*Table structure for table `s_usergbhistory` */

DROP TABLE IF EXISTS `s_usergbhistory`;

CREATE TABLE `s_usergbhistory` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` varchar(64) NOT NULL DEFAULT '',
  `msg_id` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  KEY `user_id` (`users_id`)
) ENGINE=MyISAM AUTO_INCREMENT=943089 DEFAULT CHARSET=latin1;

/*Table structure for table `s_userinfo` */

DROP TABLE IF EXISTS `s_userinfo`;

CREATE TABLE `s_userinfo` (
  `id_id` int(1) NOT NULL AUTO_INCREMENT,
  `id_id2` varchar(32) NOT NULL,
  `u_tempemail` varchar(255) NOT NULL DEFAULT '',
  `u_fname` varchar(255) NOT NULL DEFAULT '',
  `u_sname` varchar(255) NOT NULL DEFAULT '',
  `u_pstnr` int(1) NOT NULL DEFAULT '0',
  `u_subscr` enum('0','1') NOT NULL DEFAULT '0',
  `fake_birth` enum('0','1') NOT NULL DEFAULT '0',
  `fake_try` tinyint(3) NOT NULL DEFAULT '0',
  `u_cell` varchar(15) NOT NULL DEFAULT '',
  `u_street` varchar(255) NOT NULL DEFAULT '',
  `reg_sess` varchar(32) NOT NULL DEFAULT '',
  `reg_ip` varchar(15) NOT NULL DEFAULT '',
  `reg_code` int(1) NOT NULL,
  PRIMARY KEY (`id_id`)
) ENGINE=MyISAM AUTO_INCREMENT=194710 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userlevel` */

DROP TABLE IF EXISTS `s_userlevel`;

CREATE TABLE `s_userlevel` (
  `id_id` int(1) NOT NULL,
  `level_id` text NOT NULL,
  PRIMARY KEY (`id_id`),
  FULLTEXT KEY `level_id` (`level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userlevel_off` */

DROP TABLE IF EXISTS `s_userlevel_off`;

CREATE TABLE `s_userlevel_off` (
  `id_id` int(1) NOT NULL,
  `level_id` text NOT NULL,
  PRIMARY KEY (`id_id`),
  FULLTEXT KEY `level_id` (`level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userlevelbirth` */

DROP TABLE IF EXISTS `s_userlevelbirth`;

CREATE TABLE `s_userlevelbirth` (
  `id_id` int(1) NOT NULL,
  `level_id` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id_id`,`level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userlogin` */

DROP TABLE IF EXISTS `s_userlogin`;

CREATE TABLE `s_userlogin` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `id_id` int(1) NOT NULL,
  `sess_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  UNIQUE KEY `id_id` (`id_id`)
) ENGINE=MyISAM AUTO_INCREMENT=112 DEFAULT CHARSET=latin1;

/*Table structure for table `s_usermail` */

DROP TABLE IF EXISTS `s_usermail`;

CREATE TABLE `s_usermail` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(1) NOT NULL,
  `sender_id` int(1) NOT NULL,
  `is_answered` enum('0','1') NOT NULL DEFAULT '0',
  `sent_by` enum('0','1') NOT NULL DEFAULT '1',
  `status_id` enum('1','2') NOT NULL DEFAULT '1',
  `sender_status` enum('1','2') NOT NULL DEFAULT '1',
  `user_read` enum('0','1') NOT NULL DEFAULT '0',
  `sent_ttl` text NOT NULL,
  `sent_cmt` text NOT NULL,
  `sent_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  KEY `sender_idx` (`sender_id`,`status_id`),
  KEY `user_id` (`user_id`,`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=52769 DEFAULT CHARSET=latin1;

/*Table structure for table `s_usermailhistory` */

DROP TABLE IF EXISTS `s_usermailhistory`;

CREATE TABLE `s_usermailhistory` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` varchar(64) NOT NULL,
  `msg_id` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  KEY `user_id` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `s_usermailout` */

DROP TABLE IF EXISTS `s_usermailout`;

CREATE TABLE `s_usermailout` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(32) NOT NULL DEFAULT '',
  `sender_id` varchar(32) NOT NULL DEFAULT '',
  `status_id` enum('1','D') NOT NULL DEFAULT '1',
  `user_del` enum('0','1') NOT NULL DEFAULT '0',
  `sender_del` enum('0','1') NOT NULL DEFAULT '0',
  `user_dir` int(11) NOT NULL DEFAULT '1',
  `sender_dir` int(11) NOT NULL DEFAULT '2',
  `user_read` enum('0','1') NOT NULL DEFAULT '0',
  `sent_ttl` varchar(255) NOT NULL DEFAULT '',
  `sent_cmt` text NOT NULL,
  `sent_html` enum('0','1') NOT NULL DEFAULT '0',
  `sent_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `to_self` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  KEY `check_read` (`user_id`,`user_read`),
  KEY `sender_id` (`sender_id`,`sender_dir`,`sender_del`,`to_self`),
  KEY `user_del` (`user_del`,`user_id`,`user_dir`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `s_useronline` */

DROP TABLE IF EXISTS `s_useronline`;

CREATE TABLE `s_useronline` (
  `id_id` int(1) NOT NULL,
  `account_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `u_sex` enum('','F','M') NOT NULL,
  PRIMARY KEY (`id_id`),
  KEY `online_id` (`account_date`),
  KEY `u_sex` (`u_sex`,`account_date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userphoto` */

DROP TABLE IF EXISTS `s_userphoto`;

CREATE TABLE `s_userphoto` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `user_id` int(1) NOT NULL,
  `status_id` enum('1','2') NOT NULL DEFAULT '1',
  `hidden_id` enum('0','1','2') NOT NULL DEFAULT '0',
  `picd` char(2) NOT NULL DEFAULT '01',
  `old_filename` varchar(50) NOT NULL,
  `hidden_value` varchar(32) NOT NULL DEFAULT '',
  `pht_name` enum('gif','png','jpg','jpeg') NOT NULL DEFAULT 'jpg',
  `pht_size` int(1) NOT NULL DEFAULT '0',
  `pht_cmt` varchar(50) NOT NULL DEFAULT '',
  `pht_rate` enum('0','1') NOT NULL DEFAULT '0',
  `pht_score` float NOT NULL DEFAULT '0',
  `pht_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pht_voters` int(11) NOT NULL DEFAULT '0',
  `pht_click` int(11) NOT NULL DEFAULT '0',
  `pht_cmts` int(1) NOT NULL DEFAULT '0',
  `extra_info` varchar(255) NOT NULL DEFAULT '',
  `view_id` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  KEY `key_idx` (`user_id`,`status_id`),
  KEY `view_id` (`view_id`,`status_id`,`hidden_id`)
) ENGINE=MyISAM AUTO_INCREMENT=309519 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userphoto_previous` */

DROP TABLE IF EXISTS `s_userphoto_previous`;

CREATE TABLE `s_userphoto_previous` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `user_id` int(1) NOT NULL,
  `status_id` enum('1','2') NOT NULL DEFAULT '1',
  `hidden_id` enum('0','1','2') NOT NULL DEFAULT '0',
  `picd` char(2) NOT NULL DEFAULT '01',
  `old_filename` varchar(100) NOT NULL,
  `hidden_value` varchar(32) NOT NULL DEFAULT '',
  `pht_name` enum('gif','png','jpg','jpeg') NOT NULL DEFAULT 'jpg',
  `pht_size` int(1) NOT NULL DEFAULT '0',
  `pht_cmt` varchar(50) NOT NULL DEFAULT '',
  `pht_rate` enum('0','1') NOT NULL DEFAULT '0',
  `pht_score` float NOT NULL DEFAULT '0',
  `pht_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pht_voters` int(11) NOT NULL DEFAULT '0',
  `pht_click` int(11) NOT NULL DEFAULT '0',
  `pht_cmts` int(1) NOT NULL DEFAULT '0',
  `extra_info` varchar(255) NOT NULL DEFAULT '',
  `view_id` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  KEY `key_idx` (`user_id`,`status_id`),
  KEY `view_id` (`view_id`,`status_id`,`hidden_id`)
) ENGINE=MyISAM AUTO_INCREMENT=37786 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userphotocmt` */

DROP TABLE IF EXISTS `s_userphotocmt`;

CREATE TABLE `s_userphotocmt` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `photo_id` int(1) NOT NULL DEFAULT '0',
  `user_id` int(1) NOT NULL,
  `c_msg` text NOT NULL,
  `c_html` enum('0','1') NOT NULL DEFAULT '0',
  `c_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `private_id` enum('0','1') NOT NULL DEFAULT '0',
  `id_id` int(1) NOT NULL,
  `status_id` enum('0','1','2') NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  KEY `unique_id` (`photo_id`,`status_id`),
  KEY `status_id` (`status_id`),
  KEY `user_id` (`user_id`,`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=33599 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userphotomms` */

DROP TABLE IF EXISTS `s_userphotomms`;

CREATE TABLE `s_userphotomms` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `recieve_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `id_id` varchar(32) NOT NULL DEFAULT '',
  `recieve_sender` varchar(100) NOT NULL DEFAULT '',
  `recieve_file` varchar(20) NOT NULL DEFAULT '',
  `blocked_id` varchar(32) NOT NULL DEFAULT '',
  `view_id` enum('0','1','2') NOT NULL DEFAULT '0',
  `sess_id` varchar(32) NOT NULL DEFAULT '',
  `sess_ip` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`main_id`),
  KEY `view_id` (`view_id`),
  KEY `id_id` (`id_id`)
) ENGINE=MyISAM AUTO_INCREMENT=145 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userphotomms_limit` */

DROP TABLE IF EXISTS `s_userphotomms_limit`;

CREATE TABLE `s_userphotomms_limit` (
  `id_id` varchar(32) NOT NULL DEFAULT '',
  `last_date` date NOT NULL DEFAULT '0000-00-00',
  `last_times` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userphotosel` */

DROP TABLE IF EXISTS `s_userphotosel`;

CREATE TABLE `s_userphotosel` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(32) NOT NULL DEFAULT '',
  `status_id` enum('1','2') NOT NULL DEFAULT '1',
  `hidden_id` enum('0','1','2') NOT NULL DEFAULT '0',
  `pht_cmt` varchar(50) NOT NULL DEFAULT '',
  `p_id` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  KEY `key_idx` (`user_id`,`status_id`),
  KEY `p_id` (`p_id`,`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10987 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userphotovisit` */

DROP TABLE IF EXISTS `s_userphotovisit`;

CREATE TABLE `s_userphotovisit` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(1) NOT NULL,
  `visitor_id` int(1) NOT NULL,
  `visitor_obj` int(1) NOT NULL DEFAULT '0',
  `status_id` enum('1','2') NOT NULL DEFAULT '1',
  `visit_item` int(1) NOT NULL DEFAULT '1',
  `visit_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  UNIQUE KEY `user_id` (`visitor_id`,`visitor_obj`),
  KEY `user_id_2` (`visitor_obj`,`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userphotovote` */

DROP TABLE IF EXISTS `s_userphotovote`;

CREATE TABLE `s_userphotovote` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `photo_id` int(1) NOT NULL DEFAULT '0',
  `user_id` varchar(32) NOT NULL DEFAULT '',
  `rnd` int(1) NOT NULL DEFAULT '0',
  `voter_id` varchar(32) NOT NULL DEFAULT '',
  `vote_id` int(1) NOT NULL DEFAULT '0',
  `vote_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  UNIQUE KEY `photo_id` (`voter_id`,`photo_id`),
  KEY `photo_id_2` (`photo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userpicvalid` */

DROP TABLE IF EXISTS `s_userpicvalid`;

CREATE TABLE `s_userpicvalid` (
  `id_id` int(1) NOT NULL,
  `flow_id` varchar(32) NOT NULL DEFAULT '',
  `key_id` varchar(32) NOT NULL,
  `status_id` enum('1','2','3') NOT NULL DEFAULT '1',
  `img_id` varchar(6) NOT NULL,
  PRIMARY KEY (`id_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userposition` */

DROP TABLE IF EXISTS `s_userposition`;

CREATE TABLE `s_userposition` (
  `id_id` varchar(32) NOT NULL DEFAULT '',
  `level_id` enum('5','6','7','kar','dj','8','9','10') NOT NULL DEFAULT '5',
  `city_id` enum('100','110','120','130','140','150','160','170') NOT NULL DEFAULT '100',
  `u_name` varchar(50) NOT NULL DEFAULT '',
  `u_position` varchar(255) NOT NULL DEFAULT '',
  `u_text` text NOT NULL,
  `u_text2` text NOT NULL,
  `u_text3` text NOT NULL,
  `u_textupd` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `order_id` int(1) NOT NULL DEFAULT '0',
  `img_str` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_id`,`level_id`,`city_id`),
  KEY `order_id` (`order_id`,`city_id`,`level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userregfast` */

DROP TABLE IF EXISTS `s_userregfast`;

CREATE TABLE `s_userregfast` (
  `id_id` varchar(32) NOT NULL DEFAULT '',
  `u_email` varchar(100) NOT NULL DEFAULT '',
  `activate_code` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_id`),
  KEY `u_email` (`u_email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userrel` */

DROP TABLE IF EXISTS `s_userrel`;

CREATE TABLE `s_userrel` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(32) NOT NULL DEFAULT '',
  `friend_id` varchar(32) NOT NULL DEFAULT '',
  `rel_id` varchar(50) NOT NULL DEFAULT 'VÃ¤n',
  `activated_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  KEY `key_idx` (`user_id`),
  KEY `friend_idx` (`friend_id`,`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=58443 DEFAULT CHARSET=latin1;

/*Table structure for table `s_userrelation` */

DROP TABLE IF EXISTS `s_userrelation`;

CREATE TABLE `s_userrelation` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(1) NOT NULL,
  `friend_id` int(1) NOT NULL,
  `rel_id` varchar(50) NOT NULL DEFAULT 'VÃ¤n',
  `activated_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  KEY `key_idx` (`user_id`),
  KEY `friend_idx` (`friend_id`,`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=58329 DEFAULT CHARSET=latin1;

/*Table structure for table `s_userrelquest` */

DROP TABLE IF EXISTS `s_userrelquest`;

CREATE TABLE `s_userrelquest` (
  `main_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(1) NOT NULL,
  `sender_id` int(1) NOT NULL,
  `status_id` enum('0','1','2','E','D') NOT NULL DEFAULT '0',
  `deleted_id` int(1) NOT NULL,
  `sent_cmt` varchar(25) NOT NULL DEFAULT 'VÃ¤n',
  `sent_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  KEY `user_id` (`user_id`),
  KEY `sender_id` (`sender_id`),
  KEY `key_idx` (`status_id`,`sender_id`,`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=32223 DEFAULT CHARSET=latin1;

/*Table structure for table `s_usersearch` */

DROP TABLE IF EXISTS `s_usersearch`;

CREATE TABLE `s_usersearch` (
  `id_id` varchar(32) NOT NULL DEFAULT '',
  `search_field` text NOT NULL,
  PRIMARY KEY (`id_id`),
  FULLTEXT KEY `search_field` (`search_field`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_usersess` */

DROP TABLE IF EXISTS `s_usersess`;

CREATE TABLE `s_usersess` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `id_id` int(1) NOT NULL,
  `unique_id` varchar(32) NOT NULL DEFAULT '',
  `sess_id` varchar(32) NOT NULL DEFAULT '',
  `sess_ip` varchar(15) NOT NULL DEFAULT '',
  `sess_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type_inf` enum('i','o','f') NOT NULL DEFAULT 'i',
  PRIMARY KEY (`main_id`),
  KEY `id_id` (`id_id`)
) ENGINE=MyISAM AUTO_INCREMENT=972935 DEFAULT CHARSET=latin1;

/*Table structure for table `s_userspy` */

DROP TABLE IF EXISTS `s_userspy`;

CREATE TABLE `s_userspy` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(32) NOT NULL DEFAULT '',
  `status_id` enum('1','2','3') NOT NULL DEFAULT '1',
  `spy_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `msg_id` text NOT NULL,
  `link_id` varchar(255) NOT NULL DEFAULT '',
  `object_id` varchar(32) NOT NULL DEFAULT '0',
  `type_id` char(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`main_id`),
  KEY `object_id` (`object_id`),
  KEY `user_id` (`user_id`,`status_id`,`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1196085 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userspycheck` */

DROP TABLE IF EXISTS `s_userspycheck`;

CREATE TABLE `s_userspycheck` (
  `id_id` int(1) NOT NULL,
  `level_id` text NOT NULL,
  PRIMARY KEY (`id_id`),
  FULLTEXT KEY `level_id` (`level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userupgrade` */

DROP TABLE IF EXISTS `s_userupgrade`;

CREATE TABLE `s_userupgrade` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `id_id` varchar(32) NOT NULL DEFAULT '',
  `upgrade_level` enum('3','5','6','0') NOT NULL DEFAULT '3',
  `order_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status_id` enum('0','1') NOT NULL DEFAULT '0',
  `download_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20348 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userupgradeaccount` */

DROP TABLE IF EXISTS `s_userupgradeaccount`;

CREATE TABLE `s_userupgradeaccount` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(32) NOT NULL DEFAULT '',
  `status_id` enum('1','2') NOT NULL DEFAULT '1',
  `trans_msg` varchar(255) NOT NULL DEFAULT '',
  `trans_cost` float NOT NULL DEFAULT '0',
  `trans_now` float NOT NULL DEFAULT '0',
  `trans_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_userupgradelog` */

DROP TABLE IF EXISTS `s_userupgradelog`;

CREATE TABLE `s_userupgradelog` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `id_id` varchar(32) NOT NULL DEFAULT '',
  `msg` varchar(255) NOT NULL DEFAULT '',
  `upgrade_level` enum('3','5','6') NOT NULL DEFAULT '3',
  `upgrade_type` enum('tele','sms','bm') NOT NULL DEFAULT 'tele',
  `order_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `order_id` int(1) NOT NULL DEFAULT '0',
  `status_id` enum('0','1','2') NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_id`),
  KEY `id_id` (`id_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1507 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_uservalid_off` */

DROP TABLE IF EXISTS `s_uservalid_off`;

CREATE TABLE `s_uservalid_off` (
  `id_id` int(1) NOT NULL,
  `status_id` enum('M','F','0','2') NOT NULL DEFAULT '2',
  PRIMARY KEY (`id_id`),
  KEY `status_id` (`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*Table structure for table `s_uservisit` */

DROP TABLE IF EXISTS `s_uservisit`;

CREATE TABLE `s_uservisit` (
  `main_id` int(1) NOT NULL AUTO_INCREMENT,
  `user_id` int(1) NOT NULL,
  `visitor_id` int(1) NOT NULL,
  `status_id` enum('1','2') NOT NULL DEFAULT '1',
  `visit_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`main_id`),
  UNIQUE KEY `visitor_id` (`visitor_id`,`user_id`),
  KEY `key_idx` (`user_id`,`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=384 DEFAULT CHARSET=latin1 PACK_KEYS=0;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
