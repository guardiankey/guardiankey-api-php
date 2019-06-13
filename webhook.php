<?php

require_once("guardiankey.class.php");


function doAction($data) {
	
	/*In this function do you need define what action the webhook
	will make. Example:
		- Alert by email the users, and the username is the email of the user:

	$GK = new guardiankey($GKconfig);	
	$subject = "Atypical login from ".$data['city'].'/'.$data['region'].'/'.$data['country'];
	$message = "Hi,
	
			We detectead a atypical login on your account. Please view below:
			Device:".$data['cliente_ua'].'/'.$data['client_os']."
			IP:".$data['clientIP']."
			Location: ".$data['city'].'/'.$data['region'].'/'.$data['country']."
			
			Please confirm in link below if are you or not:
			".$GKconfig['resolveEventURL']."?t=".$data['event_token']."&e=".$data['eventId'];
			
	mail($data->userName,$subject,$message);
	
			*/
}		


$data = json_decode(file_get_contents('php://input'), true);
if ($data) {	

    try {
        $GK = new guardiankey($GKconfig);
        $output=$GK->processWebHookPost($authgroupid,$keyb64,$ivb64);
        doAction($output);
    } catch (Exception $e) {
        echo "Something got wrong, probably the key do not match.\n";
    }
}

?>
