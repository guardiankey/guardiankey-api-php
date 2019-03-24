<?php
/* Dependencies: php-curl */

require_once("guardiankey.class.php");


if (@$_SERVER['SERVER_NAME']) {
    if ($_POST) {
        $email = $_POST['email'];
    } else {
        echo "<form action=# method=post>
		<p>Please enter admin email:</p>
		<input type=email name=email><br>
		<input type=submit value=submit>
	</form>";
        die();
    }
} else {
    echo "Please enter admin email: ";
    $handle = fopen("php://stdin", "r");
    $email = trim(fgets($handle));
}

$GK = new guardiankey();

try {
    $GKReturn=$GK->register($email);
} catch (Exception $e) {
    echo $e->getMessage()."\n";
    exit;
}

if (@$_SERVER['SERVER_NAME']) {
    echo "<pre>";
}
echo 'Please add in your GuardianKey configuration:
					$GKconfig = array(
					\'email\' => "' . $GKReturn["email"] . '",
					\'agentid\' => "' . $GKReturn["agentid"]  . '",
					\'key\' => "' . $GKReturn["key"]  . '",
					\'iv\' => "' . $GKReturn["iv"]  . '",
					\'orgid\' => "' . $GKReturn["orgid"]  . '",
					\'authgroupid\' => "' . $GKReturn["groupid"]  . '",
					\'service\' => "MyServiceName",
					\'reverse\' => "True",
					);'."\n";

