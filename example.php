<?php

require_once("guardiankey.class.php");

// data below available at: Panel->Settings->Authgroup->edit the authgroup->Deploy tab.
$GKconfig = array(
    'orgid' => "",   /* Your Org identification in GuardianKey */
    'authgroupid' => "",
    'key' => "",     /* Key in B64 to communicate with GuardianKey */
    'iv' => "",      /* IV in B64 for the key */
    
    'service' => "TestServicePHP",      /* Your service name*/
    'email' => "",   /* Admin e-mail */
    'agentid' => "myServer",  /* ID for the agent (your system) */
    'reverse' => "False", /* If you will locally perform a reverse DNS resolution */
);

$GK = new guardiankey($GKconfig);

if (@$_SERVER['SERVER_NAME']) {
    if ($_POST) {

        // Remember to properly sanitize the input
        $username=htmlspecialchars($_POST['user'],ENT_QUOTES);
        $password=htmlspecialchars($_POST['password'],ENT_QUOTES);

        // Set the user_email. 
        $user_email = $username;

        if($username == $password) {
            // Password match
            $login_failed = 0; // 0 -> success, 1 -> failed. Check in your system

            //Now, verify GuardianKey
            $GKRet        = $GK->checkaccess($username,$user_email,$login_failed);
            $GKJSONReturn = @json_decode($GKRet);

            // Block used when notifications should be sent by this system and not by GuardianKey
            // if ($GKJSONReturn->response == 'BLOCK' || $GKJSONReturn->response == 'NOTIFY' || $GKJSONReturn->response == 'HARD_NOTIFY') {
            //      // function to send e-mail to user
            //      // NOTIFY USER VIA EMAIL HERE. 
            //      // Example message in GuardianKey panel, Settings->Authgroup->edit authgroup->tab Alert
            // }
            if ($GKJSONReturn->response == 'BLOCK' ) {
                // function to Block the access! Same msg when credentials are invalid.
                echo "Invalid Credentials!";
                $logged_in=false;
            } else {
                echo "Welcome home! <a href='example.php'>return to login</a>";
                $logged_in=true;
            }
        }else{
            // Invalid credentials. Also send to GuardianKey for learning.
            $login_failed = 1;
            // GuardianKey...
            $GKRet = $GK->checkaccess($username,$user_email,$login_failed);
            // Invalid credentials 
            echo "Invalid Credentials!"; 
            $logged_in=false;
        }
    }

    if(!$logged_in)
        echo "<h2>Login on test system</h2>
        <p>Please login</p>
        <form action=# method=post>
        <p>Username:<input type=text name=user></p>
        <p>Password (equals to username):<input type=password name=password></p>
        <input type=submit value=submit>
        </form>";

    // For debugging
    if($GKRet)
        echo "<br /><br />GuardianKey return: ".$GKRet;
      
}

?>