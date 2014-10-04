<?php

//STATUS: early wip!

//TODO: ability to add new word
//TODO: ability to remove a word

namespace cd;

$session->requireSuperAdmin();

echo '<h1>Reserved words</h1>';

$list = ReservedWord::getAll(RESERVED_USERNAME);

d($list);


?>

