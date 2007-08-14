CREATE TABLE `a_cachetest` (
  `id` varchar(255) NOT NULL default '',
  `id_id` varchar(32) NOT NULL default '',
  `last_used_by` varchar(32) NOT NULL default '',
  `times_used` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `aaa_test` (
  `main_id` bigint(1) NOT NULL auto_increment,
  `content_type` varchar(32) NOT NULL default '',
  `content` int(11) NOT NULL default '0',
  `content_more` text NOT NULL,
  `owner_id` varchar(50) NOT NULL default '',
  `sender_id` varchar(32) NOT NULL default '',
  `state_id` bigint(1) NOT NULL default '0',
  `section_id` enum('0','1','2','3','4','5','6','7','8','9') NOT NULL default '0',
  `obj_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  KEY `content_type` (`content_type`,`owner_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_aadata` (
  `main_id` int(1) NOT NULL auto_increment,
  `data_s` text NOT NULL,
  PRIMARY KEY  (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_aalog` (
  `main_id` int(1) NOT NULL auto_increment,
  `data_s` text NOT NULL,
  PRIMARY KEY  (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_ad` (
  `main_id` int(1) NOT NULL auto_increment,
  `city_id` set('100','110','120','130','140','150','160','170','180') NOT NULL default '',
  `ad_pos` enum('326_inside_highlight','140_inside_main','140_inside_profile','468_inside_popup','468_static_top','140_static_right','326_inside_nopic','326_inside_noupgrade') NOT NULL default '326_inside_highlight',
  `ad_order` smallint(1) NOT NULL default '0',
  `ad_name` varchar(255) NOT NULL default '',
  `ad_img` text,
  `ad_id` varchar(32) NOT NULL default '',
  `ad_url` varchar(255) NOT NULL default '',
  `ad_start` datetime NOT NULL default '0000-00-00 00:00:00',
  `ad_stop` datetime NOT NULL default '0000-00-00 00:00:00',
  `ad_show` int(1) NOT NULL default '0',
  `ad_showlimit` int(11) NOT NULL default '0',
  `ad_clicklimit` int(11) NOT NULL default '0',
  `ad_click` int(1) NOT NULL default '0',
  `ad_tclick` int(1) NOT NULL default '0',
  `status_id` enum('1','2') NOT NULL default '2',
  `ad_type` enum('pic','swf','event') NOT NULL default 'pic',
  `ad_size_x` smallint(1) NOT NULL default '0',
  `ad_size_y` smallint(1) NOT NULL default '0',
  `ad_target` enum('_blank','commain') NOT NULL default '_blank',
  PRIMARY KEY  (`main_id`),
  KEY `ad_pos` (`status_id`,`ad_pos`,`ad_order`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_admin` (
  `main_id` int(1) NOT NULL auto_increment,
  `user_user` varchar(20) NOT NULL default '',
  `user_name` varchar(20) NOT NULL default '',
  `user_pass` varchar(32) NOT NULL default '',
  `login_good` int(11) NOT NULL default '0',
  `login_bad` int(11) NOT NULL default '0',
  `login_page` enum('gb','pics','settings','changes') NOT NULL default 'changes',
  `u_owner` int(1) NOT NULL,
  `u_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `u_crew` enum('0','1') NOT NULL default '0',
  `status_id` enum('0','1','2','Z') NOT NULL default '0',
  `city_id` set('100','110','120','130','140','150') NOT NULL default '',
  `pos_all` set('obj_tho','obj_pcm','obj_mcm','obj_party','obj_full','obj_ue','obj_pimg','obj_tele','obj_pho','obj_event','obj_sms','obj_gb','obj_mail','obj_chat','obj_blog','poll','news_notice','news_send','pics','search_s','search_ss','search_sss','stat','log') NOT NULL default '',
  `kick_now` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `user_user` (`user_user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_adminchat` (
  `main_id` int(11) NOT NULL auto_increment,
  `user_id` int(1) NOT NULL,
  `sender_id` int(1) NOT NULL,
  `user_read` enum('0','1') NOT NULL default '0',
  `sent_cmt` text NOT NULL,
  `sent_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  KEY `key2_idx` (`sender_id`),
  KEY `key_idx` (`user_id`),
  KEY `user_idx` (`user_read`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_admindolog` (
  `main_id` varchar(32) NOT NULL default '',
  `owner_id` int(1) NOT NULL,
  `string_info` text NOT NULL,
  `about_id` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_adminlog` (
  `main_id` int(11) NOT NULL auto_increment,
  `login_ip` varchar(15) NOT NULL default '',
  `login_name` varchar(63) NOT NULL default '',
  `login_pass` varchar(99) NOT NULL default '',
  `l_type` enum('0','1','2','B') NOT NULL default '0',
  `login_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `login_ip` (`login_ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_advisit` (
  `main_id` int(11) NOT NULL auto_increment,
  `ad_id` int(1) NOT NULL default '0',
  `sess_id` varchar(32) NOT NULL default '',
  `sess_ip` varchar(20) NOT NULL default '',
  `date_snl` date NOT NULL default '0000-00-00',
  `date_cnt` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `unique_id_2` (`sess_id`,`ad_id`,`sess_ip`),
  KEY `unique_id` (`ad_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_ban` (
  `main_id` int(11) NOT NULL auto_increment,
  `ban_ip` varchar(20) NOT NULL default '',
  `ban_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `ban_reason` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `ban_ip` (`ban_ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_calcount` (
  `day_cnt` date NOT NULL default '0000-00-00',
  `cnt` int(11) NOT NULL default '0',
  PRIMARY KEY  (`day_cnt`),
  KEY `cnt` (`cnt`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_calendar` (
  `main_id` int(11) NOT NULL auto_increment,
  `user_id` int(1) NOT NULL,
  `did_cmt` text NOT NULL,
  `html_cmt` enum('0','1') NOT NULL default '0',
  `day_cnt` date NOT NULL default '0000-00-00',
  `date_cnt` datetime NOT NULL default '0000-00-00 00:00:00',
  `status_id` enum('0','1','2') NOT NULL default '1',
  `view_id` enum('0','1','2') NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  KEY `day_cnt` (`day_cnt`,`status_id`),
  KEY `view_id` (`view_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_calread` (
  `main_id` int(11) NOT NULL auto_increment,
  `todo_id` date NOT NULL default '0000-00-00',
  `user_id` varchar(32) NOT NULL default '',
  `read_cnt` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `todo_id` (`todo_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_changes` (
  `main_id` int(11) NOT NULL auto_increment,
  `user_id` int(1) NOT NULL,
  `c_type` enum('c','t') NOT NULL default 'c',
  `c_done` enum('0','1') NOT NULL default '0',
  `chg_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `chg_text` text NOT NULL,
  `chg_all` enum('1','0') NOT NULL default '1',
  PRIMARY KEY  (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_contribute` (
  `main_id` int(1) NOT NULL auto_increment,
  `con_msg` text NOT NULL,
  `city_id` enum('100','110','120','130','140','150','160','170') NOT NULL default '100',
  `con_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `con_user` int(1) NOT NULL,
  `status_id` enum('0','1','2') NOT NULL default '0',
  `con_onday` date NOT NULL,
  PRIMARY KEY  (`main_id`),
  KEY `con_onday` (`con_onday`,`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_editorial` (
  `main_id` int(11) NOT NULL auto_increment,
  `ad_title` varchar(255) NOT NULL,
  `ad_cmt` text NOT NULL,
  `ad_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `status_id` enum('1','2') NOT NULL default '2',
  PRIMARY KEY  (`main_id`),
  KEY `status_id` (`status_id`,`ad_date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_extra` (
  `main_id` int(11) NOT NULL auto_increment,
  `ad_name` varchar(255) NOT NULL default '',
  `ad_pos` enum('0','1','2','3') NOT NULL default '0',
  `ad_img` varchar(255) NOT NULL default '',
  `ad_head` varchar(255) NOT NULL default '',
  `ad_cell` varchar(255) NOT NULL default '',
  `ad_email` varchar(255) NOT NULL default '',
  `ad_extra` varchar(255) NOT NULL default '',
  `ad_cmt` text NOT NULL,
  `status_id` enum('1','2') NOT NULL default '2',
  `order_id` int(1) NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  KEY `status_id` (`status_id`,`ad_pos`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_f` (
  `main_id` int(11) NOT NULL auto_increment,
  `topic_id` int(5) NOT NULL default '0',
  `subject_id` enum('1','2','3') NOT NULL default '1',
  `top_id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `sender_id` int(1) NOT NULL,
  `status_id` enum('1','2') NOT NULL default '1',
  `sent_ttl` varchar(255) NOT NULL default '',
  `sent_cmt` text NOT NULL,
  `sent_html` enum('0','1') NOT NULL default '0',
  `sent_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `view_id` enum('1','2') NOT NULL default '1',
  `check_id` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `status_idx` (`status_id`,`main_id`),
  KEY `sender_idx` (`sender_id`,`status_id`,`main_id`,`view_id`),
  KEY `check_id` (`check_id`),
  KEY `parent_id` (`parent_id`),
  KEY `topic_idx` (`topic_id`,`parent_id`,`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_faq` (
  `main_id` int(1) NOT NULL auto_increment,
  `item_type` enum('F','U') NOT NULL default 'F',
  `order_id` int(1) NOT NULL default '0',
  `item_q` text NOT NULL,
  `item_a` text NOT NULL,
  PRIMARY KEY  (`main_id`),
  KEY `item_type` (`item_type`,`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_ftopic` (
  `main_id` int(11) NOT NULL auto_increment,
  `main_ttl` varchar(255) NOT NULL default '',
  `main_cmt` text NOT NULL,
  `status_id` enum('1','2') NOT NULL default '1',
  `order_id` int(3) NOT NULL default '555',
  `subjects` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`main_id`,`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_log` (
  `main_id` int(11) NOT NULL auto_increment,
  `sess_id` varchar(32) NOT NULL default '',
  `sess_ip` varchar(15) NOT NULL default '',
  `category_id` varchar(150) NOT NULL default '',
  `unique_id` varchar(32) NOT NULL default '',
  `type_inf` varchar(255) NOT NULL default 'S',
  `date_cnt` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  KEY `type_inf` (`type_inf`),
  KEY `date_idx` (`date_cnt`),
  KEY `sess_ip` (`sess_ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_logfilter` (
  `main_id` int(11) NOT NULL auto_increment,
  `unique_id` varchar(32) NOT NULL default '',
  `status_id` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `unique_id` (`unique_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_loginfo` (
  `main_id` int(11) NOT NULL auto_increment,
  `sess_id` varchar(32) NOT NULL default '',
  `sess_ip` varchar(20) NOT NULL default '',
  `date_snl` date NOT NULL default '0000-00-00',
  `date_cnt` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `sess_id` (`sess_id`,`sess_ip`,`date_snl`),
  KEY `date_idx` (`date_snl`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_logobject` (
  `date_cnt` date NOT NULL default '0000-00-00',
  `data_s` text NOT NULL,
  PRIMARY KEY  (`date_cnt`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_logobject_old` (
  `date_cnt` date NOT NULL default '0000-00-00',
  `data_s` text NOT NULL,
  PRIMARY KEY  (`date_cnt`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_logreferer` (
  `type_referer` varchar(255) NOT NULL default '',
  `type_cnt` int(11) NOT NULL default '0',
  PRIMARY KEY  (`type_referer`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_logreferer_old` (
  `type_referer` varchar(255) NOT NULL default '',
  `type_cnt` int(11) NOT NULL default '0',
  PRIMARY KEY  (`type_referer`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_logstat` (
  `date_cnt` date NOT NULL default '0000-00-00',
  `type_inf` enum('u','t','0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23') NOT NULL default 'u',
  `type_cnt` int(11) NOT NULL default '0',
  PRIMARY KEY  (`date_cnt`,`type_inf`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_logvisit` (
  `sess_id` varchar(32) NOT NULL default '',
  `sess_ip` varchar(20) NOT NULL default '',
  `date_snl` date NOT NULL default '0000-00-00',
  `date_cnt` datetime NOT NULL default '0000-00-00 00:00:00',
  `user_string` varchar(255) NOT NULL default '',
  UNIQUE KEY `sess_id` (`sess_id`,`sess_ip`,`date_snl`),
  KEY `date_idx` (`date_snl`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_news` (
  `main_id` int(11) NOT NULL auto_increment,
  `ad_level` enum('0','1','2','3','4','5') NOT NULL default '0',
  `city_id` set('100','110','120','130','140','150','160','170') NOT NULL default '',
  `ad_pos` smallint(1) NOT NULL default '0',
  `ad_name` varchar(255) NOT NULL default '',
  `ad_img` text NOT NULL,
  `ad_cmt` text NOT NULL,
  `ad_id` varchar(32) NOT NULL default '',
  `ad_url` varchar(255) NOT NULL default '',
  `ad_hidden` enum('0','1') NOT NULL default '0',
  `ad_start` datetime NOT NULL default '0000-00-00 00:00:00',
  `ad_stop` datetime NOT NULL default '0000-00-00 00:00:00',
  `status_id` enum('1','2') NOT NULL default '2',
  `ad_type` enum('pic','swf','event') NOT NULL default 'pic',
  PRIMARY KEY  (`main_id`),
  KEY `status_id` (`ad_pos`,`status_id`,`ad_level`,`city_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_newsevent` (
  `main_id` smallint(5) NOT NULL auto_increment,
  `e_id` int(1) NOT NULL default '0',
  `e_user` varchar(32) NOT NULL default '',
  `e_name` varchar(255) NOT NULL default '',
  `e_cell` varchar(255) NOT NULL default '',
  `e_email` varchar(255) NOT NULL default '',
  `e_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  KEY `status_id` (`e_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_newsnotice` (
  `main_id` int(11) NOT NULL auto_increment,
  `ad_cmt` text NOT NULL,
  `city_id` enum('100','110','120','130','140','150','160','170') NOT NULL default '100',
  `ad_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `status_id` enum('1','2') NOT NULL default '2',
  PRIMARY KEY  (`main_id`),
  KEY `status_id` (`status_id`,`ad_date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_obj` (
  `main_id` bigint(1) NOT NULL auto_increment,
  `content_type` varchar(32) NOT NULL default '',
  `content` text NOT NULL,
  `content_more` text NOT NULL,
  `owner_id` int(1) NOT NULL,
  `sender_id` int(1) NOT NULL,
  `state_id` bigint(1) NOT NULL default '0',
  `section_id` enum('0','1','2','3','4','5','6','7','8','9') NOT NULL default '0',
  `obj_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  KEY `content_type` (`content_type`,`owner_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_objrel` (
  `main_id` int(11) NOT NULL auto_increment,
  `content_type` varchar(50) NOT NULL default '',
  `owner_id` int(1) NOT NULL,
  `object_id` int(1) NOT NULL default '0',
  `obj_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  KEY `content_type` (`owner_id`,`content_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_poll` (
  `main_id` int(11) NOT NULL auto_increment,
  `poll_user` varchar(32) NOT NULL,
  `poll_quest` varchar(255) NOT NULL default '',
  `poll_text` text NOT NULL,
  `poll_month` varchar(7) NOT NULL default '0000-00',
  `poll_ans1` varchar(255) NOT NULL default '',
  `poll_res1` int(1) NOT NULL default '0',
  `poll_ans2` varchar(255) NOT NULL default '',
  `poll_res2` int(1) NOT NULL default '0',
  `poll_ans3` varchar(255) NOT NULL default '',
  `poll_res3` int(1) NOT NULL default '0',
  `poll_ans4` varchar(255) NOT NULL default '',
  `poll_res4` int(1) NOT NULL default '0',
  `poll_ans5` varchar(255) NOT NULL default '',
  `poll_res5` int(1) NOT NULL default '0',
  `poll_ans6` varchar(255) NOT NULL default '',
  `poll_res6` int(1) NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  KEY `poll_month` (`poll_month`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_pollcmt` (
  `main_id` int(1) NOT NULL auto_increment,
  `poll_id` int(1) NOT NULL,
  `logged_in` int(1) NOT NULL,
  `gb_msg` text NOT NULL,
  `gb_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  KEY `status_id` (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_pollvisit` (
  `main_id` int(11) NOT NULL auto_increment,
  `sess_id` varchar(32) NOT NULL default '',
  `sess_ip` varchar(20) NOT NULL default '',
  `category_id` varchar(5) NOT NULL default '',
  `unique_id` varchar(5) NOT NULL default '',
  `date_snl` date NOT NULL default '0000-00-00',
  `date_cnt` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_enabled` enum('1','0') NOT NULL default '1',
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `sess_id` (`sess_ip`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_psms2` (
  `main_id` smallint(6) NOT NULL auto_increment,
  `pic_id` int(11) NOT NULL default '0',
  `sess_id` varchar(32) NOT NULL default '',
  `sess_ip` varchar(20) NOT NULL default '',
  `s_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_pst` (
  `st_pst` int(5) NOT NULL default '0',
  `st_ort` varchar(60) NOT NULL default '',
  `st_kommun` varchar(100) NOT NULL default '',
  `st_lan` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`st_pst`),
  KEY `st_lan_idx` (`st_lan`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_pst_item` (
  `main_id` smallint(1) NOT NULL auto_increment,
  `st_item` varchar(60) NOT NULL default '',
  `st_type` enum('I','G') NOT NULL default 'I',
  `st_group` smallint(1) NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  KEY `st_group` (`st_group`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_pstlan` (
  `main_id` smallint(1) NOT NULL auto_increment,
  `st_lan` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`main_id`),
  KEY `st_lan` (`st_lan`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_pstort` (
  `st_ort` varchar(100) NOT NULL default '',
  `st_lan` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`st_ort`),
  KEY `st_lan` (`st_lan`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_sms` (
  `main_id` int(1) NOT NULL auto_increment,
  `alias` varchar(50) NOT NULL default '',
  `str` text NOT NULL,
  `sess_ip` varchar(30) NOT NULL default '',
  `sess_nmb` varchar(50) NOT NULL default '',
  `sess_prn` varchar(50) NOT NULL default '',
  `sess_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_cnt` datetime NOT NULL default '0000-00-00 00:00:00',
  `data_s` text NOT NULL,
  `rate` varchar(20) NOT NULL default '',
  `currency` varchar(10) NOT NULL default '',
  `operator` varchar(50) NOT NULL default '',
  `cc` varchar(10) NOT NULL default '',
  `moid` varchar(50) NOT NULL default '',
  `status_id` enum('0','1','2') NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  KEY `moid` (`moid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_smsin` (
  `main_id` int(1) NOT NULL auto_increment,
  `str` text NOT NULL,
  `level_id` char(3) NOT NULL default '',
  `user_id` varchar(32) NOT NULL default '',
  `tracking_id` varchar(40) NOT NULL default '',
  `tracking_status` varchar(20) NOT NULL default '',
  `shortcode` varchar(20) NOT NULL default '',
  `sess_ip` varchar(30) NOT NULL default '',
  `sess_sender` varchar(50) NOT NULL default '',
  `sess_id` varchar(50) NOT NULL default '',
  `sess_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `data_s` text NOT NULL,
  `status_id` enum('0','1','2') NOT NULL default '0',
  `country` varchar(10) NOT NULL default '',
  `operator` varchar(20) NOT NULL default '',
  `resultit` text NOT NULL,
  `type_id` varchar(5) NOT NULL default 'upgr',
  `price_id` int(11) NOT NULL default '0',
  `is_comp` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `sess_id` (`sess_id`),
  KEY `tracking_id` (`tracking_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_smslog` (
  `main_id` int(1) NOT NULL auto_increment,
  `status_id` char(1) NOT NULL default '',
  `post_d` text NOT NULL,
  `moid` int(1) NOT NULL default '0',
  `cell` varchar(40) NOT NULL default '',
  `ok_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_smsout` (
  `main_id` int(1) NOT NULL auto_increment,
  `user_id` varchar(32) NOT NULL default '',
  `sender_id` varchar(32) NOT NULL default '',
  `user_cell` varchar(25) NOT NULL default '',
  `sendto` enum('gb','cell') NOT NULL default 'cell',
  `sender_msg` text NOT NULL,
  `sender_date` date NOT NULL default '0000-00-00',
  `status_id` varchar(25) NOT NULL default '',
  PRIMARY KEY  (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_smssend` (
  `main_id` int(1) NOT NULL auto_increment,
  `cell_id` varchar(32) NOT NULL default '',
  `city_id` enum('100','110','120') NOT NULL default '110',
  PRIMARY KEY  (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_smstele` (
  `main_id` int(1) NOT NULL auto_increment,
  `str` text NOT NULL,
  `sess_ip` varchar(30) NOT NULL default '',
  `sess_nmb` varchar(50) NOT NULL default '',
  `sess_prn` varchar(50) NOT NULL default '',
  `sess_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_cnt` datetime NOT NULL default '0000-00-00 00:00:00',
  `data_s` text NOT NULL,
  `rate` varchar(20) NOT NULL default '',
  `currency` varchar(10) NOT NULL default '',
  `ratetype` varchar(10) NOT NULL default '',
  `status_id` enum('0','1','2') NOT NULL default '0',
  `country` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_smstransfer` (
  `main_id` int(1) NOT NULL auto_increment,
  `moid` varchar(50) NOT NULL default '',
  `sms_id` int(1) NOT NULL default '0',
  `user_id` varchar(32) NOT NULL default '',
  `level` enum('3','5','6') NOT NULL default '3',
  `date_cnt` datetime NOT NULL default '0000-00-00 00:00:00',
  `status_id` enum('0','1','2') NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  KEY `status_id` (`status_id`),
  KEY `moid` (`moid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_text` (
  `main_id` varchar(25) NOT NULL default '',
  `option_id` enum('0','1','2') NOT NULL default '0',
  `text_cmt` text NOT NULL,
  `text_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `status_id` enum('0','1') NOT NULL default '0',
  `auto_line` enum('0','1') NOT NULL default '1',
  `special_do` enum('0','1') NOT NULL default '0',
  `special_type` enum('0','r') NOT NULL default '0',
  PRIMARY KEY  (`main_id`,`option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_textsettings` (
  `main_id` int(1) NOT NULL auto_increment,
  `text_cmt` text NOT NULL,
  `type_id` enum('r','m','civil','alcohol','attitude','children','sex','tobacco','drink','music','length','weight') NOT NULL,
  PRIMARY KEY  (`main_id`,`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_thought` (
  `main_id` int(1) NOT NULL auto_increment,
  `status_id` enum('0','1','2') NOT NULL default '0',
  `p_city` enum('100','110','120','130','140','150','160','170') NOT NULL default '100',
  `view_id` enum('0','1') NOT NULL default '0',
  `sess_id` varchar(32) NOT NULL default '',
  `sess_ip` varchar(15) NOT NULL default '',
  `logged_in` int(1) NOT NULL,
  `gb_name` varchar(50) NOT NULL default '',
  `gb_email` varchar(100) NOT NULL default '',
  `gb_msg` text NOT NULL,
  `gb_anon` enum('0','1') NOT NULL default '0',
  `gb_html` enum('0','1') NOT NULL default '0',
  `gb_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `answer_msg` text NOT NULL,
  `answer_id` int(1) NOT NULL,
  `answer_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `admin_tips` text NOT NULL,
  PRIMARY KEY  (`main_id`),
  KEY `view_id` (`view_id`),
  KEY `status_id` (`status_id`,`p_city`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_user` (
  `id_id` int(1) NOT NULL auto_increment,
  `id_id2` varchar(32) NOT NULL,
  `status_id` enum('F','1','2','3','4') NOT NULL default 'F',
  `level_id` enum('1','2','3','4','5','6','7','8','9','10') NOT NULL default '1',
  `level_enddate` date NOT NULL default '0000-00-00',
  `level_pending` enum('0','1') NOT NULL default '0',
  `level_oldlevel` enum('1','2','3','4','5','6','7','8','9','10') NOT NULL default '1',
  `account_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastonl_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastlog_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `u_alias` varchar(20) NOT NULL default '',
  `u_pass` varchar(15) NOT NULL default '',
  `u_email` varchar(255) NOT NULL default '',
  `location_id` enum('100','110','120','130','140','150','160','170') NOT NULL default '100',
  `u_regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `u_sex` enum('0','M','F') NOT NULL default '0',
  `u_birth` date NOT NULL default '0000-00-00',
  `u_birth_x` varchar(4) NOT NULL default '',
  `u_picid` char(2) NOT NULL default '01',
  `u_picvalid` enum('0','1','2','3') NOT NULL default '0',
  `u_picd` char(2) NOT NULL default '01',
  `u_picdate` date NOT NULL default '0000-00-00',
  `u_oldpic` varchar(64) NOT NULL,
  `u_pstort` varchar(120) NOT NULL default '',
  `u_pstlan_id` smallint(1) NOT NULL default '0',
  `u_pstlan` varchar(120) NOT NULL default '',
  `view_id` enum('0','1') NOT NULL default '0',
  `beta` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id_id`),
  KEY `u_alias` (`u_alias`,`status_id`),
  KEY `u_email` (`u_email`),
  KEY `status_id` (`status_id`),
  KEY `account_date` (`account_date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userabuse` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `reporterId` bigint(20) unsigned NOT NULL,
  `reportedId` bigint(20) unsigned NOT NULL,
  `msg` text,
  `timeReported` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_userbirth` (
  `id_id` int(1) NOT NULL,
  `level_id` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id_id`,`level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userblock` (
  `main_id` int(11) NOT NULL auto_increment,
  `user_id` int(1) NOT NULL,
  `friend_id` int(1) NOT NULL,
  `rel_id` enum('u','f') NOT NULL default 'u',
  `activated_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `friend_idx` (`friend_id`,`user_id`,`rel_id`),
  KEY `key_idx` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_userblog` (
  `main_id` int(1) NOT NULL auto_increment,
  `user_id` int(1) NOT NULL,
  `status_id` enum('1','2') NOT NULL default '1',
  `hidden_id` enum('0','1','2') NOT NULL default '0',
  `blog_title` varchar(100) NOT NULL default '',
  `blog_cmt` text NOT NULL,
  `blog_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `blog_idx` date NOT NULL default '0000-00-00',
  `blog_visit` int(1) NOT NULL default '0',
  `blog_cmts` int(1) NOT NULL default '0',
  `view_id` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  KEY `key_idx` (`user_id`,`status_id`),
  KEY `view_id` (`view_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userblogcmt` (
  `main_id` int(1) NOT NULL auto_increment,
  `blog_id` int(1) NOT NULL default '0',
  `user_id` int(1) NOT NULL,
  `c_msg` text NOT NULL,
  `c_html` enum('0','1') NOT NULL default '0',
  `c_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `id_id` int(1) NOT NULL,
  `status_id` enum('0','1','2') NOT NULL default '0',
  `private_id` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  KEY `unique_id` (`blog_id`,`status_id`),
  KEY `status_id` (`status_id`),
  KEY `user_id` (`user_id`,`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userbloglink` (
  `main_id` int(11) NOT NULL auto_increment,
  `diary_id` int(1) NOT NULL default '0',
  `photo_id` int(1) NOT NULL default '0',
  `status_id` enum('1','2') NOT NULL default '1',
  PRIMARY KEY  (`main_id`),
  KEY `key_idx` (`diary_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_userblogspy` (
  `main_id` int(11) NOT NULL auto_increment,
  `user_id` varchar(32) NOT NULL default '',
  `blogger_id` varchar(32) NOT NULL default '',
  `status_id` enum('1','2') NOT NULL default '1',
  PRIMARY KEY  (`main_id`),
  KEY `key_idx` (`user_id`,`status_id`),
  KEY `blogger_id` (`blogger_id`,`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_userblogvisit` (
  `main_id` int(1) NOT NULL auto_increment,
  `visitor_id` int(1) NOT NULL,
  `blog_id` int(1) NOT NULL default '0',
  `status_id` enum('1','2') NOT NULL default '1',
  `visit_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `visitor_id` (`visitor_id`,`blog_id`),
  KEY `blog_id` (`blog_id`,`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userchat` (
  `main_id` int(11) NOT NULL auto_increment,
  `user_id` int(1) NOT NULL,
  `sender_id` int(1) NOT NULL,
  `user_read` enum('0','1') NOT NULL default '0',
  `sent_cmt` text NOT NULL,
  `sent_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  KEY `user_read` (`user_read`,`user_id`),
  KEY `user_id` (`user_id`,`sender_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_usergb` (
  `main_id` bigint(11) NOT NULL auto_increment,
  `user_id` int(1) NOT NULL,
  `sender_id` int(1) NOT NULL,
  `is_answered` enum('0','1') NOT NULL default '0',
  `status_id` enum('1','2') NOT NULL default '1',
  `private_id` enum('0','1') NOT NULL default '0',
  `deleted_id` int(1) NOT NULL,
  `user_read` enum('0','1') NOT NULL default '0',
  `sent_cmt` text NOT NULL,
  `sent_html` enum('0','1') NOT NULL default '0',
  `sent_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `deleted_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `extra_info` varchar(12) NOT NULL default '',
  PRIMARY KEY  (`main_id`),
  KEY `sender_idx` (`sender_id`,`status_id`),
  KEY `user_id` (`user_id`,`status_id`),
  KEY `status_id` (`status_id`,`user_id`,`sender_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_usergbhistory` (
  `users_id` varchar(64) NOT NULL default '',
  `msg_id` bigint(1) NOT NULL default '0',
  PRIMARY KEY  (`users_id`,`msg_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_userinfo` (
  `id_id` int(1) NOT NULL auto_increment,
  `id_id2` varchar(32) NOT NULL,
  `u_tempemail` varchar(255) NOT NULL default '',
  `u_fname` varchar(255) NOT NULL default '',
  `u_sname` varchar(255) NOT NULL default '',
  `u_pstnr` int(1) NOT NULL default '0',
  `u_subscr` enum('0','1') NOT NULL default '0',
  `fake_birth` enum('0','1') NOT NULL default '0',
  `fake_try` tinyint(3) NOT NULL default '0',
  `u_cell` varchar(15) NOT NULL default '',
  `u_street` varchar(255) NOT NULL default '',
  `reg_sess` varchar(32) NOT NULL default '',
  `reg_ip` varchar(15) NOT NULL default '',
  `reg_code` int(1) NOT NULL,
  PRIMARY KEY  (`id_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userlevel` (
  `id_id` int(1) NOT NULL,
  `level_id` text NOT NULL,
  PRIMARY KEY  (`id_id`),
  FULLTEXT KEY `level_id` (`level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userlevel_off` (
  `id_id` int(1) NOT NULL,
  `level_id` text NOT NULL,
  PRIMARY KEY  (`id_id`),
  FULLTEXT KEY `level_id` (`level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userlevelbirth` (
  `id_id` int(1) NOT NULL,
  `level_id` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id_id`,`level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userlogin` (
  `main_id` int(1) NOT NULL auto_increment,
  `id_id` int(1) NOT NULL,
  `sess_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `id_id` (`id_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_usermail` (
  `main_id` bigint(11) NOT NULL auto_increment,
  `user_id` int(1) NOT NULL,
  `sender_id` int(1) NOT NULL,
  `is_answered` enum('0','1') NOT NULL default '0',
  `sent_by` enum('0','1') NOT NULL default '1',
  `status_id` enum('1','2') NOT NULL default '1',
  `sender_status` enum('1','2') NOT NULL default '1',
  `user_read` enum('0','1') NOT NULL default '0',
  `sent_ttl` text NOT NULL,
  `sent_cmt` text NOT NULL,
  `sent_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `deleted_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  KEY `sender_idx` (`sender_id`,`status_id`),
  KEY `user_id` (`user_id`,`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_usermailhistory` (
  `main_id` int(11) NOT NULL auto_increment,
  `users_id` varchar(64) NOT NULL,
  `msg_id` int(1) NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  KEY `user_id` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_usermailout` (
  `main_id` int(11) NOT NULL auto_increment,
  `user_id` varchar(32) NOT NULL default '',
  `sender_id` varchar(32) NOT NULL default '',
  `status_id` enum('1','D') NOT NULL default '1',
  `user_del` enum('0','1') NOT NULL default '0',
  `sender_del` enum('0','1') NOT NULL default '0',
  `user_dir` int(11) NOT NULL default '1',
  `sender_dir` int(11) NOT NULL default '2',
  `user_read` enum('0','1') NOT NULL default '0',
  `sent_ttl` varchar(255) NOT NULL default '',
  `sent_cmt` text NOT NULL,
  `sent_html` enum('0','1') NOT NULL default '0',
  `sent_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `deleted_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `to_self` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  KEY `check_read` (`user_id`,`user_read`),
  KEY `sender_id` (`sender_id`,`sender_dir`,`sender_del`,`to_self`),
  KEY `user_del` (`user_del`,`user_id`,`user_dir`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_useronline` (
  `id_id` int(1) NOT NULL,
  `account_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `u_sex` enum('','F','M') NOT NULL,
  PRIMARY KEY  (`id_id`),
  KEY `online_id` (`account_date`),
  KEY `u_sex` (`u_sex`,`account_date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userphoto` (
  `main_id` int(1) NOT NULL auto_increment,
  `user_id` int(1) NOT NULL,
  `status_id` enum('1','2') NOT NULL default '1',
  `hidden_id` enum('0','1','2') NOT NULL default '0',
  `picd` char(2) NOT NULL default '01',
  `old_filename` varchar(50) NOT NULL,
  `hidden_value` varchar(32) NOT NULL default '',
  `pht_name` enum('gif','png','jpg','jpeg') NOT NULL default 'jpg',
  `pht_size` int(1) NOT NULL default '0',
  `pht_cmt` varchar(50) NOT NULL default '',
  `pht_rate` enum('0','1') NOT NULL default '0',
  `pht_score` float NOT NULL default '0',
  `pht_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `pht_voters` int(11) NOT NULL default '0',
  `pht_click` int(11) NOT NULL default '0',
  `pht_cmts` int(1) NOT NULL default '0',
  `extra_info` varchar(255) NOT NULL default '',
  `view_id` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  KEY `key_idx` (`user_id`,`status_id`),
  KEY `view_id` (`view_id`,`status_id`,`hidden_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userphoto_previous` (
  `main_id` int(1) NOT NULL auto_increment,
  `user_id` int(1) NOT NULL,
  `status_id` enum('1','2') NOT NULL default '1',
  `hidden_id` enum('0','1','2') NOT NULL default '0',
  `picd` char(2) NOT NULL default '01',
  `old_filename` varchar(100) NOT NULL,
  `hidden_value` varchar(32) NOT NULL default '',
  `pht_name` enum('gif','png','jpg','jpeg') NOT NULL default 'jpg',
  `pht_size` int(1) NOT NULL default '0',
  `pht_cmt` varchar(50) NOT NULL default '',
  `pht_rate` enum('0','1') NOT NULL default '0',
  `pht_score` float NOT NULL default '0',
  `pht_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `pht_voters` int(11) NOT NULL default '0',
  `pht_click` int(11) NOT NULL default '0',
  `pht_cmts` int(1) NOT NULL default '0',
  `extra_info` varchar(255) NOT NULL default '',
  `view_id` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  KEY `key_idx` (`user_id`,`status_id`),
  KEY `view_id` (`view_id`,`status_id`,`hidden_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userphotocmt` (
  `main_id` int(1) NOT NULL auto_increment,
  `photo_id` int(1) NOT NULL default '0',
  `user_id` int(1) NOT NULL,
  `c_msg` text NOT NULL,
  `c_html` enum('0','1') NOT NULL default '0',
  `c_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `private_id` enum('0','1') NOT NULL default '0',
  `id_id` int(1) NOT NULL,
  `status_id` enum('0','1','2') NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  KEY `unique_id` (`photo_id`,`status_id`),
  KEY `status_id` (`status_id`),
  KEY `user_id` (`user_id`,`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userphotomms` (
  `main_id` int(1) NOT NULL auto_increment,
  `recieve_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `id_id` varchar(32) NOT NULL default '',
  `recieve_sender` varchar(100) NOT NULL default '',
  `recieve_file` varchar(20) NOT NULL default '',
  `blocked_id` varchar(32) NOT NULL default '',
  `view_id` enum('0','1','2') NOT NULL default '0',
  `sess_id` varchar(32) NOT NULL default '',
  `sess_ip` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`main_id`),
  KEY `view_id` (`view_id`),
  KEY `id_id` (`id_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userphotomms_limit` (
  `id_id` varchar(32) NOT NULL default '',
  `last_date` date NOT NULL default '0000-00-00',
  `last_times` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userphotosel` (
  `main_id` int(1) NOT NULL auto_increment,
  `user_id` varchar(32) NOT NULL default '',
  `status_id` enum('1','2') NOT NULL default '1',
  `hidden_id` enum('0','1','2') NOT NULL default '0',
  `pht_cmt` varchar(50) NOT NULL default '',
  `p_id` int(1) NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  KEY `key_idx` (`user_id`,`status_id`),
  KEY `p_id` (`p_id`,`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userphotovisit` (
  `main_id` int(11) NOT NULL auto_increment,
  `user_id` int(1) NOT NULL,
  `visitor_id` int(1) NOT NULL,
  `visitor_obj` int(1) NOT NULL default '0',
  `status_id` enum('1','2') NOT NULL default '1',
  `visit_item` int(1) NOT NULL default '1',
  `visit_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `user_id` (`visitor_id`,`visitor_obj`),
  KEY `user_id_2` (`visitor_obj`,`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userphotovote` (
  `main_id` int(1) NOT NULL auto_increment,
  `photo_id` int(1) NOT NULL default '0',
  `user_id` varchar(32) NOT NULL default '',
  `rnd` int(1) NOT NULL default '0',
  `voter_id` varchar(32) NOT NULL default '',
  `vote_id` int(1) NOT NULL default '0',
  `vote_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `photo_id` (`voter_id`,`photo_id`),
  KEY `photo_id_2` (`photo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userpicvalid` (
  `id_id` int(1) NOT NULL,
  `flow_id` varchar(32) NOT NULL default '',
  `key_id` varchar(32) NOT NULL,
  `status_id` enum('1','2','3') NOT NULL default '1',
  `img_id` varchar(6) NOT NULL,
  PRIMARY KEY  (`id_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userposition` (
  `id_id` varchar(32) NOT NULL default '',
  `level_id` enum('5','6','7','kar','dj','8','9','10') NOT NULL default '5',
  `city_id` enum('100','110','120','130','140','150','160','170') NOT NULL default '100',
  `u_name` varchar(50) NOT NULL default '',
  `u_position` varchar(255) NOT NULL default '',
  `u_text` text NOT NULL,
  `u_text2` text NOT NULL,
  `u_text3` text NOT NULL,
  `u_textupd` datetime NOT NULL default '0000-00-00 00:00:00',
  `order_id` int(1) NOT NULL default '0',
  `img_str` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_id`,`level_id`,`city_id`),
  KEY `order_id` (`order_id`,`city_id`,`level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userregfast` (
  `id_id` varchar(32) NOT NULL default '',
  `u_email` varchar(100) NOT NULL default '',
  `activate_code` varchar(20) NOT NULL default '',
  `timeCreated` datetime default NULL,
  PRIMARY KEY  (`id_id`),
  KEY `u_email` (`u_email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userrelation` (
  `main_id` int(11) NOT NULL auto_increment,
  `user_id` int(1) NOT NULL,
  `friend_id` int(1) NOT NULL,
  `rel_id` varchar(50) NOT NULL default 'Vän',
  `activated_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `gallx` int(1) default NULL,
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `friend_idx` (`friend_id`,`user_id`),
  KEY `key_idx` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_userrelquest` (
  `main_id` int(11) NOT NULL auto_increment,
  `user_id` int(1) NOT NULL,
  `sender_id` int(1) NOT NULL,
  `status_id` enum('0','1','2','E','D') NOT NULL default '0',
  `deleted_id` int(1) NOT NULL,
  `sent_cmt` varchar(25) NOT NULL default 'Vän',
  `sent_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `deleted_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  KEY `user_id` (`user_id`),
  KEY `sender_id` (`sender_id`),
  KEY `key_idx` (`status_id`,`sender_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_usersearch` (
  `id_id` varchar(32) NOT NULL default '',
  `search_field` text NOT NULL,
  PRIMARY KEY  (`id_id`),
  FULLTEXT KEY `search_field` (`search_field`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_usersess` (
  `main_id` int(1) NOT NULL auto_increment,
  `id_id` int(1) NOT NULL,
  `unique_id` varchar(32) NOT NULL default '',
  `sess_id` varchar(32) NOT NULL default '',
  `sess_ip` varchar(15) NOT NULL default '',
  `sess_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `type_inf` enum('i','o','f') NOT NULL default 'i',
  PRIMARY KEY  (`main_id`),
  KEY `id_id` (`id_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `s_userspy` (
  `main_id` int(1) NOT NULL auto_increment,
  `user_id` varchar(32) NOT NULL default '',
  `status_id` enum('1','2','3') NOT NULL default '1',
  `spy_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `msg_id` text NOT NULL,
  `link_id` varchar(255) NOT NULL default '',
  `object_id` varchar(32) NOT NULL default '0',
  `type_id` char(3) NOT NULL default '',
  PRIMARY KEY  (`main_id`),
  KEY `object_id` (`object_id`),
  KEY `user_id` (`user_id`,`status_id`,`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userspycheck` (
  `main_id` int(1) NOT NULL auto_increment,
  `object_id` int(1) NOT NULL,
  `type_id` enum('f','g','b') NOT NULL,
  `user_id` int(1) NOT NULL,
  PRIMARY KEY  (`main_id`),
  KEY `msg_type` (`type_id`,`object_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userupgrade` (
  `main_id` int(1) NOT NULL auto_increment,
  `id_id` varchar(32) NOT NULL default '',
  `upgrade_level` enum('3','5','6','0') NOT NULL default '3',
  `order_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `status_id` enum('0','1') NOT NULL default '0',
  `download_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userupgradeaccount` (
  `main_id` int(1) NOT NULL auto_increment,
  `user_id` varchar(32) NOT NULL default '',
  `status_id` enum('1','2') NOT NULL default '1',
  `trans_msg` varchar(255) NOT NULL default '',
  `trans_cost` float NOT NULL default '0',
  `trans_now` float NOT NULL default '0',
  `trans_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_userupgradelog` (
  `main_id` int(1) NOT NULL auto_increment,
  `id_id` varchar(32) NOT NULL default '',
  `msg` varchar(255) NOT NULL default '',
  `upgrade_level` enum('3','5','6') NOT NULL default '3',
  `upgrade_type` enum('tele','sms','bm') NOT NULL default 'tele',
  `order_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `order_id` int(1) NOT NULL default '0',
  `status_id` enum('0','1','2') NOT NULL default '0',
  PRIMARY KEY  (`main_id`),
  KEY `id_id` (`id_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_uservalid_off` (
  `id_id` int(1) NOT NULL,
  `status_id` enum('M','F','0','2') NOT NULL default '2',
  PRIMARY KEY  (`id_id`),
  KEY `status_id` (`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_uservisit` (
  `main_id` int(1) NOT NULL auto_increment,
  `user_id` int(1) NOT NULL,
  `visitor_id` int(1) NOT NULL,
  `status_id` enum('1','2') NOT NULL default '1',
  `visit_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`main_id`),
  UNIQUE KEY `visitor_id` (`visitor_id`,`user_id`),
  KEY `key_idx` (`user_id`,`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
CREATE TABLE `s_vip` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `userId` bigint(20) unsigned NOT NULL default '0',
  `level` tinyint(3) unsigned NOT NULL default '0',
  `days` int(10) unsigned NOT NULL default '0',
  `timeSet` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `tblVerifyUsers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` bigint(20) unsigned NOT NULL default '0',
  `timeAsked` datetime default NULL,
  `verified` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
