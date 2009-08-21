<?php

require_once('../core/class.Render_Table.php');

$xls = new Render_Table_XLS();
$xls->heading(array('heading 1', 'heading 2', 'heading 3', 'heading 4'));

$xls->add('beep "hello", hi there');
$xls->add('  some text');
$xls->add('some text, with a comma');
$xls->add("text\nmore text");

$xls->add(3.141592653589793);
$xls->add(2.14);
$xls->add(700000000);
$xls->add(-47.44499998);

$xls->write('aaa');	//FIXME kunna speca output format här eller i render() istället !

?>
