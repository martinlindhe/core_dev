<?php

namespace cd;

require_once('Feedback.php');

$session->requireSuperAdmin();

echo '<h1>core_dev admin</h1>';

echo ahref('a/moderation', 'Moderation queue').'<br/>';

echo ahref('a/feedback',   'Feedback ('.Feedback::getUnansweredCount().')').'<br/>';

echo ahref('a/blogs/overview',  'Blogs').'<br/>';

echo '<br/>';
echo ahref('a/users',          'Users').'<br/>';
echo ahref('a/usergroups',     'User groups').'<br/>';
echo ahref('a/files/list',     'Files').'<br/>';
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
echo ahref('a/geoip/version',  'GeoIP information').'<br/>';
echo ahref('a/hashes',         'Available hash functions').'<br/>';
echo '<br/>';


echo ahref('a/webshop/category/0',  'Webshop admin').'<br/>';

?>
