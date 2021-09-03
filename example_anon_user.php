<?php

/* This is an example to send events to Guardiankey anonymizing the usernames. 
It is required to configure keys in GuadianKey Panel (click on padlock icon, topbar). 
You can implement any anon algorithm, like a hash. In this case, data in the Panel will always appear as sent, ie, hashed. */

require_once("guardiankey.class.php");

// Register at https://panel.guardiankey.io/auth/register
// and take the information below in: Settings->Authgroup->edit the authgroup->Deploy tab.
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
    'LocalIv' => ""
);

$GK = new guardiankey($GKconfig);

if (@$_SERVER['SERVER_NAME']) {
    echo "<h2>Login test</h2>
    <p>Please login</p>
    <form action=# method=post>
    <p>Username:<input type=text name=user></p>
    <p>Password (equals to username):<input type=password name=password></p>
    <input type=submit value=submit>
    </form>";

    if ($_POST) {

        // for anonymization
        $user_encrypted = openssl_encrypt($_POST['user'],'aes-256-cbc',base64_decode($GKconfig['LocalKey']),0,base64_decode($GKconfig['LocalIv']));
        
        if($_POST['user'] == $_POST['password']) {
            // Password match
            $login_failed = 0; // 0 -> success, 1 -> failed. Check in your system

            //Now, verify GuardianKey
            $GKRet        = $GK->checkaccess($user_encrypted,'',$login_failed);
            $GKJSONReturn = @json_decode($GKRet);

            if ($GKJSONReturn->response == 'BLOCK' || $GKJSONReturn->response == 'NOTIFY' || $GKJSONReturn->response == 'HARD_NOTIFY') {

                 // function to send e-mail to user
                 // NOTIFY USER VIA EMAIL HERE. 
                 // Example message in GuardianKey panel, Settings->Authgroup->edit authgroup->tab Alert
            
            }

            if ($GKJSONReturn->response == 'BLOCK' ) {
                // function to Block the access!
                echo "Invalid Credentials!";
            } else {
                echo "Welcome!";
            }
        }else{
            // Invalid credentials. Also send to GuardianKey for learning.
            $login_failed = 1;
            // GuardianKey...
            $GKRet = $GK->checkaccess($user_encrypted,'',$login_failed);
            // block user
            echo "Invalid Credentials!";
        }
    }
}

?>