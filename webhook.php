<?php

require_once("guardiankey.class.php");


function doAction($data) {
	
	/*In this function do you need define what action the webhook
	will make. Example:
		- Alert by email the users, and the username is the email of the user:
	
	$subject = "Atypical login from ".$GKdata->location;
	$message = "Hi,
	
			We detectead a atypical login on your account. Please view below:
			Device:".GKdata->system."
			IP:".$GKdata->ipaddress."
			Location: ".$GKdata->location."
			
			Please confirm in link below if are you or not:
			".$GKconfig['resolveEventURL']."?t=".$GKdata['EVENTTOKEN']."&e=".$GKdata['EVENTID'].";
			
	mail($GKdata->username,$subject,$message);
	
			*/
}		


			
if ($_POST) {
	
    try {
        $GK = new guardiankey($GKconfig);
        $output=$GK->processWebHookPost($authgroupid,$keyb64,$ivb64);
        doAction($output);
    } catch (Exception $e) {
        echo "Something got wrong, probably the key do not match.\n";
    }
}

?>
