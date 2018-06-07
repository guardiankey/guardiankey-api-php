<?php

echo "<h2>Any title</h2>
<p>Please login</p>
<form action=# method=post>
<p>Username:<input type=text name=user></p>
<p>Password:<input type=password name=password></p>
<input type=submit value=submit>
</form>";


require_once("guardiankey.class.php");
$GK = new guardiankey();


if ($_POST) {

$GK->send_event($username);

echo "<h2>Any data</h2>";
}
?>

