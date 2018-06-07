# GuardianKey-api-php

INSTALATION AND USAGE
 
  First you need do a basic register. Yon can run register.php file or access:
  https://guardiankey.io/register

  You need include guardiankey.class.php in your code (eg: In post-auth page),
and adjust the variables in accord with your application. 

Exampla of usage:

------------------------------
\<?php

\echo "\<h2>Any title\</h2>
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
---------------------------------
