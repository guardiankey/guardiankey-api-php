<?php

class GKdata{
        public $hashid;
        public $key;
        public $iv;
}

function getKey() {
        /*You need load hashid,key and iv. If you save in the database, you need find us in this function*/
        $GK = new GKdata;
        $GK->hashid = 'asdasdasda';
        $GK->key = 'asd';
        $GK->iv = 'dsa';
        return $GK;

}

function doAction($data) {
	$GKdata = json_decode($data);
	
	/*In this function do you need define what action the webhook
	will make. Example:
		- Alert by email the users, and the username is the email of the user:
	
	$subject = "Atypical login from ".$GKdata->city."-".$GKdata->country;
	$message = "Hi,
	
			We detectead a atypical login on your account. Please view below:
			Device:".GKdata->client_ua." - ".$GKdata->client_os."
			IP:".$GKdata->clientIP."
			Location: ".$GKdata->city." - ".$GKdata->region." - ".$GKdata->country."
			
			If is not you, tell the System Administrator";
			
	mail($GKdata->userName,$subject,$message);
	
			*/
}		
			

if ($_POST) {
		$GKinfo = getKey();
        if ($_POST['hashid'] == $GKinfo->hashid ) {
			$key = base64_decode($GKinfo->key);
			$iv  = base64_decode($GKinfo->iv);
			try {
				$output = openssl_decrypt(base64_decode($_POST['data'])	, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
			} catch (Exception $e) {
				echo 'Error decrypting: ',  $e->getMessage(), "\n";
			}
			if ($output) {
				doAction($output);
			}
		}
}
?>
