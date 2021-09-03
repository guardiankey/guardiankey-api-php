<?php

/* This is a example to send anon users to Guardiankey. Need configure keys in GuadianKey Panel->click on padlock icon */

require_once("guardiankey.class.php");

// Please run "register.php" for generate your configuration
$GKconfig = array(
    'email' => "",   /* Admin e-mail */
  
    'agentid' => "",  /* ID for the agent (your system) */
    'key' => "",     /* Key in B64 to communicate with GuardianKey */
    'iv' => "",      /* IV in B64 for the key */
    'service' => "TestServicePHP",      /* Your service name*/
    'orgid' => "",   /* Your Org identification in GuardianKey */
    'authgroupid' => "",
    'reverse' => "True", /* If you will locally perform a reverse DNS resolution */

    /*Keys to anon users for send to GuardianKey*/
    'LocalKey' => "",
    'LocalIv' => "",
                                 
);

$GK = new guardiankey($GKconfig);

if (@$_SERVER['SERVER_NAME']) {
    echo "<h2>Login test</h2>
    <p>Please login</p>
    <form action=# method=post>
    <p>Username:<input type=text name=user></p>
    <p>Password:<input type=password name=password></p>
    <input type=submit value=submit>
    </form>";
    if ($_POST) {

		$user = openssl_encrypt($_POST['user'],'aes-256-cbc',base64_decode($GKconfig['LocalKey']),0,base64_decode($GKconfig['LocalIv']));
        $login_failed = 0; // 0 -> success, 1 -> failed. Check in your system
        $GKRet        = $GK->checkaccess($user,'',$login_failed);
        $GKJSONReturn = @json_decode($GKRet);
       
       //Verify GuardianKey response
        if ($GKJSONReturn->response == 'BLOCK' OR $GKJSONReturn->response == 'NOTIFY' OR $GKJSONReturn->response == 'HARD NOTIFY') {
			if ($GKJSONReturn->response == 'BLOCK' ) {
				// function to Block the access!
			}
			else {
				// function to send e-mail to user
			}
			
		}

    }
}

?>

