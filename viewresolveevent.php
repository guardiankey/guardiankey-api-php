<?php

require_once("guardiankey.class.php");

$GK = new guardiankey(null);

$eventid="xxx";
$token="xxx";
print_r($GK->getEvent($eventid,$token));
print_r($GK->resolveEvent($eventid,$token,'GOOD'));
exit;

if (@$_SERVER['SERVER_NAME']) {
    echo "<h2>Any title</h2>
    <p>Please login</p>
    <form action=# method=post>
    <p>Username:<input type=text name=user></p>
    <p>Password:<input type=password name=password></p>
    <input type=submit value=submit>
    </form>";
    if ($_POST) {
        $GK->sendevent($username);
        echo "<h2>Any data</h2>";
    }
}else{
    echo "Please an example username: ";
    $handle = fopen("php://stdin", "r");
    $username = trim(fgets($handle));
//     $GK->sendevent($username);
    echo $GK->checkaccess($username);
    echo "\n event sent!";
}



?>

