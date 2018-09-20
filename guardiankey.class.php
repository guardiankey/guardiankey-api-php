<?php

// Please run "register.php" for generate your configuration

$GKconfig = array(
	'email' => "",
	'hashid' => "",
	'key' => "",
	'Salt' => "",
	'iv' => "",
	'orgid' => "",
	'groupid' => "",
	'reverse' => "",
);



class guardiankey {
     
    function send_event($username)  {
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
          $cipher = openssl_encrypt($message, 'aes-256-cfb8', $key, 0, $iv);
          $payload=$hashid."|".$cipher;
          $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
          socket_sendto($socket, $payload, strlen($payload), 0, "collector.guardiankey.net", "8888");
        }
    }
    
    function check_access($username) {
		
			global $GKconfig;
		$guardianKeyWS='https://api.guardiankey.io/checkaccess';
		
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
     
          $message = base64_encode(json_encode($json,JSON_UNESCAPED_SLASHES));
          $data = array(
					'hashid' => $hashid,
					'message' => $message
					);
          $ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $guardianKeyWS);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$return = curl_exec($ch);
			curl_close($ch);
			return $return;
		}
	}
}
