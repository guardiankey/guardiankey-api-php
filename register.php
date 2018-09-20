<?php
/* Dependencies: php-curl */

define('AES_256_CBC', 'aes-256-cbc');
function register($email) {
			$guardianKeyWS='https://api.guardiankey.io/register';
            // Create new Key
            $key = openssl_random_pseudo_bytes(32);
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC));
            $keyb64 = base64_encode($key);
            $ivb64 =  base64_encode($iv);
            /* Optionally, you can set the notification parameters, such as:
              - notify_method: email or webhook
			  - notify_data: A base64-encoded json containing URL (if method is webhook), server and SMTP port, user, and email password.
		Example for e-mail:
			$notify_method = 'email';
			$notify_data = base64_encode('{"smtp_method":"TLS","smtp_host":"smtp.example.foo","smtp_port":"587","smtp_user":"myuser","smtp_pass":"mypass"}');
		Example for webhook:
		    $notify_method = 'webhook';
			$notify_data = base64_encode('{"webhook_url":"https://myorganization.com/guardiankey.php"}');
			*/

			$data = array(
					'email' => $email,
					'keyb64' => $keyb64,
					'ivb64' => $ivb64,
					/*Uncoment if you defined notify options
					 'notify_method' => $notify_method,
					'notify_data' => $notify_data*/
					);
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $guardianKeyWS);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$hashid = curl_exec($ch);
			curl_close($ch);
            $salt = md5(rand().rand().rand().rand().$hashid);
			if ($_SERVER['SERVER_NAME']) {
				echo "<pre>";
			}
            echo  'Please add in your GuardianKey configuration: 
					$GKconfig = array(
					\'email\' => "'.$email.'",
					\'hashid\' => "'.$hashid.'",
					\'key\' => "'.$keyb64.'",
					\'Salt\' => "'.$salt.'",
					\'iv\' => "'.$ivb64.'",
					\'orgid\' => "",
					\'groupid\' => "",
					\'reverse\' => "1",
					);';
}
if ($_SERVER['SERVER_NAME']) {
	if ($_POST) {
		$email = $_POST['email'];
	} else {
	echo "<form action=# method=post>
		<p>Please enter admin email:</p>
		<input type=email name=email><br>
		<input type=submit value=submit>
	</form>";
	die;
	}
} else {
		echo "Please enter admin email:";
		$handle = fopen ("php://stdin","r");
		$email = trim(fgets($handle));	
	}
register($email);

