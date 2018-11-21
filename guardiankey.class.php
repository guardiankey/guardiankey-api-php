<?php

define('AES_256_CBC', 'aes-256-cbc');

class guardiankey
{

    private $GKconfig = array(
                                'email' => "",   /* Admin e-mail */
                                'agentid' => "",  /* ID for the agent (your system) */
                                'key' => "",     /* Key in B64 to communicate with GuardianKey */
                                'iv' => "",      /* IV in B64 for the key */
                                'service' => "TestServicePHP",      /* Your service name*/
                                'orgid' => "",   /* Your Org identification in GuardianKey */
                                'authgroupid' => "", /* A Authentication Group identification, generated by GuardianKey */
                                'reverse' => "True" /* If you will locally perform a reverse DNS resolution */
                                );
    
    function check_extensions()
    {
        $nook=False;
        $extensions=array("curl");
        
        foreach($extensions as $ext){
            if ( !extension_loaded ($ext) )
            {
                echo "You have to install the PHP extension $ext\n";
                $nook=1;
            }
        }
        if($nook)
            exit;
    }

    function __construct($GKconfig=null)
    {
        $this->check_extensions();
        if($GKconfig!=null)
            $this->GKconfig = $GKconfig;
    }

    function _json_encode($obj)
    {
        array_walk_recursive($obj, function (&$item, $key) {
            $item = utf8_encode($item);
        });
        return json_encode($obj);
    }

    function create_message($username, $attempt = 0, $eventType)
    {
        $GKconfig = $this->GKconfig;
        $keyb64 = $GKconfig['key'];
        $ivb64 = $GKconfig['iv'];
        $agentid = $GKconfig['agentid'];
        $orgid = $GKconfig['orgid'];
        $authgroupid = $GKconfig['groupid'];
        $reverse = $GKconfig['reverse'];
        $timestamp = time();
        if (strlen($agentid) > 0) {
            $key = base64_decode($keyb64);
            $iv = base64_decode($ivb64);

            $json = new stdClass();
            $json->generatedTime = $timestamp;
            $json->agentId = $agentid;
            $json->organizationId = $orgid;
            $json->authGroupId = $authgroupid;
            $json->service = $GKconfig['service'];
            
            $json->clientIP = $_SERVER['REMOTE_ADDR'];
            $json->clientReverse = ($reverse == "True") ? gethostbyaddr($json->clientIP) : "";
            $json->userName = $username;
            $json->authMethod = "";
            $json->eventType = $eventType;
            $json->loginFailed = $attempt;
            $json->userAgent = substr($_SERVER['HTTP_USER_AGENT'], 0, 500);
            $json->psychometricTyped = "";
            $json->psychometricImage = "";

            $tmpmessage = $this->_json_encode($json);
            $blocksize = 8;
            $padsize = $blocksize - (strlen($tmpmessage) % $blocksize);
            $message = str_pad($tmpmessage, $padsize, " ");
            $cipher = openssl_encrypt($message, 'aes-256-cfb8', $key, 0, $iv);
            return $cipher;
        }
    }

    function sendevent($username, $attempt = "0", $eventType = 'Authentication')
    {
        $GKconfig = $this->GKconfig;
        $cipher = $this->create_message($username, $attempt, $eventType);
        $payload = $GKconfig['groupid'] . "|" . $cipher;
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_sendto($socket, $payload, strlen($payload), 0, "collector.guardiankey.net", "8888");
    }

    function checkaccess($username, $attempt = "0", $eventType = 'Authentication')
    {
        $GKconfig = $this->GKconfig;
        $guardianKeyWS = 'https://api.guardiankey.io/checkaccess';
        $message = $this->create_message($username, $attempt, $eventType);
        $tmpdata = new stdClass();
        $tmpdata->id = $GKconfig['groupid'];
        $tmpdata->message = $message;
        $data = $this->_json_encode($tmpdata);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $guardianKeyWS);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//         curl_setopt($ch, CURLOPT_VERBOSE, true);
        $return = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        
        try {
            $foo = json_decode($return);
            return $return;
        } catch (Exception $e) {
            return '{"response":"ERROR"}';
        }
    }
    
    /*
     * Optionally, you can set the notification parameters, such as:
     *   - notify_method: email or webhook
     *   - notify_data: A base64-encoded json containing URL (if method is webhook), server and SMTP port, user, and email password.
     * Example for e-mail:
     * $notify_method = 'email';
     * $notify_data = base64_encode('{"smtp_method":"TLS","smtp_host":"smtp.example.foo","smtp_port":"587","smtp_user":"myuser","smtp_pass":"mypass"}');
     * Example for webhook:
     * $notify_method = 'webhook';
     * $notify_data = base64_encode('{"webhook_url":"https://myorganization.com/guardiankey.php"}');
     */
    function register($email, $notify_method = null, $notify_data_json = null)
    {
        $guardianKeyWS = 'https://api.guardiankey.io/register';
        // Create new Key
        $key = openssl_random_pseudo_bytes(32);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC));
        $agentid = sha1(base64_encode(openssl_random_pseudo_bytes(20)));
        $keyb64 = base64_encode($key);
        $ivb64 = base64_encode($iv);

        $data = array(
            'email' => $email,
            'keyb64' => $keyb64,
            'ivb64' => $ivb64
        );
        
        if($notify_method!=null && $notify_data_json!=null)
        {
            $data = array(
                'email' => $email,
                'keyb64' => $keyb64,
                'ivb64' => $ivb64,
                'notify_method' => $notify_method,
                'notify_data' => base64_encode($notify_data_json)
            );
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $guardianKeyWS);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//         curl_setopt($ch, CURLOPT_VERBOSE, true);
        $returned = curl_exec($ch);
        curl_close($ch);
        $returns = @json_decode($returned);
        if ($returns === null) {
            throw new Exception('An error ocurred: ' . $returned);
        } else {
            return array(   "email"=> $email,
                            "agentid"=> $agentid,
                            "key"=>$keyb64,
                            "iv"=>$ivb64,
                            "orgid"=>$returns->organizationId,
                            "groupid"=>$returns->authGroupId
                        );
        }
    }
    
    function processWebHookPost($authgroupid,$keyb64,$ivb64)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($data['authGroupId'] == $authgroupid ) {
            $key = base64_decode($keyb64);
            $iv  = base64_decode($ivb64);
            try {
                $msgcrypt = base64_decode($data['data']);
                $output = openssl_decrypt($msgcrypt, 'aes-256-cfb8', $key, 1, $iv);
                $dataReturn=json_decode($output);
            } catch (Exception $e) {
                throw $e; // 'Error decrypting: ',  $e->getMessage(), "\n";
            }
            
            return $dataReturn;
            
        }   
    }
}
?>
