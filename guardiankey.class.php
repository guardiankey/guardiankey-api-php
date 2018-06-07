<?php

// Please run "register.php" for generate your configuration

$GKconfig = array(
	$GKconfig['key'] => "",
	$GKconfig['Salt'] => "",
	$GKconfig['iv'] => "",
	$GKconfig['orgid'] => "",
	$GKconfig['groupid'] => "",
	$GKconfig['reverse'] => "",
);

class guardiankey {
     
    function send_event($user, $username)  {
		global $GKconfig;
		
        $keyb64    = $GKconfig['key'];
        $salt      = $GKconfig['Salt'];
        $ivb64 	   = $GKconfig['iv'];
        $hashid    = $GKconfig['id'];
        $orgid     = $GKconfig['orgid'];
        $authgroupid    = $GKconfig['groupid'];
        $reverse   = $GKconfig['reverse'];
        $timestamp = time();

        if(strlen($hashid)>0){

          $key=base64_decode($keyb64);
          $iv=base64_decode($ivb64);
          
          $json = new stdClass();
          $json->generatedTime=$timestamp;
          $json->agentId=$hashid;
          $json->organizationId=$hashid;
          $json->authGroupId=$hashid;
          $json->service=$GKconfig['service'];;
          $json->clientIP=$_SERVER['REMOTE_ADDR'];
          $json->clientReverse = ($reverse==1)?  gethostbyaddr($json->clientIP) : "";
          $json->userName=$username;
          $json->authMethod="";
          $json->loginFailed="0";
          $json->userAgent=substr($_SERVER['HTTP_USER_AGENT'],0,500);
          $json->psychometricTyped="";
          $json->psychometricImage="";
     
          $message = json_encode($json,JSON_UNESCAPED_SLASHES);
          // some PHP versions needs it.. aff
          $blocksize=16;
          $padsize = $blocksize - (strlen($message) % $blocksize);
          $message=str_pad($message,$padsize," ");
          $cipher = openssl_encrypt($message, AES_256_CBC, $key, 0, $iv);
          $payload=$hashid."|".$cipher;
          $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
          socket_sendto($socket, $payload, strlen($payload), 0, "collector.guardiankey.net", "8888");
        }
    }
}
