<?php
class GKdata{
        public $authgroupid;
        public $key;
        public $iv;
}
function getKey() {
        /*You need load hashid,key and iv. If you save in the database, you need find us in this function*/
        $GK = new GKdata;
        $GK->authgroupid = '';
        $GK->key = '';
        $GK->iv = '';
        return $GK;
}


function doAction($data) {
	$GKdata = json_decode($data);
	
	/*In this function do you need define what action the webhook
	will make. Example:
		- Alert by email the users, and the username is the email of the user:
	
	$subject = "Atypical login from ".$GKdata->location;
	$message = "Hi,
	
			We detectead a atypical login on your account. Please view below:
			Device:".GKdata->system."
			IP:".$GKdata->ipaddress."
			Location: ".$GKdata->location."
			
			If is not you, tell the System Administrator";
			
	mail($GKdata->username,$subject,$message);
	
			*/
}		


			
if ($_POST) {
	
		$GKinfo = getKey();
		$data = json_decode(file_get_contents('php://input'), true);
		
        if ($data['authGroupId'] == $GKinfo->authgroupid ) {
			$key = base64_decode($GKinfo->key);
			$iv  = base64_decode($GKinfo->iv);
			try {
				$msgcrypt = base64_decode($data['data']);
				$output = openssl_decrypt($msgcrypt, 'aes-256-cfb8', $key, 1, $iv);
			    }
			 catch (Exception $e) {
				echo 'Error decrypting: ',  $e->getMessage(), "\n";
			}
			
			if ($output) {
				doAction($output);
				
			}
		
	}
}

?>
