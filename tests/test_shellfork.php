<?php

// test async command

echo "start: ".microtime(true)."\n";

// sox -V "$f" --type mp3 --comment "" "$out";

$c = '7z a -r -mx=9 /tmp/crap_test.7z /ast-root/recordings >/dev/null 2>/dev/null &';  // the "&" spawns this command in a new process

shell_exec($c);

echo "after exec: ".microtime(true)."\n";



echo "end: ".microtime(true)."\n";

?>
