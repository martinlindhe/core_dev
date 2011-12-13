<?php

$session->requireSuperAdmin();

echo '<h1>core_dev admin</h1>';
echo ahref('a/moderation', 'Moderation queue').'<br/>';
echo ahref('a/feedback',   'User feedback').'<br/>';
echo '<br/>';
echo ahref('a/users',          'Users').'<br/>';
echo ahref('a/usergroups',     'User groups').'<br/>';
echo ahref('a/files',          'Files').'<br/>';
echo ahref('a/polls',          'Polls').'<br/>';
echo ahref('a/chatroom/list',  'Chatrooms').'<br/>';
echo ahref('a/userdata/list',  'Userdata types').'<br/>';
echo ahref('a/reserved_words', 'Reserved words').'<br/>';
echo ahref('a/faq',            'FAQ').'<br/>';
echo '<br/>';
echo ahref('a/phpinfo',        'phpinfo()').'<br/>';
echo ahref('a/compatiblity',   'Compatibility check').'<br/>';
echo ahref('t/timezones',      'Time zones').'<br/>';
echo ahref('t/currency',       'Currencies').'<br/>';
echo '<br/>';
echo ahref('a/mysql_config',   'MySQL information').'<br/>';
echo '<br/>';

?>