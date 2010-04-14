<?php

require_once('config.php');
$session->requireLoggedIn();

require('design_head.php');

$text = '';
if (!empty($_POST['text'])) {
    $text = $_POST['text'];

    guessLanguage($text);
}
?>
<h2>Guess language</h2>

Enter some text and see if the program can guess the language that the text were written in.

<form method="post" action="">
    <textarea name="text" cols="70" rows="20"></textarea><br/>
    <input type="submit" class="button" value="Submit"/>
</form>
<?php

require('design_foot.php');
?>
