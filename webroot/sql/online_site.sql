# MySQL-Front Dump 2.5
#
# Host: localhost   Database: online_site
# --------------------------------------------------------
# Server version 3.23.56


#
# Table structure for table 'tblBugReports'
#

CREATE TABLE tblBugReports (
  bugId bigint(20) unsigned NOT NULL auto_increment,
  bugDesc blob NOT NULL,
  bugCreator bigint(20) unsigned NOT NULL default '0',
  reportMethod tinyint(3) unsigned NOT NULL default '0',
  timestamp bigint(20) unsigned NOT NULL default '0',
  bugClosed tinyint(3) unsigned NOT NULL default '0',
  bugClosedReason tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (bugId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblBugReports'
#

INSERT INTO tblBugReports VALUES("1", "Jao hautar buggelibuggaur!", "1", "0", "1037336441", "1", "0");
INSERT INTO tblBugReports VALUES("3", "Jag gillar bajs", "8", "0", "1037389948", "1", "0");
INSERT INTO tblBugReports VALUES("4", "Jag vill kunna ändra lösenord!", "8", "0", "1037390002", "1", "1");
INSERT INTO tblBugReports VALUES("5", "martins snopp är för stor", "11", "0", "1038409304", "1", "0");


#
# Table structure for table 'tblContentCodes'
#

CREATE TABLE tblContentCodes (
  code bigint(20) unsigned NOT NULL default '0',
  months tinyint(1) unsigned NOT NULL default '0',
  timestamp bigint(20) unsigned NOT NULL default '0',
  used tinyint(1) unsigned NOT NULL default '0',
  userId bigint(20) unsigned NOT NULL default '0',
  usedTimestamp bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (code)
) TYPE=MyISAM;



#
# Dumping data for table 'tblContentCodes'
#

INSERT INTO tblContentCodes VALUES("828669015086", "6", "1037416939", "1", "1", "1037429324");
INSERT INTO tblContentCodes VALUES("428060964829", "6", "1037416939", "1", "1", "1037428711");
INSERT INTO tblContentCodes VALUES("208143975252", "6", "1037416939", "1", "1", "1037429396");
INSERT INTO tblContentCodes VALUES("124933481435", "6", "1037416939", "0", "0", "0");
INSERT INTO tblContentCodes VALUES("227150278277", "6", "1037416939", "0", "0", "0");
INSERT INTO tblContentCodes VALUES("874214665796", "6", "1037416939", "0", "0", "0");
INSERT INTO tblContentCodes VALUES("464677785532", "6", "1037416939", "1", "11", "1038412127");
INSERT INTO tblContentCodes VALUES("667504911873", "6", "1037416939", "0", "0", "0");
INSERT INTO tblContentCodes VALUES("649043874953", "6", "1037416939", "0", "0", "0");
INSERT INTO tblContentCodes VALUES("65414695837", "6", "1037416939", "0", "0", "0");
INSERT INTO tblContentCodes VALUES("687878114956", "3", "1037417144", "0", "0", "0");
INSERT INTO tblContentCodes VALUES("174674389568", "3", "1037417144", "0", "0", "0");
INSERT INTO tblContentCodes VALUES("84396538578", "3", "1037417144", "0", "0", "0");
INSERT INTO tblContentCodes VALUES("817018687166", "3", "1037417144", "0", "0", "0");
INSERT INTO tblContentCodes VALUES("459330062590", "3", "1037417144", "0", "0", "0");
INSERT INTO tblContentCodes VALUES("362834080669", "3", "1037417144", "0", "0", "0");
INSERT INTO tblContentCodes VALUES("709024742107", "3", "1037417144", "1", "1", "1037466584");
INSERT INTO tblContentCodes VALUES("17992380778", "3", "1037417144", "1", "10", "1037546029");
INSERT INTO tblContentCodes VALUES("41585175261", "3", "1037417144", "0", "0", "0");
INSERT INTO tblContentCodes VALUES("18341271348", "3", "1037417144", "0", "0", "0");


#
# Table structure for table 'tblCountries'
#

CREATE TABLE tblCountries (
  countryId bigint(20) unsigned NOT NULL auto_increment,
  countryName varchar(50) NOT NULL default '',
  countrySuffix varchar(5) NOT NULL default '',
  timezoneId tinyint(3) unsigned default NULL,
  PRIMARY KEY  (countryId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblCountries'
#

INSERT INTO tblCountries VALUES("6", "Afghanistan", "af", NULL);
INSERT INTO tblCountries VALUES("7", "Albania", "", NULL);
INSERT INTO tblCountries VALUES("8", "Albania", "al", NULL);
INSERT INTO tblCountries VALUES("9", "Algeria", "dz", NULL);
INSERT INTO tblCountries VALUES("10", "Andorra", "ad", NULL);
INSERT INTO tblCountries VALUES("11", "Angola", "ao", NULL);
INSERT INTO tblCountries VALUES("12", "Antigua and Barbuda", "ag", NULL);
INSERT INTO tblCountries VALUES("13", "Argentina", "ar", NULL);
INSERT INTO tblCountries VALUES("14", "Armenia", "am", NULL);
INSERT INTO tblCountries VALUES("15", "Australia", "au", NULL);
INSERT INTO tblCountries VALUES("16", "Austria", "at", NULL);
INSERT INTO tblCountries VALUES("17", "Azerbaijan", "az", NULL);
INSERT INTO tblCountries VALUES("18", "Bahamas", "bs", NULL);
INSERT INTO tblCountries VALUES("19", "Bahrain", "bh", NULL);
INSERT INTO tblCountries VALUES("20", "Bangladesh", "bd", NULL);
INSERT INTO tblCountries VALUES("21", "Barbados", "bb", NULL);
INSERT INTO tblCountries VALUES("22", "Belarus", "by", NULL);
INSERT INTO tblCountries VALUES("23", "Belgium", "be", NULL);
INSERT INTO tblCountries VALUES("24", "Belize", "bz", NULL);
INSERT INTO tblCountries VALUES("25", "Benin", "bj", NULL);
INSERT INTO tblCountries VALUES("26", "Bhutan", "bt", NULL);
INSERT INTO tblCountries VALUES("27", "Bolivia", "bo", NULL);
INSERT INTO tblCountries VALUES("28", "Bosnia And Herzegovina", "ba", NULL);
INSERT INTO tblCountries VALUES("29", "Botswana", "bw", NULL);
INSERT INTO tblCountries VALUES("30", "Brazil", "br", NULL);
INSERT INTO tblCountries VALUES("31", "Brunei", "bn", NULL);
INSERT INTO tblCountries VALUES("32", "Bulgaria", "bg", NULL);
INSERT INTO tblCountries VALUES("33", "Burkina Faso", "bf", NULL);
INSERT INTO tblCountries VALUES("34", "Burundi", "bi", NULL);
INSERT INTO tblCountries VALUES("35", "Cambodia", "kh", NULL);
INSERT INTO tblCountries VALUES("36", "Cameroon", "cm", NULL);
INSERT INTO tblCountries VALUES("37", "Canada", "ca", NULL);
INSERT INTO tblCountries VALUES("38", "Cape Verde", "cv", NULL);
INSERT INTO tblCountries VALUES("39", "Central African Republic", "cf", NULL);
INSERT INTO tblCountries VALUES("40", "Chad", "td", NULL);
INSERT INTO tblCountries VALUES("41", "Chile", "cl", NULL);
INSERT INTO tblCountries VALUES("42", "China", "cn", NULL);
INSERT INTO tblCountries VALUES("43", "Colombia", "co", NULL);
INSERT INTO tblCountries VALUES("44", "Comoros", "km", NULL);
INSERT INTO tblCountries VALUES("45", "Congo", "cg", NULL);
INSERT INTO tblCountries VALUES("46", "Congo (DRC)", "cd", NULL);
INSERT INTO tblCountries VALUES("47", "Costa Rica", "cr", NULL);
INSERT INTO tblCountries VALUES("48", "Cote d\'Ivoire", "ci", NULL);
INSERT INTO tblCountries VALUES("49", "Croatia", "hr", NULL);
INSERT INTO tblCountries VALUES("50", "Cuba", "cu", NULL);
INSERT INTO tblCountries VALUES("51", "Cyprus", "cy", NULL);
INSERT INTO tblCountries VALUES("52", "Czech Republic", "cz", NULL);
INSERT INTO tblCountries VALUES("53", "Denmark", "dk", NULL);
INSERT INTO tblCountries VALUES("54", "Djibouti", "dj", NULL);
INSERT INTO tblCountries VALUES("55", "Dominica", "dm", NULL);
INSERT INTO tblCountries VALUES("56", "Dominican Republic", "do", NULL);
INSERT INTO tblCountries VALUES("57", "Ecuador", "ec", NULL);
INSERT INTO tblCountries VALUES("58", "Egypt", "eg", NULL);
INSERT INTO tblCountries VALUES("59", "El Salvador", "sv", NULL);
INSERT INTO tblCountries VALUES("60", "Equatorial Guinea", "gq", NULL);
INSERT INTO tblCountries VALUES("61", "Eritrea", "er", NULL);
INSERT INTO tblCountries VALUES("62", "Estonia", "ee", NULL);
INSERT INTO tblCountries VALUES("63", "Ethiopia", "et", NULL);
INSERT INTO tblCountries VALUES("64", "Fiji", "fj", NULL);
INSERT INTO tblCountries VALUES("65", "Finland", "fi", NULL);
INSERT INTO tblCountries VALUES("66", "Former Yugoslav Republic of Macedonia", "mk", NULL);
INSERT INTO tblCountries VALUES("67", "France", "fr", NULL);
INSERT INTO tblCountries VALUES("68", "Gabon", "ga", NULL);
INSERT INTO tblCountries VALUES("69", "Gambia", "gm", NULL);
INSERT INTO tblCountries VALUES("71", "Georgia", "ge", NULL);
INSERT INTO tblCountries VALUES("72", "Germany", "de", NULL);
INSERT INTO tblCountries VALUES("73", "Ghana", "gh", NULL);
INSERT INTO tblCountries VALUES("74", "Greece", "gr", NULL);
INSERT INTO tblCountries VALUES("75", "Grenada", "gd", NULL);
INSERT INTO tblCountries VALUES("76", "Guatemala", "gt", NULL);
INSERT INTO tblCountries VALUES("77", "Guinea", "gn", NULL);
INSERT INTO tblCountries VALUES("78", "Guinea-Bissau", "gw", NULL);
INSERT INTO tblCountries VALUES("79", "Guyana", "gy", NULL);
INSERT INTO tblCountries VALUES("80", "Haiti", "ht", NULL);
INSERT INTO tblCountries VALUES("81", "Honduras", "hn", NULL);
INSERT INTO tblCountries VALUES("82", "Hungary", "hu", NULL);
INSERT INTO tblCountries VALUES("83", "Iceland", "is", NULL);
INSERT INTO tblCountries VALUES("84", "India", "in", NULL);
INSERT INTO tblCountries VALUES("85", "Indonesia", "id", NULL);
INSERT INTO tblCountries VALUES("86", "Iran", "ir", NULL);
INSERT INTO tblCountries VALUES("87", "Iraq", "iq", NULL);
INSERT INTO tblCountries VALUES("88", "Ireland", "ie", NULL);
INSERT INTO tblCountries VALUES("89", "Israel", "il", NULL);
INSERT INTO tblCountries VALUES("90", "Italy", "it", NULL);
INSERT INTO tblCountries VALUES("91", "Jamaica", "jm", NULL);
INSERT INTO tblCountries VALUES("92", "Japan", "jp", NULL);
INSERT INTO tblCountries VALUES("93", "Jordan", "jo", NULL);
INSERT INTO tblCountries VALUES("94", "Kazakhstan", "kz", NULL);
INSERT INTO tblCountries VALUES("95", "Kenya", "ke", NULL);
INSERT INTO tblCountries VALUES("96", "Kiribati", "ki", NULL);
INSERT INTO tblCountries VALUES("97", "Kuwait", "kw", NULL);
INSERT INTO tblCountries VALUES("98", "Kyrgyzstan", "kg", NULL);
INSERT INTO tblCountries VALUES("99", "Laos", "", NULL);
INSERT INTO tblCountries VALUES("100", "Latvia", "lv", NULL);
INSERT INTO tblCountries VALUES("101", "Lebanon", "lb", NULL);
INSERT INTO tblCountries VALUES("102", "Lesotho", "ls", NULL);
INSERT INTO tblCountries VALUES("103", "Liberia", "lr", NULL);
INSERT INTO tblCountries VALUES("104", "Libya", "ly", NULL);
INSERT INTO tblCountries VALUES("105", "Liechtenstein", "li", NULL);
INSERT INTO tblCountries VALUES("106", "Lithuania", "lt", NULL);
INSERT INTO tblCountries VALUES("107", "Luxembourg", "lu", NULL);
INSERT INTO tblCountries VALUES("108", "Madagascar", "mg", NULL);
INSERT INTO tblCountries VALUES("109", "Malawi", "mw", NULL);
INSERT INTO tblCountries VALUES("110", "Malaysia", "my", NULL);
INSERT INTO tblCountries VALUES("111", "Maldives", "mv", NULL);
INSERT INTO tblCountries VALUES("112", "Mali", "ml", NULL);
INSERT INTO tblCountries VALUES("113", "Malta", "mt", NULL);
INSERT INTO tblCountries VALUES("114", "Marshall Islands", "mh", NULL);
INSERT INTO tblCountries VALUES("115", "Mauritania", "mr", NULL);
INSERT INTO tblCountries VALUES("116", "Mauritius", "mu", NULL);
INSERT INTO tblCountries VALUES("117", "Mexico", "mx", NULL);
INSERT INTO tblCountries VALUES("118", "Micronesia", "fm", NULL);
INSERT INTO tblCountries VALUES("119", "Moldova", "md", NULL);
INSERT INTO tblCountries VALUES("120", "Monaco", "mc", NULL);
INSERT INTO tblCountries VALUES("121", "Mongolia", "mn", NULL);
INSERT INTO tblCountries VALUES("122", "Morocco", "ma", NULL);
INSERT INTO tblCountries VALUES("123", "Mozambique", "mz", NULL);
INSERT INTO tblCountries VALUES("124", "Myanmar", "mm", NULL);
INSERT INTO tblCountries VALUES("125", "Namibia", "na", NULL);
INSERT INTO tblCountries VALUES("126", "Nauru", "nr", NULL);
INSERT INTO tblCountries VALUES("127", "Nepal", "np", NULL);
INSERT INTO tblCountries VALUES("128", "Netherlands", "nl", NULL);
INSERT INTO tblCountries VALUES("129", "New Zealand", "nz", NULL);
INSERT INTO tblCountries VALUES("130", "Nicaragua", "ni", NULL);
INSERT INTO tblCountries VALUES("131", "Niger", "ne", NULL);
INSERT INTO tblCountries VALUES("132", "Nigeria", "ng", NULL);
INSERT INTO tblCountries VALUES("133", "North Korea", "", NULL);
INSERT INTO tblCountries VALUES("134", "Norway", "no", NULL);
INSERT INTO tblCountries VALUES("135", "Oman", "om", NULL);
INSERT INTO tblCountries VALUES("136", "Pakistan", "pk", NULL);
INSERT INTO tblCountries VALUES("137", "Palau", "pw", NULL);
INSERT INTO tblCountries VALUES("138", "Panama", "pa", NULL);
INSERT INTO tblCountries VALUES("139", "Papua New Guinea", "pg", NULL);
INSERT INTO tblCountries VALUES("140", "Paraguay", "py", NULL);
INSERT INTO tblCountries VALUES("141", "Peru", "pe", NULL);
INSERT INTO tblCountries VALUES("142", "Phillippines", "ph", NULL);
INSERT INTO tblCountries VALUES("143", "Poland", "pl", NULL);
INSERT INTO tblCountries VALUES("144", "Portugal", "pt", NULL);
INSERT INTO tblCountries VALUES("145", "Qatar", "qa", NULL);
INSERT INTO tblCountries VALUES("146", "Romania", "ro", NULL);
INSERT INTO tblCountries VALUES("147", "Russia", "ru", NULL);
INSERT INTO tblCountries VALUES("148", "Rwanda", "rw", NULL);
INSERT INTO tblCountries VALUES("149", "Samoa", "", NULL);
INSERT INTO tblCountries VALUES("150", "San Marino", "sm", NULL);
INSERT INTO tblCountries VALUES("151", "Sao Tome And Principe", "st", NULL);
INSERT INTO tblCountries VALUES("152", "Saudi Arabia", "sa", NULL);
INSERT INTO tblCountries VALUES("153", "Senegal", "sn", NULL);
INSERT INTO tblCountries VALUES("154", "Seychelles", "sc", NULL);
INSERT INTO tblCountries VALUES("155", "Sierra Leone", "sl", NULL);
INSERT INTO tblCountries VALUES("156", "Singapore", "sg", NULL);
INSERT INTO tblCountries VALUES("157", "Slovakia", "sk", NULL);
INSERT INTO tblCountries VALUES("158", "Slovenia", "si", NULL);
INSERT INTO tblCountries VALUES("159", "Solomon Islands", "sb", NULL);
INSERT INTO tblCountries VALUES("160", "Somalia", "so", NULL);
INSERT INTO tblCountries VALUES("161", "South Africa", "za", NULL);
INSERT INTO tblCountries VALUES("162", "South Korea", "", NULL);
INSERT INTO tblCountries VALUES("163", "Spain", "es", NULL);
INSERT INTO tblCountries VALUES("164", "Sri Lanka", "lk", NULL);
INSERT INTO tblCountries VALUES("165", "St. Kitts And Nevis", "kn", NULL);
INSERT INTO tblCountries VALUES("166", "St. Lucia", "lc", NULL);
INSERT INTO tblCountries VALUES("167", "St. Vincent And the Grenadines", "vc", NULL);
INSERT INTO tblCountries VALUES("168", "Sudan", "sd", NULL);
INSERT INTO tblCountries VALUES("169", "Suriname", "sr", NULL);
INSERT INTO tblCountries VALUES("170", "Swaziland", "sz", NULL);
INSERT INTO tblCountries VALUES("171", "Sweden", "se", "13");
INSERT INTO tblCountries VALUES("172", "Switzerland", "ch", NULL);
INSERT INTO tblCountries VALUES("173", "Syria", "sy", NULL);
INSERT INTO tblCountries VALUES("174", "Tajikistan", "tj", NULL);
INSERT INTO tblCountries VALUES("175", "Tanzania", "tz", NULL);
INSERT INTO tblCountries VALUES("176", "Thailand", "th", NULL);
INSERT INTO tblCountries VALUES("177", "Togo", "tg", NULL);
INSERT INTO tblCountries VALUES("178", "Tonga", "to", NULL);
INSERT INTO tblCountries VALUES("179", "Trinidad and Tobago", "tt", NULL);
INSERT INTO tblCountries VALUES("180", "Tunisia", "tn", NULL);
INSERT INTO tblCountries VALUES("181", "Turkey", "tr", NULL);
INSERT INTO tblCountries VALUES("182", "Turkmenistan", "tm", NULL);
INSERT INTO tblCountries VALUES("183", "Tuvalu", "tv", NULL);
INSERT INTO tblCountries VALUES("184", "Uganda", "ug", NULL);
INSERT INTO tblCountries VALUES("185", "Ukraine", "ua", NULL);
INSERT INTO tblCountries VALUES("186", "United Arab Emirates", "ae", NULL);
INSERT INTO tblCountries VALUES("187", "United Kingdom", "uk", NULL);
INSERT INTO tblCountries VALUES("188", "United States", "us", NULL);
INSERT INTO tblCountries VALUES("189", "Uruguay", "uy", NULL);
INSERT INTO tblCountries VALUES("190", "Uzbekistan", "uz", NULL);
INSERT INTO tblCountries VALUES("191", "Vanuatu", "vu", NULL);
INSERT INTO tblCountries VALUES("192", "Vatican City", "va", NULL);
INSERT INTO tblCountries VALUES("193", "Venezuela", "ve", NULL);
INSERT INTO tblCountries VALUES("194", "Vietnam", "vn", NULL);
INSERT INTO tblCountries VALUES("195", "Yemen", "ye", NULL);
INSERT INTO tblCountries VALUES("196", "Yugoslavia", "yu", NULL);
INSERT INTO tblCountries VALUES("197", "Zambia", "zm", NULL);
INSERT INTO tblCountries VALUES("198", "Zimbabwe", "zw", NULL);


#
# Table structure for table 'tblForums'
#

CREATE TABLE tblForums (
  itemId bigint(20) unsigned NOT NULL auto_increment,
  itemType tinyint(3) unsigned NOT NULL default '0',
  authorId bigint(20) unsigned NOT NULL default '0',
  parentId bigint(20) unsigned NOT NULL default '0',
  itemSubject varchar(100) NOT NULL default '0',
  itemBody blob NOT NULL,
  fileId bigint(20) unsigned NOT NULL default '0',
  timestamp bigint(20) unsigned NOT NULL default '0',
  itemDeleted tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (itemId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblForums'
#

INSERT INTO tblForums VALUES("3", "1", "1", "0", "General chat", "Here you can talk about anything", "0", "1043182144", "0");
INSERT INTO tblForums VALUES("4", "2", "1", "3", "New discussion", "wee", "0", "1043182158", "0");
INSERT INTO tblForums VALUES("5", "2", "1", "4", "x1", "x2", "0", "1043182165", "0");
INSERT INTO tblForums VALUES("6", "2", "1", "5", "Re: x1", "> x2\r\n\r\nx3", "0", "1043182203", "0");
INSERT INTO tblForums VALUES("7", "2", "1", "6", "Re: x1", "> > x2\r\n> \r\n> x3\r\n\r\nx45", "0", "1043182246", "0");
INSERT INTO tblForums VALUES("8", "2", "1", "7", "Re: x1", "> > > x2\r\n> > \r\n> > x3\r\n> \r\n> x45\r\n\r\n", "0", "1043182249", "0");
INSERT INTO tblForums VALUES("9", "2", "1", "8", "Re: x1", "> > > > x2\r\n> > > \r\n> > > x3\r\n> > \r\n> > x45\r\n> \r\n> \r\n\r\n", "0", "1043182251", "0");
INSERT INTO tblForums VALUES("10", "2", "1", "9", "Re: x1", "> > > > > x2\r\n> > > > \r\n> > > > x3\r\n> > > \r\n> > > x45\r\n> > \r\n> > \r\n> \r\n> \r\n\r\n", "0", "1043182253", "0");
INSERT INTO tblForums VALUES("11", "2", "1", "7", "Re: x1", "> > > x2\r\n> > \r\n> > x3\r\n> \r\n> x45\r\n\r\n", "0", "1043182255", "0");
INSERT INTO tblForums VALUES("12", "2", "1", "11", "Re: x1", "> > > > x2\r\n> > > \r\n> > > x3\r\n> > \r\n> > x45\r\n> \r\n> \r\n\r\n", "0", "1043182256", "0");
INSERT INTO tblForums VALUES("13", "2", "1", "5", "Re: x1", "> x2\r\n\r\n", "0", "1043182259", "0");


#
# Table structure for table 'tblLoginAttempts'
#

CREATE TABLE tblLoginAttempts (
  idx bigint(20) unsigned NOT NULL auto_increment,
  userId bigint(20) unsigned NOT NULL default '0',
  IP varchar(30) NOT NULL default '',
  loggedin bigint(20) NOT NULL default '0',
  loggedout bigint(20) unsigned NOT NULL default '0',
  bygame tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (idx)
) TYPE=MyISAM COMMENT='timeplayed används inte för sajtinlogg';



#
# Dumping data for table 'tblLoginAttempts'
#

INSERT INTO tblLoginAttempts VALUES("1", "1", "127.0.0.1", "1048327163", "1048327346", "1");
INSERT INTO tblLoginAttempts VALUES("2", "1", "127.0.0.1", "1048327168", "1048327346", "1");
INSERT INTO tblLoginAttempts VALUES("3", "1", "127.0.0.1", "1048328806", "1048328813", "1");
INSERT INTO tblLoginAttempts VALUES("4", "1", "127.0.0.1", "1048329612", "1048329613", "1");
INSERT INTO tblLoginAttempts VALUES("5", "1", "127.0.0.1", "1048329657", "0", "1");
INSERT INTO tblLoginAttempts VALUES("6", "1", "127.0.0.1", "1048329673", "0", "1");
INSERT INTO tblLoginAttempts VALUES("7", "1", "127.0.0.1", "1048329690", "1048329692", "1");
INSERT INTO tblLoginAttempts VALUES("8", "1", "127.0.0.1", "1048329857", "1048329866", "1");
INSERT INTO tblLoginAttempts VALUES("9", "1", "127.0.0.1", "1048329975", "1048329978", "1");
INSERT INTO tblLoginAttempts VALUES("10", "1", "127.0.0.1", "1048330063", "1048330071", "1");
INSERT INTO tblLoginAttempts VALUES("11", "1", "127.0.0.1", "1048330382", "1048330391", "1");
INSERT INTO tblLoginAttempts VALUES("12", "1", "127.0.0.1", "1048330536", "1048330540", "1");
INSERT INTO tblLoginAttempts VALUES("13", "1", "127.0.0.1", "1048330549", "1048330560", "1");
INSERT INTO tblLoginAttempts VALUES("14", "1", "127.0.0.1", "1048330785", "1048330792", "1");
INSERT INTO tblLoginAttempts VALUES("15", "1", "127.0.0.1", "1048330803", "1048330814", "1");
INSERT INTO tblLoginAttempts VALUES("16", "1", "127.0.0.1", "1048330824", "1048330845", "1");
INSERT INTO tblLoginAttempts VALUES("17", "1", "127.0.0.1", "1048330965", "1048330977", "1");
INSERT INTO tblLoginAttempts VALUES("18", "1", "127.0.0.1", "1048343011", "1048343017", "1");
INSERT INTO tblLoginAttempts VALUES("19", "1", "127.0.0.1", "1048343159", "0", "1");
INSERT INTO tblLoginAttempts VALUES("20", "1", "127.0.0.1", "1048343308", "1048343311", "1");
INSERT INTO tblLoginAttempts VALUES("21", "1", "127.0.0.1", "1048343510", "1048343512", "1");
INSERT INTO tblLoginAttempts VALUES("22", "1", "127.0.0.1", "1048343524", "1048343537", "1");
INSERT INTO tblLoginAttempts VALUES("23", "1", "127.0.0.1", "1048343636", "1048343639", "1");
INSERT INTO tblLoginAttempts VALUES("24", "1", "127.0.0.1", "1048343657", "1048343659", "1");
INSERT INTO tblLoginAttempts VALUES("25", "1", "127.0.0.1", "1048343667", "0", "1");
INSERT INTO tblLoginAttempts VALUES("26", "1", "127.0.0.1", "1048343674", "1048343676", "1");
INSERT INTO tblLoginAttempts VALUES("27", "1", "127.0.0.1", "1048343684", "0", "1");
INSERT INTO tblLoginAttempts VALUES("28", "1", "127.0.0.1", "1048343717", "1048343721", "1");
INSERT INTO tblLoginAttempts VALUES("29", "1", "127.0.0.1", "1048343734", "1048343735", "1");
INSERT INTO tblLoginAttempts VALUES("30", "1", "127.0.0.1", "1048343750", "1048343751", "1");
INSERT INTO tblLoginAttempts VALUES("31", "1", "127.0.0.1", "1048343752", "1048343753", "1");
INSERT INTO tblLoginAttempts VALUES("32", "1", "127.0.0.1", "1048343753", "0", "1");
INSERT INTO tblLoginAttempts VALUES("33", "1", "127.0.0.1", "1048343803", "1048343809", "1");
INSERT INTO tblLoginAttempts VALUES("34", "1", "127.0.0.1", "1048343813", "0", "1");
INSERT INTO tblLoginAttempts VALUES("35", "1", "127.0.0.1", "1048344178", "1048344180", "1");
INSERT INTO tblLoginAttempts VALUES("36", "1", "127.0.0.1", "1048344181", "1048344182", "1");
INSERT INTO tblLoginAttempts VALUES("37", "1", "127.0.0.1", "1048344183", "0", "1");
INSERT INTO tblLoginAttempts VALUES("38", "1", "127.0.0.1", "1048344192", "1048344193", "1");
INSERT INTO tblLoginAttempts VALUES("39", "1", "127.0.0.1", "1048361849", "1048361895", "1");
INSERT INTO tblLoginAttempts VALUES("40", "1", "127.0.0.1", "1048361859", "1048361863", "1");
INSERT INTO tblLoginAttempts VALUES("41", "1", "127.0.0.1", "1048361864", "1048361865", "1");
INSERT INTO tblLoginAttempts VALUES("42", "1", "127.0.0.1", "1048361869", "1048361871", "1");
INSERT INTO tblLoginAttempts VALUES("43", "1", "127.0.0.1", "1048361880", "1048361881", "1");
INSERT INTO tblLoginAttempts VALUES("44", "1", "127.0.0.1", "1048361911", "1048361914", "1");
INSERT INTO tblLoginAttempts VALUES("45", "1", "127.0.0.1", "1048361918", "0", "1");
INSERT INTO tblLoginAttempts VALUES("46", "1", "127.0.0.1", "1048362025", "1048362027", "1");
INSERT INTO tblLoginAttempts VALUES("47", "1", "127.0.0.1", "1048362028", "0", "1");
INSERT INTO tblLoginAttempts VALUES("48", "1", "127.0.0.1", "1048362067", "1048362069", "1");
INSERT INTO tblLoginAttempts VALUES("49", "1", "127.0.0.1", "1048362069", "0", "1");
INSERT INTO tblLoginAttempts VALUES("50", "1", "127.0.0.1", "1048362132", "1048362134", "1");
INSERT INTO tblLoginAttempts VALUES("51", "1", "127.0.0.1", "1048362134", "0", "1");
INSERT INTO tblLoginAttempts VALUES("52", "1", "127.0.0.1", "1048363825", "1048363826", "1");
INSERT INTO tblLoginAttempts VALUES("53", "1", "127.0.0.1", "1048363828", "1048363828", "1");
INSERT INTO tblLoginAttempts VALUES("54", "1", "127.0.0.1", "1048363829", "1048363830", "1");
INSERT INTO tblLoginAttempts VALUES("55", "1", "127.0.0.1", "1048363831", "1048363831", "1");
INSERT INTO tblLoginAttempts VALUES("56", "1", "127.0.0.1", "1048363910", "1048363912", "1");
INSERT INTO tblLoginAttempts VALUES("57", "1", "127.0.0.1", "1048364098", "0", "1");
INSERT INTO tblLoginAttempts VALUES("58", "1", "127.0.0.1", "1048364136", "0", "1");
INSERT INTO tblLoginAttempts VALUES("59", "1", "127.0.0.1", "1048364142", "0", "1");
INSERT INTO tblLoginAttempts VALUES("60", "1", "127.0.0.1", "1048364148", "0", "1");
INSERT INTO tblLoginAttempts VALUES("61", "1", "127.0.0.1", "1048364280", "0", "1");
INSERT INTO tblLoginAttempts VALUES("62", "1", "127.0.0.1", "1048364283", "0", "1");
INSERT INTO tblLoginAttempts VALUES("63", "1", "127.0.0.1", "1048364285", "0", "1");
INSERT INTO tblLoginAttempts VALUES("64", "1", "127.0.0.1", "1048364779", "0", "1");
INSERT INTO tblLoginAttempts VALUES("65", "1", "127.0.0.1", "1048364785", "0", "1");
INSERT INTO tblLoginAttempts VALUES("66", "1", "127.0.0.1", "1048364787", "0", "1");
INSERT INTO tblLoginAttempts VALUES("67", "1", "127.0.0.1", "1048365295", "1048365297", "1");
INSERT INTO tblLoginAttempts VALUES("68", "1", "127.0.0.1", "1048365297", "0", "1");
INSERT INTO tblLoginAttempts VALUES("69", "1", "127.0.0.1", "1048365359", "1048365361", "1");
INSERT INTO tblLoginAttempts VALUES("70", "1", "127.0.0.1", "1048365361", "0", "1");
INSERT INTO tblLoginAttempts VALUES("71", "1", "127.0.0.1", "1048365367", "1048365386", "1");
INSERT INTO tblLoginAttempts VALUES("72", "1", "127.0.0.1", "1048365370", "1048365372", "1");
INSERT INTO tblLoginAttempts VALUES("73", "1", "127.0.0.1", "1048365373", "0", "1");
INSERT INTO tblLoginAttempts VALUES("74", "1", "127.0.0.1", "1048365391", "1048365394", "1");
INSERT INTO tblLoginAttempts VALUES("75", "1", "127.0.0.1", "1048365394", "1048365395", "1");
INSERT INTO tblLoginAttempts VALUES("76", "1", "127.0.0.1", "1048365541", "1048365543", "1");
INSERT INTO tblLoginAttempts VALUES("77", "1", "127.0.0.1", "1048365543", "1048365544", "1");
INSERT INTO tblLoginAttempts VALUES("78", "1", "127.0.0.1", "1048365550", "1048365551", "1");
INSERT INTO tblLoginAttempts VALUES("79", "1", "127.0.0.1", "1048365553", "1048365554", "1");
INSERT INTO tblLoginAttempts VALUES("80", "1", "127.0.0.1", "1048365557", "1048365558", "1");
INSERT INTO tblLoginAttempts VALUES("81", "1", "127.0.0.1", "1048365559", "0", "1");
INSERT INTO tblLoginAttempts VALUES("82", "1", "127.0.0.1", "1048365624", "1048365626", "1");
INSERT INTO tblLoginAttempts VALUES("83", "1", "127.0.0.1", "1048365627", "0", "1");
INSERT INTO tblLoginAttempts VALUES("84", "1", "127.0.0.1", "1048365664", "1048365665", "1");
INSERT INTO tblLoginAttempts VALUES("85", "1", "127.0.0.1", "1048365666", "1048365666", "1");
INSERT INTO tblLoginAttempts VALUES("86", "1", "127.0.0.1", "1048366243", "0", "1");
INSERT INTO tblLoginAttempts VALUES("87", "1", "127.0.0.1", "1048366251", "0", "1");
INSERT INTO tblLoginAttempts VALUES("88", "1", "127.0.0.1", "1048366360", "0", "1");
INSERT INTO tblLoginAttempts VALUES("89", "1", "127.0.0.1", "1048366373", "0", "1");
INSERT INTO tblLoginAttempts VALUES("90", "1", "127.0.0.1", "1048366385", "0", "1");
INSERT INTO tblLoginAttempts VALUES("91", "1", "127.0.0.1", "1048367488", "1048367490", "1");
INSERT INTO tblLoginAttempts VALUES("92", "1", "127.0.0.1", "1048367743", "1048367745", "1");
INSERT INTO tblLoginAttempts VALUES("93", "1", "127.0.0.1", "1048367746", "1048367746", "1");
INSERT INTO tblLoginAttempts VALUES("94", "1", "127.0.0.1", "1048367748", "1048367748", "1");
INSERT INTO tblLoginAttempts VALUES("95", "1", "127.0.0.1", "1048367754", "1048367755", "1");
INSERT INTO tblLoginAttempts VALUES("96", "1", "127.0.0.1", "1048367756", "1048367756", "1");
INSERT INTO tblLoginAttempts VALUES("97", "1", "127.0.0.1", "1048368067", "1048368068", "1");
INSERT INTO tblLoginAttempts VALUES("98", "1", "127.0.0.1", "1048371770", "1048371772", "1");
INSERT INTO tblLoginAttempts VALUES("99", "1", "127.0.0.1", "1048371773", "1048371773", "1");
INSERT INTO tblLoginAttempts VALUES("100", "1", "127.0.0.1", "1048371774", "1048371775", "1");
INSERT INTO tblLoginAttempts VALUES("101", "1", "127.0.0.1", "1048371775", "1048371776", "1");
INSERT INTO tblLoginAttempts VALUES("102", "1", "127.0.0.1", "1048371776", "1048371777", "1");
INSERT INTO tblLoginAttempts VALUES("103", "1", "127.0.0.1", "1048371777", "1048371778", "1");
INSERT INTO tblLoginAttempts VALUES("104", "1", "127.0.0.1", "1048371778", "1048371779", "1");
INSERT INTO tblLoginAttempts VALUES("105", "1", "127.0.0.1", "1048371781", "1048371782", "1");
INSERT INTO tblLoginAttempts VALUES("106", "1", "127.0.0.1", "1048527646", "0", "1");
INSERT INTO tblLoginAttempts VALUES("107", "1", "127.0.0.1", "1048527706", "0", "1");
INSERT INTO tblLoginAttempts VALUES("108", "1", "127.0.0.1", "1048527816", "0", "1");
INSERT INTO tblLoginAttempts VALUES("109", "1", "127.0.0.1", "1048527914", "0", "1");
INSERT INTO tblLoginAttempts VALUES("110", "1", "127.0.0.1", "1048527943", "0", "1");
INSERT INTO tblLoginAttempts VALUES("111", "1", "127.0.0.1", "1048528194", "0", "1");
INSERT INTO tblLoginAttempts VALUES("112", "1", "127.0.0.1", "1048528232", "0", "1");
INSERT INTO tblLoginAttempts VALUES("113", "1", "127.0.0.1", "1048528288", "0", "1");
INSERT INTO tblLoginAttempts VALUES("114", "1", "127.0.0.1", "1048528354", "0", "1");
INSERT INTO tblLoginAttempts VALUES("115", "1", "127.0.0.1", "1048528385", "0", "1");
INSERT INTO tblLoginAttempts VALUES("116", "1", "127.0.0.1", "1048528438", "0", "1");
INSERT INTO tblLoginAttempts VALUES("117", "1", "127.0.0.1", "1048528583", "0", "1");
INSERT INTO tblLoginAttempts VALUES("118", "1", "127.0.0.1", "1048528648", "0", "1");
INSERT INTO tblLoginAttempts VALUES("119", "1", "127.0.0.1", "1048528675", "0", "1");
INSERT INTO tblLoginAttempts VALUES("120", "1", "127.0.0.1", "1048528744", "0", "1");
INSERT INTO tblLoginAttempts VALUES("121", "1", "127.0.0.1", "1048528766", "0", "1");
INSERT INTO tblLoginAttempts VALUES("122", "1", "127.0.0.1", "1048528788", "0", "1");
INSERT INTO tblLoginAttempts VALUES("123", "1", "127.0.0.1", "1048528815", "0", "1");
INSERT INTO tblLoginAttempts VALUES("124", "1", "127.0.0.1", "1048529057", "0", "1");
INSERT INTO tblLoginAttempts VALUES("125", "1", "127.0.0.1", "1048529086", "0", "1");
INSERT INTO tblLoginAttempts VALUES("126", "1", "127.0.0.1", "1048529144", "0", "1");
INSERT INTO tblLoginAttempts VALUES("127", "1", "127.0.0.1", "1048529171", "0", "1");
INSERT INTO tblLoginAttempts VALUES("128", "1", "127.0.0.1", "1048529247", "0", "1");
INSERT INTO tblLoginAttempts VALUES("129", "1", "127.0.0.1", "1048529294", "0", "1");
INSERT INTO tblLoginAttempts VALUES("130", "1", "127.0.0.1", "1048529383", "0", "1");
INSERT INTO tblLoginAttempts VALUES("131", "1", "127.0.0.1", "1048529494", "0", "1");
INSERT INTO tblLoginAttempts VALUES("132", "1", "127.0.0.1", "1048529537", "0", "1");
INSERT INTO tblLoginAttempts VALUES("133", "1", "127.0.0.1", "1048529551", "0", "1");
INSERT INTO tblLoginAttempts VALUES("134", "1", "127.0.0.1", "1048529618", "0", "1");
INSERT INTO tblLoginAttempts VALUES("135", "1", "127.0.0.1", "1048529661", "0", "1");
INSERT INTO tblLoginAttempts VALUES("136", "1", "127.0.0.1", "1048529670", "0", "1");
INSERT INTO tblLoginAttempts VALUES("137", "1", "127.0.0.1", "1048529904", "0", "1");
INSERT INTO tblLoginAttempts VALUES("138", "1", "127.0.0.1", "1048530022", "0", "1");
INSERT INTO tblLoginAttempts VALUES("139", "1", "127.0.0.1", "1048530070", "0", "1");
INSERT INTO tblLoginAttempts VALUES("140", "1", "127.0.0.1", "1048530109", "0", "1");
INSERT INTO tblLoginAttempts VALUES("141", "1", "127.0.0.1", "1048530211", "0", "1");
INSERT INTO tblLoginAttempts VALUES("142", "1", "127.0.0.1", "1048530292", "0", "1");
INSERT INTO tblLoginAttempts VALUES("143", "1", "127.0.0.1", "1048530307", "0", "1");
INSERT INTO tblLoginAttempts VALUES("144", "1", "127.0.0.1", "1048530358", "0", "1");
INSERT INTO tblLoginAttempts VALUES("145", "1", "127.0.0.1", "1048530534", "0", "1");
INSERT INTO tblLoginAttempts VALUES("146", "1", "127.0.0.1", "1048530623", "0", "1");
INSERT INTO tblLoginAttempts VALUES("147", "1", "127.0.0.1", "1048530678", "0", "1");
INSERT INTO tblLoginAttempts VALUES("148", "1", "127.0.0.1", "1048530773", "0", "1");
INSERT INTO tblLoginAttempts VALUES("149", "1", "127.0.0.1", "1048530823", "0", "1");
INSERT INTO tblLoginAttempts VALUES("150", "1", "127.0.0.1", "1048530853", "0", "1");
INSERT INTO tblLoginAttempts VALUES("151", "1", "127.0.0.1", "1048530895", "0", "1");
INSERT INTO tblLoginAttempts VALUES("152", "1", "127.0.0.1", "1048530958", "1048530984", "1");
INSERT INTO tblLoginAttempts VALUES("153", "1", "127.0.0.1", "1048530984", "0", "1");
INSERT INTO tblLoginAttempts VALUES("154", "1", "127.0.0.1", "1048531092", "0", "1");
INSERT INTO tblLoginAttempts VALUES("155", "1", "127.0.0.1", "1048531146", "1048531161", "1");
INSERT INTO tblLoginAttempts VALUES("156", "1", "127.0.0.1", "1048531195", "1048531199", "1");
INSERT INTO tblLoginAttempts VALUES("157", "1", "127.0.0.1", "1048531437", "0", "1");
INSERT INTO tblLoginAttempts VALUES("158", "1", "127.0.0.1", "1048531602", "0", "1");
INSERT INTO tblLoginAttempts VALUES("159", "1", "127.0.0.1", "1048531630", "0", "1");
INSERT INTO tblLoginAttempts VALUES("160", "1", "127.0.0.1", "1048531656", "1048531727", "1");
INSERT INTO tblLoginAttempts VALUES("161", "1", "127.0.0.1", "1048531727", "0", "1");
INSERT INTO tblLoginAttempts VALUES("162", "1", "127.0.0.1", "1048531768", "0", "1");
INSERT INTO tblLoginAttempts VALUES("163", "1", "127.0.0.1", "1048531808", "0", "1");
INSERT INTO tblLoginAttempts VALUES("164", "1", "127.0.0.1", "1048532001", "0", "1");
INSERT INTO tblLoginAttempts VALUES("165", "1", "127.0.0.1", "1048532411", "0", "1");
INSERT INTO tblLoginAttempts VALUES("166", "1", "127.0.0.1", "1048532419", "0", "1");
INSERT INTO tblLoginAttempts VALUES("167", "1", "127.0.0.1", "1048532426", "0", "1");
INSERT INTO tblLoginAttempts VALUES("168", "1", "127.0.0.1", "1048532605", "1048532642", "1");
INSERT INTO tblLoginAttempts VALUES("169", "1", "127.0.0.1", "1048532768", "0", "1");
INSERT INTO tblLoginAttempts VALUES("170", "1", "127.0.0.1", "1048532776", "0", "1");
INSERT INTO tblLoginAttempts VALUES("171", "1", "127.0.0.1", "1048533210", "0", "1");
INSERT INTO tblLoginAttempts VALUES("172", "1", "127.0.0.1", "1048533309", "0", "1");
INSERT INTO tblLoginAttempts VALUES("173", "1", "127.0.0.1", "1048533531", "0", "1");
INSERT INTO tblLoginAttempts VALUES("174", "1", "127.0.0.1", "1048533540", "0", "1");
INSERT INTO tblLoginAttempts VALUES("175", "1", "127.0.0.1", "1048533612", "0", "1");
INSERT INTO tblLoginAttempts VALUES("176", "1", "127.0.0.1", "1048533614", "0", "1");
INSERT INTO tblLoginAttempts VALUES("177", "1", "127.0.0.1", "1048533719", "0", "1");
INSERT INTO tblLoginAttempts VALUES("178", "1", "127.0.0.1", "1048533722", "0", "1");
INSERT INTO tblLoginAttempts VALUES("179", "1", "127.0.0.1", "1048533881", "1048533886", "1");
INSERT INTO tblLoginAttempts VALUES("180", "1", "127.0.0.1", "1048533909", "0", "1");
INSERT INTO tblLoginAttempts VALUES("181", "1", "127.0.0.1", "1048533911", "0", "1");
INSERT INTO tblLoginAttempts VALUES("182", "1", "127.0.0.1", "1048533971", "0", "1");
INSERT INTO tblLoginAttempts VALUES("183", "1", "127.0.0.1", "1048533973", "0", "1");
INSERT INTO tblLoginAttempts VALUES("184", "1", "127.0.0.1", "1048534157", "0", "1");
INSERT INTO tblLoginAttempts VALUES("185", "1", "127.0.0.1", "1048534188", "0", "1");
INSERT INTO tblLoginAttempts VALUES("186", "1", "127.0.0.1", "1048534372", "0", "1");
INSERT INTO tblLoginAttempts VALUES("187", "1", "127.0.0.1", "1048534375", "0", "1");
INSERT INTO tblLoginAttempts VALUES("188", "1", "127.0.0.1", "1048534592", "0", "1");
INSERT INTO tblLoginAttempts VALUES("189", "1", "127.0.0.1", "1048534594", "0", "1");
INSERT INTO tblLoginAttempts VALUES("190", "1", "127.0.0.1", "1048534679", "0", "1");
INSERT INTO tblLoginAttempts VALUES("191", "1", "127.0.0.1", "1048534685", "0", "1");
INSERT INTO tblLoginAttempts VALUES("192", "1", "127.0.0.1", "1048616483", "0", "1");
INSERT INTO tblLoginAttempts VALUES("193", "1", "127.0.0.1", "1048617757", "0", "1");
INSERT INTO tblLoginAttempts VALUES("194", "1", "127.0.0.1", "1048618336", "0", "1");
INSERT INTO tblLoginAttempts VALUES("195", "1", "127.0.0.1", "1048619127", "1048619154", "1");
INSERT INTO tblLoginAttempts VALUES("196", "1", "127.0.0.1", "1048619162", "1048619217", "1");
INSERT INTO tblLoginAttempts VALUES("197", "1", "127.0.0.1", "1048619406", "1048619408", "1");
INSERT INTO tblLoginAttempts VALUES("198", "1", "127.0.0.1", "1048624801", "1048624946", "1");
INSERT INTO tblLoginAttempts VALUES("199", "1", "127.0.0.1", "1048625012", "0", "1");
INSERT INTO tblLoginAttempts VALUES("200", "1", "127.0.0.1", "1048625088", "0", "1");
INSERT INTO tblLoginAttempts VALUES("201", "1", "127.0.0.1", "1048625396", "1048625410", "1");
INSERT INTO tblLoginAttempts VALUES("202", "1", "127.0.0.1", "1048625405", "1048625410", "1");
INSERT INTO tblLoginAttempts VALUES("203", "1", "127.0.0.1", "1048625734", "1048625741", "1");
INSERT INTO tblLoginAttempts VALUES("204", "1", "127.0.0.1", "1048626035", "1048626066", "1");
INSERT INTO tblLoginAttempts VALUES("205", "1", "127.0.0.1", "1048626171", "1048626174", "1");
INSERT INTO tblLoginAttempts VALUES("206", "1", "127.0.0.1", "1048626365", "1048626378", "1");
INSERT INTO tblLoginAttempts VALUES("207", "1", "127.0.0.1", "1048626412", "1048626438", "1");
INSERT INTO tblLoginAttempts VALUES("208", "1", "127.0.0.1", "1048626679", "1048626707", "1");
INSERT INTO tblLoginAttempts VALUES("209", "1", "127.0.0.1", "1048626705", "1048626738", "1");
INSERT INTO tblLoginAttempts VALUES("210", "1", "127.0.0.1", "1048627197", "1048627378", "1");
INSERT INTO tblLoginAttempts VALUES("211", "1", "127.0.0.1", "1048627211", "0", "1");
INSERT INTO tblLoginAttempts VALUES("212", "1", "127.0.0.1", "1048627222", "1048627222", "1");
INSERT INTO tblLoginAttempts VALUES("213", "1", "127.0.0.1", "1048627397", "1048627466", "1");
INSERT INTO tblLoginAttempts VALUES("214", "1", "127.0.0.1", "1048627403", "1048627464", "1");
INSERT INTO tblLoginAttempts VALUES("215", "1", "127.0.0.1", "1048627468", "1048627468", "1");
INSERT INTO tblLoginAttempts VALUES("216", "1", "127.0.0.1", "1048627477", "1048627584", "1");
INSERT INTO tblLoginAttempts VALUES("217", "1", "127.0.0.1", "1048627482", "1048627586", "1");
INSERT INTO tblLoginAttempts VALUES("218", "1", "127.0.0.1", "1048627600", "1048627600", "1");
INSERT INTO tblLoginAttempts VALUES("219", "1", "127.0.0.1", "1048627604", "1048627776", "1");
INSERT INTO tblLoginAttempts VALUES("220", "1", "127.0.0.1", "1048627606", "1048627776", "1");
INSERT INTO tblLoginAttempts VALUES("221", "1", "127.0.0.1", "1048627791", "1048628006", "1");
INSERT INTO tblLoginAttempts VALUES("222", "1", "127.0.0.1", "1048627794", "1048628006", "1");
INSERT INTO tblLoginAttempts VALUES("223", "1", "127.0.0.1", "1048628025", "1048628072", "1");
INSERT INTO tblLoginAttempts VALUES("224", "1", "127.0.0.1", "1048628028", "1048628072", "1");
INSERT INTO tblLoginAttempts VALUES("225", "1", "127.0.0.1", "1048628038", "1048628072", "1");
INSERT INTO tblLoginAttempts VALUES("226", "1", "127.0.0.1", "1048628052", "1048628072", "1");
INSERT INTO tblLoginAttempts VALUES("227", "1", "127.0.0.1", "1048628364", "1048628373", "1");
INSERT INTO tblLoginAttempts VALUES("228", "1", "127.0.0.1", "1048628366", "1048628373", "1");
INSERT INTO tblLoginAttempts VALUES("229", "1", "127.0.0.1", "1048628368", "1048628373", "1");
INSERT INTO tblLoginAttempts VALUES("230", "1", "127.0.0.1", "1048628370", "1048628373", "1");
INSERT INTO tblLoginAttempts VALUES("231", "1", "127.0.0.1", "1048628937", "1048628944", "1");
INSERT INTO tblLoginAttempts VALUES("232", "1", "127.0.0.1", "1048628962", "1048629094", "1");
INSERT INTO tblLoginAttempts VALUES("233", "1", "127.0.0.1", "1048629099", "1048629100", "1");
INSERT INTO tblLoginAttempts VALUES("234", "1", "127.0.0.1", "1048629111", "1048629129", "1");
INSERT INTO tblLoginAttempts VALUES("235", "1", "127.0.0.1", "1048629114", "1048629129", "1");
INSERT INTO tblLoginAttempts VALUES("236", "1", "127.0.0.1", "1048629793", "1048629801", "1");
INSERT INTO tblLoginAttempts VALUES("237", "1", "127.0.0.1", "1048629950", "1048629957", "1");
INSERT INTO tblLoginAttempts VALUES("238", "1", "127.0.0.1", "1048630201", "1048630255", "1");
INSERT INTO tblLoginAttempts VALUES("239", "1", "127.0.0.1", "1048630220", "1048630255", "1");
INSERT INTO tblLoginAttempts VALUES("240", "1", "127.0.0.1", "1048630223", "1048630255", "1");
INSERT INTO tblLoginAttempts VALUES("241", "1", "127.0.0.1", "1048630225", "1048630255", "1");


#
# Table structure for table 'tblMailActivation'
#

CREATE TABLE tblMailActivation (
  userId bigint(20) unsigned NOT NULL default '0',
  activationCode varchar(20) NOT NULL default '',
  PRIMARY KEY  (userId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblMailActivation'
#



#
# Table structure for table 'tblNews'
#

CREATE TABLE tblNews (
  itemId int(10) unsigned NOT NULL auto_increment,
  subject varchar(200) NOT NULL default '',
  body blob NOT NULL,
  timestamp bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (itemId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblNews'
#

INSERT INTO tblNews VALUES("9", "Local times", "Local time for a user is now shown on their personal page, so you can get a idea on when the guy perhaps will come back online again =)", "1037395238");
INSERT INTO tblNews VALUES("10", "Your characters is now displayed on your page", "Also server information pages has been added, which shows some brief information of each server. For example how many users play on it, and how many characters exist there.", "1037410408");
INSERT INTO tblNews VALUES("11", "Content codes!", "You can now enter content codes under Settings to get more gaming time!", "1037430545");
INSERT INTO tblNews VALUES("12", "Added guild info to db+site display", "", "1037907080");
INSERT INTO tblNews VALUES("13", "The patcher finally available!", "Yes!\r\nThe first version of the patcher is out.\r\n\r\n<a href=\"/online/patcher/patcher101.rar\">Click here to download</a> (1004k)\r\nYou should always start it by running launch.exe, so the patcher can update itself.", "1038607639");
INSERT INTO tblNews VALUES("14", "Databases", "Downgraded from MySQL 4.0.5 to 3.23.54, because of data corruption and stability issues with 4.0.5\r\n\r\nWe\'ll get back to 4.0.x as soon as that branch has been declared stable.", "1039799651");
INSERT INTO tblNews VALUES("15", "Software updates", "Upgraded to PHP 4.3.0, Apache 2.0.44", "1043180897");


#
# Table structure for table 'tblNewsletters'
#

CREATE TABLE tblNewsletters (
  itemId bigint(20) unsigned NOT NULL auto_increment,
  subject varchar(200) NOT NULL default '',
  body blob NOT NULL,
  headers blob NOT NULL,
  timestamp bigint(20) unsigned NOT NULL default '0',
  recievers bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (itemId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblNewsletters'
#

INSERT INTO tblNewsletters VALUES("3", "test #2", "oki sista testet för idag", "From: \"Online Newsletter\" <support@inthc.net>\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=iso-8859-1\r\nX-Priority: 1\r\nBcc: martin2@inthc.net, agaton@inthc.net, phobia@bigfoot.com\r\n", "1041551567", "3");
INSERT INTO tblNewsletters VALUES("2", "test #1", "wiep sorry mattias..", "From: \"online test\" <support@inthc.net>\r\nTo: support@inthc.net\r\nReply-To: noreply@inthc.net\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=iso-8859-1\r\nBcc: martin2@inthc.net, agaton@2000.com, phobia@bigfoot.com\r\n", "1041551336", "3");
INSERT INTO tblNewsletters VALUES("4", "hej agaton", "tja där funkar de elle?", "From: \"Online Newsletter\" <support@inthc.net>\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=iso-8859-1\r\nX-Priority: 1\r\nBcc: agaton2000@hotmail.com\r\n", "1041552162", "3");


#
# Table structure for table 'tblServerDowntimes'
#

CREATE TABLE tblServerDowntimes (
  itemId bigint(20) unsigned NOT NULL auto_increment,
  info blob NOT NULL,
  timestamp bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (itemId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblServerDowntimes'
#

INSERT INTO tblServerDowntimes VALUES("1", "All servers will be down to celibrate christmas kthx!", "1040684400");
INSERT INTO tblServerDowntimes VALUES("2", "All servers will be taken down between GMT 8 am and 4 pm for maintanance.\r\n", "1042498800");
INSERT INTO tblServerDowntimes VALUES("3", "Everythign went black!", "1037314800");


#
# Table structure for table 'tblTimezones'
#

CREATE TABLE tblTimezones (
  zoneId tinyint(3) unsigned NOT NULL default '0',
  zoneName varchar(40) NOT NULL default '',
  zoneGMT smallint(5) NOT NULL default '0'
) TYPE=MyISAM;



#
# Dumping data for table 'tblTimezones'
#

INSERT INTO tblTimezones VALUES("0", "International Date Line West (IDLW)", "-1200");
INSERT INTO tblTimezones VALUES("1", "Nome (NT)", "-1100");
INSERT INTO tblTimezones VALUES("2", "Havaiian Standard Time (HST)", "-1000");
INSERT INTO tblTimezones VALUES("3", "Ykon Standard (YST)", "-900");
INSERT INTO tblTimezones VALUES("4", "Pacific Standard (PST)", "-800");
INSERT INTO tblTimezones VALUES("5", "Mountain Standard (MST)", "-700");
INSERT INTO tblTimezones VALUES("6", "Central Standard Time (CST)", "-600");
INSERT INTO tblTimezones VALUES("7", "Eastern Standard Time (EST)", "-500");
INSERT INTO tblTimezones VALUES("8", "Atlantic Standard (AT)", "-400");
INSERT INTO tblTimezones VALUES("9", "", "-300");
INSERT INTO tblTimezones VALUES("10", "Azores (AT)", "-200");
INSERT INTO tblTimezones VALUES("11", "West Africa (WAT)", "-100");
INSERT INTO tblTimezones VALUES("12", "Greenwich Mean Time (GMT)", "0");
INSERT INTO tblTimezones VALUES("13", "Central Europe Time (CET)", "100");
INSERT INTO tblTimezones VALUES("14", "Eastern Europe Time (EET)", "200");
INSERT INTO tblTimezones VALUES("15", "Baghdad (BT)", "300");
INSERT INTO tblTimezones VALUES("16", "", "400");
INSERT INTO tblTimezones VALUES("17", "", "500");
INSERT INTO tblTimezones VALUES("18", "", "600");
INSERT INTO tblTimezones VALUES("19", "West Australian Standard (WAS)", "700");
INSERT INTO tblTimezones VALUES("20", "China Coast (CCT)", "800");
INSERT INTO tblTimezones VALUES("21", "Japan Standard Time (JST)", "900");
INSERT INTO tblTimezones VALUES("22", "Australia Central Standard (ACS)", "930");
INSERT INTO tblTimezones VALUES("23", "Guam Standard (GST)", "1000");
INSERT INTO tblTimezones VALUES("24", "", "1100");
INSERT INTO tblTimezones VALUES("25", "New Zealand Standard (NZST)", "1200");


#
# Table structure for table 'tblTodoListComments'
#

CREATE TABLE tblTodoListComments (
  itemId bigint(20) unsigned NOT NULL default '0',
  itemComment blob NOT NULL,
  timestamp bigint(20) NOT NULL default '0',
  userId bigint(20) unsigned NOT NULL default '0'
) TYPE=MyISAM;



#
# Dumping data for table 'tblTodoListComments'
#

INSERT INTO tblTodoListComments VALUES("6", "Apache + mod_ssl finns inte i binary format för windows, pga licensfrågor tror jag... Kolla runt efter inofficiella releaser osv!", "1037328183", "1");
INSERT INTO tblTodoListComments VALUES("8", "Status changed from OPEN to CLOSED.", "1037322934", "1");
INSERT INTO tblTodoListComments VALUES("5", "Status changed from OPEN to ASSIGNED.<br>(Meaning item is now assigned to martin).", "1037327973", "1");
INSERT INTO tblTodoListComments VALUES("4", "Status changed from OPEN to ASSIGNED.<br>(Meaning item is now assigned to martin).", "1037322130", "1");
INSERT INTO tblTodoListComments VALUES("7", "Alltså buggrapporter/feature requests från användare. Även \"överföra\" en rapport in till to do list-systemet, och markera en rapport som BOGUS/FIXED (?)", "1037328291", "1");
INSERT INTO tblTodoListComments VALUES("7", "Status changed from OPEN to ASSIGNED.<br>(Meaning item is now assigned to martin).", "1037328296", "1");
INSERT INTO tblTodoListComments VALUES("4", "Status changed from ASSIGNED to CLOSED.", "1037333967", "1");
INSERT INTO tblTodoListComments VALUES("5", "Status changed from ASSIGNED to CLOSED.", "1037333997", "1");
INSERT INTO tblTodoListComments VALUES("35", "Imported from a reported bug.", "1037338733", "1");
INSERT INTO tblTodoListComments VALUES("3", "martin assigned the task to martin.", "1037335152", "1");
INSERT INTO tblTodoListComments VALUES("7", "Visa rapporter, överföra rapporter  och stänga rapporter är implementerat. Dock kan man fortfarande inte markera en stängd rapport med en motivering. Jag hade tänkt mej \"BOGUS och ALREADY FIXED\" som alternativ, något mer?", "1037339605", "1");
INSERT INTO tblTodoListComments VALUES("8", "Status changed from CLOSED to OPEN.", "1037341335", "1");
INSERT INTO tblTodoListComments VALUES("8", "martin assigned the task to martin.", "1037341337", "1");
INSERT INTO tblTodoListComments VALUES("8", "Status changed from ASSIGNED to CLOSED.", "1037341340", "1");
INSERT INTO tblTodoListComments VALUES("37", "Status changed from OPEN to ASSIGNED.<br>(Meaning item is now assigned to martin).", "1037341520", "1");
INSERT INTO tblTodoListComments VALUES("38", "Status changed from OPEN to ASSIGNED.<br>(Meaning item is now assigned to martin).", "1037341598", "1");
INSERT INTO tblTodoListComments VALUES("7", "nu får man välja anledning till varför en bugg ska stängas, samt det finns plats för flera alternativ", "1037370392", "1");
INSERT INTO tblTodoListComments VALUES("7", "Status changed from ASSIGNED to CLOSED.", "1037370394", "1");
INSERT INTO tblTodoListComments VALUES("38", "Status changed from ASSIGNED to CLOSED.", "1037372285", "1");
INSERT INTO tblTodoListComments VALUES("39", "martin assigned the task to martin.", "1037372540", "1");
INSERT INTO tblTodoListComments VALUES("39", "Status changed from ASSIGNED to CLOSED.", "1037377663", "1");
INSERT INTO tblTodoListComments VALUES("37", "Dölja mailaddress för andra medlemmar finns nu i register new user samt my settings-skärmen", "1037379925", "1");
INSERT INTO tblTodoListComments VALUES("37", "Newsletter-kryssruta finns nu både i Register user och My settings!", "1037388533", "1");
INSERT INTO tblTodoListComments VALUES("40", "Status changed from OPEN to ASSIGNED.<br>(Meaning item is now assigned to martin).", "1037388915", "1");
INSERT INTO tblTodoListComments VALUES("36", "Status changed from OPEN to ASSIGNED.<br>(Meaning item is now assigned to martin).", "1037388933", "1");
INSERT INTO tblTodoListComments VALUES("36", "Måste få tillbaks bostream först.. =)", "1037388948", "1");
INSERT INTO tblTodoListComments VALUES("13", "Python används i EVA:Second genesis, verkar relativt enkelt att integrera, har bra dokumentation på ämnet osv.\r\nPHP har ingen dokumentation alls av vad jag hittat om hur man embeddar PHP. Kolla MyPHP-projektet, som embeddar PHP i MySQL.\r\nSkulle föredra PHP eftersom jag är vanare vid det.", "1037395537", "1");
INSERT INTO tblTodoListComments VALUES("37", "Skicka nyhetsbrevs-dialog finns nu, och skickat brev sparas dessutom i databasen tillsammans med antal mottagare, så man kan få se lite siffror sen ;)... Det enda som saknas är koden för att verkligen skicka ut brevet, samt splitta upp brevet i flera utskick, med t.ex 500 mottagare på varje (i BCC).", "1037401220", "1");
INSERT INTO tblTodoListComments VALUES("37", "La till visa nyhetsbrevs-arkivgrejer..", "1037402081", "1");
INSERT INTO tblTodoListComments VALUES("41", "Måste fundera lite på hur kommunikationen på sajten ska fungera, då man i spelet kan ha flera karaktärer...", "1037408383", "1");
INSERT INTO tblTodoListComments VALUES("42", "Status changed from OPEN to ASSIGNED by martin.<br>(Meaning item is now assigned to martin).", "1037410614", "0");
INSERT INTO tblTodoListComments VALUES("35", "Status changed from OPEN to ASSIGNED by martin.<br>(Meaning item is now assigned to martin).", "1037410649", "0");
INSERT INTO tblTodoListComments VALUES("35", "Har förberett för detta. Namnen på karaktärerna listas på My page.\r\nFinns ännu inte mer information i databasen, ska lägga till en skelettsida för en karaktär och sen stänger vi buggen och bygger ut skelettsidan eftersom.", "1037410704", "1");
INSERT INTO tblTodoListComments VALUES("35", "Character info for Raija\r\n\r\nRaija is played by martin, on Test server #1.\r\n\r\n--\r\nstänger buggen.", "1037411373", "1");
INSERT INTO tblTodoListComments VALUES("35", "Status changed from ASSIGNED to CLOSED by martin.", "1037411374", "0");
INSERT INTO tblTodoListComments VALUES("42", "12-siffrig säkerhetskod borde räcka.", "1037414974", "1");
INSERT INTO tblTodoListComments VALUES("42", "Har gjort admingränssnitt för att skapa koder samt visa hur många som finns i databasen (använda/oanvända)", "1037417750", "1");
INSERT INTO tblTodoListComments VALUES("42", "Måste klura mera på hur användarens \"subscription\" ska se ut innan ja implementerar resten, men koderna finns iallafall där nu =)", "1037417817", "1");
INSERT INTO tblTodoListComments VALUES("42", "La till gränssnitt för användare med. Vid upplåsning markeras koden som använd, samt userId och timestamp för upplåsningen sparas, ska även logga alla upplåsningsförsök till en textfil (inte gjort ännu). Dock görs inget mer med de månader som man nu har fått till godo. Dessa ska adderas på \"kontot giltigt t.o.m\" timestampen...", "1037426400", "1");
INSERT INTO tblTodoListComments VALUES("42", "Nu räknas upplåsta månader dit!", "1037428074", "1");
INSERT INTO tblTodoListComments VALUES("42", "Nu loggas alla 12-siffriga koder, IP och userId! Stänger rapporten", "1037429552", "1");
INSERT INTO tblTodoListComments VALUES("42", "Status changed from ASSIGNED to CLOSED by martin.", "1037429553", "0");
INSERT INTO tblTodoListComments VALUES("40", "Lookup user fungerar nu", "1037455803", "1");
INSERT INTO tblTodoListComments VALUES("40", "Admins kan nu komma åt settings-sidan för en användare från deras sida.", "1037460775", "1");
INSERT INTO tblTodoListComments VALUES("40", "Admin kan nu ändra all info som den vanlige användaren kan göra! =) weehz t.ex ladda på en användares konto med content-koder, ändra lösenord och mail-address", "1037461837", "1");
INSERT INTO tblTodoListComments VALUES("40", "Status changed from ASSIGNED to CLOSED by martin.", "1037461839", "0");
INSERT INTO tblTodoListComments VALUES("41", "Status changed from OPEN to ASSIGNED by martin.<br>(Meaning item is now assigned to martin).", "1037467228", "0");
INSERT INTO tblTodoListComments VALUES("18", "hm för att skicka meddelanden till offlinekaraktärer kan det bli drygt för en spelare som spelar flera karaktärer att kontrollera att han har meddelanden.. kanske enklast att bara tillåta meddelandeskickande mellan användare som är online?", "1037546614", "1");
INSERT INTO tblTodoListComments VALUES("12", "sdl har en threads-implementation som är en wrapper runt pthreads på unix osv, verkar smart för det är portabelt", "1037650027", "1");
INSERT INTO tblTodoListComments VALUES("13", "Lutar åt python", "1037650213", "1");
INSERT INTO tblTodoListComments VALUES("3", "privat forum för guilds till att börja med", "1037907130", "1");
INSERT INTO tblTodoListComments VALUES("44", "martin assigned the task to martin.", "1037923815", "0");
INSERT INTO tblTodoListComments VALUES("45", "martin assigned the task to martin.", "1038410144", "0");
INSERT INTO tblTodoListComments VALUES("45", "fixat, men $db_info[] bör skrivas om lite, \"host\", \"port\" etc istället. stänger inte denna pr förns det är gjort", "1038479471", "1");
INSERT INTO tblTodoListComments VALUES("45", "Status changed from ASSIGNED to CLOSED by martin.", "1038518329", "0");
INSERT INTO tblTodoListComments VALUES("45", "klart", "1038518342", "1");
INSERT INTO tblTodoListComments VALUES("44", "Man kan ändra servernamn/ip, samt lägga till servrar nu.\r\n\r\nIOM att man kan stänga av en server, och att det kan vara lite väl förödande om man \"råkar\" ta bort en server så väljer jag att inte lägga till nån sån funktion..", "1038609690", "1");
INSERT INTO tblTodoListComments VALUES("44", "Status changed from ASSIGNED to CLOSED by martin.", "1038609691", "0");
INSERT INTO tblTodoListComments VALUES("9", "implementerat. vi använder .tar.bz2, klienten hanterar dessa. varje fil innehåller en patch.txt, som är \"whats new\"-fil för den aktuella patchen.", "1038954785", "1");
INSERT INTO tblTodoListComments VALUES("9", "martin assigned the task to martin.", "1038954790", "0");
INSERT INTO tblTodoListComments VALUES("9", "Status changed from ASSIGNED to CLOSED by martin.", "1038954791", "0");
INSERT INTO tblTodoListComments VALUES("10", "Status changed from OPEN to ASSIGNED by martin.<br>(Meaning item is now assigned to martin).", "1038954824", "0");
INSERT INTO tblTodoListComments VALUES("10", "allt implementerat exklusive ordentlig felkontroll. patchern bör försöka igen 3 gånger innan den ger upp nerladdandet. .xdelta-patchar inte implementerade, flyttar .xdelta-featuren till en separat PR", "1038954897", "1");
INSERT INTO tblTodoListComments VALUES("46", "Status changed from OPEN to ASSIGNED by martin.<br>(Meaning item is now assigned to martin).", "1038954909", "0");
INSERT INTO tblTodoListComments VALUES("10", ".xdelta-filer är nu i PR0046\r\n", "1038954926", "1");
INSERT INTO tblTodoListComments VALUES("11", "Status changed from OPEN to ASSIGNED by martin.<br>(Meaning item is now assigned to martin).", "1038955008", "0");
INSERT INTO tblTodoListComments VALUES("11", "avvaktar med detta till senare", "1038955020", "1");
INSERT INTO tblTodoListComments VALUES("47", "Status changed from OPEN to ASSIGNED by martin.<br>(Meaning item is now assigned to martin).", "1038955104", "0");
INSERT INTO tblTodoListComments VALUES("12", "har påbörjat en simpel server som använder SDL_net. kommer även använda SDL_threads.", "1038955272", "1");
INSERT INTO tblTodoListComments VALUES("12", "Status changed from OPEN to ASSIGNED by martin.<br>(Meaning item is now assigned to martin).", "1038955274", "0");
INSERT INTO tblTodoListComments VALUES("12", "Status changed from ASSIGNED to CLOSED by martin.", "1038955279", "0");
INSERT INTO tblTodoListComments VALUES("13", "Status changed from OPEN to ASSIGNED by martin.<br>(Meaning item is now assigned to martin).", "1038955287", "0");
INSERT INTO tblTodoListComments VALUES("13", "har börjat pilla med python. verkar fint, ska få python att kunna kommunicera med databasen direkt, så blir saker och ting enklast möjligt", "1038955346", "1");
INSERT INTO tblTodoListComments VALUES("13", "Status changed from ASSIGNED to CLOSED by martin.", "1038955347", "0");
INSERT INTO tblTodoListComments VALUES("22", "extern databas. embedded mysql lämpar sig inte åt så här stora projekt.", "1038955379", "1");
INSERT INTO tblTodoListComments VALUES("22", "Status changed from OPEN to ASSIGNED by martin.<br>(Meaning item is now assigned to martin).", "1038955382", "0");
INSERT INTO tblTodoListComments VALUES("22", "Status changed from ASSIGNED to CLOSED by martin.", "1038955383", "0");
INSERT INTO tblTodoListComments VALUES("25", "Status changed from OPEN to ASSIGNED by martin.<br>(Meaning item is now assigned to martin).", "1038955409", "0");
INSERT INTO tblTodoListComments VALUES("25", "fast jag tror att min bfont-baserade fontvisare är mycket snabbare, så det blir den till att börja med iallafall", "1038955440", "1");
INSERT INTO tblTodoListComments VALUES("25", "Status changed from ASSIGNED to CLOSED by martin.", "1038955441", "0");
INSERT INTO tblTodoListComments VALUES("24", "Status changed from OPEN to ASSIGNED by martin.<br>(Meaning item is now assigned to martin).", "1038955447", "0");
INSERT INTO tblTodoListComments VALUES("24", "har skrivit ett VÄLDIGT simplet api. fönster går att sätta i fokus framför varandra, resiza och dra runt på skärmen", "1038955474", "1");
INSERT INTO tblTodoListComments VALUES("24", "Status changed from ASSIGNED to CLOSED by martin.", "1038955476", "0");
INSERT INTO tblTodoListComments VALUES("11", "Även möjlighet för servern att tvinga fullständig koll regelbundet", "1039799787", "1");
INSERT INTO tblTodoListComments VALUES("48", "Status changed from OPEN to ASSIGNED by martin.<br>(Meaning item is now assigned to martin).", "1041548856", "0");
INSERT INTO tblTodoListComments VALUES("10", "Status changed from ASSIGNED to CLOSED by martin.", "1041549675", "0");
INSERT INTO tblTodoListComments VALUES("37", "Mailutskick implementerat. Bör dubbelkolla me hotmail så att hotmail inte filtrerar mailen som reklam beroende på mailheadern innan ja stänger den här.", "1041551768", "1");
INSERT INTO tblTodoListComments VALUES("37", "agaton2000@hotmail.com, pwd bajskorv är testkontot.", "1041552050", "1");
INSERT INTO tblTodoListComments VALUES("37", "hotmail blockerar INTE nyhetsbrevsutskicken!", "1041552237", "1");
INSERT INTO tblTodoListComments VALUES("37", "Status changed from ASSIGNED to CLOSED by martin.", "1041552238", "0");
INSERT INTO tblTodoListComments VALUES("49", "Status changed from OPEN to ASSIGNED by martin.<br>(Meaning item is now assigned to martin).", "1041554026", "0");
INSERT INTO tblTodoListComments VALUES("49", "dessutom ska den inte skicka pwd till user, utan en ny \"klicka här för att få ändra pwd\"-sida eftersom pwd e krypterat i databasen. användaren måste åxå fylla i typ nåt mer fält än email för att man ska skicka rätt, t.ex email & födelsedatum", "1043117361", "1");
INSERT INTO tblTodoListComments VALUES("50", "martin assigned the task to martin.", "1043117550", "0");
INSERT INTO tblTodoListComments VALUES("51", "martin assigned the task to martin.", "1043117558", "0");
INSERT INTO tblTodoListComments VALUES("51", "för allmäna forum...", "1043117588", "1");
INSERT INTO tblTodoListComments VALUES("3", "Se även PR0051", "1043117600", "1");
INSERT INTO tblTodoListComments VALUES("3", "Fungerande.", "1043182656", "1");
INSERT INTO tblTodoListComments VALUES("3", "Status changed from ASSIGNED to CLOSED by martin.", "1043182658", "0");


#
# Table structure for table 'tblTodoLists'
#

CREATE TABLE tblTodoLists (
  itemId bigint(20) unsigned NOT NULL auto_increment,
  listId tinyint(4) NOT NULL default '0',
  itemDesc varchar(100) NOT NULL default '',
  itemDetails blob NOT NULL,
  itemStatus tinyint(3) unsigned NOT NULL default '0',
  itemCategory tinyint(3) unsigned NOT NULL default '0',
  timestamp bigint(20) unsigned NOT NULL default '0',
  itemCreator bigint(20) NOT NULL default '0',
  assignedTo bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (itemId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblTodoLists'
#

INSERT INTO tblTodoLists VALUES("1", "0", "Kontokortsvalidering", "Kolla med banken att kortet finns och innehåller pengar", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("2", "0", "Fakturering", "Kolla upp diverse modeller, t.ex PayPal och kreditkortsfakturering", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("3", "0", "Debattforum", " ", "2", "0", "1037315199", "1", "1");
INSERT INTO tblTodoLists VALUES("4", "0", "Lägga till kontokortsuppgifter", "", "2", "0", "1037315199", "1", "1");
INSERT INTO tblTodoLists VALUES("5", "0", "Redigera inställningar", "", "2", "0", "1037315199", "1", "1");
INSERT INTO tblTodoLists VALUES("6", "0", "SSL", "Använd SSL för delar av sajten (inskrivande av kontokort etc)", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("7", "0", "Visa buggrapporter för administratörer", "", "2", "0", "1037315199", "1", "1");
INSERT INTO tblTodoLists VALUES("8", "0", "Kunna lägga till kommentarer på todo-objekten", "", "2", "0", "1037315199", "1", "1");
INSERT INTO tblTodoLists VALUES("9", "1", "Inkrementala patchrevisioner", "Inkrementala patchrevisioner som ska gå att ladda ner separat från mirrors osv", "2", "0", "1037315199", "1", "1");
INSERT INTO tblTodoLists VALUES("10", "2", "Patchrevision", "Berätta för servern vilken patchrevision man har för tillfället och sen inkrementalt ladda ner en patch i taget, varje patch är bz2-packad och innehåller flera filer, filer som slutar med .xdelta är diffs mot tidigare revision, misslyckas en revision att installeras avbryts patchandet.", "2", "0", "1037315199", "1", "1");
INSERT INTO tblTodoLists VALUES("11", "2", "Fullständig filkontroll", "Möjlighet att även göra \"fullständig koll\", som räknar crc32 på alla filer och jämför med lista från servern och begär nya versioner av felaktiga filer", "1", "0", "1037315199", "1", "1");
INSERT INTO tblTodoLists VALUES("12", "4", "Klient/server design", "Trådad server (pthreads?), finns det nåt bra server-skelett att bygga på?", "2", "0", "1037315199", "1", "1");
INSERT INTO tblTodoLists VALUES("13", "4", "Skriptspråk", "Scriptspråk för NPC:s osv (Python, eller PHP?)", "2", "0", "1037315199", "1", "1");
INSERT INTO tblTodoLists VALUES("14", "4", "Dynamisk värld", "Träd kan växa och förstöras (?), GM:s kan förändra världen i realtid", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("15", "4", "Buggrapportering direkt från spelet", "", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("16", "4", "Egen bostad", "Möjlighet att bygga ut/om den", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("17", "4", "Kompislista", "Vem är online och i vilket område befinner sig personen?", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("18", "4", "Instant messaging", "Skicka meddelanden till spelare som är online/offline", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("19", "4", "In-game chat", "Inom zonen, privat, grupp, guild", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("20", "4", "Filformat för banor och 3d-objekt?", "", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("21", "4", "Regelsystem", "Något flexibelt och dynamiskt (som typ baldurs gate motorn som kan anpassas till massa olika rpg:s?)", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("22", "4", "Databas?", "Embedded MySQL 4 eller använda en extern databas?", "2", "0", "1037315199", "1", "1");
INSERT INTO tblTodoLists VALUES("23", "5", "3d-motor", "Kollisionshantering, animerade 3dmodeller, texturanimeringar, partikelmotor, transparenta texturer, vatteneffekter etc (använd nån existerande? crystalspace?)", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("24", "5", "API för fönster osv i spelet", "", "2", "0", "1037315199", "1", "1");
INSERT INTO tblTodoLists VALUES("25", "5", "Font library", "FÅ sdl_ttf2 att funka!!!!!", "2", "0", "1037315199", "1", "1");
INSERT INTO tblTodoLists VALUES("26", "5", "Möjlighet att göra skärmdumpar", "", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("27", "5", "Vädereffekter", "Himmel, regn, åska, dag & natt", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("28", "5", "Interface", "Designat interface (skinmöjligheter?), winamp-ingame?", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("29", "5", "Fordon", "Drakar, hästar osv", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("30", "5", "Levande värld", "Fjärilar och fåglar, gräs som rör sig", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("31", "5", "Dialogrutor \"Assigned quests\", \"Completed quests\"", "", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("32", "5", "Chattrum i klienten (IRC!)", "", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("33", "5", "Konfigureringsskärm", "Ljud, grafik och kontroll", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("34", "5", "Skapa ny karaktär-skärm", "Max X karaktärer per server", "0", "0", "1037315199", "1", "0");
INSERT INTO tblTodoLists VALUES("35", "0", "Karaktärsinfo", "Jag saknar en feature, nämligen att kunna se information om sina karaktärer på webbsajten!", "2", "0", "1037336496", "1", "1");
INSERT INTO tblTodoLists VALUES("36", "0", "Maila devlog", "Devlog-ändringar ska mailas till en mailinglista där alla utvecklare är medlemmar + den som är assignad till aktuell PR.", "1", "0", "1037341463", "1", "1");
INSERT INTO tblTodoLists VALUES("37", "0", "Nyhetsbrev till alla medlemmar", "Ska man kunna skicka ut + nya medlemmar ska kryssa i om dom vill ha nyhetsbrevet eller inte. Dessutom kryssryta om man vill dölja sin mailaddress för andra medlemmar.", "2", "0", "1037341515", "1", "1");
INSERT INTO tblTodoLists VALUES("38", "0", "Personlig sida", "Visa personlig sida, med lite info t.ex Land & Stad, email (om man tillåter)", "2", "0", "1037341591", "1", "1");
INSERT INTO tblTodoLists VALUES("39", "0", "Lista alla från samma Land, tidszon, Stad", "länkbart från personliga sidan", "2", "0", "1037372534", "1", "1");
INSERT INTO tblTodoLists VALUES("40", "0", "Lookup user i adminpanelen", "som Lookup PR, på id eller namn, fast man kommer till show_user.php\r\noch där visas (för administratören) all information om användaren (även möjlighet att ändra info??)", "2", "0", "1037388912", "1", "1");
INSERT INTO tblTodoLists VALUES("41", "0", "Instant messaging", "Jag vill kunna skicka meddelanden till andra på sajten/i spelet", "1", "0", "1037390137", "8", "1");
INSERT INTO tblTodoLists VALUES("42", "0", "Content-koder", "Typ som pinkoder för comviq, som ger användaren 1/3/6 månaders access. Gör admingränssnitt för att skapa nya koder, administrera dessa samt användargränssnitt för att skriva in koder", "2", "0", "1037392732", "1", "1");
INSERT INTO tblTodoLists VALUES("43", "4", "guilds", "skapa, joina, lämna, privat guildchat i spelet", "0", "0", "1037907109", "1", "0");
INSERT INTO tblTodoLists VALUES("44", "0", "Kunna ändra servernamn&ip samt lägga till/ta bort servrar", "..", "2", "0", "1037923811", "1", "1");
INSERT INTO tblTodoLists VALUES("45", "0", "admin screen - site information saknar dbserver port", "ja..", "2", "0", "1038410140", "1", "1");
INSERT INTO tblTodoLists VALUES("46", "2", "hantera .xdelta-filer", "", "1", "0", "1038954906", "1", "1");
INSERT INTO tblTodoLists VALUES("47", "1", "gränssnitt för att skapa nya patchar", "någon form av gränssnitt för att enkelt få fram vilka filer som skiljer sig från förra revisionen, samt skapa .tar.bz2 av dessa, flytta till servern och uppdatera latest.rev på patcher-servern", "1", "0", "1038955098", "1", "1");
INSERT INTO tblTodoLists VALUES("48", "0", "Klura ut land och timezone från host address", "De flesta land kluras ut automatiskt fast bara Sveriges tidszon kluras ut automatiskt. Bara att fylla i timezoneId i tblCountries, observera att många länder har mer än en tidszon, så vi kan bara tillämpa detta på vissa länder (antagligen samtliga i europa iaf), detta gör registreringsprocessen smidigare.", "1", "1", "1041548852", "1", "1");
INSERT INTO tblTodoLists VALUES("49", "0", "lost_password.php saknas", "Skapa, den är klickbar från login.php", "1", "0", "1041554021", "1", "1");
INSERT INTO tblTodoLists VALUES("50", "0", "Skriv in år-månad-dag i personinfo", "Ska användas för att:\r\n\r\n* Verifiera användare vid bortglömt lösenord\r\n* Kunna ge bort en gratismånad till 20 födelsedagsbarn varje dag eller nåt\r\n* Kunna se statistik över kön/ålder/land", "1", "0", "1043117475", "1", "1");
INSERT INTO tblTodoLists VALUES("51", "0", "Skriv in \"Forum-signatur\"", "Användaren ska kunna ange en forum-signatur från My settings, som ska användas i forumen istället för username för att slippa avslöja username.", "1", "0", "1043117545", "1", "1");


#
# Table structure for table 'tblUserAddress'
#

CREATE TABLE tblUserAddress (
  userId bigint(20) unsigned NOT NULL default '0',
  timezone tinyint(3) unsigned NOT NULL default '0',
  realName varchar(50) NOT NULL default '',
  gender tinyint(3) unsigned NOT NULL default '0',
  userMail varchar(50) NOT NULL default '',
  userMailSecret tinyint(3) unsigned NOT NULL default '0',
  adrPhoneHome varchar(20) NOT NULL default '',
  adrCountry tinyint(3) unsigned NOT NULL default '0',
  adrCity varchar(50) NOT NULL default '',
  adrZipcode varchar(10) NOT NULL default '',
  adrStreet varchar(60) NOT NULL default '',
  newsletter tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (userId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblUserAddress'
#

INSERT INTO tblUserAddress VALUES("1", "13", "Martin Lindhe", "0", "martin@inthc.net", "0", "+46739903306", "171", "Skärholmen", "127 32", "Bredängs Allé 4, 3tr", "1");
INSERT INTO tblUserAddress VALUES("10", "13", "Agaton 2000", "0", "agaton@inthc.net", "0", "123456", "171", "Stockholm", "NAE", "DERU", "1");
INSERT INTO tblUserAddress VALUES("11", "13", "Tuffmattias", "0", "phobia@bigfoot.com", "1", "0737857132", "171", "Linköping", "58439", "Rydsvägen 372b", "1");


#
# Table structure for table 'tblUserBilling'
#

CREATE TABLE tblUserBilling (
  userId bigint(20) unsigned NOT NULL default '0',
  ccNumber bigint(20) unsigned NOT NULL default '0',
  ccExpireMonth tinyint(2) unsigned NOT NULL default '0',
  ccExpireYear int(10) unsigned NOT NULL default '0',
  ccExtraCode varchar(11) NOT NULL default '',
  ccOwnerName varchar(60) NOT NULL default '',
  PRIMARY KEY  (userId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblUserBilling'
#

INSERT INTO tblUserBilling VALUES("1", "4581097744640645", "1", "2005", "225", "LINDHE MARTIN JOHANNIS");


#
# Table structure for table 'tblUserStats'
#

CREATE TABLE tblUserStats (
  userId bigint(20) unsigned NOT NULL default '0',
  timeCreated bigint(20) NOT NULL default '0',
  timeActivated bigint(20) unsigned NOT NULL default '0',
  timeExpires bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (userId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblUserStats'
#

INSERT INTO tblUserStats VALUES("1", "1019426974", "1041555559", "1091860711");
INSERT INTO tblUserStats VALUES("10", "1037391768", "1041551482", "1045322029");
INSERT INTO tblUserStats VALUES("11", "1038409236", "1038409245", "1053964127");


#
# Table structure for table 'tblUsers'
#

CREATE TABLE tblUsers (
  userId bigint(20) unsigned NOT NULL auto_increment,
  userName varchar(20) NOT NULL default '',
  userPass varchar(32) NOT NULL default '',
  userType tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (userId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblUsers'
#

INSERT INTO tblUsers VALUES("1", "martin", "1a4a3fcd0de382f7be304d0df4e27617", "1");
INSERT INTO tblUsers VALUES("10", "agaton2000", "1a4a3fcd0de382f7be304d0df4e27617", "0");
INSERT INTO tblUsers VALUES("11", "mattias800", "cf3d9f7ffefd60855d531f6a8267bea8", "1");
