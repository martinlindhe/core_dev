# MySQL-Front Dump 2.5
#
# Host: localhost   Database: online_game
# --------------------------------------------------------
# Server version 3.23.56


#
# Table structure for table 'tblCharacterAbilityScores'
#

CREATE TABLE tblCharacterAbilityScores (
  charId bigint(20) unsigned NOT NULL default '0',
  charSTR tinyint(3) unsigned NOT NULL default '0',
  charDEX tinyint(3) unsigned NOT NULL default '0',
  charCON tinyint(3) unsigned NOT NULL default '0',
  charINT tinyint(3) unsigned NOT NULL default '0',
  charWIS tinyint(3) unsigned NOT NULL default '0',
  charCHA tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (charId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblCharacterAbilityScores'
#

INSERT INTO tblCharacterAbilityScores VALUES("1", "11", "12", "13", "14", "15", "16");
INSERT INTO tblCharacterAbilityScores VALUES("2", "18", "18", "18", "18", "18", "18");
INSERT INTO tblCharacterAbilityScores VALUES("3", "9", "9", "9", "9", "9", "9");
INSERT INTO tblCharacterAbilityScores VALUES("14", "5", "6", "7", "8", "9", "10");


#
# Table structure for table 'tblCharacterCoords'
#

CREATE TABLE tblCharacterCoords (
  charId bigint(20) NOT NULL default '0',
  zone tinyint(3) unsigned NOT NULL default '0',
  x float NOT NULL default '0',
  y float NOT NULL default '0',
  z float NOT NULL default '0',
  PRIMARY KEY  (charId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblCharacterCoords'
#

INSERT INTO tblCharacterCoords VALUES("1", "2", "1.2", "2", "3");
INSERT INTO tblCharacterCoords VALUES("2", "2", "2", "3", "4");
INSERT INTO tblCharacterCoords VALUES("3", "2", "3", "4", "5");
INSERT INTO tblCharacterCoords VALUES("14", "2", "14", "15", "16");


#
# Table structure for table 'tblCharacterSessions'
#

CREATE TABLE tblCharacterSessions (
  idx bigint(20) unsigned NOT NULL auto_increment,
  charId bigint(20) unsigned NOT NULL default '0',
  loggedin bigint(20) unsigned NOT NULL default '0',
  loggedout bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (idx)
) TYPE=MyISAM COMMENT='Innehåller logintimestamp samt hur länge man spelade';



#
# Dumping data for table 'tblCharacterSessions'
#

INSERT INTO tblCharacterSessions VALUES("1", "1", "1048327164", "1048327346");
INSERT INTO tblCharacterSessions VALUES("2", "2", "1048327169", "1048327346");
INSERT INTO tblCharacterSessions VALUES("3", "1", "1048328807", "1048328813");
INSERT INTO tblCharacterSessions VALUES("4", "2", "1048329613", "1048329613");
INSERT INTO tblCharacterSessions VALUES("5", "1", "1048329658", "0");
INSERT INTO tblCharacterSessions VALUES("6", "2", "1048329690", "1048329692");
INSERT INTO tblCharacterSessions VALUES("7", "2", "1048329858", "1048329866");
INSERT INTO tblCharacterSessions VALUES("8", "2", "1048329976", "1048329978");
INSERT INTO tblCharacterSessions VALUES("9", "2", "1048330063", "1048330071");
INSERT INTO tblCharacterSessions VALUES("10", "2", "1048330383", "1048330391");
INSERT INTO tblCharacterSessions VALUES("11", "2", "1048330537", "1048330540");
INSERT INTO tblCharacterSessions VALUES("12", "2", "1048330550", "1048330560");
INSERT INTO tblCharacterSessions VALUES("13", "2", "1048330786", "1048330792");
INSERT INTO tblCharacterSessions VALUES("14", "2", "1048330804", "1048330814");
INSERT INTO tblCharacterSessions VALUES("15", "2", "1048330825", "1048330845");
INSERT INTO tblCharacterSessions VALUES("16", "2", "1048330966", "1048330977");
INSERT INTO tblCharacterSessions VALUES("17", "1", "1048343014", "1048343017");
INSERT INTO tblCharacterSessions VALUES("18", "1", "1048343161", "0");
INSERT INTO tblCharacterSessions VALUES("19", "2", "1048343309", "1048343311");
INSERT INTO tblCharacterSessions VALUES("20", "2", "1048343512", "1048343512");
INSERT INTO tblCharacterSessions VALUES("21", "2", "1048343525", "1048343537");
INSERT INTO tblCharacterSessions VALUES("22", "2", "1048343638", "1048343639");
INSERT INTO tblCharacterSessions VALUES("23", "2", "1048343659", "1048343659");
INSERT INTO tblCharacterSessions VALUES("24", "1", "1048343668", "0");
INSERT INTO tblCharacterSessions VALUES("25", "2", "1048343675", "1048343676");
INSERT INTO tblCharacterSessions VALUES("26", "1", "1048343686", "1048343686");
INSERT INTO tblCharacterSessions VALUES("27", "2", "1048343718", "1048343721");
INSERT INTO tblCharacterSessions VALUES("28", "2", "1048343735", "1048343735");
INSERT INTO tblCharacterSessions VALUES("29", "1", "1048343751", "1048343751");
INSERT INTO tblCharacterSessions VALUES("30", "1", "1048343753", "1048343753");
INSERT INTO tblCharacterSessions VALUES("31", "3", "1048343754", "1048343754");
INSERT INTO tblCharacterSessions VALUES("32", "14", "1048343805", "1048343809");
INSERT INTO tblCharacterSessions VALUES("33", "1", "1048343815", "1048343815");
INSERT INTO tblCharacterSessions VALUES("34", "1", "1048344179", "1048344180");
INSERT INTO tblCharacterSessions VALUES("35", "2", "1048344182", "1048344182");
INSERT INTO tblCharacterSessions VALUES("36", "1", "1048344193", "1048344193");
INSERT INTO tblCharacterSessions VALUES("37", "1", "1048361850", "1048361895");
INSERT INTO tblCharacterSessions VALUES("38", "2", "1048361860", "1048361863");
INSERT INTO tblCharacterSessions VALUES("39", "2", "1048361865", "1048361865");
INSERT INTO tblCharacterSessions VALUES("40", "14", "1048361871", "1048361871");
INSERT INTO tblCharacterSessions VALUES("41", "2", "1048361881", "1048361881");
INSERT INTO tblCharacterSessions VALUES("42", "1", "1048361912", "1048361914");
INSERT INTO tblCharacterSessions VALUES("43", "1", "1048361919", "1048361919");
INSERT INTO tblCharacterSessions VALUES("44", "1", "1048362025", "1048362027");
INSERT INTO tblCharacterSessions VALUES("45", "1", "1048362028", "1048362028");
INSERT INTO tblCharacterSessions VALUES("46", "1", "1048362068", "1048362069");
INSERT INTO tblCharacterSessions VALUES("47", "1", "1048362070", "1048362070");
INSERT INTO tblCharacterSessions VALUES("48", "1", "1048362133", "1048362134");
INSERT INTO tblCharacterSessions VALUES("49", "1", "1048362136", "1048362136");
INSERT INTO tblCharacterSessions VALUES("50", "1", "1048363825", "1048363826");
INSERT INTO tblCharacterSessions VALUES("51", "2", "1048363828", "1048363828");
INSERT INTO tblCharacterSessions VALUES("52", "3", "1048363830", "1048363830");
INSERT INTO tblCharacterSessions VALUES("53", "1", "1048363831", "1048363831");
INSERT INTO tblCharacterSessions VALUES("54", "1", "1048363910", "1048363912");
INSERT INTO tblCharacterSessions VALUES("55", "1", "1048364099", "1048364099");
INSERT INTO tblCharacterSessions VALUES("56", "1", "1048364137", "0");
INSERT INTO tblCharacterSessions VALUES("57", "2", "1048364142", "0");
INSERT INTO tblCharacterSessions VALUES("58", "3", "1048364148", "0");
INSERT INTO tblCharacterSessions VALUES("59", "1", "1048364281", "0");
INSERT INTO tblCharacterSessions VALUES("60", "2", "1048364283", "0");
INSERT INTO tblCharacterSessions VALUES("61", "3", "1048364286", "0");
INSERT INTO tblCharacterSessions VALUES("62", "1", "1048364780", "0");
INSERT INTO tblCharacterSessions VALUES("63", "2", "1048364786", "0");
INSERT INTO tblCharacterSessions VALUES("64", "3", "1048364788", "0");
INSERT INTO tblCharacterSessions VALUES("65", "1", "1048365296", "1048365297");
INSERT INTO tblCharacterSessions VALUES("66", "1", "1048365298", "1048365298");
INSERT INTO tblCharacterSessions VALUES("67", "1", "1048365360", "1048365361");
INSERT INTO tblCharacterSessions VALUES("68", "1", "1048365362", "1048365362");
INSERT INTO tblCharacterSessions VALUES("69", "1", "1048365368", "1048365386");
INSERT INTO tblCharacterSessions VALUES("70", "2", "1048365370", "1048365372");
INSERT INTO tblCharacterSessions VALUES("71", "1", "1048365393", "1048365394");
INSERT INTO tblCharacterSessions VALUES("72", "2", "1048365395", "1048365395");
INSERT INTO tblCharacterSessions VALUES("73", "1", "1048365542", "1048365543");
INSERT INTO tblCharacterSessions VALUES("74", "2", "1048365544", "1048365544");
INSERT INTO tblCharacterSessions VALUES("75", "1", "1048365551", "1048365551");
INSERT INTO tblCharacterSessions VALUES("76", "1", "1048365554", "1048365554");
INSERT INTO tblCharacterSessions VALUES("77", "1", "1048365557", "1048365558");
INSERT INTO tblCharacterSessions VALUES("78", "1", "1048365560", "1048365560");
INSERT INTO tblCharacterSessions VALUES("79", "1", "1048365625", "1048365626");
INSERT INTO tblCharacterSessions VALUES("80", "1", "1048365628", "1048365628");
INSERT INTO tblCharacterSessions VALUES("81", "1", "1048365665", "1048365665");
INSERT INTO tblCharacterSessions VALUES("82", "2", "1048365666", "1048365666");
INSERT INTO tblCharacterSessions VALUES("83", "2", "1048366244", "0");
INSERT INTO tblCharacterSessions VALUES("84", "1", "1048366252", "0");
INSERT INTO tblCharacterSessions VALUES("85", "1", "1048366366", "0");
INSERT INTO tblCharacterSessions VALUES("86", "3", "1048366386", "0");
INSERT INTO tblCharacterSessions VALUES("87", "1", "1048367489", "1048367490");
INSERT INTO tblCharacterSessions VALUES("88", "1", "1048367744", "1048367745");
INSERT INTO tblCharacterSessions VALUES("89", "2", "1048367746", "1048367746");
INSERT INTO tblCharacterSessions VALUES("90", "1", "1048367748", "1048367748");
INSERT INTO tblCharacterSessions VALUES("91", "1", "1048367755", "1048367755");
INSERT INTO tblCharacterSessions VALUES("92", "1", "1048367756", "1048367756");
INSERT INTO tblCharacterSessions VALUES("93", "1", "1048368068", "1048368068");
INSERT INTO tblCharacterSessions VALUES("94", "1", "1048371771", "1048371772");
INSERT INTO tblCharacterSessions VALUES("95", "1", "1048371773", "1048371773");
INSERT INTO tblCharacterSessions VALUES("96", "2", "1048371775", "1048371775");
INSERT INTO tblCharacterSessions VALUES("97", "1", "1048371776", "1048371776");
INSERT INTO tblCharacterSessions VALUES("98", "1", "1048371777", "1048371777");
INSERT INTO tblCharacterSessions VALUES("99", "1", "1048371778", "1048371778");
INSERT INTO tblCharacterSessions VALUES("100", "1", "1048371779", "1048371779");
INSERT INTO tblCharacterSessions VALUES("101", "1", "1048371782", "1048371782");
INSERT INTO tblCharacterSessions VALUES("102", "1", "1048530960", "1048530984");
INSERT INTO tblCharacterSessions VALUES("103", "1", "1048531152", "1048531161");
INSERT INTO tblCharacterSessions VALUES("104", "1", "1048531197", "1048531199");
INSERT INTO tblCharacterSessions VALUES("105", "2", "1048531656", "1048531727");
INSERT INTO tblCharacterSessions VALUES("106", "1", "1048531727", "0");
INSERT INTO tblCharacterSessions VALUES("107", "1", "1048531768", "0");
INSERT INTO tblCharacterSessions VALUES("108", "1", "1048531808", "0");
INSERT INTO tblCharacterSessions VALUES("109", "1", "1048532001", "0");
INSERT INTO tblCharacterSessions VALUES("110", "1", "1048532411", "0");
INSERT INTO tblCharacterSessions VALUES("111", "2", "1048532419", "0");
INSERT INTO tblCharacterSessions VALUES("112", "3", "1048532426", "0");
INSERT INTO tblCharacterSessions VALUES("113", "14", "1048532605", "1048532642");
INSERT INTO tblCharacterSessions VALUES("114", "14", "1048532768", "0");
INSERT INTO tblCharacterSessions VALUES("115", "3", "1048532776", "0");
INSERT INTO tblCharacterSessions VALUES("116", "1", "1048533210", "0");
INSERT INTO tblCharacterSessions VALUES("117", "2", "1048533309", "0");
INSERT INTO tblCharacterSessions VALUES("118", "1", "1048533531", "0");
INSERT INTO tblCharacterSessions VALUES("119", "2", "1048533540", "0");
INSERT INTO tblCharacterSessions VALUES("120", "2", "1048533612", "0");
INSERT INTO tblCharacterSessions VALUES("121", "1", "1048533614", "0");
INSERT INTO tblCharacterSessions VALUES("122", "1", "1048533719", "0");
INSERT INTO tblCharacterSessions VALUES("123", "2", "1048533723", "0");
INSERT INTO tblCharacterSessions VALUES("124", "2", "1048533881", "1048533886");
INSERT INTO tblCharacterSessions VALUES("125", "2", "1048533909", "0");
INSERT INTO tblCharacterSessions VALUES("126", "1", "1048533911", "0");
INSERT INTO tblCharacterSessions VALUES("127", "2", "1048533971", "0");
INSERT INTO tblCharacterSessions VALUES("128", "1", "1048533973", "0");
INSERT INTO tblCharacterSessions VALUES("129", "1", "1048534157", "0");
INSERT INTO tblCharacterSessions VALUES("130", "2", "1048534189", "0");
INSERT INTO tblCharacterSessions VALUES("131", "1", "1048534372", "0");
INSERT INTO tblCharacterSessions VALUES("132", "2", "1048534375", "0");
INSERT INTO tblCharacterSessions VALUES("133", "1", "1048534592", "0");
INSERT INTO tblCharacterSessions VALUES("134", "2", "1048534594", "0");
INSERT INTO tblCharacterSessions VALUES("135", "1", "1048534679", "0");
INSERT INTO tblCharacterSessions VALUES("136", "2", "1048534685", "0");
INSERT INTO tblCharacterSessions VALUES("137", "1", "1048619128", "1048619154");
INSERT INTO tblCharacterSessions VALUES("138", "1", "1048619162", "1048619217");
INSERT INTO tblCharacterSessions VALUES("139", "1", "1048619407", "1048619408");
INSERT INTO tblCharacterSessions VALUES("140", "1", "1048624801", "1048624946");
INSERT INTO tblCharacterSessions VALUES("141", "1", "1048625012", "0");
INSERT INTO tblCharacterSessions VALUES("142", "1", "1048625088", "0");
INSERT INTO tblCharacterSessions VALUES("143", "1", "1048625396", "1048625410");
INSERT INTO tblCharacterSessions VALUES("144", "2", "1048625405", "1048625410");
INSERT INTO tblCharacterSessions VALUES("145", "1", "1048625734", "1048625741");
INSERT INTO tblCharacterSessions VALUES("146", "1", "1048626035", "1048626066");
INSERT INTO tblCharacterSessions VALUES("147", "1", "1048626171", "1048626174");
INSERT INTO tblCharacterSessions VALUES("148", "1", "1048626365", "1048626378");
INSERT INTO tblCharacterSessions VALUES("149", "1", "1048626412", "1048626438");
INSERT INTO tblCharacterSessions VALUES("150", "1", "1048626679", "1048626707");
INSERT INTO tblCharacterSessions VALUES("151", "2", "1048626705", "1048626738");
INSERT INTO tblCharacterSessions VALUES("152", "2", "1048627197", "1048627378");
INSERT INTO tblCharacterSessions VALUES("153", "1", "1048627222", "1048627222");
INSERT INTO tblCharacterSessions VALUES("154", "1", "1048627397", "1048627466");
INSERT INTO tblCharacterSessions VALUES("155", "2", "1048627403", "1048627464");
INSERT INTO tblCharacterSessions VALUES("156", "1", "1048627468", "1048627468");
INSERT INTO tblCharacterSessions VALUES("157", "1", "1048627477", "1048627584");
INSERT INTO tblCharacterSessions VALUES("158", "2", "1048627482", "1048627586");
INSERT INTO tblCharacterSessions VALUES("159", "2", "1048627600", "1048627600");
INSERT INTO tblCharacterSessions VALUES("160", "2", "1048627604", "1048627776");
INSERT INTO tblCharacterSessions VALUES("161", "1", "1048627606", "1048627776");
INSERT INTO tblCharacterSessions VALUES("162", "1", "1048627791", "1048628006");
INSERT INTO tblCharacterSessions VALUES("163", "2", "1048627794", "1048628006");
INSERT INTO tblCharacterSessions VALUES("164", "1", "1048628025", "1048628072");
INSERT INTO tblCharacterSessions VALUES("165", "2", "1048628028", "1048628072");
INSERT INTO tblCharacterSessions VALUES("166", "3", "1048628038", "1048628072");
INSERT INTO tblCharacterSessions VALUES("167", "14", "1048628052", "1048628072");
INSERT INTO tblCharacterSessions VALUES("168", "1", "1048628364", "1048628373");
INSERT INTO tblCharacterSessions VALUES("169", "2", "1048628366", "1048628373");
INSERT INTO tblCharacterSessions VALUES("170", "3", "1048628368", "1048628373");
INSERT INTO tblCharacterSessions VALUES("171", "14", "1048628370", "1048628373");
INSERT INTO tblCharacterSessions VALUES("172", "1", "1048628937", "1048628944");
INSERT INTO tblCharacterSessions VALUES("173", "1", "1048628962", "1048629094");
INSERT INTO tblCharacterSessions VALUES("174", "1", "1048629099", "1048629100");
INSERT INTO tblCharacterSessions VALUES("175", "1", "1048629111", "1048629129");
INSERT INTO tblCharacterSessions VALUES("176", "2", "1048629114", "1048629129");
INSERT INTO tblCharacterSessions VALUES("177", "2", "1048629793", "1048629801");
INSERT INTO tblCharacterSessions VALUES("178", "2", "1048629950", "1048629957");
INSERT INTO tblCharacterSessions VALUES("179", "1", "1048630201", "1048630255");
INSERT INTO tblCharacterSessions VALUES("180", "2", "1048630220", "1048630255");
INSERT INTO tblCharacterSessions VALUES("181", "3", "1048630223", "1048630255");
INSERT INTO tblCharacterSessions VALUES("182", "14", "1048630225", "1048630255");


#
# Table structure for table 'tblCharacters'
#

CREATE TABLE tblCharacters (
  charId bigint(20) unsigned NOT NULL default '0',
  userId bigint(20) unsigned NOT NULL default '0',
  charName varchar(30) NOT NULL default '',
  charGender tinyint(1) unsigned NOT NULL default '0',
  charRace tinyint(1) unsigned NOT NULL default '0',
  timeCreated bigint(20) unsigned NOT NULL default '0',
  timeLastPlayed bigint(20) unsigned NOT NULL default '0',
  timePlayed bigint(20) unsigned NOT NULL default '0',
  playedCount bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (charId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblCharacters'
#

INSERT INTO tblCharacters VALUES("1", "1", "Raija", "0", "1", "1037389948", "1037389948", "73431", "6");
INSERT INTO tblCharacters VALUES("2", "1", "Volverine", "1", "0", "1037389948", "1037389948", "21434", "3");
INSERT INTO tblCharacters VALUES("3", "1", "Agaton", "0", "0", "1037389948", "1037389948", "67329", "9");
INSERT INTO tblCharacters VALUES("14", "1", "Bajsmannen", "1", "1", "1038146127", "1038146127", "361", "1");


#
# Table structure for table 'tblGameServers'
#

CREATE TABLE tblGameServers (
  serverId tinyint(1) unsigned NOT NULL auto_increment,
  serverIP varchar(15) NOT NULL default '',
  serverName varchar(25) NOT NULL default '',
  serverOnline tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (serverId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblGameServers'
#

INSERT INTO tblGameServers VALUES("1", "217.215.191.103", "Test server", "1");
INSERT INTO tblGameServers VALUES("2", "255.255.255.255", "Fake bogus server", "0");
INSERT INTO tblGameServers VALUES("3", "1.1.1.1", "Another bogus server", "0");


#
# Table structure for table 'tblGuildMembers'
#

CREATE TABLE tblGuildMembers (
  charId bigint(20) unsigned NOT NULL default '0',
  guildId bigint(20) unsigned NOT NULL default '0',
  timeJoinedGuild bigint(20) unsigned NOT NULL default '0',
  guildMemberType tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (charId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblGuildMembers'
#

INSERT INTO tblGuildMembers VALUES("1", "1", "1037489008", "2");


#
# Table structure for table 'tblGuilds'
#

CREATE TABLE tblGuilds (
  guildId bigint(20) unsigned NOT NULL auto_increment,
  guildName varchar(40) NOT NULL default '',
  creatorId bigint(20) unsigned NOT NULL default '0',
  timestamp bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (guildId)
) TYPE=MyISAM;



#
# Dumping data for table 'tblGuilds'
#

INSERT INTO tblGuilds VALUES("1", "Lords of Tomorrow", "1", "1037399948");
