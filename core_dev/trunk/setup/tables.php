<?php

// 1. hårdkoda en beskrivning av en tabell i php-kod (tblUsers)

//  XXX TODO LATER: gör ett script som spottar ur sig php-kod som beskriver hur en databas ser ut, ett table itaget


$tblUsers = array(
'xx'
);


$tblComments = array(
'commentId'      => array('bigint:20:unsigned','null:NO','key:PRI','extra:auto_increment'),
'commentType'    => array('tinyint:3:unsigned','null:NO'),
'commentText'    => array('text'),
'commentPrivate' => array('tinyint:3:unsigned','null:NO'),
'timeCreated'    => array('datetime','null:YES'),
'timeDeleted'    => array('datetime','null:YES'),
'deletedBy'      => array('bigint:20:unsigned','null:NO'),
'ownerId'        => array('bigint:20:unsigned','null:NO'),
'userId'         => array('bigint:20:unsigned','null:NO'),
'userIP'         => array('bigint:20:unsigned','null:NO')
);

?>
