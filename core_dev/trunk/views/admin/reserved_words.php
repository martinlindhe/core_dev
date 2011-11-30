<?php

//STATUS: early wip!

//TODO: ability to add new word
//TODO: ability to remove a word

$session->requireSuperAdmin();

require_once('ReservedWord.php');

echo '<h1>Reserved words</h1>';

$list = ReservedWord::getAll(RESERVED_USERNAME);

d($list);


?>

